<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetOrganizationDatabase
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = session('organization_user');
        
        if ($user && isset($user['database_name'])) {
            try {
                // Simple: juste changer le nom de la base de données
                config(['database.connections.org.database' => $user['database_name']]);
                
                // Purger la connexion pour forcer la reconnexion
                DB::purge('org');
                
                // Test simple de connexion
                DB::connection('org')->select('SELECT 1 as test');
                
            } catch (\Exception $e) {
                \Log::error('SetOrganizationDatabase failed', [
                    'database' => $user['database_name'],
                    'error' => $e->getMessage()
                ]);
                
                // En cas d'erreur, déconnecter et rediriger
                session()->forget('organization_user');
                $orgSlug = $request->route('org_slug');
                
                return redirect()->route('org.login', ['org_slug' => $orgSlug])
                    ->withErrors(['database' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
            }
        }
        
        return $next($request);
    }
}