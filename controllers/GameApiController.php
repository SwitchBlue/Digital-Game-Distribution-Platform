<?php
// =============================================================================
// GAME API CONTROLLER
// =============================================================================

class GameApiController extends Controller {

    public function list() {
        $filters = [];
        if (!empty($_GET['q'])) $filters['q'] = $_GET['q'];
        if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
        if (!empty($_GET['platform'])) $filters['platform'] = $_GET['platform'];

        $limit = min(100, max(1, (int)($_GET['limit'] ?? 50)));
        $offset = max(0, (int)($_GET['offset'] ?? 0));

        $games = GameModel::listPublished($filters, $limit, $offset);
        header('Content-Type: application/json');
        echo json_encode(['data' => $games]);
        exit;
    }

    public function get(array $params) {
        $id = (int)($params['id'] ?? 0);
        $game = GameModel::find($id);
        header('Content-Type: application/json');
        if (!$game || $game['trang_thai'] !== 'published') {
            http_response_code(404);
            echo json_encode(['error' => 'Game not found']);
            exit;
        }
        echo json_encode(['data' => $game]);
        exit;
    }

    public function create() {
        $user = $this->requireAuth();
        $data = $this->requestData();
        $title = trim((string)($data['tieu_de'] ?? ''));
        $slug = trim((string)($data['duong_dan_rut_gon'] ?? ''));

        $shortDescription = trim((string)($data['mo_ta_ngan'] ?? ''));
        if ($title === '' || strlen($title) > 200 || strlen($slug) > 220
            || !preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $slug)
            || strlen($shortDescription) > 500) {
            $this->jsonError('A valid title, slug, and short description are required', 422);
        }
        if (GameModel::findBySlug($slug)) {
            $this->jsonError('This slug is already in use', 409);
        }

        $gameId = GameModel::create([
            'nguoi_tao_id' => $user['ma'],
            'tieu_de' => $title,
            'duong_dan_rut_gon' => $slug,
            'mo_ta_ngan' => $shortDescription,
            'mo_ta' => trim((string)($data['mo_ta'] ?? '')),
            'trang_thai' => 'draft',
        ]);
        GameAuthorModel::add($gameId, (int)$user['ma'], 100);

        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode(['data' => GameModel::find($gameId)]);
        exit;
    }

    public function update(array $params) {
        $user = $this->requireAuth();
        $game = GameModel::find((int)($params['id'] ?? 0));
        if (!$game) {
            $this->jsonError('Game not found', 404);
        }
        $isAuthor = (int)$game['nguoi_tao_id'] === (int)$user['ma'];
        if (!$isAuthor) {
            foreach (GameAuthorModel::listForGame((int)$game['ma']) as $author) {
                if ((int)$author['nguoi_dung_id'] === (int)$user['ma']) {
                    $isAuthor = true;
                    break;
                }
            }
        }
<<<<<<< HEAD
        if (!$isAuthor && !in_array('admin', $this->currentUserRoles(), true)) {
=======
        if (!$isAuthor && !in_array('admin', $_SESSION['user_roles'] ?? [], true)) {
>>>>>>> origin/main
            $this->jsonError('You cannot edit this game', 403);
        }

        $input = $this->requestData();
        $allowed = ['tieu_de', 'duong_dan_rut_gon', 'mo_ta_ngan', 'mo_ta'];
        $data = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $input)) {
                $data[$field] = trim((string)$input[$field]);
            }
        }
        if (array_key_exists('tieu_de', $data) && $data['tieu_de'] === '') {
            $this->jsonError('Title cannot be empty', 422);
        }
        if (array_key_exists('duong_dan_rut_gon', $data) && $data['duong_dan_rut_gon'] === '') {
            $this->jsonError('Slug cannot be empty', 422);
        }
        if ((isset($data['tieu_de']) && strlen($data['tieu_de']) > 200)
            || (isset($data['duong_dan_rut_gon']) && (strlen($data['duong_dan_rut_gon']) > 220
                || !preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $data['duong_dan_rut_gon'])))
            || (isset($data['mo_ta_ngan']) && strlen($data['mo_ta_ngan']) > 500)) {
            $this->jsonError('One or more fields exceed their allowed length or format', 422);
        }
        if (isset($data['duong_dan_rut_gon'])) {
            $existing = GameModel::findBySlug($data['duong_dan_rut_gon']);
            if ($existing && (int)$existing['ma'] !== (int)$game['ma']) {
                $this->jsonError('This slug is already in use', 409);
            }
        }
<<<<<<< HEAD
        $data = array_filter(
            $data,
            static function (string $value, string $field) use ($game): bool {
                return $value !== ($game[$field] ?? null);
            },
            ARRAY_FILTER_USE_BOTH
        );
        if ($data) {
            GameModel::update($game['ma'], $data);
            NotificationModel::notifyGameFollowers(
                (int)$game['ma'],
                (int)$user['ma'],
                'game_updated',
                'Trò chơi đã được cập nhật',
                ($data['tieu_de'] ?? $game['tieu_de']) . ' vừa có thông tin mới.',
                '/games/' . rawurlencode($data['duong_dan_rut_gon'] ?? $game['duong_dan_rut_gon'])
            );
=======
        if ($data) {
            GameModel::update($game['ma'], $data);
>>>>>>> origin/main
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => GameModel::find($game['ma'])]);
        exit;
    }

    private function requestData(): array {
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === 0) {
            $data = json_decode(file_get_contents('php://input'), true);
            return is_array($data) ? $data : [];
        }
        return $_POST;
    }

    private function jsonError(string $message, int $status): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode(['error' => $message]);
        exit;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
