


<!-- <div class="row page-titles">
   <div class="col-md-5 align-self-center">
      <h5 class="text-themecolor"><?= $judul_besar; ?></h5>
      <?= $judul_kecil; ?>
   </div>
   <div class="col-md-7 align-self-center text-right">
      <div class="d-flex justify-content-end align-items-center">

         <button class="btn btn-default  d-none d-lg-block m-l-15" type="button" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
      </div>
   </div>
</div> -->







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
                              <!-- <img loading="lazy" width="25px" height="25px" class="rounded-circle object-fit-cover me-1" src="<?= base_url('assets/image_web/fiq.PNG'); ?>"> -->
                              <p class="text-15px mt-1">By <?php echo $creator; ?></p>
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
                     <?php include "course_page_info_description.php"; ?>
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





