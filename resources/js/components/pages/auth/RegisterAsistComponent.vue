<template>

    <div class="mx-auto">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div v-if="showSuccess.show" class="alert alert-success left-icon-big alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button>
                    <div class="media">
                        <div class="alert-left-icon-big">
                            <span><i class="mdi mdi-check-circle-outline"></i></span>
                        </div>
                        <div class="media-body">
                            <h5 class="mt-1 mb-2">{{showSuccess.msg}}</h5>
                        </div>
                    </div>
				</div>
                <div v-if="showError.show" class="alert alert-warning left-icon-big alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button>
                    <div class="media">
                        <div class="alert-left-icon-big">
                            <span><i class="mdi mdi-help-circle-outline"></i></span>
                        </div>
                        <div class="media-body">
                            <h5 class="mt-1 mb-2">{{showError.msg}}</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">REGISTRO DE ASISTENCIA</h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form @submit.prevent="checkForm">
                                        <div class="form-group">
                                            <input id="fullname" v-model="form.fullname" type="text" class="form-control input-default " placeholder="Nombre completo *">
                                        </div>
                                        <div class="form-group">
                                            <input id="email" v-model="form.email" type="email" class="form-control input-default " placeholder="Correo electroníco *">
                                        </div>
                                        <div class="form-group">
                                            <input id="company" v-model="form.company" type="text" class="form-control input-default " placeholder="Empresa *">
                                        </div>
                                        <div class="col-lg-3  mx-auto text-center">
                                            <button type="submit" class="btn btn-primary mb-2">Enviar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
            </div>
        </div>

    </div>
</template>

<script>
import Select_Savk from '../otros/Select_Savk.vue';
export default {
    props:
        {
            id_training: Object
        },
    components: {
        Select_Savk,
    },
    mounted() {

    },

    data() {
        return {
            sectores: [],
            url: ROOT_URL,
            form: {
                fullname: '',
                email: '',
                company: '',
                codigo: this.id_training.codigo
            },
            showSuccess:{
                show: false,
                msg: ""
            },
            showError:{
                show: false,
                msg: ""
            },
            optionsFetch: (data) => ({
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })

        }
    },

    methods: {
        checkForm: function (e) {
            var validado = true;
            for (const [key, value] of Object.entries(this.form))
            {
                //ID PARA AGREGAR O QUITAR CLASS
                var elemento = document.getElementById(key);
                // console.log(elemento);

                if (value == '' || typeof value === 'undefined') {
                    validado = false;
                    // elemento.classList.add("noDiligenciado");

                    // window.scroll({
                    //     top: 180,
                    //     left: 0,
                    //     behavior: 'smooth'
                    // });
                }
            }
            if (validado == false) {
                toastr.warning("Debes diligenciar todos los campos.")
            } else {
                //toastr.success("Todos los campos diligenciados correctamente")
                this.onSubmit()
            }
        },
        validar: function (e) {
            var elemento = document.getElementById(e.target.id);

            if (e.target.value != '') {
                elemento.classList.remove("noDiligenciado");
            }
        },

        async onSubmit() {
            try {
                loading()
                const response = await fetch(this.url + '/crear-asistencia', this.optionsFetch(this.form))
                const { status, msg } = await response.json()
                loading(false)
                if (status === 202) {
                    this.showSuccess = {
                        msg: msg,
                        show: true
                    }

                    this.showError = {
                        msg: "",
                        show: false
                    }
                    this.form =  {
                        fullname: '',
                        email: '',
                        company: '',
                        codigo: this.id_training.codigo
                    }
                } else {
                    this.showSuccess = {
                        msg: "",
                        show: false
                    }

                    this.showError = {
                        msg: msg,
                        show: true
                    }
                }

            } catch (error) {
                loading(false)
                toastr.error('Ha ocurrido un error interno.')
            }
        }
    },
}
</script>

<style scoped>
.authincation-content {
    background-color: #145c54;
    font-family: 'Roboto', sans-serif;
}

.requerido {
    color: red;
}

.noDiligenciado {
    box-shadow: 0 0 12px red;
}
</style>
