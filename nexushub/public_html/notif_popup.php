<?php
// Skip notification popup on admin pages
$uri = strtolower($_SERVER['REQUEST_URI'] ?? '');
if (strpos($uri, 'admin') !== false || strpos($uri, 'user_management') !== false) {
    return;
}
?>
<style>
/* Notification Popup Shared Styles */
#notification-toast {
    position: fixed;
    left: 20px;
    bottom: 20px;
    width: min(380px, calc(100vw - 32px));
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;

    background: #ffffff;
    color: #111827;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.3);

    z-index: 99999;
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
    transform: translateY(20px);

    transition:
        opacity 0.3s ease,
        transform 0.3s ease,
        visibility 0.3s ease;
}

#notification-toast.show {
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}

#notification-toast .notif-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #16a34a;
    color: #ffffff;
}

#notification-toast p {
    overflow-wrap: anywhere;
}

#notification-toast button {
    border: none;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    align-self: flex-start;
}

@media (max-width: 480px) {
    #notification-toast {
        left: 16px;
        bottom: 16px;
        width: 65%;
        max-width: 65%;
    }
}

@media (prefers-reduced-motion: reduce) {
    #notification-toast {
        transition: none;
    }
}
</style>

<script>
(function () {
    if (window.__notifToastInitialized) return;
    window.__notifToastInitialized = true;

    function initNotifToast() {
        let toast = document.getElementById('notification-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'notification-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.innerHTML = `
                <div class="notif-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p id="notif-text" style="margin: 0; font-size: 13px; font-weight: 600; color: #111827;">Welcome to NexusHub</p>
                    <p id="notif-sub" style="margin: 5px 0 0; font-size: 11px; color: #6b7280;">Platform notifications</p>
                </div>
                <button type="button" id="notif-close" aria-label="Close notification">
                    <i class="fas fa-times"></i>
                </button>
            `;
            document.body.appendChild(toast);
        }

        const text = document.getElementById('notif-text');
        const sub = document.getElementById('notif-sub');
        const closeBtn = document.getElementById('notif-close');
        if (!text || !sub) return;

        const names = ['Michael','Sarah','Enock','Joebat','Gabriella','Kidi','Adam','Lisa','James','Raj','Tommy','Ernesto'];
        const actions = ['withdrawal'];
        const amounts = ['$120,000','$350,000','$800,000','$100,000','$250,000','$500,000','$1,000,000'];
        const timeAgo = ['Just now','2s ago','5s ago','12s ago','28s ago','45s ago','1 min ago'];

        let dismissed = false;
        let showTimer, hideTimer;

        const badge = '<span style="display:inline-flex;align-items:center;gap:3px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.35);color:#16a34a;font-size:10px;padding:1px 7px;border-radius:12px;font-weight:700;"><i class="fas fa-check-circle" style="font-size:9px;"></i> Verified</span><br>';

        function hide() { toast.classList.remove('show'); }

        function show() {
            if (dismissed) return;
            const name = names[Math.floor(Math.random() * names.length)];
            const action = 'withdrawal';
            const amount = amounts[Math.floor(Math.random() * amounts.length)];
            const ago = timeAgo[Math.floor(Math.random() * timeAgo.length)];

            text.innerHTML = name + ' ' + badge + ' ' + action + ' <strong>' + amount + '</strong>';
            sub.textContent = ago + ' · BTC';

            toast.classList.add('show');
            hideTimer = setTimeout(function () { hide(); schedule(); }, 3500 + Math.random() * 2000);
        }

        function schedule() {
            if (dismissed) return;
            showTimer = setTimeout(show, 2500 + Math.random() * 1000);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                dismissed = true;
                clearTimeout(showTimer);
                clearTimeout(hideTimer);
                hide();
            });
        }

        schedule();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifToast);
    } else {
        initNotifToast();
    }
})();
</script>
