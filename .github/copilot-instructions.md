<!-- Copilot instructions for working with this Laravel app -->
# Repo snapshot

This repository is a Laravel 12 application (PHP ^8.2) with a Vite/Tailwind frontend. Back-end code lives in `app/` (Eloquent models in `app/Models`), routes in `routes/`, DB migrations/seeders in `database/migrations` and `database/seeders`, and frontend assets under `resources/`.

# What matters for an AI coding agent (concise)

- Architecture: standard Laravel MVC with API authentication via Sanctum. Models use Eloquent relationships (see `app/Models/User.php` and `app/Models/Apartment.php`).
- Dev scripts: use Composer scripts for common flows—see `composer.json` `scripts.setup`, `scripts.dev`, `scripts.test`.
- Frontend: built with Vite. Use `npm run dev` for local hot-reload and `npm run build` to produce assets.
- Tests: Pest + PHPUnit. CI/test config uses in-memory SQLite (`phpunit.xml`) so unit/feature tests run isolated without an external DB.

# Quick actionable commands

- Setup fresh dev environment (one-line):

  composer run-script setup

- Start full dev environment (server, queues, logs, vite):

  composer run-script dev

- Run tests:

  composer run-script test

- Frontend only:

  npm run dev

# Project-specific patterns & gotchas

- Table naming: `Apartment` sets `protected $table = 'Apartments'` (capital A) — watch for nonstandard table names when generating migrations or queries (`app/Models/Apartment.php`).
- Image paths: `User` has `getIdPhotoUrlAttribute()` and `getPersonalPhotoUrlAttribute()` that return `asset('storage/...')`; use `storage` disk and `php artisan storage:link` when testing file uploads.
- Tests rely on `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` in `phpunit.xml`; mock external services or check env overrides when proposing integration tests.
- Auth: API token support via `laravel/sanctum` (look for Sanctum middleware or tokens on `User`).
- Queues/logging: `composer dev` starts `queue:listen` and `php artisan pail` — be conservative when modifying queue jobs; verify `QUEUE_CONNECTION` in tests/config.

# Integration points to inspect before making changes

- `composer.json` — dependency list, `scripts` and PHP requirement. See [composer.json](composer.json#L1).
- `package.json` & `vite.config.js` — frontend build/dev. See [package.json](package.json#L1).
- `phpunit.xml` — test DB env and flags. See [phpunit.xml](phpunit.xml#L1).
- `routes/`, `app/Http/Controllers/` — where endpoints are declared.
- `database/migrations/` and `database/seeders/` — schema and seed data used in dev.

# Example code patterns to follow

- Eloquent relations: `User` -> `apartments()` (hasMany) and `Apartment` -> `owner()` (belongsTo). Use these accessors for eager-loading (`with('owner')`) where appropriate.
- Fillable lists: models define `$fillable` (see `app/Models/Apartment.php` and `app/Models/User.php`) — prefer mass assignment through validated request data.

# When to ask the human

- Any change that affects database schema (migrations) or table names — confirm intended table naming and migration ordering.
- When adding external services (mail, broadcast, payments) — request credentials and expected behavior.

Please review and tell me if you want more examples (controller snippets, common request/response shapes, or CI run commands). I can iterate on this file.
