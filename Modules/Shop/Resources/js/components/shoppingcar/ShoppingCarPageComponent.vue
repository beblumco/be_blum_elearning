<template>
  <div class="container-fluid">
    <div class="page-titles row" style="display: flex">
      <div class="col-sm-6">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a :href="`${this.config.url}catalogo`">Catálogo</a>
          </li>
          <li class="breadcrumb-item active">
            <a href="#">Carrito</a>
          </li>
        </ol>
      </div>
    </div>

    <div class="row col-lg-12 card">
      <div class="col-lg-12 card-body">
        <div class="d-flex justify-content-end mb-3">
          <h5 v-if="totales.total_presupuesto != null" class="modal-title mr-3" :class="{ 'skeleton-loader': this.skeleton }">
            <span class="font-weight-bold" >Total presupuesto mes (<span>{{ this.totales.month }}</span>): </span>
            {{ $FormatCOMoney(totales.total_presupuesto) }}
          </h5>

          <h5 v-if="totales.total_presupuesto != null" class="modal-title mr-3" :class="{ 'skeleton-loader': this.skeleton }">
            <span class="font-weight-bold">Total consumido: </span>
            {{ $FormatCOMoney(totales.total_consumido) }}
          </h5>
        </div>
        <div class="table-responsive col-lg-12">
          <table class="table table-responsive-md">
            <thead>
              <tr>
                <th></th>
                <th>Producto</th>
                <th>Extensión</th>
                <th>Unidad</th>
                <th>Valor bruto</th>
                <th>Cantidad</th>
                <th>Impuesto</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody class="dev_skeleton" :class="{ '-loading': this.skeleton }">
              <template v-if="products === null">
                <tr>
                  <td colspan="8" class="pt-3 dev_empty"></td>
                </tr>
              </template>
              <template v-if="products?.length === 0">
                <tr>
                  <td colspan="8" class="pt-3 dev_empty">
                    No tienes productos para mostrar.
                  </td>
                </tr>
              </template>

              <tr v-for="product in products">
                <td>
                  <i
                    class="fa fa-trash-o m-1 dev_icon_table"
                    @click="OnClickDeleteDetailOrder(product.id)"
                  ></i>
                </td>
                <td>
                  <div class="dev_container_product">
                    <img
                      class="dev_img_shopping_car"
                      :class="{ '-loading': true }"
                      :src="product.image"
                      alt=""
                    />
                    <span class="ml-3">{{ product.name }}</span>
                  </div>
                </td>
                <td>{{ product.ext }}</td>
                <td>{{ product.unit }}</td>
                <td>{{ $FormatCOMoney(product.price) }}</td>
                <td>
                  <span
                    class="dev_quantity"
                    @click="OnClickChangeQuantity(product)"
                    >{{ product.cantidad }}</span
                  >
                </td>
                <td>{{ product.impuesto }}%</td>
                <td>{{ $FormatCOMoney(product.total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="col-lg-2 d-flex justify-content-start flex-wrap mt-3">
          <div class="d-flex col-lg-12 justify-content-between">
            <p class="p-0">
              <span class="font-weight-bold mr-2">Subtotal: </span>
            </p>
            <span
              class="dev_text_shopping_car"
              :class="{ '-loading': this.skeleton }"
              >{{
                totales.subtotal == "" || totales.subtotal == null
                  ? ""
                  : $FormatCOMoney(totales.subtotal)
              }}</span
            >
          </div>
          <div class="d-flex col-lg-12 justify-content-between">
            <p class="p-0"><span class="font-weight-bold mr-2">Iva: </span></p>
            <span
              class="dev_text_shopping_car"
              :class="{ '-loading': this.skeleton }"
              >{{
                totales.impuesto == "" || totales.impuesto == null
                  ? ""
                  : $FormatCOMoney(totales.impuesto)
              }}</span
            >
          </div>
          <div class="d-flex col-lg-12 justify-content-between">
            <p class="p-0">
              <span class="font-weight-bold mr-2">Total: </span>
            </p>
            <span
              class="dev_text_shopping_car"
              :class="{ '-loading': this.skeleton }"
              >{{
                totales.total == "" || totales.total == null
                  ? ""
                  : $FormatCOMoney(totales.total)
              }}</span
            >
          </div>
        </div>

        <div
          class="col-lg-12 d-flex justify-content-center mt-4"
          v-if="this.products != null && this.products?.length != 0"
        >
          <div class="col-lg-4 d-flex justify-content-center">
            <button class="btn btn-secondary" @click="OnClickClearCar()">
              Limpiar carrito
            </button>
          </div>

          <div class="col-lg-4 d-flex justify-content-center">
            <button
              :disabled="!visibleActions"
              class="btn btn-primary"
              @click="OnClickOpenObs(1)"
            >
              Enviar para aprobación
            </button>
          </div>

          <div
            class="col-lg-4 d-flex justify-content-center"
            v-if="this.has_approve == 1"
          >
            <button
              :disabled="!visibleActions"
              class="btn btn-success"
              @click="OnClickOpenObs(2)"
            >
              Enviar pedido
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL PRESUPUESTO VALUE -->
    <div
      class="modal fade"
      id="modal-presupuesto-value"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modificar presupuesto</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="col-sm-12">
              <div class="form-group">
                <input
                  type="text"
                  class="form-control input-number"
                  placeholder="$00.000"
                />
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-primary"
              @click.stop.prevent="OnClickClosePresupuestoValue()"
            >
              Guardar cambios
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click.stop.prevent="OnClickClosePresupuestoValue()"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- MODAL PRESUPUESTO VALUE -END -->

    <!-- MODAL CAMBIO DE CANTIDAD -->
    <div
      class="modal fade"
      id="modal-quantity-value"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modificar cantidad del producto</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              @click="OnClickCloseModalQuantity()"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="col-sm-12">
              <div class="form-group">
                <input
                  type="text"
                  class="form-control input-number"
                  placeholder="0"
                  v-model="modals.modal_modify_quantity"
                />
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-primary"
              @click.stop.prevent="OnClickSaveChanges()"
            >
              Guardar cambios
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click.stop.prevent="OnClickCloseModalQuantity()"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--END -  MODAL CAMBIO DE CANTIDAD -->

    <!-- MODAL AGREGAR COMENTARIO -->
    <div
      class="modal fade"
      id="modal-obs-pedido"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Agrega una observación (pedido)</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              @click="OnClickCloseModalObs()"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="col-sm-12">
              <div class="form-group">
                <textarea
                  class="form-control"
                  placeholder="Agrega tu observación..."
                  style="resize: none"
                  rows="5"
                  v-model="modals.modal_add_comment"
                >
                </textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn" :class="content_section.form_data.pdf.value == '' ? 'btn-primary' : 'btn-danger'" @click="OnClickAddContent">Subir orden de compra</button>
            <button
              type="button"
              class="btn"
              :class="{
                'btn-success': modals.modal_add_comment_mode == 2,
                'btn-primary': modals.modal_add_comment_mode == 1,
              }"
              @click.stop.prevent="OnClickSaveChangesObs()"
            >
              {{
                modals.modal_add_comment_mode == 1
                  ? "Enviar aprobación"
                  : "Enviar pedido"
              }}
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click.stop.prevent="OnClickCloseModalObs()"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--END -  MODAL AGREGAR COMENTARIO -->

    <!-- MODAL ADJUNTAR PDF -->
    <div class="modal fade" id="modal_add_content_pdf" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar orden de compra</h5>
                </div>
                <div class="modal-body">
                    <div class="col-lg-12 d-flex justify-content-center">
                        <div class="form-group col-md-6">
                            <label> Adjuntar pdf: <span class="dev-required">{{ (content_section.form_data.pdf.required ? '*' : '') }}</span></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" accept="application/pdf" @change="OnChangeFileContentPDF" class="custom-file-input" ref="file_pdf_content">
                                    <label class="custom-file-label">{{ content_section.default.label_file }}</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" @click="OnClickSaveContentPDF">Guardar cambios</button>
                  <button type="button" class="btn btn-danger light" @click="OnClickCloseModalContentPDF">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL ADJUNTAR PDF - END-->

  </div>
</template>

<script>
export default {
  props: {
    has_approve: String,
  },
  components: {},
  created() {},
  async mounted() {
    $('[data-toggle="tooltip"]').tooltip();
    await this.GetDataInit();
  },
  watch: {
    totales: {
      handler(val) {
        // if (
        //   val.total_presupuesto != null &&
        //   parseInt(val.total) > parseInt(val.total_presupuesto)
        // ) {
        //   this.visibleActions = false;
        // } else {
        //   this.visibleActions = true;
        // }
      },
      deep: true,
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
      skeleton: false,
      modals: {
        modal_modify_quantity: "",
        detail_order_to_update: null,
        modal_add_comment: "",
        modal_add_comment_mode: 1,
      },
      products: null,
      totales: {
        impuesto: "",
        subtotal: "",
        total: "",
        total_presupuesto: "",
        total_consumido: "",
        month: ""
      },
      visibleActions: true,
      content_section:
      {
        type: 0,
        mode: 1, //1: AGREGAR; 2: ACTUALIZAR
        default:
        {
            label_file: "Seleccionar un archivo"
        },
        form_data:
        {
            pdf:
            {
                value: "",
                required: true
            },
            ClearData()
            {
                Object.entries(this).forEach(object => {
                    const [key, value] = object;
                    if (typeof(value) != 'function')
                        this[key].value = "";
                });
            },
            ValidData()
            {
                let allow = true;
                Object.entries(this).forEach(object => {
                    const [key, value] = object;
                    if (typeof(value) != 'function') {
                        if (this[key].required) {
                            if (this[key].value == "")
                                allow = false;
                        }
                    }
                });

                return allow;
            }
        },
      }
    };
  },
  methods: {
    ValidConsu()
    {
        if (
          this.totales.total_presupuesto != null &&
          parseInt(this.totales.total_consumido) > parseInt(this.totales.total_presupuesto)
        ) 
          this.visibleActions = false;
        else 
          this.visibleActions = true;
    },
    async GetDataInit() {
      try {
        let data_form = new FormData();

        this.skeleton = true;
        let rs = await fetch(`${this.config.url}catalogo/get_data_init_car`, {
          method: "POST",
          body: data_form,
          headers: { "X-CSRF-TOKEN": this.config.token },
        });
        let rd = await rs.json();
        this.skeleton = false;

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 202:
            let items_locales = 0;
            if (data.totales == null) {
              this.products = [];
              this.totales.impuesto = "";
              this.totales.subtotal = "";
              this.totales.total = "";
              this.totales.month = "";
            } else {
              this.products = data.detail;
              this.totales.impuesto = data.totales.IVA;
              this.totales.subtotal = data.totales.SUBTOTAL;
              this.totales.total = data.totales.TOTAL;
              items_locales = parseInt((data.totales.TOTAL == undefined ? 0 : data.totales.TOTAL));
              this.totales.month = data.month;
            }

            this.totales.total_presupuesto = data.presupuesto;
            this.totales.total_consumido = parseInt((data.consumido == undefined ? 0 : data.consumido))+items_locales;
            this.ValidConsu();

            break;

          default:
            break;
        }
      } catch (error) {
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    OnClickChangeQuantity(detail) {
      this.modals.detail_order_to_update = detail;
      this.modals.modal_modify_quantity = detail.cantidad;
      $("#modal-quantity-value").modal("show");
    },
    OnClickCloseModalQuantity() {
      this.modals.modal_modify_quantity = "";
      $("#modal-quantity-value").modal("hide");
    },
    async OnClickSaveChanges() {
      try {
        if (
          this.modals.modal_modify_quantity == 0 ||
          this.modals.modal_modify_quantity == ""
        ) {
          await swal({
            text: `Debes agregar una cantidad y que esta sea diferente a 0.`,
            type: "warning",
            showCancelButton: false,
            confirmButtonText: "Aceptar",
            // cancelButtonText: "Cerrar",
            confirmButtonColor: "#1f3352",
            // cancelButtonColor: '#ff7f00',
            allowOutsideClick: false,
          });
          return;
        }
        let data_form = new FormData();
        data_form.append("id_detail", this.modals.detail_order_to_update.id);
        data_form.append("quantity", this.modals.modal_modify_quantity);

        loading();
        let rs = await fetch(
          `${this.config.url}catalogo/save_quantity_detail_product`,
          {
            method: "POST",
            body: data_form,
            headers: { "X-CSRF-TOKEN": this.config.token },
          }
        );
        let rd = await rs.json();
        loading(false);

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 200:
            let items_locales = 0;
            if (data.totales == null) {
              this.products = [];
              this.totales.impuesto = "";
              this.totales.subtotal = "";
              this.totales.total = "";
            } else {
              this.products = data.detail;
              this.totales.impuesto = data.totales.IVA;
              this.totales.subtotal = data.totales.SUBTOTAL;
              this.totales.total = data.totales.TOTAL;
              items_locales = parseInt((data.totales.TOTAL == undefined ? 0 : data.totales.TOTAL));
            }

            this.totales.total_presupuesto = data.presupuesto;
            this.totales.total_consumido = parseInt((data.consumido == undefined ? 0 : data.consumido))+items_locales;

            this.ValidConsu();
            this.OnClickCloseModalQuantity();
            break;

          default:
            break;
        }
      } catch (error) {
        loading(false);
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    async OnClickDeleteDetailOrder(id_detail) {
      let answer = await swal({
        text: `¿Desea eliminar este producto del carrito de compras?`,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No, cancelar",
        confirmButtonColor: "#1f3352",
        cancelButtonColor: "#ff7f00",
        allowOutsideClick: false,
      });

      if (answer.value) {
        try {
          let data_form = new FormData();
          data_form.append("id_detail", id_detail);

          loading();
          let rs = await fetch(
            `${this.config.url}catalogo/delete_detail_product`,
            {
              method: "POST",
              body: data_form,
              headers: { "X-CSRF-TOKEN": this.config.token },
            }
          );
          let rd = await rs.json();
          loading(false);

          const { success, responseCode, message, data } = rd;

          switch (responseCode) {
            case 200:
              toastr.success(message);
              if (data.totales == null) {
                this.products = [];
                this.totales.impuesto = "";
                this.totales.subtotal = "";
                this.totales.total = "";
              } else {
                this.products = data.detail;
                this.totales.impuesto = data.totales.IVA;
                this.totales.subtotal = data.totales.SUBTOTAL;
                this.totales.total = data.totales.TOTAL;
              }

              this.totales.total_presupuesto = data.presupuesto;
              this.totales.total_consumido = data.consumido;
              break;

            default:
              break;
          }
        } catch (error) {
          loading(false);
          console.error(`Error al realizar llamado inicial: ${error.message}`);
        }
      } else {
      }
    },
    async OnClickClearCar() {
      let answer = await swal({
        text: `¿Está seguro de vaciar el carrito de compras?`,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, vaciar",
        cancelButtonText: "No, cancelar",
        confirmButtonColor: "#1f3352",
        cancelButtonColor: "#ff7f00",
        allowOutsideClick: false,
      });

      if (answer.value) {
        try {
          let data_form = new FormData();

          loading();
          let rs = await fetch(`${this.config.url}catalogo/clear_car_order`, {
            method: "POST",
            body: data_form,
            headers: { "X-CSRF-TOKEN": this.config.token },
          });
          let rd = await rs.json();
          loading(false);

          const { success, responseCode, message, data } = rd;

          switch (responseCode) {
            case 200:
              toastr.success(message);
              this.products = [];
              this.totales.impuesto = "";
              this.totales.subtotal = "";
              this.totales.total = "";
              break;

            default:
              break;
          }
        } catch (error) {
          loading(false);
          console.error(
            `Error al realizar llamado clear car: ${error.message}`
          );
        }
      }
    },
    async OnClickOpenObs(mode) {
      this.modals.modal_add_comment_mode = mode;
      if(mode == 2)
        $("#modal-obs-pedido").modal("show");
      else
      {

        let answer =  await swal({
            text: `¿Estás seguro de enviar el pedido para aprobación?`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, enviar",
            cancelButtonText: "Cerrar",
            confirmButtonColor: "#1f3352",
            cancelButtonColor: '#ff7f00',
            allowOutsideClick: false,
        });

        if(answer.value)
          await this.OnClickSaveChangesObs();
      }
    },
    OnClickCloseModalObs() {
      $("#modal-obs-pedido").modal("hide");
      this.OnClickCloseModalContentPDF();
      this.modals.modal_add_comment = "";
    },
    async OnClickSaveChangesObs() 
    {
      try {
        let data_form = new FormData();
        data_form.append("obs", this.modals.modal_add_comment);
        data_form.append("mode", this.modals.modal_add_comment_mode);

        Object.entries(this.content_section.form_data).forEach(
            field => {
                const [key, object] = field;
                if (typeof(object) != 'function')
                    data_form.append(key, object.value);
            }
        );

        loading();
        let rs = await fetch(`${this.config.url}catalogo/save_obs_pedido`, {
          method: "POST",
          body: data_form,
          headers: { "X-CSRF-TOKEN": this.config.token },
        });
        let rd = await rs.json();
        loading(false);

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 200:
            toastr.success(message);
            this.products = [];
            this.totales.impuesto = "";
            this.totales.subtotal = "";
            this.totales.total = "";
            this.OnClickCloseModalObs();
            break;

          default:
            break;
        }
      } catch (error) {
        loading(false);
        console.error(`Error al realizar llamado clear car: ${error.message}`);
      }
    },
    async OnClickAddContent()
    {
      this.content_section.type = 2;
      this.content_section.mode = 1;
      $('#modal_add_content_pdf').modal('show');
    },
    OnChangeFileContentPDF(file)
    {
        console.log(file);
        if(file != undefined)
        {
            this.content_section.default.label_file = "1 documento cargado";
            this.content_section.form_data.pdf.value = file.target.files[0];
        }
        else
        {
            this.content_section.default.label_file = "Seleccionar un archivo";
            this.content_section.form_data.pdf.value = "";
        }
    },
    async OnClickSaveContentPDF()
    {
        try
        {
            if(!this.content_section.form_data.ValidData())
            {
                await swal({
                    title: "Completa todos los datos",
                    text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return;
            }

          $("#modal_add_content_pdf").modal('hide');
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al agregar contenido: ${error.message}`);
        }
    },
    OnClickCloseModalContentPDF()
    {
        this.$refs.file_pdf_content.value = '';
        this.content_section.form_data.ClearData();
        this.content_section.default.label_file = "Seleccionar un archivo"
        $("#modal_add_content_pdf").modal('hide');
    },
  },
};
</script>

<style scoped>
.breadcrumb {
  background-color: transparent;
}
.page-titles .breadcrumb li.active a {
  color: #fe634e;
  font-weight: 600;
}

table tbody tr td,
table thead tr th {
  text-align: center;
}

.dev_empty {
  font-weight: 600;
}
.dev_img_shopping_car {
  width: 5rem;
  min-height: 5rem;
  object-fit: cover;
}

.dev_text_shopping_car {
  width: 5rem;
  cursor: pointer;
  max-height: 25px;
  border-radius: 15px;
  padding: 3px;
}

.dev_container_product {
  display: flex;
  justify-content: flex-start;
}
.dev_skeleton {
  min-width: 100%;
  min-height: 25vh;
}
.dev_container_product > span {
  align-items: center;
  display: flex;
}
.dev_img_shopping_car.-loading,
.dev_text_shopping_car.-loading,
.dev_skeleton.-loading {
  background: #e9edf1;
  border: none;
  background: linear-gradient(90deg, #e9edf1 7%, #eff2f4 12%, #e9edf1 37%);
  background-size: 200% 100%;
  -webkit-animation: 1.5s shimmer linear infinite;
  animation: 1.5s shimmer linear infinite;
}

@-webkit-keyframes shimmer {
  to {
    background-position-x: -200%;
  }
}
@keyframes shimmer {
  to {
    background-position-x: -200%;
  }
}

.dev_icon_table {
  font-size: 25px;
  color: rgb(255, 127, 0);
  cursor: pointer;
}

.dev_quantity {
  font-weight: bold;
  color: rgb(255, 127, 0);
  cursor: pointer;
}

button:disabled {
  cursor: not-allowed; /* Cursor cuando el botón está deshabilitado */
}

button:hover:disabled {
  cursor: not-allowed; /* Cursor cuando el botón está deshabilitado y el puntero está sobre él */
}
</style>
