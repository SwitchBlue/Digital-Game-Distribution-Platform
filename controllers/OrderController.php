<?php
// =============================================================================
// ORDER CONTROLLER
// =============================================================================

class OrderController extends Controller {

<<<<<<< HEAD
=======
    
>>>>>>> origin/main
    public function index() {
        $user = $this->requireAuth();
        $orders = OrderModel::listForUser($user['ma']);
        $this->render('order/list', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
