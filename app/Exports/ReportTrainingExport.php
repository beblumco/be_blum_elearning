<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportTrainingExport implements FromCollection, ShouldAutoSize, WithHeadings
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
            'Categoría',
            'Capacitación',
            'Tiempo (Horas)',
            'Certificados',
            'Creada por',
            'Asignación',
            'Grupo empresa',
            'Empresa'
        ];
    }
}
