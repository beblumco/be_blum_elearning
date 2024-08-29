async function OnClickToSendForm(e) 
{
    e.preventDefault();

    try 
    {
        let name = $('#dev_name').val();  
        let pdv = $('#dev_pdv').val();  
        let suggest = $('#dev_commit').val();

        if(name == '' || pdv == '' || suggest == '')
        {
          toastr.warning('Debes completar todos los campos');
          return;
        }

        let data = new FormData();
        data.append('name', name);
        data.append('pdv', pdv);
        data.append('suggest', suggest);

        loading();
        let rs = await fetch(`send_email`, { method: "POST", body: data, headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        }});
        let rd = await rs.json();
        loading(false);
        switch (rd.responseCode) 
        {
          case 200:
            toastr.success(rd.message);
            FunctionClearDataForm();
            break;
          case 400:
            toastr.warning(rd.message);
            break;
          default:
            break;
        }
    } 
    catch (error) 
    {
      console.error(`Error al realizar el envío de correo: ${error.message}`);
    }  
}

function FunctionClearDataForm() 
{
  $('#dev_name').val('');  
  $('#dev_pdv').val('');  
  $('#dev_commit').val('');
}

$('.dev_btn_send').on('click', OnClickToSendForm);