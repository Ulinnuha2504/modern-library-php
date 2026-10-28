<?php
namespace App\Models;
use App\Core\Model;
class Rak extends Model {
    protected string $table = 'rak';
    protected array $fillable = ['kode_rak', 'nama_rak', 'lokasi', 'deskripsi', 'status'];
    public function allActive(): array { return $this->where('status', 'aktif'); }
    public function getWithBookCount(): array {
        $stmt = $this->db()->query("SELECT r.*, COUNT(b.id) as total_buku FROM `rak` r LEFT JOIN `buku` b ON r.id = b.rak_id GROUP BY r.id ORDER BY r.kode_rak ASC");
        return $stmt->fetchAll();
    }
}
