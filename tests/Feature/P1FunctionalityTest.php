<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\Murid;
use App\Models\Notifikasi;
use App\Models\Pengajar;
use App\Models\PesertaKelas;
use App\Models\SoalKuis;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class P1FunctionalityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_and_safe_legacy_redirects(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $this->get('/kelas_detail.php')->assertRedirect(route('dashboard'));
        $this->get('/manage_kuis.php')->assertRedirect(route('dashboard'));
        $this->get('/forum_post_detail.php')->assertRedirect(route('forum.index'));
    }

    public function test_admin_can_crud_kelas_and_murid_is_blocked(): void
    {
        $admin = User::create(['username' => 'adm1', 'password' => Hash::make('StrongPass123!'), 'role' => 'Admin']);
        $uDosen = User::create(['username' => 'dsn1', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '2001', 'nama_pengajar' => 'Dosen Satu', 'email' => 'd1@test.local', 'id_user' => $uDosen->id_user]);

        $uMurid = User::create(['username' => 'mrd1', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '3001', 'nama_murid' => 'Murid Satu', 'email' => 'm1@test.local', 'id_user' => $uMurid->id_user]);

        $this->actingAs($uMurid)->post(route('admin.kelas.store'), ['nama_kelas' => 'Unauthorized'])->assertStatus(403);

        $this->actingAs($admin)->post(route('admin.kelas.store'), [
            'nama_kelas' => 'Laravel Security',
            'deskripsi' => 'Belajar keamanan web',
            'nidn' => '2001',
        ])->assertRedirect(route('dashboard'));

        $kelas = Kelas::where('nama_kelas', 'Laravel Security')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.kelas.update', $kelas->id_kelas), [
            'nama_kelas' => 'Laravel Security Updated',
            'deskripsi' => 'Deskripsi baru',
            'nidn' => '2001',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame('Laravel Security Updated', $kelas->fresh()->nama_kelas);

        $this->actingAs($admin)->delete(route('admin.kelas.delete', $kelas->id_kelas))
            ->assertRedirect(route('dashboard'));

        $this->assertNull(Kelas::find($kelas->id_kelas));
    }

    public function test_student_sees_grade_and_feedback_and_quiz_prevents_double_submit(): void
    {
        $uDosen = User::create(['username' => 'dsn2', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '2002', 'nama_pengajar' => 'Dosen Dua', 'email' => 'd2@test.local', 'id_user' => $uDosen->id_user]);
        $kelas = Kelas::create(['nama_kelas' => 'Algoritma', 'deskripsi' => 'Desc', 'nidn' => '2002']);

        $uMurid = User::create(['username' => 'mrd2', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '3002', 'nama_murid' => 'Murid Dua', 'email' => 'm2@test.local', 'id_user' => $uMurid->id_user]);
        PesertaKelas::create(['id_kelas' => $kelas->id_kelas, 'nim' => '3002']);

        // Empty class progress must be 0%, not 100%
        $this->actingAs($uMurid)->get(route('kelas.show', ['id' => $kelas->id_kelas]))
            ->assertStatus(200)
            ->assertSee('0%');

        $tugas = Tugas::create([
            'id_kelas' => $kelas->id_kelas,
            'judul_tugas' => 'Tugas Sorting',
            'deskripsi' => 'Buat quicksort',
            'deadline' => now()->addDays(2),
        ]);
        Submission::create([
            'id_tugas' => $tugas->id_tugas,
            'nim' => '3002',
            'file_jawaban' => 'dummy.pdf',
            'nilai' => 92,
            'catatan' => 'Implementasi sangat rapi',
        ]);

        $this->actingAs($uMurid)->get(route('kelas.show', ['id' => $kelas->id_kelas]))
            ->assertStatus(200)
            ->assertSee('NILAI: 92/100')
            ->assertSee('Implementasi sangat rapi');

        $kuis = Kuis::create([
            'id_kelas' => $kelas->id_kelas,
            'judul_kuis' => 'Kuis 1',
            'deskripsi' => 'Tes cepat',
            'durasi_menit' => 10,
        ]);
        $soal = SoalKuis::create([
            'id_kuis' => $kuis->id_kuis,
            'pertanyaan' => '1 + 1 = ?',
            'opsi_a' => '1',
            'opsi_b' => '2',
            'opsi_c' => '3',
            'opsi_d' => '4',
            'jawaban_benar' => 'B',
        ]);

        $this->actingAs($uMurid)->get(route('kuis.kerjakan', ['id_kuis' => $kuis->id_kuis]))
            ->assertStatus(200)
            ->assertSee('kuis-countdown');

        $this->actingAs($uMurid)->post(route('kuis.submit', ['id_kuis' => $kuis->id_kuis]), [
            'soal_' . $soal->id_soal => 'B',
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas, 'msg' => 'kuis_selesai']));

        // Second submission must not duplicate score
        $this->actingAs($uMurid)->post(route('kuis.submit', ['id_kuis' => $kuis->id_kuis]), [
            'soal_' . $soal->id_soal => 'A',
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));

        $this->assertDatabaseCount('tb_nilai_kuis', 1);
        $this->assertDatabaseHas('tb_nilai_kuis', ['id_kuis' => $kuis->id_kuis, 'nim' => '3002', 'nilai' => 100]);
    }

    public function test_profile_edit_pimpinan_label_contact_form_and_full_seeder(): void
    {
        $this->seed();

        $pimpinan = User::where('username', 'pimpinan')->firstOrFail();
        $this->actingAs($pimpinan)->get(route('profil.index'))
            ->assertStatus(200)
            ->assertSee('Pimpinan Institusi')
            ->assertDontSee('Administrator Sistem');

        $muridUser = User::where('username', 'student_adit')->firstOrFail();
        $this->actingAs($muridUser)->post(route('profil.update'), [
            'nama_lengkap' => 'Adit Pratama Putra',
            'email' => 'adit.baru@student.local',
        ])->assertRedirect(route('profil.index'));

        $this->assertDatabaseHas('tb_murid', [
            'id_user' => $muridUser->id_user,
            'nama_murid' => 'Adit Pratama Putra',
            'email' => 'adit.baru@student.local',
        ]);

        // Contact form delivers notification to Admin
        auth()->logout();
        $this->post(route('kontak.send'), [
            'nama' => 'Calon Siswa',
            'kategori' => 'Tanya Kelas',
            'email' => 'calon@example.com',
            'pesan' => 'Apakah ada kelas Laravel?',
        ])->assertRedirect(route('home') . '#kontak');

        $admin = User::where('username', 'admin')->firstOrFail();
        $this->assertTrue(
            Notifikasi::where('id_user', $admin->id_user)
                ->where('judul', 'Pesan Kontak (Tanya Kelas)')
                ->exists()
        );

        // Smoke-test seeded dashboards & forum
        $this->actingAs($admin)->get(route('dashboard'))->assertStatus(200)->assertSee('Manajemen Kelas');
        $this->actingAs($admin)->get(route('laporan.index'))->assertStatus(200);
        $this->get(route('forum.index'))->assertStatus(200);
    }

    public function test_create_admin_artisan_command_enforces_strong_password_and_creates_account(): void
    {
        $this->artisan('app:create-admin', [
            'username' => 'prod_admin',
            '--password' => 'short123',
        ])->assertExitCode(1);

        $this->artisan('app:create-admin', [
            'username' => 'prod_admin',
            '--role' => 'Admin',
            '--password' => 'SuperStrongProdPass#2026',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('tb_user', [
            'username' => 'prod_admin',
            'role' => 'Admin',
        ]);
    }
}
