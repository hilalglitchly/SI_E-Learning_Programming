<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_manifest_icons_and_service_worker_assets_are_valid(): void
    {
        $manifestPath = public_path('manifest.webmanifest');
        $this->assertFileExists($manifestPath);

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $this->assertIsArray($manifest);
        $this->assertSame('E-FORVM', $manifest['name'] ?? null);
        $this->assertSame('E-FORVM', $manifest['short_name'] ?? null);
        $this->assertSame('standalone', $manifest['display'] ?? null);
        $this->assertNotEmpty($manifest['icons'] ?? []);

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }

        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('offline.html'));
        $this->assertFileExists(public_path('assets/icons/apple-touch-icon.png'));
        $this->assertFileExists(public_path('assets/icons/icon.svg'));
    }

    public function test_home_renders_eforvm_pwa_headers_mobile_nav_and_csp(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('apple-touch-icon.png', false)
            ->assertSee('theme-color', false)
            ->assertSee('neo-mobile-nav', false)
            ->assertSee('E-FORVM', false);

        $csp = (string) $res->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("worker-src 'self'", $csp);
        $this->assertStringContainsString("manifest-src 'self'", $csp);
    }
}
