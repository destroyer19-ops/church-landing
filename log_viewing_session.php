<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    app_redirect('watch.php');
}

$viewerId = filter_input(INPUT_POST, 'viewer_id', FILTER_VALIDATE_INT);
$numberOfViewers = filter_input(INPUT_POST, 'number_of_viewers', FILTER_VALIDATE_INT);

if (!$viewerId || !$numberOfViewers || $numberOfViewers < 1) {
    app_redirect('watch.php');
}

try {
    $connection = app_db();
    app_ensure_analytics_tables($connection);

    $session = $connection->prepare('INSERT INTO viewing_sessions (viewer_id, number_of_viewers) VALUES (?, ?)');
    $session->bind_param('ii', $viewerId, $numberOfViewers);
    $session->execute();
    $session->close();
    $connection->close();

    app_redirect('live.html');
} catch (Throwable $exception) {
    http_response_code(500);
    echo 'We could not log your session right now. Please try again later.';
}
