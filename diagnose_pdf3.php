<?php
// Full simulation: capture everything that gets output before mpdf->Output()
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASEPATH', 'c:/laragon/www/wbi/server/dosen/system/');
define('FCPATH', 'c:/laragon/www/wbi/server/dosen/');
define('APPPATH', 'c:/laragon/www/wbi/server/dosen/application/');

require APPPATH . 'config/database.php';
$dbcfg = $db['default'];
$conn = new mysqli($dbcfg['hostname'], $dbcfg['username'], $dbcfg['password'], $dbcfg['database']);

$enkrip = '0deb1c54814305ca9ad266f53bc82511';
$sql = "SELECT * FROM siakad_skripsi_pengajuan_judul j
    JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = j.id_judul_skripsi
    JOIN feeder_mahasiswa_pt mp ON mp.nipd = j.nipd
    JOIN feeder_mahasiswa m ON m.id_pd = mp.id_pd
    JOIN sms s ON s.id_sms = mp.id_sms
    WHERE md5(ns.id_judul_skripsi) = '$enkrip'
    LIMIT 1";
$judul = $conn->query($sql)->fetch_object();
echo "Student: " . $judul->nipd . " | sidang_hasil: " . $judul->sidang_hasil . "\n";

// Get sidang
$res = $conn->query("SELECT * FROM siakad_skripsi_seminar_sidang WHERE id_judul_skripsi = '{$judul->id_judul_skripsi}' LIMIT 1");
$data_sidang = $res->fetch_object();
echo "penguji_3: '" . $data_sidang->penguji_sidang_3 . "'\n";

// Try create_qr and see if it generates any output
require APPPATH . 'third_party/endroid_qrcode/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

function create_qr($data, $nipd) {
    $qrCode = new QrCode($data);
    $qrCode->setSize(65);
    $qrCode->setWriterByName('png');
    $qrCode->setMargin(3);
    $qrCode->setEncoding('UTF-8');
    $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM);
    $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
    $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
    $qrCode->setValidateResult(false);
    $filename = $nipd . "_qrcode.png";
    $qrCode->writeFile(FCPATH . 'assets/qrcode/' . $filename);
    return $filename;
}

// Capture ALL output during QR generation (as Apache would)
ob_start();
$q1 = create_qr("Test Penguji 1 (01 Aug 2026)", "12345");
$q2 = create_qr("Test Penguji 2 (01 Aug 2026)", "67890");
$q3 = create_qr("Test Penguji 3 (01 Aug 2026)", "11111");
$qk = create_qr("Test Kaprodi", "99999");
$captured = ob_get_clean();

echo "\n=== OUTPUT CAPTURED DURING QR GENERATION ===\n";
echo strlen($captured) . " bytes\n";
echo $captured ? '"' . $captured . '"' : '(empty - clean!)';
echo "\n";

// Now test: what does mPDF init output?
require FCPATH . 'vendor/autoload.php';
ob_start();
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'margin_top' => 30, 'margin_left' => 30, 'margin_right' => 15, 'margin_bottom' => 35]);
$captured2 = ob_get_clean();
echo "\n=== OUTPUT DURING MPDF INIT (" . strlen($captured2) . " bytes) ===\n";
echo $captured2 ? '"' . substr($captured2, 0, 200) . '"' : '(empty - clean!)';
echo "\n";

// Now test: what does WriteHTML output?
ob_start();
$mpdf->WriteHTML('<html><body><p>Test</p></body></html>');
$captured3 = ob_get_clean();
echo "\n=== OUTPUT DURING WRITEHTML (" . strlen($captured3) . " bytes) ===\n";
echo $captured3 ? '"' . substr($captured3, 0, 200) . '"' : '(empty - clean!)';
echo "\nDone!\n";
