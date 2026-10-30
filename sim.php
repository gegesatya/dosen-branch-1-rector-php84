<?php
$conn = new mysqli("localhost", "root", "", "portal_wbi");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
    n.nipd, 
    m.nm_pd,
    n.id_penilai,
    n.nilai_akhir as sempro,
    n.semhas_nilai_akhir as semhas,
    n.sidang_nilai_a as a,
    n.sidang_nilai_b as b,
    n.sidang_nilai_c as c,
    n.sidang_nilai_d as d,
    n.sidang_nilai_e as e,
    s.penguji_sidang_1 as p1,
    s.penguji_sidang_2 as p2,
    s.penguji_sidang_3 as p3,
    n.sidang_nilai_akhir as sidang_old
FROM siakad_skripsi_nilai_sempro n 
JOIN siakad_mahasiswa_pt pt ON pt.nipd = n.nipd 
JOIN siakad_mahasiswa m ON pt.id_pd = m.id_pd
LEFT JOIN (SELECT id_judul_skripsi, MAX(penguji_sidang_1) as penguji_sidang_1, MAX(penguji_sidang_2) as penguji_sidang_2, MAX(penguji_sidang_3) as penguji_sidang_3 FROM siakad_skripsi_seminar_sidang GROUP BY id_judul_skripsi) s ON n.id_judul_skripsi = s.id_judul_skripsi
WHERE n.nipd IN ('2002010056','2003010020','2001010021','2002010081','2001010073','2003010021','2003010014','2002010073','2003010028','2003010018')";

$result = $conn->query($sql);

$students = [];

while($data = $result->fetch_assoc()) {
    $nipd = $data['nipd'];
    if (!isset($students[$nipd])) {
        $students[$nipd] = [
            'nm_pd' => $data['nm_pd'],
            'sempro' => [],
            'semhas' => [],
            'sidang_old' => [],
            'nilai_penguji' => [],
        ];
    }
    
    $students[$nipd]['sempro'][] = (float)$data['sempro'];
    $students[$nipd]['semhas'][] = (float)$data['semhas'];
    $students[$nipd]['sidang_old'][] = (float)$data['sidang_old'];
    
    $is_p1 = ($data['id_penilai'] == $data['p1']);
    
    $a = (float)$data['a'];
    $b = (float)$data['b'];
    $c = (float)$data['c'];
    $d = (float)$data['d'];
    $e = (float)$data['e'];
    $sempro = (float)$data['sempro'];
    $semhas = (float)$data['semhas'];
    
    if ($is_p1) {
        $nilai_baru = ($sempro * 0.10) + ($semhas * 0.10) + ($a * 0.15) + ($b * 0.15) + ($c * 0.20) + ($d * 0.20) + ($e * 0.10);
    } else {
        $nilai_baru = ($a * 0.25) + ($b * 0.15) + ($c * 0.25) + ($d * 0.25) + ($e * 0.10);
    }
    
    $students[$nipd]['nilai_penguji'][] = $nilai_baru;
}

echo "| NIM | Nama Mahasiswa | Nilai Akhir Lama | Nilai Akhir Baru | Selisih |\n";
echo "|---|---|:---:|:---:|:---:|\n";

foreach ($students as $nipd => $s) {
    // OLD
    $avg_sempro = round(array_sum($s['sempro']) / count($s['sempro']), 2);
    $avg_semhas = round(array_sum($s['semhas']) / count($s['semhas']), 2);
    $avg_sidang_old = round(array_sum($s['sidang_old']) / count($s['sidang_old']), 2);
    
    $old_final = round(($avg_sempro + $avg_semhas + ($avg_sidang_old * 4)) / 6, 2);
    
    // NEW
    $avg_new = round(array_sum($s['nilai_penguji']) / count($s['nilai_penguji']), 2);
    
    $diff = round($avg_new - $old_final, 2);
    $diff_str = $diff > 0 ? "+".$diff : $diff;
    
    echo "| {$nipd} | {$s['nm_pd']} | {$old_final} | **{$avg_new}** | {$diff_str} |\n";
}
