<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
   <title>Curriculum Vitae <?= $data_pribadi->nm_pd; ?></title>

   
   <style>
      @media print {
         body {
            margin: 0px;
            padding: 0;
         }

         .header,
         .footer,
         .sidebar {
            display: none;
         }

         /* html,
         body {
            height: 100%;
            margin: 0;
            padding: 0;
         }

         .resume_left {
            height: 100vh;
         background-color: #3c7f7b !important;
         -webkit-print-color-adjust: exact;
         print-color-adjust: exact;
      }*/

         @page {
            margin-top: 30px;
            margin-bottom: 30px;
            margin-left: 50px;
            margin-right: 30px;
         }
      }

      @import url("https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap");

      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
         /* list-style: none; */
         font-family: "Montserrat", sans-serif;
      }

      body {
         background: #fff;
         font-size: 12px;
         line-height: 22px;
         color: #555555;
      }

      .bold {
         font-weight: 700;
         font-size: 15px;
         text-transform: uppercase;
      }

      .semi-bold {
         font-weight: 500;
         font-size: 14px;
      }

      .resume {
         width: 900px;
         height: auto;
         display: flex;
         margin: 20px auto;
      }

      .resume .resume_left {
         width: 280px;
         height: 1150px;
         background: #50918b;
      }

      .resume .resume_left .resume_profile {
         width: 100%;
         height: 280px;
      }

      .resume .resume_left .resume_profile img {
         width: 220px;
         height: 320px;
         object-fit: cover;
         /* border-radius: 50%; */
         display: block;
         margin-top: 20px;
         margin-left: auto;
         margin-right: auto;
         border: 1px solid #000;
         padding: 5px;
         background-color: #fff;

         /* width: 100%;
         height: 100%; */
      }

      .resume .resume_left .resume_content {
         padding: 0 25px;
      }

      .resume .title {
         margin-bottom: 5px;
      }

      .resume .resume_left .bold {
         color: #fff;
      }

      .resume .resume_left .regular {
         color: #fff;
      }

      .resume .resume_item {
         padding: 10px 0;
         border-bottom: 2px solid #b1eaff;
      }

      .resume .resume_left .resume_item:last-child,
      .resume .resume_right .resume_item:last-child {
         border-bottom: 0px;
      }

      .resume .resume_left ul li {
         display: flex;
         margin-bottom: 10px;
         align-items: center;
      }

      .resume .resume_left ul li:last-child {
         margin-bottom: 0;
      }

      .resume .resume_left ul li .icon {
         width: 35px;
         height: 35px;
         background: #fff;
         color: #50918b;
         border-radius: 50%;
         margin-right: 15px;
         font-size: 16px;
         position: relative;
      }

      .resume .icon i,
      .resume .resume_right .resume_hobby ul li i {
         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
      }

      .resume .resume_left ul li .data {
         color: #fff;
      }

      .resume .resume_left .resume_skills ul li {
         display: flex;
         margin-bottom: 10px;
         color: #fff;
         justify-content: space-between;
         align-items: center;
      }

      .resume .resume_left .resume_skills ul li .skill_name {
         width: 25%;
         display: inline-block;
      }

      .resume .resume_left .resume_skills ul li .skill_progress {
         width: 60%;
         margin: 0 5px;
         height: 5px;
         background: #009fd9;
         position: relative;
      }

      .resume .resume_left .resume_skills ul li .skill_per {
         width: 15%;
      }

      .resume .resume_left .resume_skills ul li .skill_progress span {
         position: absolute;
         top: 0;
         left: 0;
         height: 100%;
         background: #fff;
      }

      .resume .resume_left .resume_social .semi-bold {
         color: #fff;
         margin-bottom: 3px;
      }

      .resume .resume_right {
         width: 620px;
         background: #fff;
         padding-top: 0;
         padding-left: 10px;
         padding-right: 10px;
         padding-bottom: 10px;
      }

      .resume .resume_right .bold {
         color: #50918b;
      }

      .resume .resume_right .resume_work ul,
      .resume .resume_right .resume_education ul {
         padding-left: 40px;
         overflow: hidden;
      }

      .resume .resume_right ul li {
         position: relative;
      }

      .resume .resume_right ul li .date {
         font-size: 12px;
         font-weight: 500;
         margin-bottom: 8px;
      }

      .resume .resume_right ul li .info {
         margin-bottom: 10px;
      }

      .resume .resume_right ul li:last-child .info {
         margin-bottom: 0;
      }

      .resume .resume_right .resume_work ul li:before,
      .resume .resume_right .resume_education ul li:before {
         content: "";
         position: absolute;
         top: 5px;
         left: -25px;
         width: 6px;
         height: 6px;
         border-radius: 50%;
         border: 2px solid #50918b;
      }

      .resume .resume_right .resume_work ul li:after,
      .resume .resume_right .resume_education ul li:after {
         content: "";
         position: absolute;
         top: 14px;
         left: -21px;
         width: 2px;
         height: 115px;
         background: #50918b;
      }

      .resume .resume_right .resume_hobby ul {
         display: flex;
         justify-content: space-between;
      }

      .resume .resume_right .resume_hobby ul li {
         width: 80px;
         height: 80px;
         border: 2px solid #50918b;
         border-radius: 50%;
         position: relative;
         color: #50918b;
      }

      .resume .resume_right .resume_hobby ul li i {
         font-size: 30px;
      }

      .resume .resume_right .resume_hobby ul li:before {
         content: "";
         position: absolute;
         top: 40px;
         right: -52px;
         width: 50px;
         height: 2px;
         background: #50918b;
      }

      .resume .resume_right .resume_hobby ul li:last-child:before {
         display: none;
      }

      /* Kelas untuk tabel tanpa border */
      .no-border-table {
         width: 100%;
         border-collapse: collapse;
      }

      /* Menghapus border pada semua elemen tabel */
      .no-border-table,
      .no-border-table th,
      .no-border-table td {
         border: none;
         padding: 1px;
         /* Contoh: menambahkan padding agar konten tidak terlalu rapat */
         text-align: left;
      }

      /* Opsional: menambah garis bawah pada setiap baris untuk pemisah visual */
      .no-border-table tr:nth-child(even) {
         background-color: #f2f2f2;
      }

      .no-border-table tr {
         border-bottom: 1px solid #ddd;
      }

      .no-border-table tr:last-child {
         border-bottom: none;
      }

      .no-border-table th {
         background-color: #50918b;
         color: white;
      }
   </style>
</head>
<script src="https://kit.fontawesome.com/b99e675b6e.js"></script>

<body>
   <?php

   if ($data_cc > 0) {
   ?>
      <div class="resume">
         <div class="resume_left">
            <div class="resume_profile">
               <img src="<?= $src_image; ?>" alt="profile_pic" />
            </div>
            <div class="resume_content">
               <div class="resume_item resume_info" style="padding-top: 50px;">

                  <ul>
                     <li>
                        <div class="data semi-bold">NIM :<br><?= $data_pribadi->nipd; ?></div>
                     </li>
                     <li>
                        <div class="data semi-bold">No Kependudukan :<br><?= $data_pribadi->nik; ?></div>
                     </li>
                     <li>
                        <div class="data semi-bold">NIK :<br><?= $data_pribadi->telepon_seluler; ?></div>
                     </li>
                     <li>
                        <div class="data semi-bold">Email WBI :<br><?= $data_pribadi->email_wbi; ?></div>
                     </li>
                     <li>
                        <div class="data semi-bold">Email :<br><?= $data_pribadi->email_pribadi; ?></div>
                     </li>

                  </ul>

               </div>

               <div class="resume_item resume_social">
                  <div class="title">
                     <p class="bold">Social</p>
                  </div>
                  <ul>
                     <li>
                        <div class="icon">
                           <i class="fab fa-facebook-square"></i>
                        </div>
                        <div class="data">
                           <p class="semi-bold">Facebook</p>
                           <p><?= $data_pribadi->akun_fb; ?></p>
                        </div>
                     </li>
                     <li>
                        <div class="icon">
                           <i class="fab fa-instagram"></i>
                        </div>
                        <div class="data">
                           <p class="semi-bold">Instagram</p>
                           <p><?= $data_pribadi->akun_ig; ?></p>
                        </div>
                     </li>
                     <li>
                        <div class="icon">
                           <i class="fab fa-linkedin"></i>
                        </div>
                        <div class="data">
                           <p class="semi-bold">Linkedin</p>
                           <p><?= $data_pribadi->linkedin; ?></p>
                        </div>
                     </li>

                  </ul>
               </div>

               <div class="resume_item resume_info">
                  <div class="title">
                     <p class="bold">Hobby</p>
                  </div>

                  <div class="regular">
                     <p><?= $data_pribadi->hobby; ?></p>
                  </div>
               </div>
            </div>
         </div>
         <div class="resume_right">
            <div class=" resume_item resume_about">
               <div class="title">
                  <p class="bold">CURRICULUM VITAE</p>

               </div>
               <table class="no-border-table">
                  <tr>
                     <td style="width: 200px;">Nama </td>
                     <td>: <?= $data_pribadi->nm_pd; ?></td>
                  </tr>
                  <!-- <tr>
                     <td>NIM </td>
                     <td>: <?= $data_pribadi->nipd; ?></td>
                  </tr>
                  <tr>
                     <td>Nomor Induk Kependudukan </td>
                     <td>: <?= $data_pribadi->nik; ?></td>
                  </tr>
                  <tr> -->
                  <td>PRODI </td>
                  <td>: <?= $nm_lemb; ?></td>
                  </tr>
                  <tr>
                     <td>IPK </td>
                     <td>: <?= $data_pribadi->ipk; ?></td>
                  </tr>
                  <!-- <tr>
                     <td>No HP/WA Aktif </td>
                     <td>: <?= $data_pribadi->telepon_seluler; ?></td>
                  </tr> -->
                  <tr>
                     <td>Alamat </td>
                     <td>: <?= $data_pribadi->jln; ?></td>
                  </tr>

               </table>
               <br>
               <p class="bold">Tentang Saya</p>
               <p style="padding: 10px;">
                  <?= $data_pribadi->about_me; ?>
               </p>
            </div>
            <?php
            if ($edukasi->num_rows() > 0) {
            ?>
               <div class="resume_item resume_work">
                  <div class="title">
                     <p class="bold">Edukasi</p>
                  </div>
                  <table class="no-border-table">
                     <tr>
                        <th>Nama Sekolah/Universitas</th>
                        <th>Tahun Lulus</th>
                     </tr>
                     <?php
                     foreach ($edukasi->result() as $edu) {
                        echo '<tr>';
                        echo '<td>' . $edu->nm_sekolah . '</td>';
                        echo '<td>' . $edu->tahun_lulus . '</td>';
                        echo '</tr>';
                     }
                     ?>
                  </table>

               </div>
            <?php
            }
            ?>

            <div class="resume_item resume_about">
               <div class="title">
                  <p class="bold">Kemampuan</p>
               </div>

               <table class="no-border-table">
                  <tr>
                     <td>Bahasa Indonesia</td>
                     <td> <?= $data_pribadi->skill_bahasa; ?></td>
                  </tr>
                  <tr>
                     <td>Bahasa Inggris</td>
                     <td> <?= $data_pribadi->skill_english; ?></td>
                  </tr>
                  <tr>
                     <td>Office (Word/Excel)</td>
                     <td> <?= $data_pribadi->skill_office_app; ?></td>
                  </tr>
                  <tr>
                     <td>Presentasi (PPT)</td>
                     <td> <?= $data_pribadi->skill_presentation; ?></td>
                  </tr>
                  <tr>
                     <td>Mengoperasikan Komputer</td>
                     <td> <?= $data_pribadi->skill_komputer; ?></td>
                  </tr>
                  <tr>
                     <td>Skill Lainnya</td>
                     <td> <?= $data_pribadi->skill_lainnya; ?></td>
                  </tr>
               </table>


            </div>

            <?php
            if ($sertif->num_rows() > 0) {
            ?>
               <div class="resume_item resume_education">
                  <div class="title">
                     <p class="bold">Pelatihan Bersertifikat</p>
                  </div>
                  <table class="no-border-table">
                     <tr>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                     </tr>
                     <?php
                     foreach ($sertif->result() as $s) {
                        echo '<tr>';
                        echo '<td>' . $s->topik_pelatihan . '</td>';
                        echo '<td>' . $s->tanggal_diselenggarakan . '</td>';
                        echo '</tr>';
                     }
                     ?>
                  </table>

                  
               </div>
            <?php
            }
            ?>
            <?php
            if ($prestasi->num_rows() > 0) {
            ?>
               <div class="resume_item resume_education">
                  <div class="title">
                     <p class="bold">Prestasi Diri</p>
                  </div>
                  <ol style="padding-left: 20px;">
                     <?php
                     foreach ($prestasi->result() as $s) {
                        echo '<li>' . $s->nm_prestasi . '</li>';
                     }
                     ?>
                  </ol>
               </div>
            <?php
            }
            ?>

            <?php
            if ($organisasi->num_rows() > 0) {
            ?>
               <div class="resume_item resume_education">
                  <div class="title">
                     <p class="bold">Pengalaman Organisasi</p>
                  </div>
                  <ol style="padding-left: 20px;">
                     <?php
                     foreach ($organisasi->result() as $s) {
                        echo '<li>' . $s->nm_pengalaman_organisasi . '</li>';
                     }
                     ?>
                  </ol>
               </div>
            <?php
            }
            ?>

            <!-- <div class="resume_item resume_hobby">
            <div class="title">
               <p class="bold">Hobby</p>
            </div>
            <p><?= $data_pribadi->hobby; ?></p>
         </div> -->
         </div>
      </div>
   <?php
   }
   ?>

</body>

</html>