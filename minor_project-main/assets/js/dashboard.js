/**
 * Dashboard Charts & Sidebar Toggle
 * Uses Chart.js (loaded via CDN)
 */

/* ============================================================
   SIDEBAR TOGGLE
   ============================================================ */
const sidebar     = document.querySelector('.sidebar');
const toggleBtn   = document.getElementById('toggle-sidebar');
const sidebarOverlay = document.querySelector('.sidebar-overlay');

if (toggleBtn) {
  toggleBtn.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
      // Mobile: slide in/out
      sidebar.classList.toggle('mobile-open');
      sidebarOverlay?.classList.toggle('visible');
    } else {
      // Desktop: collapse/expand
      sidebar.classList.toggle('collapsed');
      localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
  });
}

if (sidebarOverlay) {
  sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.remove('mobile-open');
    sidebarOverlay.classList.remove('visible');
  });
}

// Restore sidebar state on desktop
if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === 'true') {
  sidebar?.classList.add('collapsed');
}

/* ============================================================
   CHARTS - Admin Dashboard
   ============================================================ */
function initCharts() {
  // Chart.js global defaults
  if (typeof Chart === 'undefined') return;

  Chart.defaults.font.family = 'Inter, sans-serif';
  Chart.defaults.color = getComputedStyle(document.body).getPropertyValue('--text-muted').trim();

  const isDark = document.body.classList.contains('dark-mode');
  const gridColor = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';

  // ---- Monthly Issues Line Chart ----
  const issueCtx = document.getElementById('issueChart');
  if (issueCtx) {
    const labels  = issueCtx.dataset.labels  ? JSON.parse(issueCtx.dataset.labels)  : ['Jan','Feb','Mar','Apr','May','Jun'];
    const values  = issueCtx.dataset.values  ? JSON.parse(issueCtx.dataset.values)  : [12, 19, 8, 24, 15, 21];

    new Chart(issueCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Books Issued',
          data: values,
          fill: true,
          backgroundColor: 'rgba(102,126,234,.12)',
          borderColor: '#667eea',
          borderWidth: 2.5,
          tension: 0.45,
          pointBackgroundColor: '#667eea',
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: gridColor, drawBorder: false } },
          y: { grid: { color: gridColor, drawBorder: false }, beginAtZero: true }
        }
      }
    });
  }

  // ---- Category Doughnut Chart ----
  const catCtx = document.getElementById('categoryChart');
  if (catCtx) {
    const labels  = catCtx.dataset.labels ? JSON.parse(catCtx.dataset.labels) : ['CS','Math','Physics','Literature','Business'];
    const values  = catCtx.dataset.values ? JSON.parse(catCtx.dataset.values) : [8, 5, 4, 3, 3];

    new Chart(catCtx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: values,
          backgroundColor: ['#667eea','#764ba2','#f093fb','#43e97b','#f59e0b','#ef4444'],
          borderWidth: 0,
          hoverOffset: 6,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
        }
      }
    });
  }

  // ---- Available vs Issued Bar Chart ----
  const avCtx = document.getElementById('availabilityChart');
  if (avCtx) {
    new Chart(avCtx, {
      type: 'bar',
      data: {
        labels: ['Total Books','Available','Issued','Overdue'],
        datasets: [{
          label: 'Count',
          data: [
            parseInt(avCtx.dataset.total   || 0),
            parseInt(avCtx.dataset.avail   || 0),
            parseInt(avCtx.dataset.issued  || 0),
            parseInt(avCtx.dataset.overdue || 0),
          ],
          backgroundColor: ['#667eea','#10b981','#f59e0b','#ef4444'],
          borderRadius: 8,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: gridColor, drawBorder: false }, beginAtZero: true }
        }
      }
    });
  }

  // ---- Fines Bar Chart (Student dashboard) ----
  const fineCtx = document.getElementById('fineChart');
  if (fineCtx) {
    new Chart(fineCtx, {
      type: 'bar',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
          label: 'Fine (₹)',
          data: [0, 4, 0, 6, 0, 2],
          backgroundColor: 'rgba(239,68,68,.7)',
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: gridColor }, beginAtZero: true }
        }
      }
    });
  }
}

// Init charts after DOM loaded
document.addEventListener('DOMContentLoaded', initCharts);

// Re-init if dark mode toggled
document.getElementById('dark-mode-toggle')?.addEventListener('click', () => {
  setTimeout(initCharts, 300);
});
