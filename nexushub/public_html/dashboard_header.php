<?php
// Ensure $req is available
if (!isset($req)) {
    $req = '';
}
?>
<div class="min-h-screen bg-[#050505] text-white flex max-w-full overflow-x-hidden">
    <!-- Sidebar (Desktop) -->
    <aside class="hidden lg:flex flex-col w-72 bg-black border-r border-white/5 fixed h-full z-40">
        <div class="p-8">
            <a href="/" class="flex items-center gap-3">
                <img src="/images/logo.jpg" alt="Nexus" class="h-9 w-auto object-contain rounded-lg">
            </a>
        </div>

        <nav class="flex-1 px-6 space-y-2 mt-2">
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700 mb-4 ml-4">Main Menu</p>

            <a href="/dashboard/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-th-large text-lg"></i>
                </div>
                <span class="text-sm">Overview</span>
            </a>

            <a href="/web/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/web/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <span class="text-sm">Web3 Wallet</span>
            </a>

            <a href="/withdraw/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/withdraw/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-arrow-up-from-bracket text-lg"></i>
                </div>
                <span class="text-sm">Withdraw</span>
            </a>

            <a href="/history/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/history/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-clock-rotate-left text-lg"></i>
                </div>
                <span class="text-sm">History</span>
            </a>

            <div class="pt-6">
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700 mb-4 ml-4">Support & Account</p>

                <a href="/settings/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/settings/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-user-gear text-lg"></i>
                    </div>
                    <span class="text-sm">Profile & Settings</span>
                </a>

                <a href="/support/<?php echo $req; ?>-Support" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/support/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-headset text-lg"></i>
                    </div>
                    <span class="text-sm">Help Center</span>
                </a>

                <a href="/signin/logout" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl text-red-500/70 hover:bg-red-500/10 hover:text-red-500 transition-all">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-sign-out-alt text-lg"></i>
                    </div>
                    <span class="text-sm">Logout</span>
                </a>
            </div>
        </nav>

        <div class="p-6">
            <div class="card-dark p-6 rounded-3xl border-gold/20 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-12 -mt-12 transition-all group-hover:bg-primary/20"></div>
                <p class="text-[11px] text-primary font-700 uppercase tracking-widest mb-2">Pro Member</p>
                <p class="text-xs text-gray-400 leading-relaxed mb-4">Master the markets with institutional tools.</p>
                <a href="/settings/<?php echo $req; ?>" class="block text-center w-full py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-700 transition-all">Account Settings</a>
            </div>
        </div>
    </aside>

    <!-- Mobile Nav Bottom Bar -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-black/90 backdrop-blur-xl border-t border-white/10 px-6 py-4 z-50 flex justify-between items-center">
        <a href="/dashboard/<?php echo $req; ?>" class="flex flex-col items-center gap-1 <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
            <i class="fas fa-th-large text-lg"></i>
            <span class="text-[10px] font-700 uppercase">Home</span>
        </a>
        <a href="/web/<?php echo $req; ?>" class="flex flex-col items-center gap-1 <?php echo (strpos($_SERVER['REQUEST_URI'], '/web/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
            <i class="fas fa-wallet text-lg"></i>
            <span class="text-[10px] font-700 uppercase">Web3</span>
        </a>
        <a href="/withdraw/<?php echo $req; ?>" class="w-12 h-12 bg-gradient-gold rounded-full flex items-center justify-center -mt-10 shadow-lg shadow-orange-500/40 text-black">
            <i class="fas fa-plus text-xl"></i>
        </a>
        <a href="/history/<?php echo $req; ?>" class="flex flex-col items-center gap-1 <?php echo (strpos($_SERVER['REQUEST_URI'], '/history/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
            <i class="fas fa-clock-rotate-left text-lg"></i>
            <span class="text-[10px] font-700 uppercase">History</span>
        </a>
        <button id="mobile-menu-toggle" onclick="if(typeof openUserMenu==='function')openUserMenu();" class="flex flex-col items-center gap-1 text-gray-500 hover:text-white transition cursor-pointer">
            <i class="fas fa-bars text-lg pointer-events-none"></i>
            <span class="text-[10px] font-700 uppercase pointer-events-none">Menu</span>
        </button>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 w-full lg:ml-72 min-h-screen pb-24 lg:pb-0 overflow-x-hidden">
        <!-- Top Bar -->
        <header class="h-20 border-b border-white/5 bg-black/20 backdrop-blur-md px-4 lg:px-10 flex items-center justify-between sticky top-0 z-30 w-full max-w-full">
            <div class="flex items-center gap-3">
                <button id="top-hamburger-btn" onclick="if(typeof openUserMenu==='function')openUserMenu();" class="lg:hidden p-2 text-gray-300 hover:text-white focus:outline-none bg-white/5 rounded-xl border border-white/10 cursor-pointer" aria-label="Open Navigation">
                    <i class="fas fa-bars text-lg pointer-events-none"></i>
                </button>
                <h2 class="font-syne text-xl font-700 text-white hidden lg:block">
                    <?php
                        if(strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) echo "Market Overview";
                        elseif(strpos($_SERVER['REQUEST_URI'], '/web/') !== false) echo "Web3 Wallet Connect";
                        elseif(strpos($_SERVER['REQUEST_URI'], '/history/') !== false) echo "Transaction History";
                        elseif(strpos($_SERVER['REQUEST_URI'], '/support/') !== false) echo "Support Center";
                        elseif(strpos($_SERVER['REQUEST_URI'], '/withdraw/') !== false) echo "Withdraw Funds";
                        elseif(strpos($_SERVER['REQUEST_URI'], '/settings/') !== false) echo "Profile & Settings";
                        else echo "Dashboard";
                    ?>
                </h2>
                <div class="lg:hidden flex items-center gap-2">
                    <img src="/images/logo.jpg" alt="Nexus" class="h-8 w-auto object-contain rounded-lg">
                </div>
            </div>

            <div class="flex items-center gap-3 lg:gap-8">
                <!-- Market Status -->
                <div class="hidden md:flex items-center gap-2 bg-green-500/10 border border-green-500/20 px-3 py-1.5 rounded-full">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-[11px] font-700 text-green-400 uppercase tracking-wider">Markets Live</span>
                </div>

                <!-- Notifications -->
                <button class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition">
                    <i class="far fa-bell text-gray-400"></i>
                    <span class="absolute top-2.5 right-3 w-2 h-2 bg-primary rounded-full border-2 border-black"></span>
                </button>

                <!-- User Profile -->
                <a href="/settings/<?php echo $req; ?>" class="flex items-center gap-3 pl-3 border-l border-white/10 hover:opacity-90 transition">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-700 text-white uppercase tracking-wider flex items-center justify-end gap-1.5">
                            <?php echo isset($loop['uname']) ? htmlspecialchars($loop['uname']) : 'Trader'; ?>
                            <?php
                                $kycStat = isset($loop['kyc_status']) ? $loop['kyc_status'] : 'Not Submitted';
                                if ($kycStat === 'Verified') {
                                    echo '<span class="inline-flex items-center gap-1 bg-green-500/10 border border-green-500/30 text-green-400 text-[10px] font-800 px-2 py-0.5 rounded-full" title="Identity Verified"><i class="fas fa-check-circle"></i> Verified</span>';
                                } elseif ($kycStat === 'Pending Approval') {
                                    echo '<span class="inline-flex items-center gap-1 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[10px] font-800 px-2 py-0.5 rounded-full" title="KYC Under Review"><i class="fas fa-clock"></i> Pending</span>';
                                } elseif ($kycStat === 'Rejected') {
                                    echo '<span class="inline-flex items-center gap-1 bg-red-500/10 border border-red-500/30 text-red-400 text-[10px] font-800 px-2 py-0.5 rounded-full" title="KYC Rejected"><i class="fas fa-times-circle"></i> Rejected</span>';
                                } else {
                                    echo '<span class="inline-flex items-center gap-1 bg-gray-500/10 border border-gray-500/30 text-gray-400 text-[10px] font-800 px-2 py-0.5 rounded-full"><i class="fas fa-shield-alt"></i> Unverified</span>';
                                }
                            ?>
                        </p>
                        <p class="text-[10px] text-gray-500 font-500">UID: <?php echo isset($u) ? $u : (isset($numbers) ? $numbers : '0000'); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 border border-white/10 flex items-center justify-center font-syne font-800 text-sm text-gray-400 relative">
                        <?php echo isset($loop['uname']) ? strtoupper(substr($loop['uname'], 0, 1)) : 'T'; ?>
                        <?php if (isset($loop['kyc_status']) && $loop['kyc_status'] === 'Verified'): ?>
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 text-black text-[9px] font-900 rounded-full flex items-center justify-center border-2 border-black" title="Verified Account">✓</span>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </header>

        <!-- Content Padding -->
        <div class="p-3 sm:p-6 lg:p-10 max-w-full overflow-x-hidden">
