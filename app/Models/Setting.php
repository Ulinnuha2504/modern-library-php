<?php
namespace App\Models;
use App\Core\Model;
class Setting extends Model {
    protected string $table = 'settings';
    protected array $fillable = ['setting_key', 'setting_value', 'setting_group', 'deskripsi'];
    protected static array $cache = [];
    public function get(string $key, mixed $default = null): mixed {
        if (isset(self::$cache[$key])) return self::$cache[$key];
        $row = $this->firstWhere('setting_key', $key);
        if ($row) { self::$cache[$key] = $row['setting_value']; return $row['setting_value']; }
        return $default;
    }
    public function set(string $key, string $value): bool {
        self::$cache[$key] = $value;
        $existing = $this->firstWhere('setting_key', $key);
        if ($existing) return $this->update($existing['id'], ['setting_value' => $value]);
        return (bool) $this->insert(['setting_key' => $key, 'setting_value' => $value]);
    }
    public function getAllKeyed(): array {
        $all = $this->all('setting_group ASC, id ASC');
        $res = []; foreach ($all as $item) $res[$item['setting_key']] = $item['setting_value'];
        return $res;
    }
}
