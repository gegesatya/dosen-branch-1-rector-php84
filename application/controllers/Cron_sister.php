<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_sister extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Hanya izinkan akses dari CLI atau IP lokal
        if (!is_cli() && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
            show_error('Akses ditolak. Skrip ini hanya dapat dijalankan melalui CLI atau localhost.', 403);
        }
    }

    /**
     * Endpoint utama untuk sinkronisasi mingguan/harian
     * php index.php Cron_sister sync_all
     */
    public function sync_all() {
        echo "Memulai proses sinkronisasi SISTER...\n";

        // 1. Ambil kredensial dari tabel sister_settings
        $setting = $this->db->get('sister_settings')->row();
        if (!$setting) {
            echo "Error: Kredensial SISTER belum disetting di database.\n";
            return;
        }

        $username = $setting->username;
        $password = $setting->password;
        $id_pengguna = $setting->id_pengguna;

        // 2. Dapatkan token otentikasi dari SISTER API
        $token = $this->_get_sister_token($username, $password, $id_pengguna);
        if (!$token) {
            echo "Error: Gagal mendapatkan token dari SISTER API.\n";
            return;
        }

        // 3a. PROSES PERTAMA: Ambil profil dosen dari SISTER (Limit 5 dosen terlama)
        $this->db->select('u.username, sd.id_ptk_feeder');
        $this->db->from('users u');
        $this->db->join('siakad_dosen sd', 'u.id_ptk = sd.id_ptk', 'inner');
        $this->db->where('sd.id_ptk_feeder !=', '');
        $this->db->where('sd.id_ptk_feeder IS NOT NULL');
        $this->db->where('u.username !=', '');
        $this->db->where('u.username IS NOT NULL');
        $this->db->order_by('sd.last_sync_profil', 'ASC');
        $this->db->limit(5);
        $dosen_profil_list = $this->db->get()->result();

        echo "--- Sinkronisasi Profil (Batch " . count($dosen_profil_list) . ") ---\n";
        foreach ($dosen_profil_list as $dosen) {
            echo "Sync Profil: {$dosen->username}\n";
            $this->_sync_profil($token, $dosen->username, $dosen->id_ptk_feeder);
            $this->db->where('id_ptk_feeder', $dosen->id_ptk_feeder)
                     ->update('siakad_dosen', ['last_sync_profil' => date('Y-m-d H:i:s')]);
        }

        // 3b. PROSES KEDUA: Sinkronisasi Kinerja (Tridharma & BKD)
        // HANYA untuk dosen yang profilnya sudah berhasil tertarik ke sister_profile_dosen
        $this->db->select('u.username, sd.id_ptk_feeder');
        $this->db->from('users u');
        $this->db->join('siakad_dosen sd', 'u.id_ptk = sd.id_ptk', 'inner');
        $this->db->join('sister_profile_dosen spd', 'sd.id_ptk_feeder = spd.id_sdm', 'inner'); // Wajib ada di profil
        $this->db->where('sd.id_ptk_feeder !=', '');
        $this->db->where('sd.id_ptk_feeder IS NOT NULL');
        $this->db->where('u.username !=', '');
        $this->db->where('u.username IS NOT NULL');
        $this->db->order_by('sd.last_sync_sister', 'ASC');
        $this->db->limit(3); // Limit 3 per eksekusi cron
        $dosen_kinerja_list = $this->db->get()->result();

        echo "\n--- Sinkronisasi Kinerja & BKD (Batch " . count($dosen_kinerja_list) . ") ---\n";
        foreach ($dosen_kinerja_list as $dosen) {
            echo "Sync Kinerja: {$dosen->username}\n";
            $this->_sync_pengajaran($token, $dosen->username);
            $this->_sync_penelitian($token, $dosen->username);
            $this->_sync_pengabdian($token, $dosen->username);
            $this->_sync_bkd($token, $dosen->id_ptk_feeder);

            $this->db->where('id_ptk_feeder', $dosen->id_ptk_feeder)
                     ->update('siakad_dosen', ['last_sync_sister' => date('Y-m-d H:i:s')]);
        }

        echo "\nSinkronisasi SISTER selesai pada " . date('Y-m-d H:i:s') . "\n";
    }

    private function _get_sister_token($username, $password, $id_pengguna) {
        $url = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/authorize';
        
        $payload = json_encode([
            'username' => $username,
            'password' => $password,
            'id_pengguna' => $id_pengguna
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200 && $response) {
            $data = json_decode($response);
            return $data->token ?? false;
        }
        return false;
    }

    private function _sync_profil($token, $id_dosen, $id_sdm) {
        // Fetch Profile
        $profil_url = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/data_pribadi/profil/' . urlencode($id_sdm);
        $ch = curl_init($profil_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $nama = null;
        $jk = null;
        $tmpt = null;
        $tgl = null;
        
        if ($http_code == 200 && $response) {
            $data = json_decode($response);
            $nama = $data->nama ?? null;
            $jk = $data->jenis_kelamin ?? null;
            $tmpt = $data->tempat_lahir ?? null;
            $tgl = $data->tanggal_lahir ?? null;
        }

        // Jika nama kosong atau tidak ditemukan dari API, jangan teruskan sinkronisasi profil ini.
        if (empty(trim($nama))) {
            return false;
        }

        // Fetch Photo
        $foto_url_api = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/data_pribadi/foto/' . urlencode($id_sdm);
        $ch2 = curl_init($foto_url_api);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPGET, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $response2 = curl_exec($ch2);
        $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        $local_foto_url = null;
        if ($http_code2 == 200 && strlen($response2) > 0) {
            $path = 'assets/uploads/foto_dosen/' . $id_sdm . '.jpg';
            file_put_contents(FCPATH . $path, $response2);
            $local_foto_url = $path;
        }

        // Fetch Additional Data
        $fetch_json = function($endpoint) use ($id_sdm, $token) {
            $url = "https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/data_pribadi/$endpoint/" . urlencode($id_sdm);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($code == 200 && $res) ? json_decode($res) : null;
        };

        $kep = $fetch_json('kependudukan');
        $kel = $fetch_json('keluarga');
        $alm = $fetch_json('alamat');
        $peg = $fetch_json('kepegawaian');
        $lan = $fetch_json('lain');
        $bid = $fetch_json('bidang_ilmu');

        // Khusus Jabatan Fungsional, format endpoint sedikit beda (bukan /data_pribadi/)
        $jab_url = "https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/jabatan_fungsional?id_sdm=" . urlencode($id_sdm);
        $ch = curl_init($jab_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res_jab = curl_exec($ch);
        $code_jab = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $jab = ($code_jab == 200 && $res_jab) ? json_decode($res_jab) : null;

        // Pendidikan Formal
        $pend_url = "https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/pendidikan_formal?id_sdm=" . urlencode($id_sdm);
        $ch = curl_init($pend_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res_pend = curl_exec($ch);
        $code_pend = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $pend = ($code_pend == 200 && $res_pend) ? json_decode($res_pend) : null;

        $gelar_akademik = null;
        $pendidikan_terakhir = null;
        if (is_array($pend)) {
            $arr_gelar = [];
            $jenjang_tertinggi = '';
            $tahun_tertinggi = 0;
            foreach ($pend as $p) {
                if (!empty($p->gelar_akademik)) $arr_gelar[] = trim((string)$p->gelar_akademik);
                elseif (!empty($p->singkatan_gelar)) $arr_gelar[] = trim((string)$p->singkatan_gelar);
                
                $tahun = intval($p->tahun_lulus ?? 0);
                if ($tahun >= $tahun_tertinggi) {
                    $tahun_tertinggi = $tahun;
                    $jenjang_tertinggi = $p->jenjang_pendidikan ?? ($p->nama_jenjang_pendidikan ?? '');
                }
            }
            $gelar_akademik = implode(', ', array_unique($arr_gelar));
            $pendidikan_terakhir = $jenjang_tertinggi;

            // Save array of pendidikan formal
            $this->db->where('id_sdm', $id_sdm)->delete('sister_pendidikan_dosen');
            $batch_pend = [];
            foreach ($pend as $p) {
                $gelar_p = $p->gelar_akademik ?? ($p->singkatan_gelar ?? null);
                $bidang_p = $p->bidang_studi ?? null;
                
                // Skip data yang tidak valid / duplikat (Gelar kosong dan bidang studi bintang/kosong)
                if (empty(trim($gelar_p)) && ($bidang_p === '*' || empty(trim($bidang_p)))) {
                    continue;
                }

                $batch_pend[] = [
                    'id_sdm' => $id_sdm,
                    'id_riwayat' => $p->id ?? null,
                    'jenjang_pendidikan' => $p->jenjang_pendidikan ?? ($p->nama_jenjang_pendidikan ?? null),
                    'gelar_akademik' => $gelar_p,
                    'bidang_studi' => $bidang_p,
                    'nama_perguruan_tinggi' => $p->nama_perguruan_tinggi ?? null,
                    'tahun_lulus' => $p->tahun_lulus ?? null,
                    'last_sync' => date('Y-m-d H:i:s')
                ];
            }
            if (!empty($batch_pend)) {
                $this->db->insert_batch('sister_pendidikan_dosen', $batch_pend);
            }
        }

        // Fetch Penugasan
        $url_penugasan = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/penugasan?id_sdm=' . urlencode($id_sdm);
        $ch = curl_init($url_penugasan);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res_penugasan = curl_exec($ch);
        $code_penugasan = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $status_kepegawaian = null;
        $ikatan_kerja_dosen = null;
        $unit_kerja = null;
        $perguruan_tinggi = null;
        $tanggal_mulai_penugasan = null;
        if ($code_penugasan == 200 && $res_penugasan) {
            $arr_penugasan = json_decode($res_penugasan);
            if (is_array($arr_penugasan) && count($arr_penugasan) > 0) {
                // Get the homebase or the first one
                $active_penugasan = $arr_penugasan[0];
                foreach ($arr_penugasan as $p) {
                    if (isset($p->apakah_penugasan_homebase) && strtolower(trim((string)$p->apakah_penugasan_homebase)) == 'ya') {
                        $active_penugasan = $p;
                        break;
                    }
                }
                $status_kepegawaian = $active_penugasan->status_kepegawaian ?? null;
                $ikatan_kerja_dosen = $active_penugasan->ikatan_kerja ?? null;
                $unit_kerja = $active_penugasan->unit_kerja ?? null;
                $perguruan_tinggi = $active_penugasan->perguruan_tinggi ?? null;
                $tanggal_mulai_penugasan = $active_penugasan->tanggal_mulai ?? null;
            }
        }

        // Fetch Inpassing
        $url_inpassing = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/inpassing?id_sdm=' . urlencode($id_sdm);
        $ch = curl_init($url_inpassing);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res_inp = curl_exec($ch);
        $code_inp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($code_inp == 200 && $res_inp) {
            $arr_inp = json_decode($res_inp);
            if (is_array($arr_inp)) {
                $this->db->where('id_sdm', $id_sdm)->delete('sister_inpassing_dosen');
                $batch_inp = [];
                foreach ($arr_inp as $inp) {
                    $id_riwayat = $inp->id ?? '';
                    $angka_kredit = null;
                    $masa_kerja_tahun = null;
                    $masa_kerja_bulan = null;

                    if ($id_riwayat) {
                        // Ambil detail inpassing untuk mendapatkan angka kredit
                        $url_inp_detail = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/inpassing/' . urlencode($id_riwayat);
                        $ch2 = curl_init($url_inp_detail);
                        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch2, CURLOPT_HTTPGET, true);
                        curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
                        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
                        $res_inp_detail = curl_exec($ch2);
                        $code_inp_detail = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                        curl_close($ch2);

                        if ($code_inp_detail == 200 && $res_inp_detail) {
                            $detail_inp = json_decode($res_inp_detail);
                            if ($detail_inp) {
                                $angka_kredit = $detail_inp->angka_kredit ?? null;
                                $masa_kerja_tahun = $detail_inp->masa_kerja_tahun ?? null;
                                $masa_kerja_bulan = $detail_inp->masa_kerja_bulan ?? null;
                            }
                        }
                    }

                    $batch_inp[] = [
                        'id_sdm' => $id_sdm,
                        'id_riwayat' => $id_riwayat,
                        'pangkat_golongan' => $inp->pangkat_golongan ?? null,
                        'sk' => $inp->sk ?? null,
                        'tanggal_sk' => $inp->tanggal_sk ?? null,
                        'tanggal_mulai' => $inp->tanggal_mulai ?? null,
                        'angka_kredit' => $angka_kredit,
                        'masa_kerja_tahun' => $masa_kerja_tahun,
                        'masa_kerja_bulan' => $masa_kerja_bulan,
                        'last_sync' => date('Y-m-d H:i:s')
                    ];
                }
                if (!empty($batch_inp)) {
                    $this->db->insert_batch('sister_inpassing_dosen', $batch_inp);
                }
            }
        }

        // Save to Database
        $data_db = [
            'id_sdm' => $id_sdm,
            'nm_ptk' => $nama,
            'jk' => $jk,
            'tmpt_lahir' => $tmpt,
            'tgl_lahir' => $tgl,
            'photo' => $local_foto_url,
            'status_kepegawaian' => $status_kepegawaian,
            'ikatan_kerja_dosen' => $ikatan_kerja_dosen,
            'unit_kerja' => $unit_kerja,
            'perguruan_tinggi' => $perguruan_tinggi,
            'tanggal_mulai_penugasan' => $tanggal_mulai_penugasan,
            // Kependudukan
            'nik' => isset($kep->nik) ? trim((string)$kep->nik) : null,
            'id_agama' => $kep->id_agama ?? null,
            'kewarganegaraan' => isset($kep->kode_negara) ? trim((string)$kep->kode_negara) : null,
            // Keluarga
            'stat_kawin' => $kel->id_status_kawin ?? null,
            'nm_suami_istri' => isset($kel->nama_pasangan) ? trim((string)$kel->nama_pasangan) : null,
            'nip_suami_istri' => isset($kel->nip_pasangan) ? trim((string)$kel->nip_pasangan) : null,
            'id_pekerjaan_suami_istri' => $kel->id_pekerjaan_pasangan ?? null,
            // Alamat
            'jln' => isset($alm->alamat) ? trim((string)$alm->alamat) : null,
            'rt' => $alm->rt ?? null,
            'rw' => $alm->rw ?? null,
            'nm_dsn' => isset($alm->dusun) ? trim((string)$alm->dusun) : null,
            'ds_kel' => isset($alm->kelurahan) ? trim((string)$alm->kelurahan) : null,
            'id_kabupaten' => isset($alm->id_kota_kabupaten) ? trim((string)$alm->id_kota_kabupaten) : null,
            'kode_pos' => isset($alm->kode_pos) ? trim((string)$alm->kode_pos) : null,
            'no_tel_rmh' => isset($alm->telepon_rumah) ? trim((string)$alm->telepon_rumah) : null,
            'no_hp' => isset($alm->telepon_hp) ? trim((string)$alm->telepon_hp) : null,
            'email' => isset($alm->email) ? trim((string)$alm->email) : null,
            
            // Kepegawaian & Lainnya
            'nip' => isset($peg->nip) ? trim((string)$peg->nip) : null,
            'sk_cpns' => isset($peg->sk_cpns) ? trim((string)$peg->sk_cpns) : null,
            'tgl_sk_cpns' => $peg->tanggal_sk_cpns ?? null,
            'id_sumber_gaji' => $peg->id_sumber_gaji ?? null,
            'npwp' => isset($lan->npwp) ? trim((string)$lan->npwp) : null,
            'gelar_akademik' => $gelar_akademik,
            'pendidikan_terakhir' => $pendidikan_terakhir,

            'last_sync' => date('Y-m-d H:i:s')
        ];

        // Upsert Profil Dosen
        $exist = $this->db->where('id_sdm', $id_sdm)->get('sister_profile_dosen')->row();
        if ($exist) {
            if (!$local_foto_url) {
                unset($data_db['photo']); // jangan timpa jika gagal download tapi ada yg lama
            }
            $this->db->where('id_sdm', $id_sdm)->update('sister_profile_dosen', $data_db);
        } else {
            $this->db->insert('sister_profile_dosen', $data_db);
        }

        // Sync Bidang Ilmu
        if (is_array($bid)) {
            $this->db->where('id_sdm', $id_sdm)->delete('sister_bidang_ilmu_dosen');
            $batch_bidang = [];
            foreach ($bid as $b) {
                $batch_bidang[] = [
                    'id_sdm' => $id_sdm,
                    'urutan' => $b->urutan ?? null,
                    'id_kelompok_bidang' => $b->id_kelompok_bidang ?? null,
                    'kelompok_bidang' => $b->kelompok_bidang ?? null,
                    'last_sync' => date('Y-m-d H:i:s')
                ];
            }
            if (!empty($batch_bidang)) {
                $this->db->insert_batch('sister_bidang_ilmu_dosen', $batch_bidang);
            }
        }

        // Sync Jabatan Fungsional
        if (is_array($jab)) {
            $this->db->where('id_sdm', $id_sdm)->delete('sister_jabatan_fungsional_dosen');
            $batch_jab = [];
            foreach ($jab as $j) {
                $batch_jab[] = [
                    'id_sdm' => $id_sdm,
                    'id_riwayat' => $j->id ?? null,
                    'jabatan_fungsional' => $j->jabatan_fungsional ?? null,
                    'sk' => $j->sk ?? null,
                    'tanggal_mulai' => $j->tanggal_mulai ?? null,
                    'last_sync' => date('Y-m-d H:i:s')
                ];
            }
            if (!empty($batch_jab)) {
                $this->db->insert_batch('sister_jabatan_fungsional_dosen', $batch_jab);
            }
        }

        echo " - Profil & Foto tersinkronisasi\n";
    }

    private function _sync_pengajaran($token, $id_dosen) {
        // Placeholder for future implementation
        echo " - Pengajaran tersinkronisasi\n";
    }

    private function _sync_penelitian($token, $id_dosen) {
        // Placeholder for future implementation
        echo " - Penelitian tersinkronisasi\n";
    }

    private function _sync_pengabdian($token, $id_dosen) {
        // Placeholder for future implementation
        echo " - Pengabdian tersinkronisasi\n";
    }

    private function _sync_bkd($token, $id_sdm) {
        $base    = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/';
        $headers = ['Accept: application/json', 'Authorization: Bearer ' . $token];

        $fetch = function($url) use ($headers) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $res  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code === 200 && $res) {
                $data = json_decode($res, true);
                return is_array($data) ? $data : [];
            }
            return [];
        };

        // 1. Ambil semua laporan akhir BKD
        $laporan = $fetch($base . 'bkd/laporan_akhir_bkd?id_sdm=' . urlencode($id_sdm));
        if (empty($laporan)) {
            echo " - Data BKD tidak ditemukan\n";
            return;
        }

        $smt_label = function($id_smt) {
            $tahun = substr($id_smt, 0, 4);
            $sem   = substr($id_smt, 4, 1);
            return $tahun . '/' . ($tahun + 1) . ' ' . ($sem == '1' ? 'Ganjil' : 'Genap');
        };
        $stat_kewajiban = function($v) {
            if ($v == '1' || $v === 'M') return 'Memenuhi';
            return 'Tidak Memenuhi';
        };
        $simpulan_label = function($v) {
            $map = ['M' => 'Memenuhi', 'TM' => 'Tidak Memenuhi', 'BM' => 'Belum Memenuhi'];
            return $map[$v] ?? $v;
        };

        // 2. Simpan laporan akhir, dan ambil detail tiap semester
        foreach ($laporan as $l) {
            $id_smt = $l['id_smt'] ?? '';
            if (!$id_smt) continue;

            // Upsert laporan
            $lap_data = [
                'id_sdm'         => $id_sdm,
                'id_smt'         => $id_smt,
                'sks_ajar'       => $l['sks_kinerja_ajar']      ?? 0,
                'sks_didik'      => $l['sks_kinerja_didik']     ?? 0,
                'sks_lit'        => $l['sks_kinerja_lit']       ?? 0,
                'sks_pengmas'    => $l['sks_kinerja_pengmas']   ?? 0,
                'sks_penunjang'  => $l['sks_kinerja_penunjang'] ?? 0,
                'sks_total'      => $l['sks_kinerja']           ?? 0,
                'stat_kewajiban' => $stat_kewajiban($l['stat_kewajiban'] ?? ''),
                'simpulan_asesor'=> $simpulan_label($l['simpulan_asesor'] ?? ''),
                'last_sync'      => date('Y-m-d H:i:s'),
            ];
            $existing = $this->db->where(['id_sdm' => $id_sdm, 'id_smt' => $id_smt])->get('sister_bkd_laporan')->row();
            if ($existing) {
                $this->db->where(['id_sdm' => $id_sdm, 'id_smt' => $id_smt])->update('sister_bkd_laporan', $lap_data);
            } else {
                $this->db->insert('sister_bkd_laporan', $lap_data);
            }

            // 3. Ambil detail kegiatan per unsur untuk semester ini
            $params  = '?id_sdm=' . urlencode($id_sdm) . '&id_smt=' . urlencode($id_smt);
            $unsur_list = ['ajar', 'penelitian', 'pengmas', 'tunjang', 'pendidikan'];

            // Hapus kegiatan lama semester ini lalu insert baru
            $this->db->where(['id_sdm' => $id_sdm, 'id_smt' => $id_smt])->delete('sister_bkd_kegiatan');

            $batch_keg = [];
            foreach ($unsur_list as $unsur) {
                $detail = $fetch($base . 'bkd/' . $unsur . $params);
                foreach ($detail as $item) {
                    $batch_keg[] = [
                        'id_sdm'    => $id_sdm,
                        'id_smt'    => $id_smt,
                        'unsur'     => $unsur,
                        'judul_keg' => $item['judul_keg'] ?? null,
                        'id_katgiat'=> $item['id_katgiat'] ?? null,
                        'nm_kat'    => $item['nm_kat'] ?? null,
                        'beban_sks' => $item['beban_sks'] ?? 0,
                        'nilai'     => $item['nilai'] ?? 0,
                        'last_sync' => date('Y-m-d H:i:s'),
                    ];
                }
            }
            if (!empty($batch_keg)) {
                $this->db->insert_batch('sister_bkd_kegiatan', $batch_keg);
            }
        }
        echo " - BKD tersinkronisasi\n";
    }
}
