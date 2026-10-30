<div class="col-lg-3 mt-5 order-md-2 course_col hidden" id="lesson_list_loader" style="text-align: center;">
    <img src="<?php echo base_url('assets/backend/images/loader.gif'); ?>" alt="" height="50" width="50">
</div>
<div class="col-lg-3  order-md-2 course_col" id="lesson_list_area">
    <div class="text-center" style="margin: 12px 10px;">
        <h5><?php echo 'Chapter Content'; ?></h5>
    </div>
    <div class="row" style="margin: 12px -1px">
        <div class="col-12">
            <ul class="nav nav-tabs" id="lessonTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="section_and_lessons-tab" data-bs-toggle="tab" href="#section_and_lessons" role="tab" aria-controls="section_and_lessons" aria-selected="true"><?php echo get_phrase('Lessons') ?></a>
                </li>

            </ul>
            <div class="tab-content" id="lessonTabContent">
                <div class="tab-pane fade show active" id="section_and_lessons" role="tabpanel" aria-labelledby="section_and_lessons-tab">
                    
                    <div class="accordion" id="accordionExample">
                        <?php
                        $opened_section_id = 0;
                        foreach ($sections as $key => $section) :
                            $lessons = $this->Meet_model->get_lessons('section', $section['uniq'])->result_array(); ?>
                            <div class="card" style="margin:0px 0px;">
                                <div class="card-header course_card" id="<?php echo 'heading-' . $section['uniq']; ?>">

                                    <button class="btn btn-link w-100 text-start d-grid" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo '#collapse-' . $section['uniq']; ?>" <?php if ($opened_section_id == $section['uniq']) : ?> aria-expanded="true" <?php else : ?> aria-expanded="false" <?php endif; ?> aria-controls="<?php echo 'collapse-' . $section['uniq']; ?>" style="color: #535a66; background: none; border: none; white-space: normal;" onclick="toggleAccordionIcon(this, '<?php echo $section['uniq']; ?>')">
                                        <p class="w-100" style="color: #959aa2; font-size: 13px;">
                                            <span class="d-block-inline float-start"><?php echo 'Chapter ' . ($key + 1); ?></span>
                                            <span style="float: right; font-weight: 100;" class="accordion_icon" id="accordion_icon_<?php echo $section['uniq']; ?>">
                                                <?php if ($opened_section_id == $section['uniq']) : ?>
                                                    <i class="fa fa-minus"></i>
                                                <?php else : ?>
                                                    <i class="fa fa-plus"></i>
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                        <p class="d-inline-block float-start text-start text-13px fw-500"><?php echo $section['berita_acara_rencana']; ?></p>
                                    </button>
                                </div>

                                <div id="<?php echo 'collapse-' . $section['uniq']; ?>" class="collapse <?php if ($section_id == $section['uniq']) echo 'show'; ?>" aria-labelledby="<?php echo 'heading-' . $section['uniq']; ?>" data-parent="#accordionExample">
                                    <div class="card-body" style="padding:0px;">
                                        <table style="width: 100%;">
                                            <?php foreach ($lessons as $key => $lesson) : ?>
                                                <?php
                                                if (isset($bundle_id) && $bundle_id > 0) :
                                                    $lesson_url = site_url('addons/course_bundles/lesson/' . rawurlencode(slugify($course_details['berita_acara_rencana'])) . '/' . $bundle_id . '/' . $course_id . '/' . $lesson['id']);
                                                else :
                                                    $lesson_url = site_url('C_meet/lesson/' . slugify($course_details['berita_acara_rencana']) . '/' . $course_id . '/' . $lesson['id']);
                                                endif;
                                                ?>

                                                <tr style="width: 100%; padding: 5px 0px;background-color: <?php if ($lesson_id == $lesson['id']) echo '#E6F2F5';
                                                                                                            else echo '#fff'; ?>;">
                                                    <td style="text-align: left; padding:7px 10px;">
                                                        <?php
                                                        $lesson_progress = lesson_progress($lesson['id']);
                                                        ?>
                                                        <div class="form-group">
                                                            <input type="checkbox" id="<?php echo $lesson['id']; ?>" onchange="markThisLessonAsCompleted(this.id);" value=1 <?php if ($lesson_progress == 1) : ?> checked <?php endif; ?>>
                                                            <label for="<?php echo $lesson['id']; ?>"></label>
                                                        </div>

                                                        <a href="<?= $lesson_url; ?>" id="<?php echo $lesson['id']; ?>" style="color: #444549;font-size: 14px;font-weight: 400;">
                                                            <?php echo $key + 1; ?>:
                                                            <?php if ($lesson['lesson_type'] != 'other') : ?>
                                                                <?php echo $lesson['title']; ?>
                                                            <?php else : ?>
                                                                <?php echo $lesson['title']; ?>
                                                                
                                                            <?php endif; ?>
                                                        </a>

                                                        <div class="lesson_duration">
                                                            <?php if ($lesson['lesson_type'] == 'video' || $lesson['lesson_type'] == '' || $lesson['lesson_type'] == NULL) : ?>
                                                                <?php                                                                 ?>
                                                                <i class="far fa-play-circle"></i>
                                                                <?php echo readable_time_for_humans($lesson['duration']); ?>
                                                            <?php elseif ($lesson['lesson_type'] == 'quiz') : ?>
                                                                <i class="far fa-question-circle"></i> <?php echo get_phrase('quiz'); ?>

                                                            <?php elseif ($lesson_details['lesson_type'] == 'text' && $lesson_details['attachment_type'] == 'description') : ?>
                                                                <i class="far fa-file-alt"></i> <?php echo get_phrase('text'); ?>

                                                            <?php else : ?>
                                                                <?php if ($lesson['attachment_type'] == 'iframe') : ?>
                                                                    <i class="fas fa-code"></i> <?php echo get_phrase('external_source'); ?>
                                                                <?php else : ?>
                                                                    <?php $tmp           = explode('.', $lesson['attachment']);
                                                                    $fileExtension = strtolower(end($tmp)); ?>
                                                                    <?php if ($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png' || $fileExtension == 'bmp' || $fileExtension == 'svg') : ?>
                                                                        <i class="fas fa-camera-retro"></i> <?php echo get_phrase('attachment'); ?>
                                                                    <?php elseif ($fileExtension == 'pdf') : ?>
                                                                        <i class="far fa-file-pdf"></i> <?php echo get_phrase('attachment'); ?>
                                                                    <?php elseif ($fileExtension == 'doc' || $fileExtension == 'docx') : ?>
                                                                        <i class="far fa-file-word"></i> <?php echo get_phrase('attachment'); ?>
                                                                    <?php elseif ($fileExtension == 'txt') : ?>
                                                                        <i class="far fa-file-alt"></i> <?php echo get_phrase('attachment'); ?>
                                                                    <?php else : ?>
                                                                        <i class="fa fa-file"></i> <?php echo get_phrase('attachment'); ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
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
<script type="text/javascript">
    $(document).ready(function() {
        //checkCertificateEligibility();
    });

    function toggleAccordionIcon(elem, section_id) {
        var accordion_section_ids = [];
        $(".accordion_icon").each(function() {
            accordion_section_ids.push(this.id);
        });
        accordion_section_ids.forEach(function(item) {
            if (item === 'accordion_icon_' + section_id) {
                if ($('#' + item).html().trim() === '<i class="fa fa-plus"></i>') {
                    $('#' + item).html('<i class="fa fa-minus"></i>')
                } else {
                    $('#' + item).html('<i class="fa fa-plus"></i>')
                }
            } else {
                $('#' + item).html('<i class="fa fa-plus"></i>')
            }
        });
    }

    function checkCertificateEligibility() {
        $('#lesson_list_area').hide();
        $('#lesson_list_loader').show();
        $.ajax({
            url: '<?php echo site_url('addons/certificate/check_certificate_eligibility/' . $course_id); ?>',
            success: function(response) {
                if (parseInt(response) === 1) {
                    $('#download_certificate_area').show();
                    $('#certificate-alert-success').show();
                    $('#certificate-alert-warning').hide();

                }
                checkCourseProgression();
                getCertificateShareableUrl();

                $('#lesson_list_area').show();
                $('#lesson_list_loader').hide();
            }
        });
    }

    function checkCourseProgression() {
        $.ajax({
            url: '<?php echo site_url('home/check_course_progress/' . $course_id); ?>',
            success: function(response) {
                if (parseInt(response) === 100) {
                    $('#download_certificate_area').show();
                    $('#certificate-alert-success').show();
                    $('#certificate-alert-warning').hide();
                } else {
                    $('#download_certificate_area').hide();
                    $('#certificate-alert-success').hide();
                    $('#certificate-alert-warning').show();
                }
                $('#progression').text(Math.round(response));
                $('#course_progress_area').attr('data-percent', Math.round(response));
                initProgressBar(Math.round(response));
            }
        });
    }

    function initProgressBar(dataPercent) {
        console.log("Data Percent" + dataPercent);
        var totalProgress, progress;
        const circles = document.querySelectorAll('.circular-progress');
        for (var i = 0; i < circles.length; i++) {
            totalProgress = circles[i].querySelector('circle').getAttribute('stroke-dasharray');
            //progress = circles[i].parentElement.getAttribute('data-percent');
            progress = dataPercent;

            circles[i].querySelector('.bar').style['stroke-dashoffset'] = totalProgress * progress / 100;
        }
    }

    function getCertificateShareableUrl() {
        var user_id = '<?php echo $this->session->userdata('user_id'); ?>';
        var course_id = '<?php echo $course_id; ?>';
        $.ajax({
            url: '<?php echo site_url('addons/certificate/get_certificate_url'); ?>',
            type: 'POST',
            data: {
                user_id: user_id,
                course_id: course_id
            },
            success: function(response) {
                $('#certificate_download_btn').attr('href', response);
            }
        });
    }

    function sendCourseCompletionMail() {
        var user_id = '<?php echo $this->session->userdata('user_id'); ?>';
        var course_id = '<?php echo $course_id; ?>';
        $.ajax({
            url: '<?php echo site_url('addons/certificate/send_course_completion_mail'); ?>',
            type: 'POST',
            data: {
                user_id: user_id,
                course_id: course_id
            },
            success: function(response) {
                console.log(response);
            }
        });
    }
</script>