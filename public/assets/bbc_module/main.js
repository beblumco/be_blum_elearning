function OnClickRedirectVideo() 
{
    let link = $(this).attr('link');
    window.open(`${link}`, '_blank');
}

function OnClickRedirectDownloadMatriz() 
{
    let link = $(this).attr('link');
    window.open(`${link}`, '_blank');
}

function OnClickRedirectDownloadInquiet() 
{
    let link = $(this).attr('link');
    window.open(`${link}`, '_blank');
}

$('#dev_video_BBC').on('click', OnClickRedirectVideo);
$('#dev_download_matriz_BBC').on('click', OnClickRedirectDownloadMatriz);
$('#dev_download_inquiet_BBC').on('click', OnClickRedirectDownloadInquiet);