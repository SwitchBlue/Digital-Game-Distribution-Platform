<?php
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$total = 0.0;
foreach ($items as $item) {
    if ($item['loai_muc'] === 'game' && $item['mo_hinh_gia'] === 'free') {
        continue;
    }
    $total += (float)($item['gia'] ?? 0);
}
?>
<section class="cart-page">
    <div class="cart-heading">
        <div><span class="eyebrow">Sẵn sàng cho cuộc phiêu lưu tiếp theo?</span><h2>Giỏ hàng của bạn</h2></div>
        <a class="cart-back-link" href="<?php echo app_url('/games'); ?>">← Tiếp tục khám phá</a>
    </div>
    <?php if (!$items): ?>
        <div class="card cart-empty"><span aria-hidden="true">🛍</span><h3>Giỏ hàng đang trống</h3><p>Thêm trò chơi bạn yêu thích, rồi nhận chúng vào thư viện của bạn.</p><a class="button" href="<?php echo app_url('/games'); ?>">Khám phá trò chơi</a></div>
    <?php else: ?>
        <div class="cart-columns">
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                    <article class="card cart-item">
                        <div class="cart-item-icon" aria-hidden="true"><?php echo $item['loai_muc'] === 'game' ? '🎮' : '🎁'; ?></div>
                        <div class="cart-item-copy">
                            <h3><?php echo $escape($item['tieu_de'] ?? 'Sản phẩm không còn tồn tại'); ?></h3>
                            <span><?php echo $item['loai_muc'] === 'game' ? 'Trò chơi' : 'Gói game'; ?></span>
                        </div>
                        <strong class="cart-item-price">
                            <?php if ($item['loai_muc'] === 'game' && $item['mo_hinh_gia'] === 'free'): ?>
                                Miễn phí
                            <?php else: ?>
                                <?php echo $escape(number_format((float)($item['gia'] ?? 0), 2) . ' ' . ($item['ma_tien_te'] ?? $cart['ma_tien_te'])); ?>
                            <?php endif; ?>
                        </strong>
                    </article>
                <?php endforeach; ?>
            </div>
            <aside class="card cart-summary">
                <h3>Tóm tắt đơn hàng</h3>
                <div class="cart-total-row"><span><?php echo count($items); ?> sản phẩm</span><strong><?php echo $escape(number_format($total, 2) . ' ' . $cart['ma_tien_te']); ?></strong></div>
                <p class="demo-payment-note">Đây là thanh toán mô phỏng: không thu tiền thật. Sau khi xác nhận, các trò chơi sẽ được thêm vào thư viện tài khoản của bạn.</p>
                <form method="post" action="<?php echo app_url('/cart/checkout'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
                    <button class="button checkout-button" type="submit">Thanh toán mô phỏng</button>
                </form>
                <a class="order-link" href="<?php echo app_url('/orders'); ?>">Xem đơn hàng của tôi</a>
            </aside>
        </div>
    <?php endif; ?>
</section>
<style>
    .cart-heading { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
    .cart-heading h2 { margin: 6px 0 0; }
    .cart-back-link { font-size: 14px; font-weight: 700; }
    .cart-columns { display: grid; grid-template-columns: minmax(0, 1fr) 330px; align-items: start; gap: 22px; }
    .cart-items { display: grid; gap: 12px; }
    .cart-item { display: flex; align-items: center; gap: 15px; padding: 16px; }
    .cart-item-icon { display: grid; width: 52px; height: 52px; flex: 0 0 52px; place-items: center; border-radius: 16px; background: #f0ebff; font-size: 23px; }
    .cart-item-copy { flex: 1; min-width: 0; }
    .cart-item-copy h3 { overflow: hidden; margin: 0 0 3px; font-size: 16px; text-overflow: ellipsis; white-space: nowrap; }
    .cart-item-copy span { color: var(--muted); font-size: 13px; }
    .cart-item-price { color: var(--ink); font-size: 14px; white-space: nowrap; }
    .cart-summary { position: sticky; top: 20px; }
    .cart-summary h3 { margin: 0 0 18px; font-size: 19px; }
    .cart-total-row { display: flex; justify-content: space-between; gap: 10px; padding: 14px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); font-size: 14px; }
    .demo-payment-note { margin: 15px 0; padding: 12px; border-radius: 12px; color: #65532a; background: #fff7da; font-size: 13px; }
    .checkout-button { width: 100%; }
    .order-link { display: block; margin-top: 14px; text-align: center; font-size: 13px; font-weight: 700; }
    .cart-empty { padding: 52px 22px; text-align: center; }
    .cart-empty > span { font-size: 40px; }
    .cart-empty h3 { margin: 6px 0; }
    .cart-empty p { margin: 0 0 20px; }
    @media (max-width: 760px) {
        .cart-heading { align-items: flex-start; flex-direction: column; }
        .cart-columns { grid-template-columns: 1fr; }
        .cart-summary { position: static; }
        .cart-item { gap: 10px; }
        .cart-item-icon { width: 42px; height: 42px; flex-basis: 42px; }
        .cart-item-price { font-size: 12px; }
    }
</style>
