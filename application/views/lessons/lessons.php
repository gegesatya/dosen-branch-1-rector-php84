<?php
$course_details = $this->Meet_model->get_course_by_id($course_id)->row_array();

if (isset($bundle_id) && $bundle_id > 0) :
    $my_course_url = strtolower($this->session->userdata('role')) == 'user' ? site_url('home/my_bundles') : 'javascript::';
    $btn_title = 'my_bundles';
else :
    $my_course_url = strtolower($this->session->userdata('role')) == 'user' ? site_url('home/my_courses') : 'javascript::';
    $btn_title = 'my_courses';
endif;

?>
<div class="container-fluid course_container">
    
    <div class="row">
        <div class="col-md-12 col-lg-7 col-xl-9 course_header_col">
            <h5>
                <img src="<?php echo base_url('uploads/system/') . get_frontend_settings('small_logo'); ?>" height="25"> |
                <?php echo excerpt($course_details['berita_acara_rencana'], 80); ?>
            </h5>
        </div>
        <div class="col-md-12 col-lg-5 col-xl-3 course_header_col text-right">
            <a href="javascript::" class="course_btn" onclick="toggle_lesson_view()"><i class="fa fa-arrows-alt-h"></i></a>
            <!-- <a href="<?php echo $my_course_url; ?>" class="course_btn"> <i class="fa fa-chevron-left"></i> <?php echo get_phrase($btn_title); ?></a>
            <a href="<?php echo $course_details_url; ?>" class="course_btn"><?php echo get_phrase('course_details'); ?> <i class="fa fa-chevron-right"></i></a> -->
        </div>
    </div>

    <div class="row" id="lesson-container">
        <?php if (isset($lesson_id) == true || isset($scorm_curriculum) == true) : ?>
            
            <?php include 'general_course_content_body.php'; ?>
            
        <?php endif; ?>

        
        <?php         ?>
        <?php include 'course_content_sidebar.php'; ?>
        <?php         ?>
        
    </div>


    <div class="row my-4">
        <div class="col-lg-9">
            <div class="row justify-content-center">

            </div>
        </div>
    </div>
</div>