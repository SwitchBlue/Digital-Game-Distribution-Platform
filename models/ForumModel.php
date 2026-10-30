<?php
// =============================================================================
// FORUM MODEL - Table: dien_dan
// =============================================================================

class ForumModel extends Model {
    protected static $table = 'dien_dan';

<<<<<<< HEAD
    public static function create(string $title, ?string $description): int {
        return self::insert([
            'loai_dien_dan' => 'platform',
            'tro_choi_id' => null,
            'cuoc_thi_id' => null,
            'tieu_de' => $title,
            'mo_ta' => $description,
            'cong_khai' => true,
        ]);
    }

    public static function getAll(): array {
        return self::all('ngay_tao DESC, ma DESC');
=======
    public static function getAll(): array {
        return self::all();
>>>>>>> origin/main
    }

    public static function findByType(string $type): array {
        return self::findAllBy('loai_dien_dan', $type);
    }
}