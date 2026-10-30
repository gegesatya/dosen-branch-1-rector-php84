<?php
$grouped = [];
if (!empty($students) && is_iterable($students)) {
    foreach($students as $s) {
        $grouped[$s->angkatan][] = $s;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Detail Kinerja Cohort per Angkatan</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fc; color: #4e73df; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .text-center { text-align: center; }
        h2, h3, h4 { margin: 5px 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #eaecf4; padding-bottom: 15px; }
        .header h2 { color: #4e73df; }
        .header h3 { color: #5a5c69; font-weight: normal; }
        .header p { color: #858796; font-size: 11px; }
        
        .badge-danger { color: #e74a3b; font-weight: bold; }
        .badge-success { color: #1cc88a; font-weight: bold; }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page-break { page-break-before: always; }
        }
        
        .btn-print {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-print:hover { background-color: #2e59d9; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
    </div>

    <div class="header">
        <h2>Detail Pemantauan Kinerja per Angkatan</h2>
        <h3><?= $prodi_label ?></h3>
        <p>Dicetak pada: <?= date('d M Y H:i:s') ?></p>
    </div>

    <?php if(empty($grouped)): ?>
        <p class="text-center" style="margin-top: 50px; font-style: italic; color: #858796;">Tidak ada data mahasiswa ditemukan.</p>
    <?php endif; ?>

    <?php foreach($grouped as $angkatan => $mhs_list): ?>
        <h4 style="color: #4e73df; border-left: 4px solid #4e73df; padding-left: 10px; margin-bottom: 10px;">
            Angkatan <?= $angkatan ?> 
            <span style="color: #858796; font-weight: normal; font-size: 11px;">(Total: <?= count($mhs_list) ?> Mahasiswa)</span>
        </h4>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="15%">NIM</th>
                    <th width="27%">Nama Mahasiswa</th>
                    <th width="10%" class="text-center">IPK</th>
                    <th width="13%" class="text-center">SKS Total</th>
                    <th width="15%" class="text-center">Kriteria IPK</th>
                    <th width="15%" class="text-center">Status Kelulusan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($mhs_list as $m): ?>
                    <?php 
                        $ipk = floatval($m->ipk);
                        $sks_total = intval($m->sks_total);
                        $kriteria_ipk = ($ipk < 2.75) ? '<span class="badge-danger">IPK < 2.75</span>' : '<span class="badge-success">IPK &ge; 2.75</span>';
                        $status_lulus = ($m->id_jns_keluar == 1) ? '<span class="badge-success">Sdh Lulus</span>' : '<span class="badge-danger">Blm Lulus</span>';
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $m->nipd ?></td>
                        <td><?= $m->nm_pd ?></td>
                        <td class="text-center" style="font-weight: bold;"><?= number_format($ipk, 2) ?></td>
                        <td class="text-center"><?= $sks_total ?></td>
                        <td class="text-center"><?= $kriteria_ipk ?></td>
                        <td class="text-center"><?= $status_lulus ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
