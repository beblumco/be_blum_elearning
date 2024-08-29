var documentation = [];
documentation[1] = "https://klaxen.net/i3/pdfCrear.php?n=Felipe%20Gonzalez&i=1144198273&f=2022-06-23&c=Buenas%20Prácticas%20de%20Manufactura%20klaxen&ic=2022-06-23";
documentation[2] = "https://klaxen.net/i3/pdfCrear.php?n=Mariana%20Martínez&i=1182145222&f=2022-06-21&c=Protocolo%20casos%20COVID-19&ic=2022-06-21";

function OnClickDownloadDocumentation(indexDoc) 
{
    window.open(documentation[indexDoc], "_blank", "toolbar=1, scrollbars=1, resizable=1, width=" + 1015 + ", height=" + 800);
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