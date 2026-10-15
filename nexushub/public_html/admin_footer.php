        </main>
    </div>
</div>

<script>
// Mobile Sidebar Toggle
const mobileBtn = document.getElementById('mobileAdminMenuBtn');
if (mobileBtn) {
    mobileBtn.addEventListener('click', function() {
        const sidebar = document.querySelector('aside');
        if (sidebar) {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('w-full');
            sidebar.classList.toggle('z-50');
        }
    });
}
</script>
</body>
</html>
