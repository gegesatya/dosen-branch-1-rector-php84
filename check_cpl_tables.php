<?php
define('BASEPATH', 'dummy');
require_once 'application/config/database.php';
$db_config = $db['default'];
$pdo = new PDO('mysql:host=' . $db_config['hostname'] . ';dbname=' . $db_config['database'] . ';charset=utf8', $db_config['username'], $db_config['password']);

$cols = $pdo->query('SHOW COLUMNS FROM siakad_pustaka_kurikulum')->fetchAll(PDO::FETCH_ASSOC);
echo "Columns of siakad_pustaka_kurikulum:\n";
print_r($cols);

$rows = $pdo->query('SELECT * FROM siakad_pustaka_kurikulum LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);
echo "Sample rows of siakad_pustaka_kurikulum:\n";
print_r($rows);
