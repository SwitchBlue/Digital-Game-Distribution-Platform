<?php
// =============================================================================
// ADMIN CONTROLLER
// =============================================================================

class AdminController extends Controller {
<<<<<<< HEAD

=======
    
>>>>>>> origin/main
    public function dashboard() {
        $user = $this->requireRole('admin');
        $games = GameModel::all('ngay_tao DESC LIMIT 20');
        $reports = ReportModel::listOpen();
        $users = UserModel::all('ngay_tao DESC LIMIT 20');
        $this->render('admin/dashboard', [
            'user' => $user,
            'games' => $games,
            'reports' => $reports,
            'users' => $users,
        ]);
    }

    public function games() {
        $user = $this->requireRole('admin');
<<<<<<< HEAD
        $query = trim((string)($_GET['q'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $totalPages = max(1, (int)ceil(GameModel::countForAdmin($query) / $perPage));
        $page = min($page, $totalPages);
        $games = GameModel::listForAdmin($query, $perPage, ($page - 1) * $perPage);
        $this->render('admin/games', [
            'user' => $user,
            'games' => $games,
            'csrfToken' => $this->csrfToken(),
            'query' => $query,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function removeGame(array $params) {
        $this->requireRole('admin');
        $gameId = (int)($params['id'] ?? 0);
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/admin/games');
        }

        $game = GameModel::find($gameId);
        if (!$game) {
            $_SESSION['error'] = 'Không tìm thấy trò chơi cần gỡ.';
            $this->redirect('/admin/games');
        }
        if (!GameModel::removeFromPlatform($gameId)) {
            throw new RuntimeException('Không thể gỡ trò chơi khỏi nền tảng.');
        }

        $_SESSION['success'] = 'Đã gỡ trò chơi khỏi nền tảng. Lịch sử giao dịch được giữ nguyên.';
        $this->redirect('/admin/games');
    }

=======
        $games = GameModel::all('ngay_tao DESC LIMIT 100');
        $this->render('admin/games', [
            'user' => $user,
            'games' => $games,
        ]);
    }

>>>>>>> origin/main
    public function reports() {
        $user = $this->requireRole('admin');
        $reports = ReportModel::listOpen();
        $this->render('admin/reports', [
            'user' => $user,
            'reports' => $reports,
        ]);
    }

    public function users() {
        $user = $this->requireRole('admin');
<<<<<<< HEAD
        $query = trim((string)($_GET['q'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $totalPages = max(1, (int)ceil(UserModel::countForAdmin($query) / $perPage));
        $page = min($page, $totalPages);
        $users = UserModel::listForAdmin($query, $perPage, ($page - 1) * $perPage);
        $roles = RoleModel::getAll();
        $userRoles = [];
        foreach ($users as $account) {
            $userRoles[(int)$account['ma']] = array_column(
                UserModel::getRoles((int)$account['ma']),
                'ma_vai_tro'
            );
        }
        $this->render('admin/users', [
            'user' => $user,
            'users' => $users,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'csrfToken' => $this->csrfToken(),
            'query' => $query,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function updateUserRoles(array $params) {
        $admin = $this->requireRole('admin');
        $userId = (int)($params['id'] ?? 0);
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/admin/users');
        }
        if ($userId === (int)$admin['ma']) {
            $_SESSION['error'] = 'Để bảo vệ quyền quản trị, bạn không thể tự thay đổi vai trò của mình.';
            $this->redirect('/admin/users');
        }
        if (!UserModel::find($userId)) {
            $_SESSION['error'] = 'Không tìm thấy tài khoản cần cập nhật.';
            $this->redirect('/admin/users');
        }

        $availableRoleIds = array_map('intval', array_column(RoleModel::getAll(), 'ma'));
        $submittedRoleIds = $_POST['role_ids'] ?? [];
        if (!is_array($submittedRoleIds)) {
            $_SESSION['error'] = 'Danh sách vai trò không hợp lệ.';
            $this->redirect('/admin/users');
        }
        $roleIds = [];
        foreach ($submittedRoleIds as $roleId) {
            $validatedId = filter_var($roleId, FILTER_VALIDATE_INT);
            if ($validatedId === false || !in_array((int)$validatedId, $availableRoleIds, true)) {
                $_SESSION['error'] = 'Danh sách vai trò không hợp lệ.';
                $this->redirect('/admin/users');
            }
            $roleIds[] = (int)$validatedId;
        }
        $roleIds = array_values(array_unique($roleIds));
        if (!$roleIds) {
            $_SESSION['error'] = 'Mỗi tài khoản cần có ít nhất một vai trò.';
            $this->redirect('/admin/users');
        }

        if (!UserModel::syncRoles($userId, $roleIds)) {
            $_SESSION['error'] = 'Không thể gỡ vai trò admin cuối cùng khỏi hệ thống.';
            $this->redirect('/admin/users');
        }

        $_SESSION['success'] = 'Đã cập nhật vai trò cho tài khoản.';
        $this->redirect('/admin/users');
    }
}
=======
        $users = UserModel::all('ngay_tao DESC LIMIT 100');
        $this->render('admin/users', [
            'user' => $user,
            'users' => $users,
        ]);
    }
}
>>>>>>> origin/main
