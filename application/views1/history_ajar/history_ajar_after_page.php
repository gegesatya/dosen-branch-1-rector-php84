<script type="text/javascript">
    function ajax_download(id_smt) {
        $.ajax({
            url: "<?php echo site_url('C_history_ajar/get_journey_by_smt') ?>" + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#tabel').html(data.tabel);

                $('#modal_form_download').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Perkuliahan Tahun " + id_smt);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function ajax_download_2(id_smt) {
        $.ajax({
            url: "<?php echo site_url('C_history_ajar_12345/get_journey_by_smt') ?>" + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#tabel').html(data.tabel);

                $('#modal_form_download').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Perkuliahan Tahun " + id_smt);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade" id="modal_form_download" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_download'];
                echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    <div id="tabel"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>