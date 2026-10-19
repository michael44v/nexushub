<?php
include 'cdn.php';
include 'config.php';
$usr_string = $req;

// Remove all non-digit characters to get user ID
$numbers = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where id='$numbers' OR uid='$numbers'");
if (!$chk || $chk->num_rows == 0) {
    // Fallback if queried by legacy id column or empty
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
$u = isset($loop['uid']) ? $loop['uid'] : $numbers;

// Ensure balance exists
$bl_check = $conn->query("select * from bal where uid='$uid' OR uid='$u'");
if (!$bl_check || $bl_check->num_rows < 1) {
    $conn->query("insert into bal(uid,bal) values('$uid','0.00')");
    $bl_check = $conn->query("select * from bal where uid='$uid' OR uid='$u'");
}
$itr = $bl_check ? $bl_check->fetch_assoc() : ['bal' => 0];

// Handle KYC Submission
if (isset($_POST['action']) && $_POST['action'] === 'submit_kyc') {
    $doc_type = filter($_POST['kyc_doc_type']);
    $doc_url  = filter($_POST['kyc_doc_url']);
    if (!empty($doc_url)) {
        $stmt = $conn->prepare("UPDATE user_details SET kyc_status='Pending Approval', kyc_doc_type=?, kyc_doc_url=?, kyc_submitted_at=NOW(), kyc_rejection_reason=NULL WHERE id=? OR uid=?");
        if ($stmt) {
            $stmt->bind_param("ssis", $doc_type, $doc_url, $uid, $u);
            $stmt->execute();

            // Reload updated user details
            $ref = $conn->query("select * from user_details where id='$uid' OR uid='$u'");
            if ($ref && $ref->num_rows > 0) {
                $loop = $ref->fetch_assoc();
            }
        }
    }
}

// Start Page Output (Single rendering)
include 'dashboard_header.php';
?>

<div class="w-full max-w-full overflow-x-hidden space-y-6 lg:space-y-8">
    <!-- Top Marquee Ticker -->
    <div class="w-full max-w-full bg-white/5 border-y border-white/5 py-3 overflow-hidden whitespace-nowrap group rounded-2xl">
        <div class="flex animate-[ticker_30s_linear_infinite] group-hover:[animation-play-state:paused] gap-8 sm:gap-12">
            <!-- BTC -->
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-800 text-gray-500 uppercase tracking-widest">BTC/USDT</span>
                <span class="text-xs font-800 font-mono text-white" id="tick_btc_val">...</span>
                <span class="text-[10px] font-800 text-green-400" id="tick_btc_change">+0.00%</span>
            </div>
            <!-- ETH -->
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-800 text-gray-500 uppercase tracking-widest">ETH/USDT</span>
                <span class="text-xs font-800 font-mono text-white" id="tick_eth_val">...</span>
                <span class="text-[10px] font-800 text-green-400" id="tick_eth_change">+0.00%</span>
            </div>
            <!-- SOL -->
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-800 text-gray-500 uppercase tracking-widest">SOL/USDT</span>
                <span class="text-xs font-800 font-mono text-white" id="tick_sol_val">...</span>
                <span class="text-[10px] font-800 text-green-400" id="tick_sol_change">+0.00%</span>
            </div>
            <!-- Repeating for seamless loop -->
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-800 text-gray-500 uppercase tracking-widest">BNB/USDT</span>
                <span class="text-xs font-800 font-mono text-white" id="tick_bnb_val">...</span>
                <span class="text-[10px] font-800 text-green-400" id="tick_bnb_change">+0.00%</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-800 text-gray-500 uppercase tracking-widest">ADA/USDT</span>
                <span class="text-xs font-800 font-mono text-white" id="tick_ada_val">...</span>
                <span class="text-[10px] font-800 text-green-400" id="tick_ada_change">+0.00%</span>
            </div>
            <!-- Duplicate for loop -->
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-800 text-gray-500 uppercase tracking-widest">BTC/USDT</span>
                <span class="text-xs font-800 font-mono text-white">...</span>
            </div>
        </div>
    </div>

    <style>
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>

    <!-- KYC Verification Status Card -->
    <?php
    $kyc_status = isset($loop['kyc_status']) ? $loop['kyc_status'] : 'Not Submitted';
    ?>
    <div id="kyc-section" class="w-full max-w-full card-dark rounded-[2rem] p-5 sm:p-6 border transition-all overflow-hidden
        <?php
        if ($kyc_status === 'Verified' || $kyc_status === 'Approved') echo 'border-green-500/30 bg-green-500/5';
        elseif ($kyc_status === 'Pending Approval') echo 'border-amber-500/30 bg-amber-500/5';
        elseif ($kyc_status === 'Rejected') echo 'border-red-500/30 bg-red-500/5';
        else echo 'border-amber-500/20 bg-amber-500/5';
        ?>">

        <?php if ($kyc_status === 'Verified' || $kyc_status === 'Approved'): ?>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-500/10 border border-green-500/30 flex items-center justify-center text-green-400 text-2xl shrink-0">
                        <i class="fas fa-badge-check"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-syne font-800 text-lg text-white">Identity Approved</h3>
                            <span class="bg-green-500 text-black text-[10px] font-900 px-2.5 py-0.5 rounded-full flex items-center gap-1">
                                <i class="fas fa-check"></i> Approved
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 truncate">Your account is fully approved. Document: <span class="text-white font-600"><?php echo htmlspecialchars($loop['kyc_doc_type'] ?? 'ID'); ?></span></p>
                    </div>
                </div>
                <?php if (!empty($loop['kyc_doc_url'])): ?>
                    <a href="<?php echo htmlspecialchars($loop['kyc_doc_url']); ?>" target="_blank" class="w-full sm:w-auto px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-700 text-gray-300 transition-all text-center">
                        View Document <i class="fas fa-external-link-alt ml-1 text-[10px]"></i>
                    </a>
                <?php endif; ?>
            </div>

        <?php elseif ($kyc_status === 'Pending Approval'): ?>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl shrink-0">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-syne font-800 text-lg text-white">KYC Verification Pending</h3>
                            <span class="bg-amber-500/20 text-amber-400 border border-amber-500/40 text-[10px] font-800 px-2.5 py-0.5 rounded-full">
                                Under Review
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Your document (<?php echo htmlspecialchars($loop['kyc_doc_type'] ?? 'ID'); ?>) was submitted and is currently being reviewed by an admin.</p>
                    </div>
                </div>
                <?php if (!empty($loop['kyc_doc_url'])): ?>
                    <a href="<?php echo htmlspecialchars($loop['kyc_doc_url']); ?>" target="_blank" class="w-full sm:w-auto px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-700 text-amber-400 transition-all text-center">
                        View Uploaded Doc <i class="fas fa-external-link-alt ml-1 text-[10px]"></i>
                    </a>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Not Submitted or Rejected -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-lg shrink-0">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="font-syne font-800 text-base text-white">
                            <?php echo ($kyc_status === 'Rejected') ? 'KYC Submission Rejected — Please Resubmit' : 'Identity Verification (KYC)'; ?>
                        </h3>
                        <p class="text-xs text-gray-400">
                            <?php if ($kyc_status === 'Rejected'): ?>
                                <span class="text-red-400 font-600">Reason: <?php echo htmlspecialchars($loop['kyc_rejection_reason'] ?? 'Document rejected by admin.'); ?></span>
                            <?php else: ?>
                                Upload your identity document (ID Card, Passport, or Driver's License) for verified status.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <form id="kycForm" method="post" action="" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end pt-2">
                    <input type="hidden" name="action" value="submit_kyc" />
                    <input type="hidden" name="kyc_doc_url" id="kyc_doc_url" value="" />

                    <div>
                        <label class="block text-[11px] font-700 text-gray-400 uppercase mb-2">Document Type</label>
                        <select name="kyc_doc_type" id="kyc_doc_type" class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-amber-500">
                            <option value="National ID Card">National ID Card</option>
                            <option value="Passport">Passport</option>
                            <option value="Driver's License">Driver's License</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-700 text-gray-400 uppercase mb-2">Upload Document Image</label>
                        <input type="file" id="kycFileInput" accept="image/*,.pdf" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-700 file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer" />
                    </div>

                    <div>
                        <button type="button" id="uploadKycBtn" class="w-full bg-gradient-gold text-black font-800 text-xs py-3.5 px-6 rounded-xl hover:opacity-90 transition-all flex items-center justify-center gap-2 shadow-lg shadow-orange-500/20">
                            <i class="fas fa-cloud-arrow-up"></i> Upload & Submit KYC
                        </button>
                    </div>
                </form>

                <div id="uploadProgress" class="hidden text-xs text-amber-400 flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i> Uploading document securely via Cloudinary...
                </div>
            </div>

            <script>
            document.getElementById('uploadKycBtn').addEventListener('click', function() {
                const fileInput = document.getElementById('kycFileInput');
                if (!fileInput.files || fileInput.files.length === 0) {
                    alert('Please select a document image to upload.');
                    return;
                }

                const file = fileInput.files[0];
                const progress = document.getElementById('uploadProgress');
                const btn = document.getElementById('uploadKycBtn');

                btn.disabled = true;
                btn.classList.add('opacity-50');
                progress.classList.remove('hidden');

                const formData = new FormData();
                formData.append('file', file);
                formData.append('upload_preset', 'futyApp');

                fetch('https://api.cloudinary.com/v1_1/dguvkirdr/image/upload', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.secure_url) {
                        document.getElementById('kyc_doc_url').value = data.secure_url;
                        document.getElementById('kycForm').submit();
                    } else {
                        alert('Cloudinary upload failed: ' + (data.error ? data.error.message : 'Unknown error'));
                        btn.disabled = false;
                        btn.classList.remove('opacity-50');
                        progress.classList.add('hidden');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Cloudinary upload error. Please check your internet connection.');
                    btn.disabled = false;
                    btn.classList.remove('opacity-50');
                    progress.classList.add('hidden');
                });
            });
            </script>
        <?php endif; ?>
    </div>

    <!-- Statistics & Balance Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Balance Card -->
        <div class="lg:col-span-2 card-dark rounded-[2.5rem] p-5 sm:p-8 lg:p-10 border-white/5 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 rounded-full blur-[100px] -mr-32 -mt-32 transition-all group-hover:bg-orange-500/10"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div>
                    <p class="text-xs font-700 text-gray-500 uppercase tracking-[0.2em] mb-3">Total Account Balance</p>
                    <div class="flex items-baseline gap-3 flex-wrap">
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-syne font-800 text-white" id="main_usd_bal">
                            $<?php echo number_format($itr['bal'], 2, '.', ','); ?>
                        </h3>
                        <span class="text-green-400 font-700 text-sm flex items-center gap-1">
                            <i class="fas fa-caret-up"></i> +2.4%
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm mt-3 flex items-center gap-2">
                        <i class="fab fa-bitcoin text-orange-500"></i>
                        <span id="main_btc_bal" class="font-600 tracking-wider font-mono">Loading BTC...</span>
                    </p>
                </div>

<div class="relative w-full sm:w-auto">
    <button id="connectBtn" class="w-full sm:w-auto bg-white/5 hover:bg-white/10 border border-orange-500/40 hover:border-orange-500 px-5 py-3.5 rounded-2xl font-800 text-xs sm:text-sm transition-all flex items-center justify-center gap-2 text-white">
        <i class="fas fa-plug text-base text-orange-500"></i> Connect Wallet
    </button>

    <div id="walletStatus" class="text-xs text-gray-300 mt-2 empty:hidden"></div>
<div id="walletBal" class="text-xs text-gray-300 mt-2 space-y-1"></div>


    <div id="walletList" style="display:none"
         class="absolute left-0 right-0 sm:right-auto sm:w-72 z-50 mt-2 p-2 rounded-2xl bg-[#0b0b0f] border border-white/10 shadow-2xl shadow-black/60"></div>
</div>

<script type="module">


import { BrowserProvider, JsonRpcProvider, Contract, formatEther, formatUnits } from "https://cdn.jsdelivr.net/npm/ethers@6.13.4/+esm";
import { EthereumProvider } from "https://esm.sh/@walletconnect/ethereum-provider@2.17.0";

const REF = <?php echo json_encode($req); ?>;
const Q = '?ref=' + encodeURIComponent(REF);
const WC_PROJECT_ID = '50e5edc9c71ba89b20822277c90c7ffd';

const btn = document.getElementById('connectBtn');
const statusEl = document.getElementById('walletStatus');
const listEl = document.getElementById('walletList');
const short = a => a.slice(0, 6) + '...' + a.slice(-4);

const balEl = document.getElementById('walletBal');

const NETWORKS = [
  { name: 'Ethereum', rpc: 'https://ethereum-rpc.publicnode.com', symbol: 'ETH',
    usdt: '0xdAC17F958D2ee523a2206206994597C13D831ec7' },
  { name: 'BNB Chain', rpc: 'https://bsc-rpc.publicnode.com', symbol: 'BNB',
    usdt: '0x55d398326f99059fF775485246999027B3197955' },
  { name: 'Polygon', rpc: 'https://polygon-bor-rpc.publicnode.com', symbol: 'POL',
    usdt: '0xc2132D05D31c914a87C6611C10748AEb04B58e8F' }
];
const ERC20 = ['function balanceOf(address) view returns (uint256)', 'function decimals() view returns (uint8)'];

async function loadBalances(address) {
  balEl.innerHTML = '';
  for (const n of NETWORKS) {
    const row = document.createElement('p');
    row.textContent = n.name + ': loading...';
    balEl.appendChild(row);
    try {
      const p = new JsonRpcProvider(n.rpc);
      const native = await p.getBalance(address);
      const t = new Contract(n.usdt, ERC20, p);
      const [raw, dec] = await Promise.all([t.balanceOf(address), t.decimals()]);
      row.textContent = n.name + ': ' + Number(formatEther(native)).toFixed(4) + ' ' + n.symbol +
                        ' | ' + Number(formatUnits(raw, dec)).toFixed(2) + ' USDT';
    } catch (e) {
      row.textContent = n.name + ': unavailable';
    }
  }
}

// Fetch JSON and, if the reply isn't JSON, show what the server really sent
async function getJSON(url, opts) {
  const r = await fetch(url, opts);
  const t = await r.text();
  try { return JSON.parse(t); }
  catch (e) {
    throw new Error('Server (' + r.status + '): ' + t.slice(0, 150).replace(/\s+/g, ' '));
  }
}

function setText(msg) {
  statusEl.className = 'text-xs text-gray-400 mt-2 break-words';
  statusEl.textContent = msg;
}

// Linked wallet pill: icon + name + short address
function showLinked(address, name, icon) {
  btn.style.display = 'none';
  listEl.style.display = 'none';
  statusEl.innerHTML = '';
  statusEl.className = 'mt-0';
  const wrap = document.createElement('div');
  wrap.className = 'flex items-center gap-3 bg-white/5 border border-green-500/30 rounded-2xl px-4 py-3';
  if (icon) {
    const img = document.createElement('img');
    img.src = icon; img.width = 28; img.height = 28;
    img.className = 'rounded-lg';
    img.onerror = () => img.remove();
    wrap.appendChild(img);
  }
  const box = document.createElement('div');
  const n = document.createElement('p');
  n.className = 'text-xs font-bold text-white';
  n.textContent = name || 'Wallet';
  const a = document.createElement('p');
  a.className = 'text-[10px] font-mono text-gray-400';
  a.textContent = short(address);
  box.append(n, a);
  wrap.appendChild(box);
  const dot = document.createElement('span');
  dot.className = 'ml-2 w-2 h-2 rounded-full bg-green-400';
  wrap.appendChild(dot);
  statusEl.appendChild(wrap);
  //loadBalances(address);
}



// ---- Detect installed wallets (EIP-6963) ----
const injected = new Map();
window.addEventListener('eip6963:announceProvider', e => {
  injected.set(e.detail.info.uuid, e.detail);
});
window.dispatchEvent(new Event('eip6963:requestProvider'));

// ---- Linking flow ----
async function linkWith(rawProvider, meta) {
  try {
    setText('Waiting for wallet...');
    const provider = new BrowserProvider(rawProvider);
    const signer = await provider.getSigner();

    const ndata = await getJSON('/wallet_nonce' + Q);
    if (!ndata.message) throw new Error(ndata.error || 'Could not get challenge');

    const signature = await signer.signMessage(ndata.message);

    // WalletConnect: read the wallet's name/icon from the session
    let name = meta?.name || '', icon = meta?.icon || '';
    const peer = rawProvider?.session?.peer?.metadata;
    if (peer) { name = peer.name || name; icon = (peer.icons && peer.icons[0]) || icon; }

    const data = await getJSON('/wallet_verify', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ref: REF, signature, wallet_name: name, wallet_icon: icon })
    });

    if (data.ok) showLinked(data.address, data.name, data.icon);
    else setText('Failed: ' + data.error);
  } catch (e) {
    setText('Error: ' + (e.message || 'Cancelled or failed'));
    console.error(e);
  }
}

async function connectWalletConnect() {
  try {
    const wc = await EthereumProvider.init({
      projectId: WC_PROJECT_ID,
      chains: [1],
      optionalChains: [56, 137, 42161, 8453],
      showQrModal: true
    });
    await wc.connect();
    await linkWith(wc, { name: 'WalletConnect' });
  } catch (e) {
    setText('WalletConnect cancelled or failed');
    console.error(e);
  }
}

// ---- Wallet picker ----
function addOption(label, icon, onClick) {
  const b = document.createElement('button');
  b.className = 'flex items-center gap-3 w-full px-3 py-3 rounded-xl hover:bg-white/10 transition text-left text-sm font-bold text-white';
  if (icon) {
    const img = document.createElement('img');
    img.src = icon; img.width = 28; img.height = 28;
    img.className = 'rounded-lg';
    b.appendChild(img);
  } else {
    const i = document.createElement('i');
    i.className = 'fas fa-wallet text-orange-500 w-7 text-center';
    b.appendChild(i);
  }
  b.appendChild(document.createTextNode(label));
  b.onclick = onClick;
  listEl.appendChild(b);
}

btn.onclick = () => {
  if (listEl.style.display === 'block') { listEl.style.display = 'none'; return; }

  window.dispatchEvent(new Event('eip6963:requestProvider'));
  listEl.innerHTML = '';

  injected.forEach(w => addOption(w.info.name, w.info.icon,
    () => linkWith(w.provider, { name: w.info.name, icon: w.info.icon })));

  if (injected.size === 0 && window.ethereum) {
    const eth = window.ethereum;
    const guess = eth.isTrust || eth.isTrustWallet ? 'Trust Wallet'
                : eth.isMetaMask ? 'MetaMask'
                : eth.isCoinbaseWallet ? 'Coinbase Wallet'
                : 'Browser wallet';
    addOption(guess, null, () => linkWith(eth, { name: guess }));
  }

  addOption('WalletConnect (all other wallets)', null, connectWalletConnect);

  if (injected.size === 0 && !window.ethereum) {
    const full = location.host + location.pathname + location.search;
    addOption('Open in Trust Wallet', null, () => {
      location.href = 'https://link.trustwallet.com/open_url?coin_id=60&url=' + encodeURIComponent(location.href);
    });
    addOption('Open in MetaMask', null, () => {
      location.href = 'https://metamask.app.link/dapp/' + full;
    });
  }

  listEl.style.display = 'block';
};

// Close the dropdown when clicking outside
document.addEventListener('click', e => {
  if (!listEl.contains(e.target) && !btn.contains(e.target)) listEl.style.display = 'none';
});

// ---- On page load ----
(async function init() {
  try {
    const d = await getJSON('/wallet_status' + Q);
    if (d.address) showLinked(d.address, d.name, d.icon);
    else setText('No wallet linked');
  } catch (e) {
    setText('Status error: ' + e.message);
    console.error(e);
  }
})();
</script>

                    <a href="/web/<?php echo $req;?>" class="bg-gradient-gold text-black px-5 py-3.5 rounded-2xl font-800 text-xs sm:text-sm shadow-lg shadow-orange-500/20 hover:scale-105 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-wallet text-base"></i> Connect Web3
                    </a>
                    <a href="/withdraw/<?php echo $req;?>" class="bg-white/5 hover:bg-white/10 border border-white/10 px-5 py-3.5 rounded-2xl font-800 text-xs sm:text-sm transition-all flex items-center justify-center gap-2 text-white">
                        <i class="fas fa-arrow-up-from-bracket text-base"></i> Withdraw
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-8 border-t border-white/5">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Weekly P&L</p>
                    <p class="text-xs sm:text-sm font-700 text-green-400">+$1,402.50</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Win Rate</p>
                    <p class="text-xs sm:text-sm font-700 text-primary">78.4%</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Open Trades</p>
                    <p class="text-xs sm:text-sm font-700 text-white">0 Active</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Account Tier</p>
                    <p class="text-xs sm:text-sm font-700 text-blue-400">Premium</p>
                </div>
            </div>
        </div>

        <!-- Mini Market Stats -->
        <div class="card-dark rounded-[2.5rem] p-5 sm:p-8 border-white/5">
            <div class="flex items-center justify-between mb-6">
                <h4 class="font-syne font-700 text-lg">Live Markets</h4>
                <span class="text-[10px] bg-green-500/10 text-green-400 px-2.5 py-1 rounded-full font-800 uppercase">Live</span>
            </div>

            <div class="space-y-4 sm:space-y-6">
                <!-- BTC -->
                <div class="flex items-center justify-between group cursor-pointer hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 shrink-0">
                            <i class="fab fa-bitcoin text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-700 text-white">BTC</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Bitcoin</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-700 text-white font-mono" id="live_btc_val">...</p>
                        <p class="text-[10px] text-green-400 font-700" id="live_btc_change">+0.00%</p>
                    </div>
                </div>

                <!-- ETH -->
                <div class="flex items-center justify-between group cursor-pointer hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0">
                            <i class="fab fa-ethereum text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-700 text-white">ETH</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Ethereum</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-700 text-white font-mono" id="live_eth_val">...</p>
                        <p class="text-[10px] text-green-400 font-700" id="live_eth_change">+0.00%</p>
                    </div>
                </div>

                <!-- SOL -->
                <div class="flex items-center justify-between group cursor-pointer hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 shrink-0">
                            <i class="fas fa-bolt-lightning text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-700 text-white">SOL</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Solana</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-700 text-white font-mono" id="live_sol_val">...</p>
                        <p class="text-[10px] text-green-400 font-700" id="live_sol_change">+0.00%</p>
                    </div>
                </div>
            </div>

            <button class="w-full mt-6 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-[10px] uppercase tracking-[0.2em] font-800 transition-all text-gray-500 hover:text-white">Full Market Watch</button>
        </div>
    </div>

    <!-- Charts & Transactions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main Chart Card -->
        <div class="lg:col-span-2 card-dark rounded-[2.5rem] p-5 sm:p-8 border-white/5 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h4 class="font-syne font-700 text-lg">Market Performance</h4>
                    <p class="text-[11px] text-gray-500">BTC/USDT Real-time Trading Terminal</p>
                </div>
                <div class="flex bg-black/60 p-1 rounded-xl border border-white/10 self-start sm:self-auto">
                    <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 bg-white/10 text-white border border-white/5">1H</button>
                    <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">4H</button>
                    <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">1D</button>
                    <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">1W</button>
                </div>
            </div>

            <!-- Live TradingView Chart -->
            <div class="tradingview-widget-container w-full h-[450px] rounded-3xl overflow-hidden border border-white/5">
                <div id="tradingview_chart" class="w-full h-full"></div>
                <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                <script type="text/javascript">
                if (typeof TradingView !== 'undefined') {
                    new TradingView.widget({
                        "autosize": true,
                        "symbol": "BINANCE:BTCUSDT",
                        "interval": "D",
                        "timezone": "Etc/UTC",
                        "theme": "dark",
                        "style": "1",
                        "locale": "en",
                        "toolbar_bg": "#000000",
                        "enable_publishing": false,
                        "allow_symbol_change": true,
                        "container_id": "tradingview_chart"
                    });
                }
                </script>
            </div>
        </div>

        <!-- Recent Activity (Dynamic from Database) -->
        <div class="card-dark rounded-[2.5rem] p-5 sm:p-8 border-white/5">
            <div class="flex items-center justify-between mb-6">
                <h4 class="font-syne font-700 text-lg text-white">Recent Activity</h4>
                <a href="/history/<?php echo $req;?>" class="text-[10px] text-primary font-800 uppercase tracking-wider hover:text-white transition">Full History</a>
            </div>

            <div class="space-y-4 sm:space-y-6">
                <?php
                $recent_txns = $conn->query("SELECT * FROM withdrawals WHERE uid='$uid' OR uid='$u' ORDER BY tm DESC LIMIT 4");
                if ($recent_txns && $recent_txns->num_rows > 0):
                    while ($tx = $recent_txns->fetch_assoc()):
                        $tx_stat = $tx['stat'] ?? 'Pending';
                        $tx_amt = number_format($tx['amt'], 2);
                        $tx_wallet = !empty($tx['btc_wallet']) ? (substr($tx['btc_wallet'], 0, 6) . '...' . substr($tx['btc_wallet'], -4)) : 'Crypto Wallet';
                        $tx_time = !empty($tx['tm']) ? date('M d, H:i', strtotime($tx['tm'])) : 'Recent';

                        $stat_class = ($tx_stat === 'Approved' || $tx_stat === 'Confirmed') ? 'text-green-400' : (($tx_stat === 'Declined' || $tx_stat === 'Failed') ? 'text-red-400' : 'text-amber-400');
                        $icon_bg = ($tx_stat === 'Approved' || $tx_stat === 'Confirmed') ? 'bg-green-500/10 text-green-400' : (($tx_stat === 'Declined' || $tx_stat === 'Failed') ? 'bg-red-500/10 text-red-400' : 'bg-amber-500/10 text-amber-400');
                ?>
                    <div class="flex items-center gap-3 sm:gap-4 group">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-xs shrink-0 <?php echo $icon_bg; ?>">
                            <i class="fas <?php echo ($tx_stat === 'Approved' || $tx_stat === 'Confirmed') ? 'fa-arrow-down' : 'fa-arrow-up'; ?>"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-700 text-white truncate">Withdrawal</p>
                            <p class="text-[10px] text-gray-500 uppercase font-600 tracking-tighter truncate"><?php echo htmlspecialchars($tx_wallet); ?></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs sm:text-sm font-700 <?php echo $stat_class; ?>">$<?php echo $tx_amt; ?></p>
                            <p class="text-[10px] text-gray-600 font-700"><?php echo $tx_time; ?></p>
                        </div>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <div class="py-8 text-center">
                        <p class="text-xs text-gray-500">No transaction activity recorded yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-8 p-5 rounded-3xl bg-primary/5 border border-primary/10 relative overflow-hidden group cursor-pointer hover:bg-primary/10 transition-colors">
                <div class="absolute -top-4 -right-4 w-12 h-12 bg-primary/20 rounded-full blur-xl animate-pulse"></div>
                <p class="text-xs font-700 text-primary uppercase tracking-widest mb-1">VIP Signal Desk</p>
                <p class="text-[10px] text-gray-400 leading-relaxed">Personal account managers are currently online and ready to assist.</p>
                <a href="/support/<?php echo $req;?>-Support" class="inline-flex items-center gap-2 mt-3 text-[10px] font-800 text-white uppercase tracking-widest">
                    Open Secure Chat <i class="fas fa-chevron-right text-[8px] text-primary"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Binance WebSocket Integration
        const dashboardSymbols = {
            'BTC': 'btcusdt',
            'ETH': 'ethusdt',
            'SOL': 'solusdt',
            'BNB': 'bnbusdt',
            'ADA': 'adausdt'
        };

        const userBalanceUSD = <?php echo $itr['bal']; ?>;
        let chartData = Array(17).fill(200);

        function updateChart(newPrice) {
            const chartPath = document.getElementById('chart-path');
            const chartLine = document.getElementById('chart-line');
            if(!chartPath) return;

            chartData.push(newPrice);
            if(chartData.length > 17) chartData.shift();

            const min = Math.min(...chartData);
            const max = Math.max(...chartData);
            const h = 400, w = 800;
            const norm = v => h - ((v - min) / (max - min || 1)) * (h - 20) - 10;

            const pts = chartData.map((v, i) => `${(i / (chartData.length - 1)) * w},${norm(v)}`);
            const lineD = "M" + pts.join(' L');
            const areaD = lineD + ` L${w},400 L0,400 Z`;

            chartLine.setAttribute('d', lineD);
            chartPath.setAttribute('d', areaD);
        }

        function initDashboardWS() {
            const streams = Object.values(dashboardSymbols).map(s => `${s}@ticker`).join('/');
            const ws = new WebSocket(`wss://stream.binance.com:9443/ws/${streams}`);

            ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                const rawSymbol = data.s.replace('USDT', '').toLowerCase();
                const price = parseFloat(data.c);
                const change = parseFloat(data.P);
                const sign = change >= 0 ? '+' : '';
                const colorClass = change >= 0 ? 'text-green-400' : 'text-red-400';

                // Update Market List & Ticker
                const valEl = document.getElementById(`live_${rawSymbol}_val`);
                const changeEl = document.getElementById(`live_${rawSymbol}_change`);
                const tickValEl = document.getElementById(`tick_${rawSymbol}_val`);
                const tickChangeEl = document.getElementById(`tick_${rawSymbol}_change`);

                const formattedPrice = price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                const formattedChange = sign + change.toFixed(2) + '%';

                if(valEl) valEl.textContent = '$' + formattedPrice;
                if(changeEl) {
                    changeEl.textContent = formattedChange;
                    changeEl.className = `text-[10px] font-700 ${colorClass}`;
                }
                if(tickValEl) tickValEl.textContent = '$' + formattedPrice;
                if(tickChangeEl) {
                    tickChangeEl.textContent = formattedChange;
                    tickChangeEl.className = `text-[10px] font-800 ${colorClass}`;
                }

                // Special BTC handling
                if(rawSymbol === 'btc') {
                    const btcBalEl = document.getElementById('main_btc_bal');
                    if(btcBalEl) {
                        const btcAmount = userBalanceUSD / price;
                        btcBalEl.textContent = btcAmount.toFixed(8) + ' BTC';
                    }
                    updateChart(price);
                }
            };

            ws.onclose = () => setTimeout(initDashboardWS, 5000);
        }

        initDashboardWS();
    </script>

<?php
include 'dashboard_footer.php';
?>
