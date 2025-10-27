<?php

declare(strict_types=1);

$uploadsDir = __DIR__ . '/storage/uploads/';

if (!isset($_GET['file'])) {
    http_response_code(400);
    echo 'Missing file parameter.';
    exit;
}

$filename = basename((string) $_GET['file']);
$filePath = $uploadsDir . $filename;

if (!is_file($filePath)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

$mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . (string) filesize($filePath));
header('Cache-Control: public, max-age=31536000, immutable');

readfile($filePath);
