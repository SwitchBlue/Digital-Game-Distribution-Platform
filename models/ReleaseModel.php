<?php
// =============================================================================
// RELEASE MODEL - Table: phien_ban_phat_hanh
// =============================================================================

class ReleaseModel extends Model {
    protected static $table = 'phien_ban_phat_hanh';

    public static function create(int $gameId, string $version, string $channel, string $notes, int $creatorId): int {
        $releaseId = self::insert([
            'tro_choi_id' => $gameId,
            'ten_phien_ban' => $version,
            'kenh_phat_hanh' => $channel,
            'ghi_chu_phat_hanh' => $notes,
            'nguoi_tao_id' => $creatorId,
            'ngay_phat_hanh' => date('Y-m-d H:i:s'),
        ]);
        $game = GameModel::find($gameId);
        if ($game) {
            NotificationModel::notifyGameFollowers(
                $gameId,
                $creatorId,
                'game_release',
                'Có phiên bản game mới',
                $game['tieu_de'] . ' vừa phát hành phiên bản ' . $version . '.',
                '/games/' . rawurlencode($game['duong_dan_rut_gon'])
            );
        }
        return $releaseId;
    }

    public static function listForGame(int $gameId): array {
        $sql = "SELECT * FROM " . self::$table . " WHERE tro_choi_id = :gid ORDER BY ngay_phat_hanh DESC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':gid' => $gameId]);
        return $stmt->fetchAll();
    }
}