<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CategoryImport implements ToCollection
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // لتجاوز أول صف لو فيه عناوين الأعمدة
            if ($index === 0) {
                continue;
            }

            Category::create([
                'name' => $row[0],
                'description' => $row[1],
                'image_path' => $row[2], // تأكدي إنك بتتعامل مع مسار أو ترفعي صورة فعليًا
            ]);
        }
    }
}
