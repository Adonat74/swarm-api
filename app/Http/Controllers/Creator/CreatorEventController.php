<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatorEventRequest;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Group;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CreatorEventController extends Controller
{

    use AuthorizesRequests;

    protected ImagesManagementService $imagesManagementService;
    protected ErrorsService $errorsService;

    public function __construct(
        ImagesManagementService $imagesManagementService,
        ErrorsService           $errorsService,
    )
    {
        $this->imagesManagementService = $imagesManagementService;
        $this->errorsService = $errorsService;
    }


    /**
     * @OA\Post(
     *     path="/api/creator/events/{id}",
     *     summary="Update an event - need to be authentified as user and be creator of the event",
     *     tags={"CreatorEvents"},
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
    public function updateEvent(CreatorEventRequest $request, Event $event): JsonResponse
    {
        try{
            $user = Auth::user();

            $group = Group::findOrFail($event->group_id);
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
    }}
