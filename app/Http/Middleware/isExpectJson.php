<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isExpectJson
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check if the request header accept a json response
        // to ensure the validation return the json message instead of redirect to the previous page
        if(!$request->expectsJson()){
            return response()->json([
                'message' => 'Content-Type must be application/json',
            ], 400);
        }
        // if everything pass, let move to the next request-handler
        return $next($request);
    }
}
