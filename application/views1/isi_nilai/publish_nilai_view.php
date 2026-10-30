<?php
    $checked_1 = $checked_2 = $checked_3 = $checked_4 ='';

    if($list_detail_kelas->is_publish == 1){
       $checked_1 = 'checked';
    }elseif($list_detail_kelas->is_publish == 2){
      $checked_2 = 'checked';
    }elseif($list_detail_kelas->is_publish == 3){
      $checked_3 = 'checked';
    }elseif($list_detail_kelas->is_publish == 4){
      $checked_4 = 'checked';
    }

    if($list_detail_kelas->status_isi_komponen_nilai == 1){
      ?>
      <section class="content">
      
        <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-files-o"></i> Kelas Kuliah [<?=$this->session->userdata('bc_nm_kls');?>] <?=$this->session->userdata('title_nm_mk');?></h3>
            </div>
            <?php echo form_open(site_url("c_publish_nilai/simpan"), "role='form'");?>
              <div class="box-body">
                <div class="radio">
                    <label style="color: #000">
                      <input type="radio" name="is_publish" id="option1" value="1" <?=$checked_1;?>> <span class="circle"></span><span class="check"></span>
                      Tampilkan Nilai ke Mahasiswa (DEFAULT)
                      <p>Pilih opsi ini jika proses penilaian sudah selesai.</p>
                    </label>
                  </div>
                  <div class="radio">
                    <label style="color: #000">
                      <input type="radio" name="is_publish" id="option2" value="2" <?=$checked_2;?>><span class="circle"></span><span class="check"></span>
                      Jangan tampilkan Nilai ke Mahasiswa
                      <p>Pilih opsi ini jika penilaian masih belum final dan masih dalam proses.</p>
                    </label>
                  </div>
              </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            <?php echo form_close(); ?> 
          </div>

<!--           <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-files-o"></i> Preview Nilai (Yang dilihat oleh Mahasiswa)</h3>
            </div>
              <div class="box-body">
               <div class="table-responsive">
                <table class="table table-bordered table-center-th" style="font-size: 12px !important">
                  <thead>
                    <tr>
                      <th rowspan="3">No</th>
                      <th rowspan="3">NIM</th>
                      <th rowspan="3">NAMA</th>
                      <th rowspan="2">Sikap</th>
                      <th colspan="4">SUB PENGETAHUAN</th>
                      <th rowspan="2">N PENGETAHUAN</th>
                      <th colspan="5">SUB KETERAMPILAN</th>
                      <th rowspan="2">N KETERAMPILAN</th>
                      <th rowspan="3">N FINAL</th>
                      <th rowspan="3">N HURUF</th>
                      <th rowspan="3">INDEX</th>
                    </tr>
                    <tr>
                      <th>QUI</th>
                      <th>TTS</th>
                      <th>UTS</th>
                      <th>UAS</th>
                      <th>UKN</th>
                      <th>PRY</th>
                      <th>POR</th>
                      <th>PRD</th>
                      <th>PRE</th>
                    </tr>
                    <tr>
                      
                      <th><?=$bobot_sikap;?> %</th>
                      <th><?=$bobot_pengetahuan_quiz;?> %</th>
                      <th><?=$bobot_pengetahuan_tugas_terstruktur;?> %</th>
                      <th><?=$bobot_pengetahuan_uts;?> %</th>
                      <th><?=$bobot_pengetahuan_uas;?> %</th>
                      <th><?=$bobot_pengetahuan;?> %</th>
                      <th><?=$bobot_keterampilan_unjuk_kinerja;?> %</th>
                      <th><?=$bobot_keterampilan_proyek;?> %</th>
                      <th><?=$bobot_keterampilan_portofolio;?> %</th>
                      <th><?=$bobot_keterampilan_produk;?> %</th>
                      <th><?=$bobot_keterampilan_presentasi;?> %</th>
                      <th><?=$bobot_keterampilan;?> %</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $no = 1;

                      foreach ($data_nilai as $key => $value) {
                        ?>
                          <tr>
                            <td><?=$no;?></td>
                            <td><?=$value->nipd;?></td>
                            <td><?=$value->nm_pd;?></td>
                            <td><?=$value->n_sikap_2;?></td>
                            <td><?=$value->n_pengetahuan_2_quiz;?></td>
                            <td><?=$value->n_pengetahuan_2_tugas_terstruktur;?></td>
                            <td><?=$value->n_pengetahuan_2_uts;?></td>
                            <td><?=$value->n_pengetahuan_2_uas;?></td>
                            <td><?=$value->n_pengetahuan_2;?></td>
                            <td><?=$value->n_keterampilan_2_unjuk_kinerja;?></td>
                            <td><?=$value->n_keterampilan_2_proyek;?></td>
                            <td><?=$value->n_keterampilan_2_portofolio;?></td>
                            <td><?=$value->n_keterampilan_2_produk;?></td>
                            <td><?=$value->n_keterampilan_2_presentasi;?></td>
                            <td><?=$value->n_keterampilan_2;?></td>
                            <td><?=$value->nilai_angka;?></td>
                            <td><?=$value->nilai_huruf;?></td>
                            <td><?=$value->nilai_indeks;?></td>
                          </tr>
                        <?php
                        $no++;
                      }
                    ?>
                  </tbody>
                </table>
               </div>                
              </div>
              <div class="box-footer">
                
              </div>
          </div> -->
      </section>
      <?php
    }else{
      ?>
      <section class="content">
        <div class="col-md-12">
          Skema Komposisi Nilai belum diisi. Silahkan mengisi skema komposisi nilai terlebih dahulu.
        </div>
      </section>
      <?php
    }
?>