# AGRIBISNIS HORTIKULTURA CPL CANONICALIZATION FIX REPORT

**Date:** 2026-09-18  
**Scope:** Fix Agribisnis Hortikultura (AH) CPL Canonicalization ONLY (Anti-False-Positive, Formal Academic Section Categorization, Unified Canonical Set).  
**Verdict:** `PASS`

---

## 1. Executive Summary & Root Cause Analysis

### Identified Root Causes:
1. **False-Positive Heading Injection ("D4 OTHER"):**
   - The table header/column *"D4 Agribisnis Hortikultura Politeknik WBI"* from the table *"JABARAN CAPAIAN PEMBELAJARAN BERDASARKAN KKNI"* was previously picked up by loose regex matching as a CPL code `D4` with category `OTHER`.
   - Other table headings such as `Level 6`, `Sumber`, `Deskripsi Umum`, `Sikap dan Tata Nilai`, and `Penguasaan Pengetahuan` posed false-positive risks.
2. **Incorrect Categorization of A1..A5:**
   - Codes `A1..A5` were previously classified under generic regex without anchoring to their true curriculum section (*ASPEK PENGETAHUAN*).
3. **Canonical CPL Separation & Source Provenance:**
   - The formal CPL chapter (*Bab "CAPAIAN PEMBELAJARAN D4 AGRIBISNIS HORTIKULTURA"*) defines 48 comprehensive CPL items across 4 SN-Dikti aspects:
     - **Sikap (S):** 10 items (`S-1` to `S-10`, source: `a.` to `j.`, `is_normalized_code: true`)
     - **Pengetahuan (P):** 16 items (`A1..A5`, `B1..B7`, `C1..C4`, source: `A.1..A.5`, `B.1..B.7`, `C.1..C.4`, `is_normalized_code: false`)
     - **Keterampilan Khusus (KK):** 10 items (`D`, `E1..E5`, `F`, `G`, `H`, `I`, source: `D.`, `E.1..E.5`, `F.`, `G.`, `H.`, `I.`, `is_normalized_code: false`)
     - **Keterampilan Umum (KU):** 12 items (`KU-1` to `KU-12`, source: `a.` to `l.`, `is_normalized_code: true`)
   - Matriks I & Matriks II use shorthand codes (`A1..A5`, `B1..B7`, `C1..C4`, `D`, `E1..E5`, `F`, `G`, `H`) which now seamlessly map to the single canonical CPL object set without duplicating formulations.

---

## 2. Canonical CPL Object Schema

Each CPL item in the Studio CPL and backend pipeline adheres strictly to the canonical schema:

```json
{
  "code": "A1",
  "source_code": "A.1",
  "category": "P",
  "description": "konsep teoretis botani, fisiologi, ilmu tanah, hidrologi, dan klimatologi tanaman hortikultura secara mendalam.",
  "source_section": "ASPEK PENGETAHUAN",
  "is_official": true,
  "is_normalized_code": false
}
```

---

## 3. Full Listing of All 48 Agribisnis Hortikultura Canonical CPLs

| No | Code | Category | Normalized? | Source Section | Source Code | First 100 Characters Description |
|:---|:-----|:--------:|:-----------:|:---------------|:-----------:|:---------------------------------|
| 1 | `S-1` | **S** | `YES` | ASPEK SIKAP | `a.` | bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius. |
| 2 | `S-2` | **S** | `YES` | ASPEK SIKAP | `b.` | menjunjung tinggi nilai kemanusiaan dalam menjalankan tugas berdasarkan agama, moral, dan etika. |
| 3 | `S-3` | **S** | `YES` | ASPEK SIKAP | `c.` | berkontribusi dalam peningkatan mutu kehidupan bermasyarakat, berbangsa, bernegara, dan kemajuan pe... |
| 4 | `S-4` | **S** | `YES` | ASPEK SIKAP | `d.` | berperan sebagai warga negara yang bangga dan cinta tanah air, memiliki nasionalisme serta rasa tan... |
| 5 | `S-5` | **S** | `YES` | ASPEK SIKAP | `e.` | menghargai keanekaragaman budaya, pandangan, agama, dan kepercayaan, serta pendapat atau temuan ori... |
| 6 | `S-6` | **S** | `YES` | ASPEK SIKAP | `f.` | bekerja sama dan memiliki kepekaan sosial serta kepedulian terhadap masyarakat dan lingkungan. |
| 7 | `S-7` | **S** | `YES` | ASPEK SIKAP | `g.` | taat hukum dan disiplin dalam kehidupan bermasyarakat dan bernegara. |
| 8 | `S-8` | **S** | `YES` | ASPEK SIKAP | `h.` | menginternalisasi nilai, norma, dan etika akademik. |
| 9 | `S-9` | **S** | `YES` | ASPEK SIKAP | `i.` | menunjukkan sikap bertanggung jawab atas pekerjaan di bidang keahliannya secara mandiri. |
| 10 | `S-10` | **S** | `YES` | ASPEK SIKAP | `j.` | menginternalisasi semangat kemandirian, kejuangan, dan kewirausahaan; dan menginternalisasi etika b... |
| 11 | `A1` | **P** | `NO` | ASPEK PENGETAHUAN | `A.1` | konsep teoretis botani, fisiologi, ilmu tanah, hidrologi, dan klimatologi tanaman hortikultura seca... |
| 12 | `A2` | **P** | `NO` | ASPEK PENGETAHUAN | `A.2` | prinsip dan teknik produksi, pengetahuan operasional lengkap budidaya tanaman hortikultura mengguna... |
| 13 | `A3` | **P** | `NO` | ASPEK PENGETAHUAN | `A.3` | prinsip, teknik, dan pengetahuan operasional lengkap tentang pengolahan, panen, pascapanen, pengema... |
| 14 | `A4` | **P** | `NO` | ASPEK PENGETAHUAN | `A.4` | prinsip dan teknik produksi dengan limbah minimal (zero waste) dan produksi bersih (clean productio... |
| 15 | `A5` | **P** | `NO` | ASPEK PENGETAHUAN | `A.5` | konsep umum pelestarian lingkungan. |
| 16 | `B1` | **P** | `NO` | ASPEK PENGETAHUAN | `B.1` | konsep teoretis ekonomi pertanian secara mendalam. |
| 17 | `B2` | **P** | `NO` | ASPEK PENGETAHUAN | `B.2` | konsep dan teknik membaca peluang pasar, menghitung biaya produksi dan kelabaan usaha skala kecil m... |
| 18 | `B3` | **P** | `NO` | ASPEK PENGETAHUAN | `B.3` | norma, etika, dan pengetahuan faktual tentang jenis dan regulasi agribisnis hortikultura pada lingk... |
| 19 | `B4` | **P** | `NO` | ASPEK PENGETAHUAN | `B.4` | teknik riset pasar berbasis pada potensi lokal produk hortikultura. |
| 20 | `B5` | **P** | `NO` | ASPEK PENGETAHUAN | `B.5` | konsep dan teknik menyusun strategi bisnis pertanian hortikultura dan menjabarkannya dalam rencana o... |
| 21 | `B6` | **P** | `NO` | ASPEK PENGETAHUAN | `B.6` | prinsip dan teknik komunikasi persuasif dalam pemasaran produk-produk. |
| 22 | `B7` | **P** | `NO` | ASPEK PENGETAHUAN | `B.7` | konsep umum dan teknik evaluasi agribisnis hortikultura. |
| 23 | `C1` | **P** | `NO` | ASPEK PENGETAHUAN | `C.1` | konsep umum tentang fungsi manajemen meliputi: perencanaan, pelaksanaan, pemantauan, evaluasi dan p... |
| 24 | `C2` | **P** | `NO` | ASPEK PENGETAHUAN | `C.2` | metode dan perangkat analisis fungsi manajemen dan organisasi unit agribisnis hortikultura. |
| 25 | `C3` | **P** | `NO` | ASPEK PENGETAHUAN | `C.3` | prinsip pengelolaan informasi untuk pengambilan keputusan strategis dan formulasi penyelesaian masa... |
| 26 | `C4` | **P** | `NO` | ASPEK PENGETAHUAN | `C.4` | prinsip dan teknik komunikasi efektif untuk bekerja dalam tim; dan menguasai konsep integritas akade... |
| 27 | `D` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `D.` | mampu menghasilkan salah satu produk hortikultura yaitu: buah, sayur, bunga, atau tanaman obat, deng... |
| 28 | `E1` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `E.1` | memperoleh modal usaha kecil menengah. |
| 29 | `E2` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `E.2` | membaca peluang dengan menggunakan berbagai metode pengukuran dan analisis kelayakan kinerja agribis... |
| 30 | `E3` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `E.3` | menetapkan kebutuhan input dan biaya kegiatan agribisnis. |
| 31 | `E4` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `E.4` | memperoleh sumber daya (material dan manusia) yang kompetitif. |
| 32 | `E5` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `E.5` | mengemas, menyimpan, memasarkan, mendistribusi produk hortikultura. |
| 33 | `F` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `F.` | mampu mengelola agribisnis hortikultura skala kecil menengah dengan menerapkan sistem manajemen yang... |
| 34 | `G` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `G.` | mampu mengevaluasi dan mengusulkan alternatif solusi terhadap keberlanjutan agribisnis hortikultur y... |
| 35 | `H` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `H.` | mampu memberikan pendampingan pada petani dalam produksi, pengelolaan dan inkubasi agribisnis hortik... |
| 36 | `I` | **KK** | `NO` | ASPEK KETERAMPILAN KHUSUS | `I.` | Mampu menganalisa dan menyusun laporan kegiatan agribisnis. |
| 37 | `KU-1` | **KU** | `YES` | KETERAMPILAN UMUM | `a.` | mampu menerapkan pemikiran logis, kritis, inovatif, bermutu, dan terukur dalam melakukan pekerjaan y... |
| 38 | `KU-2` | **KU** | `YES` | KETERAMPILAN UMUM | `b.` | mampu menunjukkan kinerja mandiri, bermutu dan terukur. |
| 39 | `KU-3` | **KU** | `YES` | KETERAMPILAN UMUM | `c.` | mampu mengkaji kasus penerapan ilmu pengetahuan dan teknologi yang memerhatikan dan menerapkan nilai... |
| 40 | `KU-4` | **KU** | `YES` | KETERAMPILAN UMUM | `d.` | mampu menyusun hasil kajian tersebut di atas dalam bentuk kertas kerja, spesifikasi desain, atau esa... |
| 41 | `KU-5` | **KU** | `YES` | KETERAMPILAN UMUM | `e.` | mampu mengambil keputusan secara tepat berdasarkan prosedur baku, spesifikasi desain, persyaratan Ke... |
| 42 | `KU-6` | **KU** | `YES` | KETERAMPILAN UMUM | `f.` | mampu memelihara dan mengembangkan jaringan kerja sama dan hasil kerja sama di dalam maupun di luar ... |
| 43 | `KU-7` | **KU** | `YES` | KETERAMPILAN UMUM | `g.` | mampu bertanggung jawab atas pencapaian hasil kerja kelompok dan melakukan supervisi dan evaluasi te... |
| 44 | `KU-8` | **KU** | `YES` | KETERAMPILAN UMUM | `h.` | mampu melakukan proses evaluasi diri terhadap kelompok kerja yang berada di bawah tanggung jawabnya,... |
| 45 | `KU-9` | **KU** | `YES` | KETERAMPILAN UMUM | `i.` | mampu mendokumentasikan, menyimpan, mengamankan, dan menemukan kembali data untuk menjamin kesahihan... |
| 46 | `KU-10` | **KU** | `YES` | KETERAMPILAN UMUM | `j.` | mampu beradaptasi, bekerja sama, berkreasi, berkontribusi, dan berinovasi dalam menerapkan ilmu peng... |
| 47 | `KU-11` | **KU** | `YES` | KETERAMPILAN UMUM | `k.` | mampu menegakkan integritas akademik secara umum dan mencegah terjadinya praktik plagiarisme. |
| 48 | `KU-12` | **KU** | `YES` | KETERAMPILAN UMUM | `l.` | mampu menggunakan teknologi informasi dalam konteks pengembangan keilmuan dan implementasi bidang ke... |

---

## 4. Category Breakdown Summary

- **Sikap (S):** 10 items (`S-1` to `S-10`)
- **Pengetahuan (P):** 16 items (`A1..A5`, `B1..B7`, `C1..C4`)
- **Keterampilan Khusus (KK):** 10 items (`D`, `E1..E5`, `F`, `G`, `H`, `I`)
- **Keterampilan Umum (KU):** 12 items (`KU-1` to `KU-12`)
- **Total Unique Canonical CPLs:** **48 items**

---

## 5. Validation Check Results

| Check | Expected | Actual Result | Status |
|:---|:---|:---|:---:|
| 1. False-Positive Table Headings Filter | Zero occurrences of `D4`, `Level 6`, `OTHER`, `Sumber`, `Deskripsi Umum` | 0 occurrences | **PASS** |
| 2. Pengetahuan Categorization | `A1..A5`, `B1..B7`, `C1..C4` categorized as `P` | 16/16 categorized as `P` | **PASS** |
| 3. Keterampilan Khusus Categorization | `D`, `E1..E5`, `F`, `G`, `H`, `I` categorized as `KK` | 10/10 categorized as `KK` | **PASS** |
| 4. Duplicate Formulations | 0 duplicate descriptions across canonical CPLs | 0 duplicate descriptions | **PASS** |
| 5. Unique Canonical Count Integrity | Count matches total unique canonical objects (48) | 48 items | **PASS** |
| 6. Isolation of Other Study Programs | Parsers for TRPL (64), AP (73), PKA (33), MPI (50) unmodified & intact | All passed | **PASS** |

---

## 6. Final Verdict

**FINAL VERDICT:** `PASS`
