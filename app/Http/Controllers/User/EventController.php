<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\GroupUser;
use App\Services\ErrorsService;
use App\Services\FilterUsersService;
use App\Services\GroupUserService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{    use AuthorizesRequests;
    protected ImagesManagementService $imagesManagementService;
    protected ErrorsService $errorsService;
    protected FilterUsersService $filterUsersService;
    protected GroupUserService $groupUserService;

    public function __construct(
        ImagesManagementService $imagesManagementService,
        ErrorsService $errorsService,
        FilterUsersService $filterUsersService,
        GroupUserService $groupUserService
    )
    {
        $this->imagesManagementService = $imagesManagementService;
        $this->errorsService = $errorsService;
        $this->filterUsersService = $filterUsersService;
        $this->groupUserService = $groupUserService;
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
     *     path="/api/groups/{id}/request",
     *     summary="Request to join group - need to be authentified as user",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the group",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function joinGroupRequest(Group $group): JsonResponse
    {
        try{
            $user = Auth::user();

            $this->groupUserService->requestToJoin($group, $user);

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


    /**
     * @OA\Post(
     *     path="/api/groups/{id}/invitation/approve",
     *     summary="Accept invitation to join group - need to be authentified as user",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the group",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function approveInvitation(Group $group): JsonResponse
    {
        try{
            $user = Auth::user();

            $pivot = $group->users()->where('user_id', $user->id)->firstOrFail()->pivot;
            $this->authorize('updateStatus', $pivot);

            $group->users()->updateExistingPivot($user->id, [
                'status' => GroupUser::STATUS_APPROVED
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


    /**
     * @OA\Post(
     *     path="/api/groups/{id}/invitation/reject",
     *     summary="Reject invitation to join group - need to be authentified as user",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the group",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function rejectInvitation(Group $group): JsonResponse
    {
        try{
            $user = Auth::user();

            $pivot = $group->users()->where('user_id', $user->id)->firstOrFail()->pivot;
            $this->authorize('updateStatus', $pivot);

            $pivot->delete();

            return response()->json(['message' => 'You rejected the invitation']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('group', $e);
        }
    }}
