<?php

namespace Database\Seeders;

use App\Models\User;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define required roles
        $roles = [
            'super-admin' => 'Super Admin',
            'accountant'  => 'Accountant',
            'teacher'     => 'Teacher',
        ];

        $roleModels = [];
        foreach ($roles as $slug => $name) {
            $roleModels[$slug] = Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        // 2. Demo accounts configuration
        $demoUsers = [
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@example.com',
                'password' => 'password',
                'role'     => 'super-admin',
            ],
            [
                'name'     => 'Accountant User',
                'email'    => 'accountant@example.com',
                'password' => 'password',
                'role'     => 'accountant',
            ],
            [
                'name'     => 'Teacher User',
                'email'    => 'teacher@example.com',
                'password' => 'password',
                'role'     => 'teacher',
            ],
        ];

        foreach ($demoUsers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make($data['password']),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role to user
            $role = $roleModels[$data['role']] ?? null;
            if ($role && method_exists($user, 'assignRole')) {
                $user->assignRole($role);
            } elseif ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            $this->command?->info("Demo user '{$data['email']}' set up with role '{$data['role']}'.");
        }
    }
}
