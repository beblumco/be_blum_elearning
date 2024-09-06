<template>
    <!-- INICIAR CAPACITACION -->
    <div v-if="data.view == 2">
        <initTraining :moduleE="data.second_section.modules"/>
    </div>

    <div class="container-fluid">
        <div class="row menu-cap" v-if="data.view != 2">
            <!-- Columna para los botones -->
            <div class="col-md-8 pt-2 pl-1">
                <div class="d-flex flex-wrap" id="container_bnts">
                    <button v-if="permisos.includes('ent-ele-mi_plan') && data.user.main_account_id != 2"
                    id="btn-elearning-menu-plan"
                    class="btn btn-barra mr-2 mb-2"
                    :class="{'btn-barra-activo': data.button == 1}"
                    @click="OnClickChangeView(1)">
                        Mi plan L&D
                    </button>
                    <button v-if="permisos.includes('ent-ele-mis_capacitaciones')" id="btn-elearning-menu-capacitaciones" class="btn btn-barra mr-2 mb-2" :class="{'btn-barra-activo': data.button == 2}" @click="OnClickChangeView(2)">
                        Mis capacitaciones internas
                    </button>
                    <button v-if="permisos.includes('ent-ele-mis_certificados')" id="btn-elearning-menu-certificados" class="btn btn-barra mr-2 mb-2" :class="{'btn-barra-activo': data.button == 3}" @click="OnClickChangeView(3)">
                        Mis certificados
                    </button>
                    <button v-if="permisos.includes('ent-ele-certificados_equipo')" id="btn-elearning-menu-certificados-equipo" class="btn btn-barra mr-2 mb-2" :class="{'btn-barra-activo': data.button == 4}" @click="OnClickChangeView(4)">
                            Certificados de mi equipo
                    </button>
                    <button v-if="permisos.includes('ent-ele-certificados_cliente')" id="btn-elearning-menu-certificados-cliente" class="btn btn-barra mr-2 mb-2" :class="{'btn-barra-activo': data.button == 5}" @click="OnClickChangeView(5)">
                            Certificados clientes
                    </button>
                </div>
            </div>
            <!-- Columna para el input de búsqueda -->
            <div class="col-md-1 pl-1">
                <button @click.prevent="OnClickRedirectNewTraining"
                v-if="permisos.includes('ent-ele-crear_curso')"
                id="btn-elearning-menu-crear-curso"
                class="btn btn-barra-naranja"
                style="width: max-content;">
                    Crear curso
                </button>
            </div>
            <div class="col-md-3 pt-2 pr-1 pl-1">
                <div class="input-group ml-1" v-if="(data.mode != 3 && data.mode != 4)">
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
            </div>
        </div>

        <!-- <div class="row menu-cap" v-if="data.view != 2"> -->
            <!-- <div class="col-sm-1" v-if="(data.mode == 2 || data.mode == 3)">
                    <i class="fa fa-arrow-left color-danger dev-icon-back" @click="OnClickBack"></i>
                </div> -->
            <!-- <div id="dv-menu-elearning" class="d-flex">
                <div class="btn-menu" v-show="data.user.main_account_id != 2">
                    <button v-if="permisos.includes('ent-ele-mi_plan')" id="btn-elearning-menu-plan" class="btn btn-barra" :class="{'btn-barra-activo': data.button == 1}" @click="OnClickChangeView(1)">Mi plan L&D</button>
                </div>
                <div class="btn-menu">
                    <button v-if="permisos.includes('ent-ele-mis_capacitaciones')" id="btn-elearning-menu-capacitaciones" class="btn btn-barra" :class="{'btn-barra-activo': data.button == 2}" @click="OnClickChangeView(2)"> Mis capacitaciones internas</button>
                </div>
                <div class="btn-menu">
                    <button v-if="permisos.includes('ent-ele-mis_certificados')" id="btn-elearning-menu-certificados" class="btn btn-barra" :class="{'btn-barra-activo': data.button == 3}" @click="OnClickChangeView(3)"> Mis certificados</button>
                </div>
                <div class="btn-menu">
                    <button v-if="permisos.includes('ent-ele-certificados_equipo')" id="btn-elearning-menu-certificados-equipo" class="btn btn-barra" :class="{'btn-barra-activo': data.button == 4}" @click="OnClickChangeView(4)"> Certificados de mi equipo</button>
                </div>
            </div> -->


<!--
            <div class="div-busqueda">
                <div class="mr-2">
                    <a href="#" @click.prevent="OnClickRedirectNewTraining" v-if="permisos.includes('ent-ele-crear_curso')">
                        <button id="btn-elearning-menu-crear-curso" class="btn btn-barra-naranja" style="width: max-content;">Crear curso</button>
                    </a>
                </div> -->
                <!-- <div class="input-group" v-if="(data.mode != 3 && data.mode != 4)">
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
            </div> -->


            <!-- <div class="col-sm-4 d-flex justify-content-end">
                    <button class="btn btn-primary" v-if="data.mode == 3" @click="OnClickToDoTest">Realizar examen</button>
                    <div class="d-flex align-items-center" @click="OnClickCertificates">
                        <i class="flaticon-381-diploma dev-icon-certificate dev-bg" data-toggle="tooltip" data-placement="top" title="Ver certificados" v-if="data.mode == 1"></i>
                    </div>
                </div> -->
        <!-- </div> -->

        <div v-show="data.view == 1">
            <!-- </div> -->
            <div class="row">

            </div>
            <!-- FIRST SECTION -->
            <div class="row dataInfo" v-show="data.mode == 1">
                <!-- ITEM -->
                <CardV2Component v-for="training in this.filtered" :key="training.id" :data_training="training" :menu="data.button" :user="data.user"
                    @listener_training="OnClickInitTrainig" @reload="GetDataInit" @openModalModule="GetDataModules"></CardV2Component>
                <!-- FIN - ITEM -->
            </div>
            <!--END - FIRST SECTION -->

            <!-- SECOND SECTION -->
            <div class="row" v-if="data.mode == 2">
                <!-- ITEM -->
                <CardModulesComponent v-for="itemModule in this.filtered" :key="itemModule.id" :data_module="itemModule"
                    @listener_module="OnClickInitModule" @listener_generate_link="OnClickToGenerateLink"
                    @listener_view_links="OnClickToViewLinks"></CardModulesComponent>
                <!-- FIN - ITEM -->
            </div>
            <!--END - SECOND SECTION -->

            <!-- THIRD SECTION -->
            <div class="row" v-if="data.mode == 3">
                <!-- ITEM -->
                <CardContentComponent :data_content="data.third_section.contents"></CardContentComponent>
                <!-- FIN - ITEM -->
            </div>
            <!--END - THIRD SECTION -->

            <!-- FOURTH SECTION -->
            <div class="row" v-if="data.mode == 4">
                <CardTestComponent :data_test="data.fourth_section.questions"
                    :id_test_init="data.fourth_section.id_test_init" @listenerToFinishTest="OnClickToFinishTest">
                </CardTestComponent>
            </div>
            <!--END - FOURTH SECTION -->


            <!-- MODAL LINKS LIST -->
            <div class="modal fade" style="overflow-y: scroll;" ref="modal_links_list" data-toggle="modal"
                data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Listado de links</h5>
                        </div>
                        <div class="modal-body">
                            <div class="col-lg-12">

                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>Módulo</th>
                                                <th>Capacitación del módulo</th>
                                                <th>Fecha creación</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="link in data.modal_links.links" :key="link.id">
                                                <td>{{ link.MODULO }}</td>
                                                <td>{{ link.CAPACITACION_DE_MODULO }}</td>
                                                <td>{{ link.FECHA_CREACION }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary shadow btn-xs sharp mr-1"
                                                            @click.stop.prevent="OnClickCopyLink(`${link.LINK_CROP}`, this.$refs.modal_links_list)"><i
                                                                class="fa fa-link"></i></button>
                                                        <button class="btn btn-primary shadow btn-xs sharp mr-1"
                                                            @click.stop.prevent="OnClickToViewAssistants(link.id)"><i
                                                                class="fa fa-eye"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="data.modal_links.links.length == 0">
                                                <td colspan="4" class="text-center font-weight-bold">No tienes datos</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" @click="OnCloseModalLinks">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL LINKS LIST - END-->

            <!-- MODAL ASSISTANT LINKS LIST -->
            <div class="modal fade" id="modal_assistant_links_list" ref="modal_assistant_links_list" data-toggle="modal"
                data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Listado de asistentes</h5>
                            <button class="btn btn-primary"
                                @click="downloadAllCertificates(data.modal_assistant_links.assistants[0].id_link)">Descargar
                                todo</button>
                        </div>
                        <div class="modal-body">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Fecha de registro</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="assistant in data.modal_assistant_links.assistants"
                                                :key="assistant.id">
                                                <td>{{ assistant.nombre }}</td>
                                                <td>{{ assistant.fecha }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="#" class="btn btn-primary shadow btn-xs sharp mr-1"
                                                            @click.prevent="downloadCertificate(assistant.id, assistant.id_link)"><i
                                                                class="fa fa-download"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="data.modal_assistant_links.assistants.length == 0">
                                                <td colspan="4" class="text-center font-weight-bold">No tienes datos</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light"
                                @click="OnCloseModalAssistantLinks">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL ASSISTANT LINKS LIST - END-->
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

        <!-- Mis certificados -->
        <div v-show="data.view == 3" class="dataInfo">
            <PageTrainingCertificatesComponent :search="data.input_search"/>
        </div>

        <!-- Mis certificados equipo-->
        <div v-show="data.view == 4" class="dataInfo">
            <PageTrainingTeamCertificatesComponent :search="data.input_search"/>
        </div>

        <!-- Mis certificados Clientes comercial-->
        <div v-show="data.view == 5" class="dataInfo">
            <PageTrainingClientCertificatesComponent :search="data.input_search"/>
        </div>
    </div>
</template>

<script>
import CardTrainingComponent from "./CardTrainingComponent.vue";
import CardModulesComponent from "./CardModulesComponent.vue";
import CardContentComponent from "./CardContentComponent.vue";
import CardTestComponent from "./CardTestComponent.vue";
import CardV2Component from "./CardV2Component.vue";
import initTraining from "./initTraining.vue";
import ContentModuleComponent from '../CreateTraining/ContentModuleComponent.vue';
import PageTrainingCertificatesComponent from "../Certificates/PageTrainingCertificatesComponent.vue";
import PageTrainingTeamCertificatesComponent from "../Certificates/PageTrainingTeamCertificatesComponent.vue";
import PageTrainingClientCertificatesComponent from "../Certificates/PageTrainingClientCertificatesComponent.vue";
import { guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas, skeletonLoader } from "../../../../../../public/assets/js/functions.js";

export default {
    props: {
        idtraining: String,
        menu: {
            type: String,
            required: false,
            default: '1',
            note: 'menu seleccionado'
        },
    },
    components: {
        CardTrainingComponent,
        CardModulesComponent,
        CardContentComponent,
        CardTestComponent,
        CardV2Component,
        initTraining,
        ContentModuleComponent,
        PageTrainingCertificatesComponent,
        PageTrainingTeamCertificatesComponent,
        PageTrainingClientCertificatesComponent
    },
    async created() {
        await this.dataUser();
        if (this.menu != 1) {
            this.OnClickChangeView(this.menu)
        }else{
            await this.GetDataInit();
        }
        this.skeletonLoader('.menu-cap')
        this.skeletonLoader('.btn-barra')

        if (this.idtraining != '') {
            const item = {id: this.idtraining}
            this.OnClickInitTrainig(item)
        }

        if(localStorage.getItem('id_training') != null){
           let id = {id: localStorage.getItem('id_training')};

            localStorage.removeItem('id_training');

            this.OnClickInitTrainig(id);
        }
    },
    async mounted(){
        this.skeletonLoader('.menu-cap')
        this.skeletonLoader('.btn-barra')
        await this.guiaGetAll();
        this.CreateTour(this.guias);
        this.tour.start();
    },
    computed: {
        filtered() {
            if (this.data.mode == 1) {
                return this.data.first_section.trainings.filter(training => {
                    return training.nombre?.toLowerCase().includes(this.data.input_search.toLocaleLowerCase())
                });
            }

            if (this.data.mode == 2) {
                return this.data.second_section.modules.filter(module => {
                    return module.nombre?.toLowerCase().includes(this.data.input_search.toLocaleLowerCase())
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
                user: [],
                mode: 1,
                view: 1,
                button: 1,
                placeholder_search: 'Buscar por capacitación',
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
        skeletonLoader,
        CreateTour,
        async dataUser(){
            let rs = await fetch(`${this.url}capacitaciones/dataUser`, {
                method: "POST", headers: {
                    'X-CSRF-TOKEN': this.token
                }
            });
            let rd = await rs.json();
            this.data.user =  rd.data;
        },
        OnClickRedirectNewTraining() {
            window.location.href = `${this.url}capacitaciones/administracion`;
        },
        async GetDataInit(showLoading = false) {
            try {
                let data = new FormData();
                data.append('mode_query', this.data.button);

                if (showLoading)
                    this.skeletonLoader('.dataInfo')
                    //loading();
                this.data.first_section.trainings = [];
                let rs = await fetch(`${this.url}capacitaciones/get_data_init`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();

                if (showLoading)
                    this.skeletonLoader('.dataInfo')
                    //loading(false);

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
                    this.skeletonLoader('.dataInfo')
                    //loading(false);

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
                this.data.placeholder_search = 'Buscar por capacitación'
                // window.open(`${this.url}capacitaciones/certificados`);
            }else if(number == 1){
                this.data.view = number
                this.GetDataInit(true);
                this.data.placeholder_search = 'Buscar por capacitación'
            }else if(number == 2){
                this.data.view = 1
                this.GetDataInit(true);
                this.data.placeholder_search = 'Buscar por capacitación'
            }else if (number == 4){
                this.data.view = 4
                this.data.placeholder_search = 'Buscar por nombre, id, certificado o fecha'
            }else if (number == 5){
                this.data.view = 5
                this.data.placeholder_search = 'Buscar por nombre, id, certificado o fecha'
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
    align-items: center;
}

.btn-barra{
    padding: 0.6rem 0.8rem;
    Background-color: #E6F0FF;
    Border-color: #E6F0FF;
    Color: #002F54;
  box-shadow: 0 0.46875rem 2.1875rem rgba(4,9,20,0.03), 0 0.9375rem 1.40625rem rgba(4,9,20,0.03), 0 0.25rem 0.53125rem rgba(4,9,20,0.05), 0 0.125rem 0.1875rem rgba(4,9,20,0.03) !important;
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
    padding: 0.6rem 0.8rem;
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

.form-control
{
    height: 45px!important;
}

button{
    font-size: 14px ;
}

.input-group-text{
    margin-bottom: 8px;
}
</style>

