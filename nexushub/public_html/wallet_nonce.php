<?php
require 'wallet_common.php';
$uid = wallet_user($conn, $_GET['ref'] ?? '');
if (!$uid) wfail(404, 'User not found');

$message = "Link this wallet to NexusHub\nUser: $uid\nNonce: " . bin2hex(random_bytes(16));
$_SESSION['wallet_msg'] = $message;
$_SESSION['wallet_uid'] = $uid;
$_SESSION['wallet_msg_time'] = time();

echo json_encode(['message' => $message]);