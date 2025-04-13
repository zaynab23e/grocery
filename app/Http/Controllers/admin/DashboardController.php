<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoryImport;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])->paginate(10);
        $categories = Category::all();
        return view('dashboard', compact('products', 'categories'));
    }

    public function importCategories(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new CategoryImport, $request->file('excel_file'));

        return back()->with('success', 'تم رفع الفئات بنجاح');
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new ProductImport, $request->file('excel_file'));

        return back()->with('success', 'تم رفع المنتجات بنجاح');
    }

    // عرض تفاصيل منتج واحد
    public function showProduct($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        return view('show', compact('product'));
    }

    // حذف منتج
    public function destroyProduct($id)
    {
        $product = Product::with('images')->findOrFail($id);
        
        // حذف الصورة الرئيسية إذا كانت موجودة
        if ($product->image_path) {
            $path = str_replace('storage/', '', $product->image_path);
            Storage::disk('public')->delete($path);
        }
        
        // حذف جميع الصور الإضافية
        foreach ($product->images as $image) {
            $imagePath = str_replace('storage/', '', $image->image_path);
            Storage::disk('public')->delete($imagePath);
            $image->delete();
        }
        
        $product->delete();
        
        return back()->with('success', 'تم حذف المنتج بنجاح');
    }

    // تحديث صورة المنتج الرئيسية
    // public function updateProductImage(Request $request, $id)
    // {
    //     $request->validate([
    //         'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $product = Product::findOrFail($id);
        
    //     // حذف الصورة القديمة إذا كانت موجودة
    //     if ($product->image_path) {
    //         $oldPath = str_replace('storage/', '', $product->image_path);
    //         Storage::disk('public')->delete($oldPath);
    //     }
        
    //     // حفظ الصورة الجديدة
    //     $path = $request->file('product_image')->store('products', 'public');
    //     $product->update(['image_path' => 'storage/'.$path]);
        
    //     return back()->with('success', 'تم تحديث صورة المنتج بنجاح');
    // }
    
    // تحديث صورة المنتج الرئيسية
public function updateProductImage(Request $request, $id)
{
    $request->validate([
        'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $product = Product::findOrFail($id);
    
    // حذف الصورة القديمة إذا كانت موجودة
    if ($product->image_path) {
        $oldPath = $product->image_path;  // هنا المسار سيكون فقط products/apple.jpg
        Storage::disk('public')->delete($oldPath);
    }
    
    // حفظ الصورة الجديدة في مجلد "products"
    $path = $request->file('product_image')->store('products', 'public');
    
    // حفظ المسار في قاعدة البيانات بدون "storage/"
    $product->update(['image_path' => $path]);  // هنا ستكون المسار products/apple.jpg

    return back()->with('success', 'تم تحديث صورة المنتج بنجاح');
}

// إضافة صورة إضافية للمنتج
public function addProductImage(Request $request, $id)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $product = Product::findOrFail($id);
    
    // حفظ الصورة الإضافية في مجلد "product_images"
    $path = $request->file('image')->store('product_images', 'public');
    
    // حفظ المسار في قاعدة البيانات بدون "storage/"
    $product->images()->create(['image_path' => $path]);  // هنا ستكون المسار product_images/image.jpg

    return back()->with('success', 'تم إضافة الصورة بنجاح');
}


    // إضافة صورة إضافية للمنتج
    // public function addProductImage(Request $request, $id)
    // {
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $product = Product::findOrFail($id);
        
    //     $path = $request->file('image')->store('product_images', 'public');
    //     $product->images()->create(['image_path' => 'storage/'.$path]);
        
    //     return back()->with('success', 'تم إضافة الصورة بنجاح');
    // }

    // حذف صورة إضافية للمنتج
    public function deleteProductImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $path = str_replace('storage/', '', $image->image_path);
        Storage::disk('public')->delete($path);
        $image->delete();
        
        return back()->with('success', 'تم حذف الصورة بنجاح');
    }

    // عرض نموذج تعديل المنتج
    public function editProduct($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::all();
        return view('edit', compact('product', 'categories'));
    }

    // تنفيذ عملية التعديل
    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->only(['name', 'category_id', 'price', 'quantity', 'stock_status']));
        
        return redirect()->route('admin.dashboard')->with('success', 'تم تحديث المنتج بنجاح');
    }
}