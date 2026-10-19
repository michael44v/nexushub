<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'config.php';
include_once __DIR__ . '/lib/eth.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

function wfail($code, $msg) {
    http_response_code($code);
    exit(json_encode(['ok' => false, 'error' => $msg]));
}

function wallet_user($conn, $ref) {
    $n = preg_replace('/\D/', '', (string)$ref);
    if ($n === '') return null;
    $stmt = $conn->prepare("SELECT id FROM user_details WHERE id=? LIMIT 1");
    if (!$stmt) wfail(500, 'DB error: ' . $conn->error);
    $stmt->bind_param('s', $n);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (int)$row['id'] : null;
}