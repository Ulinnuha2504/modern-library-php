<?php
namespace App\Models;
use App\Core\Model;
use PDO;
class Buku extends Model {
    protected string $table = 'buku';
    protected array $fillable = ['kode_buku', 'isbn', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'kategori_id', 'rak_id', 'jumlah_total', 'jumlah_tersedia', 'jumlah_dipinjam', 'jumlah_rusak', 'jumlah_hilang', 'cover', 'deskripsi', 'status'];
    public function findWithRelations(int $id): ?array {
        $stmt = $this->db()->prepare("SELECT b.*, k.nama as nama_kategori, r.kode_rak, r.nama_rak, r.lokasi as lokasi_rak FROM `buku` b LEFT JOIN `kategori` k ON b.kategori_id = k.id LEFT JOIN `rak` r ON b.rak_id = r.id WHERE b.id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function paginateWithRelations(int $page = 1, int $perPage = 15, array $filters = []): array {
        $page = max(1, $page); $perPage = max(1, $perPage); $offset = ($page - 1) * $perPage;
        $where = ["1=1"]; $params = [];
        if (!empty($filters['search'])) {
            $kw = '%' . trim($filters['search']) . '%';
            $where[] = "(b.kode_buku LIKE :kw1 OR b.isbn LIKE :kw2 OR b.judul LIKE :kw3 OR b.penulis LIKE :kw4)";
            $params['kw1'] = $kw; $params['kw2'] = $kw; $params['kw3'] = $kw; $params['kw4'] = $kw;
        }
        if (!empty($filters['kategori_id'])) { $where[] = "b.kategori_id = :kategori_id"; $params['kategori_id'] = $filters['kategori_id']; }
        if (!empty($filters['rak_id'])) { $where[] = "b.rak_id = :rak_id"; $params['rak_id'] = $filters['rak_id']; }
        if (!empty($filters['status'])) { $where[] = "b.status = :status"; $params['status'] = $filters['status']; }
        $whereClause = implode(' AND ', $where);
        $stmtCount = $this->db()->prepare("SELECT COUNT(*) as total FROM `buku` b WHERE {$whereClause}");
        $stmtCount->execute($params);
        $totalRecords = (int) ($stmtCount->fetch()['total'] ?? 0);
        $totalPages = (int) ceil($totalRecords / $perPage);
        $sql = "SELECT b.*, k.nama as nama_kategori, r.kode_rak, r.nama_rak FROM `buku` b LEFT JOIN `kategori` k ON b.kategori_id = k.id LEFT JOIN `rak` r ON b.rak_id = r.id WHERE {$whereClause} ORDER BY b.id DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmtData = $this->db()->prepare($sql);
        $stmtData->execute($params);
        return ['data' => $stmtData->fetchAll(), 'total' => $totalRecords, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => max(1, $totalPages), 'has_prev' => $page > 1, 'has_next' => $page < $totalPages];
    }
    public function searchAvailable(string $keyword, int $limit = 10): array {
        $kw = '%' . trim($keyword) . '%';
        $stmt = $this->db()->prepare("SELECT b.id, b.kode_buku, b.isbn, b.judul, b.penulis, b.jumlah_tersedia, k.nama as nama_kategori, r.kode_rak FROM `buku` b LEFT JOIN `kategori` k ON b.kategori_id = k.id LEFT JOIN `rak` r ON b.rak_id = r.id WHERE b.status = 'aktif' AND b.jumlah_tersedia > 0 AND (b.kode_buku LIKE :kw1 OR b.isbn LIKE :kw2 OR b.judul LIKE :kw3 OR b.penulis LIKE :kw4) ORDER BY b.judul ASC LIMIT :limit");
        $stmt->bindValue(':kw1', $kw); $stmt->bindValue(':kw2', $kw); $stmt->bindValue(':kw3', $kw); $stmt->bindValue(':kw4', $kw);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function generateKodeBuku(): string {
        $year = date('Y'); $prefix = "BUK-{$year}-";
        $stmt = $this->db()->prepare("SELECT kode_buku FROM `buku` WHERE kode_buku LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $latest = $stmt->fetch();
        $next = ($latest && preg_match('/BUK-\d{4}-(\d+)/', $latest['kode_buku'], $m)) ? ((int)$m[1] + 1) : 1;
        return sprintf("BUK-%s-%04d", $year, $next);
    }
    public function getBorrowHistory(int $bukuId, int $limit = 15): array {
        $stmt = $this->db()->prepare("SELECT pd.*, p.kode_transaksi, p.tanggal_pinjam, p.tanggal_jatuh_tempo, s.nis, s.nama as nama_santri, s.kelas, u.name as nama_petugas FROM `peminjaman_detail` pd JOIN `peminjaman` p ON pd.peminjaman_id = p.id JOIN `santri` s ON p.santri_id = s.id JOIN `users` u ON p.petugas_id = u.id WHERE pd.buku_id = :id ORDER BY p.tanggal_pinjam DESC LIMIT :limit");
        $stmt->bindValue(':id', $bukuId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
