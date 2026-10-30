<script type="text/javascript">
    var save_method;

    $(document).ready(function() {
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $('.summernote').summernote({
            placeholder: '',
            tabsize: 2,
            height: 150,
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
            ]
        });

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            //if (save_method == 'add') {
            url = "<?php echo site_url('C_rps_obsolete/ajax_add') ?>";
            // } else {
            //url = "<?php echo site_url('C_rps_obsolete/ajax_update') ?>";
            //}

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);
                    window.location.href = data.redirect;

                }
            });
        });

    });

    function add_rps() {
        save_method = 'add';
        $('#form')[0].reset();
        $('[name="deskripsi_mata_kuliah"]').summernote("code", '');
        $('[name="capaian_pembelajaran_mata_kuliah"]').summernote("code", '');
        $('[name="referensi"]').summernote("code", '');
        $('[name="cpl"]').summernote("code", '');
        $('[name="tujuan_belajar"]').summernote("code", '');

        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Pengusulan RPS');
    }

    function edit_rps(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_rps_obsolete/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('[name="id_cakupan_rps"]').val(data.id_cakupan_rps);
                $('[name="tanggal_rps"]').val(data.tanggal_rps);
                $('[name="nm_mk"]').val(data.nm_mk);
                //$('[name="tipe_usulan"]').val(data.tipe_usulan);
                $('[name="deskripsi_mata_kuliah"]').summernote("code", data.deskripsi_mata_kuliah);
                $('[name="capaian_pembelajaran_mata_kuliah"]').summernote("code", data.capaian_pembelajaran_mata_kuliah);
                $('[name="referensi"]').summernote("code", data.referensi);
                $('[name="cpl"]').summernote("code", data.cpl);
                $('[name="tujuan_belajar"]').summernote("code", data.tujuan_belajar);

                $('#modal_form').modal('show');
                $('.modal-title').text("Pengusulan RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>