        </div>
    </main>
</div>

<!-- Mobile Menu Overlay (for 'More' button) -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[60] hidden flex-col p-8">
    <div class="flex justify-between items-center mb-12">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-gold rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-black text-lg"></i>
            </div>
            <span class="font-syne text-xl font-800 gradient-text">vertexxHub</span>
        </div>
        <button id="close-overlay" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <div class="space-y-6">
        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700">Account & Support</p>
        <a href="/support/<?php echo $req; ?>-Support" class="flex items-center gap-5 text-xl font-syne font-700 text-white">
            <i class="fas fa-headset text-primary"></i> Help Center
        </a>
        <a href="/signin/logout" class="flex items-center gap-5 text-xl font-syne font-700 text-red-500">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="mt-auto pb-12">
        <div class="card-dark p-8 rounded-3xl border-gold/20">
            <p class="text-primary font-700 uppercase tracking-widest mb-2 text-xs">Join our community</p>
            <p class="text-gray-400 text-sm leading-relaxed mb-6">Stay updated with real-time market signals and platform news.</p>
            <div class="flex gap-4">
                <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white text-xl"><i class="fab fa-telegram"></i></a>
                <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white text-xl"><i class="fab fa-twitter"></i></a>
                <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white text-xl"><i class="fab fa-discord"></i></a>
            </div>
        </div>
    </div>
</div>

<script>
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const closeOverlay = document.getElementById('close-overlay');
    const overlay = document.getElementById('mobile-overlay');

    if(mobileToggle && overlay) {
        mobileToggle.addEventListener('click', () => {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    }

    if(closeOverlay && overlay) {
        closeOverlay.addEventListener('click', () => {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = 'auto';
        });
    }
</script>
