<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserFormRequest;
use Exception;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;

class UserController extends ApiController {
    public function __construct(private UserService $userService) {
        $this->middleware('acceptjson', ['only' =>  'update']);
        $this->middleware('role:admin,super-admin', ['only' => ['index']]);
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     operationId="getAllUsers",
     *     tags={"User"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *     @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */

    public function index() {
        try {
            $roles = $this->userService->findAll();

            return $this->sendResponse(UserResource::collection($roles), "successfully fetched users");
        } catch (Exception $ex) {
            return $this->sendError($ex, 'Error encountered', 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/users/{user_uuid}",
     *     tags={"User"},
     *     summary="Get user by uuid",
     *     operationId="getAUserByUUID",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     ),
     * )
     */

    public function show($uuid) {
        try {
            $user = $this->userService->findOne(['uuid' => $uuid]);

            return $this->sendResponse(new UserResource($user));
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'unable to fetch user', 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/users/{uuid}",
     *     tags={"User"},
     *     summary="Updated user",
     *     description="This can only be done by an auth users.",
     *     operationId="updateUser",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Updated user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUser")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessible Entity"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     */

    public function update(UserFormRequest $request, $uuid) {
        try {
            $user = $this->userService->updateUser($uuid, $request->validated());

            return $this->sendResponse(new UserResource($user));
        } catch (Exception $ex) {
            return $this->sendError($ex, 'unable to update user details', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/users/{user_uuid}",
     *     tags={"User"},
     *     summary="Delete user details",
     *     description="This can only be done by an admin or auth user.",
     *     operationId="deleteUser",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfull operation",
     *     )
     * )
     */

    public function destroy($uuid) {
        try {
            $this->userService->deleteUser($uuid);

            return $this->sendResponse(new UserResource(null, 'User deleted successfully'));
        } catch (Exception $ex) {
            return $this->sendError($ex, 'unable to delete user details', 500);
        }
    }
}
