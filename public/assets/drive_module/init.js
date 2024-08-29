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
			handleMetisMenu(); //MENÚ INTERACTIVE CLOSE/SHOW
			handleNavigation(); //INTERACTIVING WITH MENÚ LETTERS AND ICONS
			handlePerfectScrollbar(); //SCROLL MENÚ
			handleHeaderHight(); //SCROLL SZ-SCROLL CLASS
			handleDzScroll(); //SCROLL SZ-SCROLL CLASS
		}
	}
}(); 

$(()=>{
	all.init();
})
