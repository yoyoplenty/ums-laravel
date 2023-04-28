<?php

namespace App\Http\Controllers\Api;

use Exception;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LoginFormRequest;
use App\Http\Controllers\Api\ApiController;
use App\Repositories\UserRepository as User;
use App\Repositories\RoleRepository as Role;
use App\Http\Requests\ResetPasswordFormRequest;
use App\Http\Requests\UserFormRequest;
use App\Http\Trait\UserDetailsTrait;
use App\Repositories\PasswordRepository as Password;

class AuthController extends ApiController {

    use UserDetailsTrait;

    public function __construct(private User $user, private Role $role, private Password $password) {
        $this->middleware('acceptjson', ['only' => ['login', 'resetPassword']]);

        $this->middleware('auth', ['only' => ['logout']]);
    }




    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Auth"},
     *     summary="Register user",
     *     description="This can only be done by an guest.",
     *     operationId="createUser",
     *     @OA\RequestBody(
     *         description="Create user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateUser")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessible Entity"
     *     )
     * )
     */

    public function register(UserFormRequest $request) {
        $requestPayload = $request->safe()->except('role_id');
        $payload = $this->generateUserDetails($requestPayload);

        try {
            $role = $this->role->findWhere(['slug' => 'user'])->first();
            $payload['role_id'] = $role->id;

            $user = $this->user->create($payload);
        } catch (Exception $ex) {
            $user->delete();
            return response()->json(['message' => 'There was a problem registering user' . $ex], 500);
        }

        $activation_link = env('APP_URL') . '/api/v1/' . $user->email . '/verify/' . $user->verification_code;

        return $activation_link;
    }


    /**
     * @OA\Post(
     *     path="login",
     *     tags={"Auth"},
     *     operationId="userLogin",
     *     description="Login in a User",
     *     @OA\RequestBody(
     *         description="Updated user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserLogin")
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathorize Access"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */

    public function login(LoginFormRequest $request) {
        try {
            $credentials = request(['email', 'password']);
            (auth()->attempt($credentials)) ?: abort(response()->json('Invalid credentials', 422));

            $user = auth()->user();
            $token = $user->createToken('smsApiToken')->plainTextToken;

            ($user->verified == 1) ?: abort(response()->json('Please check your email to verify your account!!!', 405));
            ($user->role_id == $request->role_id) ?: abort(response()->json('Invalid role id provided', 404));

            return $this->getUser($user, $token);
        } catch (\PDOException $ex) {
            return response()->json('Error occured please contact admin: ' . $ex, 500);
        }
    }


    /**
     * Verify a user account.
     *
     * @OA\Get(
     *     path="/{email}/verify/{verification_code}",
     *     tags={"Auth"},
     *     operationId="verifyUser",
     *     description="Verify a User",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="verification_code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathorize Access"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     * )
     */
    public function verify($email, $verificationCode) {
        $user = $this->user->findByField('email', $email)->first();
        if (!$user || $user->verification_code !== $verificationCode)
            return response()->json(trans('Not found!'), 404);

        ($user->verified == 0) ?: abort(response()->json('User already Verified Please Login!!!', 406));
        ($this->verifyTimeDiff($user->verification_code_generated_at)) ?: abort(response()->json('Activation link has expired. Please request a new activation link', 421));

        $user = $this->user->updateEntity([
            'verified' => 1,
            'verified_at' => now()
        ], $user->id);

        $token = auth()->user()->createToken('smsApiToken')->plainTextToken;

        return $this->getUser($user, $token);
    }

    private function getUser($user, $token) {
        return response()->json(
            [
                'user' => $user->withoutRelations(),
                'token' => $token
            ],
            200
        );
    }


    /**
     * Resend verification link.
     *
     * @OA\Get(
     *     path="/{email}/resend_verification_code",
     *     tags={"Auth"},
     *     operationId="resendVerification",
     *     description="Resend verification link",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathorize Access"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     * )
     */

    public function resendVerification($email) {
        $user = $this->user->findByField('email', $email)->first();

        ($user) ?: abort(response()->json('Not found', 422));
        ($user->verified == 0) ?: abort(response()->json('User already verified!', 406));

        try {
            $updateUser = $this->user->updateEntityUuid(
                [
                    'verification_code' => $this->generateVerificationCode(),
                    'verification_code_generated_at' => now()
                ],
                $user->uuid
            );

            return response()->json(['message' => 'Verification email sent!']);
        } catch (Exception $ex) {
            return response()->json(['message' => 'unable to send reactivation code' . $ex], 500);
        }
    }


    /**
     * Forgot Password.
     *
     * @OA\Get(
     *     path="/{email}/forgot_password",
     *     tags={"Auth"},
     *     operationId="forgotPassword",
     *     description="Sends mail containing token to users who forgot their password",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathorize Access"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     * )
     */
    public function forgotPassword($email) {
        $user = $this->user->findByField('email', $email)->first();

        ($user) ?: abort(response()->json(['message' => 'Data not Found'], 404));

        ($user->verify = true) ?: abort(response()->json(['message' => 'Unverified Account'], 403));

        try {
            $passwordResetPayload = $this->password->updateOrCreate(['email' => $email], [
                'email' => $email,
                'token' => $this->generateIdentity(),
                'created_at' => Carbon::now()
            ]);

            $this->user->updateEntity(['password' => ''], $user->id);

            if ($this->sendEmail('reset_password', $user, $passwordResetPayload->token)) {

                return response()->json(['message' => trans('A reset link has been sent to your email address.')], 200);
            }
        } catch (Exception $ex) {

            return response()->json(['message' => 'A Network Error occurred. Please try again' . $ex], 500);
        }
    }


    /**
     * Reset User password.
     *
     * @OA\Post(
     *     path="/{email}/reset_password",
     *     tags={"Auth"},
     *     operationId="resetUserPassword",
     *     description="Reset a user's password",
     *     @OA\Parameter(name="passwordreset",in="query",required=true,
     *          @OA\Schema(ref="#/components/schemas/PasswordReset")
     *	    ),
     *     @OA\Response(response=401,description="Unathorize Access"),
     *     @OA\Response(response=200,description="successful operation"),
     * )
     */
    public function resetPassword(ResetPasswordFormRequest $request, $email) {
        ($tokenData = $this->password->findByField('email', $email)->first()) ?: abort(response()->json('Email does not exist', 422));

        ($user = $this->user->findWhere(['email' => $tokenData->email, 'verified' => 1])->first() ?: abort(response()->json('Email not verified', 422)));
        ($this->verifyTimeDiff($tokenData->created_at)) ?: abort(response()->json(['message' => 'Code Expired'], 422));

        try {
            $user = $this->user->updateEntity(['password' => $request->password], $user->id);
            $this->password->deleteWhere(['token' => $request->token]);
            $token = auth()->login($user);
            return $this->getUser($user, $token);
        } catch (Exception $ex) {

            return response()->json(['message' => trans('A network error occurred. Please try again.' . $ex)] . 408);
        }
    }


    /**
     * @OA\Get(
     *     path="/logout",
     *     tags={"Auth"},
     *     operationId="userLogout",
     *     description="Logout a user",
     *     @OA\Response(
     *         response=401,
     *         description="Unathorize Access"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     * security={
     *         {"APP_auth": {"read:user"}}
     *     },
     * )
     */

    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
