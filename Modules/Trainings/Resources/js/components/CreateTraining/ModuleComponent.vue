<template>
    <div class="col-lg-12">
        <div class="accordion accordion-primary">
            <div class="accordion__item" @click="OnClickOpenClose">
                <div :class="`accordion__header rounded-lg ${(data.component_collapse ? '' : 'collapsed')}`">
                    <div class="col-lg-12 d-flex justify-content-between">
                        <span class="accordion__header--text mt-2">
                            <i class="bi bi-arrow-down-circle-fill mr-2"></i>
                            {{ data_component.nombre }}
                        </span>
                        <div class="">
                            <!-- <i class="la la-pencil dev-fonts-icon-mod dev-fonts-color-white" @click.stop="EmitEditInformation"></i>
                            <i class="la la-trash dev-fonts-icon-mod dev-fonts-color-white" @click.stop="EmitDeleteModule"></i> -->
                            <button class="btn btn-sm btn-barrat" @click.stop="EmitEditInformation">Editar</button>
                            <button class="btn btn-sm btn-barrat ml-1" @click.stop="EmitDeleteModule">Eliminar</button>
                        </div>
                    </div>
                    <!-- <span class="accordion__header--indicator"></span> -->
                </div>
                <div :class="`collapse accordion__body ${(data.component_collapse ? 'show' : '')} row`">
                    <div :id="'dv-crear-video-cap-'+this.contador" :class="`col-lg-2 mt-4 d-flex justify-content-center flex-wrap dev-cursor-pointer dev-section-card ${(data_component.tiene_video == 0 ? '' : 'active')}`" @click.stop="OnClickVideoPopUp">
                        <div class="border border-primary rounded-circle p-3" style="border-color: #002F54 !important">
                           <i class="la la-video dev-fonts-icon-mod dev-fonts-color-primary"></i>
                        </div>
                        <p class="col-lg-12 text-center">Video</p>
                    </div>

                    <div :id="'dv-crear-recursos-cap-'+this.contador" :class="`col-lg-2 mt-4 d-flex justify-content-center flex-wrap dev-cursor-pointer dev-section-card ${(data_component.tiene_contenido == 0 ? '' : 'active')}`" @click.stop="OnClickContentPopUp">
                        <div class="border border-primary rounded-circle p-3" style="border-color: #002F54 !important">
                           <i class="la la-files-o dev-fonts-icon-mod dev-fonts-color-primary"></i>
                        </div>
                        <p class="col-lg-12 text-center">Recursos</p>
                    </div>

                    <div :id="'dv-crear-evaluacion-cap-'+this.contador" v-if="assessBy == 2" :class="`col-lg-2 mt-4 d-flex justify-content-center flex-wrap dev-cursor-pointer dev-section-card ${(data_component.tiene_preguntas == 0 ? '' : 'active')}`" @click.stop="OnClickTestPopUp">
                        <div class="border border-primary rounded-circle p-3" style="border-color: #002F54 !important">
                           <i class="la la-list-alt dev-fonts-icon-mod dev-fonts-color-primary"></i>
                        </div>
                        <p class="col-lg-12 text-center">Evaluación</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas  } from "../../../../../../public/assets/js/functions.js";

export default {
    props:
    {
        data_component: Object,
        show: Boolean,
        assessBy: Number,
        contador : Number
    },
    created()
    {
        this.data_component.component_collapse = this.show;
    },
    async mounted(){
        // await this.guiaGetAll();
        // let guiasEspecificas = await this.guiasEspecificas(['dv-crear-video-cap-0', 'dv-crear-recursos-cap-0', 'dv-crear-evaluacion-cap-0']);
        // this.CreateTour(guiasEspecificas);
        // this.tour.start();
        if (this.contador == 0) {
            this.OnClickOpenClose()
        }
    },
    data()
    {
        return {
            guias: [],
            guiasSecundarias: [],
            tour: null,
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            data:
            {
                component_collapse: false
            }
        }
    },
    methods:
    {
        guiaGetAll,
        saveVisualizacionGuia,
        CreateTour,
        guiasEspecificas,
        OnClickOpenClose()
        {
            this.data.component_collapse = !this.data.component_collapse;
        },
        EmitEditInformation()
        {
            this.$emit('edit_pop_up', this.data_component);
        },
        EmitDeleteModule()
        {
            this.$emit('delete_module', this.data_component);
        },
        OnClickContentPopUp()
        {
            this.$emit('open_modal_content', this.data_component);
        },
        OnClickTestPopUp()
        {
            this.$emit('open_modal_test', this.data_component);
        },
        OnClickVideoPopUp(){
            this.$emit('open_modal_video', this.data_component);
        }
    }
}
</script>

<style scoped>
.dev-icon-modules
{
    width: 30%;
    display: flex;
    justify-content: center;
}

.dev-fonts-icon-mod
{
    font-size: 30px;
}

.dev-fonts-color-primary
{
    color: #002F54;
}
.dev-fonts-color-white
{
    color: white;
}

.accordion-primary .accordion__header.collapsed
{
    background: #fe634e;
    border-color: #fe634e;
    color: #ffffff;
    /* box-shadow: none; */
}
.dev-section-card.active .rounded-circle
{
    background: #002F54!important;
}

.rounded-circle{
    max-height: 65px;
}
.dev-section-card.active .rounded-circle i
{
    color: #ffffff;
}

.dev-section-card.active p
{
    font-weight: bold;
    color: #002F54;
}
</style>
