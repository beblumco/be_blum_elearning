<template>
  <div
    class="modal fade"
    id="modal_create_organization"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{
              data.mode === "create"
                ? "Crear Grupo Empresa"
                : "Editar Grupo Empresa"
            }}
          </h5>
          <button type="button" class="close" @click="closeModalCreate">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div
            class="alert alert-warning alert-dismissible fade show"
            role="alert"
            x-show="data.errorValidation"
          >
            Los campos con (*) son obligatorios, asegurese de diligenciarlos.
          </div>

          <div class="row m-auto" x-ref="formCreateOrganization">
            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Nombre: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="text"
                    class="form-control input-default"
                    x-model="data.form.nombre.val"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->

            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label"
                >Nit: <span style="color: red">*</span>
              </label>
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="number"
                    class="form-control input-default"
                    placeholder=""
                    x-model="data.form.nit.val"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Estado: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <select x-ref="sel2Estado" class="single-select">
                  <option value=" " selected>Seleccione un estado.</option>
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Sector: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <select class="single-select" x-ref="sel2Sector">
                  <option value=" " selected>Seleccione un sector.</option>
                  <template v-for="sector in data.sectors" :key="sector.id">
                    <option :value="sector.id">
                      {{ sector.nombre }}
                    </option>
                  </template>
                </select>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label"> Pais: </label>
              <div class="col-sm-12">
                <select id="" class="single-select" x-ref="sel2Pais">
                  <option value=" ">Seleccione un pais</option>
                  <template
                    v-for="(pais, index) in data.countries"
                    :key="index"
                  >
                    <option :value="pais.id">
                      {{ pais.nombre }}
                    </option>
                  </template>
                </select>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Departamento:
              </label>
              <div class="col-sm-12">
                <select id="" class="single-select" x-ref="sel2Departamento">
                  <option value=" ">Seleccione un departamento</option>
                  <template
                    v-for="(departament, index) in data.departaments"
                    :key="index"
                  >
                    <option :value="departament.id">
                      {{ departament.nombre }}
                    </option>
                  </template>
                </select>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label"> Ciudad: </label>
              <div class="col-sm-12">
                <select id="" class="single-select" x-ref="sel2Ciudad">
                  <option value=" ">Seleccione una ciudad</option>
                  <template v-for="(city, index) in data.cities" :key="index">
                    <option :value="city.id">{{ city.nombre }}</option>
                  </template>
                </select>
              </div>
            </div>

            <!-- <div class="mb-3 row col-lg-6">
                                                <label for="staticAsesor" class="col-form-label">
                                                    Asesor: </label>
                                                <div class="col-sm-12">
                                                    <select id="" class="single-select" x-ref="sel2Asesor">
                                                        <option value=" ">Seleccione un asesor</option>
                                                        <template v-for="asesor in data.asesores">
                                                            <option :value="asesor.id" >
                                                                {{asesor.nombre_com}}
                                                            </option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div> -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="createOrUpdate">
            {{ data.mode === "create" ? "Crear" : "Actualizar" }}
          </button>
          <button
            type="button"
            class="btn btn-danger light"
            @click="closeModalCreate"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style>
</style>