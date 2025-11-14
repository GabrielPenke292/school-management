<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário admin já existe
        $admin = User::where('username', 'admin')->first();

        if (!$admin) {
            User::create([
                'name' => 'Administrador',
                'username' => 'admin',
                'email' => 'admin@schoolmanagement.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);

            $this->command->info('Usuário admin criado com sucesso!');
            $this->command->info('Username: admin');
            $this->command->info('Senha: admin123');
        } else {
            $this->command->warn('Usuário admin já existe!');
        }
    }
}
