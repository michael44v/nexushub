<?php
include 'cdn.php';
include 'config.php';
$usr_string = $req;

// Remove all non-digit characters to get user ID
$numbers = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$numbers'");
if($chk->num_rows > 0) {
    while($loop = $chk->fetch_assoc()) {
        $uid = $loop['uid'];

        // Ensure balance exists
        $bl_check = $conn->query("select * from bal where uid='$uid'");
        if($bl_check->num_rows < 1) {
            $conn->query("insert into bal(uid,bal) values('$uid','0.00')");
            $bl_check = $conn->query("select * from bal where uid='$uid'");
        }
        $itr = $bl_check->fetch_assoc();

        // Start Page Output
        include 'dashboard_header.php';
        ?>

        <!-- Top Marquee Ticker -->
        <div class="mb-8 -mx-4 lg:-mx-10 bg-white/5 border-y border-white/5 py-3 overflow-hidden whitespace-nowrap group">
            <div class="flex animate-[ticker_30s_linear_infinite] group-hover:[animation-play-state:paused] gap-12">
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

        <!-- Statistics & Balance Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 mb-8 lg:mb-12">
            <!-- Balance Card -->
            <div class="lg:col-span-2 card-dark rounded-[2.5rem] p-8 lg:p-10 border-white/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 rounded-full blur-[100px] -mr-32 -mt-32 transition-all group-hover:bg-orange-500/10"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <p class="text-xs font-700 text-gray-500 uppercase tracking-[0.2em] mb-3">Total Account Balance</p>
                        <div class="flex items-baseline gap-3">
                            <h3 class="text-4xl lg:text-5xl font-syne font-800 text-white" id="main_usd_bal">
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

                    <div class="flex flex-wrap gap-3">
                        <a href="/web/<?php echo $req;?>" class="bg-gradient-gold text-black px-6 py-4 rounded-2xl font-800 text-sm shadow-lg shadow-orange-500/20 hover:scale-105 transition-all flex items-center gap-3">
                            <i class="fas fa-wallet text-lg"></i> Connect Web3
                        </a>
                        <a href="/withdraw/<?php echo $req;?>" class="bg-white/5 hover:bg-white/10 border border-white/10 px-6 py-4 rounded-2xl font-800 text-sm transition-all flex items-center gap-3 text-white">
                            <i class="fas fa-arrow-up-from-bracket text-lg"></i> Withdraw
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10 pt-10 border-t border-white/5">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Weekly P&L</p>
                        <p class="text-sm font-700 text-green-400">+$1,402.50</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Win Rate</p>
                        <p class="text-sm font-700 text-primary">78.4%</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Open Trades</p>
                        <p class="text-sm font-700 text-white">0 Active</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider mb-1">Account Tier</p>
                        <p class="text-sm font-700 text-blue-400">Premium</p>
                    </div>
                </div>
            </div>

            <!-- Mini Market Stats -->
            <div class="card-dark rounded-[2.5rem] p-8 border-white/5">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="font-syne font-700 text-lg">Live Markets</h4>
                    <span class="text-[10px] bg-green-500/10 text-green-400 px-2.5 py-1 rounded-full font-800 uppercase">Live</span>
                </div>

                <div class="space-y-6">
                    <!-- BTC -->
                    <div class="flex items-center justify-between group cursor-pointer hover:bg-white/5 p-2 rounded-xl transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
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
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400">
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
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400">
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

                <button class="w-full mt-8 py-3.5 bg-white/5 hover:bg-white/10 rounded-xl text-[10px] uppercase tracking-[0.2em] font-800 transition-all text-gray-500 hover:text-white">Full Market Watch</button>
            </div>
        </div>

        <!-- Charts & Transactions Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Chart Card -->
            <div class="lg:col-span-2 card-dark rounded-[2.5rem] p-8 border-white/5 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h4 class="font-syne font-700 text-lg">Market Performance</h4>
                        <p class="text-[11px] text-gray-500">BTC/USDT Real-time Trading Terminal</p>
                    </div>
                    <div class="flex bg-black/60 p-1 rounded-xl border border-white/10">
                        <button class="px-4 py-1.5 rounded-lg text-[10px] font-800 bg-white/10 text-white border border-white/5">1H</button>
                        <button class="px-4 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">4H</button>
                        <button class="px-4 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">1D</button>
                        <button class="px-4 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">1W</button>
                    </div>
                </div>

                <!-- TradingView Embed Placeholder -->
                <div class="aspect-[16/9] bg-black/40 rounded-3xl border border-white/5 relative overflow-hidden flex items-center justify-center group cursor-pointer shadow-inner">
                    <div class="absolute inset-0 opacity-30 pointer-events-none transition-opacity group-hover:opacity-50">
                        <svg viewBox="0 0 800 400" class="w-full h-full">
                            <path id="chart-path" d="M0 350 L50 320 L100 340 L150 280 L200 300 L250 220 L300 250 L350 180 L400 200 L450 120 L500 150 L550 80 L600 110 L650 40 L700 70 L750 20 L800 5 L800 400 L0 400 Z" fill="url(#chartGrad)"></path>
                            <path id="chart-line" d="M0 350 L50 320 L100 340 L150 280 L200 300 L250 220 L300 250 L350 180 L400 200 L450 120 L500 150 L550 80 L600 110 L650 40 L700 70 L750 20 L800 5" fill="none" stroke="#f97316" stroke-width="2"></path>
                            <defs>
                                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#f97316" stop-opacity="0.6"/>
                                    <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="relative z-10 text-center">
                        <div class="w-16 h-16 bg-gradient-gold rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-orange-500/10 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-line text-2xl text-black"></i>
                        </div>
                        <p class="text-sm font-700 text-white font-syne">Advanced Pro Chart</p>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest font-700">Open Interactive Terminal</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card-dark rounded-[2.5rem] p-8 border-white/5">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="font-syne font-700 text-lg text-white">Recent Activity</h4>
                    <a href="/history/<?php echo $req;?>" class="text-[10px] text-primary font-800 uppercase tracking-wider hover:text-white transition">Full History</a>
                </div>

                <div class="space-y-6">
                    <!-- Tx 1 -->
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 text-xs transition-colors group-hover:bg-red-500/20">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-700 text-white">BTC Sent</p>
                            <p class="text-[10px] text-gray-500 uppercase font-600 tracking-tighter">TX: 0x8a2...3f1</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-700 text-red-400">-0.50 BTC</p>
                            <p class="text-[10px] text-gray-600 font-700">2h ago</p>
                        </div>
                    </div>

                    <!-- Tx 2 -->
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-500 text-xs transition-colors group-hover:bg-green-500/20">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-700 text-white">USDT Deposit</p>
                            <p class="text-[10px] text-gray-500 uppercase font-600 tracking-tighter">TRC20 Network</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-700 text-green-400">+$2,450.00</p>
                            <p class="text-[10px] text-gray-600 font-700">Yesterday</p>
                        </div>
                    </div>

                    <!-- Tx 3 -->
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 text-xs transition-colors group-hover:bg-blue-500/20">
                            <i class="fas fa-right-left"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-700 text-white">BTC Exchange</p>
                            <p class="text-[10px] text-gray-500 uppercase font-600 tracking-tighter">Internal Swap</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-700 text-white">0.05 BTC</p>
                            <p class="text-[10px] text-gray-600 font-700">Jan 12</p>
                        </div>
                    </div>
                </div>

                <div class="mt-10 p-6 rounded-3xl bg-primary/5 border border-primary/10 relative overflow-hidden group cursor-pointer hover:bg-primary/10 transition-colors">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-primary/20 rounded-full blur-xl animate-pulse"></div>
                    <p class="text-xs font-700 text-primary uppercase tracking-widest mb-1">VIP Signal Desk</p>
                    <p class="text-[10px] text-gray-400 leading-relaxed">Personal account managers are currently online and ready to assist.</p>
                    <a href="/support/<?php echo $req;?>-Support" class="inline-flex items-center gap-2 mt-4 text-[10px] font-800 text-white uppercase tracking-widest">
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
    }
}
?>
