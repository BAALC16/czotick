<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifierAuth
{
    public function handle(Request $request, Closure $next)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        Log::info('VerifierAuth middleware - vérification', [
            'authenticated' => session()->has('verifier_authenticated'),
            'verifier_id' => session('verifier_id'),
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug,
            'route' => $request->route()->getName()
        ]);
        
        if (!session()->has('verifier_authenticated') || !session('verifier_id')) {
            // Sauvegarder l'URL de destination
            session(['verifier_intended_url' => $request->fullUrl()]);
            
            return redirect()->route('event.verifier.auth', [
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ])->with('error', 'Veuillez vous authentifier pour accéder à cette page.');
        }
        
        // Vérifier la cohérence des slugs
        $sessionOrgSlug = session('verifier_org_slug');
        $sessionEventSlug = session('verifier_event_slug');
        
        if ($sessionOrgSlug && $sessionOrgSlug !== $orgSlug) {
            // Nettoyer la session
            session()->forget([
                'verifier_authenticated',
                'verifier_id', 
                'verifier_name',
                'verifier_email',
                'verifier_role',
                'verifier_allowed_zones',
                'verifier_last_activity',
                'verifier_event_id',
                'verifier_org_slug',
                'verifier_event_slug'
            ]);
            
            return redirect()->route('event.verifier.auth', [
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ])->with('error', 'Session expirée, veuillez vous reconnecter.');
        }
        
        return $next($request);
    }
}