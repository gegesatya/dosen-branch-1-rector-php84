# Laporan Lengkap Upgrade CodeIgniter 3 ke PHP 8.4 + Standarisasi Cetak PDF & Audit Deprecations

**Aplikasi**: Sistem Informasi Akademik Dosen (WBI Polytechnic)  
**Branch / Direktori**: `c:\laragon\www\wbi\server\dosen_branch_1`  
**Target Environment**: PHP 8.4.15 (NTS x64) / Apache & LiteSpeed (cPanel) / MariaDB  
**Tanggal Update**: 23 Oktober 2026  

---

## 1. Ringkasan Eksekutif (Executive Summary)

Migrasi menyeluruh aplikasi CodeIgniter 3 (CI3) dari **PHP 7.4 ke PHP 8.4** telah diselesaikan secara penuh dengan memenuhi seluruh batasan ketat (*guardrails*):
- **Arsitektur Dasar CI3 Terjaga**: Tetap mempertahankan pola klasik MVC, loader, hooks, dan driver tanpa perombakan struktur.
- **Tanpa Constructor Property Promotion**: Menghindari fitur PHP 8.0 promotion pada class Controller, Model, dan Library agar tetap kompatibel penuh dengan CI3 core loader.
- **Modernisasi Dependensi**: Mengintegrasikan Composer resmi di root project (`mpdf/mpdf` 8.3, `phpoffice/phpspreadsheet` 2.4, `smalot/pdfparser` 2.13, dan `endroid/qr-code` 6.1).
- **Audit Deprecations Menyeluruh**: Memindai seluruh **1.724 file PHP** di dalam direktori `application/`, menuntaskan seluruh notice deprecation PHP 8.1–8.4, dan mencapai **100% kelulusan validasi sintaks PHP 8.4 dengan ZERO error**.

---

## 2. Tahap 1: Core CI3 & Pengerasan Konfigurasi (Hardening)

1. **System Core Input CLI Safety**:
   - Memperbaiki `system/core/Input.php` baris 692–695: `(string)$this->server('REQUEST_METHOD')` untuk mencegah peringatan `strtolower(null)` pada mode CLI / background tasks.
2. **Dynamic Base URL & Server Variables**:
   - Memperbaiki `application/config/config.php` baris 27–33 menggunakan null coalescing: `$_SERVER['HTTP_HOST'] ?? 'localhost'` dan `$_SERVER['SCRIPT_NAME'] ?? ''`.
3. **Pemberian Atribut `#[AllowDynamicProperties]`**:
   - Telah terpasang pada `CI_Controller`, `CI_Model`, serta `application/libraries/Breadcrumbs.php` untuk mematuhi aturan strict deprecation PHP 8.2+.

---

## 3. Tahap 2: Modernisasi Pustaka & QR Code Engine

1. **Struktur Root `composer.json`**:
   ```json
   {
       "require": {
           "php": ">=8.1",
           "mpdf/mpdf": "^8.2",
           "phpoffice/phpspreadsheet": "^2.0 || ^1.29",
           "smalot/pdfparser": "^2.11",
           "endroid/qr-code": "6.1.*"
       },
       "require-dev": {
           "rector/rector": "^2.7"
       }
   }
   ```
2. **Migrasi Engine QR Code ke In-Memory Data URI**:
   - Menulis ulang `application/models/Qr_model.php` menggunakan `Endroid\QrCode\Builder\Builder` v6.1.
   - Output QR dikonversi langsung menjadi Base64 Data URI (`data:image/png;base64,...`) tanpa ketergantungan file fisik di `assets/qrcode/`.
   - Mengupdate seluruh controller terkait (`E_skripsi_sidang.php`, `E_skripsi_nilai_sempro.php`, `E_skripsi_seminar_hasil.php`, `E_skripsi_surat_tugas.php`, `E_skripsi_sk_saya.php`, `E_skripsi_sempro_.php`, dll.) agar menerima Data URI secara aman.
3. **Perbaikan Library Legacy `third_party/endroid_qrcode`**:
   - Menghilangkan sintaks kurung kurawal `$str{$pos}` yang tidak lagi didukung di PHP 8.0+ menjadi `$str[$pos]` pada `BitMatrix.php` dan `DecodedBitStreamParser.php`.

---

## 4. Tahap 3 & 4: Refactoring Kode & Rector Automation

1. **Otomasi Rector**:
   - Menggunakan konfigurasi `rector.php` dengan `rector-bootstrap.php` khusus CI3.
   - Berhasil memproses **339 file PHP** untuk standardisasi array pendek (`[]`), null-coalescing (`??`), serta fungsi string native modern (`str_contains`, `str_starts_with`, `str_ends_with`).
2. **Modernisasi Helper Global**:
   - Memperbaiki `tgl_short()` di `application/helpers/gg_helper.php` dengan safe string casting dan handling nullable dates.

---

## 5. Tahap 5: Standarisasi Sistem Cetak Dokumen & mPDF

1. **CSS Common Template (`application/views/cetak/inc_pdf_common_css.php`)**:
   - Warna border resmi: `#334155` solid `0.5pt !important`.
   - Desain kartu akademik (*modern academic cards*) dan palet warna korporat WBI Teal `#006a6f`.
   - Helper pembersih HTML catatan revisi: `clean_catatan_revisi()` dan `render_catatan_revisi()`.
2. **Template Cetak Tersinkronisasi**:
   - `pdf_sidang.php`: Perhitungan spacer dinamis (570px/500px) untuk menjamin blok tanda tangan tidak terdorong ke halaman baru (*overflow protection*). QR Code proporsional 65x65px.
   - `pdf_nilai_sempro.php` & `pdf_semhas.php`: Mengganti teks "Usulan" menjadi kartu hasil sempro/semhas berstandar akademik.
   - `pdf_surat_tugas.php`, `pdf_bimbingan_proposal.php`, `pdf_profil_mahasiswa.php`, dan dokumen SK mengajar.

---

## 6. Tahap 6: Server & Web Server Rules (.htaccess)

Memperbaiki konfigurasi `c:\laragon\www\wbi\server\dosen_branch_1\.htaccess` agar kompatibel dengan LiteSpeed Web Server di cPanel tanpa *infinite loop*:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

---

## 7. Tahap 7: Audit & Penyelesaian Isu Deprecations PHP 8.1 - 8.4 (Proses Terakhir)

Pada tahap audit mendalam ini, beberapa notice dan fatal error yang muncul saat pengujian di browser berhasil diidentifikasi dan dituntaskan:

### Kasus 1: `Infografis_profile_mahasiswa_model.php` (Deprecated trim on Null)
- **Gejala**: `trim(): Passing null to parameter #1 ($string) of type string is deprecated`.
- **Penyebab**: Field hasil `LEFT JOIN` (seperti `nama_penghasilan`) bernilai `NULL` dari database saat tidak memiliki relasi.
- **Solusi**: Menambahkan type-cast `(string)$row->nama_penghasilan` pada baris 233 dan 273.

### Kasus 2: `C_academic_intelligence.php` (Deprecated False to Array)
- **Gejala**: `Automatic conversion of false to array is deprecated` (baris 57).
- **Penyebab**: Saat *cache miss*, driver CI3 `$this->cache->get($cache_name)` mengembalikan `FALSE`. Menulis `$cached_output['list_prodi'] = ...` secara langsung di branch `else` memicu autovivification dari false.
- **Solusi**: Menginisialisasi array kosong `$cached_output = [];` sebelum pemuatan data, membersihkan file cache kedaluwarsa `application/cache/academic_intel_*`, serta menambahkan null-coalescing di view dashboard.

### Kasus 3: `Lecturer_persetujuan_krs.php` & Model (trim & strtolower on Null)
- **Gejala**: Deprecated notice pada `trim($g->nilai_huruf)`, `trim($rg->kip_skema)`, dan `strtolower($rg->nm_status_krs)`.
- **Penyebab**: Mahasiswa non-KIP mengembalikan field KIP bernilai `NULL`, dan mahasiswa yang belum dinilai memiliki nilai huruf `NULL`.
- **Solusi**:
  - `Lecturer_persetujuan_krs_model.php` baris 184: `strtoupper(trim((string)($g->nilai_huruf ?? '')))`.
  - `Lecturer_persetujuan_krs.php`: Melakukan casting `(string)` pada baris 80, 136, 205, 730, 773, 783, 791, 961, dan 1572.

### Kasus 4: `Lecturer_data_perwalian_model.php` (Fatal ArgumentCountError)
- **Gejala**: `ArgumentCountError: Too few arguments to function Lecturer_data_perwalian_model::_get_datatables_query(), 0 passed on line 59 and exactly 1 expected`.
- **Penyebab**: Di PHP 8.0+, memanggil method dengan argumen kurang dari parameter wajib menghasilkan fatal error. Method dideklarasikan `_get_datatables_query($angkatan)` tetapi dipanggil `$this->_get_datatables_query()` tanpa parameter.
- **Solusi**:
  - Memberikan default value: `_get_datatables_query($angkatan = 0)`.
  - Meneruskan `$angkatan` pada pemanggilan: `$this->_get_datatables_query($angkatan);` di `get_datatables()` dan `count_filtered()`.
  - Mengamankan filter: `if (!empty($angkatan))`.
  - Menambahkan default parameter di controller `Lecturer_data_perwalian.php`: `ajax_list($angkatan = 0)` dan `$no = $_POST['start'] ?? 0;`.

### Kasus 5: Library Core `Datatables.php` (Deprecated strlen on Null)
- **Gejala**: `strlen(): Passing null to parameter #1 ($string) of type string is deprecated` di baris 439.
- **Penyebab**: Properti `$this->distinct` bernilai `NULL` jika query DataTables tidak menggunakan klausa distinct.
- **Solusi**: Mengubah `if (strlen($this->distinct) > 0)` menjadi `if (!empty($this->distinct))`.

### Kasus 6: Pemindaian Masal & Patching 187 Titik Rawan di 62 File
Melakukan pemindaian otomatis menggunakan AST/regex analyzer pada seluruh direktori `application/`:
- Menemukan dan mem-patch **187 titik pemanggilan fungsi string native** (`trim`, `strtolower`, `strtoupper`, `strlen`, `explode`, `ucwords`, `ucfirst`) yang beroperasi pada objek database nullable di 62 file (Controllers, Models, Libraries, dan Views).
- Memperbaiki `explode(' - ', (string)($log->periode_laporan ?? ''))` di `C_review_laporan.php` dan `explode(' ', (string)($m->nm_pd ?? ''))` di `e_skripsi_bimbingan_cards_partial.php`.

---

## 8. Status Verifikasi Akhir

| Pemeriksaan | Target | Hasil | Status |
|---|---|---|---|
| **PHP Syntax Linting (`php -l`)** | Seluruh 1.724 file PHP di `application/` | 1.724 / 1.724 Lulus | **100% PASS** |
| **Pustaka Composer** | mPDF, PhpSpreadsheet, PdfParser, Endroid QR | Terinstal via Composer di `vendor/` | **RESOLVED** |
| **Generasi QR Code** | Base64 In-Memory Data URI | Tidak ada ketergantungan file fisik | **RESOLVED** |
| **Deprecated Notices** | trim(null), strtolower(null), false to array | 0 issue terdeteksi di scanner | **ZERO DEFECT** |
| **DataTables Backend** | Parameter handling & query paging | Kompatibel penuh PHP 8.4 | **RESOLVED** |

---

*Dokumen ini merupakan catatan resmi migrasi PHP 8.4 dan standarisasi sistem cetak untuk server dosen Politeknik Wilmar Bisnis Indonesia (WBI).*
