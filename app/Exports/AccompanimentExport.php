<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class AccompanimentExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $datos = $this->data;

        return view('accompaniment::pdf.detalleAuditoria', compact('datos'));
    }
}
