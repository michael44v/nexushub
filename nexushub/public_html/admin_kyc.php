<?php
include 'config.php';

$alertType = "";
$alertMsg = "";

// Handle Approve
if (isset($_POST['action']) && $_POST['action'] === 'approve') {
    $target_uid = intval($_POST['uid']);
    $stmt = $conn->prepare("UPDATE user_details SET kyc_status='Approved', kyc_rejection_reason=NULL WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $target_uid);
        if ($stmt->execute()) {
            $alertType = "success";
            $alertMsg = "KYC Application for User #" . $target_uid . " has been APPROVED.";
        }
    }
}

// Handle Reject
if (isset($_POST['action']) && $_POST['action'] === 'reject') {
    $target_uid = intval($_POST['uid']);
    $reason = filter($_POST['rejection_reason'] ?? 'Document rejected by administrator.');
    $stmt = $conn->prepare("UPDATE user_details SET kyc_status='Rejected', kyc_rejection_reason=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("si", $reason, $target_uid);
        if ($stmt->execute()) {
            $alertType = "error";
            $alertMsg = "KYC Application for User #" . $target_uid . " has been REJECTED.";
        }
    }
}

// Filter
$statusFilter = isset($_GET['filter']) ? filter($_GET['filter']) : 'all';

include 'admin_header.php';
?>

<div class="space-y-6">

    <?php if (!empty($alertMsg)): ?>
    <div class="p-4 rounded-xl text-xs font-700 flex items-center gap-2
        <?php echo ($alertType === 'success') ? 'bg-green-500/10 border border-green-500/30 text-green-400' : 'bg-red-500/10 border border-red-500/30 text-red-400'; ?>">
        <i class="fas <?php echo ($alertType === 'success') ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($alertMsg); ?>
    </div>
    <?php endif; ?>

    <!-- Filter Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-syne font-800 text-2xl text-white">KYC Document Verification Requests</h2>
            <p class="text-xs text-gray-400 mt-1">Review uploaded identity document images and approve or reject KYC submissions.</p>
        </div>

        <div class="flex bg-black/60 p-1 rounded-xl border border-white/10 text-xs font-700">
            <a href="?filter=all" class="px-4 py-2 rounded-lg transition <?php echo ($statusFilter === 'all') ? 'bg-gradient-gold text-black font-800' : 'text-gray-400 hover:text-white'; ?>">All Submissions</a>
            <a href="?filter=pending" class="px-4 py-2 rounded-lg transition <?php echo ($statusFilter === 'pending') ? 'bg-gradient-gold text-black font-800' : 'text-gray-400 hover:text-white'; ?>">Pending Review</a>
            <a href="?filter=verified" class="px-4 py-2 rounded-lg transition <?php echo ($statusFilter === 'verified') ? 'bg-gradient-gold text-black font-800' : 'text-gray-400 hover:text-white'; ?>">Verified</a>
            <a href="?filter=rejected" class="px-4 py-2 rounded-lg transition <?php echo ($statusFilter === 'rejected') ? 'bg-gradient-gold text-black font-800' : 'text-gray-400 hover:text-white'; ?>">Rejected</a>
        </div>
    </div>

    <!-- Table of Submissions -->
    <div class="admin-card p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-800 uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Doc Type</th>
                        <th class="py-3 px-4">Cloudinary Document Image</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Submitted At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    <?php
                    $whereClause = "";
                    if ($statusFilter === 'pending') $whereClause = "WHERE kyc_status = 'Pending Approval'";
                    elseif ($statusFilter === 'verified' || $statusFilter === 'approved') $whereClause = "WHERE kyc_status IN ('Verified', 'Approved')";
                    elseif ($statusFilter === 'rejected') $whereClause = "WHERE kyc_status = 'Rejected'";

                    $query = "SELECT * FROM user_details $whereClause ORDER BY (kyc_status = 'Pending Approval') DESC, id DESC";
                    $res = $conn->query($query);

                    if ($res && $res->num_rows > 0):
                        while ($u = $res->fetch_assoc()):
                            $uid = $u['id'];
                            $kStat = $u['kyc_status'] ?? 'Not Submitted';
                            $docType = $u['kyc_doc_type'] ?? 'Not Specified';
                            $docUrl = $u['kyc_doc_url'] ?? '';
                    ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="py-4 px-4">
                            <span class="font-700 text-white font-syne"><?php echo htmlspecialchars($u['uname']); ?></span>
                            <span class="block text-[10px] text-gray-400"><?php echo htmlspecialchars($u['uemail']); ?></span>
                            <span class="text-[9px] text-amber-400 font-mono">UID: #<?php echo $uid; ?></span>
                        </td>

                        <td class="py-4 px-4">
                            <span class="bg-white/10 text-gray-200 text-[11px] font-700 px-2.5 py-1 rounded-lg">
                                <?php echo htmlspecialchars($docType); ?>
                            </span>
                        </td>

                        <td class="py-4 px-4">
                            <?php if (!empty($docUrl)): ?>
                                <div class="flex items-center gap-3">
                                    <a href="<?php echo htmlspecialchars($docUrl); ?>" target="_blank" class="block w-16 h-12 rounded-lg bg-black/60 border border-white/10 overflow-hidden hover:scale-105 transition group relative">
                                        <img src="<?php echo htmlspecialchars($docUrl); ?>" alt="KYC Document" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/100?text=Doc';">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs">
                                            <i class="fas fa-expand"></i>
                                        </div>
                                    </a>
                                    <a href="<?php echo htmlspecialchars($docUrl); ?>" target="_blank" class="text-[11px] text-amber-400 font-700 hover:underline">
                                        View Full Photo <i class="fas fa-external-link-alt text-[9px]"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-500 italic text-[11px]">No document image uploaded</span>
                            <?php endif; ?>
                        </td>

                        <td class="py-4 px-4">
                            <?php
                            if ($kStat === 'Verified' || $kStat === 'Approved') {
                                echo '<span class="bg-green-500/10 text-green-400 border border-green-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full flex items-center gap-1 w-max"><i class="fas fa-check-circle"></i> Approved</span>';
                            } elseif ($kStat === 'Pending Approval') {
                                echo '<span class="bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full flex items-center gap-1 w-max"><i class="fas fa-clock"></i> Pending Review</span>';
                            } elseif ($kStat === 'Rejected') {
                                echo '<span class="bg-red-500/10 text-red-400 border border-red-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full flex items-center gap-1 w-max"><i class="fas fa-times-circle"></i> Rejected</span>';
                                if (!empty($u['kyc_rejection_reason'])) {
                                    echo '<span class="block text-[10px] text-red-400/80 mt-1">Reason: '.htmlspecialchars($u['kyc_rejection_reason']).'</span>';
                                }
                            } else {
                                echo '<span class="bg-gray-500/10 text-gray-400 border border-gray-500/30 text-[10px] font-800 px-2.5 py-1 rounded-full">Not Submitted</span>';
                            }
                            ?>
                        </td>

                        <td class="py-4 px-4 text-gray-400 text-[11px]">
                            <?php echo htmlspecialchars($u['kyc_submitted_at'] ?? 'N/A'); ?>
                        </td>

                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Approve Form -->
                                <form method="post" action="" class="inline">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                                    <button type="submit" class="bg-green-500 hover:bg-green-400 text-black px-3 py-1.5 rounded-lg text-xs font-800 transition flex items-center gap-1" <?php echo ($kStat === 'Verified' || $kStat === 'Approved') ? 'disabled opacity-50 cursor-not-allowed' : ''; ?>>
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>

                                <!-- Reject Modal Button -->
                                <button type="button" onclick="openRejectModal(<?php echo $uid; ?>, '<?php echo htmlspecialchars(addslashes($u['uname'])); ?>')" class="bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 px-3 py-1.5 rounded-lg text-xs font-800 transition flex items-center gap-1">
                                    <i class="fas fa-times"></i> Decline
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-500">
                            <i class="fas fa-id-card text-3xl mb-2 text-gray-600 block"></i>
                            No KYC verification requests found for filter: <strong class="text-white"><?php echo htmlspecialchars($statusFilter); ?></strong>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Rejection Reason Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="admin-card p-6 w-full max-w-md space-y-4">
        <div class="flex items-center justify-between border-b border-white/5 pb-3">
            <h3 class="font-syne font-800 text-base text-white">Decline KYC Submission</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-white text-lg"><i class="fas fa-times"></i></button>
        </div>

        <form method="post" action="" class="space-y-4">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="uid" id="reject_target_uid" value="">

            <p class="text-xs text-gray-300">Rejecting KYC for trader: <strong id="reject_target_name" class="text-amber-400"></strong></p>

            <div>
                <label class="block text-[10px] font-800 uppercase text-gray-400 mb-1.5">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="3" class="w-full bg-black/60 border border-white/10 rounded-xl p-3 text-xs text-white outline-none focus:border-red-500" required placeholder="e.g. Document image is blurry or unreadable. Please upload a clear photo."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-700 text-gray-400">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-red-500 hover:bg-red-400 text-white rounded-xl text-xs font-800 transition">Decline KYC</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(uid, name) {
    document.getElementById('reject_target_uid').value = uid;
    document.getElementById('reject_target_name').textContent = name;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>

<?php include 'admin_footer.php'; ?>
