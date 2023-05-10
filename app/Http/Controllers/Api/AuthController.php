<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Http\Requests\LoginFormRequest;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ResetPasswordFormRequest;
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

            return $this->sendResponse($data, "successfully registered user", 201);
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Error encountered');
        }
    }


    /**
     * @OA\Post(
     *     path="/login",
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
            return $this->sendError($ex->getMessage(), 'Error encountered');
        }
    }

    /**
     *
     * @OA\Get(
     *     path="/{email}/verify/{verification_code}",
     *     tags={"Auth"},
     *     summary="Verify a User Email Address",
     *     description="This can be done by a guest user",
     *     operationId="verifyUser",
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

            return $this->sendResponse($data, "successfully verified user");
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Error encountered');
        }
    }

    /**
     * Resend verification link.
     *
     * @OA\Get(
     *     path="/{email}/resend_verification_code",
     *     tags={"Auth"},
     *     summary="Resend verification Link",
     *     description="Resend verification link",
     *     operationId="resendVerification",
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
            return $this->sendError($ex->getMessage(), 'Error encountered');
        }
    }

    /**
     * Forgot Password.
     *
     * @OA\Get(
     *     path="/{email}/forgot_password",
     *     tags={"Auth"},
     *     summary="Send Forgot Password Email",
     *     description="This can be done by a guest user, to reset password",
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
        try {
            $data = $this->authService->forgotPassword($email);

            return $this->sendResponse($data, "successfully sent forgot password email");
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Error encountered', 500);
        }
    }

    /**
     * Reset User password.
     *
     * @OA\Post(
     *     path="/{email}/reset_password",
     *     tags={"Auth"},
     *     summary="Reset User Password",
     *     description="This can be done by a guest user",
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
        try {
            $data = $this->authService->resetPassword($email, $request->all());

            return $this->sendResponse($data, "Password reset successful");
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Error encountered');
        }
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Auth"},
     *     summary="Logout a User",
     *     description="Logout a user",
     *     operationId="userLogout",
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
        try {
            $data = $this->authService->logout();

            return $this->sendResponse($data, "Logout Successful");
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Error encountered', 500);
        }
    }
}
