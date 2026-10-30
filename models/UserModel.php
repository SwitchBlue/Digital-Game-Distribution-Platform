<?php
// =============================================================================
// USER MODEL - Table: nguoi_dung
// =============================================================================

class UserModel extends Model {
    protected static $table = 'nguoi_dung';
    protected static $pk = 'ma';

    public static function create(string $username, string $email, string $password, string $displayName): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
<<<<<<< HEAD
        $userId = self::insert([
=======
        return self::insert([
>>>>>>> origin/main
            'ten_dang_nhap' => $username,
            'email' => $email,
            'mat_khau_da_bam' => $hash,
            'ten_hien_thi' => $displayName,
        ]);
<<<<<<< HEAD
        if (!self::updateProfile($userId, [
            'url_anh_dai_dien' => app_url('/avatars/' . $userId),
        ])) {
            throw new RuntimeException('Unable to assign a generated avatar to the new account.');
        }
        return $userId;
=======
>>>>>>> origin/main
    }

    public static function authenticate(string $email, string $password): ?array {
        $user = self::findBy('email', $email);
        if ($user && password_verify($password, $user['mat_khau_da_bam'])) {
            unset($user['mat_khau_da_bam']);
            return $user;
        }
        return null;
    }

    public static function updateProfile(int $id, array $data): bool {
        return self::update($id, $data);
    }

<<<<<<< HEAD
    public static function listForAdmin(string $query, int $limit, int $offset): array {
        $where = '';
        $params = [];
        if ($query !== '') {
            $where = ' WHERE ten_hien_thi LIKE :display_query
                       OR ten_dang_nhap LIKE :username_query
                       OR email LIKE :email_query
                       OR CAST(ma AS CHAR) = :user_id';
            $likeQuery = '%' . $query . '%';
            $params = [
                ':display_query' => $likeQuery,
                ':username_query' => $likeQuery,
                ':email_query' => $likeQuery,
                ':user_id' => $query,
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
            $where = ' WHERE ten_hien_thi LIKE :display_query
                       OR ten_dang_nhap LIKE :username_query
                       OR email LIKE :email_query
                       OR CAST(ma AS CHAR) = :user_id';
            $likeQuery = '%' . $query . '%';
            $params = [
                ':display_query' => $likeQuery,
                ':username_query' => $likeQuery,
                ':email_query' => $likeQuery,
                ':user_id' => $query,
            ];
        }
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM ' . self::$table . $where);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

=======
>>>>>>> origin/main
    public static function getRoles(int $userId): array {
        $sql = "SELECT r.ma_vai_tro, r.ten_vai_tro
                FROM vai_tro r
                INNER JOIN nguoi_dung_vai_tro uvr ON uvr.vai_tro_id = r.ma
                WHERE uvr.nguoi_dung_id = :uid";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function addRole(int $userId, int $roleId): bool {
        $sql = "INSERT IGNORE INTO nguoi_dung_vai_tro (nguoi_dung_id, vai_tro_id) VALUES (:uid, :rid)";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':rid' => $roleId]);
    }
<<<<<<< HEAD

    public static function syncRoles(int $userId, array $roleIds): bool {
        $db = self::db();
        $db->beginTransaction();

        try {
            $adminRole = RoleModel::findByCode('admin');
            if (!$adminRole) {
                throw new RuntimeException('Không tìm thấy vai trò quản trị viên.');
            }

            $lock = $db->prepare('SELECT ma FROM vai_tro WHERE ma = :role_id FOR UPDATE');
            $lock->execute([':role_id' => $adminRole['ma']]);

            $currentRoleStmt = $db->prepare(
                'SELECT vai_tro_id FROM nguoi_dung_vai_tro WHERE nguoi_dung_id = :user_id'
            );
            $currentRoleStmt->execute([':user_id' => $userId]);
            $currentRoleIds = array_map('intval', $currentRoleStmt->fetchAll(PDO::FETCH_COLUMN));
            if (in_array((int)$adminRole['ma'], $currentRoleIds, true)
                && !in_array((int)$adminRole['ma'], $roleIds, true)) {
                $adminCountStmt = $db->prepare(
                    'SELECT COUNT(*) FROM nguoi_dung_vai_tro WHERE vai_tro_id = :role_id'
                );
                $adminCountStmt->execute([':role_id' => $adminRole['ma']]);
                if ((int)$adminCountStmt->fetchColumn() <= 1) {
                    $db->rollBack();
                    return false;
                }
            }

            $delete = $db->prepare('DELETE FROM nguoi_dung_vai_tro WHERE nguoi_dung_id = :user_id');
            $delete->execute([':user_id' => $userId]);
            $insert = $db->prepare(
                'INSERT INTO nguoi_dung_vai_tro (nguoi_dung_id, vai_tro_id) VALUES (:user_id, :role_id)'
            );
            foreach ($roleIds as $roleId) {
                $insert->execute([':user_id' => $userId, ':role_id' => $roleId]);
            }
            $db->commit();
            return true;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }
=======
>>>>>>> origin/main
}