<?php namespace Boarworm\Restapi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		$apiKey = $request->header('Authorization');
		if ($apiKey !== env('API_KEY')) {
			return response()->json(['message' => 'Unauthorized'], 401);
		}
		return $next($request);
	}
}
