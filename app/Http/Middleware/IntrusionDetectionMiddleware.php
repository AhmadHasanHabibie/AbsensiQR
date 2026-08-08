<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IntrusionDetectionMiddleware
{
    /**
     * Handle an incoming request for Active Defense Auto-Blocking.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Deteksi SQL Injection pada Query & Input Payload
        $allPayload = json_encode($request->all());

        if (preg_match('/(union\s+select|select\s+.*\s+from|insert\s+into|delete\s+from|drop\s+table|information_schema|\' OR 1=1|\' OR \'1\'=\'1|--|\/\*)/i', $allPayload)) {
            BlockedIp::block($request->ip(), 'Percobaan serangan SQL Injection terdeteksi.', 60);
        }

        // 2. Deteksi XSS pada Query & Input Payload
        if (preg_match('/(<script|javascript:|onerror\s*=|onclick\s*=|<iframe|<svg|eval\(|document\.cookie)/i', $allPayload)) {
            BlockedIp::block($request->ip(), 'Percobaan serangan XSS (Cross-Site Scripting) terdeteksi.', 60);
        }

        $response = $next($request);

        // 3. Deteksi Invalid Route / Sensitive URL Probe (404)
        if ($response->getStatusCode() === 404) {
            $path = strtolower($request->path());
            $suspiciousPaths = [
                'phpmyadmin', '.env', 'vendor', 'storage', 'wp-admin', 'admin/login.php',
                'login.php', 'config', '.git', 'xmlrpc.php', 'eval-stdin.php', 'api/v1/auth'
            ];

            foreach ($suspiciousPaths as $probe) {
                if (str_contains($path, $probe)) {
                    BlockedIp::block($request->ip(), "Mengakses URL terlarang berkali-kali: /{$path}", 30);
                    break;
                }
            }
        }

        // 4. Deteksi Rate Limit Exceeded (429)
        if ($response->getStatusCode() === 429) {
            BlockedIp::block($request->ip(), 'Terkena Laravel Rate Limiter berulang kali.', 15);
        }

        return $response;
    }
}

