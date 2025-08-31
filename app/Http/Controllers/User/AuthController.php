<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Services\ErrorsService;
use App\Services\ImagesManagementService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
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
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"email", "password"},
     *                  @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                  @OA\Property(property="password", type="string", description="User's password"),
     *              )
     *          )
     *     ),
     *     @OA\Response(response=200, description="User successfully login"),
     *     @OA\Response(response=401, description="Credentials mismatch"),
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        $user->token_version += 1;
        $user->save();
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user->load(['images']),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register new user",
     *     tags={"Auth"},
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
    public function register(AuthRequest $request): JsonResponse
    {
        try {
            $user = new User($request->safe()->except(['image']));
            $user->save();

            $this->imagesManagementService->addSingleImage($request, $user, 'user_id');


            $credentials = $request->only('email', 'password');
            $token = Auth::attempt($credentials);
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user->load(['images']),
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        } catch (ValidationException $e) {
            return $this->errorsService->validationException('user', $e);
        } catch (Exception $e) {
            return $this->errorsService->exception('user', $e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/refresh",
     *     summary="refresh the token- need to be authentified and role = user",
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="User fetched"),
     * )
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }



    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout - need to be authentified and role = user",
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="User successfully logout"),
     * )
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

}
