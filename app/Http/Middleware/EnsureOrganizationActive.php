<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\DB;

class EnsureOrganizationActive
{
    public function handle(Request $request, Closure $next)
    {
        if (!AuthHelper::isOrganizationLoggedIn()) {
            return $next($request);
        }

        $orgId = AuthHelper::organizationId();
        
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $orgId)
            ->first();

        if (!$organization) {
            session()->forget('organization_user');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Organisation non trouvée'
                ], 404);
            }
            
            return redirect()->route('organization.login')
                ->with('error', 'Organisation non trouvée.');
        }
        
        // La vérification de l'abonnement se fait maintenant au niveau des événements
        // Plus besoin de vérifier subscription_status ici

        return $next($request);
    }
}
