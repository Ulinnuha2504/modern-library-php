<?php
namespace App\Core;
abstract class Controller {
    protected function view(string $viewPath, array $data = [], string|bool $layout = 'main'): void { View::render($viewPath, $data, $layout); }
    protected function json(mixed $data, int $status = 200): void { Response::json($data, $status); }
    protected function redirect(string $path, array $flash = []): void {
        foreach ($flash as $key => $message) Session::setFlash($key, $message);
        Response::redirect(url($path));
    }
    protected function back(array $flash = []): void {
        foreach ($flash as $key => $message) Session::setFlash($key, $message);
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        Response::redirect($referer);
    }
    protected function validate(Request $request, array $rules): array {
        $errors = []; $data = [];
        foreach ($rules as $field => $ruleString) {
            $value = $request->input($field);
            $ruleList = explode('|', $ruleString);
            foreach ($ruleList as $rule) {
                if ($rule === 'required' && ($value === null || trim((string)$value) === '')) $errors[$field][] = "Kolom $field wajib diisi.";
                elseif (str_starts_with($rule, 'min:') && strlen((string)$value) < (int)substr($rule, 4)) $errors[$field][] = "Kolom $field minimal " . substr($rule, 4) . " karakter.";
                elseif (str_starts_with($rule, 'max:') && strlen((string)$value) > (int)substr($rule, 4)) $errors[$field][] = "Kolom $field maksimal " . substr($rule, 4) . " karakter.";
                elseif ($rule === 'numeric' && !is_numeric($value)) $errors[$field][] = "Kolom $field harus angka.";
                elseif ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) $errors[$field][] = "Format email tidak valid.";
            }
            $data[$field] = is_string($value) ? trim($value) : $value;
        }
        if (!empty($errors)) {
            Session::setFlash('_errors', $errors);
            Session::setFlash('_old_inputs', $request->all());
            if ($request->isAjax()) Response::json(['status' => 'error', 'errors' => $errors, 'message' => 'Validasi gagal.'], 422);
            else $this->back();
        }
        return $data;
    }
}
