<?php
// =============================================================================
// GAME CONTROLLER
// =============================================================================


class GameController extends Controller {

    public function index() {
        $q = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $platform = $_GET['platform'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 24;
        $offset = ($page - 1) * $limit;

        $filters = [];
        if ($q) $filters['q'] = $q;
        if ($category) $filters['category'] = $category;
        if ($platform) $filters['platform'] = $platform;

        $games = GameModel::listPublished($filters, $limit, $offset);
        $total = GameModel::countPublished($filters);
        $totalPages = max(1, (int)ceil($total / $limit));

        $categories = CategoryModel::getAll();
        $platforms = PlatformModel::getAll();

        $this->render('home/games', [
            'games' => $games,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'categories' => $categories,
            'platforms' => $platforms,
            'q' => $q,
            'selectedCategory' => $category,
            'selectedPlatform' => $platform,
        ]);
    }

    public function show(array $params) {
        $slug = $params['slug'] ?? '';
        $game = GameModel::findBySlug($slug);
        $user = $this->currentUser();
        if ($game && $game['trang_thai'] !== 'published'
            && (!$user || ((int)$game['nguoi_tao_id'] !== (int)$user['ma']
                && !in_array('admin', $_SESSION['user_roles'] ?? [], true)))) {
            $game = null;
        }
        if (!$game) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Trò chơi không tìm thấy.']);
            return;
        }

        $authors = GameAuthorModel::listForGame($game['ma']);
        $media = GameMediaModel::listForGame($game['ma']);
        $categories = GameCategoryModel::listForGame($game['ma']);
        $platforms = GamePlatformModel::listForGame($game['ma']);
        $reviews = ReviewModel::listForGame($game['ma']);
        $rating = ReviewModel::averageRating($game['ma']);
        $comments = GameCommentModel::listForGame($game['ma']);
        $devlogs = DevlogModel::listForGame($game['ma']);

        $isAuthor = false;
        if ($user) {
            foreach ($authors as $a) {
                if ($a['nguoi_dung_id'] == $user['ma']) {
                    $isAuthor = true;
                    break;
                }
            }
        }

        $this->render('game/detail', [
            'game' => $game,
            'authors' => $authors,
            'media' => $media,
            'categories' => $categories,
            'platforms' => $platforms,
            'reviews' => $reviews,
            'rating' => $rating,
            'comments' => $comments,
            'devlogs' => $devlogs,
            'isAuthor' => $isAuthor,
        ]);
    }

    public function showCreate() {
        $this->requireAuth();
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $this->render('game/create', [
            'error' => $error,
            'categories' => CategoryModel::getAll(),
            'platforms' => PlatformModel::getAll(),
        ]);
    }

    public function create() {
        $user = $this->requireAuth();
        $title = trim((string)($_POST['tieu_de'] ?? ''));
        $slug = trim((string)($_POST['duong_dan_rut_gon'] ?? ''));
        $shortDescription = trim((string)($_POST['mo_ta_ngan'] ?? ''));
        $description = trim((string)($_POST['mo_ta'] ?? ''));
        $pricingModel = (string)($_POST['mo_hinh_gia'] ?? 'free');
        $minimumPriceInput = trim((string)($_POST['gia_toi_thieu'] ?? '0'));
        $allowedPricingModels = ['free', 'paid', 'pay_what_you_want', 'donation'];
        $validPrice = preg_match('/^\d{1,10}(?:\.\d{1,2})?$/', $minimumPriceInput) === 1;
        $minimumPrice = $validPrice ? (float)$minimumPriceInput : 0.0;
        $categoryIds = array_values(array_unique(array_filter(
            array_map('intval', (array)($_POST['categories'] ?? [])),
            static function (int $id): bool { return $id > 0; }
        )));
        $platformIds = array_values(array_unique(array_filter(
            array_map('intval', (array)($_POST['platforms'] ?? [])),
            static function (int $id): bool { return $id > 0; }
        )));

        if ($title === '' || strlen($title) > 200 || $slug === '' || strlen($slug) > 220
            || !preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $slug)
            || strlen($shortDescription) > 500
            || !in_array($pricingModel, $allowedPricingModels, true)
            || !$validPrice
            || ($pricingModel === 'paid' && $minimumPrice <= 0)) {
            $_SESSION['error'] = 'Vui lòng nhập tiêu đề, slug hợp lệ, mô tả ngắn không quá 500 ký tự và mức giá hợp lệ. Giá trò chơi trả phí phải lớn hơn 0.';
            $this->redirect('/games/new');
        }
        if (GameModel::findBySlug($slug)) {
            $_SESSION['error'] = 'Slug này đã được sử dụng.';
            $this->redirect('/games/new');
        }
        foreach ($categoryIds as $categoryId) {
            if (!CategoryModel::find($categoryId)) {
                $_SESSION['error'] = 'Thể loại không hợp lệ.';
                $this->redirect('/games/new');
            }
        }
        foreach ($platformIds as $platformId) {
            if (!PlatformModel::find($platformId)) {
                $_SESSION['error'] = 'Nền tảng không hợp lệ.';
                $this->redirect('/games/new');
            }
        }

        $coverUpload = $this->storeCoverUpload($_FILES['cover_image'] ?? null);
        if (isset($coverUpload['error'])) {
            $_SESSION['error'] = $coverUpload['error'];
            $this->redirect('/games/new');
        }

        $db = Model::db();
        $db->beginTransaction();
        try {
            $gameId = GameModel::create([
                'nguoi_tao_id' => $user['ma'],
                'tieu_de' => $title,
                'duong_dan_rut_gon' => $slug,
                'mo_ta_ngan' => $shortDescription,
                'mo_ta' => $description,
                'mo_hinh_gia' => $pricingModel,
                'gia_toi_thieu' => $pricingModel === 'free' ? 0 : $minimumPrice,
                'trang_thai' => 'published',
                'ngay_cong_bo' => date('Y-m-d H:i:s'),
            ]);
            GameAuthorModel::add((int)$gameId, (int)$user['ma'], 100);
            GameCategoryModel::setForGame((int)$gameId, $categoryIds);
            GamePlatformModel::setForGame((int)$gameId, $platformIds);
            if ($coverUpload !== null) {
                GameMediaModel::add(
                    (int)$gameId,
                    'cover',
                    app_url('/public/uploads/game-covers/' . $coverUpload['filename']),
                    '',
                    $title
                );
            }
            $db->commit();
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ($coverUpload !== null && is_file($coverUpload['path']) && !unlink($coverUpload['path'])) {
                error_log('Unable to remove uploaded cover after failed game creation: ' . $coverUpload['path']);
            }
            throw $exception;
        }

        $this->redirect('/games/' . rawurlencode($slug));
    }

    private function storeCoverUpload(?array $file): ?array {
        if ($file === null || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['error' => 'Không thể tải ảnh lên. Vui lòng chọn lại ảnh (tối đa 5 MB).'];
        }
        if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Tệp ảnh tải lên không hợp lệ.'];
        }
        if ((int)$file['size'] <= 0 || (int)$file['size'] > 5 * 1024 * 1024) {
            return ['error' => 'Ảnh bìa phải có dung lượng từ 1 byte đến 5 MB.'];
        }

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        if (!isset($extensions[$mimeType]) || @getimagesize($file['tmp_name']) === false) {
            return ['error' => 'Định dạng ảnh không hợp lệ. Hãy dùng JPG, PNG, WebP hoặc GIF.'];
        }

        $directory = PUBLIC_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'game-covers';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            error_log('Unable to create game cover upload directory: ' . $directory);
            return ['error' => 'Máy chủ chưa thể lưu ảnh. Vui lòng thử lại sau.'];
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        $path = $directory . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            error_log('Unable to move uploaded game cover to: ' . $path);
            return ['error' => 'Không thể lưu ảnh bìa. Vui lòng thử lại.'];
        }

        return ['filename' => $filename, 'path' => $path];
    }
}
