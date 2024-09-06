<template>
  <div class="container-fluid">
    <div class="flex-wrap mb-2">
      <div
        class="d-flex flex-wrap mb-2 align-items-end justify-content-end row menu-cap"
      >
        <div class="container_bnts d-flex" id="container_bnts">
          <div class="d-flex col-12 mr-2"></div>
        </div>
        <div class="div-busqueda d-flex justify-content-end">
          <a
            class="badge badge-success mr-2 mb-2 mt-2"
            style="color: white"
            href="javascript:void(0)"
            @click="downloadReportTraining()"
          >
            Descargar Excel
          </a>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <div class="tab-content">
          <div class="table-responsive">
            <table class="table card-table display dataTablesCard">
              <thead>
                <tr class="">
                  <th>Categoría</th>
                  <!-- <th>Fecha</th> -->
                  <th>Capacitación</th>
                  <th>Tiempo (Horas)</th>
                  <!-- <th>Asistentes</th> -->
                  <th>Certificados</th>
                  <th>Creada por</th>
                  <th>Asignación</th>
                  <th>Grupo empresa</th>
                  <th>Empresa</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="result in data.data_table" :key="result.id">
                  <td>{{ result.categoria }}</td>
                  <!-- <td>{{ result.fecha }}</td> -->
                  <td>{{ result.capacitacion }}</td>
                  <td>{{ result.duracion * result.certificados }}</td>
                  <!-- <td>{{ result.asistentes }}</td> -->
                  <td>{{ result.certificados }}</td>
                  <td>{{ result.asesor }}</td>
                  <td>{{ result.asignacion }}</td>
                  <td>{{ result.grupo_empresa }}</td>
                  <td>{{ result.empresa }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination pagination-circle">
              <li class="page-item page-indicator">
                <a
                  class="page-link"
                  href="javascript:void(0)"
                  @click="previousPage"
                >
                  <i class="la la-angle-left"></i
                ></a>
              </li>

              <template
                v-for="(link, index) in data.paginate.links"
                :key="index"
              >
                <template
                  v-if="
                    !(link.label.indexOf('Previous') > -1) &&
                    !(link.label.indexOf('Next') > -1)
                  "
                >
                  <li class="page-item" :class="link.active ? 'active' : ''">
                    <a
                      class="page-link"
                      href="javascript:void(0)"
                      @click="numPage(link.label)"
                      >{{ link.label }}</a
                    >
                  </li>
                </template>
              </template>

              <li class="page-item page-indicator">
                <a
                  class="page-link"
                  href="javascript:void(0)"
                  @click="nextPage"
                >
                  <i class="la la-angle-right"></i
                ></a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  created() {
    this.getDataAll();
  },
  data() {
    return {
      permisos: JSON.parse(localStorage.getItem("permisos")),
      token: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      data: {
        paginate: {
          cant: 10,
          total: 1,
          current_page: 1,
          links: [],
        },
        data_table: [],
      },
    };
  },
  methods: {
    async getDataAll(url = null) {
      url = url ?? `${this.url}` + "informes/get-report-training";

      //load(true);
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-type": "application/json; charset=UTF-8",
          "X-CSRF-Token": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
        body: JSON.stringify({
          search: this.search,
          filters: this.data.filters,
          paginate: this.data.paginate,
        }),
      });
      const { status, data, usuarios, centros_costos } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.data.paginate.current_page = data.current_page;
      this.data.paginate.total = data.total;
      this.data.paginate.links = data.links;
      this.data.data_table = data.data;
    },
    previousPage() {
      loading(true);
      if (this.data.paginate.current_page === 1) return;

      this.data.paginate.current_page--;
      this.getDataAll(
        `${this.url}` +
          "informes/get-report-training?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    nextPage() {
      loading(true);
      if (this.data.paginate.current_page === this.data.paginate.total) return;

      this.data.paginate.current_page++;
      this.getDataAll(
        `${this.url}` +
          "informes/get-report-training?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    async numPage(num) {
      loading(true);
      await this.getDataAll(
        `${this.url}` + "informes/get-report-training?page=" + num
      );
      loading(false);
    },

    async downloadReportTraining() {
      loading();
      let rs = await fetch(
        this.url + "informes/descargar-excel-entrenamiento",
        {
          method: "POST",
          body: JSON.stringify({
            search: this.search,
            filters: this.data.filters,
            paginate: this.data.paginate,
          }),
          headers: {
            "X-CSRF-TOKEN": this.token,
          },
        }
      );
      loading(false);

      if (rs.status == 200) {
        const blob = await rs.blob();
        const url = URL.createObjectURL(blob);

        // Crear un enlace de descarga y hacer clic en él para iniciar la descarga
        const link = document.createElement("a");
        link.href = url;
        link.download = "Informe Entrenamiento.xlsx";
        link.click();

        // Limpiar la URL del objeto
        URL.revokeObjectURL(url);
      } else {
        const rd = await rs.json();
        console.error("Error al descargar el Excel: " + rd.msg);
        toastr.error("Error al descargar el Excel: " + rd.msg);
      }
    },
  },
};
</script>

<style>
</style>
