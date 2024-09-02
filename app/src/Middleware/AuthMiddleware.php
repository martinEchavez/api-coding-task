<?php

namespace App\Middleware;

use App\Services\AuthService;
use App\Utils\Response;

class AuthMiddleware
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle($request, $next)
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            $decoded = $this->authService->validateToken($token);

            if ($decoded) {
                $request['user'] = $decoded;
                return $next($request);
            }
        }

        return Response::json(['error' => 'Unauthorized'], 401);
    }
}