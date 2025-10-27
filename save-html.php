<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Use POST to submit editor content.'
    ]);
    exit;
}

$saveDir = __DIR__ . '/storage/snapshots/';
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

$filename = basename($_POST['filename'] ?? 'new-page.html');
$title = trim($_POST['title'] ?? 'Untitled Page');
$slug = trim($_POST['slug'] ?? '');
$htmlContent = $_POST['htmlContent'] ?? '';

$response = [
    'status' => 'success',
    'message' => 'Page saved successfully.',
    'meta' => [
        'filename' => $filename,
        'title' => $title,
        'slug' => $slug,
    ],
    'warnings' => [],
];

$filePath = $saveDir . $filename;

if (file_put_contents($filePath, $htmlContent) === false) {
    $response['status'] = 'error';
    $response['message'] = 'Unable to write HTML file to disk.';
    echo json_encode($response);
    exit;
}

// Database connection (optional)
$host = 'localhost';
$dbname = 'cms';
$username = 'root';
$password = '';

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    $response['warnings'][] = 'Database connection unavailable. Content stored locally only.';
    echo json_encode($response);
    exit;
}

$inserted = false;

try {
    $slugValue = $slug !== '' ? $slug : null;
    $stmt = $pdo->prepare("INSERT INTO pages (title, slug, content, created_at, updated_at) VALUES (:title, :slug, :content, NOW(), NOW())");
    $stmt->bindValue(':title', $title !== '' ? $title : 'Untitled Page');
    $stmt->bindValue(':slug', $slugValue);
    $stmt->bindValue(':content', $htmlContent);
    $stmt->execute();
    $inserted = true;
} catch (PDOException $e) {
    try {
        $stmt = $pdo->prepare("INSERT INTO pages (title, content, created_at, updated_at) VALUES (:title, :content, NOW(), NOW())");
        $stmt->bindValue(':title', $title !== '' ? $title : 'Untitled Page');
        $stmt->bindValue(':content', $htmlContent);
        $stmt->execute();
        $inserted = true;
        $response['warnings'][] = 'Page saved without slug field. Ensure the pages table has a slug column for friendly URLs.';
    } catch (PDOException $inner) {
        $response['warnings'][] = 'Database insert failed: ' . $inner->getMessage();
    }
}

if (!$inserted) {
    $response['message'] = 'HTML file saved. Database write failed.';
    if ($response['status'] !== 'error') {
        $response['status'] = 'partial';
    }
}

echo json_encode($response);
?>
