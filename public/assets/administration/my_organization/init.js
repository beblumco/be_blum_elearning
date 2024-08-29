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

	return {
		init: function(){
			handleLoading(); //HIDE LOADING
			handleMetisMenu(); //MENÚ INTERACTIVE CLOSE/SHOW
			handleNavigation(); //INTERACTIVING WITH MENÚ LETTERS AND ICONS
			handlePerfectScrollbar(); //SCROLL MENÚ
		}
	}
}(); 

$(()=>{
	all.init();
})
