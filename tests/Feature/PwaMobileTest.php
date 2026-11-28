<?php

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_guest_uses_top_navbar_only_and_authenticated_user_uses_mobile_bottom_nav(): void
    {
        $guestRes = $this->get('/');
        $guestRes->assertStatus(200)
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('apple-touch-icon.png', false)
            ->assertSee('theme-color', false)
            ->assertSee('is-guest-shell', false)
            ->assertSee('speculationrules', false)
            ->assertDontSee('neo-mobile-nav', false)
            ->assertDontSee('mobile-ptr-pill', false)
            ->assertSee('E-FORVM', false);

        $csp = (string) $guestRes->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("worker-src 'self'", $csp);
        $this->assertStringContainsString("manifest-src 'self'", $csp);

        $user = User::create([
            'username' => 'pwa_murid',
            'password' => Hash::make('StrongPass123!'),
            'role' => 'Murid',
        ]);
        Murid::create([
            'nim' => '91001',
            'nama_murid' => 'Murid PWA',
            'email' => 'pwa@student.local',
            'id_user' => $user->id_user,
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('is-auth-shell', false)
            ->assertSee('neo-mobile-app-header', false)
            ->assertSee('mobile-notif-btn', false)
            ->assertSee('neo-mobile-nav', false)
            ->assertSee('mobile-nav-notif-badge', false)
            ->assertSee('nav-progress-bar', false)
            ->assertSee('mobile-ptr-pill', false)
            ->assertSee('neo-skeleton-list', false);

        $this->actingAs($user)->get(route('profil.index'))
            ->assertStatus(200)
            ->assertSee('account-menu-list', false)
            ->assertSee('Kelola Profil', false)
            ->assertSee('Pembayaran &amp; Status Pro', false)
            ->assertSee('FAQ &amp; Panduan Pengguna', false)
            ->assertSee('profil-notif-list', false)
            ->assertSee('neo-skeleton-list', false)
            ->assertSee('profil-theme-label', false);

        // Catalog cache warms on /enroll and invalidates immediately when a new Kelas is saved
        $this->actingAs($user)->get(route('enroll.index'))
            ->assertStatus(200)
            ->assertDontSee('Kelas Cache Baru');

        Kelas::create([
            'nama_kelas' => 'Kelas Cache Baru',
            'deskripsi' => 'Uji invalidasi cache katalog.',
            'is_premium' => false,
        ]);

        $this->actingAs($user)->get(route('enroll.index'))
            ->assertStatus(200)
            ->assertSee('Kelas Cache Baru');

        // Reddit-style mobile forum feed & thread controls render cleanly
        $post = ForumPost::create([
            'id_user' => $user->id_user,
            'judul' => 'Uji Layout Forum Mobile Reddit',
            'konten' => '<p>Konten diskusi mobile gaya Reddit.</p>',
            'tipe' => 'pertanyaan',
            'status' => 'open',
            'tgl_post' => now(),
        ]);

        $this->actingAs($user)->get(route('forum.index'))
            ->assertStatus(200)
            ->assertSee('forum-mobile-create-btn', false)
            ->assertSee('forum-mobile-vote-pill', false)
            ->assertSee('Uji Layout Forum Mobile Reddit');

        $this->actingAs($user)->get(route('forum.show', ['id' => $post->id_post]))
            ->assertStatus(200)
            ->assertSee('forum-mobile-vote-pill', false)
            ->assertSee('Uji Layout Forum Mobile Reddit');

        // User-scoped notification cache key prevents cross-account leakage
        $this->actingAs($user)->get(route('dashboard'))
            ->assertSee('eforvm_notif_cache_v2_' . $user->id_user, false);

        // Forum API rejects non-existent targets cleanly with 404
        $this->actingAs($user)->postJson(route('api.forum.vote'), ['id' => 999999, 'type' => 'post', 'vote' => 'up'])
            ->assertStatus(404);
        $this->actingAs($user)->postJson(route('api.forum.simpan'), ['id_post' => 999999])
            ->assertStatus(404);

        // Login page displays clear alert when redirected with msg=login_required
        auth()->logout();
        $this->get(route('login', ['msg' => 'login_required']))
            ->assertStatus(200)
            ->assertSee('Silakan masuk terlebih dahulu untuk mengakses fitur tersebut.');

        // Cross-table duplicate email is blocked during registration
        $pengajarUser = User::create([
            'username' => 'dsn_unique',
            'password' => Hash::make('StrongPass123!'),
            'role' => 'Pengajar',
        ]);
        Pengajar::create([
            'nidn' => 'DSN991',
            'nama_pengajar' => 'Dosen Unik',
            'email' => 'dosen.unik@eforvm.test',
            'id_user' => $pengajarUser->id_user,
        ]);

        $this->post('/register', [
            'nama_lengkap' => 'Murid Duplikat',
            'email' => 'dosen.unik@eforvm.test',
            'username' => 'mrd_dup_email',
            'password' => 'StrongPass123!',
            'konfirmasi_password' => 'StrongPass123!',
        ])->assertRedirect(route('register'))
            ->assertSessionHas('auth_error');

        // Quill.js <pre class="ql-syntax"> code block preview extraction works
        $codeBlock = extractCodeBlock('<p>Halo</p><pre class="ql-syntax">echo "ok";</pre>');
        $this->assertNotNull($codeBlock);
        $this->assertSame('echo "ok";', $codeBlock['code']);
    }
}
