const training_section =
{
    training_section:
    {
        default:
        {
            main_account_id: null,
            label_file: 'Seleccionar una imagen',
            label_assign: 'Selecciona el sector',
            sector_check: true,
            centro_emp_check: false,
            typeTrainingSelect : false,
            certify_yes : true,
            certify_no : false,
            assess_yes : true,
            assess_no : false,
            estadoAgendado : true,
            estadoFinalizado : false,
            assessByModified: null,
            options_certified:[{
                id: 1,
                name: "Capacitación general",
            },
            {
                id: 2,
                name: "Módulos",
            }],
            options_type_training:[{
                id: 1,
                name: "E-Learning",
            },
            {
                id: 2,
                name: "Asistida por experto",
            },
            {
                id: 3,
                name: "Webinar",
            }],
            options_assess:[{
                id: 1,
                name: "Capacitación general",
            },
            {
                id: 2,
                name: "Módulo",
            }],
        },
        form_data:
        {
            training_name:
            {
                value: "",
                required: true
            },
            description:
            {
                value: "",
                required: false
            },
            designedBy:
            {
                value: "",
                required: false
            },
            principal_image:
            {
                value: "",
                required: false
            },
            time:
            {
                value:"",
                required: true
            },
            points:
            {
                value: "",
                required: true
            },
            price:
            {
                value: null,
                required: false
            },
            date_webinars:
            {
                value: null,
                required: false
            },
            assign:
            {
                value: 1,
                required: true
            },
            id_selected:
            {
                value: [],
                required: true
            },
            certified:
            {
                value: "",
                required: true
            },
            type_training:
            {
                value: "",
                required: true
            },
            certify:{
                value: 1,
                required: true
            },
            assess:{
                value: 1,
                required: true
            },
            estado:{
                value: null,
                required: false
            },
            assessBy:{
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

                            if ((this[key].value == '' || this[key].value == null) && this[key].value !== 0){
                                allow = false;
                            }
                            if (Array.isArray(this[key].value)){
                                if (this[key].value.length == 0) {
                                    allow = false;
                                }
                            }
                        }
                    }
                });

                return allow;
            }
        },
        show_sector: true,
        sectors: [],
        operations_center: [],
        training_type_show: true,
        training_asign_show: true,
    }
}

const training_events =
{
    filterOptions() {
        if (this.data.training_section.default.main_account_id === 2) {
            this.data.training_section.default.options_type_training =
                this.data.training_section.default.options_type_training.filter(option => option.id !== 3);
        }
    },
    OnChangeFile(file)
    {
        if(file != undefined)
        {
            const maxSize = 3 * 1024 * 1024; // 3MB en bytes

            if (file.target.files[0].size <= maxSize) {
                this.data.training_section.default.label_file = "1 imagen cargada";
                this.data.training_section.form_data.principal_image.value = file.target.files[0];
            }else{
                swal({
                    title: "Error de imagen",
                    text: `El tamaño de la imagen excede el límite de 3 MB.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
            }

        }
        else
        {
            this.data.training_section.default.label_file = "Seleccionar una imagen";
            this.data.training_section.form_data.principal_image.value = "";
        }
    },
    async OnClickContinueSection()
    {
        try
        {
            //SE CAPTURA FECHA WEBINARS SI HAY UNA
            if (this.$refs.fecha_webinar.value) {
                this.data.training_section.form_data.date_webinars.value = this.$refs.fecha_webinar.value
            }

            if(!this.data.training_section.form_data.ValidData())
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

            if (isNaN(this.data.training_section.form_data.time.value)) {
                swal({
                    title: "Información errónea",
                    text: "Debes agregar un tiempo correcto",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return;
            }

            //VALIDAR SI CAMBIO LA EVALUACION
            if (this.data.training_section.form_data.assessBy.value != this.data.training_section.default.assessByModified) {
                if( this.data.training_section.default.assessByModified != null){
                    let msj
                    if (this.data.training_section.default.assessByModified == 1) {
                        msj = "El examen de capacitación general se inactivará, para activar el examen por módulo"
                        if(this.data.training_section.form_data.assessBy.value == null){
                            msj = "El examen de capacitación general se inactivará"
                        }
                    }else if (this.data.training_section.default.assessByModified == 2){
                        msj = "El examen de los módulos se inactivará, para activar el examen por capacitación general"
                        if(this.data.training_section.form_data.assessBy.value == null){
                            msj = "El examen de los módulos se inactivará"
                        }
                    }
                    let answer = await Swal({
                        title: '¿Está seguro que desea continuar?',
                        text: msj,
                        type: "warning",
                        cancelButtonText: "No",
                        confirmButtonText: "Si, continuar",
                        showCancelButton: true,
                        showCloseButton: true,
                        confirmButtonColor: '#1f3352',
                        cancelButtonColor: '#ff7f00',
                        allowOutsideClick: false
                    });
                    if(!answer.value){
                        return
                    }
                }
            }

            if (this.$refs.fecha_webinar.value.includes("/")) {
                const fecha_webinar = this.$refs.fecha_webinar.value
                const [date, time] = fecha_webinar.split(' ');
                const [day, month, year] = date.split('/');
                const formattedDate = `${year}-${month}-${day} ${time}`;
                this.data.training_section.form_data.date_webinars.value = formattedDate
            }

            let data_form = new FormData();
            Object.entries(this.data.training_section.form_data).forEach(
                field => {
                    const [key, object] = field;
                    if (typeof(object) != 'function')
                        data_form.append(key, object.value);
                }
            );
            data_form.append('id_training', this.id_training);

            loading();
            let rs = await fetch(`${this.url}capacitaciones/administracion/save_training_section`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();
            loading(false);

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 206:
                        const msj = await swal({
                            title: "¡Éxitoso!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: '#1f3352',
                            allowOutsideClick: false
                        });
                        if (msj.value) {
                            if (this.data.training_section.form_data.estado.value == 1) {
                                window.location.href = `${this.url}capacitaciones/administracion`;
                            }else{
                                loading()
                                this.OnClickTabSelected(2);
                                loading(false)
                            }

                        }
                    break;

                default:
                    break;
            }
        }
        catch (error)
        {
            loading(false);
            console.error(`Error al continuar a la siguiente sección: ${error.message}`);
        }

    },
    async OnClickFinallySection(){
        if (this.data.training_section.form_data.type_training.value == 3) {
            const guardar = await this.guardarEstado()
            if (guardar) {
                window.location.href = `${this.url}capacitaciones/administracion`;
            }else{
                swal({
                    text: 'Para finalizar la creación del Webinar, debes agregar un video.',
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return
            }
        }else{
            if (this.data.modules_section.modules.length > 0) {
                window.location.href = `${this.url}capacitaciones/administracion`;
            }else{
                swal({
                    text: 'Para finalizar la creación, debes agregar un módulo mínimo.',
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return
            }
        }
    },

    async guardarEstado()
    {
        let data_form = new FormData();
        data_form.append('id_training', this.id_training);

        loading();
        let rs = await fetch(`${this.url}capacitaciones/administracion/guardar_estado`, { method: "POST", body: data_form, headers: {
            'X-CSRF-TOKEN': this.token
        }});
        let rd = await rs.json();
        loading(false);

        const { responseCode, message, data } = rd;

        if (responseCode == 204) {
            return false
        }else{
            return true
        }
    },

    async OnClickContinueSectionTest()
    {
        if (this.data.training_section.form_data.type_training.value == 3) {
            const guardar = await this.guardarEstado()
            if (guardar) {
                this.OnClickTabSelected(3);
            }else{
                swal({
                    text: 'Para finalizar la creación del Webinar, debes agregar un video.',
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return
            }
        }else{
            if (this.data.modules_section.modules.length > 0) {
                this.OnClickTabSelected(3);
            }else{
                swal({
                    text: 'Para continuar la creación, debes agregar un módulo mínimo.',
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                return
            }
        }
    },

    async OnClickBackSection()
    {
        this.OnClickTabSelected(1);
    },

    async OnClickBackSection2()
    {
        this.OnClickTabSelected(2);
    },

    OnChangeAssign(assign)
    {
        this.data.training_section.form_data.assign.value = assign;
        this.data.training_section.form_data.id_selected.value = [];
        if(assign == 1) //SECTOR CHECKED
        {
            this.data.training_section.default.label_assign = 'Selecciona el sector';
            this.data.training_section.default.sector_check = true;
            this.data.training_section.default.centro_emp_check = false;
        }
        else if(assign == 2) //CENTRO EMPRESA CHECKED
        {
            this.data.training_section.default.label_assign = 'Selecciona el grupo empresa';
            this.data.training_section.default.sector_check = false;
            this.data.training_section.default.centro_emp_check = true;
        }
    },

    OnChangeCertify(value)
    {
        this.data.training_section.form_data.certify.value = value;
        if(value == 1) //Si
        {
            this.data.training_section.default.certify_yes = true;
            this.data.training_section.default.certify_no = false;
            this.data.training_section.form_data.certified.required = true;
            // this.data.training_section.form_data.points.required = true;
            // this.data.training_section.form_data.points.value = "";
            if (this.data.training_section.form_data.type_training.value == 3) {
                this.data.training_section.form_data.certified.value = 1; //POR DEFECTO SE DEJA CERTIFICADO POR CAPACITACIÓN
            }
        }
        else //No
        {
            this.data.training_section.default.certify_yes = false;
            this.data.training_section.default.certify_no = true;
            this.data.training_section.form_data.certified.value = null;
            this.data.training_section.form_data.certified.required = false;
            // this.data.training_section.form_data.points.required = false;
            // this.data.training_section.form_data.points.value = 0;
            this.$refs.allow_certified.Clear();
        }
    },

    OnChangeAssess(value)
    {
        this.data.training_section.form_data.assess.value = value;
        // $(this.$refs.assessBy).val(0).change();
        if(value == 1) //Si
        {
            this.data.training_section.default.assess_yes = true;
            this.data.training_section.default.assess_no = false;
            this.data.training_section.form_data.assessBy.required = true;
            if (this.data.training_section.form_data.type_training.value == 3) {
                this.data.training_section.form_data.assessBy.value = 1; //POR DEFECTO SE DEJA CERTIFICADO POR CAPACITACIÓN
            }
        }
        else //No
        {
            this.data.training_section.default.assess_yes = false;
            this.data.training_section.default.assess_no = true;
            this.data.training_section.form_data.assessBy.value = null;
            this.data.training_section.form_data.assessBy.required = false;
            this.$refs.assess_by.Clear();
        }
    },

    OnChangeEstado(value)
    {
        this.data.training_section.form_data.estado.value = value;
        if(value == 1) //agendado
        {
            this.data.training_section.default.estadoAgendado = true;
            this.data.training_section.default.estadoFinalizado = false;
            // this.data.training_section.form_data.assessBy.required = true;
            // if (this.data.training_section.form_data.type_training.value == 3) {
            //     this.data.training_section.form_data.assessBy.value = 1; //POR DEFECTO SE DEJA CERTIFICADO POR CAPACITACIÓN
            // }
        }
        else //Finalizado
        {
            this.data.training_section.default.estadoAgendado = false;
            this.data.training_section.default.estadoFinalizado = true;
            // this.data.training_section.form_data.assessBy.value = null;
            // this.data.training_section.form_data.assessBy.required = false;
        }
    },

    OnSelectedItem(item)
    {
        this.data.training_section.form_data.certified.value = item.id;
    },
    OnSelectedItemtype(item)
    {
        this.data.training_section.form_data.type_training.value = item.id;
        if (item.id == 1) { //privado
            this.data.training_section.default.typeTrainingSelect = true
            this.data.training_section.form_data.assign.required = true
            this.data.training_section.form_data.id_selected.required = true
            //SE DEJA POR DEFECTO SELECCIONADO SECTOR
            this.data.training_section.form_data.assign.value = 1;
            this.data.training_section.default.label_assign = 'Selecciona el sector';
            this.data.training_section.default.sector_check = true;
            this.data.training_section.default.centro_emp_check = false;
            this.data.training_section.form_data.price.required = false
            this.data.training_section.form_data.price.value = null
            this.data.training_section.form_data.date_webinars.required = false
            this.data.training_section.form_data.date_webinars.value = null
            this.data.training_section.form_data.estado.value = null;
            this.data.training_section.form_data.estado.required = false;
        }else if(item.id == 2){ //ASISTIDO POR EXPERTO
            this.data.training_section.default.typeTrainingSelect = false
            this.data.training_section.form_data.assign.required = false
            this.data.training_section.form_data.id_selected.required = false
            this.data.training_section.form_data.assign.value = null;
            this.data.training_section.form_data.id_selected.value = null; //enviamos el id_select null por que es ASISTIDA
            this.data.training_section.form_data.price.required = false
            this.data.training_section.form_data.price.value = null
            this.data.training_section.form_data.date_webinars.required = false
            this.data.training_section.form_data.date_webinars.value = null
            this.data.training_section.form_data.estado.value = null;
            this.data.training_section.form_data.estado.required = false;
        }else if(item.id == 3){ //webinar
            this.data.training_section.default.typeTrainingSelect = true
            this.data.training_section.form_data.assign.required = true
            this.data.training_section.form_data.id_selected.required = true
            this.data.training_section.form_data.price.required = true
            this.data.training_section.form_data.price.value = this.data.training_section.form_data.price.value == null ? "0" : this.data.training_section.form_data.price.value;
            this.data.training_section.form_data.date_webinars.required = true
            this.data.training_section.form_data.date_webinars.value = this.data.training_section.form_data.date_webinars.value == null ? '' : this.data.training_section.form_data.date_webinars.value;
            //SE DEJA POR DEFECTO SELECCIONADO SECTOR
            this.data.training_section.form_data.assign.value = 1;
            this.data.training_section.default.label_assign = 'Selecciona el sector';
            this.data.training_section.default.sector_check = true;
            this.data.training_section.default.centro_emp_check = false;
            this.data.training_section.form_data.estado.value = this.data.training_section.form_data.estado.value == null ? 1: this.data.training_section.form_data.estado.value;
            this.data.training_section.form_data.estado.required = false;
            this.data.training_section.default.estadoAgendado = true;
            this.data.training_section.default.estadoFinalizado = false;
        }
    },
    OnSelectedAssess(item)
    {
        this.data.training_section.form_data.assessBy.value = item.id;
    },
    myChangeEventSector(val) {
        if (Array.isArray(val)) {
            this.data.training_section.form_data.id_selected.value = val;
        }
    },
    myChangeEventOperationCenter(val) {
        console.log(val);

        if (Array.isArray(val)) {
            this.data.training_section.form_data.id_selected.value = val;
            if(val.includes('0')){
                this.data.training_section.form_data.id_selected.value = []
                const idsArray = this.data.training_section.operations_center.filter(item => item.id !== '0').map(item => item.id);
                this.data.training_section.form_data.id_selected.value = idsArray
            }
        }
    },
}

export { training_section, training_events };
