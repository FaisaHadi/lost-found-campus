# Claims API

Base URL: `/api/v1`

All claim endpoints require Sanctum bearer authentication.

## Business Rules

- Users cannot claim their own reports.
- Claims require `proof_text`.
- Claims can only be submitted for reports with `status=approved` and `moderation_status=approved`.
- One report can have multiple claims from different users.
- A user can submit only one active claim per report.
- Admins review claims.
- Approving a claim changes the claim to `approved`, changes the report to `claimed`, and rejects competing pending claims.
- Rejecting a claim changes the claim to `rejected`.
- Claim approval and rejection create unread notifications for claimants.
- Admins can view all claims.
- Regular users can view claims they submitted and claims on reports they own.

## Create Claim

`POST /api/v1/claims`

Auth: required

Request:

```json
{
  "report_id": 10,
  "proof_text": "I can identify the item serial number and describe the stickers on the case."
}
```

Response:

```json
{
  "success": true,
  "message": "Claim submitted successfully and is pending admin review.",
  "data": {
    "id": 4,
    "report_id": 10,
    "status": "pending",
    "proof_text": "I can identify the item serial number and describe the stickers on the case."
  }
}
```

Validation error example:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Users cannot claim their own reports.",
    "details": {
      "report_id": [
        "Users cannot claim their own reports."
      ]
    }
  }
}
```

## List Claims

`GET /api/v1/claims`

Auth: required

Query parameters:

| Name | Description |
| --- | --- |
| `status` | `pending`, `approved`, or `rejected` |
| `report_id` | Filter by report |
| `claimant_id` | Admin only filter |
| `sort_by` | `created_at`, `updated_at`, `status` |
| `sort_dir` | `asc` or `desc` |
| `per_page` | 1 to 100 |

Example:

```http
GET /api/v1/claims?status=pending&per_page=10
Authorization: Bearer <token>
```

Response:

```json
{
  "success": true,
  "message": "Claims retrieved successfully.",
  "data": [
    {
      "id": 4,
      "report_id": 10,
      "claimant_id": 8,
      "status": "pending",
      "report": {
        "id": 10,
        "title": "Found backpack"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

## Show Claim

`GET /api/v1/claims/{id}`

Auth: admin, claimant, or report owner

Response:

```json
{
  "success": true,
  "message": "Claim retrieved successfully.",
  "data": {
    "id": 4,
    "status": "pending",
    "claimant": {
      "id": 8,
      "name": "Student User"
    }
  }
}
```

## Admin Claim Review

### Approve Claim

`PATCH /api/v1/admin/claims/{id}/approve`

Auth: admin

Response:

```json
{
  "success": true,
  "message": "Claim approved successfully.",
  "data": {
    "id": 4,
    "status": "approved",
    "reviewed_by": 1
  }
}
```

### Reject Claim

`PATCH /api/v1/admin/claims/{id}/reject`

Auth: admin

Response:

```json
{
  "success": true,
  "message": "Claim rejected successfully.",
  "data": {
    "id": 4,
    "status": "rejected",
    "reviewed_by": 1
  }
}
```
