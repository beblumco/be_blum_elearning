<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaContenido extends Model
{
    use HasFactory;

    protected $table = 'ca_contenido';
    protected $fillable = [
        'id',
        'nombre',
        'description',
        'ruta_contenido',
        'tipo_contenido',
        'orden',
        'estado',
        'id_modulo',
    ];

    public function GetAllContentByIdModule($id_module)
    {
        $contents = $this->select(
            'ca_contenido.*',
            \DB::raw('IF(ca_contenido.ruta_contenido IS NULL, "", CONCAT("storage/",ca_contenido.ruta_contenido)) ruta_contenido_completa'),
            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "Imagen"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "PDF"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "Video"
                            ELSE "The quantity is under 30"
                       END) as type_text'),

            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "picture-2"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "file"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "video-player"
                            ELSE "The quantity is under 30"
                       END) as icon'),

            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "danger"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "danger"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "danger"
                            ELSE "The quantity is under 30"
                       END) as class')
        )
        ->where([
            ['ca_contenido.estado', '=', 1],
            ['ca_contenido.id_modulo', '=', $id_module]
        ])
        ->orderBy('ca_contenido.orden','ASC')
        ->get();

        return $contents;
    }

    public function GetContentById($id_content)
    {
        $content = $this->select(
            'ca_contenido.*',
            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "Imagen"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "PDF"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "Video"
                            ELSE "The quantity is under 30"
                       END) as type_text'),

            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "picture-2"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "file"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "video-player"
                            ELSE "The quantity is under 30"
                       END) as icon'),

            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "danger"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "danger"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "danger"
                            ELSE "The quantity is under 30"
                       END) as class')
        )
        ->where('ca_contenido.id', '=', $id_content)
        ->first();

        return $content;
    }

    public function GetResourcesByModule($id_module)
    {
        $content = $this->select(
            'ca_contenido.*'
        )
        ->where('ca_contenido.id_modulo', '=', $id_module)
        ->where('ca_contenido.tipo_contenido', '!=', '3')
        ->orderBy('ca_contenido.orden')
        ->get();
        // ->toSql();

        return $content;
    }

    public function DeleteContent($id_content)
    {
        $delete_content = CaContenido::where('id','=', $id_content)->delete();
        return true;
    }

    public function GetContentByOrder($order, $id_module)
    {
        $content = $this->select(
            'ca_contenido.*',
            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "Imagen"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "PDF"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "Video"
                            ELSE "The quantity is under 30"
                       END) as type_text'),

            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "picture-2"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "file"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "video-player"
                            ELSE "The quantity is under 30"
                       END) as icon'),

            \DB::raw('(CASE
                            WHEN ca_contenido.tipo_contenido = 1 THEN "danger"
                            WHEN ca_contenido.tipo_contenido = 2 THEN "danger"
                            WHEN ca_contenido.tipo_contenido = 3 THEN "danger"
                            ELSE "The quantity is under 30"
                       END) as class')
        )
        ->where([
            ['ca_contenido.id_modulo', '=', $id_module],
            ['ca_contenido.orden', '=', $order]
        ])
        ->first();

        return $content;
    }
    public function SortContent($id_module){
       $contents  = CaContenido::select('id','orden')->where('id_modulo', $id_module)->orderBy('orden','ASC')->get();

       $position = 1;
       foreach ($contents as $key => $value) {

            CaContenido::findOrFail($value->id)->update([
                'orden' => $position
            ]);

            $position++;
       }

    }

    public function GetUrlVideo($url){
        $urlExplode = explode("/", $url );

        if (strpos($url, 'youtu') !== false) {

            if(strpos($url, 'youtu.be') !== false){
                $url = "https://www.youtube.com/embed/".$urlExplode[3];
            }else{
                if (strpos($url, 'embed') == false) {
                    $url = "https://www.youtube.com/embed/".explode("=", explode("&",$urlExplode[3])[0])[1];
                }

            }

            return $url;
        }else if (strpos($url, 'vimeo') !== false){

            $url = "https://player.vimeo.com/video/".$urlExplode[3];

            return $url;
        }else{
            return $url;
        }
    }

}
