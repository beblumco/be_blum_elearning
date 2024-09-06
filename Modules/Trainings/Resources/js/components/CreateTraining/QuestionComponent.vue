<template>
    <div class="col-lg-12">
        <div class="card border-0 pb-0" style="background: #F7F7F7">
            <div class="card-body">
                <div id="DZ_W_Todo4" class="">
                    <ul class="timeline mb-3">
                        <li>
                            <div class="timeline-panel mb-2 d-flex">
                                <div class="col-lg-1 p-0 ml-3">
                                    <button class="btn btn-danger dev-font-11 btn-sm" @click="OnClickRemoveQuestion">Remover</button>
                                </div>
                                <div class="media-body col-lg-8">
                                    <div class="form-group">
                                        <input type="text" class="form-control" v-model="data_test.pregunta" :placeholder="`Pregunta # ${data_test.orden}...`" />
                                    </div>
                                </div>
                            </div>
                                <div class="card border-0 pb-0" style="background: #FAFAFA">
                                    <div class="card-body">
                                         <div class="col-lg-3 mb-3 p-0 ml-1">
                                    <button class="btn btn-primary dev-font-11" @click="OnClickAddAnswer">Agregar opción</button>
                                </div>
                             <div v-for="(answer,key) in data_test.respuestas" :key="key" class="d-flex mb-2 col-lg-12 align-items-center ml-5">
                                <div class="custom-control custom-checkbox checkbox-success check-lg mr-2">
                                    <input type="checkbox" class="custom-control-input" :id="`checkbox_${answer.id}_${answer.id}`" required="" v-model="answer.checked">
                                    <label class="custom-control-label" :for="`checkbox_${answer.id}_${answer.id}`"></label>
                                </div>

                                <div class="align-items-center col-lg-8 p-0">
                                    <input type="text" class="form-control" :placeholder="`Opción respuesta`" v-model="answer.respuesta" />
                                </div>

                                <div class="col-lg-3 p-0 ml-1">
                                    <button class="btn btn-danger dev-font-11 btn-sm" @click="OnClickRemoveAnswer(answer)">Remover</button>
                                </div>

                            </div>
                                    </div>
                                </div>
                        </li>


                    </ul>

                </div>

            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        data_test: Object
    },
    mounted()
    {
    },
    methods: {
        OnClickAddAnswer()
        {
            this.data_test.respuestas.push({
                id: (new Date()).getTime(),
                orden: (this.data_test.respuestas.length + 1),
                respuesta: "",
                checked: false
            });
            // this.$emit('scroll_down', $(".dev_container_questions")[0]);
        },
        OnClickRemoveQuestion()
        {
            this.$emit('remove_question', this.data_test);
        },
        OnClickRemoveAnswer(answer)
        {
            this.data_test.respuestas = this.data_test.respuestas.filter(ans => ans.id != answer.id);
        }
    },
    data()
    {
        return {

        }
    },
}
</script>

<style scoped>
</style>
