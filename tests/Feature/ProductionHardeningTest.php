<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\PesertaKelas;
use App\Models\SoalKuis;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_csp_header_and_custom_error_pages(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200);
        $csp = (string) $res->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString('youtube-nocookie.com', $csp);

        $this->get('/halaman-tidak-ada-xyz')
            ->assertStatus(404)
            ->assertSee('Halaman Tidak Ditemukan');

        $murid = User::create(['username' => 'err_murid', 'password' => Hash::make('SecretPass123'), 'role' => 'Murid']);
        $this->actingAs($murid)->get(route('admin.langganan.index'))
            ->assertStatus(403)
            ->assertSee('Akses Ditolak');
    }

    public function test_register_endpoint_enforces_rate_limit(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/register', [
                'nama_lengkap' => '',
                'email' => 'bad',
                'username' => 'x',
                'password' => '123',
                'konfirmasi_password' => '123',
            ])->assertRedirect(route('register'));
        }

        $this->post('/register', [
            'nama_lengkap' => 'Bot Spam',
            'email' => 'bot@example.com',
            'username' => 'botspam99',
            'password' => 'SecretPass123',
            'konfirmasi_password' => 'SecretPass123',
        ])->assertStatus(429)
            ->assertSee('Terlalu Banyak Permintaan');
    }

    public function test_quiz_submit_enforces_server_side_timer(): void
    {
        $dosen = User::create(['username' => 'dsn_tm', 'password' => Hash::make('SecretPass123'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '8101', 'nama_pengajar' => 'Dosen Timer', 'email' => 'tm@dosen.local', 'id_user' => $dosen->id_user]);
        $kelas = Kelas::create(['nama_kelas' => 'Timer Class', 'deskripsi' => 'Desc', 'nidn' => '8101']);

        $murid = User::create(['username' => 'mrd_tm', 'password' => Hash::make('SecretPass123'), 'role' => 'Murid']);
        Murid::create(['nim' => '81001', 'nama_murid' => 'Murid Timer', 'email' => 'tm@murid.local', 'id_user' => $murid->id_user]);
        PesertaKelas::create(['id_kelas' => $kelas->id_kelas, 'nim' => '81001']);

        $kuis = Kuis::create(['id_kelas' => $kelas->id_kelas, 'judul_kuis' => 'Kuis Kilat', 'deskripsi' => 'Desc', 'durasi_menit' => 1]);
        $soal = SoalKuis::create([
            'id_kuis' => $kuis->id_kuis, 'pertanyaan' => '2+2?', 'opsi_a' => '3', 'opsi_b' => '4', 'opsi_c' => '5', 'opsi_d' => '6', 'jawaban_benar' => 'B',
        ]);

        // Direct submit without starting quiz session must be rejected
        $this->actingAs($murid)->post(route('kuis.submit', ['id_kuis' => $kuis->id_kuis]), [
            'soal_' . $soal->id_soal => 'B',
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));
        $this->assertDatabaseCount('tb_nilai_kuis', 0);

        // Expired session (started 5 minutes ago for a 1-minute quiz) must be rejected
        $this->actingAs($murid)
            ->withSession(["kuis_started_at_{$kuis->id_kuis}_81001" => now()->subMinutes(5)->timestamp])
            ->post(route('kuis.submit', ['id_kuis' => $kuis->id_kuis]), [
                'soal_' . $soal->id_soal => 'B',
            ])
            ->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]))
            ->assertSessionHas('pesan');

        $this->assertDatabaseCount('tb_nilai_kuis', 0);
    }

    public function test_admin_deleting_class_cleans_up_private_files(): void
    {
        Storage::fake('local');

        $admin = User::create(['username' => 'adm_clean', 'password' => Hash::make('SecretPass123'), 'role' => 'Admin']);
        $murid = User::create(['username' => 'mrd_clean', 'password' => Hash::make('SecretPass123'), 'role' => 'Murid']);
        Murid::create(['nim' => '82001', 'nama_murid' => 'Murid Clean', 'email' => 'clean@murid.local', 'id_user' => $murid->id_user]);

        $kelas = Kelas::create(['nama_kelas' => 'Kelas Hapus', 'deskripsi' => 'Desc']);
        Materi::create(['id_kelas' => $kelas->id_kelas, 'judul_materi' => 'M', 'deskripsi' => 'D', 'file_materi' => 'mat1.pdf']);
        $tugas = Tugas::create(['id_kelas' => $kelas->id_kelas, 'judul_tugas' => 'T', 'deskripsi' => 'D', 'file_tugas' => 'tug1.pdf', 'deadline' => now()->addDay()]);
        Submission::create(['id_tugas' => $tugas->id_tugas, 'nim' => '82001', 'file_jawaban' => 'sub1.pdf']);

        Storage::disk('local')->put('private/materi/mat1.pdf', 'pdf');
        Storage::disk('local')->put('private/tugas/tug1.pdf', 'pdf');
        Storage::disk('local')->put('private/submission/sub1.pdf', 'pdf');

        $this->actingAs($admin)->delete(route('admin.kelas.delete', $kelas->id_kelas))
            ->assertRedirect(route('dashboard'));

        Storage::disk('local')->assertMissing('private/materi/mat1.pdf');
        Storage::disk('local')->assertMissing('private/tugas/tug1.pdf');
        Storage::disk('local')->assertMissing('private/submission/sub1.pdf');
    }

    public function test_password_reset_and_email_verification_flows(): void
    {
        Mail::fake();

        $user = User::create(['username' => 'reset_user', 'password' => Hash::make('OldPassword123'), 'role' => 'Murid']);
        Murid::create(['nim' => '83001', 'nama_murid' => 'Reset Murid', 'email' => 'reset@student.local', 'id_user' => $user->id_user]);

        // Unverified student sees verification banner on dashboard
        $this->actingAs($user)->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Email belum terverifikasi');

        // Signed verification link marks email verified
        $verifyUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id_user,
            'hash' => sha1('reset@student.local'),
        ]);
        $this->actingAs($user)->get($verifyUrl)->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // Password reset request creates hashed token and completes reset
        auth()->logout();
        $this->post(route('password.email'), ['email' => 'reset@student.local'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'reset@student.local']);

        $knownToken = 'plain-reset-token-1234567890';
        DB::table('password_reset_tokens')->where('email', 'reset@student.local')->update([
            'token' => Hash::make($knownToken),
            'created_at' => now(),
        ]);

        $this->post(route('password.update'), [
            'token' => $knownToken,
            'email' => 'reset@student.local',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'reset@student.local']);
    }

    public function test_admin_dashboard_paginates_and_materi_video_embed_blocks_mp4(): void
    {
        Storage::fake('local');

        $admin = User::create(['username' => 'adm_pg', 'password' => Hash::make('SecretPass123'), 'role' => 'Admin']);
        for ($i = 1; $i <= 12; $i++) {
            Kelas::create(['nama_kelas' => "Kelas Paginate {$i}", 'deskripsi' => 'Desc']);
        }

        $res = $this->actingAs($admin)->get(route('dashboard'));
        $res->assertStatus(200)
            ->assertSee('Halaman 1 dari 2')
            ->assertSee('Kelas Paginate 12')
            ->assertDontSee('Kelas Paginate 1"');

        $kelas = Kelas::firstOrFail();

        // Raw mp4 upload must be rejected
        $fakeMp4 = UploadedFile::fake()->create('demo.mp4', 500, 'video/mp4');
        $this->actingAs($admin)->post(route('kelas.post', ['id' => $kelas->id_kelas]), [
            'action' => 'tambah_materi',
            'judul_materi' => 'Video Mentah',
            'hidden_deskripsi_materi' => '<p>Test</p>',
            'file_materi' => $fakeMp4,
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));
        $this->assertDatabaseMissing('tb_materi', ['judul_materi' => 'Video Mentah']);

        // Invalid video URL must be rejected
        $this->actingAs($admin)->post(route('kelas.post', ['id' => $kelas->id_kelas]), [
            'action' => 'tambah_materi',
            'judul_materi' => 'Video Bad URL',
            'hidden_deskripsi_materi' => '<p>Test</p>',
            'video_url' => 'javascript:alert(1)',
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));
        $this->assertDatabaseMissing('tb_materi', ['judul_materi' => 'Video Bad URL']);

        // Valid YouTube URL normalizes to youtube-nocookie embed and renders iframe
        $this->actingAs($admin)->post(route('kelas.post', ['id' => $kelas->id_kelas]), [
            'action' => 'tambah_materi',
            'judul_materi' => 'Video YouTube',
            'hidden_deskripsi_materi' => '<p>Belajar</p>',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));

        $this->assertDatabaseHas('tb_materi', [
            'judul_materi' => 'Video YouTube',
            'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
        ]);

        $this->actingAs($admin)->get(route('kelas.show', ['id' => $kelas->id_kelas]))
            ->assertStatus(200)
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
    }
}
