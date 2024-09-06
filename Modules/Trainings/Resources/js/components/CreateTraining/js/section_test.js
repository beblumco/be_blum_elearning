const section_test =
{
    section_test:
    {
        data_test: [],
        data_module: {},
        id_training: null,
        hasTestTraining : null
    },
    form_data:
    {
        porcentajeAprobacion:
        {
            value: 75,
            required: true
        }
    }
}

const section_test_events =
{
    async OnClickOpenModalTest(data_module)
    {
        this.data.section_test.data_module = data_module;
        if(data_module.tiene_preguntas == 1)
        {
            try
            {
                let data_form = new FormData();

                data_form.append('id_module', data_module.id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/get_test_module`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 202:
                        if(data.questions.length != 0)
                        {
                            Array.from(data.questions).map(pr => {
                                pr.respuestas.map(rta => {
                                    rta.checked = (rta.checked == 0 ? false : true);
                                });
                            });
                        }
                        this.data.section_test.data_test = data.questions;
                        this.data.form_data.porcentajeAprobacion.value = data.porcentajeAprobacion;
                        $('#modal_add_test').modal('show');
                        break;

                    case 400:
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                console.error(`Error al traer preguntas: ${error.message}`);
                loading(false);
            }
        }
        else
        {
            this.data.section_test.data_test = [];
            this.data.form_data.porcentajeAprobacion.value = 75;
            $('#modal_add_test').modal('show');
        }

    },
    OnClickCloseModalTest()
    {
        $('#modal_add_test').modal('hide');
    },
    OnClickAddQuestion()
    {
        this.data.section_test.data_test.push({
            id: (this.data.section_test.data_test.length + 1),
            orden: (this.data.section_test.data_test.length + 1),
            pregunta: "",
            respuestas: []
        });

        this.FunctionScrollDown($(".dev_container_questions")[0]);

    },
    async OnClickSaveChangesTest()
    {
        let quantity_question_empties = this.data.section_test.data_test.filter(que => que.pregunta == '').length;
        let quantity_questions_without_aswers = this.data.section_test.data_test.filter(que => { if(que.respuestas.length == 0) return que.id;});
        let quantity_answers_empties = this.data.section_test.data_test.filter(que => {return que.respuestas.filter(ans => ans.respuesta == '').length});
        let quantity_one_option = this.data.section_test.data_test.filter(que => {return que.respuestas.filter(ans => ans.checked == true).length});

        if(this.data.form_data.porcentajeAprobacion.value == "" || this.data.form_data.porcentajeAprobacion.value == null)
        {
            swal({
                title: "Información incompleta",
                text: "Debes agregar el Porcentaje de aprobación para poder continuar",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }

        if(this.data.form_data.porcentajeAprobacion.value < 0 || this.data.form_data.porcentajeAprobacion.value > 100)
        {
            swal({
                title: "Información errónea",
                text: "Debes agregar un Porcentaje de aprobación entre 1 a 100 para poder continuar",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }

        if(this.data.section_test.data_test.length == 0)
        {
            swal({
                title: "Información incompleta",
                text: "Debes agregar almenos 1 pregunta para poder continuar",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }

        if(quantity_question_empties != 0)
        {
            swal({
                title: "Información incompleta",
                text: "Una/unas preguntas se encuentran vacías, es obligatorio colocar la pregunta",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }

        if(quantity_questions_without_aswers.length != 0)
        {
            swal({
                title: "Información incompleta",
                text: "Una/unas preguntas no tienes respuestas asignadas",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }

        if(quantity_answers_empties.length != 0)
        {
            swal({
                title: "Información incompleta",
                text: "Una/unas respuestas se encuentran vacías, es obligatorio colocar el texto de cada opción",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }

        if(quantity_one_option.length == 0)
        {
            swal({
                title: "Información incompleta",
                text: "Almenos 1 opción debe estar marcada como correcta",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                confirmButtonColor: '#1f3352',
                allowOutsideClick: false
            });
            return;
        }
        var boton = document.querySelector('.btn-finish-test');

        try
        {
            boton.disabled = true;

            let data_form = new FormData();
            data_form.append('data_test', JSON.stringify(this.data.section_test.data_test));

            if (!this.data.section_test.id_training) {
                data_form.append('id_module', this.data.section_test.data_module.id);
                data_form.append('has_question', this.data.section_test.data_module.tiene_preguntas);
                data_form.append('porcentajeAprobacion', this.data.form_data.porcentajeAprobacion.value);
            }else{
                data_form.append('id_capacitacion', this.data.section_test.id_training);
                data_form.append('has_question', this.data.section_test.hasTestTraining);
                data_form.append('porcentajeAprobacion', this.data.form_data.porcentajeAprobacion.value);
            }

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/add_test_module`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();
            loading(false);

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 206:
                    this.data.modules_section.modules.map(obj => {
                        if(obj.id == this.data.section_test.data_module.id)
                            obj.tiene_preguntas = 1;
                    });
                    this.OnClickCloseModalTest();
                    swal({
                        title: "¡Éxitoso!",
                        text: message,
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        confirmButtonColor: '#1f3352',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.value) {
                            if (this.data.section_test.id_training) {//solo se llena la variable si la evaluacion es por capacitacion
                                window.location.href = `${this.url}capacitaciones/administracion`;
                            }else{
                                boton.disabled = false;
                            }
                        }
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
                    }).then((result) => {
                        if (result.value) {
                            if (this.data.section_test.id_training) {//solo se llena la variable si la evaluacion es por capacitacion
                                window.location.href = `${this.url}capacitaciones/administracion`;
                            }
                        }
                    });
                    boton.disabled = false;
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            console.error(`Error al crear preguntas: ${error.message}`);
            loading(false);
            boton.disabled = false;
        }
        // this.data.section_test.data_test.forEach(question => {
        //     if(question.pregunta == '')
        //         quantity_question_empties++;

        // });

        // let element = document.querySelectorAll(`[data-id="${id_pregunta}"]`);
        // let velocity = 500;
        // let position = element[0].offsetTop;

        // $($(".dev_container_questions")[0]).animate({
        //     scrollTop: position
        // }, velocity);

    },
    FunctionScrollDown(parentElement)
    {
        let velocity = 500;
        let position = parentElement.scrollHeight;

        $($(".dev_container_questions")[0]).animate({
            scrollTop:position
        }, velocity);
    }

}

export { section_test, section_test_events };
