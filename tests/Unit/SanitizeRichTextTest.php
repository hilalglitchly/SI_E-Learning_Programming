<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/Helpers/helpers.php';

class SanitizeRichTextTest extends TestCase
{
    public function test_blocks_xss_and_scheme_bypasses(): void
    {
        $payloads = [
            '<a href="java&#x09;script:alert(1)">klik</a>',
            '<a href="&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;:alert(1)">klik</a>',
            '<a href="data:text/html,<script>alert(1)</script>">klik</a>',
            '<img src="/admin/users/13/delete" onerror="alert(1)">',
            '<img src="data:image/svg+xml;base64,PHN2Zz4=">',
            '<div onclick="alert(1)" style="color:red">teks</div>',
            '</div><a href="javascript:alert(1)">lepas</a>',
        ];

        foreach ($payloads as $payload) {
            $clean = sanitize_rich_text($payload);
            $this->assertStringNotContainsStringIgnoringCase('javascript:', $clean);
            $this->assertStringNotContainsStringIgnoringCase('onerror', $clean);
            $this->assertStringNotContainsStringIgnoringCase('onclick', $clean);
            $this->assertStringNotContainsStringIgnoringCase('/admin/users/13/delete', $clean);
            $this->assertStringNotContainsStringIgnoringCase('data:', $clean);
        }
    }

    public function test_preserves_safe_markup_and_enforces_noopener(): void
    {
        $input = '<p>Halo <strong>dunia</strong></p><pre><code class="language-php">echo 1;</code></pre><a href="https://example.com" target="_blank">link</a>';
        $clean = sanitize_rich_text($input);

        $this->assertStringContainsString('<strong>dunia</strong>', $clean);
        $this->assertStringContainsString('class="language-php"', $clean);
        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }
}
