<?php
namespace App\Models;
use App\Core\Model;
class ActivityLog extends Model {
    protected string $table = 'activity_logs';
    protected array $fillable = ['user_id', 'action', 'module', 'description', 'ip_address', 'user_agent'];
    public function paginateWithUser(int $page = 1, int $perPage = 20, array $filters = []): array {
        $page = max(1, $page); $perPage = max(1, $perPage); $offset = ($page - 1) * $perPage;
        $where = ["1=1"]; $params = [];
        if (!empty($filters['module'])) { $where[] = "l.module = :module"; $params['module'] = $filters['module']; }
        if (!empty($filters['search'])) {
            $kw = '%' . trim($filters['search']) . '%';
            $where[] = "(l.description LIKE :kw1 OR l.action LIKE :kw2)";
            $params['kw1'] = $kw; $params['kw2'] = $kw;
        }
        $whereClause = implode(' AND ', $where);
        $stmtCount = $this->db()->prepare("SELECT COUNT(*) as total FROM `activity_logs` l WHERE {$whereClause}");
        $stmtCount->execute($params);
        $totalRecords = (int) ($stmtCount->fetch()['total'] ?? 0);
        $totalPages = (int) ceil($totalRecords / $perPage);
        $sql = "SELECT l.*, u.name as nama_user, u.username, u.role FROM `activity_logs` l LEFT JOIN `users` u ON l.user_id = u.id WHERE {$whereClause} ORDER BY l.id DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmtData = $this->db()->prepare($sql);
        $stmtData->execute($params);
        return ['data' => $stmtData->fetchAll(), 'total' => $totalRecords, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => max(1, $totalPages), 'has_prev' => $page > 1, 'has_next' => $page < $totalPages];
    }
}
