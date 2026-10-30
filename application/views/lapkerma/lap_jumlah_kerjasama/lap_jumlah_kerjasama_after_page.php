<script type="text/javascript">
    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function sinkron_data(id) {


        $.ajax({
            url: "<?php echo site_url('C_lap_jumlah_kerjasama/sinkron') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                swalert('disinkronisasi');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>