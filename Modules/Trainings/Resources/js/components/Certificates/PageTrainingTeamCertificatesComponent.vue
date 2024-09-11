<template>
    <div class="table-responsive">
        <table id="tableOrganization" class="table card-table display dataTablesCard">
            <thead>
                <tr class="">
                    <th>Nombre</th>
                    <th>Identificación</th>
                    <th>Estado</th>
                    <th>Certificado</th>
                    <th>Tiempo</th>
                    <th class="align-middle text-center">Intentos</th>
                    <th>Fecha</th>
                    <!-- <th>Puntos</th> -->
                    <th>
                        <button v-if="data.data.length > 0" class="badge badge-success" @click.prevent="downloadCertificateAll">
                            Descargar todo
                        </button>
                        <button v-if="data.data.length > 0" class="badge badge-success ml-1" @click.prevent="downloadExcelCertificateAll">
                            Descargar excel
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(valor) in data.data" :key="valor.id">
                    <td>{{ valor.nombre }}</td>
                    <td>{{ valor.documento }}</td>
                    <td>{{ valor.estado }}</td>
                    <td>{{ valor.nom_capacitacion }}</td>
                    <td>{{ valor.tiempo }} Hr(s)</td>
                    <td class="align-middle text-center">
                        <button class="badge badge-primary ml-1" @click.prevent="openModalViewInstantes(valor)">
                            {{ valor.intentos.intentos }} <span v-if="valor.permitir_certificacion == 1 && valor.evaluara_por == 2" > de {{ valor.intentos.gano }}</span>
                        </button>
                    </td>
                    <td>{{ valor.fecha_terminada }}</td>
                    <!-- <td>{{ valor.puntos }}</td> -->
                    <td>
                        <div>
                            <button class="badge badge-primary ml-1 mt-1" @click.prevent="shareCertificate(valor)">
                                 Compartir
                            </button>
                            <button class="badge badge-primary ml-1 mt-1" @click.prevent="downloadCertificate(valor)">
                                 Descargar
                            </button>
                            <!-- <button class="badge badge-primary ml-1">
                                 Renovar
                            </button> -->
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination pagination-circle">
            <li class="page-item page-indicator">
                <a class="page-link" href="javascript:void(0)" @click="previousPage">
                    <i class="la la-angle-left"></i></a>
            </li>

            <template v-for="(link, index) in data.paginate.links" :key="index">
                <template v-if="!(link.label.indexOf('Previous') > -1) &&
                    !(link.label.indexOf('Next') > -1)
                    ">
                    <li class="page-item" :class="link.active ? 'active' : ''">
                        <a class="page-link" href="javascript:void(0)" @click="numPage(link.label)">{{ link.label }}</a>
                    </li>
                </template>
            </template>

            <li class="page-item page-indicator">
                <a class="page-link" href="javascript:void(0)" @click="nextPage">
                    <i class="la la-angle-right"></i></a>
            </li>
        </ul>
    </nav>
    <!-- MODAL VER INTENTOS -->
    <div
        class="modal fade"
        id="modal_view_instantes_t"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
        ref="modal_view_instantes_t"
    >
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modal-title-create-company">
                Intentos
            </h5>
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableSecciones" class="table card-table display dataTablesCard">
                        <thead>
                            <tr>
                                <th v-if="data.dataSelect.evaluara_por == 2">Módulo</th>
                                <th>Fecha</th>
                                <th>Calificación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="data.dataIntentos.length == 0">
                                <td>
                                    No se generó evaluación para la certificación.
                                </td>
                            </tr>
                            <template v-for="(intento, index) in data.dataIntentos" :key="index">
                            <tr>
                                <td v-if="data.dataSelect.evaluara_por == 2">{{ intento.nombre }}</td>
                                <td>{{ intento.fecha_terminada }}</td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6 pt-2">{{ intento.resultado }}</div>
                                        <div class="col-md-4 pl-1">
                                            <i v-if="parseFloat(intento.resultado) >= parseFloat(intento.aprobacion)" class="bi bi-check-circle-fill icon-intentos"></i>
                                            <i v-if="parseFloat(intento.resultado) < parseFloat(intento.aprobacion)" class="bi bi-x-circle-fill icon-intentos"></i>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm"
                                        @click="openModalViewEvaluacion(intento)"
                                    >
                                        Ver
                                    </button>
                                </td>
                            </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            <button
                type="button"
                class="btn btn-danger light"
                data-dismiss="modal"
            >
                Cerrar
            </button>
            </div>
        </div>
        </div>
  </div>

  <!-- MODAL VER EVALUACIÓN -->
  <div
        class="modal fade"
        id="modal_view_evaluacion_t"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
        ref="modal_view_evaluacion_t"
    >
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modal-title-create-company">
                Evaluación
            </h5>
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <CardTestComponent :data_test="data.questions" :view="true"></CardTestComponent>
            </div>
            <div class="modal-footer">
            <button
                type="button"
                class="btn btn-danger light"
                data-dismiss="modal"
            >
                Cerrar
            </button>
            </div>
        </div>
        </div>
  </div>
</template>

<script>
import CardTestComponent from "../TrainingExcecution/CardTestComponent.vue";
export default {
    components:{
        CardTestComponent
    },
    created() {
        this.getDataAll()
    },
    props: {
        search: String,
    },
    data() {
        return {
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            data: {
                paginate: {
                    cant: 10,
                    total: 1,
                    current_page: 1,
                    links: [],
                },
                data : [],
                dataSelect : [],
                questions: [],
                dataIntentos: [],
                optionsFetch: (dataForm) => ({
                    method: "POST",
                    headers: {
                        "Content-type": "application/json; charset=UTF-8",
                        "X-CSRF-Token": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    },
                    body: JSON.stringify(dataForm),
                }),
            },
        };
    },
    watch: {
        search: {
            async handler(val) {
                await this.getDataAll();
            },
            deep: true,
        },
    },
    methods: {
        async openModalViewEvaluacion(intento) {
            try {
                let data = new FormData();
                if (this.data.dataSelect.evaluara_por == 1) { //CAPACITACIÓN GENERAL
                    data.append('id_module', null);
                    data.append('id_capacitacion', this.data.dataSelect.id_capacitacion);
                    data.append('id_evaluacion', intento.id)
                }else if (this.data.dataSelect.evaluara_por == 2) { // POR MÓDULOS
                    data.append('id_module', intento.id_modulo);
                    data.append('id_capacitacion', null);
                    data.append('id_evaluacion', intento.id)
                }

                loading();
                let rs = await fetch(`${this.url}capacitaciones/get_data_test_view`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 202:
                        this.data.questions = rd.data.questions;
                        $("#modal_view_evaluacion_t").modal("show");
                        break;

                    case 400:
                        toastr.warning('No se encontró examen.');
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al consultar el examen para realizarse: ${error.message}`);

            }
        },
        async openModalViewInstantes(certificado) {
            try {
                let data = new FormData();

                loading();
                data.append('id_capacitacion', certificado.id_capacitacion);
                data.append('id_modulo', certificado.id_modulo);
                data.append('id_usuario', certificado.id_usuario);
                let rs = await fetch(`${this.url}capacitaciones/certificados/get-instantes`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.status) {
                    case 200:
                        this.data.dataSelect = certificado
                        this.data.dataIntentos = rd.data;
                        $("#modal_view_instantes_t").modal("show");
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error`);
            }
        },
        async downloadExcelCertificateAll() {
            loading();
            window.open(`${this.url}capacitaciones/download-excel-certificateAll/2`, '_blank');
            loading(false);
        },
        async downloadCertificate(data) {
            loading();
            window.open(`${this.url}capacitaciones/download-certificate/${data.id}`, '_blank');
            loading(false);
        },

        async downloadCertificateAll() {
            loading();
            window.open(`${this.url}capacitaciones/download-certificateAll/2`, '_blank');
            loading(false);
        },

        async shareCertificate(dataPrincipal)
        {
            try {
                let data = new FormData();

                loading();
                data.append('id', dataPrincipal.id);
                let rs = await fetch(`${this.url}capacitaciones/share-certificate`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 200:

                        let answer = await swal({
                            title: "Link generado",
                            text: `El link ha sido generado correctamente, puedes copiarlo.`,
                            type: "success",
                            showCancelButton: true,
                            confirmButtonText: "Copiar link",
                            cancelButtonText: "Cerrar",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#145c54',
                            allowOutsideClick: false
                        });

                        if (answer) {
                            this.CopyLink(`${rd.data}`, document.body);
                        }
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al generar el link de los módulos: ${error.message}`);
            }
        },
        CopyLink(url, element_save) {
            var c = document.createElement("textarea");
            c.value = url;
            c.style.maxWidth = '0px';
            c.style.maxHeight = '0px';
            element_save.appendChild(c);

            c.focus();
            c.select();
            document.execCommand("copy");
            element_save.removeChild(c);

            toastr.success('Link de la capacitación copiada.')
        },

        async getDataAll(url = null) {
            url = url ?? `${this.url}` + "capacitaciones/certificados-team/all";

            const response = await fetch(
                url,
                this.data.optionsFetch({
                    filters: this.data.filters,
                    search: this.search,
                    paginate: this.data.paginate,
                })
            );
            const { status, data } = await response.json();

            if (status != 200) {
                toastr.error("Hubo un error al obtener la información.");
                return;
            }

            this.data.paginate.current_page = data.current_page;
            this.data.paginate.total = data.total;
            this.data.paginate.links = data.links;
            this.data.data = data.data;
        },

        //PAGINACION
        previousPage() {
            loading(true);
            if (this.data.paginate.current_page === 1){
                loading(false);
                return;
            }

            this.data.paginate.current_page--;
            this.getDataAll(
                `${this.url}` +
                "capacitaciones/certificados-team/all?page=" +
                this.data.paginate.current_page
            );
            loading(false);
        },

        nextPage() {
            loading(true);
            if (this.data.paginate.current_page === this.data.paginate.total) {
                loading(false);
                return;
            }

            this.data.paginate.current_page++;
            this.getDataAll(
                `${this.url}` +
                "capacitaciones/certificados-team/all?page=" +
                this.data.paginate.current_page
            );
            loading(false);
        },

        async numPage(num) {
            loading(true);
            await this.getDataAll(
                `${this.url}` + "capacitaciones/certificados-team/all?page=" + num
            );
            loading(false);
        },

        changeCantPaginate() {
            this.getDataAll();
        },

    },
};
</script>

<style>
.badge {
    font-size: 10px;
}

.pagination .page-item.active .page-link {
    background-color: #002f54;
    color: white;
    border-color: #002f54;
    box-shadow: 0 10px 20px 0px rgba(254, 99, 78, 0.05);
}

.pagination .page-item .page-link:hover{
    background-color: #145c54;
    color: white;
    border-color: #145c54;
    box-shadow: 0 10px 20px 0px rgba(254, 99, 78, 0.05);
}

.icon-intentos{
    font-size: 25px;
}

.bi-x-circle-fill.icon-intentos{
    color: red;
}

.bi-check-circle-fill.icon-intentos{
    color: green;
}
</style>
