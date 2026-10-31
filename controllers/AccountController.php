<?php
// =============================================================================
// ACCOUNT CONTROLLER
// =============================================================================

class AccountController extends Controller {

    public function dashboard() {
        $user = $this->requireAuth();
        $games = GameModel::findAllBy('nguoi_tao_id', $user['ma']);
        $ownedGames = OwnershipModel::listForUser((int)$user['ma']);
        $collections = CollectionModel::listForOwner($user['ma']);
        $watchedGames = WatchModel::watchedGamesForUser((int)$user['ma']);
        $followedUsers = FollowModel::followedUsersForUser((int)$user['ma']);
        $this->render('account/dashboard', [
            'user' => $user,
            'games' => $games,
            'ownedGames' => $ownedGames,
            'collections' => $collections,
            'watchedGames' => $watchedGames,
            'followedUsers' => $followedUsers,
        ]);
    }

    public function profile() {
        $sessionUser = $this->requireAuth();
        $user = UserModel::find((int)$sessionUser['ma']);
        if (!$user) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Không tìm thấy tài khoản.']);
            return;
        }

        $this->render('account/profile', [
            'user' => $user,
            'roles' => UserModel::getRoles((int)$user['ma']),
        ]);
    }

    public function notifications() {
        $user = $this->requireAuth();
        $notifications = NotificationModel::listForUser((int)$user['ma'], 100);
        $this->render('account/notifications', [
            'notifications' => $notifications,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function markNotificationRead(array $params) {
        $user = $this->requireAuth();
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/notifications');
        }

        NotificationModel::markReadForUser((int)($params['id'] ?? 0), (int)$user['ma']);
        $this->redirect('/notifications');
    }
}
