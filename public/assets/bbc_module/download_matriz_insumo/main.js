function OnClickRedirectDownloadMatriz() 
{
    window.open(`https://klaxen.co/savk/assets/docs/Matriz_de_insumos_Cocina_PUBs_BBC.pdf`, '_blank');
}

function OnClickRedirectDownloadZones() 
{
    window.open(`https://klaxen.co/savk/assets/docs/Matriz_de_insumos_Zonas_comunes_PUBs_BBC.pdf`, '_blank');
}

$('#dev_mat_ins_coc').on('click', OnClickRedirectDownloadMatriz);
$('#dev_mat_ins_zone').on('click', OnClickRedirectDownloadZones);