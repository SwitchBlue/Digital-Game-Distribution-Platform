<?php
// =============================================================================
// FORUM CONTROLLER
// =============================================================================

class ForumController extends Controller {

    public function index() {
        $forums = ForumModel::getAll();
        $forumGroups = [];
        foreach ($forums as $forum) {
            $createdAt = (string)($forum['ngay_tao'] ?? '');
            $dateKey = substr($createdAt, 0, 10);
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateKey);
            $groupKey = $date ? $date->format('Y-m-d') : 'unknown';

            if (!isset($forumGroups[$groupKey])) {
                $forumGroups[$groupKey] = [
                    'date' => $date ? $date->format('d/m/Y') : 'Chưa xác định ngày',
                    'forums' => [],
                ];
            }
            $forumGroups[$groupKey]['forums'][] = $forum;
        }

        $this->render('forum/list', [
            'forumGroups' => $forumGroups,
        ]);
    }

    public function showCreate() {
        $this->requireAuth();
        $this->render('forum/create', [
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function create() {
        $this->requireAuth();

        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/forums/new');
        }

        $title = trim((string)($_POST['tieu_de'] ?? ''));
        $description = trim((string)($_POST['mo_ta'] ?? ''));
        if (preg_match('/^.{1,180}$/us', $title) !== 1
            || ($description !== '' && preg_match('/^.{1,1000}$/us', $description) !== 1)) {
            $_SESSION['error'] = 'Vui lòng nhập tên diễn đàn (tối đa 180 ký tự) và mô tả không quá 1.000 ký tự.';
            $this->redirect('/forums/new');
        }

        $forumId = ForumModel::create($title, $description !== '' ? $description : null);
        $_SESSION['success'] = 'Đã tạo diễn đàn thành công.';
        $this->redirect('/forums/' . $forumId);
    }

    public function show(array $params) {
        $id = (int)($params['id'] ?? 0);
        $forum = ForumModel::find($id);
        if (!$forum) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Diễn đàn không tìm thấy.']);
            return;
        }
        $topics = TopicModel::listForForum($id);
        $this->render('forum/detail', [
            'forum' => $forum,
            'topics' => $topics,
        ]);
    }

    public function showCreateTopic(array $params) {
        $this->requireAuth();
        $forumId = (int)($params['id'] ?? 0);
        $forum = ForumModel::find($forumId);
        if (!$forum) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Diễn đàn không tìm thấy.']);
            return;
        }

        $this->render('forum/topic-create', [
            'forum' => $forum,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function showTopic(array $params) {
        $forumId = (int)($params['forumId'] ?? 0);
        $topicId = (int)($params['topicId'] ?? 0);
        $forum = ForumModel::find($forumId);
        $topic = TopicModel::find($topicId);
        if (!$forum || !$topic || (int)$topic['dien_dan_id'] !== $forumId || $topic['trang_thai'] !== 'published') {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Chủ đề thảo luận không tìm thấy.']);
            return;
        }

        $this->render('forum/topic-detail', [
            'forum' => $forum,
            'topic' => $topic,
            'posts' => PostModel::listForTopic($topicId),
            'topicAuthor' => UserModel::find((int)$topic['tac_gia_nguoi_dung_id']),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function createTopic(array $params) {
        $user = $this->requireAuth();
        $forumId = (int)($params['id'] ?? 0);
        $forum = ForumModel::find($forumId);
        if (!$forum) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Diễn đàn không tìm thấy.']);
            return;
        }
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/forums/' . $forumId . '/topics/new');
        }

        $title = trim((string)($_POST['tieu_de'] ?? ''));
        $content = trim((string)($_POST['noi_dung'] ?? ''));
        if (preg_match('/^.{1,220}$/us', $title) !== 1
            || preg_match('/^.{1,10000}$/us', $content) !== 1) {
            $_SESSION['error'] = 'Vui lòng nhập tiêu đề (tối đa 220 ký tự) và nội dung (tối đa 10.000 ký tự).';
            $this->redirect('/forums/' . $forumId . '/topics/new');
        }

        $topicId = TopicModel::create($forumId, (int)$user['ma'], $title, $content);
        $_SESSION['success'] = 'Đã đăng chủ đề thảo luận.';
        $this->redirect('/forums/' . $forumId . '/topics/' . $topicId);
    }

    public function createPost(array $params) {
        $user = $this->requireAuth();
        $forumId = (int)($params['forumId'] ?? 0);
        $topicId = (int)($params['topicId'] ?? 0);
        $topic = TopicModel::find($topicId);
        if (!$topic || (int)$topic['dien_dan_id'] !== $forumId || $topic['trang_thai'] !== 'published') {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Chủ đề thảo luận không tìm thấy.']);
            return;
        }
        if (!empty($topic['da_khoa'])) {
            $_SESSION['error'] = 'Chủ đề này đã khóa bình luận.';
            $this->redirect('/forums/' . $forumId . '/topics/' . $topicId);
        }
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/forums/' . $forumId . '/topics/' . $topicId);
        }

        $content = trim((string)($_POST['noi_dung'] ?? ''));
        if (preg_match('/^.{1,10000}$/us', $content) !== 1) {
            $_SESSION['error'] = 'Vui lòng nhập nội dung bình luận (tối đa 10.000 ký tự).';
            $this->redirect('/forums/' . $forumId . '/topics/' . $topicId);
        }

        PostModel::create($topicId, (int)$user['ma'], $content);
        $_SESSION['success'] = 'Đã đăng bình luận.';
        $this->redirect('/forums/' . $forumId . '/topics/' . $topicId);
    }
}
