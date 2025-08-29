<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminEventRequest;
use App\Models\Event;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AdminEventController extends Controller
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
     *     path="/api/admin/events",
     *     summary="Get all events- need to be authentified as admin",
     *     tags={"AdminEvents"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getAllEvents(): JsonResponse
    {
        try {
            $events = Event::all();
            return response()->json($events);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/admin/events/{id}",
     *     summary="Get one event by id - need to be authentified as admin",
     *     tags={"AdminEvents"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getEvent(Event $event): JsonResponse
    {
        try {
            return response()->json($event);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/admin/events/{id}/users",
     *     summary="Get one event by id and it's users - need to be authentified as admin",
     *     tags={"AdminEvents"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getEventUsers(Event $event): JsonResponse
    {
        try {
            return response()->json($event->load(['users']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/events/{id}/comments",
     *     summary="Get one event by id and it's comments - need to be authentified as admin",
     *     tags={"AdminEvents"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getEventComments(Event $event): JsonResponse
    {
        try {
            return response()->json($event->load(['comments']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/events/{id}/images",
     *     summary="Get one event by id and it's images - need to be authentified as admin",
     *     tags={"AdminEvents"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getEventImages(Event $event): JsonResponse
    {
        try {
            return response()->json($event->load(['images']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/admin/events/{id}",
     *     summary="Delete a event by id- need to be authentified as admin",
     *     tags={"AdminEvents"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the event",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function deleteEvent(Event $event): JsonResponse
    {
        try {
            $this->imagesManagementService->deleteSingleImage($event);
            $event->delete();

            return response()->json(['message' => 'event deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }

}
