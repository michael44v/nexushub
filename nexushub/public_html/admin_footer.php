        </main>
    </div>
</div>

<!-- Mobile Admin Menu Drawer -->
<div id="adminMobileDrawer" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[60] hidden flex-col">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-gold rounded-xl flex items-center justify-center text-black font-800 text-lg">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <h2 class="font-syne font-800 text-lg text-white">NexusHub Admin</h2>
                <p class="text-[10px] text-amber-400 font-700 uppercase tracking-widest">Control Panel</p>
            </div>
        </div>
        <button id="closeAdminDrawerBtn" class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 p-6 space-y-2 overflow-y-auto">
        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-800 mb-3">Admin Navigation</p>

        <a href="/admin/secured/@root#hash" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-chart-pie text-amber-400 w-5"></i> Overview</div>
        </a>

        <a href="/user_management" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-users-gear text-amber-400 w-5"></i> User Management</div>
        </a>

        <a href="/admin_deposits/new" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-money-bill-transfer text-amber-400 w-5"></i> Transactions/connected wallets</div>
            <?php if (isset($pendingWithdrawalsCount) && $pendingWithdrawalsCount > 0): ?>
                <span class="px-2 py-0.5 rounded-full text-xs font-800 bg-red-500 text-white"><?php echo $pendingWithdrawalsCount; ?></span>
            <?php endif; ?>
        </a>

        <a href="/special_payments" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-receipt text-amber-400 w-5"></i> Special Payments</div>
        </a>

        <a href="/profits" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-chart-line text-amber-400 w-5"></i> Profits & ROI</div>
        </a>

        <a href="/messenger" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-comments text-amber-400 w-5"></i> Live Messenger</div>
        </a>

        <a href="/admin_kyc" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 text-gray-200 font-syne font-700 text-sm hover:text-white">
            <div class="flex items-center gap-3"><i class="fas fa-id-card text-amber-400 w-5"></i> KYC Requests</div>
            <?php if (isset($pendingKycCount) && $pendingKycCount > 0): ?>
                <span class="px-2 py-0.5 rounded-full text-xs font-800 bg-amber-500 text-black"><?php echo $pendingKycCount; ?></span>
            <?php endif; ?>
        </a>

        <div class="pt-6">
            <a href="/signin/logout" class="flex items-center gap-3 p-4 rounded-2xl bg-red-500/10 text-red-400 font-syne font-700 text-sm">
                <i class="fas fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </nav>
</div>

<script>
const mobileBtn = document.getElementById('mobileAdminMenuBtn');
const closeBtn = document.getElementById('closeAdminDrawerBtn');
const drawer = document.getElementById('adminMobileDrawer');

if (mobileBtn && drawer) {
    mobileBtn.addEventListener('click', function() {
        drawer.classList.remove('hidden');
        drawer.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
}

if (closeBtn && drawer) {
    closeBtn.addEventListener('click', function() {
        drawer.classList.add('hidden');
        drawer.classList.remove('flex');
        document.body.style.overflow = 'auto';
    });
}
</script>
</body>
</html>
