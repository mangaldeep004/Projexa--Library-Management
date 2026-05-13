/**
 * Smart Library Management System
 * Main JavaScript - Dark Mode, Navigation, FAQ, Counters
 */

/* ============================================================
   LOADING OVERLAY
   ============================================================ */
window.addEventListener('load', () => {
  const overlay = document.getElementById('loading-overlay');
  if (overlay) {
    setTimeout(() => overlay.classList.add('hidden'), 1200);
    setTimeout(() => overlay.remove(), 1800);
  }
});

/* ============================================================
   DARK MODE
   ============================================================ */
const darkModeToggle = document.getElementById('dark-mode-toggle');
const body = document.body;

// Apply saved preference
if (localStorage.getItem('darkMode') === 'true') {
  body.classList.add('dark-mode');
  updateDarkIcon(true);
}

if (darkModeToggle) {
  darkModeToggle.addEventListener('click', () => {
    const isDark = body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', isDark);
    updateDarkIcon(isDark);
  });
}

function updateDarkIcon(isDark) {
  if (!darkModeToggle) return;
  const icon = darkModeToggle.querySelector('i');
  if (icon) {
    icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
  }
}

/* ============================================================
   MOBILE NAVBAR
   ============================================================ */
const hamburger = document.querySelector('.hamburger');
const navLinks  = document.querySelector('.nav-links');

if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    hamburger.classList.toggle('open');
  });
  // Close when link clicked
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      hamburger.classList.remove('open');
    });
  });
}

/* ============================================================
   FAQ ACCORDION
   ============================================================ */
document.querySelectorAll('.faq-question').forEach(q => {
  q.addEventListener('click', () => {
    const answer  = q.nextElementSibling;
    const isOpen  = q.classList.contains('active');

    // Close all
    document.querySelectorAll('.faq-question').forEach(item => {
      item.classList.remove('active');
      item.nextElementSibling.classList.remove('open');
    });

    // Open clicked if was closed
    if (!isOpen) {
      q.classList.add('active');
      answer.classList.add('open');
    }
  });
});

/* ============================================================
   ANIMATED COUNTERS
   ============================================================ */
function animateCounter(el) {
  const target   = parseInt(el.dataset.target, 10);
  const duration = 1800;
  const step     = target / (duration / 16);
  let current    = 0;

  const timer = setInterval(() => {
    current += step;
    if (current >= target) {
      current = target;
      clearInterval(timer);
    }
    el.textContent = Math.floor(current).toLocaleString();
  }, 16);
}

// Use IntersectionObserver to trigger counters when visible
const counters = document.querySelectorAll('.counter');
if (counters.length) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !entry.target.dataset.animated) {
        entry.target.dataset.animated = 'true';
        animateCounter(entry.target);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => observer.observe(c));
}

/* ============================================================
   FLASH MESSAGES - Auto hide
   ============================================================ */
document.querySelectorAll('.alert').forEach(alert => {
  setTimeout(() => {
    alert.style.opacity = '0';
    alert.style.transition = 'opacity 0.5s';
    setTimeout(() => alert.remove(), 500);
  }, 4000);
});

/* ============================================================
   NAVBAR SCROLL EFFECT
   ============================================================ */
const navbar = document.querySelector('.navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
      navbar.style.boxShadow = '0 4px 24px rgba(0,0,0,.12)';
    } else {
      navbar.style.boxShadow = 'none';
    }
  });
}

/* ============================================================
   ACTIVE NAV LINK
   ============================================================ */
const currentPath = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-links a').forEach(a => {
  if (a.getAttribute('href') && a.getAttribute('href').includes(currentPath)) {
    a.classList.add('active');
  }
});

/* ============================================================
   REAL-TIME SEARCH (for browse pages)
   ============================================================ */
const searchInput = document.getElementById('live-search');
if (searchInput) {
  let timeout;
  searchInput.addEventListener('input', () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      const query = searchInput.value.trim();
      const category = document.getElementById('cat-filter')?.value || '';

      fetch(`../api/search_books.php?q=${encodeURIComponent(query)}&cat=${category}`)
        .then(r => r.json())
        .then(data => renderBooks(data))
        .catch(() => {});
    }, 350);
  });
}

function renderBooks(books) {
  const grid = document.getElementById('books-container');
  if (!grid) return;
  if (!books.length) {
    grid.innerHTML = '<p class="text-center text-muted" style="grid-column:1/-1;padding:3rem">No books found.</p>';
    return;
  }
  const colors = ['c1','c2','c3','c4','c5','c6'];
  grid.innerHTML = books.map((b, i) => `
    <div class="book-card">
      <div class="book-cover ${colors[i % colors.length]}">
        <i class="fa-solid fa-book"></i>
        <span class="book-avail ${b.available_copies > 0 ? '' : 'unavailable'}">
          ${b.available_copies > 0 ? 'Available' : 'Unavailable'}
        </span>
      </div>
      <div class="book-info">
        <div class="book-title">${escHtml(b.title)}</div>
        <div class="book-author">${escHtml(b.author)}</div>
        <div class="book-actions">
          <a href="book_detail.php?id=${b.id}" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-eye"></i> View
          </a>
        </div>
      </div>
    </div>
  `).join('');
}

function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

/* ============================================================
   NOTIFICATION DROPDOWN
   ============================================================ */
const notifBtn  = document.querySelector('.notif-btn');
const notifDrop = document.querySelector('.notif-dropdown');
if (notifBtn && notifDrop) {
  notifBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    notifDrop.classList.toggle('open');
  });
  document.addEventListener('click', () => {
    notifDrop.classList.remove('open');
  });
}

/* ============================================================
   FORM VALIDATION HELPERS
   ============================================================ */
function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
function validatePhone(phone) {
  return /^[0-9]{10}$/.test(phone);
}
function showError(inputId, msg) {
  const el = document.getElementById(inputId);
  if (!el) return;
  el.style.borderColor = 'var(--danger)';
  let err = el.parentElement.querySelector('.field-error');
  if (!err) {
    err = document.createElement('small');
    err.className = 'field-error text-danger';
    el.parentElement.appendChild(err);
  }
  err.textContent = msg;
}
function clearError(inputId) {
  const el = document.getElementById(inputId);
  if (!el) return;
  el.style.borderColor = '';
  const err = el.parentElement.querySelector('.field-error');
  if (err) err.remove();
}

/* ============================================================
   CONFIRM BEFORE DELETE
   ============================================================ */
document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
  btn.addEventListener('click', (e) => {
    if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
      e.preventDefault();
    }
  });
});

/* ============================================================
   TOOLTIP (simple)
   ============================================================ */
document.querySelectorAll('[data-tooltip]').forEach(el => {
  el.style.position = 'relative';
  el.addEventListener('mouseenter', () => {
    const tip = document.createElement('div');
    tip.className = 'tooltip-popup';
    tip.textContent = el.dataset.tooltip;
    tip.style.cssText = `
      position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);
      background:#1e293b;color:white;padding:.3rem .7rem;border-radius:6px;
      font-size:.78rem;white-space:nowrap;z-index:999;pointer-events:none;
    `;
    el.appendChild(tip);
  });
  el.addEventListener('mouseleave', () => {
    el.querySelector('.tooltip-popup')?.remove();
  });
});
