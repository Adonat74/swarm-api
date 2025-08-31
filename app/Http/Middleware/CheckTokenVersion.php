<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();
            $user = auth()->user();

            if ($payload->get('token_version') !== $user->token_version) {
                return response()->
                json(['error' => 'Token has been invalidated'], 401);
            }
            return $next($request);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}
