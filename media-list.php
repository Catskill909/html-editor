<?php

declare(strict_types=1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Use GET to fetch media list.'
    ]);
    exit;
}

$uploadsDir = __DIR__ . '/storage/uploads/';

if (!is_dir($uploadsDir)) {
    echo json_encode([
        'status' => 'success',
        'media' => []
    ]);
    exit;
}

$iterator = new DirectoryIterator($uploadsDir);
$media = [];

foreach ($iterator as $fileInfo) {
    if ($fileInfo->isDot() || !$fileInfo->isFile()) {
        continue;
    }
    $filename = $fileInfo->getFilename();
    $filepath = $fileInfo->getPathname();
    $mimeType = mime_content_type($filepath) ?: 'application/octet-stream';

    if (strpos($mimeType, 'image/') !== 0) {
        continue;
    }

    $media[] = [
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'filename' => $filename,
        'url' => 'media.php?file=' . rawurlencode($filename),
        'size' => $fileInfo->getSize(),
        'mime' => $mimeType,
        'uploadedAt' => gmdate('c', $fileInfo->getMTime())
    ];
}

echo json_encode([
    'status' => 'success',
    'media' => $media
]);
