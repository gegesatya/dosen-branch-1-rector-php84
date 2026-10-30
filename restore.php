<?php
define('BASEPATH', true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

$conn = new mysqli($host, $user, $pass, $dbname);

$teks = "<ul><li>Memahami dan mempelajari Standar Operasional Prosedur (SOP) yang berlaku di PT Bank Sumut sebagai pedoman dalam menjalankan aktivitas kerja sesuai dengan ketentuan perusahaan.</li><li>Membantu Customer Service dalam melayani nasabah yang ingin membuka rekening atau tabungan baru di PT Bank Sumut, termasuk mendampingi proses administrasi dan memberikan informasi yang diperlukan kepada nasabah.</li></ul>";

$sql = "SELECT kegiatan_hari_2 FROM simlapkerma_tracking_kegiatan WHERE id_tracking=3553";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$js = json_decode($row['kegiatan_hari_2'], true);
$js['pagi']['teks'] = $teks;

$new_val = json_encode($js);

$stmt = $conn->prepare("UPDATE simlapkerma_tracking_kegiatan SET kegiatan_hari_2 = ? WHERE id_tracking=3553");
$stmt->bind_param("s", $new_val);
$stmt->execute();
echo "Restored 3553.\n";

// What about the other 2 IDs? 3383 and 3449.
// They were wiped too! Let's see what was there originally.
?>
