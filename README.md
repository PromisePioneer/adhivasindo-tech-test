# Adhivasindo Tech Test API

REST API untuk manajemen user dengan autentikasi Sanctum dan pencarian student dari API eksternal.

## Requirements

- PHP 8.2+
- Composer
- MySQL

## Instalasi

```bash
# Clone repository
git clone https://github.com/PromisePioneer/adhivasindo-tech-test.git
cd adhivasindo-tech-test

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env dengan kredensial database kamu
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adhivasindo
DB_USERNAME=root
DB_PASSWORD=password_kamu

# Generate app key
php artisan key:generate

# Jalankan migration
php artisan migrate

# Seed data dummy user
php artisan db:seed
```

## Menjalankan Server

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## Endpoint API

### Autentikasi

#### POST /api/login
Login dan dapatkan token.

**Request:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin",
            "email": "admin@example.com"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### POST /api/logout
Logout (revoke token). **Butuh auth**.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "message": "Logout successful"
}
```

---

### Manajemen User

#### GET /api/users
List semua user (paginated). **Butuh auth**.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "id": 1,
            "name": "Admin",
            "email": "admin@example.com",
            "created_at": "2024-01-01T00:00:00+00:00",
            "updated_at": "2024-01-01T00:00:00+00:00"
        }
    ]
}
```

#### POST /api/users
Buat user baru. **Butuh auth**.

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "User created",
    "data": {
        "id": 2,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2024-01-01T00:00:00+00:00",
        "updated_at": "2024-01-01T00:00:00+00:00"
    }
}
```

#### GET /api/users/{id}
Get user berdasarkan ID. **Butuh auth**.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "data": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com",
        "created_at": "2024-01-01T00:00:00+00:00",
        "updated_at": "2024-01-01T00:00:00+00:00"
    }
}
```

---

### Pencarian Student (API Eksternal)

#### GET /api/search/{field}
Cari student dari API eksternal real-time. **Butuh auth**.

**Headers:** `Authorization: Bearer {token}`

**Path Parameters:**
- `field`: `nama`, `nim`, atau `ymd`

**Query Parameters:**
- `q`: nilai pencarian

| Field | Tipe Pencarian | Contoh |
|-------|---------------|--------|
| `nama` | Case-insensitive partial match | `?q=Turner` |
| `nim` | Exact match | `?q=9352078461` |
| `ymd` | Exact match (YYYYMMDD) | `?q=20230405` |

**Contoh:**

```bash
# Cari berdasarkan nama
GET /api/search/nama?q=Turner%20Mia

# Cari berdasarkan NIM
GET /api/search/nim?q=9352078461

# Cari berdasarkan YMD
GET /api/search/ymd?q=20230405
```

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "nama": "Turner Mia",
            "nim": "9352078461",
            "ymd": "20230405"
        }
    ],
    "meta": {
        "total": 1,
        "query": "Turner Mia",
        "field": "nama"
    }
}
```

---

## Format Response Error

```json
{
    "status": false,
    "message": "Pesan error di sini"
}
```

HTTP Status Codes:
- `200` - Sukses
- `201` - Dibuat
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `422` - Validation Error

---

## Postman Collection

Import `adhivasindo-api.postman_collection.json` ke Postman.

**Variables:**
- `base_url`: `http://localhost:8000`
- `token`: (set setelah login)

---

## Struktur Project

```
app/
├── ApiResponse.php              # Trait untuk response konsisten
├── Http/
│   ├── Controllers/Api/
│   │   ├── LoginController.php
│   │   ├── UserController.php
│   │   └── StudentSearchController.php
│   ├── Requests/
│   │   ├── LoginRequest.php
│   │   └── StoreUserRequest.php
│   └── Resources/
│       └── UserResource.php
├── Models/
│   └── User.php
└── Services/
    └── ExternalStudentService.php  # Logic untuk fetch API eksternal
```

---

## Lisensi

MIT
