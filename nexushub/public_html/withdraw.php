<?php
include 'cdn.php';
include 'config.php';
$usr_string = $req;

$numbers = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$numbers' OR id='$numbers'");
if (!$chk || $chk->num_rows == 0) {
    $chk = $conn->query("select * from user_details limit 1");
}

if ($chk && $chk->num_rows > 0) {
    $loop = $chk->fetch_assoc();
} else {
    $loop = [
        'id' => 1,
        'uid' => !empty($numbers) ? $numbers : '1001',
        'uname' => 'Trader',
        'kyc_status' => 'Not Submitted'
    ];
}

$uid = isset($loop['id']) ? $loop['id'] : 1;
$u = isset($loop['uid']) ? $loop['uid'] : (!empty($numbers) ? $numbers : '1001');

// Handle withdrawal request
if (isset($_POST['btc_wallet']) || isset($_POST['wallet_address'])) {
    $amt = filter($_POST['exact_amount']);
    $wal_raw = filter($_POST['wallet_address'] ?? $_POST['btc_wallet']);
    $coin = filter($_POST['coin_type'] ?? 'BTC');
    $wal = "[" . strtoupper($coin) . "] " . $wal_raw;
    $n = isset($loop['uname']) ? $loop['uname'] : 'Trader';

    $qry = $conn->query("insert into withdrawals(uid,uname,btc_wallet,amt) values('$u','$n','$wal','$amt')");

    if ($qry) {
        $message = "Your " . strtoupper($coin) . " withdrawal has been accepted and is being processed.";
        $message_type = "success";
    }
}

$bl_check = $conn->query("select * from bal where uid='$uid' OR uid='$u'");
$itr = ($bl_check && $bl_check->num_rows > 0) ? $bl_check->fetch_assoc() : ['bal' => 0];

include 'dashboard_header.php';
?>

<div class="max-w-3xl mx-auto space-y-6 sm:space-y-8 w-full max-w-full overflow-x-hidden">
    <div class="text-center mb-6 sm:mb-10">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-gold rounded-3xl flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-xl shadow-orange-500/20">
            <i class="fas fa-arrow-up-from-bracket text-black text-2xl sm:text-3xl"></i>
        </div>
        <h2 class="font-syne text-2xl sm:text-3xl font-800 text-white mb-2 sm:mb-3">Withdraw Funds</h2>
        <p class="text-gray-400 text-xs sm:text-sm max-w-sm mx-auto leading-relaxed">Securely transfer crypto (BTC, ETH, USDT) to an external wallet.</p>
    </div>

    <?php if(isset($message)): ?>
        <div class="mb-6 sm:mb-8 p-4 sm:p-6 rounded-2xl sm:rounded-[2rem] bg-green-500/10 border border-green-500/20 text-green-400 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-500/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-check text-xs sm:text-base"></i>
                </div>
                <p class="font-700 text-xs sm:text-sm"><?php echo $message; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="card-dark rounded-2xl sm:rounded-[2.5rem] p-5 sm:p-8 lg:p-12 border-white/5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[100px] -mr-32 -mt-32"></div>

        <form method="post" action="/withdraw/<?php echo $req;?>" class="relative z-10 space-y-6 sm:space-y-8">
            <!-- Crypto Coin Selection -->
            <div class="space-y-2 sm:space-y-3">
                <label class="block text-xs font-700 text-gray-500 uppercase tracking-widest ml-1">Select Asset</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" onclick="selectCoin('BTC')" id="btn-btc" class="coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border transition-all bg-orange-500/10 border-orange-500 text-orange-400 font-700 text-xs sm:text-sm">
                        <i class="fab fa-bitcoin text-lg"></i>
                        <span>BTC</span>
                    </button>
                    <button type="button" onclick="selectCoin('ETH')" id="btn-eth" class="coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border border-white/10 bg-white/5 text-gray-400 font-700 text-xs sm:text-sm hover:border-white/20">
                        <i class="fab fa-ethereum text-lg"></i>
                        <span>ETH</span>
                    </button>
                    <button type="button" onclick="selectCoin('USDT')" id="btn-usdt" class="coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border border-white/10 bg-white/5 text-gray-400 font-700 text-xs sm:text-sm hover:border-white/20">
                        <i class="fas fa-coins text-lg"></i>
                        <span>USDT</span>
                    </button>
                </div>
                <input type="hidden" name="coin_type" id="coin_type_input" value="BTC">
            </div>

            <!-- Network Info -->
            <div class="flex items-center justify-between p-3.5 sm:p-4 bg-white/5 rounded-2xl border border-white/5">
                <div class="flex items-center gap-3">
                    <div id="net-icon" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 shrink-0">
                        <i class="fab fa-bitcoin text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[9px] sm:text-[10px] text-gray-500 uppercase font-700 tracking-wider">Network</p>
                        <p class="text-xs sm:text-sm font-700 text-white" id="net-name">Bitcoin Mainnet</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[9px] sm:text-[10px] text-gray-500 uppercase font-700 tracking-wider">Available</p>
                    <p class="text-xs sm:text-sm font-800 text-primary" id="disp_bal">
                        $<?php echo number_format($itr['bal'], 2, '.', ','); ?>
                    </p>
                </div>
            </div>

            <!-- Wallet Address -->
            <div class="space-y-2 sm:space-y-3">
                <label class="block text-xs font-700 text-gray-500 uppercase tracking-widest ml-1" id="addr-label">Destination BTC Wallet Address</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-500 transition-colors group-focus-within:text-primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <input type="text" name="wallet_address" id="wallet_addr_input" placeholder="Enter Bitcoin Address" required
                        class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 sm:py-5 pl-12 sm:pl-14 pr-4 sm:pr-6 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all font-mono text-xs sm:text-sm">
                </div>
                <p class="text-[10px] text-gray-500 ml-1 italic"><i class="fas fa-info-circle mr-1"></i> Ensure the address matches the selected cryptocurrency network.</p>
            </div>

            <!-- Amount -->
            <div class="space-y-2 sm:space-y-3">
                <div class="flex justify-between items-center ml-1">
                    <label class="block text-xs font-700 text-gray-500 uppercase tracking-widest">Amount to Withdraw</label>
                    <button type="button" onclick="setMax();" class="text-[10px] font-800 text-primary uppercase tracking-widest hover:text-white transition">Use Max</button>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-500 transition-colors group-focus-within:text-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <input type="number" step="any" id="amt_input" name="exact_amount" placeholder="0.00" required
                        class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 sm:py-5 pl-12 sm:pl-14 pr-4 sm:pr-6 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all font-mono text-base sm:text-lg">
                </div>
            </div>

            <!-- Summary -->
            <div class="p-4 sm:p-6 bg-black/40 rounded-2xl sm:rounded-3xl border border-white/5 space-y-2.5 sm:space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Network Fee</span>
                    <span class="text-white font-700" id="net-fee">0.0001 BTC</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Processing Time</span>
                    <span class="text-white font-700">~ 15-60 Minutes</span>
                </div>
                <div class="pt-2.5 border-t border-white/5 flex justify-between">
                    <span class="text-xs sm:text-sm font-700 text-white">Estimated Total</span>
                    <span class="text-xs sm:text-sm font-800 text-primary" id="est_total">$0.00</span>
                </div>
            </div>

            <button type="submit" <?php echo ($itr['bal'] <= 0) ? 'disabled' : ''; ?>
                class="w-full bg-gradient-gold text-black font-800 py-4 sm:py-5 rounded-2xl shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 font-syne uppercase tracking-wider text-xs sm:text-sm <?php echo ($itr['bal'] <= 0) ? 'opacity-50 grayscale cursor-not-allowed' : ''; ?>">
                Review Withdrawal <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

    <div class="mt-6 sm:mt-8 text-center">
        <a href="/history/<?php echo $req; ?>" class="text-xs font-700 text-gray-500 uppercase tracking-widest hover:text-primary transition">View Past Withdrawals</a>
    </div>
</div>

<script>
    const balance = <?php echo $itr['bal']; ?>;
    const input = document.getElementById('amt_input');
    const total = document.getElementById('est_total');

    function setMax() {
        if (input) {
            input.value = balance;
            updateTotal();
        }
    }

    function updateTotal() {
        if (input && total) {
            const val = parseFloat(input.value) || 0;
            total.textContent = '$' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    if (input) input.oninput = updateTotal;

    function selectCoin(coin) {
        document.getElementById('coin_type_input').value = coin;

        ['btc', 'eth', 'usdt'].forEach(c => {
            const btn = document.getElementById('btn-' + c);
            if (btn) {
                btn.className = 'coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border border-white/10 bg-white/5 text-gray-400 font-700 text-xs sm:text-sm hover:border-white/20';
            }
        });

        const netIcon = document.getElementById('net-icon');
        const netName = document.getElementById('net-name');
        const addrLabel = document.getElementById('addr-label');
        const addrInput = document.getElementById('wallet_addr_input');
        const netFee = document.getElementById('net-fee');
        const activeBtn = document.getElementById('btn-' + coin.toLowerCase());

        if (coin === 'BTC') {
            if(activeBtn) activeBtn.className = 'coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border transition-all bg-orange-500/10 border-orange-500 text-orange-400 font-700 text-xs sm:text-sm';
            if(netIcon) {
                netIcon.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 shrink-0';
                netIcon.innerHTML = '<i class="fab fa-bitcoin text-lg sm:text-xl"></i>';
            }
            if(netName) netName.textContent = 'Bitcoin Mainnet';
            if(addrLabel) addrLabel.textContent = 'Destination BTC Wallet Address';
            if(addrInput) addrInput.placeholder = 'Enter Bitcoin Address (e.g. bc1q...)';
            if(netFee) netFee.textContent = '0.0001 BTC';
        } else if (coin === 'ETH') {
            if(activeBtn) activeBtn.className = 'coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border transition-all bg-blue-500/10 border-blue-500 text-blue-400 font-700 text-xs sm:text-sm';
            if(netIcon) {
                netIcon.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0';
                netIcon.innerHTML = '<i class="fab fa-ethereum text-lg sm:text-xl"></i>';
            }
            if(netName) netName.textContent = 'Ethereum Mainnet (ERC-20)';
            if(addrLabel) addrLabel.textContent = 'Destination ETH Wallet Address';
            if(addrInput) addrInput.placeholder = 'Enter Ethereum Address (e.g. 0x...)';
            if(netFee) netFee.textContent = '0.002 ETH';
        } else if (coin === 'USDT') {
            if(activeBtn) activeBtn.className = 'coin-btn flex items-center justify-center gap-2 p-3 sm:p-4 rounded-2xl border transition-all bg-emerald-500/10 border-emerald-500 text-emerald-400 font-700 text-xs sm:text-sm';
            if(netIcon) {
                netIcon.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 shrink-0';
                netIcon.innerHTML = '<i class="fas fa-coins text-lg sm:text-xl"></i>';
            }
            if(netName) netName.textContent = 'USDT (ERC-20 / TRC-20)';
            if(addrLabel) addrLabel.textContent = 'Destination USDT Wallet Address';
            if(addrInput) addrInput.placeholder = 'Enter USDT Address (0x... or T...)';
            if(netFee) netFee.textContent = '1.00 USDT';
        }
    }
</script>

<?php
include 'dashboard_footer.php';
?>
