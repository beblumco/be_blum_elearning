<template>
  <div class="container-fluid">
    <div class="d-flex justify-content-between">
      <div>
        <h3>DETALLE</h3>
      </div>
      <div class="d-flex">
        <button
          class="btn btn-barra-naranja mr-2"
          @click="descargarDocumento(1)"
        >
          <i class="flaticon-381-file-2"></i><span class="ml-2">PDF</span>
        </button>
        <!-- <button
          @click="descargarDocumento(2)"
          class="btn btn-barra-naranja mr-2"
        >
          <i class="flaticon-381-file-2"></i><span class="ml-2">EXCEL</span>
        </button>-->
        <button
          @click="CopyLink(datos.link)"
          class="btn btn-barra-naranja mr-2"
        >
          <i class="flaticon-381-link"></i><span class="ml-2">COMPARTIR</span>
        </button>
      </div>
    </div>
    <br />
    <div class="contenido">
      <div class="d-flex justify-content-between mr-4 ml-4">
        <table class="datosPrincipales" width="80%">
          <tbody>
            <tr>
              <td>
                <b>Fecha: </b><span>{{ datos.fecha }}</span>
              </td>
              <td>
                <b>Centro de costo: </b><span>{{ datos.centro_costo }}</span>
              </td>
              <td v-if="datos.nombreArea">
                <b>Área: </b> <span>{{ datos.nombreArea }}</span>
              </td>
            </tr>
            <tr>
              <td>
                <b>Auditor: </b> <span>{{ datos.auditor }}</span>
              </td>
              <td>
                <b>Coordinado con: </b>
                <span>{{ datos.responsable }}</span>
                <img
                  style="max-width: 80px; max-height: 80px; margin-left: 10px"
                  :src="'https://klaxen.co/storage/Signatures/' + datos.firma"
                  alt=""
                />
              </td>
            </tr>
          </tbody>
        </table>
        <div
          v-if="datos.auditoria_id != 65"
          class="resultadoFinal d-flex justify-content-center"
        >
          <div>
            <div style="height: 40px; font-size: 12px !important">
              <p style="margin-top: 10px">Resultado final</p>
            </div>
            <div>
              <p style="font-size: 30px !important">
                {{ datos.resultado }}
              </p>
            </div>
          </div>
        </div>
        <div
          v-if="datos.auditoria_id == 65"
          class="d-flex justify-content-center"
        >
          <div style="height: 130px; !important"></div>
        </div>
      </div>
      <br />
      <div class="datosPrincipales">
        <b>Comentarios: </b>
        <span>{{ datos.observacion }}</span>
      </div>
      <hr v-if="datos.auditoria_id != 65" />
      <div
        v-if="datos.auditoria_id != 65"
        class="d-flex justify-content-center"
      >
        <div v-for="item in datos.resultadosPorCategoria" :key="item.id">
          <div class="resultadoPorCategoria d-flex justify-content-center">
            <div>
              <div style="font-size: 12px !important; height: 70px">
                <p>{{ item.CATEGORIA }}</p>
              </div>
              <div>
                <p>
                  <b>{{ item.RESULTADO }}%</b>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr v-if="datos.auditoria_id != 65" />
      <div v-if="datos.auditoria_id != 65" class="d-flex justify-content-end">
        <div class="col-4">
          <Select_Savk
            :options="respuestas"
            :maxItem="20"
            placeholder="Filtro por respuesta"
            @selected="OnSelectedFiltro"
          />
        </div>
      </div>
      <br />
      <table
        v-for="(item, index) in datos.detalle"
        :key="item.id"
        class="table"
        :id="index"
      >
        <thead>
          <tr class="theadProcedimientos">
            <th style="text-transform: uppercase" colspan="5">
              {{ item[0].categoria }}
            </th>
          </tr>
          <tr class="theadDetalle" style="">
            <th><b>ÍTEM</b></th>
            <th><b>RESULTADO</b></th>
            <th><b>OBSERVACIÓN</b></th>
            <th><b>EVIDENCIAS</b></th>
            <!--<th><b>PLAN DE ACCIÓN</b></th>-->
          </tr>
        </thead>
        <tbody>
          <tr v-for="pregunta in item" :key="pregunta.categoria_id">
            <td>{{ pregunta.pregunta }}{{ item[0].categoria }}</td>
            <td>
              <b v-if="pregunta.respuesta == 'No cumple'" style="color: red">{{
                pregunta.respuesta
              }}</b>
              <b v-if="pregunta.respuesta == 'Cumple'" style="color: green">{{
                pregunta.respuesta
              }}</b>
              <b
                v-if="
                  pregunta.respuesta == 'No aplica' ||
                  pregunta.respuesta == 'N/A'
                "
                >{{ pregunta.respuesta }}</b
              >
            </td>
            <td>{{ pregunta.observacion }}</td>
            <td>
              <div
                @click="OnClickOpenModal(pregunta.imagenes)"
                v-if="pregunta.imagenes.length != 0"
                class="rounded-circle modalImage"
                :style="
                  'background-image: url(' +
                  'https://klaxen.co/imagenes/respuesta_auditoria/' +
                  pregunta.imagenes[0] +
                  ')'
                "
              >
                <div class="imageText">
                  <span
                    ><b>{{ pregunta.imagenes.length }}</b>
                  </span>
                </div>
              </div>
            </td>
            <!--<td>Plan de accion</td>-->
          </tr>
        </tbody>
      </table>

      <div class="col-md-12">
        <table
          :class="
            datos.respuestaLuminometria != null
              ? 'table table-responsive'
              : 'table'
          "
        >
          <thead v-if="datos.respuestaActividadComun != null">
            <tr class="theadProcedimientos">
              <th style="text-transform: uppercase">{{ datos.actividad }}</th>
            </tr>
          </thead>
          <tbody
            v-for="(item, index) in datos.respuestaActividadComun"
            :key="item.id"
            :id="index"
          >
            <tr class="theadDetalle">
              <td><b>DESCRIPCÍON: </b> {{ item.descripcion_general }}</td>
            </tr>
            <tr class="d-flex justify-content-center">
              <div
                class="d-flex flex-column-reverse mr-4"
                v-for="(imagen, index) in item.detalle"
                :key="index"
                style="padding-top: 10px; padding-bottom: 10px"
              >
                <span align="center">{{ imagen.comentario }}</span>
                <img
                  style="
                    height: 300px;
                    width: 300px;
                    max-width: 300px;
                    max-height: 300px;
                  "
                  :src="
                    imagen.imagen == null
                      ? url + 'img/imagen_no_disponible.png'
                      : 'https://klaxen.co/storage/Actividades/' + imagen.imagen
                  "
                  alt=""
                />
              </div>
            </tr>
          </tbody>
          <tbody>
            <table
              class="table"
              v-for="(item, index) in datos.respuestaLuminometria"
              :key="item.id"
              :id="index"
            >
              <thead>
                <tr
                  class="theadProcedimientos"
                  style="text-transform: uppercase"
                >
                  <th
                    v-if="
                      index == 'manos' &&
                      datos.respuestaLuminometria.manos.length != 0
                    "
                    colspan="13"
                  >
                    Luminometrías de {{ index }}
                  </th>
                  <th
                    v-if="
                      index != 'manos' &&
                      datos.respuestaLuminometria.superficies.length != 0
                    "
                    colspan="13"
                  >
                    Luminometrías de {{ index }}
                  </th>
                </tr>
                <tr
                  v-if="
                    index == 'manos' &&
                    datos.respuestaLuminometria.manos.length != 0
                  "
                  class="theadDetalle"
                >
                  <th>Área</th>
                  <th>Cargo</th>
                  <th>Responsable</th>
                  <th>Antes</th>
                  <th>Después</th>
                  <th>Descripción</th>
                  <th>Item</th>
                  <th>Producto</th>
                  <th>Cant. Concentración</th>
                  <th>Unidad medida</th>
                  <th>Cant. cantidad</th>
                  <th>Unidad medida</th>
                  <th>Evidencias</th>
                </tr>
                <tr
                  v-if="
                    index != 'manos' &&
                    datos.respuestaLuminometria.superficies.length != 0
                  "
                  class="theadDetalle"
                >
                  <th>Área</th>
                  <th>Superficie</th>
                  <th>Antes</th>
                  <th>Después</th>
                  <th>Descripción</th>
                  <th>Item</th>
                  <th>Producto</th>
                  <th>Cant. Concentración</th>
                  <th>Unidad medida</th>
                  <th>Cant. cantidad</th>
                  <th>Unidad medida</th>
                  <th>Evidencias</th>
                </tr>
              </thead>
              <tbody v-if="index == 'manos'">
                <tr v-for="(dato, index) in item" :key="index">
                  <td>{{ dato.AREA }}</td>
                  <td>{{ dato.CARGO_PERSONA }}</td>
                  <td>{{ dato.NOMBRE_RESPONSABLE }}</td>
                  <td>{{ dato.ANTES }}</td>
                  <td>{{ dato.DESPUES }}</td>
                  <td>
                    {{ dato.DESCRIPCION }}
                  </td>
                  <td>
                    {{ dato.ITEM_PRODUCTO_MANOS }}
                  </td>
                  <td>
                    {{ dato.PRODUCTO_MANOS }}
                  </td>
                  <td>
                    {{ dato.CANTIDAD_CONCENTRACION_MANOS }}
                  </td>
                  <td>
                    {{ dato.UNIDAD_MEDIDA_CONCENTRACION_MANOS }}
                  </td>
                  <td>
                    {{ dato.CANT_CANTIDAD_MANOS }}
                  </td>
                  <td>
                    {{ dato.UNIDAD_MEDIDA_CANT_MANOS }}
                  </td>
                  <td>
                    <div
                      @click="OnClickOpenModal(dato.imagenes)"
                      v-if="dato.imagenes.length != 0"
                      class="rounded-circle modalImage"
                      :style="
                        'background-image: url(' +
                        'https://klaxen.co/imagenes/respuesta_auditoria/' +
                        dato.imagenes[0] +
                        ')'
                      "
                    >
                      <div class="imageText">
                        <span
                          ><b>{{ dato.imagenes.length }}</b>
                        </span>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
              <tbody v-if="index != 'manos'">
                <tr v-for="(dato, index) in item" :key="index">
                  <td>{{ dato.AREA }}</td>
                  <td>{{ dato.SUPERFICIE }}</td>
                  <td>{{ dato.ANTES }}</td>
                  <td>{{ dato.DESPUES }}</td>
                  <td>{{ dato.DESCRIPCION }}</td>
                  <td>
                    {{ dato.ITEM_PRODUCTO_SUPERFICIES }}
                  </td>
                  <td>
                    {{ dato.PRODUCTO_SUPERFICIES }}
                  </td>
                  <td>
                    {{ dato.CANTIDAD_CONCENTRACION_SUPERFICIES }}
                  </td>
                  <td>
                    {{ dato.UNIDAD_MEDIDA_CONCENTRACION_SUPERFICIES }}
                  </td>
                  <td>
                    {{ dato.CANT_CANTIDAD_SUPERFICIES }}
                  </td>
                  <td>
                    {{ dato.UNIDAD_MEDIDA_CONCENTRACION_SUPERFICIES }}
                  </td>
                  <td>
                    <div
                      @click="OnClickOpenModal(dato.imagenes)"
                      v-if="dato.imagenes.length != 0"
                      class="rounded-circle modalImage"
                      :style="
                        'background-image: url(' +
                        'https://klaxen.co/imagenes/respuesta_auditoria/' +
                        dato.imagenes[0] +
                        ')'
                      "
                    >
                      <div class="imageText">
                        <span
                          ><b>{{ dato.imagenes.length }}</b>
                        </span>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </tbody>
        </table>
      </div>

      <table class="table" v-if="datos.respuestaCapacitacion != null">
        <thead>
          <tr class="theadProcedimientos" style="text-transform: uppercase">
            <th colspan="4">Capacitación</th>
          </tr>
        </thead>
        <tbody>
          <tr class="theadDetalle" style="text-align: center">
            <td>Capacitación</td>
            <td>Observación</td>
            <td>Asistentes</td>
            <td>Evidencias</td>
          </tr>
          <tr style="text-align: center">
            <td>{{ datos.respuestaCapacitacion.capacitacion }}</td>
            <td>{{ datos.respuestaCapacitacion.observacion }}</td>
            <td>{{ datos.respuestaCapacitacion.asistentes.length }}</td>
            <td>
              <div
                @click="
                  OnClickOpenModal(
                    datos.respuestaCapacitacion.imagenes,
                    'https://klaxen.co/storage/Capacitaciones/'
                  )
                "
                v-if="datos.respuestaCapacitacion.imagenes.length != 0"
                class="rounded-circle modalImage"
                :style="
                  'background-image: url(' +
                  'https://klaxen.co/storage/Capacitaciones/' +
                  datos.respuestaCapacitacion.imagenes[0] +
                  ')'
                "
              >
                <div class="imageText">
                  <span
                    ><b>{{ datos.respuestaCapacitacion.imagenes.length }}</b>
                  </span>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
        <tbody>
          <tr class="theadDetalle" style="text-align: center">
            <td>Asistentes</td>
            <td>Identificación</td>
            <td colspan="2">Certificado</td>
          </tr>
          <tr
            v-for="item in datos.respuestaCapacitacion.asistentes"
            :key="item.id"
            style="text-align: center"
          >
            <td>{{ item.nombre }}</td>
            <td>{{ item.numero_documento }}</td>
            <td colspan="2">
              <a
                class="badge badge-primary mr-2"
                style="color: white"
                target="_blank"
                :href="url + 'accompaniment/descargar-certificacion/' + item.id"
              >
                Descargar
              </a>
            </td>
          </tr>
        </tbody>
      </table>

      <hr />
      <br />
      <br />
      <br />
      <br />
      <br />
    </div>

    <!-- MODAL IMAGENES -->
    <div
      class="modal fade"
      id="modal_image"
      tabindex="-1"
      role="dialog"
      style="overflow: scroll"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Imágenes</h5>
            <button type="button" class="close" @click="OnClickCloseModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body" style="overflow: auto">
            <div class="d-flex justify-content-between">
              <div class="mr-4" v-for="(item, index) in imagenes" :key="index">
                <img
                  style="max-width: 450px; max-height: 450px"
                  :src="urlImagenes + item"
                  alt=""
                />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger light"
              @click="OnClickCloseModal"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- END MODAL IMAGENES-->
  </div>
</template>

<script>
import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";
export default {
  mounted() {
    this.datos = this.data_auditoria;
  },
  props: {
    data_auditoria: Object,
  },
  components: {
    Select_Savk,
  },
  data() {
    return {
      datos: [],
      token: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      imagenes: [],
      urlImagenes: "https://klaxen.co/imagenes/respuesta_auditoria/",
      filters: {
        respuesta: null,
      },
      respuestas: [
        { id: 1, name: "Cumple" },
        { id: 2, name: "No Cumple" },
        { id: 3, name: "No Aplica" },
        { id: 4, name: "Todas las respuestas" },
      ],
    };
  },
  methods: {
    async getDataFilter() {
      //load(true);
      const response = await fetch(
        `${this.url}` +
          "accompaniment/detalle-auditoria-filtro/" +
          this.datos.id,
        {
          method: "POST",
          headers: {
            "Content-type": "application/json; charset=UTF-8",
            "X-CSRF-Token": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
          body: JSON.stringify({
            filters: this.filters,
          }),
        }
      );
      const { status, data } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.datos.detalle = data;
    },
    CopyLink(url) {
      var c = document.createElement("textarea");
      c.value = url;
      c.style.maxWidth = "0px";
      c.style.maxHeight = "0px";
      document.body.appendChild(c);

      c.focus();
      c.select();
      document.execCommand("copy");
      document.body.removeChild(c);

      toastr.success("Link para compartir copiada.");

      window.scrollTo(0, 0);
    },
    async OnClickOpenModal(imagenes, url = null) {
      if (url != null) {
        this.urlImagenes = url;
      }
      this.imagenes = imagenes;
      $("#modal_image").modal("show");
    },
    async OnClickCloseModal() {
      this.imagenes = [];
      $("#modal_image").modal("hide");
    },
    OnSelectedFiltro(item) {
      if (this.datos.id != null) {
        this.filters.respuesta = item.id;
        this.getDataFilter();
      }
    },
    async descargarDocumento(tipo) {
      loading();
      const rs = await fetch(
        `${this.url}` +
          "accompaniment/descargar-documento/" +
          this.datos.id +
          "/" +
          tipo,
        {
          method: "GET",
          headers: {
            "Content-type": "application/json; charset=UTF-8",
            "X-CSRF-Token": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
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
        link.download =
          tipo == 1 ? "Detalle auditoria.pdf" : "Detalle auditoria.xlsx";
        link.click();

        // Limpiar la URL del objeto
        URL.revokeObjectURL(url);
      } else {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }
    },
  },
};
</script>

<style scoped>
.dev-icon-back {
  font-size: 25px;
  cursor: pointer;
}

.dev-icon-certificate {
  color: #fe634e;
  font-size: 24px;
  cursor: pointer;
}

.dev-bg {
  background: rgba(254, 99, 78, 0.05);
  border-radius: 1.25rem;
  padding: 14px 18px 14px 18px;
}

.btn-menu {
  margin-right: 5px;
  margin-top: 5px;
  margin-bottom: 5px;
}

.menu-cap {
  background-color: white;
  border-radius: 1.25rem;
  /* padding: 8px; */
  padding-left: 5px;
  padding-right: 5px;
  margin-bottom: 20px;
  margin-left: 0px !important;
  margin-right: 0px !important;
  align-items: center;
}

.div-busqueda {
  display: flex;
  margin-left: auto;
  width: 50%;
  margin-top: 5px;
  margin-bottom: 5px;
}

.btn-barra {
  padding: 0.6rem 0.8rem;
  background-color: #e6f0ff;
  border-color: #e6f0ff;
  color: #002f54;
  box-shadow: none !important;
}

.btn-barra:hover {
  background-color: #002f54;
  border-color: #002f54;
  color: #e6f0ff;
  box-shadow: none !important;
}

.btn-barra-activo {
  background-color: #002f54;
  border-color: #002f54;
  color: #e6f0ff;
  box-shadow: none !important;
}

.btn-barra-naranja a {
  color: #e6f0ff;
}

.btn-barra-naranja {
  background-color: #ff7f00;
  border-color: #ff7f00;
  color: white;
  box-shadow: none !important;
}

.btn-barra-naranja:hover {
  background-color: #ff8000e0;
  color: white;
}

@media (max-width: 1125px) {
  .div-busqueda {
    /* margin-left: auto; */
    width: 100%;
    margin-top: 5px;
    margin-bottom: 5px;
  }
}
.dev-fonts-icon {
  font-size: 30px;
  margin-left: 8px;
}

.form-control-busqueda {
  height: 45px !important;
}

.titulo-card {
  height: 47px; /* Altura del div */
  display: -webkit-box;
  -webkit-line-clamp: 6; /* Número máximo de líneas a mostrar */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.titulo-card:hover {
  height: auto;
  display: flex;
  overflow: auto;
  min-height: 47px;
}

.datosPrincipales {
  text-align: left;
  font-size: 16px;
}

.contenido {
  text-align: left;
  background-color: white;
  border-radius: 12px;
  padding: 5px;
}

.resultadoFinal {
  background: #002f54;
  color: white;
  width: 120px;
  text-align: center;
  /* padding-top: 3.6rem; */
  border-radius: 1.4rem;
  font-size: 30px;
  margin: 4px;
  transition: all 0.3s;
  cursor: pointer;
}

.resultadoPorCategoria {
  background: #fff8f6;
  color: #e48029;
  width: 150px;
  text-align: center;
  /* padding-top: 3.6rem; */
  border-radius: 1.4rem;
  font-size: 20px;
  margin: 4px;
  transition: all 0.3s;
  cursor: pointer;
}

.theadProcedimientos {
  background: #ff7f00;
  color: #fff8f6;
  text-align: center;
}
.theadDetalle {
  background: #e6f0ff;
  color: black;
  text-align: center;
}

.imageText {
  background: #ff7f00;
  border-radius: 10rem;
  margin-left: 80%;
  margin-top: -5%;
  width: 1rem;
  height: 1rem;
  font-size: 10px;
  font-weight: bold;
  color: white;
  text-align: center;
}

.modalImage {
  height: 50px;
  width: 50px;
  background-repeat: no-repeat;
  background-position: 50%;
  border-radius: 50%;
  background-size: auto;
  cursor: pointer;
}
</style>