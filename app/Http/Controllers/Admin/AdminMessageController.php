<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AdminMessageController extends Controller
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
     *     path="/api/admin/groups/{id}/messages",
     *     summary="Get one group by id and it's messages - need to be authentified as admin",
     *     tags={"AdminMessages"},
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
            return response()->json($group->load(['messages.user']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('group', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('group', $e);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/admin/users/{id}/messages",
     *     summary="Get one user by id and it's messages - need to be authentified as admin",
     *     tags={"AdminMessages"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the user",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getUserMessages(User $user): JsonResponse
    {
        try {
            return response()->json($user->load(['messages.group']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }



    /**
     * @OA\Delete(
     *     path="/api/admin/messages/{id}",
     *     summary="Delete a message by id- need to be authentified as admin",
     *     tags={"AdminMessages"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the message",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function deleteMessage(Message $message): JsonResponse
    {
        try {
            $message->delete();
            return response()->json(['message' => 'message deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('message', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('message', $e);
        }
    }
}
