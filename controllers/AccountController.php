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
        $this->render('account/dashboard', [
            'user' => $user,
            'games' => $games,
            'ownedGames' => $ownedGames,
            'collections' => $collections,
        ]);
    }
}
