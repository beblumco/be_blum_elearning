const content_section =
{
    content_section:
    {
        data_module_selected: "",
        default:
        {
            label_file: "Seleccionar una imagen"
        },
        type: 0,
        mode: 1, //1: AGREGAR; 2: ACTUALIZAR
        form_data:
        {
            image:
            {
                value: "",
                required: true
            },
            ClearData()
            {
                Object.entries(this).forEach(object => {
                    const [key, value] = object;
                    if (typeof(value) != 'function')
                        this[key].value = "";
                });
            },
            ValidData()
            {
                let allow = true;
                Object.entries(this).forEach(object => {
                    const [key, value] = object;
                    if (typeof(value) != 'function') {
                        if (this[key].required) {
                            if (this[key].value == "")
                                allow = false;
                        }
                    }
                });

                return allow;
            }
        }
    }
}

const content_events =
{
    async OnClickOpenModalContent(data_module)
    {
        this.data.content_section.contents = [];
        if(data_module.tiene_contenido == 1)
        {
            try
            {
                let data_form = new FormData();

                data_form.append('id_module', data_module.id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/get_data_contents`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 202:
                        this.data.content_section.contents = data;
                        this.data.content_section.data_module_selected = data_module;
                        $('#modal_add_content').modal('show');
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                loading(false);
                console.error(`Error al agregar contenido: ${error.message}`);
            }
        }
        else
        {
            this.data.content_section.data_module_selected = data_module;
            $('#modal_add_content').modal('show');
        }
    },
    OnClickCloseModalContent()
    {
        $('#modal_add_content').modal('hide');
    },
    async OnClickAddContent()
    {
        let answer = await Swal({
            title: 'Selecciona el tipo de documento',
            input: 'select',
            inputOptions: {
                '1': 'Imagen',
                '2': 'Documento PDF',
                '3': 'Video'
            },
            inputPlaceholder: 'Seleccione un tipo',
            cancelButtonText: "Cancelar",
            confirmButtonText: "Aceptar",
            showCancelButton: true,
            inputValidator: function (value) {
                return new Promise(function (resolve, reject) {
                if (value !== '')
                    resolve();
                else
                    resolve('Debes seleccionar un tipo obligatoriamente');
                });
            }
        });

        if(answer.value)
        {
            switch (answer.value)
            {
                case '1': //IMAGEN
                    this.data.content_section.type = 1;
                    this.data.content_section.mode = 1;
                    $('#modal_add_content_image').modal('show');

                    break;
                case '2': // PDF
                    this.data.content_section.type = 2;
                    this.data.content_section.mode = 1;
                    $('#modal_add_content_pdf').modal('show');

                    break;
                case '3': //VIDEO
                    this.data.content_section.type = 3;
                    this.data.content_section.mode = 1;
                    $('#modal_add_content_video').modal('show');

                    break;

                default:
                    break;
            }
        }
    },
    async OnClickContentChange(content)
    {
        let answer = await Swal({
            title: '¿Qué desea realizar?',
            cancelButtonText: "Eliminar contenido",
            confirmButtonText: "Actualizar contenido",
            showCancelButton: true,
            allowOutsideClick: false
        });

        if(answer.value) //ACTUALIZAR CONTENIDO
        {
            this.data.content_section.data_content_selected = content;
            switch (content.tipo_contenido)
            {
                case 1: //IMAGEN
                    this.data.content_section.type = 1;
                    this.data.content_section.mode = 2;
                    $('#modal_add_content_image').modal('show');

                    break;
                case 2: // PDF
                    this.data.content_section.type = 2;
                    this.data.content_section.mode = 2;
                    $('#modal_add_content_pdf').modal('show');

                    break;
                case 3: //VIDEO
                    this.data.content_section.type = 3;
                    this.data.content_section.mode = 2;
                    $('#modal_add_content_video').modal('show');
                    break;

                default:
                    break;
            }
        }
        else
        {

            try
            {
                let data_form = new FormData();

                data_form.append('type_content', content.tipo_contenido);
                data_form.append('id_content', content.id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/delete_content`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 206:
                        this.data.content_section.contents = this.data.content_section.contents.filter(element => element.id != content.id);
                        swal({
                            title: "¡Éxitoso!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false
                        });
                        break;

                    default:
                        break;
                }
            }
            catch (error)
            {
                loading(false);
                console.error(`Error al agregar contenido: ${error.message}`);
            }
        }
    },
    OnChangeFileContenImage(file)
    {
        if(file != undefined)
        {
            this.data.content_section.default.label_file = "1 imagen cargada";
            this.data.content_section.form_data.image.value = file.target.files[0];
        }
        else
        {
            this.data.content_section.default.label_file = "Seleccionar un archivo";
            this.data.content_section.form_data.image.value = "";
        }
    },
    OnClickCloseModalContentImage()
    {
        $("#modal_add_content_image").modal('hide');
    },
    async OnClickSaveContentImage()
    {
        try
        {
            this.data.content_section.form_data.pdf.required = false;
            this.data.content_section.form_data.video.required = false;
            if(!this.data.content_section.form_data.ValidData())
            {
                swal({
                    title: "Completa todos los datos",
                    text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    allowOutsideClick: false
                });
                return;
            }
            this.data.content_section.form_data.pdf.required = true;
            this.data.content_section.form_data.video.required = true;

            let data_form = new FormData();
            Object.entries(this.data.content_section.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_training', this.id_training);
            data_form.append('id_module', this.data.content_section.data_module_selected.id);
            data_form.append('content_type', this.data.content_section.type);
            data_form.append('mode', this.data.content_section.mode);
            data_form.append('id_content', this.data.content_section.data_content_selected.id);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/save_content`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();
            loading(false);

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 206:
                    this.data.modules_section.modules.map(mo => {
                        mo.tiene_contenido = 1;
                    });
                    this.data.content_section.contents.push(data);
                    swal({
                        title: "¡Éxitoso!",
                        text: message,
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false
                    });
                    this.OnClickCloseModalContentImage();
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al agregar contenido: ${error.message}`);
        }

    },

    //PDF
    OnClickCloseModalContentPDF()
    {
        this.data.content_section.form_data.ClearData();
        this.data.content_section.default.label_file = "Seleccionar un archivo"
        $("#modal_add_content_pdf").modal('hide');
    },
    OnChangeFileContentPDF(file)
    {
        if(file != undefined)
        {
            this.data.content_section.default.label_file = "1 imagen cargada";
            this.data.content_section.form_data.pdf.value = file.target.files[0];
        }
        else
        {
            this.data.content_section.default.label_file = "Seleccionar un archivo";
            this.data.content_section.form_data.pdf.value = "";
        }
    },
    async OnClickSaveContentPDF()
    {
        try
        {
            this.data.content_section.form_data.image.required = false;
            this.data.content_section.form_data.video.required = false;
            if(!this.data.content_section.form_data.ValidData())
            {
                swal({
                    title: "Completa todos los datos",
                    text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    allowOutsideClick: false
                });
                return;
            }
            this.data.content_section.form_data.image.required = true;
            this.data.content_section.form_data.video.required = true;

            let data_form = new FormData();
            Object.entries(this.data.content_section.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_training', this.id_training);
            data_form.append('id_module', this.data.content_section.data_module_selected.id);
            data_form.append('content_type', this.data.content_section.type);
            data_form.append('mode', this.data.content_section.mode);
            data_form.append('id_content', this.data.content_section.data_content_selected.id);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/save_content`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();
            loading(false);

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 206:
                    this.data.modules_section.modules.map(mo => {
                        mo.tiene_contenido = 1;
                    });
                    this.data.content_section.contents.push(data);
                    swal({
                        title: "¡Éxitoso!",
                        text: message,
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false
                    });
                    this.OnClickCloseModalContentPDF();
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al agregar contenido: ${error.message}`);
        }

    },

    //VIDEO
    OnClickCloseModalContentVideo()
    {
        this.data.content_section.form_data.ClearData();
        $("#modal_add_content_video").modal('hide');
    },
    async OnClickSaveContentVideo()
    {
        try
        {
            this.data.content_section.form_data.image.required = false;
            this.data.content_section.form_data.pdf.required = false;
            if(!this.data.content_section.form_data.ValidData())
            {
                swal({
                    title: "Completa todos los datos",
                    text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    allowOutsideClick: false
                });
                return;
            }

            let data_form = new FormData();
            Object.entries(this.data.content_section.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_training', this.id_training);
            data_form.append('id_module', this.data.content_section.data_module_selected.id);
            data_form.append('content_type', this.data.content_section.type);
            data_form.append('mode', this.data.content_section.mode);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/save_content`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();
            loading(false);

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 206:
                        swal({
                            title: "¡Éxitoso!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false
                        });
                        this.OnClickCloseModalContentImage();
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al agregar contenido: ${error.message}`);
        }

    },

    //ORDER
    OnChangeNumberOrder(data_content)
    {
        this.data.content_section.modal_order.form_data.order.value = data_content.orden;
        this.data.content_section.data_content_selected = data_content;
        $('#modal_change_order').modal('show');
    },
    OnClickCloseModalContentOrder()
    {
        this.data.content_section.modal_order.form_data.ClearData();
        $('#modal_change_order').modal('hide');
    },
    async OnClickSaveOrderContent()
    {
        if(!this.data.content_section.modal_order.form_data.ValidData())
        {
            swal({
                title: "Completa todos los datos",
                text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                cancelButtonText: "No",
                allowOutsideClick: false
            });
            return;
        }

        try
        {
            if(Math.max.apply(Math, this.data.content_section.contents.map(function(o) { return o.orden; })) < this.data.content_section.modal_order.form_data.order.value
            || Math.min.apply(Math, this.data.content_section.contents.map(function(o) { return o.orden; })) > this.data.content_section.modal_order.form_data.order.value)
            {
                swal({
                    title: "Fuera del rango",
                    text: `Debes colocar un número que este dentro de la cantidad de contenidos que tienes`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    allowOutsideClick: false
                });
                return;
            }

            let data_form = new FormData();
            Object.entries(this.data.content_section.modal_order.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_module', this.data.content_section.data_module_selected.id);
            data_form.append('id_content', this.data.content_section.data_content_selected.id);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/update_order_content`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();
            loading(false);

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 206:
                    this.data.content_section.contents = data;
                    swal({
                        title: "¡Éxitoso!",
                        text: message,
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false
                    });
                    this.OnClickCloseModalContentOrder();
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al cambiar orden del contenido: ${error.message}`);
        }
        

    }
}

export { content_section, content_events };
