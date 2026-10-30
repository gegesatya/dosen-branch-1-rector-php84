<?php
// Simulate what Apache (Laragon) does with output_buffering=4096
// Check if CodeIgniter hooks or config output anything early

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

// Get sidang
$res = $conn->query("SELECT * FROM siakad_skripsi_seminar_sidang WHERE id_judul_skripsi = '{$judul->id_judul_skripsi}' LIMIT 1");
$data_sidang = $res->fetch_object();

echo "penguji_3 value: '" . $data_sidang->penguji_sidang_3 . "'\n";
echo "penguji_3 empty: " . (empty($data_sidang->penguji_sidang_3) ? 'YES' : 'NO') . "\n";

// This is what the code does right now - NO null check!
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

function get_penguji($conn, $id_ptk) {
    if (empty($id_ptk)) return null;
    $res = $conn->query("SELECT * FROM feeder_dosen_pt WHERE id_ptk = '$id_ptk' LIMIT 1");
    return $res ? $res->fetch_object() : null;
}

$penguji_3 = get_penguji($conn, $data_sidang->penguji_sidang_3);
echo "penguji_3 object: " . ($penguji_3 ? $penguji_3->nama_lengkap : 'NULL') . "\n";

// This is what the PRODUCTION code does - tries $penguji_3->nama_lengkap even if null!
echo "\nSimulating production code line:\n";
echo "   \$data_penguji_3 = \$penguji_3->nama_lengkap . '...' \n";
try {
    $data_penguji_3 = $penguji_3->nama_lengkap . ' (01 Aug 2026)'; // This throws if null!
    echo "   -> OK: $data_penguji_3\n";
} catch (\Throwable $e) {
    echo "   -> EXCEPTION: " . $e->getMessage() . "\n";
    echo "   -> This exception is echoed to browser, CORRUPTING the PDF!\n";
}

// Now simulate what happens when the exception is caught and echoed
echo "\nIn production, catch(Throwable) does: echo \$th;\n";
echo "This outputs the full stack trace as text, which goes BEFORE the PDF!\n";

// ALSO check: kop_wbi.png - does it produce GD warnings?
$imgPath = FCPATH . 'assets/image_web/kop_wbi.png';
echo "\nTesting kop_wbi.png...\n";
ob_start();
$imgData = file_get_contents($imgPath);
$img = imagecreatefromstring($imgData);
$out = ob_get_clean();
echo "imagecreatefromstring output: " . ($out ? '"'.trim($out).'"' : '(empty - clean!)') . "\n";
imagedestroy($img);
