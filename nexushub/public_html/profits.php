<?php
include 'config.php';

$alertMsg = "";
if (isset($_POST['pft'])) {
    $user_to_add = intval($_POST['uid']);
    $amount_add = floatval($_POST['pft']);

    $get_prev_bal = $conn->query("SELECT * FROM bal WHERE uid='$user_to_add'");
    if ($get_prev_bal && $get_prev_bal->num_rows > 0) {
        $tr = $get_prev_bal->fetch_assoc();
        $new_bal = $tr['bal'] + $amount_add;
        $conn->query("UPDATE bal SET bal='$new_bal' WHERE uid='$user_to_add'");
        $alertMsg = "Added $" . number_format($amount_add, 2) . " profit to User #" . $user_to_add . "!";
    } else {
        $conn->query("INSERT INTO bal(uid, bal) VALUES('$user_to_add', '$amount_add')");
        $alertMsg = "Added $" . number_format($amount_add, 2) . " profit to User #" . $user_to_add . "!";
    }
}

include 'admin_header.php';
?>

<div class="space-y-6">

    <?php if (!empty($alertMsg)): ?>
    <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-700 flex items-center gap-2">
        <i class="fas fa-circle-check"></i> <?php echo htmlspecialchars($alertMsg); ?>
    </div>
    <?php endif; ?>

    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-syne font-800 text-lg text-white">Profit & ROI Manager</h3>
                <p class="text-xs text-gray-400">Add trading profits directly to any user account balance.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-800 uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4">UID</th>
                        <th class="py-3 px-4">User Name</th>
                        <th class="py-3 px-4">Current Balance</th>
                        <th class="py-3 px-4">Add Profit Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    <?php
                    $sql = "SELECT u.id, u.uname, u.uemail, b.bal FROM user_details u LEFT JOIN bal b ON u.id = b.uid ORDER BY u.id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $uId = $row['id'];
                            $currentBal = floatval($row['bal'] ?? 0);
                    ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="py-3.5 px-4 font-mono font-700 text-amber-400">#<?php echo $uId; ?></td>
                        <td class="py-3.5 px-4">
                            <span class="font-700 text-white"><?php echo htmlspecialchars($row['uname']); ?></span>
                            <span class="block text-[10px] text-gray-500"><?php echo htmlspecialchars($row['uemail']); ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-syne font-800 text-white">
                            $<?php echo number_format($currentBal, 2); ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <form action="/profits" method="post" class="flex items-center gap-2">
                                <input type="hidden" name="uid" value="<?php echo $uId; ?>">
                                <input type="number" step="0.01" name="pft" placeholder="e.g. 250.00" class="bg-black/60 border border-white/10 rounded-lg px-3 py-1.5 text-xs font-mono font-700 text-white outline-none focus:border-amber-500 w-36" required>
                                <button type="submit" class="bg-gradient-gold text-black px-3 py-1.5 rounded-lg font-800 text-xs hover:opacity-90 transition shadow-md shadow-amber-500/20">
                                    + Add Profit
                                </button>
                            </form>
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

</div>

<?php include 'admin_footer.php'; ?>
