<?php
$saveDir = __DIR__ . '/storage/snapshots/';

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
$filePath = $saveDir . $filename;

if ($filename && file_exists($filePath)) {
    echo file_get_contents($filePath);
} else {
    echo "<h2>File not found.</h2>";
}
