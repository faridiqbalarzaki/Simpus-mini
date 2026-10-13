</main>

        <footer>
            <div class="footer-content">
                <p>&copy; <?php echo date("Y"); ?> <strong style="color: var(--bar);">SIMPUS-Mini</strong> &middot; Jobsheet 13</p>
            </div>
        </footer>
    </div> <!-- end app-layout -->

    <!-- JS Utama (Menggunakan Path Absolut) -->
    <script src="/assets/js/app.js"></script>
    <script>
    // Penanganan toggle sidebar untuk tampilan mobile
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("nav-toggle-btn");
        const sidebar = document.getElementById("app-sidebar");
        const backdrop = document.getElementById("sidebar-backdrop");
        
        // Ambil elemen ikon di dalam tombol toggle
        const toggleIcon = toggleBtn ? toggleBtn.querySelector('i') : null;

        function toggleSidebar(open) {
            sidebar.classList.toggle("open", open);
            backdrop.classList.toggle("show", open);
            
            // Ubah ikon saat sidebar terbuka/tertutup (List -> X)
            if (toggleIcon) {
                if (open) {
                    toggleIcon.classList.remove('ph-list');
                    toggleIcon.classList.add('ph-x');
                } else {
                    toggleIcon.classList.remove('ph-x');
                    toggleIcon.classList.add('ph-list');
                }
            }
        }

        if (toggleBtn && sidebar && backdrop) {
            toggleBtn.addEventListener("click", function () {
                toggleSidebar(!sidebar.classList.contains("open"));
            });
            backdrop.addEventListener("click", function () {
                toggleSidebar(false);
            });
        }
    });
    </script>
    <?php if (!empty($extra_scripts)): foreach ($extra_scripts as $src): ?>
    <script src="<?php echo $src; ?>"></script>
    <?php endforeach;
    endif; ?>
</body>
</html>