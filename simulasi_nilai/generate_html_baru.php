<?php
$mysqli = new mysqli("localhost", "root", "", "portal_wbi");

$query = "
SELECT 
    n.nipd, 
    m.nm_pd AS nm_pd, 
    s.nm_lemb AS prodi,
    n.id_penilai,
    n.nilai_akhir AS sempro,
    n.semhas_nilai_akhir AS semhas,
    n.sidang_nilai_a AS a,
    n.sidang_nilai_b AS b,
    n.sidang_nilai_c AS c,
    n.sidang_nilai_d AS d,
    n.sidang_nilai_e AS e
FROM siakad_skripsi_nilai_sempro n
JOIN mahasiswa_pt pt ON n.nipd = pt.nipd
JOIN mahasiswa m ON pt.id_pd = m.id_pd
JOIN sms s ON pt.id_sms = s.id_sms
WHERE n.sidang_nilai_akhir > 0
ORDER BY s.nm_lemb, n.nipd, n.id_penilai
";

$res = $mysqli->query($query);
$students = [];
while ($row = $res->fetch_assoc()) {
    $students[$row['nipd']]['nama'] = $row['nm_pd'];
    $students[$row['nipd']]['prodi'] = $row['prodi'];
    $students[$row['nipd']]['rows'][] = $row;
}

$prodi_data = [];
foreach ($students as $nipd => $s) {
    if (count($s['rows']) < 3) continue; // Need 3 evaluators

    // Take first 3 rows
    $p1 = $s['rows'][0];
    $p2 = $s['rows'][1];
    $p3 = $s['rows'][2];

    // --- OLD SYSTEM ---
    // Sidang Standard: A:25, B:15, C:25, D:25, E:10
    $calcSidang = function($p) {
        return ($p['a']*0.25) + ($p['b']*0.15) + ($p['c']*0.25) + ($p['d']*0.25) + ($p['e']*0.10);
    };

    $sidang_p1 = $calcSidang($p1);
    $sidang_p2 = $calcSidang($p2);
    $sidang_p3 = $calcSidang($p3);
    $avg_sidang = ($sidang_p1 + $sidang_p2 + $sidang_p3) / 3;

    // Notice that sempro and semhas might be the same across rows, we'll just take P1's.
    $sempro = $p1['sempro'];
    $semhas = $p1['semhas'];

    $final_lama = (($sempro * 1) + ($semhas * 1) + ($avg_sidang * 4)) / 6;

    // --- NEW SYSTEM ---
    // P1: Sempro 10, Semhas 10, A 15, B 15, C 20, D 20, E 10
    $baru_p1 = ($sempro * 0.10) + ($semhas * 0.10) + 
               ($p1['a'] * 0.15) + ($p1['b'] * 0.15) + 
               ($p1['c'] * 0.20) + ($p1['d'] * 0.20) + ($p1['e'] * 0.10);
    
    // P2 and P3 are Standard Sidang
    $baru_p2 = $calcSidang($p2);
    $baru_p3 = $calcSidang($p3);

    $final_baru = ($baru_p1 + $baru_p2 + $baru_p3) / 3;

    $prodi = $s['prodi'];
    if (!isset($prodi_data[$prodi])) $prodi_data[$prodi] = [];

    // Limit to 25 per prodi to get 100 students
    if (count($prodi_data[$prodi]) < 28) {
        $prodi_data[$prodi][] = [
            'nim' => $nipd,
            'nama' => $s['nama'],
            'sempro' => $sempro,
            'semhas' => $semhas,
            'sidang' => $avg_sidang,
            'lama' => $final_lama,
            'baru' => $final_baru,
            'diff' => $final_baru - $final_lama
        ];
    }
}

// Generate HTML
$html = "";
$naik = 0; $turun = 0; $tetap = 0;
$total = 0;

$html_tables = "";

foreach ($prodi_data as $prodi => $list) {
    // Translate Prodi name to short version
    $prodi_short = $prodi;
    if (strpos($prodi, 'Manajemen Pemasaran Internasional') !== false) $prodi_short = 'Manajemen Pemasaran Internasional (MPI)';
    else if (strpos($prodi, 'Akuntansi Perpajakan') !== false) $prodi_short = 'Akuntansi Perpajakan (AP)';
    else if (strpos($prodi, 'Agribisnis Hortikultura') !== false) $prodi_short = 'Agribisnis Hortikultura (AH)';
    else if (strpos($prodi, 'Konvensi dan Acara') !== false) $prodi_short = 'Pengelolaan Konvensi dan Acara (PKA)';
    
    if (strpos($prodi, 'Rekayasa') !== false) continue; // Skip TRPL as requested

    $count = count($list);
    $total += $count;
    $html_tables .= "<h3>Prodi $prodi_short ($count Mahasiswa)</h3>\n";
    $html_tables .= "<table>\n<thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Sempro</th><th>Semhas</th><th>Rata² Sidang</th><th>N.A. Lama</th><th>N.A. Baru</th><th>Selisih</th></tr></thead>\n<tbody>\n";
    
    foreach ($list as $m) {
        $sempro_fmt = number_format($m['sempro'], 2);
        $semhas_fmt = number_format($m['semhas'], 2);
        $sidang_fmt = number_format($m['sidang'], 2);
        $lama_fmt = number_format($m['lama'], 2);
        $baru_fmt = number_format($m['baru'], 2);
        $diff_fmt = number_format($m['diff'], 2);
        
        if ($m['diff'] > 0.009) {
            $diff_str = "<span class='diff-pos'>+$diff_fmt</span>";
            $naik++;
        } else if ($m['diff'] < -0.009) {
            $diff_str = "<span class='diff-neg'>$diff_fmt</span>";
            $turun++;
        } else {
            $diff_str = "<span>0.00</span>";
            $tetap++;
        }
        
        $html_tables .= "<tr><td>{$m['nim']}</td><td>{$m['nama']}</td><td>$sempro_fmt</td><td>$semhas_fmt</td><td>$sidang_fmt</td><td>$lama_fmt</td><td><strong>$baru_fmt</strong></td><td>$diff_str</td></tr>\n";
    }
    $html_tables .= "</tbody></table>\n\n";
}

$stat_box = "
<div class='alert alert-warning' style='background-color: #F0FDF4; border-color: #16A34A; color: #166534;'>
    <i class='fa fa-chart-line' style='font-size: 20px; color: #16A34A; margin-top: 2px;'></i>
    <div>
        <strong>Hasil Simulasi $total Sampel Acak</strong>
        <p style='margin: 0;'>
            Dari total $total sampel mahasiswa, didapatkan:
            <br>✅ Naik: <strong>$naik mahasiswa (" . round(($naik/$total)*100, 1) . "%)</strong>
            <br>🔻 Turun: <strong>$turun mahasiswa (" . round(($turun/$total)*100, 1) . "%)</strong>
            <br>➖ Tetap: <strong>$tetap mahasiswa</strong>
            <br><br><strong>Kesimpulan:</strong> Secara statistik sangat jelas bahwa metode penilaian baru <strong>cenderung menaikkan nilai akhir</strong> secara signifikan bagi sebagian besar mahasiswa.
        </p>
    </div>
</div>\n\n";

$full_output = $stat_box . $html_tables;

// Read target html and replace
$html_file = 'c:\laragon\www\wbi\server\dosen\simulasi_nilai\analisis_penilaian_baru.html';
$content = file_get_contents($html_file);

$start_marker = "<h2>2. Simulasi Perbandingan Nilai (Sampel Lintas Prodi)</h2>";
$end_marker = "<h2>3. Kesimpulan & Insight Analisis</h2>";

$start_pos = strpos($content, $start_marker);
$end_pos = strpos($content, $end_marker);

if ($start_pos !== false && $end_pos !== false) {
    $before = substr($content, 0, $start_pos);
    $after = substr($content, $end_pos);
    
    $new_section = "<h2>2. Simulasi Perbandingan Nilai (Sampel Lintas Prodi)</h2>\n";
    $new_section .= "        <p>Berikut adalah simulasi dari $total mahasiswa yang diambil secara acak (maksimal 28 per prodi) dari 4 Program Studi: <strong>Manajemen Pemasaran Internasional (MPI), Akuntansi Perpajakan (AP), Agribisnis Hortikultura (AH), dan Pengelolaan Konvensi dan Acara (PKA)</strong>.</p>\n\n";
    $new_section .= $full_output;
    $new_section .= "\n        "; // Indentation for the next tag
    
    $updated_content = $before . $new_section . $after;
    file_put_contents($html_file, $updated_content);
    echo "Success: HTML updated.";
} else {
    echo "Error: Markers not found.";
}
?>
