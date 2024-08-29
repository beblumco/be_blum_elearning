<template>
    <div class="col-lg-12">
        <div class="card border-0 pb-0">
            <div class="card-body">
                <div id="DZ_W_Todo4" class="">
                    <div class="row" v-if="view">
                        <div class="col-lg-12 d-flex justify-content-end">
                            <button class="btn btn-primary" @click="OnClickToPdf">Descargar PDF</button>
                        </div>
                    </div>
                    <ul class="timeline mb-3" v-for="(item, index) in data_test" :key="item.id">
                        <li v-if="!view">
                            <div class="timeline-panel mb-2">
                                <div class="media-body">
                                    <h5 class="mb-0">{{ index+1 }}. {{ item.pregunta }}</h5>
                                </div>
                            </div>

                            <div class="d-flex mb-2" v-for="answer in item.answers" :key="answer.id">
                                <div class="custom-control custom-checkbox checkbox-success check-lg mr-3">
                                    <input type="checkbox" class="custom-control-input" :id="`checkbox_${item.id}_${answer.id}`" required="" v-model="answer.checked" @change="OnChangeAnswer(answer,item.answers)">
                                    <label class="custom-control-label" :for="`checkbox_${item.id}_${answer.id}`"></label>
                                </div>

                                <div class="align-items-center">
                                    <small class="text-muted">{{ answer.respuesta }}</small>
                                </div>
                            </div>

                        </li>
                        <li v-if="view">
                            <div class="timeline-panel mb-2">
                                <div class="media-body">
                                    <h5 class="mb-0">{{ index+1 }}. {{ item.pregunta }}</h5>
                                </div>
                            </div>

                             <div class="row mb-2" v-for="answer in item.answers" :key="answer.id">
                                <div class="custom-control custom-checkbox checkbox-success check-lg col-lg-1">
                                    <input type="checkbox" class="custom-control-input" :id="`checkbox_${item.id}_${answer.id}`" required="" v-model="answer.checked" :disabled="answer.checked">
                                    <label class="custom-control-label" :for="`checkbox_${item.id}_${answer.id}`"></label>
                                </div>
                                <div class="align-items-center col-lg-9">
                                    <small class="text-muted">{{ answer.respuesta }}</small>
                                </div>
                                <div class="col-lg-2">
                                    <i v-if="answer.checked == 'true' && answer.correct == 'true'" class="bi bi-check-circle-fill icon-intentos"></i>
                                    <i v-if="answer.checked == 'true' && answer.correct == 'false'" class="bi bi-x-circle-fill icon-intentos"></i>
                                </div>
                            </div>

                        </li>
                    </ul>

                </div>

                <div class="col-lg-12 d-flex justify-content-center mt-3" v-if="!view">
                    <button class="btn btn-primary" @click="OnClickToFinishTest">Terminar examen</button>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data_test: Array,
        id_test_init: Array,
        publica: String, //ES TRUE CUANDO SE REALIZARA EL TEST EN CAP ASISTIDA PUBLICA
        view: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        async OnClickToPdf() {
            let data = new FormData();

            data.append('data', JSON.stringify(this.data_test));
            loading();
            let rs = await fetch(`${this.url}capacitaciones/download-test`, { method: "POST", body: data, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            loading(false);
            if (rs.ok) {
                const blob = await rs.blob();
                const url = URL.createObjectURL(blob);

                // Crear un enlace de descarga y hacer clic en él para iniciar la descarga
                const link = document.createElement('a');
                link.href = url;
                link.download = 'Evaluacion.pdf';
                link.click();

                // Limpiar la URL del objeto
                URL.revokeObjectURL(url);
            } else {
                console.error('Error al descargar el PDF');
            }
        },
        OnChangeAnswer(item, listItems)
        {
            Array.from(listItems).forEach((answer,index) => {
                if(answer.checked && answer != item)
                    listItems[index].checked = false;
            });
        },
        async OnClickToFinishTest()
        {
            let count = 0;
            Array.from(this.data_test).forEach(question => {
                Array.from(question.answers).forEach(answer => {
                    if(answer.checked)
                        count = count + 1;
                });
            });

            if(count != this.data_test.length)
            {
                toastr.warning(`Debes responder todas las preguntas!`);
                return;
            }

            try
            {
                let data = new FormData();

                data.append('id_test_init', JSON.stringify(this.id_test_init));
                data.append('data_responses', JSON.stringify(this.data_test));
                data.append('publica', this.publica);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/finish_test`, { method: "POST", body: data, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode)
                {
                    case 206:

                        if(this.publica == 'true'){
                            await swal({
                                title: "¡Felicitaciones!",
                                text: rd.message,
                                type: "success",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: "Cerrar",
                                cancelButtonColor: '#ff7f00',
                                allowOutsideClick: false
                            }).then((result)=>{
                                if (!result.value) {
                                    this.$emit('listenerToFinishTest');
                                }
                            });
                        }else{
                            await swal({
                                title: "¡Felicitaciones!",
                                text: rd.message,
                                type: "success",
                                showCancelButton: true,
                                confirmButtonText: "Ir a mis certificados",
                                cancelButtonText: "Cerrar",
                                confirmButtonColor: '#1f3352',
                                cancelButtonColor: '#ff7f00',
                                allowOutsideClick: false
                            }).then((result)=>{
                                if (result.value) {
                                    this.$emit('listenerToFinishTestCertificados');
                                    // window.location.href = `${this.url}capacitaciones/menu/3`;
                                }else{
                                    this.$emit('listenerToFinishTest');
                                }
                            });
                        }
                        break;
                    case 200:
                        await swal({
                                title: "¡Felicitaciones!",
                                text: rd.message,
                                type: "success",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: "Cerrar",
                                cancelButtonColor: '#ff7f00',
                                allowOutsideClick: false
                            }).then((result)=>{
                                if (!result.value) {
                                    this.$emit('listenerToFinishTest');
                                }
                            });
                        break;
                    case 406:
                        await swal({
                            title: "¡Lo sentimos!",
                            text: rd.message,
                            type: "error",
                            showCancelButton: true,
                            confirmButtonText: "Intentar de nuevo",
                            cancelButtonText: "Cerrar",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        }).then((result)=>{
                            if (result.value) { //INTENTAR DE NUEVO
                                this.$emit('listenerToRepethTest');
                            }else{
                                this.$emit('listenerToFinishTest');
                            }
                        });
                        break;

                    default:
                        this.$emit('listenerToFinishTest');
                        break;
                }

            }
            catch (error)
            {
                loading(false);
                console.error(`Error al finalizar el examen para realizarse: ${error.message}`);

            }
        },
        FunctionClearTest()
        {
            Array.from(this.data.questions).forEach(question => {
                Array.from(question.answers).forEach((answer,index) => {
                    question.answers[index].checked = false;
                });
            });
        }

    },
    data()
    {
        return {
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
        }
    },
}
</script>

<style scoped>

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
