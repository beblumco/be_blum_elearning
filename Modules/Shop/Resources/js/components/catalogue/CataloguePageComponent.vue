<template>
  <div class="container-fluid">
    <!-- HEADER -->
    <div class="card" style="height: 90px">
      <div class="row" style="margin: auto; width: 100%">
        <div class="col-sm-4"></div>

        <div class="col-sm-4">
          <div class="input-group d-xl-inline-flex d-none">
            <input
              v-model="search_input"
              type="text"
              class="form-control"
              placeholder="Buscar por nombre o referencia..."
              @keyup.enter="SearchKeyPress"
            />
            <div class="input-group-append">
              <span class="input-group-text"
                ><a href="javascript:void(0)"
                  ><i class="flaticon-381-search-2"></i></a
              ></span>
            </div>
          </div>
        </div>

        <!-- HEADER ICONS -->
        <div class="col-sm-4 d-flex justify-content-end">
          <div class="d-flex justify-content-end">
            <div
              class="shop-cart-icon p-1 dev_container_icon_pres"
              data-toggle="tooltip"
              data-placement="top"
              title="Presupuesto"
              @click.prevent.stop="OnClickOpenModalPresupuesto()"
              v-if="this.has_ajust_pres == 1"
            >
              <svg
                style="cursor: pointer"
                width="25"
                height="25"
                viewBox="0 0 25 25"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M6.07438 25H7.95454V22.6464C11.8595 22.302 14 19.6039 14 16.8197C14 12.7727 10.8471 11.9977 7.95454 11.3088V5.10907C9.34297 5.4535 10.1529 6.5155 10.2686 7.66361H13.7975C13.5372 4.42021 11.281 2.61194 7.95454 2.32492V0H6.07438V2.35362C2.4876 2.66935 0 4.87945 0 8.09415C0 12.1412 3.18182 12.9449 6.07438 13.6625V19.977C4.45455 19.69 3.64463 18.628 3.52893 17.1929H0C0 20.4363 2.54545 22.3594 6.07438 22.6751V25ZM10.6736 16.992C10.6736 18.4845 9.69008 19.69 7.95454 19.977V14.1504C9.51653 14.6383 10.6736 15.3559 10.6736 16.992ZM3.35537 7.92193C3.35537 6.17107 4.48347 5.22388 6.07438 5.02296V10.8209C4.5124 10.333 3.35537 9.58668 3.35537 7.92193Z"
                  fill="#FE634E"
                ></path>
              </svg>
            </div>

            <div
              class="shop-cart-icon p-1"
              data-toggle="tooltip"
              data-placement="top"
              title="Carrito"
              @click="OnClickShoppingCar()"
            >
              <span>{{ total_car_buy }}</span>
              <img
                class="shop-icon-svg m-1"
                src="img/shop_module/shopping-cart-solid.svg"
                alt=""
              />
            </div>

            <div
              class="shop-cart-icon p-1"
              data-toggle="tooltip"
              data-placement="top"
              title="Administración"
              v-if="can_to_approve == 1"
              @click="OnClickReportsLink()"
            >
              <img
                class="shop-icon-svg m-1"
                src="img/shop_module/administration.svg"
                alt=""
              />
            </div>
            <img
              class="shop-icon-svg m-1 p-1"
              data-toggle="tooltip"
              data-placement="top"
              title="Historial"
              src="img/shop_module/history-solid.svg"
              alt=""
              onclick="OnClickRecord()"
            />
          </div>
        </div>
        <!-- HEADER ICONS - END -->
      </div>
    </div>
    <!-- HEADER - END -->

    <!-- PRODUCTS -->
    <div class="row col-lg-12">
      <div class="col-sm-4" v-for="product in filterProducts" :key="product.id">
        <ProductCardShopComponent :product="product" @update-car="UpdateCar" />
      </div>
      <div v-if="products.length == 0" class="card col-lg-12">
        <div class="col-lg-12 card-body">
          <p class="dev_no_data">No tienes productos asignados.</p>
        </div>
      </div>
    </div>
    <!-- PRODUCTS - END -->

    <!-- Modal Compartir -->
    <div
      class="modal fade"
      id="modal-share"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-title-share">
              Compartir - Carpeta 1
            </h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label for="staticEmail" class="col-sm-2 col-form-label"
                >Grupo Empresa</label
              >
              <div class="col-sm-10">
                <select id="select-grupo-empresa" class="single-select">
                  <option value="" selected>
                    Seleccione un grupo empresa.
                  </option>
                  <option value="AL">Grupo Empresa 1</option>
                  <option value="WY">Grupo Empresa 2</option>
                </select>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="staticEmail" class="col-sm-2 col-form-label"
                >Empresa</label
              >
              <div class="col-sm-10">
                <select
                  id="select-empresa"
                  class="single-select"
                  placeholder="Seleccione una empresa"
                >
                  <option value="" selected>Seleccione una empresa.</option>
                  <option value="AL">Empresa 1</option>
                  <option value="WY">Empresa 2</option>
                </select>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="staticEmail" class="col-sm-2 col-form-label"
                >Punto de Venta</label
              >
              <div class="col-sm-10">
                <select
                  id="select-pdv"
                  class="multi-select"
                  placeholder="Seleccione un punto de venta"
                >
                  <option value="all" selected>Todos</option>
                  <option value="AL">Pdv 1</option>
                  <option value="WY">Pdv 2</option>
                </select>
              </div>
            </div>

            <fieldset>
              <legend>
                <h6>Permisos</h6>
              </legend>

              <div class="row mb-2">
                <div class="col-sm-3 col-6">
                  <div class="custom-control custom-checkbox mb-3">
                    <input
                      type="checkbox"
                      class="custom-control-input check-permits"
                      checked
                    />
                    <label class="custom-control-label" for="customCheckBox1"
                      >Crear</label
                    >
                  </div>
                </div>

                <div class="col-sm-3 col-6">
                  <div class="custom-control custom-checkbox mb-3">
                    <input
                      type="checkbox"
                      class="custom-control-input check-permits"
                      checked
                    />
                    <label class="custom-control-label" for="customCheckBox1"
                      >Modificar</label
                    >
                  </div>
                </div>

                <div class="col-sm-3 col-6">
                  <div class="custom-control custom-checkbox mb-3">
                    <input
                      type="checkbox"
                      class="custom-control-input check-permits"
                    />
                    <label class="custom-control-label" for="customCheckBox1"
                      >Compartir</label
                    >
                  </div>
                </div>

                <div class="col-sm-3 col-6">
                  <div class="custom-control custom-checkbox mb-3">
                    <input
                      type="checkbox"
                      class="custom-control-input check-permits"
                    />
                    <label class="custom-control-label" for="customCheckBox1"
                      >Eliminar</label
                    >
                  </div>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger light"
              data-dismiss="modal"
            >
              Cerrar
            </button>
            <button type="button" class="btn btn-primary">Compartir</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal compartir -->

    <!-- MODAL PRESUPUESTO -->
    <div
      class="modal fade"
      id="modal-presupuesto"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      data-backdrop="static" data-keyboard="false"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <div class="col-lg-12 d-flex justify-content-between">
              <h5 class="modal-title" id="modal-title-share">Presupuesto</h5>
              <div class="col-lg-4 mt-2">
                <Select_Savk
                  id="selectYear"
                  ref="selectYear"
                  v-model="modals.modal_presupuesto.tab_config.data"
                  :options="modals.modal_presupuesto.tab_config.options"
                  :maxItem="20"
                  placeholder="Seleccione un año"
                  @selected="OnSelectedYear"
                  :class="{ 'skeleton-loader': modals.modal_presupuesto.skeleton_year }"
                />
              </div>
              <h5 class="modal-title mr-3">
                <span class="font-weight-bold">Presupuesto <span>({{ (modals.modal_presupuesto.tab_pdvs.options?.find(mn => mn.id == modals.modal_presupuesto.filters.mouth))?.name }})</span>: </span>
                {{ $FormatCOMoney(modals.modal_presupuesto.tab_pdvs.total)}}
              </h5>
            </div>
            <button
              type="button"
              class="close"
              @click.stop.prevent="OnClickCloseModalPresupuesto()"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="default-tab">
              <ul class="nav nav-tabs" role="tablist">
                <li
                  class="nav-item dev-cursor-pointer"
                  @click="OnClickChangeTab(1)"
                >
                  <a
                    class="nav-link"
                    :class="{ active: modals.modal_presupuesto.mode == 1 }"
                    ><i class="la la-cube mr-2"></i> PDV's</a
                  >
                </li>
                <!--
                  <li
                  class="nav-item dev-cursor-pointer"
                  @click="OnClickChangeTab(2)"
                >
                  <a
                    class="nav-link"
                    :class="{ active: modals.modal_presupuesto.mode == 2 }"
                    ><i class="fa fa-link mr-2"></i> Asignación</a
                  >
                </li>
                -->
                <li
                  class="nav-item dev-cursor-pointer"
                  @click="OnClickChangeTab(3)"
                >
                  <a
                    class="nav-link"
                    :class="{ active: modals.modal_presupuesto.mode == 3 }"
                    ><i class="las la-edit mr-2"></i> Modificación</a
                  >
                </li>
              </ul>
              <div class="tab-content">
                <div
                  class="tab-pane fade"
                  :class="{ 'active show': modals.modal_presupuesto.mode == 1 }"
                  id="list"
                  role="tabpanel"
                  style="overflow: auto;max-height: 600px;"
                >
                  <div class="col-lg-4 mt-2">
                    <Select_Savk
                      id="selectMonth"
                      ref="selectMonth"
                      v-model="modals.modal_presupuesto.filters.mouth"
                      :options="modals.modal_presupuesto.tab_pdvs.options"
                      :maxItem="20"
                      placeholder="Seleccione un mes"
                      @selected="OnSelectedMonth"
                      :class="{ 'skeleton-loader': modals.modal_presupuesto.skeleton_pdv }"
                    />
                  </div>
                  <div class="col-lg-12 mt-2 d-flex flex-wrap">
                    <div class="table-responsive">
                      <table class="table card-table dev_table_pdvs" :class="{ 'skeleton-loader': modals.modal_presupuesto.skeleton_pdv }">
                        <thead>
                          <tr>
                            <th>P. Evaluación</th>
                            <th>Presupuestado</th>
                            <th>Consumido</th>
                            <th>Porcentaje</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="item in modals.modal_presupuesto.tab_pdvs
                              .data"
                            :key="item.id"
                          >
                            <td>{{ item.nombre }}</td>
                            <td>{{ $FormatCOMoney(item.presupuesto) }}</td>
                            <td>{{ $FormatCOMoney(item.consumido) }}</td>
                            <td>
                              <span class=""
                                >{{ item.porcentaje }}%</span
                              >
                            </td>
                          </tr>

                          <tr v-if="modals.modal_presupuesto.tab_pdvs.data.length == 0">
                            <td colspan="4" class="font-weight-bold text-center">No tienes presupuestos registrados.</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div
                  class="tab-pane fade"
                  id="assign"
                  :class="{ 'active show': modals.modal_presupuesto.mode == 2 }"
                  style="overflow: auto;max-height: 600px;"
                >
                  <div class="col-lg-12 d-flex mt-3 d-flex flex-wrap">
                    <div
                      class="col-lg-12 d-flex justify-content-between p-0 mb-3"
                    >
                      <div class="col-lg-10 d-flex align-items-center">
                        <div class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input check-permits"
                            id="check_0"
                            v-model="modals.modal_presupuesto.tab_assign.all"
                            @click="OnChangeCheckPdv({})"
                          />
                          <label
                            class="custom-control-label dev-cursor-pointer"
                            :for="`check_0`"
                            >Seleccionar todo</label
                          >
                        </div>
                      </div>

                      <div
                        class="col-lg-2 d-flex justify-content-end"
                        v-if="modals.modal_presupuesto.tab_assign.show_button"
                      >
                        <button
                          type="button"
                          class="btn btn-primary"
                          @click.stop.prevent="OnClickOpenPresupuestoAssign()"
                        >
                          Asignar
                        </button>
                      </div>
                    </div>

                    <div
                      class="col-lg-4"
                      v-for="pdv in modals.modal_presupuesto.tab_assign
                        .pdvs_selected"
                      :key="pdv.id"
                    >
                      <div class="custom-control custom-checkbox mb-3">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          v-model="pdv.selected"
                          :id="`check_${pdv.id}`"
                          @click="OnChangeCheckPdv(pdv)"
                        />
                        <label
                          class="custom-control-label dev-cursor-pointer"
                          :for="`check_${pdv.id}`"
                          >{{ pdv.nombre }}</label
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div
                  class="tab-pane fade"
                  :class="{ 'active show': modals.modal_presupuesto.mode == 3 }"
                  id="settings"
                  style="overflow: auto;max-height: 600px;"
                >
                  <div class="table-responsive">
                    <table class="table card-table dev_table_months_pdvs" :class="{ 'skeleton-loader': modals.modal_presupuesto.skeleton_modify }">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Ene.</th>
                          <th>Feb.</th>
                          <th>Mar.</th>
                          <th>Abr.</th>
                          <th>May.</th>
                          <th>Jun.</th>
                          <th>Jul.</th>
                          <th>Ago.</th>
                          <th>Sept.</th>
                          <th>Oct.</th>
                          <th>Nov.</th>
                          <th>Dic.</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item, index) in modals.modal_presupuesto
                            .tab_modify.data"
                          :key="index"
                        >
                          <td>{{ item.nombre }}</td>
                          <td
                            v-for="(press, i) in item.presupuesto"
                            :key="i"
                            @click.stop.prevent="
                              OnClickPresupuestoValue(
                                i,
                                modals.modal_presupuesto.tab_config.data,
                                press,
                                item.id
                              )
                            "
                          >
                            <i
                              v-if="press == null"
                              class="bi bi-plus-circle-fill"
                              style="font-size: 22px; color: #2d8b1a"
                            ></i>
                            {{
                              press != null
                                ? $FormatCOMoney(press.presupuesto)
                                : ""
                            }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger light"
              @click.stop.prevent="OnClickCloseModalPresupuesto()"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- MODAL PRESUPUESTO - END -->

    <!-- MODAL PRESUPUESTO VALUE -->
    <div
      class="modal fade"
      id="modal-presupuesto-value"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      data-backdrop="static" data-keyboard="false"
    >
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modificar presupuesto <span>({{ modals.modal_presupuesto_value.month_text }})</span></h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <div v-if="modals.modal_presupuesto_value.checkVisible == true" class="custom-control custom-checkbox mb-3">
              <input id="apply_all_months" type="checkbox" class="custom-control-input" :checked="modals.modal_presupuesto_value.allMonth" @change="OnChangeAssignAllMonth()" />
              <label class="custom-control-label" for="apply_all_months">Aplicar valor y día de corte a todos los meses sin presupuestar</label>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <input type="text" class="form-control input-number" placeholder="$00.000" v-model="modals.modal_presupuesto_value.data" />
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control input-number" placeholder="Día" v-model="modals.modal_presupuesto_value.day_end" />
              </div>
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click.stop.prevent="OnClickAssignPresupuesto()">
              Guardar cambios
            </button>
            <button type="button" class="btn btn-danger" @click.stop.prevent="OnClickClosePresupuestoValue()">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- MODAL PRESUPUESTO VALUE -END -->

    <!-- MODAL ASSIGNACION PRESUPUESTO X MES -->
    <div
      class="modal fade"
      id="modal-presupuesto-assign"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      data-backdrop="static" data-keyboard="false"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Asignar presupuesto</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12 d-flex">
              <div class="col-lg-6 mt-2">
                <Select_Savk
                  id="selectMonthAssign"
                  ref="selectMonthAssign"
                  v-model="modals.modal_presupuesto_assign.data.value_month"
                  :options="modals.modal_presupuesto_assign.options"
                  :maxItem="20"
                  placeholder="Seleccione un mes"
                  @selected="OnSelectedMonthAssign"
                />
              </div>

              <div class="col-lg-6">
                <div class="form-group m-0">
                  <input
                    type="text"
                    class="form-control input-number"
                    placeholder="$00.000"
                    v-model="
                      modals.modal_presupuesto_assign.data.value_presupuesto
                    "
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-primary"
              @click.stop.prevent="OnClickAssignPresupuesto()"
            >
              Guardar cambios
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click.stop.prevent="OnClickClosePresupuestoAssign()"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- MODAL ASSIGNACION PRESUPUESTO X MES - END -->
  </div>
</template>

<script>
import Product from "./model/products";
import ProductCardShopComponent from "./ProductCardShopComponent.vue";
import Select_Savk from "../../../../../../resources/js/components/pages/otros/Select_Savk.vue";

export default {
  props: {
    has_ajust_pres: String,
    can_to_approve: String
  },
  components: {
    ProductCardShopComponent,
    Select_Savk,
  },
  created() {},
  async mounted() {
    $('[data-toggle="tooltip"]').tooltip();
    await this.GetDataInit();
  },
  computed: {
    filterProducts() {
      if (this.search_input == "") return this.products;

      return this.products.filter(
        (item) =>
          item.nombre.toLowerCase().includes(this.search_input.toLowerCase()) ||
          item.referencia
            .toLowerCase()
            .includes(this.search_input.toLowerCase())
      );
    },
  },
  data() {
    return {
      config: {
        url: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("url"),
        token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
      },
      total_car_buy: 0,
      modals: {
        modal_presupuesto: {
          skeleton_pdv: false,
          skeleton_modify: false,
          skeleton_year: false,
          mode: 1,
          filters: {
            year: "",
            mouth: "",
          },
          tab_pdvs: {
            options: [
              {
                id: 9,
                name: "Septiembre",
              },
              {
                id: 10,
                name: "Octubre",
              },
              {
                id: 11,
                name: "Noviembre",
              },
            ],
            data: [],
            total: 0,
          },
          tab_assign: {
            show_button: false,
            pdvs_selected: [
              {
                id: 1,
                name: "F. FRISBY Cr.42  (P10)(G61)",
                selected: false,
              },
              {
                id: 2,
                name: "FRISBY H24",
                selected: false,
              },
              {
                id: 3,
                name: "FRISBY -PUERTA PRINCIPAL",
                selected: false,
              },
              {
                id: 4,
                name: "I39 SAN PIO FRISBY",
                selected: false,
              },
            ],
            all: false,
          },
          tab_config: {
            options: [],
            data: "",
          },
          tab_modify: {
            data: [],
          },
        },
        modal_presupuesto_value: {
          data: "",
          month: "",
          id_pdv: null,
          year: "",
          allMonth: 0,
          checkVisible: false,
          month_text: "",
          day_end: ""
        },
        modal_presupuesto_assign: {
          options: [
            {
              id: 1,
              name: "Enero",
            },
            {
              id: 2,
              name: "Febrero",
            },
            {
              id: 3,
              name: "Marzo",
            },
            {
              id: 4,
              name: "Abril",
            },
            {
              id: 5,
              name: "Mayo",
            },
            {
              id: 6,
              name: "Junio",
            },
            {
              id: 7,
              name: "Julio",
            },
            {
              id: 8,
              name: "Agosto",
            },
            {
              id: 9,
              name: "Septiembre",
            },
            {
              id: 10,
              name: "Octubre",
            },
            {
              id: 11,
              name: "Noviembre",
            },
            {
              id: 12,
              name: "Diciembre",
            },
          ],
          data: {
            value_month: "",
            value_presupuesto: "",
          },
        },
      },
      products: [],
      search_input: "",
    };
  },
  methods: {
    async GetDataInit() {
      try {
        let data_form = new FormData();

        let rs = await fetch(`${this.config.url}catalogo/get_data_init`, {
          method: "POST",
          body: data_form,
          headers: { "X-CSRF-TOKEN": this.config.token },
        });
        let rd = await rs.json();

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 202:
            if (data.products.length == 0) this.products = [];
            else this.products = data.products.map((el) => new Product(el));

            let total_quantity = 0;
            if (data.order_pending.length != 0)
              total_quantity = data.order_pending.reduce(
                (acumulador, registro) => acumulador + registro.cantidad,
                0
              );

            this.total_car_buy = total_quantity;
            break;

          default:
            break;
        }
      } catch (error) {
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    async OnClickOpenModalPresupuesto() {
      $("#modal-presupuesto").modal("show");
      await this.GetDataArrayYears();
      await this.OnClickChangeTab(1);
    },
    OnClickCloseModalPresupuesto() {
      $("#modal-presupuesto").modal("hide");
      this.modals.modal_presupuesto.mode = 1;
    },
    OnSelectedMonth(item) {
      if (item.id) {
        this.modals.modal_presupuesto.filters.mouth = item.id;
      }
      this.OnClickChangeTab(this.modals.modal_presupuesto.mode);
    },
    OnSelectedYear(item) {
      this.modals.modal_presupuesto.tab_config.data = item.id;

      this.OnClickChangeTab(this.modals.modal_presupuesto.mode);
    },
    OnClickPresupuestoValue(month, year, data, id_pdv) {
      this.modals.modal_presupuesto_value.allMonth = 0;
      this.modals.modal_presupuesto_value.month = month;
      this.modals.modal_presupuesto_value.year = year;
      this.modals.modal_presupuesto_value.month_text = this.modals.modal_presupuesto_assign.options.find(mn => mn.id == month)?.name;
      this.modals.modal_presupuesto_value.data = data == null ? "" : data.presupuesto;
      this.modals.modal_presupuesto_value.day_end = (data?.dia_corte == null || data?.dia_corte == "null") ? "" : data.dia_corte;
      this.modals.modal_presupuesto_value.id_pdv = id_pdv;

      let pdv = this.modals.modal_presupuesto.tab_modify.data.find(
        (pdv) => pdv.id == id_pdv
      );

      let countNull = 0;

      Object.entries(pdv.presupuesto).forEach((object) => {
        const [key, value] = object;

        if (value == null) {
          countNull++;
        }

        console.log(countNull);
      });

      if (countNull > 0) {
        this.modals.modal_presupuesto_value.checkVisible = true;
      } else {
        this.modals.modal_presupuesto_value.checkVisible = false;
      }

      $("#modal-presupuesto-value").modal("show");
    },
    OnClickClosePresupuestoValue() {
      $("#modal-presupuesto-value").modal("hide");
    },
    OnChangeCheckPdv(pdv) {
      if (pdv.id == undefined)
        this.modals.modal_presupuesto.tab_assign.pdvs_selected.map(
          (pdv) =>
            (pdv.selected = !this.modals.modal_presupuesto.tab_assign.all
              ? true
              : false)
        );
      else pdv.selected = !pdv.selected;

      this.CheckSelectedPdvs();
    },
    CheckSelectedPdvs() {
      let one = this.modals.modal_presupuesto.tab_assign.pdvs_selected.find(
        (pdv) => pdv.selected == true
      );
      if (one) this.modals.modal_presupuesto.tab_assign.show_button = true;
      else this.modals.modal_presupuesto.tab_assign.show_button = false;
    },
    OnSelectedMonthAssign(item) {
      this.modals.modal_presupuesto_assign.data.value_month = item.id;
    },
    OnClickOpenPresupuestoAssign() {
      $("#modal-presupuesto-assign").modal("show");
    },
    OnClickClosePresupuestoAssign() {
      $("#modal-presupuesto-assign").modal("hide");
    },
    OnClickShoppingCar() {
      window.location.href = `${this.config.url}catalogo/carrito`;
    },
    UpdateCar(total_quantity) {
      this.total_car_buy = total_quantity;
    },
    SearchKeyPress() {},
    async OnClickChangeTab(tab) {
      try {

        if(this.modals.modal_presupuesto.skeleton_pdv || this.modals.modal_presupuesto.skeleton_modify) return;
        this.modals.modal_presupuesto.mode = tab;
        let data_form = new FormData();
        data_form.append("mode", tab);

        data_form.append("year", this.modals.modal_presupuesto.tab_config.data);
        data_form.append("month", this.modals.modal_presupuesto.filters.mouth);

        this.modals.modal_presupuesto.tab_modify.data = [];
        this.modals.modal_presupuesto.tab_pdvs.data = [];
        if(tab == 1)
          this.modals.modal_presupuesto.skeleton_pdv = true;
        else if(tab == 3)
          this.modals.modal_presupuesto.skeleton_modify = true;
        let rs = await fetch(
          `${this.config.url}catalogo/get_data_init_assign`,
          {
            method: "POST",
            body: data_form,
            headers: { "X-CSRF-TOKEN": this.config.token },
          }
        );
        let rd = await rs.json();
        this.modals.modal_presupuesto.skeleton_pdv = false;
        this.modals.modal_presupuesto.skeleton_modify = false;

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 202:
            switch (tab) {
              case 1:
                this.modals.modal_presupuesto.tab_pdvs.options = data.months;
                this.modals.modal_presupuesto.tab_pdvs.data = data.press;
                this.modals.modal_presupuesto.tab_pdvs.total =
                  data.totalPresuPuntos;

                if (this.modals.modal_presupuesto.filters.mouth == "") {
                  var fechaActual = new Date();

                  let option =
                    this.modals.modal_presupuesto.tab_pdvs.options.find(
                      (item) => item.id == fechaActual.getMonth() + 1
                    );
                  if (option) this.$refs.selectMonth.selectOption(option);

                  this.modals.modal_presupuesto.filters.mouth = option.id;
                }

                break;

              case 2:
                this.modals.modal_presupuesto.tab_assign.pdvs_selected = data;
                this.modals.modal_presupuesto.mode = tab;
                break;

              case 3:
                this.modals.modal_presupuesto.mode = tab;
                this.modals.modal_presupuesto.tab_modify.data = data;
                break;

              default:
                break;
            }
            break;

          default:
            break;
        }
      } catch (error) {
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    async OnClickAssignPresupuesto() {
      try 
      {
        if(this.modals.modal_presupuesto_value.day_end === "0"|| this.modals.modal_presupuesto_value.day_end >= 32)
        {
          toastr.warning(`No existe ese día para ningún mes, colocar número válido.`);
          return;
        }

        let data_form = new FormData();
        data_form.append("all_month",this.modals.modal_presupuesto_value.allMonth);
        data_form.append("id_pdv", this.modals.modal_presupuesto_value.id_pdv);
        data_form.append("month", this.modals.modal_presupuesto_value.month);
        data_form.append("year", this.modals.modal_presupuesto_value.year);
        data_form.append("value", this.modals.modal_presupuesto_value.data);
        data_form.append("day", this.modals.modal_presupuesto_value.day_end);

        loading();
        let rs = await fetch(`${this.config.url}catalogo/save_assign`, {
          method: "POST",
          body: data_form,
          headers: { "X-CSRF-TOKEN": this.config.token },
        });
        let rd = await rs.json();
        loading(false);

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 202:
            this.OnClickChangeTab(this.modals.modal_presupuesto.mode);
            this.OnClickClosePresupuestoValue();
            this.modals.modal_presupuesto.tab_pdvs.total = data.totalPresuPuntos;
            toastr.success(message);
            break;

          default:
            break;
        }
      } catch (error) {
        loading(false);
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    async GetDataArrayYears() {
      try 
      {
        this.modals.modal_presupuesto.skeleton_year = true;
        let rs = await fetch(`${this.config.url}catalogo/get_years`, {
          method: "GET",
          headers: { "X-CSRF-TOKEN": this.config.token },
        });
        let rd = await rs.json();
        this.modals.modal_presupuesto.skeleton_year = false;

        const { success, responseCode, message, data } = rd;
        switch (responseCode) {
          case 202:
            this.modals.modal_presupuesto.tab_config.options = data;

            var fechaActual = new Date();

            let option = this.modals.modal_presupuesto.tab_config.options.find(
              (item) => item.name == fechaActual.getFullYear()
            );
            if (option) this.$refs.selectYear.selectOption(option);

            modals.modal_presupuesto.tab_config.data = option.id;

            break;

          default:
            break;
        }
      } catch (error) {
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    OnChangeAssignAllMonth() 
    {
      this.modals.modal_presupuesto_value.allMonth = !this.modals.modal_presupuesto_value.allMonth;
      console.log(this.modals.modal_presupuesto_value.allMonth);
    },
OnClickReportsLink()
{
    window.location.href = `${this.config.url}catalogo/reportes`;
}
  },
};
</script>

<style scoped>
.dev_icon_i {
  font-size: 1.6rem;
  cursor: pointer;
  color: #ff5544;
}

.dev_container_icon_pres {
  display: flex;
  align-items: center;
}

.dev_percentage_kl {
  font-weight: bold;
  color: #ff5544;
  cursor: pointer;
}
.dev_table_pdvs {
  text-align: center;
}

.dev_table_months_pdvs tbody tr td:nth-child(n + 2) {
  font-weight: bold;
  color: #ff5544;
  cursor: pointer;
}

.dev-cursor-pointer::after {
  cursor: pointer;
}

.dev_no_data {
  text-align: center;
  font-weight: bold;
  margin: 0;
}
</style>

