<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;


class ForgotPasswordController extends Controller
{
    // Mengirim email verifikasi untuk reset password
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi input email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Kirim link reset password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Cek status apakah link berhasil dikirim atau tidak
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Email reset password telah dikirim.'])
            : response()->json(['message' => 'Gagal mengirim email reset password.'], 500);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
