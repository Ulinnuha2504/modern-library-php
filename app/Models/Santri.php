<?php
namespace App\Models;
use App\Core\Model;
use PDO;
class Santri extends Model {
    protected string $table = 'santri';
    protected array $fillable = ['nis', 'nisn', 'nama', 'jenis_kelamin', 'kelas', 'kamar', 'tahun_masuk', 'nama_wali', 'no_hp', 'alamat', 'status'];
    public function findByNis(string $nis): ?array { return $this->firstWhere('nis', $nis); }
    public function search(string $keyword, int $limit = 10): array {
        $kw = '%' . trim($keyword) . '%';
        $stmt = $this->db()->prepare("SELECT id, nis, nisn, nama, kelas, kamar, status FROM `{$this->table}` WHERE (`nis` LIKE :kw1 OR `nama` LIKE :kw2 OR `nisn` LIKE :kw3) ORDER BY nama ASC LIMIT :limit");
        $stmt->bindValue(':kw1', $kw);
        $stmt->bindValue(':kw2', $kw);
        $stmt->bindValue(':kw3', $kw);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getActiveLoansCount(int $santriId): int {
        $stmt = $this->db()->prepare("SELECT COUNT(pd.id) as total_pinjam FROM `peminjaman_detail` pd JOIN `peminjaman` p ON pd.peminjaman_id = p.id WHERE p.santri_id = :id AND pd.status = 'dipinjam' AND p.status IN ('dipinjam', 'sebagian_dikembalikan', 'terlambat')");
        $stmt->execute(['id' => $santriId]);
        return (int)($stmt->fetch()['total_pinjam'] ?? 0);
    }
    public function getBorrowingHistory(int $santriId): array {
        $stmt = $this->db()->prepare("SELECT p.kode_transaksi, p.tanggal_pinjam, p.tanggal_jatuh_tempo, b.judul as judul_buku, b.kode_buku, pd.status as status_item, pd.tanggal_kembali, pd.denda, pd.kondisi_kembali FROM `peminjaman_detail` pd JOIN `peminjaman` p ON pd.peminjaman_id = p.id JOIN `buku` b ON pd.buku_id = b.id WHERE p.santri_id = :id ORDER BY p.tanggal_pinjam DESC");
        $stmt->execute(['id' => $santriId]);
        return $stmt->fetchAll();
    }
}
