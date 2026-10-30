<?php
$grouped = [];
foreach($alumni_data as $s) {
    $grouped[$s->nm_prodi][] = $s;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Data Alumni</title>
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
        <h2>Data Alumni Politeknik WBI</h2>
        <h3>Berdasarkan Program Studi</h3>
        <p>Dicetak pada: <?= date('d M Y H:i:s') ?></p>
    </div>

    <?php if(empty($grouped)): ?>
        <p class="text-center" style="margin-top: 50px; font-style: italic; color: #858796;">Tidak ada data alumni ditemukan.</p>
    <?php endif; ?>

    <?php foreach($grouped as $prodi => $mhs_list): ?>
        <h4 style="color: #4e73df; border-left: 4px solid #4e73df; padding-left: 10px; margin-bottom: 10px;">
            Program Studi: <?= $prodi ?> 
            <span style="color: #858796; font-weight: normal; font-size: 11px;">(Total: <?= count($mhs_list) ?> Alumni)</span>
        </h4>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="15%">NIM</th>
                    <th width="50%">Nama Alumni</th>
                    <th width="15%" class="text-center">Tahun Masuk</th>
                    <th width="15%" class="text-center">Tahun Keluar</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($mhs_list as $m): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $m->nipd ?></td>
                        <td><?= $m->nm_pd ?></td>
                        <td class="text-center"><?= $m->tahun_masuk ?></td>
                        <td class="text-center"><?= $m->tahun_keluar ?></td>
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
