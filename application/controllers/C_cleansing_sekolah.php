<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_cleansing_sekolah extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        // Ambil data sekolah_asal yang belum dipetakan
        $this->db->select('sekolah_asal, MAX(alamat_sekolah_asal) as alamat_sekolah, MAX(kategori_sekolah) as kategori_sekolah, COUNT(*) as jumlah_mahasiswa');
        $this->db->from('mahasiswa');
        $this->db->where('npsn_sekolah_asal IS NULL');
        $this->db->where('sekolah_asal !=', '');
        $this->db->group_by('sekolah_asal');
        $this->db->order_by('jumlah_mahasiswa', 'DESC');
        $data['unmapped_sekolah'] = $this->db->get()->result();

        // Ambil data yang sudah terpetakan untuk preview (opsional)
        $this->db->select('m.sekolah_asal, d.sekolah as nama_referensi, COUNT(*) as jumlah_mahasiswa');
        $this->db->from('mahasiswa m');
        $this->db->join('dapo_sekolah d', 'm.npsn_sekolah_asal = d.npsn');
        $this->db->where('m.npsn_sekolah_asal IS NOT NULL');
        $this->db->group_by(['m.sekolah_asal', 'd.sekolah']);
        $this->db->order_by('jumlah_mahasiswa', 'DESC');
        $this->db->limit(100);
        $data['mapped_sekolah'] = $this->db->get()->result();

        $this->load->view('cleansing_sekolah_view', $data);
    }

    public function search_referensi() {
        $term = $this->input->get('q');
        
        $this->db->select('id, npsn, sekolah, kabupaten_kota');
        $this->db->from('dapo_sekolah');
        $this->db->where_in('bentuk', ['SMA', 'SMK', 'MA', 'SMLB', 'SMTK', 'SMAK']); // Hanya filter setingkat SMA
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('sekolah', $term);
            $this->db->or_like('npsn', $term);
            $this->db->group_end();
        }
        $this->db->limit(50);
        $query = $this->db->get()->result();

        $result = [];
        foreach ($query as $row) {
            $result[] = [
                'id' => $row->npsn,
                'text' => $row->npsn . ' - ' . $row->sekolah . ' (' . $row->kabupaten_kota . ')'
            ];
        }

        echo json_encode(['items' => $result]);
    }

    public function tambah_referensi() {
        $npsn = $this->input->post('npsn');
        $sekolah = $this->input->post('sekolah');
        $bentuk = $this->input->post('bentuk');
        $kabupaten_kota = $this->input->post('kabupaten_kota');

        if (empty($sekolah) || empty($bentuk)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama Sekolah dan Kategori wajib diisi!']);
            return;
        }

        // Generate ID / UUID
        $new_id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        if (empty($npsn)) {
            $npsn = 'MANUAL-' . strtoupper(substr(md5(time()), 0, 8)); // Generate fake NPSN for manual entries if empty
        }

        $data = [
            'id' => $new_id,
            'npsn' => $npsn,
            'sekolah' => strtoupper($sekolah),
            'bentuk' => $bentuk,
            'status' => 'SWASTA', // Default assumption, although user can't select right now. Or we can just set S.
            'kabupaten_kota' => $kabupaten_kota,
            'lintang' => 0,
            'bujur' => 0,
        ];

        $this->db->insert('dapo_sekolah', $data);
        
        echo json_encode(['status' => 'success', 'message' => 'Referensi sekolah baru berhasil ditambahkan! Anda kini bisa mencarinya di dropdown atau Auto-Match.']);
    }

    public function save_mapping() {
        $sekolah_asal_asli = $this->input->post('sekolah_asal');
        $id_referensi = $this->input->post('id_referensi'); // Ini sekarang berisi NPSN dari Select2

        if (!empty($sekolah_asal_asli) && !empty($id_referensi)) {
            $this->db->where('sekolah_asal', $sekolah_asal_asli);
            $this->db->update('mahasiswa', ['npsn_sekolah_asal' => $id_referensi]);

            echo json_encode(['status' => 'success', 'message' => 'Data berhasil dipetakan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }
    }
    
    public function auto_match_smart() {
        ini_set('max_execution_time', 300); // Izinkan proses sampai 5 menit
        
        // Ambil SEMUA data referensi SMA sederajat ke memori (sekitar 26rb data, aman di memori PHP)
        $this->db->select('id, npsn, sekolah, bentuk, status, kabupaten_kota, alamat_jalan');
        $this->db->from('dapo_sekolah');
        $this->db->where_in('bentuk', ['SMA', 'SMK', 'MA', 'SMLB', 'SMTK', 'SMAK']);
        $all_sekolah = $this->db->get()->result();

        // Ambil data belum terpetakan
        $this->db->select('sekolah_asal, MAX(kategori_sekolah) as kategori_sekolah, MAX(status_sekolah) as status_sekolah, MAX(alamat_sekolah_asal) as alamat_sekolah_asal');
        $this->db->from('mahasiswa');
        $this->db->where('npsn_sekolah_asal IS NULL');
        $this->db->where('sekolah_asal !=', '');
        $this->db->group_by('sekolah_asal');
        $unmapped = $this->db->get()->result();

        $match_count = 0;
        
        // Fungsi pembantu pembersihan teks
        $clean_text = function($text) {
            $text = strtoupper(trim($text));
            $text = preg_replace('/[^A-Z0-9]/', ' ', $text); // Hapus tanda baca
            
            // Hapus kata umum
            $remove_words = ['SMA', 'SMK', 'MA', 'NEGERI', 'SWASTA', 'PLUS', 'TERPADU', 'YAYASAN', 'PERGURUAN', 'SMAN', 'SMKN', 'SMAS', 'SMKS', 'MAS', 'MAN', 'NEGRI', 'KABUPATEN', 'KOTA', 'KAB', 'KECAMATAN', 'KEC', 'PROVINSI', 'PROV'];
            foreach ($remove_words as $word) {
                $text = preg_replace('/\b' . $word . '\b/', ' ', $text);
            }
            // Ubah singkatan umum
            $text = preg_replace('/\bTJ\b/', 'TANJUNG', $text);
            
            return trim(preg_replace('/\s+/', ' ', $text));
        };

        foreach($unmapped as $u) {
            $asal = strtoupper(trim((string)$u->sekolah_asal));
            $kategori = strtoupper(trim((string)$u->kategori_sekolah));
            $status = strtoupper(trim((string)$u->status_sekolah));
            $alamat = strtoupper(trim((string)$u->alamat_sekolah_asal));

            // Bentuk filter (SMA/SMK/MA)
            $bentuk_filter = [];
            if ($kategori == 'SMA' || str_contains($asal, 'SMA') || str_contains($asal, 'SMAN') || str_contains($asal, 'SMAS')) $bentuk_filter[] = 'SMA';
            if ($kategori == 'SMK' || str_contains($asal, 'SMK') || str_contains($asal, 'SMKN') || str_contains($asal, 'SMKS')) $bentuk_filter[] = 'SMK';
            if ($kategori == 'MA' || str_contains($asal, 'ALIYAH') || str_contains($asal, 'MAS ') || str_contains($asal, 'MAN ')) $bentuk_filter[] = 'MA';
            if (empty($bentuk_filter)) $bentuk_filter = ['SMA', 'SMK', 'MA', 'SMLB', 'SMTK', 'SMAK'];

            // Status filter (Negeri/Swasta)
            $status_filter = [];
            if ($status == 'NEGERI') {
                $status_filter = ['N', 'Negeri', 'NEGERI'];
            } elseif ($status == 'SWASTA') {
                $status_filter = ['S', 'Swasta', 'SWASTA'];
            }

            $clean_asal = $clean_text($asal);
            if (strlen($clean_asal) < 2) continue;

            $asal_words = explode(' ', $clean_asal);
            $candidates = [];

            foreach ($all_sekolah as $ref) {
                if (!in_array($ref->bentuk, $bentuk_filter)) continue;
                if (!empty($status_filter) && !in_array($ref->status, $status_filter)) continue;

                $clean_ref = $clean_text($ref->sekolah);
                
                // Lewati jika nama referensi kosong
                if (empty($clean_ref)) continue; 
                
                similar_text($clean_asal, $clean_ref, $sim);
                
                // Bobot tambahan untuk kesamaan alamat/kabupaten
                $clean_alamat = preg_replace('/[^A-Z0-9]/', '', $alamat);
                $kab_kota = preg_replace('/[^A-Z0-9]/', '', strtoupper(str_replace(['KAB. ', 'KOTA '], '', $ref->kabupaten_kota)));
                
                // Cek apakah kota ada di alamat atau bahkan di nama sekolah asal inputan mahasiswa
                if (!empty($kab_kota) && (str_contains($clean_alamat, $kab_kota) || str_contains($clean_asal, $kab_kota) || str_contains(strtoupper((string)$u->sekolah_asal), $kab_kota))) {
                    $sim += 15;
                }
                
                // Cek alamat spesifik (jalan)
                if (!empty($clean_alamat)) {
                    $ref_almt = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)$ref->alamat_jalan));
                    if (!empty($ref_almt) && (str_contains($clean_alamat, $ref_almt) || str_contains($ref_almt, $clean_alamat))) {
                        $sim += 20; // Bonus lebih besar jika jalan cocok
                    }
                }

                // Hanya pertimbangkan jika skor >= 75
                if ($sim >= 75) {
                        $candidates[] = ['id' => $ref->id, 'npsn' => $ref->npsn, 'sim' => $sim, 'sekolah' => $ref->sekolah];
                    }
            }

            if (count($candidates) > 0) {
                // Urutkan dari skor tertinggi ke terendah
                usort($candidates, fn($a, $b) => $b['sim'] <=> $a['sim']);
                
                // Jika kandidat terbaik sangat meyakinkan (hanya 1 kandidat, atau selisih poin dengan juara ke-2 cukup aman)
                if (count($candidates) == 1 || ($candidates[0]['sim'] - $candidates[1]['sim'] > 5)) {
                    $this->db->where('sekolah_asal', $u->sekolah_asal);
                    $this->db->update('mahasiswa', ['npsn_sekolah_asal' => $candidates[0]['npsn']]);
                    $match_count++;
                }
            }
        }
        
        $this->session->set_flashdata('message', $match_count . ' data berhasil dipetakan secara otomatis dengan pencocokan cerdas tingkat lanjut.');
        redirect('C_cleansing_sekolah');
    }
}
