<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('email')) {
            $allowedDomains = ['@company.com']; 
            $email = $request->input('email');
            $domain = '@' . substr(strrchr($email, '@'), 1);

            if (!in_array($domain, $allowedDomains)) {
                return back()->withErrors([
                    'email' => 'Registration is only allowed with company email addresses (@company.com).'
                ])->withInput();
            }
        }

        return $next($request);
    }
}
