<template>
  <div>
    <div class="container-fluid">
          <div class="page-titles row" style="display: flex;">
              <div class="col-sm-6">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item dev-cursor-pointer"><a @click.prevent.stop="OnClickRedirect">Catálogo</a></li>
                      <li class="breadcrumb-item active"><a href="#">Historial</a></li>
                    </ol>
              </div>
              
          </div>
          
          <div class="row">
              <div class="col-lg-12">
                  <div class="card">
                      <div class="card-body">
  
                          <div class="col-lg-12 d-flex justify-content-center">
                              <div class="col-sm-6">
                                  <ul class="navbar-nav header-right">
                                          <li class="nav-item">
                                          <div class="input-group search-area d-xl-inline-flex d-none col-lg-12" >
                                              <input type="text" class="form-control" placeholder="Buscar por orden de compra... (Enter)" @keyup.enter="OnKeyUpSearch()" v-model="inputSearch">
                                              <div class="input-group-append">
                                              <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                                              </div>
                                          </div>
                                          </li>
                                  </ul>
                              </div>
                          </div>
                          
                          <div class="table-responsive text-center mt-3">
                              <table class="table table-responsive-md" :class="{'skeleton-loader': skeleton_table}">
                                  <thead >
                                      <tr>
                                          <th>Fecha</th>
                                          <th>Centro de costo</th>
                                          <th>Ordén de compra</th>
                                          <th>Subtotal</th>
                                          <th>Impuesto</th>
                                          <th>Total</th>
                                          <th>Estado</th>
                                          <th>Acciones</th>
                                      </tr>
                                  </thead>
                                  <tbody >
                                        <tr v-if="this.orders == null">
                                            <td colspan="8" class="text-center"></td>
                                        </tr>

                                        <tr v-if="this.orders?.length == 0">
                                            <td colspan="8" class="text-center font-weight-bold">No hay ordenes en el historial.</td>
                                        </tr>
                                        <tr v-for="order in this.orders" :key="order.id">
                                            <td>{{ order.format_date }}</td>
                                            <td>
                                                <template v-if="order.cc_implicados == null">
                                                    {{ order.pdv }}
                                                </template>
                                                <template v-if="order.cc_implicados != null">
                                                    <li v-for="cc in order.cc_implicados" class="dev_li">
                                                        {{ cc.nombre }}
                                                    </li>
                                                </template>
                                            </td>
              
                                            <td>{{ order.orden_compra }}</td>
                                            <td>{{ $FormatCOMoney(order.subtotal) }}</td>
                                            <td>{{ $FormatCOMoney(order.impuestos) }}</td>
                                            <td>{{ $FormatCOMoney(order.total) }}</td>
                                            <td><span :class="`badge ${status[order.estado]}`">{{ order.estado_texto }}</span></td>
                                            <td>
                                                <div class="dropdown">
                                                <button type="button" class="btn btn-light  light sharp" data-toggle="dropdown">
                                                    <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" @click.prevent.stop="OnClickOpenModal(order.id)">Ver productos</a>
                                                </div>
                                                </div>
                                            </td>
                                        </tr>
                                  </tbody>
                              </table>

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
                          </div>
                      </div>
                  </div>
              </div>
  
          </div>
    </div>

    <!-- {{-- MODAL DATALLE PRODUCTOS --}} -->
    <div class="modal fade modal-products-watch" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ver detalle productos</h5>
              </div>
              <div class="modal-body">
                <div class="dev-content-products col-lg-12">
                    <div class="table-responsive text-center">
                        <table class="table table-responsive-md" :class="{'skeleton-loader': modal.skeleton_table_modal}">
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
                                <tr v-for="detail in modal.data" :key="detail.id">
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
                <button class="btn btn-default" @click.prevent.stop="OnClickCloseModal();">Cerrar</button>
              </div>
          </div>
        </div>
    </div>
    <!-- {{-- FIN - MODAL DETALLE PRODUCTOS --}} -->
  </div>
</template>

<script>
export default 
{
    async mounted()
    {
        console.log(`Init`)        
        await this.GetDataInit()
    },
    data() {
        return {
            config:
            {
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            status: ['badge-danger', 'badge-warning', 'badge-primary', 'badge-secondary', 'badge-success'],
            skeleton_table: true,
            orders: null,
            modal:
            {
                data: [],
                skeleton_table_modal: false
            },
            pagination: 
            {
                total_pages: 1,
                current_page: 1
            },
            inputSearch:""
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
        async OnKeyUpSearch()
        {
            this.GetDataInit();
        },
        OnClickRedirect()
        {
            window.location.href = `${this.config.url}catalogo`;
        },
        OnClickOpenModal(id)
        {
            $(".modal-products-watch").focus();
            this.GetDetailOrder(id);
            $(".modal-products-watch").modal("show");
        },
        OnClickCloseModal()
        {
            this.modal.data = [];
            $(".modal-products-watch").modal("hide");
        },
        async GetDetailOrder(id)
        {
            try 
            {
                let data_form = new FormData();

                data_form.append('id', id);
                
                this.modal.skeleton_table_modal = true;
                let rs = await fetch(`${this.config.url}catalogo/get_data_detail_order`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
                let rd = await rs.json();
                this.modal.skeleton_table_modal = false;
                
                const {success, responseCode, message, data} = rd;

                switch (responseCode) 
                {
                    case 202:
                        this.modal.data = data;
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

    }
};
</script>

<style>
</style>