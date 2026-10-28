<?php
namespace App\Models;
use App\Core\Model;
class User extends Model {
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'username', 'password', 'role', 'status', 'last_login'];
    public function findByUsernameOrEmail(string $identifier): ?array {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE (`username` = :id1 OR `email` = :id2) LIMIT 1");
        $stmt->execute(['id1' => $identifier, 'id2' => $identifier]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function updateLastLogin(int $id): bool {
        $stmt = $this->db()->prepare("UPDATE `{$this->table}` SET `last_login` = NOW() WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }
}
