<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun admin UJI untuk komputer pengembang saja (APP_ENV=local).
 * Email & kata sandi dibaca dari .env (DEV_ADMIN_EMAIL, DEV_ADMIN_PASSWORD),
 * yang tidak ikut tersimpan ke Git/GitHub.
 */
class DevAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        $email = env('DEV_ADMIN_EMAIL');
        $password = env('DEV_ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            $this->command?->warn('DEV_ADMIN_EMAIL / DEV_ADMIN_PASSWORD belum diisi di .env — akun admin uji tidak dibuat.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin Uji',
                'password' => $password,
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );
    }
}
