var api = "https://klaxen.net/api_cap_virtual/api/";
// Jquery functions by plugins
$(async ()=>{
  $('.single-select').select2();

  // $('.multi-select').select2({
  //   multiple: true
  // });
  await Promise.all([ OnLoadTrainingsCompaniesGruops() ]);
});
// End Jquery functions

function OnClickCollapse(control)
{
  if($('#creation_collapse').hasClass('show'))
  {
    $('#creation_collapse').collapse('hide');
    ClearDataToCreateTraining();
  }
  else
    $('#creation_collapse').collapse('show');
}

async function OnClickCollapseTrainings(control)
{
  if($('#creation_collapse_trinings').hasClass('show'))
  {
    $('#creation_collapse_trinings').collapse('hide');
    // ClearDataToCreateTraining();
  }
  else
  {
    try
    {
      let data = new FormData();
      data.append('documentUser', $('.dev_user').attr('codeUser'));

      loading();
      let rs = await fetch(`${api}GetLinksCap`, { method: "POST", body: data });
      let rd = await rs.json();
      loading(false);
      switch (rd.responseCode)
      {
        case 202:
          let string = '';

          Array.from(rd.data).forEach(element => {
            string += ComponentRowTrainings(element);
          });

          $('.table_trainings tbody').html(string);
          $('#creation_collapse_trinings').collapse('show');
          break;
        case 406:

          break;

        default:
          break;
      }
    } catch (error)
    {
      console.error(`Error al traer las capacitaciones: ${error.message}`);
      loading(false);
    }
  }

}

function ComponentRowTrainings(data)
{
  return `
        <tr class="btn-reveal-trigger">
            <td class="py-2 text-center">${data.NOMBRE_CLIENTE}</td>
            <td class="py-2 text-center">${data.FECHA}</td>
            <td class="py-2 text-center">${data.CAPACITACION}</td>
            <td class="py-2 text-center">${data.MODULO}</td>
            <td class="py-2 text-center">
              <div class="dropdown text-sans-serif">
                <button class="btn btn-primary tp-btn-light sharp" type="button" id="order-dropdown-0" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></span></button>
                <div class="dropdown-menu dropdown-menu-right border py-0" aria-labelledby="order-dropdown-0">
                  <div class="py-2">
                    <a class="dropdown-item text-danger" href="#!" onclick="OnClickCopyClipBoard(this);" url="${data.URL}">Copiar link</a>
                  </div>
                </div>
              </div>
            </td>
        </tr>`;
}

async function OnLoadTrainingsCompaniesGruops()
{
  try
  {
    let data = new FormData();

    loading();
    let rs = await fetch(`${api}GetDefaultsData`, { method: "POST", body: data });
    let rd = await rs.json();
    loading(false);
    switch (rd.responseCode)
    {
      case 202:
        // CARGAR CAPACTICACIONES
        $('#training_select').html('');

        $('#training_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona la capacitación'));

        rd.data.trainings.forEach(training => {
            $('#training_select')
            .append($("<option></option>")
            .attr("value", training.id)
            .text(training.nombre));
        });

        $("#training_select").select2();

        // CARGAR GRUPOS EMPRESA
        $('#company_group_select').html('');

        $('#company_group_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona el grupo empresa'));

        rd.data.company_groups.forEach(training => {
            $('#company_group_select')
            .append($("<option></option>")
            .attr("value", training.id)
            .text(training.ge_nombre));
        });

        $("#company_group_select").select2();
        break;
      case 406:
        $('#training_select').html('');
        $("#company_group_select").html('');
        break;

      default:
        break;
    }
  }
  catch (error)
  {
    console.error(`Error al traer las capacitaciones: ${error.message}`);
    loading(false);
  }
}

async function OnChangeTrainingModules(control)
{
  try
  {
    let valueTraining = $("#training_select").val();
    if(valueTraining == null || valueTraining == 'null')
    {
        $('#module_select').html('');

        $('#module_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona el módulo'));
        return;
    }

    let data = new FormData();
    data.append('idCap', valueTraining);
    loading();
    let rs = await fetch(`${api}GetCapsModules`, { method: "POST", body: data });
    let rd = await rs.json();
    loading(false);
    switch (rd.responseCode)
    {
      case 202:
        // CARGAR MODULOS
        $('#module_select').html('');

        $('#module_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona el módulo'));

        rd.data.forEach(training => {
            $('#module_select')
            .append($("<option></option>")
            .attr("value", training.id)
            .text(training.nombre));
        });

        $("#module_select").select2();
        break;
      case 406:
        $('#module_select').html('');
        break;

      default:
        break;
    }
  }
  catch (error)
  {
    console.error(`Error al traer los módulo: ${error.message}`);
    loading(false);
  }
}

async function OnChangeCompanyGroup(control)
{
  try
  {
    let idCompanyGroup = $("#company_group_select").val();
    if(idCompanyGroup == null || idCompanyGroup == 'null')
    {
        $('#company_select').html('');

        $('#company_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona la empresa'));
        return;
    }

    $('#pdv_select').val("null").change();

    let data = new FormData();
    data.append('id_company_group', idCompanyGroup);
    loading();
    let rs = await fetch(`${api}GetCompanies`, { method: "POST", body: data });
    let rd = await rs.json();
    loading(false);
    switch (rd.responseCode)
    {
      case 202:
        // CARGAR EMPRESAS
        $('#company_select').html('');

        $('#company_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona la empresa'));

        rd.data.forEach(training => {
            $('#company_select')
            .append($("<option></option>")
            .attr("value", training.id)
            .text(training.nombre));
        });

        $("#company_select").select2();
        break;
      case 406:
        $('#company_select').html('');
        break;

      default:
        break;
    }
  }
  catch (error)
  {
    console.error(`Error al traer las empresas: ${error.message}`);
    loading(false);
  }
}

async function OnChangeCompany(control)
{
  try
  {
    let idCompany = $("#company_select").val();
    if(idCompany == null || idCompany == 'null')
    {
        $('#pdv_select').html('');

        $('#pdv_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona un punto de evaluación'));
        return;
    }

    let data = new FormData();
    data.append('id_company', idCompany);
    loading();
    let rs = await fetch(`${api}GetPdvs`, { method: "POST", body: data });
    let rd = await rs.json();
    loading(false);
    switch (rd.responseCode)
    {
      case 202:
        // CARGAR PUNTOS DE EVALUACIÓN
        $('#pdv_select').html('');

        $('#pdv_select')
        .append($("<option></option>")
        .attr("value", 'null')
        .text('Selecciona un punto de evaluación'));

        rd.data.forEach(training => {
            $('#pdv_select')
            .append($("<option></option>")
            .attr("value", training.id)
            .text(training.nombre));
        });

        $("#pdv_select").select2();
        break;
      case 406:
        $('#pdv_select').html('');
        break;

      default:
        break;
    }
  }
  catch (error)
  {
    console.error(`Error al traer los puntos de evaluación: ${error.message}`);
    loading(false);
  }
}

async function OnClickToCreateLink(control)
{
  try
  {
    let objectData = {
      id_training: $("#training_select").val(),
      id_module:$("#module_select").val(),
      id_company_group:$("#company_group_select").val(),
      id_company:$("#company_select").val(),
      id_pdv:$("#pdv_select").val(),
      virtual_date:$("#fechaTraining").val(),
      description:$("#descriptionTextArea").val(),
      documentUser: $('.dev_user').attr('codeUser')
    }

    if(objectData.id_training == 'null' || objectData.id_module == 'null' || objectData.id_company_group == 'null' || objectData.id_company == 'null'
      || objectData.id_pdv == 'null' || objectData.virtual_date == '' ||objectData.description == '')
    {
        swal({
            title: "Tienes campos vacíos",
            text: "Recuerda que todos los campos que tienen un (*) son obligatorios",
            type: "warning",
            confirmButtonColor: '#1f3352',
            cancelButtonColor: '#ff7f00',
            confirmButtonText: "Aceptar",
        });
        return;
    }

    let data = new FormData();
    Object.entries(objectData).forEach(arrayObject => {
      data.append(arrayObject[0], arrayObject[1]);
    });
    loading();
    let rs = await fetch(`${api}RegisterVirtualTraining`, { method: "POST", body: data });
    let rd = await rs.json();
    loading(false);
    switch (rd.responseCode)
    {
      case 206:
        swal({
            title: "¡Éxitoso!",
            text: rd.message,
            type: "success",
            confirmButtonColor: "#FE634E",
            confirmButtonText: "Aceptar",
            footer: `<a hreft="#" title="${rd.data}" style="cursor: pointer;color: rgb(254, 99, 78);font-weight: bold;" onclick="OnClickCopyClipBoard(this);" url="${rd.data}">Copiar link</a>`
        });

        ClearDataToCreateTraining();
        break;
      case 406:
        swal({
            title: "¡Fallido!",
            text: rd.message,
            type: "error",
            confirmButtonColor: "#FE634E",
            confirmButtonText: "Aceptar",
        });
        break;

      default:
        break;
    }
  }
  catch (error)
  {
    console.error(`Error al crear link: ${error.message}`);
    loading(false);
  }
}

function ClearDataToCreateTraining()
{
  $('#training_select').val("null").change();
  $('#company_group_select').val("null").change();
  $('#pdv_select').val("null").change();
  $('#fechaTraining').val("");
  $('#descriptionTextArea').val("");
}

function OnClickCopyClipBoard(control)
{
  const el = document.createElement('textarea');
  el.value = $(control).attr('url');
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);

  toastr.success('Link de la capacitación copiada.')
}
