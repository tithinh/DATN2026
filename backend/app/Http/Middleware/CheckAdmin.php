<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user instanceof Admin) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return $next($request);
    }
}
