# Alur Roadmap Pembelajaran (Format WBI)

## Kapan dipakai

Trigger: "roadmap pembelajaran", "roadmap RPS", "lihat roadmapnya", "roadmap format WBI", "peta modul per pertemuan", "buatkan roadmap mata kuliah ini". **Hanya berlaku setelah Sub-CPMK mata kuliah yang bersangkutan sudah tersusun** (dari sesi ini, atau dari RPS yang sudah ada/dilampirkan pengguna). Jika Sub-CPMK belum ada, jalankan dulu Mode CPL/CPMK/Sub-CPMK atau minta RPS yang sudah jadi — jangan membuat roadmap dari Sub-CPMK yang dikarang.

## Prasyarat data (source-first, jangan mengarang — lihat `alur_anti_ngarang.md`)

- Rumusan Learning Outcome/CPMK level mata kuliah (jadi judul besar roadmap).
- Daftar Sub-CPMK lengkap beserta: rumusan kemampuan akhir, bahan kajian/materi, indikator capaian, dan metode/model pembelajaran (Learning Strategy) per Sub-CPMK/pertemuan — semua ini seharusnya sudah ada di RPS yang sudah disusun sebelumnya.
- Nama mata kuliah (untuk label vertikal di sisi kanan tabel).
- Jumlah pertemuan efektif: **default 16** (14 pertemuan materi + UTS di pertemuan 8 + UAS di pertemuan 16), mengikuti pola RPS institusi. Jika RPS memakai jumlah/posisi UTS-UAS berbeda, ikuti itu.

Jika salah satu data di atas belum ada di percakapan/RPS yang sudah dibuat, tanyakan dulu sebelum menyusun roadmap.

## Langkah pengelompokan Modul

1. Kelompokkan Sub-CPMK yang berdekatan dan serumpun (bahan kajian/tema yang sama atau berurutan secara logis) menjadi satu Modul — satu Modul boleh mencakup satu atau beberapa pertemuan berurutan, mengikuti pola: Modul dasar/pengantar biasanya 1 pertemuan, Modul inti/kompleks bisa beberapa pertemuan.
2. **Tampilkan usulan pengelompokan Modul ke pengguna (Modul apa saja, mencakup pertemuan berapa saja dan Sub-CPMK mana saja) dan minta konfirmasi sebelum membangun file** — pengelompokan ini bersifat interpretatif, jangan difinalkan sepihak.
3. Pertemuan UTS dan UAS selalu jadi kolom tersendiri (bukan bagian Modul manapun), dengan label "UTS"/"UAS" di semua baris isi kecuali baris "Meeting".
4. Jangan mengubah urutan pertemuan asli RPS saat mengelompokkan ke Modul.

## Isi tiap sel — diturunkan dari data Sub-CPMK, bukan dikarang ulang

- **Output** (per Modul): rangkuman/gabungan rumusan kemampuan akhir dari Sub-CPMK yang tercakup di Modul tsb. Reformulasi ringkas boleh, tetapi tidak boleh menambah kompetensi yang tidak ada di Sub-CPMK aslinya.
- **Concept**: bahan kajian/materi dari Sub-CPMK yang tercakup, ditulis sebagai poin-poin singkat (bukan kalimat panjang bersambung).
- **Indicators**: indikator capaian dari Sub-CPMK yang tercakup, kalimat "Mahasiswa dapat/mampu ...", diambil langsung dari rumusan Sub-CPMK/indikator yang sudah ada.
- **Learning Strategy**: metode/model pembelajaran (mis. Discovery Learning, Problem/Project-Based Learning, Case-Based Learning) yang sudah ditentukan untuk pertemuan dalam Modul tsb, plus deskripsi singkat aktivitas jika sudah tersedia di RPS.
- Baris "Meeting": nomor pertemuan 1..N sesuai kolom.
- Baris header Modul (paling atas tabel): nama Modul + rentang topik singkat, digabung (merge) menutupi semua kolom pertemuan anggotanya.
- Judul besar di atas tabel: rumusan Learning Outcome/CPMK level mata kuliah, huruf kapital, digabung menutupi seluruh lebar tabel.
- Label mata kuliah: teks vertikal (rotasi 90°) di kolom paling kanan, mencakup semua baris tabel.

## Format visual file Excel

Untuk deliverable roadmap ini, ikuti permintaan visual eksplisit pengguna (warna per Modul + merge cell) — ini berlaku khusus untuk roadmap, bukan pengecualian umum terhadap preferensi grayscale pengguna di deliverable lain:

- Tiap Modul diberi warna latar pastel berbeda, konsisten di semua baris Modul tsb (kecuali baris "Meeting" yang tetap putih dengan border tebal).
- Kolom UTS dan UAS selalu berwarna kuning terang.
- Semua sel memakai border tipis hitam; teks rata kiri-atas dengan wrap text untuk sel isi panjang; judul Modul dan label baris kiri (Output/Concept/Indicators/Learning Strategy) ditebalkan (bold).
- Bangun file ini dengan skill `xlsx` (lihat `/mnt/skills/public/xlsx/SKILL.md`) menggunakan openpyxl — bukan markdown/tabel teks.

## Self-check sebelum file final diberikan

- [ ] Semua Sub-CPMK di RPS sudah masuk ke salah satu Modul (tidak ada yang tertinggal).
- [ ] Total pertemuan pada roadmap = total pertemuan pada RPS, termasuk posisi UTS/UAS yang sama.
- [ ] Isi Output/Concept/Indicators/Learning Strategy tidak mengarang kompetensi atau topik baru di luar yang sudah ada di RPS/Sub-CPMK.
- [ ] Pengelompokan Modul sudah dikonfirmasi pengguna sebelum file final dibangun.
- [ ] Nama mata kuliah dan rumusan Learning Outcome pada judul sesuai data yang diberikan pengguna, bukan dikarang.
