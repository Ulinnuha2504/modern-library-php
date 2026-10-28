<?php
namespace App\Models;
use App\Core\Model;
class PeminjamanDetail extends Model {
    protected string $table = 'peminjaman_detail';
    protected array $fillable = ['peminjaman_id', 'buku_id', 'tanggal_kembali', 'kondisi_pinjam', 'kondisi_kembali', 'denda', 'status', 'catatan'];
    public function getDetailsByPeminjamanId(int $peminjamanId): array {
        $stmt = $this->db()->prepare("SELECT pd.*, b.kode_buku, b.judul as judul_buku, b.penulis, b.cover, k.nama as nama_kategori, r.kode_rak FROM `peminjaman_detail` pd JOIN `buku` b ON pd.buku_id = b.id LEFT JOIN `kategori` k ON b.kategori_id = k.id LEFT JOIN `rak` r ON b.rak_id = r.id WHERE pd.peminjaman_id = :id ORDER BY pd.id ASC");
        $stmt->execute(['id' => $peminjamanId]);
        return $stmt->fetchAll();
    }
}
