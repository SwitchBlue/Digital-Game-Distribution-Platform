<?php
// =============================================================================
// ORDER CONTROLLER
// =============================================================================

class OrderController extends Controller {

    public function index() {
        $user = $this->requireAuth();
        $orders = OrderModel::listForUser($user['ma']);
        $this->render('order/list', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }
}
