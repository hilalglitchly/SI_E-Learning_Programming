<?php

namespace Tests\Feature;

use App\Models\Murid;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_render_with_accessible_markup(): void
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('autocomplete="username"', false)
            ->assertSee('autocomplete="current-password"', false)
            ->assertSee('data-toggle-password', false);

        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('autocomplete="name"', false)
            ->assertSee('autocomplete="email"', false)
            ->assertSee('autocomplete="new-password"', false)
            ->assertSee('data-match-password="password"', false);
    }

    public function test_failed_login_uses_prg_and_preserves_username(): void
    {
        User::create([
            'username' => 'adit_test',
            'password' => Hash::make('CorrectPass123'),
            'role' => 'Murid',
        ]);

        $response = $this->post(route('login'), [
            'username' => 'adit_test',
            'password' => 'WrongPass123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('auth_error');
        $response->assertSessionHasInput('username', 'adit_test');
        $this->assertGuest();
    }

    public function test_register_saves_real_name_and_email_and_rejects_duplicate_email(): void
    {
        $response = $this->post(route('register'), [
            'nama_lengkap' => 'Siti Aisyah Putri',
            'email' => 'Siti.Aisyah@Example.com',
            'username' => 'siti.aisyah',
            'password' => 'SecretPass123',
            'konfirmasi_password' => 'SecretPass123',
        ]);

        $response->assertRedirect(route('login', ['msg' => 'register_success']));

        $user = User::where('username', 'siti.aisyah')->firstOrFail();
        $murid = Murid::where('id_user', $user->id_user)->firstOrFail();
        $this->assertSame('Siti Aisyah Putri', $murid->nama_murid);
        $this->assertSame('siti.aisyah@example.com', $murid->email);

        // Duplicate email must redirect back with preserved input and error message
        $dup = $this->post(route('register'), [
            'nama_lengkap' => 'Orang Lain',
            'email' => 'siti.aisyah@example.com',
            'username' => 'orang.lain',
            'password' => 'SecretPass123',
            'konfirmasi_password' => 'SecretPass123',
        ]);

        $dup->assertRedirect(route('register'));
        $dup->assertSessionHas('auth_error');
        $dup->assertSessionHasInput('nama_lengkap', 'Orang Lain');
        $dup->assertSessionHasInput('username', 'orang.lain');
        $this->assertFalse(User::where('username', 'orang.lain')->exists());
    }

    public function test_murid_enforces_one_to_one_unique_user_id(): void
    {
        $user = User::create([
            'username' => 'unique_user_check',
            'password' => Hash::make('SecretPass123'),
            'role' => 'Murid',
        ]);

        Murid::create([
            'nim' => '9990001',
            'nama_murid' => 'Profil Pertama',
            'email' => 'pertama@example.com',
            'id_user' => $user->id_user,
        ]);

        $this->expectException(QueryException::class);

        Murid::create([
            'nim' => '9990002',
            'nama_murid' => 'Profil Ganda',
            'email' => 'ganda@example.com',
            'id_user' => $user->id_user,
        ]);
    }
}
