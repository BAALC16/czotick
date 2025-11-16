<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log seulement pour les organisations connectées
        if (AuthHelper::isOrganizationLoggedIn()) {
            $this->logOrganizationActivity($request, $response);
        }

        return $response;
    }

    protected function logOrganizationActivity($request, $response)
    {
        try {
            $user = AuthHelper::organizationUser();
            $orgId = AuthHelper::organizationId();

            $data = [
                'organization_id' => $orgId,
                'user_id' => $user['id'],
                'action' => $this->getActionFromRequest($request),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
                'created_at' => now(),
            ];

            // Log uniquement les actions importantes
            if ($this->shouldLog($request)) {
                DB::connection('saas_master')
                    ->table('organization_logs')
                    ->insert($data);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors du logging d\'activité: ' . $e->getMessage());
        }
    }

    protected function getActionFromRequest($request)
    {
        $route = $request->route();
        if (!$route) return 'unknown';

        $name = $route->getName();
        $method = $request->method();

        // Mapping des actions
        $actions = [
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete',
            'GET' => 'view',
        ];

        return $actions[$method] ?? 'action';
    }

    protected function shouldLog($request)
    {
        // Ne pas logger les requêtes AJAX fréquentes
        $ignoredRoutes = [
            'organization.api.stats',
            'organization.api.notifications',
        ];

        $routeName = $request->route() ? $request->route()->getName() : '';
        
        return !in_array($routeName, $ignoredRoutes) && 
               !$request->ajax() || 
               in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }
}
