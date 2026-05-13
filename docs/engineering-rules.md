# Engineering Rules

These rules define the engineering discipline for the Lost & Found Campus Platform. The project must remain realistic, maintainable, and aligned with the selected architecture.

## 1. Branch Strategy

- `main` contains stable, reviewed code only.
- `develop` contains integrated work prepared for the next stable milestone.
- `feature/<short-name>` is used for planned feature work.
- `fix/<short-name>` is used for bug fixes.
- `docs/<short-name>` is used for documentation-only changes.
- Branches must have a clear scope and should not mix unrelated work.

## 2. Commit Convention

Use Conventional Commit style:

```text
type(scope): short description
```

Accepted types:

- `feat`: new feature
- `fix`: bug fix
- `docs`: documentation update
- `refactor`: code restructuring without behavior change
- `test`: test addition or update
- `chore`: maintenance task
- `style`: formatting-only change

Examples:

```text
docs(api): add reports contract foundation
chore(backend): initialize laravel project
```

## 3. API Response Standard

All API responses must use a consistent JSON shape.

Successful response:

```json
{
  "success": true,
  "message": "Request completed successfully.",
  "data": {},
  "errors": null,
  "meta": {}
}
```

Error response:

```json
{
  "success": false,
  "message": "Request failed.",
  "data": null,
  "errors": {
    "field": [
      "Validation message."
    ]
  },
  "meta": {}
}
```

Controllers must not return inconsistent ad hoc response shapes.

## 4. Naming Convention

- Backend classes use PascalCase.
- Backend methods and variables use camelCase.
- Database tables use snake_case plural names.
- Database columns use snake_case.
- API routes use kebab-case or clear REST resource names.
- Flutter files and folders use snake_case.
- Flutter classes use PascalCase.
- Documentation files use kebab-case.

## 5. Folder Structure Policy

The repository is organized by platform and engineering concern:

```text
backend/
mobile/
docs/
api-contract/
ui-design/
```

Backend code must follow layered responsibilities:

- Controllers handle HTTP input and output.
- Services hold business workflow logic.
- Repositories handle data access boundaries.
- Models represent database-backed entities.
- Policies handle authorization decisions.
- Notifications handle notification delivery concerns.

Flutter code must keep concerns separated:

- `models/` for data models
- `services/` for API and platform service integration
- `providers/` for state management
- `screens/` for screen-level UI
- `widgets/` for reusable UI components
- `utils/` for shared helpers

## 6. Engineering Discipline Rules

- Do not implement business features without a defined phase scope.
- Do not introduce microservices.
- Do not introduce websocket realtime behavior.
- Do not add AI features.
- Do not bypass the REST API contract.
- Do not create giant monolithic files.
- Prefer clear, boring, maintainable code over clever abstractions.
- Keep changes small enough to review.

## 7. Code Review Rules

Code review must check:

- Scope matches the assigned phase.
- API contracts and implementation are consistent.
- Authentication and authorization boundaries are respected.
- Validation is present where input is accepted.
- Naming and folder placement follow project conventions.
- No unrelated refactors are included.
- Tests or verification notes are included when appropriate.

## 8. Scope Control Rules

- Phase 0 is foundation only.
- Feature implementation begins only after contracts and roadmap are agreed.
- A task must not expand into unrelated modules.
- Platform-specific features must stay within their platform boundaries.
- New dependencies require a clear reason and must fit the selected stack.

## 9. API-First Workflow

The REST API contract is the coordination point between backend, web, and mobile.

Required workflow:

1. Define endpoint behavior in `api-contract/`.
2. Agree on request and response structures.
3. Implement backend route, validation, service, repository, and model changes.
4. Connect web and mobile clients to the documented API.
5. Keep API changes backward-conscious and documented.

## 10. Stability-First Development Principle

The system should favor stable foundations over premature complexity.

- Use Laravel and Flutter standard practices unless the project has a clear reason not to.
- Centralize shared response formatting.
- Keep authentication consistent through Sanctum.
- Keep database access predictable through repositories where useful.
- Prefer explicit validation and authorization over implicit behavior.
- Avoid speculative abstractions until repeated patterns prove they are needed.

