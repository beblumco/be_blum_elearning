<template>
    <div>
        <div class="row bg_dark text-light">
            <div class="col-lg-9 mt-2 mb-2">
                <div class="embed-responsive embed-responsive-16by9" v-show="moduleView.ruta != 'noVideo'">
                    <YoutubeApi :videoId="moduleView.ruta" @videoFinalizo="startCountdown" ref="youtubePlayer"></YoutubeApi>
                    <div class="div2 d-flex align-items-center justify-content-center" v-if="viewContador == true">
                        <div class="nextModule">
                            <i :class="'fa fa-hourglass-'+iconCargando" aria-hidden="true"></i>
                            Siguiente módulo en: {{ contador }}
                        </div>
                    </div>
                </div>
                <div v-show="moduleView.ruta == 'noVideo'">
                    <img :src="`${url}assets/images/training/video_no_encontrado.png`" class="img-fluid" alt="Responsive image">
                </div>
            </div>
            <div class="col-lg-3 mt-2">
                <div class="row text-center">
                    <div class="col-lg-4 pb-1 pr-1 pl-1" :class="{ 'activo': windowsSelect === 1 }">
                    <!-- <div class="col-lg-4 offset-lg-1 pb-1" :class="{ 'activo': windowsSelect === 1 }"> -->
                        <a href="" @click.prevent="OnClickView(1)">Módulos</a>
                    </div>
                    <div class="col-lg-4 pb-1 pr-1 pl-1" :class="{ 'activo': windowsSelect === 2 }">
                    <!-- <div class="col-lg-5 offset-lg-1 pb-1" :class="{ 'activo': windowsSelect === 2 }"> -->
                        <a href="" @click.prevent="OnClickView(2)">
                            Recursos
                            <div class="rounded-circle countResorces">{{ moduleView.RECURSOS }}</div>
                        </a>

                    </div>
                    <div class="col-lg-4 pb-1 pr-1 pl-1" :class="{ 'activo': windowsSelect === 3 }"
                    v-if="moduleView.main_account == 1 || moduleView.main_account == 2">
                    <!-- <div class="col-lg-5 offset-lg-1 pb-1" :class="{ 'activo': windowsSelect === 2 }"> -->
                        <a href="" @click.prevent="OnClickView(3)">
                            Preguntas
                            <div class="rounded-circle countResorces">{{ moduleView.preguntas }}</div>
                        </a>

                    </div>

                </div>
                <!-- MÓDULOS -->
                <div class="row mt-3 scroll" v-if="windowsSelect == 1">
                    <div class="col-lg-12 div-con-punto"
                        :class="{ 'moduloActivo div-con-punto-activo': moduleSelect === value.id || value.moduloIni >= 1, 'punto-activo':moduleSelect === value.id}"
                        v-for="(value, key) in module" :key="key"
                    >
                        <div class="row">
                            <div class="col-lg-10">
                                <a href="" @click.prevent="onClickModule(value.id)">
                                    <p>{{ value.nombre }}</p>
                                </a>
                                <a href="" @click.prevent="OnClickToDoTest(value.id)" v-if="moduleView.evaluara_por == 2 && value.moduloEvaluacion >= 1">
                                    <button class="btn btn-sm btn-evaluacion-mod" :class="{ 'btn-evaluacion-mod-activo': value.moduloIni >= 1}">
                                        <span v-if="value.moduloIni >= 1">Evaluación finalizada</span>
                                        <span v-if="value.moduloIni == 0">Evaluación pendiente</span>
                                    </button>
                                </a>
                            </div>
                            <div class="col-lg-2" style="margin: auto;" :class="{ 'iconoActivo': value.moduloIni >= 1}">
                                <i
                                aria-hidden="true"
                                :class="{'fa fa-check-circle': value.moduloIni >= 1, 'fa fa-times-circle': value.moduloIni == 0}">
                                </i>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-lg-12 pt-1 pb-1 pl-5 h-15" v-if="windowsSelect == 1 && moduleView.evaluara_por == 1">
                    <a href="" @click.prevent="OnClickToDoTest()">
                        <button class="btn btn-sm btn-evaluacion" :class="{'btn-evaluacion-activo': module[0].evaluacionAprovadaCap >= 1}">
                            <span v-if="module[0].evaluacionAprovadaCap >= 1">Evaluación finalizada</span>
                            <span v-if="module[0].evaluacionAprovadaCap == 0">Evaluación pendiente</span>
                        </button>
                    </a>
                </div>
                <!-- RECURSOS -->
                <div class="row mt-3" v-if="windowsSelect == 2">
                    <div class="col-lg-12">
                        <h4 class="modulo-select">{{ moduleView.nombre }}</h4>
                    </div>
                    <div class="col-lg-12" v-for="(value, key) in resources" :key="key">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-2 pr-1">
                                <i :class="{'flaticon-381-file': value.tipo_contenido === 2, 'flaticon-381-picture': value.tipo_contenido === 1,}" style="font-size: 30px;"></i>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-9 pl-3 pr-0 mx-auto"  style="margin: auto;">
                                <p class="nomRecurso">
                                    {{ quitarExtension(value.nombre) }}
                                </p>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-1 sinPadding">
                                <div class="d-flex">
                                    <div class="dropdown">
                                        <!-- <button type="button" class="btn btn-xs text-white" data-toggle="dropdown" @click.prevent="recursos(value.ruta_contenido)">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button> -->
                                        <a href="" data-toggle="dropdown" @click.prevent="recursos(value.ruta_contenido)">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" :href="descargar" :download="value.nombre">Descargar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PREGUNTAS -->
                <div class="row mt-3" v-if="windowsSelect == 3">
                    <div class="col-lg-12">
                        <h4 class="modulo-select">{{ moduleView.nombre }}</h4>
                    </div>

                    <div class="col-lg-12">
                        <h6 class="text-white">Ingrese su pregunta al experto KLAXEN</h6>
                    </div>

                    <div class="col-lg-12">
                        <div class="input-group">
                            <textarea class="form-control" aria-label="With textarea" style="min-height: 130px;" v-model="preguntaCliente.pregunta.value"></textarea>
                        </div>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                            <button class="btn btn-lg pl-5 pr-5 pt-2 pb-2 btn-evaluacion-mod-activo" @click="enviarPregunta">
                                Enviar
                            </button>
                        </div>
                    </div>

                    <div class="row scrollPreguntas">
                        <div class="col-lg-12 mt-3 ml-1" v-for="(value, key) in preguntaCliente.data" :key="key">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-8 pl-8 pr-0 mx-auto"  style="margin: auto; min-width: 235px;">
                                    <p class="pregunta mb-0">
                                        {{ value.pregunta }}
                                    </p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-4 pt-2">
                                    <button class="btn btn-sm btn-evaluacion-mod" :class="{ 'btn-evaluacion-mod-activo': value.estado == 'Resuelta'}" @click="OnClickOpenModalPreguntaUsuario(value.id)">
                                        {{ value.estado }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal EVALUACIÓN-->
        <div class="modal fade" ref="modal_evaluacion" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Evaluación de conocimientos</h5>
                </div>
                <div class="modal-body">
                    <CardTestComponent :data_test="fourth_section.questions"
                        :id_test_init="fourth_section.id_test_init"
                        @listenerToFinishTest="OnClickToFinishTest"
                        @listenerToFinishTestCertificados="OnClickToFinishTestCertificados"
                        @listenerToRepethTest="OnClickToDoTest()">
                    </CardTestComponent>
                </div>
                </div>
            </div>
        </div>
        <!-- Modal Pregunta Usuario-->
        <div
            class="modal fade"
            id="modal_pregunta_usuario"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-create-company">
                        {{ preguntaCliente.preguntaView.pregunta }}
                    </h5>
                    <button type="button" class="close" @click="OnClickCloseModalPreguntaUsuario">
                        <span>&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <div><h6>Respuesta: </h6></div>
                        <p v-show="preguntaCliente.preguntaView.estado == 'Pendiente'">Aun no hay una respuesta disponible.</p>
                        <p v-show="preguntaCliente.preguntaView.estado == 'Resuelta'">{{ preguntaCliente.preguntaView.respuesta }}</p>
                    </div>
                    <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-primary">
                        Invitar
                    </button> -->
                    <button
                        type="button"
                        class="btn btn-danger light"
                        @click="OnClickCloseModalPreguntaUsuario"
                    >
                        Cerrar
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CardTestComponent from "./CardTestComponent.vue";
import YoutubeApi from "./YoutubeApi.vue";
// import { extraerIdVideo } from "../../../../../../public/assets/js/functions.js";
export default {
    computed: {
        quitarExtension: function() {
            return function(nombre) {
            const lastDotIndex = nombre.lastIndexOf(".");
            if (lastDotIndex !== -1) {
                return nombre.slice(0, lastDotIndex);
            }
            return nombre;
            };
        }
    },
    mounted() {
        this.module = this.moduleE
        //console.log(this.module[0].main_account);
        this.moduleView = Object.values(this.module)[0] //DEL OBJETO ENVIADO SELECCIONAMOS EL PRIMER MODULO
        this.moduleSelect = this.moduleView.id //SELECCIONAMOS EL PRIMER ID PARA MARCARLO
        this.extraerIdVideo(this.moduleView.ruta)
        this.trainingInit()
    },
    components:{
        CardTestComponent,
        YoutubeApi
    },
    props: {
        moduleE: Object
    },
    filters: {
        quitarExtension: function(value) {
            return "hola";
        }
    },
    data() {
        return {
            iconCargando: 'start',
            viewContador: false,
            contador: 5,
            module : {},
            descargar: null,
            windowsSelect: 1,
            moduleSelect: null,
            moduleView: {ruta:'noVideo'},
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            resources: {},
            fourth_section: {
                questions: [],
                id_test_init: []
            },
            preguntaCliente: {
                preguntaView: [],
                pregunta:{
                    value :'',
                    required : true
                },
                data: []
            }
        }
    },
    methods: {
        pausarVideo() {
            this.$refs.youtubePlayer.pauseVideo();
        },
        async OnClickOpenModalPreguntaUsuario(id) {
            this.preguntaCliente.preguntaView = this.preguntaCliente.data.find(item => item.id === id);
            console.log(this.preguntaCliente.preguntaView );
            $("#modal_pregunta_usuario").modal("show");
        },

        async OnClickCloseModalPreguntaUsuario() {
            $("#modal_pregunta_usuario").modal("hide");
        },

        startCountdown() {
            const indexSiguiente = this.module.findIndex(item => item.id === this.moduleView.id) + 1; //SE CONSULTA EL INDEX ACTUAL Y SE SUMA UNO

            if(Object.values(this.module)[indexSiguiente]){//SE VALIDA SI HAY UN SIGUIENTE MODULO
                if (this.moduleView.evaluara_por == 2 && this.moduleView.moduloIni == 0) {
                    Swal({
                            title: '¡Ya estás listo para iniciar la evaluación!',
                            type: "warning",
                            cancelButtonText: "Más tarde",
                            confirmButtonText: "Iniciar evaluación",
                            showCancelButton: true,
                            showCloseButton: true,
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        }).then((result)=>{
                            if (result.value) {
                                this.OnClickToDoTest()
                            }else{
                                this.viewContador = true;
                                // Actualizar el contador cada segundo utilizando setInterval
                                const intervalo = setInterval(() => {
                                    if (this.contador > 0) {
                                        switch (this.contador) {
                                            case 3:
                                                this.iconCargando = 'half'
                                                break;
                                            case 1:
                                                this.iconCargando = 'end'
                                                break;
                                            default:
                                                break;
                                        }
                                        this.contador--;
                                    } else {
                                        // Cuando el contador llega a 0, se detiene la cuenta regresiva
                                        clearInterval(intervalo);
                                        this.siguienteVideo()
                                        this.viewContador = false;
                                        this.contador= 5;
                                        this.iconCargando = 'start'
                                    }
                                }, 1000);
                            }
                        });
                }else{
                    this.viewContador = true;
                    // Actualizar el contador cada segundo utilizando setInterval
                    const intervalo = setInterval(() => {
                        if (this.contador > 0) {
                            switch (this.contador) {
                                case 3:
                                    this.iconCargando = 'half'
                                    break;
                                case 1:
                                    this.iconCargando = 'end'
                                    break;
                                default:
                                    break;
                            }
                            this.contador--;
                        } else {
                            // Cuando el contador llega a 0, se detiene la cuenta regresiva
                            clearInterval(intervalo);
                            this.siguienteVideo()
                            this.viewContador = false;
                            this.contador= 5;
                            this.iconCargando = 'start'
                        }
                    }, 1000);
                }
            }else{
                if (this.moduleView.evaluara_por == 1 || this.moduleView.moduloIni == 0) {
                    if (this.module[0].evaluacionAprovadaCap == 0 || this.moduleView.moduloIni == 0) { //FALTA POR REALIZAR EVALUACIÓN POR CAPACITACIÓN
                        Swal({
                            title: '¡Ya estás listo para iniciar la evaluación!',
                            type: "warning",
                            cancelButtonText: "Más tarde",
                            confirmButtonText: "Iniciar evaluación",
                            showCancelButton: true,
                            showCloseButton: true,
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        }).then((result)=>{
                            if (result.value) {
                                this.OnClickToDoTest()
                            }
                        });

                    }
                }
            }


        },


        siguienteVideo(){
            const indexSiguiente = this.module.findIndex(item => item.id === this.moduleView.id) + 1; //SE CONSULTA EL INDEX ACTUAL Y SE SUMA UNO

            this.moduleView = Object.values(this.module)[indexSiguiente] //BUSCAMOS CON EL INDICE EL SIGUIENTE MODULO
            if(this.moduleView){
                this.moduleSelect = this.moduleView.id //SELECCIONAMOS EL ID PARA MARCARLO
                this.extraerIdVideo(this.moduleView.ruta)
                this.trainingInit()

            }else{//ES EL ULTIMO MODULO LA BÚSQUEDA NO ENCUENTRA SIGUIENTE
                this.moduleView = Object.values(this.module)[indexSiguiente-1] //DEJEMOS SELECCIONADO EL ULTIMO MODULO
                this.moduleSelect = this.moduleView.id //SELECCIONAMOS EL ID PARA MARCARLO
                this.extraerIdVideo(this.moduleView.ruta)
                this.trainingInit()
            }
        },
        async OnClickView(view) {
            if (view == 2) {
                try {
                    let data = new FormData();

                    data.append(`id_module`, this.moduleView.id);
                    loading();
                    let rs = await fetch(`${this.url}capacitaciones/get_data_resources_module`, {
                        method: "POST", body: data, headers: {
                            'X-CSRF-TOKEN': this.token
                        }
                    });
                    let rd = await rs.json();
                    loading(false);

                    switch (rd.responseCode) {
                        case 202:
                            this.resources = rd.data;
                            this.windowsSelect = view
                            break;

                        default:
                            break;
                    }
                }
                catch (error) {
                    loading(false);
                    console.log(`Error al traer información de recursos`);
                }
            }else if (view == 3){
                try {
                    let data = new FormData();

                    data.append('id_capacitacion', this.moduleView.id_capacitacion);
                    data.append('id_modulo', this.moduleView.id);
                    loading();
                    let rs = await fetch(`${this.url}capacitaciones/get_all_questions_users`, {
                        method: "POST", body: data, headers: {
                            'X-CSRF-TOKEN': this.token
                        }
                    });
                    let rd = await rs.json();
                    loading(false);

                    switch (rd.responseCode) {
                        case 202:
                            this.preguntaCliente.data = rd.data;
                            console.log(this.preguntaCliente.data);
                            this.windowsSelect = view
                            break;

                        default:
                            break;
                    }
                }
                catch (error) {
                    loading(false);
                    console.log(`Error al traer información de recursos`);
                }
            }else{
                this.windowsSelect = view
            }
        },
        onClickModule(mudule) {
            this.moduleSelect = mudule
            this.moduleView = this.module.find(item => item.id === this.moduleSelect);
            this.extraerIdVideo(this.moduleView.ruta)
            this.trainingInit()
        },

        async trainingInit(){
            //CAPACITACION INICIADA GUARDAR REGISTRO
            try {
                let data = new FormData();

                data.append(`id_capacitacion`, this.moduleView.id_capacitacion);
                data.append(`id_module`, this.moduleView.id);
                data.append(`evaluara_por`, this.moduleView.evaluara_por);

                let rs = await fetch(`${this.url}capacitaciones/save_training_init`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                switch (rd.responseCode) {
                    case 200:
                        let proxyArray = new Proxy(rd.data, {});
                        this.module = proxyArray;
                        this.moduleView = this.module.find(item => item.id === this.moduleView.id);
                        this.extraerIdVideo(this.moduleView.ruta)
                        break;
                    default:
                        break;
                }
                cargarPuntos() //Se llama funcion desde el main para actualizar los puntos globales

            }
            catch (error) {
                console.log(`Error al iniciar capacitación`+error);
            }
        },
        extraerIdVideo(ruta) {
            if (ruta) { //SOLO HACE PROCESO SI RUTA NO ES NULL
                //const vimeo = ruta.includes("vimeo");
                const youtube = ruta.includes("youtu");

                if (youtube) {
                    if (ruta.includes("v=")) {
                        const regex = /v=([^&]+)/;
                        const match = ruta.match(regex);

                        if (match) {
                            const videoId = match[1];
                            this.moduleView.ruta = videoId
                        }
                    }else{
                        const parts = ruta.split("/");
                        const videoId = parts[parts.length - 1];
                        this.moduleView.ruta = videoId
                    }

                }
                //https://www.youtube.com/watch?v=m9KRFR7L0zk&ab_channel=C%C3%A1maradeComerciodeCali
                //https://youtu.be/m9KRFR7L0zk
                //https://www.youtube.com/embed/m9KRFR7L0zk

                // if (vimeo) {
                //     const regex = /\/(\d+)$/;
                //     const match = ruta.match(regex);

                //     if (match) {
                //         const videoId = match[1];
                //         // return "https://player.vimeo.com/video/"+videoId
                //         this.moduleView.ruta = videoId
                //     }
                // }
            }else{
                this.moduleView.ruta = 'noVideo'
            }
        },
        recursos(ruta){
            this.descargar = this.url+'storage/'+ruta
        },
        OnClickToFinishTest(){
            $(this.$refs.modal_evaluacion).modal('hide');
            this.trainingInit()
        },
        OnClickToFinishTestCertificados(){
            this.trainingInit()
            window.location.href = `${this.url}capacitaciones/menu/3`;
        },
        async OnClickToDoTest(id = null) {
            this.pausarVideo()
            if (id != null) { //SELECCIONA PRIMERO EL MODULO ANTES DE INICIAR LA EVALUACIÓN
                this.onClickModule(id)
            }

            if ((this.moduleView.evaluara_por == 2 && this.moduleView.moduloIni >= 1) || (this.moduleView.evaluara_por == 1 && this.moduleView.evaluacionAprovadaCap >= 1)) {
                const confir = await swal({
                    title: "Evaluación aprobada",
                    text: "Actualmente ya aprobaste está evaluación ¿quieres realizarla de nuevo?",
                    type: "success",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                })

                if (!confir.value) {
                    return
                }
            }

            try {
                let data = new FormData();
                if (this.moduleView.evaluara_por == 1) { //CAPACITACIÓN GENERAL
                    data.append('id_module', null);
                    data.append('id_capacitacion', this.moduleView.id_capacitacion);
                }else if (this.moduleView.evaluara_por == 2) { // POR MÓDULOS
                    data.append('id_module', this.moduleView.id);
                    data.append('id_capacitacion', null);
                }

                loading();
                let rs = await fetch(`${this.url}capacitaciones/get_data_test`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 202:
                        this.fourth_section.questions = rd.data.questions;
                        this.fourth_section.questions.sort(() => Math.random() - 0.5);
                        this.fourth_section.id_test_init = rd.data.id_test_init;
                        $(this.$refs.modal_evaluacion).modal('show');
                        break;

                    case 400:
                        toastr.warning('No tienes examen para este módulo');
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al consultar el examen para realizarse: ${error.message}`);

            }
        },
        async OnClickTMP(item)
        {
            try {
                let data = new FormData();

                loading();
                data.append('id_module', item.id);
                let rs = await fetch(`${this.url}capacitaciones/generate_link_module`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 200:

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
                            this.CopyLink(`${rd.data}`, document.body);
                        }
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al generar el link de los módulos: ${error.message}`);
            }
        },
        CopyLink(url, element_save) {
            var c = document.createElement("textarea");
            c.value = url;
            c.style.maxWidth = '0px';
            c.style.maxHeight = '0px';
            element_save.appendChild(c);

            c.focus();
            c.select();
            document.execCommand("copy");
            element_save.removeChild(c);

            toastr.success('Link de la capacitación copiada.')
        },

        async enviarPregunta(){
            try {
                if (this.preguntaCliente.pregunta.value == '') {
                    toastr.warning(`Debes ingresar una pregunta!`);
                    return;
                }

                let data = new FormData();

                loading();
                data.append('id_capacitacion', this.moduleView.id_capacitacion);
                data.append('id_modulo', this.moduleView.id);
                data.append('pregunta', this.preguntaCliente.pregunta.value);

                let rs = await fetch(`${this.url}capacitaciones/save_questions_cliente`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 200:
                        swal({
                            text: "Su pregunta ha sido registrada con éxito, en menos de 24 Horas el experto KLAXEN le dará una respuesta. Llegará a su correo y podrá consultarla en esta misma sección de preguntas",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Cerrar",
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        this.preguntaCliente.pregunta.value = ''
                        this.preguntaCliente.data = rd.data
                        this.moduleView.preguntas = rd.data.length;
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al guardar pregunta: ${error.message}`);
            }
        }
    }
}
</script>

<style scoped>
a {
    color: white;
}

.bg_dark {
    background-color: #011627;
    padding: 10px;
    font-size: 14px;
}

.activo {
    background-image: linear-gradient(to right, transparent 0%, #ff7f00 40%);
    background-position: bottom center;
    background-size: 100% 1px;
    background-repeat: no-repeat;
}

.activo>a {
    color: #ff7f00 !important;
    font-weight: bold;
}

.moduloActivo>div>div>a{
    color: #ff7f00 !important;
    font-weight: bold;
}

.iconoActivo{
    color: #ff7f00 !important;
}

.div-con-punto {
    border-left: 1px solid white;
    position: relative;
    padding-left: 25px;
    margin-left: 10px;
    padding-bottom: 10px;
    padding-top: 0px;
}

.div-con-punto::after {
    content: "";
    position: absolute;
    top: 35%;
    left: 0%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: white;
    box-shadow: 0 0 0 4px white;
}

.div-con-punto-activo {
    border-left: 1px solid #ff7f00;
    position: relative;
    padding-left: 25px;
    margin-left: 10px;
    padding-bottom: 10px;
    padding-top: 0px;
}

.punto-activo::after{
    background-color: white !important;
    box-shadow: 0 0 0 4px #ff7f00;
}

.div-con-punto-activo::after {
    content: "";
    position: absolute;
    top: 35%;
    left: 0%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #ff7f00;
    box-shadow: 0 0 0 4px #ff7f00;
}


.btn-evaluacion{
    background-color: #E6F0FF !important;
    color: #002F54 !important;
}

.btn-evaluacion-activo{
    background-color: #ff7f00 !important;
    color: white !important;
}

.btn-evaluacion-mod{
    background-color: #E6F0FF;
    color: #002F54;
    padding: 2px 1rem;
    margin-bottom: 12px;
}

.btn-evaluacion-mod-activo{
    background-color: #ff7f00 !important;
    color: white !important;
    padding: 2px 1rem;
    margin-bottom: 12px;
}

.recursos{
    color: darkgray;
    font-size: 13px !important;
}

a > p{
    margin-bottom: 0px !important;
}

.right{
    margin-left: auto;
}

.scroll{
    overflow-y: auto;
    max-height: 500px;
    display: -webkit-box !important;
}

.scrollPreguntas{
    overflow-y: auto;
    max-height: 245px;
    margin-right: 0px;
    display: -webkit-box !important;
    overflow-x: hidden;
    padding-right: 2px;
}

.scrollPreguntas::-webkit-scrollbar {
    width: 8px;     /* Tamaño del scroll en vertical */
}
.scrollPreguntas::-webkit-scrollbar-thumb {
  background-color: #888; /* Color del pulgar de la barra de desplazamiento */
  border-radius: 4px; /* Borde redondeado del pulgar */
}

.scrollPreguntas::-webkit-scrollbar-thumb:hover {
  background-color: #555; /* Color del pulgar al pasar el cursor sobre él */
}

.scrollPreguntas::-webkit-scrollbar-track {
  background-color: #f1f1f1; /* Color de fondo de la barra de desplazamiento */
  border-radius: 4px; /* Borde redondeado del fondo de la barra de desplazamiento */
}

.scroll::-webkit-scrollbar {
    width: 1px;     /* Tamaño del scroll en vertical */
    height: 1px;    /* Tamaño del scroll en horizontal */
    display: none;  /* Ocultar scroll */
}

.countResorces{
    display: inline-block;
    width: 20px;
    height: 20px;
    background-color: #ff7f00;
    color: white !important;
}

.sinPadding{
    padding-left: 1px;
    padding-right: 1px;
    padding-top: 14px;
}

.modulo-select{
    color: white;
    text-align: center;
}

.div2{
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    background-color: #e6f0ffd8;
    z-index: 2;
}

.nextModule{
    font-size: 50px;
    color: #003054;
    font-weight: bold;
}

.nomRecurso{
    width: 70%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}

.pregunta{
    width: 90%;
    height: 47px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
