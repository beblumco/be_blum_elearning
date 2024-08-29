<template>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <div class="row m-b-30">

                    <div class="col-md-5 col-xxl-12 container-p">
                        <div class="new-arrival-product  text-center content-a m-0">
                            <img class="img-fluid content-image" :src="`${data_training.IMAGE_PATH}`" alt="Capacitación">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="new-arrival-content position-relative text-center">
                            <h4 class="mb-5"><a href="#">{{ data_training.nombre }}</a></h4>
                        </div>
                        <div class="shop-footer-product">
                            <p :class="`price ${data_training.COLOR_PERCENT}`">{{ data_training.TRAINING_PERCENT }}%</p>
                            <button class="btn btn-sm btn-primary btn-add-cart" @click="OnClickInitTraining">
                                Iniciar capacitación
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
        data_training: Object
    },
    methods: {
        OnClickInitTraining()
        {
            this.$emit('listener_training', this.data_training);
        }
    }
}
</script>

<style scoped>
.content-image
{
    /* width: 20vh; */
    max-height: 15vh;
}
.content-a {
    width: 100%;
}

</style>
