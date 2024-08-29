<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class CaAsistentesLinks extends Model
{
    use HasFactory;

    protected $table = 'ca_asistentes_links';
    protected $fillable = [
        'id',
        'id_link',
        'id_asistente'
    ];

    public static function GetAllAssistantsByLink($id_link){
        
        $assistants = self::select(
            'ca_asistentes.id',
            'ca_asistentes_links.id_link',
            'ca_asistentes.nombre',
            \DB::raw('DATE_FORMAT(ca_asistentes_links.created_at, "%d %M %Y") AS fecha')            
        )
        ->Join('ca_links','ca_asistentes_links.id_link','=','ca_links.id')
        ->Join('ca_asistentes','ca_asistentes_links.id_asistente','=','ca_asistentes.id')
        ->where('ca_asistentes_links.id_link', '=', $id_link)
        ->get();

        return $assistants;
    }   
}
