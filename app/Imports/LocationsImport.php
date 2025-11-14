<?php
namespace App\Imports;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Validator;

class LocationsImport implements ToModel, WithHeadingRow, SkipsOnFailure
{
use Importable, SkipsFailures;

    public function model(array $row)
    {
        // Validate the row structure
        $validator = Validator::make($row, [
            'name'        => 'required|string',
            'code'        => 'required|string|unique:locations,code',
            'parent_code' => 'nullable|string',
            'level'       => 'required|integer'
        ]);

        if ($validator->fails()) {
            // Collect errors
            $this->onFailure($validator->errors()->all());
            return null;
        }

        // Wrap database writes in a transaction
        return DB::transaction(function () use ($row) {

            return Location::updateOrCreate(
                [
                    'code' => $row['code']   // Unique key
                ],
                [
                    'name'        => $row['name'],
                    'parent_code' => $row['parent_code'] ?? null,
                    'level'       => $row['level'],
                ]
            );
        });
    }

}
