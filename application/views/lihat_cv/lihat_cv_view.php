 <link rel="stylesheet" type="text/css" href="style_cv.css" media="all" />
 <div class="page-content">
   <div class="row">
     <div class="col-md-12">
       <div class="portlet light">
         <div class="portlet-title">
           <div class="caption">
             <i class="fa fa-gift font-green-sharp"></i>
             <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small> </span>
           </div>
         </div>
         <div class="portlet-body">
           <?= $tombol_download; ?>
           <iframe src="<?= site_url('C_lihat_cv/generate') ?>" width="100%" height="600px" style="border:none;"></iframe>
         </div>
       </div>
     </div>
   </div>
 </div>