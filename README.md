# AURA — Adaptive Understanding & Responsive Assistant

> **AI Virtual Life Companion Web Application**  
> Dibuat dengan stack Laravel 12 modern, Google Gemini AI 2.5 Flash, responsif penuh untuk PC/Desktop dan Mobile Web browser.

---

## ✨ Fitur Utama

- **Web-First Responsive Design**: Tampilan desktop dengan sidebar profil & live status widget; tampilan mobile web layar penuh dengan drawer menu sentuh.
- **Skenario Pertemuan Acak & Narasi Prolog**: Pop-up prolog otomatis saat perkenalan pertama atau obrolan baru, membentuk latar belakang pertemuan yang natural (panitia kampus, reuni teman lama, cafe, rekomendasi teman, dll.).
- **Gaya Bicara Otentik & Adaptif**: AURA merespons dengan gaya bahasa gadis Indonesia (slang gaul, partikel santai, emosi ekspresif) dan menyesuaikan gaya bicaranya dengan skenario pertemuan.
- **Dinamika Panjang Obrolan**: Respons AURA semakin antusias dan hangat ketika kamu bercerita panjang, serta tidak memaksakan topik berlebihan.
- **Sistem Kehidupan Virtual Mandiri (Virtual Life System)**: Jadwal kegiatan real-time (tidur, kuliah, santai, kafe), jam lokal WIB, mood dinamis, dan indikator status online/offline.
- **PWA Ready**: Dapat di-"Add to Home Screen" di smartphone iOS dan Android untuk tampilan layar penuh seperti aplikasi native tanpa instalasi app store.
- **Production-Ready**: Dilengkapi Dockerfile multi-arch, docker-compose, migrasi otomatis, dan integrasi Google Gemini API.

---

## 🚀 Menjalankan di Komputer Lokal (Windows)

### Cara Cepat (1-Klik via Batch File):
1. Pastikan PHP 8.2+ dan Composer sudah terinstal di komputer Anda.
2. Buka folder proyek, lalu klik dua kali file **`start_aura_server.bat`**.
3. Buka browser dan akses: `http://localhost:8000`.

### Cara Manual (Terminal):
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

> **Catatan**: Jangan lupa mengisi `GEMINI_API_KEY` di file `backend/.env` dengan API Key dari [Google AI Studio](https://aistudio.google.com/) agar otak AI AURA dapat berpikir dan merespons.

---

## 🐳 Menjalankan dengan Docker

```bash
# Set Gemini API Key di environment atau .env
export GEMINI_API_KEY="AIzaSy...your_gemini_key"

# Jalankan container
docker compose up -d --build
```
Aplikasi langsung dapat diakses di `http://localhost:8000`.

---

## 🌐 Panduan Hosting & Deployment

Untuk panduan lengkap cara hosting ke platform gratis/berbayar:
- **Cloud PaaS (Railway / Render / Koyeb)** — *1-Click deploy dari GitHub*
- **VPS Ubuntu (Docker Compose / Nginx + PHP 8.2 Native)**
- **Shared Hosting (cPanel)**

👉 **Silakan baca panduan lengkap di: [`docs/DEPLOYMENT_GUIDE.md`](docs/DEPLOYMENT_GUIDE.md)**

---

## 📁 Struktur Direktori

```
AURA/
├── backend/               # Aplikasi utama Web Laravel 12 & Simulator
│   ├── app/
│   │   ├── Services/Ai/   # Integrasi Google Gemini LLM Provider & Prompt Engine
│   │   └── Services/Personalization/ # Skenario cerita & narasi perkenalan
│   ├── database/          # Migrasi & Seeders (Life schedule, karakter master sheet)
│   ├── docker/            # Konfigurasi Nginx, Supervisord, PHP.ini, Entrypoint
│   └── resources/views/   # welcome.blade.php (UI Web PC & Mobile responsive)
├── docs/                  # Dokumentasi teknis & deployment guide
├── docker-compose.yml     # Konfigurasi Docker compose siap pakai
├── Dockerfile             # Dockerfile produksi berbasis PHP 8.2 Alpine + Nginx
└── start_aura_server.bat  # Launcher lokal 1-klik untuk Windows
```
