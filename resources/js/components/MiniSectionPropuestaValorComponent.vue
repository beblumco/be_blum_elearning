<template>
  <li>
      <div class="timeline-panel">

        <div class="media mr-2 media-info">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
        </div>

        <div class="media-body click-ver-detalle">
          <h5 class="mb-1">{{ 'ACOMPAÑAMIENTO' }} - <small>{{ data_proposal.GRUPO_EMPRESA.toUpperCase() }} - {{ data_proposal.EMPRESA }} - {{ data_proposal.PDV }}</small></h5>
          <small class="text-muted">{{ data_proposal.FECHA_FIN }} {{data_proposal.HORA_FIN }} <b>por {{ data_proposal.EVALUADOR }}</b></small>
          <p class="mb-1"><b>{{ data_proposal.AUDITORIA }}: </b> <span :title="data_proposal.OBSERVACION_GENERAL" class="dev-multiline-two">{{ data_proposal.OBSERVACION_GENERAL }}</span></p>
          <a href="#" class="btn btn-danger btn-xxs shadow">Alerta</a>
        </div>

        <div class="dropdown">

          <button type="button" class="btn btn-info light sharp" data-toggle="dropdown">
            <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
          </button>

          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#">Agregar observación</a>
          </div>
        </div>
      </div>
  </li>
</template>

<script>
export default 
{
    mounted() {
    },
    props:
    {
      data_proposal: Object 
    },
    data()
    {
      return {

      }
    }
}
</script>

<style scoped>
.dev-multiline-two
{
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>