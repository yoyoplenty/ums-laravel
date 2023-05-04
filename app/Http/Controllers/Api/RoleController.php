<?php

namespace App\Http\Controllers\Api;

use App\Repositories\RoleRepository as Role;
use App\Http\Requests\RoleFormRequest;
use App\Http\Resources\RoleResource;
use Exception;

class RoleController extends ApiController {

    public function __construct(private Role $role) {
        $this->middleware('acceptjson', ['only' => ['store', 'update']]);
        $this->middleware('auth');
        $this->middleware('role:admin,super-admin');
    }

    /**
     * @OA\Get(
     *     path="/roles",
     *     summary="Get all roles",
     *     operationId="getAllRoles",
     *     tags={"Role"},
     *      description="Returns list of roles",
     *     @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Role"),
     *     ),
     * )
     */

    public function index() {
        try {
            $roles = $this->role->all();

            return $this->sendResponse(
                RoleResource::collection($roles),
                "successfully fetched roles"
            );
        } catch (Exception $ex) {
            return $this->sendError($ex, 'Error encountered', 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/roles",
     *     tags={"Role"},
     *     summary="Create role",
     *     description="This can only be done by an admin.",
     *     operationId="createRole",
     *     @OA\RequestBody(
     *         description="Create role object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateRole")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/Role"),
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessible Entity"
     *     )
     * )
     */

    public function store(RoleFormRequest $request) {
        try {
            $payload = $request->validated();
            $role = $this->role->create($payload);

            return $this->sendResponse(
                new RoleResource($role),
                "successfully created role",
                201
            );
        } catch (Exception $ex) {
            return $this->sendError($ex, 'There was a problem creating role', 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/roles/{role_id}",
     *     tags={"Role"},
     *     summary="Get role by role id",
     *     operationId="getRoleById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Role"),
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

    public function show($id) {
        $role = $this->role->find($id);
        if (!$role) return response()->json([trans('Not Found')], 404);

        return $this->sendResponse(new roleResource($role));
    }


    /**
     * @OA\Patch(
     *     path="/roles/{role_id}",
     *     tags={"Role"},
     *     summary="Updated role",
     *     description="This can only be done by an admin user.",
     *     operationId="updateRole",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Updated role object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateRole")
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
     *          @OA\JsonContent(ref="#/components/schemas/Role"),
     *     )
     * )
     */

    public function update(RoleFormRequest $request, $id) {
        $role = $this->role->find($id);
        if (!$role) return response()->json([trans('Not Found')], 404);

        $payload = $request->only('title', 'description');
        $role = $this->role->update($payload, $id);

        return $this->sendResponse(new RoleResource($role));
    }


    /**
     * @OA\Delete(
     *     path="/roles/{role_id}",
     *     tags={"Role"},
     *     summary="Delete role",
     *     description="This can only be done by an admin user.",
     *     operationId="deleteRole",
     *     @OA\Parameter(
     *         name="id",
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
     *         description="Role not found",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfull operation",
     *     )
     * )
     */

    public function destroy($id) {
        $role = $this->role->find($id);
        if (!$role) return response()->json([trans('Not Found')], 404);

        $role->delete();
        return $this->sendResponse(new RoleResource(null, 'Role deleted successfully'));
    }
}
