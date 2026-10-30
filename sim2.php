<?php
$conn = new mysqli("localhost", "root", "", "portal_wbi");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getData($conn, $angkatan, $id_sms, $limit = 10) {
    $nipd_prefix = substr($angkatan, 2, 2); // '20' or '21'
    
    // First, find students that match criteria
    $sql_students = "SELECT n.nipd, m.nm_pd
        FROM siakad_skripsi_nilai_sempro n
        JOIN siakad_mahasiswa_pt pt ON pt.nipd = n.nipd
        JOIN siakad_mahasiswa m ON pt.id_pd = m.id_pd
        WHERE pt.id_sms = '$id_sms' AND n.nipd LIKE '$nipd_prefix%' AND n.sidang_nilai_akhir > 0
        GROUP BY n.nipd, m.nm_pd
        LIMIT $limit";
        
    $res = $conn->query($sql_students);
    $nipds = [];
    $student_names = [];
    while($row = $res->fetch_assoc()) {
        $nipds[] = "'" . $row['nipd'] . "'";
        $student_names[$row['nipd']] = $row['nm_pd'];
    }
    
    if (empty($nipds)) return [];
    
    $nipds_str = implode(",", $nipds);
    
    $sql_data = "SELECT 
        n.nipd, 
        n.id_penilai,
        n.nilai_akhir as sempro,
        n.semhas_nilai_akhir as semhas,
        n.sidang_nilai_a as a,
        n.sidang_nilai_b as b,
        n.sidang_nilai_c as c,
        n.sidang_nilai_d as d,
        n.sidang_nilai_e as e,
        s.penguji_sidang_1 as p1,
        n.sidang_nilai_akhir as sidang_old
    FROM siakad_skripsi_nilai_sempro n 
    LEFT JOIN (SELECT id_judul_skripsi, MAX(penguji_sidang_1) as penguji_sidang_1 FROM siakad_skripsi_seminar_sidang GROUP BY id_judul_skripsi) s ON n.id_judul_skripsi = s.id_judul_skripsi
    WHERE n.nipd IN ($nipds_str)";
    
    $res_data = $conn->query($sql_data);
    $students = [];
    
    while($data = $res_data->fetch_assoc()) {
        $nipd = $data['nipd'];
        if (!isset($students[$nipd])) {
            $students[$nipd] = [
                'nm_pd' => $student_names[$nipd],
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
    
    $results = [];
    foreach ($students as $nipd => $s) {
        $avg_sempro = round(array_sum($s['sempro']) / count($s['sempro']), 2);
        $avg_semhas = round(array_sum($s['semhas']) / count($s['semhas']), 2);
        $avg_sidang_old = round(array_sum($s['sidang_old']) / count($s['sidang_old']), 2);
        
        $old_final = round(($avg_sempro + $avg_semhas + ($avg_sidang_old * 4)) / 6, 2);
        $avg_new = round(array_sum($s['nilai_penguji']) / count($s['nilai_penguji']), 2);
        
        $diff = round($avg_new - $old_final, 2);
        $diff_str = $diff > 0 ? "+".$diff : $diff;
        if ($diff == 0) $diff_str = "0";
        
        $results[] = "| {$nipd} | {$s['nm_pd']} | {$old_final} | **{$avg_new}** | {$diff_str} |";
    }
    
    return $results;
}

$id_mpi = '63f083bc-b3a7-4396-99b3-c707575f7823';
$id_ap = 'f28c79a3-18c1-4b40-b3af-b2bedc810e20';

$mpi20 = getData($conn, '2020', $id_mpi, 10);
$mpi21 = getData($conn, '2021', $id_mpi, 10);
$ap20 = getData($conn, '2020', $id_ap, 10);
$ap21 = getData($conn, '2021', $id_ap, 10);

echo "### Prodi Manajemen Pemasaran Internasional (MPI)\n\n";
echo "**Angkatan 2020:**\n";
echo "| NIM | Nama Mahasiswa | Nilai Akhir Lama | Nilai Akhir Baru | Selisih |\n";
echo "|---|---|:---:|:---:|:---:|\n";
echo implode("\n", $mpi20) . "\n\n";

echo "**Angkatan 2021:**\n";
echo "| NIM | Nama Mahasiswa | Nilai Akhir Lama | Nilai Akhir Baru | Selisih |\n";
echo "|---|---|:---:|:---:|:---:|\n";
echo implode("\n", $mpi21) . "\n\n";

echo "### Prodi Akuntansi Perpajakan (AP)\n\n";
echo "**Angkatan 2020:**\n";
echo "| NIM | Nama Mahasiswa | Nilai Akhir Lama | Nilai Akhir Baru | Selisih |\n";
echo "|---|---|:---:|:---:|:---:|\n";
echo implode("\n", $ap20) . "\n\n";

echo "**Angkatan 2021:**\n";
echo "| NIM | Nama Mahasiswa | Nilai Akhir Lama | Nilai Akhir Baru | Selisih |\n";
echo "|---|---|:---:|:---:|:---:|\n";
echo implode("\n", $ap21) . "\n\n";
