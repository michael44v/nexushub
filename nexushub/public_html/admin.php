<?php
include 'config.php';
include 'admin_header.php';

// Stats queries
$totalSecurities = 0;
$secQuery = $conn->query("SELECT SUM(bal) as total FROM bal WHERE uid != 0");
if ($secQuery && $row = $secQuery->fetch_assoc()) {
    $totalSecurities = floatval($row['total'] ?? 0);
}

$totalInvestors = 0;
$invQuery = $conn->query("SELECT COUNT(*) as total FROM user_details");
if ($invQuery && $row = $invQuery->fetch_assoc()) {
    $totalInvestors = intval($row['total'] ?? 0);
}

$pendingWithdrawals = 0;
$witQuery = $conn->query("SELECT COUNT(*) as total FROM withdrawals WHERE stat='Pending'");
if ($witQuery && $row = $witQuery->fetch_assoc()) {
    $pendingWithdrawals = intval($row['total'] ?? 0);
}

$pendingKyc = 0;
$kycQuery = $conn->query("SELECT COUNT(*) as total FROM user_details WHERE kyc_status='Pending Approval'");
if ($kycQuery && $row = $kycQuery->fetch_assoc()) {
    $pendingKyc = intval($row['total'] ?? 0);
}
?>

<!-- Overview Metrics Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

    <!-- Total Securities -->
    <div class="admin-card p-6 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-700 text-gray-400 uppercase tracking-wider">Total Securities</span>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <h3 class="text-3xl font-syne font-800 text-white mb-1">
            $<?php echo number_format($totalSecurities, 2); ?>
        </h3>
        <p class="text-[11px] text-gray-500">Cumulative User Balances</p>
    </div>

    <!-- Total Investors -->
    <div class="admin-card p-6 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-700 text-gray-400 uppercase tracking-wider">Total Investors</span>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <h3 class="text-3xl font-syne font-800 text-white mb-1">
            <?php echo number_format($totalInvestors); ?>
        </h3>
        <p class="text-[11px] text-gray-500">Registered Accounts</p>
    </div>

    <!-- Pending Withdrawals -->
    <div class="admin-card p-6 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-700 text-gray-400 uppercase tracking-wider">Pending Withdrawals</span>
            <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-lg">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <h3 class="text-3xl font-syne font-800 text-white mb-1">
            <?php echo number_format($pendingWithdrawals); ?>
        </h3>
        <a href="/admin_deposits/new" class="text-[11px] text-red-400 font-700 hover:underline">Review Requests &rarr;</a>
    </div>

    <!-- Pending KYC -->
    <div class="admin-card p-6 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-700 text-gray-400 uppercase tracking-wider">Pending KYC</span>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-lg">
                <i class="fas fa-id-card"></i>
            </div>
        </div>
        <h3 class="text-3xl font-syne font-800 text-white mb-1">
            <?php echo number_format($pendingKyc); ?>
        </h3>
        <a href="/admin_kyc" class="text-[11px] text-purple-400 font-700 hover:underline">Review Submissions &rarr;</a>
    </div>

</div>

<!-- Quick Action Portal -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    <!-- Recent Users Table -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-syne font-800 text-lg text-white">Recent Registered Traders</h3>
            <a href="/user_management" class="text-xs font-700 text-amber-400 hover:underline">View All Users &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-800 uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Connected Wallet</th>
                        <th class="py-3 px-4">KYC Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    <?php
                    $uRes = $conn->query("SELECT * FROM user_details ORDER BY id DESC LIMIT 5");
                    if ($uRes && $uRes->num_rows > 0):
                        while ($u = $uRes->fetch_assoc()):
                            $kStat = $u['kyc_status'] ?? 'Not Submitted';
                            $wName = $u['wallet_name'] ?? '';
                            $wAddr = $u['wallet_address'] ?? '';
                    ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="py-3.5 px-4 font-700 text-white"><?php echo htmlspecialchars($u['uname']); ?></td>
                        <td class="py-3.5 px-4 text-gray-400"><?php echo htmlspecialchars($u['uemail']); ?></td>
                        <td class="py-3.5 px-4">
                            <?php if (!empty($wAddr)): ?>
                                <span class="font-700 text-amber-400 block"><?php echo htmlspecialchars($wName ?: 'Wallet'); ?></span>
                                <span class="font-mono text-[10px] text-gray-400" title="<?php echo htmlspecialchars($wAddr); ?>"><?php echo htmlspecialchars(substr($wAddr, 0, 6) . '...' . substr($wAddr, -4)); ?></span>
                            <?php else: ?>
                                <span class="text-gray-500 italic">None</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <?php
                            if ($kStat === 'Verified' || $kStat === 'Approved') {
                                echo '<span class="bg-green-500/10 text-green-400 text-[10px] font-800 px-2 py-0.5 rounded-full border border-green-500/30">Approved</span>';
                            } elseif ($kStat === 'Pending Approval') {
                                echo '<span class="bg-amber-500/10 text-amber-400 text-[10px] font-800 px-2 py-0.5 rounded-full border border-amber-500/30">Pending</span>';
                            } elseif ($kStat === 'Rejected') {
                                echo '<span class="bg-red-500/10 text-red-400 text-[10px] font-800 px-2 py-0.5 rounded-full border border-red-500/30">Rejected</span>';
                            } else {
                                echo '<span class="bg-gray-500/10 text-gray-400 text-[10px] font-800 px-2 py-0.5 rounded-full border border-gray-500/30">Unverified</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">No registered users found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Administrative Shortcuts -->
    <div class="admin-card p-6 flex flex-col justify-between">
        <div>
            <h3 class="font-syne font-800 text-lg text-white mb-2">System Status & Shortcuts</h3>
            <p class="text-xs text-gray-400 mb-6">Manage platform trading variables, crypto deposit gateways, user balances, and identity verification requests.</p>

            <div class="grid grid-cols-2 gap-4">
                <a href="/user_management" class="p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl block transition">
                    <i class="fas fa-user-gear text-amber-400 text-xl mb-2"></i>
                    <h4 class="font-700 text-sm text-white">Manage Users</h4>
                    <p class="text-[10px] text-gray-500">Edit balances & details</p>
                </a>

                <a href="/admin_kyc" class="p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl block transition">
                    <i class="fas fa-id-card text-purple-400 text-xl mb-2"></i>
                    <h4 class="font-700 text-sm text-white">KYC Center</h4>
                    <p class="text-[10px] text-gray-500">Review document photos</p>
                </a>

                <a href="/special_payments" class="p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl block transition">
                    <i class="fas fa-wallet text-emerald-400 text-xl mb-2"></i>
                    <h4 class="font-700 text-sm text-white">Crypto Portals</h4>
                    <p class="text-[10px] text-gray-500">Custom payment links</p>
                </a>

                <a href="/profits" class="p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl block transition">
                    <i class="fas fa-chart-line text-blue-400 text-xl mb-2"></i>
                    <h4 class="font-700 text-sm text-white">Profits & ROI</h4>
                    <p class="text-[10px] text-gray-500">Update ROI settings</p>
                </a>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-white/5 flex items-center justify-between text-xs text-gray-500">
            <span>NexusHub Core v3.0.0</span>
            <span class="text-emerald-400"><i class="fas fa-server mr-1"></i> Database Connected</span>
        </div>
    </div>

</div>

<?php include 'admin_footer.php'; ?>
