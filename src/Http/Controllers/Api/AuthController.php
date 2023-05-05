<?php

namespace Pqt2p1\User\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Pqt2p1\User\Encryption;
use Illuminate\Http\Request;
use Pqt2p1\User\Models\User;
use Illuminate\Routing\Controller;
use Pqt2p1\User\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Pqt2p1\User\Http\Requests\ChangePasswordUserRequest;
use Pqt2p1\User\Http\Requests\ForgotPasswordUserRequest;
use Pqt2p1\User\Notifications\ResetPassword;
use Pqt2p1\User\Http\Requests\LoginUserRequest;
use Pqt2p1\User\Http\Requests\RegisterUserRequest;
use Pqt2p1\User\Http\Requests\UpdateProfileUserRequest;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $request->validated();
        
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::errorResponse('Bad cred...');
        }

        $data = [
            'user' => $user,
            'token' => $user->createToken($user->email)->plainTextToken,
        ];

        return ApiResponse::successResponse('Login Success', $data);
    }

    public function register(RegisterUserRequest $request)
    {
        $request->validated();

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        event(new Registered($user));

        $data = [
            'user' => $user,
            'token' => $token,
        ];

        return ApiResponse::successResponse('Created Successfully', $data);
    }

    public function logout(Request $request)
    {
        Auth::user()->currentAccessToken()->delete();
        return ApiResponse::successResponse('Logout Success');
    }

    public function getProfile(Request $request)
    {
        $user = Auth::user();
        return ApiResponse::successResponse('Get profile success', $user);
    }

    public function updateProfile(UpdateProfileUserRequest $request)
    {
        $request->validated();

        $user = Auth::user();
        $user->name = $request->name;

        $user->save();

        return response()->json(['error' => 0, 'mes' => __('Update Successfully')]);
    }

    public function changePassword(ChangePasswordUserRequest $request)
    {
        $request->validated();

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response(['message' => 'The current password is incorrect'], 422);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['error' => 0, 'mes' => __('Password has been changed')]);
    }

    public function forgotPassword(ForgotPasswordUserRequest $request)
    {
        $request->validated(); 
        
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
