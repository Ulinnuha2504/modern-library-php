<?php
namespace App\Core;
use PDO;
abstract class Model {
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected function db(): PDO { return Database::getConnection(); }
    public function all(string $orderBy = 'id DESC'): array {
        $stmt = $this->db()->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }
    public function find(int|string $id): ?array {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function where(string $column, mixed $value, string $operator = '='): array {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` {$operator} :val");
        $stmt->execute(['val' => $value]);
        return $stmt->fetchAll();
    }
    public function firstWhere(string $column, mixed $value, string $operator = '='): ?array {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` {$operator} :val LIMIT 1");
        $stmt->execute(['val' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function count(string $whereClause = '', array $params = []): int {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        if ($whereClause !== '') $sql .= " WHERE " . $whereClause;
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }
    public function insert(array $data): int {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        if (empty($fields)) $fields = $data;
        $columns = implode('`, `', array_keys($fields));
        $placeholders = ':' . implode(', :', array_keys($fields));
        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($fields);
        return (int) $this->db()->lastInsertId();
    }
    public function update(int|string $id, array $data): bool {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        if (empty($fields)) $fields = $data;
        $setPart = [];
        foreach (array_keys($fields) as $column) $setPart[] = "`{$column}` = :{$column}";
        $setString = implode(', ', $setPart);
        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = :primary_id";
        $params = array_merge($fields, ['primary_id' => $id]);
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($params);
    }
    public function delete(int|string $id): bool {
        $stmt = $this->db()->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function paginate(int $page = 1, int $perPage = 15, string $where = '', array $params = [], string $orderBy = 'id DESC'): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $totalRecords = $this->count($where, $params);
        $totalPages = (int) ceil($totalRecords / $perPage);
        $sql = "SELECT * FROM `{$this->table}`";
        if ($where !== '') $sql .= " WHERE " . $where;
        $sql .= " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        return [
            'data' => $data, 'total' => $totalRecords, 'current_page' => $page,
            'per_page' => $perPage, 'last_page' => max(1, $totalPages),
            'has_prev' => $page > 1, 'has_next' => $page < $totalPages,
        ];
    }
}
