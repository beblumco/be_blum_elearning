<template>
    <!-- INICIAR CAPACITACION -->
    <div v-if="data.view == 2">
        <initTraining :moduleE="data.second_section.modules"/>
    </div>

    <div class="container-fluid">

        <div class="row menu-cap" v-if="data.view != 2">
            <!-- <div class="col-sm-1" v-if="(data.mode == 2 || data.mode == 3)">
                    <i class="fa fa-arrow-left color-danger dev-icon-back" @click="OnClickBack"></i>
                </div> -->
            <div id="dv-menu-webinars" class="d-flex">
                <div class="btn-menu">
                    <button v-if="permisos.includes('ent-web-mis_webinars')" id="btn-webinars-menu-mis-webinars" class="btn btn-barra" :class="{'btn-barra-activo': data.button == 3}" @click="OnClickChangeView(3)"> Mis Webinars</button>
                </div>
                <div class="btn-menu">
                    <button v-if="permisos.includes('ent-web-agenda_eventos')" id="btn-webinars-menu-agendar" class="btn btn-barra" :class="{'btn-barra-activo': data.button == 1}" @click="OnClickChangeView(1)">Agenda de eventos</button>
                </div>
            </div>

            <div class="div-busqueda">
                <div class="mr-2 d-flex align-items-center" v-if="permisos.includes('ent-web-crear_webinar')">
                    <a href="#" @click.prevent="OnClickRedirectNewTraining">
                        <button class="btn btn-barra-naranja" style="width: max-content;">Crear webinar</button>
                    </a>
                </div>
                <div class="input-group" v-if="(data.mode != 3 && data.mode != 4)">
                    <input type="text" class="form-control" v-model="data.input_search"
                        :placeholder="data.placeholder_search" @keyup="OnKeyUpSearch()">
                    <div class="input-group-append">
                        <span class="input-group-text btn-barra-naranja">
                            <a href="javascript:void(0)" class="aBuscar">
                                <i class="flaticon-381-search-2"></i>
                            </a>
                        </span>
                    </div>
                </div>
                <h4 v-if="data.mode == 4" class="text-center">Evaluación de competencias</h4>
            </div>


            <!-- <div class="col-sm-4 d-flex justify-content-end">
                    <button class="btn btn-primary" v-if="data.mode == 3" @click="OnClickToDoTest">Realizar examen</button>
                    <div class="d-flex align-items-center" @click="OnClickCertificates">
                        <i class="flaticon-381-diploma dev-icon-certificate dev-bg" data-toggle="tooltip" data-placement="top" title="Ver certificados" v-if="data.mode == 1"></i>
                    </div>
                </div> -->
        </div>

        <div v-if="data.view != 2">
            <!-- FIRST SECTION -->
            <div class="row" v-if="data.view == 1">
                <CardScheduleEventsComponent v-for="training in this.filtered" :key="training.id" :data_training="training"
                    @listener_training="OnClickInitTrainig" @reload="GetDataInit" @openModalModule="GetDataModules"></CardScheduleEventsComponent>
            </div>
            <!--END - FIRST SECTION -->

            <div class="row" v-if="data.view == 3">
                <CardExecuteEventsComponent v-for="training in this.filtered" :key="training.id" :data_training="training"
                    @listener_training="OnClickInitTrainig" @reload="GetDataInit" @openModalModule="GetDataModules"></CardExecuteEventsComponent>
            </div>

            <!-- MODAL VER MODULO -->
        <div class="modal fade" id="modal_view_module" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Módulos</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="form-group col-md-12">
                                <div class="accordion accordion-primary">
                                     <div v-for="module in data.modal_view_module.modules" :key="module.id" class="accordion__item" >
                                        <div :class="`accordion__header rounded-lg`">
                                                <div class="col-lg-12 d-flex justify-content-between">
                                                <span class="accordion__header--text">{{module.nombre}}</span>
                                        <div class="">
                                            <i v-if="module.hasVideo" class="la la-video dev-fonts-icon dev-fonts-color-white"  @click.stop="OnClickModalVideo(module.id)"></i>
                                            <i v-if="module.hasRecursos" class="la la-files-o dev-fonts-icon dev-fonts-color-white" @click.stop="OnClickModalContent(module.id)"></i>
                                            <i v-if="module.hasTest" class="la la-question dev-fonts-icon dev-fonts-color-white"  @click.stop="OnClickModalQuestions(module.id)"></i>
                                        </div>
                                        </div>

                                        </div>

                                </div>
                                </div>
                        </div>

                    </div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalViewModule">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL VER MODULO - END-->
        <!-- MODAL VER CONTENIDO -->
        <div class="modal fade" id="modal_view_content" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="modal-title">Ver recursos</h5>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex flex-wrap">
                            <content-module-component v-for="content in data.modal_view_content.contents" :data_content="content" :key="content.id" :only_view="1" @view_content="OnClickModalViewContent"></content-module-component>
                            <p class="col-lg-12 text-center font-weight-bold" v-if="data.modal_view_content.contents.length == 0">No tienes contenido cargado para este módulo</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContent">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL VER CONTENIDO - END-->

        <!-- MODAL VISTA PREVIA CONTENIDO -->
        <div class="modal fade" id="modal_preview_content" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h3>Vista previa del contenido</h3>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="justify-content-center">
                            <iframe :src="data.modal_preview_content.url_content" width="738" height="407" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalViewContent">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL VISTA PREVIA CONTENIDO END -->
         <!-- MODAL AGREGAR EVALUACIÓN -->
        <div class="modal fade" id="modal_view_test" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="modal-title">Ver evaluación</h5>
                        </div>
                    </div>
                    <div class="modal-body p-2 ">
                        <div v-for="data_test in data.modal_view_test.data_tests" :key="data_test.id" class="col-lg-12 dev-over-flow dev_container_questions">
                            <div class="card border-0 pb-0" style="background: #F7F7F7">
                                <div class="card-body">
                                    <div id="DZ_W_Todo4" class="">
                                        <ul class="timeline mb-3">
                                            <li>
                                                <div class="timeline-panel mb-2 d-flex">
                                                    <div class="media-body col-lg-8">
                                                        <div class="form-group">
                                                            <input disabled type="text" class="form-control" v-model="data_test.pregunta" :placeholder="`Pregunta # ${data_test.orden}...`" />
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="card border-0 pb-0" style="background: #FAFAFA">
                                                        <div class="card-body">
                                                            <div class="col-lg-3 mb-3 p-0 ml-1">
                                                    </div>
                                                <div v-for="(answer,key) in data_test.respuestas" :key="key" class="d-flex mb-2 col-lg-12 align-items-center ml-5">

                                                    <div class="align-items-center col-lg-8 p-0">
                                                        <input disabled type="text" class="form-control" :placeholder="`Opción respuesta`" v-model="answer.respuesta" />
                                                    </div>

                                                    <div class="col-lg-3 p-0 ml-1">
                                                    </div>

                                                </div>
                                                        </div>
                                                    </div>
                                            </li>


                                        </ul>

                                    </div>

                                </div>
        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalTest">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL AGREGAR EVALUACIÓN - END-->
        </div>

    </div>
</template>

<script>
import CardTrainingComponent from "../TrainingExcecution/CardTrainingComponent.vue";
import CardModulesComponent from "../TrainingExcecution/CardModulesComponent.vue";
import CardContentComponent from "../TrainingExcecution/CardContentComponent.vue";
import CardTestComponent from "../TrainingExcecution/CardTestComponent.vue";
import CardScheduleEventsComponent from "./CardScheduleEventsComponent.vue";
import CardExecuteEventsComponent from "./CardExecuteEventsComponent.vue";
import initTraining from "../TrainingExcecution/initTraining.vue";
import ContentModuleComponent from '../CreateTraining/ContentModuleComponent.vue';
import PageTrainingCertificatesComponent from "../Certificates/PageTrainingCertificatesComponent.vue";
import { guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas  } from "../../../../../../public/assets/js/functions.js";

export default {
    props: {
        id_grupo: String
    },
    components: {
    CardTrainingComponent,
    CardModulesComponent,
    CardContentComponent,
    CardTestComponent,
    CardScheduleEventsComponent,
    CardExecuteEventsComponent,
    initTraining,
    ContentModuleComponent,
    PageTrainingCertificatesComponent
},
    async created() {
        await this.GetDataInit();
    },
    async mounted() {
        await this.guiaGetAll();
        this.CreateTour(this.guias);
        this.tour.start();
    },
    computed: {
        filtered() {
            if (this.data.mode == 1) {
                return this.data.first_section.trainings.filter(training => {
                    return training.nombre.toLowerCase().includes(this.data.input_search.toLocaleLowerCase())
                });
            }

            if (this.data.mode == 2) {
                return this.data.second_section.modules.filter(module => {
                    return module.nombre.toLowerCase().includes(this.data.input_search.toLocaleLowerCase())
                });
            }
        }

    },
    data() {
        return {
            permisos : JSON.parse(localStorage.getItem('permisos')),
            guias: [],
            guiasSecundarias: [],
            tour: null,
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            data:
            {
                mode: 1,
                view: 3,
                button: 3,
                placeholder_search: 'Buscar por Webinar',
                input_search: '',
                first_section: {
                    trainings: [],
                },
                second_section: {
                    modules: [],
                },
                third_section: {
                    contents: [],
                    id_module: ''
                },
                fourth_section: {
                    questions: [],
                    id_test_init: []
                },
                modal_links:
                {
                    links: []
                },
                modal_assistant_links: {
                    assistants: []
                },
                modal_view_module :{
                    modules: []
                },
                modal_view_content:{
                    contents: []
                },
                modal_preview_content:{
                    url_content : ""
                },
                modal_view_test: {
                    data_tests: []
                }
            }
        }
    },
    methods:
    {
        guiaGetAll,
        saveVisualizacionGuia,
        CreateTour,
        OnClickRedirectNewTraining() {
            window.location.href = `${this.url}capacitaciones/administracion`;
        },
        async GetDataInit(showLoading = false) {
            try {
                let data = new FormData();
                data.append('mode_query', this.data.button);

                if (showLoading)
                    loading();
                this.data.first_section.trainings = [];
                let rs = await fetch(`${this.url}capacitaciones/webinars/get_data_init`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();

                if (showLoading)
                    loading(false);

                switch (rd.responseCode) {
                    case 202:
                        this.data.first_section.trainings = rd.data;
                        break;

                    default:
                        break;
                }
            }
            catch (error) {
                if (showLoading)
                    loading(false);

                console.error(`Error to get principal Data: ${error.message}`);
            }

        },
        async OnClickInitTrainig(item) {
            try {
                let data = new FormData();

                data.append(`id_training`, item.id);
                loading();
                let rs = await fetch(`${this.url}capacitaciones/get_data_modules_by_id`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);
                switch (rd.responseCode) {
                    case 202:
                        this.data.second_section.modules = rd.data;
                        this.data.view = 2
                        break;

                    default:
                        break;
                }
            }
            catch (error) {
                loading(false);
                console.log(`Error al traer información de módulos`);
            }
        },
        async OnClickInitModule(item) {
            try {
                let data = new FormData();

                loading();
                data.append('id_module', item.id);
                let rs = await fetch(`${this.url}capacitaciones/get_data_content_modules`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 202:
                        this.data.third_section.id_module = item.id;
                        this.data.third_section.contents = rd.data;
                        this.data.mode = 3;
                        this.data.placeholder_search = "";
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al traer el contenido de los módulos: ${error.message}`);
            }
        },
        async OnClickToGenerateLink(item) {
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
        async OnClickCopyLink(url, element_save) {
            this.CopyLink(`${url}`, element_save);
        },
        async OnClickToViewLinks(item) {
            try {
                let data = new FormData();

                loading();
                data.append('id_module', item.id);
                let rs = await fetch(`${this.url}capacitaciones/view_links_by_user`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 202:

                        this.data.modal_links.links = rd.data;
                        $(this.$refs.modal_links_list).modal('show');
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al traer links: ${error.message}`);
            }
        },
        async OnClickToViewAssistants(id) {
            try {
                let data = new FormData();

                loading();
                data.append('id_link', id);
                let rs = await fetch(`${this.url}capacitaciones/view_assistants_by_link`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 202:

                        this.data.modal_assistant_links.assistants = rd.data;
                        $(this.$refs.modal_assistant_links_list).modal('show');
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al traer links: ${error.message}`);
            }

        },
        async OnClickToDoTest() {
            try {
                let data = new FormData();

                data.append('id_module', this.data.third_section.id_module);
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
                        this.data.fourth_section.questions = rd.data.questions;
                        this.data.fourth_section.questions.sort(() => Math.random() - 0.5);
                        this.data.fourth_section.id_test_init = rd.data.id_test_init;
                        this.data.mode = 4;
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
        async GetDataModules(id){
            try
                {
                    let data_form = new FormData();
                    data_form.append('tab_selected', 2);
                    data_form.append('id_training', id);


                        loading();

                    let rs = await fetch(`${this.url}capacitaciones/administracion/get_data_init_general`, { method: "POST", body: data_form, headers: {
                        'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();

                    const { responseCode, message, data } = rd;

                        loading(false);

                    switch (responseCode)
                    {
                        case 202:
                            this.data.modal_view_module.modules = data.modules;
                             $('#modal_view_module').modal('show');
                            break;

                        default:
                            break;
                    }
                }
                catch (error)
                {

                        loading(false);

                    console.error(`Error to get principal Data: ${error.message}`);
                }
        },
        OnClickBack() {
            if (this.data.mode == 2) {
                this.data.input_search = "";
                this.data.second_section.modules = [];
                this.data.mode = 1;
                this.data.placeholder_search = "Buscar por capacitación";
            }
            else if (this.data.mode == 3) {
                this.data.input_search = "";
                this.data.third_section.contents = [];
                this.data.mode = 2;
                this.data.placeholder_search = "Buscar por módulos";
            }
        },
        async OnClickToFinishTest() {
            this.data.placeholder_search = "Buscar por capacitación";
            this.data.mode = 1;
            await this.GetDataInit();
        },
        OnClickCertificates() {
            window.location.href = `${this.url}certificados`;
        },
        OnKeyUpSearch() {

        },
        OnClickCloseModalViewModule(){
            $('#modal_view_module').modal('hide');
        },
        async OnClickModalContent(id){
            try
            {
                let data_form = new FormData();

                data_form.append('id_module', id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/get_data_contents`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 202:
                        this.data.modal_view_content.contents = data;
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                loading(false);
                console.error(`Error al agregar contenido: ${error.message}`);
            }

            $('#modal_view_content').modal('show');
        },
        OnClickCloseModalContent(){
            $('#modal_view_content').modal('hide');
        },
        OnClickModalViewContent(url){
            this.data.modal_preview_content.url_content = url;
            $('#modal_preview_content').modal('show');
        },
        OnClickCloseModalViewContent(){
             $('#modal_preview_content').modal('hide');
        },
        async OnClickModalVideo(id){
            try
            {
                let data_form = new FormData();

                data_form.append('id_module', id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/get_data_contents`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 202:
                        let url = "";
                        data.forEach(element => {
                            if(element.tipo_contenido == 3)
                            url = element.ruta_contenido;
                        });
                        this.OnClickModalViewContent(url);
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                loading(false);
                console.error(`Error al agregar contenido: ${error.message}`);
            }
        },
        async OnClickModalQuestions(id){
             try
            {
                let data_form = new FormData();

                data_form.append('id_module', id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/get_test_module`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                debugger
                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 202:
                        if(data.length != 0)
                        {
                            Array.from(data).map(pr => {
                                pr.respuestas.map(rta => {
                                    rta.checked = (rta.checked == 0 ? false : true);
                                });
                            });
                        }
                        this.data.modal_view_test.data_tests = data;

                        break;

                    case 400:
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                console.error(`Error al traer preguntas: ${error.message}`);
                loading(false);
            }
            $('#modal_view_test').modal('show');
        },
        OnClickCloseModalTest(){
            $('#modal_view_test').modal('hide');
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
        OnCloseModalLinks() {
            $(this.$refs.modal_links_list).modal('hide');
        },
        OnCloseModalAssistantLinks() {
            $(this.$refs.modal_assistant_links_list).modal('hide');
        },
        async downloadCertificate(id, id_link) {
            loading();
            window.open(`${this.url}capacitaciones/download-certificate/${id}/${id_link}`, '_blank');
            loading(false);
        },
        async downloadAllCertificates(id_link) {
            loading();
            window.open(`${this.url}capacitaciones/download-all-certificates-by-link/${id_link}`, '_blank');
            loading(false);
        },
        OnClickChangeView(number)
        {
            this.data.button = number;

            if(number == 3){
                this.data.view = number
                this.GetDataInit(true);
            }else if(number == 1){
                this.data.view = number
                this.GetDataInit(true);
            }else if(number == 2){
                this.data.view = 1
                this.GetDataInit(true);
            }
        }
    }
}
</script>

<style scoped>
.dev-icon-back {
    font-size: 25px;
    cursor: pointer;
}

.dev-icon-certificate {
    color: #FE634E;
    font-size: 24px;
    cursor: pointer;
}

.dev-bg {
    background: rgba(254, 99, 78, 0.05);
    border-radius: 1.25rem;
    padding: 14px 18px 14px 18px;
}

.btn-menu {
    margin-right: 5px;
    margin-top: 5px;
    margin-bottom: 5px;
}

.menu-cap {
    background-color: white;
    border-radius: 1.25rem;
    /* padding: 8px; */
    padding-left: 5px;
    padding-right: 5px;
    padding-top: 5px;
    padding-bottom: 5px;
    margin-bottom: 20px;
    margin-left: 0px !important;
    margin-right: 0px !important;
    align-items: center;
}

.div-busqueda {
    display: flex;
    margin-left: auto;
    width: 43%;
    margin-top: 5px;
    margin-bottom: 5px;
}

.btn-barra{
    padding: 0.6rem 0.8rem;
    Background-color: #E6F0FF;
    Border-color: #E6F0FF;
    Color: #002F54;
    /* box-shadow: none !important; */
}

.btn-barra:hover{
    Background-color: #002F54;
    Border-color: #002F54;
    Color: #E6F0FF;
    box-shadow: none !important;
}

.btn-barra-activo{
    Background-color: #002F54;
    Border-color: #002F54;
    Color: #E6F0FF;
    box-shadow: none !important;
}
.btn-barra-naranja a{
    Color: #E6F0FF;
}

.btn-barra-naranja{
    Background-color: #ff7f00;
    Border-color: #ff7f00;
    Color: white;
    /* box-shadow: none !important; */
}

.btn-barra-naranja:hover{
    Background-color: #ff8000e0;
    color: white;
}


@media(max-width: 1125px) {
    .div-busqueda {
        /* margin-left: auto; */
        width: 100%;
        margin-top: 5px;
        margin-bottom: 5px;
    }
}
.dev-fonts-icon
{
    font-size: 30px;
    margin-left: 8px;
}
.form-control {
    height: 45px!important;
}
</style>

