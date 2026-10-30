<?php
// =============================================================================
// AUTHOR CONTROLLER
// =============================================================================

class AuthorController extends Controller {

    public function show(array $params) {
        $id = (int)($params['id'] ?? 0);
        $author = UserModel::find($id);
        if (!$author) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Tác giả không tìm thấy.']);
            return;
        }
<<<<<<< HEAD
        $currentUser = $this->currentUser();
        $isFollowing = $currentUser && (int)$currentUser['ma'] !== $id
            ? FollowModel::isFollowing((int)$currentUser['ma'], $id)
            : false;
=======
>>>>>>> origin/main

        // Get games by this author
        $sql = "SELECT g.* FROM tro_choi g
                INNER JOIN tac_gia_tro_choi t ON t.tro_choi_id = g.ma
                WHERE t.nguoi_dung_id = :uid AND g.trang_thai = 'published'
                ORDER BY g.ngay_tao DESC";
        $stmt = UserModel::db()->prepare($sql);
        $stmt->execute([':uid' => $id]);
        $games = $stmt->fetchAll();

        $this->render('author/page', [
            'author' => $author,
            'games' => $games,
<<<<<<< HEAD
            'isFollowing' => $isFollowing,
            'currentUser' => $currentUser,
            'csrfToken' => $this->csrfToken(),
=======
>>>>>>> origin/main
        ]);
    }

    public function games(array $params) {
        $this->show($params);
    }
<<<<<<< HEAD

    public function follow(array $params) {
        $currentUser = $this->requireAuth();
        $targetId = (int)($params['id'] ?? 0);
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/author/' . $targetId);
        }
        if (!$targetId || $targetId === (int)$currentUser['ma'] || !UserModel::find($targetId)) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Người chơi không tìm thấy.']);
            return;
        }

        if (FollowModel::isFollowing((int)$currentUser['ma'], $targetId)) {
            FollowModel::unfollow((int)$currentUser['ma'], $targetId);
            $_SESSION['success'] = 'Đã bỏ theo dõi người chơi.';
        } else {
            FollowModel::follow((int)$currentUser['ma'], $targetId);
            $_SESSION['success'] = 'Đã theo dõi người chơi. Bạn sẽ nhận thông báo khi họ có cập nhật mới.';
        }
        $this->redirect('/author/' . $targetId);
    }
}
=======
}
>>>>>>> origin/main
