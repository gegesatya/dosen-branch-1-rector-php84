<style>
.pedoman-modal .pedoman-section {
    margin-bottom: 28px;
}
.pedoman-modal .pedoman-section-title {
    font-size: 14px;
    font-weight: 800;
    color: #1E293B;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 2px solid #EEF2FF;
}
.pedoman-modal .pedoman-section-title .section-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.pedoman-modal .badge-legend {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.pedoman-modal .badge-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 10px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
}
.pedoman-modal .badge-item .badge-dot {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.pedoman-modal .badge-item .badge-desc strong {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 2px;
}
.pedoman-modal .badge-item .badge-desc span {
    font-size: 12px;
    color: #64748B;
    line-height: 1.5;
}
.pedoman-modal .btn-legend-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.pedoman-modal .btn-legend-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
}
.pedoman-modal .btn-legend-item .btn-swatch {
    width: 60px;
    height: 30px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}
.pedoman-modal .btn-legend-item .btn-desc strong {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 2px;
}
.pedoman-modal .btn-legend-item .btn-desc span {
    font-size: 11.5px;
    color: #64748B;
    line-height: 1.5;
}
.pedoman-modal .review-card-legend {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.pedoman-modal .tip-box {
    background: #EEF2FF;
    border: 1px solid #C7D2FE;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 12.5px;
    color: #3730A3;
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-top: 14px;
}
.pedoman-modal .tip-box i { margin-top: 2px; flex-shrink: 0; }
</style>

<div class="pedoman-modal">

    <!-- Section 1: Card Mahasiswa -->
    <div class="pedoman-section">
        <div class="pedoman-section-title">
            <div class="section-icon" style="background: #EEF2FF;">👤</div>
            <span>1. Card Mahasiswa &amp; Indikator Status</span>
        </div>
        <p style="font-size: 13px; color: #475569; margin-bottom: 14px; line-height: 1.7;">
            Setiap kartu mewakili satu mahasiswa yang sedang melaksanakan Internship/Magang. Kartu menampilkan informasi identitas, nama perusahaan tempat magang, periode pelaksanaan, mata kuliah terkait, dan status laporan mingguan. Terdapat indikator berwarna pada bagian bawah info kartu yang memiliki arti sebagai berikut:
        </p>
        <div class="badge-legend">
            <div class="badge-item">
                <div class="badge-dot" style="background: #F1F5F9; border: 1px dashed #CBD5E1; color: #475569;">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div class="badge-desc">
                    <strong>Abu-abu — Belum ada laporan dibuat</strong>
                    <span>Mahasiswa belum membuat laporan mingguan sama sekali. Segera ingatkan mahasiswa untuk mulai mengisi laporan.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626;">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div class="badge-desc">
                    <strong>Merah — Ada laporan belum direview</strong>
                    <span>Mahasiswa sudah membuat laporan, namun Anda (dosen pembimbing) belum memberikan review. Angka merah menunjukkan jumlah laporan yang masih menunggu review.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="badge-desc">
                    <strong>Hijau — Semua laporan telah direview</strong>
                    <span>Seluruh laporan mingguan mahasiswa sudah mendapatkan review dari dosen pembimbing. Status terbaik!</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #D97706;">
                    <i class="fa fa-warning"></i>
                </div>
                <div class="badge-desc">
                    <strong>Kuning — Peringatan kekosongan laporan</strong>
                    <span>Sistem mendeteksi ada minggu yang seharusnya sudah diisi laporan namun belum dibuat, atau ada laporan harian yang tidak lengkap. Informasi ini mencakup: tanggal terakhir diisi, jumlah minggu yang kosong, dan peringatan laporan harian tidak lengkap.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626;">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="badge-desc">
                    <strong>Merah kecil di atas (dekat nama) — Belum isi tanggal PKL</strong>
                    <span>Mahasiswa belum mengisi tanggal mulai dan tanggal selesai pelaksanaan magang di sistem mahasiswa. Minta mahasiswa melengkapi data ini terlebih dahulu.</span>
                </div>
            </div>
        </div>
    </div>

    <hr style="border-color: #F1F5F9; margin: 4px 0 24px;">

    <!-- Section 2: Tombol Aksi -->
    <div class="pedoman-section">
        <div class="pedoman-section-title">
            <div class="section-icon" style="background: #F0FDF4;">🖱️</div>
            <span>2. Tombol Aksi pada Setiap Kartu</span>
        </div>
        <p style="font-size: 13px; color: #475569; margin-bottom: 14px; line-height: 1.7;">
            Setiap kartu mahasiswa memiliki tiga tombol aksi di bagian bawah kartu:
        </p>
        <div class="btn-legend-grid">
            <div class="btn-legend-item">
                <div class="btn-swatch" style="background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0;">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </div>
                <div class="btn-desc">
                    <strong>Tombol PDF</strong>
                    <span>Mengunduh / menampilkan dokumen Tracking Kegiatan Magang mahasiswa dalam format PDF. Berguna untuk arsip atau pelaporan.</span>
                </div>
            </div>
            <div class="btn-legend-item">
                <div class="btn-swatch" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE;">
                    <i class="fa fa-file-text-o"></i> Usulan
                </div>
                <div class="btn-desc">
                    <strong>Tombol Usulan</strong>
                    <span>Hanya muncul jika mahasiswa telah mengunggah berkas pendaftaran/usulan magang. Klik untuk melihat dokumen usulan dari Google Drive, lengkap dengan fitur AI Summary untuk meringkas isi dokumen secara otomatis.</span>
                </div>
            </div>
            <div class="btn-legend-item" style="grid-column: 1 / -1;">
                <div class="btn-swatch" style="background: linear-gradient(135deg, #4F46E5, #312E81); color: #fff; border: none; width: 80px;">
                    <i class="fa fa-pencil"></i> Review
                </div>
                <div class="btn-desc">
                    <strong>Tombol Review</strong>
                    <span>Membuka halaman review mingguan untuk mahasiswa yang bersangkutan. Di sinilah Anda dapat membaca laporan harian mahasiswa, memberikan komentar, dan me-review laporan minggu demi minggu.</span>
                </div>
            </div>
        </div>
    </div>

    <hr style="border-color: #F1F5F9; margin: 4px 0 24px;">

    <!-- Section 3: Halaman Review Mingguan -->
    <div class="pedoman-section">
        <div class="pedoman-section-title">
            <div class="section-icon" style="background: #FFF7ED;">📋</div>
            <span>3. Halaman Review Mingguan</span>
        </div>
        <p style="font-size: 13px; color: #475569; margin-bottom: 14px; line-height: 1.7;">
            Halaman review mingguan adalah halaman utama untuk memantau dan mengevaluasi progres magang mahasiswa. Halaman ini dibuka dengan klik tombol <strong>"Review"</strong> pada kartu mahasiswa. Berikut hal-hal yang dapat dilakukan di halaman ini:
        </p>
        <div class="badge-legend">
            <div class="badge-item">
                <div class="badge-dot" style="background: #EEF2FF; color: #4F46E5; font-size: 13px;">📊</div>
                <div class="badge-desc">
                    <strong>Ringkasan Statistik</strong>
                    <span>Menampilkan jumlah total laporan yang telah dibuat, jumlah yang sudah direview, dan jumlah yang belum direview.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #F0FDF4; color: #059669; font-size: 13px;">📖</div>
                <div class="badge-desc">
                    <strong>Daftar Laporan per Minggu</strong>
                    <span>Setiap laporan ditampilkan dalam sebuah kartu yang dikelompokkan per minggu. Dosen dapat membaca isi laporan harian mahasiswa dan memberikan komentar/review untuk setiap minggu.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #EEF2FF; color: #6366F1; font-size: 13px;">🤖</div>
                <div class="badge-desc">
                    <strong>AI Review Otomatis</strong>
                    <span>Dosen dapat memanfaatkan fitur AI untuk membantu menyusun komentar review secara otomatis berdasarkan isi laporan. Klik tombol AI Review untuk menghasilkan saran komentar yang dapat diedit sebelum disimpan.</span>
                </div>
            </div>
        </div>
        <div class="tip-box">
            <i class="fa fa-lightbulb-o"></i>
            <span><strong>Tips:</strong> Gunakan fitur AI Review untuk mempercepat proses review. AI akan menganalisis isi laporan harian dan menghasilkan komentar yang relevan, sehingga Anda hanya perlu menyesuaikan sebelum menyimpan.</span>
        </div>
    </div>

    <hr style="border-color: #F1F5F9; margin: 4px 0 24px;">

    <!-- Section 4: Card Review Mingguan -->
    <div class="pedoman-section">
        <div class="pedoman-section-title">
            <div class="section-icon" style="background: #FDF4FF;">🗂️</div>
            <span>4. Card Review Mingguan &amp; Indikatornya</span>
        </div>
        <p style="font-size: 13px; color: #475569; margin-bottom: 14px; line-height: 1.7;">
            Setiap laporan ditampilkan dalam sebuah <em>card</em> (kartu) berisi aktivitas harian mahasiswa selama seminggu, lengkap dengan indikator status sebagai berikut:
        </p>
        <div class="badge-legend">
            <div class="badge-item">
                <div class="badge-dot" style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626;">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="badge-desc">
                    <strong>Merah / "Menunggu Review"</strong>
                    <span>Laporan sudah diisi oleh mahasiswa namun belum mendapatkan komentar/review dari dosen. Prioritaskan laporan dengan status ini.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="badge-desc">
                    <strong>Hijau / "Sudah Direview"</strong>
                    <span>Laporan sudah mendapatkan komentar dari dosen pembimbing. Komentar dosen ditampilkan di bagian bawah kartu dengan kotak berwarna hijau muda.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #F1F5F9; border: 1px dashed #CBD5E1; color: #475569; font-size: 11px; text-align:center; line-height:1.2; padding: 3px;">
                    <i class="fa fa-edit"></i>
                </div>
                <div class="badge-desc">
                    <strong>Kolom Komentar Dosen</strong>
                    <span>Dosen dapat mengetikkan komentar langsung pada field yang tersedia di setiap kartu laporan. Klik <strong>"Simpan Review"</strong> untuk menyimpan komentar. Komentar yang sudah disimpan dapat diedit kembali kapan saja.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #EEF2FF; color: #6366F1; font-size: 12px;">
                    <i class="fa fa-magic"></i>
                </div>
                <div class="badge-desc">
                    <strong>Tombol AI Review (ungu)</strong>
                    <span>Klik tombol ini untuk meminta AI menganalisis laporan minggu tersebut dan menghasilkan draf komentar review secara otomatis. Draf ini akan langsung mengisi kolom komentar dan dapat Anda edit sebelum disimpan.</span>
                </div>
            </div>
            <div class="badge-item">
                <div class="badge-dot" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #D97706; font-size: 11px;">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="badge-desc">
                    <strong>Indikator "Tidak Lengkap"</strong>
                    <span>Muncul apabila ada hari kerja dalam minggu tersebut yang tidak diisi laporan hariannya oleh mahasiswa. Informasi ini membantu dosen memantau kedisiplinan mahasiswa dalam mengisi jurnal harian.</span>
                </div>
            </div>
        </div>
    </div>

</div>
