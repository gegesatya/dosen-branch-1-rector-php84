# Alur Evaluasi Ketercapaian CPMK Berdasarkan Nilai dan Kelulusan

## Kapan dipakai

Trigger: "evaluasi CPMK", "ketercapaian CPMK", "apakah CPMK tercapai", "evaluasi capaian pembelajaran mata kuliah", "rekap nilai per CPMK", "evaluasi akhir semester", "laporan ketercapaian CPMK/CPL", atau permintaan terkait siklus PPEPP untuk mata kuliah yang sudah berjalan.

## Data minimum wajib (source-first, jangan mengarang — lihat `alur_anti_ngarang.md`)

1. **Peta Sub-CPMK/CPMK -> instrumen asesmen -> bobot**, diambil dari RPS mata kuliah bersangkutan. Jika RPS mata kuliah ini sudah dibuat/dilampirkan di percakapan atau folder kerja, gunakan itu; jangan minta ulang.
2. **Nilai mahasiswa per instrumen asesmen** (tugas, kuis, UTS, UAS, proyek, praktikum, dst.) — idealnya berupa tabel/file (Excel/CSV) yang diunggah pengguna.
3. **Skala nilai** yang dipakai institusi (0-100, 0-4, huruf mutu, dll.) dan **Nilai Batas Minimal/KKM** kelulusan per CPMK sesuai kebijakan prodi/institusi.
4. **Target ketercapaian klasikal** per CPMK sesuai kebijakan mutu/SPMI institusi.
5. **Identitas dan jumlah mahasiswa** (NIM/nama atau kode anonim) pada kelas/mata kuliah yang dievaluasi.
6. **Ambang kelulusan mata kuliah** (nilai akhir minimal dan/atau huruf mutu) sesuai pedoman akademik institusi.

Jika data 1-4 dan 6 belum lengkap, tanyakan dulu sebelum menghitung apa pun. Jika pengguna minta tetap dihitung dengan angka contoh, boleh, tetapi seluruh hasil wajib diberi label **[ASUMSI DEFAULT - PERLU VALIDASI PRODI/SPMI]** dan dicatat di Catatan Validasi. Jangan pernah mengarang nilai mahasiswa.

## Langkah perhitungan

Gunakan rumus di `calculators/kalkulator_evaluasi_cpmk.md` — jangan menghitung dari perkiraan.

1. Hitung Nilai CPMK per mahasiswa dari nilai instrumen asesmen yang memetakan ke CPMK tsb, sesuai bobotnya.
2. Tentukan status ketercapaian individual per mahasiswa per CPMK: Tercapai/Belum Tercapai terhadap NBM.
3. Hitung persentase ketercapaian klasikal per CPMK, bandingkan dengan target institusi, tentukan status dan kategori (Sangat Baik/Baik/Cukup/Kurang).
4. Hitung Nilai Akhir Mata Kuliah per mahasiswa dari agregasi Nilai CPMK sesuai bobot CPMK terhadap mata kuliah — bukan rata-rata sederhana, kecuali institusi menetapkan lain.
5. Tentukan status kelulusan mata kuliah per mahasiswa (Lulus/Tidak Lulus) terhadap ambang kelulusan institusi.
6. Rekap kelas: jumlah/persentase lulus-tidak lulus, rata-rata dan sebaran nilai akhir.
7. Untuk setiap CPMK yang ketercapaian klasikalnya "Belum Tercapai" atau berkategori "Kurang", susun Rencana Tindak Lanjut (RTL) mengikuti siklus PPEPP: kemungkinan penyebab, opsi perbaikan (revisi RPS/metode/instrumen asesmen/remedial/pengayaan), dan pihak penanggung jawab tindak lanjut (dosen/prodi/SPMI).

## Format output

Gunakan `templates/template_evaluasi_cpmk.md`, berisi:
- tabel Nilai CPMK per Mahasiswa (mahasiswa × CPMK);
- tabel Rekap Ketercapaian Klasikal per CPMK;
- tabel Nilai Akhir dan Status Kelulusan Mata Kuliah per Mahasiswa;
- ringkasan kelas;
- Rencana Tindak Lanjut (RTL) untuk CPMK yang belum tercapai;
- Catatan Validasi dan status dokumen (Final/Draf Usulan Awal).

## Versi Excel dengan formula otomatis

Jika pengguna secara eksplisit minta bentuk file (bukan hanya tabel di chat) — mis. "buatkan file excel-nya", "template excel bobot asesmen ke CPMK", "saya mau input nilai langsung di Excel" — gunakan `templates/template_evaluasi_cpmk_bobot_asesmen.xlsx` sebagai dasar:

1. **Salin dulu** template ke folder kerja mata kuliah (jangan edit file template aslinya secara langsung), lalu ganti judul `[Nama Mata Kuliah]` di sheet Petunjuk, Bobot CPMK, Peta CPL, Input Nilai, Nilai CPMK per Mahasiswa, dan Evaluasi Ketercapaian CPMK.
2. Isi sheet **Bobot CPMK**: kode CPMK, nama Sub-CPMK, bahan kajian, bobot instrumen (kolom QUI/TTS/UTS/UAS/UKN/PRY/POR/PRD/PRE/Sikap — 9+1 komponen resmi Politeknik WBI; kolom yang tidak dipakai MK ini dibiarkan 0), Bobot thd CPMK (%), serta tiga parameter di baris 17-19 (Nilai Batas Lulus MK, Batas Nilai "Tercapai" per mahasiswa, Batas % Kelas) — ambil dari kebijakan institusi/SPMI, jangan mengarang; jika pengguna minta contoh sementara, tandai sebagai **[ASUMSI DEFAULT - PERLU VALIDASI PRODI/SPMI]** di chat.
3. Isi sheet **Peta CPL**: daftar kode+rumusan CPL yang dibebankan, lalu tabel pemetaan CPMK→CPL (isi kode CPMK yang PERSIS sama dengan yang dipakai di sheet Bobot CPMK, karena rumus mencocokkan berdasarkan teks kode ini).
4. Sheet **Input Nilai** adalah satu-satunya sheet yang diisi dosen rutin (NIM, nama, skor 0-100 per instrumen per Sub-CPMK, dan Sikap satu kali per mahasiswa). Sheet **Nilai CPMK per Mahasiswa** dan **Evaluasi Ketercapaian CPMK** terisi otomatis — jangan diedit manual.
5. Template menyediakan kapasitas maksimum 8 Sub-CPMK, 8 CPMK unik, 6 kode CPL, dan 30 mahasiswa. Baris yang tidak dipakai boleh dibiarkan kosong. Jika mata kuliah butuh lebih dari itu, salin pola kolom/baris yang sudah ada dan sesuaikan rentang rumus secara konsisten dengan pola yang sama — jangan menyusun rumus baru dari nol.
6. Setelah file terisi, tetap jalankan pengecekan: total bobot instrumen tiap Sub-CPMK = 100% (indikator "OK 100%"/"!! PERIKSA" di sheet Bobot CPMK), total Bobot thd CPMK per kelompok CPMK = 100% (kolom Status), dan total Bobot thd MK seluruh Sub-CPMK = 100%.

## Batasan

- Jangan mengarang nilai mahasiswa, NBM, ambang kelulusan, atau target ketercapaian klasikal jika tidak diberikan pengguna.
- Jika nilai yang tersedia belum dipecah/dipetakan ke CPMK (mis. hanya ada nilai UTS/UAS/tugas total), tanyakan pemetaannya, atau tawarkan menyusun pemetaan berdasarkan RPS yang sudah ada dengan konfirmasi pengguna terlebih dahulu — jangan langsung mengasumsikan pemetaannya sendiri.
- Evaluasi ini adalah evaluasi ketercapaian CPMK **tingkat mata kuliah**, bukan pengganti evaluasi CPL prodi. Evaluasi CPL memerlukan agregasi lintas beberapa mata kuliah dan pemetaan CPMK-CPL yang sudah ditetapkan kurikulum; jika pengguna meminta evaluasi CPL, jelaskan kebutuhan data tambahan ini terlebih dahulu.
