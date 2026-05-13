# Web Admin Platform

Phase: 3 - Web Administration Platform

The web platform is a Laravel Blade administration and moderation surface built inside the existing Laravel backend application. It uses the shared authentication system, shared models, shared policies, and shared services created during the backend phases.

## Architecture

The web platform follows the existing layered backend architecture.

Request flow:

```text
Blade route
-> Web controller
-> Form request validation
-> Policy / middleware authorization
-> Service
-> Repository / Model
-> Blade response
```

Key directories:

| Area | Path |
| --- | --- |
| Web routes | `backend/routes/web.php` |
| Web controllers | `backend/app/Http/Controllers/Web` |
| Web form requests | `backend/app/Http/Requests/Web/Admin` |
| Layouts | `backend/resources/views/layouts` |
| Reusable UI components | `backend/resources/views/components` |
| Admin pages | `backend/resources/views/admin` |
| PWA files | `backend/public/manifest.json`, `backend/public/service-worker.js`, `backend/public/offline.html` |

The web platform does not duplicate report, claim, notification, image upload, or category business logic. It calls the same services used by the API layer.

## UI Structure

Primary layout:

- Fixed desktop sidebar for administration sections.
- Mobile and tablet drawer sidebar for compatibility.
- Sticky top navigation with notification dropdown.
- Page heading and action slot.
- Session success, status, and validation error states.
- Responsive content area with cards, tables, forms, and empty states.

Reusable Blade components:

- `x-ui.button`
- `x-ui.badge`
- `x-ui.card`
- `x-ui.alert`
- `x-ui.empty-state`
- `x-admin.sidebar`
- `x-admin.topbar`

Design system:

- Neutral page background and white working surfaces.
- Emerald for primary actions.
- Amber for pending states.
- Rose for destructive or rejected states.
- Sky, teal, violet, and gray for secondary status distinction.
- 8px card radius through `rounded-lg`.
- Consistent form controls through `.admin-control`.
- Consistent focus treatment through `.admin-focus`.
- Tables use compact spacing and horizontal overflow for smaller screens.

## Authentication

Routes:

| Method | Route | Purpose |
| --- | --- | --- |
| `GET` | `/admin/login` | Admin login page |
| `POST` | `/admin/login` | Admin session login |
| `POST` | `/admin/logout` | Admin logout |
| `GET` | `/admin` | Dashboard |

Only users with `role=admin` can access `/admin/*`. Non-admin users cannot sign in to the admin platform.

The shared role middleware supports both:

- JSON API responses for `/api/*`
- Browser redirects or `403` pages for web routes

## Dashboard

Dashboard cards show:

- Total reports
- Pending reports
- Pending claims
- Approved reports
- Category count
- Registered user count
- Report status distribution

Dashboard activity sections show:

- Recent reports
- Recent claims
- Unread notifications

The dashboard is designed for moderation triage: pending work is visible immediately, with direct links into report and claim detail pages.

## Moderation Workflow

Report moderation:

1. Admin opens `Report Management`.
2. Admin filters by keyword, category, type, status, or moderation status.
3. Admin opens a report detail page.
4. Admin approves, rejects, or edits report metadata/status.
5. Report owner receives a notification when approval or rejection occurs.

Claim moderation:

1. Admin opens `Claim Management`.
2. Admin reviews ownership proof.
3. Admin approves or rejects the claim.
4. Approved claims mark the report as `claimed`.
5. Competing pending claims are rejected by the backend service.
6. Claimants receive notifications.

Category management:

1. Admin creates, edits, activates, deactivates, or deletes categories.
2. Categories remain shared across API clients and the web platform.

Notification workflow:

1. Admin opens the dropdown or notification list.
2. Admin reviews unread/read messages.
3. Admin marks unread notifications as read.

## Web-Specific Features

### Drag and Drop Upload

Implemented on report detail edit forms.

Capabilities:

- Drag image onto the upload zone.
- Click to browse.
- Preview selected image before submit.
- Validate type in browser: JPG, PNG, WEBP.
- Validate max size in browser: 4 MB.
- Show inline file errors.
- Server-side validation remains authoritative.
- Existing image cleanup remains handled by `ImageStorageService`.

### PWA Foundation

Implemented files:

- `public/manifest.json`
- `public/service-worker.js`
- `public/offline.html`
- `public/pwa/icon.svg`

Behavior:

- App can be installed by browsers that support manifest-based installation.
- Service worker caches the offline fallback and icon.
- Navigation requests show `offline.html` when the network is unavailable.
- The offline layer is intentionally small. Moderation still requires live backend access.

## Responsive Strategy

The platform is admin-first, not mobile-first.

Desktop:

- Persistent sidebar.
- Sticky topbar.
- Dense tables.
- Multi-column dashboard and detail layouts.

Tablet:

- Drawer sidebar.
- Tables keep horizontal overflow.
- Cards collapse to fewer columns.

Mobile browser:

- Drawer navigation.
- Forms stack vertically.
- Tables remain horizontally scrollable to preserve admin data density.

## Commands

Run from `backend/`.

```bash
composer install
npm install
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan test
php artisan serve
```

Open:

```text
http://127.0.0.1:8000/admin/login
```

Seeded admin account:

```text
admin@example.com
password123
```

## Testing Checklist

- Admin can log in.
- Non-admin users cannot log in to `/admin`.
- Admin can log out.
- Dashboard loads stats and recent activity.
- Report filters work by keyword, category, type, and status.
- Report detail loads report metadata, image, and claims.
- Admin can approve reports.
- Admin can reject reports.
- Admin can change report status from the report detail form.
- Drag and drop upload previews valid images.
- Drag and drop upload rejects invalid image types.
- Drag and drop upload rejects files above 4 MB.
- Server stores uploaded images in `storage/app/public/reports`.
- Server removes replaced or deleted report images.
- Claim list filters by status, report id, and claimant id.
- Admin can approve claims.
- Admin can reject claims.
- Category create, edit, and delete flows work.
- Notification dropdown shows recent notifications.
- Notification list filters unread/read status.
- Admin can mark notifications as read.
- `/manifest.json`, `/service-worker.js`, and `/offline.html` are present.
- Browser install prompt is available where supported.
- Offline navigation shows the fallback page.
- Pages remain usable at desktop, tablet, and mobile browser widths.

## Debugging Checklist

- If Blade assets fail, run `npm install` and `npm run build`.
- If uploaded images do not display, run `php artisan storage:link`.
- If admin login redirects unexpectedly, confirm the user role is `admin`.
- If moderation actions fail, confirm policies and role middleware are active.
- If validation errors appear, inspect the relevant form request in `app/Http/Requests/Web/Admin`.
- If PWA changes do not appear, unregister the old service worker in browser devtools and reload.
- If pagination links lose filters, verify controllers pass paginated query strings from the repository layer.
- If API and web status behavior diverges, update the shared service rather than controller-specific logic.
