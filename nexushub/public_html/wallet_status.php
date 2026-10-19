<?php
require 'wallet_common.php';
$uid = wallet_user($conn, $_GET['ref'] ?? '');
if (!$uid) { echo json_encode(['address' => null]); exit; }

$stmt = $conn->prepare("SELECT wallet_address, wallet_name, wallet_icon FROM user_details WHERE id=?");
if (!$stmt) wfail(500, 'DB error: ' . $conn->error);
$stmt->bind_param('i', $uid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
echo json_encode([
    'address' => $row['wallet_address'] ?? null,
    'name'    => $row['wallet_name'] ?? null,
    'icon'    => $row['wallet_icon'] ?? null
]);