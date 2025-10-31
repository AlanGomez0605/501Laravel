<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NgrokHeaderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('ngrok-skip-browser-warning', 'true');
        }
        
        return $response;
    }
}