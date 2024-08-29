<template>
  <!-- MODAL CREAR USUARIO -->
  <div
    class="modal fade"
    id="modal_login_register"
    tabindex="-1"
    role="dialog"
    aria-hidden="false"
    data-backdrop="static"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Para ingresar a la capacitación debes iniciar sesión, si no tienes
            una cuenta debes registrarte
          </h5>
        </div>
        <div class="modal-body">
          <div v-if="view == ''" class="row m-auto">
            <div class="col-6 justify-content-center">
              <button class="btn btn-barra-naranja" @click="setView(1)">
                Iniciar sesión
              </button>
            </div>
            <div class="col-6 justify-content-center">
              <button class="btn btn-barra-naranja" @click="setView(2)">
                Registrarse
              </button>
            </div>
          </div>
          <div v-if="view == 1" class="m-auto">
            <login-component
              mode="1"
              @redirect="OnPushRedirect"
            ></login-component>
          </div>
          <div v-if="view == 2" class="">
            <register-lider-grupo-empresa-component
              :email_admin="email_administrador"
              :nombre_org="nom_organizacion"
              mode="1"
              @redirect="OnPushRedirect"
            ></register-lider-grupo-empresa-component>
          </div>
        </div>
        <div class="modal-footer"></div>
      </div>
    </div>
  </div>
</template>

<script>
import LoginComponent from "../../../../../../resources/js/components/pages/auth/LoginComponent.vue";
import RegisterLiderGrupoEmpresaComponent from "../../../../../../resources/js/components/pages/auth/RegisterLiderGrupoEmpresaComponent.vue";

export default {
  props: {
    id_training: String,
    main_account_id: String,
    email_administrador: String,
    nom_organizacion: String,
  },
  components: {
    LoginComponent,
    RegisterLiderGrupoEmpresaComponent,
  },
  mounted() {
    $("#modal_login_register").modal("show");
  },
  data() {
    return {
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      view: "",
    };
  },
  methods: {
    setView(number) {
      this.view = number;
    },
    OnPushRedirect() {
      loading(true);
      localStorage.setItem("id_training", this.id_training);
      window.location.href = this.url + "capacitaciones";
    },
  },
};
</script>

<style scoped>
.btn-barra-naranja a {
  color: #e6f0ff;
}

.btn-barra-naranja {
  background-color: #ff7f00;
  border-color: #ff7f00;
  color: white;
  /* box-shadow: none !important; */
}

.btn-barra-naranja:hover {
  background-color: #ff8000e0;
  color: white;
}
</style>