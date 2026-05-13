# Claims API Contract

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

Claim endpoints are expected to require Laravel Sanctum authentication.

```http
Authorization: Bearer <token>
```

## Endpoint Table

| Method | Endpoint | Description | Auth Required |
| --- | --- | --- | --- |
| GET | `/api/v1/claims` | List submitted claims | Yes |
| POST | `/api/v1/reports/{report}/claims` | Submit a claim for a report | Yes |
| GET | `/api/v1/claims/{claim}` | Show claim detail | Yes |
| PUT | `/api/v1/claims/{claim}` | Update claim information | Yes |
| POST | `/api/v1/claims/{claim}/review` | Review claim status | Yes |

## Request Example

```json
{
  "claim_message": "This item belongs to me. I can describe the contents.",
  "proof_description": "The backpack contains a blue notebook and student card."
}
```

## Response Example

```json
{
  "success": true,
  "message": "Claim submitted successfully.",
  "data": {
    "claim": {
      "id": 1,
      "report_id": 1,
      "status": "pending"
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
    "claim_message": [
      "The claim message field is required."
    ]
  },
  "meta": {}
}
```

