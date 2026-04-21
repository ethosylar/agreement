<?php
session_start();
include "dbconn.php";

// Helper (you can also move this into a shared include)
function write_audit(
    mysqli $conn,
    string $user,
    string $dept,
    string $action,
    string $table_name,
    int    $record_id,
    array  $data = []
) {
    $ip   = $_SERVER['REMOTE_ADDR']     ?? '';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare(<<<'SQL'
        INSERT INTO audit_log
          (user_id, department, action, table_name, record_id, changed_data, ip_address, user_agent)
        VALUES
          (?,       ?,          ?,      ?,          ?,         CAST(? AS JSON), ?,          ?)
    SQL
    );
    $stmt->bind_param(
        "ssssisss",
        $user,
        $dept,
        $action,
        $table_name,
        $record_id,
        $json,
        $ip,
        $ua
    );
    $stmt->execute();
    $stmt->close();
}

// 1) Grab who’s logging out
$user = $_SESSION['username']   ?? '';
$dept = $_SESSION['department'] ?? '';

// 2) Write the audit entry
write_audit(
    $connection,
    $user,
    $dept,
    'LOGOUT',   // new enum value
    'depart',   // table context
    0,          // no specific record
    []          // no extra data
);

// 3) Destroy session & redirect
session_unset();
session_destroy();
header("Location: index.php");
exit;