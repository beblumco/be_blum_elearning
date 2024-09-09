<template>
<div class="col-md-12">
      <div class="authincation-content">
        <div class="row no-gutters">
              <div class="col-xl-12">
                  <div class="auth-form">
                      <div class="text-center mb-3">
                        <a href="" class="title-login">
                            <img class="logo_login" :src="url + 'img/logo_principal.png'" alt="BeBlum">
                        </a>
                      </div>
                      <h4 class="text-center mb-4 text-white subtitulo" style="font-size: 19px;">Cambiar contraseña</h4>
                      <form method="POST" action="#">
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Nueva contraseña</strong></label>
                              <input type="password" v-model="data.newPassword" name="password" class="form-control" placeholder="Escriba una nueva contraseña" required>
                          </div>
                          <div class="form-group">
                              <label class="mb-1 text-white"><strong>Confirmar Contraseña</strong></label>
                              <input type="password" v-model="data.confirmPassword" name="confirm_password" placeholder="Repita la contraseña" class="form-control" required>
                          </div>
                          <div class="text-center">
                              <button type="button" class="btn bg-white text-primary btn-block dev_button_login" @click="OnClickChangePassword()">
                               Actualizar contraseña 
                              </button>
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
        mounted() {
            this.data.emailCrypted = this.email;
        },
        props: {
            email: String
        },
        data()
        {
            return {
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                data:
                {
                    newPassword: '',
                    confirmPassword: '',
                    emailCrypted: ''
                }
            }
        },
        methods:
        {
            async OnClickChangePassword()
            {
                try 
                {
                    if(this.data.newPassword == '' || this.data.confirmPassword == '')
                    {
                        toastr.warning(`Se debe diligenciar ambas contraseñas para realizar el cambio.`);
                        return;
                    }

                    if(this.data.newPassword.length < 8)
                    {
                        toastr.warning(`La contraseña debe ser mínimo de 8 carácteres`);
                        return;
                    }

                    if(this.data.newPassword != this.data.confirmPassword)
                    {
                        toastr.warning(`Las contraseñas no coinciden, por favor vuelva a intentarlo.`);
                        return;
                    }

                    let data = new FormData();
                    Object.entries(this.data).forEach(el => {
                        data.append(el[0],el[1]);                        
                    });

                    loading();
                    let rs = await fetch(`${this.url}change_password`, { method: "POST", body: data, headers: {
                      'X-CSRF-TOKEN': this.token 
                    }});
                    let rd = await rs.json();
                    loading(false);
                    switch (rd.responseCode) 
                    {
                        case 200:
                            let answer = await Swal.fire({
                                html: "<span>La <strong>contraseña</strong> ha sido restablecida correctamente.</span>",
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
                    console.error(`Error para cambiar el password: ${error.message}`);                    
                }
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

