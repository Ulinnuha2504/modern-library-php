<?php
namespace App\Models;
use App\Core\Model;
class Peminjaman extends Model {
    protected string $table = 'peminjaman';
    protected array $fillable = ['kode_transaksi', 'santri_id', 'tanggal_pinjam', 'tanggal_jatuh_tempo', 'tanggal_kembali', 'status', 'denda', 'catatan', 'petugas_id'];
    public function findByKodeTransaksi(string $kode): ?array {
        $stmt = $this->db()->prepare("SELECT p.*, s.nis, s.nama as nama_santri, s.kelas, s.kamar, s.no_hp as hp_santri, u.name as nama_petugas FROM `peminjaman` p JOIN `santri` s ON p.santri_id = s.id JOIN `users` u ON p.petugas_id = u.id WHERE p.kode_transaksi = :kode LIMIT 1");
        $stmt->execute(['kode' => trim($kode)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function findWithFullDetails(int $id): ?array {
        $stmt = $this->db()->prepare("SELECT p.*, s.nis, s.nama as nama_santri, s.kelas, s.kamar, s.no_hp as hp_santri, u.name as nama_petugas FROM `peminjaman` p JOIN `santri` s ON p.santri_id = s.id JOIN `users` u ON p.petugas_id = u.id WHERE p.id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $trx = $stmt->fetch();
        if (!$trx) return null;
        $trx['items'] = (new PeminjamanDetail())->getDetailsByPeminjamanId($id);
        return $trx;
    }
    public function paginateWithRelations(int $page = 1, int $perPage = 15, array $filters = []): array {
        $page = max(1, $page); $perPage = max(1, $perPage); $offset = ($page - 1) * $perPage;
        $where = ["1=1"]; $params = [];
        if (!empty($filters['search'])) {
            $kw = '%' . trim($filters['search']) . '%';
            $where[] = "(p.kode_transaksi LIKE :kw1 OR s.nis LIKE :kw2 OR s.nama LIKE :kw3)";
            $params['kw1'] = $kw; $params['kw2'] = $kw; $params['kw3'] = $kw;
        }
        if (!empty($filters['status'])) { $where[] = "p.status = :status"; $params['status'] = $filters['status']; }
        if (!empty($filters['tanggal_awal'])) { $where[] = "p.tanggal_pinjam >= :tgl_awal"; $params['tgl_awal'] = $filters['tanggal_awal']; }
        if (!empty($filters['tanggal_akhir'])) { $where[] = "p.tanggal_pinjam <= :tgl_akhir"; $params['tgl_akhir'] = $filters['tanggal_akhir']; }
        $whereClause = implode(' AND ', $where);
        $stmtCount = $this->db()->prepare("SELECT COUNT(*) as total FROM `peminjaman` p JOIN `santri` s ON p.santri_id = s.id WHERE {$whereClause}");
        $stmtCount->execute($params);
        $totalRecords = (int) ($stmtCount->fetch()['total'] ?? 0);
        $totalPages = (int) ceil($totalRecords / $perPage);
        $sql = "SELECT p.*, s.nis, s.nama as nama_santri, s.kelas, u.name as nama_petugas, (SELECT COUNT(*) FROM `peminjaman_detail` pd WHERE pd.peminjaman_id = p.id) as total_item FROM `peminjaman` p JOIN `santri` s ON p.santri_id = s.id JOIN `users` u ON p.petugas_id = u.id WHERE {$whereClause} ORDER BY p.id DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmtData = $this->db()->prepare($sql);
        $stmtData->execute($params);
        return ['data' => $stmtData->fetchAll(), 'total' => $totalRecords, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => max(1, $totalPages), 'has_prev' => $page > 1, 'has_next' => $page < $totalPages];
    }
    public function generateKodeTransaksi(): string {
        $dateStr = date('Ymd'); $prefix = "TRX-{$dateStr}-";
        $stmt = $this->db()->prepare("SELECT kode_transaksi FROM `peminjaman` WHERE kode_transaksi LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $latest = $stmt->fetch();
        $next = ($latest && preg_match('/TRX-\d{8}-(\d+)/', $latest['kode_transaksi'], $m)) ? ((int)$m[1] + 1) : 1;
        return sprintf("TRX-%s-%04d", $dateStr, $next);
    }
}
