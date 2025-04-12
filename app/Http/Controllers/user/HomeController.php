<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $caetories = Category::all();
        return response()->json([
                'categories' => $caetories]);
    }

//____________________________________________________________________________________________________________
public function search(Request $request)
{
    $search = $request->query('search');
        
    $product = Product::with('category')
        ->when($search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            })->get();

    return response()->json([
        'product' => $product
    ]);
}
//____________________________________________________________________________________________________________
// public function getProductsByCategory($categoryId)
// {
    //     $products = Product::where('category_id', $categoryId)
    //         ->where('stock_status', 'in_stock')
    //         ->get();
    
    //     return response()->json([
        //         'products' => $products
        //     ]);
        // }
        
        
    }