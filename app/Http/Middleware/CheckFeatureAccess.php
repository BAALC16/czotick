<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\DB;

class CheckFeatureAccess
{
    protected $planFeatures = [
        'trial' => [
            'max_events' => 1,
            'max_participants_per_event' => 50,
            'max_storage_mb' => 50,
            'advanced_analytics' => false,
            'custom_branding' => false,
            'api_access' => false,
        ],
        'basic' => [
            'max_events' => 5,
            'max_participants_per_event' => 200,
            'max_storage_mb' => 500,
            'advanced_analytics' => false,
            'custom_branding' => false,
            'api_access' => false,
        ],
        'premium' => [
            'max_events' => 20,
            'max_participants_per_event' => 1000,
            'max_storage_mb' => 2000,
            'advanced_analytics' => true,
            'custom_branding' => true,
            'api_access' => true,
        ],
        'enterprise' => [
            'max_events' => -1, // illimité
            'max_participants_per_event' => -1,
            'max_storage_mb' => 10000,
            'advanced_analytics' => true,
            'custom_branding' => true,
            'api_access' => true,
        ],
    ];

    public function handle(Request $request, Closure $next, $feature = null)
    {
        if (!AuthHelper::isOrganizationLoggedIn()) {
            return $next($request);
        }

        $plan = AuthHelper::subscriptionPlan();
        
        if (!$plan || !isset($this->planFeatures[$plan])) {
            return $next($request);
        }

        $features = $this->planFeatures[$plan];

        // Vérifier une fonctionnalité spécifique
        if ($feature && isset($features[$feature]) && !$features[$feature]) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Fonctionnalité non disponible dans votre plan',
                    'feature' => $feature,
                    'current_plan' => $plan
                ], 403);
            }
            
            return redirect()->route('organization.dashboard')
                ->with('error', 'Cette fonctionnalité n\'est pas disponible dans votre plan actuel.');
        }

        // Ajouter les informations de plan à la requête
        $request->attributes->set('subscription_features', $features);
        $request->attributes->set('subscription_plan', $plan);

        return $next($request);
    }
}
