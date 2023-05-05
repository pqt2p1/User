<?php

namespace Pqt2p1\User\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Pqt2p1\User\Encryption;
use Illuminate\Http\Request;
use Pqt2p1\User\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Pqt2p1\User\Notifications\ResetPassword;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'error' => 1,
                'mes' => 'Invalid request data: ' . $validatedData->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 1, 'mes' => __('Bad cred...')]);
        }

        $data = response()->json(['error' => 0, 'mes' => __('Login Success'), 'result' => [
            'user' => $user,
            'token' => $user->createToken($user->email)->plainTextToken,
        ]]);


        return $data;
    }

    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|max:255',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'error' => 1,
                'mes' => 'Invalid request data: ' . $validatedData->errors()->first(),
            ], 422);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        event(new Registered($user));

        $data = response()->json(['error' => 0, 'mes' => __('Created Successfully'), 'result' => [
            'user' => $user,
            'token' => $token,
        ]]);

        return $data;
    }

    public function logout(Request $request)
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json(['error' => 0, 'mes' => __('Logout Successfully')]);
    }

    public function getProfile(Request $request)
    {
        $user = Auth::user();

        return response()->json(['error' => 0, 'mes' => __('Get Profile Successully'), 'result' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'error' => 1,
                'mes' => 'Invalid request data: ' . $validatedData->errors()->first(),
            ], 422);
        }

        $user = Auth::user();
        $user->name = $request->name;

        $user->save();

        return response()->json(['error' => 0, 'mes' => __('Update Successfully')]);

    }

    public function changePassword(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'error' => 1,
                'mes' => 'Invalid request data: ' . $validatedData->errors()->first(),
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response(['message' => 'The current password is incorrect'], 422);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['error' => 0, 'mes' => __('Password has been changed')]);
    }

    public function forgotPassword(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'error' => 1,
                'mes' => 'Invalid request data: ' . $validatedData->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 1, 'mes' => __('The selected email is invalid')]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['error' => 0, 'mes' => __('We have sent an email to your registered email address with instructions on how to reset your password. Please check your inbox and follow the steps provided')]);
    }

    public function resetPassword(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|confirmed|max:255',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'error' => 1,
                'mes' => 'Invalid request data: ' . $validatedData->errors()->first(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'error' => 0,
                'mes' => 'Password reset success'
            ]);;
        }

        return response()->json([
            'error' => 1,
            'mes' => 'Error happenned when reseting your password'
        ]);
    }
}
