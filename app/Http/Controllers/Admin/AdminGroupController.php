<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGroupRequest;
use App\Models\Group;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminGroupController extends Controller
{
    protected ImagesManagementService $imagesManagementService;
    protected ErrorsService $errorsService;

    public function __construct(
        ImagesManagementService $imagesManagementService,
        ErrorsService $errorsService

    )
    {
        $this->imagesManagementService = $imagesManagementService;
        $this->errorsService = $errorsService;
    }



    /**
     * @OA\Get(
     *     path="/api/admin/groups",
     *     summary="Get all groups- need to be authentified as admin",
     *     tags={"AdminGroups"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getAllGroups(): JsonResponse
    {
        try {
            $groups = Group::all();
            return response()->json($groups);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/admin/groups/{id}",
     *     summary="Get one group by id - need to be authentified as admin",
     *     tags={"AdminGroups"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the group",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getGroup(Group $group): JsonResponse
    {
        try {
            return response()->json($group);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/admin/groups/{id}/users",
     *     summary="Get one group by id and it's users - need to be authentified as admin",
     *     tags={"AdminGroups"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the group",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getGroupUsers(Group $group): JsonResponse
    {
        try {
            return response()->json($group->load(['users']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/groups/{id}/events",
     *     summary="Get one group by id and it's events - need to be authentified as admin",
     *     tags={"AdminGroups"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the group",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getGroupEvents(Group $group): JsonResponse
    {
        try {
            return response()->json($group->load(['events']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/groups/{id}/messages",
     *     summary="Get one group by id and it's messages - need to be authentified as admin",
     *     tags={"AdminGroups"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the group",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getGroupMessages(Group $group): JsonResponse
    {
        try {
            return response()->json($group->load(['messages']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/groups/{id}",
     *     summary="Update an existing group- need to be authentified as admin",
     *     tags={"AdminGroups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the group",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   required={"name", "image"},
     *                   @OA\Property(property="name", type="string",description="Group's name"),
     *                   @OA\Property(property="image", type="string", format="binary")
     *               )
     *          )
     *     ),
     *     @OA\Response(response=200, description="Group successfully updated"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function updateGroup(AdminGroupRequest $request, Group $group): JsonResponse
    {
        try{
            $group->update($request->safe()->except(['image']));

            $this->imagesManagementService->updateSingleImage($request, $group, 'group_id');

            return response()->json($group);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('group', $e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/admin/groups/{id}",
     *     summary="Delete a group by id- need to be authentified as admin",
     *     tags={"AdminGroups"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the group",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function deleteGroup(Group $group): JsonResponse
    {
        try {
            $this->imagesManagementService->deleteSingleImage($group);
            $group->delete();

            return response()->json(['message' => 'group deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

}
