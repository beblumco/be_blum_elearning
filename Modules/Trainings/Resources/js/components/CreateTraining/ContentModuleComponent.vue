<template>
  <div class="col-xl-3 col-lg-6 col-sm-6 dev-cursor-pointer" v-if="data_content.type_text != 'Video'">
    <div :class="`widget-stat card`" style="background-color: #002F54 !important;">
    <!-- <div :class="`widget-stat card bg-${data_content.class}`"> -->
      <div class="card-body p-2 justify-content-center d-flex">
        <div class="col-lg-12 d-flex flex-wrap">
          <div class="col-lg-12 p-0 text-center">
            <p class="m-0 dev-color-white">{{ data_content.type_text }}</p>
          </div>
          <div class="col-lg-12 no_padding">
            <div class="media">
              <span class="icono">
                <i :class="`flaticon-381-${data_content.icon}`"></i>
              </span>
            </div>
          </div>
          <div v-if="!only_view" id="opciones" class="col-lg-6 p-0 d-flex">
            <div class="col-lg-9 p-0 d-flex" @click.stop="OnClickChangeOrder">
              <div id="ver" class="media justify-content-start">
                <p  class="dev-normal-text">
                  <i
                    class="la la-eye dev-fonts-icon dev-fonts-color-white"
                    @click.stop="OnClickViewContent()"
                  ></i>
                </p>
              </div>
              <div id="editar" class="media justify-content-start">
                <p  class="dev-normal-text">
                  <i
                    class="la la-pencil dev-fonts-icon dev-fonts-color-white"
                    @click.stop="OnClickActionContent(1)"
                  ></i>
                </p>
              </div>
              <div id="eliminar" class="media justify-content-start">
                <p  class="dev-normal-text">
                  <i
                    class="la la-trash dev-fonts-icon dev-fonts-color-white"
                    @click.stop="OnClickActionContent(2)"
                  ></i>
                </p>
              </div>
              <div id="orden"
                class="col-lg-12 p-0 d-flex justify-content-end"
                @click.stop="OnClickChangeOrder"
              >
                <div class="media">
                  <p  class="dev-normal-text">{{ data_content.orden }}</p>
                </div>
              </div>
            </div>
          </div>
          <div v-if="only_view" class="col-lg-12 p-0 d-flex justify-content-center">
             <div id="ver2" class="media justify-content-start">
                <p  class="dev-normal-text">
                  <i
                    class="la la-eye dev-fonts-icon dev-fonts-color-white"
                    @click.stop="OnClickViewContent()"
                  ></i>
                </p>
              </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    data_content: Object,
    only_view: Number,
  },
  mounted() {
  },
  methods: {
    OnClickChangeOrder() {
      this.$emit("change_order", this.data_content);
    },
    OnClickActionContent(action) {
      console.log(action);
      this.$emit("action_content", [this.data_content, action]);
    },
    OnClickViewContent() {
      if(this.data_content.tipo_contenido !=3){
        let ruta = `${this.url}${this.data_content.ruta_contenido_completa}`;
        this.$emit("view_content", ruta);
      }else{
        this.$emit("view_content", this.data_content.ruta_contenido);
      }

    },
  },
  data() {
    return {
        url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
    };
  },
};
</script>

<style scoped>
.dev-color-white {
  color: white;
}

.dev-normal-text {
  font-weight: bold;
  color: white;
  font-size: 25px;
  margin: 0;
}
.dev-normal-text:hover {
  color: #2bc155;
}

.no_padding{
    padding-left: 0px;
    padding-right: 0px;
}

.icono{
  margin: auto;
}
</style>
