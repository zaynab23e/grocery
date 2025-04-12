<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\user\store;
use App\Http\Requests\user\login;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // _________________________________________________________________________________________________________
    public function register(Store $request)
    {
        $validatedData = $request->validated();

        $user = User::create( $validatedData);

        return response()->json(['message'=>'تم التسجيل بنجاح'], 201);
    }

    // _______________________________________________________________________________________________________________
    public function login(Login $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    // _________________________________________________________________________________________________________________
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
        }

        return response()->json(['message' => 'لم يتم تسجيل الدخول'], 401);
    }

    // _________________________________________________________________________________________________________________
    public function forgotPassword(Request $request)
    {
        $request->validate(['identifier' => 'required']);

        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $column = $isEmail ? 'email' : 'phone';

        $user = User::where($column, $request->identifier)->first();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        $code = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            [$column => $request->identifier],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        if ($isEmail) {
            Mail::raw("رمز إعادة تعيين كلمة المرور الخاص بك هو: $code", function ($message) use ($user) {
                $message->to($user->email)->subject('رمز إعادة تعيين كلمة المرور');
            });
        }

        return response()->json(['message' => 'تم إرسال رمز إعادة تعيين كلمة المرور']);
    }

    // _____________________________________________________________________________________________________________
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
            ->first();

        if (!$resetEntry || !Hash::check($request->token, $resetEntry->token)) {
            return response()->json(['message' => 'الرمز غير صحيح أو منتهي الصلاحية'], 400);
        }

        $user = User::where($column, $request->identifier)->first();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where($column, $request->identifier)->delete();

        return response()->json(['message' => 'تمت إعادة تعيين كلمة المرور بنجاح']);
    }
//____________________________________________________________________________________________________________________ 
}

// keda tmam fahmty ana 3amlt eh ? 