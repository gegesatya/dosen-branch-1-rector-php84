










<section>
   <div class="bread-crumb courses-details">
      <div class="container">
         <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-12">
               <div class="courses-details-1st-text">
                  <h1><?= $judul_besar; ?></h1>
                  <p class="mb-3"><?= $judul_kecil; ?></p>

                  <div class="review">
                     <div class="row ">
                        <div class="col-12 course-heading-info mb-3">
                           <div class="info-tag">
                              <p class="text-15px mt-1">By <?php echo $creator->nm_ptk; ?></p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<section class="course-decription">
   <div class="container">
      <div class="row">
         <div class="col-lg-9 col-md-12 col-sm-12 order-2 order-lg-1">
            <div class="course-left-side m-b-15">

               <div class="tab-content">
                  <div class="tab-pane fade show active" id="course-overview" role="tabpanel" aria-labelledby="course-overview-tab">
                     <div class="course-description">
                        <h3 class="description-head"><?php echo "Organisasi Kelas" ?></h3>
                        <p>Struktur Organisasi Kelas adalah sebagai berikut:</p>
                        <ol>
                           <li>Ketua Program Studi</li>
                           <li>Dosen Pengampu</li>
                           <li>Komting (Mahasiswa)</li>
                           <li>Wakil Komting 1 (Mahasiswa)</li>
                           <li>Wakil Komting 2 (Mahasiswa)</li>
                        </ol>
                        <p>Untuk Komting, Wakil Komting 1 dan Wakil Komting 2 dipilih oleh Dosen Pengampu.</p>
                     </div>
                     <div class="table-responsive">
                        <table class="table table-bordered mt-3" id="x">
                           <thead>
                              <tr role="row" class="heading">
                                 <th>NIDN</th>
                                 <th>NAMA</th>
                                 <th>Keterangan</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td><?= $kaprodi->nidn; ?></td>
                                 <td><?= $kaprodi->nm_ptk; ?></td>
                                 <td><?= "Ketua Program Studi"; ?></td>
                              </tr>
                              <tr>
                                 <td><?= $creator->nidn; ?></td>
                                 <td><?= $creator->nm_ptk; ?></td>
                                 <td><?= "Pengampu Mata Kuliah"; ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="d-flex align-items-center mt-3">
                        <button type="button" class="btn btn-info d-none d-lg-block m-l-15" onclick="add_organisasi()"><i class="fa fa-plus-circle"></i> Add</button>
                        <button class="btn btn-primary ml-3" type="button" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
                     </div>
                     <div class="table-responsive">
                        <table class="table table-bordered mt-3" id="datatable_ajax">
                           <thead>
                              <tr role="row" class="heading">
                                 <th>NIM</th>
                                 <th>Nama</th>
                                 <th>Keterangan</th>
                                 <th style="max-width: 15px !important;"></th>
                              </tr>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>

                     <div class="table-responsive">
                        <h3 class="mt-3">Peserta Kelas</h3>
                        <table class="table table-bordered mt-3" id="datatable_ajax">
                           <thead>
                              <tr role="row" class="heading">
                                 <th>NIM</th>
                                 <th>Nama</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              foreach ($peserta_kelas->result() as $key => $value) {
                              ?>
                                 <tr>
                                    <td><?= $value->nipd; ?></td>
                                    <td><?= $value->nm_pd; ?></td>
                                 </tr>
                              <?php
                              }
                              ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-3 col-md-12 col-sm-12 order-1 order-lg-2">
            <div class="course-right-section">
               <div class="course-card">
                  <div class="card-img">
                     <div class="courses-card-image">
                        <?php
                        if ($this->session->userdata('photo') <> 'no_photo.jpg') {
                           $p = $this->session->userdata('photo') . '.PNG';
                        } else {
                           $p = $this->session->userdata('photo');
                        }
                        ?>
                        <img loading="lazy" class="w-100" src="<?php echo base_url('assets/image_web/' . $p); ?>">
                     </div>
                  </div>

                  <div class="enrol">
                     <div class="icon">
                        <h4><?php echo get_phrase('Realisasi') ?></h4>
                     </div>
                     <h5><?= $i_realisasi; ?> / <?= $i_pertemuan; ?></h5>
                  </div>
                  <div class="enrol">
                     <div class="icon">
                        <h4><?php echo get_phrase('Mahasiswa') ?></h4>
                     </div>
                     <h5><?= $jumlah_mahasiswa; ?></h5>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>


