document.addEventListener('DOMContentLoaded', () => {
  const header = document.getElementById('site-header');
  const mobileToggle = document.getElementById('mobile-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const yearSpan = document.getElementById('year');

  // Solid header on scroll
  if (header) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        header.classList.add('header-solid');
      } else {
        header.classList.remove('header-solid');
      }
    });
  }

  // Mobile menu toggle with slide animation
  if (mobileToggle && mobileMenu) {
    const icon = mobileToggle.querySelector('i');

    const openMenu = () => {
      mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
      mobileToggle.setAttribute('aria-expanded', 'true');
      if (icon) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
      }
    };

    const closeMenu = () => {
      mobileMenu.style.maxHeight = '0px';
      mobileToggle.setAttribute('aria-expanded', 'false');
      if (icon) {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    };

    mobileToggle.addEventListener('click', (event) => {
      event.stopPropagation();
      const isOpen = mobileMenu.style.maxHeight !== '' && mobileMenu.style.maxHeight !== '0px';
      isOpen ? closeMenu() : openMenu();
    });

    document.addEventListener('click', (event) => {
      if (!mobileMenu.contains(event.target) && !mobileToggle.contains(event.target)) {
        closeMenu();
      }
    });
  }

  // Auto-update footer year
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
});