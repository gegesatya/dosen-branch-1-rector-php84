<?php
// Process Deskripsi Mata Kuliah
$desc_raw = !empty($rps->deskripsi_mata_kuliah) ? $rps->deskripsi_mata_kuliah : '';
if (!empty($desc_raw)) {
    // Strip redundant markdown headers like ### Deskripsi Singkat
    $desc_raw = preg_replace('/^(?:[#*`\s]*)(?:DESKRIPSI\s*(?:SINGKAT)?(?:\s*MATA\s*KULIAH)?|RINGKASAN\s*MATA\s*KULIAH)(?:\*{1,2})?[:\s\-]*\n*/i', '', trim($desc_raw));
    if (strpos($desc_raw, '<p>') === false && strpos($desc_raw, '<div>') === false) {
        $desc_display = nl2br(trim($desc_raw));
    } else {
        $desc_display = $desc_raw;
    }
} else {
    $desc_display = '<span class="text-muted font-italic">Deskripsi mata kuliah belum diisi pada dokumen RPS.</span>';
}

// Process Referensi
$ref_raw = !empty($rps->referensi) ? $rps->referensi : '';
$ref_display = '';
if (!empty($ref_raw)) {
    if (strpos($ref_raw, '<li') !== false) {
        $ref_display = '<ul class="step-down mb-0 pl-3">' . $ref_raw . '</ul>';
    } else {
        $lines = preg_split('/\r\n|\r|\n/', trim($ref_raw));
        $ref_items = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                // Strip leading numbering or bullets: 1., *, -
                $clean_line = preg_replace('/^(?:\d+[\.\)]\s*|[\-\*•]\s*)/u', '', $line);
                $clean_line = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $clean_line);
                $ref_items .= '<li class="mb-2" style="line-height: 1.55;">' . $clean_line . '</li>';
            }
        }
        if (!empty($ref_items)) {
            $ref_display = '<ol class="mb-0 pl-3" style="font-size: 13.5px;">' . $ref_items . '</ol>';
        } else {
            $ref_display = nl2br($ref_raw);
        }
    }
}
?>

<div class="course-description mb-4">
    <h3 class="description-head font-weight-bold text-primary mb-2"><i class="fa fa-info-circle mr-1"></i> <?php echo get_phrase('Deskripsi Mata Kuliah') ?></h3>
    <div class="p-3 bg-light rounded" style="font-size: 13.5px; line-height: 1.6; border-left: 4px solid #007bff; color: #1e293b;">
        <?= $desc_display; ?>
    </div>
</div>

<?php if (!empty($ref_display)): ?>
<div class="course-description mb-4">
    <h3 class="description-head font-weight-bold text-success mb-2"><i class="fa fa-book mr-1"></i> <?php echo get_phrase('Referensi Utama') ?></h3>
    <div class="p-3 bg-light rounded" style="font-size: 13px; line-height: 1.6; border-left: 4px solid #28a745; color: #1e293b;">
        <?= $ref_display; ?>
    </div>
</div>
<?php endif; ?>

<div class="course-description mb-3 d-flex justify-content-between align-items-center">
    <h3 class="description-head font-weight-bold text-dark mb-0"><i class="fa fa-list-alt mr-1"></i> <?php echo get_phrase('Rencana & Realisasi 16 Pertemuan') ?></h3>
    <button class="btn btn-outline-primary btn-sm rounded shadow-sm" type="button" onclick="reload_table()">
        <i class="fa fa-refresh mr-1"></i> Reload
    </button>
</div>

<div class="table-responsive rounded shadow-sm bg-white p-2">
    <table class="table table-bordered table-hover mb-0" id="datatable_ajax" style="width: 100%;">
        <thead class="bg-light">
            <tr role="row" class="heading text-dark font-weight-bold">
                <th style="width: 170px;">Meet | Jadwal</th>
                <th>Skenario Strategi Pembelajaran (SCL), Sub-CPMK & Draf BAP</th>
                <th style="width: 130px; text-align: center;">Status Realisasi</th>
                <th style="width: 90px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>