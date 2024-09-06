<template>
    <div>
        <div class="page-titles row" style="display: flex;">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <template v-for="(el, i) in history_folder_nav" :key="i">
                        <li class="breadcrumb-item" :class="el.active ? 'active' : ''">
                            <a href="javascript:void(0)" @click="backFolder(el.id)">{{ el.name }}</a>
                        </li>
                    </template>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end" style="display: flex !important; ">
                <a href="javascript:void(0)" class="btn btn-primary text-nowrap btn-sm d-flex mr-3"
                    @click="OnClickSelectGE" v-if="permisos.includes('dri-dri-cambiar_grupo_empresa') && this.current_folder_id == null">
                    <i class="bi bi-folder-plus center-p mr-2"></i>
                    <p class="center-p"> Cambiar grupo empresa</p>
                </a>
                <a  href="javascript:void(0)" class="btn btn-primary text-nowrap btn-sm mr-3 d-flex" @click="OnClickOpenModalDocTecnica"  v-if="visibleElementsPermanentes">
                    <i class="bi bi-cloud-upload center-p mr-2"></i>
                    <p class="center-p"> Subir Doc. Técnica</p>
                </a>
                <a href="javascript:void(0)" class="btn btn-primary text-nowrap btn-sm mr-3 d-flex" @click="btnUploadFile"  v-if="permisos.includes('dri-dri-subir_archivo') && !visibleElementsPermanentes">
                    <i class="bi bi-cloud-upload center-p mr-2"></i>
                    <p class="center-p"> Subir Archivo</p>
                    <input ref='uploadFiles' type='file'
                        accept="*/*"
                        @change="loadInputFiles" multiple hidden />
                </a>
                <a href="javascript:void(0)" class="btn btn-primary text-nowrap btn-sm d-flex"
                    @click="openModalNewFolder" v-if="permisos.includes('dri-dri-nueva_carpeta')">
                    <i class="bi bi-folder-plus center-p mr-2"></i>
                    <p class="center-p"> Nueva Carpeta</p>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card" style="min-height: 500px;">
                    <div class="card" style="min-height: 500px;" @scroll.window="OnScroll">
                        <div id="content-zone-drop" ref="content-zone-drop" @drop.prevent="dropHandler"
                            @dragover.prevent="dragOverHandler" @dragleave.prevent="dropLeave">
                            <img id="img-zone-drop" :src="url+'img/upload_zone_icon.svg'" alt="Image Upload">
                            <h3 id="subtitle-img-zone">Subir Archivo(s)</h3>
                        </div>

                        <div class="card-body" id="zone-drop" @dragover.prevent="dragOverHandler">
                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <ul class="navbar-nav header-right">
                                        <li class="nav-item">
                                            <div class="input-group search-area d-xl-inline-flex d-none" style="width: 80%;">
                                                <input type="text" class="form-control" placeholder="Buscar en drive"
                                                    v-model="filter" @keyup="search">
                                                <!-- <div class="input-group-append">
                                                    <span class="input-group-text"><a href="javascript:void(0)"><i
                                                                class="flaticon-381-search-2" @click="search"></i></a></span>
                                                </div> -->
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-4" style="padding-left: 30px;">
                                    <h6>Almacenamiento Disponible:
                                        <span class="pull-right">{{ storage_company_group.text_total }}</span>
                                    </h6>
                                    <div class="progress ">
                                        <div class="progress-bar bg-danger progress-animated" ref="storage"
                                            style="width: 0%; height:6px;" role="progressbar" data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                            <!-- data-bs-placement="top" :title="storage_company_group.text_current_size"> -->
                                            <span class="sr-only">60% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive" style="min-height: 450px;">
                                <table class="table table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Propietario</th>
                                            <th>Fecha Creación</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="filteredData.length === 0">
                                            <tr>
                                                <td colspan="4" class="pt-3">No tienes elementos para mostrar.</td>
                                            </tr>
                                        </template>
                                        <template v-else v-for="(el, i) in filteredData" :key="i">
                                            <!-- <template v-if="validateRenderPermmissions(el)"> -->
                                                <tr class="hover-carpeta"  style="cursor: pointer">
                                                    <td @click="open(el)">
                                                        <template v-if="el.type === 'folder'">
                                                            <img :src="url+'img/carpeta.svg'" class="ico-folder" alt="folder">
                                                        </template>
                                                        <template v-if="el.type === 'file'">
                                                            <img :src="getIcoFiles(el.ext)" class="ico-folder" alt="folder">
                                                        </template>
                                                        <a class="ml-2" href="javascript:void(0)" >{{ el.nombre }}</a>
                                                    </td>
                                                    <td @click="open(el)">{{ el.propietario_nombre }}</td>
                                                    <td @click="open(el)">{{ formatDate(el.created_at) }}</td>
                                                    <td>
                                                        <div class="dropdown" >
                                                            <button  type="button" class="btn btn-light  light sharp"
                                                                data-toggle="dropdown">
                                                                <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <circle fill="#000000" cx="5" cy="12"
                                                                            r="2" />
                                                                        <circle fill="#000000" cx="12" cy="12"
                                                                            r="2" />
                                                                        <circle fill="#000000" cx="19" cy="12"
                                                                            r="2" />
                                                                    </g>
                                                                </svg>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <template v-if="el.type === 'folder' && permisos.includes('dri-dri-compartir')">
                                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                                        @click="openModalShare(el.nombre, el.id)">Compartir</a>
                                                                </template>
                                                                <a class="dropdown-item" href="javascript:void(0);"
                                                                    @click="openModalRename(el.id, el.nombre, el.type)"
                                                                    v-if="permisos.includes('dri-dri-cambiar_nombre')">
                                                                    Cambiar nombre
                                                                </a>
                                                                <a class="dropdown-item" href="javascript:void(0);"  v-if="permisos.includes('dri-dri-propiedades')"
                                                                    @click="openProperties(el)">Propiedades</a>
                                                                <template v-if="el.type === 'file' && permisos.includes('dri-dri-descargar')">
                                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                                        @click="downloadFile(el)">
                                                                        Descargar
                                                                    </a>
                                                                </template>
                                                                <a class="dropdown-item" href="javascript:void(0);"
                                                                    @click="deleteDoc(el.id, el.type)"  v-if="permisos.includes('dri-dri-eliminar')">
                                                                    Eliminar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <!-- </template> -->
                                        </template>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Compartir -->
        <div class="modal fade" ref="modal_share" id="modal_share" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ modal_share_title }}</h5>
                        <button type="button" class="close" @click="closeModalNewFolder"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3 row" v-show="!mode_share">
                            <label class="col-sm-2 col-form-label">Nombre
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Nombre de la carpeta"
                                    v-model="new_folder.name">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-2 col-form-label">Compartir por <span style="color: red;">*</span></label>

                            <div class="col-sm-10">
                                <Select_Savk ref="allow_share" :options="options_share" v-model="shareSelect" :maxItem="20" placeholder="Seleccione una opción" @selected="OnSelectedShare"/>
                            </div>
                        </div>

                        <div class="mb-3 row" v-if="shareSelect == 1">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Grupo Empresa
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select2-multiple-control
                                    :value="new_folder.company_group"
                                    id="select-grupo-empresa"
                                    ref="sel2CompanyGroup"
                                    :options="companies_group"
                                    @change="myChangeEventCompaniesGroup($event)"
                                />
                            </div>
                        </div>

                        <div class="mb-3 row" v-if="shareSelect == 2">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Empresa
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select2-multiple-control
                                    :value="new_folder.company"
                                    id="select-empresa"
                                    ref="sel2Company"
                                    :options="companies"
                                    @change="myChangeEventCompanies($event)"
                                />
                            </div>
                        </div>

                        <div class="mb-3 row" v-if="shareSelect == 3">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Centro de operación
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select2-multiple-control
                                    :value="new_folder.evaluation_point"
                                    id="select-pdv"
                                    ref="sel2Point"
                                    :options="evaluation_points"
                                    @change="myChangeEventEvaluationPoints($event)"
                                />
                            </div>
                        </div>

                        <div v-show="current_folder_id == null" class="mb-3 row">
                            <label class="col-sm-2 col-form-label">Tipo carpeta <span style="color: red;">*</span></label>

                            <div class="col-sm-10">
                                <Select_Savk ref="selectState" :options="options_state" v-model="this.new_folder.status" :maxItem="20" placeholder="Seleccione una opción" @selected="OnSelectedStatus"/>
                            </div>
                        </div>

                        <fieldset>
                            <legend>
                                <h6 style="display: flex;">Permisos
                                    <span style="color: red;">*</span>
                                </h6>
                            </legend>

                            <div class="row mb-2">

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="read" class="custom-control-input check-permits"
                                            v-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Lectura</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="create" class="custom-control-input check-permits"
                                            v-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Crear</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="update" class="custom-control-input check-permits"
                                            v-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Modificar</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="share" class="custom-control-input check-permits"
                                            v-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Compartir</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="delete" class="custom-control-input check-permits"
                                            v-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Eliminar</label>
                                    </div>
                                </div>

                            </div>

                        </fieldset>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="closeModalNewFolder">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="save">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin modal compartir -->
        <!-- MODAL RENOMBRAR -->
        <div class="modal fade" ref="modalRename" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Nombre</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" v-model="rename.name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="saveRename">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIN MODAL RENOMBRAR -->
        <!-- MODAL PROPIEDADES -->
        <div class="modal fade" ref="modalProperties" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Propiedades</h6>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="card-header border-0 pb-0" style="overflow-wrap: anywhere;">
                                        <h4 class="card-title">{{ property.name }}</h4>
                                    </div>
                                    <div class="card-body pb-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Propietario</strong>
                                                <span class="mb-0">{{ property.owner }}</span>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Tamaño</strong>
                                                <span class="mb-0">{{ property.tamano }}</span>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Fecha Creación</strong>
                                                <span class="mb-0">{{ property.created_at }}</span>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Fecha de Actualización</strong>
                                                <span class="mb-0">{{ property.updated_at }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-footer pt-0 pb-0 text-center">
                                        <div class="row">
                                            <div class="col-6 pt-3 pb-3 border-right">
                                                <h3 class="mb-1">{{ property.count_files }}</h3>
                                                <span>Cant. Archivos</span>
                                            </div>
                                            <div class="col-6 pt-3 pb-3 border-right">
                                                <h3 class="mb-1">{{ property.count_folders }}</h3>
                                                <span>Cant. Carpetas</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIN MODAL PROPIEDADES -->
        <!-- MODAL VER IMAGEN -->
        <div class="modal fade" ref="modalPreviewImage" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Previsualizar</h6>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img :src="preview_img" alt="preview" style="width: 90%; max-height: 300px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIN MODAL VER IMAGEN -->
        <!-- Modal subir doc tecnica -->
        <div class="modal fade" ref="modal_doc_tecnica" id="modal_doc_tecnica" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar documentación técnica</h5>
                        <button type="button" class="close" @click="closeModalDocTecnica"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a :class="`nav-link dev-tab dev-cursor-pointer ${(modal_doc_tecnica.tab_selected == 1 ? 'active' : '')}`"> Selección de etiqueta</a>
                            </li>
                            <li class="nav-item">
                                <a :class="`nav-link dev-tab dev-cursor-pointer ${(modal_doc_tecnica.tab_selected == 2 ? 'active' : '')}`"> Productos asociados</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active mt-4" v-show="modal_doc_tecnica.tab_selected == 1">
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Etiqueta <span style="color: red;">*</span></label>

                                    <div class="col-sm-10">
                                        <Select_Savk ref="select_etiqueta" v-model="modal_doc_tecnica.etiqueta" :options="modal_doc_tecnica.etiquetas" :maxItem="20" placeholder="Seleccione una opción" @selected="OnSelectedEtiqueta"/>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade show active mt-4 ml-4" v-show="modal_doc_tecnica.tab_selected == 2">
                                <ul v-for="item in modal_doc_tecnica.productos" :key="item.id">
                                    <li style="list-style-type: disc;" type="disc">{{item.name}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" v-show="modal_doc_tecnica.tab_selected == 2" @click="openModalCargarDoc" >Cargar documentos</button>
                        <button v-show="modal_doc_tecnica.tab_selected == 1" type="button" class="btn btn-primary" @click="NextTab">Siguiente</button>
                        <button v-show="modal_doc_tecnica.tab_selected == 2" type="button" class="btn btn-primary" @click="closeModalDocTecnica">Finalizar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal cargar documentacion -->
        <div class="modal fade" ref="modal_cargar_doc" id="modal_cargar_doc" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cargar documentación</h5>
                        <button type="button" class="close" @click="closeModalCargarDoc"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group col-md-12">
                            <label>Documentos: <span class="dev-required"></span></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" accept="application/pdf" @change="OnChangeFile" class="custom-file-input" multiple>
                                        <label class="custom-file-label">{{modal_doc_tecnica.documentos.length > 0 ? 'Elementos cargados ('+modal_doc_tecnica.documentos.length+')' : 'Cargar archivos...'}}</label>
                                    </div>
                                </div>
                        </div>
                        <div>
                            <table class="table card-table display dataTablesCard">
                                <thead>
                                    <tr>
                                        <th>Documento cargado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <tr v-for="item in modal_doc_tecnica.documentosCargados" :key="item.id">
                                        <td>{{item.nombre}}</td>
                                        <td><a href="#" @click="EliminarDocumento(item.id_documentacion)" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a></td>
                                   </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="cargarDocumento">Subir</button>
                        <button type="button" class="btn btn-primary" @click="closeModalCargarDoc">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";
export default {
    components:{
        Select_Savk
    },
    computed: {
        filteredData() {
            return this.data_all.filter(el => this.validateRenderPermmissions(el));
        }
    },
    props: {
        user: {
            type: [String, Number],
        },
        type: {
            type: [String, Number],//TIPO 1:DRIVE, 2:ENTORNO DE APRENDIZAJE
        },
    },
    async mounted()
    {
        this.loadStorage();
        await this.loadData()
        this.grupo == 30 || this.grupo == 39 ? this.OnClickSelectGE() : null
        this.zone = this.$refs["content-zone-drop"];

        if(this.permisos.includes('dri-dri-opcion-permanente')) {
            this.options_state.push({
                id: 1,
                name: "Permanente",
            });
        }else{
            this.options_state.push({
                id: 2,
                name: "Estandar",
            });
        }

        this.$refs.selectState.selectOption(this.options_state[0]);

        this.new_folder.status = this.options_state[0].id;
    },
    created(){

    },
    watch: {
        'storage_company_group.percentage': function(newPercentage, oldPercentage) {
            this.refreshStorageFront();
        },
    },
    data()
    {
        return {
            permisos : JSON.parse(localStorage.getItem('permisos')),
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            data_all : [],
            data_all_back: [],
            share: [],
            filter : '',
            file_ext_ico: {
                xls: url + "img/xls_ico.svg",
                xlsx: url + "img/xls_ico.svg",
                ppt: url + "img/ppt.svg",
                pdf: url + "img/pdf_ico.svg",
                docx: url + "img/doc_ico.svg",
                png: url + "img/image.svg",
                jpg: url + "img/image.svg",
            },
            history_folder_nav: [
                {
                    id: 0,
                    name: "Principal",
                    active: true,
                },
            ],
            current_folder_id: null,
            folder_parent_data: null,
            preview_img: "",
            storage_company_group: {
                text_current_size: "...",
                percentage: 1,
                total_size: 0,
                current_size: 0,
                text_total: "0 GB",
            },
            new_file: {
                files: new FormData(),
                folder_id: "",
            },
            excluded_files: [
                "application/zip",
                "application/vnd.debian.binary-package",
                "application/x-executable",
            ],
            modal_doc_tecnica:{
                tab_selected: 1,
                etiqueta: null,
                productos: [],
                etiquetas: [],
                documentos: [],
                documentosCargados: []
            },
            modal_share_title: "",
            mode_share: false,
            new_folder: {
                id: null,
                name: "",
                permissions: ["read", "create", "update", "share", "delete"],
                company_group: [],
                company: [],
                evaluation_point: [],
                parent: "",
                status: '',
            },
            company_group_required: true,
            company_required: false,
            evaluation_point_required: false,
            companies_group: [],
            companies: [],
            evaluation_points: [],
            rename: {
                id: null,
                name: "",
                type: "",
            },
            property: {
                name: "",
                owner: "",
                tamano: "",
                count_files: 0,
                created_at: "",
                updated_at: "",
                count_folders: 0,
            },
            zone: null,
            paginate: {
                current_page: 1,
                last_page: 1,
                next_page_url: ''
            },
            options_state:[

            ],
            options_share:[{
                id: 1,
                name: "Grupo empresa",
            },
            {
                id: 2,
                name: "Empresa ",
            },
            {
                id: 3,
                name: "Centro de operación",
            }],
            shareSelect: '',
            visibleElementsPermanentes: false,
            savk_principal: null,
            grupo: null,
            selectGrupoEmpresa: null,
            optionsFetch: (data) => ({
                method: "POST",
                headers: {
                    "Content-type": "application/json; charset=UTF-8",
                    "X-CSRF-Token": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify(data),
            }),
        }
    },
    methods: {
        async OnClickSelectGE()
        {
            let answer = await Swal({
                title: 'Selecciona el grupo empresa',
                input: 'select',
                inputOptions: this.share.gempresa,
                inputPlaceholder: 'Selecciona',
                cancelButtonText: "Cancelar",
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                confirmButtonColor: '#1f3352',
                cancelButtonColor: '#ff7f00',
                showCancelButton: false,
                inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                    if (value !== '')
                        resolve();
                    else
                        resolve('Debes seleccionar un grupo empresa obligatoriamente');
                    });
                }
            });

            if(answer.value)
            {
                this.history_folder_nav[0].name = "Principal " + this.share.gempresa[answer.value]
                this.selectGrupoEmpresa = Number(answer.value)
            }
        },
        OnSelectedShare(item){
            switch (item.id) {
                case 1:
                    this.new_folder.company = "";
                    this.new_folder.evaluation_point = "";

                    this.company_group_required = true
                    this.company_required = false
                    this.evaluation_point_required = false
                    break;
                case 2:
                    this.new_folder.company_group = "";
                    this.new_folder.evaluation_point = "";

                    this.company_group_required = false
                    this.company_required = true
                    this.evaluation_point_required = false
                    break;
                case 3:
                    this.new_folder.company_group = "";
                    this.new_folder.company = "";

                    this.company_group_required = false
                    this.company_required = false
                    this.evaluation_point_required = true
                    break;
            }
            this.shareSelect = item.id
        },
        OnSelectedStatus(item){
            this.new_folder.status = item.id
        },
        OnSelectedEtiqueta(item){
            this.modal_doc_tecnica.etiqueta = item.id
        },
        refreshStorageFront() {
            this.$refs.storage.style.width = `${this.storage_company_group.percentage}%`;
        },
        downloadFile(elem) {
            window.open(this.url + "drive/descargar-archivo/" + elem.id, "_blank");
        },
        OnScroll() {
            if (
                document.body.scrollHeight - window.innerHeight ===
                window.scrollY
            ) {
                if (this.paginate.current_page < this.paginate.last_page) {
                    this.paginate.current_page++
                    this.loadData(this.paginate.next_page_url)
                }
            }
        },
        removeDragData(ev) {
            if (ev.dataTransfer.items) {
                ev.dataTransfer.items.clear();
            } else {
                ev.dataTransfer.clearData();
            }
        },
        _activeZone(status = false) {
            if (status) {
                this.zone.classList.add("zone-active");
            } else {
                this.zone.classList.remove("zone-active");
            }
        },
        async dropHandler(ev) {
            if (ev.dataTransfer.items) {
                this._activeZone(false);
                for (var i = 0; i < ev.dataTransfer.items.length; i++) {
                    if (ev.dataTransfer.items[i].kind === "file") {
                        var file = ev.dataTransfer.items[i].getAsFile();
                        if (file.size > 10485760) {
                            toastr.warning(
                                `El archivo ${file.name} supera el límite de 10MB.`
                            );
                        } else if (this.excluded_files.includes(file.type)) {
                            toastr.warning(
                                "El tipo de archivo no está permitido."
                            );
                        } else {
                            if (this.validateStorageFree(file.size)) {
                                this.new_file.files.append("files[]", file);
                                this.new_file.files.append(
                                    "files_obj[]",
                                    JSON.stringify({
                                        name: file.name,
                                        type: file.type,
                                        size: file.size,
                                    })
                                );
                                this.new_file.folder_id = this.current_folder_id;
                            } else {
                                toastr.warning(
                                    "Supera la capacidad máxima de almacenamiento."
                                );
                            }
                        }
                    }
                }
                if (this.new_file.files.get("files[]")) this.uploadFiels();
            }
            this.removeDragData(ev);
        },
        dragOverHandler(ev) {
            if (!this.zone.classList.contains("zone-active")) {
                this._activeZone(true);
            }
        },
        dropLeave(){
            if (this.zone.classList.contains("zone-active")) {
                this._activeZone(false);
            }
        },
        async deleteDoc(id, type) {
            if(type == 'folder'){
                if (!await this.validateFolderPermissions(id, 'delete')) return;
            }else{
                if (!await this.validateFolderPermissions(this.current_folder_id, 'delete')) return;
            }

            const confir = await Swal.fire({
                type: "warning",
                text: "Una vez elimine este documento no podrá recuperar la información, está seguro?",
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                loading()
                const response = await fetch(
                    this.url + "drive/eliminar",
                    this.optionsFetch({
                        id,
                        type,
                    })
                );
                const { status, msg } = await response.json();
                loading(false)
                if (status != 200) toastr.warning(msg);

                //Limpio los archivos que hay en pantalla actualmente
                this.data_all = []
                this.data_all_back = []
                this.loadData();
                this.loadStorage();
            }
        },
        async openProperties(data) {
            this.property.name = data.nombre;
            this.property.owner = data.propietario_nombre;
            this.property.tamano = this.formatBytes(data.tamano, 2, true);
            this.property.created_at = this.formatDate(data.created_at);
            this.property.updated_at = this.formatDate(data.updated_at);
            this.property.count_files = data.cant_archivos ?? 1;
            this.property.count_folders = data.type === "folder" ? await this.countFolders(data.id) : 0;
            $(this.$refs.modalProperties).modal('show');
        },
        async countFolders(folder_id) {
            try {
                const response = await fetch(
                    this.url + "drive/cantidad-subcarpetas/" + folder_id
                );
                const resp = await response.json();

                return resp.data;
            } catch (error) {
                return 0;
            }
        },
        async openModalRename(id, name, type) {
            if(type == 'folder'){
                if (!await this.validateFolderPermissions(id, 'edit')) return;
            }else{
                if (!await this.validateFolderPermissions(this.current_folder_id, 'edit')) return;
            }
            // if (!await this.validateFolderPermissions(this.current_folder_id, 'edit', true)) return;
            this.rename.id = id;
            this.rename.name = name;
            this.rename.type = type;
            $(this.$refs.modalRename).modal('show');
        },
        async saveRename() {
            if (this.rename.name.length === 0) {
                toastr.warning("Debe escribir un nombre");
                return;
            }
            loading();
            const response = await fetch(
                this.url + "drive/renombrar",
                this.optionsFetch(this.rename)
            );
            const { status, msg } = await response.json();
            loading(false);

            if (status != 200) {
                toastr.warning(msg);
                return;
            }
            //Renombro el archivo en la variable data_all
            let index = this.data_all
                .map((el) => {
                    return el.id;
                })
                .indexOf(this.rename.id);
            this.data_all[index].nombre = this.rename.name;

            //Limpio varaible rename
            this.rename.name = "";
            this.rename.id = null;
            this.rename.type = null;
            $(this.$refs.modalRename).modal('hide');
        },
        closeModalNewFolder() {
            this.resetModalShare();
            this.resetNewFolderModel();
            this.mode_share = false;
            $(this.$refs.modal_share).modal('hide');
        },
        closeModalDocTecnica() {
            this.modal_doc_tecnica.tab_selected = 1;
            this.$refs.select_etiqueta.Clear();
            $(this.$refs.modal_doc_tecnica).modal('hide');
        },
        closeModalCargarDoc() {
            $(this.$refs.modal_cargar_doc).modal('hide');
        },
        resetModalShare() {
            this.new_folder.id = null;
            this.new_folder.company_group = "";
            this.new_folder.company = "";
            this.new_folder.evaluation_point = "";
            this.shareSelect = ""
            this.$refs.allow_share.Clear();
        },
        resetNewFolderModel() {
            this.new_folder.permissions = [
                "create",
                "update",
                "share",
                "delete",
                "read",
            ];
            this.new_folder.company_group = "";
            this.new_folder.company = "";
            this.new_folder.evaluation_point = "";
            this.shareSelect = ""
            this.$refs.allow_share.Clear();
        },
        validateFormModalShare() {
            let next = true;
            Object.keys(this.new_folder).forEach((prop) => {
                if (
                    this.new_folder[prop]?.length === 0 &&
                    prop != "parent" &&
                    prop != "company_group" &&
                    prop != "company" &&
                    prop != "evaluation_point" &&
                    !this.mode_share
                )
                    next = false;
            });
            if ((this.company_group_required && this.new_folder.company_group.length === 0) ||
                (this.company_required && this.new_folder.company.length === 0) ||
                (this.evaluation_point_required && this.new_folder.evaluation_point.length === 0)
            ) {
                next = false;
            }

            return next;
        },
        //Crear carpetas o cambiar permisos
        async save() {
            if (!this.validateFormModalShare()) {
                toastr.warning(
                    "Debe diligenciar todos los campos obligatorios (*)"
                );
                return;
            }

            loading();
            this.new_folder.parent = this.current_folder_id;
            this.new_folder.type = this.type;

            if (this.new_folder.company_group.includes('all')) {
                this.new_folder.company_group = this.companies_group.map(item => item.id); //asignamos todos los GE
                this.new_folder.company_group = this.new_folder.company_group.filter(id => id !== 'all');
            }else if (this.new_folder.company.includes('all')) {
                this.new_folder.company = this.companies.map(item => item.id); //asignamos todas las empresas
                this.new_folder.company = this.new_folder.company.filter(id => id !== 'all');
            }else if (this.new_folder.evaluation_point.includes('all')) {
                this.new_folder.evaluation_point = this.evaluation_points.map(item => item.id); //asignamos todos los ptos
                this.new_folder.evaluation_point = this.new_folder.evaluation_point.filter(id => id !== 'all');
            }

            const response = await fetch(
                this.url + "drive/nueva-carpeta",
                this.optionsFetch(this.new_folder)
            );
            const { status, msg, folder } = await response.json();
            this.closeModalNewFolder();
            loading(false);
            switch (status) {
                case 200:
                    toastr.success(msg);
                    if (folder != null)
                        this.data_all = [folder].concat(this.data_all);
                    break;

                case 500:
                    toastr.error(msg);
                    break;

                default:
                    toastr.warning(msg);
                    break;
            }
            this.loadStorage();
            this.data_all = [];
            this.data_all_back = [];
            this.loadData()
        },
        myChangeEventCompaniesGroup(val) {
            if (Array.isArray(val)) {
                this.new_folder.company_group = val;
            }
        },
        myChangeEventCompanies(val) {
            if (Array.isArray(val)) {
                this.new_folder.company = val;
            }
        },
        myChangeEventEvaluationPoints(val) {
            if (Array.isArray(val)) {
                this.new_folder.evaluation_point = val;
            }
        },
        async getCompnayGroup() {
            const response = await fetch(this.url + "drive/grupo-empresa");
            const res = await response.json();
            this.companies_group = res.data.map(({id, nombre}) => ({id: id, text: nombre}));
            this.companies_group.length > 0 ? this.companies_group.unshift({ id: 'all', text: 'Todos' }): null;
        },
        async getCompanies() {

            this.companies = [];
            const response = await fetch(this.url + "drive/empresas");
            const res = await response.json();
            this.companies = res.data.map(({id, nombre}) => ({id: id, text: nombre}));
            this.companies.length > 0 ? this.companies.unshift({ id: 'all', text: 'Todos' }): null;
        },
        async getEvaluationPoints() {
            this.evaluation_points = [];
            const response = await fetch(this.url + "drive/puntos");
            const res = await response.json();
            this.evaluation_points = res.data.map(({id, nombre}) => ({id: id, text: nombre}));
            this.evaluation_points.length > 0 ? this.evaluation_points.unshift({ id: 'all', text: 'Todos' }) : null;
        },
        async loadSelectsModalShare() {
            loading()
            await Promise.all([
                this.getCompnayGroup(),
                this.getCompanies(),
                this.getEvaluationPoints(),
            ]);
            loading(false)
        },
        async openModalNewFolder() {
            if (!await this.validateFolderPermissions(this.current_folder_id, 'write')) return;
            this.mode_share = false; //True para modo "Compartir"
            this.modal_share_title = "Nueva Carpeta";
            this.new_folder.name = "";
            await this.loadSelectsModalShare();
            $(this.$refs.modal_share).modal('show');
        },
        async openModalShare(name, folder_id) {
            if (!await this.validateFolderPermissions(folder_id, 'share')) return;
            this.mode_share = true;
            this.new_folder.id = folder_id;
            this.modal_share_title = "Compartir - " + name;
            //Cargo Grupo empresa, empresa y puntos de evaluación
            await this.loadSelectsModalShare();

            //Obtengo los permisos actuales de la carpeta
            const response = await fetch(
                this.url + "drive/obtener-permisos/" + folder_id
            );
            const res = await response.json();
            this.new_folder.permissions = res.permissions;
            this.new_folder.company_group = res.company_group;
            this.new_folder.evaluation_point = res.points;
            this.new_folder.company = res.companies;

            if (this.new_folder.company_group.length > 0) {
                this.shareSelect = 1
                this.$refs.allow_share.selectOption(this.options_share.find(option => option.id === this.shareSelect));
            }
            if (this.new_folder.company.length > 0) {
                this.shareSelect = 2
                this.$refs.allow_share.selectOption(this.options_share.find(option => option.id === this.shareSelect));

            }
            if (this.new_folder.evaluation_point.length > 0) {
                this.shareSelect = 3
                this.$refs.allow_share.selectOption(this.options_share.find(option => option.id === this.shareSelect));
            }

            $(this.$refs.modal_share).modal('show');
        },
        search(e) {
            if (this.filter.length != 0) {
                let obj = this.data_all_back.find((o, i) => {
                    if (o.nombre.toLowerCase().includes(this.filter))
                        return true;
                });

                this.data_all = obj != undefined ? [obj] : obj;
            } else {
                this.data_all = Object.assign([{}], this.data_all_back);
            }
        },
        validateStorageFree(file_size) {
            let next = true;

            let total = file_size + this.storage_company_group.current_size;
            if (total > this.storage_company_group.total_size) next = false;
            return next;
        },
        async uploadFiels() {
            if(!await this.validateFolderPermissions(this.current_folder_id, 'write')){
                this.new_file.files.delete("files[]");
                this.new_file.files.delete("folder_id");
                this.new_file.files.delete("files_obj[]");
                this.new_file.folder_id = "";

                return;
            }
            this.new_file.files.append("folder_id", this.new_file.folder_id);
            this.new_file.files.append("type", this.type);

            loading();
            const response = await fetch(this.url + "drive/subir-archivos", {
                headers: {
                    "X-CSRF-Token": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                method: "POST",
                body: this.new_file.files,
            });
            try {
                const { status, files } = await response.json();
                loading(false);
                if (status === 200) {
                    files.forEach((file) => {
                        this.data_all.push(file);
                    });
                }
            } catch (error) {
                loading(false);
                toastr.error("Ha ocurrido un error interno");
            }
            this.new_file.files.delete("files[]");
            this.new_file.files.delete("folder_id");
            this.new_file.files.delete("files_obj[]");
            this.new_file.folder_id = "";
            this.loadStorage();
        },
        async loadInputFiles(e) {
            let files = e.target.files;

            Object.values(files).forEach(async (file) => {
                if (file.size > 10485760) {
                    toastr.warning(
                        `El archivo ${file.name} supera el límite de 10MB.`
                    );
                } else if (this.excluded_files.includes(file.type)) {
                    toastr.warning("El tipo de archivo no está permitido.");
                } else {
                    if (this.validateStorageFree(file.size)) {
                        this.new_file.files.append("files[]", file);
                        this.new_file.files.append(
                            "files_obj[]",
                            JSON.stringify({
                                name: file.name,
                                type: file.type,
                                size: file.size,
                            })
                        );
                        this.new_file.folder_id = this.current_folder_id;
                    } else {
                        toastr.warning(
                            "Supera la capacidad maxíma de almacenamiento."
                        );
                    }
                }
            });
            if (this.new_file.files.get("files[]")) this.uploadFiels();
            this.$refs.uploadFiles.value = "";
        },
        btnUploadFile() {
            const input = this.$refs.uploadFiles;
            input.click();
        },
        formatBytes(bytes, decimals = 2, text = false) {
            if (!+bytes) return "0 Bytes";

            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = [
                "Bytes",
                "KB",
                "MB",
                "GB",
                "TB",
                "PB",
                "EB",
                "ZB",
                "YB",
            ];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            if (text) {
                return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]
                    }`;
            }

            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm));
        },
        async loadStorage() {
            const response = await fetch(this.url + "drive/almacenamiento");
            const { status, total_size, current_size, percentage } =
                await response.json();

            this.storage_company_group.total_size = total_size;
            this.storage_company_group.current_size = current_size;
            this.storage_company_group.percentage = percentage;
            this.storage_company_group.text_current_size = `Usado: ${this.formatBytes(
                current_size,
                2,
                true
            )}`;
            this.storage_company_group.text_total = this.formatBytes(
                total_size,
                2,
                true
            );
        },
        async getPermissionsFolderParent(id) {
            try {
                const response = await fetch(
                    url + "drive/obtener-permisos-carpeta/" + id
                );
                const resp = await response.json();
                return resp
            } catch (error) {
                return null
            }
        },
        async validateFolderPermissions(folder_id, access_type, subfolder = false) {
            let next = true;
            let folder = []

            if(folder_id == null || this.savk_principal == 1){ //esta en la carpeta raíz - SI ES SAVK 1 TIENE AUTORIDAD SOBRE TODO
                return next
            }

            folder = await this.getPermissionsFolderParent(folder_id)

            if(folder === null) {
                toastr.warning('No se pudo leer los permisos de la carpeta.')
                return false;
            }
            folder = [folder]

            if (Number(this.user) === folder[0].propietario_id) return next
            switch (access_type) {
                case 'read':
                    next = folder[0].permissions[0].lectura === 0 ? false : true
                    break;
                case 'write':
                    next = folder[0].permissions[0].escritura === 0 ? false : true
                    break;
                case 'delete':
                    next = folder[0].permissions[0].eliminar === 0 ? false : true
                    break;
                case 'edit':
                    next = folder[0].permissions[0].editar === 0 ? false : true
                    break;
                case 'share':
                    next = folder[0].permissions[0].compartir === 0 ? false : true
                    break;
            }
            if (!next) toastr.warning('No tiene permisos para realizar esta acción.')

            //VALIDAMOS QUE EL LIDER TENGA ACCESO A TODOS LOS GE, EMP O PUNTOS A LOS QUE LA CARPETA ESTA ASIGNADA
            //SI NO TIENE ACCESO SOLO EL SAVK_PRINCIPAL PUEDE HACER CAMBIOS O EL LIDES QUE TENGA ACCESO A TODOS
            if (next) {
                for (const el of folder[0].share) {
                    next = (this.share.gempresa.hasOwnProperty(el.centro_operacion_id) ||
                            this.share.empresa.includes(el.unidad_id) ||
                            this.share.punto.includes(el.punto_evaluacion_id));

                    if (!next) {
                        break; // Sale del ciclo cuando la condición no se cumple
                    }
                }
                if (!next) toastr.warning('La carpeta esta compartida con otro grupo empresa al que no tienes acceso.')
            }
            return next;
        },
        backFolder(id) {
            this.current_folder_id = id === 0 ? null : id;
            this.data_all = []
            this.data_all_back = []
            this.folder_parent_data = null
            this._resetActiveNav();
            let index = this.history_folder_nav
                .map((el) => {
                    return el.id;
                })
                .indexOf(id);
            //elimino el historico
            this.history_folder_nav.splice(
                index + 1,
                this.history_folder_nav.length - 1
            );
            loading();
            this.loadData();
            loading(false);
            setTimeout(() => {
                this.history_folder_nav[index].active = true;
            }, 1000);
        },
        async OnClickOpenModalDocTecnica(){
            const rs = await fetch(
                this.url + 'drive/get-etiquetas',
                {}
            );
            this.modal_doc_tecnica.etiquetas = await rs.json();


            $('#modal_doc_tecnica').modal('show');
        },
        async openModalCargarDoc(){
            $('#modal_cargar_doc').modal('show')
        },
        async NextTab(){
            loading();
            const rs = await fetch(
                this.url + 'drive/get-productos',
                {
                    method: 'POST',
                     body: JSON.stringify({
                      id_etiqueta:  this.modal_doc_tecnica.etiqueta
                     }),
                     headers: {
                        'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }
            );
            let rd = await rs.json();

            this.modal_doc_tecnica.productos = rd.productos;
            this.modal_doc_tecnica.documentosCargados = rd.documentos;

            this.modal_doc_tecnica.tab_selected = 2;
            loading(false);
        },
        async open(elem) {
            switch (elem.type) {
                case "folder":
                    if (!await this.validateFolderPermissions(elem.id, 'read', true)) return;
                    this.data_all = []
                    this.data_all_back = []
                    this.current_folder_id = elem.id;
                    this._resetActiveNav();
                    this.history_folder_nav.push({
                        id: elem.id,
                        name: elem.nombre,
                        active: true,
                    });
                    loading();
                    this.loadData();
                    loading(false);
                    break;

                case "file":
                    if (!await this.validateFolderPermissions(this.current_folder_id, 'read', true)) return;
                    if (elem.tipo.includes("image")) {
                        this.preview_img = url + "storage/" + elem.ruta;
                        $(this.$refs.modalPreviewImage).modal('show');
                    }
                    break;
            }
        },
        _resetActiveNav() {
            this.history_folder_nav.forEach((el) => {
                el.active = false;
            });
        },
        validateRenderPermmissions(item) {
            let visible = null
            if (item.type === 'folder') {

                if (this.savk_principal == 1 && item.tipo == Number(this.type)) {
                    visible = true //Son visibles todas las carpetas del main account
                }else if((this.grupo == 30 || this.grupo == 39) && item.tipo == Number(this.type)){
                    item.share.forEach(el => {
                        if(!visible){
                            visible = (this.share.gempresa.hasOwnProperty(el.centro_operacion_id)  ||
                                this.share.empresa.includes(el.unidad_id) ||
                                this.share.punto.includes(el.punto_evaluacion_id) ||
                                Number(this.user) === item.propietario_id)
                                && (item.tipo == Number(this.type) && item.grupoEmpresa.includes(this.selectGrupoEmpresa)) ? true : false
                        }
                    })
                }else{
                    item.share.forEach(el => {
                        if(!visible){
                            visible = (this.share.gempresa.hasOwnProperty(el.centro_operacion_id)  ||
                                this.share.empresa.includes(el.unidad_id) ||
                                this.share.punto.includes(el.punto_evaluacion_id) ||
                                Number(this.user) === item.propietario_id)
                                && (item.tipo == Number(this.type)) ? true : false
                        }
                    })
                }

                if ((visible == false || visible == null) && this.grupo != 30) {
                    //SI LA PERSONA ES PROPIETARIA DE LA CARPETA SIEMPRE SERA VISUALIZADA NO IMPORTA EL COMPARTIR
                    visible = Number(this.user) == item.propietario_id && item.tipo == Number(this.type) ? true : false
                }
            }else{
                if (item.carpeta_id == null) {
                    visible = Number(this.user) == item.propietario_id && item.tipo_drive == Number(this.type) ? true : false
                }else{
                    visible = item.tipo_drive == Number(this.type) ? true : false
                }
            }

            return visible
        },
        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleString("es-CO", {
                hour12: false,
                day: "2-digit",
                month: "long",
                year: "numeric",
            });
        },
        getIcoFiles(ext) {
            return this.file_ext_ico[ext] ?? this.url + "img/other.svg";
        },
        async loadData(url = "drive/data") {
            const rs = await fetch(
                this.url + url,
                this.optionsFetch({
                    current_folder: this.current_folder_id,
                })
            );
            let { status, data, paginate, share, savk_principal, grupo, isParentFolderPermanent } = await rs.json();
            switch (status) {
                case 200:
                    data.forEach(el => {
                        this.data_all.push(el)
                        this.data_all_back.push(el)
                    })
                    this.share = share
                    this.paginate.current_page = paginate?.current_page;
                    this.paginate.last_page = paginate.last_page;
                    this.paginate.next_page_url = paginate.next_page_url ?? 0
                    this.savk_principal = savk_principal
                    this.grupo = grupo
                    this.visibleElementsPermanentes = isParentFolderPermanent
                    break;

                default:
                    break;
            }
        },
        OnChangeFile(file)
        {
            if(file != undefined)
            {
                this.modal_doc_tecnica.documentos = file.target.files;
            }
        },
        async EliminarDocumento(id){
            const confir = await Swal.fire({
                type: "warning",
                text: "Una vez elimine este documento no podrá recuperar la información, ¿está seguro?",
                showCancelButton: true,
                confirmButtonText: "Si, eliminar",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                loading()
            const rs = await fetch(
                this.url + 'drive/eliminar-documentacion',
                this.optionsFetch({
                    id_etiqueta: this.modal_doc_tecnica.etiqueta,
                    id_doc: id
                })
            );
            let { status } = await rs.json();
            switch (status) {
                case 200:
                    this.data_all = [];
                    this.data_all_back = [];
                    this.loadData();
                    this.NextTab();
                    break;

                default:
                    break;
            }
            loading(false)
        }},
        async cargarDocumento(){
            loading()
            let data_form = new FormData();

            for (let index = 0; index < this.modal_doc_tecnica.documentos.length; index++) {
                data_form.append('files[]', this.modal_doc_tecnica.documentos[index]);
            }

            data_form.append('id_etiqueta', this.modal_doc_tecnica.etiqueta);
            data_form.append('folder_id', this.current_folder_id);
            data_form.append("type", this.type);

            const rs = await fetch(
                this.url + 'drive/cargar-documentacion-tecnica',
                {
                headers: {
                    "X-CSRF-Token": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                method: "POST",
                body: data_form,
                }
            );

            this.NextTab();
            this.modal_doc_tecnica.documentos = [];
            this.data_all = [];
            this.data_all_back = [];
            this.loadData();
            loading(false)
        }
    },
}
</script>

<style scoped>
 .bi{
    font-size: 22px !important;
 }


 .search-area .form-control {
    border-radius: 1.25rem !important;
    border-right: 1.25rem !important;
    border-top-left-radius: 1.25rem !important;
    border-bottom-left-radius: 1.25rem !important;
}

.hover-carpeta:hover{
    background-color: #002f5408;
}
</style>
