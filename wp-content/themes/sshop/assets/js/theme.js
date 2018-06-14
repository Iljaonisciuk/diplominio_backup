
jQuery( document).ready( function( $ ){
    // Main navigation
    var $window = $(window);
    var $body = $( 'body' );
    var menu_break_point = 720;
    var header_elements = [ '#site-branding', '#site-navigation-right' ];
    var header = $( '#site-header' );
    var main_nav = $( '#site-navigation', header );
    var main_nav_ul = $( '.menu', main_nav );
    var menu_toggle_icon = $( '.menu-toggle', header );
    var main_nav_more = $( '<li class="nav-more menu-item-has-children"><a href="#"><i class="fa fa-ellipsis-h"></i></a><ul style="max-height: 99999px; visibility: visible" class="sub-filters"></ul></li>' );
    main_nav.find( '.menu').append( main_nav_more );

    main_nav_ul.find( 'li').each( function(  ){
        var li = $( this );
        var btn = $( "<span class='child-toggle'></span>" );
        if ( $( '> ul', li ).length > 0 ) {
            $( btn ).insertAfter( $( ' > a', li ) );
        }

        btn.on( 'click', function( e ){
            e.preventDefault();
            li.toggleClass( 'child-open' );
        } );

    } );

    function setupMobileHeader(){
        var ww = $( window ).width();
        header.append( $( '#site-navigation-right' ) );
        $( '#site-branding').css( 'width', '' );
        var brand_w = $( '#site-branding').outerWidth();
        var rw =  $( '#site-navigation-right').outerWidth();
        var t_has_label = menu_toggle_icon.outerWidth();
        menu_toggle_icon.addClass( 'hide-label' );
        var t_no_label = menu_toggle_icon.outerWidth();
        if ( ww > brand_w + rw + t_has_label ) {
            // Can show nav label
            if ( menu_toggle_icon )
            menu_toggle_icon.removeClass( 'hide-label' );
            return true;
        }

        if (  ww >= brand_w + rw + t_no_label ) {
            // Can show nav with no label
            menu_toggle_icon.addClass( 'hide-label' );
            return true;
        }

        if ( ww < brand_w + t_no_label + rw  ) {
            menu_toggle_icon.addClass( 'hide-label' );
            $( '#primary-menu-wrapper' ).prepend( $( '#site-navigation-right' ) );
            $( '#site-branding').css( 'width', ww - t_no_label );
            return true;
        }

    }

    var setupHeader = function(){
        var ww = $( window).width();
        var hw = header.width();
        var _w = 0;

        $( '> ul > li', main_nav_more ).each( function(){
            main_nav_ul.append( $( this ) );
        });

        if ( ww < menu_break_point ) {
            main_nav_more.hide();
            setupMobileHeader();
            return false;
        } else {
            setupMobileHeader();
        }

        var more_width = main_nav_more.outerWidth();

        $.each( header_elements, function( index, id ) {
            var el = $( id );
            _w +=  el.outerWidth();
        } );

        var nav_width = hw - _w;

        var w = more_width;
        $( '> li', main_nav_ul ).not('.nav-more').each( function(){
            var width = $( this ).outerWidth();

            if ( nav_width < w + width ) {
                var li = $( this );
                main_nav_more.find( '.sub-filters' ).append( li );
            }
            w += width;
        } );

        main_nav_ul.append( main_nav_more );
        main_nav.removeClass( 'no-js' );
        if (  $( '> ul > li', main_nav_more).length > 0 ) {
            main_nav_more.show();
        } else {
            main_nav_more.hide();
        }

    };

    setupHeader();
    $( window).on( 'resize', function(){
        setupHeader();
    } );

    // Sticky Header
    if ( SShop.header_sticky ) {
        header.wrap('<div id="site-header-wrap"></div>');
        var headerP = header.parent();
        headerP.css({'height': header.height(), 'display': 'block'});
        $window.on('resize', function () {
            headerP.removeAttr('style');
            headerP.css({'height': header.height(), 'display': 'block'});
        });
        $window.on("scroll", function () {
            var t = $window.scrollTop();
            var headerPos = 0;
            var barFixed = false;
            if ($('#wpadminbar').length > 0) {
                headerPos += $('#wpadminbar').height();
                barFixed = ( $('#wpadminbar').css('position') === 'fixed' ) ? true : false;
            }
            if (t > 0) {
                $body.addClass('header-sticky');
                if (!barFixed) {
                    if (headerPos >= t) {
                        headerPos = headerPos - t;
                    } else {
                        headerPos = 0;
                    }
                }
                header.css({top: headerPos});
            } else {
                $body.removeClass('header-sticky');
                header.css({top: ''});
            }
        });
    }


    // menu-mobile
    menu_toggle_icon.on( 'click', function( e ){
        e.preventDefault();
        $( '#site-navigation #primary-menu-wrapper').toggleClass( 'menu-open' );
    } );

    //-------------------------------------


    $("#secondary").stick_in_parent();


    // Layout tabs widget
    jQuery( '.layout-tabs').each( function(){
        var tab = $( this );
        var headingLabel = $( '.widget-title', tab );
        var filter = $( '.nav-tabs-filter', tab );
        var subLi = $( '.subfilter-more', filter );
        var ajaxUrl = tab.data( 'ajax' );
        var instance = tab.data( 'instance' ) || {};
        instance.action = 'sshop_tabs_content_ajax';

        var number_item = tab.data( 'number' ) || 5;

        var slickArgs = {
            accessibility: false,
            slidesToShow: number_item,
            slidesToScroll: 1,
            autoplay: false,
            infinite: true,
            autoplaySpeed: 2000,
            prevArrow: $( '.slider-prev', tab ),
            nextArrow: $( '.slider-next', tab ),
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: number_item,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 940,
                    settings: {
                        slidesToShow: (  number_item -1 > 4 ) ? 4 :  number_item -1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 720,
                    settings: {
                        slidesToShow: (  number_item -1 > 3 ) ? 3 :  number_item -1,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow:  ( number_item - 1 > 2 ) ? 2 :  number_item - 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 460,
                    settings: {
                        slidesToShow:  1,
                        slidesToScroll: 1
                    }
                }
            ]
        };

        var setup = function( ){
            // Reset all sub filters
            $( '.subfilter-more ul li', filter).each( function(){
                filter.append( $( this ) );
            });
            var headingWidth = 0;
            if ( headingLabel.length > 0 ){
                headingWidth = headingLabel.outerWidth();
            }
            var tabWidth = tab.innerWidth();

            var w = 0;
            var filterW = tabWidth - headingWidth - 20 - 65; // slick nav width
            var more_width = $( '.subfilter-more .a-more', tab).outerWidth();
            filterW = filterW - more_width;

            $( 'li', filter).not('.subfilter-more').each( function(){
                var width = $( this ).outerWidth();

                w += width;
                if ( filterW < w + width ) {
                    var li = $( this );
                    subLi.find( '.sub-filters' ).append( li );
                }
            } );

            // Move sub filter to the end of list
            filter.append( subLi );
            var l = $( '.subfilter-more ul li', filter ).length;

            if ( l > 0 ) {
                $( '> li', filter ).eq ( $( '> li', filter ).length - 2).removeClass( 'last' );
                subLi.show();
            } else {
                $( '> li', filter ).eq ( $( '> li', filter ).length - 2).addClass( 'last');
                subLi.hide();
            }

            if ( $( 'li.active', filter).length <= 0 ) {
                $( 'li', filter).eq( 0 ).addClass( 'active' );
            }

            $('.tabs-content-items', tab ).slick( slickArgs );

        };

        setup();

        $( window).resize( function(){
            setup();
        } );

        // Ajax load posts
        tab.on( 'click', '.nav-tabs-filter a, .tab-paging-wrap .tab-paging', function( e ){
            e.preventDefault();
            var a  = $( this );
            var id = a.data( 'term-id' ) || '';
            var paged = 1;
            var canSend = false;

            if ( id ) {
                canSend = 1;
                $( 'a, li', filter ).removeClass( 'active' );
                a.parent().addClass( 'active' );
            }

            if (  a.hasClass( 'tab-paging' ) ) {
                if ( ! a.hasClass( 'disable' ) ) {
                    canSend = 2;
                    paged = a.attr( 'data-paged' ) || '';
                }
            }

            if ( canSend ) {
                var contentLayout = $('.tabs-layout-contents', tab);
                //var ch = contentLayout.height();
                //contentLayout.css( 'min-height', ch );
                var spinner = '<div class="tab-loading"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>';

                $( '.tab-loading', contentLayout).remove();
                spinner = $(spinner);
                contentLayout.css( 'height', contentLayout.outerHeight() );
                tab.addClass('loading');
                contentLayout.append(spinner);

                //return  false;

                tab._lastSent = instance;
                if (id) {
                    tab._lastSent.viewing = id;
                }
                tab._lastSent.paged = paged;

                if (tab._jqxhr) {
                    tab._jqxhr.abort();
                }


                var appendTabContent = function (html, delay ) {

                    if ( $('.tabs-content-items', tab ).length > 0 ) {
                        try {
                            $('.tabs-content-items', tab ).slick('unslick');
                        } catch ( e ) {

                        }
                    }

                    var $html = $( '<div>'+html+'</div>' );
                    $('.tabs-item-inside', $html ).addClass('animate');
                    if (  typeof delay === "undefined" ) {
                        delay = 500;
                    }
                    spinner.remove();
                    setTimeout(function () {
                        contentLayout.addClass('animate');
                        var $content = $( '.tabs-layout-contents', $html).html();
                        contentLayout.html( $content );
                        var t = 0;

                        if ( $( '.tabs-content-items .tabs-item-inside', tab).length > 0 ) {
                            $('.tabs-content-items', tab ).slick( slickArgs );
                        }

                        spinner.animate({
                            opacity: 0,
                        }, t - 100, function () {
                            // Animation complete.
                            tab.removeClass('loading');

                            contentLayout.css( 'height', '' );
                        });

                    }, delay );


                };

                var cacheData = a.data( '_ajaxData' ) || '';

                if ( canSend == 1 &&  cacheData ) {
                    contentLayout.removeClass('animate');
                    appendTabContent( cacheData, 0 );
                    tab._jqxhr = false;
                } else {

                    tab._jqxhr = $.get(ajaxUrl, tab._lastSent, function (html) {
                        //contentLayout.html('');
                        contentLayout.removeClass('animate');
                        if ( canSend == 1) {
                            a.data( '_ajaxData', html );
                        }
                        appendTabContent(html);
                        tab._jqxhr = false;

                    })
                        .done(function () {
                            tab._jqxhr = false;
                            //tab.removeClass( 'loading' );
                            //spinner.remove();
                        })
                        .fail(function () {
                            tab._jqxhr = false;
                            //tab.removeClass( 'loading' );
                            //spinner.remove();
                        })
                        .always(function () {
                            tab._jqxhr = false;
                            //tab.removeClass( 'loading' );
                            //spinner.remove();
                        });
                }
            }


        } );

    } );

} );
