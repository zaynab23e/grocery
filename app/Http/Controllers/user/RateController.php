<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\Request;
use App\Http\Requests\user\ratings;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    /**
     * Store a new rating.
     */
    public function store(ratings $request)
    {
        
        $validatedData=$request->validated();


        $rate = Rate::create([
            'user_id' => Auth::id(),  
            'product_id' => $validatedData['product_id'],
            'rate' => $validatedData['rate'],
        ]);

        return response()->json([
            'message' => 'تم إضافة التقييم بنجاح',
            'rate' => $rate
        ], 201);
    }

//_________________________________________________________________________
    public function index($productId)
    {
        // استرجاع التقييمات الخاصة بالمنتج
        $ratings = Rate::where('product_id', $productId)->get();

        return response()->json([
            'ratings' => $ratings
        ]);
    }

//______________________________________________________________________________
    public function update(Request $request, $productId)
    {
    
        $request->validate([
            'rate' => 'required|integer|min:1|max:5',
        ]);

        
        $rate = Rate::where('user_id', Auth::id())
                    ->where('product_id', $productId)
                    ->first();

        if (!$rate) {
            return response()->json([
                'message' => 'لم تجد تقييم لهذا المنتج من قبل المستخدم.'
            ], 404);
        }


        $rate->update([
            'rate' => $request->rate,
        ]);

        return response()->json([
            'message' => 'تم تحديث التقييم بنجاح',
            'rate' => $rate
        ]);
    }
    //______________________________________________________________________________
}
