<?php
/**
 * Hospital facilities are stored on users (role_id = 2).
 * Legacy `hospitals` table is NOT used after fresh.sql import.
 */

function uses_unified_hospital_schema(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $hasRoleId = $conn->query("SHOW COLUMNS FROM `users` LIKE 'role_id'")->rowCount() > 0;
        $hasHospitalsTable = $conn->query("SHOW TABLES LIKE 'hospitals'")->rowCount() > 0;
        $cache = $hasRoleId && !$hasHospitalsTable;
    } catch (Throwable $e) {
        $cache = true;
    }
    return $cache;
}

/** @deprecated use uses_unified_hospital_schema */
function hospitals_table_exists(?PDO $conn = null): bool
{
    if (!$conn) {
        return false;
    }
    return !uses_unified_hospital_schema($conn);
}

function fetch_approved_hospitals(PDO $conn, string $search = ''): array
{
    $sql = "SELECT id, hospital_name, location, id AS hospital_user_id
            FROM users
            WHERE role_id = 2 AND facility_status = 'Approved'";
    $params = [];
    if ($search !== '') {
        $sql .= ' AND (hospital_name LIKE :q OR location LIKE :q OR name LIKE :q)';
        $params[':q'] = '%' . $search . '%';
    }
    $sql .= ' ORDER BY hospital_name ASC';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_hospital_for_booking(PDO $conn, int $hospitalUserId): ?array
{
    $stmt = $conn->prepare("
        SELECT id, id AS user_id, hospital_name, location, id AS booking_hospital_id
        FROM users
        WHERE id = :id AND role_id = 2 AND facility_status = 'Approved'
    ");
    $stmt->execute([':id' => $hospitalUserId]);
    $h = $stmt->fetch();
    return $h ?: null;
}

function fetch_hospital_account_by_user(PDO $conn, int $userId): ?array
{
    $stmt = $conn->prepare("
        SELECT id, name, email, hospital_name, location, license_number,
               facility_status AS status
        FROM users
        WHERE id = :uid AND role_id = 2
    ");
    $stmt->execute([':uid' => $userId]);
    $u = $stmt->fetch();
    if ($u) {
        $u['user_id'] = $userId;
    }
    return $u ?: null;
}

function hospital_join_sql(PDO $conn, string $apptAlias = 'a'): string
{
    return "JOIN users hu ON {$apptAlias}.hospital_id = hu.id AND hu.role_id = 2";
}

function hospital_name_expr(PDO $conn, string $alias = 'hu'): string
{
    return "COALESCE({$alias}.hospital_name, {$alias}.name)";
}

function hospital_appointment_filter_id(PDO $conn, int $hospitalUserId): int
{
    return $hospitalUserId;
}
