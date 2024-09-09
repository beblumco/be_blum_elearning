<template>
    <div class="col-md-4 col-sm-6 col-12">
        <div class="card" >

            <div class="contenedor">
                <div class="col-lg-12 d-flex justify-content-end mb-3 icono-encima">
                    <div class="dropdown">
                        <button type="button" class="btn opciones sharp mt-2" data-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a v-if="permisos.includes('ent-ele-ver_capacitacion')" class="dropdown-item" href="#" @click.prevent="OnClickToView(data_training.id)">Ver</a>
                            <a v-if="permisos.includes('ent-ele-modificar_capacitacion') && menu==2" class="dropdown-item" href="#"
                                @click.prevent="OnClickToUpdate(data_training.id_training_encrypt)">Modificar</a>
                            <a v-if="permisos.includes('ent-ele-inactivar_capacitacion') && menu==2" class="dropdown-item" href="#"
                                @click.prevent="OnClickToInactivate(data_training.id)">{{ data_training.estado == 1 ?
                                    'Inactivar' : 'Activar' }}</a>
                            <a v-if="permisos.includes('ent-ele-compartir_capacitacion') && menu==2" class="dropdown-item" href="#"
                                @click.prevent="OnClickShareTraining(data_training.id)">Compartir</a>
                            <a v-if="permisos.includes('ent-ele-eliminar_capacitacion') && menu==2" class="dropdown-item" href="#"
                                @click.prevent="OnClickDeleteTraining(data_training.id)">Eliminar</a>
                        </div>
                    </div>
                </div>
                <img class="card-img-top imagenCard" :src="`${data_training.IMAGE_PATH}`" alt="Capacitación">
                <a v-if="permisos.includes('ent-ele-iniciar_capacitacion')" href="" :style="data_training.estado != 1 ? 'cursor: not-allowed;' : ''"
                    :disabled="data_training.estado == 1 ? false : true" @click.prevent="OnClickInitTraining">
                    <i class="bi bi-play-circle-fill" :class="data_training.estado == 1 ? 'centrado' : 'centrado-off'"></i>
                    <div class="blanco"></div>
                </a>
                <div class="porcentaje texto-encima">{{ data_training.TRAINING_PERCENT }}%</div>
                <div class="puntaje">{{ (data_training.TRAINING_PERCENT < 100 ? "0" : data_training.puntos) }}/{{ data_training.puntos }} Puntos</div>
            </div>

            <div class="card-body">
                <div class="new-arrival-content position-relative titulo-card">
                    <span>{{ data_training.nombre }}</span>
                </div>
                <!-- <div class="row mb-3">
                    <div class="col-sm-3 col-4">
                        <button class="btn btn-savak btn-sm">
                            <i class="flaticon-381-earth-globe-1"></i>
                                Planeta
                        </button>
                    </div>
                    <div class="col-sm-3 col-4">
                        <button class="btn btn-savak btn-sm ml-2">
                            <i class="flaticon-381-gift"></i>
                            capacitaciones
                        </button>
                    </div>
                </div> -->
                <button
                    class="mt-2"
                    :class="data_training.estado == 1 ? 'btn btn-lg btn-savak-2' : 'btn btn-lg btn btn-secondary'"
                    :style="data_training.estado != 1 ? 'cursor: not-allowed;' : ''"
                    :disabled="data_training.estado == 1 ? false : true" @click="OnClickInitTraining" id="btnIniciar"
                    v-if="permisos.includes('ent-ele-iniciar_capacitacion')">
                    Iniciar capacitación
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: Object,
        data_training: Object,
        menu: Number //para saber si viene de mi plan o de mis capacitaciones
    },
    data() {
        return {
            permisos : JSON.parse(localStorage.getItem('permisos')),
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
        }
    },
    methods: {
        OnClickInitTraining() {
            this.$emit('listener_training', this.data_training);
        },
        async OnClickDeleteTraining(id) {
            if (this.user.id == this.data_training.id_usuario || this.user.savk_principal == 1) {
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
                                    title: "¡Éxitoso!",
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
            }else{
                toastr.warning("Esta acción solo puede ser realizada por el creador de la capacitación.");
            }
        },
        async OnClickShareTraining(id){
             try {

                let data_form = new FormData();
                data_form.append('id', id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/share-training`, {
                    method: "POST", body: data_form, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode) {
                    case 202:
                        let answer = await swal({
                            title: "Link generado",
                            text: `El link ha sido generado correctamente, puedes copiarlo.`,
                            type: "success",
                            showCancelButton: true,
                            confirmButtonText: "Copiar link",
                            cancelButtonText: "Cerrar",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });

                        if (answer) {
                            this.CopyLink(`${data}`, document.body);
                        }

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
        async OnClickToInactivate(id) {
            if (this.user.id == this.data_training.id_usuario || this.user.savk_principal == 1) {
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
                                title: "¡Éxitoso!",
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
            }else{
                toastr.warning("Esta acción solo puede ser realizada por el creador de la capacitación.");
            }
        },
        OnClickToUpdate(id) {
            if (this.user.id == this.data_training.id_usuario || this.user.savk_principal == 1) {
                window.location.href = `${this.url}capacitaciones/administracion/${id}`;
            }else{
                toastr.warning("Esta acción solo puede ser realizada por el creador de la capacitación.");
            }
        },
        OnClickToView(id) {
            this.$emit('openModalModule', id);
        },
        CopyLink(url, element_save) {
            var c = document.createElement("textarea");
            c.value = url;
            c.style.maxWidth = "0px";
            c.style.maxHeight = "0px";
            element_save.appendChild(c);

            c.focus();
            c.select();
            document.execCommand("copy");
            element_save.removeChild(c);

            toastr.success("Link de invitación copiada.");
    },
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

#btnIniciar {
    width: 100%;
}

.contenedor {
    position: relative;
    display: inline-block;
    text-align: center;
    height: 217px;
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

.puntaje{
    position: absolute;
    bottom: 0px;
    left: 0px;
    color: #145c54 !important;
    font-weight: 600;
    background-color: rgb(230 240 255 / 92%);
    padding: 5px 10px 5px 10px;
    font-size: 12px;
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
    height: 47px; /* Altura del div */
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Número máximo de líneas a mostrar */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #145c54 !important;
    font-weight: bolder;
    font-size: 15px;
}

.titulo-card:hover{
    height: auto;
    display: flex;
    overflow: auto;
    min-height: 47px;
}

.opciones{
    background-color:  rgb(230 240 255 / 92%);
    color: #145c54;
    box-shadow: none;
}

.opciones:hover{
    background-color: #145c54 ;
    color: #E6F0FF;
    box-shadow: none;
    border: solid 2px rgb(230 240 255 / 92%);
}

.btn-savak{
    background-color: transparent;
    border-color: #145c54;
    color: #145c54;
}

.btn-savak:hover{
    background-color: #145c54;
    border-color: #145c54;
    color: white;
}

.btn-savak-2{
    background-color: #145c54;
    border-color: #145c54;
    color: white;
}

.btn-savak-2:hover{
    background-color: #073d36;
    border-color: #145c54;
    color: white;
}
.imagenCard{
    width: 100%;
    height: 100%;
    object-fit: cover;
}

</style>
