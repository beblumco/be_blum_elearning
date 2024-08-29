// Jquery functions by plugins
$(() => {
    $(".single-select").select2();
    $(".multi-select").select2({
        multiple: true,
    });
});

const load = (status = true) => {
    const elemLoading = $("#preloader");
    if (status) elemLoading.fadeIn(500);
    else elemLoading.fadeOut(700);
};

document.addEventListener("alpine:init", () => {
    Alpine.data("drive", () => ({
        async init() {
            document.oncontextmenu = function () {
                return false;
            };
            this.modal_share = new bootstrap.Modal(this.$refs["modal-share"]);
            this.modal_rename = new bootstrap.Modal(this.$refs.modalRename);
            this.modal_preview_img = new bootstrap.Modal(
                this.$refs.modalPreviewImage
            );
            this.modal_properties = new bootstrap.Modal(
                this.$refs.modalProperties
            );
            this.zone = this.$refs["content-zone-drop"];

            //Scroll infinito
            // window.addEventListener("scroll", this.OnScroll);

            //Actualizo la barra de espacio de almacenamiento
            this.refreshStorageFront();

            this.$watch("storage_company_group.percentage", () => {
                this.refreshStorageFront();
            });

            const refs_model = {
                sel2CompanyGroup: "company_group",
                sel2Company: "company",
                sel2Point: "evaluation_point",
            };

            //Eventos para Select2
            Object.keys(refs_model).forEach((ref) => {
                $(this.$refs[ref]).on("select2:select", (e) => {
                    let ref_val = refs_model[ref];
                    this.new_folder[ref_val] = $(this.$refs[ref]).val();
                });

                //Evento cuando se elimina una opción del Multiselect
                $(this.$refs[ref]).on("select2:unselect", (e) => {
                    let ref_val = refs_model[ref];
                    this.new_folder[ref_val] = $(this.$refs[ref]).val();
                });
            });

            //Creo los Watch
            Object.keys(refs_model).forEach((ref) => {
                let ref_val = refs_model[ref];
                this.$watch(`new_folder.${ref_val}`, async (value) => {
                    $(this.$refs[ref]).val(value).trigger("change");
                });
            });

            this.loadStorage();
            this.loadData();
        },
        open: false,
        data_all_back: [],
        data_all: [],
        share: {},
        paginate: {
            current_page: 1,
            last_page: 1,
            next_page_url: ''
        },
        companies_group: [],
        companies: [],
        evaluation_points: [],
        modal_share: null,
        modal_rename: null,
        mode_share: false,
        modal_preview_img: null,
        zone: null,
        modal_share_title: "",
        storage_company_group: {
            text_current_size: "...",
            percentage: 1,
            total_size: 0,
            current_size: 0,
            text_total: "0 GB",
        },
        current_folder_id: null,
        folder_parent_data: null,
        history_folder_nav: [
            {
                id: 0,
                name: "Principal",
                active: true,
            },
        ],
        new_folder: {
            id: null,
            name: "",
            permissions: ["read", "create", "update", "share", "delete"],
            company_group: ["all"],
            company: ["all"],
            evaluation_point: ["all"],
            parent: "",
        },
        new_file: {
            files: new FormData(),
            folder_id: "",
        },
        excluded_files: [
            "application/zip",
            "application/vnd.debian.binary-package",
            "application/x-executable",
        ],
        file_ext_ico: {
            xls: ROOT_URL + "/img/xls_ico.svg",
            xlsx: ROOT_URL + "/img/xls_ico.svg",
            ppt: ROOT_URL + "/img/ppt.svg",
            pdf: ROOT_URL + "/img/pdf_ico.svg",
            docx: ROOT_URL + "/img/doc_ico.svg",
            png: ROOT_URL + "/img/image.svg",
        },
        filter: "",
        rename: {
            id: null,
            name: "",
            type: "",
        },
        modal_properties: null,
        property: {
            name: "",
            owner: "",
            tamano: "",
            count_files: 0,
            created_at: "",
            updated_at: "",
            count_folders: 0,
        },
        preview_img: "",

        optionsFetch: (data) => ({
            method: "POST",
            headers: {
                "Content-type": "application/json; charset=UTF-8",
                "X-CSRF-Token": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(data),
        }),

        refreshStorageFront() {
            this.$refs.storage.style.width = `${this.storage_company_group.percentage}%`;
        },
        closeModalNewFolder() {
            this.resetModalShare();
            this.resetNewFolderModel();
            this.mode_share = false;
            this.modal_share.hide();
        },
        resetModalShare() {
            this.new_folder.id = null;
            this.new_folder.company_group = "";
            this.new_folder.company = "";
        },
        resetNewFolderModel() {
            this.new_folder.permissions = [
                "create",
                "update",
                "share",
                "delete",
                "read",
            ];
            this.new_folder.company_group = ["all"];
            this.new_folder.company = ["all"];
            this.new_folder.evaluation_point = ["all"];
        },
        async loadSelectsModalShare() {
            await Promise.all([
                this.getCompnayGroup(),
                this.getCompanies(),
                this.getEvaluationPoints(),
            ]);
        },
        async openModalNewFolder() {
            if (!await this.validateFolderPermissions(this.current_folder_id, 'write')) return;
            this.mode_share = false; //True para modo "Compartir"
            this.modal_share_title = "Nueva Carpeta";
            this.new_folder.name = "";
            await this.loadSelectsModalShare();
            this.modal_share.show();
        },
        async openModalShare(name, folder_id) {
            if (!await this.validateFolderPermissions(folder_id, 'share')) return;
            this.mode_share = true;
            this.new_folder.id = folder_id;
            this.modal_share_title = "Compartir - " + name;
            //Cargo Grupo empresa, empresa y puntos de evaluación
            await this.loadSelectsModalShare();

            //Obtengo los permisos actuales de la carpeta
            const response = await fetch(
                ROOT_URL + "/drive/obtener-permisos/" + folder_id
            );
            const res = await response.json();
            this.new_folder.permissions = res.permissions;
            this.new_folder.company_group = res.company_group;
            this.new_folder.evaluation_point = res.points;
            this.new_folder.company = res.companies;
            this.modal_share.show();
        },
        async getCompnayGroup() {
            load(true);
            const response = await fetch(ROOT_URL + "/drive/grupo-empresa");
            const res = await response.json();
            load(false);
            this.companies_group = res.data;
        },
        async getCompanies() {
            load(true);
            this.companies = [];
            const response = await fetch(ROOT_URL + "/drive/empresas");
            const res = await response.json();
            load(false);
            this.companies = res.data;
        },
        async getEvaluationPoints() {
            load(true);
            this.evaluation_points = [];
            const response = await fetch(ROOT_URL + "/drive/puntos");
            const res = await response.json();
            load(false);
            this.evaluation_points = res.data;
        },
        validateFormModalShare() {
            let next = true;
            Object.keys(this.new_folder).forEach((prop) => {
                if (
                    this.new_folder[prop]?.length === 0 &&
                    prop != "parent" &&
                    !this.mode_share
                )
                    next = false;
            });

            return next;
        },
        //Crear carpetas o cambiar permisos
        async save() {
            if (!this.validateFormModalShare()) {
                toastr.warning(
                    "Debe diligenciar todos los campos obligatorios (*)"
                );
                return;
            }
            load(true);
            this.new_folder.parent = this.current_folder_id;
            const response = await fetch(
                ROOT_URL + "/drive/nueva-carpeta",
                this.optionsFetch(this.new_folder)
            );
            const { status, msg, folder } = await response.json();
            this.closeModalNewFolder();
            load(false);
            switch (status) {
                case 200:
                    toastr.success(msg);
                    if (folder != null)
                        this.data_all = [folder].concat(this.data_all);
                    break;

                case 500:
                    toastr.error(msg);
                    break;

                default:
                    toastr.warning(msg);
                    break;
            }
            this.loadStorage();
            this.data_all = [];
            this.data_all_back = [];
            this.loadData()
        },
        removeDragData(ev) {
            if (ev.dataTransfer.items) {
                ev.dataTransfer.items.clear();
            } else {
                ev.dataTransfer.clearData();
            }
        },
        _activeZone(status = false) {
            if (status) {
                this.zone.classList.add("zone-active");
            } else {
                this.zone.classList.remove("zone-active");
            }
        },
        formatBytes(bytes, decimals = 2, text = false) {
            if (!+bytes) return "0 Bytes";

            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = [
                "Bytes",
                "KB",
                "MB",
                "GB",
                "TB",
                "PB",
                "EB",
                "ZB",
                "YB",
            ];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            if (text) {
                return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]
                    }`;
            }

            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm));
        },
        async dropHandler(ev) {
            //prevent item from running by default
            ev.preventDefault();
            if (!await this.validateFolderPermissions(this.current_folder_id, 'write')){
                this.zone.classList.remove("zone-active");
                return;
            }

            if (ev.dataTransfer.items) {
                this._activeZone(false);
                for (var i = 0; i < ev.dataTransfer.items.length; i++) {
                    if (ev.dataTransfer.items[i].kind === "file") {
                        var file = ev.dataTransfer.items[i].getAsFile();
                        if (file.size > 5242880) {
                            toastr.warning(
                                `El archivo ${file.name} supera el límite de 5MB.`
                            );
                        } else if (this.excluded_files.includes(file.type)) {
                            toastr.warning(
                                "El tipo de archivo no está permitido."
                            );
                        } else {
                            if (this.validateStorageFree(file.size)) {
                                this.new_file.files.append("files[]", file);
                                this.new_file.files.append(
                                    "files_obj[]",
                                    JSON.stringify({
                                        name: file.name,
                                        type: file.type,
                                        size: file.size,
                                    })
                                );
                                this.new_file.folder_id =
                                    this.current_folder_id;
                            } else {
                                toastr.warning(
                                    "Supera la capacidad maxíma de almacenamiento."
                                );
                            }
                        }
                    }
                }
                if (this.new_file.files.get("files[]")) this.uploadFiels();
            }

            this.removeDragData(ev);
        },
        dragOverHandler(ev) {
            ev.preventDefault();
            if (!this.zone.classList.contains("zone-active")) {
                this._activeZone(true);
            }
        },
        async uploadFiels() {
            if(!await this.validateFolderPermissions(this.current_folder_id, 'write')){
                this.new_file.files.delete("files[]");
                this.new_file.files.delete("folder_id");
                this.new_file.files.delete("files_obj[]");
                this.new_file.folder_id = "";

                return;
            }
            this.new_file.files.append("folder_id", this.new_file.folder_id);
            load(true);
            const response = await fetch(ROOT_URL + "/drive/subir-archivos", {
                headers: {
                    "X-CSRF-Token": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                method: "POST",
                body: this.new_file.files,
            });
            try {
                const { status, files } = await response.json();
                load(false);
                if (status === 200) {
                    files.forEach((file) => {
                        this.data_all.push(file);
                    });
                }
            } catch (error) {
                load(false);
                toastr.error("Ha ocurrido un error interno");
            }
            this.new_file.files.delete("files[]");
            this.new_file.files.delete("folder_id");
            this.new_file.files.delete("files_obj[]");
            this.new_file.folder_id = "";
            this.loadStorage();
        },
        async loadData(url = "/drive/data") {
            load(true);
            const response = await fetch(
                ROOT_URL + url,
                this.optionsFetch({
                    current_folder: this.current_folder_id,
                })
            );
            const { status, data, paginate, share } = await response.json();

            switch (status) {
                case 200:
                    data.forEach(el => {
                        this.data_all.push(el)
                        this.data_all_back.push(el)
                        this.share = share
                    })
                    this.paginate.current_page = paginate?.current_page;
                    this.paginate.last_page = paginate.last_page;
                    this.paginate.next_page_url = paginate.next_page_url ?? 0
                    break;

                default:
                    break;
            }
            load(false);
        },
        async loadStorage() {
            const response = await fetch(ROOT_URL + "/drive/almacenamiento");
            const { status, total_size, current_size, percentage } =
                await response.json();

            this.storage_company_group.total_size = total_size;
            this.storage_company_group.current_size = current_size;
            this.storage_company_group.percentage = percentage;
            this.storage_company_group.text_current_size = `Usado: ${this.formatBytes(
                current_size,
                2,
                true
            )}`;
            this.storage_company_group.text_total = this.formatBytes(
                total_size,
                2,
                true
            );
        },
        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleString("es-CO", {
                hour12: false,
                day: "2-digit",
                month: "long",
                year: "numeric",
            });
        },
        getIcoFiles(ext) {
            return this.file_ext_ico[ext] ?? ROOT_URL + "/img/other.svg";
        },
        async deleteDoc(id, type) {
            if (!await this.validateFolderPermissions(id, 'delete')) return;
            const confir = await Swal.fire({
                type: "warning",
                text: "Una vez elimine este documento no podra recuperar la información, está seguro?",
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: `No`,
            });

            if (confir.value) {
                load(false);
                const response = await fetch(
                    ROOT_URL + "/drive/eliminar",
                    this.optionsFetch({
                        id,
                        type,
                    })
                );
                const { status, msg } = await response.json();
                if (status != 200) toastr.warning(msg);

                //Limpio los archivos que hay en pantalla actualmente
                this.data_all = []
                this.data_all_back = []
                this.loadData();
                this.loadStorage();
            }
        },
        async openModalRename(id, name, type) {
            if (!await this.validateFolderPermissions(id, 'edit', true)) return;
            this.rename.id = id;
            this.rename.name = name;
            this.rename.type = type;
            this.modal_rename.show();
        },
        async saveRename() {
            if (this.rename.name.length === 0) {
                toastr.warning("Debe escribir un nombre");
                return;
            }
            load(true);
            const response = await fetch(
                ROOT_URL + "/drive/renombrar",
                this.optionsFetch(this.rename)
            );
            const { status, msg } = await response.json();
            load(false);

            if (status != 200) {
                toastr.warning(msg);
                return;
            }
            //Renombro el archivo en la variable data_all
            let index = this.data_all
                .map((el) => {
                    return el.id;
                })
                .indexOf(this.rename.id);
            this.data_all[index].nombre = this.rename.name;

            //Limpio varaible rename
            this.rename.name = "";
            this.rename.id = null;
            this.rename.type = null;
            this.modal_rename.hide();
        },
        search(e) {
            if (this.filter.length != 0) {
                let obj = this.data_all.find((o, i) => {
                    if (o.nombre.toLowerCase().includes(this.filter))
                        return true;
                });

                this.data_all = obj != undefined ? [obj] : obj;
            } else {
                this.data_all = Object.assign([{}], this.data_all_back);
            }
        },
        _resetActiveNav() {
            this.history_folder_nav.forEach((el) => {
                el.active = false;
            });
        },
        async open(elem) {
            if (!await this.validateFolderPermissions(elem.id, 'read', true)) return;
            switch (elem.type) {
                case "folder":
                    this.data_all = []
                    this.data_all_back = []
                    this.current_folder_id = elem.id;
                    this._resetActiveNav();
                    this.history_folder_nav.push({
                        id: elem.id,
                        name: elem.nombre,
                        active: true,
                    });
                    this.loadData();
                    // this.getPermissionsFolderParent()
                    break;

                case "file":
                    if (elem.tipo.includes("image")) {
                        this.preview_img = ROOT_URL + "/storage/" + elem.ruta;
                        this.modal_preview_img.show();
                    }
                    break;
            }
        },
        backFolder(id) {
            this.current_folder_id = id === 0 ? null : id;
            this.data_all = []
            this.data_all_back = []
            this.folder_parent_data = null
            this._resetActiveNav();
            let index = this.history_folder_nav
                .map((el) => {
                    return el.id;
                })
                .indexOf(id);
            //elimino el historico
            this.history_folder_nav.splice(
                index + 1,
                this.history_folder_nav.length - 1
            );

            this.loadData();
            setTimeout(() => {
                this.history_folder_nav[index].active = true;
            }, 1000);
        },
        //Obtengo la data de la carpeta padre con sus permisos
        async getPermissionsFolderParent(id) {
            try {
                load(true)
                const response = await fetch(
                    ROOT_URL + "/drive/obtener-permisos-carpeta/" + id
                );
                const resp = await response.json();
                load(false)
                return resp
            } catch (error) {
                return null
            }
        },
        btnUploadFile() {
            const input = this.$refs.uploadFiles;
            input.click();
        },
        async loadInputFiles(e) {
            let files = e.target.files;

            Object.values(files).forEach(async (file) => {
                if (file.size > 5242880) {
                    toastr.warning(
                        `El archivo ${file.name} supera el límite de 5MB.`
                    );
                } else if (this.excluded_files.includes(file.type)) {
                    toastr.warning("El tipo de archivo no está permitido.");
                } else {
                    if (this.validateStorageFree(file.size)) {
                        this.new_file.files.append("files[]", file);
                        this.new_file.files.append(
                            "files_obj[]",
                            JSON.stringify({
                                name: file.name,
                                type: file.type,
                                size: file.size,
                            })
                        );
                        this.new_file.folder_id = this.current_folder_id;
                    } else {
                        toastr.warning(
                            "Supera la capacidad maxíma de almacenamiento."
                        );
                    }
                }
            });
            if (this.new_file.files.get("files[]")) this.uploadFiels();
            this.$refs.uploadFiles.value = "";
        },
        validateStorageFree(file_size) {
            let next = true;

            let total = file_size + this.storage_company_group.current_size;
            if (total > this.storage_company_group.total_size) next = false;
            return next;
        },
        async openProperties(data) {
            this.property.name = data.nombre;
            this.property.owner = data.propietario_nombre;
            this.property.tamano = this.formatBytes(data.tamano, 2, true);
            this.property.created_at = this.formatDate(data.created_at);
            this.property.updated_at = this.formatDate(data.updated_at);
            this.property.count_files = data.cant_archivos ?? 1;
            this.property.count_folders =
                data.type === "folder" ? await this.countFolders(data.id) : 0;
            this.modal_properties.show();
        },
        async countFolders(folder_id) {
            try {
                const response = await fetch(
                    ROOT_URL + "/drive/cantidad-subcarpetas/" + folder_id
                );
                const resp = await response.json();

                return resp.data;
            } catch (error) {
                return 0;
            }
        },
        downloadFile(elem) {
            window.open(ROOT_URL + "/drive/descargar-archivo/" + elem.id, "_blank");
        },
        OnScroll() {
            if (
                document.body.scrollHeight - window.innerHeight ===
                window.scrollY
            ) {
                if (this.paginate.current_page < this.paginate.last_page) {
                    this.paginate.current_page++
                    this.loadData(this.paginate.next_page_url)
                }
            }
        },
        //Valido los permisos de "Compartir" de las carpetas
        validateRenderPermmissions(item) {
            let visible = null
            if (item.type === 'folder') {
                item.share.forEach(el => {
                    if(!visible){
                        visible = this.share.gempresa.includes(el.centro_operacion_id) ||
                            this.share.empresa.includes(el.unidad_id) ||
                            this.share.punto.includes(el.punto_evaluacion_id) ? true : false
                    }
                })
            }else{
                visible = true;
            }

            return visible
        },
        //Valido los permisos de acciones que tiene la carpeta
        async validateFolderPermissions(folder_id, access_type, subfolder = false) {
            let next = true;
            let folder = []

            if (folder_id != null && subfolder) {
                folder = this.data_all.filter(el => {
                    return (el.id === folder_id && el.type == 'folder') ? true : false
                })
            }else if(this.current_folder_id != null) {
                folder = await this.getPermissionsFolderParent(folder_id)
                if(folder === null) {
                    toastr.warning('No se pudo leer los permisos de la carpeta.')
                   return false;
                }
                folder = [folder]
            }else {
                return next
            }

            if (Number(this.$refs.userAuth.value) === folder[0].propietario_id) return next
            switch (access_type) {
                case 'read':
                    next = folder[0].permissions[0].lectura === 0 ? false : true
                    break;
                case 'write':
                    next = folder[0].permissions[0].escritura === 0 ? false : true
                    break;
                case 'delete':
                    next = folder[0].permissions[0].eliminar === 0 ? false : true
                    break;
                case 'edit':
                    next = folder[0].permissions[0].editar === 0 ? false : true
                    break;
                case 'share':
                    next = folder[0].permissions[0].compartir === 0 ? false : true
                    break;
            }
            if (!next) toastr.warning('No tiene permisos para realizar esta accioón.')
            return next;
        },
    }));
});
