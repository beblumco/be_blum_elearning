<template>
<div class="col-md-12">
      <div class="authincation-content">
        <div class="row no-gutters">
              <div class="col-xl-12">
                  <div class="auth-form">
                      <div class="text-center mb-3">
                        <a href="" class="title-login">
                            <img class="logo_login" :src="url + '/img/logoSavakWhite.png' " alt="Klaxen SAS">
                        </a>
                      </div>
                      <h4 class="text-center mb-4 text-white subtitulo"
                      style="font-size: 19px;">Recuperar Contraseña</h4>
                      <form method="POST" action="#">
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Correo electrónico</strong></label>
                              <input type="email" name="email" v-model="data.email" placeholder="Escriba su correo electrónico" class="form-control" required>
                          </div>
                          <div class="text-center">
                              <button type="button" class="btn bg-white text-primary btn-block dev_button_login" @click.prevent.stop="OnClickSendLink()">
                                Enviar enlace de recuperación
                              </button>
                              <div class="new-account mt-3 texto-12"> 
                                <p class="text-white"
                                style="font-style: italic;">¿Ya tienes una cuenta? <a class="text-white" href="/" >
                                    <b style="color: #ff7f00;">Iniciar sesión</b></a></p> 
                              </div> 
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
    export default {
        props:{
            
        },
        mounted() {
        },
        data()
        {
            return {
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                data:
                {
                    email: ''
                }
            }
        },
        methods:
        {
            async OnClickSendLink()
            {
                try 
                {
                    if(this.data.email == '')
                    {
                        toastr.warning(`Debes completar el correo electrónico para poder enviar el link de recuperación.`);
                        return;
                    }

                    let data = new FormData();
                    Object.entries(this.data).forEach(el => {
                        data.append(el[0],el[1]);                        
                    });

                    loading();
                    let rs = await fetch(`${this.url}recuperar_contrasena_enviar`, { method: "POST", body: data, headers: {
                      'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();
                    loading(false);
                    switch (rd.responseCode) 
                    {
                        case 200:
                            let answer = await Swal.fire({
                                html: "<span>Correo electrónico <strong>enviado</strong>, revise su bandeja de entrada o spam y siga las instrucciones.</span>",
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: "Iniciar sesión",
                                cancelButtonText: ``,
                                confirmButtonColor: '#1f3352',
                                cancelButtonColor: '#ff7f00',
                                allowOutsideClick: false
                            });

                            if(answer.value) //INICIAR SESIÓN
                                window.location.replace(`${this.url}`);
                            break;

                        case 400:
                            toastr.error(rd.message);
                            break;
                    
                        default:
                            break;
                    }
                    
                } 
                catch (error) 
                {
                    loading(false);
                    console.error(`Error para recuperar el password: ${error.message}`);                    
                }
            },
            OnClickRedirectLogin()
            {
                window.location.href = `${this.url}/login`;
            }
        }
    }
</script>

<style scoped>
.authincation-content {
    background-color: #145c54;
    font-family: 'Roboto', sans-serif;
}

</style>

