<?php

namespace App\Imports;

use App\Eloquent\Venue;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VenuesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        $data = [
            'name' => $row['name'],
            'foreign_id' => $row['foreign_id'],
            'poi_id' => $row['poi_id'],
        ];

        return Venue::query()
            ->updateOrCreate([
                'foreign_id' => $row['foreign_id'],
            ], $data);
    }
}
