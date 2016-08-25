(function($) {
    "use strict";

    window.azexo = $.extend({}, window.azexo);

    $('#page').css('visibility', 'hidden');
    $("#status").css('display', 'block');
    $("#preloader").css('display', 'block');
    $(function() {
        $("#preloader").trigger('before-hide');
        $('#page').css('visibility', 'visible');
        $("#status").fadeOut("slow");
        $("#preloader").fadeOut("slow");
    });

    $.fn.equalizeHeights = function() {
        var max = Math.max.apply(this, $(this).map(function(i, e) {
            return $(e).height()
        }).get());
        if (max > 0)
            this.height(max);
        return max;
    }
    $.fn.equalizeWidths = function() {
        var max = Math.max.apply(this, $(this).map(function(i, e) {
            return $(e).width()
        }).get());
        if (max > 0)
            this.width(max);
        return max;
    }
    $.fn.marginBackground = function() {
        var bg = $(this).find('> .background');
        if ($(bg).length == 0) {
            bg = $('<div class="background"></div>').prependTo(this);
            $(this).css('position', 'relative');
            $(bg).css('position', 'absolute');
            $(bg).css('top', '0');
            $(bg).css('bottom', '0');
            $(bg).css('background-color', $(this).css('background-color'));
            $(this).css('background-color', '');
            $(bg).css('background-image', $(this).css('background-image'));
            $(this).css('background-image', '');
        }
        var m = ($(this).parent().width() - $(this).width()) / 2;
        $(bg).css('left', '-' + m + 'px');
        $(bg).css('right', '-' + m + 'px');
    }
    $.fn.hierarchicalSelect = function() {
        $(this).each(function() {
            function create_select(before, node, level) {
                if (node) {
                    $(base_select).val(node.value);
                } else {
                    $(base_select).val('');
                    return false;
                }
                if (node.children.length == 0)
                    return false;
                var select = $('<select class="hierarchy-level"></select>').insertAfter(before);
                if (placeholders)
                    $(select).append('<option>' + placeholders[level] + '</option>');
                $(node.children).each(function() {
                    $('<option value="' + this.value + '">' + this.label + '</option>').appendTo(select).data('node', this);
                });
                $(select).data('after', false);
                $(select).on('change', function() {
                    function remove_after(after) {
                        if (after) {
                            remove_after($(after).data('after'));
                            if ('select2' in $.fn)
                                $(after).select2('destroy');
                            $(after).remove();
                        }
                    }
                    remove_after($(this).data('after'));
                    var after = create_select(this, $(this).find(":selected").data('node'), level + 1);
                    $(this).data('after', after);
                });
                if ('select2' in $.fn)
                    $(select).select2();
                return select;
            }
            var base_select = this;
            $(base_select).hide();
            var placeholders = false;
            if ($(base_select).data('placeholders')) {
                placeholders = $(base_select).data('placeholders').split('|');
            } else {
                if ($(base_select).find('option[value=""]'))
                    placeholders = $(base_select).find('option[value=""]').text().split('|');
            }
            var tree = {value: '', label: '', children: []};
            var path = [tree];
            var selected_path = false;
            var current_level = 0;
            $(base_select).find('option').each(function() {
                if ($(this).val() != '') {
                    var level = 0;
                    if (($(this).text().match(/^-+/g) || []).length) {
                        level = ($(this).text().match(/^-+/g) || [])[0].length;
                    }
                    var node = {value: $(this).val(), label: $(this).text().replace(/^-+/g, ''), children: []};
                    if (level > current_level) {
                        path.push(path[current_level].children[path[current_level].children.length - 1]);
                        current_level = level;
                    }
                    if (level < current_level) {
                        path.pop();
                        current_level = level;
                    }
                    path[current_level].children.push(node);
                    if ($(this).is(':selected')) {
                        selected_path = path.slice(0);
                        selected_path.push(selected_path[current_level].children[selected_path[current_level].children.length - 1]);
                        selected_path.shift();
                    }
                }
            });
            var root_select = create_select(base_select, tree, 0);
            if (selected_path) {
                var select = root_select;
                $(selected_path).each(function() {
                    if (select)
                        $(select).val(this.value).change();
                    select = $(select).data('after');
                });
            }
        });
    }

    $.fn.jRMenuMore = function() {
        $(this).each(function() {
            function alignMenu(obj) {
                var w = 0;
                var mw = $(obj).width() - 30;
                var i = -1;
                var menuhtml = '';
                $.each($(obj).children(), function() {
                    i++;
                    w += $(this).outerWidth(true);
                    if (mw < w) {
                        menuhtml += $('<div>').append($(this).clone()).html();
                        $(this).remove();
                    }
                });
                $(obj).append(
                        '<li  style="position:relative;" class="hideshow">' +
                        '<a href="javascript:void(0)">' +
                        '<i class="fa fa-angle-double-down"></i>' +
                        '</a><ul>' +
                        menuhtml +
                        '</ul></li>'
                        );
                $(obj).children("li.hideshow ul").css("top",
                        $(obj).children("li.hideshow").outerHeight(true) + "px");
                $(obj).children("li.hideshow").on('click', function() {
                    $(this).children("ul").toggle();
                });
            }
            if ($(this).width() >= $(this).parent().width()) {
                alignMenu(this);
                var robj = this;
                $(window).on('resize', function() {
                    var cobj = $($($(robj).children("li.hideshow")).children("ul")).html();
                    $(robj).append(cobj);
                    $(robj).children("li.hideshow").remove();
                    alignMenu(robj);
                });
                $(this).addClass("horizontal-responsive-menu");
            }
            else {
                $(this).addClass("horizontal-responsive-menu");
            }
        });
    };
    function initEntryGallery() {
        if ('flexslider' in $.fn) {
            $('.entry .images:not(.carousel):not(.thumbnails)').each(function() {
                var gallery = this;
                if ($(gallery).data('flexslider') == undefined) {
                    if ($(gallery).find('.image').length > 1) {
                        $(gallery).flexslider({
                            selector: '.image',
                            prevText: '',
                            nextText: '',
                            touch: true,
                            pauseOnHover: true,
                            mousewheel: false,
                            controlNav: false
                        }).show();
                    }
                }
            });
            $('.entry .images:not(.carousel).thumbnails').each(function() {
                function unique_id() {
                    return Math.round(new Date().getTime() + (Math.random() * 100));
                }
                var gallery = this;
                if ($(gallery).data('flexslider') == undefined) {
                    if ($(gallery).find('.image').length > 1) {
                        $(gallery).attr('id', unique_id());
                        var thumbnails = $('<div id="' + $(gallery).attr('id') + '-thumbnails" class="thumbnails"></div>').append('<ul class="slides"></ul>').insertAfter(gallery);
                        $(gallery).find('.image').each(function() {
                            $(this).clone().removeClass('zoom').appendTo($('<li></li>').appendTo($(thumbnails).find('.slides')));
                        });
                        var itemWidth = parseInt($(thumbnails).find('ul.slides li').css('width'), 10);
                        if (!itemWidth)
                            itemWidth = 150;
                        var itemHeight = parseInt($(thumbnails).find('ul.slides li').css('height'), 10);
                        if (!itemHeight)
                            itemHeight = 150;
                        $(thumbnails).flexslider({
                            prevText: '',
                            nextText: '',
                            animation: "slide",
                            controlNav: false,
                            animationLoop: false,
                            pauseOnHover: true,
                            slideshow: false,
                            itemWidth: itemWidth,
                            itemHeight: itemHeight,
                            direction: $(gallery).data('vertical') ? 'vertical' : 'horizontal',
                            touch: true,
                            mousewheel: false,
                            asNavFor: '#' + $(gallery).attr('id')
                        });

                        $(gallery).flexslider({
                            selector: '.image',
                            prevText: '',
                            nextText: '',
                            touch: true,
                            pauseOnHover: true,
                            mousewheel: true,
                            controlNav: false,
                            sync: '#' + $(gallery).attr('id') + '-thumbnails'
                        }).show();
                    }
                }
            });
        }
        if ('owlCarousel' in $.fn) {
            $('.entry .images.carousel').each(function() {
                var carousel = this;
                $(carousel).data('center', 'yes');
                $(carousel).data('loop', 'yes');
                initCarousel(carousel);
                if ('magnificPopup' in $) {
                    var isDragging = false;
                    $(carousel).find('.image[data-popup]:not([data-popup=""])').on('mousedown', function() {
                        $(window).on('mousemove.popup', function() {
                            isDragging = true;
                            $(window).off('mousemove.popup');
                        });
                    }).on('mouseup', function() {
                        var wasDragging = isDragging;
                        isDragging = false;
                        $(window).off('mousemove.popup');
                        if (!wasDragging) {
                            var gallery = $.makeArray($(carousel).find('.image[data-popup]:not([data-popup=""])').map(function() {
                                return {src: $(this).data('popup')};
                            }));
                            if (gallery.length > 0) {
                                var owlCarousel = $(carousel).data('owlCarousel');
                                $.magnificPopup.open({
                                    items: gallery,
                                    gallery: {
                                        enabled: true
                                    },
                                    type: 'image'
                                }, $(this).closest('.owl-item').index());
                            } else {
                                $.magnificPopup.open({
                                    items: {
                                        src: $(this).data('popup')
                                    },
                                    type: 'image'
                                });
                            }
                        }
                    });
                }
            });
        }
    }
    function initCarousel(carousel) {
        function show(carousel) {
            if ($(carousel).data('owlCarousel') == undefined) {
                while ($(carousel).find('> div:not(.item)').length) {
                    $(carousel).find('> div:not(.item)').slice(0, contents_per_item).wrapAll('<div class="item" />');
                }
                $(carousel).show();
                if (width == '')
                    width = $(carousel).width();
                var items = Math.round($(carousel).width() / width);
                if (items == 0)
                    items = 1;
                if (items > 1)
                    items = Math.ceil($(carousel).width() / width);
                if (full_width) {
                    items = 1;
                }
                if (lazy && ($(carousel).find('.item').length >= items)) {
                    $(carousel).find('.item .image.lazy').each(function() {
                        $(this).removeClass('lazy');
                        $(this).addClass('owl-lazy');
                    });
                }
                var options = {
                    items: items,
                    dotsEach: items,
                    responsive: false,
                    center: ($(carousel).data('center') == 'yes'),
                    loop: ($(carousel).data('loop') == 'yes'),
                    autoplay: true,
                    lazyLoad: lazy && ($(carousel).find('.item').length >= items),
                    autoplayHoverPause: true,
                    nav: true,
                    dots: true,
                    navText: ['', '']
                };
                if (margin > 0) {
                    options.margin = margin;
                }
                if (stagePadding > 0) {
                    options.stagePadding = stagePadding;
                }
                $(carousel).owlCarousel(options).on('translated.owl.carousel', function(event) {
                    try {
                        BackgroundCheck.refresh($(carousel).find('.owl-controls .owl-prev, .owl-controls .owl-next'));
                    } catch (e) {
                    }
                });
                $(carousel).find('.item').equalizeHeights();
                var height = $(carousel).find('.owl-item').height();
                $(carousel).find('.owl-stage').css('height', height + 'px');
                try {
                    BackgroundCheck.init({
                        targets: $(carousel).find('.owl-controls .owl-prev, .owl-controls .owl-next'),
                        images: $(carousel).find('.item .image')
                    });
                } catch (e) {
                }
            }
        }
        function is_visible(carousel) {
            var visible = true;
            $(carousel).parents().each(function() {
                var parent = this;
                if ($(parent).css('display') == 'none' && visible) {
                    visible = false;
                }
            });
            return visible;
        }
        var width = $(carousel).data('width') ? $(carousel).data('width') : '';
        var height = $(carousel).data('height') ? $(carousel).data('height') : '';
        var margin = isNaN(parseInt($(carousel).data('margin'), 10)) ? 0 : parseInt($(carousel).data('margin'), 10);
        var stagePadding = isNaN(parseInt($(carousel).data('stagePadding'), 10)) ? 0 : parseInt($(carousel).data('stagePadding'), 10);
        var full_width = $(carousel).data('full-width') == 'yes';
        var contents_per_item = $(carousel).data('contents-per-item') ? $(carousel).data('contents-per-item') : 1;
        if (!$(carousel).data('entries')) {
            $(carousel).data('entries', $(carousel).find('> div'));
        }
        var lazy = ($(carousel).find('.image.lazy').length > 0);
        if (typeof width !== typeof undefined && width !== false && typeof height !== typeof undefined && height !== false) {
            if (height != '') {
                $(carousel).find('.item .image').each(function() {
                    $(this).height(height);
                });
            }
            if (is_visible(carousel)) {
                show(carousel);
            } else {
                $(document).on('click.azexo', function() {
                    if ($(carousel).data('owlCarousel') == undefined) {
                        if (is_visible(carousel)) {
                            show(carousel);
                        }
                    }
                });
            }
        }
        return show;
    }
    function initAzexoPostList() {
        if ('owlCarousel' in $.fn) {
            $('.owl-carousel.posts-list').each(function() {
                var carousel = this;
                var show = initCarousel(carousel);
                $(carousel).closest('.posts-list-wrapper').find('> .list-header > .list-filter > .filter-term').off('click.posts-list-carousel').on('click.posts-list-carousel', function() {
                    if ($(carousel).data('owlCarousel') !== undefined) {
                        $(carousel).data('owlCarousel').destroy();
                        $($(carousel).data('entries')).detach();
                        $(carousel).empty();
                        $(carousel).append($(carousel).data('entries'));
                        if ($(this).data('term')) {
                            $(carousel).find('.filterable:not(.' + $(this).data('term') + ')').detach();
                        }
                        show(carousel);
                    }
                });
            });
        }
        $('.posts-list-wrapper > .list-header > .list-filter > .filter-term').off('click.posts-list').on('click.posts-list', function() {
            $(this).closest('.list-filter').find('.filter-term').removeClass('active');
            $(this).addClass('active');
            if ($(this).data('term')) {
                $(this).closest('.posts-list-wrapper').find('> .posts-list .filterable.' + $(this).data('term')).addClass('showed');
                $(this).closest('.posts-list-wrapper').find('> .posts-list .filterable:not(.' + $(this).data('term') + ')').removeClass('showed');
            } else {
                $(this).closest('.posts-list-wrapper').find('> .posts-list .filterable').addClass('showed');
            }
        });
        $('.posts-list-wrapper > .posts-list .filterable').addClass('showed');
    }
    function initAzexoPostMasonry() {
        if ('masonry' in $.fn) {
            $('.site-content.masonry-post').each(function() {
                var grid = this;
                var width = $(grid).find('.entry:not(.no-results) .entry-thumbnail .image[data-width]').attr('data-width');
                if (typeof width === 'undefined') {
                    width = 400;
                }
                var height = $(grid).find('.entry:not(.no-results) .entry-thumbnail .image[data-height]').attr('data-height');
                if (typeof height === 'undefined') {
                    height = 400;
                }
                var columns = Math.ceil($(grid).width() / width);
                var columnWidth = Math.floor($(grid).width() / columns);
                $(grid).find('.entry:not(.no-results)').css('width', columnWidth + 'px');
                var ratio = columnWidth / width;
                $(grid).find('.entry:not(.no-results) .entry-thumbnail .image').css('height', height * ratio + 'px');
                setTimeout(function() {
                    $(grid).masonry('destroy');
                    $(grid).masonry({
                        columnWidth: columnWidth,
                        itemSelector: '.entry:not(.no-results)'
                    });
                }, 0);
            });
        }
    }
    function initMobileMenu() {
        $(".mobile-menu-button span").on("tap click", function(e) {
            e.preventDefault();
            if ($(".mobile-menu > div > ul").is(":visible")) {
                $(".mobile-menu > div > ul").slideUp(200)
            } else {
                $(".mobile-menu > div > ul").slideDown(200)
            }
        });

        $('.mobile-menu ul.nav-menu:not(.vc) > li.menu-item.menu-item-has-children, .mobile-menu ul.sub-menu:not(.vc) > li.menu-item.menu-item-has-children').append('<span class="mobile-arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>');
        $('.mobile-menu ul.nav-menu:not(.vc) > .mega, .mobile-menu ul.sub-menu:not(.vc) > .mega').append('<span class="mobile-arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>');

        $(".mobile-menu ul > li.menu-item-has-children > span.mobile-arrow").on("tap click", function(e) {
            e.preventDefault();
            if ($(this).closest("li.menu-item-has-children").find("> ul.sub-menu").is(":visible")) {
                $(this).closest("li.menu-item-has-children").find("> ul.sub-menu").slideUp(200);
                $(this).closest("li.menu-item-has-children").removeClass("open-sub")
            } else {
                $(this).closest("li.menu-item-has-children").addClass("open-sub");
                $(this).closest("li.menu-item-has-children").find("> ul.sub-menu").slideDown(200)
            }
        });
        $(".mobile-menu > div > ul > li.mega > span.mobile-arrow").on("tap click", function(e) {
            e.preventDefault();
            if ($(this).closest("li.mega").find("> .page").is(":visible")) {
                $(this).closest("li.mega").find("> .page").slideUp(200);
                $(this).closest("li.mega").removeClass("open-sub")
            } else {
                $(this).closest("li.mega").addClass("open-sub");
                $(this).closest("li.mega").find("> .page").slideDown(200)
            }
        });
        $(".mobile-menu ul li > a").on("click", function() {
            if ($(this).attr("href") !== "http://#" && $(this).attr("href") !== "#") {
            }
        })
    }
    function initSearchForm() {
        $('.searchform').each(function() {
            var form = this;
            $(form).find('input[name="s"]').attr('placeholder', $(form).find('[type="submit"]').val()).on('keydown', function(event) {
                if (event.keyCode == 13) {
                    $(form).find('[type="submit"]').click();
                }
            });
            var toggled = false;
            $(form).find('.toggle').on('click', function() {
                $(form).find('.search-wrapper').show().find('input[name="s"]').focus();
                $(this).hide();
                toggled = true;
            });
            $(form).find('input[name="s"]').on('blur', function() {
                if (toggled) {
                    $(form).find('.toggle').show();
                    $(form).find('.search-wrapper').hide();
                    toggled = false;
                }
            });
        });

    }
    function initMegaMenu() {
        function on_hover(menu, page) {
            $(page).css('width', $(menu).closest('.container').width() + 'px');
            $(page).css('left', ($(menu).closest('.container').offset().left - $(page).parent().offset().left) + 'px');
        }
        function widget_on_hover(menu, page) {
            $(page).css('width', $(menu).closest('.container').width() - $(page).parent().outerWidth() + 'px');
        }
        $('.primary-navigation .nav-menu, .secondary-navigation .nav-menu').each(function() {
            var menu = this;
            $(menu).find('.mega:not(.compact) .page').each(function() {
                var page = this;
                $(page).parent().on('hover', function() {
                    on_hover(menu, page);
                });
                on_hover(menu, page);
                if ('imagesLoaded' in $.fn) {
                    $(menu).closest('.container').imagesLoaded(function() {
                        on_hover(menu, page);
                    });
                }
                $(window).on('resize', function() {
                    on_hover(menu, page);
                });
            });
        });
        $('.widget_nav_menu .menu').each(function() {
            var menu = this;
            $(menu).find('.page').each(function() {
                var page = this;
                $(page).parent().on('hover', function() {
                    widget_on_hover(menu, page);
                });
                widget_on_hover(menu, page);
                if ('imagesLoaded' in $.fn) {
                    $(menu).closest('.container').imagesLoaded(function() {
                        widget_on_hover(menu, page);
                    });
                }
                $(window).on('resize', function() {
                    widget_on_hover(menu, page);
                });
            });
        });
    }
    function initStickyMenu() {
        var header_main_top = 0;
        header_main_top = $('.header-main').offset().top;
        $('.site-header').imagesLoaded(function() {
            var interval = setInterval(function() {
                if (!$('.site-header').hasClass('scrolled')) {
                    header_main_top = $('.header-main').offset().top;
                    clearInterval(interval);
                }
            }, 100);
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > header_main_top) {
                    $('.site-header').addClass('scrolled');
                    if ($('nav.mobile-menu').css('display') == 'none')
                        $('.site-header .header-main').addClass('animated fadeInDown');
                } else {
                    $('.site-header').removeClass('scrolled');
                    if ($('nav.mobile-menu').css('display') == 'none')
                        $('.site-header .header-main').removeClass('animated fadeInDown');
                }
            });
        });
    }
    function initStickySidebar() {
        if ('stick_in_parent' in $.fn) {
            if ($(window).width() > 1100)
                $("#tertiary .sidebar-inner").stick_in_parent({
                    offset_top: $('.header-main').height()
                });
        }
    }
    function initImageZoom() {
        $('.image.zoom').each(function() {
            var image = this;
            var img = new Image();
            img.src = $(image).css('background-image').replace(/url\(|\)$|"|'/ig, '');
            $(img).imagesLoaded(function() {
                if (img.width > $(image).width() || img.height > $(image).height()) {
                    $(image).off('mouseenter.azexo').on('mouseenter.azexo', function() {
                        $(image).css('background-size', 'initial');
                    });
                    $(image).off('mouseleave.azexo').on('mouseleave.azexo', function() {
                        $(image).css('background-size', '');
                        $(image).css('background-position', '');
                    });
                    $(image).off('mousemove.azexo').on('mousemove.azexo', function(event) {
                        $(image).css('background-position', event.offsetX / $(image).width() * 100 + '% ' + event.offsetY / $(image).height() * 100 + '%');
                    });
                }
            });
        });
    }
    function initImageLazyLoad() {
        if ('waypoint' in $.fn) {
            $('.image.lazy').each(function() {
                var image = this;
                var waypoint_handler = function(direction) {
                    if ($(image).prop('tagName') == 'IMG') {
                        $(image).attr('src', $(image).data('src'));
                    } else {
                        $(image).css('background-image', 'url(' + $(image).data('src') + ')');
                    }
                }
                $(image).waypoint(waypoint_handler, {offset: '100%', triggerOnce: true});
                $(image).data('waypoint_handler', waypoint_handler);
            });
        }
    }
    function initLinkScrolling() {
        $('a[href*="#"].roll, .roll a[href*="#"]').on('click', function(e) {
            if (this.href.split('#')[0] == '' || window.location.href.split('#')[0] == this.href.split('#')[0]) {
                e.preventDefault();
                var hash = this.hash;
                $('html, body').stop().animate({
                    'scrollTop': $(hash).offset().top - $('.header-main').height()
                }, 2000)
            }
        });
    }
    function initCountDown() {
        if ('countdown' in $.fn) {
            $('.time-left .time').each(function() {
                var timer = this;
                if ($(timer).data('countdownInstance') == undefined) {
                    $(timer).countdown($(timer).data('time'), function(event) {
                        $(timer).find('.days .count').text(event.offset.totalDays);
                        $(timer).find('.hours .count').text(event.offset.hours);
                        $(timer).find('.minutes .count').text(event.offset.minutes);
                        $(timer).find('.seconds .count').text(event.offset.seconds);
                    });
                }
            });
        }
    }
    function initTrigger() {
        $('.trigger').each(function() {
            var trigger = this;
            //$(trigger).data('trigger-on');
            //$(trigger).data('trigger-off');
            var triggerable = null;
            if ($(trigger).is('a')) {
                triggerable = trigger;
            } else {
                triggerable = $(trigger).find('a');
            }
            $(triggerable).addClass('triggerable').off('click.azexo').on('click.azexo', function() {
                $($(trigger).data('trigger-on') + ', ' + $(trigger).data('trigger-off')).each(function() {
                    $(this).removeClass('end');
                });
                if ($(this).is('.active')) {
                    if ($(trigger).closest('.triggers').find('.triggerable').length == 0) {
                        $(this).removeClass('active');
                        $($(trigger).data('trigger-off')).removeClass($(trigger).data('trigger-class'));
                        $(document).trigger('triggered.azexo');
                    }
                } else {
                    $(trigger).closest('.triggers').find('.triggerable.active').each(function() {
                        $(this).removeClass('active');
                        $($(this).closest('.trigger').data('trigger-off')).removeClass($(trigger).data('trigger-class'));
                        $(document).trigger('triggered.azexo');
                    });
                    $(this).addClass('active');
                    $($(trigger).data('trigger-on')).addClass($(trigger).data('trigger-class'));
                    $(document).trigger('triggered.azexo');
                }
                return false;
            });
            $($(trigger).data('trigger-on') + ', ' + $(trigger).data('trigger-off')).on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(e) {
                $(this).addClass('end');
            });
        });
    }
    function initSelect() {
        if ('select2' in $.fn) {
            $('select.select2').select2();
        }
        $('select.hierarchical').hierarchicalSelect();
    }
    function initInfiniteScroll() {
        if ('infinitescroll' in $.fn) {
            $('#content.infinite-scroll').infinitescroll({
                navSelector: "nav.navigation .loop-pagination",
                nextSelector: "nav.navigation .loop-pagination a.next",
                itemSelector: "#content > .entry.post",
                loading: {
                    img: templateurl + "/images/infinitescroll-loader.svg",
                    msgText: '<em class="infinite-scroll-loading">Loading ...</em>',
                    finishedMsg: '<em class="infinite-scroll-done">Done</em>',
                },
                callback: function(arrayOfNewElems) {
                    window.azexo.refresh();
                    $('#content.infinite-scroll .image.lazy').each(function() {
                        if ($(this).data('waypoint_handler'))
                            $(this).data('waypoint_handler')();
                    });
                },
                errorCallback: function() {
                }
            });
            $('#content.infinite-scroll .image.lazy').each(function() {
                if ($(this).data('waypoint_handler'))
                    $(this).data('waypoint_handler')();
            });
            if ($('#content').is('.infinite-scroll')) {
                $('nav.navigation.paging-navigation').hide();
            }
        }
    }
    window.azexo.refresh = function() {
        initEntryGallery();
        initAzexoPostList();
        initAzexoPostMasonry();
        initImageZoom();
        initImageLazyLoad();
        initCountDown();
        initTrigger();
    };
    $(function() {
        initMobileMenu();
        initMegaMenu();
        initSearchForm();
        initStickySidebar();
        initEntryGallery();
        initStickyMenu();
        initAzexoPostList();
        initAzexoPostMasonry();
        initImageZoom();
        initImageLazyLoad();
        initLinkScrolling();
        initCountDown();
        initTrigger();
        initSelect();
        initInfiniteScroll();
        if ('fitVids' in $.fn) {
            $('.entry-video, .wpb_video_wrapper').fitVids();
        }
        $(document).ajaxComplete(function() {
            window.azexo.refresh();
        });
        $(window).on('resize', function() {
            initAzexoPostMasonry();
        });
        if ('stacktable' in $.fn) {
            //$('#content table').stacktable({myClass: 'stacked'});
        }
    });
})(jQuery);