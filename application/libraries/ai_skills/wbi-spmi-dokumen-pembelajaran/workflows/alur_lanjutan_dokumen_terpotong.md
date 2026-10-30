# Alur Penanganan Dokumen Terpotong (Tail-Context Continuation)

Dokumen ini merinci prosedur menyambung pembuatan dokumen yang terhenti di tengah jalan karena mencapai batas kapasitas output token (*max tokens / length finish reason*).

---

## 🔍 Deteksi Terpotong

Kondisi dokumen terpotong dideteksi ketika:
1. Respon AI mengembalikan `finish_reason: "MAX_TOKENS"` atau `"LENGTH"`.
2. Output markdown berakhir secara tidak wajar (misal: di tengah baris tabel pertemuan 9 atau di tengah kata).

---

## ⚙️ Prosedur Eksekusi Continuation

1. **Pengambilan Konteks Ekor (*Tail Context*)**:
   - Sistem mengambil maksimal **4.000 karakter terakhir** dari naskah sebelumnya (`tail_context`).
   - Warning/banner penanda terpotong sebelumnya dibersihkan.
2. **Formulasi Prompt Sambungan**:
   - Prompt instruksi diubah menjadi:
     ```markdown
     PERINTAH KELANJUTAN EKSKLUSIF (CONTINUATION):
     Dokumen '[JUDUL]' sebelumnya terputus karena batasan token output per respons AI.
     Berikut adalah potongan teks bagian paling akhir yang SUDAH SELESAI disusun sebelumnya:
     ```markdown
     [TAIL_CONTEXT]
     ```
     
     INSTRUKSI PENULISAN KELANJUTAN:
     1. TULISKAN LANGSUNG KELANJUTANNYA mulai dari baris/titik terputus di atas hingga seluruh dokumen tuntas.
     2. DILARANG KERAS mengulang judul dokumen, identitas, atau bab/tabel yang sudah selesai tertulis di atas.
     3. Jika terputus di tengah tabel, lanjutkan baris-baris pertemuan berikutnya dengan format tabel markdown yang valid (|...|...|).
     4. Lanjutkan seluruh sisa bagian/bab hingga tuntas dan lengkap sesuai standar format SPMI WBI.
     ```
3. **Penggabungan Mulus (*Seamless Markdown Merge*)**:
   - Sistem di frontend/backend menyambungkan output baru tepat di ujung potongan naskah lama.
   - Tabel markdown yang sempat terbuka otomatis dipadukan menjadi satu kesatuan tabel utuh.
