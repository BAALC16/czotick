<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SimpleAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        // Vérifier si déjà connecté
        if (session()->has('super_admin_logged_in')) {
            return redirect()->route('super-admin.dashboard');
        }
        
        return view('super-admin.auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Vérifier les identifiants
        $admin = DB::table('system_admins')
            ->where('username', $request->username)
            ->where('is_active', 1)
            ->first();

        if (!$admin) {
            throw ValidationException::withMessages([
                'username' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // Vérifier si le compte est verrouillé
        if ($admin->locked_until && now() < $admin->locked_until) {
            throw ValidationException::withMessages([
                'username' => ['Compte temporairement verrouillé. Réessayez plus tard.'],
            ]);
        }

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $admin->password)) {
            // Incrémenter les tentatives
            $attempts = $admin->login_attempts + 1;
            $lockTime = $attempts >= 5 ? now()->addMinutes(30) : null;

            DB::table('system_admins')
                ->where('id', $admin->id)
                ->update([
                    'login_attempts' => $attempts,
                    'locked_until' => $lockTime,
                ]);

            throw ValidationException::withMessages([
                'password' => ['Mot de passe incorrect.'],
            ]);
        }

        // Connexion réussie
        $this->loginAdmin($admin, $request);

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Connexion réussie !');
    }

    /**
     * Connecter l'administrateur
     */
    private function loginAdmin($admin, Request $request)
    {
        // Mettre à jour les informations de connexion
        DB::table('system_admins')
            ->where('id', $admin->id)
            ->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'login_attempts' => 0,
                'locked_until' => null,
            ]);

        // Stocker en session - SIMPLE
        session([
            'super_admin_logged_in' => true,
            'super_admin_id' => $admin->id,
            'super_admin_username' => $admin->username,
            'super_admin_level' => $admin->admin_level,
            'super_admin_name' => $admin->first_name . ' ' . $admin->last_name,
            'login_time' => now()->timestamp
        ]);

        // Log de connexion
        DB::table('admin_activity_logs')->insert([
            'admin_id' => $admin->id,
            'action' => 'login',
            'description' => 'Connexion au dashboard super admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $adminId = session('super_admin_id');

        if ($adminId) {
            // Log de déconnexion
            DB::table('admin_activity_logs')->insert([
                'admin_id' => $adminId,
                'action' => 'logout',
                'description' => 'Déconnexion du dashboard super admin',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        // Vider toute la session
        session()->flush();

        return redirect()->route('super-admin.login')
            ->with('success', 'Déconnexion réussie');
    }

    /**
     * Vérifier l'authentification (AJAX)
     */
    public function checkAuth()
    {
        return response()->json([
            'authenticated' => session()->has('super_admin_logged_in'),
            'admin' => [
                'id' => session('super_admin_id'),
                'username' => session('super_admin_username'),
                'name' => session('super_admin_name'),
                'level' => session('super_admin_level'),
            ]
        ]);
    }

    /**
     * Statistiques système pour API
     */
    public function getSystemStats()
    {
        // Vérifier l'authentification
        if (!session()->has('super_admin_logged_in')) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        $stats = [
            'organizations' => [
                'total' => DB::table('organizations')->count(),
                'active' => DB::table('organizations')->where('subscription_status', 'active')->count(),
                'trial' => DB::table('organizations')->where('subscription_plan', 'trial')->count(),
                'suspended' => DB::table('organizations')->where('subscription_status', 'suspended')->count(),
            ],
            'users' => [
                'total' => DB::table('saas_users')->count(),
                'active' => DB::table('saas_users')->where('is_active', 1)->count(),
                'owners' => DB::table('saas_users')->where('role', 'owner')->count(),
            ],
            'revenue' => [
                'monthly' => DB::table('organizations')
                    ->join('subscription_plans', 'organizations.subscription_plan', '=', 'subscription_plans.plan_code')
                    ->where('organizations.subscription_status', 'active')
                    ->sum('subscription_plans.monthly_price'),
                'annual' => DB::table('organizations')
                    ->join('subscription_plans', 'organizations.subscription_plan', '=', 'subscription_plans.plan_code')
                    ->where('organizations.subscription_status', 'active')
                    ->sum('subscription_plans.yearly_price'),
            ],
            'recent_activity' => DB::table('organizations')
                ->select('org_name', 'created_at', 'subscription_plan')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'system_health' => [
                'database' => 'OK',
                'storage' => 'OK',
                'memory' => 'OK',
            ]
        ];

        return response()->json($stats);
    }
}