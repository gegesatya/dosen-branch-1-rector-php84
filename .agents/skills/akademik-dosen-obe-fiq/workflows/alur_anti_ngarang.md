# Alur Anti-Ngarang & Provenance Data

1. **Source-First Data Faktual (`FACTUAL`):**
   - Dokumen kurikulum resmi, profil lulusan, CPL prodi, bahan kajian, pemetaan CPL ↔ MK, SKS, semester, dan deskripsi MK adalah *source of truth* faktual. Data ini **tidak boleh dibuat ulang oleh AI**.
   - Jika mapping CPL ↔ MK tersedia di kurikulum, gunakan persis mapping tersebut tanpa memilih ulang CPL.
2. **Kategori Status Sumber (Provenance):**
   - `FACTUAL`: Berasal dari database atau dokumen kurikulum resmi institusi.
   - `AI_PROPOSAL`: Draf usulan AI yang belum disetujui dosen/tim kurikulum.
   - `MANUAL`: Dibuat atau diubah langsung oleh dosen.
   - `APPROVED`: Telah disetujui dosen/prodi dan sah menjadi dasar tahap penyusunan berikutnya.
3. **Ekstraksi Dokumen Lampiran:**
   - Cek dokumen lampiran/upload percakapan (kurikulum, RPS lama, daftar penelitian/CV dosen) terlebih dahulu sebelum menanyakan secara teks.
4. **Penyusunan CPMK & Sub-CPMK:**
   - CPMK yang belum tersedia disusun oleh AI sebagai `AI_PROPOSAL` dan disajikan langsung sebagai draf editable tanpa memblokir konfirmasi jumlah di depan.
   - Sub-CPMK hanya diturunkan dari CPMK yang sudah `APPROVED`.
5. **Penelitian Dosen sebagai Pengayaan (Non-Blocking):**
   - Integrasikan penelitian dosen jika data tersedia. Jika tidak tersedia, proses RPS tetap berjalan tanpa integrasi penelitian dosen. Dilarang keras mengarang data/judul penelitian, dan jangan membuat konten atau placeholder yang dapat dianggap sebagai penelitian dosen pada dokumen final.
6. **Validasi Dependensi:**
   - Jika CPMK `APPROVED` diubah kembali, Sub-CPMK dan artefak turunannya harus dianggap memerlukan validasi ulang sebelum dapat dinyatakan final.
