# Auth API Contract

Base path: `/api/v1`

## Standard JSON Format

```json
{
  "success": true,
  "message": "Request completed successfully.",
  "data": {},
  "errors": null,
  "meta": {}
}
```

## Auth Requirement Placeholder

Authentication will use Laravel Sanctum. Public endpoints do not require a token. Protected endpoints require:

```http
Authorization: Bearer <token>
```

## Endpoint Table

| Method | Endpoint | Description | Auth Required |
| --- | --- | --- | --- |
| POST | `/api/v1/auth/register` | Register a new user account | No |
| POST | `/api/v1/auth/login` | Authenticate user and return token | No |
| GET | `/api/v1/auth/me` | Get authenticated user profile | Yes |
| POST | `/api/v1/auth/logout` | Revoke current access token | Yes |

## Request Example

```json
{
  "name": "Student User",
  "email": "student@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

## Response Example

```json
{
  "success": true,
  "message": "Authentication completed successfully.",
  "data": {
    "user": {
      "id": 1,
      "name": "Student User",
      "email": "student@example.com"
    },
    "token": "plain-text-token-placeholder"
  },
  "errors": null,
  "meta": {}
}
```

## Error Response Example

```json
{
  "success": false,
  "message": "Validation failed.",
  "data": null,
  "errors": {
    "email": [
      "The email field is required."
    ]
  },
  "meta": {}
}
```

