<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$content = app_site_content();
$home = $content['home'] ?? [];

app_json_response([
    'hero_title' => $home['hero_title'] ?? 'Welcome to Christ Embassy Barking',
    'hero_subtitle' => $home['hero_subtitle'] ?? 'Giving Your Life a Meaning',
    'hero_background_image' => $home['hero_background_image'] ?? 'images/1B7A1007.jpg',
]);
