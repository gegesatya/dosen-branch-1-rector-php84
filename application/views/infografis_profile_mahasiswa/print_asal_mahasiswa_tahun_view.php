<!DOCTYPE html>
<html>
<head>
    <title>Cetak Data Asal Mahasiswa Per Tahun</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #333; }
        table { width: 100%; max-width: 800px; border-collapse: collapse; margin: 0 auto 30px auto; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fc; color: #4e73df; font-weight: bold; text-transform: uppercase; font-size: 13px; text-align: center; }
        .text-center { text-align: center; }
        h2, h3 { margin: 5px 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #eaecf4; padding-bottom: 15px; }
        .header h2 { color: #4e73df; }
        .header h3 { color: #5a5c69; font-weight: normal; }
        .header p { color: #858796; font-size: 12px; }
        
        .year-title {
            background-color: #1cc88a;
            color: white;
            padding: 8px 15px;
            margin: 20px auto 10px auto;
            max-width: 800px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
        }

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
    <div class="no-print" style="text-align: right; margin-bottom: 15px; max-width: 800px; margin-left: auto; margin-right: auto;">
        <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
    </div>

    <div class="header">
        <h2>Data Asal Mahasiswa Per Tahun Masuk</h2>
        <h3>Politeknik WBI</h3>
        <p>Dicetak pada: <?= date('d M Y H:i:s') ?></p>
    </div>

    <?php if(empty($asal_mahasiswa_tahun)): ?>
        <p class="text-center" style="margin-top: 50px; font-style: italic; color: #858796;">Tidak ada data ditemukan.</p>
    <?php else: ?>
        <?php foreach($asal_mahasiswa_tahun as $tahun => $data): ?>
            <div class="year-container">
                <div class="year-title">
                    Tahun Masuk: <?= $tahun ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th width="60%">Nama Kota/Kabupaten</th>
                            <th width="30%">Jumlah Mahasiswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; $grand_total = 0; foreach($data as $row): 
                            $grand_total += $row->total;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= (strlen((string)$row->nm_wil) > 4 && substr($row->nm_wil, 0, 4) === 'Kab.') ? substr($row->nm_wil, 4) : $row->nm_wil ?></td>
                                <td class="text-center fw-bold" style="font-weight: bold; color: #4e73df;"><?= $row->total ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align: right; background-color: #eaecf4; font-size: 14px;">Total Mahasiswa</th>
                            <th class="text-center" style="font-size: 15px; color: #1cc88a; background-color: #eaecf4;"><?= $grand_total ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
