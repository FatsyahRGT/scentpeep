<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_web_routes_include_defensive_security_headers(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }

    public function test_trusted_https_proxy_generates_https_urls_and_upgrade_csp(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'scentpeep-msys.duckdns.org',
            'X-Forwarded-Port' => '443',
        ])->get('/login');

        $response->assertStatus(200);
        $response->assertSee('action="https://scentpeep-msys.duckdns.org/login"', false);
        $this->assertStringContainsString(
            'upgrade-insecure-requests;',
            (string) $response->headers->get('Content-Security-Policy')
        );
    }
}
