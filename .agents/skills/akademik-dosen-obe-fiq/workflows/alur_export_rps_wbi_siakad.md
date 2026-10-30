# Alur Export RPS ke Format Rincian Pertemuan WBI (untuk Upload SIAKAD)

Gunakan alur ini ketika pengguna minta RPS dalam **format resmi WBI** agar bisa diupload ke SIAKAD, atau minta "rincian pertemuan RPS", "export RPS ke Excel WBI", dsb.

## Konteks

- File `templates/template_rps_wbi_rincian_pertemuan.xlsx` adalah **template resmi bagian Rincian Pertemuan** dari sistem SIAKAD WBI. Ini BUKAN keseluruhan RPS — bagian identitas mata kuliah, CPL, CPMK, dan bobot penilaian keseluruhan diisi terpisah lewat form lain di SIAKAD (bukan lewat file ini).
- Template ini murni berisi **matriks 16 minggu** dengan kolom baku sistem: Minggu-Ke, Pertemuan, Kemampuan Akhir Tiap Tahapan Belajar, Bahan Kajian, Bentuk dan Modalitas Pembelajaran, Metode Pembelajaran, Pengalaman Belajar, Alokasi Waktu (Teori, Praktek), Evaluasi/Penilaian (Kriteria, Indikator, Bobot), Sumber Belajar.
- Baris 1 (`KOLOM_1`...`KOLOM_13`) adalah **header teknis untuk mapping import SIAKAD** — jangan pernah diubah, dihapus, atau diberi nilai lain.
- Baris 2-3 adalah header manusia (merged cells), baris 4 adalah nomor kolom `(1)`-`(13)`, baris 5 adalah tipe data per kolom (`Integer`/`Text`). Baris-baris ini adalah metadata struktur sistem — **jangan diubah**.
- Baris 6-21 adalah 16 baris data (Minggu-Ke dan Pertemuan sudah terisi 1-16 di kolom A dan B secara default) — ini satu-satunya bagian yang diisi.

## Langkah kerja

1. **Pastikan data mingguan RPS sudah lengkap** (dari Mode RPS OBE / data minimum bagian A di `SKILL.md`). Jika belum ada rincian per minggu (Sub-CPMK, bahan kajian, bentuk pembelajaran, metode, pengalaman belajar, sumber belajar per minggu), tanyakan dulu — jangan mengarang isi per minggu.
2. **Konfirmasi data tambahan yang khusus dibutuhkan format ini** dan belum tentu sudah ditanyakan di Mode RPS OBE biasa:
   - Pengalaman belajar mahasiswa per minggu (aktivitas konkret, bukan cuma kemampuan akhir).
   - Sumber belajar per minggu (bisa beda-beda tiap minggu, bukan daftar pustaka global).
   - Kriteria dan indikator evaluasi per minggu (bukan cuma bobot total).
   - Satuan Alokasi Waktu Teori/Praktek yang dipakai SIAKAD WBI (menit atau jam). Jika belum jelas, **tanyakan ke pengguna**; jangan asumsikan. Jika pengguna tidak tahu, gunakan menit per pertemuan hasil `calculators/kalkulator_sks_bobot.md` sebagai default sementara dan beri catatan **[PERLU KONFIRMASI SATUAN SIAKAD]**.
3. **Salin `templates/template_rps_wbi_rincian_pertemuan.xlsx` ke working directory**, jangan edit file template aslinya di folder skill.
4. **Isi dengan `openpyxl`, load_workbook biasa (bukan `data_only=True`)**:
   - Isi hanya kolom C sampai M pada baris 6-21 (minggu 1-16). Jangan sentuh baris 1-5 atau kolom A/B.
   - Kolom H (Teori), I (Praktek), dan L (Bobot) wajib berupa **integer**, sesuai tipe data baris 5 — jangan tulis desimal atau teks di kolom ini.
   - Kolom lain (C, D, E, F, G, J, K, M) berupa teks bebas sesuai isi RPS yang sudah divalidasi/disediakan pengguna.
   - Jika suatu minggu tidak ada evaluasi, boleh kosongkan sel J/K/L untuk minggu itu (bukan diisi 0 atau "-" kalau memang tidak relevan), kecuali kebijakan kampus meminta lain.
   - Jangan mengubah styling/format kolom kecuali diminta; ikuti prinsip "match file conventions exactly" dari skill xlsx.
5. **Verifikasi sebelum menyimpan sebagai output akhir:**
   - Total baris tetap 16 (minggu 1-16), kecuali pengguna secara eksplisit menyatakan jumlah pertemuan efektif berbeda dan meminta struktur khusus.
   - Total kolom L (Bobot) yang terisi across 16 minggu **rekonsili** dengan total bobot 100% dari `calculators/kalkulator_sks_bobot.md`/tabel bobot penilaian keseluruhan RPS. Jika tidak sama, jelaskan selisihnya (mis. karena UTS/UAS direkap terpisah di SIAKAD) — jangan diam-diam memaksakan angka supaya pas 100%.
   - Tidak ada label kode Bloom (C4/C5/C6) yang ikut tertulis di kolom "Kemampuan Akhir Tiap Tahapan Belajar" (lihat aturan format output di `SKILL.md`).
6. **Simpan file** dengan nama yang jelas, misalnya `RPS_[KodeMK]_[NamaMK]_RincianPertemuan_WBI.xlsx`.
7. Beri catatan singkat ke pengguna: file ini hanya bagian Rincian Pertemuan; bagian identitas MK/CPL/CPMK/bobot keseluruhan tetap diisi lewat form SIAKAD terpisah.
