<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportAccopanimentExport implements FromCollection, WithColumnWidths, ShouldAutoSize, WithStyles, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $data;

    function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return $this->data;
    }

    public function columnWidths(): array
    {
        return [
            'H' => 55
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('H')->getAlignment()->setWrapText(true);        
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Tipo',
            'Operacion',
            'Centro de costo',
            'Tiempo',
            'Ubicación',
            'Observación',
            'Resultado'
        ];
    }
}
