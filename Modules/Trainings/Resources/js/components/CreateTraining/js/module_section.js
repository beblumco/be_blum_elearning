const module_section =
{
    modules_section:
    {
        id_training : null,
        modules: [],
        hasTestTraining: null,
        assessBy: null,
        form_data:
        {
            module_name:
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

        },
        modal_module_information:
        {
            module_selected: "",
            default:
            {
                label_file: "Seleccionar una imagen"
            },
            form_data:
            {
                name:
                {
                    value: "",
                    required: true
                },
                description:
                {
                    value: "",
                    required: false
                },
                image:
                {
                    value: "",
                    required: false
                },
                percentage:
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
}

const module_events =
{
    OnClickOpenModalAddModulo(){
        $('#modal_add_modulo').modal('show');
    },
    OnClickCloseModalAddModulo(){
        $('#modal_add_modulo').modal('hide');
    },
    async OnClickAddModule()
    {
        try
        {
            if (this.data.modules_section.modules.length >= 1) {
                if (this.data.modules_section.modules[0].tipo_capacitacion == 3) {
                    swal({
                        title: "No puedes crear el módulo",
                        text: `En tipo capacitación Webinar solo permite 1 módulo`,
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        cancelButtonText: "No",
                        confirmButtonColor: '#1f3352',
                        allowOutsideClick: false
                    });
                    return;
                }
            }

            if(!this.data.modules_section.form_data.ValidData())
            {
                swal({
                    title: "Completa todos los datos",
                    text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return;
            }

            let data_form = new FormData();
            Object.entries(this.data.modules_section.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_training', this.id_training);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/save_module`, { method: "POST", body: data_form, headers: {
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
                            confirmButtonColor: '#1f3352',
                            allowOutsideClick: false
                        });

                        this.data.modules_section.modules.push(data);
                        this.data.modules_section.form_data.ClearData();
                        this.OnClickCloseModalAddModulo()
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al agregar un módulo: ${error.message}`);
        }

    },
    OnClickEditInformation(module_selected)
    {
        this.data.modules_section.modal_module_information.module_selected = module_selected;
        this.data.modules_section.modal_module_information.form_data.name.value = module_selected.nombre;
        this.data.modules_section.modal_module_information.form_data.description.value = (module_selected.descripcion == 'Sin descripción' ? '' : module_selected.descripcion);
        if(module_selected.imagen != null)
            this.data.modules_section.modal_module_information.default.label_file = "1 imagen cargada";
        else
            this.data.modules_section.modal_module_information.default.label_file = "Seleccionar una imagen";
        this.data.modules_section.modal_module_information.form_data.percentage.value = parseFloat(module_selected.porcentaje_aprueba).toFixed(0);
        $('#modal_edit_basic_information').modal('show');
    },
    OnChangeFileModule(file)
    {
        if(file != undefined)
        {
            this.data.modules_section.modal_module_information.default.label_file = "1 imagen cargada";
            this.data.modules_section.modal_module_information.form_data.image.value = file.target.files[0];
        }
        else
        {
            this.data.modules_section.modal_module_information.default.label_file = "Seleccionar una imagen";
            this.data.modules_section.modal_module_information.form_data.image.value = "";
        }

    },
    OnClickCloseModalModule()
    {
        this.data.modules_section.modal_module_information.form_data.ClearData();
        $('#modal_edit_basic_information').modal('hide');
    },
    async OnClickSaveChanges()
    {
        try
        {
            if(!this.data.modules_section.modal_module_information.form_data.ValidData())
            {
                swal({
                    title: "Completa todos los datos",
                    text: `Debes completar todos los campos que tienen el carácter (*) de color rojo`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return;
            }

            let data_form = new FormData();
            Object.entries(this.data.modules_section.modal_module_information.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_module', this.data.modules_section.modal_module_information.module_selected.id);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/update_module`, { method: "POST", body: data_form, headers: {
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
                            confirmButtonColor: '#1f3352',
                            allowOutsideClick: false
                        });

                        this.data.modules_section.modules.map(mod => {
                            if(mod.id == data.id)
                            {
                                mod.nombre = data.nombre;
                                mod.descripcion = (data.descripcion == 'Sin descripción' ? '' : data.descripcion)
                                mod.imagen = data.imagen;
                                mod.porcentaje_aprueba = data.porcentaje_aprueba;
                            }
                        });

                        this.OnClickCloseModalModule();
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al actualiar un módulo: ${error.message}`);
        }
    },
    async OnClickDeleteModule(module_selected)
    {
        try
        {
            let answer = await swal({
                title: "¿Estás seguro?",
                text: `Recuerda que una vez eliminado el módulo no se podrá recuperar, las preguntas y contenido será eliminado también`,
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
                data_form.append('id_module', module_selected.id);

                loading();
                let rs = await fetch(`${this.url}capacitaciones/administracion/delete_module`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();
                loading(false);

                const { responseCode, message, data } = rd;

                switch (responseCode)
                {
                    case 206:
                        this.data.modules_section.modules = this.data.modules_section.modules.filter(mod => mod.id != module_selected.id);
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
            console.error(`Error al eliminar módulo: ${error.message}`);
            loading(false);
        }
    },
}

export { module_section, module_events };
