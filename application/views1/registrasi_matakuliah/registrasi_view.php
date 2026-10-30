<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number" style="font-size: 20px !important"><?=!empty($mahasiswa->nm_pd)?$mahasiswa->nm_pd:'';?></div>
                <div class="desc">Diajukan Oleh</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number" style="font-size: 20px !important"><?=!empty($mahasiswa->nipd)?$mahasiswa->nipd:'';?></div>
                <div class="desc">NIM</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number" style="font-size: 20px !important"><?=!empty($jumlah_sks_pengajuan)?$jumlah_sks_pengajuan:'';?></div>
                <div class="desc">SKS Diajukan</div>
            </div>
            
        </div>
    </div>
</div>
            
<?php
    if($fase == 3){
        ?>
        <div class="table-responsive" style="padding: 10px">
            <a type="button" class="btn btn-primary btn-sm" href="#" onclick="reload_table_registrasi()">
                <i class="fa fa-refresh "></i> REFRESH TABEL
            </a>
            <table id="table-registrasi"  class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width:8px;">#</th>
                        <th>Kode MK / Mata Kuliah</th>
                        <th>Nm Kelas / Dosen</th>
                        <th>SKS</th>
                        <th>Hari / Jam</th>
                        <th>Status</th>   
                        <th>Tgl Diajukan</th>   
                        <th>Tgl Approve</th>   
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <?php
    }else{
        ?>
        <div class="table-responsive" style="padding: 10px">
            <div class="clearfix">
                <a type="button" class="btn bg-teal waves-effect" href="#" onclick="reload_table_registrasi()">
                    <i class="material-icons">cached</i> REFRESH TABEL
                </a>
            </div>
            <table id="table-registrasi" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width:8px;">#</th>
                        <th>Kode MK / Mata Kuliah</th>
                        <th>Nm Kelas / Dosen</th>
                        <th>SKS</th>
                        <th>Hari / Jam</th>
                        <th>Status</th>   
                        <th>Tgl Diajukan</th>   
                        <th>Tgl Approve</th>   
                        <th>Action</th>   
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="clearfix text-center">
                <br>
                
                <button id="button_boleh" class="btn bg-red waves-effect" onclick="set_fase_3('<?=$mahasiswa->id_reg_pd;?>')"> <i class="material-icons">print</i>Boleh Cetak KRS dan Mengikuti Perkuliahan</button>
            </div>
        </div>
        <?php
    }
?>

<script type="text/javascript">
    // REGISTRASI KRS
    var save_method; //for save method string
    var table_registrasi;
    var disetujui;
    var diajukan;

    $(document).ready(function() {
        cek_data("<?=$id_reg_pd;?>");
        //alert("<?=$id_reg_pd;?>");

        table_registrasi = $('#table-registrasi').DataTable({ 

            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?php echo site_url('c_bimbingan_krs_dosen/validasi_ajax_list')?>"+"/"+"<?=$id_reg_pd;?>",
                "type": "POST",
                "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
                "dataType": "JSON",
            },
            "bFilter":false,
            "bPaginate":false,
            "bInfo":false,
            //Set column definition initialisation properties.
            "columnDefs": [
            { 
                "targets": [0,1,2,3,4,5,6,7, -1 ],
                "orderable": false,
            },
            ],

        });

    });

    $(document).on('change', '.mk_check', function() {
      // Does some stuff and logs the event to the console
       var id_reg_pd = $(this).attr("data-id_reg_pd");
       var id_kls = $(this).attr("data-id_kls");
       var state = $(this).is(':checked');
     
        if(state){ 
            $.post( "<?=site_url('c_bimbingan_krs/add_krs');?>"+ '/0/' + id_kls + '/' + id_reg_pd, function( data ) {
               reload_table_registrasi();
            });
        }else{
           $.post( "<?=site_url('c_bimbingan_krs/add_krs');?>"+ '/1/'+ id_kls + '/' + id_reg_pd, function( data ) {
                reload_table_registrasi();
            });
        }

    });


    function reload_table_registrasi()
    {
        table_registrasi.ajax.reload(null,false);
    }

    function delete_kelas_kuliah(id)
    {
        if(confirm('Are you sure delete this data?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo site_url('kelas_kuliah/ajax_delete')?>/"+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table_registrasi();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }

    function set_fase_3(id_reg_pd)
    {
        if(confirm('Apakah Anda telah memeriksa dan menyetujui permohonan Rencana Studi mahasiswa ini?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_set_fase_3')?>/"+id_reg_pd,
                type: "POST",
                "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
                "dataType": "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    $('#modal_bimbingan').modal('hide');
                    reload_table();
                    //window.close();
                    //window.location = document.referrer;
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error update data');
                }
            });

        }
    }

    function set_fase_2(id_reg_pd)
    {
        if(confirm('Apakah Anda Ingin Menyetujui Langkah Ini?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_set_fase_2')?>/"+id_reg_pd,
                type: "POST",
                "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
                "dataType": "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    //$('#modal_form').modal('hide');
                    //reload_table();
                    window.close();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error update data');
                }
            });

        }
    }

    function cek_data(id_reg_pd){
        $.ajax({
            url : "<?php echo site_url('c_bimbingan_krs_dosen/get_data_approve')?>/"+id_reg_pd,
            type: "POST",
            "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
            "dataType": "JSON",
            success: function(data)
            {
                //alert(data);
                diajukan = data.diajukan;
                disetujui = data.disetujui;

                if(disetujui != diajukan){
                    $('#button_boleh').hide();
                }else{
                    $('#button_boleh').show();
                }

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error update data');
            }
        });
    }

    function approve_krs(id_reg_pd, id_kls)
    {
        //if(confirm('Apakah Anda Menyetujui Pengambilan Mata Kuliah Ini?'))
       // {
            // ajax delete data to database

            $.ajax({
                url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_approve')?>/"+id_reg_pd+"/"+id_kls,
                type: "POST",
                "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
                "dataType": "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    //$('#modal_form').modal('hide');
                    cek_data(id_reg_pd);
                    reload_table_registrasi();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error update data');
                }
            });

        //}
    }

    function not_approve_krs(id_reg_pd, id_kls)
    {
        if(confirm('Anda Tidak Menyetujui Pengambilan Mata Kuliah Ini, Lanjutkan?'))
        {
            $.ajax({
                url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_not_approve')?>/"+id_reg_pd+"/"+id_kls,
                type: "POST",
                "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
                "dataType": "JSON",
                success: function(data)
                {
                    cek_data(id_reg_pd);
                    reload_table_registrasi();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error update data');
                }
            });

        }
    }

</script>