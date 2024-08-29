<template>
  <div class="container-fluid">
    <div class="flex-wrap mb-2 align-items-center justify-content-between">
      <!-- <div class="mb-3 mr-3 col-lg-12 d-flex justify-content-between"> -->
      <!--  <h6 class="fs-16 text-red font-w600 mb-0" style="color: #002f54">{{ Auth::user()->nombre_com }}</h6>-->
      <!-- <button class="btn btn-primary" onclick="OnClickCrearEmpresa(this);">Crear empresa</button> -->
      <!-- </div> -->

      <div class="row menu-cap">
        <div class="container_bnts d-flex" id="container_bnts">

          <div class="btn-menu">
            <button
              @click="changeMode(1)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 1 }"
            >
              Grupo Empresas
            </button>
          </div>

          <div class="btn-menu">
            <button
              @click="changeMode(2)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 2 }"
            >
              Empresas
            </button>
          </div>

          <div class="btn-menu">
            <button
              @click="changeMode(3)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 3 }"
            >
              Centros de costo
            </button>
          </div>

          <div class="btn-menu">
            <button
              @click="changeMode(4)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 4 }"
            >
              Usuarios
            </button>
          </div>
        </div>

        <div class="div-busqueda">
            <div class="mr-2" v-if="permisos.includes('org-mio-invitar_usuarios')">
                <button
                class="btn btn-barra-naranja"
                ref="tour_btn_invite"
                style="width: max-content"
                @click="OnClickOpenModalInvitar"
                >
                Invitar usuarios
                </button>
            </div>
            <div class="mr-2" v-if="(
                (permisos.includes('org-mio-crear_usuarios') && titleButton == 'Crear usuarios') ||
                (permisos.includes('org-mio-crear_centro_costo') && titleButton == 'Crear centro de costo') ||
                (permisos.includes('org-mio-crear_empresa') && titleButton == 'Crear empresa') ||
                (permisos.includes('org-mio-crear_grupo_empresa') && titleButton == 'Crear grupo empresa')
                )">
            <button
              class="btn btn-barra-naranja"
              style="width: max-content"
              ref="tour_btn_create_user"
              @click="OnClickOpenModal"
            >
              {{ titleButton }}
            </button>
          </div>

          <div class="input-group">
            <input
              v-model="inputSearch"
              type="text"
              class="form-control form-control-busqueda"
              :placeholder="placeholder"
              @keyup.enter="OnKeyUpSearch()"
            />
            <div class="input-group-append">
              <span class="input-group-text btn-barra-naranja">
                <a href="javascript:void(0)" class="aBuscar">
                  <i class="flaticon-381-search-2"></i>
                </a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <div class="tab-content">
          <!-- Company Group -->

          <div id="All" v-if="mode == 1">
            <company-group-component
              ref="componentCompanyGroup"
              :search="inputSearchCompanyGroup"
            ></company-group-component>
          </div>

          <!-- Company -->
          <div id="Empresa" v-if="mode == 2">
            <company-component
              ref="componentCompany"
              :search="inputSearchCompany"
            ></company-component>
          </div>

          <!-- Punto Evaluacion -->
          <div id="id_pe" v-if="mode == 3">
            <punto-evaluacion-component
              ref="componentPuntoEvaluacion"
              :search="inputSearchPuntosEvaluacion"
            ></punto-evaluacion-component>
          </div>

          <!-- Usuarios -->
          <div id="Usuarios" v-if="mode == 4">
            <usuarios-component
              :search="inputSearchUsuarios"
              ref="componentUsuario"
            ></usuarios-component>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CompanyComponent from "./CompanyComponent.vue";
import CompanyGroupComponent from "./CompanyGroupComponent.vue";
import PuntoEvaluacionComponent from "./PuntoEvaluacionComponent.vue";
import UsuariosComponent from "./UsuariosComponent.vue";
import { useShepherd } from 'vue-shepherd'
import '../../../../../node_modules/shepherd.js/dist/css/shepherd.css';

export default {
  components: {
    CompanyGroupComponent,
    PuntoEvaluacionComponent,
    UsuariosComponent,
    CompanyComponent,
  },
  data() {
    return {
    permisos : JSON.parse(localStorage.getItem('permisos')),
      mode: 1,
      inputSearch: "",
      inputSearchCompanyGroup: "",
      inputSearchCompany: "",
      inputSearchUsuarios: "",
      inputSearchPuntosEvaluacion: "",
      placeholder: "Buscar nombre",
      titleButton: "Crear Usuario",
      tour: null
    };
  },
  mounted()
  {
    // this.CreateTour();
    // this.tour.start();
  },
  methods: {
      CreateTour()
      {
          this.tour = useShepherd({
              useModalOverlay: true,
              defaultStepOptions: {
                  classes: 'shadow-md bg-purple-dark',
                  scrollTo: true
              }
          });

          this.tour.addStep({
              id: "step_number_indicators_one",
              title: `<b>Titulo</b>`,
              text: '...',
              attachTo: { element: document.getElementById("container_bnts"), on: 'bottom-end' },
              classes: 'example-step-extra-class',
              buttons: [
                  {
                      text: 'Siguiente',
                      action: this.tour.next,
                      classes: "btn btn-primary"
                  }
              ],
              when: {
                  show: function() {
                      window.scrollTo(0, 0);
                  }
              },
              cancelIcon: {
                  enabled: false,
                  label: "aria-label"
              }
          });

          this.tour.addStep({
              id: "step_number_indicators_two",
              title: `<b>Titulo 2</b>`,
              text: '...',
              attachTo: { element: this.$refs.tour_btn_invite, on: 'bottom-end' },
              classes: 'example-step-extra-class',
              buttons: [
                  {
                      text: 'Siguiente',
                      action: this.tour.next,
                      classes: "btn btn-primary"
                  }
              ],
              when: {
                  show: function() {
                      window.scrollTo(0, 0);
                  }
              },
              cancelIcon: {
                  enabled: false,
                  label: "aria-label"
              }
          });

          this.tour.addStep({
              id: "step_number_indicators_three",
              title: `<b>Titulo 3</b>`,
              text: '...',
              attachTo: { element: this.$refs.tour_btn_create_user, on: 'bottom-end' },
              classes: 'example-step-extra-class',
              buttons: [
                  {
                      text: 'Siguiente',
                      action: this.tour.next,
                      classes: "btn btn-primary"
                  }
              ],
              when: {
                  show: function() {
                      window.scrollTo(0, 0);
                  }
              },
              cancelIcon: {
                  enabled: false,
                  label: "aria-label"
              }
          });

          this.tour.addStep({
              id: "step_number_indicators_fourth",
              title: `<b>Titulo 4</b>`,
              text: '...',
              attachTo: { element: document.getElementById("tour_head_cc"), on: 'bottom-end' },
              classes: 'example-step-extra-class',
              buttons: [
                  {
                      text: 'Terminar',
                      action: this.tour.next,
                      classes: "btn btn-primary"
                  }
              ],
              when: {
                  show: function() {
                      window.scrollTo(0, 0);
                  }
              },
              cancelIcon: {
                  enabled: false,
                  label: "aria-label"
              }
          });
      },
    changeMode(mode) {
      loading(true);
      this.mode = mode;

      switch (mode) {
        case 1:
          this.titleButton = "Crear grupo empresa";
          this.placeholder = "Buscar nombre";
          break;
        case 2:
          this.titleButton = "Crear empresa";
          this.placeholder = "Buscar nombre";
          break;
        case 3:
          this.titleButton = "Crear centro de costo";
          this.placeholder = "Buscar nombre";
          break;
        case 4:
          this.titleButton = "Crear usuario";
          this.placeholder = "Buscar nombre";
          break;

        default:
          break;
      }

      this.inputSearch = "";
      this.inputSearchCompanyGroup = "";
      this.inputSearchCompany = "";
      this.inputSearchUsuarios = "";
      this.inputSearchPuntosEvaluacion = "";

      loading(false);
    },
    OnKeyUpSearch() {
      switch (this.mode) {
        case 1:
          this.inputSearchCompanyGroup = this.inputSearch;
          break;

        case 2:
          this.inputSearchCompany = this.inputSearch;
          break;
        case 3:
          this.inputSearchPuntosEvaluacion = this.inputSearch;
          break;
        case 4:
          this.inputSearchUsuarios = this.inputSearch;
          break;
        default:
      }
    },

    async OnClickOpenModalInvitar() {
        await this.$refs.componentUsuario.OnClickOpenModalInvitar();
    },

    async OnClickOpenModal() {
      switch (this.mode) {
        case 1:
          await this.$refs.componentCompanyGroup.openModalCreate();
          break;

        case 2:
          await this.$refs.componentCompany.openModalCreate();
          break;

        case 3:
          await this.$refs.componentPuntoEvaluacion.openModalCreate();
          break;

        case 4:
          await this.$refs.componentUsuario.openModalCreate();
          break;
        default:
      }
    },
  },
};
</script>

<style scoped>
.dev-icon-back {
  font-size: 25px;
  cursor: pointer;
}

.dev-icon-certificate {
  color: #fe634e;
  font-size: 24px;
  cursor: pointer;
}

.dev-bg {
  background: rgba(254, 99, 78, 0.05);
  border-radius: 1.25rem;
  padding: 14px 18px 14px 18px;
}

.btn-menu {
  margin-right: 5px;
  margin-top: 5px;
  margin-bottom: 5px;
}

.menu-cap {
  background-color: white;
  border-radius: 1.25rem;
  /* padding: 8px; */
  padding-left: 5px;
  padding-right: 5px;
  margin-bottom: 20px;
  margin-left: 0px !important;
  margin-right: 0px !important;
  align-items: center;
}

.div-busqueda {
  display: flex;
  margin-left: auto;
  width: 50%;
  margin-top: 5px;
  margin-bottom: 5px;
}

.btn-barra {
  padding: 0.6rem 0.8rem;
  background-color: #e6f0ff;
  border-color: #e6f0ff;
  color: #002f54;
  box-shadow: none !important;
}

.btn-barra:hover {
  background-color: #002f54;
  border-color: #002f54;
  color: #e6f0ff;
  box-shadow: none !important;
}

.btn-barra-activo {
  background-color: #002f54;
  border-color: #002f54;
  color: #e6f0ff;
  box-shadow: none !important;
}

.btn-barra-naranja a {
  color: #e6f0ff;
}

.btn-barra-naranja {
  background-color: #ff7f00;
  border-color: #ff7f00;
  color: white;
  box-shadow: none !important;
}

.btn-barra-naranja:hover {
  background-color: #ff8000e0;
  color: white;
}

@media (max-width: 1125px) {
  .div-busqueda {
    /* margin-left: auto; */
    width: 100%;
    margin-top: 5px;
    margin-bottom: 5px;
  }
}
.dev-fonts-icon {
  font-size: 30px;
  margin-left: 8px;
}

.form-control-busqueda {
    height: 45px!important;
}

</style>
