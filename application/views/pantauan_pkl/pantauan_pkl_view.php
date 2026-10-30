<style>
    .page-content {
        background-color: #f4f6f9 !important;
        padding-top: 15px !important;
        border: none !important;
        box-shadow: none !important;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03);
        height: 100%;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon-wrapper {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        float: right;
    }

    .bg-gradient-blue {
        background: linear-gradient(135deg, #3a7bd5, #3a6073);
    }
    .bg-gradient-green {
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }
    .bg-gradient-red {
        background: linear-gradient(135deg, #e52d27, #b31217);
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    .bg-gradient-orange {
        background: linear-gradient(135deg, #f12711, #f5af19);
    }

    .kpi-title {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8a909d;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .kpi-desc {
        font-size: 12px;
        color: #5c6873;
        font-weight: 500;
    }
</style>
<div class="page-content" style="min-height: 100vh;">
    <div class="portlet light bordered" style="border-radius: 12px !important; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04); margin-bottom: 25px; padding: 20px 25px;">
        <div class="page-title" style="margin: 0;">
            <h1 style="font-weight: 700; color: #1e40af; font-size: 22px; margin: 0; letter-spacing: -0.5px;">
                <?php echo $judul_besar; ?>
                <span style="font-size: 13px; font-weight: 500; color: #94a3b8; display: block; margin-top: 6px; letter-spacing: 0;"><?php echo $judul_kecil; ?></span>
            </h1>
        </div>
    </div>

    <!-- START STATISTIK -->
    <div style="display: flex; flex-wrap: wrap; margin-left: -10px; margin-right: -10px; margin-bottom: 20px;">
        <div style="flex: 1 1 22%; min-width: 220px; padding: 0 10px; margin-bottom: 15px;">
            <div class="kpi-card" style="margin-bottom: 0;">
                <div class="kpi-icon-wrapper bg-gradient-blue">
                    <i class="fa fa-users"></i>
                </div>
                <div class="kpi-title">MHS PKL</div>
                <div class="kpi-value"><?= $stats['total_pkl'] ?></div>
                <div class="kpi-desc text-primary"><i class="fa fa-check-circle"></i> Terdaftar</div>
            </div>
        </div>
        <div style="flex: 1 1 22%; min-width: 220px; padding: 0 10px; margin-bottom: 15px;">
            <div class="kpi-card" style="margin-bottom: 0;">
                <div class="kpi-icon-wrapper bg-gradient-green">
                    <i class="fa fa-play"></i>
                </div>
                <div class="kpi-title">BARU MULAI</div>
                <div class="kpi-value"><?= $stats['baru_mulai'] ?></div>
                <div class="kpi-desc text-success"><i class="fa fa-clock-o"></i> 14 Hari Terakhir</div>
            </div>
        </div>
        <div style="flex: 1 1 22%; min-width: 220px; padding: 0 10px; margin-bottom: 15px;">
            <div class="kpi-card" style="margin-bottom: 0;">
                <div class="kpi-icon-wrapper bg-gradient-red">
                    <i class="fa fa-warning"></i>
                </div>
                <div class="kpi-title">AKAN BERAKHIR</div>
                <div class="kpi-value"><?= $stats['segera_berakhir'] ?></div>
                <div class="kpi-desc text-danger"><i class="fa fa-calendar"></i> 30 Hari Kedepan</div>
            </div>
        </div>
        <div style="flex: 1 1 22%; min-width: 220px; padding: 0 10px; margin-bottom: 15px;">
            <div class="kpi-card" style="margin-bottom: 0;">
                <div class="kpi-icon-wrapper bg-gradient-purple">
                    <i class="fa fa-user-md"></i>
                </div>
                <div class="kpi-title">STATUS DPL</div>
                <div class="kpi-value" style="display: flex; align-items: center;">
                    <span style="color: #667eea;" title="Sudah Ada DPL"><?= $stats['sudah_dpl'] ?></span>
                    <span style="font-size: 20px; color: #cbd5e1; margin: 0 8px; font-weight: 300;">|</span>
                    <span style="color: #f5af19;" title="Belum Ada DPL"><?= $stats['belum_dpl'] ?></span>
                </div>
                <div class="kpi-desc">
                    <span style="color: #667eea; font-weight: 600;"><i class="fa fa-check"></i> Sudah</span> 
                    <span style="color: #cbd5e1; margin: 0 4px;">&bull;</span> 
                    <span style="color: #f5af19; font-weight: 600;"><i class="fa fa-times"></i> Belum</span>
                </div>
            </div>
        </div>
    </div>
    <!-- END STATISTIK -->

	<div class="row" style="display: flex; flex-wrap: wrap;">
        <?php if(empty($students)): ?>
        <div class="col-md-12">
            <div class="alert alert-info text-center" style="padding: 40px; background: #fff; border: 1px dashed #cbd5e1; color: #475569; border-radius: 12px;">
                <i class="icon-info" style="font-size: 48px; color: #94a3b8; display: block; margin-bottom: 15px;"></i>
                <h4 style="font-weight: 600;">Tidak ada data mahasiswa PKL di Prodi ini.</h4>
            </div>
        </div>
        <?php else: ?>
            <?php $i = 0; foreach($students as $s): 
                $foto_src = !empty($s->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $s->foto : base_url('assets/no_profile_photo.png');
                $foto_src_besar = !empty($s->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $s->foto : base_url('assets/no_profile_photo.png');
                $dpl_name = !empty($s->nm_dpl) ? $s->nm_dpl : '<span class="text-danger">Belum Ditentukan</span>';
                $lokasi = !empty($s->nama_perusahaan) ? $s->nama_perusahaan : '<span class="text-warning">Belum Ditentukan</span>';
                
                $tgl_mulai = !empty($s->tanggal_mulai) ? date('d M Y', strtotime($s->tanggal_mulai)) : '-';
                $tgl_selesai = !empty($s->tanggal_selesai) ? date('d M Y', strtotime($s->tanggal_selesai)) : '-';
                
                // Progress color logic for Pengisian
                $color_pengisian = 'danger';
                if($s->progress_pengisian === 'N/A') {
                    $color_pengisian = 'default';
                } elseif($s->progress_pengisian >= 80) {
                    $color_pengisian = 'success';
                } elseif($s->progress_pengisian >= 40) {
                    $color_pengisian = 'warning';
                }
                
                // Progress color logic for Review
                $color_review = 'danger';
                if($s->progress_review >= 80) {
                    $color_review = 'success';
                } elseif($s->progress_review >= 40) {
                    $color_review = 'warning';
                }
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 student-card" style="<?= $i >= 12 ? 'display: none;' : 'display: flex;' ?> margin-bottom: 20px;">
                <div class="portlet light bordered" style="width: 100%; border-radius: 12px !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.2s; margin-bottom: 0;">
                    <div class="portlet-title" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; min-height: 48px; margin-bottom: 15px;">
                        <div class="caption" style="display: flex; align-items: center; width: 100%;">
                            <img src="<?= $foto_src ?>" onerror="this.onerror=null; this.src='<?= base_url('assets/no_profile_photo.png') ?>';" alt="Foto Mahasiswa" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; margin-right: 15px; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" onclick="showZoomFoto('<?= $foto_src_besar ?>', '<?= htmlspecialchars($s->nm_pd, ENT_QUOTES) ?>', '<?= htmlspecialchars(strip_tags($lokasi), ENT_QUOTES) ?>', '<?= htmlspecialchars(strip_tags($dpl_name), ENT_QUOTES) ?>')">
                            <div style="flex-grow: 1; overflow: hidden;">
                                <span class="caption-subject font-blue-madison bold uppercase" style="font-size: 13px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;" title="<?= $s->nm_pd ?>"><?= $s->nm_pd ?></span>
                                <span class="caption-helper font-grey-gallery" style="font-size: 12px; font-weight: 600;"><?= $s->nipd ?></span>
                            </div>
                            <div style="text-align: right; margin-left: 15px; flex-shrink: 0; background: #f8fafc; padding: 6px 10px; border-radius: 8px;">
                                <div style="font-size: 11px; color: #334155; font-weight: 600; margin-bottom: 4px;"><span style="font-size: 10px; color: #94a3b8; font-weight: 700; margin-right: 5px; text-transform: uppercase;"><i class="fa fa-calendar"></i> Mulai</span><?= $tgl_mulai ?></div>
                                <div style="font-size: 11px; color: #334155; font-weight: 600;"><span style="font-size: 10px; color: #94a3b8; font-weight: 700; margin-right: 5px; text-transform: uppercase;"><i class="fa fa-calendar-check-o"></i> Selesai</span><?= $tgl_selesai ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;"><i class="fa fa-building-o"></i> Tempat PKL</div>
                                <div style="font-size: 13px; color: #334155; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= strip_tags($lokasi) ?>"><?= $lokasi ?></div>
                            </div>
                            <div style="flex: 1; min-width: 0; padding-left: 5px; border-left: 1px solid #e2e8f0;">
                                <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;"><i class="fa fa-user-md"></i> DPL</div>
                                <div style="font-size: 13px; color: #334155; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= strip_tags($dpl_name) ?>"><?= $dpl_name ?></div>
                            </div>
                        </div>

                        <!-- Progress Bar: Pengisian Laporan -->
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                                <span style="color: #475569;">Pengisian Laporan</span>
                                <span class="font-<?= $color_pengisian ?>"><?= $s->progress_pengisian === 'N/A' ? '<span style="font-size: 10px; font-weight: 500; font-style: italic;" title="Tanggal PKL belum diset, target persentase tidak diketahui"><i class="fa fa-info-circle"></i> Tgl Belum Diset</span>' : $s->progress_pengisian . '%' ?></span>
                            </div>
                            <?php if($s->progress_pengisian === 'N/A'): ?>
                            <div class="progress progress-striped" style="height: 10px; margin-bottom: 5px; border-radius: 5px; background-color: #f1f5f9; box-shadow: none;">
                                <div class="progress-bar progress-bar-default" role="progressbar" style="width: 100%;"></div>
                            </div>
                            <?php else: ?>
                            <div class="progress progress-striped" style="height: 10px; margin-bottom: 5px; border-radius: 5px; background-color: #f1f5f9; box-shadow: none;">
                                <div class="progress-bar progress-bar-<?= $color_pengisian ?>" role="progressbar" aria-valuenow="<?= $s->progress_pengisian ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $s->progress_pengisian ?>%;"></div>
                            </div>
                            <?php endif; ?>
                            <div style="font-size: 11px; color: #94a3b8; text-align: right;"><?= $s->total_filled ?> disubmit <?= $s->total_weeks ? '(est. ' . $s->total_weeks . ' mg)' : '' ?></div>
                        </div>

                        <!-- Progress Bar: Review DPL -->
                        <div style="margin-bottom: 5px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                                <span style="color: #475569;">Review DPL</span>
                                <span class="font-<?= $color_review ?>"><?= $s->progress_review ?>%</span>
                            </div>
                            <div class="progress progress-striped" style="height: 10px; margin-bottom: 5px; border-radius: 5px; background-color: #f1f5f9; box-shadow: none;">
                                <div class="progress-bar progress-bar-<?= $color_review ?>" role="progressbar" aria-valuenow="<?= $s->progress_review ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $s->progress_review ?>%;"></div>
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; text-align: right;"><?= $s->total_reviewed ?> dari <?= $s->total_filled ?> laporan direview</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $i++; endforeach; ?>
            
            <?php if(count($students) > 12): ?>
            <div class="col-md-12 text-center" id="load-more-container" style="margin-top: 10px; margin-bottom: 40px;">
                <button id="btn-load-more" class="btn btn-primary" style="background-color: #3b82f6; border-color: #3b82f6; border-radius: 20px; padding: 8px 30px; font-weight: 600; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2); transition: all 0.2s ease;">
                    <i class="fa fa-arrow-down"></i> Tampilkan Lebih Banyak
                </button>
            </div>
            <?php endif; ?>
        <?php endif; ?>
	</div>
</div>

<!-- Modal Zoom Foto -->
<div class="modal fade" id="modalZoomFoto" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.8);">
    <div class="modal-dialog modal-dialog-centered" role="document" style="margin-top: 5vh; max-width: 450px;">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
            <div class="modal-header" style="border: none; padding-bottom: 0; text-align: right;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 1; text-shadow: none; font-size: 35px; margin-top: -20px; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 0;">
                <img id="zoomFotoImg" src="" onerror="this.onerror=null; this.src='<?= base_url('assets/no_profile_photo.png') ?>';" alt="Zoom Foto" style="width: 100%; height: 450px; max-height: 60vh; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); object-fit: cover; background: #fff; padding: 8px;">
                
                <div style="background: #ffffff; border-radius: 12px; padding: 15px; margin-top: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); text-align: left;">
                    <div id="zoomFotoName" style="color: #1e293b; font-weight: 700; font-size: 18px; margin-bottom: 12px; text-align: center; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;"></div>
                    
                    <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;"><i class="fa fa-building-o"></i> Tempat PKL</div>
                    <div id="zoomFotoLokasi" style="font-size: 14px; color: #334155; font-weight: 600; margin-bottom: 10px;"></div>
                    
                    <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;"><i class="fa fa-user-md"></i> DPL</div>
                    <div id="zoomFotoDpl" style="font-size: 14px; color: #334155; font-weight: 600;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showZoomFoto(src, name, lokasi, dpl) {
    document.getElementById('zoomFotoImg').src = src;
    document.getElementById('zoomFotoName').innerText = name;
    document.getElementById('zoomFotoLokasi').innerText = lokasi;
    document.getElementById('zoomFotoDpl').innerText = dpl;
    $('#modalZoomFoto').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    var loadMoreBtn = document.getElementById('btn-load-more');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            // Find all hidden student cards
            var hiddenCards = document.querySelectorAll('.student-card[style*="display: none"]');
            
            // Show the next 12 cards
            for (var i = 0; i < 12 && i < hiddenCards.length; i++) {
                hiddenCards[i].style.display = 'flex';
                // Trigger a reflow for smooth display
                void hiddenCards[i].offsetWidth;
            }
            
            // If no more hidden cards, hide the load more button
            if (hiddenCards.length <= 12) {
                document.getElementById('load-more-container').style.display = 'none';
            }
        });
    }
});
</script>
