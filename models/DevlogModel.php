<?php
// =============================================================================
// DEVLOG MODEL - Table: nhat_ky_phat_trien
// =============================================================================

class DevlogModel extends Model {
    protected static $table = 'nhat_ky_phat_trien';

    public static function create(int $gameId, int $authorId, string $title, string $slug, string $content, bool $publish = false): int {
<<<<<<< HEAD
        $devlogId = self::insert([
=======
        return self::insert([
>>>>>>> origin/main
            'tro_choi_id' => $gameId,
            'tac_gia_nguoi_dung_id' => $authorId,
            'tieu_de' => $title,
            'duong_dan_rut_gon' => $slug,
            'noi_dung' => $content,
            'trang_thai' => $publish ? 'published' : 'draft',
            'ngay_cong_bo' => $publish ? date('Y-m-d H:i:s') : null,
        ]);
<<<<<<< HEAD
        if ($publish) {
            $game = GameModel::find($gameId);
            if ($game) {
                NotificationModel::notifyGameFollowers(
                    $gameId,
                    $authorId,
                    'game_devlog',
                    'Nhật ký phát triển mới',
                    $game['tieu_de'] . ': ' . $title,
                    '/games/' . rawurlencode($game['duong_dan_rut_gon'])
                );
            }
        }
        return $devlogId;
=======
>>>>>>> origin/main
    }

    public static function listForGame(int $gameId): array {
        $sql = "SELECT * FROM " . self::$table . " WHERE tro_choi_id = :gid AND trang_thai = 'published' ORDER BY ngay_cong_bo DESC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':gid' => $gameId]);
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array {
        return self::findBy('duong_dan_rut_gon', $slug);
    }
}