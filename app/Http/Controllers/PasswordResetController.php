<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/password/email",
     *      operationId="sendResetLinkEmail",
     *      tags={"Authentication"},
     *      summary="Send password reset link",
     *      description="Sends a password reset link to the specified email address",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset link sent successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to send password reset link"
     *      )
     * )
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)], 200)
                    : response()->json(['message' => __($status)], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/password/reset",
     *      operationId="resetPassword",
     *      tags={"Authentication"},
     *      summary="Reset password",
     *      description="Resets the user's password using a token sent in the reset email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token", "email", "password", "password_confirmation"},
     *              @OA\Property(property="token", type="string", example="token_from_email"),
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to reset password"
     *      )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

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

        return $status === Password::PASSWORD_RESET
                    ? response()->json(['message' => __($status)], 200)
                    : response()->json(['message' => __($status)], 400);
    }
}
