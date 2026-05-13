# Notifications API Contract

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

Notification endpoints are expected to require Laravel Sanctum authentication.

```http
Authorization: Bearer <token>
```

## Endpoint Table

| Method | Endpoint | Description | Auth Required |
| --- | --- | --- | --- |
| GET | `/api/v1/notifications` | List user notifications | Yes |
| GET | `/api/v1/notifications/{notification}` | Show notification detail | Yes |
| POST | `/api/v1/notifications/{notification}/read` | Mark notification as read | Yes |
| POST | `/api/v1/notifications/read-all` | Mark all notifications as read | Yes |

## Request Example

```json
{
  "read": true
}
```

## Response Example

```json
{
  "success": true,
  "message": "Notification updated successfully.",
  "data": {
    "notification": {
      "id": "notification-id-placeholder",
      "read_at": "2026-05-13T10:00:00Z"
    }
  },
  "errors": null,
  "meta": {}
}
```

## Error Response Example

```json
{
  "success": false,
  "message": "Notification not found.",
  "data": null,
  "errors": {
    "notification": [
      "The requested notification could not be found."
    ]
  },
  "meta": {}
}
```

