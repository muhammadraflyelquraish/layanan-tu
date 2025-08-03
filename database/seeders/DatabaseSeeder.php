<?php

namespace Database\Seeders;

use App\Models\Disposisi;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\SPJCategory;
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
                "name" => "TU",
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
                "name" => "Dekan",
                "role_id" => 4,
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
                "name" => "Prodi Teknik Informatika",
                "role_id" => 6,
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
                "name" => "Prodi Agribisnis",
                "role_id" => 7,
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
                "name" => "Prodi Sistem Informasi",
                "role_id" => 8,
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
                "name" => "Prodi Matematika",
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
                "name" => "Prodi Fisika",
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
                "name" => "Prodi Kimia",
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
                "name" => "Prodi Biologi",
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
            [
                "name" => "Prodi Teknik Pertambangan",
                "role_id" => 13,
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
                "name" => "PLT",
                "role_id" => 14,
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
                "role_id" => 15,
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
                "role_id" => 16,
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
                "name" => "Perpus",
                "role_id" => 17,
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
                "role_id" => 1,
                "name" => "Admin TU",
                "email" => "admin@gmail.com",
                "no_identity" => "0000000000001"
            ],
            [
                "role_id" => 2,
                "name" => "Gibral Anugrah",
                "email" => "pemohon@gmail.com",
                "no_identity" => "0000000000002"
            ],
            [
                "role_id" => 3,
                "name" => "Susi Sundari",
                "email" => "tu@gmail.com",
                "no_identity" => "0000000000003"
            ],
            [
                "role_id" => 4,
                "name" => "Alwi Khasani",
                "email" => "dekan@gmail.com",
                "no_identity" => "0000000000004"
            ],
            [
                "role_id" => 5,
                "name" => "Nia Sumianingsih",
                "email" => "keuangan@gmail.com",
                "no_identity" => "0000000000005"
            ]
        ];

        $dispositions = [
            [
                "name" => "Dekan",
                "approver_id" => 4,
                "urutan" => 1,
            ],
            [
                "name" => "Wadek Akademik",
                "approver_id" => 4,
                "urutan" => 2,
            ],
            [
                "name" => "Wadek Kemahasiswaan",
                "approver_id" => 4,
                "urutan" => 3,
            ],
            [
                "name" => "Wadek Administrasi Umum",
                "approver_id" => 4,
                "urutan" => 4,
            ],
            [
                "name" => "Kabag TU",
                "approver_id" => 3,
                "urutan" => 5,
            ],
            [
                "name" => "Keuangan",
                "approver_id" => 5,
                "urutan" => 6,
            ],
            [
                "name" => "Prodi Teknik Informatika",
                "approver_id" => 6,
                "urutan" => 7,
            ],
            [
                "name" => "Prodi Agribisnis",
                "approver_id" => 7,
                "urutan" => 8,
            ],
            [
                "name" => "Prodi Sistem Informasi",
                "approver_id" => 8,
                "urutan" => 9,
            ],
            [
                "name" => "Prodi Matematika",
                "approver_id" => 9,
                "urutan" => 10,
            ],
            [
                "name" => "Prodi Fisika",
                "approver_id" => 10,
                "urutan" => 11,
            ],
            [
                "name" => "Prodi Kimia",
                "approver_id" => 11,
                "urutan" => 12,
            ],
            [
                "name" => "Prodi Biologi",
                "approver_id" => 12,
                "urutan" => 13,
            ],
            [
                "name" => "Prodi Teknik Pertambangan",
                "approver_id" => 13,
                "urutan" => 14,
            ],
            [
                "name" => "PLT",
                "approver_id" => 14,
                "urutan" => 15,
            ],
            [
                "name" => "Akademik",
                "approver_id" => 15,
                "urutan" => 16,
            ],
            [
                "name" => "Umum",
                "approver_id" => 16,
                "urutan" => 17,
            ],
            [
                "name" => "Perpus",
                "approver_id" => 17,
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

        foreach ($users as $user) {
            User::create([
                "name" => $user['name'],
                "no_identity" => $user['no_identity'],
                "email" => $user['email'],
                "password" => Hash::make("tugogo+"),
                "role_id" => $user['role_id'],
                "status" => "ACTIVE"
            ]);
        };

        foreach ($dispositions as $disposition) {
            Disposisi::create([
                "name" => $disposition['name'],
                "approver_id" => $disposition['approver_id'],
                "urutan" => $disposition['urutan'],
            ]);
        };

        foreach ($labelSPJS as $labelSPJ) {
            SPJCategory::create([
                "nama" => $labelSPJ['nama'],
                "jenis" => $labelSPJ['jenis'],
            ]);
        };
    }
}
