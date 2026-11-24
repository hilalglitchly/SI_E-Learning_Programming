<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-admin {username} {--role=Admin} {--password=}', function (string $username) {
    $role = ucfirst(strtolower((string) $this->option('role')));
    if (!in_array($role, ['Admin', 'Pimpinan'], true)) {
        $this->error('Role hanya boleh Admin atau Pimpinan.');
        return 1;
    }

    $username = trim($username);
    if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username)) {
        $this->error('Username harus 3-50 karakter (huruf, angka, ., _, -).');
        return 1;
    }

    $password = (string) ($this->option('password') ?: $this->secret('Masukkan password (minimal 12 karakter)'));
    if (strlen($password) < 12) {
        $this->error('Password produksi minimal harus 12 karakter.');
        return 1;
    }

    $user = User::updateOrCreate(
        ['username' => $username],
        ['password' => Hash::make($password), 'role' => $role]
    );

    $this->info("Akun {$user->role} '{$user->username}' berhasil disimpan (ID: {$user->id_user}).");
    return 0;
})->purpose('Buat atau perbarui akun Admin/Pimpinan produksi dengan password kuat');
