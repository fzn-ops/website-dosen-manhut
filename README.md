# DosenManhut - Web Profil Dosen Manajemen Hutan

DosenManhut adalah platform web profil dan informasi aktivitas akademik untuk dosen Manajemen Hutan di IPB University. Proyek ini dikembangkan dengan arsitektur hibrida untuk menyeimbangkan performa publik (SEO-friendly) dan pengalaman dasbor interaktif (SPA).

## 🚀 Tech Stack

Aplikasi ini dibangun menggunakan teknologi berikut:
* **Backend:** [Laravel 11.x](https://laravel.com)
* **Frontend (Dasbor Admin):** [Vue.js 3](https://vuejs.org/) + [Inertia.js](https://inertiajs.com/)
* **Frontend (Landing Page & Publik):** Pure Laravel Blade 
* **Styling:** [Tailwind CSS](https://tailwindcss.com/)
* **Build Tool:** Vite

## 🏗️ Struktur Arsitektur

Proyek ini sengaja memisahkan pendekatan rendering untuk memaksimalkan performa:
1. **Public Pages (`/`, `/tentang-kami`, `/dosen`, dll):** Menggunakan murni **Blade Templates** dan CSS untuk memastikan *loading* yang sangat cepat dan optimasi SEO maksimal.
2. **Admin Dashboard (`/admin/*`):** Menggunakan **Inertia + Vue** untuk memberikan pengalaman *Single Page Application* (SPA) yang dinamis, reaktif, dan mulus saat mengelola data.

## 🛠️ Persyaratan Sistem

Pastikan sistem komputermu sudah terinstall:
* PHP >= 8.2
* Composer
* Node.js & NPM
* Database (MySQL / PostgreSQL / SQLite)

## ⚙️ Instalasi & Setup Lokal

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di mesin lokal:

1. **Clone repository ini** (jika menggunakan git):
   ```bash
   git clone <url-repo-kamu>
   cd dosenmanhut
   ```

2. **Install dependensi PHP dan Node.js:**
   ```bash
   composer install
   npm install
   ```

3. **Salin file konfigurasi environment:**
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi Database:**
   Buka file `.env` dan sesuaikan kredensial database kamu (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

6. **Jalankan Migrasi Database:**
   ```bash
   php artisan migrate
   ```

7. **Compile Assets (Tailwind & Vue):**
   ```bash
   npm run dev
   ```

8. **Jalankan Development Server Laravel:**
   Buka terminal baru dan jalankan:
   ```bash
   php artisan serve
   ```

Aplikasi sekarang dapat diakses melalui `http://localhost:8000`.

## 📝 Catatan Pengembangan

Karena proyek ini saat ini dikembangkan secara mandiri, manajemen *task* dan *versioning* difokuskan pada efisiensi teknis dan fungsionalitas utama. Struktur *layout* publik dikelola di dalam folder `resources/views/components/layouts` (menggunakan Blade Components), sementara halaman dasbor dikelola di `resources/js/Pages`.

## 🛡️ Lisensi

Proyek ini bersifat tertutup (Proprietary) untuk keperluan akademik dan administratif Manajemen Hutan.