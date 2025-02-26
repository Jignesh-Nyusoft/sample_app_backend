$(window).on('load', function() {
    $('.main-loader').hide();
    new WOW().init();
});


jQuery(document).ready(function () {
    if(jQuery(window).width() > 991){
        var header_height = jQuery('header').outerHeight();
        var stickyHeader = (jQuery('header').offset().top + header_height);
        jQuery(window).scroll(function(){
            if( jQuery(window).scrollTop() > stickyHeader ) {
                jQuery('header').addClass('stickyheader');
                jQuery('header').next('.page-height').css('padding-top', header_height+40);
            } else {
                jQuery('header').removeClass('stickyheader');
                jQuery('header').next('.page-height').css('padding-top', '');
            }
        });
    }
    jQuery(".toggle-btn").click(function(){
      jQuery("body").toggleClass("menu-open");
    });
    jQuery('.sidebar_overley').click(function (event) {
        // jQuery('body').removeClass('sidebar_open');
        jQuery('body').removeClass('menu-open');
    });
    jQuery(".menu li a").on("click", function(event) {
       jQuery("body").removeClass("menu-open");
    });

    /***DAshboard Sidebar***/
    jQuery('.has_sub_menu > a').click(function(){
        jQuery(this).next('.sub_menu').slideToggle();
        if(jQuery(this).parent('.has_sub_menu').hasClass('open')){
            jQuery(this).parent('.has_sub_menu').removeClass('open');
        } else {
            jQuery(this).parent('.has_sub_menu').addClass('open');
        }
    });
    /***/
    jQuery('.offcanvas-btn').click(function(){
        jQuery('body').toggleClass('dashboard-sidebar-wrap')
    });

    /******After Login MEnu*****/
     jQuery('.nav-user-linkbtn').click(function(){
        jQuery(".nav-user-links").not(jQuery(this).next()).removeClass('open-nav-box');
        jQuery(this).next('.nav-user-links').toggleClass('open-nav-box');
    });

    //default 5000 
    function noty_alert(type="success", text="Success!", duration=4000){

        new Noty({
        text: text,//'<strong>Warning!</strong> <br /> Best check yo self, you\'re not looking too good.',
        type: type,
        timeout: duration,
        animation: {
            open: function (promise) {
                var n = this;
                var Timeline = new mojs.Timeline();
                var body = new mojs.Html({
                    el        : n.barDom,
                    x         : {500: 0, delay: 0, duration: 500, easing: 'elastic.out'},
                    isForce3d : true,
                    onComplete: function () {
                        promise(function(resolve) {
                            resolve();
                        })
                    }
                });

                var parent = new mojs.Shape({
                    parent: n.barDom,
                    width      : 200,
                    height     : n.barDom.getBoundingClientRect().height,
                    radius     : 0,
                    x          : {[150]: -150},
                    duration   : 1.2 * 500,
                    isShowStart: true
                });

                n.barDom.style['overflow'] = 'visible';
                parent.el.style['overflow'] = 'hidden';

                var burst = new mojs.Burst({
                    parent  : parent.el,
                    count   : 10,
                    top     : n.barDom.getBoundingClientRect().height + 75,
                    degree  : 90,
                    radius  : 75,
                    angle   : {[-90]: 40},
                    children: {
                        fill     : '#EBD761',
                        delay    : 'stagger(500, -50)',
                        radius   : 'rand(8, 25)',
                        direction: -1,
                        isSwirl  : true
                    }
                });

                var fadeBurst = new mojs.Burst({
                    parent  : parent.el,
                    count   : 2,
                    degree  : 0,
                    angle   : 75,
                    radius  : {0: 100},
                    top     : '90%',
                    children: {
                        fill     : '#EBD761',
                        pathScale: [.65, 1],
                        radius   : 'rand(12, 15)',
                        direction: [-1, 1],
                        delay    : .8 * 500,
                        isSwirl  : true
                    }
                });

                Timeline.add(body, burst, fadeBurst, parent);
                Timeline.play();
            },
            close: function (promise) {
                var n = this;
                new mojs.Html({
                    el        : n.barDom,
                    x         : {0: 500, delay: 10, duration: 500, easing: 'cubic.out'},
                    skewY     : {0: 10, delay: 10, duration: 500, easing: 'cubic.out'},
                    isForce3d : true,
                    onComplete: function () {
                        promise(function(resolve) {
                            resolve();
                        })
                    }
                }).play();
            }
        }
    }).show();  
    }
})
