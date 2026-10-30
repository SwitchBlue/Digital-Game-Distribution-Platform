<?php
// =============================================================================
// REVIEW CONTROLLER
// =============================================================================

<<<<<<< HEAD
=======

>>>>>>> origin/main
class ReviewController extends Controller {

    public function create(array $params) {
        $user = $this->requireAuth();
        $gameId = (int)($params['id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 5);
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $game = GameModel::find($gameId);
        if (!$game || $game['trang_thai'] !== 'published' || !$game['cho_phep_danh_gia']) {
            http_response_code(404);
            echo 'Game not found';
            exit;
        }
        if ($rating < 1 || $rating > 5) {
            http_response_code(422);
            echo 'Rating must be between 1 and 5';
            exit;
        }

        $reviewId = ReviewModel::create($gameId, $user['ma'], $rating, $title, $content);
        NotificationModel::create(
            $user['ma'],
            'review',
            'Bài đánh giá của bạn đã được đăng',
            'Bài đánh giá #' . $reviewId,
            $game ? '/games/' . $game['duong_dan_rut_gon'] : '/games'
        );

        $this->redirect($game ? '/games/' . rawurlencode($game['duong_dan_rut_gon']) : '/games');
    }

    public function list(array $params) {
        $gameId = (int)($params['id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode(ReviewModel::listForGame($gameId));
        exit;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
