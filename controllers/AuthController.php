<?php
// =============================================================================
// AUTH CONTROLLER
// =============================================================================

class AuthController extends Controller {

    public function showLogin() {
        $this->render('auth/login');
    }

    public function showRegister() {
        $this->render('auth/register');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $user = UserModel::authenticate($email, $password);
        if (!$user) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
            $this->redirect('/auth/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        $roles = UserModel::getRoles($user['ma']);
        $_SESSION['user_roles'] = array_column($roles, 'ma_vai_tro');

        $redirect = $_SESSION['redirect_after_login'] ?? '/';
        unset($_SESSION['redirect_after_login']);
        $this->redirect($redirect);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $username = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        $displayName = trim((string)($_POST['display_name'] ?? ''));

        if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($password) < 8
            || trim($displayName) === ''
            || strlen($displayName) > 100) {
            $_SESSION['error'] = 'Vui lòng nhập tên đăng nhập (3–50 ký tự), email hợp lệ, tên hiển thị và mật khẩu ít nhất 8 ký tự.';
            $this->redirect('/auth/register');
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            $this->redirect('/auth/register');
        }

        if (UserModel::findBy('email', $email)) {
            $_SESSION['error'] = 'Email đã được sử dụng.';
            $this->redirect('/auth/register');
        }

        if (UserModel::findBy('ten_dang_nhap', $username)) {
            $_SESSION['error'] = 'Tên đăng nhập đã tồn tại.';
            $this->redirect('/auth/register');
        }

        $userId = UserModel::create($username, $email, $password, $displayName);

        // Assign default player role
        $playerRole = RoleModel::findByCode('player');
        if ($playerRole) {
            UserModel::addRole($userId, $playerRole['ma']);
        }

        // Create default settings
        UserSettingsModel::createDefault($userId);

        // Auto login
        $user = UserModel::find($userId);
        unset($user['mat_khau_da_bam']);
        $_SESSION['user'] = $user;
        $_SESSION['user_roles'] = ['player'];

        $_SESSION['success'] = 'Đăng ký thành công! Chào mừng bạn đến với ' . APP_NAME;
        session_regenerate_id(true);
        $this->redirect('/games');
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/');
    }

    public function me() {
        header('Content-Type: application/json');
        echo json_encode($_SESSION['user'] ?? null);
        exit;
    }
}
