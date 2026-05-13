# Phase 2 Testing and Validation

## Migration and Setup Commands

Run from `backend/`.

```bash
composer install
php artisan migrate:fresh --seed
php artisan storage:link
php artisan route:list --path=api/v1
php artisan test
```

Seeded accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password123` |
| User | `test@example.com` | `password123` |

Seeded categories:

- Electronics
- Documents
- Bags
- Keys
- Clothing

## API Testing Flow

1. Register or log in a user with `/api/v1/auth/login`.
2. Log in as admin with `admin@example.com`.
3. List categories with `GET /api/v1/categories`.
4. Create a report as a user with `POST /api/v1/reports`.
5. Approve or reject the report as admin.
6. Confirm the report owner receives a notification.
7. Create a second user and submit a claim against the approved report.
8. Approve or reject the claim as admin.
9. Confirm claim approval changes the report status to `claimed`.
10. Confirm the claimant receives a notification.
11. Mark the notification as read.
12. Test filters on reports and claims.

## Postman Collection Structure

Recommended variables:

| Variable | Example |
| --- | --- |
| `base_url` | `http://localhost:8000/api/v1` |
| `user_token` | Bearer token from normal login |
| `admin_token` | Bearer token from admin login |
| `report_id` | Created report id |
| `claim_id` | Created claim id |
| `notification_id` | Created notification id |

Folders:

- Auth
  - Register
  - Login User
  - Login Admin
  - Me
  - Logout
- Categories
  - List Categories
  - Admin Create Category
  - Admin Update Category
  - Admin Delete Category
- Reports
  - List Reports
  - Create Report
  - Show Report
  - Update Report
  - Delete Report
  - Admin Approve Report
  - Admin Reject Report
- Claims
  - Create Claim
  - List Claims
  - Show Claim
  - Admin Approve Claim
  - Admin Reject Claim
- Notifications
  - List Notifications
  - Mark Notification Read

## Validation Checklist

- Authenticated endpoints reject missing tokens with `UNAUTHENTICATED`.
- Non-admin category mutations return `FORBIDDEN`.
- Non-owners cannot update or delete another user's report.
- Users cannot claim their own report.
- Claims require at least 20 characters of ownership proof.
- Pending or rejected reports cannot be claimed.
- Report image validation accepts `jpg`, `jpeg`, `png`, and `webp` up to 4 MB.
- Report image replacement deletes the previous stored file.
- Report deletion deletes the stored image file.
- Admin report approval creates an unread notification for the owner.
- Admin report rejection creates an unread notification for the owner.
- Admin claim approval creates an unread notification for the claimant.
- Admin claim rejection creates an unread notification for the claimant.
- Users cannot mark another user's notification as read.
- Report filters work for keyword, category, type, status, and pagination.
