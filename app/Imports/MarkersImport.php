<?php

namespace App\Imports;

use App\Eloquent\Marker;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MarkersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Marker
     */
    public function model(array $row)
    {
        return new Marker([
            'name' => $row['slug'],
        ]);
    }
}
