<?php
/**
 * Hospital facilities are stored on users (role_id = 2).
 * Legacy `hospitals` table is NOT used after fresh.sql import.
 */

function uses_unified_hospital_schema($conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $resRoleId = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'role_id'");
    $hasRoleId = $resRoleId && mysqli_num_rows($resRoleId) > 0;
    $resHospitals = mysqli_query($conn, "SHOW TABLES LIKE 'hospitals'");
    $hasHospitalsTable = $resHospitals && mysqli_num_rows($resHospitals) > 0;
    $cache = $hasRoleId && !$hasHospitalsTable;
    return $cache;
}

/** @deprecated use uses_unified_hospital_schema */
function hospitals_table_exists($conn = null): bool
{
    if (!$conn) {
        return false;
    }
    return !uses_unified_hospital_schema($conn);
}

function fetch_approved_hospitals($conn, string $search = ''): array
{
    $sql = "SELECT id, hospital_name, location, id AS hospital_user_id
            FROM users
            WHERE role_id = 2 AND facility_status = 'Approved'";
    if ($search !== '') {
        $q = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (hospital_name LIKE '%$q%' OR location LIKE '%$q%' OR name LIKE '%$q%')";
    }
    $sql .= " ORDER BY hospital_name ASC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function fetch_hospital_for_booking($conn, int $hospitalUserId): ?array
{
    $hospitalUserId = (int)$hospitalUserId;
    $sql = "SELECT id, id AS user_id, hospital_name, location, id AS booking_hospital_id
            FROM users
            WHERE id = $hospitalUserId AND role_id = 2 AND facility_status = 'Approved'";
    $result = mysqli_query($conn, $sql);
    $h = $result ? mysqli_fetch_assoc($result) : null;
    return $h ?: null;
}

function fetch_hospital_account_by_user($conn, int $userId): ?array
{
    $userId = (int)$userId;
    $sql = "SELECT id, name, email, hospital_name, location, license_number,
               facility_status AS status
            FROM users
            WHERE id = $userId AND role_id = 2";
    $result = mysqli_query($conn, $sql);
    $u = $result ? mysqli_fetch_assoc($result) : null;
    if ($u) {
        $u['user_id'] = $userId;
    }
    return $u ?: null;
}

function hospital_join_sql($conn, string $apptAlias = 'a'): string
{
    return "JOIN users hu ON {$apptAlias}.hospital_id = hu.id AND hu.role_id = 2";
}

function hospital_name_expr($conn, string $alias = 'hu'): string
{
    return "COALESCE({$alias}.hospital_name, {$alias}.name)";
}

function hospital_appointment_filter_id($conn, int $hospitalUserId): int
{
    return $hospitalUserId;
}
