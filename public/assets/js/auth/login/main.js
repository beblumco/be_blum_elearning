$("document").ready(function(){

});

async function OnClickLogin()
{
	try
	{
		console.log(`Uepaje`);
	}
	catch(error)
	{
		console.error(`Error al realizar el login: ${error.message}`);
	}
}

$('.dev_button_login').on("click", OnClickLogin);
