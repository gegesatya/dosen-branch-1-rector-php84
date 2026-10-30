# Alur Struktur Folder Kerja (Mode Cowork/Project)

Berlaku saat skill ini dipakai lewat mode Cowork atau di dalam sebuah Project yang tersambung ke folder kerja pengguna (bukan chat biasa tanpa akses folder).

## Struktur folder baku

Pengguna hanya menyiapkan **satu folder utama** (misalnya `Akademik 2026`), tidak dibuat per mata kuliah/per topik secara manual. Semua folder di bawahnya dibuat dan dirapikan sendiri oleh skill ini mengikuti pola berikut:

```
Akademik 2026/                          (folder utama, dibuat sekali oleh pengguna)
├── Peraturan & Kurikulum/              (referensi tetap, BUKAN per mata kuliah)
│   ├── dokumen kurikulum prodi
│   ├── SK/regulasi terkait
│   └── acuan lain yang berlaku lintas mata kuliah
├── [Nama Mata Kuliah A]/
│   ├── RPS, Modul, LKM, PPT untuk MK ini (di root folder MK)
│   ├── Pertemuan 1/
│   ├── Pertemuan 2/
│   └── Pertemuan .../
├── [Nama Mata Kuliah B]/
│   ├── Pertemuan 1/
│   └── Pertemuan .../
└── [Mata Kuliah lain, dst. — pola sama]
```

## Aturan penempatan file

1. **Folder `Peraturan & Kurikulum/`** khusus untuk dokumen yang berlaku lintas mata kuliah: kurikulum prodi, SK kurikulum, regulasi, daftar penelitian dosen umum, dll. Jangan menaruh dokumen ini di dalam folder mata kuliah tertentu.
2. **Folder per mata kuliah** dibuat dengan nama mata kuliah apa adanya (sesuai yang disebut pengguna), bukan disingkat/diterjemahkan sendiri.
3. **Dokumen tingkat mata kuliah** (RPS, Modul/bahan ajar, LKM, PPT yang berlaku untuk satu semester penuh) ditaruh di root folder mata kuliah tersebut — bukan di dalam folder Pertemuan.
4. **Folder `Pertemuan N/`** dipakai untuk materi/berkas yang spesifik ke satu sesi pertemuan (mis. slide pertemuan tertentu, LKM pertemuan tertentu, rekaman, dsb.) — buat sesuai jumlah pertemuan yang diminta pengguna, jangan membuat 16 folder pertemuan sekaligus kalau yang diminta baru beberapa.
5. Sebelum membuat file baru, **cek dulu struktur folder yang sudah ada** di working folder (jangan asumsikan kosong). Jika folder mata kuliah/pertemuan yang relevan sudah ada, isi ke situ — jangan membuat folder duplikat dengan nama mirip.
6. Jika pengguna belum pernah menyebut nama mata kuliah sebelumnya dalam sesi ini, tanyakan nama mata kuliahnya dulu sebelum membuat folder baru (supaya tidak salah nama/duplikat).
7. Skill ini **tidak membuat folder pribadi/acak** di luar pola di atas. Kalau ada kebutuhan folder di luar pola (mis. folder khusus akreditasi), tanyakan dulu ke pengguna di mana sebaiknya ditempatkan.

## Yang dikomunikasikan ke pengguna

- Pengguna cukup menyebutkan **nama folder utama sekali di awal** (atau membiarkan skill memakai folder kerja yang sudah tersambung ke sesi Cowork/Project).
- Setelah itu, pengguna tinggal meminta dokumen apa pun (RPS mata kuliah A, LKM pertemuan 3 mata kuliah B, dst.) — skill yang menentukan lokasi folder yang tepat mengikuti pola di atas, tanpa perlu diarahkan manual setiap kali.
- Kalau skill baru saja membuat folder baru (mata kuliah baru atau pertemuan baru), sebutkan singkat di chat folder mana yang baru dibuat — tidak perlu penjelasan panjang.
