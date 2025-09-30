<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ErrorsService;
use App\Services\FilterGroupsService;
use App\Services\FilterUsersService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected ImagesManagementService $imagesManagementService;
    protected ErrorsService $errorsService;
    protected FilterGroupsService $filterGroupsService;

    public function __construct(
        ImagesManagementService $imagesManagementService,
        ErrorsService $errorsService,
        FilterGroupsService $filterGroupsService

    )
    {
        $this->imagesManagementService = $imagesManagementService;
        $this->errorsService = $errorsService;
        $this->filterGroupsService = $filterGroupsService;
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get authenticated user",
     *     tags={"Users"},
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
    public function getUser(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/groups",
     *     summary="Get authenticated user groups",
     *     tags={"Users"},
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
    public function getUserGroups(): JsonResponse
    {
        try {
            $user = Auth::user();
            $userWithApprovedGroup = $this->filterGroupsService->filterUserApprovedGroups($user);

            return response()->json($userWithApprovedGroup);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/events",
     *     summary="Get authenticated user events",
     *     tags={"Users"},
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
    public function getUserEvents(): JsonResponse
    {
        try {
            $user = Auth::user();
            return response()->json($user->load(['events']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Update authenticated user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   required={"email", "password", "username", "city", "postal_code", "country"},
     *                      @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                      @OA\Property(property="password", type="string", description="User's password (minimum 10 characters)"),
     *                      @OA\Property(property="username", type="string", maxLength=40, description="the username"),
     *                      @OA\Property(property="city", type="string", maxLength=255, description="User's city"),
     *                      @OA\Property(property="postal_code", type="string", description="User's postal code (5 digits)"),
     *                      @OA\Property(property="country", type="string", maxLength=20, description="User's country"),
     *                      @OA\Property(property="image", type="string", format="binary")
     *               )
     *          )
     *     ),
     *     @OA\Response(response=200, description="User successfully updated"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function updateUser(UserRequest $request): JsonResponse
    {
        try{
            $user = Auth::user();

            $user->update($request->safe()->except(['image']));

            $this->imagesManagementService->updateSingleImage($request, $user, 'user_id');

            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e){
            return $this->errorsService->exception('user', $e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/user",
     *     summary="Delete authenticated user",
     *     tags={"Users"},
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
    public function deleteUser(): JsonResponse
    {
        try {
            $user = Auth::user();

            $this->imagesManagementService->deleteSingleImage($user);
            $user->delete();

            return response()->json(['message' => 'user deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }}
