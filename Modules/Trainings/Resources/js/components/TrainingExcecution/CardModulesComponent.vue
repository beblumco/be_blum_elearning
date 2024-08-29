<template>
    <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row m-b-30">
                                <div class="col-lg-12 d-flex justify-content-end mb-3">
                                    <div class="dropdown" v-if="data_module.permitir_certificacion == 2">
                                        <button type="button" class="btn btn-warning light sharp" data-toggle="dropdown">
                                            <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" @click.prevent="OnClickToViewLinks">Ver links</a>
                                            <a class="dropdown-item" href="#" @click.prevent="OnClickToGenerateLink">Generar link</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="new-arrival-content position-relative text-center">
                                        <h4 class="mb-5"><a href="#">{{ data_module.nombre }}</a></h4>
                                    </div>
                                    <div class="shop-footer-product">
                                        <p :class="`price ${data_module.COLOR_PERCENT}`">{{ data_module.PERCENT_MODULE }}%</p>
                                        <button class="btn btn-sm btn-primary btn-add-cart" @click="OnClickInitModule">
                                            Iniciar Módulo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </div>
</template>

<script>
export default {
    props: {
        data_module: Object
    },
    methods: {
        OnClickInitModule()
        {
            this.$emit('listener_module', this.data_module);
        },
        OnClickToGenerateLink()
        {
            this.$emit('listener_generate_link', this.data_module);
        },
        OnClickToViewLinks()
        {
            this.$emit('listener_view_links', this.data_module);
        }
    }
}
</script>

<style scoped>
</style>
