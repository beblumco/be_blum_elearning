<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class CaLinks extends Model
{
    use HasFactory;

    protected $table = 'ca_links';
    protected $fillable = [
        'id',
        'id_capacitacion',
        'id_modulo',
        'id_usuario'
    ];

    public static function GetAllLinksByUserAndModule($id_user, $id_module)
    {
        $links = self::select(
            'ca_links.*',
            \DB::raw('DATE_FORMAT(ca_links.created_at, "%d %M %Y") AS FECHA_CREACION'),
            'ca_modulos.nombre AS MODULO',
            'ca_capacitaciones.nombre AS CAPACITACION_DE_MODULO'
        )
            ->Join('ca_modulos', 'ca_links.id_modulo', '=', 'ca_modulos.id')
            ->Join('ca_capacitaciones', 'ca_modulos.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->where('ca_links.id_usuario', '=', $id_user)
            ->where('ca_links.id_modulo', '=', $id_module)
            ->orderby('ca_links.id', 'DESC')
            ->get();

        foreach ($links as $link) {
            $enlace = env('URL') . "registrar-asistencia/" . CaLinks::GenerateLink($link->id);
            $cropLink = CropLink($enlace);

            $link->LINK_CROP = $cropLink;
        }


        return $links;
    }

    public static function GenerateLink($id_module)
    {
        return Crypt::encryptString($id_module);
    }
}
