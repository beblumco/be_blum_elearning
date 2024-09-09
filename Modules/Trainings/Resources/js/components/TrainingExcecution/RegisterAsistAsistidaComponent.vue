<template>
    <div class="mx-auto">
        <div class="row">
            <div class="col-md-2 offset-md-10 dv-qr">
                <a href="javascript:void(0)" @click="OnClickOpenModalLink()" class="a-qr">
                    <i class="bi bi-qr-code-scan"></i>
                </a>
            </div>
        </div>
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div v-if="showSuccess.show" class="alert alert-success left-icon-big alert-dismissible fade show">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button> -->
                    <div class="media">
                        <div class="alert-left-icon-big">
                            <span><i class="mdi mdi-check-circle-outline"></i></span>
                        </div>
                        <div class="media-body">
                            <h5 class="mt-1 mb-2">{{showSuccess.msg}}</h5>
                        </div>
                    </div>
				</div>
                <div v-if="showError.show" class="alert alert-warning left-icon-big alert-dismissible fade show">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button> -->
                    <div class="media">
                        <div class="alert-left-icon-big">
                            <span><i class="mdi mdi-help-circle-outline"></i></span>
                        </div>
                        <div class="media-body">
                            <h5 class="mt-1 mb-2">{{showError.msg}}</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">REGISTRO DE ASISTENCIA</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form @submit.prevent="checkForm">
                                <div class="form-group">
                                    <input id="documento" v-model.lazy="form.documento.value" type="text" class="form-control input-default " :placeholder="`Numero de documento ${form.documento.required ? '*' : ''}`" @input="validarNumeros">
                                </div>
                                <div class="form-group">
                                    <input id="nombreCom" v-model="form.nombreCom.value" type="text" class="form-control input-default " placeholder="Nombre completo *">
                                </div>
                                <div class="form-group">
                                    <input id="email" v-model="form.email.value" type="text" class="form-control input-default " placeholder="Correo electrónico">
                                </div>
                                <div class="form-group" v-if="form.empresa.required">
                                    <input id="empresa" v-model="form.empresa.value" type="text" class="form-control input-default " placeholder="Empresa *">
                                </div>

                                <div class="form-group" v-if="!form.empresa.required">
                                    <label for="name" class="col-form-label">
                                        Centro de costo:
                                    </label>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <Select_Savk
                                            ref="selectCliente1"
                                            id="selectCliente1"
                                            :maxItem="20"
                                            v-model="form.cc.value"
                                            :options="centroCosto"
                                            placeholder="Seleccione un centro de costo"
                                            @selected="OnSelectedCC"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="mx-auto text-center">
                                    <button class="btn btn-danger mr-2 mt-1" @click.prevent="openModalFirmar" v-if="asistencia != null && signature == null">Firmar</button>
                                    <!-- <button class="btn btn-primary mr-2 mt-1" v-if="asistencia != null && signature != null" disabled>Firmado</button> -->
                                    <button class="btn btn-primary mr-2 mt-1" @click.prevent="openModalFirmar" v-if="asistencia != null && signature != null">Firmado</button>
                                    <button type="submit" class="btn btn-primary" v-if="!evaluacion && capacitacion == ''">Enviar</button>

                                    <button class="btn btn-danger mr-2 mt-1" @click.prevent="realizarEvaluacion" v-if="evaluacion && !form.empresa.required">Realizar evaluación</button>
                                    <button class="btn btn-danger mr-2 mt-1" @click.prevent="realizarEvaluacionPublica" v-if="evaluacion && form.empresa.required">Realizar evaluación</button>
                                    <button class="btn btn-danger mr-2 mt-1" @click.prevent="downloadCertificate(iniciada)" v-if="id_training.aplica_certificado == 1 && !evaluacion && capacitacion != '' && !Array.isArray(iniciada)">Descargar certificado</button>
                                    <button class="btn btn-primary mr-2 mt-1" @click.prevent="sendCertificate(iniciada)" v-if="id_training.aplica_certificado == 1 && !evaluacion && capacitacion != '' && form.empresa.required && !Array.isArray(iniciada)">Enviar certificado</button>
                                    <button class="btn btn-danger mr-2 mt-1" @click.prevent="realizarEvaluacionPublica" v-if="Array.isArray(iniciada) && form.empresa.required">Certificados</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

         <!-- MODAL VER MODULO -->
         <div class="modal fade" id="modal_view_module" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Capacitación</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="form-group col-md-12">
                                <div class="accordion accordion-primary">
                                    <div v-if="id_training.evaluara_por == null || id_training.evaluara_por == 2 || (id_training.evaluara_por == 1 && id_training.permitir_certificacion == 2)" v-for="module in modal_view_module" :key="module.id" class="accordion__item">
                                        <div :class="`accordion__header rounded-lg`">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <span class="accordion__header--text">{{module.nombre}}</span>
                                                    </div>
                                                    <div class="col-lg-6 d-flex justify-content-end">
                                                        <button class="btn btn-sm btn-danger" v-if="module.moduloEvaluacion > 0" @click.prevent="OnClickToDoTest(module.id)">Realizar evaluación</button>
                                                        <button  v-if="module.evaluacionAprobada" class="ml-2 btn btn-sm btn-danger" @click.prevent="downloadCertificate(module.evaluacionAprobada)">Certificado</button>
                                                        <button class="btn btn-sm btn-danger ml-2" @click.prevent="sendCertificate(module.evaluacionAprobada)" v-if="module.evaluacionAprobada">Enviar certificado</button>
                                                    </div>
                                                </div>

                                        </div>
                                    </div>
                                    <div class="row" v-if="id_training.evaluara_por == 1 && id_training.permitir_certificacion == 2">
                                        <div class="col-lg-12 d-flex justify-content-center">
                                            <button class="btn btn-sm btn-danger" @click.prevent="OnClickToDoTest()">Realizar evaluación</button>
                                        </div>
                                    </div>
                                    <div class="accordion__item" v-if="id_training.evaluara_por == 1 && (id_training.permitir_certificacion == 1 || id_training.permitir_certificacion == null)">
                                        <div :class="`accordion__header rounded-lg`">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <span class="accordion__header--text">{{id_training.nom_cap}}</span>
                                                    </div>
                                                    <div class="col-lg-6 d-flex justify-content-end">
                                                            <button class="btn btn-sm btn-danger" @click.prevent="OnClickToDoTest()">Realizar evaluación</button>
                                                            <button class="btn btn-sm btn-danger ml-2" v-if="Object.keys(modal_view_module).length > 0 && id_training.aplica_certificado == 1"  @click.prevent="downloadCertificate(modal_view_module[0].id)">Certificado</button>
                                                            <button class="btn btn-sm btn-danger ml-2" @click.prevent="sendCertificate(modal_view_module[0].id)" v-if="Object.keys(modal_view_module).length > 0 && id_training.aplica_certificado == 1">Enviar certificado</button>
                                                    </div>

                                                </div>

                                        </div>
                                    </div>

                                    <!-- BOTONES CERTIFICA GENERAL, EVALÚA POR MÓDULOS -->
                                    <div class="row" v-if="Object.keys(modal_view_module).length > 0 && id_training.aplica_certificado == 1">
                                        <div class="col-lg-12 d-flex justify-content-center" v-if="modal_view_module[0].evaluacionAprobadaCap != null && modal_view_module[0].evaluacionAprobadaCap != 3">
                                            <button class="btn btn-sm btn-danger" @click.prevent="downloadCertificate(modal_view_module[0].evaluacionAprobadaCap)">Certificado</button>
                                            <button class="btn btn-sm btn-danger ml-2" @click.prevent="sendCertificate(modal_view_module[0].evaluacionAprobadaCap)">Enviar certificado</button>
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
                        publica = "true"
                        @listenerToFinishTest="OnClickToFinishTest"
                        @listenerToRepethTest="OnClickToDoTest(moduloSelect)">
                    </CardTestComponent>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal login-->
        <div class="modal fade" ref="modal_login" id="modal_login" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Iniciar sesión</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input id="password" v-model="formLogin.password.value" type="password" class="form-control input-default " placeholder="Contraseña *">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="OnClickLogin">Continuar</button>
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalLogin">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal enviar correo-->
        <div class="modal fade" ref="modal_send" id="modal_send" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Enviar certificado al correo</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input id="email-send" v-model="formSend.email.value" type="text" class="form-control input-default " placeholder="Correo electrónico *" >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="OnClickSendEmail">Continuar</button>
                        <button type="button" class="btn btn-danger light" @click="OnClickCloseModalSend">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- POP UP FIRMA -->
        <div class="modal fade" id="modal-signature" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title pull-left">Creación de firma</h5>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item" v-if="signature == null">
                                <a class="nav-link" :class="{'active': tabSelect == 1}" href="#" @click.prevent="tabSignature(1)">Firmar</a>
                            </li>
                            <li class="nav-item" v-if="signature == null">
                                <a class="nav-link" :class="{'active': tabSelect == 2}" href="#" @click.prevent="tabSignature(2)">Adjuntar firma</a>
                            </li>
                            <li class="nav-item" v-if="signature == null">
                                <a class="nav-link" :class="{'active': tabSelect == 3}" href="#" @click.prevent="tabSignature(3)">Tomar fotografía</a>
                            </li>
                            <li class="nav-item" v-if="signature != null">
                                <a class="nav-link active">Ver firma</a>
                            </li>
                        </ul>

                        <div v-show="tabSelect == 1 && signature == null">
                            <div id="signature">
                                <canvas width="300" height="200" ref="canvas"></canvas>
                                <div class="controls mt-2">
                                    <button class="btn btn-success" @click.prevent="clearFirmar">Limpiar</button>
                                </div>
                            </div>
                        </div>
                        <div v-if="tabSelect == 2 && signature == null">
                            <div class="col-lg-12 d-flex justify-content-center">
                                <div class="form-group col-md-12">
                                    <label> Adjuntar firma: <span class="dev-required">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" accept="image/*" @change="OnChangeFileContenImagen" class="custom-file-input" ref="file_imagen_content">
                                            <label class="custom-file-label">{{ label_fileImg }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="tabSelect == 3 && signature == null">
                            <div class="row">
                                <div class="col-lg-12" v-show="!firma">
                                    <video ref="videoElement" class="w-100 h-100" autoplay></video>
                                </div>
                                <div class="col-lg-12" v-show="firma">
                                    <img class="w-100 h-100" :src="firma" alt="Captured Image">
                                </div>
                                <div class="col-lg-12">
                                    <button class="btn btn-primary" @click="takePhoto" v-if="!firma">Tomar foto</button>
                                    <button class="btn btn-primary" @click="resetPhoto" v-if="firma">Repetir foto</button>
                                </div>
                            </div>
                        </div>
                        <div v-if="signature != null">
                            <div class="row">
                                <div class="col-lg-12">
                                    <img class="w-100 h-100" :src="url+'/'+signature" alt="Captured Image">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click.prevent="saveFirma" v-if="signature == null">Guardar firma</button>
                        <button class="btn btn-danger" @click.prevent="closeModalFirmar">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
        <!-- POP UP FIRMA - FIN -->
        <!-- MODAL QR CAPACITACIÓN -->
        <div
            class="modal fade"
            id="modal_link"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
            ref="modal_link"
        >
            <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modal-title-create-company">
                    Compartir código
                </h5>
                <button type="button" class="close" @click="closeModalLink">
                    <span>&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10 offset-md-1">
                            <img class="w-100 h-100" :src="url+qr" alt="Código qr">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a :href="url+qr" download="QR">
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm">
                                    Descargar código
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-danger light"
                    @click="closeModalLink"
                >
                    Cerrar
                </button>
                </div>
            </div>
            </div>
        </div>
        <!--END MODAL LINK CAPACITACIÓN -->

    </div>
</template>

<script>
import Select_Savk from "../../../../../../resources/js/components/pages/otros/Select_Savk.vue";
import CardTestComponent from "./CardTestComponent.vue";
import { validarNumeros } from "../../../../../../public/assets/js/functions.js";
import SignaturePad  from "signature_pad";
export default {
    props:
        {
            id_training: Object
        },
    components: {
        Select_Savk,
        CardTestComponent
    },
    mounted() {
        if (this.id_training.id_cliente == null) {
            this.form.documento.required = false
            this.form.empresa.required = true
        }else{
            this.getCC()
            this.form.cc.value = this.id_training.id_cliente
        }
        this.signaturePad = new SignaturePad(this.$refs.canvas);

    },

    data() {
        return {
            qr : '',
            modal_view_module: [],
            sectores: [],
            url: ROOT_URL,
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            evaluacion : false,
            capacitacion : '',
            id_asistente : '',
            iniciada: '',
            login : '',
            moduloSelect : '',
            centroCosto: [],

            fourth_section: {
                questions: [],
                id_test_init: []
            },

            form: {
                documento: { value: '', required: true},
                nombreCom: { value: '', required: true},
                email: { value: '', required: false},
                empresa: { value: '', required: false},
                idAsistida:  { value: this.id_training.id_cap_asis, required: false},
                cc: {value: this.id_training.id_cliente, required: false}
            },
            formLogin : {
                user:{ value: '', required: true},
                id:{ value: '', required: false},
                password :{ value: '', required: true}
            },
            formSend :{
                email:{ value: '', required: true},
                idIniciada: { value: '', required: true},
            },
            showSuccess:{
                show: false,
                msg: ""
            },
            showError:{
                show: false,
                msg: ""
            },
            asistencia: null,
            signature: null,
            tabSelect: 1,
            firma: null,
            label_fileImg: 'Selecciona una firma',
            stream: null,
            videoWidth: 640,
            videoHeight: 480,
            optionsFetch: (data) => ({
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })

        }
    },

    methods: {
        validarNumeros,
        async OnClickOpenModalLink() {
            const data_form = new FormData();
            data_form.append('link', window.location.href);

            let rs = await fetch(`${this.url}/capacitaciones/asistida/generarQr`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();

            const { responseCode, message, data } = rd;

            if (responseCode == '200') {
                this.qr = `/storage/qr_codes/${data}`;
            }else{
                console.log('Fallo generando código QR');
            }

            $("#modal_link").modal("show");
        },
        closeModalLink(e) {
            $("#modal_link").modal("hide");
        },
        async setupCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.videoElement.srcObject = stream;
                this.stream = stream;
            } catch (error) {
                console.error('Error al acceder a la cámara:', error);
            }
        },
        takePhoto() {
            const video = this.$refs.videoElement;
            const canvas = document.createElement('canvas');
            canvas.width = this.videoWidth;
            canvas.height = this.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, this.videoWidth, this.videoHeight);
            this.firma = canvas.toDataURL('image/png');
        },
        resetPhoto(){
            this.firma = null;
        },
        OnChangeFileContenImagen(file){
            if(file != undefined)
            {
                const maxSize = 3 * 1024 * 1024; // 3MB en bytes

                if (file.target.files[0].size <= maxSize) {
                    this.label_fileImg = "1 firma cargada";
                    //SE PASA IMAGEN A BASE64
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.firma = reader.result;
                    };
                    reader.readAsDataURL(file.target.files[0]);
                }else{
                    swal({
                        title: "Error de imagen",
                        text: `El tamaño de la imagen excede el límite de 3 MB.`,
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        cancelButtonText: "No",
                        confirmButtonColor: '#1f3352',
                        allowOutsideClick: false
                    });
                }

            }
            else
            {
                this.label_fileImg = "Seleccionar una firma";
                this.firma = null;
            }
        },
        closeCamera(){
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }
        },
        tabSignature(tab){
            if (tab == 3) {
                this.setupCamera();
            }else{
                this.closeCamera()
            }
            this.label_fileImg = "Seleccionar una firma";
            this.firma = null;
            this.tabSelect = tab;
        },
        async saveFirma(){
            try {
                if (this.tabSelect == 1) {
                    // Verificar si se ha realizado algún trazo en el signaturePad
                    const isDrawing = !this.signaturePad.isEmpty();
                    if (isDrawing) {
                        this.firma = this.signaturePad.toDataURL();
                    }
                }
                if (this.firma == '' || this.firma == null) {
                    swal({
                        text: this.tabSelect === 1 ? "No has realizado la firma." : this.tabSelect === 2 ? "Por favor seleccionar firma." : "Por favor tomar foto de la  firma.",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        cancelButtonText: "No",
                        confirmButtonColor: "#1f3352",
                        allowOutsideClick: false,
                    });
                    return
                }
                let data = new FormData();
                data.append('signature', this.firma);
                data.append('asistencia', this.asistencia);

                loading();
                let rs = await fetch(`${this.url}/capacitaciones/registrar-asistencia/saveSignature`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.status) {
                    case 200:
                        toastr.success(rd.msg);
                        this.closeModalFirmar()
                        this.label_fileImg = "Seleccionar una firma";
                        this.firma = null;
                        this.signature = rd.signature;
                        break;

                    default:
                        toastr.warning('Algo no salio bien');
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al guardar la firma: ${error.message}`);

            }
        },
        clearFirmar(){
            this.signaturePad.clear();
        },
        openModalFirmar(){
            $('#modal-signature').modal('show');
        },
        closeModalFirmar(){
            this.label_fileImg = "Seleccionar una firma";
            this.firma = null;
            this.clearFirmar()
            this.closeCamera()
            this.tabSignature(1)
            $('#modal-signature').modal('hide');
        },
        async downloadCertificate(data) {
            loading();
            window.open(`${this.url}/capacitaciones/download-certificate-public/${data}`, '_blank');
            loading(false);
        },
        async sendCertificate(data) {
            this.formSend.email.value = this.form.email.value
            // this.formSend.idIniciada.value = this.iniciada
            this.formSend.idIniciada.value = data

            $('#modal_send').modal('show');
        },
        async OnClickSendEmail(){
            if (!this.isValidEmail(this.formSend.email.value)) {
                toastr.error("Ingrese correo valido por favor.");
                return
            }
            loading()
            const response = await fetch(this.url + '/capacitaciones/asistida/sendCertificado', this.optionsFetch(this.formSend))
            const { status, msg } = await response.json()
            loading(false)
            if (status === 200) {
                toastr.success(msg)
                $('#modal_send').modal('hide');
            }else if (status === 202) {
                toastr.warning(msg)
            }else{
                toastr.warning("Comunícate con el administrador del sistema.")
            }

        },
        realizarEvaluacion(){
            if (this.login == false) {
                $('#modal_login').modal('show');
            }else{
                window.location.href =  `${this.url}/capacitaciones/init/${this.capacitacion}`;
            }
        },
        async OnClickLogin(){
            if (this.formLogin.password.value == '') {
                toastr.warning("Debes diligenciar la contraseña.")
                return
            }
            loading()
            const response = await fetch(this.url + '/capacitaciones/asistida/loginAsistente', this.optionsFetch(this.formLogin))
            const { status, msg } = await response.json()
            loading(false)
            if (status === 200) {
                window.location.href =  `${this.url}/capacitaciones/init/${this.capacitacion}`;
            }else if (status === 202) {
                toastr.warning(msg)
            }else{
                toastr.warning("Comunícate con el administrador del sistema.")
            }

        },

        async realizarEvaluacionPublica(){
            const response = await fetch(`${this.url}` + `/capacitaciones/get_data_training_public/${this.capacitacion}/${this.id_asistente}`);
            const data = await response.json();
            this.modal_view_module = data

            $('#modal_view_module').modal('show');
        },
        OnClickCloseModalViewModule(){
            $('#modal_view_module').modal('hide');
        },
        OnClickCloseModalLogin(){
            $('#modal_login').modal('hide');
        },
        OnClickCloseModalSend(){
            $('#modal_send').modal('hide');
        },
        checkForm: function (e) {
            var validado = true;

            if (this.form.email.value != '' && !this.isValidEmail(this.form.email.value)) {
                toastr.error("Ingrese correo valido por favor.");
                return
            }

            for (const [key, value] of Object.entries(this.form))
            {
                //ID PARA AGREGAR O QUITAR CLASS
                var elemento = document.getElementById(key);
                // console.log(elemento);

                if ((value.value == '' || typeof value.value === 'undefined') && value.required === true) {
                    validado = false;
                    // elemento.classList.add("noDiligenciado");

                    // window.scroll({
                    //     top: 180,
                    //     left: 0,
                    //     behavior: 'smooth'
                    // });
                }
            }
            if (validado == false) {
                toastr.warning("Debes diligenciar todos los campos.")
            } else {
                //toastr.success("Todos los campos diligenciados correctamente")
                this.onSubmit()
            }
        },
        validar: function (e) {
            var elemento = document.getElementById(e.target.id);

            if (e.target.value != '') {
                elemento.classList.remove("noDiligenciado");
            }
        },

        async onSubmit() {
            try {
                loading()
                let api = '';

                if (this.id_training.id_cliente != null) {
                    api = this.url + '/capacitaciones/asistida/guardarAsistente'
                }else{
                    api = this.url + '/capacitaciones/asistida/guardarAsistentePublica'
                }

                const response = await fetch(api, this.optionsFetch(this.form))
                const { status, msg, data, evaluacion, asistente, iniciada, login, asistencia, signature } = await response.json()
                loading(false)
                if (status === 201) {
                    this.evaluacion = evaluacion,
                    this.id_asistente = asistente,
                    this.capacitacion = data,
                    this.iniciada = iniciada
                    this.login = login
                    this.formLogin.user.value = this.form.email.value
                    this.formLogin.id.value = this.form.documento.value
                    this.asistencia = asistencia
                    this.signature = signature

                    this.showSuccess = {
                        msg: msg,
                        show: true
                    }

                    this.showError = {
                        msg: "",
                        show: false
                    }

                }else if (status === 202) {
                    toastr.warning(msg)
                } else {
                    this.showSuccess = {
                        msg: "",
                        show: false
                    }

                    this.showError = {
                        msg: msg,
                        show: true
                    }
                }

            } catch (error) {
                loading(false)
                toastr.error('Ha ocurrido un error interno.')
            }
        },
        async OnClickToDoTest(id = null) {
            try {
                // let moduleView = this.modal_view_module.find(item => item.id === id);
                this.moduloSelect = id

                let data = new FormData();
                if (this.id_training.evaluara_por == 1) { //CAPACITACIÓN GENERAL
                    data.append('id_module', null);
                    data.append('id_capacitacion', this.capacitacion);
                }else if (this.id_training.evaluara_por == 2) { // POR MÓDULOS
                    data.append('id_module', id);
                    data.append('id_capacitacion', null);
                }
                data.append('publica', true);
                data.append('id_asistente', this.id_asistente);

                loading();
                let rs = await fetch(`${this.url}/capacitaciones/get_data_test`, {
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
        OnClickToFinishTest(){
            this.realizarEvaluacionPublica()
            $(this.$refs.modal_evaluacion).modal('hide');
        },
        isValidEmail(email) {
            // Expresión regular para validar el formato del correo electrónico
            const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
            return emailRegex.test(email);
        },

        async getCC() {
            // loading()
            const response = await fetch(`${this.url}` + `/capacitaciones/get_centro_costo/${this.id_training.id_unidad}`);
            const data = await response.json();
            this.centroCosto = data;

            let optionCC = this.centroCosto.find(item => item.id == this.id_training.id_cliente);
            this.$refs.selectCliente1.selectOption(optionCC)
        },

        OnSelectedCC(item) {
            this.form.cc.value = item.id;
        },
    },
}
</script>

<style scoped>
.authincation-content {
    background-color: #145c54;
    font-family: 'Roboto', sans-serif;
}

.requerido {
    color: red;
}

.noDiligenciado {
    box-shadow: 0 0 12px red;
}
canvas {
    display: block;
    box-shadow: rgb(0, 0, 0) 0px 0px 8px -3px;
    cursor: crosshair;
    background: rgb(255, 255, 255);
    margin: 10px auto;
    border-radius: 20px;
}
.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: white;
    background-color: #002f54;
    border-color: #002f54;
}

.nav-tabs {
    border-bottom: 1px solid #002f54;
}

li > a:hover  {
    color: #ff7f00;
}
.dv-qr{
    font-size: 25px;
    margin-bottom: 5px;
}

.a-qr{
    color: black;
}
</style>
