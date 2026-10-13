(() => {
  'use strict';

  const header = document.querySelector('.topbar');
  const toggle = document.querySelector('.nav-toggle');
  const navigation = document.getElementById('primary-navigation');
  const mobile = window.matchMedia('(max-width: 950px)');

  if (header && toggle && navigation) {
    const closeMenu = () => {
      toggle.setAttribute('aria-expanded', 'false');
      navigation.classList.remove('is-open');
    };
    header.classList.add('navigation-enhanced');
    toggle.hidden = false;
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(open));
      navigation.classList.toggle('is-open', open);
    });
    navigation.addEventListener('click', (event) => {
      if (mobile.matches && event.target.closest('a')) closeMenu();
    });
    header.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        closeMenu();
        toggle.focus();
      }
    });
    mobile.addEventListener('change', closeMenu);
  }

  const items = document.querySelectorAll('.reveal');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (!('IntersectionObserver' in window) || reducedMotion.matches) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08 });

  items.forEach((item) => {
    item.classList.add('reveal-ready');
    observer.observe(item);
  });
  reducedMotion.addEventListener('change', (event) => {
    if (event.matches) {
      observer.disconnect();
      items.forEach((item) => item.classList.add('visible'));
    }
  });
})();
