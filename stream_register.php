<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    app_redirect('stream-register.html');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$country = trim((string) ($_POST['country'] ?? ''));

if ($name === '' || $country === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    app_redirect('stream-register.html');
}

try {
    $connection = app_db();
    app_ensure_analytics_tables($connection);

    $viewerId = null;

    $select = $connection->prepare('SELECT id FROM viewers WHERE email = ? LIMIT 1');
    $select->bind_param('s', $email);
    $select->execute();
    $result = $select->get_result();

    if ($result->num_rows > 0) {
        $viewer = $result->fetch_assoc();
        $viewerId = (int) $viewer['id'];

        $update = $connection->prepare('UPDATE viewers SET name = ?, phone = ?, country = ? WHERE id = ?');
        $update->bind_param('sssi', $name, $phone, $country, $viewerId);
        $update->execute();
        $update->close();
    } else {
        $insert = $connection->prepare('INSERT INTO viewers (name, email, phone, country) VALUES (?, ?, ?, ?)');
        $insert->bind_param('ssss', $name, $email, $phone, $country);
        $insert->execute();
        $viewerId = $connection->insert_id;
        $insert->close();
    }

    $select->close();

    $session = $connection->prepare('INSERT INTO viewing_sessions (viewer_id, number_of_viewers) VALUES (?, ?)');
    $numberOfViewers = 1;
    $session->bind_param('ii', $viewerId, $numberOfViewers);
    $session->execute();
    $session->close();

    $connection->close();

    setcookie(
        'viewer_id',
        (string) $viewerId,
        [
            'expires' => time() + (365 * 24 * 60 * 60),
            'path' => '/',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => false,
            'samesite' => 'Lax',
        ]
    );

    app_redirect('live.html');
} catch (Throwable $exception) {
    error_log('stream_register.php failed: ' . $exception->getMessage());
    http_response_code(500);

    if ($exception instanceof RuntimeException && str_contains($exception->getMessage(), 'Database configuration is incomplete')) {
        echo 'Registration is unavailable because the database is not configured yet. Create a .env file or set DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASSWORD.';
        exit();
    }

    echo 'We could not complete your registration right now. Please try again later.';
}
