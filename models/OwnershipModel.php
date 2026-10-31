<?php
// =============================================================================
// OWNERSHIP MODEL - Table: quyen_so_huu_tro_choi
// =============================================================================

class OwnershipModel extends Model {
    protected static $table = 'quyen_so_huu_tro_choi';

    public static function grant(int $userId, int $gameId, string $type, ?int $orderItemId = null): int {
        $sql = "INSERT INTO " . self::$table . "
                    (nguoi_dung_id, tro_choi_id, chi_tiet_don_hang_id, loai_nhan_quyen)
                VALUES (:user_id, :game_id, :order_item_id, :type)
                ON DUPLICATE KEY UPDATE
                    chi_tiet_don_hang_id = VALUES(chi_tiet_don_hang_id),
                    loai_nhan_quyen = VALUES(loai_nhan_quyen),
                    trang_thai = 'active',
                    ngay_thu_hoi = NULL";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':game_id' => $gameId,
            ':order_item_id' => $orderItemId,
            ':type' => $type,
        ]);
        return (int)self::db()->lastInsertId();
    }

    public static function has(int $userId, int $gameId): bool {
        $row = self::db()->prepare("SELECT 1 FROM " . self::$table . " WHERE nguoi_dung_id = :uid AND tro_choi_id = :gid AND trang_thai = 'active' LIMIT 1");
        $row->execute([':uid' => $userId, ':gid' => $gameId]);
        return $row->fetch() !== false;
    }

    public static function listForUser(int $userId): array {
        $sql = "SELECT g.*, o.loai_nhan_quyen, o.ngay_nhan_quyen,
                       (SELECT m.url_media
                        FROM media_tro_choi m
                        WHERE m.tro_choi_id = g.ma
                          AND m.loai_media IN ('cover', 'thumbnail')
                        ORDER BY CASE m.loai_media WHEN 'cover' THEN 0 ELSE 1 END, m.thu_tu_sap_xep, m.ma
                        LIMIT 1) AS anh_mo_rong
                FROM " . self::$table . " o
                INNER JOIN tro_choi g ON g.ma = o.tro_choi_id
                WHERE o.nguoi_dung_id = :uid
                  AND o.trang_thai = 'active'
                  AND g.trang_thai = 'published'
                ORDER BY o.ngay_nhan_quyen DESC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
}