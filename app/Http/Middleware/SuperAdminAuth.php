<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('super_admin_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            
            return redirect()->route('super-admin.login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page');
        }

        return $next($request);
    }
}