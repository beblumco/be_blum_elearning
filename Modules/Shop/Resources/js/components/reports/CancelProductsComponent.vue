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
          <th>Producto</th>
          <th>Tipo</th>
          <th>Ext</th>
          <th>Unidad</th>
          <th>V. Bruto</th>
          <th>Impuesto</th>
          <th>Cantidad</th>
          <th>Total</th>
          <th>Fecha</th>
          <th>Comentario</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="active_products.length == 0">
          <td colspan="12" class="text-center font-weight-bold">No hay pedidos cancelados.</td>
        </tr>

        <tr v-for="product in active_products">
          <td>
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input" :id="`sector_${product.id}`" v-model="product.selected">
                <label class="custom-control-label dev-cursor-pointer" :for="`sector_${product.id}`"></label>
            </div>
          </td>
          <td>{{ product.usuario }}</td>
          <td>{{ product.pdv }}</td>
          <td>{{ product.producto }}</td>
          <td>{{ product.tipo }}</td>
          <td>{{ product.ext }}</td>
          <td>{{ product.und }}</td>
          <td>{{ $FormatCOMoney(product.valor_bruto) }}</td>
          <td>{{ product.impuesto }}%</td>
          <td class="text-center">
            <span>{{ product.quantity }}</span>
          </td>
          <td>{{ $FormatCOMoney(product.total) }}</td>
          <td>{{ product.format_date }}</td>
          <td class="text-center">
            <span>{{ product.observacion }}</span>
          </td>
        </tr>
      </tbody>
    </table>

    
  </div>

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
      <!-- <button class="btn btn-danger col-lg-2 m-2" @click="OnClickDeleteSelectedItem">Eliminar seleccionados</button> -->
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
    OnClickChangeQuantity(detail) 
    {
      this.modals.detail_order_to_update = detail;
      this.modals.modal_modify_quantity = detail.quantity;
      $("#modal-quantity-value").modal("show");
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
          data_form.append("mode", 1); // PRODUCTOS CANCELADOS

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
</style>
