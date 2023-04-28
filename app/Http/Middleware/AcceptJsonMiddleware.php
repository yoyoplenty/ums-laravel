<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptJsonMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        if (!$request->isMethod('post')) return $next($request);

        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response()->json("incorrect request header provided", 406);
        }

        return $next($request);
    }
}
