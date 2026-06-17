(function () {
  'use strict';

  function ready(callback) {
    if (document.readyState !== 'loading') {
      callback();
      return;
    }
    document.addEventListener('DOMContentLoaded', callback);
  }

  ready(function () {
    const hamburger = document.querySelector('.header__hamburger');
    if (hamburger) {
      hamburger.addEventListener('click', function () {
        const isOpen = document.body.classList.toggle('menu-open');
        hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }

    const iconSearch = document.querySelectorAll('.icon-search a');
    const searchPanels = document.querySelectorAll('.search-panel');

    iconSearch.forEach(function (link, index) {
      link.addEventListener('click', function (event) {
        event.preventDefault();

        const panel = searchPanels[index] || searchPanels[0];
        if (!panel) {
          return;
        }

        panel.style.display = 'block';
        panel.setAttribute('aria-hidden', 'false');

        const input = panel.querySelector('input[type="text"]');
        if (input) {
          setTimeout(function () {
            document.body.classList.add('js-focus-active');
            input.focus({ preventScroll: true });

            const removeJsFocus = function () {
              document.body.classList.remove('js-focus-active');
              window.removeEventListener('keydown', removeJsFocus);
              window.removeEventListener('mousedown', removeJsFocus);
            };
            window.addEventListener('keydown', removeJsFocus);
            window.addEventListener('mousedown', removeJsFocus);
          }, 200);
        }
      });
    });

    document.querySelectorAll('.btn-close-search').forEach(function (link, index) {
      link.addEventListener('click', function (event) {
        event.preventDefault();

        const panel = searchPanels[index] || searchPanels[0];
        if (!panel) {
          return;
        }

        panel.style.display = 'none';
        panel.setAttribute('aria-hidden', 'true');

        if (iconSearch[index]) {
          iconSearch[index].focus();
        }
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') {
        return;
      }

      searchPanels.forEach(function (panel, index) {
        if (panel.style.display === 'block') {
          panel.style.display = 'none';
          panel.setAttribute('aria-hidden', 'true');
          if (iconSearch[index]) {
            iconSearch[index].focus();
          }
        }
      });
    });

    document.querySelectorAll('.header__menu li.menu-item-has-children').forEach(function (item) {
      const link = item.querySelector('a');
      const subMenu = item.querySelector('ul');

      if (!link || !subMenu) {
        return;
      }

      link.addEventListener('focus', function () {
        item.classList.add('is-focused');
      });

      const subMenuLinks = subMenu.querySelectorAll('a');
      const lastSubMenuLink = subMenuLinks[subMenuLinks.length - 1];

      if (lastSubMenuLink) {
        lastSubMenuLink.addEventListener('blur', function () {
          item.classList.remove('is-focused');
        });
      }

      item.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          item.classList.remove('is-focused');
          link.focus();
        }
      });
    });

    const backTop = document.querySelector('.back-top');
    if (backTop) {
      const backTopLink = backTop.querySelector('a');
      if (backTopLink) {
        backTopLink.addEventListener('focus', function () {
          document.body.classList.add('up-page');
        });
      }
    }

    document.querySelectorAll('.scroll a[href^="#"]').forEach(function (elem) {
      elem.addEventListener('click', function (event) {
        const target = elem.getAttribute('href');
        const block = target ? document.querySelector(target) : null;

        if (!block) {
          return;
        }

        event.preventDefault();
        const offset = elem.dataset.offset ? parseInt(elem.dataset.offset, 10) : 0;
        const bodyOffset = document.body.getBoundingClientRect().top;

        window.scrollTo({
          top: block.getBoundingClientRect().top - bodyOffset + offset,
          behavior: 'smooth'
        });
        document.body.classList.remove('menu-open');
      });
    });

    const rootUrl = document.location.href.match(/(^[^#]*)/);
    if (rootUrl && rootUrl[0]) {
      document.querySelectorAll('.page-scroll a[href^="' + rootUrl[0] + '#"]').forEach(function (elem) {
        elem.addEventListener('click', function (event) {
          const elemId = elem.getAttribute('href').replace(rootUrl[0], '');
          const block = elemId ? document.querySelector(elemId) : null;

          if (!block) {
            return;
          }

          event.preventDefault();
          const offset = elem.dataset.offset ? parseInt(elem.dataset.offset, 10) : 0;
          const bodyOffset = document.body.getBoundingClientRect().top;

          window.scrollTo({
            top: block.getBoundingClientRect().top - bodyOffset + offset,
            behavior: 'smooth'
          });
          document.body.classList.remove('menu-open');
        });
      });
    }

    document.querySelectorAll('.back-top').forEach(function (button) {
      button.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });

    document.querySelectorAll('.accordion-header').forEach(function (header) {
      header.addEventListener('click', function () {
        const content = this.nextElementSibling;
        if (!content) {
          return;
        }

        if (content.style.maxHeight) {
          content.style.maxHeight = null;
          return;
        }

        document.querySelectorAll('.accordion-content').forEach(function (item) {
          item.style.maxHeight = null;
        });
        content.style.maxHeight = content.scrollHeight + 'px';
      });
    });

    document.querySelectorAll('a.toggle-link[data-toggle="true"]').forEach(function (link) {
      link.addEventListener('click', function (event) {
        event.preventDefault();
        const li = this.closest('li');
        const container = document.getElementById(this.getAttribute('aria-controls'));

        if (!li) {
          return;
        }

        const isOpen = li.classList.contains('open');
        li.classList.toggle('open');
        this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

        if (container) {
          container.style.display = isOpen ? 'none' : 'block';
        }
      });
    });

    document.querySelectorAll('.product-categories a.current-cat, .product-categories a.current-cat-parent, .product-categories a.current-cat-ancestor').forEach(function (activeLink) {
      let li = activeLink.closest('li');
      while (li) {
        if (li.classList.contains('has-children')) {
          li.classList.add('open');
          const toggle = li.querySelector('a.toggle-link');
          if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
          }
          const container = li.querySelector('.submenu-container');
          if (container) {
            container.style.display = 'block';
          }
        }
        li = li.parentElement ? li.parentElement.closest('li') : null;
      }
    });

    document.querySelectorAll('.tab-list li').forEach(function (tab) {
      tab.addEventListener('click', function () {
        document.querySelectorAll('.tab-list li').forEach(function (item) {
          item.classList.remove('active');
        });
        tab.classList.add('active');
      });
    });

    if (typeof gsap !== 'undefined') {
      const timeline = gsap.timeline();
      timeline.to('.fade-in', { opacity: 1, y: 0, duration: 0.4, stagger: 0.3, ease: 'power4.out' }, '0.5');
      timeline.to('.text-reveal', { clipPath: 'polygon(0 0, 100% 0, 100% 100%, 0 100%)', y: 0, duration: 0.8, stagger: 0.3 }, 0.7);

      if (typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        ScrollTrigger.batch('.fade-in-scroll', {
          start: 'top 60%',
          onEnter: function (elements) {
            gsap.to(elements, { opacity: 1, y: 0, stagger: 0.3, duration: 0.8, ease: 'power2.out' });
          }
        });
      }
    }

    if (typeof Splide !== 'undefined' && document.querySelector('.splide')) {
      const splide = new Splide('.splide', {
        type: 'loop',
        perPage: 5,
        perMove: 1,
        gap: '15px',
        pagination: false,
        breakpoints: {
          768: { perPage: 1 },
          1024: { perPage: 3 }
        }
      });
      splide.mount();
    }
  });

  window.addEventListener('keydown', function (event) {
    if (event.key === 'Tab') {
      document.body.classList.add('user-is-tabbing');
    }
  });

  window.addEventListener('mousedown', function () {
    document.body.classList.remove('user-is-tabbing');
  });

  window.addEventListener('scroll', function () {
    document.body.classList.toggle('scroll-down', window.scrollY > 300);
    document.body.classList.toggle('up-page', window.scrollY > 1600);
  });

  document.addEventListener('click', function (event) {
    const button = event.target.closest('.qty-btn');
    if (!button) {
      return;
    }

    const wrapper = button.closest('.quantity-wrapper');
    const input = wrapper ? wrapper.querySelector('.qty') : null;
    if (!input) {
      return;
    }

    let value = parseInt(input.value, 10) || 1;
    const min = parseInt(input.min, 10) || 1;
    const max = parseInt(input.max, 10) || 999;

    if (button.classList.contains('qty-plus') && value < max) {
      input.value = value + 1;
    }

    if (button.classList.contains('qty-minus') && value > min) {
      input.value = value - 1;
    }

    input.dispatchEvent(new Event('change', { bubbles: true }));
  });
}());