<template>
  <div class="authincation-content">
    <div class="row no-gutters">
      <div class="col-xl-12">
        <div class="auth-form">
          <div class="text-center mb-3">
            <a href="" class="title-login">
              <img
                class="logo_registrarme"
                :src="url + '/img/logo_principal_primary.png'"
                alt="BeBlum"
              />
            </a>
          </div>
          <h4
            class="text-center mb-4 text-white subtitulo"
            style="font-size: 19px"
          >
            Crear cuenta
          </h4>
          <h4
            v-if="email_admin == null"
            class="text-center mb-4 text-white subtitulo"
            style="font-size: 19px"
          >
            Creación de cuenta para
            <span style="color: rgb(255, 127, 0)">colaboradores</span>, para su
            registro necesitará el correo del
            <span style="color: rgb(255, 127, 0)"
              >administrador de su cuenta.</span
            >
          </h4>
          <h4
            v-if="email_admin != null"
            class="text-center mb-4 text-white subtitulo"
            style="font-size: 19px"
          >
            Bienvenido al registro de colaboradores de la organización
            <span style="color: rgb(255, 127, 0)">{{ nombre_org }}</span>
          </h4>
          <form @submit.prevent="checkForm">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label class="mb-1 text-white"
                    ><strong>Nombre Completo</strong>
                    <span class="requerido">*</span></label
                  >
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.fullname"
                    name="fullname"
                    id="fullname"
                    v-on:keyup="validar"
                  />
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label class="mb-1 text-white"
                    ><strong>Numero de identificación</strong>
                    <span class="requerido">*</span></label
                  >
                  <input
                    type="number"
                    class="form-control"
                    v-model="form.identification"
                    name="identification"
                    id="identification"
                    v-on:keyup="validar"
                  />
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label class="mb-1 text-white ml-1"
                    ><strong>Correo electrónico</strong
                    ><span class="requerido">*</span></label
                  >
                  <input
                    type="email"
                    class="form-control"
                    v-model="form.email"
                    name="email"
                    id="email"
                    v-on:keyup="validar"
                  />
                </div>
              </div>
            </div>

            <div class="row mb-3">
                <div class="col" v-if="email_admin != null">
                    <label class="mb-1 text-white">
                        <strong>Centro de costo asignado:</strong><span class="requerido">*</span>
                    </label>
                    <div class="rounded" id="punto">
                        <Select_Savk
                            ref="sel2Punto"
                            :options="evaluation_points"
                            :maxItem="20"
                            placeholder="Seleccione una opción"
                            @selected="OnSelectedPuntoEvaluacion"
                        />
                    </div>
                </div>
              <div class="col">
                <div class="form-group">
                  <label class="mb-1 text-white"
                    ><strong>Contraseña</strong>
                    <span class="requerido">*</span></label
                  >
                  <input
                    type="password"
                    class="form-control"
                    v-model="form.password"
                    name="password"
                    id="password"
                    v-on:keyup="validar"
                  />
                </div>
              </div>

              <div class="col">
                <div class="form-group">
                  <label class="mb-1 text-white"
                    ><strong>Confirmar contraseña</strong
                    ><span class="requerido">*</span></label
                  >
                  <input
                    id="confirm_password"
                    type="password"
                    class="form-control"
                    v-model="form.confirm_password"
                    name="confirm_password"
                    v-on:keyup="validar"
                  />
                </div>
              </div>
            </div>

            <div v-if="email_admin == null" class="row mb-3">
              <div class="col">
                <div class="form-group">
                  <label class="mb-1 text-white"
                    ><strong
                      >Correo electrónico del administrador de su cuenta</strong
                    >
                    <span class="requerido">*</span></label
                  >
                  <input
                    type="email"
                    class="form-control"
                    v-model="form.email_administrator"
                    name="email_administrator"
                    id="email_administrator"
                  />
                </div>
              </div>
            </div>

            <div class="new-account mt-3 mb-3 texto-12 text-center">
              <input
                id="checkAcceptTerm"
                v-model="form.checkAcceptTerm"
                class="form-check-input"
                type="checkbox"
                value=""
              />
              <label class="form-check-label text-white" for="checkAcceptTerm">
                Acepto términos y condiciones.
              </label>
            </div>

            <div class="col-lg-3 mx-auto text-center">
              <button
                type="submit"
                class="btn text-light btn-block dev_button_login"
                style="background-color: #145c54"
              >
                Registrarme
              </button>
            </div>
          </form>
          <div v-if="mode != 1" class="new-account mt-3 texto-12 text-center">
            <p class="text-white">
              ¿Ya tienes cuenta?
              <a class="text-white" :href="url"
                ><b style="color: #ff7f00">Iniciar sesión</b></a
              >
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Select_Savk from "../otros/Select_Savk.vue";
export default {
  components: {
    Select_Savk,
  },
  async mounted() {
    this.evaluation_points = await this.getEvaluationPoints();
    if (this.email_admin != null) {
      this.form.email_administrator = this.email_admin;
      this.form.id_grupo = 48;
    }
  },
  props: {
    email_admin: String,
    nombre_org: String,
    mode: String,
    main_account: String
  },
  data() {
    return {
      sectores: [],
      url: ROOT_URL,
      evaluation_points: null,
      form: {
        fullname: "",
        identification: "",
        email: "",
        password: "",
        confirm_password: "",
        punto: null,
        email_administrator: "",
        id_grupo: 48,
        checkAcceptTerm: false,
      },
      optionsFetch: (data) => ({
        method: "POST",
        body: JSON.stringify(data),
        headers: {
          "X-CSRF-TOKEN": document
            .querySelector('[name="csrf-token"]')
            .getAttribute("content"),
          "Content-Type": "application/json",
        },
      }),
    };
  },

  methods: {
    OnSelectedPuntoEvaluacion(item) {
      this.form.punto = item.id;
      var elemento = document.getElementById('punto');

      if (this.form.punto != null) {
        elemento.classList.remove("noDiligenciado");
      }
    },
    fetchSectores() {
      fetch(this.url + "/sendSectores") // Llamar a la ruta en Laravel
        .then((response) => response.json()) // Convertir la respuesta a JSON
        .then((data) => {
          this.sectores = data; // Asignar los datos a la propiedad del componente
        })
        .catch((error) => console.error(error));
    },
    checkForm: async function (e) {
      var validado = true;
      for (const [key, value] of Object.entries(this.form)) {
        //ID PARA AGREGAR O QUITAR CLASS
        var elemento = document.getElementById(key);

        if (value == "" || typeof value === "undefined") {
          validado = false;
          elemento.classList.add("noDiligenciado");

          window.scroll({
            top: 180,
            left: 0,
            behavior: "smooth",
          });
        }
      }
      if (validado == false) {
        if (this.form.checkAcceptTerm == false) {
          toastr.warning("Por favor acepte los términos y condiciones!!!");
          return;
        }
        toastr.warning("Por favor diligenciar todos los campos!!!");
      } else {
        //toastr.success("Todos los campos diligenciados correctamente")
        this.onSubmit();
      }
    },
    validar: function (e) {
      var elemento = document.getElementById(e.target.id);

      if (e.target.value != "") {
        elemento.classList.remove("noDiligenciado");
      }
    },

    //Obtengo todas los puntos para el Select
    async getEvaluationPoints() {
      //load(true);
      const response = await fetch(
        `${this.url}` + `/puntos-evaluacion-main-account/${this.main_account}`
      );
      const data = await response.json();
      //load(false);
      return data;
    },

    async onSubmit() {
      try {
        loading();
        const response = await fetch(
          this.url + "/registro-lideres-grupo-empresa",
          this.optionsFetch(this.form)
        );
        const { status, msg } = await response.json();
        loading(false);
        if (status === 200) {
          if (this.mode == 1) {
            this.$emit("redirect");
          } else {
            window.location.replace(this.url);
          }
        } else {
          toastr.warning(msg);
        }
      } catch (error) {
        loading(false);
        toastr.error("Ha ocurrido un error interno.");
      }
    },
  },
};
</script>

<style scoped>
.authincation-content {
  background-color: white;
  font-family: "Roboto", sans-serif;
}

.requerido {
  color: red;
}

.text-white {
  color: #145c54 !important;
}

.noDiligenciado {
  box-shadow: 0 0 12px red;
}
#swal2-content strong {
  font-weight: bold !important;
}
</style>
