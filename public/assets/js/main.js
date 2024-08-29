var loading = {}
var delay = {}
var token = document.querySelector('meta[name="csrf-token"]').getAttribute("content")
var url = document.querySelector('meta[name="csrf-token"]').getAttribute("url")

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

	$('.input-number').on('input', function () {
		this.value = this.value.replace(/[^0-9]/g,'');
	});

    function skeletonLoader(element)
    {
        var $element = $(element);
        if ($element.hasClass('skeleton-loader')) {
            $element.removeClass('skeleton-loader');
            $element.prop("disabled", false);
        } else {
            $element.addClass('skeleton-loader');
            $element.prop("disabled", true);
        }
    }
})


function OnClickAudeed()
{
	window.open(`https://postobon.audeed.app/api/login_savk`,`_blank`);
}

window.addEventListener('DOMContentLoaded', () => {
    $('#preloader').fadeOut(500);
    $('#main-wrapper').addClass('show');
    cargarPuntos()
})

async function cargarPuntos(){
    const puntosKlaxen = document.getElementById('puntos-klaxen');
    const puntosEmpresa = document.getElementById('puntos-empresa');
    const nombreUser = document.getElementsByClassName('nombreUSer');
    const sumaPuntos = document.getElementById('sumaPuntos');
    const empresaAvatar = document.getElementById('empresaAvatar');

    const urlPrincipal= document.querySelector('meta[name="csrf-token"]').getAttribute("url")

    urlI = urlPrincipal+"capacitaciones/puntos-usuario";

    const response = await fetch(
        urlI,
    );
    const { status, data } = await response.json();

    const puntosKlaxenNum = parseInt(data['puntosKlaxen'] == null ? 0 : data['puntosKlaxen']) ;
    const puntosEmpresaNum = parseInt(data['puntosEmpresa'] == null ? 0 : data['puntosEmpresa']);
    const suma = puntosKlaxenNum + puntosEmpresaNum;

    puntosKlaxen.textContent = puntosKlaxenNum;
    puntosEmpresa.textContent = puntosEmpresaNum;

    for (let i = 0; i < nombreUser.length; i++) {
        nombreUser[i].textContent = data['nombre'];
    }
    // nombreUser.textContent = data['nombre'];

    const puntosAcumulados = document.getElementById('puntosAcumulados');
    puntosAcumulados.textContent = suma;
    sumaPuntos.textContent = suma;
    empresaAvatar.src = data['avatar'] == null ? urlPrincipal + 'img/logo_klaxen.png' : urlPrincipal +'storage/'+ data['avatar'];

    if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
    }
}
