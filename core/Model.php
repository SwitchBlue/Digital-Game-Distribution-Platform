<?php
// =============================================================================
// BASE MODEL - PDO wrapper with CRUD helpers
// =============================================================================

class Model {
    protected static $db = null;
    protected static $table = '';
    protected static $pk = 'ma';

    public static function db() {
        if (self::$db === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$db = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                error_log('Database connection failed: ' . $e->getMessage());
                die('Database connection failed. Verify MySQL is running and config/config.php is correct, then import new.sql into the configured database in phpMyAdmin.');
            }
        }
        return self::$db;
    }

    public static function all(string $orderBy = '', string $limit = '') {
        $sql = "SELECT * FROM " . static::$table;
        if ($orderBy) $sql .= " ORDER BY " . $orderBy;
        if ($limit)   $sql .= " LIMIT " . (int)$limit;
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(mixed $id): ?array {
        $stmt = self::db()->prepare("SELECT * FROM " . static::$table . " WHERE " . static::$pk . " = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBy(string $column, mixed $value): ?array {
        $stmt = self::db()->prepare("SELECT * FROM " . static::$table . " WHERE " . $column . " = :v LIMIT 1");
        $stmt->execute([':v' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findAllBy(string $column, mixed $value): array {
        $stmt = self::db()->prepare("SELECT * FROM " . static::$table . " WHERE " . $column . " = :v");
        $stmt->execute([':v' => $value]);
        return $stmt->fetchAll();
    }

    public static function insert(array $data): int {
        $stmt = static::prepareInsert($data);
        $stmt->execute($data);
        return (int)self::db()->lastInsertId();
    }

    public static function insertRow(array $data): bool {
        $stmt = static::prepareInsert($data);
        return $stmt->execute($data);
    }

    protected static function prepareInsert(array $data): PDOStatement {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO " . static::$table . " ($cols) VALUES ($placeholders)";
        return self::db()->prepare($sql);
    }

    public static function update(mixed $id, array $data): bool {
        if (empty($data)) return false;
        $sets = [];
        foreach (array_keys($data) as $col) {
            $sets[] = "$col = :$col";
        }
        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $sets) . " WHERE " . static::$pk . " = :id";
        $data[':id'] = $id;
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($data);
    }

    public static function delete(mixed $id): bool {
        $stmt = self::db()->prepare("DELETE FROM " . static::$table . " WHERE " . static::$pk . " = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) AS cnt FROM " . static::$table;
        if ($where) $sql .= " WHERE " . $where;
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'];
    }

    public static function query(string $sql, array $params = []): array {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
