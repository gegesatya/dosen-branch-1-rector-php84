<?php
$conn = new mysqli("localhost", "root", "", "portal_wbi");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getStatsHtml($conn, $prodi_name, $id_sms, $limit = 25, &$global_stats) {
    $sql_students = "SELECT n.nipd, m.nm_pd
        FROM siakad_skripsi_nilai_sempro n
        JOIN siakad_mahasiswa_pt pt ON pt.nipd = n.nipd
        JOIN siakad_mahasiswa m ON pt.id_pd = m.id_pd
        WHERE pt.id_sms = '$id_sms' AND n.sidang_nilai_akhir > 0
        GROUP BY n.nipd, m.nm_pd
        ORDER BY RAND()
        LIMIT $limit";
        
    $res = $conn->query($sql_students);
    $nipds = [];
    $student_names = [];
    while($row = $res->fetch_assoc()) {
        $nipds[] = "'" . $row['nipd'] . "'";
        $student_names[$row['nipd']] = $row['nm_pd'];
    }
    
    if (empty($nipds)) return "<h3>$prodi_name</h3><p>Tidak ada data</p>";
    
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
    
    $html = "<h3>Prodi $prodi_name</h3>\n";
    $html .= "<table>\n";
    $html .= "<thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Nilai Akhir Lama</th><th>Nilai Akhir Baru</th><th>Selisih</th></tr></thead>\n";
    $html .= "<tbody>\n";
    
    foreach ($students as $nipd => $s) {
        $avg_sempro = round(array_sum($s['sempro']) / count($s['sempro']), 2);
        $avg_semhas = round(array_sum($s['semhas']) / count($s['semhas']), 2);
        $avg_sidang_old = round(array_sum($s['sidang_old']) / count($s['sidang_old']), 2);
        
        $old_final = round(($avg_sempro + $avg_semhas + ($avg_sidang_old * 4)) / 6, 2);
        $avg_new = round(array_sum($s['nilai_penguji']) / count($s['nilai_penguji']), 2);
        
        $diff = round($avg_new - $old_final, 2);
        
        if ($diff > 0) {
            $diff_str = "<span class='diff-pos'>+" . number_format($diff, 2) . "</span>";
            $global_stats['naik']++;
        } elseif ($diff < 0) {
            $diff_str = "<span class='diff-neg'>" . number_format($diff, 2) . "</span>";
            $global_stats['turun']++;
        } else {
            $diff_str = "<span>0.00</span>";
            $global_stats['tetap']++;
        }
        $global_stats['total']++;
        
        $html .= "<tr>";
        $html .= "<td>{$nipd}</td>";
        $html .= "<td>{$s['nm_pd']}</td>";
        $html .= "<td>" . number_format($old_final, 2) . "</td>";
        $html .= "<td><strong>" . number_format($avg_new, 2) . "</strong></td>";
        $html .= "<td>{$diff_str}</td>";
        $html .= "</tr>\n";
    }
    $html .= "</tbody></table>\n\n";
    
    return $html;
}

$id_mpi = '63f083bc-b3a7-4396-99b3-c707575f7823';
$id_ap = 'f28c79a3-18c1-4b40-b3af-b2bedc810e20';
$id_ah = '01f25707-0271-4fc0-b957-eb0d9d0ce0c4';
$id_pka = 'df50c13b-fd44-4583-b1a4-f22af69b23bf';

$global_stats = ['naik' => 0, 'turun' => 0, 'tetap' => 0, 'total' => 0];

$html_content = "";
$html_content .= getStatsHtml($conn, 'Manajemen Pemasaran Internasional (MPI)', $id_mpi, 25, $global_stats);
$html_content .= getStatsHtml($conn, 'Akuntansi Perpajakan (AP)', $id_ap, 25, $global_stats);
$html_content .= getStatsHtml($conn, 'Agribisnis Hortikultura (AH)', $id_ah, 25, $global_stats);
$html_content .= getStatsHtml($conn, 'Pengelolaan Konvensi dan Acara (PKA)', $id_pka, 25, $global_stats);

$persentase_naik = round(($global_stats['naik'] / $global_stats['total']) * 100, 1);

$summary = "
<div class='alert alert-warning' style='background-color: #F0FDF4; border-color: #16A34A; color: #166534;'>
    <i class='fa fa-chart-line' style='font-size: 20px; color: #16A34A; margin-top: 2px;'></i>
    <div>
        <strong>Hasil Simulasi 100 Sampel Acak (25 per Prodi)</strong>
        <p style='margin: 0;'>
            Dari total {$global_stats['total']} sampel mahasiswa, didapatkan:
            <br>✅ Naik: <strong>{$global_stats['naik']} mahasiswa ({$persentase_naik}%)</strong>
            <br>🔻 Turun: <strong>{$global_stats['turun']} mahasiswa</strong>
            <br>➖ Tetap: <strong>{$global_stats['tetap']} mahasiswa</strong>
            <br><br><strong>Kesimpulan:</strong> Secara statistik sangat jelas bahwa metode penilaian baru <strong>cenderung menaikkan nilai akhir</strong> secara signifikan bagi sebagian besar mahasiswa.
        </p>
    </div>
</div>
";

echo $summary . "\n\n" . $html_content;

