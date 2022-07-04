<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIkey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
      $envKey = env("API_KEY");

      $requestKey = $request->header('x-api-key');

      if(strcmp($envKey, $requestKey) !== 0)
      {
        abort(response()->json(
            'Invalid API key.', 401));
      }
      else
      {
        return $next($request);
      }
    }
}
