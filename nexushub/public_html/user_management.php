<?php
include 'config.php';
include 'admin_header.php';

// Handle delete
if (isset($_POST['delete_id'])) {
    $del = intval($_POST['delete_id']);
    $conn->query("DELETE FROM user_details WHERE id ='$del'");
    $conn->query("DELETE FROM bal WHERE uid ='$del'");
    $conn->query("DELETE FROM withdrawals WHERE uid ='$del'");
}

// Handle balance update
if (isset($_POST['update_bal_id']) && isset($_POST['new_balance'])) {
    $uid_to_update = intval($_POST['update_bal_id']);
    $new_bal = floatval($_POST['new_balance']);

    // Check if balance record exists
    $chk = $conn->query("SELECT * FROM bal WHERE uid='$uid_to_update'");
    if ($chk && $chk->num_rows > 0) {
        $conn->query("UPDATE bal SET bal='$new_bal' WHERE uid='$uid_to_update'");
    } else {
        $conn->query("INSERT INTO bal(uid, bal) VALUES('$uid_to_update', '$new_bal')");
    }
}
?>

<div class="space-y-6">

    <!-- Header info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-syne font-800 text-2xl text-white">Registered Users & Balance Manager</h2>
            <p class="text-xs text-gray-400 mt-1">Search, update account balances, and manage trader accounts.</p>
        </div>
        <div class="bg-white/5 border border-white/10 px-4 py-2 rounded-xl text-xs font-700 text-amber-400">
            Total Users: <?php
            $cntRes = $conn->query("SELECT COUNT(*) as c FROM user_details");
            echo ($cntRes && $r = $cntRes->fetch_assoc()) ? $r['c'] : 0;
            ?>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $sql = "SELECT u.*, b.bal FROM user_details u LEFT JOIN bal b ON u.id = b.uid ORDER BY u.id DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $userId = $row['id'];
                $balVal = floatval($row['bal'] ?? 0);
                $kycStat = $row['kyc_status'] ?? 'Not Submitted';
        ?>
        <div class="admin-card p-6 flex flex-col justify-between space-y-4 relative overflow-hidden">

            <div>
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <span class="text-[10px] font-800 text-amber-400 uppercase tracking-widest">UID: #<?php echo $userId; ?></span>
                        <h3 class="font-syne font-800 text-lg text-white leading-snug"><?php echo htmlspecialchars($row['uname']); ?></h3>
                        <p class="text-xs text-gray-400 mt-0.5"><?php echo htmlspecialchars($row['uemail']); ?></p>
                    </div>

                    <!-- KYC Status Pill -->
                    <div>
                        <?php
                        if ($kycStat === 'Verified') {
                            echo '<span class="bg-green-500/10 text-green-400 border border-green-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full"><i class="fas fa-check-circle mr-1"></i> Verified</span>';
                        } elseif ($kycStat === 'Pending Approval') {
                            echo '<span class="bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full"><i class="fas fa-clock mr-1"></i> Pending</span>';
                        } elseif ($kycStat === 'Rejected') {
                            echo '<span class="bg-red-500/10 text-red-400 border border-red-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full"><i class="fas fa-times-circle mr-1"></i> Rejected</span>';
                        } else {
                            echo '<span class="bg-gray-500/10 text-gray-400 border border-gray-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full"><i class="fas fa-shield mr-1"></i> Unverified</span>';
                        }
                        ?>
                    </div>
                </div>

                <div class="space-y-2 pt-3 border-t border-white/5 text-xs">
                    <div class="flex justify-between text-gray-400">
                        <span>Password:</span>
                        <span class="font-mono text-white"><?php echo htmlspecialchars($row['upwd']); ?></span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Registered Date:</span>
                        <span class="text-gray-300"><?php echo htmlspecialchars($row['created_at'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <!-- Balance Update Form -->
                <form action="/user_management" method="post" class="mt-4 p-3 bg-black/40 rounded-xl border border-white/5 space-y-2">
                    <input type="hidden" name="update_bal_id" value="<?php echo $userId; ?>">
                    <label class="block text-[10px] font-800 uppercase tracking-wider text-gray-400">Account Balance (USD)</label>
                    <div class="flex gap-2">
                        <input type="number" step="0.01" name="new_balance" value="<?php echo $balVal; ?>" class="w-full bg-black border border-white/10 rounded-lg px-3 py-1.5 text-xs font-mono font-700 text-white outline-none focus:border-amber-500">
                        <button type="submit" class="bg-gradient-gold text-black px-3 py-1.5 rounded-lg font-800 text-xs hover:opacity-90 transition">Save</button>
                    </div>
                </form>
            </div>

            <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                <a href="/admin_kyc" class="text-[11px] text-amber-400 font-700 hover:underline flex items-center gap-1">
                    <i class="fas fa-id-card text-xs"></i> KYC Record
                </a>

                <form action="/user_management" method="post" onsubmit="return confirm('Are you sure you want to delete user <?php echo htmlspecialchars($row['uname']); ?>?');">
                    <input type="hidden" name="delete_id" value="<?php echo $userId; ?>">
                    <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-700 transition flex items-center gap-1.5">
                        <i class="fas fa-trash-can text-xs"></i> Delete User
                    </button>
                </form>
            </div>

        </div>
        <?php
            endwhile;
        else:
        ?>
            <div class="col-span-full admin-card p-10 text-center text-gray-400">
                <i class="fas fa-users-slash text-4xl mb-3 text-gray-600"></i>
                <p class="font-700">No users found in database.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'admin_footer.php'; ?>
