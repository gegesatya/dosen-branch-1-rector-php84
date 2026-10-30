


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h5 class="text-themecolor"><?= $judul_besar; ?></h5>
        <?= $judul_kecil; ?>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <a class="btn waves-effect waves-light btn-dark d-none d-lg-block m-l-10" href="<?= site_url('C_meet/preview/' . $course_id); ?>" target="_new"><i class="fas fa-tv"></i> View Frontend</a>
        </div>
    </div>
</div>






<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    <h3 class="text-info"><i class="fa fa-exclamation-circle"></i> Information</h3>
                    Fitur ini merupakan versi ALPHA. Implementasi fitur ini adalah untuk melihat dan menguji tingkat penerimaan user. Di sisi lain, ditinjau dari sudut teknologi, implementasi versi ALPHA ini juga untuk melihat kesiapan teknologi yang ada pada server.
                </div>
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
                                            <h4 class="box-title m-b-10 card-title"><?php echo $section['berita_acara_rencana']; ?></h4>
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


