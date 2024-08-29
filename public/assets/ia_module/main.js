var videos = [];
videos[1] = "https://www.youtube.com/embed/qMUYu8yFQc4";
videos[2] = "https://www.youtube.com/embed/xRDLWSx0w0M";
videos[3] = "https://www.youtube.com/embed/Az-uusOD_-M";
videos[4] = "https://www.youtube.com/embed/ESnyT7310cY";

var documentation = [];
documentation[1] = "https://klaxen.net/i3/doc/FTCK60147.pdf";
documentation[2] = "https://klaxen.net/i3/doc/HSCK60147.pdf";
documentation[3] = "https://klaxen.net/i3/doc/CBCK60147.pdf";
documentation[4] = "https://klaxen.net/i3/doc/CFFCK60147.pdf";
documentation[5] = "https://klaxen.net/i3/doc/PBCK60147.pdf";
documentation[6] = "https://klaxen.net/i3/doc/NSCK60147.pdf";
documentation[7] = "https://klaxen.net/i3/doc/GACK60147.pdf";
documentation[8] = "https://klaxen.net/i3/doc/TECK60147.pdf";
documentation[9] = "https://klaxen.net/i3/doc/PEDCK60147.pdf";

function OnClickWatchVideo(indexSrc)
{
    $('#dev-video-frame').attr('src', videos[indexSrc]);
    $('.modal-video-watch').modal('show');
}

function OnClickCloseModalVideo() 
{
    $('#dev-video-frame').attr('src', '');
    $('.modal-video-watch').modal('hide');
}

async function OnClickDownloadDocumentation(indexDoc) 
{
    window.open(documentation[indexDoc], "_blank", "toolbar=1, scrollbars=1, resizable=1, width=" + 1015 + ", height=" + 800);
}

function OnClickRecord() 
{
    let url = document.querySelector('meta[name="csrf-token"]').getAttribute("url");
    window.open(`${url}catalogo/historial`,'_self');
}

function OnClickSharePdf(indexDoc) 
{
    var input = document.createElement("input");
    input.type = "text";
    input.value = documentation[indexDoc];
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    toastr.success('Link copiado!');
}