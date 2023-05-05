<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Http\Requests\LoginFormRequest;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UserFormRequest;
use App\Http\Services\AuthService;
use App\Http\Trait\UserDetailsTrait;

class AuthController extends ApiController {

    use UserDetailsTrait;

    public function __construct(private AuthService $authService) {
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
        try {
            $data = $this->authService->register($request->all());

            return $this->sendResponse($data, "successfully registered user");
        } catch (Exception $ex) {
            return $this->sendError($ex, 'Error encountered', 500);
        }
    }


    /**
     * @OA\Post(
     *     path="login",
     *     tags={"Auth"},
     *     summary="Login a User",
     *     description="This can be done by a guest user",
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
            $data = $this->authService->login($request->all());

            return $this->sendResponse($data, "successfully logged in user");
        } catch (Exception $ex) {
            return $this->sendError($ex, 'Error encountered', 500);
        }
    }

    /**
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
        try {
            $data = $this->authService->verifyEmail($email, $verificationCode);

            return $this->sendResponse($data, "successfully verified user user");
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Error encountered', 500);
        }
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
        try {
            $data = $this->authService->resendVerificationEmail($email);

            return $this->sendResponse($data, "successfully resent email");
        } catch (Exception $ex) {
            return $this->sendError($ex, 'Error encountered', 500);
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

    /*

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

    */

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

    /*

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

    
    */

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
