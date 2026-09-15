# Worqsy V1

Worqsy is a project and work management platform built around a simple principle:

> **Simple by default. Powerful when needed.**

This repository is a Laravel + MySQL implementation of the Worqsy V1 product and system architecture baseline.

## Technology

- Laravel 12
- PHP 8.2+
- MySQL 8 / MariaDB compatible
- Blade
- Vanilla CSS + JavaScript (no Node.js build step required for the V1 UI)
- Laravel Socialite for Google OAuth
- Laravel Policies + custom Worqsy permission service
- Eloquent ORM
- Laravel Notifications (database channel)
- PHPUnit

The application intentionally uses a modular monolith. It does not require microservices, Redis, or Node.js to run locally.

---

## 1. Requirements

For Windows/XAMPP:

- XAMPP with PHP **8.2 or newer**
- MySQL / MariaDB running
- Composer 2.x
- PHP extensions normally included with XAMPP:
  - PDO
  - PDO MySQL
  - OpenSSL
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - Fileinfo

Check:

```bash
php -v
composer --version
```

If `php` points to another PHP installation, run Composer/Artisan with XAMPP PHP explicitly, for example:

```bash
C:\xampp\php\php.exe artisan --version
```

---

## 2. Put the Project in XAMPP

Extract this project to:

```text
C:\xampp\htdocs\worqsy
```

Open terminal:

```bash
cd C:\xampp\htdocs\worqsy
```

---

## 3. Install PHP Dependencies

```bash
composer install
```

No `node_modules` or `npm install` is required for this build because the frontend assets are already in `public/css` and `public/js`.

---

## 4. Create the MySQL Database in phpMyAdmin

Start **Apache** and **MySQL** in XAMPP.

Open:

```text
http://localhost/phpmyadmin
```

Create a database:

```text
worqsy
```

Recommended collation:

```text
utf8mb4_unicode_ci
```

Do **not** create tables manually. Laravel migrations create the complete schema.

---

## 5. Configure `.env`

Copy:

```bash
copy .env.example .env
```

or:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Default XAMPP database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=worqsy
DB_USERNAME=root
DB_PASSWORD=
```

If your MySQL root account has a password, set it in `DB_PASSWORD`.

---

## 6. Create Tables and Demo Data

```bash
php artisan migrate --seed
```

This creates the Worqsy domain model including:

- users
- workspaces
- workspace_memberships
- workspace_admin_permissions
- projects
- project_positions
- project_memberships
- project_phase_cycles
- project_phase_access
- tasks
- subtasks
- task_assignees
- task_reviewers
- task_approvers
- task_submissions
- task_revisions
- revision_items
- task_comments
- project_discussions
- direct_conversations
- direct_conversation_members
- direct_messages
- files
- file_links
- notifications
- audit_events
- access_records
- invitations
- project_reports
- project_health_snapshots

Create the public storage symlink:

```bash
php artisan storage:link
```

---

## 7. Run Worqsy

### Recommended local method

Use XAMPP only for MySQL, and use Laravel's development server:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

This is the simplest setup.

### Apache/XAMPP method

If using Apache directly, the web root **must point to the `public` directory**, not the project root.

Example VirtualHost:

```apache
<VirtualHost *:80>
    ServerName worqsy.local
    DocumentRoot "C:/xampp/htdocs/worqsy/public"

    <Directory "C:/xampp/htdocs/worqsy/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to Windows hosts:

```text
127.0.0.1 worqsy.local
```

Then set:

```env
APP_URL=http://worqsy.local
```

---

## 8. Demo Login

Google authentication is the primary Worqsy authentication model, but this repository includes a local-development-only login so the system can be tested immediately.

Default:

```env
WORQSY_ALLOW_DEMO_LOGIN=true
WORQSY_DEMO_PASSWORD=password
```

Demo accounts:

| Role | Email | Password |
|---|---|---|
| Owner | budi@worqsy.local | password |
| Admin / CTO | raka@worqsy.local | password |
| Project Manager | abdul@worqsy.local | password |
| Lead | arif@worqsy.local | password |
| Member | lukman@worqsy.local | password |
| Member / Read Only example | dela@worqsy.local | password |
| Guest / Client | alif@client.local | password |

For production:

```env
WORQSY_ALLOW_DEMO_LOGIN=false
```

---

## 9. Google OAuth

Create Google OAuth credentials and configure:

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Example local redirect URI:

```text
http://127.0.0.1:8000/auth/google/callback
```

The Google email is matched to the Worqsy user account and pending invitations.

---

## 10. Core Product Rules Implemented

### One account, multiple projects

A user has one global account and separate project memberships/identities.

### Role vs position

Role controls authorization.

Position describes the person's real project function.

### Project lifecycle

```text
CREATE
  ↓
MAINTENANCE
  ↕
DEVELOPMENT
  ↓
CLOSED
```

Each transition creates a separate phase cycle. Historical memberships are not deleted.

### Task workflow

```text
ASSIGNED
  ↓
IN_PROGRESS
  ↓
DONE_SUBMITTED / REVIEWING
  ↓
APPROVED
or
REVISION
  ↓
RESUBMITTED / REVIEWING
```

Only `APPROVED` task weight counts toward project completion.

### Structured revision

Revision is a first-class workflow with:

- revision number
- overall deadline
- items
- priority
- urgency
- item deadline
- completion status
- resubmission

### Permission model

Worqsy authorization combines:

- user identity
- workspace membership
- organization role
- admin permissions
- project membership
- project role
- permission mode
- phase access
- task responsibility

Protected actions are checked server-side.

### Guest isolation

Guest:

- receives explicit project access
- can use permitted project discussion/task comments
- cannot use internal direct messages
- does not receive organization-management navigation
- does not automatically receive file-download permission

### Audit

Critical domain actions create immutable-style audit entries from the normal UI perspective.

---

## 11. Source Structure

```text
app/
├── Actions/          # Domain commands / use cases
├── Enums/            # Stable Worqsy domain states
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Notifications/
├── Policies/
├── Providers/
└── Services/

database/
├── factories/
├── migrations/
└── seeders/

resources/views/
├── auth/
├── audit/
├── calendar/
├── components/
├── dashboard/
├── layouts/
├── messages/
├── notifications/
├── people/
├── projects/
├── reports/
├── revisions/
├── reviews/
└── tasks/

public/
├── css/
└── js/
```

---

## 12. Reset Database

During development:

```bash
php artisan migrate:fresh --seed
```

This deletes all current data and recreates the demo dataset.

---

## 13. Tests

```bash
php artisan test
```

Tests use SQLite in-memory by default and do not modify the MySQL `worqsy` database.

---

## 14. Production Checklist

Before deploying:

1. Set `APP_ENV=production`.
2. Set `APP_DEBUG=false`.
3. Disable demo login.
4. Use HTTPS.
5. Configure valid Google OAuth credentials.
6. Configure a production MySQL user; do not use root.
7. Configure backup strategy.
8. Configure mail provider.
9. Set correct filesystem/object storage.
10. Run:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
11. Use a proper web server with document root pointing to `/public`.
12. Review file permissions for `storage` and `bootstrap/cache`.

---

## 15. Important V1 Notes

This codebase is deliberately structured so future realtime communication, Redis queues, object storage, mobile API clients, advanced automation, AI assistance, and portfolio features can be added without rewriting the core Worqsy domain model.

The project code (for example `WEB201`) is **not** a credential. Internal UUIDs, authentication, membership, phase access, and server-side authorization protect resources.
