<?php
namespace App\Models;
use App\Core\Model;
class Kategori extends Model {
    protected string $table = 'kategori';
    protected array $fillable = ['nama', 'deskripsi', 'status'];
    public function allActive(): array { return $this->where('status', 'aktif'); }
    public function getWithBookCount(): array {
        $stmt = $this->db()->query("SELECT k.*, COUNT(b.id) as total_buku FROM `kategori` k LEFT JOIN `buku` b ON k.id = b.kategori_id GROUP BY k.id ORDER BY k.nama ASC");
        return $stmt->fetchAll();
    }
}
