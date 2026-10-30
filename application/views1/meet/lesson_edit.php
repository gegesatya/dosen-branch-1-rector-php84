<?php
$lesson_details = $this->Meet_model->get_lessons('lesson', $param2)->row_array();
$sections = $this->Meet_model->get_section('course', $param3)->result_array();
?>

<div class="alert alert-info" role="alert">
    <?php echo "Lesson Type"; ?> :
    <strong>
        <?php
        if ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'html5') {
            echo "Video Url" . ' [.mp4]';
        } elseif ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'system') {
            echo "Video File";
        } elseif ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'youtube') {
            echo 'YouTube video';
        } elseif ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'vimeo') {
            echo 'Vimeo video';
        } elseif ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'doc' || strtolower($lesson_details['attachment_type']) == 'pdf' || strtolower($lesson_details['attachment_type']) == 'txt') {
            echo "Document";
        } elseif ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'img') {
            echo "Image";
        } elseif ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'iframe') {
            echo "iFrame";
        } elseif ($lesson_details['video_type'] == 'google_drive') {
            echo "Google Drive Video";
        } elseif ($lesson_details['lesson_type'] == 'text' && strtolower($lesson_details['attachment_type']) == 'description') {
            echo "Text";
        } elseif ($lesson_details['lesson_type'] == 'google_drive_document') {
            echo "Google Drive Document";
        }

        ?>.
    </strong>
</div>


<form action="<?php echo site_url('C_meet/lessons/' . $param3 . '/edit' . '/' . $param2); ?>" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label><?php echo "Title"; ?></label>
        <input type="text" name="title" class="form-control" required value="<?php echo $lesson_details['title']; ?>">
    </div>

    <input type="hidden" name="course_id" value="<?php echo $param3; ?>">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="form-group">
        <label for="section_id"><?php echo "Section"; ?></label>
        <select class="form-control select2" data-toggle="select2" name="section_id" id="section_id" required>
            <?php foreach ($sections as $section) : ?>
                <option value="<?php echo $section['uniq']; ?>" <?php if ($lesson_details['section_id'] == $section['uniq']) echo 'selected'; ?>><?php echo $section['berita_acara_rencana']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'youtube') : include('youtube_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'vimeo') : include('vimeo_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'html5') : include('html5_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'system') : include('video_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'video' && strtolower($lesson_details['video_type']) == 'google_drive') : include('google_drive_video_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'doc' || strtolower($lesson_details['attachment_type']) == 'pdf' || strtolower($lesson_details['attachment_type']) == 'txt') : include('document_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'text' && strtolower($lesson_details['attachment_type']) == 'description') : include('text_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'img') : include('image_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'iframe') : include('iframe_type_lesson_edit.php');
    endif; ?>
    <?php if ($lesson_details['lesson_type'] == 'other' && strtolower($lesson_details['attachment_type']) == 'gdrive_document') : include('google_drive_document_lesson_edit.php');
    endif; ?>

    <div class="form-group">
        <label><?php echo "Summary"; ?></label>
        <textarea name="summary" class="form-control"><?php echo htmlspecialchars_decode($lesson_details['summary']); ?></textarea>
    </div>



    <div class="text-center">
        <button class="btn btn-success" type="submit" name="button"><?php echo "Update Lesson"; ?></button>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        initSelect2(['#section_id', '#lesson_type', '#lesson_provider', '#lesson_provider_for_mobile_application']);
        initTimepicker();

        // HIDING THE SEARCHBOX FROM SELECT2
        $('select').select2({
            minimumResultsForSearch: -1
        });
    });

    function ajax_get_video_details(video_url) {
        $('#perloader').show();
        if (checkURLValidity(video_url)) {
            $.ajax({
                url: '<?php echo site_url('C_meet/ajax_get_video_details'); ?>',
                type: 'POST',
                data: {
                    video_url: video_url
                },
                success: function(response) {
                    jQuery('#duration').val(response);
                    $('#perloader').hide();
                    $('#invalid_url').hide();
                }
            });
        } else {
            $('#invalid_url').show();
            $('#perloader').hide();
            jQuery('#duration').val('');

        }
    }

    function checkURLValidity(video_url) {
        var youtubePregMatch = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        var vimeoPregMatch = /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
        if (video_url.match(youtubePregMatch)) {
            return true;
        } else if (vimeoPregMatch.test(video_url)) {
            return true;
        } else {
            return false;
        }
    }
</script>