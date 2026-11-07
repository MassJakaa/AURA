# Panduan Lengkap Hosting & Deployment AURA (Web & Mobile Web)

Dokumen ini adalah panduan lengkap langkah-demi-langkah untuk mendeploy dan menghosting **AURA Web Companion** agar dapat diakses publik dari PC, laptop, maupun browser handphone (Mobile Web) secara online 24/7.

---

## 🌟 Ringkasan Arsitektur Web AURA

- **Frontend**: Responsive Single-Page Web App (Blade + Vanilla JS + CSS3 Modern).
  - Tampilan **Desktop (> 880px)**: Sidebar profil AURA, status virtual live, kotak skenario aktif, dan ruang chat lebar yang nyaman.
  - Tampilan **Mobile (≤ 880px)**: Tampilan native-style penuh layar, drawer menu geser, tombol ramah jemari, dan safe-area support untuk notch smartphone.
- **Backend**: Laravel 12 (PHP 8.2+) dengan arsitektur REST API & Simulator.
- **AI Brain**: Google Gemini API (`gemini-2.5-flash` / `gemini-2.0-flash`) dengan prompt persona gadis Indonesia otentik & adaptasi gaya bicara skenario pertemuan.
- **Database**: 
  - **SQLite (Default / Zero-Config)**: Tidak membutuhkan server database terpisah, otomatis dibuat dan disiapkan.
  - **MySQL / PostgreSQL (Opsional)**: Didukung penuh jika ingin menggunakan database terkelola (RDS/PlanetScale/Supabase).
- **Deployment Ready**: Dilengkapi `Dockerfile`, `docker-compose.yml`, dan script otomatisasi migrasi & seeding.

---

## 🔑 Langkah 0: Mendapatkan Google Gemini API Key

AURA membutuhkan Google Gemini API Key agar otaknya dapat merespons percakapan:

1. Kunjungi **[Google AI Studio](https://aistudio.google.com/)**.
2. Masuk menggunakan akun Google Anda.
3. Klik tombol **Get API key** di pojok kiri atas.
4. Klik **Create API key** (pilih project baru atau project yang sudah ada).
5. Salin API key yang muncul (format: `AIzaSy...`).
6. *Catatan: Google AI Studio menyediakan kuota gratis yang sangat melimpah untuk penggunaan personal companion.*

---

## 🚀 Pilihan Hosting 1: Cloud PaaS (Railway / Render) — *Paling Mudah & Direkomendasikan*

Metode ini paling cepat karena Anda tidak perlu mengatur server Linux manual. Cukup hubungkan repository GitHub dan platform akan otomatis membangun aplikasi menggunakan `Dockerfile` yang telah disediakan.

### Opsi A: Railway.app (Sangat Cepat & Mendukung Volume Persistent)

1. Push repository AURA Anda ke akun GitHub (pribadi atau publik).
2. Buka **[Railway.app](https://railway.app)** dan login menggunakan GitHub.
3. Klik **+ New Project** -> **Deploy from GitHub repo**.
4. Pilih repositori AURA Anda.
5. Masuk ke tab **Variables** pada service yang baru dibuat, lalu tambahkan environment variables berikut:
   ```env
   GEMINI_API_KEY=AIzaSy... (API Key Anda)
   GEMINI_MODEL=gemini-2.5-flash
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=sqlite
   ```
6. Masuk ke tab **Settings** -> **Networking** -> Klik **Generate Domain** (Anda akan mendapatkan URL publik gratis, misal: `https://aura-production-xxxx.up.railway.app`).
7. *(Opsional untuk menyimpan chat permanen saat redeploy)*:
   - Di Railway canvas, klik **+ New** -> **Volume**.
   - Hubungkan volume tersebut ke service AURA dan set Mount Path ke: `/var/www/html/database`.
8. Selesai! Buka URL domain Anda dan AURA siap diajak mengobrol.

---

### Opsi B: Render.com

1. Buka **[Render.com](https://render.com)** dan buat akun.
2. Klik **New +** -> **Web Service**.
3. Hubungkan repository GitHub AURA.
4. Konfigurasikan detail berikut:
   - **Language**: `Docker`
   - **Dockerfile Path**: `Dockerfile` (atau `backend/Dockerfile` jika root directory diatur ke `backend`)
   - **Instance Type**: `Free`
5. Pada bagian **Environment Variables**, tambahkan:
   - `GEMINI_API_KEY` = `(API Key Anda)`
   - `GEMINI_MODEL` = `gemini-2.5-flash`
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
6. Klik **Create Web Service**. Render akan mem-build container dan memberikan domain HTTPS gratis (misal: `https://aura-companion.onrender.com`).

---

## 🖥️ Pilihan Hosting 2: VPS Ubuntu (Docker Compose) — *Paling Mandiri & Stabil*

Cocok untuk VPS murah seperti IDCloudHost, Niagahoster, Hetzner, DigitalOcean, atau Linode ($3 - $5/bulan).

### 1. Install Docker & Git di VPS
Jalankan di terminal SSH server Anda:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y git curl docker.io docker-compose-v2
sudo systemctl enable --now docker
```

### 2. Clone Repositori AURA
```bash
git clone https://github.com/username/AURA.git /opt/aura
cd /opt/aura
```

### 3. Buat File Environment (`.env`)
```bash
cat << 'EOF' > .env
GEMINI_API_KEY=AIzaSy...ganti_dengan_key_anda
GEMINI_MODEL=gemini-2.5-flash
APP_ENV=production
APP_DEBUG=false
APP_URL=http://ip_server_anda:8000
EOF
```

### 4. Jalankan dengan Docker Compose
```bash
sudo docker compose up -d --build
```
Aplikasi AURA akan langsung berjalan di port `8000`. Anda bisa langsung mengaksesnya melalui browser di `http://IP_SERVER:8000`. Database SQLite tersimpan di Docker volume sehingga riwayat chat dan status hidup AURA tetap aman saat server restart.

### 5. Setup Domain & SSL Gratis (Nginx Reverse Proxy + Let's Encrypt)
Jika ingin menggunakan domain sendiri (misal: `aura.namakamu.com`):

1. Arahkan DNS Record A dari domain ke IP VPS Anda.
2. Install Nginx & Certbot di host VPS:
   ```bash
   sudo apt install -y nginx certbot python3-certbot-nginx
   ```
3. Buat konfigurasi Nginx:
   ```bash
   sudo nano /etc/nginx/sites-available/aura
   ```
   Isi dengan:
   ```nginx
   server {
       server_name aura.namakamu.com;

       location / {
           proxy_pass http://127.0.0.1:8000;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;
       }
   }
   ```
4. Aktifkan konfigurasi dan dapatkan sertifikat SSL:
   ```bash
   sudo ln -s /etc/nginx/sites-available/aura /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   sudo certbot --nginx -d aura.namakamu.com
   ```
Sekarang web AURA dapat diakses aman via `https://aura.namakamu.com`!

---

## 🛠️ Pilihan Hosting 3: VPS Ubuntu Tanpa Docker (Nginx + PHP 8.2 Native)

Jika server Anda tidak ingin menggunakan Docker:

1. **Install PHP 8.2 & Ekstensi**:
   ```bash
   sudo apt update
   sudo apt install -y software-properties-common
   sudo add-apt-repository ppa:ondrej/php -y
   sudo apt update
   sudo apt install -y nginx php8.2-fpm php8.2-cli php8.2-sqlite3 php8.2-curl \
       php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath composer git
   ```

2. **Clone & Setup Aplikasi**:
   ```bash
   sudo git clone https://github.com/username/AURA.git /var/www/aura
   cd /var/www/aura/backend
   sudo composer install --no-dev --optimize-autoloader
   sudo cp .env.example .env
   ```

3. **Konfigurasi `.env`**:
   Buka file `.env` dan masukkan:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://aura.namakamu.com
   GEMINI_API_KEY=AIzaSy...
   GEMINI_MODEL=gemini-2.5-flash
   DB_CONNECTION=sqlite
   ```

4. **Inisialisasi Database & Caching**:
   ```bash
   sudo php artisan key:generate
   sudo touch database/database.sqlite
   sudo php artisan migrate --force
   sudo php artisan db:seed --force
   sudo php artisan config:cache
   sudo php artisan route:cache
   sudo php artisan view:cache
   ```

5. **Atur Hak Akses Folder**:
   ```bash
   sudo chown -R www-data:www-data /var/www/aura/backend/storage /var/www/aura/backend/bootstrap/cache /var/www/aura/backend/database
   sudo chmod -R 775 /var/www/aura/backend/storage /var/www/aura/backend/bootstrap/cache /var/www/aura/backend/database
   ```

6. **Konfigurasi Nginx Server Block**:
   ```nginx
   server {
       listen 80;
       server_name aura.namakamu.com;
       root /var/www/aura/backend/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;
       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

---

## 📱 Tips Akses Mobile: Pasang sebagai Aplikasi (PWA / Add to Home Screen)

AURA telah dioptimalkan secara responsif dengan viewport native untuk smartphone:

1. Buka URL AURA di browser handphone (Chrome di Android atau Safari di iOS).
2. **Android (Chrome)**:
   - Ketuk menu titik tiga (⋮) di pojok kanan atas.
   - Pilih **"Tambahkan ke Layar Utama"** (*Add to Home screen*) atau **"Install Aplikasi"**.
3. **iOS (Safari)**:
   - Ketuk tombol **Share** (ikon kotak panah ke atas) di bilah bawah.
   - Gulir dan pilih **"Tambah ke Layar Utama"** (*Add to Home Screen*).
4. Sekarang ikon AURA akan muncul di menu aplikasi handphone Anda dan ketika dibuka akan tampil penuh layar tanpa address bar browser, persis seperti aplikasi mobile native!

---

## 🔍 Verifikasi & Uji Mandiri Pasca Hosting

Setelah website aktif:
1. Buka halaman utama: Verifikasi modal prolog skenario perkenalan muncul secara elegan.
2. Coba kirim pesan pertama dalam Bahasa Indonesia santai: Pastikan AURA membalas sesuai kepribadiannya dan gaya bicara skenarionya.
3. Buka menu status di sidebar/drawer: Pastikan jam lokal WIB, mood, dan aktivitas virtual AURA berjalan sinkron.
4. Coba klik **"Mulai Obrolan Baru"**: Pastikan obrolan ter-reset bersih dan modal skenario baru muncul secara acak.
