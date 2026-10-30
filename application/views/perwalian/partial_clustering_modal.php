<?php
// Group students by cluster
$clustered_students = [];
foreach($clustering as $std) {
    $clustered_students[$std['cluster']][] = $std;
}
$badge_colors = [
    'Prestasi Tinggi (Unggul)' => 'bs5-badge-primary',
    'Prestasi Menengah (Rata-rata)' => 'bs5-badge-success',
    'Perhatian Khusus (Bawah)' => 'bs5-badge-warning'
];
?>

<!-- Modal Detail Kluster -->
<div class="modal fade" id="clusterDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);">
            <div class="modal-header" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; border-radius: 1rem 1rem 0 0;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -5px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" style="font-weight:700; color:#4e73df; text-transform: uppercase; letter-spacing: 0.05em; font-size: 1rem;">
                    <i class="fa fa-users"></i> Detail Mahasiswa per Kluster Prestasi
                </h4>
            </div>
            <div class="modal-body" style="max-height: 65vh; overflow-y: auto; padding: 1.5rem;">
                
                <?php foreach($clustered_students as $cluster_name => $students): ?>
                    <?php 
                        $badge_class = isset($badge_colors[$cluster_name]) ? $badge_colors[$cluster_name] : 'bs5-badge-primary';
                        $icon = 'fa-check-circle';
                        if ($cluster_name == 'Perhatian Khusus (Bawah)') $icon = 'fa-exclamation-triangle';
                        if ($cluster_name == 'Prestasi Menengah (Rata-rata)') $icon = 'fa-info-circle';
                        
                        $text_color = '#4e73df';
                        if ($cluster_name == 'Perhatian Khusus (Bawah)') $text_color = '#e74a3b';
                        if ($cluster_name == 'Prestasi Menengah (Rata-rata)') $text_color = '#1cc88a';
                    ?>
                    <h5 style="font-weight: 700; color: <?= $text_color ?>; margin-top: <?= $cluster_name == array_key_first($clustered_students) ? '0' : '2rem' ?>; margin-bottom: 1rem; border-bottom: 2px solid #eaecf4; padding-bottom: 0.5rem;">
                        <i class="fa <?= $icon ?>"></i> <?= $cluster_name ?> 
                        <span class="bs5-badge <?= $badge_class ?>" style="float: right; margin-top: -3px;"><?= count($students) ?> Mhs</span>
                    </h5>
                    
                    <div class="bs5-table-wrapper" style="max-height: none; overflow-y: visible; border: 1px solid #eaecf4; border-radius: 0.5rem;">
                        <table class="bs5-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th style="width: auto;">NIM & Nama Mahasiswa</th>
                                    <th style="width: 15%;">IPK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($students as $std): ?>
                                    <?php 
                                        $fallback_url = "https://ui-avatars.com/api/?name=".urlencode($std['nm_pd'])."&background=random&color=fff"; $avatar_url = $fallback_url;
                                        if (!empty($std['foto'])) {
                                            $avatar_url = 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $std['foto'];
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="bs5-d-flex bs5-align-items-center">
                                                <img src="<?= $avatar_url ?>" onerror="this.onerror=null;this.src='<?= $fallback_url ?>';" class="bs5-avatar" alt="Foto">
                                                <div>
                                                    <div style="font-size: 11px; color: #858796; margin-bottom: 2px; font-weight: 700;"><?= $std['nipd'] ?></div>
                                                    <span><?= $std['nm_pd'] ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="bs5-badge <?= $badge_class ?>"><?= $std['x'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
                
            </div>
            <div class="modal-footer" style="background-color: #f8f9fc; border-top: 1px solid #e3e6f0; border-radius: 0 0 1rem 1rem;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 0.5rem; font-weight: 700;">Tutup</button>
            </div>
        </div>
    </div>
</div>
