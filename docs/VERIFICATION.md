# Build Verification

This source package was generated and statically checked before packaging.

## Checks performed

- PHP syntax lint (`php -l`) across application, config, migrations, routes, seeders, and tests.
- Blade route-name reference check against named routes.
- Blade control-directive balance check for `@if`, `@foreach`, `@forelse`, `@can`, `@unless`, and block `@section`.
- App namespace `use` references checked for matching source files.
- Manual checks for UUID-backed resource identifiers, reviewer/approver pivot keys, phase-access table naming, and UUID notification morphs.

## Runtime verification limitation

The generation environment did not contain Composer/vendor dependencies, so Laravel framework boot, migrations against an actual MySQL server, and browser-level integration tests were not executed here.

After extraction, run:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan test
php artisan serve
```

Any environment-specific issue should be diagnosed using `storage/logs/laravel.log`.
