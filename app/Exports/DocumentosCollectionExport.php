<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DocumentosCollectionExport implements FromCollection, WithHeadings
{
    protected $collection;

    /**
     * Constructor del exportador de documentos.
     *
     * @param Collection $collection La colecci n de documentos a exportar.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Devuelve la colecci n de documentos a exportar.
     * 
     * @return Collection La colecci n de documentos a exportar.
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * Devuelve un array con las cabeceras de las columnas que se van a exportar.
     *
     * @return array Un array con las cabeceras de las columnas.
     */
    public function headings(): array
    {
        return ['Origen', 'Tipo', 'Numero', 'Conductor', 'Version', 'Fecha emision', 'Fecha vencimiento', 'Estado', 'Propietario', 'Placa', 'Creado por'];
    }
}
