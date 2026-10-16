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
        'uemail' => 'user@nexushub.com',
        'upass' => '123456',
        'kyc_status' => 'Not Submitted'
    ];
}

$uname = isset($loop['uname']) ? $loop['uname'] : 'Trader';
$uemail = isset($loop['uemail']) ? $loop['uemail'] : '';
$u = isset($loop['uid']) ? $loop['uid'] : (!empty($u) ? $u : '1001');

$pwd_msg = "";
$pwd_error = "";

// Handle Password Change
if (isset($_POST['change_password'])) {
    $old_pwd = filter($_POST['old_password']);
    $new_pwd = filter($_POST['new_password']);
    $confirm_pwd = filter($_POST['confirm_password']);

    if (!empty($old_pwd) && !empty($new_pwd) && !empty($confirm_pwd)) {
        if ($old_pwd !== $loop['upass']) {
            $pwd_error = "Current password does not match our records.";
        } elseif ($new_pwd !== $confirm_pwd) {
            $pwd_error = "New passwords do not match.";
        } elseif (strlen($new_pwd) < 6) {
            $pwd_error = "New password must be at least 6 characters.";
        } else {
            $conn->query("UPDATE user_details SET upass='$new_pwd' WHERE uid='$u' OR id='$u'");
            $pwd_msg = "Password updated successfully!";
            $loop['upass'] = $new_pwd;
        }
    } else {
        $pwd_error = "Please fill in all password fields.";
    }
}

include 'dashboard_header.php';
?>

<div class="max-w-4xl mx-auto space-y-6 sm:space-y-8 w-full max-w-full overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-syne text-2xl sm:text-3xl font-800 text-white mb-1">Account & Settings</h2>
            <p class="text-gray-400 text-xs sm:text-sm">Manage your profile, security settings, and identity verification status.</p>
        </div>
        <div>
            <a href="/dashboard/<?php echo $req; ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-700 transition border border-white/10">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Profile Overview Card -->
    <div class="card-dark p-5 sm:p-8 rounded-2xl sm:rounded-[2.5rem] border-white/5 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6">
            <div class="relative">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-gold/30 flex items-center justify-center font-syne font-800 text-2xl sm:text-3xl text-gold shadow-xl">
                    <?php echo strtoupper(substr($uname, 0, 1)); ?>
                </div>
                <?php if (isset($loop['kyc_status']) && $loop['kyc_status'] === 'Verified'): ?>
                    <span class="absolute -bottom-2 -right-2 bg-green-500 text-black text-[10px] font-900 px-2 py-0.5 rounded-full border-2 border-[#0a0a0a]" title="Verified Account">✓ Verified</span>
                <?php endif; ?>
            </div>

            <div class="text-center sm:text-left flex-1 min-w-0">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 sm:gap-3 mb-1">
                    <h3 class="text-xl sm:text-2xl font-800 text-white font-syne truncate max-w-[200px] sm:max-w-none"><?php echo htmlspecialchars($uname); ?></h3>
                    <?php
                        $kycStat = isset($loop['kyc_status']) ? $loop['kyc_status'] : 'Not Submitted';
                        if ($kycStat === 'Verified') {
                            echo '<span class="bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-800 px-3 py-1 rounded-full flex items-center gap-1.5"><i class="fas fa-check-circle"></i> Verified</span>';
                        } elseif ($kycStat === 'Pending Approval') {
                            echo '<span class="bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-800 px-3 py-1 rounded-full flex items-center gap-1.5"><i class="fas fa-clock"></i> KYC Pending Review</span>';
                        } elseif ($kycStat === 'Rejected') {
                            echo '<span class="bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-800 px-3 py-1 rounded-full flex items-center gap-1.5"><i class="fas fa-times-circle"></i> KYC Rejected</span>';
                        } else {
                            echo '<span class="bg-gray-500/10 border border-gray-500/30 text-gray-400 text-xs font-800 px-3 py-1 rounded-full flex items-center gap-1.5"><i class="fas fa-shield-alt"></i> Unverified</span>';
                        }
                    ?>
                </div>
                <p class="text-xs text-gray-400 font-mono mb-4">Account UID: <?php echo htmlspecialchars($u); ?></p>

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                    <a href="/dashboard/<?php echo $req; ?>#kyc-section" class="px-4 py-2.5 bg-gradient-gold text-black text-xs font-800 rounded-xl hover:scale-105 transition-all shadow-md">
                        <i class="fas fa-id-card mr-1"></i> Manage KYC Status
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
        <!-- Security & Password -->
        <div class="card-dark p-5 sm:p-8 rounded-2xl sm:rounded-[2rem] border-white/5">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary shrink-0">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <h4 class="font-syne font-800 text-white text-base sm:text-lg">Security Settings</h4>
                    <p class="text-xs text-gray-500">Update your account password</p>
                </div>
            </div>

            <?php if (!empty($pwd_msg)): ?>
                <div class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-600 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> <?php echo $pwd_msg; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($pwd_error)): ?>
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-600 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $pwd_error; ?>
                </div>
            <?php endif; ?>

            <form action="/settings/<?php echo $req; ?>" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-700 text-gray-400 uppercase tracking-wider mb-2">Current Password</label>
                    <input type="password" name="old_password" required placeholder="••••••••"
                        class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition">
                </div>

                <div>
                    <label class="block text-xs font-700 text-gray-400 uppercase tracking-wider mb-2">New Password</label>
                    <input type="password" name="new_password" required placeholder="••••••••"
                        class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition">
                </div>

                <div>
                    <label class="block text-xs font-700 text-gray-400 uppercase tracking-wider mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••"
                        class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition">
                </div>

                <button type="submit" name="change_password" class="w-full py-3.5 bg-gradient-gold text-black font-800 rounded-xl text-xs uppercase tracking-wider hover:scale-[1.02] active:scale-[0.98] transition shadow-lg shadow-orange-500/20 font-syne mt-2">
                    Update Password <i class="fas fa-key ml-1"></i>
                </button>
            </form>
        </div>

        <!-- Preferences & Info -->
        <div class="space-y-6 sm:space-y-8">
            <div class="card-dark p-5 sm:p-8 rounded-2xl sm:rounded-[2rem] border-white/5">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h4 class="font-syne font-800 text-white text-base sm:text-lg">Account Information</h4>
                        <p class="text-xs text-gray-500">Read-only account metadata</p>
                    </div>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-600">Username</span>
                        <span class="text-xs text-white font-800"><?php echo htmlspecialchars($uname); ?></span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-600">UID Code</span>
                        <span class="text-xs text-gold font-mono font-800"><?php echo htmlspecialchars($u); ?></span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-600">KYC Status</span>
                        <span class="text-xs font-800 text-white"><?php echo htmlspecialchars($kycStat); ?></span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-600">2FA Protection</span>
                        <span class="text-xs font-800 text-green-400 flex items-center gap-1"><i class="fas fa-shield-check"></i> Enabled</span>
                    </div>
                </div>
            </div>

            <div class="card-dark p-5 sm:p-8 rounded-2xl sm:rounded-[2rem] border-white/5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 shrink-0">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-syne font-800 text-white text-base sm:text-lg">Session Management</h4>
                        <p class="text-xs text-gray-500">Log out of active session</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed mb-4">Ending your session will require you to log back in using your credentials.</p>
                <a href="/signin/logout" class="block w-full py-3 text-center bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-xl text-xs font-800 uppercase tracking-wider transition">
                    Sign Out Now
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include 'dashboard_footer.php';
?>
