// Jquery functions by plugins
$(()=>{
  $('.single-select').select2();
  $('.multi-select').select2({
    multiple: true
  });
})
// End Jquery functions

function OnClickShowModalProducts(e)
{
  e.preventDefault();
  $('.modal-products-watch').modal('show');
}

function OnClickCloseModalProducts(e)
{
  e.preventDefault();
  $('.modal-products-watch').modal('hide');
}