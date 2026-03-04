<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\{Role, Permission};

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ────────────────────────────────────────
        $permissions = [
            // Bookings
            'view bookings', 'create bookings', 'edit bookings', 'cancel bookings',
            'checkin guests', 'checkout guests',

            // Rooms
            'view rooms', 'edit rooms', 'manage room types',

            // Guests
            'view guests', 'edit guests',

            // Restaurant
            'view orders', 'manage orders', 'manage menu', 'manage tables',

            // Events
            'view events', 'manage events', 'manage event packages',

            // Tickets
            'view tickets', 'scan tickets',

            // Reports
            'view reports',

            // HR
            'manage jobs', 'view applications', 'manage applications',

            // Gift Cards & Loyalty
            'view gift cards', 'view loyalty',

            // Settings (super_admin only)
            'manage settings', 'manage users', 'manage pricing',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Roles & their permissions ──────────────────────────

        $roles = [
            'super_admin' => $permissions, // all permissions

            'manager' => [
                'view bookings', 'create bookings', 'edit bookings', 'cancel bookings',
                'checkin guests', 'checkout guests',
                'view rooms', 'edit rooms',
                'view guests', 'edit guests',
                'view orders', 'manage orders',
                'view events', 'manage events',
                'view tickets', 'scan tickets',
                'view reports',
                'manage jobs', 'view applications', 'manage applications',
                'view gift cards', 'view loyalty',
            ],

            'receptionist' => [
                'view bookings', 'create bookings', 'edit bookings',
                'checkin guests', 'checkout guests',
                'view rooms', 'edit rooms',
                'view guests', 'edit guests',
                'view tickets',
                'view gift cards', 'view loyalty',
            ],

            'fnb_staff' => [
                'view orders', 'manage orders',
                'view guests',
            ],

            'housekeeper' => [
                'view rooms', 'edit rooms',
            ],

            'hr_admin' => [
                'manage jobs', 'view applications', 'manage applications',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        // ── Assign roles to existing users ─────────────────────
        $userRoles = [
            'admin@kgr.co.ke' => 'super_admin',
            'james@kgr.co.ke' => 'manager',
            'grace@kgr.co.ke' => 'receptionist',
            'peter@kgr.co.ke' => 'fnb_staff',
            'ann@kgr.co.ke'   => 'housekeeper',
            'hr@kgr.co.ke'    => 'hr_admin',
        ];

        foreach ($userRoles as $email => $role) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => ucfirst(explode('@', $email)[0]),
                    'password' => Hash::make('password'),
                ]
            );
            $user->syncRoles([$role]);
        }

        $this->command->info('✓ Roles, permissions and user assignments seeded.');
    }
}
