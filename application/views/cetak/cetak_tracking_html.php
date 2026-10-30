<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ringkasan Laporan Mingguan <?= htmlspecialchars($nm_pd) ?></title>
    
    <!-- Google Fonts for Premium Look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0f172a;
            --secondary-color: #475569;
            --border-color: #cbd5e1;
            --bg-color: #f8fafc;
            --paper-color: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--primary-color);
            padding: 40px 20px;
        }

        /* Kertas Efek (On Screen) */
        .paper-document {
            background-color: var(--paper-color);
            max-width: 210mm; /* Ukuran Lebar A4 */
            margin: 0 auto;
            padding: 20mm; /* Margin A4 standar */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05), 0 4px 10px rgba(0, 0, 0, 0.03);
            border-radius: 8px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .landscape-page {
            max-width: 297mm !important;
        }

        /* Header Styling */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px double #94a3b8;
        }

        .header-left {
            flex: 1;
            padding-right: 20px;
        }

        .header-left h2 {
            font-family: 'Merriweather', serif;
            font-weight: 700;
            font-size: 18px;
            color: #1e293b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .header-left h3 {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .header-right {
            text-align: right;
            flex-basis: auto;
            white-space: nowrap;
        }

        .header-right img {
            width: 100px;
            margin-bottom: 12px;
        }

        .header-right h4 {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .header-right h3 {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        /* Judul Section (Mahasiswa Info) */
        .section-title {
            background-color: #f1f5f9;
            border-left: 4px solid #3b82f6;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 0 6px 6px 0;
        }

        .section-title h4 {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
            letter-spacing: 0.5px;
        }

        .section-title p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #64748b;
        }

        /* Tabel Info Mahasiswa */
        .table-info-mhs {
            width: 100%;
            margin-bottom: 30px;
            background: #ffffff;
        }

        .table-info-mhs td {
            padding: 8px 12px;
            vertical-align: top;
            font-size: 14px;
        }

        .table-info-mhs td:first-child {
            width: 180px;
            font-weight: 600;
            color: #475569;
        }

        .table-info-mhs tr {
            border-bottom: 1px dashed #e2e8f0;
        }
        .table-info-mhs tr:last-child {
            border-bottom: none;
        }

        /* Konten Utama (Tanpa Box Table) */
        .content-block {
            margin-bottom: 25px;
            page-break-inside: auto;
        }

        .content-heading {
            font-weight: 700;
            padding: 8px 0;
            color: #1e293b;
            margin: 0 0 10px 0;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14.5px;
            page-break-after: avoid;
        }

        .content-body {
            padding: 5px 0 10px 15px;
            font-size: 13.5px;
            color: #1e293b;
            line-height: 1.6;
        }

        .day-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #cbd5e1;
            page-break-inside: auto;
        }
        .day-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        /* Floating Print Button */
        .print-btn-container {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 1000;
        }

        .btn-print-custom {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }

        .btn-print-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            color: white;
        }

        /* Cetak (Print) Styles */
        @media print {
            @page {
                size: A4;
                margin: 25mm 15mm 20mm 15mm;
            }
            @page landscape {
                size: A4 landscape;
                margin: 25mm 15mm 20mm 15mm;
            }
            body {
                background-color: transparent;
                padding: 0;
            }
            .paper-document {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                padding: 0;
                max-width: none !important;
            }
            .landscape-page {
                page: landscape;
            }
            .no-print {
                display: none !important;
            }
            
            /* Penyesuaian print konten */
            .content-heading {
                border-bottom: 2px solid #000;
                color: #000;
            }
            .content-body {
                color: #000;
            }
            .day-item {
                color: #000;
                border-bottom: 1px solid #cbd5e1;
            }
            .day-item:last-child {
                border-bottom: none;
            }
            .section-title {
                border-left: 4px solid #000;
                background-color: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
            }
            .header-section {
                border-bottom: 3px double #000;
            }
            .table-info-mhs td:first-child {
                color: #000;
            }
            .table-info-mhs tr {
                border-bottom: 1px dashed #cbd5e1;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="btn-print-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Dokumen
        </button>
    </div>

    <!-- Cover Page -->
    <div class="paper-document d-flex flex-column" style="min-height: 250mm;">
        <div class="text-end" style="padding-top: 50px;">
            <h2 style="font-family: 'Merriweather', serif; font-size: 36px; font-weight: 700; color: #1e293b;">LAPORAN MINGGUAN</h2>
            <div style="height: 20px;"></div>
            <h3 style="font-size: 24px; font-weight: 600; color: #475569; letter-spacing: 1px;">PROGRAM INTERNSHIP</h3>
            <h3 style="font-size: 24px; font-weight: 700; color: #0f172a; text-transform: uppercase;"><?= !empty($lokasi) ? htmlspecialchars($lokasi) : '<Isi Nama Perusahaan>' ?></h3>
        </div>
        
        <div class="text-end" style="margin-top: auto; padding-bottom: 30px;">
            <h3 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 5px;"><?= htmlspecialchars($nm_pd) ?></h3>
            <h3 style="font-size: 22px; font-weight: 600; color: #475569; margin-bottom: 40px;"><?= htmlspecialchars($nipd) ?></h3>
            <img src="<?= base_url('assets/image_web/logo-white-3.png') ?>" alt="Logo Kampus" style="width: 280px; margin-bottom: 40px;">
            
            <h4 style="font-size: 18px; font-weight: 700; color: #475569; letter-spacing: 1px; margin-bottom: 10px; white-space: nowrap;">PROGRAM STUDI <?= strtoupper(htmlspecialchars($nm_lemb)) ?></h4>
            <h3 style="font-family: 'Merriweather', serif; font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 15px; white-space: nowrap;">POLITEKNIK WILMAR BISNIS INDONESIA</h3>
            <h4 style="font-size: 18px; font-weight: 600; color: #64748b;"><?= !empty($tahun_magang) ? htmlspecialchars($tahun_magang) : '<Isi Tahun Magang>' ?></h4>
        </div>
    </div>
    
    <div class="page-break"></div>

    <?php foreach ($data as $key => $d): ?>
    
    <?php if($key > 0): ?>
    <div class="page-break"></div>
    <?php endif; ?>

    <div class="paper-document">
        <div class="header-section">
            <div class="header-left">
                <h2>LAPORAN MINGGUAN</h2>
                <h3>PROGRAM INTERNSHIP</h3>
                <h3><?= !empty($lokasi) ? htmlspecialchars($lokasi) : '<Isi Nama Perusahaan>' ?></h3>
            </div>
            <div class="header-right">
                <img src="<?= base_url('assets/image_web/logo-white-3.png') ?>" alt="Logo Kampus">
                <h3>POLITEKNIK WILMAR BISNIS INDONESIA</h3>
                <h4><?= !empty($tahun_magang) ? htmlspecialchars($tahun_magang) : '<Isi Tahun Magang>' ?></h4>
            </div>
        </div>

        <div class="section-title">
            <h4>RINGKASAN LAPORAN</h4>
            <p>Program Internship Prodi <?= strtoupper(htmlspecialchars($nm_lemb)) ?></p>
        </div>
        
        <table class="table-info-mhs">
            <tbody>
                <tr>
                    <td>Nama Mahasiswa</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($d->nm_pd) ?></td>
                </tr>
                <tr>
                    <td>NIM</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($d->nipd) ?></td>
                </tr>
                <tr>
                    <td>Periode Laporan</td>
                    <td>:</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><?= htmlspecialchars($d->periode_laporan) ?></span></td>
                </tr>
            </tbody>
        </table>

        <div class="content-block">
            <div class="content-heading">Kegiatan yang dilakukan</div>
            <div class="content-body" style="padding-top: 10px;">
                <div class="day-item"><?= $d->kegiatan_hari_1 ?></div>
                <div class="day-item"><?= $d->kegiatan_hari_2 ?></div>
                <div class="day-item"><?= $d->kegiatan_hari_3 ?></div>
                <div class="day-item"><?= $d->kegiatan_hari_4 ?></div>
                <div class="day-item"><?= $d->kegiatan_hari_5 ?></div>
                <div class="day-item"><?= $d->kegiatan_hari_6 ?></div>
            </div>
        </div>

        <div class="content-block">
            <div class="content-heading">Pengetahuan baru yang diperoleh</div>
            <div class="content-body">
                <div><?= $d->pengetahuan_baru ?></div>
            </div>
        </div>

        <div class="content-block">
            <div class="content-heading">Keterampilan baru yang diperoleh</div>
            <div class="content-body">
                <div><?= $d->keterampilan_baru ?></div>
            </div>
        </div>

        <div class="content-block">
            <div class="content-heading">Tujuan Internship yang dicapai <small class="fw-normal text-muted">(Sesuaikan dengan Usulan Kegiatan Internship)</small></div>
            <div class="content-body">
                <div><?= $d->tujuan_internship ?></div>
            </div>
        </div>

        <div class="content-block">
            <div class="content-heading">Catatan Lain-lain <small class="fw-normal text-muted">(Catatan yang dianggap perlu / kejadian penting)</small></div>
            <div class="content-body">
                <div><?= $d->catatan_lain ?></div>
            </div>
        </div>

        <div class="content-block">
            <div class="content-heading">Data Pendukung (Link Google Drive)</div>
            <div class="content-body">
                <div>
                    <?php if(!empty($d->link_google_drive)): ?>
                        <a href="<?= htmlspecialchars(trim((string)$d->link_google_drive)) ?>" target="_blank" class="text-decoration-none text-primary fw-semibold" style="word-break: break-all;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-link-45deg me-1" viewBox="0 0 16 16">
                              <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/>
                              <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/>
                            </svg>
                            <?= htmlspecialchars(trim((string)$d->link_google_drive)) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Tidak ada link terlampir</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-block" style="margin-bottom: 0;">
            <div class="content-heading">Review Dosen Pembimbing Lapangan</div>
            <div class="content-body">
                <div><?= $d->review_dpl ?></div>
            </div>
        </div>
    </div>

    <?php if(!empty($d->link_google_drive)): ?>
        <?php
        $link = trim((string)$d->link_google_drive);
        $is_image = false;
        $is_folder = false;
        $img_src = '';
        $iframe_src = '';
        
        // Deteksi jika link adalah file tunggal Google Drive
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $link, $matches)) {
            $file_id = $matches[1];
            $img_src = "https://drive.google.com/uc?export=view&id=" . $file_id;
            $is_image = true;
        } 
        // Deteksi jika link diakhiri dengan ekstensi gambar
        elseif (preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $link)) {
            $img_src = $link;
            $is_image = true;
        }
        // Deteksi jika link adalah folder Google Drive
        elseif (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $link, $matches) || preg_match('/id=([a-zA-Z0-9_-]+)/', $link, $matches)) {
            $folder_id = $matches[1];
            $iframe_src = "https://drive.google.com/embeddedfolderview?id=" . $folder_id . "#grid";
            $is_folder = true;
        }
        ?>
        
        <?php if($is_image || $is_folder): ?>
        <div class="page-break"></div>
        <div class="paper-document">
            <?php if($is_image): ?>
                <div class="text-center">
                    <img src="<?= htmlspecialchars($img_src) ?>" alt="Lampiran Bukti" style="max-width: 100%; max-height: 900px; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                </div>
            <?php elseif($is_folder): ?>
                <div style="width: 100%; height: 900px; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden; position: relative; background: #f8fafc;">
                    <iframe src="<?= htmlspecialchars($iframe_src) ?>" style="width: 75%; height: 75%; transform: scale(1.3333); transform-origin: 0 0; border: none;"></iframe>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php endforeach; ?>

</body>
</html>
