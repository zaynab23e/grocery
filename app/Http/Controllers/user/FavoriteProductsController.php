<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteProduct;
use App\Models\Product;

class FavoriteProductsController extends Controller
{
    //_________________________________________________________________________________________
    public function index(string $id)
    {
        $user=User::find($id);
        // Check if the user is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مسجل '], 401);
        }

        $favorites = FavoriteProduct::where('user_id', $user->id)->with('product')->get();

        return response()->json(['favorites' => $favorites]);
    
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مسجل '], 401);
        }

        $favorites = $user->favoriteProducts()->with('product')->get();
        return response()->json(['message' => $favorites]);
    }

    // _________________________________________________________________________________________
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مسجل '], 401);
        }
    
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
    
        $product = Product::findOrFail($request->product_id);
    
        $result = $user->favourites()->toggle($product->id);
    
        if ($result['attached']) {
            return response()->json(['message' => 'تم إضافة المنتج إلى المفضلة بنجاح']);
        } else {
            return response()->json(['message' => 'تمت إزالة المنتج من المفضلة']);
        }
    }
    

    //________________________________________________________________________________________
    public function destroy($product_id,string $id)
    {
        $user=User::find($id);
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم ليس مسجل '], 401);
        }

        $favorite = FavoriteProduct::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'المنتج ليس في المفضله'], 404);
        }

        $favorite->delete();
        return response()->json(['message' => 'تم حذف المنتج من المفضله بنجاح']);
    }
//______________________________________________________________________________________________
}
