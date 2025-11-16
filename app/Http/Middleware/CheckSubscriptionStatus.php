<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckSubscriptionStatus
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
            return $next($request);
        }

        // Vérifier l'expiration de l'essai
        if ($organization->subscription_plan === 'trial' && 
            $organization->trial_ends_at && 
            Carbon::parse($organization->trial_ends_at)->isPast()) {
            
            // Mettre à jour le statut
            DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $orgId)
                ->update(['subscription_status' => 'expired']);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Période d\'essai expirée',
                    'action_required' => 'upgrade_subscription'
                ], 402);
            }
            
            return redirect()->route('organization.dashboard')
                ->with('warning', 'Votre période d\'essai a expiré. Veuillez mettre à niveau votre abonnement.');
        }

        // Vérifier l'expiration de l'abonnement
        if ($organization->subscription_ends_at && 
            Carbon::parse($organization->subscription_ends_at)->isPast()) {
            
            DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $orgId)
                ->update(['subscription_status' => 'expired']);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Abonnement expiré',
                    'action_required' => 'renew_subscription'
                ], 402);
            }
            
            return redirect()->route('organization.dashboard')
                ->with('error', 'Votre abonnement a expiré. Veuillez le renouveler.');
        }

        return $next($request);
    }
}