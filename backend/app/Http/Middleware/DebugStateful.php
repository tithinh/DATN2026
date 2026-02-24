<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Support\Facades\Log;

class DebugStateful extends EnsureFrontendRequestsAreStateful
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Sanctum Debug', [
            'origin' => $request->header('Origin'),
            'referer' => $request->header('Referer'),
            'host' => $request->getHost(),
            'stateful_domains' => config('sanctum.stateful'),
            'is_stateful' => $this->fromStatefulDomain($request) ? 'YES' : 'NO',
            'has_session_cookie' => $request->hasCookie('laravel_session') ? 'YES' : 'NO',
        ]);

        return parent::handle($request, $next);
    }
}
