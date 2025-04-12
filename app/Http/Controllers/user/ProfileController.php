<?php
namespace App\Http\Controllers\user;

use App\Models\User;
use App\Http\Requests\user\ProfilUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\user\ChangePasswordRequest;

class ProfileController extends Controller
{
    //___________________________________________________________________________________
    public function getProfileUser(string $id)
    {
        $user = auth('web')->user();
        $user=User::find($id);
        return response()->json(['message' => $user], 200);
    } 
    //__________________________________________________________________________________________________
    public function updateProfileUser(ProfilUpdate $request,string $id)
    {
        $user = auth('web')->user();
        $user=User::find($id);
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            if ($user->image) {
                $oldImagePath = public_path(str_replace(env('APP_URL') . '/', '', $user->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); 
                }
            }
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('users'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/users/' . $imageName;
        }

        $user->update($validatedData);

        return response()->json(['message' => $validatedData], 200);
    }

    //__________________________________________________________________________________________________
    public function deleteAccountUser(string $id)
    {
        $user = auth('web')->user();
        auth('web')->logout(); 
        $user=User::find($id);
        $user->delete();

        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }

    //__________________________________________________________________________________________________
    public function changePassword(ChangePasswordRequest $request, string $id)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح' ], 200);
    }
//__________________________________________________________________________________________________
}
