  <template>
  <div class="container-fluid">
    <div class="page-titles row" style="display: flex;">
        <div class="col-sm-6">
              <ol class="breadcrumb">
                <li class="breadcrumb-item dev-cursor-pointer"><a @click.prevent.stop="OnClickRedirect">Catálogo</a></li>
                <li class="breadcrumb-item active"><a href="#">Administración</a></li>
              </ol>
        </div>
        
    </div>
    <div class="flex-wrap mb-2 align-items-center justify-content-between">
      <div class="row menu-cap">
        <div class="container_bnts d-flex" id="container_bnts">
          <div class="btn-menu">
            <button
              @click="OnChangeMode(1)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 1 }"
            >
             Productos activos 
            </button>
          </div>
          <div class="btn-menu">
            <button
              @click="OnChangeMode(2)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 2 }"
            >
              Productos cancelados
            </button>
          </div>
          <div class="btn-menu">
            <button
              @click="OnChangeMode(3)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 3 }"
            >
              Pedidos solicitados (Administrador)
            </button>
          </div>
          <div class="btn-menu">
            <button
              @click="OnChangeMode(4)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 4 }"
            >
              Pedidos cancelados (tienda) 
            </button>
          </div>

          <div class="btn-menu">
            <button
              @click="OnChangeMode(5)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 5 }"
            >
              Pedidos despachados (tienda)
            </button>
          </div>
            
          <!-- <div class="btn-menu">
            <button
              @click="OnChangeMode(6)"
              class="btn btn-barra"
              :class="{ 'btn-barra-activo': mode == 6 }"
            >
              Reportes
            </button>
          </div> -->
        </div>
        
      </div>
    </div>

    <div class="row">
      <div class="col-xl-12">
        <div class="tab-content">

          <!-- PRODUCTOS ACTIVOS -->
          <div v-if="mode == 1">
            <ActiveProductsComponent :mode="mode" />            
          </div>

          <!-- PRODUCTOS CANCELADOS -->
          <div v-if="mode == 2">
            <CancelProductsComponent :mode="mode" />            
          </div>

          <!-- PEDIDOS SOLICITADOS -->
          <div v-if="mode == 3">
            <OrderAdminProductsComponent :mode="mode" />            
          </div>

          <!-- PEDIDOS CANCELADOS -->
          <div v-if="mode == 4">
            <CancelOrdersFacComponent :mode="mode" />            
          </div>

          <!-- PEDIDOS DESPACHADOS -->
          <div v-if="mode == 5">
            <OrdersDespComponent :mode="mode" />            
          </div>

          <!-- REPORTES -->
          <div v-if="mode == 6">

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ActiveProductsComponent from "./ActiveProductsComponent.vue";
import CancelProductsComponent from "./CancelProductsComponent.vue";
import OrderAdminProductsComponent from "./OrderAdminProductsComponent.vue";
import CancelOrdersFacComponent from "./CancelOrdersFacComponent.vue";
import OrdersDespComponent from "./OrdersDespComponent.vue";
export default 
{
    components:
    {
        ActiveProductsComponent,
        CancelProductsComponent,
        OrderAdminProductsComponent,
        CancelOrdersFacComponent,
        OrdersDespComponent
    },
    async mounted()
    {
        console.log(`Init`)        
        // await this.GetDataInit()
    },
    data() {
        return {
            config:
            {
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            mode: 1
        };
    },
    methods: 
    {
        async GetDataInit()
        {
            try 
            {
                let data_form = new FormData();
                data_form.append('search', this.inputSearch);
                data_form.append('current_page', this.pagination.current_page);
                
                this.skeleton_table = true;
                let rs = await fetch(`${this.config.url}catalogo/get_data_init_historical`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
                let rd = await rs.json();
                this.skeleton_table = false;
                
                const {success, responseCode, message, data} = rd;

                switch (responseCode) 
                {
                    case 202:
                        this.orders = data.data;
                        this.pagination.total_pages = data.per_page;
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
        OnClickRedirect()
        {
            window.location.href = `${this.config.url}catalogo`;
        },
        OnChangeMode(number)
        {
            this.mode = number;
        }
    }
};
</script>

<style>
</style>