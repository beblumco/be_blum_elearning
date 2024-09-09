<template>
<div class="authincation-content">
    <div class="row no-gutters">
          <div class="col-xl-12">
              <div class="auth-form">
                  <div class="text-center mb-3">
                    <a href=""><img src="assets/images/logo-full.png" alt=""></a>
                  </div>
                  <h4 class="text-center mb-4 text-white">Inicia sesión con tu cuenta</h4>
                  <form action="">
                      <div class="form-group">
                          <label class="mb-1 text-white"><strong>Nombre Completo</strong></label>
                          <input type="email" placeholder="Nombre" class="form-control" value="">
                      </div>
                      <div class="form-group">
                          <label class="mb-1 text-white"><strong>Telefono</strong></label>
                          <input type="number" placeholder="" class="form-control" value="">
                      </div>
                      <div class="form-group">
                          <label class="mb-1 text-white"><strong>Contraseña</strong></label>
                          <input type="password" placeholder="Escriba su contraseña..." class="form-control" value="Password">
                      </div>
                      <div class="form-row d-flex justify-content-between mt-4 mb-2">
                          <!-- <div class="form-group"> -->
                          <!--     <div class="custom-control custom-checkbox ml-1 text-white"> -->
                          <!--       <input type="checkbox" class="custom-control-input" id="basic_checkbox_1"> -->
                          <!--       <label class="custom-control-label" for="basic_checkbox_1">Remember my preference</label> -->
                          <!--     </div> -->
                          <!-- </div> -->
                          <!-- <div class="form-group"> -->
                          <!--     <a class="text-white" href="">Forgot Password?</a> -->
                          <!-- </div> -->
                      </div>
                      <div class="text-center">
                          <button type="submit" class="btn bg-white text-primary btn-block">Iniciar sesión</button>
                      </div>
                  </form>
                  <!-- <div class="new-account mt-3"> -->
                  <!--     <p class="text-white">Don't have an account? <a class="text-white" href="{!! url('/page-register'); !!}" >Sign up</a></p> -->
                  <!-- </div> -->
              </div>
          </div>
      </div>
</div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted')
        }
    }
</script>