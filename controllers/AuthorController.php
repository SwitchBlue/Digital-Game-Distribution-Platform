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
        ]);
    }

    public function games(array $params) {
        $this->show($params);
    }
}
