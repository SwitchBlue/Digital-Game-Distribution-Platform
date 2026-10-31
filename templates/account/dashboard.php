<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section>
    <h2>Xin chào, <?php echo $escape($user['ten_hien_thi']); ?></h2>
    <p><?php echo $escape($user['email']); ?></p>
    <p><a class="button" href="<?php echo app_url('/account/profile'); ?>">Thông tin tài khoản</a>
       <a href="<?php echo app_url('/games/new'); ?>">Đăng trò chơi mới</a>
       <a href="<?php echo app_url('/orders'); ?>">Đơn hàng</a>
       <a href="<?php echo app_url('/collections'); ?>">Bộ sưu tập</a>
       <a href="<?php echo app_url('/notifications'); ?>">Thông báo</a></p>
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
                <a class="card library-game" href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>">
                    <?php if (!empty($game['anh_mo_rong'])): ?>
                        <img class="library-game-cover" src="<?php echo $escape(app_media_url($game['anh_mo_rong'])); ?>" alt="<?php echo $escape($game['tieu_de']); ?>" loading="lazy">
                    <?php else: ?>
                        <div class="library-game-cover library-game-cover-empty" aria-hidden="true"><span>✦</span></div>
                    <?php endif; ?>
                    <strong class="library-game-title"><?php echo $escape($game['tieu_de']); ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <h3>Trò chơi đang theo dõi</h3>
    <?php if (!$watchedGames): ?><p>Bạn chưa theo dõi trò chơi nào.</p><?php else: ?>
        <ul><?php foreach ($watchedGames as $watchedGame): ?>
            <li><a href="<?php echo app_url('/games/' . rawurlencode($watchedGame['duong_dan_rut_gon'])); ?>"><?php echo $escape($watchedGame['tieu_de']); ?></a></li>
        <?php endforeach; ?></ul>
    <?php endif; ?>
    <h3>Người chơi đang theo dõi</h3>
    <?php if (!$followedUsers): ?><p>Bạn chưa theo dõi người chơi nào.</p><?php else: ?>
        <ul><?php foreach ($followedUsers as $followedUser): ?>
            <li><a href="<?php echo app_url('/author/' . (int)$followedUser['ma']); ?>"><?php echo $escape($followedUser['ten_hien_thi']); ?></a></li>
        <?php endforeach; ?></ul>
    <?php endif; ?>
    <h3>Bộ sưu tập</h3>
    <?php if (!$collections): ?><p>Chưa có bộ sưu tập.</p><?php else: ?>
        <ul><?php foreach ($collections as $collection): ?><li><?php echo $escape($collection['tieu_de']); ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
</section>
<style>
    .library-game { display: block; padding: 12px; color: var(--ink); }
    .library-game:hover { color: var(--purple-dark); text-decoration: none; }
    .library-game-cover { display: block; width: 100%; aspect-ratio: 16 / 9; margin-bottom: 12px; border-radius: 12px; object-fit: cover; background: #eee8ff; }
    .library-game-cover-empty { display: grid; place-items: center; color: #fff; background: radial-gradient(circle at 30% 20%, #ffad79, transparent 32%), linear-gradient(135deg, #7047eb, #a486f5 55%, #ffc56c); }
    .library-game-cover-empty span { font-size: 48px; text-shadow: 0 3px 12px rgba(49, 26, 111, .2); }
    .library-game-title { display: block; }
</style>
