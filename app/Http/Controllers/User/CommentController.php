<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Event;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

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
}
