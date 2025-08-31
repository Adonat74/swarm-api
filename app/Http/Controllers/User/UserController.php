<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
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
     *     path="/api/users/{id}",
     *     summary="Get one user by id - need to be authentified as user",
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
    public function getUser(User $user): JsonResponse
    {
        try {
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/groups",
     *     summary="Get one user by id and it's groups - need to be authentified as admin",
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
    public function getUserGroups(User $user): JsonResponse
    {
        try {
            return response()->json($user->load(['groups']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/events",
     *     summary="Get one user by id and it's events - need to be authentified as admin",
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
    public function getUserEvents(User $user): JsonResponse
    {
        try {
            return response()->json($user->load(['events']));
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }




    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="add a new user- need to be authentified as admin",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"email", "password", "username", "city", "postal_code", "country"},
     *                  @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                  @OA\Property(property="password", type="string", description="User's password (minimum 10 characters)"),
     *                  @OA\Property(property="username", type="string", maxLength=40, description="the username"),
     *                  @OA\Property(property="city", type="string", maxLength=255, description="User's city"),
     *                  @OA\Property(property="postal_code", type="string", description="User's postal code (5 digits)"),
     *                  @OA\Property(property="country", type="string", maxLength=20, description="User's country"),
     *                  @OA\Property(property="image", type="string", format="binary")
     *              )
     *          )
     *     ),
     *     @OA\Response(response=201, description="User successfully created"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function addUser(AdminUserRequest $request): JsonResponse
    {
        try {
            $user = new User($request->safe()->except(['image']));
            $user->save();

            $this->imagesManagementService->addSingleImage($request, $user, 'user_id');

            return response()->json($user, 201);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/users/{id}",
     *     summary="Update an existing user- need to be authentified as admin",
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
     *              @OA\Schema(
     *                  required={"email", "password", "username", "city", "postal_code", "country"},
     *                     @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                     @OA\Property(property="password", type="string", description="User's password (minimum 10 characters)"),
     *                     @OA\Property(property="username", type="string", maxLength=40, description="the username"),
     *                     @OA\Property(property="city", type="string", maxLength=255, description="User's city"),
     *                     @OA\Property(property="postal_code", type="string", description="User's postal code (5 digits)"),
     *                     @OA\Property(property="country", type="string", maxLength=20, description="User's country"),
     *                     @OA\Property(property="image", type="string", format="binary")
     *              )
     *          )
     *     ),
     *     @OA\Response(response=200, description="User successfully updated"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="An error occurred")
     * )
     */
    public function updateUser(AdminUserRequest $request, User $user): JsonResponse
    {
        try{
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
     *     path="/api/users/{id}",
     *     summary="Delete a user by id- need to be authentified as admin",
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
    public function deleteUser(User $user): JsonResponse
    {
        try {
            $this->imagesManagementService->deleteSingleImage($user);
            $user->delete();

            return response()->json(['message' => 'user deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorsService->modelNotFoundException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }}
