<?php
define('BASEPATH', '');
require 'application/config/database.php';
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$res = $conn->query("ALTER TABLE siakad_skripsi_bimbingan_log ADD COLUMN is_ai_review_visible TINYINT(1) DEFAULT 0 AFTER ai_review_result");
if ($res) echo "Column added successfully";
else echo "Error: " . $conn->error;
