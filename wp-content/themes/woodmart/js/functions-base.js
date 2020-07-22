var woodmartThemeModule;
var wooFile = false;

(function ($) {
	"use strict";

	woodmartThemeModule = (function () {

		var woodmartTheme = {
			popupEffect: 'mfp-move-horizontal',
			bootstrapTooltips: '.woodmart-tooltip, .woodmart-hover-icons .woodmart-buttons .wd-action-btn:not(.wd-add-cart-btn) > a, .woodmart-hover-icons .woodmart-buttons .wd-add-cart-btn, body:not(.catalog-mode-on):not(.login-see-prices) .woodmart-hover-base .wd-bottom-actions .wd-action-btn.wd-style-icon:not(.wd-add-cart-btn) > a, body:not(.catalog-mode-on):not(.login-see-prices) .woodmart-hover-base .wd-bottom-actions .wd-action-btn.wd-style-icon.wd-add-cart-btn, .woodmart-hover-base .wd-compare-btn > a, .woodmart-products-nav .woodmart-back-btn',
		};

		return {

			init: function () {
				// Disable nanoscroller option
				woodmartTheme.disableNanoScrollerWebkit = woodmart_settings.disable_nanoscroller == 'webkit' && (jscd.browser == 'Chrome' || jscd.browser == 'Opera' || jscd.browser == 'Safari');

				this.headerBanner();

				this.headerBuilder()

				this.visibleElements();

				this.bannersHover();

				this.portfolioEffects();

				this.parallax();

				this.googleMap();

				this.scrollTop();

				this.sidebarMenu();

				this.widgetsHidable();

				this.stickyColumn();

				this.mfpPopup();

				this.blogMasonry();

				this.blogLoadMore();

				this.portfolioLoadMore();

				this.equalizeColumns();

				this.menuSetUp();

				this.menuOffsets();

				this.onePageMenu();

				this.mobileNavigation();

				this.simpleDropdown();

				this.promoPopup();

				this.contentPopup();

				this.cookiesPopup();

				this.btnsToolTips();

				this.stickyFooter();

				this.countDownTimer();

				this.nanoScroller();

				this.gradientShift();

				this.videoPoster();

				this.mobileSearchIcon();

				this.fullScreenMenu();

				this.searchFullScreen();

				this.wooInit();

				this.lazyLoading();

				this.ajaxSearch();

				this.photoswipeImages();

				this.stickySocialButtons();

				this.animationsOffset();

				this.hiddenSidebar();

				this.imageHotspot();

				this.woodSliderLazyLoad();

				this.owlCarouselInit();

				this.portfolioPhotoSwipe();

				this.woocommerceNotices();

				this.menuDropdownsAJAX();

				this.instagramAjaxQuery();

				this.footerWidgetsAccordion();

				this.googleMapInit();

				this.moreCategoriesButton();

				this.buttonSmoothScroll();

				this.ageVerify();

				this.htmlBlockColorSchemeSwitcher();

				this.view3d();

				this.infoboxSvg();

				this.imagesGalleryMasonry();

				this.imagesGalleryJustified();

				$(window).resize();

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * WooCommerce init
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			wooInit: function () {
				if (!woodmart_settings.woo_installed) return;

				this.loginDropdown();

				this.loginSidebar();

				this.productLoaderPosition();

				this.initZoom();

				this.woocommerceWrappTable();

				this.woocommerceComments();

				this.onRemoveFromCart();

				this.woocommerceQuantity();

				this.cartWidget();

				this.ajaxFilters();

				this.shopPageInit();

				this.filtersArea();

				this.categoriesMenu();

				this.headerCategoriesMenu();

				this.loginTabs();

				this.productVideo();

				this.product360Button();

				this.wishList();

				this.compare();

				this.woodmartCompare();

				this.productsLoadMore();

				this.productsTabs();

				this.swatchesVariations();

				this.swatchesOnGrid();

				this.quickViewInit();

				this.quickShop();

				this.addToCart();

				this.productAccordion();

				this.productImagesGallery();

				this.productImages();

				this.stickyDetails();

				this.stickyAddToCart();

				this.stickySidebarBtn();

				this.productMoreDescription();

				this.variationsPrice();

				this.wishlist();

				this.singleProductTabsAccordion();

				this.singleProductTabsCommentsFix();

				this.commentImage();

				this.commentImagesUploadValidation();

				this.wcTabsHoverFix();

				this.gridQuantity();

				this.miniCartQuantity();

				this.swatchesLimit();
			},

			imagesGalleryMasonry: function () {
				if (typeof($.fn.isotope) == "undefined" || typeof($.fn.imagesLoaded) == "undefined") return;
				var $container = $(".view-masonry .gallery-images");

				// initialize Masonry after all images have loaded
				$container.imagesLoaded(function() {
					$container.isotope({
						gutter: 0,
						isOriginLeft: ! $("body").hasClass("rtl"),
						itemSelector: ".woodmart-gallery-item"
					});
				});
			},

			imagesGalleryJustified: function () {
				$('.view-justified').each(function () {
					$(this).find('.gallery-images').justifiedGallery({
						margins: 1,
						cssAnimation: true,
					});
				});
			},

			infoboxSvg: function () {
				$('.woodmart-info-box.with-animation').each(function () {
					new Vivus($(this).find('.info-svg-wrapper > svg')[0], {
						type: "delayed",
						duration: 200,
						start: "inViewport",
						animTimingFunction: Vivus.EASE_OUT
					});
				});
			},

			view3d: function () {
				$('.woodmart-threed-view').each(function () {
					var $this = $(this);
					var data = $this.data('args');

					if ($this.hasClass('threesixty-ready') || !data){
						return false;
					}

					$this.ThreeSixty({
						totalFrames: data.frames_count,
						endFrame: data.frames_count,
						currentFrame: 1,
						imgList: '.threed-view-images',
						progress: '.spinner',
						imgArray: data.images,
						height: data.height,
						width: data.width,
						responsive: true,
						navigation: true,
					});
				});
			},

			htmlBlockColorSchemeSwitcher: function () {
				$('.wd-html-block-scheme-switcher > div').on('click', function () {
					$('.website-wrapper').css('background-color', $(this).data('color'));
				});
			},

			buttonSmoothScroll	: function () {
				$('.woodmart-button-wrapper.wd-smooth-scroll a').on('click', function(e) {
					e.stopPropagation();
					e.preventDefault();

					var $button = $(this);
					var time = $button.parent().data('smooth-time');
					var offset = $button.parent().data('smooth-offset');
					var hash = $button.attr('href').split('#')[1];

					var $anchor = $('#' + hash);

					if ($anchor.length < 1) {
						return;
					}

					var position = $anchor.offset().top;

					$('html, body').animate({
						scrollTop: position - offset,
					}, time);
				});
			},

			moreCategoriesButton: function () {
				$('.wd-more-cat').each(function () {
					var $wrapper = $(this);

					$wrapper.find('.wd-more-cat-btn a').on('click', function (e) {
						e.preventDefault();
						$wrapper.addClass('wd-show-cat');
					});
				});
			},

			googleMapInit: function () {
				$('.google-map-container').each(function () {
					var $map = $(this);
					var data = $map.data('map-args');

					var config = {
						locations: [{
							lat: data.latitude,
							lon: data.longitude,
							icon: data.marker_icon,
							animation: google.maps.Animation.DROP
						}],
						controls_on_map: false,
						map_div: '#' + data.selector,
						start: 1,
						map_options: {
							zoom: parseInt( data.zoom ),
							scrollwheel: 'yes' === data.mouse_zoom ? true : false,
						},
					};

					if (data.json_style && !data.elementor) {
						config.styles = {};
						config.styles[woodmart_settings.google_map_style_text] = $.parseJSON(data.json_style);
					} else if (data.json_style && data.elementor) {
						config.styles = {};
						config.styles[woodmart_settings.google_map_style_text] = $.parseJSON(atob(data.json_style));
					}

					if ('yes' === data.marker_text_needed) {
						config.locations[0].html = data.marker_text;
					}

					if ('button' === data.init_type) {
						$map.find('.woodmart-init-map').on('click', function(e){
							e.preventDefault();
							
							if ($map.hasClass('woodmart-map-inited')) {
								return;
							}

							$map.addClass('woodmart-map-inited');
							new Maplace(config).Load();
						});
					} else if ('scroll' === data.init_type) {
						$(window).scroll(function () {
							if (window.innerHeight + $(window).scrollTop() + parseInt(data.init_offset) > $map.offset().top) {
								if ($map.hasClass('woodmart-map-inited')) {
									return;
								}

								$map.addClass('woodmart-map-inited');
								new Maplace(config).Load();
							}
						});

						$(window).scroll();
					} else {
						new Maplace(config).Load();
					}
				});
			},

			footerWidgetsAccordion: function () {
				if ($(window).width() >= 576) {
					return;
				}

				$('.footer-widget-collapse .widget-title').on('click', function () {
					var $title = $(this);
					var $widget = $title.parent();
					var $content = $widget.find('> *:not(.widget-title)');

					if ($widget.hasClass('footer-widget-opened')) {
						$widget.removeClass('footer-widget-opened');
						$content.stop().slideUp(200);
					} else {
						$widget.addClass('footer-widget-opened');
						$content.stop().slideDown(200);
					}
				});
			},

			instagramAjaxQuery: function () {
				$('.instagram-widget').each(function () {
					var $instagram = $(this);
					if (!$instagram.hasClass('instagram-with-error')) {
						return;
					}

					var username = $instagram.data('username');
					var atts = $instagram.data('atts');
					var request_param = username.indexOf('#') > -1 ? 'explore/tags/' + username.substr(1) : username;

					var url = 'https://www.instagram.com/' + request_param + '/';

					$instagram.addClass('loading');

					$.ajax({
						url: url,
						success: function (response) {
							$.ajax({
								url: woodmart_settings.ajaxurl,
								data: {
									action: 'woodmart_instagram_ajax_query',
									body: response,
									atts: atts,
								},
								dataType: 'json',
								method: 'POST',
								success: function (response) {
									$instagram.parent().html(response);
									woodmartThemeModule.owlCarouselInit();
								},
								error: function (data) {
									console.log('instagram ajax error');
								},
							});
						},
						error: function (data) {
							console.log('instagram ajax error');
						},
					});

				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Menu dropdowns AJAX
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			menuDropdownsAJAX: function () {
				var $menus = jQuery('.menu').has('.dropdown-load-ajax');

				jQuery('body').on('mousemove', checkMenuProximity);

				function checkMenuProximity(event) {

					$menus.each(function () {
						var $menu = jQuery(this);

						if ($menu.hasClass('dropdowns-loading') || $menu.hasClass('dropdowns-loaded')) return;

						if (!isNear($menu, 50, event)) return;

						loadDropdowns($menu);

					});
				}

				function loadDropdowns($menu) {
					$menu.addClass('dropdowns-loading');

					var $items = $menu.find('.dropdown-load-ajax'),
						ids = [];

					$items.each(function () {
						ids.push(jQuery(this).find('.dropdown-html-placeholder').data('id'));
					});

					jQuery.ajax({
						url: woodmart_settings.ajaxurl,
						data: {
							action: 'woodmart_load_html_dropdowns',
							ids: ids,
						},
						dataType: 'json',
						method: 'POST',
						success: function (response) {

							if (response.status == 'success') {
								Object.keys(response.data).forEach(function (id) {
									var html = response.data[id];
									$menu.find('[data-id="' + id + '"]').replaceWith(html);
								});

								// Initialize OWL Carousels
								woodmartThemeModule.owlCarouselInit();

							} else {
								console.log('loading html dropdowns returns wrong data - ', response.message);
							}
						},
						error: function (data) {
							console.log('loading html dropdowns ajax error');
						},
						complete: function () {
							$menu.removeClass('dropdowns-loading').addClass('dropdowns-loaded');
						},
					});
				}

				function isNear($element, distance, event) {

					var left = $element.offset().left - distance,
						top = $element.offset().top - distance,
						right = left + $element.width() + (2 * distance),
						bottom = top + $element.height() + (2 * distance),
						x = event.pageX,
						y = event.pageY;

					return (x > left && x < right && y > top && y < bottom);

				};
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * WooCommerce pretty notices
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			woocommerceNotices: function () {
				var notices = '.woocommerce-error, .woocommerce-info, .woocommerce-message, div.wpcf7-response-output, #yith-wcwl-popup-message, .mc4wp-alert, .dokan-store-contact .alert-success, .yith_ywraq_add_item_product_message';

				$('body').on('click', notices, function () {
					var $msg = $(this);
					hideMessage($msg);
				});

				var showAllMessages = function () {
					$notices.addClass('shown-notice');
				};

				var hideAllMessages = function () {
					hideMessage($notices);
				};

				var hideMessage = function ($msg) {
					$msg.removeClass('shown-notice').addClass('hidden-notice');
				};
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Portfolio photo swipe
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			portfolioPhotoSwipe: function () {
				$(document).on('click', '.portfolio-enlarge', function (e) {
					e.preventDefault();
					var $parent = $(this).parents('.owl-item');
					if ($parent.length == 0) {
						$parent = $(this).parents('.portfolio-entry');
					}
					var index = $parent.index();
					var items = getPortfolioImages();
					woodmartThemeModule.callPhotoSwipe(index, items);
				});

				var getPortfolioImages = function () {
					var items = [];
					$('.portfolio-entry').find('figure a img').each(function () {
						items.push({
							src: $(this).attr('src'),
							w: $(this).attr('width'),
							h: $(this).attr('height')
						});
					});
					return items;
				};
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Owl carousel init function
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			owlCarouselInit: function () {
				$(document).on('FrontendEditorCarouselInit', function (event, $element) {
					owlInit($element);
				});

				$('div[data-owl-carousel]:not(.scroll-init)').each(function () {
					owlInit($(this));
				});

				if (typeof ($.fn.waypoint) != 'undefined') {
					$('div[data-owl-carousel].scroll-init').waypoint(function () {
						owlInit($(this));
					}, {
							offset: '100%'
						});
				}

				function owlInit($this) {
					var $owl = $this.find('.owl-carousel');

					if ($(window).width() <= 1024 && $this.hasClass('disable-owl-mobile') || $owl.hasClass('owl-loaded')) {
						return;
					}

					var options = {
						rtl: $('body').hasClass('rtl'),
						items: $this.data('desktop') ? $this.data('desktop') : 1,
						responsive: {
							1025: {
								items: $this.data('desktop') ? $this.data('desktop') : 1
							},
							769: {
								items: $this.data('tablet_landscape') ? $this.data('tablet_landscape') : 1
							},
							577: {
								items: $this.data('tablet') ? $this.data('tablet') : 1
							},
							0: {
								items: $this.data('mobile') ? $this.data('mobile') : 1
							}
						},
						autoplay: $this.data('autoplay') == 'yes' ? true : false,
						autoplayHoverPause: $this.data('autoplay') == 'yes' ? true : false,
						autoplayTimeout: $this.data('speed') ? $this.data('speed') : 5000,
						dots: $this.data('hide_pagination_control') == 'yes' ? false : true,
						nav: $this.data('hide_prev_next_buttons') == 'yes' ? false : true,
						autoHeight: $this.data('autoheight') == 'yes' ? true : false,
						slideBy: typeof $this.data('scroll_per_page') !== 'undefined' ? 1 : 'page',
						navText: false,
						center: $this.data('center_mode') == 'yes' ? true : false,
						loop: $this.data('wrap') == 'yes' ? true : false,
						dragEndSpeed: $this.data('dragendspeed') ? $this.data('dragendspeed') : 200,
						onRefreshed: function () {
							$(window).resize();
						}
					};

					if ($this.data('sliding_speed')) {
						options.smartSpeed = $this.data('sliding_speed');
						options.dragEndSpeed = $this.data('sliding_speed');
					}

					if ($this.data('animation')) {
						options.animateOut = $this.data('animation');
						options.mouseDrag = false;
					}

					function determinePseudoActive() {
						var id = $owl.find('.owl-item.active').find('.woodmart-slide').attr('id');
						$owl.find('.owl-item.pseudo-active').removeClass('pseudo-active');
						var $els = $owl.find('[id="' + id + '"]');
						$els.each(function () {
							if (!$(this).parent().hasClass('active')) {
								$(this).parent().addClass('pseudo-active');
							}
						});
					}

					if ($this.data('content_animation')) {
						determinePseudoActive();
						options.onTranslated = function () {
							determinePseudoActive();
						};
					}

					$(window).on('vc_js', function () {
						$owl.trigger('refresh.owl.carousel');
					});

					$owl.owlCarousel(options);

					if ($this.data('autoheight') == 'yes') {
						$owl.imagesLoaded(function () {
							$owl.trigger('refresh.owl.carousel');
						});
					}
				}
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Woodmart slider lazyload
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			woodSliderLazyLoad: function () {
				$('.woodmart-slider').on('changed.owl.carousel', function (event) {
					var $this = $(this);
					var active = $this.find('.owl-item').eq(event.item.index);
					var id = active.find('.woodmart-slide').attr('id');
					var $els = $this.find('[id="' + id + '"]');

					active.find('.woodmart-slide').addClass('woodmart-loaded');
					$els.each(function () {
						$(this).addClass('woodmart-loaded');
					});
				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Image hotspot
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			imageHotspot: function () {
				//Hotspot action
				$('.woodmart-image-hotspot').each(function () {
					var _this = $(this);
					var btn = _this.find('.hotspot-btn');
					// var content = _this.find('.hotspot-content');
					var parentWrapper = _this.parents('.woodmart-image-hotspot-wrapper');

					if (!parentWrapper.hasClass('hotspot-action-click') && $(window).width() > 1024) return;

					btn.on('click', function () {
						if (_this.hasClass('hotspot-opened')) {
							_this.removeClass('hotspot-opened');
						} else {
							_this.addClass('hotspot-opened');
							_this.siblings().removeClass('hotspot-opened');
						}
						$(document).trigger('wood-images-loaded');
						return false;
					});

					$(document).on('click', function (e) {
						var target = e.target;
						if (_this.hasClass('hotspot-opened') && !$(target).is('.woodmart-image-hotspot') && !$(target).parents().is('.woodmart-image-hotspot')) {
							_this.removeClass('hotspot-opened');
							return false;
						}
					});
				});

				//Image loaded
				$('.woodmart-image-hotspot-wrapper').each(function () {
					var _this = $(this);
					_this.imagesLoaded(function () {
						_this.addClass('loaded');
					});
				});

				//Content position
				$('.woodmart-image-hotspot .hotspot-content').each(function () {
					var content = $(this);
					var offsetLeft = content.offset().left;
					var offsetRight = $(window).width() - (offsetLeft + content.outerWidth());

					if ($(window).width() > 768) {
						if (offsetLeft <= 0) content.addClass('hotspot-overflow-right');
						if (offsetRight <= 0) content.addClass('hotspot-overflow-left');
					}

					if ($(window).width() <= 768) {
						if (offsetLeft <= 0) content.css('marginLeft', Math.abs(offsetLeft - 15) + 'px');
						if (offsetRight <= 0) content.css('marginLeft', offsetRight - 15 + 'px');
					}
				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Hidden sidebar button
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			hiddenSidebar: function () {
				$('body').on('click', '.woodmart-show-sidebar-btn, .woodmart-sticky-sidebar-opener', function (e) {
					e.preventDefault();
					if ($('.sidebar-container').hasClass('show-hidden-sidebar')) {
						woodmartThemeModule.hideShopSidebar();
					} else {
						showSidebar();
					}
				});

				$('body').on("click touchstart", ".woodmart-close-side, .close-side-widget", function () {
					woodmartThemeModule.hideShopSidebar();
				});

				var showSidebar = function () {
					$('.sidebar-container').addClass('show-hidden-sidebar');
					$('.woodmart-close-side').addClass('woodmart-close-side-opened');
					$('.woodmart-show-sidebar-btn').addClass('btn-clicked');

					if ($(window).width() >= 1024 && (!woodmartTheme.disableNanoScrollerWebkit && woodmart_settings.disable_nanoscroller != 'disable')) {
						$(".sidebar-inner.woodmart-sidebar-scroll").nanoScroller({
							paneClass: 'woodmart-scroll-pane',
							sliderClass: 'woodmart-scroll-slider',
							contentClass: 'woodmart-sidebar-content',
							preventPageScrolling: false
						});
					}
				};
			},

			hideShopSidebar: function () {
				$('.woodmart-show-sidebar-btn').removeClass('btn-clicked');
				$('.sidebar-container').removeClass('show-hidden-sidebar');
				$('.woodmart-close-side').removeClass('woodmart-close-side-opened');
				if (!woodmartTheme.disableNanoScrollerWebkit && woodmart_settings.disable_nanoscroller != 'disable') {
					$('.sidebar-inner.woodmart-scroll').nanoScroller({ destroy: true });
				}
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Css animations offset
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			animationsOffset: function () {
				if (typeof ($.fn.waypoint) == 'undefined') return;

				$('.wpb_animate_when_almost_visible:not(.wpb_start_animation)').waypoint(function () {
					$(this).addClass('wpb_start_animation animated')
				}, {
						offset: '100%'
					});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Social buttons class on load
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			stickySocialButtons: function () {
				$('.woodmart-sticky-social').addClass('buttons-loaded');
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Photoswipe gallery
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			photoswipeImages: function () {
				$('.photoswipe-images').each(function () {
					var $this = $(this);
					$this.on('click', 'a', function (e) {
						e.preventDefault();
						var index = $(e.currentTarget).data('index') - 1;
						var items = getGalleryItems($this, []);
						woodmartThemeModule.callPhotoSwipe(index, items);
					});
				});

				var getGalleryItems = function ($gallery, items) {
					var src, width, height, title;

					$gallery.find('a').each(function () {
						src = $(this).attr('href');
						width = $(this).data('width');
						height = $(this).data('height');
						title = $(this).attr('title');
						if (!isItemInArray(items, src)) {
							items.push({
								src: src,
								w: width,
								h: height,
								title: title
							});
						}
					});

					return items;
				};

				var isItemInArray = function (items, src) {
					var i;
					for (i = 0; i < items.length; i++) {
						if (items[i].src == src) {
							return true;
						}
					}

					return false;
				};
			},

			callPhotoSwipe: function (index, items) {
				var pswpElement = document.querySelectorAll('.pswp')[0];

				if ($('body').hasClass('rtl')) {
					index = items.length - index - 1;
					items = items.reverse();
				}

				// define options (if needed)
				var options = {
					// optionName: 'option value'
					// for example:
					index: index, // start at first slide
					shareButtons: [
						{ id: 'facebook', label: woodmart_settings.share_fb, url: 'https://www.facebook.com/sharer/sharer.php?u={{url}}' },
						{ id: 'twitter', label: woodmart_settings.tweet, url: 'https://twitter.com/intent/tweet?text={{text}}&url={{url}}' },
						{
							id: 'pinterest', label: woodmart_settings.pin_it, url: 'http://www.pinterest.com/pin/create/button/' +
								'?url={{url}}&media={{image_url}}&description={{text}}'
						},
						{ id: 'download', label: woodmart_settings.download_image, url: '{{raw_image_url}}', download: true }
					],
					// getThumbBoundsFn: function(index) {

					//     // get window scroll Y
					//     var pageYScroll = window.pageYOffset || document.documentElement.scrollTop; 
					//     // optionally get horizontal scroll

					//     // get position of element relative to viewport
					//     var rect = $target.offset(); 

					//     // w = width
					//     return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};


					// }
				};

				// Initializes and opens PhotoSwipe
				var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
				gallery.init();
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Header banner
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			headerBanner: function () {
				var banner_version = woodmart_settings.header_banner_version,
					banner_btn = woodmart_settings.header_banner_close_btn,
					banner_enabled = woodmart_settings.header_banner_enabled;
				if (Cookies.get('woodmart_tb_banner_' + banner_version) == 'closed' || banner_btn == false || banner_enabled == false) return;
				var banner = $('.header-banner');

				if (!$('body').hasClass('page-template-maintenance')) {
					$('body').addClass('header-banner-display');
				}

				banner.on('click', '.close-header-banner', function (e) {
					e.preventDefault();
					closeBanner();
				})

				var closeBanner = function () {
					$('body').removeClass('header-banner-display').addClass('header-banner-hide');
					Cookies.set('woodmart_tb_banner_' + banner_version, 'closed', { expires: 60, path: '/' });
				};

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Full screen menu
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			fullScreenMenu: function () {
				$('.full-screen-burger-icon > a').on('click', function (e) {
					e.preventDefault();
					$('body').toggleClass('full-screen-menu-open');
				});

				$(document).keyup(function (e) {
					if (e.keyCode === 27) $('.full-screen-close-icon').click();
				});

				$('.full-screen-close-icon').on('click', function () {
					$('body').removeClass('full-screen-menu-open');
					setTimeout(function () {
						$('.full-screen-nav .menu-item-has-children').removeClass('sub-menu-open');
						$('.full-screen-nav .menu-item-has-children .icon-sub-fs').removeClass('up-icon');
					}, 200)
				});

				$('.full-screen-nav .menu > .menu-item.menu-item-has-children, .full-screen-nav .menu-item-design-default.menu-item-has-children .menu-item-has-children').append('<span class="icon-sub-fs"></span>');

				$('.full-screen-nav').on('click', '.icon-sub-fs', function (e) {
					var $icon = $(this),
						$parentItem = $icon.parent();

					e.preventDefault();
					if ($parentItem.hasClass('sub-menu-open')) {
						$parentItem.removeClass('sub-menu-open');
						$icon.removeClass('up-icon');
					} else {
						$parentItem.siblings('.sub-menu-open').find('.icon-sub-fs').removeClass('up-icon');
						$parentItem.siblings('.sub-menu-open').removeClass('sub-menu-open');
						$parentItem.addClass('sub-menu-open');
						$icon.addClass('up-icon');
					}
				});
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Mobile search icon 
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			mobileSearchIcon: function () {
				$('.mobile-search-icon.search-button').on('click', function (e) {
					e.preventDefault();
					if (!$('.mobile-nav').hasClass('act-mobile-menu')) {
						$('.mobile-nav').addClass('act-mobile-menu');
						$('.woodmart-close-side').addClass('woodmart-close-side-opened');
						$('.mobile-nav .searchform').find('input[type="text"]').focus();
					}
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Video Poster
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			videoPoster: function () {
				$('.woodmart-video-poster-wrapper').on('click', function () {
					var videoWrapper = $(this),
						video = videoWrapper.parent().find('iframe'),
						videoScr = video.attr('src'),
						videoNewSrc = videoScr + '&autoplay=1';

					if (videoScr.indexOf('vimeo.com') + 1) {
						videoNewSrc = videoScr + '?autoplay=1';
					}
					video.attr('src', videoNewSrc);
					videoWrapper.addClass('hidden-poster');
				})
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Header builder scripts for sticky header 
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			headerBuilder: function () {
				var $header = $('.whb-header'),
					$stickyElements = $('.whb-sticky-row'),
					$firstSticky = '',
					headerHeight = $header.find('.whb-main-header').outerHeight(),
					$window = $(window),
					isSticked = false,
					adminBarHeight = $('#wpadminbar').outerHeight(),
					stickAfter = 300,
					cloneHTML = '',
					isHideOnScroll = $header.hasClass('whb-hide-on-scroll');

				$stickyElements.each(function () {
					if ($(this).outerHeight() > 10) {
						$firstSticky = $(this);
						return false;
					}
				});


				// Real header sticky option
				if ($header.hasClass('whb-sticky-real')) {

					// if no sticky rows
					if ($firstSticky.length == 0 || $firstSticky.outerHeight() < 10) return;

					$header.addClass('whb-sticky-prepared').css({
						paddingTop: headerHeight
					})

					stickAfter = $firstSticky.offset().top - adminBarHeight
				}


				// Sticky header clone 

				if ($header.hasClass('whb-sticky-clone')) {
					var data = []
					data['cloneClass'] = $header.find('.whb-general-header').attr('class')

					if (isHideOnScroll) {
						data['wrapperClasses'] = 'whb-hide-on-scroll';
					}

					cloneHTML = woodmart_settings.whb_header_clone

					cloneHTML = cloneHTML.replace(/<%([^%>]+)?%>/g, function (replacement) {
						var selector = replacement.slice(2, -2)

						return $header.find(selector).length
							? $('<div>')
								.append($header.find(selector).first().clone())
								.html()
							: (data[selector] !== undefined) ? data[selector] : ''
					})

					$header.after(cloneHTML)
					$header = $header.parent().find('.whb-clone')

					$header.find('.whb-row').removeClass('whb-flex-equal-sides').addClass('whb-flex-flex-middle');
				}

				if ($('.whb-header').hasClass('whb-scroll-slide')) {
					stickAfter = headerHeight + adminBarHeight
				}

				var previousScroll;

				$window.on('scroll', function () {
					var after = stickAfter;
					var currentScroll = $(window).scrollTop();
					var windowHeight = $(window).height();
					var documentHeight = $(document).height();
					if ($('.header-banner').length > 0 && $('body').hasClass('header-banner-display')) {
						after += $('.header-banner').outerHeight();
					}

					if (!$('.close-header-banner').length && $header.hasClass('whb-scroll-stick')) {
						after = stickAfter
					}

					if (currentScroll > after) {
						stickHeader();
					} else {
						unstickHeader();
					}

					var startAfter = 100;

					if ($header.hasClass('whb-scroll-stick')) {
						startAfter = 500;
					}

					if (isHideOnScroll) {
						if (previousScroll - currentScroll > 0 && currentScroll > after ) {
							$header.addClass('whb-scroll-up');
							$header.removeClass('whb-scroll-down');
						} else if (currentScroll - previousScroll > 0 && currentScroll + windowHeight != documentHeight && currentScroll > (after + startAfter)) {
							$header.addClass('whb-scroll-down');
							$header.removeClass('whb-scroll-up');
						} else if (currentScroll <= after) {
							$header.removeClass('whb-scroll-down');
							$header.removeClass('whb-scroll-up');
						} else if (currentScroll + windowHeight >= documentHeight - 5) {
							$header.addClass('whb-scroll-up');
							$header.removeClass('whb-scroll-down');
						}
					}

					previousScroll = currentScroll;
				});

				function stickHeader() {
					if (isSticked) return
					isSticked = true
					$header.addClass('whb-sticked')
				}

				function unstickHeader() {
					if (!isSticked) return

					isSticked = false
					$header.removeClass('whb-sticked')
				}
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Counter shortcode method
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			counterShortcode: function (counter) {
				if (counter.attr('data-state') == 'done' || counter.text() != counter.data('final')) {
					return;
				}
				counter.prop('Counter', 0).animate({
					Counter: counter.text()
				}, {
						duration: 3000,
						easing: 'swing',
						step: function (now) {
							if (now >= counter.data('final')) {
								counter.attr('data-state', 'done');
							}
							counter.text(Math.ceil(now));
						}
					});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Activate methods in viewport
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			visibleElements: function () {
				$('.woodmart-counter .counter-value').each(function () {
					$(this).waypoint(function () {
						woodmartThemeModule.counterShortcode($(this));
					}, { offset: '100%' });
				});
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Banner hover effect with jquery panr
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			bannersHover: function () {
				if (typeof ($.fn.panr) == 'undefined') return;
				$('.promo-banner.banner-hover-parallax').panr({
					sensitivity: 20,
					scale: false,
					scaleOnHover: true,
					scaleTo: 1.15,
					scaleDuration: .34,
					panY: true,
					panX: true,
					panDuration: 0.5,
					resetPanOnMouseLeave: true
				});
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Portfolio hover effects
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			portfolioEffects: function () {
				if (typeof ($.fn.panr) == 'undefined') return;
				$('.woodmart-portfolio-holder .portfolio-parallax').panr({
					sensitivity: 15,
					scale: false,
					scaleOnHover: true,
					scaleTo: 1.12,
					scaleDuration: 0.45,
					panY: true,
					panX: true,
					panDuration: 1.5,
					resetPanOnMouseLeave: true
				});
			},

			// Age verify.
			ageVerify: function () {
				if ( woodmart_settings.age_verify != 'yes' || Cookies.get('woodmart_age_verify') == 'confirmed' ){
					return;
				}

				$.magnificPopup.open({
					items: {
						src: '.wd-age-verify'
					},
					type: 'inline',
					closeOnBgClick: false,
					closeBtnInside: false,
					showCloseBtn: false,
					enableEscapeKey: false,
					removalDelay: 500,
					tClose: woodmart_settings.close,
					tLoading: woodmart_settings.loading,
					callbacks: {
						beforeOpen: function () {
							this.st.mainClass = woodmartTheme.popupEffect + ' promo-popup-wrapper';
						},
					}
				});

				$('.wd-age-verify-allowed').on('click', function(){
					Cookies.set('woodmart_age_verify', 'confirmed', { expires: parseInt( woodmart_settings.age_verify_expires ), path: '/' });
					$.magnificPopup.close();
				});

				$('.wd-age-verify-forbidden').on('click', function(){
					$('.wd-age-verify').addClass('wd-forbidden');
				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Promo popup
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			promoPopup: function () {
				var promo_version = woodmart_settings.promo_version;

				if ($('body').hasClass('page-template-maintenance') || woodmart_settings.enable_popup != 'yes' || (woodmart_settings.promo_popup_hide_mobile == 'yes' && $(window).width() < 768) || (Cookies.get('woodmart_age_verify') != 'confirmed' && woodmart_settings.age_verify == 'yes') ) return;

				var popup = $('.woodmart-promo-popup'),
					shown = false,
					pages = Cookies.get('woodmart_shown_pages');

				var showPopup = function () {
					$.magnificPopup.open({
						items: {
							src: '.woodmart-promo-popup'
						},
						type: 'inline',
						removalDelay: 500, //delay removal by X to allow out-animation
						tClose: woodmart_settings.close,
						tLoading: woodmart_settings.loading,
						callbacks: {
							beforeOpen: function () {
								this.st.mainClass = woodmartTheme.popupEffect + ' promo-popup-wrapper';
							},
							open: function () {
								// Will fire when this exact popup is opened
								// this - is Magnific Popup object
							},
							close: function () {
								Cookies.set('woodmart_popup_' + promo_version, 'shown', { expires: 7, path: '/' });
							}
							// e.t.c.
						}
					});
					$(document).trigger('wood-images-loaded');
				};

				$('.woodmart-open-newsletter').on('click', function (e) {
					e.preventDefault();
					showPopup();
				})

				if (!pages) pages = 0;

				if (pages < woodmart_settings.popup_pages) {
					pages++;
					Cookies.set('woodmart_shown_pages', pages, { expires: 7, path: '/' });
					return false;
				}

				if (Cookies.get('woodmart_popup_' + promo_version) != 'shown') {
					if (woodmart_settings.popup_event == 'scroll') {
						$(window).scroll(function () {
							if (shown) return false;
							if ($(document).scrollTop() >= woodmart_settings.popup_scroll) {
								showPopup();
								shown = true;
							}
						});
					} else {
						setTimeout(function () {
							showPopup();
						}, woodmart_settings.popup_delay);
					}
				}


			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Content in popup element
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			contentPopup: function () {
				var popup = $('.woodmart-open-popup');

				popup.magnificPopup({
					type: 'inline',
					removalDelay: 500, //delay removal by X to allow out-animation
					tClose: woodmart_settings.close,
					tLoading: woodmart_settings.loading,
					callbacks: {
						beforeOpen: function () {
							this.st.mainClass = woodmartTheme.popupEffect + ' content-popup-wrapper';
						},

						open: function () {
							$(document).trigger('wood-images-loaded');
						}
					}
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Cookies law
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			cookiesPopup: function () {
				var cookies_version = woodmart_settings.cookies_version;
				if (Cookies.get('woodmart_cookies_' + cookies_version) == 'accepted') return;
				var popup = $('.woodmart-cookies-popup');

				setTimeout(function () {
					popup.addClass('popup-display');
					popup.on('click', '.cookies-accept-btn', function (e) {
						e.preventDefault();
						acceptCookies();
					})
				}, 2500);

				var acceptCookies = function () {
					popup.removeClass('popup-display').addClass('popup-hide');
					Cookies.set('woodmart_cookies_' + cookies_version, 'accepted', { expires: 60, path: '/' });
				};
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Google map
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			googleMap: function () {
				var gmap = $(".google-map-container-with-content");

				$(window).resize(function () {
					gmap.css({
						'height': gmap.find('.woodmart-google-map.with-content').outerHeight()
					})
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Menu preparation
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			menuSetUp: function () {
				var hasChildClass = 'menu-item-has-children',
					mainMenu = $('.woodmart-navigation').find('ul.menu'),
					lis = mainMenu.find(' > li'),
					openedClass = 'item-menu-opened';

				$('.mobile-nav').find('ul.site-mobile-menu').find(' > li').has('.sub-menu-dropdown').addClass(hasChildClass);

				mainMenu.on('click', ' > .item-event-click > a', function (e) {
					e.preventDefault();
					if (!$(this).parent().hasClass(openedClass)) {
						$('.' + openedClass).removeClass(openedClass);
					}
					$(this).parent().toggleClass(openedClass);
				});

				$(document).on('click', function (e) {
					var target = e.target;
					if ($('.' + openedClass).length > 0 && !$(target).is('.item-event-hover') && !$(target).parents().is('.item-event-hover') && !$(target).parents().is('.' + openedClass + '')) {
						mainMenu.find('.' + openedClass + '').removeClass(openedClass);
						return false;
					}
				});

				var menuForIPad = function () {
					if ($(window).width() <= 1024) {
						mainMenu.find(' > .menu-item-has-children.item-event-hover').each(function () {
							$(this).data('original-event', 'hover').removeClass('item-event-hover').addClass('item-event-click');
						});
					} else {
						mainMenu.find(' > .item-event-click').each(function () {
							if ($(this).data('original-event') == 'hover') {
								$(this).removeClass('item-event-click').addClass('item-event-hover');
							}
						});
					}
				};

				$(window).on('resize', menuForIPad);
			},
			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Keep navigation dropdowns in the screen
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			menuOffsets: function () {

				var mainMenu = $('.main-nav, .whb-secondary-menu').find('ul.menu'),
					lis = mainMenu.find(' > li.menu-item-design-sized, li.menu-item-design-full-width');


				mainMenu.on('hover', ' > li.menu-item-design-sized, li.menu-item-design-full-width', function (e) {
					setOffset($(this));
				});

				var setOffset = function (li) {

					var dropdown = li.find(' > .sub-menu-dropdown'),
						styleID = 'arrow-offset',
						siteWrapper = $('.website-wrapper');

					dropdown.attr('style', '');

					var dropdownWidth = dropdown.outerWidth(),
						dropdownOffset = dropdown.offset(),
						screenWidth = $(window).width(),
						bodyRight = siteWrapper.outerWidth() + siteWrapper.offset().left,
						viewportWidth = $('body').hasClass('wrapper-boxed') || $('body').hasClass('wrapper-boxed-2') ? bodyRight : screenWidth,
						extraSpace = (li.hasClass('menu-item-design-full-width')) ? 0 : 10;

					if (!dropdownWidth || !dropdownOffset) return;

					var dropdownOffsetRight = screenWidth - dropdownOffset.left - dropdownWidth;

					if ($('body').hasClass('rtl') && dropdownOffsetRight + dropdownWidth >= viewportWidth && (li.hasClass('menu-item-design-sized') || li.hasClass('menu-item-design-full-width'))) {
						// If right point is not in the viewport
						var toLeft = dropdownOffsetRight + dropdownWidth - viewportWidth;

						dropdown.css({
							right: - toLeft - extraSpace
						});

					} else if (dropdownOffset.left + dropdownWidth >= viewportWidth && (li.hasClass('menu-item-design-sized') || li.hasClass('menu-item-design-full-width'))) {
						// If right point is not in the viewport
						var toRight = dropdownOffset.left + dropdownWidth - viewportWidth;

						dropdown.css({
							left: - toRight - extraSpace
						});
					}

				};

				lis.each(function () {
					setOffset($(this));
					$(this).addClass('with-offsets');
				});
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * One page menu
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			onePageMenu: function () {

				var scrollToRow = function (hash) {
					var row = $('#' + hash);

					if (row.length < 1) return;

					var position = row.offset().top;

					$('html, body').animate({
						scrollTop: position - woodmart_settings.one_page_menu_offset
					}, 800);

					setTimeout(function () {
						activeMenuItem(hash);
					}, 800);
				};

				var activeMenuItem = function (hash) {
					var itemHash;
					$('.onepage-link').each(function () {
						itemHash = $(this).find('> a').attr('href').split('#')[1];

						if (itemHash == hash) {
							$('.onepage-link').removeClass('current-menu-item');
							$(this).addClass('current-menu-item');
						}

					});
				};

				$('body').on('click', '.onepage-link > a', function (e) {
					var $this = $(this),
						hash = $this.attr('href').split('#')[1];

					if ($('#' + hash).length < 1) return;

					e.preventDefault();

					scrollToRow(hash);

					// close mobile menu
					$('.woodmart-close-side').trigger('click');
				});

				if ($('.onepage-link').length > 0) {
					$('.entry-content > .vc_section, .entry-content > .vc_row').waypoint(function () {
						var hash = $(this).attr('id');
						activeMenuItem(hash);
					}, { offset: 150 });

					// $('.onepage-link').removeClass('current-menu-item');

					// URL contains hash
					var locationHash = window.location.hash.split('#')[1];

					if (window.location.hash.length > 1) {
						setTimeout(function () {
							scrollToRow(locationHash);
						}, 500);
					}

				}
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * mobile responsive navigation
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */

			mobileNavigation: function () {

				var body = $("body"),
					mobileNav = $(".mobile-nav"),
					wrapperSite = $(".website-wrapper"),
					dropDownCat = $(".mobile-nav .site-mobile-menu .menu-item-has-children"),
					elementIcon = '<span class="icon-sub-menu"></span>',
					butOpener = $(".icon-sub-menu");

				var closeSide = $('.woodmart-close-side');

				dropDownCat.append(elementIcon);

				mobileNav.on("click", ".icon-sub-menu", function (e) {
					e.preventDefault();

					if ($(this).parent().hasClass("opener-page")) {
						$(this).parent().removeClass("opener-page").find("> ul").slideUp(200);
						$(this).parent().removeClass("opener-page").find(".sub-menu-dropdown .container > ul, .sub-menu-dropdown > ul").slideUp(200);
						$(this).parent().find('> .icon-sub-menu').removeClass("up-icon");
					} else {
						$(this).parent().addClass("opener-page").find("> ul").slideDown(200);
						$(this).parent().addClass("opener-page").find(".sub-menu-dropdown .container > ul, .sub-menu-dropdown > ul").slideDown(200);
						$(this).parent().find('> .icon-sub-menu').addClass("up-icon");
					}
				});

				mobileNav.on('click', '.mobile-nav-tabs li', function () {
					if ($(this).hasClass('active')) return;
					var menuName = $(this).data('menu');
					$(this).parent().find('.active').removeClass('active');
					$(this).addClass('active');
					$('.mobile-menu-tab').removeClass('active');
					$('.mobile-' + menuName + '-menu').addClass('active');
				});

				body.on("click", ".mobile-nav-icon > a", function (e) {
					e.preventDefault();

					if (mobileNav.hasClass("act-mobile-menu")) {
						closeMenu();
					} else {
						openMenu();
					}

				});

				body.on("click touchstart", ".woodmart-close-side", function () {
					closeMenu();
				});

				body.on('click', '.mobile-nav .login-side-opener', function () {
					closeMenu();
				});

				function openMenu() {
					mobileNav.addClass("act-mobile-menu");
					closeSide.addClass('woodmart-close-side-opened');
				}

				function closeMenu() {
					mobileNav.removeClass("act-mobile-menu");
					closeSide.removeClass('woodmart-close-side-opened');
					$('.mobile-nav .searchform input[type=text]').blur();
				}
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Simple dropdown for category select on search form
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			simpleDropdown: function () {

				$('.input-dropdown-inner').each(function () {
					var dd = $(this);
					var btn = dd.find('> a');
					var input = dd.find('> input');
					var list = dd.find('> .list-wrapper');

					inputPadding();

					$(document).on('click', function (e) {
						var target = e.target;
						if (dd.hasClass('dd-shown') && !$(target).is('.input-dropdown-inner') && !$(target).parents().is('.input-dropdown-inner')) {
							hideList();
							return false;
						}
					});

					btn.on('click', function (e) {
						e.preventDefault();

						if (dd.hasClass('dd-shown')) {
							hideList();
						} else {
							showList();
						}
						return false;
					});

					list.on('click', 'a', function (e) {
						e.preventDefault();
						var value = $(this).data('val');
						var label = $(this).text();
						list.find('.current-item').removeClass('current-item');
						$(this).parent().addClass('current-item');
						if (value != 0) {
							list.find('ul:not(.children) > li:first-child').show();
						} else if (value == 0) {
							list.find('ul:not(.children) > li:first-child').hide();
						}
						btn.text(label);
						input.val(value).trigger('cat_selected');
						hideList();
						inputPadding();
					});


					function showList() {
						dd.addClass('dd-shown');
						list.slideDown(100);
						if (typeof ($.fn.devbridgeAutocomplete) != 'undefined') {
							dd.parent().siblings('[type="text"]').devbridgeAutocomplete('hide');
						}
						setTimeout(function () {
							woodmartThemeModule.nanoScroller();
						}, 300);
					}

					function hideList() {
						dd.removeClass('dd-shown');
						list.slideUp(100);
					}

					function inputPadding() {
						if ($(window).width() <= 768) return;
						var paddingValue = dd.innerWidth() + dd.parent().siblings('.searchsubmit').innerWidth() + 17,
							padding = 'padding-right';
						if ($('body').hasClass('rtl')) padding = 'padding-left';

						dd.parent().parent().find('.s').css(padding, paddingValue);
					}
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Function to make columns the same height
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			equalizeColumns: function () {

				$.fn.woodmart_equlize = function (options) {

					var settings = $.extend({
						child: "",
					}, options);

					var that = this;

					if (settings.child != '') {
						that = this.find(settings.child);
					}

					var resize = function () {

						var maxHeight = 0;
						var height;
						that.each(function () {
							$(this).attr('style', '');
							if ($(window).width() > 767 && $(this).outerHeight() > maxHeight)
								maxHeight = $(this).outerHeight();
						});

						that.each(function () {
							$(this).css({
								minHeight: maxHeight
							});
						});

					}

					$(window).on('resize', function () {
						resize();
					});
					setTimeout(function () {
						resize();
					}, 200);
					setTimeout(function () {
						resize();
					}, 500);
					setTimeout(function () {
						resize();
					}, 800);
				}

				$('.equal-columns').each(function () {
					$(this).woodmart_equlize({
						child: '> [class*=col-]'
					});
				});
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Enable masonry grid for blog
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			blogMasonry: function () {
				if (typeof ($.fn.isotope) == 'undefined' || typeof ($.fn.imagesLoaded) == 'undefined') return;
				var $container = $('.masonry-container');

				// initialize Masonry after all images have loaded
				$container.imagesLoaded(function () {
					$container.isotope({
						gutter: 0,
						isOriginLeft: !$('body').hasClass('rtl'),
						itemSelector: '.blog-design-masonry, .blog-design-mask, .masonry-item'
					});
				});


				//Portfolio filters
				$('.masonry-filter').on('click', 'a', function (e) {
					e.preventDefault();
					setTimeout(function () {
						jQuery(document).trigger('wood-images-loaded');
					}, 300);

					$('.masonry-filter').find('.filter-active').removeClass('filter-active');
					$(this).addClass('filter-active');
					var filterValue = $(this).attr('data-filter');
					$(this).parents('.portfolio-filter').next('.masonry-container.woodmart-portfolio-holder').isotope({
						filter: filterValue
					});
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Helper function that make btn click when you scroll page to it
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			clickOnScrollButton: function (btnClass, destroy, offset) {
				if (typeof $.waypoints != 'function') return;

				var $btn = $(btnClass);
				if (destroy) {
					$btn.waypoint('destroy');
				}

				if (!offset) {
					offset = 0;
				}

				$btn.waypoint(function () {
					$btn.trigger('click');
				}, {
						offset: function () {
							return $(window).outerHeight() + parseInt(offset);
						}
					});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Load more button for blog shortcode
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			blogLoadMore: function () {
				var btnClass = '.woodmart-blog-load-more.load-on-scroll',
					process = false;

				woodmartThemeModule.clickOnScrollButton(btnClass, false, false);

				$('.woodmart-blog-load-more').on('click', function (e) {
					e.preventDefault();

					if (process || $(this).hasClass('no-more-posts')) return;

					process = true;

					var $this = $(this),
						holder = $this.parent().siblings('.woodmart-blog-holder'),
						source = holder.data('source'),
						action = 'woodmart_get_blog_' + source,
						ajaxurl = woodmart_settings.ajaxurl,
						dataType = 'json',
						method = 'POST',
						atts = holder.data('atts'),
						paged = holder.data('paged');

					$this.addClass('loading');

					var data = {
						atts: atts,
						paged: paged,
						action: action,
					};

					if (source == 'main_loop') {
						ajaxurl = $(this).attr('href');
						method = 'GET';
						data = {};
					}

					$.ajax({
						url: ajaxurl,
						data: data,
						dataType: dataType,
						method: method,
						success: function (data) {

							var items = $(data.items);

							if (items) {
								if (holder.hasClass('masonry-container')) {
									// initialize Masonry after all images have loaded  
									holder.append(items).isotope('appended', items);
									holder.imagesLoaded().progress(function () {
										holder.isotope('layout');
										woodmartThemeModule.clickOnScrollButton(btnClass, true, false);
									});
								} else {
									holder.append(items);
									woodmartThemeModule.clickOnScrollButton(btnClass, true, false);
								}

								holder.data('paged', paged + 1);

								if (source == 'main_loop') {
									$this.attr('href', data.nextPage);
									if (data.status == 'no-more-posts') {
										$this.hide().remove();
									}
								}
							}

							if (data.status == 'no-more-posts') {
								$this.addClass('no-more-posts');
								$this.hide();
							}

						},
						error: function (data) {
							console.log('ajax error');
						},
						complete: function () {
							$this.removeClass('loading');
							process = false;
						},
					});

				});

			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Load more button for portfolio shortcode
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			portfolioLoadMore: function () {

				if (typeof $.waypoints != 'function') return;

				var waypoint = $('.woodmart-portfolio-load-more.load-on-scroll').waypoint(function () {
					$('.woodmart-portfolio-load-more.load-on-scroll').trigger('click');
				}, { offset: '100%' }),
					process = false;

				$('.woodmart-portfolio-load-more').on('click', function (e) {
					e.preventDefault();

					if (process || $(this).hasClass('no-more-posts')) return;

					process = true;

					var $this = $(this),
						holder = $this.parent().parent().find('.woodmart-portfolio-holder'),
						source = holder.data('source'),
						action = 'woodmart_get_portfolio_' + source,
						ajaxurl = woodmart_settings.ajaxurl,
						dataType = 'json',
						method = 'POST',
						timeout,
						atts = holder.data('atts'),
						paged = holder.data('paged');

					$this.addClass('loading');

					var data = {
						atts: atts,
						paged: paged,
						action: action,
					};

					if (source == 'main_loop') {
						ajaxurl = $(this).attr('href');
						method = 'GET';
						data = {};
					}

					$.ajax({
						url: ajaxurl,
						data: data,
						dataType: dataType,
						method: method,
						success: function (data) {

							var items = $(data.items);

							if (items) {
								if (holder.hasClass('masonry-container')) {
									// initialize Masonry after all images have loaded
									holder.append(items).isotope('appended', items);
									holder.imagesLoaded().progress(function () {
										holder.isotope('layout');

										clearTimeout(timeout);

										timeout = setTimeout(function () {
											$('.woodmart-portfolio-load-more.load-on-scroll').waypoint('destroy');
											waypoint = $('.woodmart-portfolio-load-more.load-on-scroll').waypoint(function () {
												$('.woodmart-portfolio-load-more.load-on-scroll').trigger('click');
											}, { offset: '100%' });
										}, 1000);
									});
								} else {
									holder.append(items);
								}

								holder.data('paged', paged + 1);

								$this.attr('href', data.nextPage);
							}

							woodmartThemeModule.mfpPopup();
							woodmartThemeModule.portfolioEffects();

							if (data.status == 'no-more-posts') {
								$this.addClass('no-more-posts');
								$this.hide();
							}

						},
						error: function (data) {
							console.log('ajax error');
						},
						complete: function () {
							$this.removeClass('loading');
							process = false;
						},
					});

				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * MEGA MENU
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			sidebarMenu: function () {
				var heightMegaMenu = $(".widget_nav_mega_menu #menu-categories").height();
				var heightMegaNavigation = $(".categories-menu-dropdown").height();
				var subMenuHeight = $(".widget_nav_mega_menu ul > li.menu-item-design-sized > .sub-menu-dropdown, .widget_nav_mega_menu ul > li.menu-item-design-full-width > .sub-menu-dropdown");
				var megaNavigationHeight = $(".categories-menu-dropdown ul > li.menu-item-design-sized > .sub-menu-dropdown, .categories-menu-dropdown ul > li.menu-item-design-full-width > .sub-menu-dropdown");
				subMenuHeight.css(
					"min-height", heightMegaMenu + "px"
				);

				megaNavigationHeight.css(
					"min-height", heightMegaNavigation + "px"
				);
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Hide widget on title click
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			widgetsHidable: function () {

				$(document).on('click', '.widget-hidable .widget-title', function () {
					var content = $(this).siblings('ul, div, form, label, select');
					$(this).parent().toggleClass('widget-hidden');
					content.stop().slideToggle(200);
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Sticky column for portfolio items
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			stickyColumn: function () {
				var details = $('.woodmart-sticky-column');

				details.each(function () {
					var $column = $(this),
						offset = 0;

					if ($('body').hasClass('enable-sticky-header') || $('.whb-sticky-row').length > 0 || $('.whb-sticky-header').length > 0) {
						offset = 150;
					}

					$column.find(' > .vc_column-inner > .wpb_wrapper').stick_in_parent({
						offset_top: offset
					});
				})

				$('.wd-elementor-sticky-column').each(function () {
					var $column = $(this);
					var offset = 150;
					var classes = $column.attr('class').split(' ');

					for (var index = 0; index < classes.length; index++) {
						if (classes[index].indexOf('wd_sticky_offset_') >= 0) {
							var data = classes[index].split('_');
							offset = parseInt(data[3]);
						}
					}
					$column.find('> .elementor-column-wrap > .elementor-widget-wrap').stick_in_parent({
						offset_top: offset,
					});
				})
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Use magnific popup for images
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			mfpPopup: function () {

				$('.gallery').magnificPopup({
					delegate: ' > a',
					type: 'image',
					removalDelay: 500, //delay removal by X to allow out-animation
					tClose: woodmart_settings.close,
					tLoading: woodmart_settings.loading,
					callbacks: {
						beforeOpen: function () {
							this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
							this.st.mainClass = woodmartTheme.popupEffect;
						}
					},
					image: {
						verticalFit: true
					},
					gallery: {
						enabled: true,
						navigateByImgClick: true
					},
				});

				$('[data-rel="mfp"]').magnificPopup({
					type: 'image',
					removalDelay: 500, //delay removal by X to allow out-animation
					tClose: woodmart_settings.close,
					tLoading: woodmart_settings.loading,
					callbacks: {
						beforeOpen: function () {
							this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
							this.st.mainClass = woodmartTheme.popupEffect;
						}
					},
					image: {
						verticalFit: true
					},
					gallery: {
						enabled: false,
						navigateByImgClick: false
					},
				});

				$(document).on('click', '.mfp-img', function () {
					var mfp = jQuery.magnificPopup.instance; // get instance
					mfp.st.image.verticalFit = !mfp.st.image.verticalFit; // toggle verticalFit on and off
					mfp.currItem.img.removeAttr('style'); // remove style attribute, to remove max-width if it was applied
					mfp.updateSize(); // force update of size
				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Parallax effect
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			parallax: function () {
				if ($(window).width() <= 1024) return;

				$('.parallax-yes').each(function () {
					var $bgobj = $(this);
					$(window).scroll(function () {
						var yPos = -($(window).scrollTop() / $bgobj.data('speed'));
						var coords = 'center ' + yPos + 'px';
						$bgobj.css({
							backgroundPosition: coords
						});
					});
				});

				$('.woodmart-parallax').each(function () {
					var $this = $(this);
					if ($this.hasClass('wpb_column')) {
						$this.find('> .vc_column-inner').parallax("50%", 0.3);
					} else {
						$this.parallax("50%", 0.3);
					}
				});

			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Scroll top button
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			scrollTop: function () {
				//Check to see if the window is top if not then display button
				$(window).scroll(function () {
					if ($(this).scrollTop() > 100) {
						$('.scrollToTop').addClass('button-show');
					} else {
						$('.scrollToTop').removeClass('button-show');
					}
				});

				//Click event to scroll to top
				$('.scrollToTop').on('click', function () {
					$('html, body').animate({
						scrollTop: 0
					}, 800);
					return false;
				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * ToolTips titles
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			btnsToolTips: function () {
				var $tooltips = $('.woodmart-css-tooltip, .woodmart-buttons[class*="wd-pos-r"] div > a'),
					$bootstrapTooltips = $(woodmartTheme.bootstrapTooltips);

				// .product-grid-item .add_to_cart_button

				$tooltips.each(function () {
					if (!$(this).hasClass('wd-add-img-msg') && $(window).width() <= 1024) {
						return;
					}
					$(this).find('.woodmart-tooltip-label').remove();
					$(this).addClass('woodmart-tltp').prepend('<span class="woodmart-tooltip-label">' + $(this).text() + '</span>');
					$(this).find('.woodmart-tooltip-label').trigger('mouseover');
				})

					.off('mouseover.tooltips')

					.on('mouseover.tooltips', function () {
						var $label = $(this).find('.woodmart-tooltip-label'),
							width = $label.outerWidth();

						if ( $(this).hasClass('woodmart-tltp-top') ) {
							$label.css({
								marginLeft: - parseInt(width / 2)
							})
						}
					});

				// Bootstrap tooltips
				if ($(window).width() <= 1024) return;
				$bootstrapTooltips.tooltip({
					animation: false,
					container: 'body',
					trigger: 'hover',
					title: function () {
						if ($(this).find('.added_to_cart').length > 0) return $(this).find('.add_to_cart_button').text();
						return $(this).text();
					}
				});

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Sticky footer: margin bottom for main wrapper
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			stickyFooter: function () {

				if (!$('body').hasClass('sticky-footer-on') || $(window).width() <= 1024) return;

				var $footer = $('.footer-container'),
					$page = $('.main-page-wrapper'),
					$window = $(window);

				if ($('.woodmart-prefooter').length > 0) {
					$page = $('.woodmart-prefooter');
				}

				var footerOffset = function () {
					$page.css({
						marginBottom: $footer.outerHeight()
					})
				};

				$window.on('resize', footerOffset);

				$footer.imagesLoaded(function () {
					footerOffset();
				});

				//Safari fix
				var footerSafariFix = function () {
					if (!$('html').hasClass('browser-Safari')) return;
					var windowScroll = $window.scrollTop();
					var footerOffsetTop = $(document).outerHeight() - $footer.outerHeight();

					if (footerOffsetTop < windowScroll + $footer.outerHeight() + $window.outerHeight()) {
						$footer.addClass('visible-footer');
					} else {
						$footer.removeClass('visible-footer');
					}
				};

				footerSafariFix();
				$window.on('scroll', footerSafariFix);

			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Back in history
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			backHistory: function () {
				history.go(-1);

				setTimeout(function () {
					$('.filters-area').removeClass('filters-opened').stop().hide();
					$('.open-filters').removeClass('btn-opened');
					if ($(window).width() <= 1024) {
						$('.woodmart-product-categories').removeClass('categories-opened').stop().hide();
						$('.woodmart-show-categories').removeClass('button-open');
					}

					woodmartThemeModule.btnsToolTips();
					woodmartThemeModule.categoriesAccordion();
					woodmartThemeModule.woocommercePriceSlider();
				}, 20);


			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Ajax Search for products
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			ajaxSearch: function () {
				if (typeof ($.fn.devbridgeAutocomplete) == 'undefined') return;

				var escapeRegExChars = function (value) {
					return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
				};

				$('form.woodmart-ajax-search').each(function () {
					var $this = $(this),
						number = parseInt($this.data('count')),
						thumbnail = parseInt($this.data('thumbnail')),
						symbols_count = parseInt($this.data('symbols_count')),
						productCat = $this.find('[name="product_cat"]'),
						$results = $this.parent().find('.woodmart-search-results'),
						postType = $this.data('post_type'),
						url = woodmart_settings.ajaxurl + '?action=woodmart_ajax_search',
						price = parseInt($this.data('price')),
						sku = $this.data('sku');

					if (number > 0) url += '&number=' + number;
					url += '&post_type=' + postType;

					$results.on('click', '.view-all-results', function () {
						$this.submit();
					});

					if (productCat.length && productCat.val() !== '') {
						url += '&product_cat=' + productCat.val();
					}

					$this.find('[type="text"]').devbridgeAutocomplete({
						serviceUrl: url,
						appendTo: $results,
						minChars: symbols_count,

						onSelect: function (suggestion) {
							if (suggestion.permalink.length > 0)
								window.location.href = suggestion.permalink;
						},
						onSearchStart: function (query) {
							$this.addClass('search-loading');
						},
						beforeRender: function (container) {
							$(container).find('.suggestion-divider-text').parent().addClass('suggestion-divider');
							if (container[0].childElementCount > 2)
								$(container).append('<div class="view-all-results"><span>' + woodmart_settings.all_results + '</span></div>');

						},
						onSearchComplete: function (query, suggestions) {
							$this.removeClass('search-loading');

							if ($(window).width() >= 1024 && (!woodmartTheme.disableNanoScrollerWebkit && woodmart_settings.disable_nanoscroller != 'disable')) {
								$(".woodmart-scroll").nanoScroller({
									paneClass: 'woodmart-scroll-pane',
									sliderClass: 'woodmart-scroll-slider',
									contentClass: 'woodmart-scroll-content',
									preventPageScrolling: false
								});
							}

							$(document).trigger('wood-images-loaded');

						},
						formatResult: function (suggestion, currentValue) {
							if (currentValue == '&') currentValue = "&#038;";
							var pattern = '(' + escapeRegExChars(currentValue) + ')',
								returnValue = '';

							if ( suggestion.divider ) {
								returnValue += ' <h5 class="suggestion-divider-text">' + suggestion.divider + '</h5>';
							}

							if (thumbnail && suggestion.thumbnail) {
								returnValue += ' <div class="suggestion-thumb">' + suggestion.thumbnail + '</div>';
							}

							if ( suggestion.value) {
								returnValue += '<h4 class="suggestion-title result-title">' + suggestion.value
									.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>')
									// .replace(/&/g, '&amp;')
									// .replace(/</g, '&lt;')
									// .replace(/>/g, '&gt;')
									// .replace(/"/g, '&quot;')
									.replace(/&lt;(\/?strong)&gt;/g, '<$1>') + '</h4>';
							}

							if (suggestion.no_found) returnValue = '<div class="suggestion-title no-found-msg">' + suggestion.value + '</div>';

							if (sku && suggestion.sku) {
								returnValue += ' <div class="suggestion-sku">' + suggestion.sku + '</div>';
							}

							if (price && suggestion.price) {
								returnValue += ' <div class="suggestion-price price">' + suggestion.price + '</div>';
							}

							return returnValue;
						}
					});

					if (productCat.length) {

						var searchForm = $this.find('[type="text"]').devbridgeAutocomplete(),
							serviceUrl = woodmart_settings.ajaxurl + '?action=woodmart_ajax_search';

						if (number > 0) serviceUrl += '&number=' + number;
						serviceUrl += '&post_type=' + postType;

						productCat.on('cat_selected', function () {
							if (productCat.val() != '') {
								searchForm.setOptions({
									serviceUrl: serviceUrl + '&product_cat=' + productCat.val()
								});
							} else {
								searchForm.setOptions({
									serviceUrl: serviceUrl
								});
							}

							searchForm.hide();
							searchForm.onValueChange();
						});
					}

					$(document).on('click', function (e) {
						var target = e.target;
						if (!$(target).is('.woodmart-search-form') && !$(target).parents().is('.woodmart-search-form')) {
							$this.find('[type="text"]').devbridgeAutocomplete('hide');
						}
					});

					$('.woodmart-search-results').on('click', function (e) {
						e.stopPropagation();
					});

				});

			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Search full screen
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			searchFullScreen: function () {

				var body = $('body'),
					searchWrapper = $('.woodmart-search-full-screen'),
					offset = 0;


				body.on('click', '.search-button:not(.mobile-search-icon) > a', function (e) {

					e.preventDefault();

					if ($(this).parent().find('.woodmart-search-dropdown').length > 0) return; // if dropdown search on header builder

					if (body.hasClass('global-search-dropdown') || $(window).width() < 1024) return;

					if (isOpened()) {
						closeWidget();
					} else {
						setTimeout(function () {
							openWidget();
						}, 10);
					}
				})


				body.on("click", ".woodmart-close-search, .main-page-wrapper, .header-banner", function (event) {

					if (!$(event.target).is('.woodmart-close-search') && $(event.target).closest(".woodmart-search-full-screen").length) return;

					if (isOpened()) {
						closeWidget();
					}
				});


				var closeByEsc = function (e) {
					if (e.keyCode === 27) {
						closeWidget();
						body.unbind('keyup', closeByEsc);
					}
				};


				var closeWidget = function () {
					$('body').removeClass('woodmart-search-opened');
					searchWrapper.removeClass('search-overlap');
				};

				var openWidget = function () {
					var bar = $('#wpadminbar').outerHeight();

					var offset = 0;

					if ($('.whb-sticked').length > 0) {
						if ($('.whb-clone').length > 0)
							offset = $('.whb-sticked').outerHeight() + bar;
						else
							offset = $('.whb-main-header').outerHeight() + bar;
					} else {
						offset = $('.whb-main-header').outerHeight() + bar;
						if ($('body').hasClass('header-banner-display')) {
							offset += $('.header-banner').outerHeight();
						}
					}

					searchWrapper.css('top', offset);

					// Close by esc
					body.on('keyup', closeByEsc);

					$('body').addClass('woodmart-search-opened');
					searchWrapper.addClass('search-overlap');
					setTimeout(function () {
						searchWrapper.find('input[type="text"]').focus();
						$(window).one('scroll', function () {
							if (isOpened()) {
								closeWidget();
							}
						});
					}, 300);
				};

				var isOpened = function () {
					return $('body').hasClass('woodmart-search-opened');
				};
			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Sale final date countdown
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			countDownTimer: function () {

				$('.woodmart-timer').each(function () {
					var time = moment.tz($(this).data('end-date'), $(this).data('timezone'));
					$(this).countdown(time.toDate(), function (event) {
						$(this).html(event.strftime(''
							+ '<span class="countdown-days">%-D <span>' + woodmart_settings.countdown_days + '</span></span> '
							+ '<span class="countdown-hours">%H <span>' + woodmart_settings.countdown_hours + '</span></span> '
							+ '<span class="countdown-min">%M <span>' + woodmart_settings.countdown_mins + '</span></span> '
							+ '<span class="countdown-sec">%S <span>' + woodmart_settings.countdown_sec + '</span></span>'));
					});
				});

			},


			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * Init nanoscroller
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			nanoScroller: function () {

				if ($(window).width() < 1024 || (woodmart_settings.disable_nanoscroller == 'webkit' && (jscd.browser == 'Chrome' || jscd.browser == 'Opera' || jscd.browser == 'Safari')) || woodmart_settings.disable_nanoscroller == 'disable') return;

				$(".woodmart-scroll").nanoScroller({
					paneClass: 'woodmart-scroll-pane',
					sliderClass: 'woodmart-scroll-slider',
					contentClass: 'woodmart-scroll-content',
					preventPageScrolling: false
				});

				$('body').on('wc_fragments_refreshed wc_fragments_loaded added_to_cart', function () {
					$(".widget_shopping_cart .woodmart-scroll").nanoScroller({
						paneClass: 'woodmart-scroll-pane',
						sliderClass: 'woodmart-scroll-slider',
						contentClass: 'woodmart-scroll-content',
						preventPageScrolling: false
					});
					$(".widget_shopping_cart .woodmart-scroll-content").scroll(function () {
						$(document).trigger('wood-images-loaded');
					})
				});
			},

			/**
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 * WoodMart gradient
			 *-------------------------------------------------------------------------------------------------------------------------------------------
			 */
			gradientShift: function () {
				$('.woodmart_gradient').each(function () {
					var selector = $(this);
					var parent = selector.prev();
					parent.css('position', 'relative');
					parent.prepend(selector);
				});
			},

			/**
			*-------------------------------------------------------------------------------------------------------------------------------------------
			* Lazy loading
			*-------------------------------------------------------------------------------------------------------------------------------------------
			*/
			lazyLoading: function () {
				if (!window.addEventListener || !window.requestAnimationFrame || !document.getElementsByClassName) return;

				// start
				var pItem = document.getElementsByClassName('woodmart-lazy-load'), pCount, timer;

				$(document).on('wood-images-loaded added_to_cart', function () {
					inView();
				})

				$('.woodmart-scroll-content, .woodmart-sidebar-content').scroll(function () {
					$(document).trigger('wood-images-loaded');
				})
				// $(document).on( 'scroll', '.woodmart-scroll-content', function() {
				//     $(document).trigger('wood-images-loaded');
				// })

				// WooCommerce tabs fix
				$('.wc-tabs > li').on('click', function () {
					$(document).trigger('wood-images-loaded');
				});

				// scroll and resize events
				window.addEventListener('scroll', scroller, false);
				window.addEventListener('resize', scroller, false);

				// DOM mutation observer
				if (MutationObserver) {

					var observer = new MutationObserver(function () {
						// console.log('mutated', pItem.length, pCount)
						if (pItem.length !== pCount) inView();
					});

					observer.observe(document.body, { subtree: true, childList: true, attributes: true, characterData: true });

				}

				// initial check
				inView();

				// throttled scroll/resize
				function scroller() {

					timer = timer || setTimeout(function () {
						timer = null;
						inView();
					}, 100);

				}


				// image in view?
				function inView() {

					if (pItem.length) requestAnimationFrame(function () {
						var offset = parseInt(woodmart_settings.lazy_loading_offset);
						var wT = window.pageYOffset, wB = wT + window.innerHeight + offset, cRect, pT, pB, p = 0;

						while (p < pItem.length) {

							cRect = pItem[p].getBoundingClientRect();
							pT = wT + cRect.top;
							pB = pT + cRect.height;

							if (wT < pB && wB > pT && !pItem[p].loaded) {
								loadFullImage(pItem[p], p);
							}
							else p++;

						}

						pCount = pItem.length;

					});

				}


				// replace with full image
				function loadFullImage(item, i) {

					item.onload = addedImg;

					item.src = item.dataset.woodSrc;
					if (typeof (item.dataset.srcset) != 'undefined') {
						item.srcset = item.dataset.srcset;
					}

					item.loaded = true

					// replace image
					function addedImg() {

						requestAnimationFrame(function () {
							item.classList.add('woodmart-loaded')

							var $masonry = jQuery(item).parents('.view-masonry .gallery-images, .grid-masonry, .masonry-container');
							if ($masonry.length > 0) {
								$masonry.isotope('layout');
							}
							var $categories = jQuery(item).parents('.categories-masonry');
							if ($categories.length > 0) {
								$categories.packery();
							}

							// var $owl = jQuery(item).parents('.owl-carousel');
							// if ($owl.length > 0) {
							//     $owl.trigger('refresh.owl.carousel');
							// }

						});

					}

				}

			},
		}
	}());

})(jQuery);

jQuery(window).load(function() {
	jQuery('.wd-preloader').delay(parseInt( woodmart_settings.preloader_delay )).addClass('preloader-hide');
	jQuery('.wd-preloader-style').remove();
	setTimeout(function() {
		jQuery('.wd-preloader').remove();
	}, 200);
});

jQuery(window).on('elementor/frontend/init', function() {
	if (!elementorFrontend.isEditMode()) {
		return;
	}

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_blog.default', function($wrapper, $) {
		woodmartThemeModule.blogMasonry();
		woodmartThemeModule.blogLoadMore();
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_images_gallery.default', function($wrapper, $) {
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
		woodmartThemeModule.imagesGalleryMasonry();
		woodmartThemeModule.imagesGalleryJustified();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_product_categories.default', function($wrapper, $) {
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_slider.default', function($wrapper, $) {
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_banner_carousel.default', function($wrapper, $) {
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
		woodmartThemeModule.bannersHover();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_banner.default', function($wrapper, $) {
		woodmartThemeModule.lazyLoading();
		woodmartThemeModule.bannersHover();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_infobox_carousel.default', function($wrapper, $) {
		woodmartThemeModule.infoboxSvg();
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_infobox.default', function($wrapper, $) {
		woodmartThemeModule.infoboxSvg();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_instagram.default', function($wrapper, $) {
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_testimonials.default', function($wrapper, $) {
		woodmartThemeModule.owlCarouselInit();
		woodmartThemeModule.lazyLoading();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_3d_view.default', function($wrapper, $) {
		woodmartThemeModule.view3d();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_search.default', function($wrapper, $) {
		woodmartThemeModule.ajaxSearch();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_counter.default', function($wrapper, $) {
		woodmartThemeModule.visibleElements();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_countdown_timer.default', function($wrapper, $) {
		woodmartThemeModule.countDownTimer();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_google_map.default', function($wrapper, $) {
		woodmartThemeModule.googleMapInit();
		woodmartThemeModule.googleMap();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_image_hotspot.default', function($wrapper, $) {
		woodmartThemeModule.imageHotspot();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_popup.default', function($wrapper, $) {
		woodmartThemeModule.contentPopup();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/wd_portfolio.default', function($wrapper, $) {
		woodmartThemeModule.portfolioEffects();
		woodmartThemeModule.portfolioLoadMore();
		woodmartThemeModule.portfolioPhotoSwipe();
		woodmartThemeModule.blogMasonry();
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/global', function($wrapper, $) {
		if ($wrapper.attr('style') && $wrapper.attr('style').indexOf('transform:translate3d') === 0 && !$wrapper.hasClass('wd-parallax-on-scroll')) {
			$wrapper.attr('style', '');
		}
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/column', function($wrapper, $) {
		if ($wrapper.attr('style') && $wrapper.attr('style').indexOf('transform:translate3d') === 0 && !$wrapper.hasClass('wd-parallax-on-scroll')) {
			$wrapper.attr('style', '');
		}

		setTimeout(function() {
			woodmartThemeModule.stickyColumn();
		}, 100);
	});
});

jQuery(document).ready(function () {
	if (!wooFile) {
		woodmartThemeModule.init();
	}
});
