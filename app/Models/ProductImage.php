<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'product_id',
        'image_path'
    ];

    /**
     * علاقة الصورة بالمنتج
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * حذف الصورة من التخزين عند حذف السجل
     */
    protected static function booted()
    {
        static::deleting(function ($image) {
            // حذف الملف الفعلي من التخزين
            if ($image->image_path) {
                $path = str_replace('storage/', '', $image->image_path);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    /**
     * الحصول على اسم الملف فقط
     */
    public function getImageNameAttribute()
    {
        return basename($this->image_path);
    }

    /**
     * الحصول على المسار الكامل للصورة
     */
    public function getFullImagePathAttribute()
    {
        return asset($this->image_path);
    }
}