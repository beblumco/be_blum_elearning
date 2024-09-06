<template>
    <div class="container-fluid">

        <div class="card">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs justify-content-center">
                                <li class="nav-item">
                                    <a :class="`nav-link dev-tab dev-cursor-pointer ${(data.tab_selected == 1 ? 'active' : '')}`"><i class="la la-homela la-graduation-cap mr-2"></i> Información de capacitación</a>
                                </li>
                                <li class="nav-item">
                                    <a :class="`nav-link dev-tab dev-cursor-pointer ${(data.tab_selected == 2 ? 'active' : '')}`"><i class="la la-cube mr-2"></i> Información de módulos</a>
                                </li>
                                <li class="nav-item" v-if="this.data.modules_section.assessBy == 1">
                                    <a :class="`nav-link dev-tab dev-cursor-pointer ${(data.tab_selected == 3 ? 'active' : '')}`"><i class="la la-list-alt mr-2"></i> Evaluación capacitación</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" v-show="data.tab_selected == 1">
                                    <div class="col-lg-12 mt-4">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Nombre de capacitación: <span class="dev-required">{{ (data.training_section.form_data.training_name.required ? '*' : '') }}</span></label>
                                                <input type="text" v-model="data.training_section.form_data.training_name.value" class="form-control" placeholder="...">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Diseñada por: <span class="dev-required">{{ (data.training_section.form_data.designedBy.required ? '*' : '') }}</span></label>
                                                <input type="text" v-model="data.training_section.form_data.designedBy.value" class="form-control" placeholder="XXXXX XXXX, Ingeniero de Alimentos">
                                            </div>
                                            <!-- <div class="form-group col-md-6">
                                                <label>Descripción: <span class="dev-required">{{ (data.training_section.form_data.description.required ? '*' : '') }}</span></label>
                                                <textarea class="form-control" v-model="data.training_section.form_data.description.value" rows="4" placeholder="..."></textarea>
                                            </div> -->
                                            <div class="form-group col-md-6">
                                                <label>Imagen principal: <span class="dev-required">{{ (data.training_section.form_data.principal_image.required ? '*' : '') }}</span></label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" accept="image/*" @change="OnChangeFile" class="custom-file-input">
                                                        <label class="custom-file-label">{{ data.training_section.default.label_file }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Tiempo (Minutos): <span class="dev-required">{{ (data.training_section.form_data.time.required ? '*' : '') }}</span></label>
                                                <input type="text" v-model.lazy="data.training_section.form_data.time.value" class="form-control" placeholder="..." @input="validarNumeros" >
                                            </div>

                                            <div class="form-group col-md-6" v-if="data.training_section.training_type_show" id="dv-crear-tipo-cap">
                                                <label>Tipo de capacitación: <span class="dev-required">{{ (data.training_section.form_data.type_training.required ? '*' : '') }}</span></label>
                                                <div class="col-lg-12 d-flex ">
                                                    <Select_Savk id="type_training" ref="type_training" v-model="data.training_section.form_data.type_training.value" :options="data.training_section.default.options_type_training" :maxItem="20" placeholder="Seleccione una opción" @selected="OnSelectedItemtype"/>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6" v-show="data.training_section.default.typeTrainingSelect && data.training_section.training_asign_show">
                                                <label>Asignar capacitación: <span class="dev-required">{{ (data.training_section.form_data.assign.required ? '*' : '') }}</span></label>

                                                <div class="col-lg-12 d-flex ">
                                                    <div class="custom-control custom-checkbox mb-3" v-show="data.training_section.show_sector">
                                                        <input type="checkbox" class="custom-control-input" id="sector_control" v-model="data.training_section.default.sector_check" @change="OnChangeAssign(1)">
                                                        <label class="custom-control-label" for="sector_control">A sector</label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox mb-3 ml-3" v-if="data.training_section.form_data.type_training.value != 3">
                                                        <input type="checkbox" class="custom-control-input" id="centro_op_control" v-model="data.training_section.default.centro_emp_check" @change="OnChangeAssign(2)">
                                                        <label class="custom-control-label" for="centro_op_control">A grupo empresa</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6" v-show="data.training_section.default.typeTrainingSelect">
                                                <label>{{ data.training_section.default.label_assign }}: <span class="dev-required">{{ (data.training_section.form_data.assign.required ? '*' : '') }}</span></label>

                                                <div class="form-group" v-show="data.training_section.form_data.assign.value == 1">
                                                    <select2-multiple-control
                                                                        :value="data.training_section.form_data.id_selected.value"
                                                                        ref="select_sector"
                                                                        :options="data.training_section.sectors"
                                                                        @change="myChangeEventSector($event)"
                                                    />
                                                </div>

                                                <div class="form-group" v-show="data.training_section.form_data.assign.value == 2">
                                                    <select2-multiple-control
                                                                        :value="data.training_section.form_data.id_selected.value"
                                                                        ref="select_centro_operacion"
                                                                        :options="data.training_section.operations_center"
                                                                        @change="myChangeEventOperationCenter($event)"
                                                    />
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6" id="dv-crear-certificara-cap">
                                                <label>¿Certificará?: <span class="dev-required">{{ (data.training_section.form_data.certify.required ? '*' : '') }}</span></label>

                                                <div class="col-lg-12 d-flex ">
                                                    <div class="custom-control custom-checkbox mb-3">
                                                        <input type="checkbox" class="custom-control-input" id="certify_yes" v-model="data.training_section.default.certify_yes" @change="OnChangeCertify(1)">
                                                        <label class="custom-control-label" for="certify_yes">Si</label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox mb-3 ml-3">
                                                        <input type="checkbox" class="custom-control-input" id="certify_no" v-model="data.training_section.default.certify_no" @change="OnChangeCertify(2)">
                                                        <label class="custom-control-label" for="certify_no">No</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6" v-show="data.training_section.default.certify_yes && data.training_section.form_data.type_training.value != 3" >
                                                <label>Certificará por: <span class="dev-required">{{ (data.training_section.form_data.certified.required ? '*' : '') }}</span></label>

                                                <div class="col-lg-12 d-flex ">
                                                    <Select_Savk id="allow_certified" ref="allow_certified" v-model="data.training_section.form_data.certified.value" :options="data.training_section.default.options_certified" :maxItem="20" placeholder="Seleccione una opción" @selected="OnSelectedItem"/>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6" id="dv-crear-puntos-cap">
                                                <label>Puntos: <span class="dev-required">{{ (data.training_section.form_data.points.required ? '*' : '') }}</span></label>
                                                <input type="text" v-model.lazy="data.training_section.form_data.points.value" class="form-control" placeholder="..." @input="validarNumeros" >
                                            </div>

                                            <div class="form-group col-md-6" id="dv-crear-evaluara-cap">
                                                <label>¿Evaluará? <span class="dev-required">{{ (data.training_section.form_data.assess.required ? '*' : '') }}</span></label>

                                                <div class="col-lg-12 d-flex ">
                                                    <div class="custom-control custom-checkbox mb-3">
                                                        <input type="checkbox" class="custom-control-input" id="assess_yes" v-model="data.training_section.default.assess_yes" @change="OnChangeAssess(1)">
                                                        <label class="custom-control-label" for="assess_yes">Si</label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox mb-3 ml-3">
                                                        <input type="checkbox" class="custom-control-input" id="assess_no" v-model="data.training_section.default.assess_no" @change="OnChangeAssess(2)">
                                                        <label class="custom-control-label" for="assess_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6" v-show="data.training_section.default.assess_yes && data.training_section.form_data.type_training.value != 3">
                                                <label>Evaluará por: <span class="dev-required">{{ (data.training_section.form_data.assessBy.required ? '*' : '') }}</span></label>

                                                <div class="col-lg-12 d-flex ">
                                                    <Select_Savk id="assess_by" ref="assess_by" v-model="data.training_section.form_data.assessBy.value" :options="data.training_section.default.options_assess" :maxItem="20" placeholder="Seleccione una opción" @selected="OnSelectedAssess"/>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6" v-if="data.training_section.form_data.type_training.value == 3">
                                                <label>Precio: <span class="dev-required">{{ (data.training_section.form_data.price.required ? '*' : '') }}</span></label>
                                                <input type="text" v-model.lazy="data.training_section.form_data.price.value" class="form-control" placeholder="..." @input="validarNumeros" >
                                            </div>
                                            <!-- <div class="form-group col-md-6" v-if="data.training_section.form_data.type_training.value == 3">
                                                <label>Fecha realización: <span class="dev-required">{{ (data.training_section.form_data.date_webinars.required ? '*' : '') }}</span></label>
                                                <input type="text" v-model="data.training_section.form_data.date_webinars.value" class="form-control" placeholder="...">
                                            </div> -->

                                            <div class="form-group col-md-6" v-show="data.training_section.form_data.type_training.value == 3">
                                                <label>Estado: <span class="dev-required">{{ (data.training_section.form_data.estado.required ? '*' : '') }}</span></label>

                                                <div class="col-lg-12 d-flex ">
                                                    <div class="custom-control custom-checkbox mb-3">
                                                        <input type="checkbox" class="custom-control-input" id="estado_agendado" v-model="data.training_section.default.estadoAgendado" @change="OnChangeEstado(1)">
                                                        <label class="custom-control-label" for="estado_agendado">Agendado</label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox mb-3 ml-3">
                                                        <input type="checkbox" class="custom-control-input" id="estado_finalizado" v-model="data.training_section.default.estadoFinalizado" @change="OnChangeEstado(2)">
                                                        <label class="custom-control-label" for="estado_finalizado">Finalizado</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6" v-show="data.training_section.form_data.type_training.value == 3">
                                                <label>Fecha realización: <span class="dev-required">{{ (data.training_section.form_data.date_webinars.required ? '*' : '') }}</span></label>
                                                <input type="text" class="form-control" v-model="data.training_section.form_data.date_webinars.value" id="min-date" ref="fecha_webinar">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 p-0 m-0 mt-3 d-flex justify-content-end">
                                        <button class="btn btn-primary" @click="OnClickContinueSection">Continuar</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade show active" v-if="data.tab_selected == 2">
                                    <div class="form-row mt-4">
                                        <!-- <div class="form-group col-lg-10">
                                            <label>Nombre del módulo: <span class="dev-required">{{ (data.modules_section.form_data.module_name.required ? '*' : '') }}</span></label>
                                            <input type="text" v-model="data.modules_section.form_data.module_name.value" class="form-control" placeholder="...">
                                        </div> -->
                                        <div class="form-group col-lg-2">
                                            <label style="opacity: 0;">Nombre</label>
                                            <!-- <button class="btn btn-primary" @click="OnClickAddModule">Agregar módulo</button> -->
                                            <button id="dv-crear-agregar-mod-cap" class="btn btn-primary" @click="OnClickOpenModalAddModulo">Agregar módulo</button>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <module-component v-for="(data_module, index) in data.modules_section.modules" :key="data_module.id"
                                        :data_component="data_module" :assessBy="this.data.modules_section.assessBy" :contador="index"
                                        :show="false"
                                        @edit_pop_up="OnClickEditInformation"
                                        @delete_module="OnClickDeleteModule"
                                        @open_modal_test="OnClickOpenModalTest(data_module)"
                                        @open_modal_content="OnClickOpenModalContent(data_module)"
                                        @open_modal_video="OnClickOpenModalVideo(data_module)"></module-component>
                                    </div>

                                    <div class="col-lg-12 p-0 m-0 mt-3 d-flex justify-content-between">
                                        <button class="btn btn-primary" @click="OnClickBackSection">Atrás</button>
                                        <button class="btn btn-primary" @click="OnClickFinallySection" v-if="this.data.modules_section.assessBy != 1">Finalizar</button>
                                        <button class="btn btn-primary" @click="OnClickContinueSectionTest" v-if="this.data.modules_section.assessBy == 1">Continuar</button>
                                    </div>
                                </div>


                                <div class="tab-pane fade show active" v-if="data.tab_selected == 3">

                                    <div class="modal-header">
                                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                                            <h5 class="modal-title">Agregar evaluación</h5>
                                            <button class="btn btn-primary dev-font-11" @click="OnClickAddQuestion">Agregar pregunta</button>
                                        </div>
                                    </div>
                                    <div class="modal-body p-2 ">
                                        <div class="col-lg-12 dev-over-flow dev_container_questions">
                                            <div class="form-inline col-md-6">
                                                <label>Porcentaje de aprobación: <span class="dev-required">{{ (data.form_data.porcentajeAprobacion.required ? '*' : '') }}</span></label>
                                                <input type="text" class="form-control ml-3" @input="validarNumeros" v-model.lazy="data.form_data.porcentajeAprobacion.value">
                                            </div>
                                            <p class="col-lg-12 dev-subtitle">Esta sección es para que puedes ir agregando las preguntas que tendrá la evaluación del módulo, <strong>agregue las preguntas y seleccione la/las respuestas correctas.</strong></p>

                                            <question-component v-for="question in data.section_test.data_test" :key="question.id" :data-id="question.id" :data_test="question" @scroll_down="FunctionScrollDown" @remove_question="RemoveQuestion" class="questions_class"></question-component>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 p-0 m-0 mt-3 d-flex justify-content-between">
                                        <button class="btn btn-primary" @click="OnClickBackSection2">Atrás</button>
                                        <button class="btn btn-primary btn-finish-test" @click="OnClickSaveChangesTest">Finalizar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL AGREGAR MODULO -->
        <div class="modal fade" id="modal_add_modulo" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="modal-title">Agregar módulo</h5>
                        </div>
                    </div>
                    <div class="modal-body p-2 ">
                        <div class="form-group col-lg-10 offset-lg-1">
                            <label>Nombre del módulo: <span class="dev-required">{{ (data.modules_section.form_data.module_name.required ? '*' : '') }}</span></label>
                            <input type="text" v-model="data.modules_section.form_data.module_name.value" class="form-control" placeholder="...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalAddModulo">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="OnClickAddModule">Agregar módulo</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL AGREGAR INFORMACIÓN AL MÓDULO -->
        <div class="modal fade" id="modal_edit_basic_information" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar información</h5>
                        <button type="button" class="close" @click="OnClickCloseModalModule"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del módulo: <span class="dev-required">{{ (data.modules_section.modal_module_information.form_data.name.required ? '*' : '') }}</span></label>
                                    <input type="text" v-model="data.modules_section.modal_module_information.form_data.name.value" class="form-control" placeholder="...">
                                </div>
                                <!-- <div class="form-group col-md-6">
                                    <label>Descripción: <span class="dev-required">{{ (data.modules_section.modal_module_information.form_data.description.required ? '*' : '') }}</span></label>
                                    <textarea class="form-control" v-model="data.modules_section.modal_module_information.form_data.description.value" rows="4" placeholder="..."></textarea>
                                </div> -->
                                <!-- <div class="form-group col-md-6">
                                    <label>Imagen: <span class="dev-required">{{ ( data.modules_section.modal_module_information.form_data.image.required ? '*' : '') }}</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" accept="image/*" @change="OnChangeFileModule" class="custom-file-input">
                                            <label class="custom-file-label">{{ data.modules_section.modal_module_information.default.label_file }}</label>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- <div class="form-group col-md-6">
                                    <label>Porcentaje para aprobar: <span class="dev-required">{{ (data.modules_section.modal_module_information.form_data.percentage.required ? '*' : '') }}</span></label>
                                    <input type="text" v-model="data.modules_section.modal_module_information.form_data.percentage.value" class="form-control" placeholder="...">
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalModule">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="OnClickSaveChanges">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL AGREGAR INFORMACIÓN AL MÓDULO - END-->

        <!-- MODAL AGREGAR EVALUACIÓN -->
        <div class="modal fade" id="modal_add_test" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="modal-title">Agregar evaluación</h5>
                            <button class="btn btn-primary dev-font-11" @click="OnClickAddQuestion">Agregar pregunta</button>
                        </div>
                    </div>
                    <div class="modal-body p-2 ">
                        <div class="col-lg-12 dev-over-flow dev_container_questions">
                            <div class="form-inline col-md-6">
                                <label>Porcentaje de aprobación: <span class="dev-required">{{ (data.form_data.porcentajeAprobacion.required ? '*' : '') }}</span></label>
                                <input type="text" class="form-control ml-3 mt-2" @input="validarNumeros" v-model.lazy="data.form_data.porcentajeAprobacion.value">
                            </div>
                            <p class="col-lg-12 dev-subtitle">Esta sección es para que puedes ir agregando las preguntas que tendrá la evaluación del módulo, <strong>agregue las preguntas y seleccione la/las respuestas correctas.</strong></p>
                            <question-component v-for="question in data.section_test.data_test" :key="question.id" :data-id="question.id" :data_test="question" @scroll_down="FunctionScrollDown" @remove_question="RemoveQuestion" class="questions_class"></question-component>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalTest">Cerrar</button>
                        <button type="button" class="btn btn-primary btn-finish-test" @click="OnClickSaveChangesTest">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL AGREGAR EVALUACIÓN - END-->

        <!-- MODAL AGREGAR CONTENIDO -->
        <div class="modal fade" id="modal_add_content" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="modal-title">Agregar recurso</h5>
                            <button class="btn btn-primary dev-font-11" @click="OnClickAddContent">Agregar recurso</button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex flex-wrap">
                            <content-module-component v-for="content in data.content_section.contents" :key="content.id" :data_content="content" @view_content="OnClickModalViewContent" @action_content="OnClickContentChange" @change_order="OnChangeNumberOrder"></content-module-component>
                            <p class="col-lg-12 text-center font-weight-bold" v-if="data.content_section.contents.length == 0">No tienes contenido cargado para este módulo</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContent">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL AGREGAR CONTENIDO - END-->

        <!-- MODAL ADJUNTAR IMAGEN -->
        <div class="modal fade" id="modal_add_content_image" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar imagen</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="form-group col-md-6">
                                <label> Adjuntar imagen: <span class="dev-required">{{ (data.content_section.form_data.image.required ? '*' : '') }}</span></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" @change="OnChangeFileContenImage" class="custom-file-input">
                                        <label class="custom-file-label">{{ data.content_section.default.label_file }}</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContentImage">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="OnClickSaveContentImage">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL ADJUNTAR IMAGEN - END-->

        <!-- MODAL ADJUNTAR PDF -->
        <div class="modal fade" id="modal_add_content_pdf" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar documento PDF</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="form-group col-md-6">
                                <label> Adjuntar pdf: <span class="dev-required">{{ (data.content_section.form_data.pdf.required ? '*' : '') }}</span></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" accept="application/pdf" @change="OnChangeFileContentPDF" class="custom-file-input" ref="file_pdf_content">
                                        <label class="custom-file-label">{{ data.content_section.default.label_file }}</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContentPDF">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="OnClickSaveContentPDF">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL ADJUNTAR PDF - END-->

        <!-- MODAL AGREGAR LINK VIDEO -->
        <div class="modal fade" id="modal_add_content_video" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar link de video</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="form-group col-md-6">
                                <label> Agrega link : <span class="dev-required">{{ (data.content_section.form_data.video.required ? '*' : '') }}</span></label>
                                <div class="form-group">
                                    <input type="text" class="form-control input-default" v-model="data.content_section.form_data.video.value" placeholder="(Youtube)">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContentVideo">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="OnClickSaveContentVideo">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL AGREGAR LINK VIDEO - END-->

        <!-- MODAL CAMBIAR ORDEN -->
        <div class="modal fade" id="modal_change_order" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar orden contenido</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="form-group col-md-6">
                                <label>Orden de contenido: <span class="dev-required">{{ (data.content_section.modal_order.form_data.order.required ? '*' : '') }}</span></label>
                                <div class="form-group">
                                    <input type="text" class="form-control input-default input-number" v-model="data.content_section.modal_order.form_data.order.value" placeholder="Orden...">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContentOrder">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="OnClickSaveOrderContent">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL CAMBIAR ORDEN - END-->

        <!-- MODAL VISTA PREVIA CONTENIDO -->
        <div class="modal fade" id="modal_view_content" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                            <h3>Vista previa del contenido</h3>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="justify-content-center">
                            <iframe :src="data.content_section.url_content" width="738" height="407" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalViewContent">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL MODAL VISTA PREVIA CONTENIDO - END-->
    </div>
</template>

<!-- <script src="training_section.js"></script> -->
<script>
    import ModuleComponent from "./ModuleComponent.vue";
    import QuestionComponent from "./QuestionComponent.vue";
    import ContentModuleComponent from "./ContentModuleComponent.vue";
    import Select_Savk from "../../../../../../resources/js/components/pages/otros/Select_Savk.vue";
    import { training_section, training_events } from "./js/training_section";
    import { module_section, module_events } from "./js/module_section";
    import { section_test, section_test_events } from "./js/section_test";
    import { content_section, content_events } from "./js/content_section";
    import { validarNumeros, guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas } from "../../../../../../public/assets/js/functions.js";

    export default {
        props:
        {
            id_training: String,
            id_grupo: String,
            id_main_account: String
        },
        components:
        {
            ModuleComponent,
            QuestionComponent,
            ContentModuleComponent,
            Select_Savk
        },
        async created()
        {
            if(this.id_grupo == 27)
            {
                this.data.training_section.show_sector = false;
            }
            else
            {
                this.data.training_section.show_sector = true;
            }
            await this.GetDataInit();
            if(this.id_main_account != 1 && this.id_main_account != 2) //SUPER ADMIN
            {
                switch (this.id_grupo)
                {
                    case 44: //Lider grupo empresa
                        if(this.data.training_section.form_data.id_selected.value.length == 0)
                        {
                            let optionTypeTraining = this.data.training_section.default.options_type_training.find(item => item.id == 1); //ASIGNACIÓN PRIVADA
                            optionTypeTraining ? this.$refs.type_training.selectOption(optionTypeTraining) : null;
                            this.OnChangeAssign(2); //SIEMPRE GRUPO EMPRESARIAL
                        }
                        this.data.training_section.training_asign_show = false;
                        this.data.training_section.training_type_show = false;
                        break;

                    default:
                        break;
                }
            }
        },
        async mounted()
        {
            await this.guiaGetAll();
            this.CreateTour(this.guias);
            this.tour.start();
        },
        computed:
        {

        },
        data()
        {
            return {
                guias: [],
                guiasSecundarias: [],
                tour: null,
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                data:
                {
                    tab_selected: 1,
                    ...training_section,
                    ...module_section,
                    ...section_test,
                    ...content_section
                }
            }
        },
        methods:
        {
            guiaGetAll,
            saveVisualizacionGuia,
            CreateTour,
            guiasEspecificas,
            validarNumeros,
            async GetDataInit(showLoading = false)
            {
                try
                {
                    let data_form = new FormData();
                    data_form.append('tab_selected', this.data.tab_selected);
                    data_form.append('id_training', this.id_training);

                    // if(showLoading)
                    //     loading();

                    let rs = await fetch(`${this.url}capacitaciones/administracion/get_data_init_general`, { method: "POST", body: data_form, headers: {
                        'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();

                    const { responseCode, message, data } = rd;

                    switch (responseCode)
                    {
                        case 202:
                            switch (this.data.tab_selected)
                            {
                                case 1: //TRAINING
                                    this.data.training_section.default.main_account_id = data.main_account_id
                                    this.filterOptions(); //quita el webinar para main account 2

                                    this.data.training_section.operations_center = data.operation_center.map(({id, nombre}) => ({id: id, text: nombre}));
                                    this.data.training_section.operations_center = [
                                        { id: '0', text: "Seleccionar todos" },
                                        ...data.operation_center.map(({ id, nombre }) => ({ id: id, text: nombre }))
                                    ];

                                    this.data.training_section.sectors = data.sectors.map(({id, nombre}) => ({id: id, text: nombre}));

                                    this.data.training_section.form_data.type_training.value = data.current_training.tipo_capacitacion;
                                    this.data.training_section.form_data.assessBy.value = data.current_training.evaluara_por;
                                    this.data.training_section.default.assessByModified = data.current_training.evaluara_por;//para validar si cambia

                                    this.data.training_section.form_data.training_name.value = data.current_training.nombre;
                                    this.data.training_section.form_data.designedBy.value = data.current_training.designed_by;
                                    this.data.training_section.form_data.points.value = data.current_training.puntos;
                                    this.data.training_section.form_data.price.value = data.current_training.precio;
                                    this.data.training_section.form_data.date_webinars.value = data.current_training.fecha_realizacion;
                                    this.data.training_section.form_data.description.value = data.current_training.descripcion;
                                    if(data.current_training.imagen != null && data.current_training.imagen != "")
                                        this.data.training_section.default.label_file = '1 imagen cargada';
                                    this.data.training_section.form_data.time.value = data.current_training.tiempo_minutos;

                                    let option = this.data.training_section.default.options_certified.find(item => item.id == data.current_training.permitir_certificacion);
                                    let optionTypeTraining = this.data.training_section.default.options_type_training.find(item => item.id == data.current_training.tipo_capacitacion);
                                    let optionAssess = this.data.training_section.default.options_assess.find(item => item.id == data.current_training.evaluara_por);

                                    option ? this.$refs.allow_certified.selectOption(option) : null;
                                    optionTypeTraining ? this.$refs.type_training.selectOption(optionTypeTraining) : null;
                                    optionAssess ? this.$refs.assess_by.selectOption(optionAssess) : null;

                                    this.data.training_section.form_data.certified.value = data.current_training.permitir_certificacion;

                                    this.OnChangeAssign(data.current_training.assign);
                                    this.OnChangeAssess(data.current_training.aplica_evaluacion);
                                    this.OnChangeCertify(data.current_training.aplica_certificado);
                                    this.OnChangeEstado(data.current_training.estado_proceso);

                                    if(data.current_training.assign == 1) //SECTOR
                                    {
                                        let selected_options = [];
                                        Array.from(data.current_training.sectors).forEach(sc => {
                                            selected_options.push(sc.id);
                                        });
                                        this.data.training_section.form_data.id_selected.value = selected_options;
                                    }
                                    else //CENTRO OPERACIONES
                                    {
                                        let selected_options = [];
                                        Array.from(data.current_training.operation_center).forEach(cn => {
                                            selected_options.push(cn.id);
                                        });
                                        this.data.training_section.form_data.id_selected.value = selected_options;
                                    }
                                    break;

                                case 2: //MODULE
                                    section_test.section_test.id_training = null;
                                    this.data.modules_section.modules = data.modules;
                                    this.data.modules_section.assessBy = data.assessBy[0].evaluara_por;
                                    break;
                                case 3: //EVALUACIÓN POR CAPACITACIÓN
                                    section_test.section_test.id_training = data.id_training;
                                    section_test.section_test.hasTestTraining = data.hasTestTraining[0].tiene_preguntas_capacitacion;


                                    if(data.hasTestTraining[0].tiene_preguntas_capacitacion == 1){
                                        try
                                        {
                                            let data_form = new FormData();

                                            data_form.append('id_training', section_test.section_test.id_training);
                                            loading();
                                            let rs = await fetch(`${this.url}capacitaciones/administracion/get_test_training`, { method: "POST", body: data_form, headers: {
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
                                                        Array.from(data.questions).map(pr => {
                                                            pr.respuestas.map(rta => {
                                                                rta.checked = (rta.checked == 0 ? false : true);
                                                            });
                                                        });
                                                    }
                                                    this.data.section_test.data_test = data.questions;
                                                    this.data.form_data.porcentajeAprobacion.value = data.porcentajeAprobacion;
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
                                    }

                                    break;

                                default:
                                    break;
                            }
                            break;

                        default:
                            break;
                    }

                    if(showLoading)
                        loading(false);
                }
                catch (error)
                {
                    if(showLoading)
                        loading(false);

                    console.error(`Error to get principal Data: ${error.message}`);
                }

            },
            async OnClickTabSelected(number)
            {
                this.data.tab_selected = number;
                let loading = (number == 1 ? true : false);
                await this.GetDataInit(loading);
                if (number == 2) {
                    await this.guiaGetAll();
                    let guiasEspecificas = await this.guiasEspecificas(['dv-crear-agregar-mod-cap','dv-crear-video-cap-0', 'dv-crear-recursos-cap-0', 'dv-crear-evaluacion-cap-0']);
                    this.CreateTour(guiasEspecificas);
                    this.tour.start();
                }
            },
            RemoveQuestion(question)
            {
                this.data.section_test.data_test = this.data.section_test.data_test.filter(que => que.id != question.id);
            },
            //EVENTOS PARA SECCIÓN CAPACITACIÓN
            ...training_events,
            //EVENTOS PARA SECCIÓN CAPACITACIÓN - END

            //EVENTOS PARA SECCIÓN MODULOS
            ...module_events,
            //EVENTOS PARA SECCIÓN MODULOS - END

            //EVENTOS PARA SECCIÓN EVALUACIÓN
            ...section_test_events,
            //EVENTOS PARA SECCIÓN EVALUACIÓN - END

            //EVENTOS PARA SECCIÓN CONTENTENIDO
            ...content_events
            //EVENTOS PARA SECCIÓN CONTENTENIDO - END
        }
    }
</script>

<style scoped>
.dev-subtitle
{
    font-size: 12px;
    text-align: center;
}

.dev-over-flow
{
    overflow: auto;
    max-height: 500px;
}
</style>
