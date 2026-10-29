<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section>
    <h2>Quản trị</h2>
    <nav><a href="<?php echo app_url('/admin/games'); ?>">Trò chơi</a> ·
        <a href="<?php echo app_url('/admin/reports'); ?>">Báo cáo</a> ·
        <a href="<?php echo app_url('/admin/users'); ?>">Người dùng</a></nav>
    <h3>Trò chơi mới</h3>
    <ul><?php foreach ($games as $game): ?><li><?php echo $escape($game['tieu_de']); ?> — <?php echo $escape($game['trang_thai']); ?></li><?php endforeach; ?></ul>
    <h3>Báo cáo đang mở: <?php echo count($reports); ?></h3>
    <h3>Tài khoản mới</h3>
    <ul><?php foreach ($users as $account): ?><li><?php echo $escape($account['ten_hien_thi']); ?> (<?php echo $escape($account['email']); ?>)</li><?php endforeach; ?></ul>
</section>
