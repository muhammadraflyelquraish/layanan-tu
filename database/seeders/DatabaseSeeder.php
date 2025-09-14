<?php

namespace Database\Seeders;

use App\Models\Disposisi;
use App\Models\DisposisiRole;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\SPJCategory;
use App\Models\User;
use App\Models\UserRole;
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
                "is_allow_deleted" => false,
                "role_id" => 1,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 1,
                    'ROLE' => 1,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 1,
                    'DISPOSISI' => 1,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Pemohon",
                "role_id" => 2,
                "is_disposition" => false,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 0,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 0,
                ]
            ],
            [
                "name" => "Administrasi Umum (Tata Usaha)",
                "role_id" => 3,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 1,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Administrasi Umum (Dekan)",
                "role_id" => 4,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Keuangan",
                "role_id" => 5,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 1,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Dosen",
                "role_id" => 6,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 0,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 0,
                ]
            ],
            [
                "name" => "Prodi",
                "role_id" => 7,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Sekretaris Prodi",
                "role_id" => 8,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 1,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "PLT",
                "role_id" => 9,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Akademik",
                "role_id" => 10,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Umum",
                "role_id" => 11,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
            [
                "name" => "Perpustakaan",
                "role_id" => 12,
                "is_disposition" => true,
                "is_allow_deleted" => false,
                "permission" => [
                    'DASHBOARD' => 1,
                    'USER' => 0,
                    'ROLE' => 0,
                    'LETTER' => 1,
                    'SPJ' => 0,
                    'LABEL_SPJ' => 0,
                    'DISPOSISI' => 0,
                    'ARSIP' => 1,
                ]
            ],
        ];

        $users = [
            [
                "name" => "Admin TU",
                "email" => "admin@gmail.com",
                "roles" => [
                    ["role_id" => 1],
                ]
            ],
            [
                "name" => "Gibral Anugrah, S.Kom",
                "email" => "pemohonti@gmail.com",
                "roles" => [
                    ["role_id" => 2, "prodi_id" => 5]
                ]
            ],
            [
                "name" => "Muhammad Rafly El Quraish, S.Kom",
                "email" => "pemohonmatematika@gmail.com",
                "roles" => [
                    ["role_id" => 2, "prodi_id" => 3],
                ]
            ],
            [
                "name" => "Susi Sundari, SE",
                "email" => "tu@gmail.com",
                "roles" => [
                    ["role_id" => 3],
                ]
            ],
            [
                "name" => "Alwi Khasani, SM",
                "email" => "dekan@gmail.com",
                "roles" => [
                    ["role_id" => 4],
                ]
            ],
            [
                "name" => "Nia Sumianingsih, SE",
                "email" => "keuangan@gmail.com",
                "roles" => [
                    ["role_id" => 5],
                ]
            ],
            [
                "name" => "Dr. Dewi Kharani, M.Sc",
                "email" => "proditi@gmail.com",
                "roles" => [
                    ["role_id" => 6, "prodi_id" => 5],
                    ["role_id" => 7, "prodi_id" => 5],
                ]
            ],
            [
                "name" => "Saepul Aripiyanto, S.Kom",
                "email" => "sekproditi@gmail.com",
                "roles" => [
                    ["role_id" => 6, "prodi_id" => 5],
                    ["role_id" => 8, "prodi_id" => 5],
                ]
            ],
            [
                "name" => "Zulmaneri, MM",
                "email" => "prodiagri@gmail.com",
                "roles" => [
                    ["role_id" => 6, "prodi_id" => 7],
                    ["role_id" => 7, "prodi_id" => 7],
                ]
            ],
            [
                "name" => "Dr. Qurrotulaini, MT",
                "email" => "prodisi@gmail.com",
                "roles" => [
                    ["role_id" => 6, "prodi_id" => 2],
                    ["role_id" => 7, "prodi_id" => 2],
                ]
            ],
            [
                "name" => "Taufik E.S. M.Sc. Tech. Ph.D",
                "email" => "prodimatematika@gmail.com",
                "roles" => [
                    ["role_id" => 7, "prodi_id" => 3],
                    ["role_id" => 6, "prodi_id" => 5],
                ]
            ]
        ];

        $dispositions = [
            [
                "name" => "Dekan",
                "approver" => [
                    ["role_id" => 4],
                ],
                "urutan" => 1
            ],
            [
                "name" => "Wadek Akademik",
                "approver" => [
                    ["role_id" => 4],
                ],
                "urutan" => 2,
            ],
            [
                "name" => "Wadek Kemahasiswaan",
                "approver" => [
                    ["role_id" => 4],
                ],
                "urutan" => 3,
            ],
            [
                "name" => "Wadek Administrasi Umum",
                "approver" => [
                    ["role_id" => 4],
                ],
                "urutan" => 4,
            ],
            [
                "name" => "Kabag TU",
                "approver" => [
                    ["role_id" => 3],
                ],
                "urutan" => 5,
            ],
            [
                "name" => "Keuangan",
                "approver" => [
                    ["role_id" => 5],
                ],
                "urutan" => 6,
            ],
            [
                "name" => "Prodi Fisika",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 1],
                    ["role_id" => 8, "prodi_id" => 1],
                ],
                "urutan" => 7,
            ],
            [
                "name" => "Prodi Sistem Informasi",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 2],
                    ["role_id" => 8, "prodi_id" => 2],
                ],
                "urutan" => 8,
            ],
            [
                "name" => "Prodi Matematika",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 3],
                    ["role_id" => 8, "prodi_id" => 3],
                ],
                "urutan" => 9,
            ],
            [
                "name" => "Prodi Kimia",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 4],
                    ["role_id" => 8, "prodi_id" => 4],
                ],
                "urutan" => 10,
            ],
            [
                "name" => "Prodi Teknik Informatika",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 5],
                    ["role_id" => 8, "prodi_id" => 5],
                ],
                "urutan" => 11,
            ],
            [
                "name" => "Prodi Biologi",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 6],
                    ["role_id" => 8, "prodi_id" => 6],
                ],
                "urutan" => 12,
            ],
            [
                "name" => "Prodi Agribisnis",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 7],
                    ["role_id" => 8, "prodi_id" => 7],
                ],
                "urutan" => 13,
            ],
            [
                "name" => "Prodi Teknik Pertambangan",
                "approver" => [
                    ["role_id" => 7, "prodi_id" => 8],
                    ["role_id" => 8, "prodi_id" => 8],
                ],
                "urutan" => 14,
            ],
            [
                "name" => "PLT",
                "approver" => [
                    ["role_id" => 9],
                ],
                "urutan" => 15,
            ],
            [
                "name" => "Akademik",
                "approver" => [
                    ["role_id" => 10],
                ],
                "urutan" => 16,
            ],
            [
                "name" => "Umum",
                "approver" => [
                    ["role_id" => 11],
                ],
                "urutan" => 17,
            ],
            [
                "name" => "Perpustakaan",
                "approver" => [
                    ["role_id" => 12],
                ],
                "urutan" => 18,
            ],
        ];

        $labelSPJS = [
            [
                "nama" => "SK KPA Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Amprahan Honor Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Daftar Hadir Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Bukti Lembar MCM *apabila dibayar melalui non tunai/MCM",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Lembar SPPR Aplikasi Bendara *non tunai/MCM",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Surat Undangan Menjadi Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Surat Undangan Kegiatan Kepada Peserta (Link Zoom)",
                "jenis" => "FILE",
            ],
            [
                "nama" => "CV Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "NPWP, dan Lembar Depan Rekening Bank",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Daftar Hadir Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Foto Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Jadwal/Susun Acara",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Materi/Handout Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Materi/Handout Narasumber",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Notulensi Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Proposal Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Laporan Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Rekaman (Dalam Bentuk CD) dan Melampirkan Link",
                "jenis" => "LINK",
            ],
            [
                "nama" => "Surat Pengajuan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Kwitansi Pembelian",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Nota Pembelian",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Surat Undangan Kegiatan Kepada Peserta",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Foto Pembelian",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Foto Kegiatan/Acara",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Daftar Hadir Peserta Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Proposal Kegiatan",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Notulensi Kegiatan (gambaran berjalannya kegiatan dari awal sampai akhir)",
                "jenis" => "FILE",
            ],
            [
                "nama" => "Laporan Kegiatan Beserta Jadwal Kegiatan",
                "jenis" => "FILE",
            ],
        ];

        $fakultas = [
            [
                "name" => "Fakultas Sains dan Teknologi",
            ],
        ];

        $prodi = [
            [
                "id" => 1,
                "fakultas_id" => 1,
                "name" => "Fisika",
            ],
            [
                "id" => 2,
                "fakultas_id" => 1,
                "name" => "Sistem Informasi",
            ],
            [
                "id" => 3,
                "fakultas_id" => 1,
                "name" => "Matematika",
            ],
            [
                "id" => 4,
                "fakultas_id" => 1,
                "name" => "Kimia",
            ],
            [
                "id" => 5,
                "fakultas_id" => 1,
                "name" => "Teknik Informatika",
            ],
            [
                "id" => 6,
                "fakultas_id" => 1,
                "name" => "Biologi",
            ],
            [
                "id" => 7,
                "fakultas_id" => 1,
                "name" => "Agribisnis",
            ],
            [
                "id" => 8,
                "fakultas_id" => 1,
                "name" => "Teknik Pertambangan",
            ],
            [
                "id" => 9,
                "fakultas_id" => 1,
                "name" => "Magister Agribisnis",
            ],
            [
                "id" => 10,
                "fakultas_id" => 1,
                "name" => "Magister Teknologi Informasi",
            ],
        ];

        // Fakultas
        foreach ($fakultas as $fak) {
            Fakultas::create([
                "name" => $fak['name'],
            ]);
        }

        // Prodi
        foreach ($prodi as $pro) {
            Prodi::create([
                "name" => $pro['name'],
                "fakultas_id" => $pro['fakultas_id'],
            ]);
        }

        // Role & Permission
        foreach ($role_permissions as $role) {
            Role::create([
                "name" => $role['name'],
                "is_disposition" => $role['is_disposition'],
                "is_allow_deleted" => $role['is_allow_deleted'],
            ]);
            foreach ($role['permission'] as $permission => $is_permitted) {
                RolePermission::create([
                    "role_id" => $role['role_id'],
                    "menu" => $permission,
                    "is_permitted" => $is_permitted,
                ]);
            }
        }

        // User & User Role
        foreach ($users as $user) {
            $newUser = User::create([
                "name" => $user['name'],
                "email" => $user['email'],
                "password" => Hash::make("Admin1++"),
                "status" => "ACTIVE"
            ]);

            foreach ($user["roles"] as $rl) {
                UserRole::create([
                    "user_id" => $newUser->id,
                    "role_id" => $rl['role_id'] ?? null,
                    "prodi_id" => $rl['prodi_id'] ?? null,
                ]);
            };
        };

        foreach ($dispositions as $disposition) {
            $disposisi = Disposisi::create([
                "name" => $disposition['name'],
                "urutan" => $disposition['urutan'],
            ]);

            foreach ($disposition["approver"] as $app) {
                DisposisiRole::create([
                    "disposisi_id" => $disposisi->id,
                    "role_id" => $app['role_id'] ?? null,
                    "prodi_id" => $app['prodi_id'] ?? null,
                ]);
            };
        };

        foreach ($labelSPJS as $labelSPJ) {
            SPJCategory::create([
                "nama" => $labelSPJ['nama'],
                "jenis" => $labelSPJ['jenis'],
            ]);
        };
    }
}
