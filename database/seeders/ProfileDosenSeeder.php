<?php

namespace Database\Seeders;

use App\Models\ProfileDosen;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profilesData = [
            [
                'nip' => '197503122000031001',
                'division' => 'Perencanaan Kehutanan',
                'research' => 'Inventarisasi Sumberdaya Hutan, Penginderaan Jauh Kehutanan, Pemodelan Pertumbuhan dan Hasil Hutan Tropis.',
                'scholar_link' => 'https://scholar.google.com/citations?user=budisantoso',
                'linkedin_link' => 'https://linkedin.com/in/budisantoso-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '1998',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Universitas Gadjah Mada',
                        'major' => 'Ilmu Kehutanan',
                        'graduationYear' => '2003',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Kyoto University',
                        'major' => 'Forest Resource Management',
                        'graduationYear' => '2009',
                    ],
                ],
            ],
            [
                'nip' => '197503122000031111',
                'division' => 'Perencanaan Kehutanan',
                'research' => 'Inventarisasi Sumberdaya Hutan, Penginderaan Jauh Kehutanan, Pemodelan Pertumbuhan dan Hasil Hutan Tropis.',
                'scholar_link' => 'https://scholar.google.com/citations?user=budisantoso',
                'linkedin_link' => 'https://linkedin.com/in/budisantoso-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '1998',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Universitas Gadjah Mada',
                        'major' => 'Ilmu Kehutanan',
                        'graduationYear' => '2003',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Kyoto University',
                        'major' => 'Forest Resource Management',
                        'graduationYear' => '2009',
                    ],
                ],
            ],
            [
                'nip' => '198005142005012002',
                'division' => 'Kebijakan Kehutanan',
                'research' => 'Tata Kelola Kehutanan, Perhutanan Sosial, Resolusi Konflik Sumberdaya Alam, Hukum dan Kebijakan Lingkungan.',
                'scholar_link' => 'https://scholar.google.com/citations?user=sitiaminah',
                'linkedin_link' => 'https://linkedin.com/in/sitiaminah-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '2003',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Sosiologi Pedesaan',
                        'graduationYear' => '2007',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Universitas Indonesia',
                        'major' => 'Ilmu Lingkungan',
                        'graduationYear' => '2015',
                    ],
                ],
            ],
            [
                'nip' => '198208222008121003',
                'division' => 'Pemanfaatan Sumberdaya Hutan',
                'research' => 'Pemanenan Kayu Berdampak Rendah (RIL), Ergonomi Kehutanan, Logistik Kayu Hutan Tanaman, Valuasi Ekonomi Hasil Hutan Bukan Kayu.',
                'scholar_link' => 'https://scholar.google.com/citations?user=hendrasetiawan',
                'linkedin_link' => 'https://linkedin.com/in/hendrasetiawan-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Teknologi Hasil Hutan',
                        'graduationYear' => '2005',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Georg-August-Universität Göttingen',
                        'major' => 'Forest Sciences and Forest Ecology',
                        'graduationYear' => '2009',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'University of British Columbia',
                        'major' => 'Forest Resources Management',
                        'graduationYear' => '2016',
                    ],
                ],
            ],
            [
                'nip' => '197001151995031004',
                'division' => 'Perencanaan Kehutanan',
                'research' => 'Sistem Informasi Geografis (SIG) Kehutanan, Analisis Spasial Lanskap Hutan, Mitigasi Deforestasi dan Degradasi Hutan.',
                'scholar_link' => 'https://scholar.google.com/citations?user=ahmadfauzi',
                'linkedin_link' => 'https://linkedin.com/in/ahmadfauzi-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '1993',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'University of Queensland',
                        'major' => 'Geographic Information Science',
                        'graduationYear' => '1999',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Ilmu Pengelolaan Hutan',
                        'graduationYear' => '2006',
                    ],
                ],
            ],
            [
                'nip' => '197811092003122005',
                'division' => 'Pemanfaatan Sumberdaya Hutan',
                'research' => 'Optimasi Rantai Pasok Industri Kayu, Bioenergi Berbasis Biomassa Hutan, Pemanfaatan Limbah Pemanenan Hutan.',
                'scholar_link' => 'https://scholar.google.com/citations?user=rinamarlina',
                'linkedin_link' => 'https://linkedin.com/in/rinamarlina-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Universitas Gadjah Mada',
                        'major' => 'Teknologi Hasil Hutan',
                        'graduationYear' => '2001',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Ilmu Keteknikan Pertanian',
                        'graduationYear' => '2006',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Tokyo University of Agriculture and Technology',
                        'major' => 'Bioresource Science',
                        'graduationYear' => '2013',
                    ],
                ],
            ],
            [
                'nip' => '198404172010121006',
                'division' => 'Kebijakan Kehutanan',
                'research' => 'Ekonomi Politik Kehutanan, Kelembagaan Pengelolaan Kawasan Konservasi, Valuasi Jasa Lingkungan Karbon Hutan.',
                'scholar_link' => 'https://scholar.google.com/citations?user=dedikusnadi',
                'linkedin_link' => 'https://linkedin.com/in/dedikusnadi-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '2007',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Ekonomi Sumberdaya dan Lingkungan',
                        'graduationYear' => '2011',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Wageningen University',
                        'major' => 'Forest and Nature Conservation Policy',
                        'graduationYear' => '2018',
                    ],
                ],
            ],
            [
                'nip' => '198609252014042007',
                'division' => 'Perencanaan Kehutanan',
                'research' => 'Pemodelan Dinamika Lanskap Hutan Tropis, LiDAR dan Drone Mapping untuk Estimasi Stok Karbon Hutan.',
                'scholar_link' => 'https://scholar.google.com/citations?user=nurulhidayah',
                'linkedin_link' => 'https://linkedin.com/in/nurulhidayah-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '2009',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'University of Melbourne',
                        'major' => 'Master of Forest Ecosystem Science',
                        'graduationYear' => '2013',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Ilmu Pengelolaan Hutan',
                        'graduationYear' => '2021',
                    ],
                ],
            ],
            [
                'nip' => '197306301999031008',
                'division' => 'Pemanfaatan Sumberdaya Hutan',
                'research' => 'Perencanaan Operasional Pemanenan Hutan, Keterlacakan Kayu (Timber Legality Assurance), Efisiensi Pengolahan Primer Kayu.',
                'scholar_link' => 'https://scholar.google.com/citations?user=bambangtriyono',
                'linkedin_link' => 'https://linkedin.com/in/bambangtriyono-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Teknologi Hasil Hutan',
                        'graduationYear' => '1997',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Australian National University',
                        'major' => 'Master of Forestry',
                        'graduationYear' => '2002',
                    ],
                ],
            ],
            [
                'nip' => '198102182006042009',
                'division' => 'Kebijakan Kehutanan',
                'research' => 'Analisis Konflik Agraria di Kawasan Hutan, Hak Tenurial Masyarakat Adat, Kebijakan Rehabilitasi Daerah Aliran Sungai.',
                'scholar_link' => 'https://scholar.google.com/citations?user=sriwahyuni',
                'linkedin_link' => 'https://linkedin.com/in/sriwahyuni-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Universitas Hasanuddin',
                        'major' => 'Kehutanan',
                        'graduationYear' => '2004',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Ilmu Pengelolaan Hutan',
                        'graduationYear' => '2008',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Universitas Gadjah Mada',
                        'major' => 'Ilmu Kehutanan',
                        'graduationYear' => '2017',
                    ],
                ],
            ],
            [
                'nip' => '198512102012121010',
                'division' => 'Perencanaan Kehutanan',
                'research' => 'Pemodelan Risiko Kebakaran Hutan dan Lahan, Restorasi Ekosistem Gambut, Adaptasi Perubahan Iklim Sektor Kehutanan.',
                'scholar_link' => 'https://scholar.google.com/citations?user=ekoprasetyo',
                'linkedin_link' => 'https://linkedin.com/in/ekoprasetyo-ipb',
                'educations' => [
                    [
                        'degree' => 'S1',
                        'university' => 'Institut Pertanian Bogor',
                        'major' => 'Manajemen Hutan',
                        'graduationYear' => '2008',
                    ],
                    [
                        'degree' => 'S2',
                        'university' => 'Seoul National University',
                        'major' => 'Forest Environmental Science',
                        'graduationYear' => '2012',
                    ],
                    [
                        'degree' => 'S3',
                        'university' => 'Seoul National University',
                        'major' => 'Forest Resources',
                        'graduationYear' => '2018',
                    ],
                ],
            ],
        ];

        foreach ($profilesData as $item) {
            $user = User::where('NIP', $item['nip'])->first();
            if (!$user) {
                continue;
            }

            ProfileDosen::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'division' => $item['division'],
                    'research' => $item['research'],
                    'image' => null,
                    'educations' => $item['educations'],
                    'scholar_link' => $item['scholar_link'],
                    'linkedin_link' => $item['linkedin_link'],
                ]
            );
        }
    }
}
