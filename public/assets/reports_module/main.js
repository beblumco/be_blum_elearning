// Jquery functions by plugins
$(()=>{
  $('.single-select').select2();
  $('.multi-select').select2({
    multiple: true
  });
})
// End Jquery functions

// Javascript puro
// autor: Andres S. Henao
// Description: Mayor velocidad para la plataforma y mayor conocimiento y manejo de JS
const modal_share = new bootstrap.Modal(document.getElementById("modal-share"));
const zone = document.getElementById('content-zone-drop');

const openModalShare = (name) => {
  let modal_title = document.getElementById('modal-title-share')
  modal_title.innerHTML = 'Compartir - ' + name;
  modal_share.show()
}

//Zone drop upload File
const removeDragData = (ev) => {
  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to remove the drag data
    ev.dataTransfer.items.clear();
  } else {
    // Use DataTransfer interface to remove the drag data
    ev.dataTransfer.clearData();
  }
}

const _activeZone = (status = false) =>{
  if(status){
    zone.classList.add('zone-active');
  }else{
    zone.classList.remove('zone-active');
  }
}

const dropHandler = (ev) =>{
  console.log('Ficheros arrastrados');
  //prevent item from running by default
  ev.preventDefault()

  if(ev.dataTransfer.items){
    _activeZone(false)
      for (var i = 0; i < ev.dataTransfer.items.length; i++){
        if (ev.dataTransfer.items[i].kind === 'file') {
              var file = ev.dataTransfer.items[i].getAsFile();
              console.log('... file[' + i + '].name = ' + file.name);
        }
      }
  }

  removeDragData(ev)

}

const dragOverHandler = (ev) => {
  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();
  if(!zone.classList.contains('zone-active')){
    _activeZone(true)
  }
}
//End zone drop file
window.onload = () =>{
}
