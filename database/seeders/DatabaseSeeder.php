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
                "approver_id" => 3,
                "urutan" => 7,
            ],
            [
                "name" => "Prodi Agribisnis",
                "approver_id" => 3,
                "urutan" => 8,
            ],
            [
                "name" => "Prodi Sistem Informasi",
                "approver_id" => 3,
                "urutan" => 9,
            ],
            [
                "name" => "Prodi Matematika",
                "approver_id" => 3,
                "urutan" => 10,
            ],
            [
                "name" => "Prodi Fisika",
                "approver_id" => 3,
                "urutan" => 11,
            ],
            [
                "name" => "Prodi Kimia",
                "approver_id" => 3,
                "urutan" => 12,
            ],
            [
                "name" => "Prodi Biologi",
                "approver_id" => 3,
                "urutan" => 13,
            ],
            [
                "name" => "Prodi Teknik Pertambangan",
                "approver_id" => 3,
                "urutan" => 14,
            ],
            [
                "name" => "PLT",
                "approver_id" => 3,
                "urutan" => 15,
            ],
            [
                "name" => "Akademik",
                "approver_id" => 3,
                "urutan" => 16,
            ],
            [
                "name" => "Umum",
                "approver_id" => 3,
                "urutan" => 17,
            ],
            [
                "name" => "Perpus",
                "approver_id" => 3,
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
                "password" => Hash::make("Admin1++"),
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
