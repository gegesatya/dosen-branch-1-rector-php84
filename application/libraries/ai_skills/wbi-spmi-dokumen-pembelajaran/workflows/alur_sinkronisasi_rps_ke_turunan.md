# Alur Sinkronisasi RPS ke Dokumen Turunan (Single Source of Truth)

Dokumen ini mengatur tata cara penguncian konsistensi antara **Dokumen Master RPS** dengan dokumen-dokumen turunan: **Kontrak Pembelajaran**, **Rancangan Tugas Mahasiswa**, dan **Roadmap Pembelajaran**.

---

## 🎯 Prinsip Utama
Dokumen Master RPS adalah **Satu-satunya Sumber Kebenaran (*Single Source of Truth*)**. Dokumen turunan dilarang mengubah atau menyimpang dari data yang sudah disepakati di RPS.

---

## 🔗 Matriks Poin Sinkronisasi Wajib

| Elemen | Sumber di Dokumen RPS | Aturan di Dokumen Turunan (Kontrak / Tugas / Roadmap) |
| :--- | :--- | :--- |
| **CPL & CPMK** | Bagian 2 & 3 RPS | Wajib disalin persis (huruf demi huruf, kode dan kalimat capaian). Dilarang menambah/mengurangi CPMK. |
| **Blueprint Asesmen** | Tabel Matriks Bobot Asesmen (Bagian 3 RPS) | Persentase bobot Tugas, Kuis, Kasus/Proyek, UTS, UAS, Sikap **WAJIB 100% SAMA**. |
| **Alokasi Minggu & Topik** | Rencana 16 Pertemuan (Bagian 6 RPS) | Topik perkuliahan per minggu pada Kontrak & Roadmap wajib selaras dengan bahan kajian di RPS. |
| **Penugasan Mahasiswa** | Sub-CPMK dan topik tugas di RPS | Bobot dan tujuan pada formulir Rancangan Tugas wajib mengacu pada persentase tugas di tabel asesmen RPS. |
| **Daftar Pustaka** | Bagian 5 RPS | Referensi pada Kontrak Kuliah wajib mengambil dari daftar referensi RPS. |

---

## 🛠️ Alur Injeksi Prompt di Generator

1. **Pemeriksaan Dokumen Master**:
   - Sistem memeriksa apakah teks naskah Master RPS (`teks_rps_induk`) sudah tersedia (dari sesi aktif atau database `siakad_rps_obe`).
2. **Suntikan Konteks ke Prompt**:
   - Jika `teks_rps_induk` tersedia, sistem menyisipkan blok:
     ```markdown
     ## DOKUMEN MASTER RPS RESMI (SINGLE SOURCE OF TRUTH):
     [Teks Naskah RPS Lengkap]
     ```
3. **Instruksi Kunci AI**:
   - Memerintahkan AI untuk secara mutlak mengadopsi struktur capaian, tabel asesmen, dan alur minggu dari naskah Master RPS tersebut tanpa melakukan improvisasi bobot atau capaian baru.
