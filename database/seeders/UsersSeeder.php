<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'NIP' => '1234567890',
                'role' => 'admin',
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'phone' => '+62 812-0000-0001',
                'profile_picture' => null,
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. 10 Data Akun Dosen
        $lecturers = [
            [
                'NIP' => '197503122000031001',
                'name' => 'Prof. Dr. Ir. Budi Santoso M.Sc.',
                'username' => 'budisantoso',
                'email' => 'budi.santoso@apps.ipb.ac.id',
                'phone' => '+62 812-3456-7890',
                'password' => Hash::make('197503122000031001'),
            ],
            [
                'NIP' => '197503122000031111',
                'name' => 'Fulano del Lagune',
                'username' => 'Fulano',
                'email' => 'dosen@example.com',
                'phone' => '+62 812-3456-7890',
                'password' => Hash::make('fulano123'),
            ],
            [
                'NIP' => '198005142005012002',
                'name' => 'Dr. Siti Aminah S.Hut., M.Si.',
                'username' => 'sitiaminah',
                'email' => 'siti.aminah@apps.ipb.ac.id',
                'phone' => '+62 813-9876-5432',
                'password' => Hash::make('198005142005012002'),
            ],
            [
                'NIP' => '198208222008121003',
                'name' => 'Dr. Hendra Setiawan S.Hut., Ph.D.',
                'username' => 'hendrasetiawan',
                'email' => 'hendra.s@apps.ipb.ac.id',
                'phone' => '+62 815-6789-0123',
                'password' => Hash::make('198208222008121003'),
            ],
            [
                'NIP' => '197001151995031004',
                'name' => 'Prof. Dr. Ir. Ahmad Fauzi M.Agr.',
                'username' => 'ahmadfauzi',
                'email' => 'ahmad.fauzi@apps.ipb.ac.id',
                'phone' => '+62 821-1234-5678',
                'password' => Hash::make('197001151995031004'),
            ],
            [
                'NIP' => '197811092003122005',
                'name' => 'Dr. Ir. Rina Marlina M.Sc.',
                'username' => 'rinamarlina',
                'email' => 'rina.marlina@apps.ipb.ac.id',
                'phone' => '+62 856-7890-1234',
                'password' => Hash::make('197811092003122005'),
            ],
            [
                'NIP' => '198404172010121006',
                'name' => 'Dr. Dedi Kusnadi S.Hut., M.Si.',
                'username' => 'dedikusnadi',
                'email' => 'dedi.kusnadi@apps.ipb.ac.id',
                'phone' => '+62 878-9012-3456',
                'password' => Hash::make('198404172010121006'),
            ],
            [
                'NIP' => '198609252014042007',
                'name' => 'Dr. Nurul Hidayah S.Hut., M.Sc.',
                'username' => 'nurulhidayah',
                'email' => 'nurul.h@apps.ipb.ac.id',
                'phone' => '+62 812-2345-6789',
                'password' => Hash::make('198609252014042007'),
            ],
            [
                'NIP' => '197306301999031008',
                'name' => 'Ir. Bambang Triyono M.For.',
                'username' => 'bambangtriyono',
                'email' => 'bambang.t@apps.ipb.ac.id',
                'phone' => '+62 819-3456-7890',
                'password' => Hash::make('197306301999031008'),
            ],
            [
                'NIP' => '198102182006042009',
                'name' => 'Dr. Sri Wahyuni S.Hut., M.Si.',
                'username' => 'sriwahyuni',
                'email' => 'sri.wahyuni@apps.ipb.ac.id',
                'phone' => '+62 822-4567-8901',
                'password' => Hash::make('198102182006042009'),
            ],
            [
                'NIP' => '198512102012121010',
                'name' => 'Dr. Eko Prasetyo S.Hut., Ph.D.',
                'username' => 'ekoprasetyo',
                'email' => 'eko.prasetyo@apps.ipb.ac.id',
                'phone' => '+62 857-5678-9012',
                'password' => Hash::make('198512102012121010'),
            ],
        ];

        foreach ($lecturers as $lecturer) {
            User::updateOrCreate(
                ['NIP' => $lecturer['NIP']],
                [
                    'name' => $lecturer['name'],
                    'username' => $lecturer['username'],
                    'role' => 'dosen',
                    'email' => $lecturer['email'],
                    'phone' => $lecturer['phone'],
                    'profile_picture' => null,
                    'password' => $lecturer['password'],
                ]
            );
        }
    }
}
