# Kalkulator Evaluasi Ketercapaian CPMK (Nilai dan Kelulusan)

## A. Nilai CPMK per mahasiswa

Input:
- peta instrumen asesmen -> CPMK/Sub-CPMK beserta bobotnya (dari RPS mata kuliah);
- nilai mahasiswa per instrumen asesmen (tugas, kuis, UTS, UAS, proyek, praktikum, dll).

Rumus:

Nilai CPMK (mahasiswa) = Σ (Bobot instrumen yang memetakan ke CPMK tsb × Nilai mahasiswa pada instrumen tsb) / Σ Bobot instrumen yang memetakan ke CPMK tsb

Normalisasi bobot instrumen per CPMK ke total 100% terlebih dulu, agar Nilai CPMK tetap berskala sama dengan skala nilai mahasiswa (mis. 0-100).

## B. Status ketercapaian individual

- **Tercapai**: Nilai CPMK mahasiswa ≥ Nilai Batas Minimal (NBM).
- **Belum Tercapai**: Nilai CPMK mahasiswa < NBM.

NBM wajib diambil dari kebijakan institusi/prodi — jangan mengarang. Jika pengguna belum punya dan minta contoh angka sebagai titik awal, boleh tawarkan ilustrasi umum (mis. setara nilai C / skor 56 dari skala 100), tetapi wajib diberi label **[ASUMSI DEFAULT - PERLU VALIDASI PRODI/SPMI]** dan dicatat di Catatan Validasi.

## C. Ketercapaian klasikal per CPMK

Persentase Ketercapaian Klasikal = (Jumlah mahasiswa Tercapai pada CPMK tsb / Total mahasiswa) × 100%

Bandingkan dengan Target Ketercapaian Klasikal institusi. Jika belum ada dan pengguna minta contoh, ilustrasi umum yang banyak dipakai PT adalah ≥80% — tetap wajib label **[ASUMSI DEFAULT - PERLU VALIDASI PRODI/SPMI]** bila dipakai tanpa konfirmasi resmi.

Kategori tingkat ketercapaian klasikal (skala ilustratif, boleh disesuaikan kebijakan institusi jika ada):
- Sangat Baik: ≥ target + 10 poin persentase
- Baik: mencapai target sampai dengan + 10 poin persentase
- Cukup: di bawah target tetapi ≥ (target − 15 poin persentase)
- Kurang: di bawah (target − 15 poin persentase)

## D. Nilai akhir dan kelulusan mata kuliah

Nilai Akhir Mata Kuliah (mahasiswa) = Σ (Bobot CPMK terhadap mata kuliah × Nilai CPMK mahasiswa)

Bobot CPMK terhadap mata kuliah mengikuti bobot yang sudah ditetapkan di RPS (lihat `calculators/kalkulator_sks_bobot.md`) — bukan dibagi rata tanpa alasan.

Status kelulusan mata kuliah (mahasiswa):
- **Lulus**: Nilai Akhir ≥ ambang kelulusan mata kuliah institusi (nilai dan/atau huruf mutu minimal yang berlaku).
- **Tidak Lulus**: Nilai Akhir < ambang tsb.

Ambang kelulusan mata kuliah wajib diambil dari kebijakan institusi (pedoman akademik/SIAKAD) — jangan mengarang.

## E. Rekap kelas

- jumlah dan persentase mahasiswa Lulus vs Tidak Lulus mata kuliah;
- rata-rata dan sebaran (min/maks) Nilai Akhir kelas;
- daftar CPMK yang ketercapaian klasikalnya "Belum Tercapai" -> lanjut ke Rencana Tindak Lanjut (RTL) mengikuti siklus PPEPP.
