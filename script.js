document.addEventListener('DOMContentLoaded', () => {
  const header = document.getElementById('site-header');
  const mobileToggle = document.getElementById('mobile-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const yearSpan = document.getElementById('year');

  // Solid header on scroll
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      header.classList.add('header-solid');
    } else {
      header.classList.remove('header-solid');
    }
  });

  // Mobile menu toggle with slide animation
  if (mobileToggle && mobileMenu) {
    mobileToggle.addEventListener('click', () => {
      if (mobileMenu.classList.contains('max-h-0')) {
        mobileMenu.classList.remove('max-h-0');
        mobileMenu.classList.add('max-h-screen');
      } else {
        mobileMenu.classList.remove('max-h-screen');
        mobileMenu.classList.add('max-h-0');
      }
    });

    // Optional: close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
        mobileMenu.classList.remove('max-h-screen');
        mobileMenu.classList.add('max-h-0');
      }
    });
  }

  // Auto-update footer year
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
});
