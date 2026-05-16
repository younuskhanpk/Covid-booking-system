<?php
/**
 * Role: 1 = Admin, 2 = Hospital, 3 = Patient
 */
function role_id_from_name(string $name): int
{
    $map = ['Admin' => 1, 'Hospital' => 2, 'Patient' => 3];
    return $map[$name] ?? 3;
}

function role_name_from_id(int $id): string
{
    $map = [1 => 'Admin', 2 => 'Hospital', 3 => 'Patient'];
    return $map[$id] ?? 'Patient';
}

function sync_user_role_columns(PDO $conn, int $userId): void
{
    $stmt = $conn->prepare('SELECT role_id, role FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $u = $stmt->fetch();
    if (!$u) {
        return;
    }
    if (!empty($u['role_id']) && empty($u['role'])) {
        $conn->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([role_name_from_id((int) $u['role_id']), $userId]);
    } elseif (!empty($u['role']) && empty($u['role_id'])) {
        $conn->prepare('UPDATE users SET role_id = ? WHERE id = ?')->execute([role_id_from_name($u['role']), $userId]);
    }
}

function user_is_admin(array $userOrSession): bool
{
    if (isset($userOrSession['role_id'])) {
        return (int) $userOrSession['role_id'] === 1;
    }
    return ($userOrSession['role'] ?? '') === 'Admin';
}

function user_is_hospital(array $userOrSession): bool
{
    if (isset($userOrSession['role_id'])) {
        return (int) $userOrSession['role_id'] === 2;
    }
    return ($userOrSession['role'] ?? '') === 'Hospital';
}

function user_is_patient(array $userOrSession): bool
{
    if (isset($userOrSession['role_id'])) {
        return (int) $userOrSession['role_id'] === 3;
    }
    return ($userOrSession['role'] ?? '') === 'Patient';
}

function session_role_check(string $expected): bool
{
    if (!isset($_SESSION['role'])) {
        return false;
    }
    if ($_SESSION['role'] === $expected) {
        return true;
    }
    if (isset($_SESSION['role_id'])) {
        return role_name_from_id((int) $_SESSION['role_id']) === $expected;
    }
    return false;
}

/** Hospital facility display name from users row */
function hospital_display_name(array $row): string
{
    if (!empty($row['hospital_name'])) {
        return $row['hospital_name'];
    }
    return $row['name'] ?? 'Hospital';
}
