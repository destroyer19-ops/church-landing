<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$content = app_site_content();
$live = $content['live_stream'] ?? [];
$url = $live['embed_url'] ?? '';

if ($url === '') {
    app_json_response(['message' => 'Live stream URL is not configured.'], 404);
}

app_json_response(['url' => $url]);
