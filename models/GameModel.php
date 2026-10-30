<?php
// =============================================================================
// GAME MODEL - Table: tro_choi
// =============================================================================

class GameModel extends Model {
    protected static $table = 'tro_choi';

    public static function create(array $data): int {
        return self::insert($data);
    }

    public static function findBySlug(string $slug): ?array {
        return self::findBy('duong_dan_rut_gon', $slug);
    }

<<<<<<< HEAD
    public static function removeFromPlatform(int $gameId): bool {
        return self::update($gameId, ['trang_thai' => 'hidden']);
    }

    public static function listForAdmin(string $query, int $limit, int $offset): array {
        $where = '';
        $params = [];
        if ($query !== '') {
            $where = ' WHERE tieu_de LIKE :title_query
                       OR duong_dan_rut_gon LIKE :slug_query
                       OR CAST(ma AS CHAR) = :game_id';
            $params = [
                ':title_query' => '%' . $query . '%',
                ':slug_query' => '%' . $query . '%',
                ':game_id' => $query,
            ];
        }

        $sql = 'SELECT * FROM ' . self::$table . $where
            . ' ORDER BY ngay_tao DESC LIMIT :limit OFFSET :offset';
        $stmt = self::db()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countForAdmin(string $query): int {
        $where = '';
        $params = [];
        if ($query !== '') {
            $where = ' WHERE tieu_de LIKE :title_query
                       OR duong_dan_rut_gon LIKE :slug_query
                       OR CAST(ma AS CHAR) = :game_id';
            $params = [
                ':title_query' => '%' . $query . '%',
                ':slug_query' => '%' . $query . '%',
                ':game_id' => $query,
            ];
        }
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM ' . self::$table . $where);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

=======
>>>>>>> origin/main
    public static function listPublished(array $filters = [], int $limit = 20, int $offset = 0): array {
        $where = ['trang_thai = :status'];
        $params = [':status' => 'published'];

        if (!empty($filters['category'])) {
            $where[] = 'ma IN (SELECT tro_choi_id FROM tro_choi_the_loai WHERE the_loai_id = :cat)';
            $params[':cat'] = $filters['category'];
        }

        if (!empty($filters['platform'])) {
            $where[] = 'ma IN (SELECT tro_choi_id FROM tro_choi_nen_tang WHERE nen_tang_id = :plat)';
            $params[':plat'] = $filters['platform'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(tieu_de LIKE :q OR mo_ta_ngan LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        $sql = "SELECT * FROM " . self::$table . " WHERE " . implode(' AND ', $where)
            . " ORDER BY ngay_tao DESC LIMIT :limit OFFSET :offset";

        $stmt = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $games = $stmt->fetchAll();

        foreach ($games as &$game) {
            $id = (int)$game['ma'];

            $categoryStmt = self::db()->prepare(
                'SELECT c.ma, c.ten FROM the_loai c
                 INNER JOIN tro_choi_the_loai gc ON gc.the_loai_id = c.ma
                 WHERE gc.tro_choi_id = :gid ORDER BY c.ten'
            );
            $categoryStmt->execute([':gid' => $id]);
            $game['the_loai'] = $categoryStmt->fetchAll();

            $platformStmt = self::db()->prepare(
                'SELECT p.ma, p.ten_nen_tang FROM nen_tang p
                 INNER JOIN tro_choi_nen_tang gp ON gp.nen_tang_id = p.ma
                 WHERE gp.tro_choi_id = :gid ORDER BY p.ten_nen_tang'
            );
            $platformStmt->execute([':gid' => $id]);
            $game['nen_tang'] = $platformStmt->fetchAll();

            $mediaStmt = self::db()->prepare(
                "SELECT url_media FROM media_tro_choi
                 WHERE tro_choi_id = :gid AND loai_media IN ('cover', 'thumbnail')
                 ORDER BY CASE loai_media WHEN 'cover' THEN 0 ELSE 1 END, thu_tu_sap_xep, ma
                 LIMIT 1"
            );
            $mediaStmt->execute([':gid' => $id]);
            $game['anh_mo_rong'] = $mediaStmt->fetchColumn() ?: null;
        }
        unset($game);

        return $games;
    }

    public static function countPublished(array $filters = []): int {
        $where = ['trang_thai = :status'];
        $params = [':status' => 'published'];

        if (!empty($filters['category'])) {
            $where[] = 'ma IN (SELECT tro_choi_id FROM tro_choi_the_loai WHERE the_loai_id = :cat)';
            $params[':cat'] = $filters['category'];
        }

        if (!empty($filters['platform'])) {
            $where[] = 'ma IN (SELECT tro_choi_id FROM tro_choi_nen_tang WHERE nen_tang_id = :plat)';
            $params[':plat'] = $filters['platform'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(tieu_de LIKE :q OR mo_ta_ngan LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        return self::count(implode(' AND ', $where), $params);
    }
}