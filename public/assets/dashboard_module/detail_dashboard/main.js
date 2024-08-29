$(()=>{

  let paginate = {
    currPage: 1,
    lastPage: 1
  }

  const _shortenText = (text) =>{
    let newText = ''
    let classTooltip = ''
    if(text.length > 100){
      newText = text.slice(0, 100) + `...`
      classTooltip = `data-bs-toggle="tooltip" data-bs-placement="top" title="${text}"`
    }else{
      newText = text
      classTooltip = ''
    }
    return {classTooltip, newText}
  }


  const loadValueProposal = (page = 1) =>{
    const elemValueProposal = $('#content-value-proposal')
    loading()
    fetch(`dashboard/data?page=${page}`).then(response => response.json())
      .then(r => {
        loading(false)
        let li = ''
        let textShort = ''
        paginate.currPage = r.current_page
        paginate.lastPage = r.last_page
        r.data.forEach(el =>{
          textShort = _shortenText(el.OBSERVACION_GENERAL)
          li += ` <li>
                   <div class="timeline-panel">
                     <div class="media mr-2 media-info">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                     </div>
                     <div class="media-body click-ver-detalle">
                       <h5 class="mb-1">Acompañamiento - <small>${ el.GRUPO_EMPRESA.toUpperCase() } - ${el.EMPRESA} - ${el.PDV}</small></h5>
                       <small class="text-muted">${el.FECHA_FIN} ${el.HORA_FIN} <b>por ${el.RESPONSABLE}</b></small>
                       <p class="mb-1" ${textShort.classTooltip}><b>${el.AUDITORIA} : </b> ${textShort.newText} <div class=""></div>	</p>
                       <a href="#" class="btn btn-danger btn-xxs shadow">Alerta</a>
                       <!-- <a href="#" class="btn btn-primary btn-xxs shadow">Agregar observación</a> -->
                       <!-- <a href="#" class="btn btn-outline-danger btn-xxs">Delete</a> -->
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-info light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <!-- <a class="dropdown-item" href="#">Editar</a>
                           <a class="dropdown-item" href="#">Eliminar</a> -->
                       </div>
                     </div>
                   </div>
                 </li>
                 <li>
`
        })
        // elemValueProposal.empty()
        elemValueProposal.append(li)
      })

  }

  $('#DZ_W_Todo1').scroll(()=>{
    let scrollPosition = $('#DZ_W_Todo1').scrollTop() + $('#DZ_W_Todo1').height()
    let contentProposal = $('#content-value-proposal').height()

    if((scrollPosition - 15) == contentProposal && paginate.currPage < paginate.lastPage){//Llego al tope inferior
      console.log(`Tamaño del contenedor ${contentProposal} Tope inferior ${scrollPosition - 15}`)
      loadValueProposal(paginate.currPage + 1)
    }

  })

  $(document).on('click', '.click-ver-detalle', function(){
      window.open('/savk/dashboard/propuesta_detalle');
  });

  loadValueProposal()
})
