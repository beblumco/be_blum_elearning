<template>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-4">
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active">
                        </li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="1" class=""></li>
                    </ol>
                    <div class="carousel-inner">

                        <div :class="`carousel-item ${(index == 0 ? 'active' : '')}`" v-for="(content, index) in data_content" :key="content.id">
                             <div class="col-lg-12 dev_image_content d-flex justify-content-center" v-if="content.tipo_contenido == 1">
                                <img :src="`${content.ruta_contenido_completa}`" class="dev_image" srcset="">
                            </div>
                            <div class="col-lg-12 justify-content-center" v-if="content.tipo_contenido == 2">
                                <embed :src="`${content.ruta_contenido_completa}#toolbar=0&navpanes=0&scrollbar=0`" type="application/pdf" width="100%" height="600px" />
                            </div>
                            <div class="col-lg-12 d-flex justify-content-center" v-if="content.tipo_contenido == 3">
                                <iframe :src="content.ruta_contenido" width="640" height="369" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                            </div>
                        </div>

                    </div>
                    
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" data-slide="prev">
                        <span class="carousel-control-prev-icon"></span> 
                        <span class="sr-only">Previous</span> 
                    </a>

                    <a class="carousel-control-next" href="#carouselExampleIndicators" data-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="sr-only">Next</span>
                    </a>

                </div>
            </div>
        </div>
        <!-- <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ this.data_content.title }}</h4>
            </div>
            <div class="card-body">
                <div class="col-lg-12 dev_responsive_content" v-if="data_content.tipo_contenido == 3">
                    <iframe :src="data_content.ruta_contenido_completa" class="dev_responsive" ></iframe>
                </div>

                <div class="col-lg-12" v-if="data_content.tipo_contenido == 2">
                    <div class="col-lg-12 d-flex justify-content-end mb-3">
                        <button class="btn btn-info" @click="OnClickOpenPDF">Abrir PDF</button>
                    </div>
                    <embed :src="`${data_content.ruta_contenido_completa}#toolbar=0&navpanes=0&scrollbar=0`" type="application/pdf" width="100%" height="600px" />
                </div>
                
            </div>
        </div> -->
    </div>
</template>

<script>
export default {
    props: {
        data_content: Array 
    },
    mounted()
    {
        console.log(this.data_content);
    },
    methods: {
        OnClickOpenPDF()
        {
            window.open(this.data_content.path_content,'_blank');
        }   
    }
}
</script>

<style scoped>
.dev_responsive 
{
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    width: 100%;
    height: 100%;
}

.dev_responsive_content
{
    position: relative;
    overflow: hidden;
    width: 100%;
    padding-top: 56.25%;
}
</style>