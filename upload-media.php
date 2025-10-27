<?php

declare(strict_types=1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Use POST to upload media.'
    ]);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'No file payload received.'
    ]);
    exit;
}

$file = $_FILES['file'];

if (!is_uploaded_file($file['tmp_name'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Upload failed. Temporary file missing.'
    ]);
    exit;
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => uploadErrorMessage($file['error'])
    ]);
    exit;
}

$maxSize = 5 * 1024 * 1024; // 5 MB
if ($file['size'] > $maxSize) {
    http_response_code(413);
    echo json_encode([
        'status' => 'error',
        'message' => 'File exceeds 5MB limit.'
    ]);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']) ?: '';

$allowedTypes = [
    'image/jpeg' => '.jpg',
    'image/png' => '.png',
    'image/gif' => '.gif',
    'image/webp' => '.webp',
    'image/svg+xml' => '.svg'
];

if (!array_key_exists($mimeType, $allowedTypes)) {
    http_response_code(415);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unsupported image format.'
    ]);
    exit;
}

$uploadsDir = __DIR__ . '/storage/uploads/';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to create uploads directory.'
    ]);
    exit;
}

$originalName = pathinfo($file['name'], PATHINFO_FILENAME);
$normalizedBase = preg_replace('/[^a-z0-9-_]+/i', '-', $originalName);
$normalizedBase = trim(preg_replace('/-+/', '-', strtolower($normalizedBase)), '-');
if ($normalizedBase === '') {
    $normalizedBase = 'media';
}

$extension = $allowedTypes[$mimeType];
$uniqueSuffix = date('Ymd-His') . '-' . substr(bin2hex(random_bytes(8)), 0, 12);
$targetFilename = sprintf('%s-%s%s', $normalizedBase, $uniqueSuffix, $extension);
$destination = $uploadsDir . $targetFilename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to store uploaded file.'
    ]);
    exit;
}

$mediaUrl = 'media.php?file=' . rawurlencode($targetFilename);

http_response_code(201);
echo json_encode([
    'status' => 'success',
    'message' => 'File uploaded successfully.',
    'media' => [
        'name' => $originalName ?: $targetFilename,
        'filename' => $targetFilename,
        'url' => $mediaUrl,
        'size' => filesize($destination),
        'mime' => $mimeType,
        'uploadedAt' => gmdate('c')
    ]
]);
	exit;

function uploadErrorMessage(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Uploaded file exceeds allowed size.',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary directory on server.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        default => 'Unknown upload error occurred.'
    };
}
