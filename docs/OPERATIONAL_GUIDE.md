# AURA Operational & Environment Guide

## 1. Environment Variables Configuration (`.env`)

Add the following environment variables to `backend/.env`:

```env
# Application
APP_NAME=AURA
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.aura.app

# Database (PostgreSQL + pgvector)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=aura_db
DB_USERNAME=aura_user
DB_PASSWORD=secret_password

# Cache & Queue (Redis)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# AURA Life & Visual Configuration
AURA_CHARACTER_TIMEZONE=Asia/Jakarta
AURA_MAX_DAILY_PROACTIVE=2
AURA_MAX_DAILY_PHOTO=1
AURA_DAILY_IMAGE_BUDGET=50.00
AURA_IMAGE_PROVIDER=mock

# Firebase Cloud Messaging
FCM_SERVER_KEY=your-firebase-server-key
FCM_SENDER_ID=your-firebase-sender-id
```

---

## 2. Running Services

### A. Database Migrations and Seeders
```bash
php artisan migrate --force
php artisan db:seed --class=AuraLifeSystemSeeder --force
```

### B. Redis Queue Worker
Start workers with concurrency and memory limits:
```bash
php artisan queue:work redis --queue=default,moments,visuals --tries=3 --timeout=90
```

### C. Cron Scheduler
Configure Linux cron to run every minute:
```cron
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

Or for local development:
```bash
php artisan schedule:work
```

---

## 3. Failure Handling & Reconciliation Runbook

| Incident | Automatic Fallback | Manual Action |
|---|---|---|
| **Worker crashed during active activity** | `ReconcileActivityStateJob` detects stale activity and transitions to completed | Run `php artisan queue:restart` |
| **Image generation provider down** | Falls back to Reusable Asset or text-only moment | Check provider quota / API key |
| **FCM push failure** | Message safely persisted in DB conversation; user views on next open | Check device token validity |
| **Master Character Sheet Updated** | Register new version in `character_versions` and mark asset `approved` | Run seeder/migration update |
