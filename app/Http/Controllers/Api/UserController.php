<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DeleteFormRequest;
use App\Http\Requests\UserFormRequest;
use Exception;
use App\Repositories\RoleRepository as Role;
use App\Repositories\UserRepository as User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends ApiController {
    public function __construct(private User $user, private Role $role) {
        $this->middleware('acceptjson', ['only' => ['store', 'update', 'destroy']]);

        $this->middleware('auth', ['except' => ['store']]);
        $this->middleware('role:admin,super-admin', ['only' => ['index', 'destroy']]);
    }

    /**
     * @OA\Get(
     *     path="/users?role=Admin,",
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
    public function index($perPage = 15) {
        if ($query = request()->query('role') != null) {
            $role = $this->role->where('title', $query)->first();
            ($role) ?: abort(response()->json('Not found', 404));
            (count($role->users) >= 1) ?: abort(response()->json('No content', 206));
            return UserResource::collection($role->users);
        }
        $users = $this->user->all();
        return UserResource::collection($users);
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
            $user = $this->user->findByField('uuid', $uuid)->first();
            if (!$user)  return response()->json(['message' => 'Not found'], 404);

            return $this->sendResponse(new UserResource($user));
        } catch (Exception $ex) {

            return response()->json(['message' => 'unable to fetch user' . $ex], 500);
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
     *         description="Role not found"
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
        ($uuid = $request->uuid) ?: abort(response()->json(['message' => 'invalid credentials'], 406));
        $user = $this->user->findByField('$uuid', $uuid)->first();
        ($user) ?: abort(response()->json('Data not found', 404));
        try {
            $updateUser = $this->user->updateEntityUuid($request->validated(), $uuid);

            return $this->sendResponse(new UserResource($updateUser));
        } catch (Exception $ex) {
            return response()->json(['message' => 'unable to update user details' . $ex], 500);
        }
    }



    public function _destory(Request $request, $uuid) {
        try {
            $user = $this->user->findByField('uuid', $uuid)->first();

            if (!$user || $uuid != $request->uuid) {
                return response()->json(['message' => 'user not found'], 404);
            }

            $user['email'] = $user->email . '_deleted';
            $user['username'] = $user->username . '_deleted';
            $user->update();
            $user->delete();
            return response()->json(['message' => 'User successfully deleted.'], 200);
        } catch (Exception $ex) {

            return response()->json('Unable to delete user please contact admin for support with the following details Error: ' . $ex, 500);
        }
    }
}
