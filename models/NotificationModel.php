<?php
// =============================================================================
// NOTIFICATION MODEL - Table: thong_bao
// =============================================================================

class NotificationModel extends Model {
    protected static $table = 'thong_bao';

    public static function create(int $userId, string $type, string $title, string $content = '', string $url = ''): int {
        return self::insert([
            'nguoi_dung_id' => $userId,
            'loai_thong_bao' => $type,
            'tieu_de' => $title,
            'noi_dung_thong_bao' => $content,
            'url_dich' => $url,
        ]);
    }

    public static function markRead(int $notificationId): bool {
        $stmt = self::db()->prepare("UPDATE " . self::$table . " SET da_doc = TRUE, ngay_doc = NOW() WHERE ma = :id");
        return $stmt->execute([':id' => $notificationId]);
    }

    public static function listForUser(int $userId, int $limit = 20): array {
        $stmt = self::db()->prepare("SELECT * FROM " . self::$table . " WHERE nguoi_dung_id = :uid ORDER BY ngay_tao DESC LIMIT :limit");
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function unreadCount(int $userId): int {
        return self::count('nguoi_dung_id = :uid AND da_doc = FALSE', [':uid' => $userId]);
    }
<<<<<<< HEAD

    public static function notifyUsers(array $userIds, int $actorId, string $type, string $title, string $content, string $url): void {
        $userIds = array_values(array_unique(array_filter(
            array_map('intval', $userIds),
            static function (int $userId) use ($actorId): bool {
                return $userId > 0 && $userId !== $actorId;
            }
        )));
        if (!$userIds) {
            return;
        }

        $stmt = self::db()->prepare(
            'INSERT INTO ' . self::$table . '
                (nguoi_dung_id, loai_thong_bao, tieu_de, noi_dung_thong_bao, url_dich)
             VALUES (:user_id, :type, :title, :content, :url)'
        );
        foreach ($userIds as $userId) {
            $stmt->execute([
                ':user_id' => $userId,
                ':type' => $type,
                ':title' => $title,
                ':content' => $content,
                ':url' => $url,
            ]);
        }
    }

    public static function notifyGameFollowers(int $gameId, int $actorId, string $type, string $title, string $content, string $url): void {
        $game = GameModel::find($gameId);
        if (!$game || $game['trang_thai'] !== 'published') {
            return;
        }

        $recipients = WatchModel::watcherIds($gameId);
        $authorStmt = self::db()->prepare(
            'SELECT nguoi_dung_id FROM tac_gia_tro_choi WHERE tro_choi_id = :game_author_id
             UNION SELECT nguoi_tao_id FROM tro_choi WHERE ma = :game_owner_id'
        );
        $authorStmt->execute([':game_author_id' => $gameId, ':game_owner_id' => $gameId]);
        foreach ($authorStmt->fetchAll(PDO::FETCH_COLUMN) as $authorId) {
            $recipients = array_merge($recipients, FollowModel::followerIds((int)$authorId));
        }

        self::notifyUsers($recipients, $actorId, $type, $title, $content, $url);
    }

    public static function notifyUserFollowers(int $userId, string $type, string $title, string $content, string $url): void {
        self::notifyUsers(FollowModel::followerIds($userId), $userId, $type, $title, $content, $url);
    }

    public static function markReadForUser(int $notificationId, int $userId): bool {
        $stmt = self::db()->prepare(
            'UPDATE ' . self::$table . '
             SET da_doc = TRUE, ngay_doc = NOW()
             WHERE ma = :notification_id AND nguoi_dung_id = :user_id'
        );
        return $stmt->execute([':notification_id' => $notificationId, ':user_id' => $userId]);
    }
=======
>>>>>>> origin/main
}