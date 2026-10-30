<?php if(empty($anomali_akademik)): ?>
    <tr><td colspan="3" style="text-align:center; padding: 2rem; color: #1cc88a;"><i class="fa fa-check-circle"></i> Tidak ditemukan anomali data KRS atau nilai pada mahasiswa bimbingan.</td></tr>
<?php else: ?>
    <?php $no = 1; foreach($anomali_akademik as $std): ?>
        <?php 
            $avatar_url = "https://ui-avatars.com/api/?name=".urlencode($std->nm_pd)."&background=random&color=fff";
            if (!empty($std->foto)) {
                $avatar_url = 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $std->foto;
            }
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <div class="bs5-d-flex bs5-align-items-center">
                    <img src="<?= $avatar_url ?>" onerror="this.onerror=null;this.src='<?= $fallback_url ?? base_url("assets/images/user.png") ?>';" class="bs5-avatar bs5-cursor-pointer" alt="Foto" data-name="<?= htmlspecialchars($std->nm_pd, ENT_QUOTES) ?>" data-avatar="<?= $avatar_url ?>" onclick="showPhotoModal(this.getAttribute('data-avatar'), this.getAttribute('data-name'), '<?= $std->nipd ?>')">
                    <div>
                        <div style="font-size: 11px; color: #858796; margin-bottom: 2px; font-weight: 700;"><?= $std->nipd ?></div>
                        <span><?= $std->nm_pd ?> <button type="button" class="btn btn-xs btn-outline-primary" style="padding: 0 5px; font-size: 13px; margin-left: 5px;" data-name="<?= htmlspecialchars($std->nm_pd, ENT_QUOTES) ?>" data-avatar="<?= $avatar_url ?>" onclick="showPhotoModal(this.getAttribute('data-avatar'), this.getAttribute('data-name'), '<?= $std->nipd ?>')"><i class="fa fa-id-card"></i> Akademik</button></span>
                    </div>
                </div>
            </td>
            <td>
                <ul style="margin: 0; padding-left: 20px; color: #e74a3b;">
                    <?php foreach($std->anomali_akademik as $msg): ?>
                        <li style="margin-bottom: 4px;"><?= $msg ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
