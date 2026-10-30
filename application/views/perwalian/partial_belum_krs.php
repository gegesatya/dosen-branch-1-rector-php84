<?php if(empty($belum_krs)): ?>
    <tr><td colspan="6" style="text-align:center; padding: 2rem;">Semua mahasiswa bimbingan aktif sudah melakukan submit KRS.</td></tr>
<?php else: ?>
    <?php $no = 1; foreach($belum_krs as $std): ?>
        <?php 
            $avatar_url = "https://ui-avatars.com/api/?name=".urlencode($std->nm_pd)."&background=random&color=fff";
            if (!empty($std->foto)) {
                $avatar_url = 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $std->foto;
            }
            $fallback_url = base_url("assets/images/user.png");
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <div class="bs5-d-flex bs5-align-items-center">
                    <img src="<?= $avatar_url ?>" onerror="this.onerror=null;this.src='<?= $fallback_url ?>';" class="bs5-avatar bs5-cursor-pointer" alt="Foto" data-name="<?= htmlspecialchars($std->nm_pd, ENT_QUOTES) ?>" data-avatar="<?= $avatar_url ?>" onclick="showPhotoModal(this.getAttribute('data-avatar'), this.getAttribute('data-name'), '<?= $std->nipd ?>')">
                    <div>
                        <div style="font-size: 11px; color: #858796; margin-bottom: 2px; font-weight: 700;"><?= $std->nipd ?></div>
                        <span><?= $std->nm_pd ?> <button type="button" class="btn btn-xs btn-outline-primary" style="padding: 0 5px; font-size: 13px; margin-left: 5px;" data-name="<?= htmlspecialchars($std->nm_pd, ENT_QUOTES) ?>" data-avatar="<?= $avatar_url ?>" onclick="showPhotoModal(this.getAttribute('data-avatar'), this.getAttribute('data-name'), '<?= $std->nipd ?>')"><i class="fa fa-id-card"></i> Akademik</button></span>
                    </div>
                </div>
            </td>
            <td style="font-size: 13px; color: #e74a3b;">
                <i class="fa fa-info-circle"></i> Mahasiswa ini sudah <b><?= $std->semester_terlampaui ?></b> semester tidak mengajukan KRS.
            </td>
            <td style="text-align: center;">
                <span class="bs5-badge bs5-badge-warning"><?= substr($std->terakhir_smt ?? '-', 0, 5) ?></span>
            </td>
            <td style="text-align: center;">
                <span class="bs5-badge bs5-badge-primary"><?= $std->sks_total ?> SKS</span>
            </td>
            <td style="text-align: center;">
                <span class="bs5-badge <?= ($std->ipk >= 2.75) ? 'bs5-badge-success' : 'bs5-badge-danger' ?>"><?= $std->ipk ?></span>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
