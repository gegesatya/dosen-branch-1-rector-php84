


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"><?= $judul; ?></h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active"><?= $judul; ?></li>
            </ol>
            <button type="button" class="btn btn-info d-none d-lg-block m-l-15" onclick="add_bidang_kerjasama()"><i class="fa fa-plus-circle"></i> Add</button>
            <button class="btn btn-default  d-none d-lg-block m-l-15" type="button" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
        </div>
    </div>
</div>






<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-stripped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 20px">No.</th>
                                <th>Kode Kerjasama</th>
                                <th>Nama Kerjasama</th>
                                <th style="width: 150px">Action</th>
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


