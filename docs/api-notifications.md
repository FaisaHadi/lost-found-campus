# Notifications API

Base URL: `/api/v1`

All notification endpoints require Sanctum bearer authentication.

## Business Rules

- Notifications are stored in the database.
- Users can only list and update their own notifications.
- Notifications start with `status=unread`.
- Marking a notification as read changes `status=read` and sets `read_at`.
- Notification triggers:
  - Report approved
  - Report rejected
  - Claim approved
  - Claim rejected

## List Notifications

`GET /api/v1/notifications`

Auth: required

Query parameters:

| Name | Description |
| --- | --- |
| `status` | `unread` or `read` |
| `sort_by` | `created_at`, `updated_at`, `status` |
| `sort_dir` | `asc` or `desc` |
| `per_page` | 1 to 100 |

Example:

```http
GET /api/v1/notifications?status=unread
Authorization: Bearer <token>
```

Response:

```json
{
  "success": true,
  "message": "Notifications retrieved successfully.",
  "data": [
    {
      "id": 12,
      "title": "Report approved",
      "message": "Your report \"Lost phone near library\" has been approved.",
      "status": "unread",
      "read_at": null,
      "report_id": 3,
      "claim_id": null
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

## Mark Notification As Read

`PATCH /api/v1/notifications/{id}/read`

Auth: notification owner

Response:

```json
{
  "success": true,
  "message": "Notification marked as read.",
  "data": {
    "id": 12,
    "title": "Report approved",
    "status": "read",
    "read_at": "2026-05-13T10:00:00.000000Z"
  }
}
```

Forbidden response:

```json
{
  "success": false,
  "error": {
    "code": "FORBIDDEN",
    "message": "You are not allowed to perform this action."
  }
}
```
