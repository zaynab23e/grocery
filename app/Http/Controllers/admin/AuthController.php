<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Store;
use App\Http\Requests\Admin\Login;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{ public function register(Store $request)
{
    $validatedData = $request->validated();
    $validatedData['plain_password'] = $validatedData['password'];
    $validatedData['password'] = Hash::make($validatedData['password']); 
    $admin = Admin::create($validatedData);
    return response()->json(['message' => 'تم انشاء حساب بنجاح'], 201);
}
//__________________________________________________________________________________________________

public function login(login $request)
{
    $validatedData = $request->validated();
    $admin = Admin::where('email', $validatedData['email'])->first();

    if (!$admin || !Hash::check($validatedData['password'], $admin->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $admin->createToken('admin_token')->plainTextToken;
    return response()->json(['token' => $token], 200);
}

//_________________________________________________________________________________________________

public function logout()
{
    auth()->user()->tokens()->delete();

    return response()->json(['message' => 'تم تسجيل الخروج بنجاح'], 200);
}


//__________________________________________________________________________________________________
public function forgotPassword(Request $request)
{
    $request->validate(['identifier' => 'required']);

    $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
    $column = $isEmail ? 'email' : 'phone';

    $admin = Admin::where($column, $request->identifier)->first();

    if (!$admin) {
        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    $code = random_int(1000, 9999);

    DB::table('password_reset_tokens')->updateOrInsert(
        [$column => $request->identifier],
        ['token' => $code, 'created_at' => now()]
    );

    if ($isEmail) {
        Mail::raw("رمز إعادة تعيين كلمة المرور الخاص بك هو: $code", function ($message) use ($admin) {
            $message->to($admin->email)->subject('رمز إعادة تعيين كلمة المرور');
        });
    }

    return response()->json(['message' => 'تم إرسال رمز إعادة تعيين كلمة المرور']);
}
//__________________________________________________________________________________________________


public function resetPassword(Request $request)
{
    $validatedData = $request->validate([
        'identifier' => 'required',
        'token' => 'required',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
    $column = $isEmail ? 'email' : 'phone';

    $resetEntry = DB::table('password_reset_tokens')
        ->where($column, $request->identifier)
        ->where('token', $request->token)
        ->first();

    if (!$resetEntry) {
        return response()->json(['message' => 'الرمز غير صحيح أو منتهي الصلاحية'], 400);
    }

    $admin = Admin::where($column, $request->identifier)->first();

    if (!$admin) {
        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    $admin->update(['password' => Hash::make($request->password)]);

    DB::table('password_reset_tokens')->where($column, $request->identifier)->delete();

    return response()->json(['message' => 'تمت إعادة تعيين كلمة المرور بنجاح']);
}
//__________________________________________________________________________________________________


}
