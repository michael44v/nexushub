        </div>
    </main>
</div>

<!-- Mobile Hamburger Sidebar Drawer -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[60] hidden flex-col transition-all duration-300">
    <div class="flex justify-between items-center p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-gold rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                <i class="fas fa-chart-line text-black text-lg"></i>
            </div>
            <span class="font-syne text-xl font-800 gradient-text">NexusHub</span>
        </div>
        <button id="close-overlay" class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700 mb-4">Main Navigation</p>
            <div class="space-y-2">
                <a href="/dashboard/<?php echo $req; ?>" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'bg-gradient-gold text-black font-800' : 'bg-white/5 text-gray-200 hover:text-white'; ?>">
                    <div class="w-6 text-center"><i class="fas fa-th-large text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Market Overview</span>
                </a>

                <a href="/web/<?php echo $req; ?>" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl <?php echo (strpos($_SERVER['REQUEST_URI'], '/web/') !== false) ? 'bg-gradient-gold text-black font-800' : 'bg-white/5 text-gray-200 hover:text-white'; ?>">
                    <div class="w-6 text-center"><i class="fas fa-wallet text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Web3 Wallet</span>
                </a>

                <a href="/withdraw/<?php echo $req; ?>" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl <?php echo (strpos($_SERVER['REQUEST_URI'], '/withdraw/') !== false) ? 'bg-gradient-gold text-black font-800' : 'bg-white/5 text-gray-200 hover:text-white'; ?>">
                    <div class="w-6 text-center"><i class="fas fa-arrow-up-from-bracket text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Withdraw Funds</span>
                </a>

                <a href="/history/<?php echo $req; ?>" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl <?php echo (strpos($_SERVER['REQUEST_URI'], '/history/') !== false) ? 'bg-gradient-gold text-black font-800' : 'bg-white/5 text-gray-200 hover:text-white'; ?>">
                    <div class="w-6 text-center"><i class="fas fa-clock-rotate-left text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Transaction History</span>
                </a>
            </div>
        </div>

        <div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700 mb-4">Account & Security</p>
            <div class="space-y-2">
                <a href="/settings/<?php echo $req; ?>" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl <?php echo (strpos($_SERVER['REQUEST_URI'], '/settings/') !== false) ? 'bg-gradient-gold text-black font-800' : 'bg-white/5 text-gray-200 hover:text-white'; ?>">
                    <div class="w-6 text-center"><i class="fas fa-user-gear text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Profile & Settings</span>
                </a>

                <a href="/support/<?php echo $req; ?>-Support" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl <?php echo (strpos($_SERVER['REQUEST_URI'], '/support/') !== false) ? 'bg-gradient-gold text-black font-800' : 'bg-white/5 text-gray-200 hover:text-white'; ?>">
                    <div class="w-6 text-center"><i class="fas fa-headset text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Support Center</span>
                </a>

                <a href="/signin/logout" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition">
                    <div class="w-6 text-center"><i class="fas fa-sign-out-alt text-lg"></i></div>
                    <span class="text-sm font-syne font-700">Logout</span>
                </a>
            </div>
        </div>

        <div class="pt-4">
            <div class="card-dark p-6 rounded-3xl border-gold/20 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-shield-halved text-gold text-xl"></i>
                    <span class="text-xs font-800 text-white uppercase tracking-wider">Account Protection</span>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed mb-4">Your connection is protected with high-grade encryption.</p>
            </div>
        </div>
    </div>
</div>

<script>
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const topHamburgerBtn = document.getElementById('top-hamburger-btn');
    const closeOverlay = document.getElementById('close-overlay');
    const overlay = document.getElementById('mobile-overlay');

    function openMenu() {
        if(overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMenu() {
        if(overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    if(mobileToggle) mobileToggle.addEventListener('click', openMenu);
    if(topHamburgerBtn) topHamburgerBtn.addEventListener('click', openMenu);
    if(closeOverlay) closeOverlay.addEventListener('click', closeMenu);
</script>
