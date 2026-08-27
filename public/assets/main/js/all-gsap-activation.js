// menu Scrool
const menuItems = document.querySelectorAll('.menu-item');
gsap.registerPlugin(ScrollTrigger);
function updateActiveState() {
  const scrollPosition = window.scrollY;
  menuItems.forEach(item => {
    const targetId = item.getAttribute('data-target');
    const targetSection = document.getElementById(targetId);
    if (targetSection && targetSection.offsetTop <= scrollPosition && targetSection.offsetTop + targetSection.offsetHeight > scrollPosition) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
}
window.addEventListener('scroll', updateActiveState);
menuItems.forEach(item => {
  item.addEventListener('click', function (event) {
    event.preventDefault();
    const targetId = this.getAttribute('data-target');
    const targetSection = document.getElementById(targetId);
    gsap.to(window, { duration: 1, scrollTo: targetSection });
  });
});



// Screen Width
var screen_width = window.screen.width;

// Active GSAP
if (document.querySelector("#smooth-animate").classList.contains("smooth-scrool-animate")) {
    const smoother = ScrollSmoother.create({
        effects: screen_width < 1025 ? false : true,
        smooth: 1.35,
        ignoreMobileResize: true,
        normalizeScroll: false,
        smoothTouch: 0.1,
    });
}

// scroll-button

const heroBtn = document.querySelector('.hero-btn');

if (heroBtn) {
    const scrollButton = document.getElementById('scroll-button');
    scrollButton.addEventListener('click', () => {
        gsap.to(window, { duration: .2, scrollTo: "#case" });
    });

}


// features hoverImg animation
const items = document.querySelectorAll('.features-item')

items.forEach((el) => {
    const image = el.querySelector('.features-masking')

    el.addEventListener('mouseenter', (e) => {
        gsap.to(image, { autoAlpha: 1 })
    })

    el.addEventListener('mouseleave', (e) => {
        gsap.to(image, { autoAlpha: 0 })
    })

    el.addEventListener('mousemove', (e) => {
        gsap.set(image, { x: e.offsetX - 200 })
    })
})

// simpleParallax activation

var image = document.getElementsByClassName('imageParallax');
new simpleParallax(image, {
    delay: .6,
    transition: 'cubic-bezier(0,0,0,1)'
});


var image = document.getElementsByClassName('imageParallax2');
new simpleParallax(image, {
    delay: .6,
    transition: 'cubic-bezier(0,0,0,1)',
    orientation: 'right'
});
var image = document.getElementsByClassName('imageParallax3');
new simpleParallax(image, {
    delay: .6,
    transition: 'cubic-bezier(0,0,0,1)',
    orientation: 'left'
});