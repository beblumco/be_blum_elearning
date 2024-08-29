function carouselReview() {
    /*  event-bx one function by = owl.carousel.js */
    jQuery('.event-bx').owlCarousel({
        loop: true,
        margin: 30,
        nav: true,
        center: true,
        autoplaySpeed: 3000,
        navSpeed: 3000,
        paginationSpeed: 3000,
        slideSpeed: 3000,
        smartSpeed: 3000,
        autoplay: false,
        navText: ['<i class="fa fa-caret-left" aria-hidden="true"></i>', '<i class="fa fa-caret-right" aria-hidden="true"></i>'],
        dots: true,
        responsive: {
            0: {
                items: 1
            },
            720: {
                items: 2
            },

            1150: {
                items: 3
            },

            1200: {
                items: 2
            },
            1749: {
                items: 3
            }
        }
    })
}
jQuery(window).on('load', function () {
    setTimeout(function () {
        carouselReview();
    }, 1000);
    // $('[data-toggle="tooltip"]').tooltip();
});

var all = function ()
{
	var handleLoading = function() 
	{
		$('#preloader').fadeOut(500);
		$('#main-wrapper').addClass('show');
	}

	var handleMetisMenu = function() 
	{
		if(jQuery('#menu').length > 0 )
			$("#menu").metisMenu();

		jQuery('.metismenu > .mm-active ').each(function(){
			if(!jQuery(this).children('ul').length > 0)
				jQuery(this).addClass('active-no-child');

		});
	}
	
	var handleNavigation = function() 
	{
		$(".nav-control").on('click', function() 
		{
			$('#main-wrapper').toggleClass("menu-toggle");

			$(".hamburger").toggleClass("is-active");
		});
	}
	
	var handlePerfectScrollbar = function() {
		if(jQuery('.deznav-scroll').length > 0)
		{
			const qs = new PerfectScrollbar('.deznav-scroll');
		}
	}

	var handleHeaderHight = function() {
		const headerHight = $('.header').innerHeight();
		$(window).scroll(function() {
			if ($('body').attr('data-layout') === "horizontal" && $('body').attr('data-header-position') === "static" && $('body').attr('data-sidebar-position') === "fixed")
				$(this.window).scrollTop() >= headerHight ? $('.deznav').addClass('fixed') : $('.deznav').removeClass('fixed')
		});
	}
	
	var handleDzScroll = function() {
		jQuery('.dz-scroll').each(function()
		{
			var scroolWidgetId = jQuery(this).attr('id');
			const ps = new PerfectScrollbar('#'+scroolWidgetId, {
			  wheelSpeed: 2,
			  wheelPropagation: true,
			  minScrollbarLength: 20
			});
		})
	}

	return {
		init: function(){
			handleLoading(); //HIDE LOADING
			handleNavigation(); //INTERACTIVING WITH MENÚ LETTERS AND ICONS
			handlePerfectScrollbar(); //SCROLL MENÚ
			handleHeaderHight(); //SCROLL SZ-SCROLL CLASS
			handleDzScroll(); //SCROLL SZ-SCROLL CLASS
			//handleMetisMenu(); //MENÚ INTERACTIVE CLOSE/SHOW
		}
	}
}(); 

$(()=>{
	all.init();
})