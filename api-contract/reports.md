# Reports API Contract

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

Report endpoints are expected to require Laravel Sanctum authentication unless explicitly marked public in a later phase.

```http
Authorization: Bearer <token>
```

## Endpoint Table

| Method | Endpoint | Description | Auth Required |
| --- | --- | --- | --- |
| GET | `/api/v1/reports` | List lost and found reports | Yes |
| POST | `/api/v1/reports` | Create a lost or found item report | Yes |
| GET | `/api/v1/reports/{report}` | Show report detail | Yes |
| PUT | `/api/v1/reports/{report}` | Update report detail | Yes |
| DELETE | `/api/v1/reports/{report}` | Delete or archive report | Yes |

## Request Example

```json
{
  "type": "lost",
  "title": "Black Backpack",
  "description": "Black backpack with campus notebook inside.",
  "location_name": "Main Library",
  "latitude": -6.200000,
  "longitude": 106.816666,
  "reported_at": "2026-05-13T09:00:00Z"
}
```

## Response Example

```json
{
  "success": true,
  "message": "Report created successfully.",
  "data": {
    "report": {
      "id": 1,
      "type": "lost",
      "title": "Black Backpack",
      "status": "open"
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
  "message": "Validation failed.",
  "data": null,
  "errors": {
    "title": [
      "The title field is required."
    ],
    "type": [
      "The selected type is invalid."
    ]
  },
  "meta": {}
}
```

