<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimpleSuperAdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!session()->has('super_admin_logged_in')) {
            return $this->redirectToLogin($request);
        }

        // Vérifier que l'admin existe toujours et est actif
        $adminId = session('super_admin_id');
        if ($adminId && !$this->isAdminValid($adminId)) {
            session()->flush();
            return $this->redirectToLogin($request);
        }

        // Optionnel : vérifier le timeout de session (2 heures par défaut)
        if ($this->isSessionExpired()) {
            session()->flush();
            return $this->redirectToLogin($request, 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        // Mettre à jour le timestamp d'activité
        session(['last_activity' => now()->timestamp]);

        return $next($request);
    }

    /**
     * Vérifier si l'admin est valide
     */
    private function isAdminValid($adminId): bool
    {
        try {
            $admin = DB::table('system_admins')
                ->where('id', $adminId)
                ->where('is_active', 1)
                ->first();

            return $admin !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Vérifier si la session a expiré
     */
    private function isSessionExpired(): bool
    {
        $lastActivity = session('last_activity', session('login_time', now()->timestamp));
        $timeoutMinutes = config('session.lifetime', 120); // 2 heures par défaut
        
        return (now()->timestamp - $lastActivity) > ($timeoutMinutes * 60);
    }

    /**
     * Rediriger vers la page de connexion
     */
    private function redirectToLogin(Request $request, string $message = null)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message ?? 'Authentification requise',
                'redirect' => route('super-admin.login')
            ], 401);
        }

        $redirect = redirect()->route('super-admin.login');
        
        if ($message) {
            $redirect->with('error', $message);
        } else {
            $redirect->with('error', 'Vous devez être connecté pour accéder à cette section');
        }

        return $redirect;
    }
}