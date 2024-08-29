//IMPORTANTE USAR v-model.lazy en el componente para que la actualización del valor se realice después del evento input
import { useShepherd } from 'vue-shepherd'

export function validarNumeros(event) {
    // Obtener el valor actual del campo de entrada
    let valor = event.target.value;

    // Filtrar y mantener solo los caracteres numéricos
    let numeros = valor.replace(/[^\d]/g, '');

    // Actualizar el valor en el campo de entrada
    event.target.value = numeros;
}

export function  isValidEmail(email) {
    // Expresión regular para validar el formato del correo electrónico
    const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    !emailRegex.test(email) ? toastr.warning("Por favor, ingrese una dirección de correo electrónico válida.") : null;
    return emailRegex.test(email);
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//----------------------GUIAS---------------------------------
//IMPORTANTE EN EL COMPONENTE DEBE ESTAR CREADO LA VARIABLE url, token, guias Y SI HAY GUIAS SECUNDARIAS(NO SE ACTIVAN AL INICIAR EJ: MODALES) guiasSecundarias
//ESTA FUNCIÓN HACE UN LLAMADO PARA TRAER TODAS LAS GUÍAS QUE NO HA VISTO UN USUARIO
export async function guiaGetAll(){
    let rs = await fetch(`${this.url}get_guia`, { method: "GET",  headers: {
        'X-CSRF-TOKEN': this.token
    }});
    let rd = await rs.json();

    const { responseCode, data } = rd;
    switch (responseCode)
    {
        case 206:
            data.forEach(element => {
                const existeId = document.getElementById(`${element.id_elemento}`);
                if (existeId) {
                    if (element.tipo == 1) {
                        this.guias.push(element) //GUARDA LAS GUÍAS PRINCIPALES
                    }else{
                        this.guiasSecundarias.push(element) //GUARDA LAS GUÍAS SECUNDARIAS
                    }
                }
            });
            break;

        default:
            break;
    }
}

//HACE EL LLAMADO PARA GUARDAR LA GUÍA EN LA BD CON SU RESPECTIVO USUARIO
export async function saveVisualizacionGuia(id_guia){
    let form_data  = new FormData
    form_data.append('id_guia', id_guia)
    let rs = await fetch(`${this.url}create_guia_visualizada`, { method: "POST", body: form_data,  headers: {
        'X-CSRF-TOKEN': this.token
    }});
    let rd = await rs.json();

    const { responseCode } = rd;
    switch (responseCode)
    {
        case 201:
            break;

        default:
            console.log("Ocurrió un problema guardando la guia visualizada");
            break;
    }
}

//CREA UN NUEVO ARREGLO Y LO DEVUELVE DE LOS IDS DE ELEMENTOS QUE NECESITAMOS MOSTRAR EN LA GUÍA, ESTOS SON SECUNDARIOS.
//EL LLAMADO SE DEBE HACER CUANDO SE EJECUTE EL MODAL O DONDE NECESITEMOS MOSTRAR LA GUÍA. SE DEJA EJEMPLO DE COMO LLAMAR
//         let guiasEspecificas = await this.guiasEspecificas(['button-copiar-link']);
//         this.CreateTour(guiasEspecificas);
//         this.tour.start();
export function guiasEspecificas(ids_elementos){
    let guiasEspecificas = []
    this.guiasSecundarias.forEach(guia => {
        ids_elementos.forEach(element => {
            if (guia.id_elemento === element) {
                guiasEspecificas.push(guia)
            }
        });
    });
    return guiasEspecificas
}


//CREAMOS EL TOUR, VACIAMOS LA VARIABLE PARA VALIDAR QUE NO TENGA PASOS ANTERIORES Y CREAMOS PASOS CON EL ARREGLO ENVIADO
export async function CreateTour(guias)
{
    this.tour = null;
    this.tour = useShepherd({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: 'shadow-md bg-purple-dark',
            scrollTo: false
        }
    });

    guias.forEach((guia, index) => {
        const isLastElement = index === guias.length - 1; //VALIDA SI ES EL ULTIMO ELEMENTO DEL ARREGLO

        this.tour.addStep({
            id: "step_number_indicators_"+guia.id,
            title: `<b>${guia.titulo}</b>`,
            text: guia.descripcion,
            attachTo: { element: document.getElementById(`${guia.id_elemento}`), on: guia.posicion_mensaje },
            classes: 'example-step-extra-class',
            buttons: [
                {
                    text: isLastElement ? 'Finalizar' : 'Siguiente',
                    action: () => {
                        if (isLastElement) {
                            this.tour.complete(); //SI ES EL ULTIMO REALIZA LA ACCIÓN DE COMPLETADO
                        } else {
                            this.tour.next();
                        }
                        this.saveVisualizacionGuia(guia.id) //HACE EL LLAMADO PARA GUARDAR EL REGISTRO EN LA BD
                    },
                    classes: "btn btn-primary"
                }
            ],
            when: {
                show: function() {
                    window.scrollTo(0, 0);
                }
            },
            cancelIcon: {
                enabled: false,
                label: "aria-label"
            }
        });
    });
    // this.tour.addStep({
    //     id: "step_number_indicators_one",
    //     title: `<b>Esta es tu página de inicio</b>`,
    //     text: 'Aquí podrás encontrar todas las cifras de ahorro y de la propuesta de valor',
    //     attachTo: { element: document.getElementById("dev_container_menu_1").parentNode, on: 'right-start' },
    //     classes: 'example-step-extra-class',
    //     buttons: [
    //         {
    //             text: 'Siguiente',
    //             action: this.tour.next,
    //             classes: "btn btn-primary"
    //         }
    //     ],
    //     when: {
    //         show: function() {
    //             window.scrollTo(0, 0);
    //         }
    //     },
    //     cancelIcon: {
    //         enabled: false,
    //         label: "aria-label"
    //     }
    // });
}

export function skeletonLoader(elemento){
    const elements = document.querySelectorAll(`${elemento}`);

    elements.forEach(element => {
        element.classList.toggle('skeleton-loader');
        element.disabled = element.classList.contains('skeleton-loader');
    });
}
