<div class="page-content" style="padding: 0 0 0 20px;">
  
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Manajemen Perkuliahan</span>
            <span class="caption-helper">[<?=$this->session->userdata('nm_kls');?>] <?=$this->session->userdata('nm_mk');?></span>
          </div>
        </div>
        <div class="portlet-body">

        <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-lg">
                  <li class="active">
                    <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                    Skema Nilai </a>
                  </li>
                  <li class="">
                    <a href="#tab_2" data-toggle="tab" aria-expanded="false">
                    Rencana Pertemuan </a>
                  </li>
                  <li class="">
                    <a href="#tab_3" data-toggle="tab" aria-expanded="false">
                    Berita Acara </a>
                  </li>
                  <li class="">
                    <a href="#tab_4" data-toggle="tab" aria-expanded="false">
                    Absen </a>
                  </li>
                  <li class="">
                    <a href="#tab_5" data-toggle="tab" aria-expanded="false">
                    Tabel Kehadiran </a>
                  </li>
                  <li class="">
                    <a href="#tab_6" data-toggle="tab" aria-expanded="false">
                    Peserta </a>
                  </li>
                  <li class="">
                    <a href="#tab_7" data-toggle="tab" aria-expanded="false">
                    Nilai </a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                    <div class="row">
                      <div class="col-md-12 col-sm-12">
                        <div class="portlet blue-hoki box">
                          <div class="portlet-title">
                            <div class="caption">
                              <i class="fa fa-cogs"></i>Skema Nilai [<?=$this->session->userdata('nm_kls');?>] <?=$this->session->userdata('nm_mk');?>
                            </div>
                            <div class="actions">
                              <a href="javascript:;" onclick="get_ajax_data('<?=$this->session->userdata('id_kls');?>')" class="btn btn-default btn-sm">
                              <i class="fa fa-pencil"></i> Reload </a>
                            </div>
                          </div>
                          <div class="portlet-body">
                            
                            <form role="form" id="form-skema-nilai" class="form-horizontal">
                              <div class="form-body">
                                <div class="form-group">
                                  <label class="col-md-4 control-label">Pilih Kategori Perkuliahan</label>
                                  <div class="radio-list">
                                    <label class="radio-inline">
                                    <input type="radio" name="kategori_p" id="teori" value="t"> Teori </label>
                                    <label class="radio-inline">
                                    <input type="radio" name="kategori_p" id="teoripraktek" value="tp"> Teori + Praktik/Praktikum </label>
                                    <label class="radio-inline">
                                    <input type="radio" name="kategori_p" id="praktek" value="p"> Praktik/Praktikum </label>
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-md-4 control-label">Sikap</label>
                                  <div class="col-md-8">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Sikap" data-container="body"></i>
                                      <input name="bobot_sikap" class="form-control" required="required" value="" type="text" readonly="readonly">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 control-label">Pengetahuan</label>
                                  <div class="col-md-8">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen pengetahuan" data-container="body"></i>
                                      <input name="bobot_pengetahuan" class="form-control has-error" required="required" value="" type="text" readonly="readonly">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">a. [QUI] Quiz, pre test, post test</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Quiz" data-container="body"></i>
                                      <input name="bobot_pengetahuan_quiz" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">b. [TTS] Tugas Terstruktur</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Tugas Terstruktur" data-container="body"></i>
                                      <input name="bobot_pengetahuan_tugas_terstruktur" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">c. [UTS] Ujian Tengah Semester</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Ujian Tengah Semester" data-container="body"></i>
                                      <input name="bobot_pengetahuan_uts" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">d. [UAS] Ujian Akhir Semester</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Ujian Akhir Semester" data-container="body"></i>
                                      <input name="bobot_pengetahuan_uas" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-md-4 control-label">Keterampilan</label>
                                  <div class="col-md-8">
                                    <div class="input-icon right">
                                      <i class="fa fa-warning tooltips" data-original-title="please provide an email" data-container="body"></i>
                                      <input name="bobot_keterampilan" class="form-control" required="required" value="" type="text" readonly="readonly">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">a. [UKN] Unjuk Kinerja</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                                      <input name="bobot_keterampilan_unjuk_kinerja" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">b. [PRY] Proyek</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                                      <input name="bobot_keterampilan_proyek" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">c. [POR] Portofolio</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                                      <input name="bobot_keterampilan_portofolio" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">d. [PRD] Produk</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                                      <input name="bobot_keterampilan_produk" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-md-4 col-md-offset-4 control-label">e. [PRE] Presentasi</label>
                                  <div class="col-md-4">
                                    <div class="input-icon right">
                                      <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                                      <input name="bobot_keterampilan_presentasi" class="form-control" required="required" value="" type="number">
                                    </div>
                                  </div>
                                </div>
                                
                              </div>
                              <div class="form-actions">
                                <div class="row">
                                  <div class="col-md-offset-4 col-md-8">
                                    <button type="submit" class="btn blue">Submit</button>
                                  </div>
                                </div>
                              </div>
                            </form>
      
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="tab_2">
                    <div class="row">
                      <div class="col-md-12 col-sm-12">
                        <div class="portlet blue-hoki box">
                          <div class="portlet-title">
                            <div class="caption">
                              <i class="fa fa-cogs"></i>Rencana Pertemuan [<?=$this->session->userdata('nm_kls');?>] <?=$this->session->userdata('nm_mk');?>
                            </div>
                            <div class="actions">
                              <a href="javascript:;" onclick="reload_table()" class="btn btn-default btn-sm">
                              <i class="fa fa-pencil"></i> Reload </a>
                              <a class="btn btn-default btn-sm" href="<?=base_url('laporan_excel/master/TemplateR1.zip');?>">
                             <i class="fa fa-file-excel-o"></i> Download Sample</a> 
                            </div>
                          </div>
                          <div class="portlet-body">
                            <?php
                              if(strtotime(date('Y-m-d H:i:s')) <= strtotime($tanggal_close)){
                                ?>
                                        <div class="row">
                                          <div class="col-md-6 col-sm-6">
                                            <?php
                                                if(!empty($this->session->flashdata('message'))){
                                                    ?>

                                                    <div class="callout callout-danger">
                                                        <h4>Peringatan!</h4>
                                                        <div id="infoMessage"><?php echo $this->session->flashdata('message');?></div>
                                                    </div>
                                                    <?php
                                                }
                                            ?>
                                            <?php
                                                if(!empty($this->session->flashdata('sukses'))){
                                                    ?>

                                                    <div class="callout callout-success">
                                                        <h4>Sukses!</h4>
                                                        <div id="infoMessage"><?php echo $this->session->flashdata('sukses');?></div>
                                                    </div>
                                                    <?php
                                                }
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                  <h4>Import/Upload Excel file ke Rencana Pertemuan </h4>   
                                                    
                                                </div>
                                            </div>
                                            <?php
                                            $output = '';
                                            $output .= form_open_multipart('C_rencana_pertemuan/save_excel');
                                            $output .= '<div class="row">';
                                            $output .= '<div class="col-lg-12 col-sm-12"><div class="form-group">';
                                            $output .= form_label('Choose file', 'image');
                                            $data = array(
                                                'name' => 'userfile',
                                                'id' => 'userfile',
                                                'class' => 'form-control filestyle',
                                                'value' => '',
                                                'data-icon' => 'false'
                                            );
                                            $output .= form_upload($data);
                                            $output .= '</div> <span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
                                            $output .= '<div class="col-lg-12 col-sm-12"><div class="form-group text-right">';
                                            $data = array(
                                                'name' => 'importfile',
                                                'id' => 'importfile-id',
                                                'class' => 'btn bg-maroon btn-flat',
                                                'value' => 'Import',
                                            );
                                            $output .= form_submit($data, 'Import Data');
                                            $output .= '</div>
                                                                    </div></div>';
                                            $output .= form_close();
                                            echo $output;
                                            ?>

                                          </div>
                                      
                                            <div class="col-md-12">
                                              <div class="table-responsive">
                                                  <?php
                                                    if($absen_master->num_rows() == 0){
                                                      echo '<button class="btn btn-success bg-blue" onclick="rollback_data()"><i class="glyphicon glyphicon-trash"></i> Rollback Data</button>';
                                                    }else{

                                                    }
                                                  ?>
                                                  <table id="datatable_ajax" class="table table-striped table-bordered table-center-th" cellspacing="0" width="100%">
                                                      <thead>
                                                          <tr>
                                                              <th style="width:20px;">No</th>
                                                              <th>Tgl Rencana | Jam</th>
                                                              <th>Topik / Pembahasan</th>
                                                              <th>Referensi</th>
                                                              <th style="width:125px;"></th>
                                                          </tr>
                                                      </thead>
                                                      <tbody>
                                                      </tbody>
                                                  </table>
                                              </div> 
                                            </div>
                                        </div>
                                <?php
                              }else{
                                ?>
                                      
                                          
                                  <div class="table-responsive">
                                      <?php
                                        if($absen_master->num_rows() == 0){
                                          echo '<button class="btn btn-success bg-blue" onclick="rollback_data()"><i class="glyphicon glyphicon-trash"></i> Rollback Data</button>';
                                        }else{

                                        }
                                      ?>
                                      <table id="datatable_ajax" class="table table-striped table-bordered table-center-th" cellspacing="0" width="100%">
                                          <thead>
                                              <tr>
                                                  <th style="width:20px;">No</th>
                                                  <th>Tgl Rencana | Jam</th>
                                                  <th>Topik / Pembahasan</th>
                                                  <th>Referensi</th>
                                                  
                                              </tr>
                                          </thead>
                                          <tbody>
                                          </tbody>
                                      </table>
                                  </div> 
                                <?php
                              }
                            ?>                              
      
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="tab_3">

                  </div>
                  <div class="tab-pane" id="tab_4">

                  </div>
                  <div class="tab-pane" id="tab_5">
                   
                  </div>
                </div>
              </div>        
        
          
            
          
        </div>
      </div>
    </div>
  </div>
</div>