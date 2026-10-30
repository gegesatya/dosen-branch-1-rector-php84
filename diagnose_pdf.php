<?php
// Check PHP config vs production
echo "=== PHP Configuration ===\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "error_reporting: " . ini_get('error_reporting') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";

// Check output buffering
echo "\n=== Output Buffering ===\n";
echo "ob_get_level: " . ob_get_level() . "\n";
echo "ob_get_length: " . ob_get_length() . "\n";

// Simulate what happens when GD warning fires
echo "\n=== GD Warning Test ===\n";
$imgPath = 'c:\laragon\www\wbi\server\dosen\assets\qrcode\\';
$files = glob($imgPath . '*.png');
if (!empty($files)) {
    $testFile = $files[0];
    echo "Testing PNG: " . basename($testFile) . "\n";
    $oldLevel = error_reporting(E_ALL);
    ob_start();
    $img = imagecreatefromstring(file_get_contents($testFile));
    $output = ob_get_clean();
    error_reporting($oldLevel);
    echo "GD warning captured: " . ($output ? trim($output) : 'none') . "\n";
} else {
    echo "No QR code PNG files found in " . $imgPath . "\n";
}

// Check if CodeIgniter outputs anything before PDF
echo "\n=== CI Config ===\n";
$ci_config = file_get_contents('c:\laragon\www\wbi\server\dosen\application\config\config.php');
preg_match("/error_threshold.*?'(.*?)'/", $ci_config, $m);
echo "CI error_threshold: " . ($m[1] ?? 'not found') . "\n";
