<template>
    <div class="card">
        <div class="card-body">
            <div class="row m-b-30">
                <div class="col-md-5 col-xxl-12 container-p">
                    <div class="new-arrival-product mb-4 mb-xxl-4 mb-md-0 text-center content-a">
                        <div class="content-overlay"></div>
                        <div class="content-details fadeIn-bottom">
                            <p> {{ this.product.descripcion }} </p>
                        </div>
                        <img class="img-fluid content-image" :src="this.product.image" alt="Producto" />
                    </div>
                </div>
                <div class="col-md-7 col-xxl-12">
                    <div class="new-arrival-content position-relative content-product-list">
                        <h4 class="mb-3"><a href="" @click.stop.prevent="OnClickDocumentationTec(this.product.id_encrypt)">{{ this.product.nombre }}</a></h4>
                        <p>Categoria: <span class="item"> {{ this.product.category }} </span></p>
                        <p>Referencia: <span class="item">{{ this.product.referencia }}</span> </p>
                        <p class="item-select d-flex">Cantidad: <span class="item">
                                <input type="text" v-model="this.quantity" class="form-control col-lg-4 dev_input_quantity" placeholder="0" @input="$OnlyNumbers($event.target)"/>
                            </span></p>

                        <p class="d-flex col-lg-12 p-0" v-if="this.product.extensions.length != 0"> 
                            <span class="col-lg-5 m-0 p-0">Extensión:</span>
                            <span class="col-lg-7 d-flex m-0 p-0">
                                <select class="form-control default-select p-0" v-model="extension_select">
                                    <template v-for="(ext, index) in this.product.extensions" :key="ext.id">
                                        <option :value="ext.id">{{ ext.nombre }}</option>
                                    </template>
                                </select>
                            </span>
                        </p>
                        <p class="item-select">
                            <a href=""><img src="img/shop_module/file-alt.svg" class="product-list-icon"
                                data-toggle="tooltip" data-placement="top" title="Ver documentación técnica" @click.stop.prevent="OnClickDocumentationTec(this.product.id_encrypt)">
                            </a>
                        </p>
                    </div>
                </div>

                <div class="shop-footer-product col-lg-12 d-flex justify-content-between align-items-center">
                        <p class="price">COP {{ $FormatCOMoney(this.product.price) }}</p>
                        <button class="btn btn-sm btn-primary btn-add-cart" @click="OnClickAddProductCar()">
                            <img src="img/shop_module/shopping-cart-solid.svg" alt="cart">
                            Agregar
                        </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Product from './model/products';
import Select_Savk from "../../../../../../resources/js/components/pages/otros/Select_Savk.vue";

export default {
    components:{
        Select_Savk
    },
    props:
    {
        product: Product  
    },
    created()
    {
    },
    async mounted()
    {
        if(this.product.extensions.length != 0)
            this.extension_select = this.product.extensions[0].id;
    },
    data()
    {
        return {
            config:
            {
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            quantity: 1,
            extension_select: null
        }
    },
    methods:
    {
        OnClickOpenClose()
        {
        },
        OnClickDocumentationTec(id_encrypt)
        {
            window.location.href = `${this.url}catalogo/detalle/${id_encrypt}/1`;
        },
        OnClickVideos(id_encrypt)
        {
            window.location.href = `${this.url}catalogo/detalle/${id_encrypt}/2`;
        },
        async OnClickAddProductCar()
        {
            console.log(this.extension_select);
            if(this.quantity == "" || this.quantity == 0)
            {
                toastr.warning(`Debes agregar almenos 1 cantidad.`);
                return;
            }

            try 
            {
                let data_form = new FormData();                
                data_form.append('id_product', this.product.id);
                data_form.append('quantity', this.quantity);
                data_form.append('ext', this.extension_select);
                
                loading();
                let rs = await fetch(`${this.url}catalogo/add_product_to_car`, { method: "POST", body: data_form, headers: {'X-CSRF-TOKEN': this.config.token}});
                let rd = await rs.json();
                loading(false);
                
                const {success, responseCode, message, data} = rd;

                switch (responseCode) 
                {
                    case 200:
                        toastr.success(message);
                        this.quantity = 1;
                        let total_quantity = 0;
                        if(data.length != 0)
                             total_quantity = data.reduce((acumulador, registro) => acumulador + registro.cantidad, 0);

                        this.$emit('update-car', total_quantity);
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
        OnSelectedExtensions(item)
        {
            this.extension_select = item.id
        },
    }
}
</script>

<style scoped>
.dev_input_quantity
{
    height: auto;
    margin-left: 10px;
}
.default-select
{
    height: auto;
}
</style>