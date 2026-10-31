<?php
// =============================================================================
// BASE CONTROLLER
// =============================================================================

class Controller {
    protected $view;

    public function __construct() {
        $this->view = new View();
    }

    protected function render(string $template, array $data = [], string $layout = 'layout') {
        echo $this->view->render($template, $data, $layout);
    }

    protected function redirect(string $url) {
        header('Location: ' . app_url($url));
        exit;
    }

    protected function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    protected function currentUserRoles(): array {
        $user = $this->currentUser();
        if (!$user) {
            return [];
        }

        $roles = array_column(UserModel::getRoles((int)$user['ma']), 'ma_vai_tro');
        $_SESSION['user_roles'] = $roles;
        return $roles;
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . app_url('/auth/login'));
            exit;
        }
        return $_SESSION['user'];
    }

    protected function requireRole(string $role): array {
        $user = $this->requireAuth();
        $userRoles = $this->currentUserRoles();
        if (!in_array($role, $userRoles, true)) {
            http_response_code(403);
            $this->render('errors/403', ['message' => 'Bạn không có quyền truy cập trang này.']);
            exit;
        }
        return $user;
    }

    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrf(string $token): bool {
        return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
