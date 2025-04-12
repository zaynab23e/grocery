<?php
namespace App\Http\Controllers\admin;

use App\Models\Admin;
use App\Http\Requests\admin\Update;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\admin\ChangePassword;

class ProfileController extends Controller
{
    //__________________________________________________________________________________________________________
    public function getProfileAdmin(string $id)
    {
        $admin = auth('admin')->user();
        $admin=Admin::find($id);
        
        return response()->json(['message' => $admin], 200);
    } 

    //________________________________________________________________________________________________________________
    public function updateProfileAdmin(string $id ,Update $request)
    {
        $admin = auth('admin')->user();
        $admin=Admin::find($id);
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            if ($admin->image) {
                $oldImagePath = public_path(str_replace(env('APP_URL') . '/', '', $admin->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); 
                }
            }
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('admins'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/admins/' . $imageName;
        }

        $validatedData['plain_password'] = $validatedData['password'];
        $admin->update($validatedData);
        return response()->json(['message' => $admin], 200);
    }

    //___________________________________________________________________________________________________________
    public function deleteAccountAdmin(string $id)
    {
        $admin = Admin::find($id);
        
        if (!$admin) {
            return response()->json(['message' => 'الحساب غير موجود'], 404);
        }
        
        $admin->tokens()->delete();
        $admin->delete();
    
        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }
    
    //______________________________________________________________________________________________________________
    public function changePassword(ChangePassword $request, string $id)
    {
        $admin = auth('admin')->user();
        $admin=Admin::find($id);

        if (Hash::check($request->old_password, $admin->password)) {
            $admin->update(['password' => Hash::make($request->new_password)]);

            return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح'], 200);
        }

        return response()->json(['message' => 'كلمة المرور القديمة غير صحيحة'], 400);
    }
//_______________________________________________________________________________________________________________
}
