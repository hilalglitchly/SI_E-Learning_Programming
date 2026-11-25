<?php

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $dosenUser = User::create([
            'username' => 'home_dosen',
            'password' => Hash::make('StrongPass123!'),
            'role' => 'Pengajar',
        ]);
        Pengajar::create([
            'nidn' => '9901',
            'nama_pengajar' => 'Dosen Demo',
            'email' => 'home_dosen@test.local',
            'id_user' => $dosenUser->id_user,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'Python Praktis',
            'deskripsi' => 'Belajar Python dari dasar.',
            'nidn' => '9901',
            'is_premium' => true,
        ]);
        Materi::create([
            'id_kelas' => $kelas->id_kelas,
            'judul_materi' => 'Pengenalan Python',
            'deskripsi' => 'Modul 1',
        ]);
        ForumPost::create([
            'id_user' => $dosenUser->id_user,
            'judul' => 'Tips Belajar Algoritma Cepat',
            'konten' => '<p>Mulai dari latihan rutin.</p>',
            'tipe' => 'berbagi',
            'status' => 'open',
            'tgl_post' => now(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Modul &amp; Materi', false)
            ->assertSee('Python Praktis')
            ->assertSee('Pro Bulanan')
            ->assertSee('Tips Belajar Algoritma Cepat')
            ->assertSee('data-course-filter="pro"', false);
    }
}
