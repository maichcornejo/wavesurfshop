/* ===============================================
  OPEN CLOSE Menu
============================================= */

function shoes_store_elementor_open_menu() {
  jQuery('button.menu-toggle').addClass('close-panal');
  setTimeout(function(){
    jQuery('nav#main-menu').show();
  }, 100);

  return false;
}

jQuery( "button.menu-toggle").on("click", shoes_store_elementor_open_menu);

function shoes_store_elementor_close_menu() {
  jQuery('button.close-menu').removeClass('close-panal');
  jQuery('nav#main-menu').hide();
}

jQuery( "button.close-menu").on("click", shoes_store_elementor_close_menu);

/* ===============================================
  TRAP TAB FOCUS ON MODAL MENU
============================================= */

jQuery('button.close-menu').on('keydown', function (e) {

  if (jQuery("this:focus") && !!e.shiftKey && e.keyCode === 9) {
  } else if (jQuery("this:focus") && (e.which === 9)) {
    e.preventDefault();
    jQuery(this).blur();
    jQuery('.nav-menu li a:first').focus()
  }
});

jQuery('.nav-menu li a:first').on('keydown', function (event) {
  if (jQuery("this:focus") && !!event.shiftKey && event.keyCode === 9) {
    event.preventDefault();
    jQuery(this).blur();
    jQuery('button.close-menu').focus()
  }
});

jQuery(document).ready(function() {
window.addEventListener('load', (event) => {
    jQuery(".loader").delay(2000).fadeOut("slow");
  });
})

/* ===============================================
  Scroll Top //
============================================= */

jQuery(window).scroll(function () {
  if (jQuery(this).scrollTop() > 100) {
      jQuery('.scroll-up').fadeIn();
  } else {
      jQuery('.scroll-up').fadeOut();
  }
});

jQuery('a[href="#tobottom"]').click(function () {
  jQuery('html, body').animate({scrollTop: 0}, 'slow');
  return false;
});
(function( $ ) {
$(window).scroll(function(){
    var sticky = $('.sticky-header'),
    scroll = $(window).scrollTop();

    if (scroll >= 100) sticky.addClass('fixed-header');
    else sticky.removeClass('fixed-header');
  });
})( jQuery );

 /* ===============================================
  Custom Cursor
============================================= */

const shoes_store_elementor_customCursor = {
  init: function () {
    this.shoes_store_elementor_customCursor();
  },
  isVariableDefined: function (el) {
    return typeof el !== "undefined" && el !== null;
  },
  select: function (selectors) {
    return document.querySelector(selectors);
  },
  selectAll: function (selectors) {
    return document.querySelectorAll(selectors);
  },
  shoes_store_elementor_customCursor: function () {
    const shoes_store_elementor_cursorDot = this.select(".cursor-point");
    const shoes_store_elementor_cursorOutline = this.select(".cursor-point-outline");
    if (this.isVariableDefined(shoes_store_elementor_cursorDot) && this.isVariableDefined(shoes_store_elementor_cursorOutline)) {
      const cursor = {
        delay: 8,
        _x: 0,
        _y: 0,
        endX: window.innerWidth / 2,
        endY: window.innerHeight / 2,
        cursorVisible: true,
        cursorEnlarged: false,
        $dot: shoes_store_elementor_cursorDot,
        $outline: shoes_store_elementor_cursorOutline,

        init: function () {
          this.dotSize = this.$dot.offsetWidth;
          this.outlineSize = this.$outline.offsetWidth;
          this.setupEventListeners();
          this.animateDotOutline();
        },

        updateCursor: function (e) {
          this.cursorVisible = true;
          this.toggleCursorVisibility();
          this.endX = e.clientX;
          this.endY = e.clientY;
          this.$dot.style.top = `${this.endY}px`;
          this.$dot.style.left = `${this.endX}px`;
        },

        setupEventListeners: function () {
          window.addEventListener("load", () => {
            this.cursorEnlarged = false;
            this.toggleCursorSize();
          });

          shoes_store_elementor_customCursor.selectAll("a, button").forEach((el) => {
            el.addEventListener("mouseover", () => {
              this.cursorEnlarged = true;
              this.toggleCursorSize();
            });
            el.addEventListener("mouseout", () => {
              this.cursorEnlarged = false;
              this.toggleCursorSize();
            });
          });

          document.addEventListener("mousedown", () => {
            this.cursorEnlarged = true;
            this.toggleCursorSize();
          });
          document.addEventListener("mouseup", () => {
            this.cursorEnlarged = false;
            this.toggleCursorSize();
          });

          document.addEventListener("mousemove", (e) => {
            this.updateCursor(e);
          });

          document.addEventListener("mouseenter", () => {
            this.cursorVisible = true;
            this.toggleCursorVisibility();
            this.$dot.style.opacity = 1;
            this.$outline.style.opacity = 1;
          });

          document.addEventListener("mouseleave", () => {
            this.cursorVisible = false;
            this.toggleCursorVisibility();
            this.$dot.style.opacity = 0;
            this.$outline.style.opacity = 0;
          });
        },

        animateDotOutline: function () {
          this._x += (this.endX - this._x) / this.delay;
          this._y += (this.endY - this._y) / this.delay;
          this.$outline.style.top = `${this._y}px`;
          this.$outline.style.left = `${this._x}px`;

          requestAnimationFrame(this.animateDotOutline.bind(this));
        },

        toggleCursorSize: function () {
          if (this.cursorEnlarged) {
            this.$dot.style.transform = "translate(-50%, -50%) scale(0.75)";
            this.$outline.style.transform = "translate(-50%, -50%) scale(1.6)";
          } else {
            this.$dot.style.transform = "translate(-50%, -50%) scale(1)";
            this.$outline.style.transform = "translate(-50%, -50%) scale(1)";
          }
        },

        toggleCursorVisibility: function () {
          if (this.cursorVisible) {
            this.$dot.style.opacity = 1;
            this.$outline.style.opacity = 1;
          } else {
            this.$dot.style.opacity = 0;
            this.$outline.style.opacity = 0;
          }
        },
      };
      cursor.init();
    }
  },
};
shoes_store_elementor_customCursor.init(); 

/* ===============================================
  Progress Bar
============================================= */
const shoes_store_elementor_progressBar = {
  init: function () {
      let shoes_store_elementor_progressBarDiv = document.getElementById("elemento-progress-bar");

      if (shoes_store_elementor_progressBarDiv) {
          let shoes_store_elementor_body = document.body;
          let shoes_store_elementor_rootElement = document.documentElement;

          window.addEventListener("scroll", function (event) {
              let shoes_store_elementor_winScroll = shoes_store_elementor_body.scrollTop || shoes_store_elementor_rootElement.scrollTop;
              let shoes_store_elementor_height =
              shoes_store_elementor_rootElement.scrollHeight - shoes_store_elementor_rootElement.clientHeight;
              let shoes_store_elementor_scrolled = (shoes_store_elementor_winScroll / shoes_store_elementor_height) * 100;
              shoes_store_elementor_progressBarDiv.style.width = shoes_store_elementor_scrolled + "%";
          });
      }
  },
};
shoes_store_elementor_progressBar.init();

/* ===============================================
   sticky copyright
============================================= */

window.addEventListener('scroll', function() {
  var shoes_store_elementor_footer = document.querySelector('.sticky-copyright');
  if (!shoes_store_elementor_footer) return; 

  var shoes_store_elementor_scrollTop = window.scrollY || document.documentElement.shoes_store_elementor_scrollTop;

  if (shoes_store_elementor_scrollTop >= 100) {
    shoes_store_elementor_footer.classList.add('active-sticky');
  }
});

/* ===============================================
   sticky sidebar
============================================= */

window.addEventListener('scroll', function () {
  var shoes_store_elementor_sidebar = document.querySelector('.sidebar-sticky');
  if (!shoes_store_elementor_sidebar) return;

  var shoes_store_elementor_scrollTop = window.scrollY || document.documentElement.scrollTop;
  var shoes_store_elementor_windowHeight = window.innerHeight;
  var shoes_store_elementor_documentHeight = document.documentElement.scrollHeight;

  var shoes_store_elementor_isBottom = shoes_store_elementor_scrollTop + shoes_store_elementor_windowHeight >= shoes_store_elementor_documentHeight - 100;

  if (shoes_store_elementor_scrollTop >= 100 && !shoes_store_elementor_isBottom) {
    shoes_store_elementor_sidebar.classList.add('sidebar-fixed');
  } else {
    shoes_store_elementor_sidebar.classList.remove('sidebar-fixed');
  }
});