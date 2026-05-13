# Auth API Documentation

Backend directory:

```text
D:\PABP\lost-found-campus\backend
```

Base URL for local development:

```text
http://localhost:8000/api/v1
```

Authentication uses Laravel Sanctum bearer tokens. API clients must send the token in the `Authorization` header for protected endpoints.

```http
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

## Standard Responses

Success:

```json
{
  "success": true,
  "message": "Request completed successfully.",
  "data": {}
}
```

Error:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error message."
  }
}
```

## Endpoints

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| POST | `/api/v1/auth/register` | Public | Register user and issue API token |
| POST | `/api/v1/auth/login` | Public | Authenticate user and issue API token |
| POST | `/api/v1/auth/logout` | Bearer token | Revoke current API token |
| GET | `/api/v1/auth/me` | Bearer token | Return authenticated user |

## Register

```http
POST /api/v1/auth/register
```

Request:

```json
{
  "name": "Student User",
  "email": "student@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Response:

```json
{
  "success": true,
  "message": "Registration completed successfully.",
  "data": {
    "user": {
      "id": 1,
      "name": "Student User",
      "email": "student@example.com",
      "role": "user"
    },
    "token": "1|plain-text-token",
    "token_type": "Bearer"
  }
}
```

## Login

```http
POST /api/v1/auth/login
```

Request:

```json
{
  "email": "student@example.com",
  "password": "password123"
}
```

Response:

```json
{
  "success": true,
  "message": "Login completed successfully.",
  "data": {
    "user": {
      "id": 1,
      "name": "Student User",
      "email": "student@example.com",
      "role": "user"
    },
    "token": "1|plain-text-token",
    "token_type": "Bearer"
  }
}
```

Invalid credentials:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The provided credentials are invalid."
  }
}
```

## Me

```http
GET /api/v1/auth/me
```

Headers:

```http
Authorization: Bearer <token>
```

Response:

```json
{
  "success": true,
  "message": "Authenticated user retrieved successfully.",
  "data": {
    "user": {
      "id": 1,
      "name": "Student User",
      "email": "student@example.com",
      "role": "user"
    }
  }
}
```

## Logout

```http
POST /api/v1/auth/logout
```

Headers:

```http
Authorization: Bearer <token>
```

Response:

```json
{
  "success": true,
  "message": "Logout completed successfully.",
  "data": []
}
```

Unauthenticated response:

```json
{
  "success": false,
  "error": {
    "code": "UNAUTHENTICATED",
    "message": "Authentication token is missing or invalid."
  }
}
```

## Migration Commands

Run from the backend directory:

```powershell
cd D:\PABP\lost-found-campus\backend
mysql -u root -e "CREATE DATABASE IF NOT EXISTS lost_found_campus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

For a clean local rebuild during development:

```powershell
php artisan migrate:fresh
```

## Artisan Validation Commands

```powershell
php artisan config:clear
php artisan route:list --path=api/v1
php artisan test
```

Optional local API server:

```powershell
php artisan serve
```

## Postman Testing Flow

1. Create an environment with `base_url` set to `http://localhost:8000`.
2. Send `POST {{base_url}}/api/v1/auth/register`.
3. Copy `data.token` from the response.
4. Set an environment variable named `token`.
5. Send `GET {{base_url}}/api/v1/auth/me` with `Authorization: Bearer {{token}}`.
6. Send `POST {{base_url}}/api/v1/auth/logout` with the same bearer token.
7. Send `GET {{base_url}}/api/v1/auth/me` again and confirm it returns `UNAUTHENTICATED`.
