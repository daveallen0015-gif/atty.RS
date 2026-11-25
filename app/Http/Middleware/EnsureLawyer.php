<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLawyer
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_lawyer, 403);

        return $next($request);
    }
}
