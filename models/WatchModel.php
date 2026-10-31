<?php
// =============================================================================
// WATCH MODEL - Table: theo_doi_tro_choi
// =============================================================================

class WatchModel extends Model {
    protected static $table = 'theo_doi_tro_choi';

    public static function toggle(int $userId, int $gameId): bool {
        $exists = self::exists($userId, $gameId);
        if ($exists) {
            $stmt = self::db()->prepare("DELETE FROM " . self::$table . " WHERE nguoi_dung_id = :uid AND tro_choi_id = :gid");
            return $stmt->execute([':uid' => $userId, ':gid' => $gameId]);
        }
        return self::insertRow(['nguoi_dung_id' => $userId, 'tro_choi_id' => $gameId]);
    }

    public static function exists(int $userId, int $gameId): bool {
        $stmt = self::db()->prepare("SELECT 1 FROM " . self::$table . " WHERE nguoi_dung_id = :uid AND tro_choi_id = :gid LIMIT 1");
        $stmt->execute([':uid' => $userId, ':gid' => $gameId]);
        return $stmt->fetch() !== false;
    }

    public static function watch(int $userId, int $gameId): bool {
        $stmt = self::db()->prepare(
            'INSERT IGNORE INTO ' . self::$table . ' (nguoi_dung_id, tro_choi_id)
             VALUES (:user_id, :game_id)'
        );
        return $stmt->execute([':user_id' => $userId, ':game_id' => $gameId]);
    }

    public static function unwatch(int $userId, int $gameId): bool {
        $stmt = self::db()->prepare(
            'DELETE FROM ' . self::$table . ' WHERE nguoi_dung_id = :user_id AND tro_choi_id = :game_id'
        );
        return $stmt->execute([':user_id' => $userId, ':game_id' => $gameId]);
    }

    public static function watcherIds(int $gameId): array {
        $stmt = self::db()->prepare(
            'SELECT nguoi_dung_id FROM ' . self::$table . '
             WHERE tro_choi_id = :game_id AND thong_bao_cap_nhat = TRUE'
        );
        $stmt->execute([':game_id' => $gameId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function watchedGamesForUser(int $userId): array {
        $sql = 'SELECT g.ma, g.tieu_de, g.duong_dan_rut_gon
                FROM ' . self::$table . ' w
                INNER JOIN tro_choi g ON g.ma = w.tro_choi_id
                WHERE w.nguoi_dung_id = :user_id AND g.trang_thai = \'published\'
                ORDER BY w.ngay_tao DESC';
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}