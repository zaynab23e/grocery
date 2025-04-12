<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Storage;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $remoteImageUrl = $row['image_path'];

            // حمل الصورة وسجل المسار المحلي باستخدام نفس اسم الملف
            $localImagePath = $this->downloadAndStoreImage($remoteImageUrl, 'products');

            Product::updateOrCreate(
                ['name' => trim($row['name'])],
                [
                    'category_id'   => $row['category_id'],
                    'description'   => $row['description'] ?? null,
                    'price'         => $row['price'],
                    'quantity'      => $row['quantity'],
                    'stock_status'  => isset($row['stock_status']) ? str_replace(' ', '', $row['stock_status']) : 'in_stock',
                    'image_path'    => $row['image_path'],
                    
                ]
            );
        }
    }

    private function downloadAndStoreImage($url, $folder = 'products')
    {
        try {
            if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                return null;
            }

            $fileName = basename(parse_url($url, PHP_URL_PATH));
            $filePath = "{$folder}/{$fileName}";

            if (!Storage::disk('public')->exists($filePath)) {
                $imageContent = file_get_contents($url);
                if ($imageContent) {
                    Storage::disk('public')->put($filePath, $imageContent);
                } else {
                    return null;
                }
            }
            
            return "storage/{$filePath}";
        } catch (\Exception $e) {
            \Log::error("فشل تحميل الصورة من {$url} - " . $e->getMessage());
            return null;
        }
    }
}