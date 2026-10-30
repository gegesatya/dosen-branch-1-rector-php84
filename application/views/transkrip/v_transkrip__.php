    <br />
    <div class="col-md-10 col-sm-10 col-xs-10 col-md-offset-1 col-sm-offset-1 col-xs-offset-1">
        <div class="table-responsive">
            <table class="table table-borderless">
                <tr>
                    <td>Nama</td><td style="width:10px">:</td><td><?=$mahasiswa->nm_pd;?></td>
                </tr>
                <tr>
                    <td>NIM</td><td>:</td><td><?=$mahasiswa->nipd;?></td>
                </tr>
            </table>
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
        </div>
    </div>