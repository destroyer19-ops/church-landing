# CE Barking Railway Deployment

This app now runs as plain PHP + JavaScript. WordPress is no longer required by the public site.

## Runtime requirements

- PHP 8.2+
- MySQL-compatible database for viewer registrations and session analytics

## Environment variables

Copy `.env.example` to `.env` for local development, or set these in Railway:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `ADMIN_USERNAME`
- `ADMIN_PASSWORD`

## Railway

This repository includes a `Dockerfile`, so Railway can deploy it directly.

- The service starts with `php -S 0.0.0.0:$PORT -t /app`
- Healthcheck endpoint: `/api/health.php`

## Data files

- `data/site-content.json`: home hero content and live stream embed URL
- `data/events.json`: events listing

## Database

The app auto-creates the `viewers` and `viewing_sessions` tables on first use.
The same schema is also provided in `database/schema.sql`.
