<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra WBI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #fff;
            color: #000;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
            font-weight: bold;
        }
        p.subtitle {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 12px;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>

    <h2>DAFTAR MITRA / INSTANSI KERJASAMA WBI</h2>
    <p class="subtitle">
        Tahun: <?= $tahun == 'All' ? 'Keseluruhan' : $tahun ?><br>
        <small>Total Mitra: <?= count($mitra_list) ?></small>
    </p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Mitra</th>
                <th width="20%">Jenis Mitra</th>
                <th width="20%">Badan Usaha</th>
                <th width="6%">Jumlah MoU</th>
                <th width="6%">Jumlah MoA</th>
                <th width="8%">Jumlah Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($mitra_list)): ?>
                <?php $no = 1; foreach ($mitra_list as $row): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $row->nm_instansi ?: '-' ?></td>
                    <td><?= $row->nm_jenis_instansi ?: 'Tidak Diketahui' ?></td>
                    <td><?= $row->nm_bidang_usaha ?: 'Tidak Diketahui' ?></td>
                    <td class="text-center"><?= $row->jml_mou ?></td>
                    <td class="text-center"><?= $row->jml_moa ?></td>
                    <td class="text-center"><?= $row->jml_kegiatan ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data mitra untuk periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
