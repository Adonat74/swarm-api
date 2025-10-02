<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\ImagesRequest;
use App\Models\Event;
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
     *     path="/api/events/{id}",
     *     summary="Get one event by id - need to be authentified as user and be part of the group",
     *     tags={"Events"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     * @throws AuthorizationException
     */
    public function getEvent(Event $event): JsonResponse
    {
        try {
            $this->authorize('view', $event); // policy check

            return response()->json($event);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}/users",
     *     summary="Get one event by id and it's users - need to be authentified as user and be part of the group",
     *     tags={"Events"},
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
    public function getEventUsers(Event $event): JsonResponse
    {
        try {
            $this->authorize('view', $event); // policy check

            $participants = $event->load(['users']);
            return response()->json($participants);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/events/{id}/comments",
     *     summary="Get one event by id and it's comments - need to be authentified as user and be part of the group",
     *     tags={"Events"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Group not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     * @throws AuthorizationException
     */
    public function getEventComments(Event $event): JsonResponse
    {
        try {
            $this->authorize('view', $event); // policy check

            return response()->json($event->load(['comments']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}/images",
     *     summary="Get one event by id and it's images - need to be authentified as user and be part of the group",
     *     tags={"Events"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the group",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     * @throws AuthorizationException
     */
    public function getEventImages(Event $event): JsonResponse
    {
        try {
            $this->authorize('view', $event); // policy check

            return response()->json($event->load(['images']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Add a event - need to be authentified as user",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the event",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   required={"name", "image"},
     *                   @OA\Property(property="name", type="string",description="Event's name"),
     *                   @OA\Property(property="image", type="string", format="binary")
     *               )
     *          )
     *     ),
     *     @OA\Response(response=201, description="Event successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function addEvent(EventRequest $request): JsonResponse
    {
        try{
            $user = Auth::user();

            $group = Group::findOrFail($request->group_id);
            $this->authorize('createEvent', $group); // policy check

            $event = Event::create($request->safe()->except(['images']));
            $event->users()->attach($user->id, [
                'is_creator' => true
            ]);
            $this->imagesManagementService->addImages($request, $event, 'event_id');

            return response()->json($event->load(['images', 'users']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('event', $e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/events/{id}/images",
     *     summary="Add a event images - need to be authentified as user and be part of the group",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the event",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   required={"name", "image"},
     *                   @OA\Property(property="name", type="string",description="Event's name"),
     *                   @OA\Property(property="image", type="string", format="binary")
     *               )
     *          )
     *     ),
     *     @OA\Response(response=201, description="Event successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function addEventImages(ImagesRequest $request, Event $event): JsonResponse
    {
        try{
            $this->authorize('addEventImages', $event); // policy check

            $this->imagesManagementService->addImages($request, $event, 'event_id');

            return response()->json($event->load(['images']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('event', $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/events/{id}/participate",
     *     summary="Participate to event - need to be authentified as user",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the event",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function participateEvent(Event $event): JsonResponse
    {
        try{
            $user = Auth::user();

            $this->authorize('participateEvent', $event); // policy check

            $event->users()->attach($user->id);

            $event->load([
                'users' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ]);
            return response()->json($event);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('group', $e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/events/{id}/leave",
     *     summary="leave event - need to be authentified as user",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the event",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Group successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function leaveEvent(Event $event): JsonResponse
    {
        try{
            $user = Auth::user();

            $this->authorize('leaveEvent', $event); // policy check

            $pivot = $event->users()->where('user_id', $user->id)->firstOrFail()->pivot;
            $pivot->delete();

            return response()->json(['message' => 'You leaved the event']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('group', $e);
        }
    }
}
