<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
//________________________________________________________________________________________________________
    public function index(string $id )
    {
        $user=User::findOrFail($id);
        $user = Auth::user();

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($cart) {
                return [
                    'id' => $cart->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price,
                    'total_price' => $cart->total_price,
                    'product_name' => $cart->product->name,
                    'product_image' => $cart->product->image_path ? asset('storage/' . $cart->product->image_path) : null,
                ];
            });
        return response()->json(['cart' => $cartItems]);
    }
//________________________________________________________________________________________________________
    public function store(Request $request , string $id )
    {
        $user=User::findOrFail($id);
        $user = Auth::user();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
            ]);
        } else {
            $cartItem = Cart::create([
                'user_id'    => $user->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
                'price'      => $product->price,
            ]);
        }
        return response()->json([
            'message' => 'Product added to cart',
            'cart_item' => [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'total_price' => $cartItem->total_price,
                'product_name' => $cartItem->product->name,
                'product_image' => $cartItem->product->image_path ? asset('storage/' . $cartItem->product->image_path) : null,
            ]
        ]);
    } 
//________________________________________________________________________________________________________
public function update(Request $request, $product_id , string $user_id )
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $user = User::findOrFail($user_id); 
    $cartItem = Cart::where('user_id', $user->id)
                    ->where('product_id', $product_id)
                    ->first();
                    if(!$cartItem || !$user){
                        return response()->json(['message' => 'المنتج ليس موجود او المستخدم ليس مسجل'], 404);
                    }

    $cartItem->update(['quantity' => $request->quantity]);

    return response()->json(['message' => 'تم تحديث الكميه بنجاح']);
}

//________________________________________________________________________________________________________
    public function destroy($id, string $user_id)
    {
        $user=User::findOrFail($user_id);
        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'تم حذف المنتج من السله بنجاح']);
    }
//________________________________________________________________________________________________________
    public function clearCart( string $user_id)
    {
        $user=User::findOrFail($user_id);
        $user = Auth::user();
        Cart::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'تم مسح محتوي السله بنجاح']);
    }
//________________________________________________________________________________________________________
}
