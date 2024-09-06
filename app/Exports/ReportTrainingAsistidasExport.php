<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportTrainingAsistidasExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public $data;
    /**
     * @return \Illuminate\Support\Collection
     */
    function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Capacitación',
            'Asesor experto',
            'Asistentes',
            'Certificados',
            'Modalidad',
            'Tipo',
            'Grupo empresa',
            'Empresa',
            'Centro de costo'
        ];
    }
}
