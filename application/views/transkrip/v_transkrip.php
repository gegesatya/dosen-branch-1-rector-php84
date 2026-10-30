<?php
    $stat_pd = $mahasiswa->stat_pd;
    $id_sms = $mahasiswa->id_sms;

    if(!empty($stat_pd)){
        $nm_stat_mhs = $this->db->where('id_stat_mhs', $stat_pd)->get('status_mahasiswa')->row()->nm_stat_mhs;
    }
    $nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;
?>
    <div class="row clearfix">
        <div class="col-md-4">
            <b>Nomor Induk Mahasiswa</b>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-tags"></i>
                </span>
                <div class="form-line">
                    <input class="form-control" type="text" value="<?=$mahasiswa->nipd;?>">
                </div>
                
            </div>
        </div>
        <div class="col-md-4">
            <b>Nama Mahasiswa</b>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <div class="form-line">
                    <input class="form-control" type="text" value="<?=!empty($mahasiswa->nm_pd) ? $mahasiswa->nm_pd : '-';?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <b>No Handphone</b>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                <div class="form-line">
                    <input class="form-control" type="text" value="<?=!empty($mahasiswa->telepon_seluler) ? $mahasiswa->telepon_seluler : '-';?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-md-4">
            <b>Program Studi</b>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-cube"></i>
                </span>
                <div class="form-line">
                    <input class="form-control" type="text" value="<?=$nm_lemb;?>">
                </div>
                
            </div>
        </div>
        <div class="col-md-4">
            <b>Status Mahasiswa</b>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-flag"></i>
                </span>
                <div class="form-line">
                    <input class="form-control" type="text" value="<?=!empty($nm_stat_mhs) ? $nm_stat_mhs : '-';?>">
                </div>
            </div>
        </div>

    </div>


    <br />
    <table id="table" class="table table-condensed table-black">
        <thead>
            <tr>
                <th>#</th>
                <th>Kode MK</th>
                <th>Nama MK</th>
                <th>Tahun Akademik</th>
                <th class="text-center">SKS</th>
                <th class="text-center">Nilai</th>
                <th class="text-center">Index</th>
                <th class="text-center">SKS*Indeks</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($transkrip as $key => $value) {
                    ?>
                        <tr>
                            <td class="text-right"><?=$value['nomor'];?></td>
                            <td><?=$value['kode_mk'];?></td>
                            <td><?=$value['nm_mk'];?></td>
                            <td><?=$value['id_smt'];?></td>
                            <td class="text-center"><?=$value['sks_mk'];?></td>
                            <td class="text-center"><?=$value['nilai_huruf'];?></td>
                            <td class="text-center"><?=$value['nilai_indeks'];?></td>
                            <td class="text-center"><?=$value['sks_indeks'];?></td>
                        </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
