<?php
// =============================================================================
// FORUM CONTROLLER
// =============================================================================

class ForumController extends Controller {

    public function index() {
        $forums = ForumModel::getAll();
        $this->render('forum/list', [
            'forums' => $forums,
        ]);
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
}
