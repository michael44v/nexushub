<?php
include 'cdn.php';
include 'config.php';
$u = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$u' OR id='$u'");
if (!$chk || $chk->num_rows == 0) {
    $chk = $conn->query("select * from user_details limit 1");
}

if ($chk && $chk->num_rows > 0) {
    $loop = $chk->fetch_assoc();
} else {
    $loop = [
        'id' => 1,
        'uid' => !empty($u) ? $u : '1001',
        'uname' => 'Trader',
        'kyc_status' => 'Not Submitted'
    ];
}

$u = isset($loop['uid']) ? $loop['uid'] : (!empty($u) ? $u : '1001');

include 'dashboard_header.php';
?>

<div class="max-w-5xl mx-auto space-y-6 sm:space-y-8 w-full max-w-full overflow-x-hidden">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-syne text-2xl sm:text-3xl font-800 text-white mb-1">Transaction History</h2>
            <p class="text-gray-400 text-xs sm:text-sm">Monitor your deposits and withdrawals across all networks.</p>
        </div>

        <div class="relative w-full md:w-80 group">
            <form action='/history/<?php echo $req;?>' method='post'>
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-primary transition-colors">
                    <i class="fas fa-search"></i>
                </div>
                <input type="search" name="query" placeholder="TXN ID or Wallet..."
                    class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all text-xs sm:text-sm">
            </form>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 sm:gap-4 overflow-x-auto pb-1">
        <button class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-gold text-black rounded-xl font-800 text-[10px] sm:text-[11px] uppercase tracking-wider whitespace-nowrap">All Assets</button>
        <button class="px-4 sm:px-6 py-2 sm:py-2.5 bg-white/5 border border-white/10 text-gray-400 rounded-xl font-800 text-[10px] sm:text-[11px] uppercase tracking-wider whitespace-nowrap hover:text-white transition">Withdrawals</button>
        <button class="px-4 sm:px-6 py-2 sm:py-2.5 bg-white/5 border border-white/10 text-gray-400 rounded-xl font-800 text-[10px] sm:text-[11px] uppercase tracking-wider whitespace-nowrap hover:text-white transition">Deposits</button>
    </div>

    <!-- History Table/List -->
    <div class="card-dark rounded-2xl sm:rounded-[2.5rem] border-white/5 overflow-hidden">
        <?php
        $query_str = "select * from withdrawals where uid = '$u'";
        if(isset($_POST['query']) && !empty($_POST['query'])) {
            $q = filter($_POST['query']);
            if(is_numeric($q)) {
                $norm_q = $q / 47229;
                $query_str .= " AND (txn_id = '$norm_q' OR btc_wallet LIKE '%$q%')";
            } else {
                $query_str .= " AND btc_wallet LIKE '%$q%'";
            }
        }
        $query_str .= " ORDER BY tm DESC";

        $txn = $conn->query($query_str);
        if($txn && $txn->num_rows > 0): ?>
            <div class="w-full max-w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-full">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/5 text-[9px] sm:text-[10px] font-800 text-gray-500 uppercase tracking-widest">
                            <th class="px-3 sm:px-6 py-3 sm:py-4">Type</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4">Asset</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 hidden md:table-cell">Destination</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4">Amount</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4">Status</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 hidden sm:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs">
                        <?php while($row = $txn->fetch_assoc()): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-3 sm:px-6 py-3.5 sm:py-5">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 text-[10px] shrink-0">
                                            <i class="fas fa-arrow-up"></i>
                                        </div>
                                        <span class="text-xs font-700 text-white">Withdrawal</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-3.5 sm:py-5">
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <i class="fab fa-bitcoin text-orange-500 text-sm"></i>
                                        <span class="text-xs font-600 text-gray-300">BTC</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-3.5 sm:py-5 hidden md:table-cell">
                                    <span class="text-xs font-mono text-gray-500 group-hover:text-gray-300 transition-colors truncate max-w-[120px] inline-block">
                                        <?php echo substr($row['btc_wallet'], 0, 6).'...'.substr($row['btc_wallet'], -6); ?>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3.5 sm:py-5">
                                    <span class="text-xs font-800 text-white font-mono">$<?php echo number_format($row['amt'], 2); ?></span>
                                </td>
                                <td class="px-3 sm:px-6 py-3.5 sm:py-5">
                                    <?php
                                    $stat = $row['stat'];
                                    $cls = "text-yellow-400 bg-yellow-400/10";
                                    $lbl = "Pending";
                                    if($stat == 'Approved' || $stat == 'Confirmed') { $cls = "text-green-400 bg-green-400/10"; $lbl = "Confirmed"; }
                                    elseif($stat == 'Declined' || $stat == 'Failed') { $cls = "text-red-400 bg-red-400/10"; $lbl = "Failed"; }
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-800 uppercase tracking-wider <?php echo $cls; ?>">
                                        <?php echo $lbl; ?>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3.5 sm:py-5 text-xs text-gray-500 font-600 hidden sm:table-cell">
                                    <?php echo date('d M, Y', strtotime($row['tm'])); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="py-16 sm:py-24 text-center px-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <i class="fas fa-folder-open text-gray-600 text-xl sm:text-2xl"></i>
                </div>
                <h4 class="font-syne font-700 text-white mb-2">No Transactions Found</h4>
                <p class="text-xs text-gray-500">Your recent activity will appear here once confirmed.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-8 sm:mt-12 card-dark p-5 sm:p-8 rounded-2xl sm:rounded-[2rem] border-white/5 flex flex-col md:flex-row items-center justify-between gap-4 sm:gap-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary shrink-0">
                <i class="fas fa-file-invoice text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-700 text-white">Export Statement</p>
                <p class="text-[10px] sm:text-xs text-gray-500">Download your history in CSV or PDF format.</p>
            </div>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="flex-1 md:flex-none px-5 py-2.5 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-700 transition-all border border-white/10">CSV</button>
            <button class="flex-1 md:flex-none px-5 py-2.5 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-700 transition-all border border-white/10">PDF Report</button>
        </div>
    </div>
</div>

<?php
include 'dashboard_footer.php';
?>
