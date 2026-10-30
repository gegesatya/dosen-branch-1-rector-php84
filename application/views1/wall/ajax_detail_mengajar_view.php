<?php if ($jadwal): ?>
    <div style="margin-bottom: 20px;">
        <div style="font-weight: 600; color: #1e293b; font-size: 14px; margin-bottom: 8px;">
            <i class="fa-regular fa-clock" style="color: #64748b; margin-right: 5px;"></i> <?= $jadwal->nama_hari ?>, <?= substr($jadwal->jam_mulai, 0, 5) ?> - <?= substr($jadwal->jam_selesai, 0, 5) ?>
        </div>
        <div style="font-weight: 600; color: #1e293b; font-size: 14px; margin-bottom: 15px;">
            <i class="fa-solid fa-location-dot" style="color: #ef4444; margin-right: 5px;"></i> <?= $jadwal->nama_ruang ?: 'Tidak Ada Ruang' ?>
        </div>

        <?php if ($materi): ?>
            <div style="background: #f1f5f9; border-radius: 8px; padding: 12px; margin-bottom: 15px; text-align: left;">
                <div style="font-weight: 700; color: #334155; font-size: 13px; margin-bottom: 5px;">
                    Materi Hari Ini (Pertemuan <?= $materi->pertemuan ?>)
                </div>
                <div style="font-size: 13px; color: #475569; line-height: 1.5;">
                    <?= nl2br(htmlspecialchars($materi->berita_acara_rencana)) ?>
                </div>
            </div>
        <?php else: ?>
            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 12px; margin-bottom: 15px; text-align: left;">
                <div style="font-size: 12px; color: #b45309;">
                    <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i> Belum ada rencana materi/pertemuan untuk hari ini.
                </div>
            </div>
        <?php endif; ?>
        
        <div style="text-align: left; margin-bottom: 10px; font-weight: 700; color: #1e293b; font-size: 14px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
            <i class="fa-solid fa-users" style="margin-right: 6px;"></i> Daftar Mahasiswa (<?= count($students) ?>)
        </div>
        
        <div style="max-height: 250px; overflow-y: auto; text-align: left; padding-right: 5px;">
            <?php if (!empty($students)): ?>
                <ul class="list-group" style="margin-bottom: 0;">
                    <?php foreach ($students as $mhs): ?>
                        <li class="list-group-item" style="border: none; border-bottom: 1px solid #e2e8f0; padding: 10px 0; display: flex; align-items: center; gap: 12px;">
                            <?php if ($mhs->foto): ?>
                                <img src="<?= base_url('assets/uploads/foto_mahasiswa/' . $mhs->foto) ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($mhs->nm_pd) ?>&background=e0f2fe&color=0284c7'" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                            <?php else: ?>
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 1px solid #bae6fd; flex-shrink: 0;"><?= substr($mhs->nm_pd, 0, 1) ?></div>
                            <?php endif; ?>
                            <div style="flex: 1; overflow: hidden;">
                                <div style="font-weight: 600; color: #334155; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($mhs->nm_pd) ?></div>
                                <div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 2px;"><?= htmlspecialchars($mhs->nipd) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div style="text-align: center; color: #64748b; font-size: 13px; padding: 20px 0;">
                    Tidak ada mahasiswa yang terdaftar di kelas ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="text-align: center;">
        <a href="<?= site_url('C_rencana_pertemuan/index/' . $id_jadwal) ?>" class="btn btn-primary btn-sm" style="border-radius: 6px; font-weight: 600; width: 100%;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Kelas
        </a>
    </div>
<?php else: ?>
    <div class="alert alert-danger">Jadwal tidak ditemukan.</div>
<?php endif; ?>
