# AURA Life System & Moment Photo Engine — API Documentation

## Base URL
- Production: `https://api.aura.app/api/v1`
- Local Development: `http://localhost:8000/api/v1`
- Android Emulator: `http://10.0.2.2:8000/api/v1`

## Standard Envelope Format
All responses adhere to the following JSON format:
```json
{
  "success": true,
  "message": "Operation description",
  "data": { ... },
  "meta": { ... }
}
```

---

## 1. Authentication
### `POST /api/v1/auth/token`
Generate a Sanctum Bearer token.
- **Request Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password123"
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Token generated successfully",
    "data": {
      "token": "1|sanctum_plain_text_token...",
      "user": {
        "id": 1,
        "name": "Demo User",
        "email": "user@example.com"
      }
    }
  }
  ```

---

## 2. Character Endpoints
### `GET /api/v1/character`
Retrieve AURA's core identity, visual style, and active version.

### `GET /api/v1/character/appearance`
Retrieve appearance specifications and master sheet approval status.
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "visual_style": "2d_soft_anime",
      "hair_style": "Shoulder-length loose wave with soft bangs",
      "hair_color": "Warm dark chestnut brown",
      "eye_color": "Soft amethyst hazel",
      "master_sheet_approved": false,
      "master_sheet_status": "pending_approval",
      "placeholder_active": true,
      "active_version": "v1.0.0-placeholder"
    }
  }
  ```

---

## 3. Life System Endpoints
### `GET /api/v1/life/status`
Retrieve current live status, ongoing activity, virtual location, and next planned activity.
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "character": {
        "id": "uuid",
        "name": "AURA",
        "status": "active"
      },
      "virtual_time": {
        "current_utc": "2026-11-01T15:00:00.000000Z",
        "character_local": "2026-11-01T22:00:00.000000+07:00",
        "character_timezone": "Asia/Jakarta"
      },
      "current_activity": {
        "id": "act_uuid",
        "name": "Evening Music Relaxation",
        "category": "relaxation",
        "location": {
          "name": "AURA Bedroom",
          "code": "aura_bedroom"
        },
        "start_time": "2026-11-01T12:30:00Z",
        "expected_end_time": "2026-11-01T14:30:00Z",
        "status": "active"
      },
      "next_planned_activity": {
        "name": "Reflections & Book Wind-down",
        "category": "relaxation"
      }
    }
  }
  ```

### `GET /api/v1/life/today`
Returns full daily schedule timeline.

### `GET /api/v1/life/locations`
Returns all available virtual locations and allowed activities.

---

## 4. Moment Endpoints
### `GET /api/v1/moments`
Paginated moments delivered to the authenticated user.
- **Query Params**: `page=1`, `per_page=10`

### `POST /api/v1/moments/{id}/interaction`
Record feedback or interaction on a delivered moment.
- **Request Body**:
  ```json
  {
    "interaction_type": "liked" // or "viewed", "replied", "dismissed"
  }
  ```

---

## 5. Companion Preferences
### `GET /api/v1/companion/preferences`
Retrieve user companion settings.

### `PATCH /api/v1/companion/preferences`
Update user quiet hours, daily limits, or toggle proactive messaging.
- **Request Body**:
  ```json
  {
    "proactive_messaging_enabled": true,
    "photo_sharing_enabled": true,
    "quiet_hours_start": "22:00",
    "quiet_hours_end": "08:00",
    "max_daily_proactive_messages": 2,
    "max_daily_photo_moments": 1
  }
  ```
