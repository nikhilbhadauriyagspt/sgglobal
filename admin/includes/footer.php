<?php
// Admin Footer Component
?>
      <!-- Admin Content Footer -->
      <footer class="admin-footer-bar">
        <div class="row align-items-center">
          <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
            <span class="text-muted" style="font-size: 0.85rem;">
              &copy; <?= date('Y') ?> <strong class="text-warning">Skill Grow Institution</strong>. Centralized Administrative Portal.
            </span>
          </div>
          <div class="col-md-6 text-center text-md-end">
            <span class="badge rounded-pill bg-dark border border-secondary px-3 py-2 text-muted" style="font-size: 0.75rem;">
              <i class="fas fa-server text-success me-1"></i> Laragon MySQL 8.4 • PHP 8.3
            </span>
          </div>
        </div>
      </footer>

    </div><!-- End admin-content -->
  </div><!-- End admin-layout-wrapper -->

  <!-- Bootstrap 5 Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Admin Interaction Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('adminSidebar');
      const toggleBtn = document.getElementById('sidebarToggle');
      const closeBtn = document.getElementById('sidebarCloseBtn');
      const overlay = document.getElementById('sidebarOverlay');
      const wrapper = document.querySelector('.admin-layout-wrapper');

      // Check saved sidebar state for desktop
      if (localStorage.getItem('adminSidebarCollapsed') === 'true' && window.innerWidth >= 992) {
        wrapper.classList.add('sidebar-collapsed');
      }

      // Sidebar Toggle Handler
      if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
          if (window.innerWidth < 992) {
            // Mobile: toggle active drawer
            sidebar.classList.toggle('show-mobile');
            if (overlay) overlay.classList.toggle('show');
          } else {
            // Desktop: toggle mini/collapsed sidebar
            wrapper.classList.toggle('sidebar-collapsed');
            localStorage.setItem('adminSidebarCollapsed', wrapper.classList.contains('sidebar-collapsed'));
          }
        });
      }

      // Mobile Close Handler
      if (closeBtn) {
        closeBtn.addEventListener('click', function() {
          sidebar.classList.remove('show-mobile');
          if (overlay) overlay.classList.remove('show');
        });
      }

      // Overlay click to close
      if (overlay) {
        overlay.addEventListener('click', function() {
          sidebar.classList.remove('show-mobile');
          overlay.classList.remove('show');
        });
      }

      // Live Clock
      function updateLiveClock() {
        const clockEl = document.getElementById('adminLiveClock');
        if (clockEl) {
          const now = new Date();
          const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
          clockEl.textContent = now.toLocaleDateString('en-GB', options);
        }
      }
      setInterval(updateLiveClock, 1000);
    });
  </script>
</body>
</html>
