<?php
define('BASEPATH', true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

$conn = new mysqli($host, $user, $pass, $dbname);

$sql = "SELECT * FROM kaprodi";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
