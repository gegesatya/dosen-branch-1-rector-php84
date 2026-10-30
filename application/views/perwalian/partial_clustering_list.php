<?php 
    // Group students by cluster in PHP to render the list
    $clustered_students = [];
    foreach($clustering as $std) {
        $clustered_students[$std['cluster']][] = $std;
    }
    
    // Badge colors based on semantic names
    $badge_colors = [
        'Prestasi Tinggi (Unggul)' => 'bs5-badge-primary',
        'Prestasi Menengah (Rata-rata)' => 'bs5-badge-success',
        'Perhatian Khusus (Bawah)' => 'bs5-badge-warning'
    ];
?>

<?php foreach($clustered_students as $cluster_name => $students): ?>
    <?php 
        $badge_class = isset($badge_colors[$cluster_name]) ? $badge_colors[$cluster_name] : 'bs5-badge-primary';
    ?>
    <div style="margin-bottom: 1rem;">
        <div style="font-weight: 700; color: #5a5c69; margin-bottom: 0.5rem; border-bottom: 1px solid #eee; padding-bottom: 4px;">
            <?= $cluster_name ?> <span style="float: right; color: #858796; font-size: 0.8rem;"><?= count($students) ?> Mhs</span>
        </div>
        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem; color: #858796;">
            <?php foreach(array_slice($students, 0, 5) as $std): // limit 5 for UI ?>
                <?php 
                    $fallback_url = "https://ui-avatars.com/api/?name=".urlencode($std['nm_pd'])."&background=random&color=fff"; $avatar_url = $fallback_url;
                    if (!empty($std['foto'])) {
                        $avatar_url = 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $std['foto'];
                    }
                ?>
                <li style="margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= $std['nm_pd'] ?>">
                    <img src="<?= $avatar_url ?>" onerror="this.onerror=null;this.src='<?= $fallback_url ?>';" class="bs5-avatar" style="width: 24px; height: 24px; margin-right: 5px;">
                    <?= $std['nm_pd'] ?> 
                    <strong style="color: #5a5c69;">(<?= $std['x'] ?>)</strong>
                </li>
            <?php endforeach; ?>
            <?php if(count($students) > 5): ?>
                <li style="font-style: italic; font-size: 1.2rem; color: #b7b9cc;">+ <?= count($students) - 5 ?> mahasiswa lainnya...</li>
            <?php endif; ?>
        </ul>
    </div>
<?php endforeach; ?>

<div style="margin-top: 1.5rem; margin-bottom: 1rem; text-align: center;">
    <button class="btn btn-primary" data-toggle="modal" data-target="#clusterDetailModal" style="width: 100%; font-weight: 700; border-radius: 0.5rem; background-color: #4e73df; border: none; padding: 0.6rem;">
        <i class="fa fa-list-alt"></i> Lihat Detail Seluruh Mahasiswa
    </button>
        </div>
    </div>
</div>
