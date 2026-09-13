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

	// ═══════════════════════════════════════════════════════════════════════
	// SIDEBAR DROPDOWN OPEN/CLOSED PERSISTENCE
	// ═══════════════════════════════════════════════════════════════════════
	// Each top-level collapsible <li> in sidebar.blade.php now carries a
	// stable `data-menu-key="..."` attribute (e.g. "user-management",
	// "projects"). We record whether that dropdown is open or closed in
	// localStorage, keyed by that attribute, and re-apply it to the raw
	// DOM BEFORE metisMenu is ever initialized on a hard page load/reload.
	//
	// Why this is needed: previously, whether a dropdown was expanded was
	// recomputed from the current URL on every load (Blade's server-side
	// mm-show classes) and re-forced on every navigation by
	// handleCurrentActive(). That meant manually closing "User Management"
	// while on a Users/Roles/Permissions page never stuck — the very next
	// reload (or even the next in-page navigation) put it right back open,
	// because nothing was actually remembering that YOU closed it; it was
	// only ever asking "does this dropdown contain the current page?".
	//
	// Now: if the user has ever manually toggled a given dropdown, that
	// explicit preference is stored and always wins over the route-based
	// default, in both directions (forces-open OR forces-closed). If a
	// dropdown has never been toggled by the user, Blade's original
	// route-based default (open only if it contains the current page)
	// is left untouched.
	var MENU_STATE_STORAGE_KEY = 'sidebarMenuOpenState';

	function readMenuState() {
		try {
			return JSON.parse(localStorage.getItem(MENU_STATE_STORAGE_KEY)) || {};
		} catch (e) {
			return {};
		}
	}

	function writeMenuState(state) {
		try {
			localStorage.setItem(MENU_STATE_STORAGE_KEY, JSON.stringify(state));
		} catch (e) {
			// localStorage unavailable (private mode, quota, etc.) — state
			// just won't persist across reloads; nothing else breaks.
		}
	}

	// Applies any saved open/closed preference onto the sidebar's raw DOM.
	// MUST run before handleMetisMenu() so the plugin reads the correct
	// starting mm-active/mm-show classes at init time, exactly the way it
	// already does for Blade's own server-rendered route-based classes —
	// this never manually calls slideDown/slideUp or touches metisMenu's
	// internal state directly, avoiding any class/animation desync.
	var applySavedMenuState = function() {
		var state = readMenuState();
		jQuery('#menu > li[data-menu-key]').each(function(){
			var $li = jQuery(this);
			var key = $li.data('menu-key');

			// No explicit preference recorded for this dropdown yet —
			// leave Blade's route-based default (already rendered) alone.
			if (!Object.prototype.hasOwnProperty.call(state, key)) return;

			var $submenu = $li.children('ul');
			if (state[key]) {
				$li.addClass('mm-active');
				$submenu.addClass('mm-show');
			} else {
				$li.removeClass('mm-active');
				$submenu.removeClass('mm-show');
			}
		});
	}

	// Watches each dropdown's submenu for class changes (which is how
	// metisMenu itself marks a panel open/closed after a click, regardless
	// of its internal animation timing) and records the resulting
	// open/closed state. Using a MutationObserver instead of guessing a
	// setTimeout after a click means this stays correct no matter how
	// metisMenu's own animation duration is configured, and it also
	// catches state changes triggered any other way.
	var handleMenuStatePersistence = function() {
		jQuery('#menu > li[data-menu-key]').each(function(){
			var li = this;
			var key = jQuery(li).data('menu-key');
			var submenu = jQuery(li).children('ul')[0];
			if (!submenu || !key) return;

			// #menu is @persist'd — this element is never recreated, so
			// guard against ever attaching a second observer to the same
			// node if this function were called more than once.
			if (submenu.__menuStateObserverAttached) return;
			submenu.__menuStateObserverAttached = true;

			var observer = new MutationObserver(function(){
				var isOpen = submenu.classList.contains('mm-show');
				var state = readMenuState();
				state[key] = isOpen;
				writeMenuState(state);
			});
			observer.observe(submenu, { attributes: true, attributeFilter: ['class'] });
		});
	}

	/*
	Only ever initializes metisMenu once. #menu is @persist'd — it is the
	SAME DOM node across every Livewire navigation (including the single
	livewire:navigated fire that happens on a plain hard refresh), so
	disposing + reinitializing it on every navigation was wiping the
	open/active state metisMenu had already set up, and setting inline
	display:none on submenus that a moment later got their mm-show class
	reapplied — but by then the inline style from metisMenu's own init
	already beat the CSS class rule, so the panel visually collapsed right
	after appearing to open. That's the flash you were seeing.
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

	/*
	REMOVED: handleNavigation()
	This used to bind its own click handler to `.nav-control` (namespaced
	`click.navControl`), toggling `menu-toggle`/`is-active` — but
	layout.blade.php ALSO binds its own handler to the exact same element
	(namespaced `click.hamburgerFix`), and does it more correctly (it's
	mobile-aware: `mobile-sidebar-open` on phone, `menu-toggle` otherwise).
	Different jQuery event namespaces on the same element/event are two
	INDEPENDENT handlers — both fired on every click. The first toggled
	the class on, the second immediately toggled it back off in the same
	click, so the hamburger visually did nothing at all. Removing this
	duplicate leaves layout.blade.php's handler as the actual single
	source of truth its own comment already claimed it was.
	*/

	/*
	Highlights the current page's own link only. Deliberately never
	touches mm-show or forces mm-active onto any ancestor <li> — dropdown
	open/closed state is now exclusively owned by applySavedMenuState()/
	handleMenuStatePersistence() above, so navigating (or refreshing) can
	never force a dropdown the user closed back open, or vice versa.
	*/
	var handleCurrentActive = function() {
		jQuery('ul#menu a').removeClass('mm-active');

		jQuery('ul#menu a').filter(function() {
			return this.href == window.location.href;
		}).addClass('mm-active');
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
	first navigation. The chat widget now uses Alpine's store
	($store.chat.open) as the single source of truth for open/close, so
	no jQuery binding is needed here at all. Kept out to avoid
	re-introducing the stale-binding bug.
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

	/*
	Delegated off `document` with a namespace + `.off()` guard so this is
	safe to re-run and keeps matching whatever `.dz-chat-user` /
	`.dz-chat-history-back` node currently exists, even after the navbar
	region gets replaced by a wire:navigate.
	*/
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

	/*
	Delegated off `document`, namespaced + `.off()` guarded, so the
	Fullscreen API toggle keeps working after any number of wire:navigate
	swaps and never double-fires from being bound twice.
	*/
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
			// Order matters: apply any saved open/closed preference to the
			// raw DOM classes FIRST, then initialize metisMenu once — so
			// the plugin's very first read of the DOM already reflects the
			// user's saved choice, exactly like it already does for
			// Blade's own route-based default classes.
			applySavedMenuState();
			handleMetisMenu();
			handleMenuStatePersistence();

			handleAllChecked();
			handleCurrentActive();
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
		Runs on every Livewire navigation. #menu is @persist'd, so it's the
		same DOM node across pages — open/closed dropdown state is already
		correct exactly as the user left it (nothing here ever needs to
		re-apply saved state or touch metisMenu). This only re-highlights
		whichever link matches the new URL.
		*/
		refresh:function(){
			handleCurrentActive();
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
Re-sync sidebar active-link highlighting after every Livewire navigation.
#menu is @persist'd (see sidebar.blade.php), so it's the same DOM node
across pages — dropdown open/closed state is untouched here and stays
exactly as the user left it; only the bolded current-page link updates.
*/
document.addEventListener('livewire:navigated', function () {
	W3Crm.refresh();
});