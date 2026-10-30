


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h5 class="text-themecolor"><?= $judul_besar; ?></h5>
        <?= $judul_kecil; ?>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <!-- <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active"><?= $judul; ?></li>
            </ol> -->
            
            <button class="btn btn-default  d-none d-lg-block m-l-15" type="button" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
        </div>
    </div>
</div>






<div class="row">
    
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="round align-self-center round-success"><i class="ti-wallet"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0"><?=$i_realisasi;?> / <?=$i_pertemuan;?></h3>
                        <h5 class="text-muted m-b-0">Realisasi Pertemuan</h5></div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="round align-self-center round-info"><i class="ti-user"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0"><?=$jumlah_mahasiswa;?></h3>
                        <h5 class="text-muted m-b-0">Jumlah Mahasiswa</h5></div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="round align-self-center round-danger"><i class="ti-calendar"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0"><?=$next_meet;?></h3>
                        <h5 class="text-muted m-b-0">Next Meet</h5></div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="round align-self-center round-success"><i class="ti-settings"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0"><?=$skema;?></h3>
                        <h5 class="text-muted m-b-0">Skema Nilai</h5></div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<div class="row">
    
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                    <thead>
                      <tr role="row" class="heading">
                        <th>W/M</th>
                        <th>Tanggal | Jam <br> Bahasan | Referensi</th>
                        <th>Is Realisasi</th>
                        <th>Tgl Realisasi</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>


