<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
//____________________________________________________________________________________________
    public function index()
    {
        $products = Product::where('stock_status', 'in_stock')->paginate(10);
        return response()->json(['message'=>$products]);
    }

//____________________________________________________________________________________________
    public function show($id)
    {
        $product = Product::where('stock_status', 'in_stock')->findOrFail($id);
        return response()->json(['message'=>$product]);
    }

//____________________________________________________________________________________________

public function search(Request $request)
    {
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        return response()->json( ['message'=>$query->get()]);
    }
//____________________________________________________________________________________________




//     public function uploadImages(Request $request, $product_id)
//     {
//         $request->validate([
//             'images' => 'required|array',
//             'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
//         ]);

//         $product = Product::findOrFail($product_id);
//         $uploadedImages = [];

//         foreach ($request->file('images') as $image) {
//             $path = $image->store('product_images', 'public');
//             $productImage = ProductImage::create([
//                 'product_id' => $product->id,
//                 'image_path' => $path
//             ]);
//             $uploadedImages[] = $productImage;
//         }

//         return response()->json(['message' => 'Images uploaded successfully', 'images' => $uploadedImages]);
//     }

// //____________________________________________________________________________________________
//     public function getImages($product_id)
//     {
//         $images = ProductImage::where('product_id', $product_id)->get();
//         return response()->json($images);
//     }
//____________________________________________________________________________________________

}
