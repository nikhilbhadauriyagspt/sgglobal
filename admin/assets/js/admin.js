// Skill Grow Institution Admin Console Script
document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('sidebarToggle') || document.getElementById('sidebarToggleBtn');
  const closeBtn = document.getElementById('sidebarCloseBtn');
  const overlay = document.getElementById('sidebarOverlay');
  const wrapper = document.querySelector('.admin-layout-wrapper');

  // Check saved sidebar state for desktop
  if (wrapper && localStorage.getItem('adminSidebarCollapsed') === 'true' && window.innerWidth >= 992) {
    wrapper.classList.add('sidebar-collapsed');
  }

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (window.innerWidth < 992) {
        sidebar.classList.toggle('show-mobile');
        sidebar.classList.toggle('show-sidebar');
        if (overlay) overlay.classList.toggle('show');
      } else if (wrapper) {
        wrapper.classList.toggle('sidebar-collapsed');
        localStorage.setItem('adminSidebarCollapsed', wrapper.classList.contains('sidebar-collapsed'));
        setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
      }
    });
  }

  if (closeBtn && sidebar) {
    closeBtn.addEventListener('click', function () {
      sidebar.classList.remove('show-mobile', 'show-sidebar');
      if (overlay) overlay.classList.remove('show');
    });
  }

  if (overlay && sidebar) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('show-mobile', 'show-sidebar');
      overlay.classList.remove('show');
    });
  }
});
