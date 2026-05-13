# Reports and Categories API

Base URL: `/api/v1`

All report endpoints require Sanctum bearer authentication. Category listing is public. Category mutations require an admin token.

## Report Business Rules

- New reports are created with `status=pending` and `moderation_status=pending`.
- Admin approval changes `status=approved` and `moderation_status=approved`.
- Admin rejection changes `status=rejected` and `moderation_status=rejected`.
- Approved reports can receive claims.
- Approved claims change the related report to `status=claimed`.
- A report can be marked `completed` only after it is `claimed`.
- Report owners and admins can update or delete reports.
- Non-admin users can view approved reports and their own reports.
- Image uploads are stored on the `public` disk under `reports/` with unique filenames.
- Replacing or deleting a report image removes the previous file from storage.

## List Reports

`GET /api/v1/reports`

Auth: required

Query parameters:

| Name | Description |
| --- | --- |
| `keyword` | Searches title, description, and location text |
| `category_id` | Filters by category id |
| `category_slug` | Filters by category slug |
| `report_type` | `lost` or `found` |
| `status` | `pending`, `approved`, `rejected`, `claimed`, `completed` |
| `moderation_status` | Admin only filter: `pending`, `approved`, `rejected` |
| `sort_by` | `created_at`, `updated_at`, `title`, `status`, `report_type` |
| `sort_dir` | `asc` or `desc` |
| `per_page` | 1 to 100 |

Example:

```http
GET /api/v1/reports?keyword=phone&category_slug=electronics&report_type=lost&status=approved
Authorization: Bearer <token>
```

Response:

```json
{
  "success": true,
  "message": "Reports retrieved successfully.",
  "data": [
    {
      "id": 1,
      "title": "Lost phone near library",
      "report_type": "lost",
      "image_url": "http://localhost/storage/reports/example.png",
      "status": "approved",
      "moderation_status": "approved"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

## Create Report

`POST /api/v1/reports`

Auth: required

Use `multipart/form-data` when sending an image.

Request:

```http
POST /api/v1/reports
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

Fields:

```json
{
  "category_id": 1,
  "title": "Lost phone near library",
  "description": "Black phone with a cracked case.",
  "report_type": "lost",
  "image": "<jpg|jpeg|png|webp up to 4MB>",
  "latitude": -6.2,
  "longitude": 106.816666,
  "location_text": "Main library entrance"
}
```

Response:

```json
{
  "success": true,
  "message": "Report created successfully and is pending moderation.",
  "data": {
    "id": 1,
    "status": "pending",
    "moderation_status": "pending"
  }
}
```

## Show Report

`GET /api/v1/reports/{id}`

Auth: required

Response:

```json
{
  "success": true,
  "message": "Report retrieved successfully.",
  "data": {
    "id": 1,
    "title": "Lost phone near library",
    "category": {
      "id": 1,
      "name": "Electronics"
    }
  }
}
```

## Update Report

`PUT /api/v1/reports/{id}`

Auth: owner or admin

Accepted fields: `category_id`, `title`, `description`, `report_type`, `image`, `remove_image`, `latitude`, `longitude`, `location_text`, `status`.

Only `status=completed` is accepted through this endpoint, and only for claimed reports.

Request:

```json
{
  "location_text": "Security office",
  "status": "completed"
}
```

## Delete Report

`DELETE /api/v1/reports/{id}`

Auth: owner or admin

Deletes the report with soft delete and removes the stored image file.

## Admin Report Moderation

`PATCH /api/v1/admin/reports/{id}/approve`

Auth: admin

Approves a report and creates an unread notification for the report owner.

`PATCH /api/v1/admin/reports/{id}/reject`

Auth: admin

Request:

```json
{
  "reason": "Photo is unclear."
}
```

Rejects a report and creates an unread notification for the report owner.

## Categories

### List Categories

`GET /api/v1/categories`

Auth: not required

Query parameters: `keyword`, `status`, `sort_by`, `sort_dir`, `per_page`, `page`.

### Create Category

`POST /api/v1/categories`

Auth: admin

```json
{
  "name": "Electronics",
  "description": "Phones, laptops, chargers, and accessories.",
  "status": "active"
}
```

### Update Category

`PUT /api/v1/categories/{id}`

Auth: admin

```json
{
  "status": "inactive"
}
```

### Delete Category

`DELETE /api/v1/categories/{id}`

Auth: admin

Soft deletes the category. Reports keep a nullable category reference.
