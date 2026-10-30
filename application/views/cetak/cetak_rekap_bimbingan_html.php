<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Bimbingan - <?= htmlspecialchars($dosen_name) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 20mm 20mm 20mm; /* Top margin reduced */
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f4f4f4; /* Gray background for screen */
        }

        .print-container {
            background: #fff;
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            padding: 15px 40px; /* Top padding reduced */
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn-print-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-print {
            background-color: #0D9488;
            color: white;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-print:hover {
            background-color: #0F766E;
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .kop-surat img {
            width: 175px;
        }

        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        /* Student Metadata Section */
        .student-section {
            page-break-inside: avoid;
            margin-bottom: 40px;
        }

        .metadata-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .metadata-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .metadata-table td:first-child {
            width: 150px;
        }
        .metadata-table td:nth-child(2) {
            width: 15px;
            text-align: center;
        }

        /* History Table */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .history-table th, .history-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .history-table th {
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 15px !important;
        }

        /* Helper for parsing */
        .parsed-log strong {
            display: block;
            margin-top: 5px;
        }
        .parsed-log strong:first-child {
            margin-top: 0;
        }

        /* Status colors */
        .status-badge {
            font-weight: bold;
        }
        .status-revisi { color: #D97706; }
        .status-menunggu { color: #2563EB; }
        .status-disetujui { color: #16A34A; }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .print-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .btn-print-wrapper {
                display: none;
            }
            
            /* Start a new page for each student if necessary, but try to keep it compact */
            .student-section {
                page-break-inside: avoid;
            }
            
            .history-table th {
                background-color: #eee !important;
                -webkit-print-color-adjust: exact; 
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-wrapper">
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak Dokumen (Printer / PDF)
        </button>
    </div>

    <div class="print-container">
        <?php foreach ($mahasiswa as $index => $m): ?>
            <div class="student-section" <?= $index > 0 ? 'style="page-break-before: always;"' : '' ?>>
                <div class="kop-surat">
                    <img src="<?= base_url('assets/image_web/kop_wbi.png') ?>" alt="Logo WBI">
                </div>

                <div class="document-title">
                    KEGIATAN BIMBINGAN TUGAS AKHIR
                </div>

                <table class="metadata-table">
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($m->nm_pd) ?></td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($m->nipd) ?></td>
                    </tr>
                    <tr>
                        <td>Prodi</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($m->nama_prodi) ?></td>
                    </tr>
                    <tr>
                        <td>Judul Tugas Akhir</td>
                        <td>:</td>
                        <td style="text-transform: uppercase;"><?= htmlspecialchars($m->judul_ditetapkan) ?></td>
                    </tr>
                    <tr>
                        <td>Dosen Pembimbing</td>
                        <td>:</td>
                        <td>
                            <?php if (!empty($m->nama_dosen_2)): ?>
                                <ol style="margin: 0; padding-left: 18px;">
                                    <li><?= htmlspecialchars($m->nama_dosen_1) ?></li>
                                    <li><?= htmlspecialchars($m->nama_dosen_2) ?></li>
                                </ol>
                            <?php else: ?>
                                <?= htmlspecialchars($m->nama_dosen_1) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <table class="history-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th style="width: 90px;">Tanggal</th>
                            <th>Kegiatan Bimbingan</th>
                            <th style="width: 60px;">Persen</th>
                            <th style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $has_valid_logs = false;
                        foreach ($m->logs as $log): 
                            
                            $has_valid_logs = true;
                            
                                                        $tgl = (!empty($log->tanggal_bimbingan) && $log->tanggal_bimbingan != '0000-00-00') ? $log->tanggal_bimbingan : ((!empty($log->created_at)) ? date('Y-m-d', strtotime($log->created_at)) : '-');
                            if ($tgl != '-') {
                                $tgl = date('d-m-Y', strtotime($tgl));
                            }
                            
                                                        $plain_text = strip_tags($log->catatan_mahasiswa);
                            $aktivitas_text = $plain_text;
                            if (preg_match('/Aktivitas:\s*(.*?)(?=Kendala:|Target Berikutnya:|$)/is', $plain_text, $matches)) {
                                $aktivitas_text = trim($matches[1]);
                            }
                        ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td style="text-align: center;"><?= $tgl ?></td>
                                <td>
                                    <div style="margin-bottom: 8px;">
                                        <strong>Tahapan:</strong> <?= htmlspecialchars($log->tahapan) ?>
                                    </div>
                                    <div class="parsed-log">
                                        <?= nl2br(htmlspecialchars($aktivitas_text)) ?>
                                    </div>
                                    <?php if (!empty($log->catatan_pendamping)): ?>
                                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
                                            <strong>Catatan Pendamping:</strong><br>
                                            <?= nl2br(htmlspecialchars($log->catatan_pendamping)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($log->catatan_dosen)): ?>
                                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
                                            <strong>Catatan Dosen Utama:</strong><br>
                                            <?= nl2br(htmlspecialchars($log->catatan_dosen)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;"><?= intval($log->progress_persen) ?>%</td>
                                <td style="text-align: center;">
                                    
                                    <?php 
                                        $has_pendamping_action = !empty($log->status_pendamping);
                                        $has_utama_action = ($log->status_bimbingan != 'Menunggu Review');
                                    ?>

                                    <?php if ($has_pendamping_action): ?>
                                        <?php 
                                            $p_class = (stripos((string)$log->status_pendamping, 'Revisi') !== false) ? 'status-revisi' : 'status-disetujui';
                                            $p_text = (stripos((string)$log->status_pendamping, 'Revisi') !== false) ? 'Permintaan revisi oleh' : 'Disetujui oleh';
                                        ?>
                                        <div style="<?= $has_utama_action ? 'margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #ccc;' : '' ?>">
                                            <span class="status-badge <?= $p_class ?>"><?= htmlspecialchars($log->status_pendamping) ?></span>
                                            <div style="font-size: 8.5pt; color: #555; margin-top: 6px; line-height: 1.3;">
                                                <?= $p_text ?>:<br>
                                                <strong><?= htmlspecialchars($m->nama_dosen_2) ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($has_utama_action): ?>
                                        <?php 
                                            $u_class = (stripos((string)$log->status_bimbingan, 'Revisi') !== false) ? 'status-revisi' : 'status-disetujui';
                                            $u_text = (stripos((string)$log->status_bimbingan, 'Revisi') !== false) ? 'Permintaan revisi oleh' : 'Disetujui oleh';
                                        ?>
                                        <div>
                                            <span class="status-badge <?= $u_class ?>"><?= htmlspecialchars($log->status_bimbingan) ?></span>
                                            <div style="font-size: 8.5pt; color: #555; margin-top: 6px; line-height: 1.3;">
                                                <?= $u_text ?>:<br>
                                                <strong><?= htmlspecialchars($m->nama_dosen_1) ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!$has_pendamping_action && !$has_utama_action): ?>
                                        <span class="status-badge status-menunggu">Menunggu Review</span>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (!$has_valid_logs): ?>
                            <tr>
                                <td colspan="5" class="no-data">Belum ada riwayat bimbingan yang memenuhi kriteria</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 15px; font-size: 10pt; color: #666; text-align: right;">
                    <strong>Academic Health Score:</strong> <?= round($m->academic_health_score) ?> 
                    (<span style="color: <?= $m->kategori_health == 'Aman' ? '#16A34A' : ($m->kategori_health == 'Perlu Perhatian' ? '#D97706' : '#DC2626') ?>"><?= $m->kategori_health ?></span>)
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($mahasiswa)): ?>
            <div style="text-align: center; padding: 50px;">
                <h3 style="color: #666;">Tidak ada data mahasiswa yang sudah melakukan bimbingan.</h3>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
