<?php
$html_file = 'd:\1 Penting\analisis_penilaian_baru.html';
$content = file_get_contents($html_file);

$new_data = file_get_contents('sim_html_output.txt');

$start_marker = '<h2>2. Simulasi Perbandingan Nilai (Sampel Lintas Prodi)</h2>';
$end_marker = '<h2>3. Kesimpulan & Insight Analisis</h2>';

$start_pos = strpos($content, $start_marker);
$end_pos = strpos($content, $end_marker);

if ($start_pos !== false && $end_pos !== false) {
    $before = substr($content, 0, $start_pos);
    $after = substr($content, $end_pos);
    
    $new_section = "<h2>2. Simulasi Perbandingan Nilai (Sampel Lintas Prodi)</h2>\n";
    $new_section .= "        <p>Berikut adalah simulasi dari 100 mahasiswa yang diambil secara acak (maksimal 25 per prodi) dari 4 Program Studi: <strong>Manajemen Pemasaran Internasional (MPI), Akuntansi Perpajakan (AP), Agribisnis Hortikultura (AH), dan Pengelolaan Konvensi dan Acara (PKA)</strong>.</p>\n\n";
    $new_section .= $new_data;
    $new_section .= "\n        "; // Indentation for the next tag
    
    $updated_content = $before . $new_section . $after;
    file_put_contents($html_file, $updated_content);
    echo "Success: HTML updated.";
} else {
    echo "Error: Markers not found.";
}
