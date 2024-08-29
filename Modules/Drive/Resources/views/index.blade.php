@extends('layout.template')

@section('title_page', 'Drive')

@section('content')
    <div class="container-fluid" x-data="drive">
        <input type="hidden" value="{{ auth()->user()->id }}" x-ref="userAuth">
        <div class="page-titles row" style="display: flex;">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    {{-- <li class="breadcrumb-item"><a href="javascript:void(0)">Drive</a></li> --}}
                    <template x-for="(el, i) in history_folder_nav" :key="i">
                        <li class="breadcrumb-item" :class="el.active ? 'active' : ''">
                            <a href="javascript:void(0)" @click="backFolder(el.id)" x-text="el.name"></a>
                        </li>
                    </template>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end" style="display: flex !important; ">
                <a href="javascript:void(0)" class="btn btn-primary text-nowrap btn-sm mr-4 d-flex" @click="btnUploadFile">
                    <img src="{{ asset('img/upload_icon.svg') }}" alt="upload" class="ico_svg_button center-p">
                    <p class="center-p">Subir Archivo</p>
                    <input x-ref='uploadFiles' type='file'
                        accept="image/*,
                    .txt,
                    .doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        @change="loadInputFiles" multiple hidden />
                </a>
                <a href="javascript:void(0)" class="btn btn-primary text-nowrap btn-sm d-flex" style="padding-top: 7px;"
                    @click="openModalNewFolder">
                    <img src="{{ asset('img/folder_icon_btn.svg') }}" alt="upload"
                        class="ico_svg_button img-folder-btn center-p">
                    <p class="center-p">Nueva Carpeta</p>
                </a>
            </div>
        </div>
        <!-- row -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card" style="min-height: 500px;" @scroll.window="OnScroll">
                    <div id="content-zone-drop" x-ref="content-zone-drop" @drop="dropHandler(event);"
                        @dragover="dragOverHandler(event);">
                        <!-- Image by zone drap and drop -->
                        <img id="img-zone-drop" src="{{ asset('img/upload_zone_icon.svg') }}" alt="Image Upload">
                        <h3 id="subtitle-img-zone">Subir Archivo(s)</h3>
                    </div>

                    <div class="card-body" id="zone-drop" @dragover="dragOverHandler(event);">
                        <div class="row">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4">
                                <ul class="navbar-nav header-right">
                                    <li class="nav-item">
                                        <div class="input-group search-area d-xl-inline-flex d-none" style="width: 80%;">
                                            <input type="text" class="form-control" placeholder="Buscar en drive"
                                                x-model="filter" @keyup.enter="search">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><a href="javascript:void(0)"><i
                                                            class="flaticon-381-search-2" @click="search"></i></a></span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-4" style="padding-left: 30px;">
                                <h6>Almacenamiento Disponible:
                                    <span class="pull-right" x-text="storage_company_group.text_total"></span>
                                </h6>
                                <div class="progress ">
                                    <div class="progress-bar bg-danger progress-animated" x-ref="storage"
                                        style="width: 0%; height:6px;" role="progressbar" data-bs-toggle="tooltip"
                                        data-bs-placement="top" :title="storage_company_group.text_current_size">
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
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(el, i) in data_all" :key="i">
                                        <template x-if="validateRenderPermmissions(el)">
                                            <tr>
                                                <td>
                                                    <template x-if="el.type === 'folder'">
                                                        <img src="{{ asset('img/carpeta.svg') }}" class="ico-folder"
                                                            alt="folder">
                                                    </template>
                                                    <template x-if="el.type === 'file'">
                                                        <img :src="getIcoFiles(el.ext)" class="ico-folder" alt="folder">
                                                    </template>
                                                    <a href="javascript:void(0)" x-text="el.nombre" @click="open(el)"></a>
                                                </td>
                                                <td x-text="el.propietario_nombre">
                                                </td>
                                                <td x-text="formatDate(el.created_at)"></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-light  light sharp"
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
                                                            <template x-if="el.type === 'folder' ">
                                                                <a class="dropdown-item" href="javascript:void(0);"
                                                                    @click="openModalShare(el.nombre, el.id)">Compartir</a>
                                                            </template>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                @click="openModalRename(el.id, el.nombre, el.type)">Cambiar
                                                                nombre</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                @click="openProperties(el)">Propiedades</a>
                                                            {{-- <template>
                                                            <a class="dropdown-item" href="javascript:void(0);">Descargar
                                                                (.zip)
                                                            </a>
                                                        </template> --}}
                                                            <template x-if="el.type === 'file'">
                                                                <a class="dropdown-item" href="javascript:void(0);"
                                                                    @click="downloadFile(el)">
                                                                    Descargar
                                                                </a>
                                                            </template>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                @click="deleteDoc(el.id, el.type)">
                                                                Eliminar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Compartir -->
        <div class="modal fade" x-ref="modal-share" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title " x-text="modal_share_title"></h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3 row" x-show="!mode_share">
                            <label class="col-sm-2 col-form-label">Nombre
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Nombre de la carpeta"
                                    x-model="new_folder.name">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Grupo Empresa
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select id="select-grupo-empresa" class="multi-select" x-ref="sel2CompanyGroup">
                                    <option value="all" selected>Todos</option>
                                    <template x-for="el in companies_group">
                                        <option :value="el.id" x-text="el.nombre"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Empresa
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select id="select-empresa" class="multi-select" placeholder="Seleccione una empresa"
                                    x-ref="sel2Company">
                                    <option value="all" selected>Todos</option>
                                    <template x-for="el in companies">
                                        <option :value="el.id" x-text="el.nombre"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Punto de Venta
                                <span style="color: red;">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select id="select-pdv" class="multi-select " placeholder="Seleccione un punto de venta"
                                    x-ref="sel2Point">
                                    <option value="all" selected>Todos</option>
                                    <template x-for="el in evaluation_points">
                                        <option :value="el.id" x-text="el.nombre"></option>
                                    </template>
                                </select>
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
                                            x-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Lectura</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="create" class="custom-control-input check-permits"
                                            x-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Crear</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="update" class="custom-control-input check-permits"
                                            x-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Modificar</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="share" class="custom-control-input check-permits"
                                            x-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Compartir</label>
                                    </div>
                                </div>

                                <div class="col-sm-3 col-6">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" value="delete" class="custom-control-input check-permits"
                                            x-model="new_folder.permissions">
                                        <label class="custom-control-label" for="customCheckBox1">Eliminar</label>
                                    </div>
                                </div>

                            </div>

                        </fieldset>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="save">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin modal compartir -->

        <!-- MODAL RENOMBRAR -->
        <div class="modal fade" x-ref="modalRename" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <input type="text" class="form-control" x-model="rename.name">
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
        <div class="modal fade" x-ref="modalProperties" tabindex="-1" role="dialog" aria-hidden="true">
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
                                        <h4 class="card-title" x-text="property.name"></h4>
                                    </div>
                                    <div class="card-body pb-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Propietario</strong>
                                                <span class="mb-0" x-text="property.owner"></span>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Tamaño</strong>
                                                <span class="mb-0" x-text="property.tamano"></span>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Fecha Creación</strong>
                                                <span class="mb-0" x-text="property.created_at">
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <strong>Fecha de Actualización</strong>
                                                <span class="mb-0" x-text="property.updated_at"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-footer pt-0 pb-0 text-center">
                                        <div class="row">
                                            <div class="col-6 pt-3 pb-3 border-right">
                                                <h3 class="mb-1 text-primary" x-text="property.count_files"></h3>
                                                <span>Cant. Archivos</span>
                                            </div>
                                            <div class="col-6 pt-3 pb-3 border-right">
                                                <h3 class="mb-1 text-primary" x-text="property.count_folders"></h3>
                                                <span>Cant. Carpetas</span>
                                            </div>
                                            {{-- <div class="col-4 pt-3 pb-3">
                                                <h3 class="mb-1 text-primary" ></h3>
                                                <span>Tamaño</span>
                                            </div> --}}
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
        <div class="modal fade" x-ref="modalPreviewImage" tabindex="-1" role="dialog" aria-hidden="true">
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

    </div>
@endsection
