<?php
// =============================================================================
// JAM CONTROLLER
// =============================================================================

class JamController extends Controller {

<<<<<<< HEAD
=======
    
>>>>>>> origin/main
    public function index() {
        $jams = JamModel::getAll();
        $this->render('jam/list', [
            'jams' => $jams,
        ]);
    }

    public function show(array $params) {
        $slug = $params['slug'] ?? '';
        $jam = JamModel::findBySlug($slug);
        if (!$jam) {
            http_response_code(404);
            $this->render('errors/404', ['message' => 'Cuộc thi không tìm thấy.']);
            return;
        }
        $entries = JamEntryModel::listForJam($jam['ma']);
        $this->render('jam/detail', [
            'jam' => $jam,
            'entries' => $entries,
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
