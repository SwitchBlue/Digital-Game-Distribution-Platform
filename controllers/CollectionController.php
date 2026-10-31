<?php
// =============================================================================
// COLLECTION CONTROLLER
// =============================================================================

class CollectionController extends Controller {

    public function index() {
        $user = $this->requireAuth();
        $collections = CollectionModel::listForOwner($user['ma']);
        $this->render('collection/list', [
            'user' => $user,
            'collections' => $collections,
        ]);
    }
}
