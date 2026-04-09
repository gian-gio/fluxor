
let hamburger = document.querySelector('.header__hamburger');

hamburger.addEventListener("click", function() {
  document.body.classList.toggle('menu-open');
});


/*  -----------------------------------------------------------------------------------------------
  Search Panel
--------------------------------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function() {

    // Search Panel Close 
    const iconSearch = document.querySelectorAll('.icon-search a');
        const searchPanels = document.querySelectorAll(".search-panel");

        iconSearch.forEach(function(link, index) {
            link.addEventListener("click", function(event) {
                event.preventDefault();
                
                if (searchPanels[index]) {
                    searchPanels[index].style.display = "block";
                    
                    const input = searchPanels[index].querySelector('input[type="text"]');
                    
                    if (input) {
                        setTimeout(() => {
                            document.body.classList.add('js-focus-active');
                            
                            input.focus({ preventScroll: true });

                            const removeJsFocus = () => {
                                document.body.classList.remove('js-focus-active');
                                window.removeEventListener('keydown', removeJsFocus);
                                window.removeEventListener('mousedown', removeJsFocus);
                            };
                            window.addEventListener('keydown', removeJsFocus);
                            window.addEventListener('mousedown', removeJsFocus);
                            // ----------------------
                        }, 200);
                    }
                }
            });
        });

    // Search Panel Close
    const btnCloseSearch = document.querySelectorAll('.btn-close-search');

    btnCloseSearch.forEach(function(link, index) {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            
            if (searchPanels[index]) {
                searchPanels[index].style.display = "none";
                
                // Riporta il focus sull'icona che ha aperto il pannello (fondamentale per accessibilità)
                if (iconSearch[index]) {
                    iconSearch[index].focus();
                }
            }
        });
    });

    // Closing with esc key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            searchPanels.forEach(function(panel, index) {
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                    if (iconSearch[index]) iconSearch[index].focus();
                }
            });
        }
    });

});

/*  -----------------------------------------------------------------------------------------------
  Skip to content and nav 
--------------------------------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.header__menu li.menu-item-has-children');

    menuItems.forEach(function(item) {
        const link = item.querySelector('a');
        const subMenu = item.querySelector('ul');

        // 1. Gestione Focus: aggiunge una classe quando il link o i figli ricevono focus
        link.addEventListener('focus', function() {
            item.classList.add('is-focused');
        });

        // 2. Gestione Blur: rimuove la classe quando il focus esce dall'ultimo elemento del submenu
        const subMenuLinks = subMenu.querySelectorAll('a');
        const lastSubMenuLink = subMenuLinks[subMenuLinks.length - 1];

        lastSubMenuLink.addEventListener('blur', function() {
            item.classList.remove('is-focused');
        });

        // 3. Supporto tasto ESC: chiude il submenu se l'utente preme Esc mentre è dentro
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                item.classList.remove('is-focused');
                link.focus(); // Riporta il focus al link principale
            }
        });
    });
});



// Accessibility Management Back to Top
const backTop = document.querySelector('.back-top');
if (backTop) {
    const backTopLink = backTop.querySelector('a');
    
    // Se l'utente arriva al fondo col tab e trova il link, 
    // lo rendiamo visibile forzatamente tramite CSS (gestito da :focus-within sopra)
    backTopLink.addEventListener('focus', function() {
        document.body.classList.add('up-page');
    });
}



/*  -----------------------------------------------------------------------------------------------
  Header Menu add class scroll 
--------------------------------------------------------------------------------------------------- */

window.addEventListener('scroll', function(e) {
  
  if(window.scrollY > 300){
    document.body.classList.add('scroll-down');
  } else {
    document.body.classList.remove('scroll-down');
  }

  if(window.scrollY > 1600){
    document.body.classList.add('up-page');
  } else {
    document.body.classList.remove('up-page');
  }

});


// js scroll to
document.querySelectorAll('.scroll a[href^="#"]').forEach(elem => {
  elem.addEventListener('click', e => {
      e.preventDefault();
      let block = document.querySelector(elem.getAttribute('href')),
          offset = elem.dataset.offset ? parseInt(elem.dataset.offset) : 0,
          bodyOffset = document.body.getBoundingClientRect().top;
      window.scrollTo({
          top: block.getBoundingClientRect().top - bodyOffset + offset,
          behavior: 'smooth'
      }); 
      document.body.classList.remove('menu-open');
  });
});

// One page scroll  
let root_url = document.location.href.match(/(^[^#]*)/)

document.querySelectorAll('.page-scroll a[href^="'+root_url[0]+'#"]').forEach(elem => {
elem.addEventListener('click', e => {
    e.preventDefault();
    elem_id = elem.getAttribute('href').replace(root_url[0], "");
    let block = document.querySelector(elem_id),
        offset = elem.dataset.offset ? parseInt(elem.dataset.offset) : 0,
        bodyOffset = document.body.getBoundingClientRect().top;
    window.scrollTo({
        top: block.getBoundingClientRect().top - bodyOffset + offset,
        behavior: 'smooth'
    }); 
    document.body.classList.remove('menu-open');
});
});



// Scroll up page
const scrollUpButtons = document.querySelectorAll('.back-top');

// Aggiunge un evento di click a ciascun elemento
scrollUpButtons.forEach(button => {
  button.addEventListener('click', () => {
    // Effettua lo scroll verso l'alto con animazione fluida
    window.scrollTo({
      top: 0, // Posizione verticale 0
      behavior: 'smooth' // Scorrimento fluido
    });
  });
});



// Accordion
document.addEventListener("DOMContentLoaded", function () {
  var headers = document.querySelectorAll(".accordion-header");

  headers.forEach(function (header) {
      header.addEventListener("click", function () {
          var content = this.nextElementSibling;

          if (content.style.maxHeight) {
              content.style.maxHeight = null;
          } else {
              var allContents = document.querySelectorAll(".accordion-content");
              allContents.forEach(function (item) {
                  item.style.maxHeight = null;
              });
              content.style.maxHeight = content.scrollHeight + "px";
          }
      });
  });
});


/*
WooCommerce scripts
----------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('a.toggle-link[data-toggle="true"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault(); // disattiva la navigazione per il toggle
      const li = this.closest('li');
      const container = document.getElementById(this.getAttribute('aria-controls'));
      const isOpen = li.classList.contains('open');

      li.classList.toggle('open');
      this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

      if (container) {
        container.style.display = isOpen ? 'none' : 'block';
      }
    });
  });

  // Apri rami attivi (se WP aggiunge classi current-cat ecc.)
  document.querySelectorAll('.product-categories a.current-cat, .product-categories a.current-cat-parent, .product-categories a.current-cat-ancestor').forEach(function (activeLink) {
    let li = activeLink.closest('li');
    while (li) {
      if (li.classList.contains('has-children')) {
        li.classList.add('open');
        const toggle = li.querySelector('a.toggle-link');
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
        const container = li.querySelector('.submenu-container');
        if (container) container.style.display = 'block';
      }
      li = li.parentElement.closest('li');
    }
  });
});

// Add to cart input qty

document.addEventListener('click', function(e) {
  if (e.target.closest('.qty-btn')) {
    const button = e.target.closest('.qty-btn');
    const input = button.closest('.quantity-wrapper').querySelector('.qty');
    let value = parseInt(input.value) || 1;
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 999;

    if (button.classList.contains('qty-plus') && value < max) {
      input.value = value + 1;
    }
    if (button.classList.contains('qty-minus') && value > min) {
      input.value = value - 1;
    }

    input.dispatchEvent(new Event('change', { bubbles: true }));
  }
});


document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".tab-list li");
  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      tabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");
    });
  });
});




/*  
  Gsap Animation
----------------------------------------------------------------------- */

/* Elements Animation */
let tl = gsap.timeline();

tl.to(".fade-in", {opacity:1, y:0, duration:0.4, stagger: 0.3, ease:Power4.easeOut}, "0.5");

tl.to(".text-reveal",{clipPath:"polygon(0 0, 100% 0, 100% 100%, 0 100%)", y:0, duration:0.8, stagger: 0.3}, 0.7);


/* Scroll Animation */
ScrollTrigger.batch(".fade-in-scroll",{
  start:"top 60%",
  onEnter: (elements, triggers) => {
    gsap.to(elements, {opacity: 1, y:0, stagger: 0.3, duration:0.8, ease:Power2.easeOut});
  }

});




//Splide Js
var splide = new Splide( '.splide', {
  type   : 'loop',
  perPage: 5,
  perMove: 1,
  gap:'15px',
  pagination: false,
  breakpoints: {
    768: { 
      perPage: 1, 
    },
    1024: { 
      perPage: 3,
    },
  },});

splide.mount();



