<template>
  <div class="table-responsive">
    <div class="d-flex col-lg-12 justify-content-between">
    <div class="mb-3 row col-lg-4">
        <label for="staticEmail" class="col-form-label">
          Filtro por centro de costos:</label
        >
        <div class="col-lg-9">
          <Select_Savk
            ref="select_centro_costos"
            v-model="filter.pdv"
            :options="filter.pdvs"
            :maxItem="20"
            placeholder="Seleccione un centro de costo"
            @selected="OnSelectedCentroCostos"
            class="col-lg-3"
            :class="{ 'skeleton-loader': skeleton_table }"
          />
        </div>
      </div>
    </div>


    <table
      id="tableOrganization"
      class="table card-table display dataTablesCard"
      :class="{ 'skeleton-loader': skeleton_table }"
    >
      <thead>
        <tr class="">
          <th>
            <div class="custom-control custom-checkbox mb-3" v-show="!skeleton_table">
                <input type="checkbox" class="custom-control-input" :id="`sector_all`" @change="OnChangeAllSelectors" v-model="check_all">
                <label class="custom-control-label dev-cursor-pointer" :for="`sector_all`"></label>
            </div>
          </th>
          <th>Usuario</th>
          <th>C. costos</th>
          <th>CC. Implicados</th>
          <th>Consecutivo</th>
          <th>OC</th>
          <th>Total</th>
          <th>Fecha</th>
          <th>Observación</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="active_products.length == 0">
          <td colspan="12" class="text-center font-weight-bold">No hay pedidos solicitados.</td>
        </tr>

        <tr v-for="order in active_products">
          <td>
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input" :id="`sector_${order.id}`" v-model="order.selected">
                <label class="custom-control-label dev-cursor-pointer" :for="`sector_${order.id}`"></label>
            </div>
          </td>
          <td>{{ order.usuario_approved }}</td>
          <td>{{ order.PDV }}</td>
          <td>
            <ul>
              <li v-for="cc in order.cc_implicados" class="dev_li">
                {{ cc.nombre }}
              </li>
            </ul>
          </td>
          <td>{{ order.consecutivo }}</td>
          <td><span :class="order.path_oc == null ? '' : 'dev-font-svk'" @click="OnClickDownloadOc(order.path_oc)">{{ order.orden_compra }}</span></td>
          <td>{{ $FormatCOMoney(order.total) }}</td>
          <td>{{ order.format_date }}</td>
          <td>{{ order.obs_general }}</td>
          <td>
              <div class=" badge badge-klaxen dev-cursor-pointer ml-1" data-toggle="tooltip" data-placement="top" title="Ver productos" @click="OnClickViewProductsOpenModal(order.id)">
                <svg class="dev_icon_products dev_icon_c_white"  viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="m47.44 61.66a1 1 0 0 1 1 .91v14.37a3.06 3.06 0 0 1 -2.87 3h-20.49a3.06 3.06 0 0 1 -3-2.88v-14.38a1 1 0 0 1 .91-1h24.5zm29.51 0a1 1 0 0 1 1 .91v14.37a3.06 3.06 0 0 1 -2.87 3h-20.49a3.06 3.06 0 0 1 -3-2.88v-14.38a1 1 0 0 1 .91-1h24.5zm-37.36 4.23-.09.11-5.82 6.32-2.63-2.55a.77.77 0 0 0 -1-.08l-.09.08-1.09 1a.62.62 0 0 0 -.07.9l.07.08 3.73 3.54a1.56 1.56 0 0 0 1.08.45 1.43 1.43 0 0 0 1.09-.45l3.14-3.32.63-.67 3.14-3.31a.78.78 0 0 0 .06-.9l-.06-.08-1.09-1a.76.76 0 0 0 -1-.12zm29.51 0-.1.11-5.82 6.32-2.64-2.55a.75.75 0 0 0 -1-.08l-.09.08-1.09 1a.62.62 0 0 0 -.07.9l.07.08 3.73 3.54a1.54 1.54 0 0 0 1.08.45 1.43 1.43 0 0 0 1.09-.45l3.14-3.32.63-.67 3.14-3.31a.78.78 0 0 0 .06-.9l-.06-.08-1.07-1.01a.76.76 0 0 0 -1-.11zm-23.43-14.41a3 3 0 0 1 2.85 2.87v3.24a1 1 0 0 1 -.84 1h-26.68a1 1 0 0 1 -.94-.9v-3.16a3 3 0 0 1 2.69-3.05h23zm31.48 0a3 3 0 0 1 2.85 2.87v3.24a1 1 0 0 1 -.84 1h-26.73a1 1 0 0 1 -1-.9v-3.16a3 3 0 0 1 2.68-3.05h23zm-15-21.29a1 1 0 0 1 1 .91v14.37a3.06 3.06 0 0 1 -2.87 3.05h-20.44a3.06 3.06 0 0 1 -3.05-2.87v-14.44a1 1 0 0 1 .9-1h24.51zm-7.85 4.22-.09.08-5.82 6.32-2.59-2.56a.76.76 0 0 0 -1-.07l-.09.07-1.08 1a.61.61 0 0 0 -.07.9l.07.08 3.72 3.53a1.56 1.56 0 0 0 1.09.45 1.43 1.43 0 0 0 1.08-.45l3.14-3.31.64-.67 3.13-3.32a.78.78 0 0 0 .06-.9l-.06-.07-1.08-1a.77.77 0 0 0 -1-.08zm7.9-14.41a3.06 3.06 0 0 1 3 2.88v3.23a1 1 0 0 1 -.91 1h-28.52a1 1 0 0 1 -1-.91v-3.14a3.06 3.06 0 0 1 2.87-3h24.56z"/></svg>
              </div>
          </td>
        </tr>
      </tbody>
    </table>

    
  </div>

  <!-- {{-- MODAL DATALLE PRODUCTOS --}} -->
  <div class="modal fade" id="modal-products-watch" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Ver detalle productos</h5>
            </div>
            <div class="modal-body">
              <div class="dev-content-products col-lg-12">
                  <div class="table-responsive text-center">
                      <table class="table table-responsive-md" :class="{'skeleton-loader': modal_detail_products.skeleton_table_modal}">
                          <thead >
                              <tr>
                                  <th>Usuario</th>
                                  <th>Centro de costos</th>
                                  <th>Producto</th>
                                  <th>Extensión</th>
                                  <th>Unidad</th>
                                  <th>Valor bruto</th>
                                  <th>Cantidad</th>
                                  <th>Impuesto</th>
                                  <th>Total</th>
                                  <th>Fecha</th>
                              </tr>
                          </thead>
                          <tbody >
                              <tr v-for="detail in modal_detail_products.data" :key="detail.id">
                                  <td>{{ detail.usuario }}</td>
                                  <td>{{ detail.centro_costos }}</td>
                                  <td>{{ detail.producto }}</td>
                                  <td>{{ detail.extension }}</td>
                                  <td>{{ detail.unidad }}</td>
                                  <td>{{ $FormatCOMoney(detail.precio_unitario) }}</td>
                                  <td>{{ detail.cantidad }}</td>
                                  <td>{{ detail.impuesto }}%</td>
                                  <td>{{ $FormatCOMoney(detail.valor_total) }}</td>
                                  <td>{{ detail.format_date }}</td>
                                  <td></td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary" @click.prevent.stop="ModalProductsDetail(false,0);">Cerrar</button>
            </div>
        </div>
      </div>
  </div>
  <!-- {{-- FIN - MODAL DETALLE PRODUCTOS --}} -->

  <nav class="d-flex justify-content-end">
      <ul class="pagination pagination-gutter">
          <li class="page-item page-indicator">
              <a class="page-link" href="javascript:void(0)" @click="OnClickPreviousPage">
              <i class="la la-angle-left"></i
              ></a>
          </li>

          <li v-for="number in pagination.total_pages" :class="'page-item '+(pagination.current_page == number ? 'active' : '')">
              <a class="page-link" href="javascript:void(0)"
              @click="OnClickNumPage(number)"
              >{{ number }}</a>
          </li>

          <li class="page-item page-indicator">
              <a class="page-link" href="javascript:void(0)" @click="OnClickNextPage">
              <i class="la la-angle-right"></i
              ></a>
          </li>
      </ul>
  </nav>

  <div class="col-lg-12 d-flex justify-content-center" v-if="active_products.some(el => el.selected)">
      <button class="btn btn-primary col-lg-2 m-2" @click="OnClickToFillCar">Llenar carrito</button>
  </div>

</template>

<script>
import Select_Savk from "../../../../../../resources/js/components/pages/otros/Select_Savk.vue";

export default {
  components: 
  {
    Select_Savk
  },
  async mounted()
  {
    await this.GetDataInit();
    console.log(`init`);
  },
  created() 
  {
  },
  props: 
  {
    mode: Number
  },
  data() {
    return {
      config: {
        url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
        token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
      check_all: false,
      filter:
      {
        pdv: null,
        pdvs: [] 
      },
      modal_detail_products:
      {
          data: [],
          skeleton_table_modal: false
      },
      active_products: [],
      skeleton_table: false,
      modals:
      {
        detail_order_to_update: null,
        modal_modify_quantity: 0,
        comment: '',
        comment_obs_general: '',
        id_detail: null
      },
      totales:
      {
        total: 0
      },
      pagination: 
      {
        cant: 10,
        total_pages: 1,
        current_page: 1
      },
    };
  },
  methods: 
  {
    async GetDataInit() 
    {
      try {
        let data_form = new FormData();
        data_form.append("mode", this.mode);
        data_form.append("current_page", this.pagination.current_page);
        data_form.append("filter", (this.filter.pdv == undefined ? '' : this.filter.pdv));

        this.active_products = [];
        this.skeleton_table = true;
        let rs = await fetch(`${this.config.url}catalogo/get_data_init_active_products`, { method: "POST", body: data_form, headers: { "X-CSRF-TOKEN": this.config.token }});
        let rd = await rs.json();
        this.skeleton_table = false;

        const { success, responseCode, message, data } = rd;

        switch (responseCode) 
        {
          case 202:
            this.active_products = data.orders.map(it => {it.selected = it.selected == 1 ? true : false; return it});
            this.totales.total = data.totales;
            this.pagination.total_pages = data.per_page;
            if(this.filter.pdvs.length == 0)
            {
              this.filter.pdvs = data.pdvs.map(item => {
                return {id: item.id, name: item.nombre };
              });
              this.filter.pdvs.unshift({id: -1, name: "Todos"});
            }
            break;

          default:
            break;
        }
      } catch (error) {
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    OnClickViewProductsOpenModal(id_order)
    {
      this.ModalProductsDetail(true, id_order);
    },
    async GetDetailOrder(id)
    {
        try
        {
            let data_form = new FormData();

            data_form.append('id', id);

            this.modal_detail_products.skeleton_table_modal = true;
            let rs = await fetch(`${this.config.url}catalogo/get_data_detail_order`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
            let rd = await rs.json();
            this.modal_detail_products.skeleton_table_modal = false;

            const {success, responseCode, message, data} = rd;

            switch (responseCode)
            {
                case 202:
                    this.modal_detail_products.data = data;
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            this.skeleton_table = false;
            console.error(`Error al realizar llamado inicial: ${error.message}`);
        }
    },
    async ModalProductsDetail(show=true, id_order)
    {
      if(show)
      {
        this.GetDetailOrder(id_order);
        $("#modal-products-watch").modal("show");
      }
      else
        $("#modal-products-watch").modal("hide");
    },
    OnClickChangeQuantity(detail) 
    {
      this.modals.detail_order_to_update = detail;
      this.modals.modal_modify_quantity = detail.quantity;
      $("#modal-quantity-value").modal("show");
    },
    async OnClickDownloadOc(path)
    {
        if(path == null) return;

        let answer =  await swal({
            text: `¿Deseas descargar la orden de compra?`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, descargar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#1f3352",
            cancelButtonColor: '#ff7f00',
            allowOutsideClick: false,
        });

        if(answer.value)
        {
          try 
          {
            loading();
            let rs = await fetch(`${this.config.url}storage/${path}`, { method: "GET", headers: { 'X-CSRF-TOKEN': this.token }});
            loading(false);
            if (rs.ok) 
            {
                const blob = await rs.blob();
                const url = URL.createObjectURL(blob);

                // Crear un enlace de descarga y hacer clic en él para iniciar la descarga
                const link = document.createElement('a');
                link.href = url;
                link.download = 'orden_compra.pdf';
                link.click();

                // Limpiar la URL del objeto
                URL.revokeObjectURL(url);
            } 
            else 
            {
                loading(false);
                toastr.warning(`Error al descargar el documento, comunícate con el administrador.`)
                console.error('Error al descargar el PDF');
            }
          } 
          catch (error) 
          {
            loading(false);
            console.error(`Error al descargar el PDF: ${error.message}`);
          }

        }
    },
    async OnClickSaveChanges() 
    {
      try 
      {
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
        data_form.append("current_page", this.pagination.current_page);

        loading();
        let rs = await fetch(
          `${this.config.url}catalogo/save_quantity_detail_admin_section`,
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
            if (data.totales == null) 
            {
              this.active_products = [];
              this.totales.total = "";
              this.pagination.total_pages = 1;
            } 
            else 
            {
              this.active_products = data.detail;
              this.totales.total = data.totales;
              this.pagination.total_pages = data.per_page;
            }

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
    OnClickCloseModalQuantity() 
    {
      this.modals.modal_modify_quantity = "";
      $("#modal-quantity-value").modal("hide");
    },
    async OnClickPreviousPage()
    {
        if (this.pagination.current_page === 1) return;
        this.pagination.current_page--;
        await this.GetDataInit();
    },
    async OnClickNextPage()
    {
        if (this.pagination.current_page === this.pagination.total_pages) return;
        this.pagination.current_page++;
        await this.GetDataInit();
    },
    async OnClickNumPage(number)
    {
        if(this.pagination.current_page == number) return;
        this.pagination.current_page = number;
        await this.GetDataInit();
    },
    OnClickComment(product)
    {
      this.modals.comment = product.observacion;      
      this.modals.id_detail = product.id;      
      $("#modal-comment").modal("show");
    },
    OnClickCloseModalComment()
    {
      $("#modal-comment").modal("hide");
      this.modals.comment = "";      
    },
    OnClickCloseModalObsGeneralOrder()
    {
      $("#modal-obs-pedido").modal("hide");
      this.modals.comment_obs_general = "";      
    },
    OnClickOpenModalObsGeneralOrder()
    {
      $("#modal-obs-pedido").modal("show");
    },
    async OnClickSaveCommentDetail() 
    {
      try 
      {
        let data_form = new FormData();
        data_form.append("id_detail", this.modals.id_detail);
        data_form.append("comment", this.modals.comment);

        loading();
        let rs = await fetch(`${this.config.url}catalogo/save_comment_detail`, { method: "POST", body: data_form, headers: { "X-CSRF-TOKEN": this.config.token } });
        let rd = await rs.json();
        loading(false);

        const { success, responseCode, message, data } = rd;

        switch (responseCode) {
          case 200:
            this.active_products.map(el => {
              el.observacion = (this.modals.comment == "" ? undefined : this.modals.comment);
              return el;
            });

            this.OnClickCloseModalComment();
            break;

          default:
            break;
        }
      } catch (error) {
        loading(false);
        console.error(`Error al realizar llamado inicial: ${error.message}`);
      }
    },
    async OnClickSaveCommentObsGeneral() 
    {
      try 
      {
          let selected = this.active_products.filter(ap => ap.selected == true);
          let data_form = new FormData();
          data_form.append("items", JSON.stringify(selected));
          data_form.append("comment", this.modals.comment_obs_general);
  
          loading();
          let rs = await fetch(`${this.config.url}catalogo/to_generate_order_products_active`, { method: "POST", body: data_form, headers: { "X-CSRF-TOKEN": this.config.token }});
          let rd = await rs.json();
          loading(false);
  
          const { success, responseCode, message, data } = rd;
  
          switch (responseCode) 
          {
            case 200:
              this.check_all = false;
              toastr.success(message);
              this.OnClickCloseModalObsGeneralOrder();
              await this.GetDataInit();
            break;

            case 400:
              toastr.success(message);
            break;
  
            default:
              break;
          }
      } 
      catch (error) 
      {
          loading(false);
          console.error(`Error al generar el pedido: ${error.message}`);
      }
    },
    async OnSelectedCentroCostos(item) 
    {
      this.filter.pdv = item.id;
      if(this.filter.pdv != undefined && !this.skeleton_table)
        await this.GetDataInit();
    },
    OnChangeAllSelectors()
    {
      this.active_products = this.active_products.map(it => { it.selected = this.check_all; return it;})
    },
    async OnClickDeleteSelectedItem()
    {
      try 
      {
        let answer =  await swal({
            text: `¿Estás seguro de eliminar los items seleccionados?`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cerrar",
            confirmButtonColor: "#1f3352",
            cancelButtonColor: '#ff7f00',
            allowOutsideClick: false,
        });

        if(answer.value)
        {
          let selected = this.active_products.filter(ap => ap.selected == true);
          let data_form = new FormData();
          data_form.append("items", JSON.stringify(selected));
  
          loading();
          let rs = await fetch(`${this.config.url}catalogo/delete_products_cancel`, { method: "POST", body: data_form, headers: { "X-CSRF-TOKEN": this.config.token }});
          let rd = await rs.json();
          loading(false);
  
          const { success, responseCode, message, data } = rd;
  
          switch (responseCode) 
          {
            case 200:
              this.check_all = false;
              toastr.success(message);
              await this.GetDataInit();
              break;
  
            default:
              break;
          }
        }

      } 
      catch (error) 
      {
          loading(false);
          console.error(`Error al eliminar los items: ${error.message}`);
      }
    },
    async OnClickToGenerateSelectedItem()
    {
      this.OnClickOpenModalObsGeneralOrder();
    },
    async OnClickToFillCar()
    {
      try 
      {
        let answer =  await swal({
            text: `¿Estás seguro de llenar el carrito con los items seleccionados?`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cerrar",
            confirmButtonColor: "#1f3352",
            cancelButtonColor: '#ff7f00',
            allowOutsideClick: false,
        });

        if(answer.value)
        {
          let data_form = new FormData();
          data_form.append("items", JSON.stringify(this.active_products.filter(it => it.selected == true)));

          loading();
          let rs = await fetch(`${this.config.url}catalogo/fill_detail_items`, { method: "POST", body: data_form, headers: { "X-CSRF-TOKEN": this.config.token }});
          let rd = await rs.json();
          loading(false);

          const { responseCode, message, success, data } = rd;

          switch (responseCode)
          {
            case 200:
              this.check_all = false;
              this.active_products = this.active_products.map(it => {
                it.selected = false;
                return it;
              });
              toastr.success(message);
              break;

          case 400:
              toastr.warning(message);
              break;

          default:
              break;
          }
        }
        
      } catch (error) 
      {
        console.error(`Error OnClickFillClient: ${error.message}`);
        loading(false);
                
      }

    }
  },
  watch: {

  },
};
</script>

<style scoped>
.countPreguntas{
    padding-top: 3px;
    display: inline-block;
    width: 20px;
    height: 20px;
    color: white !important;
    position: absolute;
    top: -9px;
    border: solid 1px;
}

.countPreguntas1{
    background-color: #ff7f00;
}

.countPreguntas0{
    background-color: #002F54;
}

.nav-link.active{
    color: white !important;
    background-color: #002F54 !important;
    border-color: #002F54 !important;
}

button.nav-link{
    background-color: #e6f0ff !important;
    border-color: #e6f0ff !important;
    color: #002f54 !important;
}

.dev_quantity {
  font-weight: bold;
  color: rgb(255, 127, 0);
  cursor: pointer;
}
.dev_icon_comment
{
  fill: white;
  width: 20px;
}

.dev_li
{
  list-style-type: circle;
}

.dev-font-svk
{
  font-weight: bold;
  cursor: pointer;
  color: #ff7f00;
}

.dev_icon_products
{
  width: 30px;
  cursor: pointer;
}
</style>
