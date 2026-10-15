<?php
include 'config.php';

$alertType = '';
$alertMsg = '';

if (strpos($req, "apid") !== false) {
    $trans_id = preg_replace('/\D/', '', $req);
    $uid_res = $conn->query("SELECT * FROM withdrawals WHERE txn_id='$trans_id'");
    if ($uid_res && $uid_res->num_rows > 0) {
        $rr = $uid_res->fetch_assoc();
        $u = $rr['uid'];
        $prev = $conn->query("SELECT * FROM bal WHERE uid='$u'");
        if ($prev && $prev->num_rows > 0) {
            $lp = $prev->fetch_assoc();
            $new_bal = $lp['bal'] - $rr['amt'];
            $update = $conn->query("UPDATE bal SET bal='$new_bal' WHERE uid='$u'");
            if ($update) {
                $conn->query("UPDATE withdrawals SET stat='Approved' WHERE txn_id='$trans_id'");
                $alertType = 'success';
                $alertMsg = 'Withdrawal ID #' . $trans_id . ' approved successfully!';
            }
        }
    }
}

if (strpos($req, "dcid") !== false) {
    $trans_id = preg_replace('/\D/', '', $req);
    $decline = $conn->query("UPDATE withdrawals SET stat='Declined' WHERE txn_id='$trans_id'");
    if ($decline) {
        $alertType = 'error';
        $alertMsg = 'Withdrawal ID #' . $trans_id . ' declined.';
    }
}

include 'admin_header.php';
?>

<div class="space-y-8">

    <?php if (!empty($alertMsg)): ?>
    <div class="p-4 rounded-xl text-xs font-700 flex items-center gap-2
        <?php echo ($alertType === 'success') ? 'bg-green-500/10 border border-green-500/30 text-green-400' : 'bg-red-500/10 border border-red-500/30 text-red-400'; ?>">
        <i class="fas <?php echo ($alertType === 'success') ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($alertMsg); ?>
    </div>
    <?php endif; ?>

    <!-- Withdrawals Section -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-syne font-800 text-lg text-white">Withdrawal Requests</h3>
                <p class="text-xs text-gray-400">Review pending user withdrawal requests and approve or decline them.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-800 uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4">Txn ID</th>
                        <th class="py-3 px-4">User ID</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Created At</th>
                        <th class="py-3 px-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    <?php
                    $sql = "SELECT w.*, u.uname FROM withdrawals w LEFT JOIN user_details u ON w.uid = u.id ORDER BY w.txn_id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $stat = $row['stat'] ?? 'Pending';
                    ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="py-3.5 px-4 font-mono font-700 text-white">#<?php echo $row['txn_id']; ?></td>
                        <td class="py-3.5 px-4">
                            <span class="font-700 text-white"><?php echo htmlspecialchars($row['uname'] ?? 'User #'.$row['uid']); ?></span>
                            <span class="block text-[10px] text-gray-500">UID: #<?php echo $row['uid']; ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-syne font-800 text-amber-400">
                            $<?php echo number_format($row['amt'], 2); ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <?php
                            if ($stat === 'Approved') {
                                echo '<span class="bg-green-500/10 text-green-400 border border-green-500/30 text-[10px] font-800 px-2.5 py-0.5 rounded-full">Approved</span>';
                            } elseif ($stat === 'Declined') {
                                echo '<span class="bg-red-500/10 text-red-400 border border-red-500/30 text-[10px] font-800 px-2.5 py-0.5 rounded-full">Declined</span>';
                            } else {
                                echo '<span class="bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-800 px-2.5 py-0.5 rounded-full">Pending</span>';
                            }
                            ?>
                        </td>
                        <td class="py-3.5 px-4 text-gray-400">
                            <?php echo htmlspecialchars($row['created_at'] ?? $row['tm'] ?? 'N/A'); ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <?php if ($stat === 'Pending' || $stat === 'Processing'): ?>
                                <div class="flex items-center gap-2">
                                    <a href="/admin_deposits/apid<?php echo $row['txn_id']; ?>" class="bg-green-500 hover:bg-green-400 text-black px-3 py-1 rounded-lg text-[11px] font-800 transition">
                                        Approve
                                    </a>
                                    <a href="/admin_deposits/dcid<?php echo $row['txn_id']; ?>" class="bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 px-3 py-1 rounded-lg text-[11px] font-800 transition">
                                        Decline
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-500 text-[11px]">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">No withdrawal requests found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'admin_footer.php'; ?>
