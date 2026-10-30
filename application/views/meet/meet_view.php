


<?php
if (!function_exists('render_scl_scenario_cockpit_html_meet')) {
    function render_scl_scenario_cockpit_html_meet($skenario_raw) {
        if (empty($skenario_raw)) {
            return '<div class="text-muted font-italic small p-2 bg-light rounded"><i class="fa fa-info-circle mr-1 text-info"></i> Skenario langkah pembelajaran SCL belum dirinci.</div>';
        }

        $clean = trim($skenario_raw);
        $clean = preg_replace('/<br\s*[\/]?>/i', "\n", $clean);
        $lines = preg_split('/\r\n|\r|\n/', $clean);
        $html = '<div class="scl-cockpit-content" style="font-size: 13.5px; line-height: 1.65; color: #1e293b;">';
        
        $in_list = false;
        foreach ($lines as $line) {
            $tline = trim($line);
            if (empty($tline)) {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                continue;
            }

            // Check Stage title
            if (preg_match('/^(\d+\.\s*)?\*\*(Tahap\s+[^:\*]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:\*]*)\*\*(?:\s*:\s*)?(.*)$/i', $tline, $sm) ||
                preg_match('/^(\d+\.\s*)(?:\*\*)?(Tahap\s+[^:\*]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:\*]*)(?:\*\*)?(?:\s*:\s*)?(.*)$/i', $tline, $sm)) {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                $stage_num = !empty($sm[1]) ? trim($sm[1]) . ' ' : '';
                $stage_title = trim($sm[2]);
                $stage_extra = trim($sm[3] ?? '');
                
                $html .= '<div style="margin-top: 14px; margin-bottom: 6px; font-weight: 700; color: #0f172a; font-size: 13.5px;">'
                       . '<span style="color: #0f172a; font-weight: 700;">' . htmlspecialchars($stage_num . $stage_title) . '</span>'
                       . ($stage_extra ? ' <span style="font-weight: 600; color: #334155;">' . htmlspecialchars($stage_extra) . '</span>' : '')
                       . '</div>';
                continue;
            }

            // Check bullet point
            if (preg_match('/^(?:[\-\*•]|\d+[\.\)])\s*(.*)$/u', $tline, $bm)) {
                if (!$in_list) {
                    $html .= '<ul style="margin: 0 0 10px 0; padding-left: 22px; list-style-type: disc;">';
                    $in_list = true;
                }
                $bullet_content = trim($bm[1]);
                $bullet_content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', htmlspecialchars($bullet_content));
                $html .= '<li style="margin-bottom: 6px; color: #334155; line-height: 1.6;">' . $bullet_content . '</li>';
                continue;
            }

            if ($in_list) {
                $html .= '</ul>';
                $in_list = false;
            }

            $line_content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', htmlspecialchars($tline));
            $html .= '<p style="margin-bottom: 8px; color: #334155;">' . $line_content . '</p>';
        }

        if ($in_list) {
            $html .= '</ul>';
        }

        $html .= '</div>';
        return $html;
    }
}
?>

<div class="row page-titles">
    <div class="col-md-7 align-self-center">
        <h5 class="text-themecolor"><?= $judul_besar; ?></h5>
        <?= $judul_kecil; ?>
    </div>
    <div class="col-md-5 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <a class="btn btn-sm btn-info mr-2" href="<?= site_url('C_strategi_pertemuan/index/' . $id_jadwal); ?>"><i class="fa fa-book mr-1"></i> Strategi Pertemuan 16 Minggu</a>
            <a class="btn waves-effect waves-light btn-dark btn-sm d-none d-lg-block" href="<?= site_url('C_meet/preview/' . $course_id); ?>" target="_new"><i class="fas fa-tv"></i> View Frontend</a>
        </div>
    </div>
</div>

<!-- SCL STRATEGY COCKPIT CARD -->
<?php if (!empty($curr_meet)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border" style="border-radius: 8px; overflow: hidden;">
            <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <h5 class="font-weight-bold mb-1 text-dark" style="font-size: 15.5px;">
                        📌 PERTEMUAN KE-<?= $curr_meet->pertemuan; ?>: <?= htmlspecialchars($topik_pertemuan); ?>
                    </h5>
                    <div class="text-muted small">
                        <i class="fa fa-calendar text-primary mr-1"></i> <?= tgl_indo($curr_meet->tanggal_rencana); ?> (<?= substr($curr_meet->jam_mulai,0,5); ?> - <?= substr($curr_meet->jam_selesai,0,5); ?>)
                        <span class="mx-2">•</span>
                        <i class="fa fa-clock-o text-info mr-1"></i> <?= $menit_t ?? 50; ?> Menit Teori / <?= $menit_p ?? 100; ?> Menit Praktik
                    </div>
                </div>
                <div class="mt-2 mt-sm-0">
                    <?php if (!empty($curr_meet->is_realisasi)): ?>
                        <span class="badge badge-success px-2 py-1"><i class="fa fa-check"></i> Terealisasi (<?= tgl_indo(date('Y-m-d', strtotime($curr_meet->tanggal_realisasi))); ?>)</span>
                    <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1"><i class="fa fa-clock-o"></i> Terencana</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-4 bg-white">
                
                <!-- Sub-CPMK & Metode Cards -->
                <div class="row mb-3">
                    <?php if (!empty($sub_cpmk) && $sub_cpmk != '-'): ?>
                    <div class="col-md-7 mb-2 mb-md-0">
                        <div class="p-3 rounded bg-light border" style="font-size: 12.5px;">
                            <strong class="text-danger"><i class="fa fa-bullseye mr-1"></i> Target Sub-CPMK:</strong>
                            <div class="text-dark mt-1"><?= nl2br(htmlspecialchars($sub_cpmk)); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-5">
                        <div class="p-3 rounded bg-light border" style="font-size: 12.5px;">
                            <strong class="text-info"><i class="fa fa-cogs mr-1"></i> Bentuk & Metode Pembelajaran:</strong>
                            <div class="text-dark mt-1 font-weight-bold"><?= htmlspecialchars($metode_pembelajaran ?: 'Kuliah Interaktif, Diskusi Kelompok'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Skenario Strategi Pembelajaran (SCL) -->
                <div class="mb-3">
                    <h6 class="font-weight-bold text-dark mb-2" style="font-size: 14px;">
                        🎯 Skenario Strategi Pembelajaran (SCL):
                    </h6>
                    <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb;">
                        <?= render_scl_scenario_cockpit_html_meet($skenario_scl); ?>
                    </div>
                </div>

                <!-- Draft Evaluasi Pertemuan -->
                <?php if (!empty($draf_bap) && $draf_bap != '-'): ?>
                <div class="mt-3 pt-3 border-top">
                    <h6 class="font-weight-bold text-success mb-2" style="font-size: 13.5px;">
                        📝 Draft Evaluasi Pertemuan:
                    </h6>
                    <div class="p-3 rounded bg-light" style="border: 1px solid #e2e8f0; border-left: 4px solid #22c55e; font-size: 12.5px; line-height: 1.6; color: #1e293b;">
                        "<?= nl2br(htmlspecialchars($draf_bap)); ?>"
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                $section = $this->Meet_model->get_section('course', $course_id)->row_array();
                ?>
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="row">
                            <?php
                            $lesson_counter = 0;
                            $quiz_counter   = 0;
                            if ($this->session->userdata('photo') <> 'no_photo.jpg') {
                                $p = $this->session->userdata('photo') . '.PNG';
                            } else {
                                $p = $this->session->userdata('photo');
                            }

                            ?>
                            <div class="col-md-12 col-lg-12 col-xlg-4">
                                <div class="card card-body bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-3 text-center">
                                            <a href="javascript:void(0)"><img src=<?= base_url("/assets/image_web/" . $p); ?> alt="user" class="img-circle img-fluid"></a>
                                        </div>
                                        <div class="col-md-8 col-lg-9">
                                            <?php 
                                                $meet_lbl = !empty($section['pertemuan']) ? 'Pertemuan ' . $section['pertemuan'] . ': ' : '';
                                                $topic_lbl = !empty($section['bacaan_materi']) && trim($section['bacaan_materi']) !== '-' ? trim($section['bacaan_materi']) : (!empty($section['berita_acara_rencana']) ? trim($section['berita_acara_rencana']) : (!empty($section['sub_cpmk']) ? trim($section['sub_cpmk']) : ''));
                                                $topic_lbl = preg_replace('/Aktivitas Perkuliahan\s*:\s*\(\s*\)/i', '', $topic_lbl);
                                                $topic_lbl = preg_replace('/^Aktivitas Perkuliahan\s*:\s*/i', '', $topic_lbl);
                                                $disp_section = !empty($topic_lbl) ? $meet_lbl . $topic_lbl : (!empty($section['pertemuan']) ? 'Pertemuan ' . $section['pertemuan'] : 'Sesi Perkuliahan');
                                            ?>
                                            <h4 class="box-title m-b-10 card-title"><?php echo htmlspecialchars($disp_section); ?></h4>
                                            <h5>By : <?= $this->session->userdata('nm_ptk'); ?></h5>
                                            <a href="javascript:void(0)" class="btn btn-info" onclick="showAjaxModal('<?php echo site_url('modal/popup/lesson_types/' . $course_id); ?>', 'Add New Lesson')"><i class="mdi mdi-plus"></i> Add Lesson</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">

                                <div class="card bg-light text-seconday on-hover-action mb-5" id="section-<?php echo $course_id; ?>">
                                    <div class="card-body row">

                                        <?php
                                        $lessons = $this->Meet_model->get_lessons('section', $course_id)->result_array();
                                        foreach ($lessons as $index => $lesson) : ?>
                                            <div class="col-md-12">

                                                <!-- <div class="card text-secondary on-hover-action mb-2" id = "<?php                                                                                                                     ?>">
                                <div class="card-body thinner-card-body">
                                    <div class="card-widgets display-none" id = "widgets-of-lesson-<?php                                                                                                     ?>">
                                        <?php                                         ?>
                                            <a href="javascript::" onclick="showLargeModal('<?php                                                                                             ?>', 'Manage quiz section')"><i class="mdi mdi-comment-question-outline"></i></a>
                                            <a href="javascript::" onclick="showAjaxModal('<?php                                                                                             ?>', 'Update quiz info')"><i class="mdi mdi-pencil-outline"></i></a>
                                        <?php                                         ?>
                                            <a href="javascript::" onclick="showAjaxModal('<?php                                                                                             ?>', 'Update lesson')"><i class="mdi mdi-pencil-outline"></i></a>
                                        <?php                                         ?>
                                        <a href="javascript::" onclick="confirm_modal('<?php                                                                                         ?>');"><i class="mdi mdi-window-close"></i></a>
                                    </div>
                                    <h5 class="card-title mb-0">
                                        <span class="font-weight-light">
                                            <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?>
                                            <img src="<?php                                                         ?>" alt="" height = "16">
                                            <?php                                             ?>
                                        </span>: <?php                                                     ?>
                                    </h5>
                                </div>
                            </div> -->
                                                <?php
                                                $lesson_counter++;

                                                if ($lesson['attachment_type'] == 'txt' || $lesson['attachment_type'] == 'pdf' || $lesson['attachment_type'] == 'doc' || $lesson['attachment_type'] == 'img') {
                                                    $lesson_type = $lesson['attachment_type'];
                                                } else {
                                                    $lesson_type = 'video';
                                                }
                                                ?>
                                                <div class="col-md-12">
                                                    <div class="card border-info">
                                                        <!-- <div class="card-header bg-info">
                                        <h4 class="m-b-0 text-white">Lesson : <?= $lesson_counter; ?></h4></div> -->
                                                        <div class="card-body">
                                                            <img src="<?php echo base_url('assets/backend/lesson_icon/' . $lesson_type . '.png'); ?>" alt="" class="m-b-5" height="16">
                                                            <h5 class="card-title"><?php echo 'Lesson ' . $lesson_counter . ' : ' . $lesson['title']; ?></h5>

                                                            <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-primary" onclick="showAjaxModal('<?php echo site_url('modal/popup/lesson_edit/' . $lesson['id'] . '/' . $course_id); ?>', 'Update lesson')"><i class="fas fa-pencil-alt"></i> Update Lesson</a>
                                                            <a href="javascript::" class="btn waves-effect waves-light btn-outline-danger" onclick="confirm_modal('<?php echo site_url('C_meet/lessons/' . $course_id . '/delete' . '/' . $lesson['id']); ?>');"><i class="mdi mdi-window-close"></i> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


