<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use App\Helpers\AuthHelper;

class ThrottleOrganizationRequests
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        if (!AuthHelper::isOrganizationLoggedIn()) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'error' => 'Trop de requêtes. Veuillez patienter.',
                'retry_after' => $this->limiter->availableIn($key)
            ], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }

    protected function resolveRequestSignature(Request $request)
    {
        $orgId = AuthHelper::organizationId();
        return 'org:' . $orgId . ':' . $request->ip();
    }
}