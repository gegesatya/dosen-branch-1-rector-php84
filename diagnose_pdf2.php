<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASEPATH', 'c:/laragon/www/wbi/server/dosen/system/');
define('FCPATH', 'c:/laragon/www/wbi/server/dosen/');
define('APPPATH', 'c:/laragon/www/wbi/server/dosen/application/');

require APPPATH . 'config/database.php';
$dbcfg = $db['default'];

echo "DB: " . $dbcfg['hostname'] . " / " . $dbcfg['database'] . "\n";

$conn = new mysqli($dbcfg['hostname'], $dbcfg['username'], $dbcfg['password'], $dbcfg['database']);
if ($conn->connect_error) die("Connect failed: " . $conn->connect_error . "\n");
echo "DB Connected OK\n";

$enkrip = '0deb1c54814305ca9ad266f53bc82511';

// Show tables to verify we're in right db
$res = $conn->query("SHOW TABLES LIKE 'siakad_skripsi_pengajuan_judul'");
echo "Table exists: " . ($res->num_rows > 0 ? 'YES' : 'NO') . "\n";

// Use the same approach as Qr_model::judul_berita_acara
$res = $conn->query("SELECT j.*, m.nm_pd, m.nipd, m.nm_lemb 
    FROM siakad_skripsi_pengajuan_judul j 
    LEFT JOIN siakad_mahasiswa_pt m ON m.nipd = j.nipd
    WHERE md5(j.id_judul_skripsi) = '$enkrip' LIMIT 1");
    
if ($res === false) {
    echo "Query error: " . $conn->error . "\n";
    // Try without join
    $res2 = $conn->query("SELECT * FROM siakad_skripsi_pengajuan_judul WHERE md5(id_judul_skripsi) = '$enkrip' LIMIT 1");
    if ($res2 === false) echo "Query2 error: " . $conn->error . "\n";
    else {
        $row = $res2->fetch_assoc();
        echo "Found: " . ($row ? $row['nipd'] : 'NULL') . "\n";
    }
} else {
    $judul = $res->fetch_object();
    echo "Mahasiswa: " . ($judul ? $judul->nm_pd . " (" . $judul->nipd . ")" : 'NOT FOUND') . "\n";
}

// Now test QR generation and capture any warnings
require APPPATH . 'third_party/endroid_qrcode/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

echo "\nTesting QR code generation...\n";
ob_start();
$qrCode = new QrCode("test_diagnostic_" . time());
$qrCode->setSize(65);
$qrCode->setWriterByName('png');
$qrCode->setMargin(3);
$qrCode->setEncoding('UTF-8');
$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM);
$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
$qrCode->setValidateResult(false);
$qrCode->writeFile(FCPATH . 'assets/qrcode/test_diag.png');
$captured = ob_get_clean();

echo "QR generation captured output: " . ($captured ? '"' . trim($captured) . '"' : 'NONE - clean') . "\n";

// Test mPDF init
echo "\nTesting mPDF initialization...\n";
ob_start();
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
$captured2 = ob_get_clean();
echo "mPDF init captured output: " . ($captured2 ? '"' . substr(trim($captured2), 0, 100) . '"' : 'NONE - clean') . "\n";

echo "\nDone!\n";
