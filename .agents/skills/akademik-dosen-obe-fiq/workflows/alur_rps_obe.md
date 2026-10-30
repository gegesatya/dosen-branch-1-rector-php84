# Alur Penyusunan RPS OBE

1. **Source-First Kurikulum & Pemetaan CPL ↔ MK:**
   - Ambil data faktual dari kurikulum prodi (profil lulusan, CPL prodi, bahan kajian, mapping CPL ↔ Mata Kuliah, SKS, semester, deskripsi MK) sebagai **source of truth (`FACTUAL`)**. Data faktual ini **tidak boleh dibuat ulang oleh AI**.
   - Jika mapping CPL ↔ MK tersedia di kurikulum, **gunakan persis mapping tersebut**; AI tidak boleh memilih ulang CPL.
   - Jika mapping tidak tersedia, AI mengusulkan pemetaan dengan status `AI_PROPOSAL` untuk direview dosen.
2. **Integrasi Penelitian Dosen sebagai Pengayaan (Non-Blocking):**
   - Cek apakah data penelitian dosen/publikasi/CV sudah dilampirkan pengguna. Jika tersedia, integrasikan sebagai referensi pengayaan pembelajaran pada minggu/topik relevan secara tepat.
   - Jika belum ada/tidak tersedia, penyusunan RPS **tetap berjalan normal tanpa integrasi penelitian dosen**. Dilarang keras mengarang penelitian dosen, dan jangan membuat konten atau placeholder yang dapat dianggap sebagai penelitian dosen pada dokumen final.
3. **Penyusunan & Approval CPMK:**
   - Jika CPMK resmi sudah tersedia, gunakan sebagai acuan (`FACTUAL`/`APPROVED`).
   - Jika belum tersedia, AI langsung membuat **Draft CPMK (`AI_PROPOSAL`)** berdasarkan: CPL yang dibebankan, bahan kajian, deskripsi MK, capaian MK existing (jika ada), SKS, semester, serta bentuk teori/praktik.
   - **Tanpa dialog pemblokiran:** AI langsung merekomendasikan jumlah CPMK yang proporsional (biasanya 2–6 CPMK) dan menyajikan draft editable.
   - Dosen dapat langsung mengedit, menambah, menghapus, menggabungkan, memecah, atau mengurutkan CPMK (status menjadi `MANUAL`/`APPROVED`).
   - CPMK hasil usulan AI baru menjadi dasar resmi setelah disetujui dosen (`APPROVED`).
4. **Penyusunan Sub-CPMK:**
   - Sub-CPMK **hanya disusun setelah CPMK berstatus `APPROVED`**.
   - AI langsung menyajikan draft Sub-CPMK editable (`AI_PROPOSAL`) tanpa perlu konfirmasi jumlah di depan.
   - Satu Sub-CPMK dapat dialokasikan untuk **satu atau beberapa pertemuan** (jangan mengasumsikan 1 pertemuan = 1 Sub-CPMK; materi berat/kompleks dapat menggunakan 1 Sub-CPMK untuk 2–3 pertemuan).
   - **Validasi Ulang Dependensi:** Jika CPMK `APPROVED` diubah kembali, Sub-CPMK dan artefak turunannya harus dianggap memerlukan validasi ulang sebelum dapat dinyatakan final.
5. **Penerapan Taksonomi Bloom C2–C6:**
   - Gunakan kata kerja operasional **Bloom C2–C6** secara proporsional sesuai karakter mata kuliah, semester, dan jenis capaian (berprogresi dari pemahaman/penerapan konseptual menuju analisis/evaluasi/kreasi).
   - Taksonomi Bloom digunakan untuk **alignment outcome, progresi kompetensi, pemilihan aktivitas, dan pemilihan asesmen** (bukan sebagai faktor bobot numerik otomatis).
   - Jangan menaikkan level Bloom secara artifisial demi formalitas; utamakan alignment antara outcome, aktivitas, dan asesmen.
   - Jangan menuliskan label/kode Bloom (C2–C6) di dokumen output RPS kecuali diminta pengguna.
6. **Penyusunan Komponen Matriks & Pembelajaran:**
   - Susun matriks CPL ↔ CPMK ↔ Sub-CPMK.
   - Susun bahan kajian dan materi mingguan (pertemuan 1–16).
   - Hitung rekap SKS, TM/PT/BM, dan total jam belajar (1 SKS = 45 jam = 2.700 menit per semester).
   - Susun aktivitas pembelajaran berbasis SCL (Case Method, PjBL, diskusi/eksplorasi).
   - Susun asesmen dan normalisasi bobot total 100%.
   - Susun rubrik analitik penilaian.
7. **Self-Check Dokumen:**
   - Jalankan `checklists/checklist_audit_obe.md` sebelum memberikan output akhir.
