<template>
    <div v-show="visible" class="col-xl-6 col-lg-6 col-sm-6" :id="(type == 1 ? 'indicator_sost' : 'indicator_ent')">
        <div class="widget-stat card">
            <div class="card-body p-custom dev-widget-stat">
                <div class="media ai-icon row">
                    <div class="col-lg-2 col-4">
                        <div class="col-lg-12">
                            <span class="mr-1 dev-bg-icon div-icon"
                                    :style="{ color : coloricon, backgroundColor : colorfondo }">
                                <!-- <i class="ti-user"></i> -->
                                <i :class="'flaticon-381-'+icono" data-v-91a1bec0=""></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-8 pl-xl-0">
                        <div class="media-body">
                            <h4 class="mb-0">{{ titulo }}</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="dropdown custom-dropdown d-flex justify-content-end">
                            <div data-toggle="dropdown" aria-expanded="false" style="font-size: 12px">
                                {{ selectFiltro }}
                                <i class="fa fa-angle-down ml-3"></i>
                            </div>
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                                style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(105px, 21px, 0px);">
                                <a class="dropdown-item" href="#" @click="filtro" id="filtroTodo">Todo el histórico</a>
                                <a class="dropdown-item" href="#" @click="filtro" id="filtroAnoAnt">Año anterior</a>
                                <a class="dropdown-item" href="#" @click="filtro" id="filtroEsteAno">Este año</a>
                                <a class="dropdown-item" href="#" @click="filtro" id="filtroMesAnt">Mes anterior</a>
                                <a class="dropdown-item" href="#" @click="filtro" id="filtroEsteMes">Este mes</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2 dev_texts" :class="{'-loading': placeholder_loading}" v-show="type==2 || type == 3">
                    <div class="col-lg-8">
                        <div class="col-lg-12">
                            <h6 v-if="type == 2">Porcentaje cumplimiento de capacitaciones de mi plan</h6>
                            <h6 v-if="type == 3">Porcentaje cumplimiento de capacitaciones de mis capacitaciones</h6>
                        </div>
                        <div class="col-lg-12 text-muted mb-2 dev-font-size">
                            <small class="text-muted mb-2 dev-font-size" style="font-weight: 300;">
                                Porcentaje de capacitaciones que he completado.
                            </small>
                        </div>

                    </div>
                    <div class="col-lg-4 pl-1 justify-content-end d-flex">
                        <span :class="'dev_text_medida badge badge-'+badge">{{ valorbadgePorcentaje }} </span>
                    </div>
                </div>
                <div class="row mt-2 dev_texts" :class="{'-loading': placeholder_loading}">
                    <div class="col-lg-8">
                        <div class="col-lg-12">
                            <h6>{{ subtitulo }}</h6>
                        </div>
                        <div class="col-lg-12 text-muted mb-2 dev-font-size">
                            <small class="text-muted mb-2 dev-font-size" style="font-weight: 300;">
                                {{ descripcion }}
                            </small>
                        </div>

                    </div>
                    <div class="col-lg-4 pl-1 justify-content-end d-flex">
                        <span :class="'dev_text_medida badge badge-'+badge">{{ valorbadge }} {{ medida }}</span>
                    </div>
                </div>
                <!-- ELIMINAR UNO SOLO ESTA QUEMADO POR PRESENTACIÓN -->
                <div class="row mt-2 dev_texts" :class="{'-loading': placeholder_loading}" v-show="type==1">
                    <div class="col-lg-8">
                        <div class="col-lg-12">
                            <h6>Productos sostenibles</h6>
                        </div>
                        <div class="col-lg-12 text-muted mb-2 dev-font-size">
                            <small class="text-muted mb-2 dev-font-size" style="font-weight: 300;">
                                Porcentaje de productos altamente sostenibles incluidos en el programa.
                            </small>
                        </div>

                    </div>
                    <div class="col-lg-4 pl-1 justify-content-end d-flex">
                        <!-- <span :class="'dev_text_medida badge badge-'+badge">{{ valorbadgePorcentaje }}</span> -->
                        <span :class="'dev_text_medida badge badge-'+badge">100%</span>
                    </div>
                </div>

                <div class="row mt-2 dev_texts" :class="{'-loading': placeholder_loading}" v-show="type==4">
                    <div class="col-lg-8">
                        <div class="col-lg-12">
                            <h6>Horas de acompañamiento</h6>
                        </div>
                        <div class="col-lg-12 text-muted mb-2 dev-font-size">
                            <small class="text-muted mb-2 dev-font-size" style="font-weight: 300;">
                                Horas de acompañamiento virtual y presencial realizados a toda la organización.
                            </small>
                        </div>

                    </div>
                    <div class="col-lg-4 pl-1 justify-content-end d-flex">
                        <span :class="'dev_text_medida badge badge-'+badge">{{ valorbadgePorcentaje }}</span>
                    </div>
                </div>

                <div class="row mt-2 justify-content-center" v-if="(type==2 || type==3) && permisos.includes('ind-ind-ver_indicadores')">
                    <div class="col-lg-8 offset-lg-2">
                        <button
                            class="btn btn-primary btn-sm ml-2 btn-entrenamiento"
                            @click.prevent="OnClickRedirect"
                            id="btn-ver-indicadores"
                        >
                        Ver indicadores de todo el equipo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import '../../../../../node_modules/shepherd.js/dist/css/shepherd.css';
import { guiaGetAll, saveVisualizacionGuia, CreateTour } from "../../../../../public/assets/js/functions.js";
export default {
    props: {
        type: String,
        main_account: String,
        icono: String,
        badge: String, //CLASE QUE LLEVA EL BADGE
        coloricon: String, //COLOR DEL ICONO
        colorfondo: String,  //COLOR DEL FONDO DEL ICONO
        titulo: String,
        subtitulo: String,
        descripcion: String,
        Filtro: String,
    },
    async mounted()
    {
        console.log(this.type);
        if(this.type == 1)
        {
            await this.GetDataInitApiBiable();
        }
        else if(this.type == 2)
        {
            // AQUI IRIA EL OTRO LLAMADO
            await this.GetDataInitEntrenamiento();
            await this.guiaGetAll();
            this.CreateTour(this.guias);
            this.tour.start();
            // this.placeholder_loading = false;
            // this.medida = this.placeholder_loading ? "" : "Horas";
            // this.valorbadge = this.placeholder_loading ? "" : "0";
        }
        else if(this.type == 3)
        {
            // AQUI IRIA EL OTRO LLAMADO
            await this.GetDataInitEntrenamiento();
            await this.guiaGetAll();
            this.CreateTour(this.guias);
            this.tour.start();
            // this.placeholder_loading = false;
            // this.medida = this.placeholder_loading ? "" : "Horas";
            // this.valorbadge = this.placeholder_loading ? "" : "0";
        }
        else if(this.type == 4){
            await this.GetDataInitAcompanamiento();
        }
        else
        {
            this.placeholder_loading = false;
            this.medida = this.placeholder_loading ? "" : "Hora";
            this.valorbadge = this.placeholder_loading ? "" : "1";
        }
    },
    data() {
        return {
            permisos : JSON.parse(localStorage.getItem('permisos')),
            guias: [],
            token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            url: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("url"),
            api: document.querySelector('meta[name="csrf-token"]').getAttribute("api"),
            perfil : '',
            medida: "",
            valorbadge: "",
            valorbadgePorcentaje: " ",
            valor: 10, // Referencia al reproductor de YouTube
            selectFiltro : 'Todo el histórico',
            selected_filter: "filtroTodo",
            visible: true,
            filters: {
                filtroTodo: {
                    init_date: 0,
                    end_date: 0
                },
                filtroAnoAnt: {
                    init_date: (`${moment().subtract(1, 'Y').format("YYYY")}0101`),
                    end_date: (`${moment().subtract(1, 'Y').format("YYYY")}1231`)
                },
                filtroEsteAno: {
                    init_date: (`${moment().format("YYYY")}0101`),
                    end_date: (`${moment().format("YYYY")}1231`)
                },
                filtroMesAnt: {
                    init_date: moment().subtract(1, 'M').startOf('month').format("YYYYMMDD"),
                    end_date: moment().subtract(1, 'M').endOf('month').format("YYYYMMDD")
                },
                filtroEsteMes: {
                    init_date: moment().startOf('month').format("YYYYMMDD"),
                    end_date: moment().endOf('month').format("YYYYMMDD")
                },
            },
            placeholder_loading: true,
            tour: null
        };
    },
    methods: {
        guiaGetAll,
        saveVisualizacionGuia,
        CreateTour,
        async GetDataInitApiBiable()
        {
            try
            {
                this.medida = "";
                this.valorbadge = "";
                let form_data = new FormData();
                form_data.append("init_date", this.filters[this.selected_filter].init_date);
                form_data.append("end_date", this.filters[this.selected_filter].end_date);
                form_data.append("main_account", this.main_account);
                form_data.append("type", this.type);

                this.placeholder_loading = true;
                let rs = await fetch(`${this.api}api/biable/get_data_dashboard`, { method: "POST", body: form_data });
                let rd = await rs.json();

                const { success, responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 202:
                        this.placeholder_loading = false;
                        if(this.type == 1)
                        {
                            this.medida = this.placeholder_loading ? "" : "Toneladas";
                            this.valorbadge = this.placeholder_loading ? "" : data;
                        }
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                console.error(`Error al traer información cards: ${error.message}`);
            }
        },
        filtro(event){
            const referencia = event.target.textContent;
            this.selected_filter = event.target.id;
            this.selectFiltro = referencia;

            if(this.type == 1)
                this.GetDataInitApiBiable();
            else if(this.type == 2 || this.type == 3)
            {
                // AQUI IRIA EL OTRO LLAMADO
                this.GetDataInitEntrenamiento();
            }
            else if(this.type == 4)
            {
                // AQUI IRIA EL OTRO LLAMADO
                this.GetDataInitAcompanamiento();
            }
            else
            {
                this.medida = this.placeholder_loading ? "" : "Hora";
                this.valorbadge = this.placeholder_loading ? "" : data;
            }
        },
        async GetDataInitEntrenamiento()
        {
            try {
                this.medida = "";
                this.valorbadge = "";
                let form_data = new FormData();
                form_data.append("init_date", this.filters[this.selected_filter].init_date);
                form_data.append("end_date", this.filters[this.selected_filter].end_date);
                form_data.append("main_account", this.main_account);
                form_data.append("type", this.type);

                this.placeholder_loading = true;
                let rs = await fetch(`${this.url}capacitaciones/horas_entrenamiento`, { method: "POST", body: form_data, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 201:
                        this.placeholder_loading = false;
                        if(this.type == 2 || this.type == 3)
                        {
                            this.medida = this.placeholder_loading ? "" : "Horas";
                            this.valorbadge = this.placeholder_loading ? "" : data.sumaMinutos;
                            let porcentaje = ((data.sumaMinutos /data.sumaMinutosTotales)*100).toFixed(2)
                            porcentaje = isNaN(porcentaje) ? 0 : porcentaje
                            this.valorbadgePorcentaje = this.placeholder_loading ? "" : porcentaje + '%';
                            this.perfil = data.perfil
                        }
                        break;
                    default:
                        break;
                }

            } catch (error) {
                console.log(error);
            }
        },
        async GetDataInitAcompanamiento()
        {
            try {
                this.medida = "";
                this.valorbadgePorcentaje = "";

                let form_data = new FormData();
                form_data.append("init_date", this.filters[this.selected_filter].init_date);
                form_data.append("end_date", this.filters[this.selected_filter].end_date);

                this.placeholder_loading = true;
                let rs = await fetch(`${this.url}accompaniment/indicadores`, { method: "POST", body: form_data, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 201:
                        if(data.numeroAcompanamiento == 0){
                            this.visible = false;
                        }
                        this.placeholder_loading = false;
                        this.medida = this.placeholder_loading ? "" : data.numeroAcompanamiento;
                        this.valorbadgePorcentaje = this.placeholder_loading ? "" : data.horasAcompanamiento + ' Horas';
                        break;
                    default:
                        break;
                }

            } catch (error) {
                console.log(error);
            }
        },
        OnClickRedirect()
        {
            console.log(this.type);
            switch (this.type) {
                case '2':
                    window.location.href = this.url + "dashboard/indicadores_equipo";
                    break;

                case '3':
                    window.location.href = this.url + "dashboard/indicadores_equipo_corporativos";
                    break;
                default:
                    break;
            }
        }
    },
}
</script>

<style scoped>
.badge-snapchat{
    font-size: 18px;
    background-color: white;
    border: 1px solid #ffc008;
    color: #082f55;
    font-weight: 600;
    padding: 10px;
    height: 43px;
    width: 160px;
    border: 1px solid #082f55;
}

.badge-green{
    font-size: 18px;
    background-color: white;
    border: 1px solid #204b23;
    color: #082f55;
    font-weight: 600;
    padding: 10px;
    height: 43px;
    width: 160px;
}

.div-icon{
    height: 60px;
    width: 60px;
    border-radius: 100%;
    padding: 10px 12px;
    font-size: 32px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.dev_texts.-loading .dev_text_medida
{
  background: #e9edf1;
  border: none;
  background: linear-gradient(90deg, #e9edf1 7%, #eff2f4 12%, #e9edf1 37%);
  background-size: 200% 100%;
  -webkit-animation: 1.5s shimmer linear infinite;
          animation: 1.5s shimmer linear infinite;
}
.dev_texts.-loading .dev_text_medida {
  border-radius: 6px;
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

.btn-entrenamiento{
    background-color: #204b23 !important;
}
</style>
