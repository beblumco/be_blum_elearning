<template>
    <div class="col-sm-4">
        <div class="card">

            <div class="contenedor">
                <img class="card-img-top imagenCard" :src="`${data_training.IMAGE_PATH}`" alt="Capacitación">
                <div class="porcentaje texto-encima">{{ (data_training.TRAINING_PERCENT == undefined ? "0" : data_training.TRAINING_PERCENT) }}%</div>
            </div>

            <div class="card-body">
                <div class="new-arrival-content position-relative titulo-card mb-2">
                    <spam >{{ data_training.nombre }}</spam>
                </div>
                <div class="new-arrival-content position-relative contenido-card mb-2">
                    <spam ><strong>{{ data_training.nombre_usuario }}</strong> - {{ data_training.cargo_usuario }} </spam>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-7">
                        <div class="col-sm-12 contenido-card-puntos">
                            <i class="icon-color bi bi-calendar4-week mr-1"></i> {{ fecha_sola }}
                            <!-- Lunes, 15 junio 2023 -->
                        </div>
                        <div class="col-sm-12 contenido-card-puntos ">
                            <i class="icon-color bi bi-clock mr-1"></i> {{ hora_sola }} - {{ hora_final }}   ({{ data_training.tiempo_minutos }} min)
                            <!-- 08:30 AM - 09:30 AM -->
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="col-sm-12 contenido-card-puntos">
                            <i class="icon-color bi bi-currency-dollar mr-1"></i> ${{ data_training.precio }}
                        </div>
                        <div class="col-sm-12 contenido-card-puntos">
                            <i class="icon-color bi bi-ticket-perforated-fill mr-1"></i> {{ data_training.puntos }} Puntos
                        </div>
                    </div>
                </div>
                <button
                    :class="data_training.estado == 1 ? 'btn btn-lg btn-savak-2' : 'btn btn-lg btn btn-secondary'"
                    :style="data_training.estado != 1 ? 'cursor: not-allowed;' : ''"
                    :disabled="data_training.estado == 1 ? false : true" @click="OnClickConfirmInscription" id="btnAgendarWebinar">
                    Inscribirme
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas  } from "../../../../../../public/assets/js/functions.js";
export default {
    async mounted() {
        this.fecha_sola = this.data_training.fecha_realizacion.split(' ')[0];
        this.fecha_sola = this.fechaFormateada(this.fecha_sola);
        this.hora_sola = this.data_training.fecha_realizacion.split(' ')[1];
        this.hora_sola = this.hora_sola.split(':')[0] +':'+this.hora_sola.split(':')[1];
        this.hora_final = this.horaConSuma(this.hora_sola, this.data_training.tiempo_minutos)
        await this.guiaGetAll();
        this.CreateTour(this.guias);
        this.tour.start();
    },
    props: {
        data_training: Object
    },
    data() {
        return {
            guias: [],
            guiasSecundarias: [],
            tour: null,
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            fecha_sola: '',
            hora_sola: '',
            hora_final: '',
        }
    },
    methods: {
        guiaGetAll,
        saveVisualizacionGuia,
        CreateTour,
        fechaFormateada(fechaSola) {
            const meses = [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ];

            const fechaSplit = fechaSola.split('-'); // Separar la fecha en año, mes y día
            const dia = fechaSplit[2]; // Obtener el día como cadena
            const mes = meses[parseInt(fechaSplit[1], 10) - 1]; // Obtener el mes como cadena
            const anio = fechaSplit[0]; // Obtener el año como cadena

            const fechaFormateada = `${dia} ${mes} ${anio}`;

            return fechaFormateada;
        },
        horaConSuma(horaOriginal, minSuma) {
            const horaOriginalSplit = horaOriginal.split(':'); // Separar la hora en horas, minutos y segundos

            let hora = parseInt(horaOriginalSplit[0], 10); // Obtener las horas como entero
            let minutos = parseInt(horaOriginalSplit[1], 10); // Obtener los minutos como entero

            // Sumar 30 minutos
            minutos += minSuma;
            if (minutos >= 60) {
                // Si los minutos superan o igualan a 60, ajustamos las horas y los minutos restantes
                hora += Math.floor(minutos / 60);
                minutos %= 60;
            }

            // Formatear la nueva hora con ceros a la izquierda si es necesario
            const horaFormateada = hora.toString().padStart(2, '0');
            const minutosFormateados = minutos.toString().padStart(2, '0');

            // Construir la nueva cadena de hora en formato "HH:mm:ss"
            const horaConSuma = `${horaFormateada}:${minutosFormateados}`;

            return horaConSuma;
        },

        async OnClickConfirmInscription(){
            let answer = await swal({
                title: "Inscripción a webinar",
                text: "¿Estas seguro de inscribirte al Webinar?",
                type: "danger",
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: "No",
                confirmButtonColor: '#1f3352',
                cancelButtonColor: '#ff7f00',
                allowOutsideClick: false
            });

            if (answer.value) {
                let data_form = new FormData();
                data_form.append('id_training', this.data_training.id);

                // loading();
                let rs = await fetch(`${this.url}capacitaciones/webinars/save_asistentes`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                // loading(false);

                const { responseCode, message, data } = rd;

                if (responseCode == 200) {
                    swal({
                        title: "¡Exitoso!",
                        text: "Te has registrado correctamente al Webinar, muy pronto a su correo le llegará toda la información para su ingreso.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        confirmButtonColor: '#1f3352',
                        allowOutsideClick: false
                    });
                }else if (responseCode == 203){
                    swal({
                        text: "No te puedes volver a registrar, ya te has registrado anteriormente al Webinar.",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        confirmButtonColor: '#1f3352',
                        allowOutsideClick: false
                    });
                }else{
                    swal({
                        title: "¡Fallo!",
                        text: "No fue posible realizar registro, consulta con el administrador.",
                        type: "danger",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        confirmButtonColor: '#1f3352',
                        allowOutsideClick: false
                    });
                }
            }
        },

        OnClickInitTraining() {
            this.$emit('listener_training', this.data_training);
        },
        async OnClickDeleteTraining(id) {
            try {
                let answer = await swal({
                    title: "¿Estás seguro?",
                    text: `Recuerda que una vez eliminada la capacitación no se podrá recuperar`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si, eliminar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });

                if (answer.value) {
                    let data_form = new FormData();
                    data_form.append('id_training', id);

                    loading();
                    let rs = await fetch(`${this.url}capacitaciones/administracion/delete_training_administration`, {
                        method: "POST", body: data_form, headers: {
                            'X-CSRF-TOKEN': this.token
                        }
                    });
                    let rd = await rs.json();
                    loading(false);

                    const { responseCode, message, data } = rd;

                    switch (responseCode) {
                        case 206:
                            this.$emit('reload');

                            swal({
                                title: "¡Exitoso!",
                                text: message,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Aceptar",
                                confirmButtonColor: '#1f3352',
                                allowOutsideClick: false
                            });


                            break;

                        case 406:
                            swal({
                                title: "¡Falló!",
                                text: message,
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Aceptar",
                                confirmButtonColor: '#1f3352',
                                allowOutsideClick: false
                            });
                            break;

                        default:
                            break;
                    }
                }
            }
            catch (error) {
                console.error(`Error al crear capacitación: ${error.message}`);
                loading(false);
            }
        },

        async OnClickToInactivate(id) {
            try {

                let data_form = new FormData();
                data_form.append('id_training', id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/change_status_training`, {
                    method: "POST", body: data_form, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode) {
                    case 202:
                        this.$emit('reload');
                        swal({
                            title: "¡Exitoso!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: '#1f3352',
                            allowOutsideClick: false
                        });


                        break;

                    case 406:
                        swal({
                            title: "¡Falló!",
                            text: message,
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: '#1f3352',
                            allowOutsideClick: false
                        });
                        break;

                    default:
                        break;
                }

            } catch (error) {

            }
        },
        OnClickToUpdate(id) {
            window.location.href = `${this.url}capacitaciones/administracion/${id}`;
        },
        OnClickToView(id) {
            this.$emit('openModalModule', id);
        }
    },

}
</script>

<style scoped>
.content-image {
    /* width: 20vh; */
    max-height: 15vh;
}

.content-a {
    width: 100%;
}

#btnAgendarWebinar {
    width: 100%;
}

.contenedor {
    position: relative;
    display: inline-block;
    text-align: center;
    height: 220px;
}

/* .contenedor::before {
    border-radius: 11px;
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 50%;
    background: linear-gradient(to bottom, rgb(255 255 255 / 47%), rgba(0, 0, 0, 0));
    pointer-events: none;
} */

.icon-color{
    color: #ff7f00;
}

.centrado {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 50px;
    color: #ff7f00;
    z-index: 2;
}

.centrado-off {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 50px;
    color: #707070;
    z-index: 2;
}

.blanco{
    position: absolute;
    top: 49%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 50px;
    background-color: white;
    height: 30px;
    width: 30px;
}

.texto-encima {
    position: absolute;
    top: 10px;
    left: 10px;
}

.porcentaje {
    background-color: #ff7f00;
    color: white !important;
    padding: 5px 10px 5px 10px;
    border-radius: 7px;
    margin-top: 8px;
    margin-left: 8px;
    font-weight: bold;
}

.icono-encima{
    position: absolute;
    top: 10px;
}

.titulo-card{
    height: 25px; /* Altura del div */
    display: -webkit-box;
    -webkit-line-clamp: 1; /* Número máximo de líneas a mostrar */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #002f54 !important;
    font-weight: bolder;
    font-size: 15px;
}

.titulo-card:hover{
    height: auto;
    display: flex;
    overflow: auto;
    min-height: 25px;
}

.contenido-card{
    height: 25px; /* Altura del div */
    display: -webkit-box;
    -webkit-line-clamp: 1; /* Número máximo de líneas a mostrar */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis !important;
    color: #002f54 !important;
    font-size: 14px;
}

.contenido-card:hover{
    height: auto;
    display: flex;
    overflow: auto;
    min-height: 25px;
}

.contenido-card-puntos{
    text-overflow: ellipsis;
    color: #002f54 !important;
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 9px;
}



.opciones{
    background-color:  #e6f0ff8f;
    color: #002f54;
    box-shadow: none;
}

.opciones:hover{
    background-color: #002f54 ;
    color: #E6F0FF;
    box-shadow: none;
}

.btn-savak{
    background-color: transparent;
    border-color: #002f54;
    color: #002f54;
}

.btn-savak:hover{
    background-color: #002f54;
    border-color: #002f54;
    color: white;
}

.btn-savak-2{
    background-color: #002f54;
    border-color: #002f54;
    color: white;
}

.btn-savak-2:hover{
    background-color: #003054e8;
    border-color: #002f54;
    color: white;
}
.imagenCard{
    width: 100%;
    height: 100%;
    object-fit: cover;
}

</style>
