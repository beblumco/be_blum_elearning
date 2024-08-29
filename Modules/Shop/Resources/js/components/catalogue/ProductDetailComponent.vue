<template>
   <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6">
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade show active" id="first">
                                        <img class="img-fluid dev_skeleton" :class="{'-loading': this.skeleton_detail}" :src="this.data_product.image" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-9 col-lg-6  col-md-6 col-xxl-7 col-sm-12">
                                <div class="product-detail-content">

                                    <div class="new-arrival-content pr">
                                        <h3 class="mb-3 dev_skeleton_text" :class="{'-loading': this.skeleton_detail}">{{ this.data_product.nombre }}</h3>
                                        <p class="item-select">Categoria: <span class="item dev_skeleton_text" :class="{'-loading': this.skeleton_detail}">{{ this.data_product.category }}</span>
                                        </p>
                                        <p class="item-select">Referencia: <span class="item dev_skeleton_text" :class="{'-loading': this.skeleton_detail}">{{ this.data_product.referencia }}</span>
                                        </p>
                                        <p class="item-select">Cantidad: <span class="item">
                                                <input type="text" class="form-control col-lg-4 dev_input_quantity" placeholder="..." v-model="this.data_product.cantidad" @input="$OnlyNumbers($event.target)"/>
                                            </span></p>
                                        <p class="text-content dev_skeleton_text" :class="{'-loading': this.skeleton_detail}">{{ this.data_product.descripcion }}</p>

                                        <p class="d-flex col-lg-12 p-0 mt-3" v-if="this.data_product.extensions.length != 0"> 
                                            <span class="col-lg-2 m-0 p-0">Extensión:</span>
                                            <span class="col-lg-3 d-flex m-0 p-0">
                                                <select class="form-control default-select p-0" v-model="extension_select">
                                                    <template v-for="(ext, index) in this.data_product.extensions" :key="ext.id">
                                                        <option :value="ext.id">{{ ext.nombre }}</option>
                                                    </template>
                                                </select>
                                            </span>
                                        </p>
                                        <div class="shopping-cart">
                                            <p class="price float-left d-block dev_skeleton_text" :class="{'-loading': this.skeleton_detail}">{{ (this.data_product.price == "" ? this.data_product.price : $FormatCOMoney(this.data_product.price))  }}</p>

                                            <a class="btn btn-primary btn-sm" href="javascript:void(0)" @click="OnClickAddProductCar()">
                                                <img :src="this.config.url+'img/shop_module/shopping-cart-solid.svg'" alt="cart">
                                                Agregar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a :class="'nav-link '+(this.opc == 1 ? 'active' : '')" data-toggle="tab" href="#home">
                                        <img :src="this.config.url+'img/shop_module/file-alt.svg'" class="tab-icon-svg">
                                        Doc. Técnica
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a :class="'nav-link '+(this.opc == 3 ? 'active' : '')" data-toggle="tab" href="#profile">
                                        <img :src="this.config.url+'img/shop_module/history-solid.svg'" class="tab-icon-svg">
                                        Historial Compras
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a :class="'nav-link '+(this.opc == 2 ? 'active' : '')" data-toggle="tab" href="#contact">
                                        <img :src="this.config.url+'img/shop_module/file-video.svg'" class="tab-icon-svg">
                                        Video
                                    </a>
                                </li> -->
                            </ul>
                            <div class="tab-content">
                                <!-- {{-- DOCUMENTACIÓN TÉCNICA --}} -->
                                <div :class="'tab-pane fade '+(this.opc == 1 ? 'show active' : '')" id="home" role="tabpanel">
                                    <div class="pt-4">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>NOMBRE</th>
                                                        <th>ACCIONES</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="dev_skeleton" :class="{'-loading': this.skeleton_detail}">
                                                    <tr v-for="documentation in this.data_product.documentacion">
                                                        <td>
                                                            <img :src="this.config.url+'img/shop_module/file-pdf.svg'" class="icon-file-table-svg">
                                                             {{ documentation.nombre }}
                                                        </td>
                                                        <td class="text-center">
                                                            <img :src="this.config.url+'img/shop_module/cloud-download-alt-solid.svg'" class="icon-table-svg" data-toggle="tooltip" data-placement="top" title="Descargar" @click.prevent='OnClickDownloadDocumentation(documentation)'>
                                                            <img :src="this.config.url+'img/shop_module/share-alt-square-solid.svg'" class="icon-table-svg" data-toggle="tooltip" data-placement="top" title="Compartir" @click.prevent="OnClickSharePdf(documentation)">
                                                        </td>
                                                    </tr>

                                                    <tr v-if="this.data_product.documentacion == null">
                                                        <td colspan="2" class="text-center"></td>
                                                    </tr>

                                                    <tr v-if="this.data_product.documentacion?.length == 0">
                                                        <td colspan="2" class="text-center">Este producto no tiene documentación.</td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- {{-- FIN - DOCUMENTACIÓN TÉCNICA --}} -->

                                <!-- {{-- HISTORIAL DE COMPRAS --}} -->
                                <div :class="'tab-pane fade' + (this.opc == 3 ? 'show active' : '')" id="profile">
                                    <div class="col-lg-12 d-flex justify-content-end">
                                        <!-- <img :src="this.config.url+'img/shop_module/cloud-download-alt-solid.svg'" class="dev-icon-table-svg" data-toggle="tooltip" data-placement="top" title="" data-original-title="Descargar excel"> -->
                                    </div>
                                    <div class="pt-2">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Centro de costo</th>
                                                        <th>Fecha</th>
                                                        <th>Orden de compra</th>
                                                        <th>Usuario</th>
                                                        <th>Valor unitario</th>
                                                        <th>Cantidad</th>
                                                        <th>Impuesto (Valor)</th>
                                                        <th>Valor total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="dev_skeleton" :class="{'-loading': this.skeleton_detail}">
                                                    <tr v-for="history in this.data_product.historial.data">
                                                        <td>{{ history.pdv }}</td>
                                                        <td>{{ history.format_date }}</td>
                                                        <td>{{ history.orden_compra }}</td>
                                                        <td>{{ history.usuario }}</td>
                                                        <td>{{ $FormatCOMoney(history.valor_bruto)  }}</td>
                                                        <td>{{ history.quantity }}</td>
                                                        <td>{{ $FormatCOMoney(history.impuesto_calc) }}</td>
                                                        <td>{{ $FormatCOMoney(history.total) }}</td>
                                                    </tr>

                                                    <tr v-if="this.data_product.historial.data == null">
                                                        <td colspan="8"></td>
                                                    </tr>
                                                    <tr v-if="this.data_product.historial?.data.length == 0">
                                                        <td colspan="8" class="text-center">No se han realizado compras.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- {{-- FIN - HISTORIAL DE COMPRAS --}} -->

                                <!-- {{-- VIDEO --}} -->
                                <div :class="'tab-pane fade '+(this.opc == 2 ? 'show active' : '')" id="contact">
                                    <div class="tab-pane" id="home" role="tabpanel">
                                        <div class="pt-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="event-bx owl-carousel">

                                                        <div class="items" style="max-width: 400px;">
                                                            <div class="image-bx">
                                                                <div class="container-video-details">
                                                                    <i class="fa fa-solid fa-play dev-play-btn-video" onclick="OnClickWatchVideo(1)"></i>
                                                                    <img src="https://img.youtube.com/vi/qMUYu8yFQc4/0.jpg" alt="">
                                                                </div>                                                                
                                                                <div class="info">
                                                                    <p class="fs-18 font-w600">
                                                                        <a href="event-detail.html"
                                                                            class="text-black">
                                                                            Aplicación del insumo químico
                                                                        </a>
                                                                    </p>
                                                                    <p class="fs-12">
                                                                        En este video podrás encontrar el equipo adecuado para la aplicación del insumo.
                                                                        </p>
                                                                    <ul>
                                                                        <li><i class="las la-user"></i>Experto LDI</li>
                                                                        <li><i class="las la-calendar"></i>Noviembre 01, 2021
                                                                        </li>
                                                                        <li><i class="fa fa-ticket"></i>561 vistas</li>
                                                                        <li><i class="las la-clock"></i>08:35 AM</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- {{-- FIN - VIDEO --}} -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- {{-- MODAL VIDEO --}} -->
        <div class="modal fade modal-video-watch" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ver video</h5>
                </div>
                <div class="modal-body">
                    <div class="dev-content-video">
                        <iframe id="dev-video-frame" width="560" height="315" src="https://www.youtube.com/embed/2b6pibWo7-c" title="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" onclick="OnClickCloseModalVideo();">Cerrar</button>
                </div>
            </div>
            </div>
        </div>
        <!-- {{-- FIN - MODAL VIDEO --}} -->
   </div> 
</template>

<script>

export default {
    props:
    {
        id_product: String,
        opc: String        
    },
    created()
    {
    },
    async mounted()
    {
        await this.GetDataInit();
    },
    data()
    {
        return {
            config:
            {
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            skeleton_detail: true,
            data_product: 
            {
                id: "",
                category: "",
                descripcion: "",
                estado: "",
                id_categoria: "",
                id_encrypt: "",
                id_etiqueta: "",
                id_linea: "",
                id_unidades_empaque: "",
                image: "",
                imagen: "",
                impuesto: "",
                nombre: "",
                price: "",
                referencia: "",
                documentacion: null,
                historial: {data: []},
                extensions: [],
                cantidad: 1
            },
            extension_select: null
        }
    },
    methods:
    {
        async GetDataInit()
        {
            try 
            {
                let data_form = new FormData();
                data_form.append('id_product', this.id_product);
                
                this.skeleton_detail = true;
                let rs = await fetch(`${this.config.url}catalogo/get_data_product_detail`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
                let rd = await rs.json();
                this.skeleton_detail = false;
                
                const {success, responseCode, message, data} = rd;

                switch (responseCode) 
                {
                    case 202:
                        this.data_product = {...data};

                        if(this.data_product.extensions.length != 0)
                            this.extension_select = this.data_product.extensions[0].id;
                        break;
                
                    default:
                        break;
                }
            } 
            catch (error) 
            {
                console.error(`Error al realizar llamado inicial: ${error.message}`);
            }
        },
        async OnClickAddProductCar()
        {
            if(this.data_product.cantidad == "" || this.data_product.cantidad == 0)
            {
                toastr.warning(`Debes agregar almenos 1 cantidad.`);
                return;
            }

            try 
            {
                let data_form = new FormData();                
                data_form.append('id_product', this.data_product.id);
                data_form.append('quantity', this.data_product.cantidad);
                data_form.append('ext', this.extension_select);
                
                loading();
                let rs = await fetch(`${this.config.url}catalogo/add_product_to_car`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
                let rd = await rs.json();
                loading(false);
                
                const {success, responseCode, message, data} = rd;

                switch (responseCode) 
                {
                    case 200:
                        toastr.success(message);
                        this.data_product.cantidad = 1;
                        // let total_quantity = 0;
                        // if(data.length != 0)
                        //      total_quantity = data.reduce((acumulador, registro) => acumulador + registro.cantidad, 0);

                        // this.$emit('update-car', total_quantity);
                        break;

                    case 400:
                        toastr.warning(message);
                        break;
                
                    default:
                        break;
                }
            } 
            catch (error) 
            {
                console.error(`Error al realizar OnClickAddProductCar: ${error.message}`);
            }
        },
        async OnClickDownloadDocumentation(documentation)
        {
            try
            {   
                documentation.doc_pdf = documentation.doc_pdf.replace('public','storage');
                let data_form = new FormData();
                data_form.append('url', documentation.doc_pdf);

                let rs = await fetch(`${this.config.url}catalogo/download_doc_product`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
                let rd = await rs.blob();
                if(rs.status == 200)
                {
                    let file_url = URL.createObjectURL(rd);
                    let a = document.createElement("a");
                    a.href = file_url;
                    a.download = `${documentation.nombre}`;
                    a.target = "_blank";
                    a.click();
                    a.remove();
                }
                else
                    console.log("Error al desccargar !");
            }
            catch(error)
            {
                console.error(`Error para descargar archivo: ${error.message}`);
            }
        },
        OnClickSharePdf(documentation)
        {
            documentation.doc_pdf = documentation.doc_pdf.replace('public','storage');
            let url_transform = documentation.doc_pdf+"#toolbar=0";

            var c = document.createElement("textarea");
            c.value = url_transform;
            c.style.maxWidth = "0px";
            c.style.maxHeight = "0px";
            document.body.appendChild(c);

            c.focus();
            c.select();
            document.execCommand("copy");
            document.body.removeChild(c);

            toastr.success("Link para compartir copiado.");
        }

    }
}
</script>

<style scoped>
.dev_input_quantity
{
    height: auto;
    margin-left: 10px;
}
.shopping-cart > a > img {
    width: 20px;
    filter: invert(100%);
}

.dev_skeleton
{
    min-width: 100%;
    min-height: 25vh;
}
.dev_skeleton_text
{
    min-width: 30%;
    min-height: 1.2rem;
}
.dev_skeleton.-loading, .dev_skeleton_text.-loading
{
  background: #e9edf1;
  border: none;
  background: linear-gradient(90deg, #e9edf1 7%, #eff2f4 12%, #e9edf1 37%);
  border-radius: 6px;
  background-size: 200% 100%;
  -webkit-animation: 1.5s shimmer linear infinite;
          animation: 1.5s shimmer linear infinite;
}

.default-select
{
    height: auto;
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
</style>