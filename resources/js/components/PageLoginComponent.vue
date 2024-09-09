<template>
<div class="col-lg-12">
      <div class="authincation-content">
        <div class="row no-gutters">
              <div class="col-xl-12">
                  <div class="auth-form">
                      <div class="text-center mb-3">
                        <a href="" class="title-login">
                          <img class="logo_login" :src="`${url}img/logo_principal_primary.png`" alt="BeBlum">
                          SAVK
                        </a>
                      </div>
                      <h4 class="text-center mb-4 text-white subtitulo">Sistema de Atención Virtual en Limpieza, Desinfección e Inocuidad</h4>
                      <form method="POST" action="#">
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Usuario o correo electrónico</strong></label>
                              <input type="text" v-model="data.email" placeholder="Escriba su correo electrónico o usuario..." name="email" class="form-control">
                          </div>

                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Contraseña</strong></label>
                              <input type="password" v-model="data.password" name="password" placeholder="Escriba su contraseña..." class="form-control" @keyup.enter="OnClickLogin()">
                          </div>

                          <div class="form-row d-flex justify-content-between mt-4 mb-2 texto-12">
                               <div class="form-group"> 
                                   <div class="custom-control custom-checkbox ml-1 text-white"> 
                                     <input type="checkbox" class="custom-control-input" id="basic_checkbox_1"> 
                                     <label class="custom-control-label p2" for="basic_checkbox_1">Recordar Contraseña</label> 
                                   </div> 
                               </div> 
                               <div class="form-group"> 
                                   <a class="text-white" href="#" @click.prevent.stop="OnClickRedirectRecovery()">¿Olvidaste tu contraseña?</a> 
                               </div> 
                          </div>

                          <div class="text-center">
                              <button type="button" class="btn bg-white text-primary btn-block dev_button_login" @click="OnClickLogin()">Iniciar sesión</button>
                          </div>

                      </form>

                       <!-- <div class="new-account mt-3 texto-12"> 
                         <p class="text-white">¿No tienes una cuenta? <a class="text-white" href="#" @click.prevent.stop="OnClickRedirectRegister()"><b>Inscribirse</b></a></p> 
                       </div>  -->

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
                    email: '',
                    password: ''
                }
            }
        },
        methods:
        {
            OnClickRedirectRegister()
            {
                window.location.href = `${this.url}registrarme`;
            },
            OnClickRedirectRecovery()
            {
                window.location.href = `${this.url}recuperar-contrasena`;
            },
            async OnClickLogin()
            {
                try 
                {
                    if(this.data.email == '' || this.data.password == '')
                    {
                        toastr.warning(`Debes completar todos los campos para iniciar sesión`);
                        return;
                    }

                    let data = new FormData();
                    Object.entries(this.data).forEach(el => {
                        data.append(el[0],el[1]);                        
                    });

                    loading();
                    let rs = await fetch(`${this.url}login`, { method: "POST", body: data, headers: {
                      'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();
                    loading(false);
                    switch (rd.responseCode) 
                    {
                        case 200:
                            window.location.href = this.url + rd.data.url;
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
                    console.error(`Error para iniciar sesión: ${error.message}`);                    
                }
            }
        }
    }
</script>

<style scoped>

</style>

