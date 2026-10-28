<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Models\ActivityLog;

class LogController extends Controller {
    protected ActivityLog $logModel;
    public function __construct() { $this->logModel = new ActivityLog(); }
    public function index(Request $request): void {
        $page = (int) $request->get('page', 1);
        $filters = ['search' => trim((string)$request->get('q', '')), 'module' => trim((string)$request->get('module', ''))];
        $modules = \App\Core\Database::getConnection()->query("SELECT DISTINCT module FROM `activity_logs` ORDER BY module ASC")->fetchAll(\PDO::FETCH_COLUMN);
        $this->view('logs.index', ['pageTitle' => 'Audit Trail & Log Aktivitas', 'pagination' => $this->logModel->paginateWithUser($page, 20, $filters), 'modules' => $modules, 'filters' => $filters]);
    }
}
