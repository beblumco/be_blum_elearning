<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DocumentacionProducto extends Model
{
    protected $table = 'documentacion_producto';

    protected $fillable = [
        'id',
        'id_documentacion',
        'id_producto',
        'id_etiqueta',
        'ruta_documento',
        'id_audeed_drive_archivo',
    ];

    public static function GetDocumentationByIdProduct($id_product) 
    {
        $documentation = self::select(
            'ada.*',
            \DB::raw('UPPER(ada.nombre) as nombre'),
            \DB::raw('CONCAT("'.env('API').'",ada.archivo) as doc_pdf')
        )
        ->join('productos as pr', 'documentacion_producto.id_producto', '=', 'pr.id')
        ->join('documentacion_tecnica as dt', 'documentacion_producto.id_documentacion', '=', 'dt.id')
        ->join('audeed_drive_archivos as ada', 'documentacion_producto.id_audeed_drive_archivo', '=', 'ada.id')
        ->where('pr.id', $id_product)
        ->get();
                
        return $documentation;
    }
}
