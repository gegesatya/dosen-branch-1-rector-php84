<script>
    function show_epom(judul, tipe, id_sms, kategori) {

        /*   $('#form')[0].reset();
          $('.form-group').removeClass('has-error');
          $('.help-block').empty(); */

        $.ajax({
            url: "<?php echo site_url('C_pusat_data_analisa_epom/ajax_show_epom/') ?>" + tipe + "/" + id_sms + "/" + kategori,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="insight_1"]').html(data.tabel);
                $('#modal_form').modal('show');
                $('.modal-title').text(judul);

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
    $(document).ready(function() {
        Morris.Bar({
            element: 'graph',
            data: <?php echo $data; ?>,
            xkey: 'thn_prodi',
            ykeys: ['rerata_epom', 'rerata_epom_tetap', 'rerata_epom_tidak_tetap'],
            labels: ['EPOM All', 'EPOM Tetap', 'EPOM Tdk Tetap'],
            xLabelMargin: 5
        });
    });
</script>

<div class="modal fade in" id="modal_form" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">

                <div class="table-responsive" id="insight_1" name="insight_1"></div>
            </div>
            <div class=" modal-footer">

                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>