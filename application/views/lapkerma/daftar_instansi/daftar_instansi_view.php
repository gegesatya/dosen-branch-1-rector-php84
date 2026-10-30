<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>

                    </div>
                </div>
                <div class="portlet-body">

                    <div class="table-container">
                        <div style="margin-bottom: 20px">
                            <button class="btn btn-default btn-sm" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
                            
                        </div>
                        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 20px">No.</th>
                                    <th>Nm Instansi</th>
                                    <th>Jenis</th>
                                    <th>Bidang Usaha</th>
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
</div>