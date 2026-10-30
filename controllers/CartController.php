<?php
// =============================================================================
// CART CONTROLLER
// =============================================================================

<<<<<<< HEAD
=======

>>>>>>> origin/main
class CartController extends Controller {

    public function index() {
        $user = $this->requireAuth();
        $cart = CartModel::getOrCreate($user['ma']);
        $items = CartItemModel::listForCart($cart['ma']);
        $this->render('cart/index', [
            'user' => $user,
            'cart' => $cart,
            'items' => $items,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function add() {
        $user = $this->requireAuth();
        $type = $_POST['type'] ?? 'game';
        $gameId = (int)($_POST['game_id'] ?? 0);
        $bundleId = (int)($_POST['bundle_id'] ?? 0);

        if ($type === 'game') {
            $item = GameModel::find($gameId);
            if (!$item || $item['trang_thai'] !== 'published') {
                http_response_code(404);
                exit('Game not found');
            }
        } elseif ($type === 'bundle') {
            $item = BundleModel::find($bundleId);
            if (!$item || $item['trang_thai'] !== 'published') {
                http_response_code(404);
                exit('Bundle not found');
            }
        } else {
            http_response_code(422);
            exit('Invalid cart item type');
        }

        $cart = CartModel::getOrCreate($user['ma']);
        CartItemModel::add($cart['ma'], $type, $gameId ?: null, $bundleId ?: null);

        $this->redirect('/cart');
    }

    public function checkout() {
        $user = $this->requireAuth();
        if (!$this->validateCsrf((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect('/cart');
        }

        $cart = CartModel::getOrCreate((int)$user['ma']);
        $db = Model::db();
        $db->beginTransaction();

        try {
            $itemCount = CartItemModel::countForCart((int)$cart['ma']);
            $games = CartItemModel::listGamesForCheckout((int)$cart['ma']);
            if ($itemCount === 0) {
                $db->rollBack();
                $_SESSION['error'] = 'Giỏ hàng đang trống.';
                $this->redirect('/cart');
            }
            if ($itemCount !== count($games)) {
                $db->rollBack();
                $_SESSION['error'] = 'Giỏ hàng hiện chỉ hỗ trợ thanh toán trò chơi, không hỗ trợ gói game.';
                $this->redirect('/cart');
            }

            $toClaim = [];
            foreach ($games as $game) {
                if ($game['trang_thai'] !== 'published') {
                    $db->rollBack();
                    $_SESSION['error'] = 'Một trò chơi trong giỏ hàng không còn được phát hành. Vui lòng kiểm tra lại giỏ hàng.';
                    $this->redirect('/cart');
                }
                if (!OwnershipModel::has((int)$user['ma'], (int)$game['game_id'])) {
                    $toClaim[] = $game;
                }
            }

            if (!$toClaim) {
                CartItemModel::clear((int)$cart['ma']);
                $db->commit();
                $_SESSION['success'] = 'Bạn đã sở hữu tất cả trò chơi trong giỏ hàng. Chúng có sẵn trong tài khoản của bạn.';
                $this->redirect('/account');
            }

            $currency = $toClaim[0]['ma_tien_te'];
            $subtotal = 0.0;
            foreach ($toClaim as $game) {
                if ($game['ma_tien_te'] !== $currency) {
                    $db->rollBack();
                    $_SESSION['error'] = 'Các trò chơi trong giỏ hàng cần cùng loại tiền tệ mới có thể thanh toán.';
                    $this->redirect('/cart');
                }
                $price = $game['mo_hinh_gia'] === 'free' ? 0.0 : (float)$game['gia_toi_thieu'];
                if ($price < 0) {
                    $db->rollBack();
                    throw new RuntimeException('Invalid negative game price encountered during checkout.');
                }
                $subtotal += $price;
            }
            $subtotal = round($subtotal, 2);

            $orderNumber = 'DEMO-' . strtoupper(bin2hex(random_bytes(6)));
            $orderId = OrderModel::create(
                (int)$user['ma'],
                $orderNumber,
                $currency,
                $subtotal,
                0,
                0,
                $subtotal
            );
            OrderModel::update($orderId, [
                'trang_thai' => 'paid',
                'ngay_thanh_toan' => date('Y-m-d H:i:s'),
            ]);

            $paymentId = PaymentModel::create(
                $orderId,
                'demo',
                'DEMO-' . strtoupper(bin2hex(random_bytes(16))),
                $subtotal,
                $currency
            );
            PaymentModel::updateStatus($paymentId, 'succeeded', 'demo-checkout');

            foreach ($toClaim as $game) {
                $price = $game['mo_hinh_gia'] === 'free' ? 0.0 : (float)$game['gia_toi_thieu'];
                $orderItemId = OrderItemModel::create(
                    $orderId,
                    'game',
                    (int)$game['game_id'],
                    null,
                    null,
                    $game['tieu_de'],
                    1,
                    $price,
                    0,
                    0,
                    $price,
                    $currency
                );
                OwnershipModel::grant(
                    (int)$user['ma'],
                    (int)$game['game_id'],
                    $price > 0 ? 'purchase' : 'free_claim',
                    $orderItemId
                );
            }

            CartItemModel::clear((int)$cart['ma']);
            $db->commit();
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }

        $_SESSION['success'] = 'Thanh toán mô phỏng thành công. Trò chơi đã được thêm vào thư viện của bạn.';
        $this->redirect('/account');
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
