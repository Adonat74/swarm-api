<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Event;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;
    protected ImagesManagementService $imagesManagementService;
    protected ErrorsService $errorsService;

    public function __construct(
        ImagesManagementService $imagesManagementService,
        ErrorsService $errorsService,
    )
    {
        $this->imagesManagementService = $imagesManagementService;
        $this->errorsService = $errorsService;
    }


    /**
     * @OA\Get(
     *     path="/api/comments/{id}/replies",
     *     summary="Get one comment by id and it's replies - need to be authentified as user and be part of the group",
     *     tags={"Comments"},
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
     * @throws AuthorizationException
     */
    public function getCommentReplies(Comment $comment): JsonResponse
    {
        try {

            $this->authorize('view', $comment); // policy check

            $replies = $comment->load(['replies'])->makeHidden(['event']);
            return response()->json($replies);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('comment', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('comment', $e);
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
    public function addEventComment(CommentRequest $request, Event $event): JsonResponse
    {
        try{
            $user = Auth::user();

            $this->authorize('addComment', $event); // policy check

            $comment = Comment::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'body' => $request->input('body'),
                // add other fields you allow
            ]);

            return response()->json($comment);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('event', $e);
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
    public function addCommentReply(CommentRequest $request, Comment $comment): JsonResponse
    {
        try{
            $user = Auth::user();

            $this->authorize('addCommentReply', $comment); // policy check

            $event = $comment->event;

            $reply = Comment::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'parent_id' => $comment->id,
                'body' => $request->input('body'),
                // add other fields you allow
            ]);

            return response()->json($reply->load(['parent']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('event', $e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/comments/{id}/likes",
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
    public function addCommentLike(Comment $comment): JsonResponse
    {
        try{
            $user = Auth::user();

            $this->authorize('addCommentLike', $comment); // policy check

            $comment->likes = $comment->likes + 1;
            $comment->save();

            return response()->json($comment);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('event', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('event', $e);
        }
    }

}
