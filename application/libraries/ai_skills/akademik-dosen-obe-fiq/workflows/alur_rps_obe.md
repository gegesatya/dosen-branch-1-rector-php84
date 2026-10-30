# Alur Penyusunan RPS OBE

1. **Langkah pertama, sebelum apa pun lainnya: minta data penelitian dosen sebagai referensi pendukung RPS.** Tanyakan minimal nama dosen/peneliti, judul penelitian/publikasi, tahun, jenis sumber, DOI/URL/identitas, dan relevansinya dengan mata kuliah yang akan dibuatkan RPS. Cek dulu apakah pengguna sudah melampirkan daftar penelitian/CV/SINTA/Google Scholar di percakapan (lihat `workflows/alur_anti_ngarang.md`) sebelum bertanya ulang secara teks. Jangan mulai menyusun bagian RPS mana pun sebelum ini ditanyakan. Jika pengguna belum/tidak mau memberi datanya, lanjutkan dengan placeholder eksplisit "Penelitian dosen: [diisi oleh dosen/prodi]" dan catat sebagai data yang belum lengkap.
2. **Minta dokumen kurikulum prodi**, diminta bersamaan dengan poin 1 di pesan yang sama (dokumen kurikulum lengkap, atau minimal profil lulusan, CPL prodi, peta kurikulum/daftar mata kuliah, dan bahan kajian). Cek dulu apakah sudah dilampirkan di percakapan; kalau belum, minta pengguna mengupload/melampirkannya sebelum lanjut ke langkah berikutnya. Ini jadi sumber CPL, posisi mata kuliah dalam struktur kurikulum, dan bahan kajian — bukan ditanyakan terpisah per item seperti data minimum lain.
3. Dari dokumen kurikulum tersebut, ekstrak: CPL yang relevan dengan mata kuliah, bahan kajian yang dibebankan, dan posisi mata kuliah dalam struktur kurikulum (semester, prasyarat).
4. **Jika dokumen kurikulum tidak memuat CPMK/Sub-CPMK** (ini wajar — CPMK/Sub-CPMK umumnya level mata kuliah/RPS, bukan level dokumen kurikulum prodi), tanyakan ke pengguna:
   - Apakah pengguna sudah punya rumusan CPMK/Sub-CPMK mata kuliah ini (mis. dari RPS versi sebelumnya)? Jika ya, minta/pakai itu.
   - Jika belum ada sama sekali, tawarkan untuk **menurunkan CPMK dan Sub-CPMK dari CPL** yang sudah diekstrak, mengikuti alur Mode CPL/CPMK/Sub-CPMK (data minimum bagian C di `SKILL.md`, gunakan `references/bank_bloom_c4_c5_c6.md` untuk kata kerja C4-C6).
   - **Sebelum merumuskan, tanyakan atau rekomendasikan dulu jumlah CPMK dan jumlah Sub-CPMK yang akan dipakai.** Jangan asumsikan otomatis 1 Sub-CPMK = 1 pertemuan — itu jarang realistis. Gunakan panduan berikut sebagai rekomendasi awal (bukan aturan kaku, tetap konfirmasi ke pengguna):
     - **Jumlah CPMK**: biasanya 2-6 per mata kuliah, mengikuti jumlah klaster bahan kajian utama/kompetensi besar yang dibebankan CPL ke MK tersebut — bukan mengikuti jumlah pertemuan.
     - **Jumlah Sub-CPMK**: dipetakan ke pertemuan efektif (biasanya 14 pertemuan materi dari total 16, tidak termasuk UTS/UAS), tapi **tidak harus satu-satu**. Topik kompleks/berat bisa memakai 1 Sub-CPMK untuk 2-3 pertemuan berurutan; topik ringan bisa 1 pertemuan 1 Sub-CPMK. Jadi jumlah Sub-CPMK realistis biasanya lebih sedikit dari jumlah pertemuan (kira-kira 6-12 Sub-CPMK untuk 14 pertemuan efektif), bukan otomatis sama dengan 14/16.
     - Sampaikan rekomendasi ini ke pengguna beserta alasannya, lalu minta konfirmasi/penyesuaian sebelum lanjut merumuskan isi tiap CPMK/Sub-CPMK.
   - Rumusan CPMK/Sub-CPMK hasil turunan ini **wajib diberi label draf usulan** dan ditandai `[PERLU VALIDASI PRODI/TIM KURIKULUM]` — jangan diperlakukan seolah sudah final/resmi, karena idealnya CPMK/Sub-CPMK ditetapkan lewat rapat prodi/tim pengembang RPS.
5. Kumpulkan sisa data minimum RPS yang belum tercakup dari dokumen kurikulum atau dari langkah 1 (lihat `SKILL.md` bagian data minimum: SKS, bentuk/metode pembelajaran, rencana bobot asesmen, jumlah minggu, kebijakan TM/PT/BM kampus).
6. Validasi CPL, CPMK, dan Sub-CPMK.
7. Pastikan kata kerja utama hanya C4-C6.
8. Susun matriks CPL-CPMK-Sub-CPMK.
9. Susun bahan kajian dan materi mingguan.
10. Hitung SKS, TM/PT/BM, dan total jam.
11. Susun aktivitas pembelajaran berbasis SCL.
12. Susun asesmen dan bobot 100%.
13. Masukkan penelitian dosen (dari data yang sudah diminta di langkah 1) sebagai referensi pendukung, dipetakan ke CPMK/Sub-CPMK dan minggu yang relevan.
14. Susun rubrik.
15. Jalankan checklist audit.

Jika pengguna benar-benar tidak punya dokumen kurikulum (mis. kurikulum belum ada/masih disusun), boleh lanjut dengan CPL/bahan kajian yang diketik manual oleh pengguna — tetap ikuti aturan anti-ngarang (`workflows/alur_anti_ngarang.md`), dan beri catatan bahwa RPS ini belum diverifikasi terhadap dokumen kurikulum resmi. Aturan ini tidak menggantikan langkah 1 — data penelitian dosen tetap wajib diminta di awal terlepas dari ada/tidaknya dokumen kurikulum.
