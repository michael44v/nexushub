<?php
require 'wallet_common.php';
if (!extension_loaded('gmp')) wfail(500, 'GMP extension missing');

$in  = json_decode(file_get_contents('php://input'), true);
$uid = wallet_user($conn, $in['ref'] ?? '');
if (!$uid) wfail(404, 'User not found');

if (empty($_SESSION['wallet_msg']) || ($_SESSION['wallet_uid'] ?? 0) !== $uid
    || time() - ($_SESSION['wallet_msg_time'] ?? 0) > 600) wfail(400, 'Challenge expired, try again');

$message = $_SESSION['wallet_msg'];
unset($_SESSION['wallet_msg'], $_SESSION['wallet_uid'], $_SESSION['wallet_msg_time']);

$address = eth_recover_address($message, $in['signature'] ?? '');
if (!$address) wfail(401, 'Invalid signature');

// Cosmetic wallet info (sanitized)
$name = mb_substr(trim(strip_tags((string)($in['wallet_name'] ?? ''))), 0, 60);
$icon = (string)($in['wallet_icon'] ?? '');
if (strlen($icon) > 60000 || !preg_match('#^(data:image/|https://)[^\s"\'<>]+$#', $icon)) $icon = null;

$stmt = $conn->prepare("SELECT id FROM user_details WHERE wallet_address=? AND id<>?");
$stmt->bind_param('si', $address, $uid);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) wfail(409, 'Wallet already linked to another account');

$stmt = $conn->prepare("UPDATE user_details SET wallet_address=?, wallet_name=?, wallet_icon=? WHERE id=?");
$stmt->bind_param('sssi', $address, $name, $icon, $uid);
$stmt->execute();

echo json_encode(['ok' => true, 'address' => $address, 'name' => $name, 'icon' => $icon]);