/*-----------------------------------------------------------------------------------

  Theme Name: Wotech - IT Service HTML Template
  Author: wotechtheme
  Support: https://wotechtheme.com/contact-us/
  Description: Wotech - IT Service HTML Template
  Version: 1.0

  ── PATCHED FOR LIVEWIRE wire:navigate ──────────────────────────────────────────
  This file only loads / executes ONCE per browser session when using
  wire:navigate (Livewire keeps <script> tags that don't change between pages,
  it does not re-run them). That means anything that used to run on
  $(window).on('load', ...) or top-level on first parse would only ever apply
  to the very first page you land on — every page you reach afterwards via
  wire:navigate would show un-initialized sliders, un-applied data-background
  images, and dead-looking click targets.

  Fix: everything that touches page content (Swiper, WOW, data-background,
  meanmenu, magnificPopup, counterUp, isotope filter, countdown, preloader,
  before/after) now lives inside initPageJS() and is re-run on Livewire's
  'livewire:navigated' event — which fires on the very first page load AND on
  every subsequent wire:navigate visit, so this is a full drop-in replacement
  for the old load/DOMContentLoaded triggers.

  Anything bound to window/document itself (header dropdown toggles, sticky
  header on scroll, back-to-top progress on scroll, delegated icon-box hover)
  is registered exactly once via bindGlobalEventsOnce(), guarded by a flag, so
  repeated navigations never stack up duplicate listeners.
-----------------------------------------------------------------------------------*/

/************ TABLE OF CONTENTS ***************

1. Preloader activation
2. Button hover
3. Mobile Menu Js
4. Sidebar Toggle
5. Body overlay Js
6. Search Header Js
7. Sticky Header Js
8. Data Css js
9. Cart Quantity Js
10. MagnificPopup image view
11. MagnificPopup video view
12. Counter Js
13. Wow Js
14. Back To Top Js
15. For language Js
16. For header Js
17. For header setting Js
18. For before-after Js
19. Testimonial slider Js
20. Review slider Js
21. Product slider Js
22. Brand slider Js
23. Service slider Js
24. Project slider Js
25. Service slider One Js
26. Slider Js
27. Postbox slider Js
28. whyChoose slider Js
29. Section Active
30. Countdown slider Js

**********************************************/

(function ($) {
	"use strict";

	var globalEventsBound = false;   // guards one-time window/document listeners
	var countdownTimerId  = null;    // guards against stacking setInterval on every nav

	/* =====================================================================
	   Runs on EVERY page view (first load + every wire:navigate visit)
	   ===================================================================== */
	function initPageJS() {

		/*======================================
		Preloader activation
		========================================*/
		if ($('#preloader').length) {
			$('#preloader').stop(true, true).delay(300).fadeOut(500);
		}

		/*======================================
		button hover
		========================================*/
		$('.btn-hover').off('mouseenter.wotech mouseout.wotech').on('mouseenter.wotech', function (e) {
			var parentOffset = $(this).offset(),
				relX = e.pageX - parentOffset.left,
				relY = e.pageY - parentOffset.top;
			$(this).find('span').css({ top: 0, left: 0 });
			$(this).find('span').css({ top: relY, left: relX });
		}).on('mouseout.wotech', function (e) {
			var parentOffset = $(this).offset(),
				relX = e.pageX - parentOffset.left,
				relY = e.pageY - parentOffset.top;
			$(this).find('span').css({ top: 0, left: 0 });
			$(this).find('span').css({ top: relY, left: relX });
		});

		/*======================================
		Mobile Menu Js
		========================================*/
		if ($('#mobile-menu').length) {
			$('#mobile-menu').meanmenu({
				meanMenuContainer: '.mobile-menu',
				meanScreenWidth: "991",
				meanExpand: ['<i class="fal fa-plus"></i>'],
			});
		}
		if ($("#mobile-menu-2").length) {
			$("#mobile-menu-2").meanmenu({
				meanMenuContainer: ".mobile-menu-2",
				meanScreenWidth: "4000",
				meanExpand: ['<i class="fal fa-plus"></i>'],
			});
		}

		/*======================================
		Sidebar Toggle / Body overlay / Search Header
		(bound directly to elements that exist fresh each nav — safe to rebind)
		========================================*/
		$(".offcanvas__close,.offcanvas__overlay").off('click.wotech').on("click.wotech", function () {
			$(".offcanvas__info").removeClass("info-open");
			$(".offcanvas__overlay").removeClass("overlay-open");
		});
		$(".sidebar__toggle").off('click.wotech').on("click.wotech", function () {
			$(".offcanvas__info").addClass("info-open");
			$(".offcanvas__overlay").addClass("overlay-open");
		});
		$(".body-overlay").off('click.wotech').on("click.wotech", function () {
			$(".offcanvas__area").removeClass("offcanvas-opened");
			$(".df-search-area").removeClass("opened");
			$(".body-overlay").removeClass("opened");
		});
		$(".search-toggle-open").off('click.wotech').on("click.wotech", function () {
			$(".df-search-area").addClass("opened");
			$(".body-overlay").addClass("opened");
		});
		$(".tp-search-close-btn").off('click.wotech').on("click.wotech", function () {
			$(".df-search-area").removeClass("opened");
			$(".body-overlay").removeClass("opened");
		});

		/*======================================
		Data Css js — this is what paints banner/section background images.
		Must re-run every navigation or new pages show blank/placeholder bg.
		========================================*/
		$("[data-background]").each(function () {
			$(this).css("background-image", "url(" + $(this).attr("data-background") + ")");
		});
		$("[data-width]").each(function () {
			$(this).css("width", $(this).attr("data-width"));
		});
		$("[data-bg-color]").each(function () {
			$(this).css("background-color", $(this).attr("data-bg-color"));
		});

		/*======================================
		Cart Quantity Js
		========================================*/
		$(".cart-minus").off('click.wotech').on('click.wotech', function () {
			var $input = $(this).parent().find("input");
			var count = parseInt($input.val()) - 1;
			count = count < 1 ? 1 : count;
			$input.val(count);
			$input.change();
			return false;
		});
		$(".cart-plus").off('click.wotech').on('click.wotech', function () {
			var $input = $(this).parent().find("input");
			$input.val(parseInt($input.val()) + 1);
			$input.change();
			return false;
		});

		/*======================================
		MagnificPopup image / video view
		========================================*/
		if ($('.popup-image').length) {
			$('.popup-image').magnificPopup({
				type: 'image',
				gallery: { enabled: true }
			});
		}
		if ($(".popup-video").length) {
			$(".popup-video").magnificPopup({ type: "iframe" });
		}

		/*======================================
		Isotope post filter
		========================================*/
		if ($(".post-filter").length) {
			var postFilterList = $(".post-filter li");
			$(".filter-layout").isotope({
				filter: ".filter-item",
				animationOptions: { duration: 500, easing: "linear", queue: false }
			});
			postFilterList.off('click.wotech').on("click.wotech", function () {
				var Self = $(this);
				var selector = Self.attr("data-filter");
				postFilterList.removeClass("active");
				Self.addClass("active");
				$(".filter-layout").isotope({
					filter: selector,
					animationOptions: { duration: 500, easing: "linear", queue: false }
				});
				return false;
			});
		}

		/*======================================
		Counter Js
		========================================*/
		if ($(".counter").length) {
			$(".counter").counterUp({ delay: 10, time: 1000 });
		}

		/*======================================
		Wow Js
		========================================*/
		if (typeof WOW !== 'undefined') {
			new WOW().init();
		}

		/*======================================
		Back To Top Js — style setup for the fresh SVG path on this page.
		Scroll-driven progress update itself is bound once in bindGlobalEventsOnce().
		========================================*/
		var progressPath = document.querySelector('.backtotop-wrap path');
		if (progressPath) {
			var pathLength = progressPath.getTotalLength();
			progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
			progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
			progressPath.style.strokeDashoffset = pathLength;
			progressPath.getBoundingClientRect();
			progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
		}
		if ($('.backtotop-wrap').length) {
			$('.backtotop-wrap').off('click.wotech').on('click.wotech', function (event) {
				event.preventDefault();
				$('html, body').animate({ scrollTop: 0 }, 550);
				return false;
			});
		}

		/*======================================
		For before-after Js
		========================================*/
		if ($(".beforeAfter").length && typeof $.fn.beforeAfter === 'function') {
			$('.beforeAfter').beforeAfter({
				movable: true,
				clickMove: true,
				position: 50,
				separatorColor: '#fafafa',
				bulletColor: '#fafafa',
			});
		}

		/*======================================
		Section Active (delegated but scoped to this fn's own namespace so
		re-binding just replaces the same single handler, not duplicates)
		========================================*/
		$(document).off('mouseover.wotechIconBox').on('mouseover.wotechIconBox', '.icon-box-area', function () {
			$('.icon-box-area').removeClass('active');
			$(this).addClass('active');
		});

		/*======================================
		Countdown Js — clear any previous interval before starting a new one
		========================================*/
		if (countdownTimerId) {
			clearInterval(countdownTimerId);
			countdownTimerId = null;
		}
		if ($(".countdown-wrapper").length) {
			(function updateCountdown() {
				var second = 1000, minute = second * 60, hour = minute * 60, day = hour * 24;
				var today = new Date();
				var dd = String(today.getDate()).padStart(2, "0");
				var mm = String(today.getMonth() + 1).padStart(2, "0");
				var yyyy = today.getFullYear();
				var nextYear = yyyy + 1;
				var dayMonth = "12/30/";
				var birthday = dayMonth + yyyy;
				today = mm + "/" + dd + "/" + yyyy;
				if (today > birthday) birthday = dayMonth + nextYear;
				var countDownDate = new Date(birthday).getTime();

				countdownTimerId = setInterval(function () {
					var now = new Date().getTime();
					var distance = countDownDate - now;
					var days = Math.floor(distance / day);
					var hours = Math.floor((distance % day) / hour);
					var minutes = Math.floor((distance % hour) / minute);
					var seconds = Math.floor((distance % minute) / second);
					var elDays = document.getElementById("days");
					var elHours = document.getElementById("hours");
					var elMinutes = document.getElementById("minutes");
					var elSeconds = document.getElementById("seconds");
					if (elDays) elDays.innerText = days;
					if (elHours) elHours.innerText = hours;
					if (elMinutes) elMinutes.innerText = minutes;
					if (elSeconds) elSeconds.innerText = seconds;
					if (distance < 0) {
						clearInterval(countdownTimerId);
						var headline = document.getElementById("headline");
						var wrap = document.getElementById("countdown");
						if (headline) headline.innerText = "It's my birthday!";
						if (wrap) wrap.style.display = "none";
					}
				}, 1000);
			})();
		}

		/* =====================================================
		   All Swiper sliders — guarded by element existence so we
		   only spend cycles on sliders actually present on this page.
		   ===================================================== */

		if ($('.testimonial-active-1').length) {
			new Swiper('.testimonial-active-1', {
				slidesPerView: 1, spaceBetween: 30, loop: true, roundLengths: true,
				autoplay: { delay: 3000 },
				navigation: { nextEl: ".testimonial-1-button-next", prevEl: ".testimonial-1-button-prev" },
				breakpoints: { '1400': { slidesPerView: 1 }, '1200': { slidesPerView: 1 }, '992': { slidesPerView: 1 }, '768': { slidesPerView: 1 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
			});
		}

		if ($('.testimonial-active-2').length) {
			new Swiper('.testimonial-active-2', {
				slidesPerView: 2, spaceBetween: 20, loop: true, roundLengths: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".testimonial-swiper-dot", clickable: true },
				navigation: { nextEl: ".testimonial-button-next", prevEl: ".testimonial-button-prev" },
				breakpoints: { '1400': { slidesPerView: 2 }, '1200': { slidesPerView: 2 }, '992': { slidesPerView: 2 }, '768': { slidesPerView: 1 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
			});
		}

		if ($('.testimonial-active-3').length) {
			new Swiper('.testimonial-active-3', {
				slidesPerView: 4, spaceBetween: 24, loop: true, roundLengths: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".bd-swiper-dot", clickable: true },
				navigation: { nextEl: ".testimonial-button-next", prevEl: ".testimonial-button-prev" },
				breakpoints: { '1400': { slidesPerView: 4 }, '1200': { slidesPerView: 3 }, '992': { slidesPerView: 2 }, '768': { slidesPerView: 2 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
			});
		}

		if ($('.team-active-3').length) {
			new Swiper('.team-active-3', {
				slidesPerView: 3, spaceBetween: 24, loop: true, roundLengths: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".bd-swiper-dot", clickable: true },
				navigation: { nextEl: ".team-3-button-next", prevEl: ".team-3-button-prev" },
				breakpoints: { '1400': { slidesPerView: 3 }, '1200': { slidesPerView: 3 }, '992': { slidesPerView: 2 }, '768': { slidesPerView: 2 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
				preventClicks: false,
				preventClicksPropagation: false,
			});
		}

		if ($('.review-active').length) {
			new Swiper('.review-active', {
				slidesPerView: 1, spaceBetween: 30, loop: true, roundLengths: true,
				observer: true, observeParents: true,
				autoplay: { delay: 3000 },
				navigation: { nextEl: ".review-button-prev", prevEl: ".review-button-next" },
			});
		}

		if ($(".review-active-two").length && typeof $.fn.slick === 'function') {
			$(".review-active-two").slick({
				infinite: true, vertical: true, speed: 1000, autoplaySpeed: 2000,
				slidesToShow: 1, autoplay: true, arrows: true,
				prevArrow: '<button type="button" class="slick-prev"><i class="fa-regular fa-chevron-left"></i></button>',
				nextArrow: '<button type="button" class="slick-next"><i class="fa-regular fa-chevron-right"></i></button>',
				appendArrows: $(".review-slider-navigation"),
				slidesToScroll: 1,
				responsive: [
					{ breakpoint: 1400, slidesToShow: 1 },
					{ breakpoint: 1200, slidesToShow: 1 },
					{ breakpoint: 992, slidesToShow: 1 },
					{ breakpoint: 768, settings: { slidesToShow: 1 } },
					{ breakpoint: 480, settings: { centerMode: false, slidesToShow: 1 } },
				],
			});
		}

		if ($('.product-active').length) {
			new Swiper('.product-active', {
				slidesPerView: 4, spaceBetween: 15, loop: true, roundLengths: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".bd-swiper-dot", clickable: true },
				breakpoints: { '1200': { slidesPerView: 4 }, '992': { slidesPerView: 3 }, '768': { slidesPerView: 3 }, '576': { slidesPerView: 2 }, '0': { slidesPerView: 1 } },
			});
		}

		if ($('.discount-active').length) {
			new Swiper('.discount-active', {
				slidesPerView: 5, spaceBetween: 15, loop: true, roundLengths: true,
				observer: true, observeParents: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".bd-swiper-dot", clickable: true },
				navigation: { nextEl: ".discount-slider-button-prev", prevEl: ".discount-slider-button-next" },
				breakpoints: { '1400': { slidesPerView: 5 }, '1200': { slidesPerView: 4 }, '992': { slidesPerView: 4 }, '768': { slidesPerView: 3 }, '576': { slidesPerView: 2 }, '0': { slidesPerView: 1 } },
			});
		}

		if ($('.brand-active').length) {
			new Swiper('.brand-active', {
				slidesPerView: 5, spaceBetween: 99, loop: true, roundLengths: true,
				autoplay: { delay: 3000 }, speed: 1000,
				breakpoints: { '1400': { slidesPerView: 5 }, '1200': { slidesPerView: 4 }, '992': { slidesPerView: 4 }, '768': { slidesPerView: 4 }, '576': { slidesPerView: 3 }, '400': { slidesPerView: 2 }, '0': { slidesPerView: 1 } },
			});
		}

		if ($('.service-active-2').length) {
			new Swiper('.service-active-2', {
				slidesPerView: 4, spaceBetween: 30, loop: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".bd-swiper-dot", clickable: true },
				navigation: { nextEl: ".service-active-2-button-next", prevEl: ".service-active-2-button-prev" },
				breakpoints: { '1200': { slidesPerView: 4 }, '992': { slidesPerView: 3 }, '768': { slidesPerView: 2 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
				preventClicks: false,
				preventClicksPropagation: false,
			});
		}

		// Project slider (home page) — links inside slides now navigate correctly
		if ($('.project-active-1').length) {
			new Swiper('.project-active-1', {
				slidesPerView: 4, spaceBetween: 30, loop: true,
				autoplay: { delay: 3000 },
				navigation: { nextEl: ".project-1-button-next", prevEl: ".project-1-button-prev" },
				breakpoints: { '1400': { slidesPerView: 4 }, '1200': { slidesPerView: 3 }, '992': { slidesPerView: 2 }, '768': { slidesPerView: 2 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
				speed: 1000,
				// Swiper's default click-prevention blocks link clicks on desktop after
				// any tiny pointer wobble (which a normal mouse click almost always has).
				// Disabling it lets <a> tags inside slides navigate normally.
				preventClicks: false,
				preventClicksPropagation: false,
			});
		}

		// Service slider (home page) — links inside slides now navigate correctly
		if ($('.service-active-1').length) {
			new Swiper('.service-active-1', {
				slidesPerView: 3, spaceBetween: 30, loop: true, roundLengths: true,
				autoplay: { delay: 3000 },
				navigation: { nextEl: ".service-1-button-next", prevEl: ".service-1-button-prev" },
				breakpoints: { '1400': { slidesPerView: 3 }, '1200': { slidesPerView: 3 }, '992': { slidesPerView: 2 }, '768': { slidesPerView: 1 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
				speed: 1500,
				preventClicks: false,
				preventClicksPropagation: false,
			});
		}

		/*======================================
		slider Js — main "slider-active" carousel + animated data-animation els
		========================================*/
		function wireAnimatedSwiper(selector, init) {
			var animated = function () {
				$(selector + " [data-animation]").each(function () {
					var anim = $(this).data("animation");
					var delay = $(this).data("delay");
					var duration = $(this).data("duration");
					$(this)
						.removeClass("anim" + anim)
						.addClass(anim + " animated")
						.css({
							webkitAnimationDelay: delay,
							animationDelay: delay,
							webkitAnimationDuration: duration,
							animationDuration: duration,
						})
						.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
							$(this).removeClass(anim + " animated");
						});
				});
			};
			animated();
			init.on("slideChange", function () {
				$(selector + " [data-animation]").removeClass("animated");
			});
			init.on("slideChange", animated);
		}

		if ($(".slider-active").length) {
			var sliderActiveSel = ".slider-active";
			var sliderInit = new Swiper(sliderActiveSel, {
				slidesPerView: 1, slidesPerColumn: 1, paginationClickable: true,
				fadeEffect: { crossFade: true }, loop: true, effect: 'fade',
				autoplay: { delay: 5000 },
				navigation: { nextEl: ".slider-button-prev", prevEl: ".slider-button-next" },
				pagination: { el: ".banner-dot-2", clickable: true },
				a11y: false,
			});
			wireAnimatedSwiper(sliderActiveSel, sliderInit);
		}

		// Home page hero banner — this is the one behind your gray/placeholder bug
		if ($(".banner-active").length) {
			var bannerActiveSel = ".banner-active";
			var bannerInit = new Swiper(bannerActiveSel, {
				slidesPerView: 1, slidesPerColumn: 1, paginationClickable: true,
				fadeEffect: { crossFade: true }, loop: true, effect: "fade",
				autoplay: { delay: 5000 },
				navigation: { nextEl: ".slider__button-prev", prevEl: ".slider__button-next" },
				pagination: { el: ".banner-dot", clickable: true },
				a11y: false,
			});
			wireAnimatedSwiper(bannerActiveSel, bannerInit);
		}

		if ($('.postbox__slider').length) {
			new Swiper('.postbox__slider', {
				slidesPerView: 1, spaceBetween: 0, loop: true,
				autoplay: { delay: 3000 },
				navigation: { nextEl: ".postbox-slider-button-next", prevEl: ".postbox-slider-button-prev" },
				breakpoints: { '1200': { slidesPerView: 1 }, '992': { slidesPerView: 1 }, '768': { slidesPerView: 1 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
				preventClicks: false,
				preventClicksPropagation: false,
			});
		}

		if ($('.why-choose-active').length) {
			new Swiper('.why-choose-active', {
				slidesPerView: 1, spaceBetween: 0, loop: true,
				autoplay: { delay: 3000 },
				pagination: { el: ".bd-swiper-dot", clickable: true },
				breakpoints: { '1200': { slidesPerView: 3 }, '992': { slidesPerView: 3 }, '768': { slidesPerView: 1 }, '576': { slidesPerView: 1 }, '0': { slidesPerView: 1 } },
			});
		}
	}

	/* =====================================================================
	   Runs exactly ONCE — bound to window/document, which persist across
	   wire:navigate visits, so these must never be re-registered.
	   ===================================================================== */
	function bindGlobalEventsOnce() {
		if (globalEventsBound) return;
		globalEventsBound = true;

		/*======================================
		Sticky Header Js — looks up #header-sticky fresh every tick,
		so it stays correct even after the header markup is replaced.
		========================================*/
		$(window).on('scroll', function () {
			if ($(this).scrollTop() > 250) {
				$("#header-sticky").addClass("sticky");
			} else {
				$("#header-sticky").removeClass("sticky");
			}
		});

		/*======================================
		Back To Top Js — progress bar + show/hide on scroll.
		Looks up the SVG path fresh every tick.
		========================================*/
		$(window).on('scroll', function () {
			var progressPath = document.querySelector('.backtotop-wrap path');
			if (!progressPath) return;
			var pathLength = progressPath.getTotalLength();
			var scroll = $(window).scrollTop();
			var height = $(document).height() - $(window).height();
			var progress = pathLength - (scroll * pathLength / (height || 1));
			progressPath.style.strokeDashoffset = progress;
		});
		$(window).on('scroll', function () {
			if ($(this).scrollTop() > 150) {
				$('.backtotop-wrap').addClass('active-progress');
			} else {
				$('.backtotop-wrap').removeClass('active-progress');
			}
		});

		/*======================================
		For language / currency / setting dropdowns.
		These already look up their target element fresh inside the
		handler, so binding once to window is all that's needed —
		binding repeatedly (the old bug) caused the "click twice to
		toggle" / seemingly unresponsive dropdown behaviour.
		========================================*/
		window.addEventListener('click', function (e) {
			var el = document.getElementById('header-lang-toggle');
			if (el && el.contains(e.target)) {
				$(".header-lang ul").toggleClass("lang-list-open");
			} else {
				$(".header-lang ul").removeClass("lang-list-open");
			}
		});

		window.addEventListener('click', function (e) {
			var el = document.getElementById('header-currency-toggle');
			if (el && el.contains(e.target)) {
				$(".tp-header-currency ul").toggleClass("tp-currency-list-open");
			} else {
				$(".tp-header-currency ul").removeClass("tp-currency-list-open");
			}
		});

		window.addEventListener('click', function (e) {
			var el = document.getElementById('header-setting-toggle');
			if (el && el.contains(e.target)) {
				$(".tp-header-setting ul").toggleClass("tp-setting-list-open");
			} else {
				$(".tp-header-setting ul").removeClass("tp-setting-list-open");
			}
		});

		/*======================================
		Force-navigate links that live inside Swiper sliders.

		Swiper attaches its own click-guard directly on the slider
		container to swallow clicks it thinks followed a drag (very
		common false-positive on desktop mouse clicks). That handler
		runs during the same bubble phase Livewire's own wire:navigate
		listener relies on, and can call stopPropagation/preventDefault
		before Livewire ever sees the click — so nothing happens on a
		normal left-click, even though the href itself is fine (which
		is why "open in new tab" via right-click still works).

		Registering this listener on `document` with { capture: true }
		makes it run in the CAPTURING phase, top-down, before the event
		ever reaches the slider container or the link itself — so it
		always wins the race against Swiper's guard, no matter what
		Swiper options are (or aren't) set. We only special-case links
		that are actually inside a `.swiper` container; every other
		link on the site is left to Livewire's normal handling.
		========================================*/
		document.addEventListener('click', function (e) {
			var link = e.target.closest('.swiper a[href]');
			if (!link) return;

			// Let modified clicks (ctrl/cmd/middle-click/new-tab) behave normally.
			if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
				return;
			}
			// Respect target="_blank" and non-navigate links as normal browser links.
			if (link.target && link.target !== '_self') return;

			var href = link.getAttribute('href');
			if (!href || href.charAt(0) === '#') return;

			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();

			if (window.Livewire && typeof window.Livewire.navigate === 'function') {
				window.Livewire.navigate(href);
			} else {
				window.location.href = href;
			}
		}, true); // capture: true — run before Swiper's own bubble-phase click-guard
	}

	/* =====================================================================
	   Entry point.
	   'livewire:navigated' fires on the very first page load AND on every
	   subsequent wire:navigate visit — this single listener replaces every
	   old $(window).on('load', ...) / top-level call in this file.
	   ===================================================================== */
	bindGlobalEventsOnce();
	document.addEventListener('livewire:navigated', initPageJS);

	// Fallback: if this script somehow loads on a plain non-Livewire page
	// (or before Livewire's navigate plugin attaches), still run once.
	if (!window.livewireNavigateAttachedFallbackRan) {
		window.livewireNavigateAttachedFallbackRan = true;
		$(function () { initPageJS(); });
	}

})(jQuery);