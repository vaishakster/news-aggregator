<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\PasswordBroker;

/**
 *     Controller for user registration, login, logout, initiate passowrd reset and passowrd reset"
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Create a new user account.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Tolulope Akinnuoye"),
     *             @OA\Property(property="email", type="string", format="email", example="akinnuoyetolulope@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password@123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password@123"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successful registration"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Server error",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occurred during registration"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     )
     * )
     *  * @OA\Schema(
     *     schema="User",
     *     type="object",
     *     properties={ 
     *         @OA\Property(property="id", type="integer", description="The user ID"),
     *         @OA\Property(property="name", type="string", description="The user's name"),
     *         @OA\Property(property="email", type="string", description="The user's email"),
     *         @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *         @OA\Property(property="updated_at", type="string", format="date-time", description="Update timestamp")
     *     }
     * )
     */
    public function register(RegistrationRequest $request)
    {

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Log::info("====>" . json_encode($user));
            return ResponseHelper::success($user, "Successful registration", 200); // Ensure this returns a response

        } catch (Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return ResponseHelper::error("An error occurred during registration", 500); // Ensure this returns a response
        }
    }

    /**
     * Authenticate a user and issue a token.
     *
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login a user",
     *     description="Authenticate a user and generate access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="akinnuoyetolulope@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password@123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login Successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1Q...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="Incorrect email or password"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="Error Occured"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     )
     * )
     */

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return ResponseHelper::error('Incorrect email or password', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return ResponseHelper::success(['token' => $token], 'Login successful');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('Error occured', 500);
        }
    }

    /**
     * Log out user and revoke the token.
     *
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout a user",
     *     description="Log out the authenticated user and revoke the access token.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Logged out successfully"),
     *             @OA\Property(property="data", nullable=true, example=null)

     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="data", nullable=true, example=null)

     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occured"),
     *             @OA\Property(property="data", nullable=true, example=null)

     *         )
     *     ),
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ResponseHelper::success([], 'Logged out successfully');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('An error occured', 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/initiate/password/reset",
     *     summary="Initiate password reset process",
     *     description="Sends a password reset link to the user's email address",
     *     operationId="initiateResetPassword",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="akinnuoyetolulope@gmail.com", description="Email of the user requesting password reset")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="email", type="string", example="akinnuoyetolulope@gmail.com")
     *             ),
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function InitiateResetPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            // Send the reset link using the Password facade
           // $send = Password::sendResetLink(
             //   $request->only('email')
            //);

            $send = $this->broker()->sendResetLink(
                $request->only('email')
            );

            // Check the status and return the appropriate response
            if ($send === 'passwords.sent') {
                return ResponseHelper::success(['email' => $request->email], "password reset link sent to your email", 200);
            } else if ($send === 'passwords.throttled') {
                Log::error($send);
                return ResponseHelper::error("Too many password reset attempt", 500);
            } else {
                Log::error($send);
                return ResponseHelper::error("error occured", 500);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('An error occured', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     summary="Reset user password",
     *     description="Resets the user's password using a token and email verification",
     *     operationId="resetPassword",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="abcdef1234567890", description="Password reset token"),
     *             @OA\Property(property="email", type="string", format="email", example="akinnuoyetolulope@gmail.com", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", example="password@123new", description="New password for the user"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password@123new", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password reset successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error occurred")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            // Reset password using the Password facade
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->setRememberToken(Str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            // Check the status and return the appropriate response
            if ($status === Password::PASSWORD_RESET) {
                return ResponseHelper::success([], "password reset successfully", 200);
            }
            return ResponseHelper::error([], "error occured", 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('An error occured', 500);
        }
    }
}
