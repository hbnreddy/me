<?php

namespace App\Exports;

use App\Eloquent\Venue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VenuesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return Collection
    */
    public function collection()
    {
        return Venue::all();
    }

    public function headings(): array
    {
        return ['name', 'foreign_id', 'poi_id'];
    }

    public function map($row): array
    {
        $data = [
            $row->name,
            $row->foreign_id,
            $row->poi_id,
        ];

        return $data;
    }
}
