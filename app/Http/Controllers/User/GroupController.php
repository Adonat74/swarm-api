<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGroupRequest;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Models\Group;
use App\Services\ErrorsService;
use App\Services\FilterUsersService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    use AuthorizesRequests;
    protected ImagesManagementService $imagesManagementService;
    protected ErrorsService $errorsService;
    protected FilterUsersService $filterUsersService;

    public function __construct(
        ImagesManagementService $imagesManagementService,
        ErrorsService $errorsService,
        FilterUsersService $filterUsersService

    )
    {
        $this->imagesManagementService = $imagesManagementService;
        $this->errorsService = $errorsService;
        $this->filterUsersService = $filterUsersService;
    }


    /**
     * @OA\Get(
     *     path="/api/groups/{id}",
     *     summary="Get one group by id - need to be authentified as user and be part of the group",
     *     tags={"Groups"},
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
     * @throws AuthorizationException
     */
    public function getGroup(Group $group): JsonResponse
    {
        $this->authorize('view', $group); // policy check

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
     *     path="/api/groups/{id}/events",
     *     summary="Get one group by id and it's events - need to be authentified as user and be part of the group",
     *     tags={"Groups"},
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
     * @throws AuthorizationException
     */
    public function getGroupEvents(Group $group): JsonResponse
    {
        $this->authorize('view', $group); // policy check

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
     *     path="/api/groups/{id}/users",
     *     summary="Get one group by id and it's users - need to be authentified as user and be part of the group",
     *     tags={"Groups"},
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
     * @throws AuthorizationException
     */
    public function getGroupUsers(Group $group): JsonResponse
    {
        $this->authorize('view', $group); // policy check

        try {
            $groupWithApprovedUsers = $this->filterUsersService->filterUsersApprovedInGroup($group);
            return response()->json($groupWithApprovedUsers);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/groups/{id}/events/images",
     *     summary="Get one group by id and it's events images - need to be authentified as user and be part of the group",
     *     tags={"Groups"},
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
     * @throws AuthorizationException
     */
    public function getGroupImages(Group $group): JsonResponse
    {
        $this->authorize('view', $group); // policy check

        try {
            $events = $group->events()
                ->select('id', 'name', 'date_time', 'location')
                ->with('images')
                ->get();

            return response()->json($events);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/groups",
     *     summary="Add a group - need to be authentified as user",
     *     tags={"Groups"},
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
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function addGroup(GroupRequest $request): JsonResponse
    {
        try{
            $user = Auth::user();

            $group = Group::create($request->safe()->except(['image']));
            $group->users()->attach($user->id, [
                'is_creator' => true,
                'status' => 'approved'
            ]);
            $this->imagesManagementService->addSingleImage($request, $group, 'group_id');

            return response()->json($group->load(['images', 'users']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('group', $e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/groups/{id}/status",
     *     summary="Update the user group status - need to be authentified as user",
     *     tags={"Groups"},
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
     *                   required={"status"},
     *                   @OA\Property(property="status", type="string",description="Status to change"),
     *               )
     *          )
     *     ),
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function updateGroupUserStatus(UpdateStatusRequest $request, Group $group): JsonResponse
    {
        try{
            $user = Auth::user();
            $group->users()->updateExistingPivot($user->id, [
                'status' => $request->status
            ]);

            $group->load([
                'users' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ]);
            return response()->json($group);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('group', $e);
        }
    }
}
