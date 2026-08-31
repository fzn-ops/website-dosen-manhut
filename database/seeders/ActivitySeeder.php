<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ProfileDosen;
use App\Models\User;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil seluruh profil dosen yang sudah terdaftar di database
        $profiles = ProfileDosen::whereNotNull('user_id')->with('user')->get();

        if ($profiles->isEmpty()) {
            // Fallback jika profile_dosen belum di-seed, ambil user dengan role dosen
            $dosenUsers = User::where('role', 'dosen')->get();
        } else {
            $dosenUsers = $profiles->pluck('user')->filter();
        }

        if ($dosenUsers->isEmpty()) {
            $this->command->error('Tidak ada data dosen atau profile dosen di database! Harap jalankan UsersSeeder dan ProfileDosenSeeder terlebih dahulu.');
            return;
        }

        // 2. Daftar 15 aktivitas bertema Manajemen Hutan (Silvikultur, Perencanaan, Kebijakan, Pemanfaatan Hutan)
        $activitiesData = [
            [
                'activity_name'       => 'Pelatihan Inventarisasi Sumberdaya Hutan Tropis & Pengukuran Biomassa',
                'description'         => 'Kegiatan pelatihan teknis lapangan untuk pengukuran biomassa dan cadangan karbon tegakan hutan menggunakan plot ukur permanen (PUP) dan teknologi terrestrial laser scanning.',
                'job'                 => 'Instruktur Utama',
                'activity_type'       => ['Workshop', 'Lainnya'],
                'quote'               => 'Akurasi data inventarisasi adalah pondasi utama pengelolaan hutan lestari jangka panjang.',
                'activity_date_start' => Carbon::now()->subDays(12),
                'activity_date_end'   => Carbon::now()->subDays(10),
            ],
            [
                'activity_name'       => 'Lokakarya Resolusi Konflik Tenurial dan Penguatan Perhutanan Sosial',
                'description'         => 'Pendampingan dan mediasi antara Kesatuan Pengelolaan Hutan (KPH) dan Lembaga Masyarakat Desa Hutan (LMDH) guna percepatan persetujuan pengelolaan hutan kemasyarakatan seluas 1.200 Ha.',
                'job'                 => 'Fasilitator Utama',
                'activity_type'       => ['Lokakarya', 'Lainnya'],
                'quote'               => 'Hutan lestari hanya akan terwujud bila masyarakat di sekitarnya hidup sejahtera dan berdaya.',
                'activity_date_start' => Carbon::now()->subDays(25),
                'activity_date_end'   => Carbon::now()->subDays(24),
            ],
            [
                'activity_name'       => 'Seminar Implementasi Reduced Impact Logging (RIL) pada Hutan Alam Produksi',
                'description'         => 'Pemaparan hasil riset tentang efektivitas penerapan teknik pemanenan ramah lingkungan untuk meminimalkan kerusakan tanah dan tegakan tinggal hingga 40% pasca pembalakan.',
                'job'                 => 'Narasumber',
                'activity_type'       => ['Seminar'],
                'quote'               => 'Pemanenan kayu modern bukan sekadar menebang pohon, tetapi menjaga regenerasi ekosistem hutan.',
                'activity_date_start' => Carbon::now()->subMonths(1),
                'activity_date_end'   => Carbon::now()->subMonths(1),
            ],
            [
                'activity_name'       => 'Bimbingan Teknis Sertifikasi Kelestarian FSC dan SVLK untuk Industri Kayu',
                'description'         => 'Pelatihan standar lacak balak (Chain of Custody) dan sertifikasi Forest Stewardship Council (FSC) bagi pengelola hutan tanaman industri dan koperasi petani kayu rakyat.',
                'job'                 => 'Konsultan Ahli',
                'activity_type'       => ['Workshop', 'Seminar'],
                'quote'               => 'Sertifikasi legalitas kayu menjamin keterbukaan rantai pasok dan daya saing di pasar global.',
                'activity_date_start' => Carbon::now()->subMonths(2),
                'activity_date_end'   => Carbon::now()->subMonths(2)->addDays(2),
            ],
            [
                'activity_name'       => 'Pelatihan Pemodelan Spasial Deforestasi dan Restorasi Gambut Berbasis Citra Satelit',
                'description'         => 'Pengembangan model prediksi kerentanan kebakaran hutan dan deforestasi menggunakan citra Sentinel-2 dan Google Earth Engine untuk deteksi titik panas secara real-time.',
                'job'                 => 'Ketua Peneliti',
                'activity_type'       => ['Workshop', 'Lainnya'],
                'quote'               => 'Teknologi geospasial memberikan kita mata elang untuk melindungi kanopi hutan nusantara.',
                'activity_date_start' => Carbon::now()->subMonths(3),
                'activity_date_end'   => Carbon::now()->subMonths(3)->addDays(1),
            ],
            [
                'activity_name'       => 'Lokakarya Pemanfaatan Hasil Hutan Bukan Kayu (HHBK): Budidaya Madu Hutan',
                'description'         => 'Program pemberdayaan petani hutan dalam pemanenan madu lebah liar (Apis dorsata) lestari serta ekstraksi getah pinus ramah pohon di kawasan hutan lindung.',
                'job'                 => 'Narasumber Teknis',
                'activity_type'       => ['Lokakarya'],
                'quote'               => 'Nilai ekonomi hasil hutan bukan kayu jauh melampaui kayu bila dikelola secara berkelanjutan.',
                'activity_date_start' => Carbon::now()->subMonths(4),
                'activity_date_end'   => Carbon::now()->subMonths(4),
            ],
            [
                'activity_name'       => 'Konsultasi Publik Penyusunan Rencana Pengelolaan Hutan Jangka Panjang (RPHJP)',
                'description'         => 'Penyelarasan zonasi pemanfaatan, perlindungan, dan rehabilitasi kawasan hutan bersama dinas kehutanan provinsi dan stakeholder multi-sektor.',
                'job'                 => 'Tenaga Ahli Perencanaan',
                'activity_type'       => ['Lokakarya', 'Seminar'],
                'quote'               => 'Rencana tata kelola yang matang adalah benteng pertama pencegahan degradasi hutan.',
                'activity_date_start' => Carbon::now()->subMonths(6),
                'activity_date_end'   => Carbon::now()->subMonths(6)->addDays(2),
            ],
            [
                'activity_name'       => 'Pelatihan Navigasi Darat dan Survival Ekologi Hutan Mahasiswa Fahutan IPB',
                'description'         => 'Kegiatan orientasi lapangan yang melatih keterampilan navigasi peta-kompas, orientasi medan karst, pendugaan potensi tegakan, dan survival darurat di Hutan Pendidikan Gunung Walat.',
                'job'                 => 'Instruktur Lapangan',
                'activity_type'       => ['Workshop', 'Lainnya'],
                'quote'               => 'Rimbawan sejati ditempa langsung oleh deru angin rimba dan ketajaman logika alam.',
                'activity_date_start' => Carbon::now()->subMonths(7),
                'activity_date_end'   => Carbon::now()->subMonths(7)->addDays(3),
            ],
            [
                'activity_name'       => 'Seminar Internasional Valuasi Ekonomi Jasa Lingkungan dan Perdagangan Karbon',
                'description'         => 'Diskusi panel kebijakan pasar karbon sukarela (Voluntary Carbon Market) dan mekanisme nilai ekonomi karbon (NEK) di sektor kehutanan (FOLU Net Sink 2030).',
                'job'                 => 'Keynote Speaker',
                'activity_type'       => ['Seminar'],
                'quote'               => 'Jasa lingkungan hutan menjaga pasokan oksigen dan air dunia yang tak ternilai harganya.',
                'activity_date_start' => Carbon::now()->subMonths(9),
                'activity_date_end'   => Carbon::now()->subMonths(9),
            ],
            [
                'activity_name'       => 'Uji Coba Lapangan Sistem Monitoring Keanekaragaman Hayati Menggunakan Bioakustik',
                'description'         => 'Pemasangan perekam suara otomatis (acoustic sensors) di tajuk pohon untuk mendeteksi keberadaan primata dan burung endemik yang terancam punah.',
                'job'                 => 'Peneliti Utama',
                'activity_type'       => ['Lainnya'],
                'quote'               => 'Suara hutan berbicara tentang kesehatan ekosistem yang ada di dalamnya.',
                'activity_date_start' => Carbon::now()->subMonths(11),
                'activity_date_end'   => Carbon::now()->subMonths(11)->addDays(4),
            ],
            [
                'activity_name'       => 'Lokakarya Restorasi Ekosistem Daerah Aliran Sungai (DAS) Hulu',
                'description'         => 'Penerapan teknik agroforestri berbasis tanaman vetiver dan pohon endemik pencegah erosi pada tebing curam kawasan tangkapan air hulu sungai.',
                'job'                 => 'Fasilitator',
                'activity_type'       => ['Lokakarya', 'Workshop'],
                'quote'               => 'Menjaga DAS hulu berarti menjamin kehidupan jutaan manusia di hilir.',
                'activity_date_start' => Carbon::now()->subMonths(14),
                'activity_date_end'   => Carbon::now()->subMonths(14)->addDays(1),
            ],
            [
                'activity_name'       => 'Pelatihan Pengujian Sifat Mekanik dan Struktur Anatomi Kayu Tropis',
                'description'         => 'Praktikum intensif karakterisasi kerapatan serat, modulus of elasticity (MOE), dan ketahanan rayap kayu cepat tumbuh jenis Sengon dan Jabon.',
                'job'                 => 'Instruktur Laboratorium',
                'activity_type'       => ['Workshop'],
                'quote'               => 'Optimalisasi sifat kayu alternatif mengurangi ketergantungan pada pembalakan kayu alam.',
                'activity_date_start' => Carbon::now()->subMonths(16),
                'activity_date_end'   => Carbon::now()->subMonths(16),
            ],
            [
                'activity_name'       => 'Focus Group Discussion Kebijakan Satu Peta (One Map Policy) Sektor Kehutanan',
                'description'         => 'Penyelarasan batas konsesi kehutanan, perkebunan, dan wilayah adat untuk menghilangkan tumpang tindih perizinan di tingkat regional.',
                'job'                 => 'Narasumber Ahli',
                'activity_type'       => ['Seminar', 'Lokakarya'],
                'quote'               => 'Kepastian hukum tata ruang kehutanan menghentikan konflik agraria yang berkepanjangan.',
                'activity_date_start' => Carbon::now()->subMonths(18),
                'activity_date_end'   => Carbon::now()->subMonths(18),
            ],
            [
                'activity_name'       => 'Studi Lapangan Dinamika Suksesi Vegetasi Pasca Kebakaran Hutan Rawa Gambut',
                'description'         => 'Pengamatan laju pertumbuhan kembali semai alami jenis Jelutung Rawa dan Meranti Merah pada petak contoh di lahan gambut terdegradasi.',
                'job'                 => 'Ketua Tim Lapangan',
                'activity_type'       => ['Lainnya'],
                'quote'               => 'Alam memiliki daya lenting luar biasa bila kita memberi ruang pemulihan yang tepat.',
                'activity_date_start' => Carbon::now()->subMonths(22),
                'activity_date_end'   => Carbon::now()->subMonths(22)->addDays(5),
            ],
            [
                'activity_name'       => 'Workshop Strategi Konservasi Genetik Pohon Langka dan Bernilai Tinggi',
                'description'         => 'Pengumpulan materi genetik (scion dan biji) pohon Ulin (Eusideroxylon zwageri) dan Gaharu (Aquilaria microcarpa) untuk pembentukan bank benih ex-situ.',
                'job'                 => 'Pemateri Utama',
                'activity_type'       => ['Workshop', 'Seminar'],
                'quote'               => 'Menyelamatkan plasma nutfah pohon langka adalah warisan masa depan bagi generasi penerus bangsa.',
                'activity_date_start' => Carbon::now()->subMonths(26),
                'activity_date_end'   => Carbon::now()->subMonths(26)->addDays(1),
            ],
        ];

        // 3. Distribusikan 15 aktivitas ke dosen-dosen yang memiliki profile dosen
        $dosenCount = $dosenUsers->count();

        foreach ($activitiesData as $index => $act) {
            // Pilih dosen secara bergantian agar semua dosen memiliki aktivitas
            $assignedDosen = $dosenUsers[$index % $dosenCount];

            Activity::updateOrCreate(
                [
                    'user_id'       => $assignedDosen->id,
                    'activity_name' => $act['activity_name'],
                ],
                [
                    'description'         => $act['description'],
                    'job'                 => $act['job'],
                    'activity_type'       => $act['activity_type'], 
                    'quote'               => $act['quote'],
                    'activity_date_start' => $act['activity_date_start'],
                    'activity_date_end'   => $act['activity_date_end'],
                ]
            );
        }

        $totalActivities = count($activitiesData);
        $this->command->info("Berhasil membuat {$totalActivities} data aktivitas bertema Manajemen Hutan yang terdistribusi ke {$dosenCount} profile dosen.");
    }
}