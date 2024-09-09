<template>
<div>
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
                        <div class="text-center mb-3">
                            <a href="" class="title-login">
                                <img class="logo_login" :src="url + '/img/logo_principal_primary.png' " alt="BeBlum">
                            </a>
                        </div>
                        <form v-on:submit.prevent="onSubmit">
                            <div class="form-group">
                                <label class="mb-1 text-black"><strong>Correo electrónico</strong></label>
                                <input type="text"
                                    v-model="email"
                                    placeholder="micorreo@ejemplo.com" name="email" class="form-control"
                                    >
                            </div>
                            <div class="form-group mb-0">
                                <label class="mb-1 text-black"><strong>Contraseña</strong></label>
                                <input type="password"
                                    v-model="password"
                                    name="password" placeholder="Escriba su contraseña."
                                    class="form-control">
                            </div>
                            <!-- <div class="form-row d-flex justify-content-between mt-1 mb-2 texto-12">
                                <div class="form-group">
                                    <a class="text-naranja" :href="url + '/recuperar-contrasena' ">¿Olvidaste tu
                                        contraseña?</a>
                                </div>
                            </div> -->
                            <div class="form-group mt-3">
                                <!-- <label class="mb-1 text-black"><strong>Protección de datos <span class="text-danger">*</span></strong></label> -->
                                <div>
                                    <input type="checkbox" v-model="checked">
                                    <!-- <span class="text-black texto-12 ml-1">
                                        He leído y acepto la Política de Privacidad:
                                        <a href="https://drive.google.com/file/d/1TkPM5LK24RNidk2dGg6Zv_VxdksGJS4X/view?usp=sharing" target="_blank" class="text-naranja">Consultar aquí.</a>
                                    </span> -->
                                    <span class="text-black texto-12 ml-1">
                                        He leído y acepto la
                                        <a href="https://drive.google.com/file/d/1TkPM5LK24RNidk2dGg6Zv_VxdksGJS4X/view?usp=sharing" target="_blank" class="text-naranja"> Política de Privacidad.</a>
                                    </span>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit"
                                class="btn btn-block dev_button_login">Iniciar
                                    sesión</button>
                            </div>
                        </form>
                        <div class="new-account mt-3 texto-12 text-center" style="font-style: italic;">
                            <!-- <p class="text-black">¿No tienes una cuenta? <a class="text-black"
                                    :href="url + '/registrarme' ">
                                    <b style="color: #ff7f00;">Inscribirse</b>
                                </a></p> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            activeAccount: String,
            mode: String
        },

        async mounted() {
            if(Number(this.activeAccount) === 1)
            {
                let answer = await Swal.fire({
                    html: "<span>Esta cuenta ha sido <strong>activada</strong>, el usuario ya puede ingresar al Sistema de Atención Virtual Klaxen.</span>",
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: "Iniciar sesión",
                    cancelButtonText: `Recuperar contraseña`,
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });

                if(answer.value) //INICIAR SESIÓN
                {
                }
                else
                    window.location.replace(`${this.url}/recuperar-contrasena`);
            }
        },

        data() {
            return {
                url: ROOT_URL,
                email: '',
                password: '',
                checked: true,
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
            async onSubmit(e){
                try {
                    if(this.email == '' || this.password == '')
                    {
                        toastr.warning('Debes completar todos los campos')
                        return;
                    }
                    if(!this.checked)
                    {
                        toastr.warning('Debes aceptar las políticas de privacidad')
                        return;
                    }
                    loading()
                    const response = await fetch(this.url + '/login', this.optionsFetch({ email: this.email, password: this.password}))
                    const {status, route, msg} = await response.json()
                    if(status === 200){
                        if(this.mode == 1){
                            this.$emit('redirect');
                        }else{
                            window.location.replace(this.url + route)
                        }

                    }else {
                        loading(false)
                        toastr.warning(msg)
                    }
                } catch (error) {
                    loading(false)
                    toastr.error('Ha ocurrido un error interno')
                }
            },
        },
    }
</script>

<style scoped>
.dev_button_login
{
    background-color: var(--color-primary);
    color: white;
}
</style>