<?php

namespace App\Imports;

use App\Eloquent\SygicTag;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class SygicTagsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        if ($row[0] && is_numeric($row[1])) {
            return new SygicTag([
                'tag_key' => $row[0],
                'tag_count' => intval($row[1]),
                'category_key' => $row[2],
                'category_count' => intval($row[3]),
            ]);
        }
    }
}
