# Lost & Found Campus Platform

Lost & Found Campus Platform is a production-oriented academic multiplatform engineering project for managing lost and found item reports in a campus environment. The system is designed around a shared Laravel REST API, a Laravel Blade web administration platform, a Flutter mobile application, and one centralized MySQL database.

This repository is initialized for disciplined engineering work. Phase 0 focuses only on foundations: repository structure, documentation, API contract preparation, backend initialization, and mobile initialization. Business features are intentionally not implemented in this phase.

## Architecture Overview

```text
Client Platforms
  - Web Administration Platform
  - Flutter Mobile Application

        |
        v

REST API
  - Laravel 12
  - Laravel Sanctum Authentication

        |
        v

Application Backend
  - Services
  - Repositories
  - Models
  - Policies
  - Notifications

        |
        v

MySQL Database
```

The backend is the single source of truth. Web and mobile clients must communicate through the documented REST API and must not bypass backend validation, authorization, or data access rules.

## Selected Platforms

- Web: Laravel Blade with Tailwind CSS
- Mobile: Flutter
- Backend API: Laravel 12 REST API
- Authentication: Laravel Sanctum
- Database: MySQL

## Tech Stack

- PHP and Laravel 12
- Laravel Sanctum
- MySQL
- Blade templates
- Tailwind CSS
- Flutter and Dart
- Git
- Markdown-based API contracts

## Platform-Specific Features

Mobile features planned for later phases:

- Camera integration
- GPS location

Web features planned for later phases:

- Drag and drop upload
- Progressive Web App support

## Repository Structure

```text
lost-found-campus/
  backend/       Laravel backend, REST API, and web administration platform
  mobile/        Flutter mobile application
  docs/          Engineering rules, roadmap, and project documentation
  api-contract/  REST API contract foundation
  ui-design/     UI references and design planning assets
  README.md      Project overview
```

## Engineering Workflow

Development must follow an API-first, stability-first workflow:

1. Define or update API contracts before implementing client or backend behavior.
2. Keep backend logic layered through controllers, services, repositories, models, policies, and notifications.
3. Keep platform-specific behavior inside the relevant platform folder.
4. Avoid feature work without a documented scope.
5. Review changes for maintainability, consistency, and API compatibility before merging.

## Setup Placeholder

Detailed setup instructions will be completed in later phases after the backend and mobile foundations are finalized.

Expected setup areas:

- Backend environment configuration
- MySQL database configuration
- Laravel Sanctum configuration
- Web asset build instructions
- Flutter dependency installation
- Local development commands
- Testing commands

