<template>
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <div class="text-center mb-3">
                        <a href="" class="title-login">
                            <img class="logo_registrarme" :src="url + '/img/logoSavakWhite.png'" alt="Klaxen SAS">
                        </a>
                    </div>
                    <h4 class="text-center mb-4 text-white subtitulo" style="font-size: 19px;">Crear cuenta</h4>
                    <h4 class="text-center mb-4 text-white subtitulo" style="font-size: 19px;">Creación de cuenta para <span style="color: rgb(255, 127, 0);">administradores de empresa</span>, si ya perteneces a una organización registrada ingresa <a style="color: rgb(255, 127, 0);" :href="url + '/registrarme-lideres-grupo-empresa'">aquí</a></h4>
                    <form @submit.prevent="checkForm">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Nombre Completo</strong> <span class="requerido">*</span></label>
                                    <input type="text" class="form-control" v-model="form.fullname" name="fullname" id="fullname" v-on:keyup="validar">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Número de identificación</strong></label>
                                    <input type="text" class="form-control" v-model.lazy="form.documento" name="documento" id="documento" v-on:keyup="validar" @input="validarNumeros">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Empresa</strong> <span class="requerido">*</span></label>
                                    <input type="text" class="form-control" v-model="form.company" name="company" id="company" v-on:keyup="validar">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Nit Empresa</strong> <span class="requerido">*</span></label>
                                    <input type="text" class="form-control" v-model.lazy="form.company_nit" name="company_nit" id="company_nit" v-on:keyup="validar" @input="validarNumeros">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Cargo</strong> <span class="requerido">*</span></label>
                                    <input type="text" class="form-control" v-model="form.job" name="job" id="job" v-on:keyup="validar">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Sector</strong> <span class="requerido">*</span></label>
                                    <Select_Savk ref="sector_select_savk" id="sector" v-model="form.sector" :options="sectores" :maxItem="20" placeholder="Seleccione un sector" @selected="OnSelectedItem"/>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white ml-1"><strong>Correo electrónico</strong><span class="requerido">*</span></label>
                                    <input type="text" class="form-control" v-model="form.email" name="email" id="email" v-on:keyup="validar">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Telefono</strong> <span class="requerido">*</span></label>
                                    <input type="number" class="form-control" v-model="form.phone" name="phone" id="phone" v-on:keyup="validar">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Contraseña</strong> <span class="requerido">*</span></label>
                                    <input type="password" class="form-control" v-model="form.password" name="password" id="password" v-on:keyup="validar">
                                     </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label class="mb-1 text-white"><strong>Confirmar contraseña</strong><span class="requerido">*</span></label>
                                    <input id="confirm_password" type="password" class="form-control" v-model="form.confirm_password" name="confirm_password" v-on:keyup="validar">
                                </div>
                            </div>
                        </div>

                        <div class="new-account mt-3 mb-3 texto-12 text-center">
                            <input id="checkAcceptTerm" v-model="form.checkAcceptTerm" class="form-check-input" type="checkbox" value="">
                            <label class="form-check-label text-white" for="checkAcceptTerm">
                                 Acepto términos y condiciones.
                            </label>
                        </div>

                        <div class="col-lg-3  mx-auto text-center">
                            <button type="submit" class="btn bg-white text-primary btn-block dev_button_login"
                                style="color: #ff7f00;">Registrarme</button>
                        </div>
                    </form>
                    <div class="new-account mt-3 texto-12 text-center">
                        <p class="text-white">¿Ya tienes cuenta? <a class="text-white" :href="url"><b
                                    style="color: #ff7f00;">Iniciar sesión</b></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Select_Savk from '../otros/Select_Savk.vue';
import { validarNumeros } from "../../../../../public/assets/js/functions.js";
export default {
    components: {
        Select_Savk,
    },
    mounted() {
        this.fetchSectores();
    },

    data() {
        return {
            sectores: [],
            url: ROOT_URL,
            form: {
                fullname: '',
                documento: '',
                email: '',
                phone: '',
                password: '',
                confirm_password: '',
                company: '',
                company_nit: '',
                job: '',
                sector: 1,
                checkAcceptTerm: false
            },
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
        fetchSectores() {
            fetch(this.url+'/sendSectores') // Llamar a la ruta en Laravel
                .then(response => response.json()) // Convertir la respuesta a JSON
                .then(data => {
                    this.sectores = data; // Asignar los datos a la propiedad del componente
                })
                .catch(error => console.error(error));
        },

        OnSelectedItem: function (seleccionado)
        {
            this.form.sector = seleccionado.id
        },
        checkForm: async function (e) {
            var validado = true;
            for (const [key, value] of Object.entries(this.form))
            {
                //ID PARA AGREGAR O QUITAR CLASS
                var elemento = document.getElementById(key);
                if ((value == '' || typeof value === 'undefined') && key !== 'documento') {
                    validado = false;
                    elemento.classList.add("noDiligenciado");

                    window.scroll({
                        top: 180,
                        left: 0,
                        behavior: 'smooth'
                    });
                }
            }
            if (validado == false) {
                if(this.form.checkAcceptTerm == false){
                    toastr.warning("Por favor acepte los términos y condiciones!!!")
                    return;
                }
                toastr.warning("Por favor diligenciar todos los campos!!!")
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
                const response = await fetch(this.url + '/registro', this.optionsFetch(this.form))
                const { status, msg } = await response.json()
                loading(false)
                if (status === 200)
                {
                    const answer = await Swal.fire({
                        html: `<span><strong>Registro éxitoso,</strong> tu cuenta se encuentra en estado <strong>"Pendiente de validación"</strong>, lo que significa que aún no puedes iniciar sesión. Pero no te preocupes, nuestro equipo está trabajando arduamente para verificar tu información lo antes posible y te estaremos informando sobre el estado de tu cuenta.</span>`,
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: "Aceptar",
                        cancelButtonText: `Ir a iniciar sesión`,
                        confirmButtonColor: '#1f3352',
                        cancelButtonColor: '#ff7f00',
                        allowOutsideClick: false
                    });
                    if (answer.value) //ACEPTAR
                    {
                        Object.entries(this.form).forEach((prop, index) => {
                            if(prop[0] == 'checkAcceptTerm')
                                this.form[prop[0]] = false;
                            else if(prop[0] == 'sector')
                            {
                                this.form[prop[0]] = 1;
                                this.$refs.sector_select_savk.Clear();
                            }
                            else
                                this.form[prop[0]] = "";
                        });
                    }
                    else //INICIAR SESIÓN
                        window.location.replace(this.url);
                } else {
                    toastr.warning(msg)
                }

            } catch (error) {
                loading(false)
                toastr.error('Ha ocurrido un error interno.')
            }
        }
    },
}
</script>

<style>
.authincation-content {
    background-color: #1e3352;
    font-family: 'Roboto', sans-serif;
}

.requerido {
    color: red;
}

.noDiligenciado {
    box-shadow: 0 0 12px red;
}
#swal2-content strong
{
    font-weight: bold!important;;
}
</style>
