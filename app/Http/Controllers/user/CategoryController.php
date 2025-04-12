<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
//____________________________________________________________________________________________
    public function index(string $id)
    {
        $user=User::find($id);
        if(!$user){
            return response()->json(['message' => 'المستخدم غير موجود '], 404);
        }
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('stock_status', 'in_stock');
        }])->get();

        return response()->json(['message' =>$categories]);
    } 
//____________________________________________________________________________________________
    public function show($id, $user_id)
    {
        $user=User::find($user_id);
        if(!$user){
            return response()->json(['message' => 'المستخدم غير موجود '], 404);
        }

        $category = Category::with(['products' => function ($query) {
            $query->where('stock_status', 'in_stock');
        }])->findOrFail($id);

        return response()->json(['message' =>$category]);
    }
// //____________________________________________________________________________________________
// public function search(Request $request)
//     {
//         $query = Category::query();

//         if ($request->has('name')) {
//             $query->where('name', 'LIKE', '%' . $request->name . '%');
//         }

//         return response()->json(['categories' => $query->get()]);
//     }
//____________________________________________________________________________________________
}
