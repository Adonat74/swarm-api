<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use App\Services\ErrorsService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AdminCommentController extends Controller
{
    protected ErrorsService $errorsService;

    public function __construct(
        ErrorsService $errorsService
    )
    {
        $this->errorsService = $errorsService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/events/{id}/comments",
     *     summary="Get one event by id and it's comments - need to be authentified as admin",
     *     tags={"AdminComments"},
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
            return response()->json($event->load(['comments.user', 'comments.parent']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('event', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{id}/comments",
     *     summary="Get one user by id and it's comments - need to be authentified as admin",
     *     tags={"AdminComments"},
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
    public function getUserComments(User $user): JsonResponse
    {
        try {
            return response()->json($user->load(['comments.parent', 'comments.event']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/admin/comments/{id}/replies",
     *     summary="Get one comment by id and it's replies - need to be authentified as admin",
     *     tags={"AdminComments"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the comment",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Comment not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function getCommentReplies(Comment $comment): JsonResponse
    {
        try {
            return response()->json($comment->load(['event', 'user', 'replies.user', 'replies.event']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('comment', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('comment', $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/comments/{id}",
     *     summary="Delete a comment by id- need to be authentified as admin",
     *     tags={"AdminComments"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the comment",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function deleteComment(Comment $comment): JsonResponse
    {
        try {
            $comment->delete();
            return response()->json(['message' => 'comment deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('comment', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('comment', $e);
        }
    }}
