<?php
// =============================================================================
// FOLLOW MODEL - Table: theo_doi_nguoi_dung
// =============================================================================

class FollowModel extends Model {
    protected static $table = 'theo_doi_nguoi_dung';

    public static function follow(int $followerId, int $targetId): bool {
        if ($followerId === $targetId) {
            return false;
        }
        $stmt = self::db()->prepare(
            'INSERT IGNORE INTO ' . self::$table . ' (nguoi_theo_doi_id, nguoi_duoc_theo_doi_id)
             VALUES (:follower_id, :target_id)'
        );
        return $stmt->execute([':follower_id' => $followerId, ':target_id' => $targetId]);
    }

    public static function unfollow(int $followerId, int $targetId): bool {
        $stmt = self::db()->prepare("DELETE FROM " . self::$table . " WHERE nguoi_theo_doi_id = :f AND nguoi_duoc_theo_doi_id = :t");
        return $stmt->execute([':f' => $followerId, ':t' => $targetId]);
    }

    public static function isFollowing(int $followerId, int $targetId): bool {
        $stmt = self::db()->prepare("SELECT 1 FROM " . self::$table . " WHERE nguoi_theo_doi_id = :f AND nguoi_duoc_theo_doi_id = :t LIMIT 1");
        $stmt->execute([':f' => $followerId, ':t' => $targetId]);
        return $stmt->fetch() !== false;
    }

    public static function followerIds(int $userId): array {
        $stmt = self::db()->prepare(
            'SELECT nguoi_theo_doi_id FROM ' . self::$table . ' WHERE nguoi_duoc_theo_doi_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function followedUsersForUser(int $userId): array {
        $sql = 'SELECT u.ma, u.ten_hien_thi, u.url_anh_dai_dien
                FROM ' . self::$table . ' f
                INNER JOIN nguoi_dung u ON u.ma = f.nguoi_duoc_theo_doi_id
                WHERE f.nguoi_theo_doi_id = :user_id
                ORDER BY f.ngay_tao DESC';
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}