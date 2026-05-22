<?php

declare(strict_types=1);

const APP_ROOT = __DIR__ . '/..';

app_load_env(APP_ROOT . '/.env');

function app_load_env(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        $value = trim($value, "\"'");

        if (getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

function app_env(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (string) $value;
}

function app_json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

function app_redirect(string $location): never
{
    header('Location: ' . $location);
    exit();
}

function app_read_json_file(string $relativePath, array $fallback = []): array
{
    $path = APP_ROOT . '/' . ltrim($relativePath, '/');

    if (!is_file($path) || !is_readable($path)) {
        return $fallback;
    }

    $contents = file_get_contents($path);

    if ($contents === false) {
        return $fallback;
    }

    $decoded = json_decode($contents, true);

    return is_array($decoded) ? $decoded : $fallback;
}

function app_db(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $host = app_env('DB_HOST');
    $port = (int) (app_env('DB_PORT', '3306') ?? '3306');
    $name = app_env('DB_NAME');
    $user = app_env('DB_USER');
    $password = app_env('DB_PASSWORD');

    if (!$host || !$name || !$user) {
        throw new RuntimeException('Database configuration is incomplete.');
    }

    $connection = new mysqli($host, $user, $password ?? '', $name, $port);
    $connection->set_charset('utf8mb4');

    return $connection;
}

function app_ensure_analytics_tables(mysqli $connection): void
{
    $connection->query(
        'CREATE TABLE IF NOT EXISTS viewers (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            country VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_viewer_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $connection->query(
        'CREATE TABLE IF NOT EXISTS viewing_sessions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            viewer_id INT UNSIGNED NOT NULL,
            number_of_viewers INT UNSIGNED NOT NULL DEFAULT 1,
            viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_viewing_sessions_viewer
                FOREIGN KEY (viewer_id) REFERENCES viewers(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
}

function app_admin_auth_enabled(): bool
{
    return app_env('ADMIN_USERNAME') !== null && app_env('ADMIN_PASSWORD') !== null;
}

function app_require_admin_auth(): void
{
    if (!app_admin_auth_enabled()) {
        http_response_code(503);
        echo 'Admin access is disabled until ADMIN_USERNAME and ADMIN_PASSWORD are configured.';
        exit();
    }

    $providedUser = $_SERVER['PHP_AUTH_USER'] ?? null;
    $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? null;
    $expectedUser = app_env('ADMIN_USERNAME');
    $expectedPassword = app_env('ADMIN_PASSWORD');

    if ($providedUser === $expectedUser && hash_equals((string) $expectedPassword, (string) $providedPassword)) {
        return;
    }

    header('WWW-Authenticate: Basic realm="CE Barking Admin"');
    http_response_code(401);
    echo 'Authentication required.';
    exit();
}

function app_site_content(): array
{
    return app_read_json_file('data/site-content.json', []);
}

function app_events(): array
{
    $events = app_read_json_file('data/events.json', []);

    usort(
        $events,
        static function (array $left, array $right): int {
            return strcmp((string) ($left['date'] ?? ''), (string) ($right['date'] ?? ''));
        }
    );

    return array_values(
        array_filter(
            $events,
            static function (array $event): bool {
                return !empty($event['title']) && !empty($event['description']);
            }
        )
    );
}
