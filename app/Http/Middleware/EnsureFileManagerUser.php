<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFileManagerUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() || config('file_manager.allow_guest', false)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication is required to use the file manager.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response('Authentication is required to use the file manager.', Response::HTTP_UNAUTHORIZED);
    }
}
