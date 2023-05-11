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
use Pqt2p1\User\Helpers\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Pqt2p1\User\Http\Requests\LoginUserRequest;
use Pqt2p1\User\Http\Requests\RegisterUserRequest;
use Pqt2p1\User\Http\Requests\UpdateProfileUserRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Pqt2p1\User\Http\Requests\ChangePasswordUserRequest;
use Pqt2p1\User\Http\Requests\ForgotPasswordUserRequest;
use Pqt2p1\User\Http\Requests\ResetPasswordUserRequest;

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

        return ApiResponse::successResponse('Update Successfully', $user);
    }

    public function changePassword(ChangePasswordUserRequest $request)
    {
        $request->validated();

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return ApiResponse::errorResponse('The current password is incorrect');
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return ApiResponse::successResponse('Password has been changed');
    }

    public function forgotPassword(ForgotPasswordUserRequest $request)
    {
        $request->validated(); 
        
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::errorResponse('The selected email is invalid');
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );
        return ApiResponse::successResponse('We have sent an email to your registered email address with instructions on how to reset your password. Please check your inbox and follow the steps provided');
    }

    public function resetPassword(ResetPasswordUserRequest $request)
    {
        $request->validated();

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
            return ApiResponse::successResponse('Password reset success');
        }

        return ApiResponse::errorResponse('Error happenned when reseting your password');
    }

    public function verifyEmail(EmailVerificationRequest $request) {
        $request->fulfill();

        return ApiResponse::successResponse('Verify email success');
    }

    public function resendVerifyEmail(Request $request) {
        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        } else {
            return ApiResponse::errorResponse('You have already verified your email.');
        }
        return ApiResponse::successResponse('Verification link sent!');
    }
}
