<?php
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$playerAvatarUrl = static function (array $player): string {
    return !empty($player['url_anh_dai_dien'])
        ? (string)$player['url_anh_dai_dien']
        : app_url('/avatars/' . (int)$player['nguoi_dung_id']);
};
$backgroundImage = null;
foreach ($media as $item) {
    if ($item['loai_media'] === 'cover') {
        $backgroundImage = $item['url_media'];
        break;
    }
    if ($item['loai_media'] === 'thumbnail' && $backgroundImage === null) {
        $backgroundImage = $item['url_media'];
    }
}
?>
<article class="game-detail">
    <?php if ($backgroundImage !== null): ?>
        <figure class="game-detail-cover">
            <img src="<?php echo $escape(app_media_url($backgroundImage)); ?>" alt="<?php echo $escape($game['tieu_de']); ?>">
        </figure>
    <?php endif; ?>
    <p><a href="<?php echo app_url('/games'); ?>">← Tất cả trò chơi</a></p>
    <h2><?php echo $escape($game['tieu_de']); ?></h2>
    <?php if (!empty($game['mo_ta_ngan'])): ?><p><?php echo $escape($game['mo_ta_ngan']); ?></p><?php endif; ?>
    <p>Giá: <?php echo $game['mo_hinh_gia'] === 'free' ? 'Miễn phí' : $escape(number_format((float)$game['gia_toi_thieu'], 2) . ' ' . $game['ma_tien_te']); ?></p>
    <?php if (!empty($_SESSION['user'])): ?>
        <form method="post" action="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon']) . '/watch'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
            <button class="button button-follow" type="submit"><?php echo $isWatching ? 'Bỏ theo dõi trò chơi' : 'Theo dõi trò chơi'; ?></button>
        </form>
        <form method="post" action="<?php echo app_url('/cart/add'); ?>">
            <input type="hidden" name="type" value="game">
            <input type="hidden" name="game_id" value="<?php echo (int)$game['ma']; ?>">
            <button class="button" type="submit">Thêm vào giỏ hàng</button>
        </form>
    <?php else: ?>
        <p><a href="<?php echo app_url('/auth/login'); ?>">Đăng nhập</a> để theo dõi trò chơi và nhận thông báo cập nhật.</p>
    <?php endif; ?>
    <div class="game-taxonomy">
        <section class="game-taxonomy-group">
            <h3>Thể loại</h3>
            <?php if ($categories): ?>
                <ul><?php foreach ($categories as $category): ?><li><?php echo $escape($category['ten']); ?></li><?php endforeach; ?></ul>
            <?php else: ?>
                <p>Chưa có thể loại.</p>
            <?php endif; ?>
        </section>
        <section class="game-taxonomy-group">
            <h3>Nền tảng</h3>
            <?php if ($platforms): ?>
                <ul><?php foreach ($platforms as $platform): ?><li><?php echo $escape($platform['ten_nen_tang']); ?></li><?php endforeach; ?></ul>
            <?php else: ?>
                <p>Chưa có nền tảng.</p>
            <?php endif; ?>
        </section>
    </div>
    <?php foreach ($media as $item): ?>
        <?php if (in_array($item['loai_media'], ['cover', 'thumbnail', 'screenshot'], true) && $item['url_media'] !== $backgroundImage): ?>
            <figure><img src="<?php echo $escape(app_media_url($item['url_media'])); ?>" alt="<?php echo $escape($item['van_ban_thay_the'] ?? $game['tieu_de']); ?>" loading="lazy"></figure>
        <?php elseif ($item['loai_media'] === 'trailer' || $item['loai_media'] === 'external_link'): ?>
            <p><a href="<?php echo $escape($item['url_media']); ?>" rel="noopener noreferrer"><?php echo $escape($item['chu_thich'] ?: 'Liên kết ngoài'); ?></a></p>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!empty($game['mo_ta'])): ?><section><h3>Giới thiệu</h3><div><?php echo nl2br($escape($game['mo_ta'])); ?></div></section><?php endif; ?>
    <section>
        <h3>Tác giả</h3>
        <?php if ($authors): ?>
            <ul><?php foreach ($authors as $author): ?>
                <li><a href="<?php echo app_url('/author/' . (int)$author['nguoi_dung_id']); ?>"><?php echo $escape($author['ten_hien_thi']); ?></a></li>
            <?php endforeach; ?></ul>
        <?php else: ?>
            <p><?php echo $escape($game['tieu_de']); ?></p>
        <?php endif; ?>
    </section>
    <section>
        <h3>Đánh giá (<?php echo (int)($rating['count'] ?? 0); ?>)</h3>
        <?php if ($rating['avg_rating'] !== null): ?>
            <?php $averageRating = max(0, min(5, (float)$rating['avg_rating'])); ?>
            <p>Điểm trung bình:
                <span class="rating-stars" role="img" aria-label="Điểm trung bình <?php echo number_format($averageRating, 1); ?> trên 5 sao"><?php for ($star = 1; $star <= 5; $star++): ?>
                    <?php $starFill = max(0, min(100, ($averageRating - ($star - 1)) * 100)); ?>
                    <span class="rating-star" style="--star-fill:<?php echo $starFill; ?>%">★</span>
                <?php endfor; ?></span>
            </p>
        <?php endif; ?>
        <?php foreach ($reviews as $review): ?>
            <article class="card player-feedback">
                <a class="feedback-avatar-link" href="<?php echo app_url('/author/' . (int)$review['nguoi_dung_id']); ?>" aria-label="Xem hồ sơ của <?php echo $escape($review['ten_hien_thi']); ?>">
                    <img class="feedback-avatar" src="<?php echo $escape($playerAvatarUrl($review)); ?>" alt="">
                </a>
                <div class="feedback-body">
                    <div class="feedback-heading">
                        <strong><a href="<?php echo app_url('/author/' . (int)$review['nguoi_dung_id']); ?>"><?php echo $escape($review['ten_hien_thi']); ?></a></strong>
                        <?php $reviewRating = max(0, min(5, (int)$review['diem_danh_gia'])); ?>
                        <span class="rating-stars" role="img" aria-label="<?php echo $reviewRating; ?> trên 5 sao"><?php for ($star = 1; $star <= 5; $star++): ?>
                            <span class="rating-star" style="--star-fill:<?php echo $star <= $reviewRating ? '100' : '0'; ?>%">★</span>
                        <?php endfor; ?></span>
                    </div>
                    <?php if ($review['tieu_de']): ?><h4><?php echo $escape($review['tieu_de']); ?></h4><?php endif; ?>
                    <p><?php echo nl2br($escape($review['noi_dung_danh_gia'] ?? '')); ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!empty($_SESSION['user']) && $game['cho_phep_danh_gia']): ?>
            <form method="post" action="<?php echo app_url('/api/games/' . (int)$game['ma'] . '/reviews'); ?>" class="card">
                <h4>Viết đánh giá</h4>
                <label for="rating">Điểm</label>
                <select id="rating" name="rating"><?php for ($score = 5; $score >= 1; $score--): ?><option value="<?php echo $score; ?>"><?php echo $score; ?>/5</option><?php endfor; ?></select>
                <label for="review-title">Tiêu đề</label><input id="review-title" name="title" maxlength="200">
                <label for="review-content">Nội dung</label><textarea id="review-content" name="content" rows="4" style="width:100%"></textarea>
                <button class="button" type="submit">Gửi đánh giá</button>
            </form>
        <?php endif; ?>
    </section>
    <?php if ($comments): ?>
        <section><h3>Bình luận</h3>
            <?php foreach ($comments as $comment): ?>
                <article class="card player-feedback">
                    <a class="feedback-avatar-link" href="<?php echo app_url('/author/' . (int)$comment['nguoi_dung_id']); ?>" aria-label="Xem hồ sơ của <?php echo $escape($comment['ten_hien_thi']); ?>">
                        <img class="feedback-avatar" src="<?php echo $escape($playerAvatarUrl($comment)); ?>" alt="">
                    </a>
                    <div class="feedback-body">
                        <strong><a href="<?php echo app_url('/author/' . (int)$comment['nguoi_dung_id']); ?>"><?php echo $escape($comment['ten_hien_thi']); ?></a></strong>
                        <p><?php echo nl2br($escape($comment['noi_dung'])); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <?php if ($devlogs): ?>
        <section><h3>Nhật ký phát triển</h3>
            <?php foreach ($devlogs as $post): ?><article class="card" style="margin:10px 0"><h4><?php echo $escape($post['tieu_de']); ?></h4><p><?php echo nl2br($escape($post['noi_dung'])); ?></p></article><?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>
<style>
    .player-feedback { display: flex; align-items: flex-start; gap: 14px; margin: 10px 0; }
    .button-follow { margin: 0 0 10px; color: var(--purple-dark); background: #e8e1fb; box-shadow: none; }
    .button-follow:hover { color: #fff; background: var(--purple-dark); }
    .feedback-avatar-link { display: block; flex: 0 0 44px; border-radius: 50%; }
    .feedback-avatar { display: block; width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
    .feedback-body { min-width: 0; flex: 1; }
    .feedback-heading { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
    .feedback-body > p { margin-bottom: 0; }
    .rating-stars { display: inline-flex; gap: 2px; vertical-align: middle; }
    .rating-star { color: transparent; background: linear-gradient(90deg, #f5b301 var(--star-fill), #d4d1da var(--star-fill)); background-clip: text; -webkit-background-clip: text; font-size: 20px; line-height: 1; }
    .game-detail-cover { height: clamp(220px, 40vw, 460px); margin: 0 0 24px; overflow: hidden; border-radius: 20px; background: #eee8ff; }
    .game-detail-cover img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .game-taxonomy { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin: 20px 0; }
    .game-taxonomy-group { padding: 16px; border: 1px solid var(--line); border-radius: 16px; background: rgba(255, 255, 255, .75); }
    .game-taxonomy-group h3 { margin: 0 0 12px; font-size: 17px; }
    .game-taxonomy-group ul { display: flex; flex-wrap: wrap; gap: 8px; padding: 0; margin: 0; list-style: none; }
    .game-taxonomy-group li { padding: 5px 11px; border-radius: 999px; background: #f0ebff; color: var(--purple-dark); font-size: 14px; font-weight: 650; }
    .game-taxonomy-group p { margin: 0; }
</style>
