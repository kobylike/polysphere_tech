var W3Crm = function(){
	"use strict"

	/* Search Bar ============ */
	var screenWidth = $( window ).width();
	var screenHeight = $( window ).height();

	var handleSelectPicker = function(){
		if(jQuery('.default-select,.table-responsive select').length > 0 ){
			jQuery('.default-select,.table-responsive select').selectpicker();
		}
	}

	var handlePreloader = function(){
		setTimeout(function() {
			jQuery('#preloader').remove();
			$('#main-wrapper').addClass('show');
		}, 300);
	}

	var handleImageSelect = function(){
		const $_SELECT_PICKER = $('.image-select');
		$_SELECT_PICKER.find('option').each((idx, elem) => {
			const $OPTION = $(elem);
			const IMAGE_URL = $OPTION.attr('data-thumbnail');
			if (IMAGE_URL) {
				$OPTION.attr('data-content', "<img src='%i'/> %s".replace(/%i/, IMAGE_URL).replace(/%s/, $OPTION.text()))
			}
		});
		$_SELECT_PICKER.selectpicker();
	}

	/*
	#menu is @persist('sidebar')'d — same DOM node across every Livewire
	navigation. metisMenu only ever needs to be initialized ONCE; the
	`mm-initialized` guard makes this safe to call again without disposing
	(disposing + reiniting on every navigation was the earlier bug that
	made dropdowns flash open then collapse).
	*/
	var handleMetisMenu = function() {
		var $menu = jQuery('#menu');
		if ($menu.length > 0 && !$menu.data('mm-initialized')) {
			$menu.metisMenu();
			$menu.data('mm-initialized', true);
		}
		jQuery('.metismenu > .mm-active ').each(function(){
			if(!jQuery(this).children('ul').length > 0)
			{
				jQuery(this).addClass('active-no-child');
			}
		});
	}

	var handleAllChecked = function() {
		$("#checkAll").on('change',function() {
			$("td input, .email-list .custom-checkbox input").prop('checked', $(this).prop("checked"));
		});
		$(".checkAllInput").on('click',function() {
			jQuery(this).closest('.ItemsCheckboxSec').find('input[type="checkbox"]').prop('checked', true);
		});
		$(".unCheckAllInput").on('click',function() {
			jQuery(this).closest('.ItemsCheckboxSec').find('input[type="checkbox"]').prop('checked', false);
		});
	}

	var handleNavigation = function() {
		$(document).off('click.navControl').on('click.navControl', '.nav-control', function() {
			$('#main-wrapper').toggleClass("menu-toggle");
			$(".hamburger").toggleClass("is-active");
		});
	}

	/*
	Resets then reapplies mm-active/mm-show based on the current URL.
	This sets the DEFAULT open/closed state for dropdowns the user has
	never manually touched. applyStoredMenuState() (below) then overrides
	this default for any dropdown the user HAS explicitly opened/closed,
	using localStorage — that's what makes a manual open/close survive a
	genuine hard reload, not just a Livewire wire:navigate.
	*/
	var handleCurrentActive = function() {
		jQuery('ul#menu a').removeClass('mm-active');
		jQuery('ul#menu li').removeClass('mm-active');
		jQuery('ul#menu ul').removeClass('mm-show');

		for (var nk = window.location,
			o = $("ul#menu a").filter(function() {
				return this.href == nk;
			})
			.addClass("mm-active")
			.parent()
			.addClass("mm-active");;)
		{
			if (!o.is("li")) break;
			o = o.parent()
				.addClass("mm-show")
				.parent()
				.addClass("mm-active");
		}
	}

	/*
	═══════════════════════════════════════════════════════════════════
	SIDEBAR DROPDOWN OPEN/CLOSED STATE PERSISTENCE
	═══════════════════════════════════════════════════════════════════
	Problem: @persist('sidebar') keeps state across Livewire SPA
	navigations, but a genuine hard reload (F5 / browser refresh) tears
	the whole DOM down and rebuilds it fresh from Blade — @persist can't
	help there. handleCurrentActive() above only opens whichever dropdown
	contains the current route, so every OTHER dropdown the user had
	manually opened (or the current one if they'd manually closed it)
	snapped back to that route-only default on every reload.

	Fix: store each dropdown's open/closed state in localStorage — which
	DOES survive a hard reload — keyed by the `data-menu-key` attribute
	on each dropdown <li> in sidebar.blade.php. The user's last manual
	toggle always wins over the route-based default, in either direction
	(stays open even if it's not the active route; stays closed even if
	it IS the active route).
	═══════════════════════════════════════════════════════════════════
	*/
	var MENU_STATE_STORAGE_KEY = 'ptech_sidebar_menu_state';

	var getStoredMenuState = function() {
		try {
			return JSON.parse(localStorage.getItem(MENU_STATE_STORAGE_KEY)) || {};
		} catch (e) {
			return {};
		}
	}

	var setStoredMenuState = function(key, isOpen) {
		var state = getStoredMenuState();
		state[key] = isOpen;
		try {
			localStorage.setItem(MENU_STATE_STORAGE_KEY, JSON.stringify(state));
		} catch (e) {
			/* localStorage unavailable (private browsing / quota) — state just won't persist, fail silently */
		}
	}

	/*
	bootstrap-metisMenu fires 'shown.metisMenu' / 'hidden.metisMenu' on
	the toggled <li> itself whenever a dropdown opens/closes — whether
	that's from a real user click OR from applyStoredMenuState() below
	triggering a click programmatically. Delegated off `document` +
	namespaced + `.off()` guarded so this is safe to call repeatedly
	without ever double-binding.

	`e.target !== this` guards against the event bubbling up from a
	nested submenu inside a different <li> that happens to be inside
	this one (not currently the case in this sidebar, but safe either way).
	*/
	var handleMenuStateTracking = function() {
		jQuery(document)
			.off('shown.metisMenu.stateTrack')
			.on('shown.metisMenu.stateTrack', '#menu li[data-menu-key]', function(e) {
				if (e.target !== this) return;
				setStoredMenuState(jQuery(this).attr('data-menu-key'), true);
			})
			.off('hidden.metisMenu.stateTrack')
			.on('hidden.metisMenu.stateTrack', '#menu li[data-menu-key]', function(e) {
				if (e.target !== this) return;
				setStoredMenuState(jQuery(this).attr('data-menu-key'), false);
			});
	}

	/*
	Applies stored state on top of whatever handleCurrentActive() just
	set. Only touches dropdowns that have an explicit entry in storage —
	one the user has never toggled keeps the route-based default.

	Reuses metisMenu's own click handler (via .trigger('click')) rather
	than manually setting mm-show/mm-active/aria-expanded ourselves, so
	arrow-rotation and any other internal bookkeeping the plugin does
	stays perfectly in sync — this is exactly what a real user click
	does. jQuery.fx.off is flipped on for the duration so the
	slideDown/slideUp metisMenu normally animates resolves instantly,
	so reloading doesn't show a visible slide as stored state reapplies.
	*/
	var applyStoredMenuState = function() {
		var $menu = jQuery('#menu');
		if ($menu.length === 0) return;

		var state = getStoredMenuState();
		var previousFxOff = jQuery.fx.off;
		jQuery.fx.off = true;

		$menu.find('> li[data-menu-key]').each(function() {
			var $li = jQuery(this);
			var key = $li.attr('data-menu-key');
			if (!Object.prototype.hasOwnProperty.call(state, key)) return;

			var desiredOpen = state[key];
			var isCurrentlyOpen = $li.children('ul').hasClass('mm-show');

			if (desiredOpen !== isCurrentlyOpen) {
				$li.children('a.has-arrow').trigger('click');
			}
		});

		jQuery.fx.off = previousFxOff;
	}

	var handleMiniSidebar = function() {
		$("ul#menu>li").on('click', function() {
			const sidebarStyle = $('body').attr('data-sidebar-style');
			if (sidebarStyle === 'mini') {
				console.log($(this).find('ul'))
				$(this).find('ul').stop()
			}
		})
	}

	var handleMinHeight = function() {
		var win_h = window.outerHeight;
		var win_h = window.outerHeight;
		if (win_h > 0 ? win_h : screen.height) {
			$(".content-body").css("min-height", (win_h + 0) + "px");
		};
	}

	var handleDataAction = function() {
		$('a[data-action="collapse"]').on("click", function(i) {
			i.preventDefault(),
			$(this).closest(".card").find('[data-action="collapse"] i').toggleClass("mdi-arrow-down mdi-arrow-up"),
			$(this).closest(".card").children(".card-body").collapse("toggle");
		});

		$('a[data-action="expand"]').on("click", function(i) {
			i.preventDefault(),
			$(this).closest(".card").find('[data-action="expand"] i').toggleClass("icon-size-actual icon-size-fullscreen"),
			$(this).closest(".card").toggleClass("card-fullscreen");
		});

		$('[data-action="close"]').on("click", function() {
			$(this).closest(".card").removeClass().slideUp("fast");
		});

		$('[data-action="reload"]').on("click", function() {
			var e = $(this);
			e.parents(".card").addClass("card-load"),
			e.parents(".card").append('<div class="card-loader"><i class=" ti-reload rotate-refresh"></div>'),
			setTimeout(function() {
				e.parents(".card").children(".card-loader").remove(),
					e.parents(".card").removeClass("card-load")
			}, 2000)
		});
	}

    var handleHeaderHight = function() {
		const headerHight = $('.header').innerHeight();
		$(window).scroll(function() {
			if ($('body').attr('data-layout') === "horizontal" && $('body').attr('data-header-position') === "static" && $('body').attr('data-sidebar-position') === "fixed")
			$(this.window).scrollTop() >= headerHight ? $('.deznav').addClass('fixed') : $('.deznav').removeClass('fixed')
		});
	}

	var handleMenuTabs = function() {
		if(screenWidth <= 991 ){
			jQuery('.menu-tabs .nav-link').on('click',function(){
				if(jQuery(this).hasClass('open'))
				{
					jQuery(this).removeClass('open');
					jQuery('.fixed-content-box').removeClass('active');
					jQuery('.hamburger').show();
				}else{
					jQuery('.menu-tabs .nav-link').removeClass('open');
					jQuery(this).addClass('open');
					jQuery('.fixed-content-box').addClass('active');
					jQuery('.hamburger').hide();
				}
			});
			jQuery('.close-fixed-content').on('click',function(){
				jQuery('.fixed-content-box').removeClass('active');
				jQuery('.hamburger').removeClass('is-active');
				jQuery('#main-wrapper').removeClass('menu-toggle');
				jQuery('.hamburger').show();
			});
		}
	}

	var headerFix = function(){
		/* Main navigation fixed on top  when scroll down function custom */
		jQuery(window).on('scroll', function () {

			if(jQuery('.header').length > 0){
				var menu = jQuery('.header');
				$(window).scroll(function(){
					var sticky = $('.header'),
					scroll = $(window).scrollTop();

					if (scroll >= 100){
						sticky.addClass('is-fixed');
					}else {
						sticky.removeClass('is-fixed');
					}
				});
			}
		});
		/* Main navigation fixed on top  when scroll down function custom end*/
	}

	/*
	REMOVED: handleChatbox()
	Bound directly to `.bell-link` / `.chatbox-close`, both of which live
	in the navbar (not @persist'd) and get destroyed/rebuilt on every
	wire:navigate — this handler would silently stop working after the
	first navigation. The chat widget uses Alpine's store
	($store.chat.open) as the single source of truth for open/close, so
	no jQuery binding is needed here.
	*/

	var handleBtnNumber = function() {
		$('.btn-number').on('click', function(e) {
			e.preventDefault();

			fieldName = $(this).attr('data-field');
			type = $(this).attr('data-type');
			var input = $("input[name='" + fieldName + "']");
			var currentVal = parseInt(input.val());
			if (!isNaN(currentVal)) {
				if (type == 'minus')
					input.val(currentVal - 1);
				else if (type == 'plus')
					input.val(currentVal + 1);
			} else {
				input.val(0);
			}
		});
	}

	var handleDzChatUser = function() {
		jQuery(document).off('click.dzChatUserOpen').on('click.dzChatUserOpen', '.dz-chat-user-box .dz-chat-user', function(){
			jQuery('.dz-chat-user-box').addClass('d-none');
			jQuery('.dz-chat-history-box').removeClass('d-none');
		});

		jQuery(document).off('click.dzChatUserBack').on('click.dzChatUserBack', '.dz-chat-history-back', function(){
			jQuery('.dz-chat-user-box').removeClass('d-none');
			jQuery('.dz-chat-history-box').addClass('d-none');
		});
	}

	var handleDzFullScreen = function() {
		jQuery(document).off('click.dzFullscreenToggle').on('click.dzFullscreenToggle', '.dz-fullscreen', function(e){
			if(document.fullscreenElement||document.webkitFullscreenElement||document.mozFullScreenElement||document.msFullscreenElement) {
				/* exit fullscreen */
				if(document.exitFullscreen) {
					document.exitFullscreen();
				} else if(document.msExitFullscreen) {
					document.msExitFullscreen(); /* IE/Edge */
				} else if(document.mozCancelFullScreen) {
					document.mozCancelFullScreen(); /* Firefox */
				} else if(document.webkitExitFullscreen) {
					document.webkitExitFullscreen(); /* Chrome, Safari & Opera */
				}
			}
			else { /* enter fullscreen */
				if(document.documentElement.requestFullscreen) {
					document.documentElement.requestFullscreen();
				} else if(document.documentElement.webkitRequestFullscreen) {
					document.documentElement.webkitRequestFullscreen();
				} else if(document.documentElement.mozRequestFullScreen) {
					document.documentElement.mozRequestFullScreen();
				} else if(document.documentElement.msRequestFullscreen) {
					document.documentElement.msRequestFullscreen();
				}
			}
			jQuery('.dz-fullscreen').toggleClass('active');
		});
	}

	var handleshowPass = function(){
		jQuery('.show-pass').on('click',function(){
			jQuery(this).toggleClass('active');
			if(jQuery('#dz-password').attr('type') == 'password'){
				jQuery('#dz-password').attr('type','text');
			}else if(jQuery('#dz-password').attr('type') == 'text'){
				jQuery('#dz-password').attr('type','password');
			}
		});
	}

	var heartBlast = function (){
		$(".heart").on("click", function() {
			$(this).toggleClass("heart-blast");
		});
	}

	var handleDzLoadMore = function() {
		$(".dz-load-more").on('click', function(e){
			e.preventDefault();
			$(this).append(' <i class="fas fa-sync"></i>');

			var dzLoadMoreUrl = $(this).attr('rel');
			var dzLoadMoreId = $(this).attr('id');

			$.ajax({
				method: "POST",
				url: dzLoadMoreUrl,
				dataType: 'html',
				success: function(data) {
					$( "#"+dzLoadMoreId+"Content").append(data);
					$('.dz-load-more i').remove();
				}
			})
		});
	}

	var handleLightgallery = function(){
		if(jQuery('#lightgallery').length > 0){
			$('#lightgallery').lightGallery({
				loop:true,
				thumbnail:true,
				exThumbImage: 'data-exthumbimage'
			});
		}
		if(jQuery('#lightgallery2').length > 0){
			$('#lightgallery2').lightGallery({
				loop:true,
				thumbnail:true,
				exThumbImage: 'data-exthumbimage'
			});
		}
	}

	var handleCustomFileInput = function() {
		$(".custom-file-input").on("change", function() {
			var fileName = $(this).val().split("\\").pop();
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});
	}

  	var vHeight = function(){
        var ch = $(window).height() - 206;
        $(".chatbox .msg_card_body").css('height',ch);
    }

	var handleDatetimepicker = function(){
		if(jQuery("#datetimepicker1").length>0) {
			$('#datetimepicker1').datetimepicker({
				inline: true,
			});
		}
	}

	var handleCkEditor = function(){
		if(jQuery("#ckeditor").length>0) {
			ClassicEditor
			.create( document.querySelector( '#ckeditor' ), {
				simpleUpload: {
                    uploadUrl: 'ckeditor-upload.php',
                }
			} )
			.then( editor => {
				window.editor = editor;
			} )
			.catch( err => {
				console.error( err.stack );
			} );
		}
	}

	var handleMenuPosition = function(){
		if(screenWidth > 1024){
			$(".metismenu  li").unbind().each(function (e) {
				if ($('ul', this).length > 0) {
					var elm = $('ul:first', this).css('display','block');
					var off = elm.offset();
					var l = off.left;
					var w = elm.width();
					var elm = $('ul:first', this).removeAttr('style');
					var docH = $("body").height();
					var docW = $("body").width();

					if(jQuery('html').hasClass('rtl')){
						var isEntirelyVisible = (l + w <= docW);
					}else{
						var isEntirelyVisible = (l > 0)?true:false;
					}

					if (!isEntirelyVisible) {
						$(this).find('ul:first').addClass('left');
					} else {
						$(this).find('ul:first').removeClass('left');
					}
				}
			});
		}
	}

	var handleChartSidebar = function(){
		$('.chat-rightarea-btn').on('click',function(){
			$(this).toggleClass('active');
			$('.chat-right-area').toggleClass('active');
		})
		$('.chat-hamburger').on('click',function(){
			$('.chat-left-area').toggleClass('active');
		})
	}

	var MagnificPopup = function(){
		if($(".popup-youtube, .popup-vimeo, .popup-gmaps").length > 0 ) {
			$('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
				disableOn: 700,
				type: 'iframe',
				mainClass: 'mfp-fade',
				removalDelay: 160,
				preloader: false,
				fixedContentPos: true
			});
		}
	}

	var handleDraggableCard = function() {
		if($('.draggable-zone').length > 0){
			var dzCardDraggable = function () {
				return {
					init: function () {
						var containers = document.querySelectorAll('.draggable-zone');

						if (containers.length === 0) {
							return false;
						}

						var swappable = new Sortable.default(containers, {
							draggable: '.draggable',
							handle: '.draggable.draggable-handle',
							mirror: {
								appendTo: 'body',
								constrainDimensions: true
							}
						});

						swappable.on('drag:stop', () => {
							setTimeout(function(){
								setBoxCount();
							}, 200);
						})
					}
				};
			}();

			jQuery(document).ready(function () {
				dzCardDraggable.init();
			});

			function setBoxCount(){
				var cardCount = 0;
				jQuery('.dropzoneContainer').each(function(){
					cardCount = jQuery(this).find('.draggable-handle').length;
					jQuery(this).find('.totalCount').html(cardCount);
				});
			}
		}
	}

	var handleConverterTheme = function(){
		if($('.btc-converts').length > 0){
			setTimeout(()=> {
				if($('body').attr('data-theme-version') === "dark"){
					$('.btc-converts').attr('dark-mode', true);
				}
			},1000);

			$('#theme_version').on('change',function(){
				if($('body').attr('data-theme-version') === "dark"){
					$('.btc-converts').attr('dark-mode', true);
				} else{
					$('.btc-converts').attr('dark-mode', false);
				}
			});
		}
	}

	var handlePageOnScroll = function(event){
		var headerHeight = parseInt($('.header').css('height'), 10);

		$('.navbar-nav .scroll').on('click', function(event){
			event.preventDefault();

			jQuery('.navbar-nav .scroll').parent().removeClass('active');
			jQuery(this).parent().addClass('active');

			if (this.hash !== "") {
				var hash = this.hash;
				var seactionPosition = parseInt($(hash).offset().top, 10);
				var headerHeight =  parseInt($('.header').css('height'), 10);

				var scrollTopPosition = seactionPosition - headerHeight;

				$('html, body').animate({scrollTop: scrollTopPosition},
				800, function(){

				});
			}
		});
		pageOnScroll();
	}

	var pageOnScroll = function(event){
		if(jQuery('.navbar-nav').length > 0){
			var headerHeight = parseInt(jQuery('.header').height(), 10);

			jQuery(document).on("scroll", function(){

				var scrollPos = jQuery(this).scrollTop();
				jQuery('.navbar-nav .scroll').each(function () {
					var elementLink = jQuery(this);

					var refElement = jQuery(elementLink.attr("href"));

					if(jQuery(this.hash).offset() != undefined){
						var seactionPosition = parseInt(jQuery(this.hash).offset().top, 10);
					}else{
						var seactionPosition = 0;
					}
					var scrollTopPosition = (seactionPosition - headerHeight);

					if (scrollTopPosition <= scrollPos){
						elementLink.parent().addClass("active");
						elementLink.parent().siblings().removeClass("active");
					}
				});

			});
		}
	}

	var tagify = function(){
		if(jQuery('input[name=tagify]').length > 0){

			// The DOM element you wish to replace with Tagify
			var input = document.querySelector('input[name=tagify]');

			// initialize Tagify on the above input node reference
			new Tagify(input);
		}
	}

	var handleSelectText = function(){
		if($('.btn-select-text').length > 0){
			$('.btn-select-text').click(function(){
				if($(this).parent().hasClass('select-text-wrap')){
					var $temp = $('<textarea>');
					$('body').append($temp);
					$temp.val($(this).siblings('.text-select-copy').text()).select();
					document.execCommand('copy');
					$temp.remove();
				}
			});
		}
	}

	var setCurrentYear = function () {
		const currentDate = new Date();
		let currentYear = currentDate.getFullYear();
		let elements = document.getElementsByClassName('current-year');

		for (const element of elements) {
			element.innerHTML = currentYear;
		}
	}


	/* Function ============ */
	return {
		init:function(){
			handleMetisMenu();
			handleAllChecked();
			handleNavigation();
			handleCurrentActive();
			handleMenuStateTracking();
			applyStoredMenuState();
			handleMiniSidebar();
			handleMinHeight();
			handleDataAction();
			handleHeaderHight();
			handleMenuTabs();
			handleBtnNumber();
			handleDzChatUser();
			handleDzFullScreen();
			handleshowPass();
			heartBlast();
			handleDzLoadMore();
			handleLightgallery();
			handleCustomFileInput();
			vHeight();
			handleDatetimepicker();
			handleCkEditor();
			headerFix();
			handleChartSidebar();
			MagnificPopup();
			handleDraggableCard();
			handleConverterTheme();
			handleSelectPicker();
			handlePageOnScroll();
			handleImageSelect();
			tagify();
			handleSelectText();
			setCurrentYear();
		},

		load:function(){
			handlePreloader();
		},

		resize:function(){
			vHeight();
		},

		handleMenuPosition:function(){
			handleMenuPosition();
		},

		/*
		Re-sync after a Livewire navigation: reapply the route-based
		default, then reapply any stored manual state on top of it.
		metisMenu itself is never disposed/reinitialized here (see
		handleMetisMenu's comment above) — only classes and, where
		stored state disagrees with the default, a simulated click on
		the affected dropdown(s).
		*/
		refresh:function(){
			handleCurrentActive();
			handleMenuStateTracking();
			applyStoredMenuState();
			handleNavigation();
		},
	}

}();

/* Document.ready Start */
jQuery(document).ready(function() {
	$('[data-bs-toggle="popover"]').popover();

	W3Crm.init();

	$('.btn-follow').click(function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active').html('<i class="la la-user me-1 fs-14"></i> Follow');
		}else{
			$(this).addClass('active').html('<i class="la la-check me-1 fs-14"></i> Following');
		}
	});

	$('.post-like').click(function(){
		$(this).toggleClass('active');
	});

});
/* Document.ready END */

/* Window Load START */
jQuery(window).on('load',function () {
	W3Crm.load();

	setTimeout(function(){
		W3Crm.handleMenuPosition();
	}, 500);

});
/*  Window Load END */

/* Window Resize START */
jQuery(window).on('resize',function () {
	W3Crm.resize();

	setTimeout(function(){
		W3Crm.handleMenuPosition();
	}, 500);

});
/*  Window Resize END */

/*
Re-sync sidebar active/open state after every Livewire navigation.
#menu is @persist'd (see sidebar.blade.php), so it's the same DOM node
across pages — W3Crm.refresh() reapplies the route default then any
stored manual open/closed state on top of it.
*/
document.addEventListener('livewire:navigated', function () {
	W3Crm.refresh();
});