<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        // 1. Cari dosen berdasarkan email yang ada di database kamu
        $dosen = User::where('email', 'dosen@example.com')->first();
        
        // Cek jaga-jaga kalau datanya terhapus
        if (!$dosen) {
            $this->command->error('Waduh wok, dosen dengan email dosen@example.com tidak ditemukan di database!');
            return;
        }

        // 2. Siapkan data-data aktivitas dummy
        $activities = [
            [
                'activity_name'       => 'Pelatihan Lifeskill Survive di Hutan, Fahutan IPB',
                'description'         => 'Kegiatan pelatihan intensif untuk membekali mahasiswa dengan keterampilan bertahan hidup dasar di hutan tropis. Pelatihan meliputi navigasi darat, mencari sumber air bersih, hingga pembuatan bivak darurat.',
                'job'                 => 'Instruktur Utama',
                'activity_type'       => ['Pelatihan', 'Pengabdian Masyarakat'],
                'quote'               => 'Bertahan hidup bukan soal kekuatan, tapi kecerdikan beradaptasi.',
                'activity_date_start' => Carbon::now()->subDays(10),
                'activity_date_end'   => Carbon::now()->subDays(8),
            ],
            [
                'activity_name'       => 'Seminar Implementasi Deteksi Objek YOLOv8',
                'description'         => 'Pemaparan hasil eksperimen tuning layer Artificial Neural Network dan penggunaan model YOLOv8 untuk sistem monitoring secara real-time.',
                'job'                 => 'Narasumber',
                'activity_type'       => ['Seminar', 'Riset IT'],
                'quote'               => 'Teknologi hadir untuk memberikan peringatan dini yang menyelamatkan nyawa.',
                'activity_date_start' => Carbon::now()->subMonths(1),
                'activity_date_end'   => null,
            ],
            [
                'activity_name'       => 'Pengembangan Sistem Deteksi Bot pada Media Sosial',
                'description'         => 'Rapat koordinasi dan penyusunan proposal penelitian mengenai analisis persepsi publik terhadap kebijakan pemerintah dengan melakukan filter otomatis pada akun-akun bot.',
                'job'                 => 'Ketua Peneliti',
                'activity_type'       => ['Penelitian', 'FGD'],
                'quote'               => 'Data yang bersih adalah kunci dari analisa yang akurat.',
                'activity_date_start' => Carbon::now()->subMonths(5),
                'activity_date_end'   => Carbon::now()->subMonths(4),
            ],
            [
                'activity_name'       => 'Uji Kompetensi (UJIKOM) Fullstack Web Development',
                'description'         => 'Menjadi penguji dalam sertifikasi kompetensi mahasiswa untuk pengembangan aplikasi web menggunakan React, TypeScript, dan Node.js.',
                'job'                 => 'Penguji Eksternal',
                'activity_type'       => ['Sertifikasi', 'Akademik'],
                'quote'               => null,
                'activity_date_start' => Carbon::now()->subWeeks(2),
                'activity_date_end'   => Carbon::now()->subWeeks(2),
            ]
        ];

        // 3. Masukkan data ke tabel activities menggunakan ID milik Fulano
        foreach ($activities as $act) {
            Activity::create([
                'user_id'             => $dosen->id, // <- Menggunakan ID dari Pak Fulano (ID: 2)
                'activity_name'       => $act['activity_name'],
                'description'         => $act['description'],
                'job'                 => $act['job'],
                'activity_type'       => $act['activity_type'], 
                'quote'               => $act['quote'],
                'activity_date_start' => $act['activity_date_start'],
                'activity_date_end'   => $act['activity_date_end'],
            ]);
        }
        
        $this->command->info('Mantap wok! Data Activity berhasil ditambahkan untuk Pak Fulano de Tal.');
    }
}