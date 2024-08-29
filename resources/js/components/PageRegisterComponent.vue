<template>
<div class="col-lg-12">
      <div class="authincation-content">
        <div class="row no-gutters">
              <div class="col-xl-12">
                  <div class="auth-form">
                      <div class="text-center mb-3">
                        <a href="" class="title-login">
                          <img class="logo_login" :src="`${prefix_subdirectory}/img/logo_klaxen.png`" alt="Klaxen SAS">
                          SAVK
                        </a>
                      </div>
                      <h4 class="text-center mb-4 text-white subtitulo">Crear Cuenta</h4>
                      <form action="">
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Usuario</strong></label>
                              <input type="text" placeholder="Usuario..." v-model="data.userName" class="form-control">
                          </div>
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Correo electrónico</strong></label>
                              <input type="email" placeholder="Correo electrónico..." v-model="data.email" class="form-control">
                          </div>
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Contraseña</strong></label>
                              <input type="password" placeholder="Contraseña..." v-model="data.password" class="form-control">
                          </div>
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Sector</strong></label>
                              <select class="form-control" name="template">
                                    <option v-for="item in data.sector" :key="item.id">
                                    {{ item.name }}
                                    </option>
                                </select>
                          </div>
                          <div class="text-center">
                              <button type="button" class="btn bg-white text-primary btn-block dev_button_login" @click.prevent.stop="OnClickRegister()">Registrarme</button>
                          </div>
                      </form>
                       <div class="new-account mt-3 texto-12"> 
                         <p class="text-white">¿Ya tienes cuenta? <a class="text-white" href="#" @click.prevent.stop="OnClickReturnLogin()"><b>Iniciar sesión</b></a></p> 
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
            
        },
        mounted() {
            this.prefix_subdirectory = prefix_subdirectory;
        },
        components:{
        },
        data()
        {
            return {
                prefix_subdirectory: null,
                data:
                {
                    email: '',
                    sector: [{
                        id: 1,
                        name: "Alimentos"
                    },
                    {
                        id: 2,
                        name: "Otras industrías"
                    }
                    ]
                }
            }
        },
        methods:
        {
            OnClickReturnLogin()
            {
                window.location.href =  `${this.prefix_subdirectory}/`;
            },
            async OnClickRegister()
            {
                try 
                {
                    // if(this.data.email == '')
                    // {
                    //     toastr.warning(`Debes completar el correo electrónico para poder enviar el link de recuperación.`);
                    //     return;
                    // }

                    // let data = new FormData();
                    // Object.entries(this.data).forEach(el => {
                    //     data.append(el[0],el[1]);                        
                    // });

                    // loading();
                    // let rs = await fetch('/recuperar_contrasena_enviar', { method: "POST", body: data, headers: {
                    //   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    // }});
                    // let rd = await rs.json();
                    // loading(false);
                    // switch (rd.responseCode) 
                    // {
                    //     case 200:
                    //         toastr.success(rd.message);
                    //         await delay(2000);
                    //         window.location.href = rd.data.url;                            
                    //         break;

                    //     case 400:
                    //         toastr.error(rd.message);
                    //         break;
                    
                    //     default:
                    //         break;
                    // }
                    
                } 
                catch (error) 
                {
                    loading(false);
                    console.error(`Error para recuperar el password: ${error.message}`);                    
                }
            }
        }
    }
</script>

<style scoped>

</style>

