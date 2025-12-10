<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders {
    public function handle(Request $request, Closure $next) {
        $response = $next($request);

        // --- Common Security Headers (Always Apply) ---
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // --- Content Security Policy (CSP) Configuration ---
        
        // Base Directives - apply to all environments
        $csp_directives = [
            'default-src' => ["'self'"],
            // Allow self and inline/eval for Vue/Inertia processing
            'script-src'  => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
            'style-src'   => ["'self'", "'unsafe-inline'"],
            'img-src'     => ["'self'", "data:", "https:"],
            'font-src'    => ["'self'", "data:"],
            'connect-src' => ["'self'"],
        ];
        
        // --- DEVELOPMENT ENVIRONMENT CSP ADJUSTMENTS (For Vite and HMR) ---
        if (app()->environment('local')) {
            // 1. Allow Vite's specific host/port for scripts and connections
            // (fixes errors like http://[::1]:5173/@vite/client)
            $csp_directives['script-src'][] = "http://localhost:5173";
            $csp_directives['script-src'][] = "http://127.0.0.1:5173";
            $csp_directives['connect-src'][] = "ws://localhost:5173";
            $csp_directives['connect-src'][] = "ws://127.0.0.1:5173";
        }
        
        // --- EXTERNAL RESOURCES CSP ADJUSTMENTS (For Bunny Fonts) ---
        // This is necessary because it's an external domain
        // (fixes errors like https://fonts.bunny.net/css?family=...)
        $csp_directives['style-src'][] = "https://fonts.bunny.net";
        $csp_directives['font-src'][] = "https://fonts.bunny.net";


        // --- BUILD THE CSP STRING ---
        $csp_string = '';
        foreach ($csp_directives as $key => $values) {
            $csp_string .= $key . ' ' . implode(' ', array_unique($values)) . '; ';
        }
        
        // Set the final CSP header
        $response->headers->set('Content-Security-Policy', trim($csp_string));

        return $response;
    }
}