<?php
$conn = new mysqli('localhost', 'root', '', 'portal_wbi_server');
$res = $conn->query("SELECT npsn FROM dapo_sekolah GROUP BY npsn HAVING count(id) > 1");
$count = 0;
while($row = $res->fetch_assoc()) {
    $npsn = $row['npsn'];
    $ids = [];
    $res2 = $conn->query("SELECT id FROM dapo_sekolah WHERE npsn = '$npsn' ORDER BY id");
    while($row2 = $res2->fetch_assoc()) {
        $ids[] = $row2['id'];
    }
    // keep the first one, delete the rest
    array_shift($ids);
    foreach($ids as $id_to_del) {
        $conn->query("DELETE FROM dapo_sekolah WHERE id = '$id_to_del'");
        $count++;
    }
}
echo 'Deleted ' . $count . " duplicates.\n";
