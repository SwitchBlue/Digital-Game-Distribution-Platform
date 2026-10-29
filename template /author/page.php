<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section>
    <h2><?php echo $escape($author['ten_hien_thi']); ?></h2>
    <?php if (!empty($author['gioi_thieu'])): ?><p><?php echo nl2br($escape($author['gioi_thieu'])); ?></p><?php endif; ?>
    <h3>Trò chơi</h3>
    <?php if (!$games): ?><p>Chưa có trò chơi được công bố.</p><?php else: ?>
        <ul><?php foreach ($games as $game): ?><li><a href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>"><?php echo $escape($game['tieu_de']); ?></a></li><?php endforeach; ?></ul>
    <?php endif; ?>
</section>
