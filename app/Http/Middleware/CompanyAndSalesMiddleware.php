<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAndSalesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth_exists = Auth::where('token', 'Bearer ' . $request->bearerToken())->exists();
        // dd($request->bearerToken());
        if ($auth_exists) {
            $auth = Auth::where('token', 'Bearer ' . $request->bearerToken())->first();
            if ($auth->role_id == 3 || $auth->role_id == 5 || $auth->role_id == 4) {
                return $next($request);
            } else {
                return response()->json(['Unauthenticated'], 401);
            }
        } else {
            return response()->json(['Unauthenticated'], 401);
        }
    }
}
