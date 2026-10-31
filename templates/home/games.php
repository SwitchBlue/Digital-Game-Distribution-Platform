<?php
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<section class="discovery-hero">
    <div class="hero-copy">
        <span class="eyebrow"><span aria-hidden="true">✦</span> Sân chơi của những ý tưởng độc đáo</span>
        <h2>Khám phá thế giới<br><span>game độc lập</span></h2>
        <p>Gặp gỡ những trò chơi mới lạ, ủng hộ nhà sáng tạo Việt và tìm cuộc phiêu lưu tiếp theo của bạn.</p>
        <a class="button" href="#game-list">Khám phá trò chơi <span aria-hidden="true">↓</span></a>
    </div>
    <div class="hero-art" aria-hidden="true">
        <span class="orbit orbit-one"></span><span class="orbit orbit-two"></span>
        <span class="art-spark spark-one">✦</span><span class="art-spark spark-two">✳</span>
        <span class="art-core"><span>PLAY</span><strong>!</strong></span>
        <span class="art-caption">made with<br>imagination</span>
    </div>
</section>

<section class="discovery-tools" aria-label="Tìm và lọc trò chơi">
    <div class="section-heading">
        <div>
            <span class="eyebrow">Bắt đầu hành trình</span>
            <h2 id="game-list">Trò chơi dành cho bạn</h2>
        </div>
        <a class="button button-coral" href="<?php echo app_url('/games/new'); ?>"><span aria-hidden="true">＋</span> Đăng trò chơi</a>
    </div>
    <form class="game-filters" method="get" action="<?php echo app_url('/games'); ?>">
        <label class="search-field">
            <span class="search-icon" aria-hidden="true">⌕</span>
            <span class="visually-hidden">Tìm kiếm trò chơi</span>
            <input type="search" name="q" value="<?php echo $escape($q); ?>" placeholder="Bạn muốn chơi gì hôm nay?">
        </label>
        <label class="visually-hidden" for="game-category">Thể loại</label>
        <select id="game-category" name="category">
            <option value="">Tất cả thể loại</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo (int)$c['ma']; ?>" <?php echo (string)$selectedCategory === (string)$c['ma'] ? 'selected' : ''; ?>><?php echo $escape($c['ten']); ?></option>
            <?php endforeach; ?>
        </select>
        <label class="visually-hidden" for="game-platform">Nền tảng</label>
        <select id="game-platform" name="platform">
            <option value="">Tất cả nền tảng</option>
            <?php foreach ($platforms as $p): ?>
            <option value="<?php echo (int)$p['ma']; ?>" <?php echo (string)$selectedPlatform === (string)$p['ma'] ? 'selected' : ''; ?>><?php echo $escape($p['ten_nen_tang']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="button">Lọc trò chơi</button>
    </form>
    <?php if ($q): ?>
    <p class="search-summary">Tìm thấy <strong><?php echo (int)$total; ?></strong> trò chơi cho “<?php echo $escape($q); ?>”</p>
    <?php endif; ?>
</section>

<div class="grid game-grid">
    <?php foreach ($games as $g): ?>
    <a class="card game-card" href="<?php echo app_url('/games/' . rawurlencode($g['duong_dan_rut_gon'])); ?>">
        <?php if (!empty($g['anh_mo_rong'])): ?>
        <img class="game-cover" src="<?php echo $escape(app_media_url($g['anh_mo_rong'])); ?>" alt="<?php echo $escape($g['tieu_de']); ?>" loading="lazy" />
        <?php else: ?>
        <div class="game-cover game-cover-empty" aria-hidden="true"><span>✦</span></div>
        <?php endif; ?>
        <div class="game-card-body">
            <h3><?php echo $escape($g['tieu_de']); ?></h3>
            <div class="game-description"><?php echo $escape(substr((string)($g['mo_ta_ngan'] ?? ''), 0, 100)); ?></div>
            <div class="game-tags">
                <?php foreach ($g['the_loai'] as $tl): ?>
                <span class="game-tag">
                    <?php echo $escape($tl['ten']); ?>
                </span>
                <?php endforeach; ?>
                <?php foreach ($g['nen_tang'] as $pt): ?>
                <span class="game-tag game-tag-platform">
                    <?php echo $escape($pt['ten_nen_tang']); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
    <?php if (empty($games)): ?>
    <div class="empty-state"><span aria-hidden="true">☁</span><h3>Chưa tìm thấy trò chơi</h3><p>Thử đổi từ khóa hoặc bộ lọc để khám phá thêm nhé.</p></div>
    <?php endif; ?>
</div>

<nav class="pagination" aria-label="Phân trang">
    <?php if ($page > 1): ?>
    <a class="page-link" href="<?php echo app_url('/games') . '?' . htmlspecialchars(http_build_query(['page' => $page - 1, 'q' => $q, 'category' => $selectedCategory, 'platform' => $selectedPlatform]), ENT_QUOTES, 'UTF-8'); ?>">← Trang trước</a>
    <?php endif; ?>
    <span class="page-count">Trang <?php echo min($page, $totalPages); ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a class="page-link" href="<?php echo app_url('/games') . '?' . htmlspecialchars(http_build_query(['page' => $page + 1, 'q' => $q, 'category' => $selectedCategory, 'platform' => $selectedPlatform]), ENT_QUOTES, 'UTF-8'); ?>">Trang sau →</a>
    <?php endif; ?>
</nav>

<style>
    .discovery-hero {
        position: relative; display: flex; min-height: 330px; align-items: center; justify-content: space-between;
        overflow: hidden; padding: 42px 7%; border-radius: 30px;
        background: linear-gradient(118deg, #292044 0%, #482b8c 56%, #7047eb 100%);
        box-shadow: 0 20px 45px rgba(68, 43, 132, .2);
    }
    .hero-copy { position: relative; z-index: 1; max-width: 570px; }
    .eyebrow { display: inline-block; color: #8d79cf; font-size: 12px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .hero-copy .eyebrow { padding: 7px 11px; border: 1px solid rgba(255,255,255,.2); border-radius: 99px; color: #e7dbff; background: rgba(255,255,255,.08); letter-spacing: .03em; text-transform: none; }
    .hero-copy h2 { margin: 17px 0 12px; color: #fff; font-size: clamp(36px, 5vw, 54px); line-height: 1.04; letter-spacing: -.055em; }
    .hero-copy h2 span { color: var(--sun); }
    .hero-copy p { max-width: 440px; margin: 0 0 22px; color: #e2dbee; }
    .hero-copy .button { color: #35265e; background: var(--sun); box-shadow: none; }
    .hero-copy .button:hover { background: #fff0bd; }
    .hero-art { position: relative; flex: 0 0 275px; height: 245px; margin-right: 1%; }
    .orbit { position: absolute; top: 50%; left: 50%; border: 1px solid rgba(255,255,255,.2); border-radius: 50%; transform: translate(-50%,-50%) rotate(-24deg); }
    .orbit-one { width: 265px; height: 132px; }
    .orbit-two { width: 225px; height: 195px; transform: translate(-50%,-50%) rotate(53deg); }
    .art-core { position: absolute; top: 50%; left: 50%; display: flex; width: 142px; height: 142px; align-items: center; justify-content: center; gap: 2px; border: 8px solid rgba(255,255,255,.2); border-radius: 42px; color: #3a2869; background: linear-gradient(145deg, #b7f2d5, #82dfc4); box-shadow: 0 20px 35px rgba(19, 10, 49, .28); font-weight: 900; transform: translate(-50%,-50%) rotate(-8deg); }
    .art-core span { font-size: 16px; letter-spacing: .07em; }
    .art-core strong { color: #ff715b; font-size: 38px; line-height: 1; }
    .art-spark { position: absolute; z-index: 1; color: var(--sun); }
    .spark-one { top: 17px; right: 28px; font-size: 34px; }
    .spark-two { bottom: 24px; left: 6px; color: #ff9a86; font-size: 30px; }
    .art-caption { position: absolute; right: -14px; bottom: 19px; color: #d7cdf2; font-size: 10px; line-height: 1.3; letter-spacing: .13em; text-transform: uppercase; transform: rotate(-8deg); }
    .discovery-tools { margin: 42px 0 24px; }
    .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 19px; }
    .section-heading h2 { margin: 5px 0 0; font-size: 28px; }
    .button-coral { background: var(--coral); box-shadow: 0 7px 15px rgba(255, 113, 91, .2); }
    .button-coral:hover { background: #ec5d49; }
    .game-filters { display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid var(--line); border-radius: 18px; background: #fff; box-shadow: 0 7px 22px rgba(48,34,86,.04); }
    .game-filters select { min-width: 150px; }
    .game-filters .button { flex: 0 0 auto; white-space: nowrap; }
    .search-field { display: flex; flex: 1 1 260px; align-items: center; gap: 8px; margin: 0; padding-left: 10px; border: 1px solid #ded9e8; border-radius: 12px; }
    .search-field:focus-within { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(112,71,235,.1); }
    .search-field input { width: 100%; border: 0; outline: 0; box-shadow: none; }
    .search-icon { color: #827a95; font-size: 25px; line-height: 1; }
    .search-summary { margin: 13px 4px; font-size: 14px; }
    .game-grid { grid-template-columns: repeat(auto-fill, minmax(245px, 1fr)); }
    .game-card { display: block; overflow: hidden; padding: 10px; color: inherit; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .game-card:hover { border-color: rgba(112,71,235,.25); color: inherit; box-shadow: var(--shadow); text-decoration: none; transform: translateY(-5px); }
    .game-cover { display: block; width: 100%; height: 168px; object-fit: cover; border-radius: 13px; background: #eee8ff; }
    .game-cover-empty { display: grid; place-items: center; color: #fff; background: radial-gradient(circle at 30% 20%, #ffad79, transparent 32%), linear-gradient(135deg, #7047eb, #a486f5 55%, #ffc56c); }
    .game-cover-empty span { font-size: 48px; text-shadow: 0 3px 12px rgba(49,26,111,.2); }
    .game-card-body { padding: 14px 7px 8px; }
    .game-card h3 { overflow: hidden; margin: 0 0 7px; font-size: 18px; text-overflow: ellipsis; white-space: nowrap; }
    .game-description { min-height: 42px; margin-bottom: 12px; color: var(--muted); font-size: 13px; line-height: 1.55; }
    .game-tags { display: flex; gap: 6px; flex-wrap: wrap; }
    .game-tag { padding: 4px 9px; border-radius: 99px; color: #5b3ac0; background: #f0ebff; font-size: 11px; font-weight: 700; }
    .game-tag-platform { color: #287353; background: #e2f8ed; }
    .empty-state { grid-column: 1 / -1; padding: 48px 20px; border: 1px dashed #d8cfee; border-radius: 20px; text-align: center; background: rgba(255,255,255,.65); }
    .empty-state > span { color: #9c87dc; font-size: 40px; }
    .empty-state h3 { margin: 6px 0; }
    .empty-state p { margin: 0; }
    .pagination { display: flex; align-items: center; justify-content: center; gap: 15px; margin-top: 32px; }
    .pagination .page-link { padding: 9px 13px; border: 1px solid var(--line); border-radius: 11px; background: #fff; font-size: 14px; font-weight: 700; }
    .page-count { color: var(--muted); font-size: 14px; }
    .visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; clip-path: inset(50%); }
    @media (max-width: 700px) {
        .discovery-hero { min-height: 0; padding: 32px 25px; }
        .hero-art { position: absolute; right: -110px; bottom: -42px; opacity: .3; transform: scale(.82); }
        .hero-copy { max-width: 100%; }
        .hero-copy p { max-width: 390px; }
        .game-filters { align-items: stretch; flex-direction: column; }
        .game-filters .search-field { flex: 0 0 auto; margin: 0; }
        .game-filters select, .game-filters .button { width: 100%; }
        .section-heading { align-items: flex-start; flex-direction: column; }
    }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
    }
</style>
