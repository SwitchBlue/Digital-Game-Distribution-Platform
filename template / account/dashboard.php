<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section>
    <h2>Xin chào, <?php echo $escape($user['ten_hien_thi']); ?></h2>
    <p><?php echo $escape($user['email']); ?></p>
    <p><a class="button" href="<?php echo app_url('/games/new'); ?>">Đăng trò chơi mới</a>
       <a href="<?php echo app_url('/orders'); ?>">Đơn hàng</a>
       <a href="<?php echo app_url('/collections'); ?>">Bộ sưu tập</a></p>
    <h3>Trò chơi của tôi</h3>
    <?php if (!$games): ?><p>Bạn chưa đăng trò chơi nào.</p><?php else: ?>
        <ul><?php foreach ($games as $game): ?>
            <li><a href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>"><?php echo $escape($game['tieu_de']); ?></a>
                — <?php echo $escape($game['trang_thai']); ?></li>
        <?php endforeach; ?></ul>
    <?php endif; ?>
    <h3>Thư viện game của tôi</h3>
    <?php if (empty($ownedGames)): ?><p>Bạn chưa nhận trò chơi nào từ giỏ hàng.</p><?php else: ?>
        <div class="grid">
            <?php foreach ($ownedGames as $game): ?>
                <a class="card" href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>">
                    <strong><?php echo $escape($game['tieu_de']); ?></strong>
                    <p>Đã thêm vào thư viện của bạn</p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <h3>Bộ sưu tập</h3>
    <?php if (!$collections): ?><p>Chưa có bộ sưu tập.</p><?php else: ?>
        <ul><?php foreach ($collections as $collection): ?><li><?php echo $escape($collection['tieu_de']); ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
</section>
