<template>
    <div class="container-fluid">

        <div class="card">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap mb-2 align-items-end justify-content-end row menu-cap">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" @click="NewTraining">Nuevo</button>
                            </div>
                            <div class="div-busqueda">
                                <div class="input-group">
                                    <input
                                    type="text"
                                    class="form-control form-control-busqueda"
                                    placeholder="Buscar capacitación"
                                    @keyup.enter="GetDataInit()"
                                    v-model="data.filters.training"
                                    />
                                    <div class="input-group-append">
                                    <span class="input-group-text btn-barra-naranja">
                                        <a
                                        href="javascript:void(0)"
                                        class="aBuscar"
                                        @click="GetDataInit()"
                                        >
                                        <i class="flaticon-381-search-2"></i>
                                        </a>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <!-- <th>Descripción</th> -->
                                        <th>Tiempo (min)</th>
                                        <th>Creado por</th>
                                        <th>Tipo</th>
                                        <!-- <th v-if="data.main_account == 1">Tipo</th> -->
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="training in data.trainings" :key="training.id">
                                        <td><strong>{{ training.nombre }}</strong></td>
                                        <!-- <td>{{ training.descripcion }}</td> -->
                                        <td>{{ training.tiempo_minutos }}</td>
                                        <td>{{ training.nombre_com }}</td>
                                        <td>{{ training.tipo_capacitacion == 1 ? 'E-Learning' : training.tipo_capacitacion == 2 ? 'Asistida por experto' : 'Webinar' }}</td>
                                        <td><div class="d-flex align-items-center dev-cursor-pointer" @click="OnClickChangeStatus(training)"><i :class="'fa fa-circle '+(training.estado == 1 ? 'text-success' : 'text-danger')+' mr-1'"></i>{{ training.estado_texto }}</div></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="#" class="badge badge-primary mr-1" @click.prevent="OnClickRedirectNewTraining(training.id_training_encrypt)">
                                                    <!-- <i class="fa fa-pencil"></i> -->
                                                    Editar
                                                </a>
                                                <a href="#" class="badge badge-primary" @click.prevent="OnClickDeleteTraining(training)">
                                                    Eliminar
                                                    <!-- <i class="fa fa-trash"></i> -->
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="data.trainings.length == 0">
                                        <td colspan="6" class="text-center font-weight-bold">No tienes datos</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <nav class="d-flex justify-content-end">
                            <ul class="pagination pagination-gutter">
                                <li class="page-item page-indicator">
                                    <a class="page-link" href="javascript:void(0)" @click="previousPage">
                                    <i class="la la-angle-left"></i
                                    ></a>
                                </li>

                                <li v-for="number in data.pagination.total_pages" :class="'page-item '+(data.pagination.current_page == number ? 'active' : '')">
                                    <a class="page-link" href="javascript:void(0)"
                                    @click="numPage(number)"
                                    >{{ number }}</a>
                                </li>

                                <li class="page-item page-indicator">
                                    <a class="page-link" href="javascript:void(0)" @click="nextPage">
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
</template>

<script>
    // import CardTrainingComponent from "./CardTrainingComponent.vue";
    export default {
        props:
        {

        },
        components:
        {
            // CardTrainingComponent,
        },
        async created()
        {
            await this.GetDataInit();
        },
        mounted()
        {

        },
        computed:
        {

        },
        data()
        {
            return {
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                data:
                {
                    trainings: [],
                    main_account: null,
                    pagination:
                    {
                        current_page: 1,
                        total_pages: 0,
                        step: 3,
                        ClearData()
                        {
                            this.current_page = 1;
                            this.total_pages = 0;
                        },
                    },
                    filters:{
                        training: ''
                    }
                }
            }
        },
        methods:
        {
            async previousPage()
            {
                if (this.data.pagination.current_page === 1) return;
                this.data.pagination.current_page--;
                await this.GetDataInit(true);
            },
            async nextPage()
            {
                if (this.data.pagination.current_page === this.data.pagination.total_pages) return;

                this.data.pagination.current_page++;
                await this.GetDataInit(true);
            },
            async numPage(number)
            {
                this.data.pagination.current_page = number;
                await this.GetDataInit(true);
            },
            async GetDataInit(showLoading = false)
            {
                try
                {
                    let data = new FormData();
                    data.append("current_page", this.data.pagination.current_page);
                    data.append("filters", this.data.filters.training);

                    if(showLoading)
                        loading();

                    let rs = await fetch(`${this.url}capacitaciones/administracion/get_data_init`, { method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();

                    if(showLoading)
                        loading(false);

                    switch (rd.responseCode)
                    {
                        case 202:
                            this.data.trainings = rd.data.trainings;
                            this.data.pagination.total_pages = rd.data.per_page;
                            this.data.main_account = rd.data.main_account;
                            break;

                        default:
                            break;
                    }
                }
                catch (error)
                {
                    if(showLoading)
                        loading(false);

                    console.error(`Error to get principal Data: ${error.message}`);
                }

            },
            async NewTraining()
            {
                try
                {
                    let data_form = new FormData();

                    loading();
                    let rs = await fetch(`${this.url}capacitaciones/administracion/new_training_administration`, { method: "POST", body: data_form, headers: {
                        'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();
                    loading(false);

                    const { responseCode, message, data } = rd;

                    switch (responseCode)
                    {
                        case 200:
                            window.location.href = `${this.url}capacitaciones/administracion/${data}`;
                            break;

                        default:
                            break;
                    }

                }
                catch (error)
                {
                    console.error(`Error al crear capacitación: ${error.message}`);
                    loading(false);
                }
            },
            OnClickRedirectNewTraining(idTraining)
            {
                window.location.href = `${this.url}capacitaciones/administracion/${idTraining}`;
            },
            async OnClickDeleteTraining(training)
            {
                try
                {
                    let answer = await swal({
                        title: "¿Estás seguro?",
                        text: `Recuerda que una vez eliminada la capacitación no se podrá recuperar`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Si, eliminar",
                        cancelButtonText: "No",
                        confirmButtonColor: '#1f3352',
                        cancelButtonColor: '#ff7f00',
                        allowOutsideClick: false
                    });

                    if(answer.value)
                    {
                        let data_form = new FormData();
                        data_form.append('id_training', training.id);

                        loading();
                        let rs = await fetch(`${this.url}capacitaciones/administracion/delete_training_administration`, { method: "POST", body: data_form, headers: {
                            'X-CSRF-TOKEN': this.token
                        }});
                        let rd = await rs.json();
                        loading(false);

                        const { responseCode, message, data } = rd;

                        switch (responseCode)
                        {
                            case 206:
                                this.data.trainings = this.data.trainings.filter(tr => tr.id != training.id);
                                swal({
                                    title: "¡Éxitoso!",
                                    text: message,
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "Aceptar",
                                    confirmButtonColor: '#1f3352',
                                    allowOutsideClick: false
                                });
                                break;

                            case 406:
                                swal({
                                    title: "¡Falló!",
                                    text: message,
                                    type: "warning",
                                    showCancelButton: false,
                                    confirmButtonText: "Aceptar",
                                    confirmButtonColor: '#1f3352',
                                    allowOutsideClick: false
                                });
                                break;

                            default:
                                break;
                        }
                    }
                }
                catch (error)
                {
                    console.error(`Error al crear capacitación: ${error.message}`);
                    loading(false);
                }
            },
            async OnClickChangeStatus(training)
            {
                try
                {
                    let data_form = new FormData();
                    data_form.append('id_training', training.id);
                    data_form.append('current_status', training.estado);

                    loading();
                    let rs = await fetch(`${this.url}capacitaciones/administracion/change_training_administration`, { method: "POST", body: data_form, headers: {
                        'X-CSRF-TOKEN': this.token
                    }});
                    let rd = await rs.json();
                    loading(false);

                    const { responseCode, message, data } = rd;

                    switch (responseCode)
                    {
                        case 206:
                            this.data.trainings.map((tr)=> {
                                if(tr.id == training.id)
                                {
                                    tr.estado = data.estado;
                                    tr.estado_texto = data.estado_texto;
                                }
                            });

                            swal({
                                title: "¡Éxitoso!",
                                text: message,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Aceptar",
                                confirmButtonColor: '#1f3352',
                                allowOutsideClick: false
                            });
                            break;

                        case 406:
                            swal({
                                title: "¡Falló!",
                                text: message,
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Aceptar",
                                confirmButtonColor: '#1f3352',
                                allowOutsideClick: false
                            });
                            break;

                        default:
                            break;
                    }
                }
                catch (error)
                {
                    console.error(`Error al crear capacitación: ${error.message}`);
                    loading(false);
                }
            }

        }
    }
</script>

<style scoped>
.dev-icon-back
{
    font-size: 25px;
    cursor: pointer;
}

.dev-icon-certificate
{
    color: #FE634E;
    font-size: 24px;
    cursor: pointer;
}
.dev-bg
{
    background: rgba(254, 99, 78, 0.05);
    border-radius: 1.25rem;
    padding: 14px 18px 14px 18px;
}
</style>

