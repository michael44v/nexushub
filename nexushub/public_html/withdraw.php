<?php
include 'cdn.php';
include 'config.php';
$usr_string = $req;

$numbers = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$numbers'");
if($chk->num_rows > 0) {
    while($loop = $chk->fetch_assoc()) {
        $uid = $loop['uid'];

        // Handle withdrawal request
        if(isset($_POST['btc_wallet'])){
            $amt = filter($_POST['exact_amount']);
            $wal = filter($_POST['btc_wallet']);
            $n = $loop['uname'];

            $qry =$conn->query("insert into withdrawals(uid,uname,btc_wallet,amt) values('$uid','$n','$wal','$amt')");

            if($qry) {
                $message = "Your withdrawal has been accepted and is being processed.";
                $message_type = "success";
            }
        }

        $bl_check = $conn->query("select * from bal where uid='$uid'");
        $itr = $bl_check->fetch_assoc();

        include 'dashboard_header.php';
        ?>

        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-10">
                <div class="w-20 h-20 bg-gradient-gold rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                    <i class="fas fa-arrow-up-from-bracket text-black text-3xl"></i>
                </div>
                <h2 class="font-syne text-3xl font-800 text-white mb-3">Withdraw Funds</h2>
                <p class="text-gray-400 max-w-sm mx-auto leading-relaxed">Securely transfer your BTC to an external wallet.</p>
            </div>

            <?php if(isset($message)): ?>
                <div class="mb-8 p-6 rounded-[2rem] bg-green-500/10 border border-green-500/20 text-green-400 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="font-700"><?php echo $message; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card-dark rounded-[2.5rem] p-8 lg:p-12 border-white/5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[100px] -mr-32 -mt-32"></div>

                <form method="post" action="/withdraw/<?php echo $req;?>" class="relative z-10 space-y-8">
                    <!-- Network Info -->
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                                <i class="fab fa-bitcoin text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider">Network</p>
                                <p class="text-sm font-700 text-white">Bitcoin Mainnet</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-700 tracking-wider">Available</p>
                            <p class="text-sm font-800 text-primary" id="disp_bal">
                                $<?php echo number_format($itr['bal'], 2, '.', ','); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Wallet Address -->
                    <div class="space-y-3">
                        <label class="block text-xs font-700 text-gray-500 uppercase tracking-widest ml-1">Destination BTC Wallet</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-500 transition-colors group-focus-within:text-primary">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <input type="text" name="btc_wallet" placeholder="Enter Bitcoin Address" required
                                class="w-full bg-black/40 border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all font-mono text-sm">
                        </div>
                        <p class="text-[10px] text-gray-500 ml-1 italic"><i class="fas fa-info-circle mr-1"></i> Ensure the address is a BTC network address to avoid loss.</p>
                    </div>

                    <!-- Amount -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center ml-1">
                            <label class="block text-xs font-700 text-gray-500 uppercase tracking-widest">Amount to Withdraw</label>
                            <button type="button" onclick="setMax();" class="text-[10px] font-800 text-primary uppercase tracking-widest hover:text-white transition">Use Max</button>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-500 transition-colors group-focus-within:text-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <input type="number" step="any" id="amt_input" name="exact_amount" placeholder="0.00" required
                                class="w-full bg-black/40 border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all font-mono text-lg">
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="p-6 bg-black/40 rounded-3xl border border-white/5 space-y-3">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Network Fee</span>
                            <span class="text-white font-700">0.0001 BTC</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Processing Time</span>
                            <span class="text-white font-700">~ 15-60 Minutes</span>
                        </div>
                        <div class="pt-3 border-t border-white/5 flex justify-between">
                            <span class="text-sm font-700 text-white">Estimated Total</span>
                            <span class="text-sm font-800 text-primary" id="est_total">$0.00</span>
                        </div>
                    </div>

                    <button type="submit" <?php echo ($itr['bal'] <= 0) ? 'disabled' : ''; ?>
                        class="w-full bg-gradient-gold text-black font-800 py-5 rounded-2xl shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 font-syne uppercase tracking-wider <?php echo ($itr['bal'] <= 0) ? 'opacity-50 grayscale cursor-not-allowed' : ''; ?>">
                        Review Withdrawal <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center">
                <a href="/history/<?php echo $req; ?>" class="text-xs font-700 text-gray-500 uppercase tracking-widest hover:text-primary transition">View Past Withdrawals</a>
            </div>
        </div>

        <script>
            const balance = <?php echo $itr['bal']; ?>;
            const input = document.getElementById('amt_input');
            const total = document.getElementById('est_total');

            function setMax() {
                input.value = balance;
                updateTotal();
            }

            function updateTotal() {
                const val = parseFloat(input.value) || 0;
                total.textContent = '$' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            input.oninput = updateTotal;
        </script>

        <?php
        include 'dashboard_footer.php';
    }
}
?>
