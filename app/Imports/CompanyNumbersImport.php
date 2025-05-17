<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CompanyNumbersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     * @return Collection
     */
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
