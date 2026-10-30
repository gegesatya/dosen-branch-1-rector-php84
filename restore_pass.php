<?php
$mysqli = new mysqli('localhost', 'root', '', 'portal_wbi_server');
$hash = '$2y$08$gkhsCjgUdgDmTVj59JJzYubbteWO3lDBMCB7VJrLYs3uEoasD7Ol2';
$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = 127");
$stmt->bind_param("s", $hash);
$stmt->execute();
echo "Restored!";
