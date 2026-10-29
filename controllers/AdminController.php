<?php
// =============================================================================
// ADMIN CONTROLLER
// =============================================================================

class AdminController extends Controller {
    
    public function dashboard() {
        $user = $this->requireRole('admin');
        $games = GameModel::all('ngay_tao DESC LIMIT 20');
        $reports = ReportModel::listOpen();
        $users = UserModel::all('ngay_tao DESC LIMIT 20');
        $this->render('admin/dashboard', [
            'user' => $user,
            'games' => $games,
            'reports' => $reports,
            'users' => $users,
        ]);
    }

    public function games() {
        $user = $this->requireRole('admin');
        $games = GameModel::all('ngay_tao DESC LIMIT 100');
        $this->render('admin/games', [
            'user' => $user,
            'games' => $games,
        ]);
    }

    public function reports() {
        $user = $this->requireRole('admin');
        $reports = ReportModel::listOpen();
        $this->render('admin/reports', [
            'user' => $user,
            'reports' => $reports,
        ]);
    }

    public function users() {
        $user = $this->requireRole('admin');
        $users = UserModel::all('ngay_tao DESC LIMIT 100');
        $this->render('admin/users', [
            'user' => $user,
            'users' => $users,
        ]);
    }
}
