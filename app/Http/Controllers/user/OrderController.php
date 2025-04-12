<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\OrderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{ 
//____________________________________________________________________________________________
    public function index(string $id)
    {
        $user=User::find($id);
        if(!$user){
            return response()->json(['message' => 'المستخدم غير موجود '], 404);
        }
        $user=Auth::user();
if(!$user){
    return response()->json(['message' => 'User not found'], 404);
}

        $orders = Order::where('user_id', Auth::id())->orderBy('order_date', 'desc')->get();
        return response()->json(['message' =>$orders]);
    }

//____________________________________________________________________________________________
    public function show($id)
    {
        $user=Auth::user();
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['message' =>$order]);
    }

//____________________________________________________________________________________________
  
public function store(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    // 1. جِيب محتويات السلة
    $cartItems = Cart::where('user_id', $user->id)->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'السلة فارغة، لا يمكن إنشاء طلب'], 400);
    }

    // 2. احسب السعر الإجمالي
    $totalPrice = 0;
    foreach ($cartItems as $item) {
        $product = Product::find($item->product_id);
        if ($product) {
            $totalPrice += $product->price * $item->quantity;
        }
    }

    // 3. إنشاء الأوردر
    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => $totalPrice,
        'delivery_price' => $request->delivery_price ?? 0,
        'order_status' => 'pending',
        'order_date' => now(),
    ]);

    Cart::where('user_id', $user->id)->delete();

    return response()->json([
        'message' => 'تم إنشاء الطلب بنجاح',
        'order' => $order,
    ], 201);
} 
//____________________________________________________________________________________________
    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'order_status' => 'required|in:pending,delivered,canceled',
    //     ]);

    //     $order = Order::where('user_id', Auth::id())->findOrFail($id);
    //     $order->update(['order_status' => $request->order_status]);

    //     return response()->json(['message' => 'تم تحديث المنتج بنجاح', 'order' => $order]);
    // }

    // public function updateStatus(Request $request, $id)
    // {
    //     $order = Order::where('user_id', Auth::id())->findOrFail($id);
        
    //     $currentStatus = $order->order_status;
    
    //     if ($request->has('order_status')) {
    //         $request->validate([
    //             'order_status' => 'required|in:pending,delivered,canceled',
    //         ]);
    
    //         $order->update(['order_status' => $request->order_status]);
    
    //         return response()->json([
    //             'message' => 'تم تحديث الحالة بنجاح',
    //             'current_status' => $order->order_status,
    //             'updated_order' => $order
    //         ]);
    //     }
    
    //     return response()->json([
    //         'message' => 'الحالة الحالية للطلب',
    //         'current_status' => $currentStatus
    //     ]);
    // }
    


//____________________________________________________________________________________________
    public function destroy($user_id , $order_id)
    {
        $user=User::find($user_id);
        if(!$user){
            return response()->json(['message' => 'المستخدم غير موجود '], 404);
        }
        $user=Auth::user();
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
    {
        $order=Order::find($order_id);
        $order = Order::where('user_id', Auth::id())->findOrFail($user_id);
        $order->delete();

        return response()->json(['message' => 'تم حذف المنتج بنجاح']);
        }

        }

 //____________________________________________________________________________________________
    public function uploadImages(Request $request, $order_id)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($order_id);
        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('order_images', 'public');
            $orderImage = OrderImage::create([
                'order_id' => $order->id,
                'image_path' => $path
            ]);
            $uploadedImages[] = $orderImage;
        }

        return response()->json(['message' => 'تم تحديث صوره المنتج بنجاح', 'images' => $uploadedImages]);
    }

//____________________________________________________________________________________________
    public function getImages($order_id)
    {
        $images = OrderImage::where('order_id', $order_id)->get();
        return response()->json(['message'=>$images]);
    }
//____________________________________________________________________________________________
// public function search(Request $request)
// {
//     $query = Order::where('user_id', Auth::id());

//     // البحث فقط في اسم الطلب
//     if ($request->has('order_name') && $request->order_name != '') {
//         $query->where('order_name', 'like', '%' . $request->order_name . '%');
//     }

//     // جلب النتائج مع رسالة
//     return response()->json(['orders' => $query->get()]);
// }
// //____________________________________________________________________________________
}
