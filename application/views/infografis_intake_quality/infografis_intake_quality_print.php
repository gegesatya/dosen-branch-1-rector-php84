<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Daftar Mahasiswa WBI</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            color: #2D3748;
            background: #fff;
            margin: 0;
            padding: 40px;
        }
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 20px;
        }
        .print-header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .print-header p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #CBD5E0;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background: #EDF2F7;
            font-weight: 700;
        }
        .text-center {
            text-align: center;
        }
        
        @media print {
            body { padding: 0; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>

<div class="print-header">
    <h1>Daftar Mahasiswa - <?= htmlspecialchars($nama_sekolah) ?></h1>
    <p>Analisis Intake Quality | Filter: <strong><?= $tahun_label ?></strong></p>
</div>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 50px;">No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th class="text-center">IPK Terakhir</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($mahasiswa)): ?>
            <?php foreach ($mahasiswa as $i => $m): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($m->nim) ?></td>
                <td><strong><?= htmlspecialchars($m->nama) ?></strong></td>
                <td class="text-center"><?= number_format((float)$m->ipk, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px; color: #A0AEC0;">
                    Tidak ada data mahasiswa untuk angkatan ini.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    // Trigger print window automatically when page loads
    window.onload = function() {
        window.print();
    };
</script>

</body>
</html>
