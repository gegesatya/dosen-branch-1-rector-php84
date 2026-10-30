<?php
define('BASEPATH', '');
require 'application/config/database.php';
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
$res = $conn->query("SELECT * FROM siakad_skripsi_bimbingan_log ORDER BY id_log DESC LIMIT 1");
print_r($res->fetch_assoc());
