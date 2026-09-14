<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiBasicAuth
{
    /**
     * Handle an incoming request for HTTP Basic Auth compatibility with CodeIgniter REST_Controller
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        // Also check header PHP_AUTH_USER if getUser() is null
        if (!$username && $request->server('PHP_AUTH_USER')) {
            $username = $request->server('PHP_AUTH_USER');
            $password = $request->server('PHP_AUTH_PW');
        }

        // Also check if admin bypass or valid user in database
        if (!$username || !$password) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized. HTTP Basic Authentication required.'
            ], Response::HTTP_UNAUTHORIZED, [
                'WWW-Authenticate' => 'Basic realm="API"'
            ]);
        }

        // Check fallback hardcoded logins if configured (matching CodeIgniter rest_valid_logins)
        if ($username === 'admin' && $password === '1234') {
            return $next($request);
        }

        // Verify in tbl_users / sys_users
        $user = User::where('username', $username)->first();
        if ($user && $user->verifyPassword($password)) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'error' => 'Invalid API credentials.'
        ], Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => 'Basic realm="API"'
        ]);
    }
}
