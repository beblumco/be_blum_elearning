$(async () => {
    $(".single-select").select2();

    // $('.multi-select').select2({
    //   multiple: true
    // });
});

const load = (status = true) => {
    const elemLoading = $("#preloader");
    if (status) elemLoading.fadeIn(500);
    else elemLoading.fadeOut(700);
};

document.addEventListener("alpine:init", () => {

    //Grupo Empresa
    Alpine.data("CompanyGroup", () => ({
        async init() {

            this.data.modal_create = new bootstrap.Modal(
                document.getElementById("modal_create_organization")
            );

            const getSectors = async () => {
                load(true);
                const response = await fetch(ROOT_URL + "/administration/sectores");
                const data = await response.json();
                this.data.sectors = data;
                load(false);
            };

            const getCountries = async () => {
                load(true);
                const response = await fetch(ROOT_URL + "/administration/paises");
                const data = await response.json();
                this.data.countries = data;
                load(false);
            };

            const getDepartaments = async (country_id) => {
                if (country_id === undefined || country_id == " ") {
                    this.data.departaments = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/departamentos/${country_id}`
                );
                const data = await response.json();
                this.data.departaments = data;
                load(false);
            };

            const getCities = async (departament_id) => {
                if (departament_id === undefined || departament_id == " ") {
                    this.data.cities = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/ciudades/${departament_id}`
                );
                const data = await response.json();
                this.data.cities = data;
                load(false);
            };

            const refs_model = {
                sel2Estado: "estado",
                sel2Pais: "pais",
                sel2Departamento: "departamento",
                sel2Ciudad: "ciudad",
                sel2Sector: "sector",
                sel2Asesor: "asesor",
            };

            //Eventos para Select2
            Object.keys(refs_model).forEach((ref) => {
                $(this.$refs[ref]).on("select2:select", (e) => {
                    let ref_val = refs_model[ref];
                    this.data.form[ref_val].val = e.target.value;
                });
            });

            //Creo los Watch
            Object.keys(refs_model).forEach((ref) => {
                let ref_val = refs_model[ref];
                this.$watch(`data.form.${ref_val}.val`, async (value) => {
                    if (ref === "sel2Pais") await getDepartaments(value);
                    if (ref === "sel2Departamento") await getCities(value);

                    $(this.$refs[ref]).val(value).trigger("change");
                });
            });

            //Watchs para editar
            this.$watch("data.departaments", () => {
                $(this.$refs.sel2Departamento)
                    .val(this.data.form.departamento.val)
                    .trigger("change");
            });
            this.$watch("data.cities", () => {
                $(this.$refs.sel2Ciudad)
                    .val(this.data.form.ciudad.val)
                    .trigger("change");
            });

            Promise.all([getCountries(), getSectors(), this.getDataAll(), this.getAsesores()]);
        },

        data: {
            countries: [],
            cities: [],
            sectors: [],
            asesores: [],
            departaments: [],
            mode: "create",
            errorValidation: false,
            form: {
                id: { required: false, val: " " },
                nombre: { required: true, val: " " },
                nit: { required: true, val: " " },
                estado: { required: true, val: " " },
                sector: { required: true, val: " " },
                pais: { required: true, val: " " },
                departamento: { required: true, val: " " },
                ciudad: { required: true, val: " " },
                asesor: { required: false, val: " " },
            },
            modal_create: null,
            paginate: {
                cant: 10,
                total: 1,
                current_page: 1,
                links: [],
            },

            data_table: [],

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

        async getAsesores() {
            const response = await fetch(ROOT_URL + '/administration/asesores')
            const data = await response.json()
            this.data.asesores = data
        },

        //Obtengo todos los centros de costos para la tabla
        async getDataAll(url = null) {
            url = url ?? ROOT_URL + "/administration/grupo-empresa/all";

            load(true);
            const response = await fetch(
                url,
                this.data.optionsFetch({
                    filters: this.data.filters,
                    paginate: this.data.paginate,
                })
            );
            const { status, data } = await response.json();
            load(false);

            if (status != 200) {
                toastr.error("Hubo un error al obtener la información.");
                return;
            }

            this.data.paginate.current_page = data.current_page;
            this.data.paginate.total = data.total;
            this.data.paginate.links = data.links;
            this.data.data_table = data.data;
        },

        openModalCreate() {
            this.data.modal_create.show();
        },

        closeModalCreate(e) {
            this.data.errorValidation = false;
            this.data.mode = "create";
            this.data.modal_create.hide();
            this.resetDataForm();
        },

        //Limpio los campos
        resetDataForm() {
            Object.keys(this.data.form).forEach((prop) => {
                this.data.form[prop].val = "";
            });
        },

        validateForm() {
            let next = true;

            Object.keys(this.data.form).forEach((el) => {
                if (
                    this.data.form[el].val === " " &&
                    this.data.form[el].required
                ) {
                    next = false;
                }
            });
            return next;
        },

        async createOrUpdate(e) {
            this.data.errorValidation = false;

            if (!this.validateForm()) {
                this.data.errorValidation = true;
                return;
            }
            try {
                load(true);
                const response = await fetch(
                    ROOT_URL + "/administration/grupo-empresa/crear",
                    this.data.optionsFetch({
                        ...this.data.form,
                        mode: this.data.mode,
                    })
                );
                // load(false);
                const resp = await response.json();

                switch (resp.status) {
                    case 201:
                        this.getDataAll();
                        this.closeModalCreate(null);
                        swal({
                            title: "¡Exitoso!",
                            text: resp.msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oops!!", resp.msg, "warning");
                        break;

                    default:
                        Swal.fire(
                            "Oops!!",
                            "Parece que algo no salio del todo bien.",
                            "warning"
                        );
                        break;
                }
            } catch (error) {
                console.log(error);
            }
        },

        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleString("es-ES", {
                hour12: false,
                weekday: "short",
                hour: "2-digit",
                month: "long",
                year: "numeric",
            });
        },

        previousPage() {
            if (this.data.paginate.current_page === 1) return;

            this.data.paginate.current_page--
            this.getDataAll(
                ROOT_URL + "/administration/grupo-empresa/all?page=" +
                    this.data.paginate.current_page
            );
        },

        nextPage() {
            if (this.data.paginate.current_page === this.data.paginate.total)
                return;

            this.data.paginate.current_page++
            this.getDataAll(
                ROOT_URL + "/administration/grupo-empresa/all?page=" +
                    this.data.paginate.current_page
            );
        },

        numPage(num) {
            console.log(this);
            this.getDataAll(ROOT_URL + "/administration/grupo-empresa/all?page=" + num);
        },

        changeCantPaginate() {
            this.getDataAll();
        },

        //Editar el Grupo Empresa
        edit(index) {
            let data_edit = this.data.data_table[index];
            this.data.mode = "edit";

            this.data.form.id.val = data_edit.id;

            this.data.form.nombre.val = data_edit.nombre;
            this.data.form.nit.val = data_edit.nit;
            this.data.form.ciudad.val = data_edit.ciudad;
            this.data.form.estado.val = data_edit.estado_num;
            this.data.form.sector.val = data_edit.sector_id;
            this.data.form.pais.val = data_edit.pais_id;
            this.data.form.departamento.val = data_edit.departamento;
            this.data.form.ciudad.val = data_edit.ciudad_id;

            this.openModalCreate();
        },

        async deleteElem(id) {
            const confir = await Swal.fire({
                title: "Esta seguro de eliminarlo?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                const response = await fetch(
                    ROOT_URL + "/administration/grupo-empresa/eliminar",
                    this.data.optionsFetch({
                        id,
                    })
                );

                const { status, msg } = await response.json();

                switch (status) {
                    case 200:
                        swal({
                            title: "¡Exitoso!",
                            text: resp.msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        this.getDataAll();
                        break;

                    case 202:
                        Swal.fire("Oopss!", msg, "warning");
                        break;
                }
            }
        },
    }));

    //Company
    Alpine.data("Company", () => ({
        async init() {
            this.data.modal_create = new bootstrap.Modal(
                document.getElementById("modal_create_company")
            );

            const getCountries = async () => {
                load(true);
                const response = await fetch(ROOT_URL + "/administration/paises");
                const data = await response.json();
                this.data.countries = data;
                load(false);
            };

            const getDepartaments = async (country_id) => {
                if (country_id === undefined || country_id == " ") {
                    this.data.departaments = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/departamentos/${country_id}`
                );
                const data = await response.json();
                this.data.departaments = data;
                load(false);
            };

            const getCities = async (departament_id) => {
                if (departament_id === undefined || departament_id == " ") {
                    this.data.cities = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/ciudades/${departament_id}`
                );
                const data = await response.json();
                this.data.cities = data;
                load(false);
            };

            const refs_model = {
                sel2Estado: "estado",
                sel2Pais: "pais",
                sel2Departamento: "departamento",
                sel2Ciudad: "ciudad",
                sel2CentroOperacion: "centro_operacion",
            };

            //Eventos para Select2
            Object.keys(refs_model).forEach((ref) => {
                $(this.$refs[ref]).on("select2:select", (e) => {
                    let ref_val = refs_model[ref];
                    this.data.form[ref_val].val = e.target.value;
                });
            });

            //Creo los Watch
            Object.keys(refs_model).forEach((ref) => {
                let ref_val = refs_model[ref];
                this.$watch(`data.form.${ref_val}.val`, async (value) => {
                    if (ref === "sel2Pais") await getDepartaments(value);
                    if (ref === "sel2Departamento") await getCities(value);

                    $(this.$refs[ref]).val(value).trigger("change");
                });
            });

            //Watchs para editar
            this.$watch("data.departaments", () => {
                $(this.$refs.sel2Departamento)
                    .val(this.data.form.departamento.val)
                    .trigger("change");
            });
            this.$watch("data.cities", () => {
                $(this.$refs.sel2Ciudad)
                    .val(this.data.form.ciudad.val)
                    .trigger("change");
            });

            Promise.all([getCountries(), this.getDataAll()]);
        },

        data: {
            countries: [],
            cities: [],
            companies_group: [],
            departaments: [],
            mode: "create",
            errorValidation: false,
            form: {
                id: { required: false, val: " " },
                nombre: { required: true, val: " " },
                nit: { required: true, val: " " },
                dir: { required: false, val: " " },
                tel: { required: false, val: " " },
                email: { required: true, val: " " },
                estado: { required: true, val: " " },
                pais: { required: false, val: " " },
                departamento: { required: false, val: " " },
                ciudad: { required: false, val: " " },
                centro_operacion: { required: true, val: " " },
            },
            modal_create: null,
            paginate: {
                cant: 10,
                total: 1,
                current_page: 1,
                links: [],
            },

            data_table: [],

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

        async getCompaniesGroup() {
            load(true);
            const response = await fetch(ROOT_URL + "/administration/grupo-empresas");
            const data = await response.json();
            load(false);
            return data.data;
        },

        //Obtengo todos los centros de costos para la tabla
        async getDataAll(url = null) {
            url = url ?? ROOT_URL + "/administration/empresa/all";

            load(true);
            const response = await fetch(
                url,
                this.data.optionsFetch({
                    filters: this.data.filters,
                    paginate: this.data.paginate,
                })
            );
            const { status, data } = await response.json();
            load(false);

            if (status != 200) {
                toastr.error("Hubo un error al obtener la información.");
                return;
            }

            this.data.paginate.current_page = data.current_page;
            this.data.paginate.total = data.total;
            this.data.paginate.links = data.links;
            this.data.data_table = data.data;
        },

        async openModalCreate() {
            this.data.companies_group = await this.getCompaniesGroup();
            this.data.modal_create.show();
        },

        closeModalCreate(e) {
            this.data.errorValidation = false;
            this.data.mode = "create";
            this.data.modal_create.hide();
            this.resetDataForm();
        },

        //Limpio los campos
        resetDataForm() {
            Object.keys(this.data.form).forEach((prop) => {
                this.data.form[prop].val = "";
            });
        },

        validateForm() {
            let next = true;

            Object.keys(this.data.form).forEach((el) => {
                if (
                    this.data.form[el].val === " " &&
                    this.data.form[el].required
                ) {
                    next = false;
                }
            });
            return next;
        },

        async createOrUpdate(e) {
            this.data.errorValidation = false;

            if (!this.validateForm()) {
                this.data.errorValidation = true;
                return;
            }
            try {
                load(true);
                const response = await fetch(
                    ROOT_URL + "/administration/empresa/crear",
                    this.data.optionsFetch({
                        ...this.data.form,
                        mode: this.data.mode,
                    })
                );
                // load(false);
                const resp = await response.json();

                switch (resp.status) {
                    case 201:
                        this.getDataAll();
                        this.closeModalCreate(null);
                        swal({
                            title: "¡Exitoso!",
                            text: resp.msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oops!!", resp.msg, "warning");
                        break;

                    default:
                        Swal.fire(
                            "Oops!!",
                            "Parece que algo no salio del todo bien.",
                            "warning"
                        );
                        break;
                }
            } catch (error) {
                console.log(error);
            }
        },

        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleString("es-ES", {
                hour12: false,
                weekday: "short",
                hour: "2-digit",
                month: "long",
                year: "numeric",
            });
        },

        previousPage() {
            if (this.data.paginate.current_page === 1) return;

            this.data.paginate.current_page--
            this.getDataAll(
                ROOT_URL + "/administration/grupo-empresa/all?page=" +
                    this.data.paginate.current_page
            );
        },

        nextPage() {
            if (this.data.paginate.current_page === this.data.paginate.total)
                return;

            this.data.paginate.current_page++
            this.getDataAll(
                ROOT_URL + "/administration/empresa/all?page=" +
                    this.data.paginate.current_page
            );
        },

        numPage(num) {
            this.getDataAll(ROOT_URL + "/administration/empresa/all?page=" + num);
        },

        changeCantPaginate() {
            this.getDataAll();
        },

        //Editar el Grupo Empresa
        edit(index) {
            let data_edit = this.data.data_table[index];
            this.data.mode = "edit";

            this.data.form.id.val = data_edit.id;

            this.data.form.nombre.val = data_edit.nombre;
            this.data.form.nit.val = data_edit.nit;
            this.data.form.ciudad.val = data_edit.ciudad;
            this.data.form.estado.val = data_edit.estado_id;
            this.data.form.centro_operacion.val = data_edit.centro_operacion_id;
            this.data.form.pais.val = data_edit.pais_id;
            this.data.form.departamento.val = data_edit.departamentos_id;
            this.data.form.ciudad.val = data_edit.ciudad_id;
            this.data.form.email.val = data_edit.email;
            this.data.form.dir.val = data_edit.direccion;
            this.data.form.tel.val = data_edit.telefono;

            this.openModalCreate();
        },

        async deleteElem(id) {
            const confir = await Swal.fire({
                title: "Esta seguro de eliminarlo?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                const response = await fetch(
                    ROOT_URL + "/administration/empresa/eliminar",
                    this.data.optionsFetch({
                        id,
                    })
                );

                const { status, msg } = await response.json();

                switch (status) {
                    case 200:
                        this.getDataAll();
                        swal({
                            title: "¡Exitoso!",
                            text: msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oopss!", msg, "warning");
                        break;
                }
            }
        },
    }));

    //Punto Evaluacion
    Alpine.data("EvaluationPoint", () => ({
        async init() {
            this.data.modal_create = new bootstrap.Modal(
                document.getElementById("modal_create_point")
            );

            const getCountries = async () => {
                load(true);
                const response = await fetch(ROOT_URL + "/administration/paises");
                const data = await response.json();
                this.data.countries = data;
                load(false);
            };

            const getDepartaments = async (country_id) => {
                if (country_id === undefined || country_id == " ") {
                    this.data.departaments = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/departamentos/${country_id}`
                );
                const data = await response.json();
                this.data.departaments = data;
                load(false);
            };

            const getCities = async (departament_id) => {
                if (departament_id === undefined || departament_id == " ") {
                    this.data.cities = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/ciudades/${departament_id}`
                );
                const data = await response.json();
                this.data.cities = data;
                load(false);
            };

            const refs_model = {
                sel2Estado: "estado",
                sel2Pais: "pais",
                sel2Departamento: "departamento",
                sel2Ciudad: "ciudad",
                sel2Empresa: "empresa",
            };

            //Eventos para Select2
            Object.keys(refs_model).forEach((ref) => {
                $(this.$refs[ref]).on("select2:select", (e) => {
                    let ref_val = refs_model[ref];
                    this.data.form[ref_val].val = e.target.value;
                });
            });

            //Creo los Watch
            Object.keys(refs_model).forEach((ref) => {
                let ref_val = refs_model[ref];
                this.$watch(`data.form.${ref_val}.val`, async (value) => {
                    if (ref === "sel2Pais") await getDepartaments(value);
                    if (ref === "sel2Departamento") await getCities(value);

                    $(this.$refs[ref]).val(value).trigger("change");
                });
            });

            //Watchs para editar
            this.$watch("data.departaments", () => {
                $(this.$refs.sel2Departamento)
                    .val(this.data.form.departamento.val)
                    .trigger("change");
            });
            this.$watch("data.cities", () => {
                $(this.$refs.sel2Ciudad)
                    .val(this.data.form.ciudad.val)
                    .trigger("change");
            });
            this.$watch("data.companies", () => {
                $(this.$refs.sel2Empresa)
                    .val(this.data.form.empresa.val)
                    .trigger("change");
            });

            Promise.all([getCountries(), this.getDataAll()]);
        },

        data: {
            countries: [],
            cities: [],
            companies: [],
            departaments: [],
            mode: "create",
            errorValidation: false,
            form: {
                id: { required: false, val: " " },
                nombre: { required: true, val: " " },
                dir: { required: false, val: " " },
                tel: { required: false, val: " " },
                email: { required: true, val: " " },
                estado: { required: true, val: " " },
                pais: { required: false, val: " " },
                departamento: { required: false, val: " " },
                ciudad: { required: false, val: " " },
                empresa: { required: false, val: " " },
            },
            modal_create: null,
            paginate: {
                cant: 10,
                total: 1,
                current_page: 1,
                links: [],
            },
            data_table: [],
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

        //Obtengo todas las empresas para el Select
        async getCompanies() {
            load(true);
            const response = await fetch(ROOT_URL + "/administration/empresas");
            const data = await response.json();
            load(false);
            return data;
        },

        //Obtengo todos los centros de costos para la tabla
        async getDataAll(url = null) {
            url = url ?? ROOT_URL + "/administration/punto/all";

            load(true);
            const response = await fetch(
                url,
                this.data.optionsFetch({
                    filters: this.data.filters,
                    paginate: this.data.paginate,
                })
            );
            const { status, data } = await response.json();
            load(false);

            if (status != 200) {
                toastr.error("Hubo un error al obtener la información.");
                return;
            }

            this.data.paginate.current_page = data.current_page;
            this.data.paginate.total = data.total;
            this.data.paginate.links = data.links;
            this.data.data_table = data.data;
        },

        async openModalCreate() {
            this.data.companies = await this.getCompanies(this.data);
            this.data.modal_create.show();
        },

        closeModalCreate(e) {
            this.data.errorValidation = false;
            this.data.mode = "create";
            this.data.modal_create.hide();
            this.resetDataForm();
        },

        //Limpio los campos
        resetDataForm() {
            Object.keys(this.data.form).forEach((prop) => {
                this.data.form[prop].val = "";
            });
        },

        validateForm() {
            let next = true;

            Object.keys(this.data.form).forEach((el) => {
                if (
                    this.data.form[el].val === " " &&
                    this.data.form[el].required
                ) {
                    next = false;
                }
            });
            return next;
        },

        async createOrUpdate(e) {
            this.data.errorValidation = false;

            if (!this.validateForm()) {
                this.data.errorValidation = true;
                return;
            }
            try {
                load(true);
                const response = await fetch(
                    ROOT_URL + "/administration/punto/crear",
                    this.data.optionsFetch({
                        ...this.data.form,
                        mode: this.data.mode,
                    })
                );
                // load(false);
                const resp = await response.json();

                switch (resp.status) {
                    case 201:
                        this.getDataAll();
                        this.closeModalCreate(null);
                        swal({
                            title: "¡Exitoso!",
                            text: resp.msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oops!!", resp.msg, "warning");
                        break;

                    default:
                        Swal.fire(
                            "Oops!!",
                            "Parece que algo no salio del todo bien.",
                            "warning"
                        );
                        break;
                }
            } catch (error) {
                console.log(error);
            }
        },

        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleString("es-ES", {
                hour12: false,
                weekday: "short",
                hour: "2-digit",
                month: "long",
                year: "numeric",
            });
        },

        previousPage() {
            if (this.data.paginate.current_page === 1) return;

            this.data.paginate.current_page--
            this.getDataAll(
                ROOT_URL + "/administration/punto/all?page=" +
                    this.data.paginate.current_page
            );
        },

        nextPage() {
            if (this.data.paginate.current_page === this.data.paginate.total)
                return;

            this.data.paginate.current_page++
            this.getDataAll(
                ROOT_URL + "/administration/punto/all?page=" +
                    this.data.paginate.current_page
            );
        },

        numPage(num) {
            this.getDataAll(ROOT_URL + "/administration/punto/all?page=" + num);
        },

        changeCantPaginate() {
            this.getDataAll();
        },

        //Editar el Grupo Empresa
        edit(index) {
            let data_edit = this.data.data_table[index];
            this.data.mode = "edit";

            this.data.form.id.val = data_edit.id;

            this.data.form.nombre.val = data_edit.nombre;
            this.data.form.ciudad.val = data_edit.ciudad;
            this.data.form.estado.val = data_edit.estado_id;
            this.data.form.empresa.val = data_edit.unidad_id ?? " ";
            this.data.form.pais.val = data_edit.pais_id;
            this.data.form.departamento.val = data_edit.departamentos_id;
            this.data.form.ciudad.val = data_edit.ciudad_id;
            this.data.form.email.val = data_edit.email ?? "";
            this.data.form.dir.val = data_edit.direccion;
            this.data.form.tel.val = data_edit.telefono;

            this.openModalCreate();
        },

        async deleteElem(id) {
            const confir = await Swal.fire({
                title: "Esta seguro de eliminarlo?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                const response = await fetch(
                    ROOT_URL + "/administration/punto/eliminar",
                    this.data.optionsFetch({
                        id,
                    })
                );

                const { status, msg } = await response.json();

                switch (status) {
                    case 200:
                        this.getDataAll();
                        swal({
                            title: "¡Exitoso!",
                            text: msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oopss!", msg, "warning");
                        break;
                }
            }
        },
    }));

    //Usuarios
    Alpine.data("User", () => ({

        async init() {
            this.data.modal_create = new bootstrap.Modal(
                document.getElementById("modal_create_user")
            );

            const getCountries = async () => {
                load(true);
                const response = await fetch(ROOT_URL + "/administration/paises");
                const data = await response.json();
                this.data.countries = data;
                load(false);
            };

            const getDepartaments = async (country_id) => {
                if (country_id === undefined || country_id == " ") {
                    this.data.departaments = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/departamentos/${country_id}`
                );
                const data = await response.json();
                this.data.departaments = data;
                load(false);
            };

            const getCities = async (departament_id) => {
                if (departament_id === undefined || departament_id == " ") {
                    this.data.cities = [];
                    return;
                }
                load(true);
                const response = await fetch(
                    ROOT_URL + `/administration/ciudades/${departament_id}`
                );
                const data = await response.json();
                this.data.cities = data;
                load(false);
            };

            const getProfiles = async () => {
                load(true)
                const response = await fetch(
                    ROOT_URL + `/administration/perfiles`
                );
                const data = await response.json();
                this.data.profiles = data
                load(false)
            }

            const getCompanies = async () => {
                load(true)
                const response = await fetch(
                    ROOT_URL + `/administration/empresas`
                );
                const data = await response.json();
                this.data.companies = data;
                load(false)
            }

            const refs_model = {
                sel2Estado: "estado",
                sel2Pais: "pais",
                sel2Departamento: "departamento",
                sel2Ciudad: "ciudad",
                sel2Punto: "punto",
                sel2Perfiles: "profile",
                sel2Lider: "lider_empresas"
            };

            //Eventos para Select2
            Object.keys(refs_model).forEach((ref) => {
                $(this.$refs[ref]).on("select2:select", (e) => {
                    let ref_val = refs_model[ref];
                    if(ref_val === 'punto'){
                        this.data.form[ref_val].val = $(this.$refs[ref]).val();
                    }else if(ref_val === 'lider_empresas'){
                        this.data.form[ref_val].val = $(this.$refs[ref]).val();
                    }else{
                        this.data.form[ref_val].val = e.target.value;
                    }
                });
            });

            //Creo los Watch
            Object.keys(refs_model).forEach((ref) => {
                let ref_val = refs_model[ref];
                this.$watch(`data.form.${ref_val}.val`, async (value) => {
                    if (ref === "sel2Pais") await getDepartaments(value);
                    if (ref === "sel2Departamento") await getCities(value);

                    $(this.$refs[ref]).val(value).trigger("change");
                });
            });

            //Watchs para editar
            this.$watch("data.departaments", () => {
                $(this.$refs.sel2Departamento)
                    .val(this.data.form.departamento.val)
                    .trigger("change");
            });
            this.$watch("data.cities", () => {
                $(this.$refs.sel2Ciudad)
                    .val(this.data.form.ciudad.val)
                    .trigger("change");
            });
            this.$watch("data.evaluation_points", () => {
                $(this.$refs.sel2Punto)
                    .val(this.data.form.punto.val)
                    .trigger("change");
            });
            this.$watch("data.profiles", () => {
                $(this.$refs.sel2Profiles)
                    .val(this.data.form.profile.val)
                    .trigger("change");
            });
            this.$watch("data.companies", () => {
                $(this.$refs.sel2Lider)
                    .val(this.data.form.lider_empresas.val)
                    .trigger("change");
            });

            Promise.all([getCountries(), this.getDataAll(), getCompanies(), getProfiles()]);
        },

        data: {
            countries: [],
            cities: [],
            evaluation_points: [],
            departaments: [],
            companies: [],
            profiles: [],
            mode: "create",
            errorValidation: false,
            user_points: [],
            form: {
                id: { required: false, val: " " },
                nombre: { required: true, val: " " },
                tel: { required: false, val: " " },
                email: { required: true, val: " " },
                estado: { required: true, val: " " },
                pais: { required: false, val: " " },
                departamento: { required: false, val: " " },
                ciudad: { required: false, val: " " },
                punto: { required: false, val: [] },
                password: { required: false, value: "" },
                profile: { required: false, val: "" },
                lider_empresas: { required: false, val: [] }
            },
            modal_create: null,
            paginate: {
                cant: 10,
                total: 1,
                current_page: 1,
                links: [],
            },
            data_table: [],
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

        //Obtengo todas los puntos para el Select
        async getEvaluationPoints() {
            load(true);
            const response = await fetch(ROOT_URL + "/administration/puntos-evaluacion");
            const data = await response.json();
            load(false);
            return data;
        },

        //Obtengo todos los centros de costos para la tabla
        async getDataAll(url = null) {
            url = url ?? ROOT_URL + "/administration/usuarios/all";

            load(true);
            const response = await fetch(
                url,
                this.data.optionsFetch({
                    filters: this.data.filters,
                    paginate: this.data.paginate,
                })
            );
            const { status, data } = await response.json();
            load(false);

            if (status != 200) {
                toastr.error("Hubo un error al obtener la información.");
                return;
            }

            this.data.paginate.current_page = data.current_page;
            this.data.paginate.total = data.total;
            this.data.paginate.links = data.links;
            this.data.data_table = data.data;
        },

        async openModalCreate() {
            this.data.evaluation_points = await this.getEvaluationPoints();
            this.data.modal_create.show();
            this.data.form.profile.val = 4
        },

        closeModalCreate(e) {
            this.data.errorValidation = false;
            this.data.mode = "create";
            this.data.modal_create.hide();
            this.resetDataForm();
        },

        //Limpio los campos
        resetDataForm() {
            this.data.form.password.required = true
            Object.keys(this.data.form).forEach((prop) => {
                if(prop === 'punto' || prop === 'lider_empresas'){
                    this.data.form[prop].val = [];
                }else {
                    this.data.form[prop].val = "";
                }
            });
        },

        validateForm() {
            let next = true;

            Object.keys(this.data.form).forEach((el) => {
                if (
                    this.data.form[el].val === " " &&
                    this.data.form[el].required
                ) {
                    next = false;
                }
            });
            return next;
        },

        async createOrUpdate(e) {
            this.data.errorValidation = false;

            if (!this.validateForm()) {
                this.data.errorValidation = true;
                return;
            }
            try {
                load(true);
                const response = await fetch(
                    ROOT_URL + "/administration/usuarios/crear",
                    this.data.optionsFetch({
                        ...this.data.form,
                        mode: this.data.mode,
                    })
                );
                // load(false);
                const resp = await response.json();

                switch (resp.status) {
                    case 201:
                        this.getDataAll();
                        this.closeModalCreate(null);
                        swal({
                            title: "¡Exitoso!",
                            text: resp.msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oops!!", resp.msg, "warning");
                        break;

                    default:
                        Swal.fire(
                            "Oops!!",
                            "Parece que algo no salio del todo bien.",
                            "warning"
                        );
                        break;
                }
            } catch (error) {
                console.log(error);
            }
        },

        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleString("es-CO", {
                hour12: false,
                day: "2-digit",
                hour: "2-digit",
                month: "long",
                year: "numeric",
            });
        },

        previousPage() {
            if (this.data.paginate.current_page === 1) return;

            this.data.paginate.current_page--
            this.getDataAll(
                ROOT_URL + "/administration/usuarios/all?page=" +
                    this.data.paginate.current_page
            );
        },

        nextPage() {
            if (this.data.paginate.current_page === this.data.paginate.total)
                return;

            this.data.paginate.current_page++
            this.getDataAll(
                ROOT_URL + "/administration/usuarios/all?page=" +
                    this.data.paginate.current_page
            );
        },

        numPage(num) {
            this.getDataAll(ROOT_URL + "/administration/usuarios/all?page=" + num);
        },

        changeCantPaginate() {
            this.getDataAll();
        },

        async getEvaluationPointsUser(user_id) {
            load(true)
            const response = await fetch(ROOT_URL + '/administration/usuarios/puntos/' + user_id)
            const res = await response.json()
            load(false)
            return res.data
        },

        //Editar el Grupo Empresa
        async edit(index) {
            this.data.form.password.required = false
            this.data.form.password.val      = ''
            let data_edit = this.data.data_table[index];
            this.data.mode = "edit";

            this.data.form.id.val = data_edit.id;

            this.data.form.nombre.val       = data_edit.nombre;
            this.data.form.ciudad.val       = data_edit.ciudad;
            this.data.form.estado.val       = data_edit.estado_id;
            this.data.form.pais.val         = data_edit.pais_id;
            this.data.form.departamento.val = data_edit.departamento_id;
            this.data.form.ciudad.val       = data_edit.ciudad_id;
            this.data.form.email.val        = data_edit.email ?? "";
            this.data.form.tel.val          = data_edit.telefono;
            this.data.form.punto.val        = await this.getEvaluationPointsUser(data_edit.id)
            this.data.form.profile.val      = data_edit.id_grupo
            this.data.form.lider_empresas.val = data_edit.empresas_lider.map(el => el.id)

            this.openModalCreate();
        },

        async deleteElem(id) {
            const confir = await Swal.fire({
                title: "Esta seguro de eliminarlo?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                const response = await fetch(
                    ROOT_URL + "/administration/usuarios/eliminar",
                    this.data.optionsFetch({
                        id,
                    })
                );

                const { status, msg } = await response.json();

                switch (status) {
                    case 200:
                        this.getDataAll();
                        swal({
                            title: "¡Exitoso!",
                            text: msg,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        break;

                    case 202:
                        Swal.fire("Oopss!", msg, "warning");
                        break;
                }
            }
        },

        async viewPoints(user_id) {
            this.data.user_points = []
            load(true)
            const response = await fetch(ROOT_URL + '/administration/usuario_puntos/' + user_id)
            const data = await response.json()
            load(false)
            this.data.user_points = data
            if(data.length > 0) $(this.$refs.modalPointsUser).modal('show');
        },

    }));
});
