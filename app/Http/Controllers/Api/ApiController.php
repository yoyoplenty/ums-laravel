<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Trait\IdentityTrait;


/**
 * @OA\PathItem(path="/api"),
 * 
 * @OA\Info(
 *      version="1.0.0",
 *      title="UMS API DOCUMENTATION",
 *      description="A simple laravel user management system for users",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url='localhost:8000/api/v1/',
 *      description="Demo API Server"
 * )
 * 
 * @OA\Schema(
 *         schema="CreateRole",
 *         required={"title",},
 *         @OA\Property(property="title",description="role name",type="string"),
 *         @OA\Property(property="description",description="role description",type="string"),
 *  ),
 *
 * @OA\Schema(
 *         schema="Role",
 *         @OA\Property(property="id",description="role id",type="integer"),
 *         @OA\Property(property="title",description="role name",type="string"),
 *         @OA\Property(property="description",description="role description",type="string"),
 *         @OA\Property(property="created_at",description="role description",type="string"),
 *         @OA\Property(property="updated_at",description="role description",type="string"),
 *  ),
 *
 * @OA\Schema(
 *         schema="UpdateRole",
 *         required={"id"},
 *         @OA\Property(property="id",description="role id",type="integer"),
 *         @OA\Property(property="title",description="role name",type="string"),
 *         @OA\Property(property="description",description="role description",type="string"),
 *  ),
 * 
 * 
 * @OA\Schema(
 *         schema="User",
 *         @OA\Property(property="firstname", type="string"),
 *         @OA\Property(property="lastname", type="string"),
 *         @OA\Property(property="middlename", type="string"),
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="phone_number", type="integer"),
 *         @OA\Property(property="profile_url", type="string"),
 *         @OA\Property(property="password", type="string"),
 *         @OA\Property(property="created_at", type="string"),
 *         @OA\Property(property="deleted_at", type="string"),
 *  ),
 * 
 * @OA\Schema(
 *         schema="CreateUser",
 *         @OA\Property(property="firstname", type="string"),
 *         @OA\Property(property="lastname", type="string"),
 *         @OA\Property(property="middlename", type="string"),
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="phone_number", type="integer"),
 *         @OA\Property(property="password", type="string"),
 *  ),
 * 
 * 
 * @OA\Schema(
 *         schema="UpdateUser",
 *         required={"uuid"},
 *         @OA\Property(property="firstname", type="string"),
 *         @OA\Property(property="lastname", type="string"),
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="phone", type="integer"),
 *         @OA\Property(property="password", type="string"),
 *         @OA\Property(property="created_at", type="string"),
 *         @OA\Property(property="deleted_at", type="string"),
 *  ),
 *
 * @OA\Schema(
 *         schema="UserLogin",
 *         required={"email", "password", "role_id"},
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="password", type="string"),
 *         @OA\Property(property="role_id", type="integer"),
 *  ),
 * 
 * @OA\Schema(
 *         schema="PasswordReset",
 *         required={"password", "token"},
 *         @OA\Property(property="password", type="string"),
 *         @OA\Property(property="token", type="string"),
 *  ),
 * 
 */



class ApiController extends BaseController {

    use IdentityTrait;

    /** 
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = 'Successfully fetched data', $code = 200) {
        $response = [
            'message' => $message,
            'success' => true,
            'data'    => $result,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = 'An error occured', $code = 422) {
        $response = [
            'message' => empty($error) ?: $errorMessages,
            'success' => false,
        ];

        return response()->json($response, $code);
    }
}
