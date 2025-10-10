<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $isAdmin = $user && $user->admin()->exists();
        $isOwner = $user && $user->owner()->exists();
        $isReceptionist = $user && $user->receptionist()->exists();

        // Customer = bukan admin, owner, atau receptionist
        if (!$user || $isAdmin || $isOwner || $isReceptionist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Customer only.',
            ], 403);
        }

        return $next($request);
    }
}
