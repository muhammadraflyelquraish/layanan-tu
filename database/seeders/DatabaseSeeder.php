<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $role_permissions = [
            [
                "name" => "Admin",
                "is_disposition" => false,
                "role_id" => 1,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 1,
                    'ROLE' => 1,
                    'LETTER' => 1,
                    'SPJ' => 1,
                ]
            ],
            [
                "name" => "Pemohon",
                "role_id" => 2,
                "is_disposition" => false,
                "permission" => [
                    'DASHBOARD' => 0,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                ]
            ],
            [
                "name" => "TU",
                "role_id" => 3,
                "is_disposition" => true,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                ]
            ],
            [
                "name" => "Dekan",
                "role_id" => 4,
                "is_disposition" => true,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                ]
            ],
            [
                "name" => "Keuangan",
                "role_id" => 5,
                "is_disposition" => true,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                ]
            ]
        ];

        $users = [
            [
                "role_id" => 1,
                "name" => "Rafly El",
                "email" => "admin@gmail.com",
                "no_identity" => "112109100001"
            ],
            [
                "role_id" => 2,
                "name" => "Joko",
                "email" => "pemohon@gmail.com",
                "no_identity" => "112109100002"
            ],
            [
                "role_id" => 3,
                "name" => "Susi",
                "email" => "tu@gmail.com",
                "no_identity" => "112109100003"
            ],
            [
                "role_id" => 4,
                "name" => "Alwi",
                "email" => "dekan@gmail.com",
                "no_identity" => "112109100004"
            ],
            [
                "role_id" => 5,
                "name" => "Nia",
                "email" => "keuangan@gmail.com",
                "no_identity" => "112109100005"
            ]
        ];

        foreach ($role_permissions as $role) {
            Role::create([
                "name" => $role['name'],
                "is_disposition" => $role['is_disposition'],
            ]);
            foreach ($role['permission'] as $permission => $is_permitted) {
                RolePermission::create([
                    "role_id" => $role['role_id'],
                    "menu" => $permission,
                    "is_permitted" => $is_permitted,
                ]);
            }
        }

        foreach ($users as $user) {
            User::create([
                "name" => $user['name'],
                "no_identity" => $user['no_identity'],
                "email" => $user['email'],
                "password" => Hash::make("admin"),
                "role_id" => $user['role_id'],
                "status" => "ACTIVE"
            ]);
        };
    }
}
