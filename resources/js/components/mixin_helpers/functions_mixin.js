var loading = {}
var delay = {}
$(()=>
{
	loading = (status = true)=>
	{
		const elemLoading = $('#preloader')
		if(status)
			elemLoading.fadeIn(500);
		else
			elemLoading.fadeOut(700);
	}

	delay = (ms) => {
		return new Promise(
			resolve => setTimeout(resolve, ms)
		);
	}
})