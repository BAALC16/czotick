<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

/**
 * Middleware pour les invités d'organisation (utilisateurs non connectés)
 */
class OrganizationGuest
{
    public function handle(Request $request, Closure $next)
    {
        $orgSlug = $request->route('org_slug');
        $user = session('organization_user');

        // Si l'utilisateur est déjà connecté, rediriger vers le dashboard
        if ($user && $user['org_subdomain'] === $orgSlug) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug]);
        }

        return $next($request);
    }
}