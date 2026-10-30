<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1; margin-top: 20px;">
                <div style="margin-bottom: 16px;">
                    <!-- Icon folder-open or similar to match the empty state style, but with an alert context -->
                    <i class="icon-folder" style="font-size: 54px; color: #94A3B8;"></i>
                </div>
                
                <h5 style="font-weight: 700; color: #475569; margin-bottom: 8px; font-size: 18px;">Akses Ditolak (Error 403)</h5>
                
                <p style="color: #64748B; font-size: 14px; max-width: 600px; margin: 0 auto 24px auto; line-height: 1.6;">
                    <?php if(isset($error_message) && !empty($error_message)): ?>
                        <?= $error_message ?>
                    <?php else: ?>
                        Anda tidak memiliki izin untuk mengakses laporan mahasiswa ini. Halaman ini diproteksi dan hanya dapat dibuka oleh <strong>Dosen Pembimbing Lapangan (DPL)</strong> yang telah ditugaskan secara resmi untuk mahasiswa tersebut.
                    <?php endif; ?>
                </p>
                
                <a href="<?php echo site_url('C_wall'); ?>" class="btn btn-primary btn-sm" style="border-radius: 6px; font-weight: 600; padding: 8px 20px;">
                    <i class="icon-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
