<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\PesertaKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class P0SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_destructive_endpoints_reject_get_requests(): void
    {
        $admin = User::create([
            'username' => 'admin_test',
            'password' => Hash::make('StrongPass123!'),
            'role' => 'Admin',
        ]);

        $this->actingAs($admin)->get('/logout')->assertStatus(405);
        $this->actingAs($admin)->get('/admin/users/999')->assertStatus(405);
        $this->actingAs($admin)->get('/kuis/1/soal/1')->assertStatus(405);
    }

    public function test_pengajar_cannot_access_other_pengajar_class(): void
    {
        $u1 = User::create(['username' => 'dosen_a', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        $u2 = User::create(['username' => 'dosen_b', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);

        Pengajar::create(['nidn' => '1001', 'nama_pengajar' => 'Dosen A', 'email' => 'a@test.local', 'id_user' => $u1->id_user]);
        Pengajar::create(['nidn' => '1002', 'nama_pengajar' => 'Dosen B', 'email' => 'b@test.local', 'id_user' => $u2->id_user]);

        $kelasA = Kelas::create(['nama_kelas' => 'Kelas A', 'deskripsi' => 'Desc', 'nidn' => '1001']);

        $this->actingAs($u2)->get(route('kelas.show', ['id' => $kelasA->id_kelas]))->assertStatus(403);
        $this->actingAs($u2)->post(route('kelas.post', ['id' => $kelasA->id_kelas]), [
            'action' => 'tambah_materi',
            'judul_materi' => 'Injected',
        ])->assertStatus(403);
    }

    public function test_pass_the_hash_login_is_rejected(): void
    {
        $hash = Hash::make('StrongPass123!');
        User::create([
            'username' => 'murid_test',
            'password' => $hash,
            'role' => 'Murid',
        ]);

        $this->post('/login', [
            'username' => 'murid_test',
            'password' => $hash,
        ]);

        $this->assertGuest();
    }

    public function test_unenrolled_murid_cannot_view_class_or_files(): void
    {
        $uDosen = User::create(['username' => 'dosen_c', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '1003', 'nama_pengajar' => 'Dosen C', 'email' => 'c@test.local', 'id_user' => $uDosen->id_user]);
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Privat', 'deskripsi' => 'Desc', 'nidn' => '1003']);

        $uMurid = User::create(['username' => 'murid_x', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '99001', 'nama_murid' => 'Murid X', 'email' => 'x@test.local', 'id_user' => $uMurid->id_user]);

        $this->actingAs($uMurid)->get(route('kelas.show', ['id' => $kelas->id_kelas]))->assertStatus(403);

        PesertaKelas::create(['id_kelas' => $kelas->id_kelas, 'nim' => '99001']);
        $this->actingAs($uMurid)->get(route('kelas.show', ['id' => $kelas->id_kelas]))->assertStatus(200);
    }
}
