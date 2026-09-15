# Worqsy V1 Architecture Notes

## Architectural style

Worqsy V1 is implemented as a **modular monolith**.

The important boundary is domain responsibility, not process separation.

Main application domains:

- Authentication
- Workspace
- Organization
- Project
- Membership
- Permission
- Lifecycle
- Task
- Review
- Revision
- Progress
- Communication
- Files
- Notifications
- Audit
- Reporting

## Why UUIDs

Human project codes are intentionally separated from internal identifiers.

Example:

```text
Project Code: WEB201
Internal ID: 550e8400-e29b-41d4-a716-446655440000
```

A project code is used for human recognition. It must never be treated as an access token or password.

## Why memberships are separate

Workspace membership answers:

> What is this person in the organization?

Project membership answers:

> What is this person in this project?

Phase access answers:

> Is this project member active in the current lifecycle cycle?

These concerns must remain separate.

## Why phase cycles are records

Development can occur more than once:

```text
CREATE #1
MAINTENANCE #1
DEVELOPMENT #1
MAINTENANCE #2
DEVELOPMENT #2
```

Therefore project lifecycle cannot be represented only by a single mutable string if history and access control matter.

## Progress

Default formula:

```text
sum(weight of APPROVED tasks)
-------------------------------- x 100
sum(weight of all included tasks)
```

`REVIEWING`, `REVISION`, and other intermediate states are not completed work.

## Authorization

UI visibility is not security.

Server-side checks are centralized through:

- Policies
- PermissionService
- workspace middleware
- task responsibility checks
- project phase access checks

## Development login

Local demo login is intentionally isolated behind:

```env
WORQSY_ALLOW_DEMO_LOGIN
```

It exists only to make XAMPP/local development practical. Google OAuth remains the primary target authentication model.
