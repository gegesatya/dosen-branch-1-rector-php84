<script src="<?= base_url('assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.summernote-review').summernote({
            placeholder: 'Tuliskan review atau umpan balik Anda di sini...',
            tabsize: 2,
            height: 180,
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ol', 'ul']],
            ]
        });

        $('#btn_ai_review').click(function() {
            var id_tracking = $('[name="id_tracking"]').val();
            if(!id_tracking) return;
            
            var btn = $(this);
            btn.html('<i class="fa fa-spinner fa-spin"></i> Menganalisis...').attr('disabled', true);
            $('#ai_review_feedback_box').slideUp();

            $.ajax({
                url: "<?php echo site_url('C_review_laporan/ajax_ai_review') ?>",
                type: "POST",
                data: {
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
                    id_tracking: id_tracking
                },
                dataType: "JSON",
                success: function(response) {
                    btn.html('<i class="fa fa-magic"></i> AI Review Laporan').attr('disabled', false);
                    if(response.status) {
                        var data = response.data;
                        $('#ai_score_badge').text('Skor: ' + (data.skor_kelengkapan || '-'));
                        $('#ai_evaluasi_analisis').text(data.evaluasi_analisis || '-');
                        $('#ai_feedback_points').html(data.feedback_html || '-');
                        $('#ai_rekomendasi_review').text(data.rekomendasi_review || '-');
                        
                        $('#ai_review_feedback_box').slideDown();
                    } else {
                        Swal({
                            title: 'Gagal',
                            text: response.msg || 'Terjadi kesalahan saat melakukan AI Review.',
                            type: 'error'
                        });
                    }
                },
                error: function() {
                    btn.html('<i class="fa fa-magic"></i> AI Review Laporan').attr('disabled', false);
                    Swal({
                        title: 'Error',
                        text: 'Gagal menghubungi server.',
                        type: 'error'
                    });
                }
            });
        });

        $('#btn_apply_ai_review').click(function() {
            var text = $('#ai_rekomendasi_review').text();
            var rawScore = $('#ai_score_badge').text().replace('Skor: ', '').trim();
            
            if(text && text !== '-') {
                var finalHtml = '<p>' + text + '</p>';
                if(rawScore && rawScore !== '-' && !isNaN(rawScore)) {
                    finalHtml += '<p><strong>Nilai Kelengkapan Laporan: ' + rawScore + '/100</strong></p>';
                }
                
                $('#review_dpl').summernote('code', finalHtml);
                Swal({
                    title: 'Berhasil',
                    text: 'Draf review beserta skor telah diterapkan ke editor.',
                    type: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
</script>
