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
                        
                        if (data.progres_kompetensi_html && data.progres_kompetensi_html.trim() !== '') {
                            $('#ai_progres_kompetensi').html(data.progres_kompetensi_html);
                            $('#ai_progres_container').slideDown();
                        } else {
                            $('#ai_progres_container').hide();
                        }
                        
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
                error: function(jqXHR, textStatus, errorThrown) {
                    btn.html('<i class="fa fa-magic"></i> AI Review Laporan').attr('disabled', false);
                    if (jqXHR.status == 401) {
                        Swal({
                            title: 'Sesi Habis',
                            text: 'Sesi Anda telah berakhir karena terlalu lama idle. Silakan login kembali.',
                            type: 'warning'
                        }).then(function() {
                            window.location.reload();
                        });
                        return;
                    }
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
            
            var progresHtml = $('#ai_progres_kompetensi').html();
            var hasProgres = $('#ai_progres_container').is(':visible');
            
            if(text && text !== '-') {
                var currentHtml = $('#review_dpl').summernote('code');
                
                if (currentHtml.indexOf(text) !== -1) {
                    Swal({
                        title: 'Info',
                        text: 'Review AI sudah dimasukkan sebelumnya.',
                        type: 'info',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return;
                }
                
                var finalHtml = currentHtml;
                var contentToAppend = '';
                
                // Bersihkan tag kosong bawaan summernote agar tidak ada newline di atas
                if(finalHtml === '<p><br></p>' || finalHtml === '<p></p>' || finalHtml.trim() === '') {
                    finalHtml = '';
                } else {
                    contentToAppend += '<br><p><strong>[Tambahan dari AI Review]</strong></p>';
                }
                
                if (hasProgres && progresHtml && progresHtml.trim() !== '') {
                    contentToAppend += '<p><strong>Evaluasi Progres Kompetensi:</strong></p>';
                    contentToAppend += progresHtml;
                }
                
                contentToAppend += '<p><strong>Review DPL:</strong><br>' + text + '</p>';
                finalHtml += contentToAppend;
                
                if(rawScore && rawScore !== '-' && !isNaN(rawScore)) {
                    $('#skor_dpl').val(rawScore);
                }
                
                $('#review_dpl').summernote('code', finalHtml);
                Swal({
                    title: 'Berhasil',
                    text: 'Review DPL beserta skor telah diterapkan ke form.',
                    type: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
</script>
