<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('cekUnreadLog')) {
    function cekUnreadLog($id_sms)
    {
        $CI = &get_instance();
        $log_book = $CI->db->where(['status_penetapan_judul' => 2, 'id_sms' => $id_sms])->get('siakad_skripsi_pengajuan_judul')->num_rows();
        return $log_book;

        /* if ($log_book->num_rows() == 0) {
            return false;
        } else {
            return true;
        } */
    }
}

if (!function_exists('cekNotifikasi')) {
    function cekNotifikasi($id_ptk)
    {
        $CI = &get_instance();
        $log_book = $CI->db->where(['id_ptk' => $id_ptk, 'is_read' => 0])->get('siakad_skripsi_notifikasi')->num_rows();
        return $log_book;

        /* if ($log_book->num_rows() == 0) {
            return false;
        } else {
            return true;
        } */
    }
}

if (!function_exists('cekApproval')) {
    function cekApproval($id_ptk)
    {
        $CI = &get_instance();
        //$log_book = $CI->db->where(['uniq' => $uniq, 'approval_p1' => 0])->get('siakad_skripsi_seminar_sidang')->num_rows();

        /*  $CI->db->group_start();
        $CI->db->where('dosen_pembimbing_1', $id_ptk);
        $CI->db->or_where('dosen_pembimbing_2', $id_ptk);
        $CI->db->group_end();

        $CI->db->group_start();
        $CI->db->where('approval_p1', 0);
        $CI->db->or_where('approval_p2', 0);
        $CI->db->group_end(); */

        $CI->db->group_start();
        $CI->db->where(['dosen_pembimbing_1' => $id_ptk, 'approval_p1' => 0]);
        $CI->db->group_end();

        $CI->db->group_start();
        $CI->db->or_where(['dosen_pembimbing_2' => $id_ptk, 'approval_p2' => 0]);
        $CI->db->group_end();

        $log_book = $CI->db->select('s.*, m.nm_pd')
            ->from('siakad_skripsi_seminar_sidang s')
            ->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')
            ->get()->num_rows();

        return $log_book;
    }
}

if (!function_exists('cekApprovalKaprodi')) {
    function cekApprovalKaprodi($pengajuan)
    {
        $CI = &get_instance();
        $id_sms = $CI->session->userdata('prodi');

        $log_book = $CI->db->select('s.*, m.nm_pd')
            ->where(['j.id_sms' => $id_sms, 'is_kaprodi' => 1, 'is_akademik' => 0, 'kategori_pengajuan' => $pengajuan])
            ->from('siakad_skripsi_seminar_sidang s')
            ->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')
            ->get()->num_rows();

        return $log_book;
    }
}

/* if (!function_exists('cekApprovalSemhasKaprodi')) {
    function cekApprovalSemhasKaprodi()
    {
        $CI = &get_instance();
        $id_sms = $CI->session->userdata('prodi');

        $log_book = $CI->db->select('s.*, m.nm_pd')
            ->where(['j.id_sms' => $id_sms, 'is_kaprodi' => 1, 'is_akademik' => 0, 'kategori_pengajuan' => 1])
            ->from('siakad_skripsi_seminar_sidang s')
            ->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')
            ->get()->num_rows();

        return $log_book;
    }
} */

if (!function_exists('notifikasi_data')) {
    function notifikasi_data($id_ptk)
    {
        $CI = &get_instance();
        /* $log_book = $CI->db->where(['id_ptk' => $id_ptk])->from('siakad_skripsi_notifikasi n')
            ->join('mahasiswa_pt mp', 'mp.nipd = n.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->get()->result(); */
        $log_book = $CI->db->where(['id_ptk' => $id_ptk])->order_by('tgl_notifikasi', 'DESC')->get('siakad_skripsi_notifikasi')->result();
        return $log_book;
    }
}

if (!function_exists('menuBimbingan')) {
    function menuBimbingan()
    {
        $CI = &get_instance();
        $id_ptk = $CI->session->userdata('id_ptk');
        $data_menu_bimbingan = $CI->db->where('dosen_pembimbing_1', $id_ptk)
            ->or_where('dosen_pembimbing_2', $id_ptk)
            ->from('siakad_skripsi_pengajuan_judul pj')
            ->join('mahasiswa_pt mp', 'mp.nipd = pj.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->order_by('nm_pd')->get()->result();

        return $data_menu_bimbingan;
    }
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal)
    {
        if (empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') {
            return '-';
        }
        $bulan = [
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        $tanggal_only = explode(' ', $tanggal)[0];
        $pecahkan = explode('-', $tanggal_only);
        if (count($pecahkan) < 3 || empty($pecahkan[0]) || $pecahkan[0] == '0000') {
            return '-';
        }
        $bln_idx = (int)$pecahkan[1];
        $bln_str = $bulan[$bln_idx] ?? '';
        return (int)$pecahkan[2] . ' ' . $bln_str . ' ' . $pecahkan[0];
    }
}

if (!function_exists('tgl_indo_short')) {
    function tgl_indo_short($tanggal)
    {
        if (empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') {
            return '';
        }
        
        // Handle datetime format (e.g. 2024-01-01 10:00:00)
        $tanggal_only = explode(' ', $tanggal)[0];
        
        $bulan = [
            1 =>   'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];
        $pecahkan = explode('-', $tanggal_only);
        
        if (count($pecahkan) == 3) {
            return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
        }
        return $tanggal;
    }
}

if (!function_exists('tgl_short')) {
    function tgl_short($tanggal)
    {
        if (empty($tanggal)) {
            return '';
        }
        $bulan = [
            1 =>   '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12'
        ];
        $pecahkan = explode('-', (string)$tanggal);
        if (count($pecahkan) < 3) {
            return (string)$tanggal;
        }
        return $pecahkan[2] . '.' . ($bulan[(int)$pecahkan[1]] ?? '') . '.' . substr((string)$pecahkan[0], 2, 2);
    }
}



if (!function_exists('clean_word_and_html_junk')) {
    function clean_word_and_html_junk($str)
    {
        if (empty($str)) return '';

        // 0. Bersihkan code fences markdown (```json, ```markdown, ```) dan backticks
        $str = preg_replace('/^```[a-zA-Z0-9_\-]*\s*$/m', '', $str);
        $str = str_replace('```', '', $str);
        $str = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $str);
        $str = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $str);
        $str = str_replace('`', '', $str);

        // 1. Hapus conditional comment Microsoft Word & HTML comments
        $str = preg_replace('/<!--\[if\b[\s\S]*?<!--\[endif\]-->/i', '', $str);
        $str = preg_replace('/<!--\[if[\s\S]*?<!\[endif\]-->/i', '', $str);
        $str = preg_replace('/<!--[\s\S]*?-->/', '', $str);

        // 2. Hapus blok style, script, xml
        $str = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $str);
        $str = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $str);
        $str = preg_replace('/<xml\b[^>]*>[\s\S]*?<\/xml>/i', '', $str);
        $str = preg_replace('/<\/?\w+:[^>]*>/i', '', $str);

        return trim($str);
    }
}

if (!function_exists('clean_wysiwyg_data')) {
    /**
     * Membersihkan input dari form WYSIWYG (pasting Word, Excel/Google Sheets, dan Halaman Web)
     * sebelum disimpan ke database (Save/Update).
     *
     * @param mixed $data String atau Array data POST
     * @param bool $allow_tables Apakah tag tabel diizinkan
     * @return mixed Data yang sudah bersih dan rapi
     */
    function clean_wysiwyg_data($data, $allow_tables = true)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = clean_wysiwyg_data($val, $allow_tables);
            }
            return $data;
        }

        if (!is_string($data) || trim($data) === '') {
            return $data;
        }

        $str = $data;

        // 1. Bersihkan komentar Word, HTML, XML, script & style
        $str = preg_replace('/<!--\[if\b[\s\S]*?<!--\[endif\]-->/i', '', $str);
        $str = preg_replace('/<!--\[if[\s\S]*?<!\[endif\]-->/i', '', $str);
        $str = preg_replace('/<!--[\s\S]*?-->/', '', $str);
        $str = preg_replace('/<\?xml[^>]*>/i', '', $str);
        $str = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $str);
        $str = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $str);
        $str = preg_replace('/<xml\b[^>]*>[\s\S]*?<\/xml>/i', '', $str);
        $str = preg_replace('/<\/?\w+:[^>]*>/i', '', $str);

        // 2. Decode non-breaking spaces & Google Sheets / Excel artifacts
        $str = str_replace('&nbsp;', ' ', $str);
        $str = preg_replace('/\s*data-sheets-[a-z0-9_-]+="[^"]*"/i', '', $str);
        $str = preg_replace('/\s*class="[^"]*Mso[^"]*"/i', '', $str);

        // 3. Hapus tag font dan unwrap span
        $str = preg_replace('/<\/?font[^>]*>/i', '', $str);
        $str = preg_replace('/<\/?span[^>]*>/i', '', $str);

        // 4. Hapus atribut inline style berlebih (font-family, margin, text-indent, dll)
        $str = preg_replace('/\s*style\s*=\s*(?:"[^"]*"|\'[^\']*\')/i', '', $str);
        $str = preg_replace('/\s*(?:lang|dir|align|valign|face|size|color)\s*=\s*(?:"[^"]*"|\'[^\']*\')/i', '', $str);

        // 5. Normalisasi bullet symbol non-standar dari Word / Web
        $str = preg_replace('/[\x{2022}\x{2023}\x{25E6}\x{2043}\x{2219}]/u', '•', $str);
        $str = preg_replace('/(?:\b|(?<=^))·\s*/u', '• ', $str);

        // 6. Filter tag yang diperbolehkan
        $allowed = $allow_tables 
            ? '<table><thead><tbody><tfoot><tr><th><td><p><br><b><strong><i><em><u><ol><ul><li><a><blockquote>' 
            : '<p><br><b><strong><i><em><u><ol><ul><li><a><blockquote>';
        $str = strip_tags($str, $allowed);

        // 7. Rapikan whitespace dan tag kosong bertumpuk
        $str = preg_replace('/[ \t]+/', ' ', $str);
        $str = preg_replace('/<(p|div)>\s*(?:<br\s*\/?>|\s)*<\/\1>/i', '', $str);
        $str = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br><br>', $str);

        return trim($str);
    }
}

if (!function_exists('clean_style')) {
    function clean_style($string, $allowed_tags = '')
    {
        if (empty($string)) return '';

        // Bersihkan sampah Word/XML comments & style/script tags
        $string = clean_word_and_html_junk($string);

        if (str_contains($string, '<table')) {
            $cleanHtml = strip_tags($string, '<table><tr><td><th><thead><tbody><tfoot><br><p><b><strong><i><em><ul><ol><li>');
            $cleanHtml = preg_replace('/<table([^>]*)>/i', '<table$1 style="border-collapse: collapse; width: 100%;" border="1" cellspacing="0" cellpadding="5">', $cleanHtml);
            $cleanHtml = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="border: 1px solid #c0c0c0;">', $cleanHtml);
            $cleanHtml = preg_replace('/<td([^>]*)>/i', '<td$1 style="border: 1px solid #c0c0c0; padding: 6px 8px;">', $cleanHtml);
            $cleanHtml = preg_replace('/<th([^>]*)>/i', '<th$1 style="border: 1px solid #7a7a7a; padding: 6px 8px; background-color: #f1f5f9;">', $cleanHtml);
        } else {
            $tags = !empty($allowed_tags) ? $allowed_tags : '<br><p><b><strong><i><em><u><ol><ul><li>';
            $cleanHtml = preg_replace('/\s*style="[^"]*"/i', '', $string);
            $cleanHtml = preg_replace('/<p[^>]*>/i', '<p>', $cleanHtml);
            $cleanHtml = preg_replace('/<font[^>]*>(.*?)<\/font>/i', '$1', $cleanHtml);
            $cleanHtml = strip_tags($cleanHtml, $tags);
            $cleanHtml = preg_replace('/<p>\s*<\/p>/i', '', $cleanHtml);
        }

        return trim($cleanHtml);
    }
}

if (!function_exists('format_rps_content_list')) {
    function format_rps_content_list($html, $is_description = false)
    {
        if (empty($html)) return '';

        // 1. Bersihkan seluruh komentar Word, XML, HTML, style/script tags
        $str = $html;
        $str = preg_replace('/<!--\[if[\s\S]*?<!\[endif\]-->/i', '', $str);
        $str = preg_replace('/<!--[\s\S]*?-->/', '', $str);
        $str = preg_replace('/<\?xml[^>]*>/i', '', $str);
        $str = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $str);
        $str = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $str);
        $str = preg_replace('/<xml\b[^>]*>[\s\S]*?<\/xml>/i', '', $str);
        $str = preg_replace('/<\/?\w+:[^>]*>/i', '', $str);

        // Decode non-breaking spaces
        $str = str_replace('&nbsp;', ' ', $str);

        // Hapus tag font/span tapi simpan isinya
        $str = preg_replace('/<\/?(?:font|span)[^>]*>/i', '', $str);

        // Hapus atribut style, class, lang, align, dll
        $str = preg_replace('/\s*(?:style|class|lang|dir|align|valign|face|size|color)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $str);

        // Jika mengandung tabel, rapikan tabelnya
        if (str_contains($str, '<table')) {
            $cleanHtml = strip_tags($str, '<table><tr><td><th><thead><tbody><tfoot><br><p><b><strong><i><em><ul><ol><li>');
            $cleanHtml = preg_replace('/<table([^>]*)>/i', '<table$1 style="border-collapse: collapse; width: 100%;" border="1" cellspacing="0" cellpadding="5">', $cleanHtml);
            $cleanHtml = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="border: 1px solid #c0c0c0;">', $cleanHtml);
            $cleanHtml = preg_replace('/<td([^>]*)>/i', '<td$1 style="border: 1px solid #c0c0c0; padding: 6px 8px;">', $cleanHtml);
            $cleanHtml = preg_replace('/<th([^>]*)>/i', '<th$1 style="border: 1px solid #7a7a7a; padding: 6px 8px; background-color: #f1f5f9;">', $cleanHtml);
            return $cleanHtml;
        }

        // Pecah berdasarkan tag penutup paragraf atau breakline
        $parts = preg_split('/<\/p>|<br\s*\/?>|<\/li>|<\/div>/i', $str);
        $output = [];
        $current_level = 0;

        foreach ($parts as $p) {
            $p = trim(strip_tags($p, '<b><strong><i><em><u>'));
            if (empty($p)) continue;

            // Bersihkan whitespace berlebih di dalam string
            $p = preg_replace('/[ \t]+/', ' ', $p);
            $plain = trim(strip_tags($p));
            if (empty($plain)) continue;

            // Rapikan spasi setelah nomor/kode (contoh: "S3.Menunjukkan" -> "S3. Menunjukkan")
            $p = preg_replace('/^([A-Za-z0-9\.\(\)]+\.)([^\s<])/i', '$1 $2', $p);
            $plain = preg_replace('/^([A-Za-z0-9\.\(\)]+\.)([^\s])/i', '$1 $2', $plain);

            // Jika mode deskripsi / narasi biasa
            if ($is_description) {
                // Jika deskripsi memiliki list seperti "1. Teks" atau "a. Teks" atau bullet
                if (preg_match('/^((?:<(?:b|strong)>)?\s*(?:(?:\d+|[a-z]|[A-Z]{1,3}\d*(?:\.\d+)*)[\.\)]|•|\-|\([a-z0-9]+\))(?:<\/(?:b|strong)>)?)\s*(.*)$/si', $p, $m)) {
                    $prefix = $m[1];
                    $body = $m[2];
                    $clean_pfx = trim(strip_tags($prefix));
                    $pfx_w = (strlen($clean_pfx) > 3) ? '24px' : ((strlen($clean_pfx) > 2) ? '18px' : '14px');
                    $output[] = '<table style="width: 100%; border: 0 !important; border-collapse: collapse; margin-bottom: 3px;">
                        <tr>
                            <td style="width: ' . $pfx_w . '; vertical-align: top; border: 0 !important; padding: 1px 2px 1px 0; font-size: 11px; line-height: 1.45;">' . $prefix . '</td>
                            <td style="vertical-align: top; border: 0 !important; padding: 1px 0; font-size: 11px; line-height: 1.45; text-align: justify;">' . $body . '</td>
                        </tr>
                    </table>';
                } else {
                    $output[] = '<div style="text-align: justify; line-height: 1.45; margin-bottom: 4px; font-size: 11px;">' . $p . '</div>';
                }
                continue;
            }

            // 1. Header Kategori Utama (contoh: "1. Sikap", "2. Pengetahuan", "3. Keterampilan Khusus", "b. Keterampilan Khusus")
            if (preg_match('/^(\d+|[a-z])\.\s*(Sikap|Pengetahuan|Keterampilan\s+Umum|Keterampilan\s+Khusus)/i', $plain)) {
                $current_level = 0;
                $title = strip_tags($p);
                $output[] = '<div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 11px; color: #000;">' . $title . '</div>';
            }
            // 2. Sub-sub item / Kode bertingkat (contoh: "P2.1.", "P2.2.", "P.3.", "1.1.", "a.1.")
            elseif (preg_match('/^((?:<(?:b|strong)>)?\s*(?:P\d*\.\d+\.?|P\.\s*\d+\.?|\d+\.\d+\.?|[a-z]\.\d+\.?)(?:<\/(?:b|strong)>)?)\s*(.*)$/si', $p, $m)) {
                $current_level = 2;
                $prefix = $m[1];
                $body = $m[2];
                $clean_pfx = trim(strip_tags($prefix));
                $pfx_w = (strlen($clean_pfx) > 4) ? '35px' : '28px';

                $output[] = '<table style="width: 100%; border: 0 !important; border-collapse: collapse; margin-bottom: 2px; margin-left: 18px;">
                    <tr>
                        <td style="width: ' . $pfx_w . '; vertical-align: top; border: 0 !important; padding: 1px 2px 1px 0; font-size: 10.5px; line-height: 1.35;">' . $prefix . '</td>
                        <td style="vertical-align: top; border: 0 !important; padding: 1px 0; font-size: 10.5px; line-height: 1.35; text-align: justify;">' . $body . '</td>
                    </tr>
                </table>';
            }
            // 3. Sub-item list (contoh: "S1.", "S2.", "P1.", "KK3.", "KU1.", "a.", "b.", "c.", "1.", "2.", "(a)", "(1)", "•", "-")
            elseif (preg_match('/^((?:<(?:b|strong)>)?\s*(?:[A-Z]{1,3}\d+\.?|[a-z]\.|\d+\.|\([a-z0-9]+\)|•|\-)(?:<\/(?:b|strong)>)?)\s*(.*)$/si', $p, $m)) {
                $current_level = 1;
                $prefix = $m[1];
                $body = $m[2];
                $clean_pfx = trim(strip_tags($prefix));
                $pfx_w = (strlen($clean_pfx) > 3) ? '26px' : ((strlen($clean_pfx) > 2) ? '18px' : '14px');

                $output[] = '<table style="width: 100%; border: 0 !important; border-collapse: collapse; margin-bottom: 2px;">
                    <tr>
                        <td style="width: ' . $pfx_w . '; vertical-align: top; border: 0 !important; padding: 1px 2px 1px 0; font-size: 10.5px; line-height: 1.35;">' . $prefix . '</td>
                        <td style="vertical-align: top; border: 0 !important; padding: 1px 0; font-size: 10.5px; line-height: 1.35; text-align: justify;">' . $body . '</td>
                    </tr>
                </table>';
            }
            // 4. Paragraf lanjutan / teks tanpa nomor
            else {
                $indent = ($current_level === 2) ? 46 : (($current_level === 1) ? 20 : 0);
                $output[] = '<div style="margin-left: ' . $indent . 'px; margin-bottom: 2px; font-size: 10.5px; line-height: 1.35; text-align: justify;">' . $p . '</div>';
            }
        }

        return implode("\n", $output);
    }
}

if (!function_exists('resolve_dosen_nama_lengkap')) {
    function resolve_dosen_nama_lengkap($nama_or_raw, $fallback_nm_ptk = '')
    {
        if (empty($nama_or_raw)) {
            return !empty($fallback_nm_ptk) ? $fallback_nm_ptk : '-';
        }

        // Jika sudah mengandung gelar (contoh: SP, M.Si, Dr., M.Kom, S.P., S.T., M.MA, dll)
        if (preg_match('/[,\.](?:\s*(?:S\.|M\.|Dr\.|Drs\.|Ir\.|Prof\.|Ph\.D|M\.Si|SP|S\.P|M\.MA|MM|M\.Kom|S\.Kom|S\.E|M\.E))/i', $nama_or_raw)) {
            return $nama_or_raw;
        }

        // Cari di database siakad_dosen
        $clean_name = trim(strip_tags($nama_or_raw));
        $ci = &get_instance();
        if (isset($ci->db)) {
            $names = preg_split('/[,;\n]+/', $clean_name);
            $resolved_names = [];

            foreach ($names as $name_item) {
                $name_item = trim($name_item);
                if (empty($name_item)) continue;

                $dosen = $ci->db->select('nm_ptk')
                    ->like('nm_ptk', $name_item)
                    ->get('siakad_dosen')->row();

                if ($dosen && !empty($dosen->nm_ptk)) {
                    $resolved_names[] = $dosen->nm_ptk;
                } else {
                    $resolved_names[] = $name_item;
                }
            }

            if (!empty($resolved_names)) {
                return implode('<br>', $resolved_names);
            }
        }

        return !empty($fallback_nm_ptk) ? $fallback_nm_ptk : $nama_or_raw;
    }
}

if (!function_exists('format_matrix_cell')) {
    function format_matrix_cell($str)
    {
        if (empty($str)) return '';

        // 1. Bersihkan komentar Word, XML, tag font/span, atribut inline
        $str = preg_replace('/<!--\[if[\s\S]*?<!\[endif\]-->/i', '', $str);
        $str = preg_replace('/<!--[\s\S]*?-->/', '', $str);
        $str = str_replace('&nbsp;', ' ', $str);
        $str = preg_replace('/<\/?(?:font|span)[^>]*>/i', '', $str);
        $str = preg_replace('/\s*(?:style|class|lang|dir|align|valign|face|size|color)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $str);

        // 2. Normalisasi bullet symbol
        $str = preg_replace('/[\x{2022}\x{2023}\x{25E6}\x{2043}\x{2219}]/u', '•', $str);

        // 3. Deteksi penomoran / bullet inline dalam satu baris (misal: "1. Teks satu. 2. Teks dua." atau "a. Satu. b. Dua.")
        $str = preg_replace('/(?<!^)(?<!\n)(?<!<br>)(?<!<br\/>)(?<!<br \/>)\s+(?=(?:(?:\d+|[a-z])[\.\)]|•|\-)\s+[A-Z0-9])/i', '<br>', $str);

        // 4. Pecah berdasarkan breakline / tag paragraf / list
        $lines = preg_split('/<br\s*\/?>|<\/p>|<\/li>|<\/div>|\n/i', $str);
        $items = [];

        foreach ($lines as $line) {
            $line = trim(strip_tags($line, '<b><strong><i><em><u>'));
            if (empty($line)) continue;

            $line = preg_replace('/[ \t]+/', ' ', $line);
            $plain = trim(strip_tags($line));
            if (empty($plain)) continue;

            // Rapikan spasi setelah nomor/kode (contoh: "1.Mahasiswa" -> "1. Mahasiswa")
            $line = preg_replace('/^((?:\d+|[a-z]|[A-Z]{1,3}\d*)[\.\)]|•|\-)([^\s<])/i', '$1 $2', $line);
            $plain = preg_replace('/^((?:\d+|[a-z]|[A-Z]{1,3}\d*)[\.\)]|•|\-)([^\s])/i', '$1 $2', $plain);

            // Cek apakah item adalah list
            if (preg_match('/^((?:<(?:b|strong)>)?\s*(?:(?:\d+|[a-z]|[A-Z]{1,3}\d*(?:\.\d+)*)[\.\)]|•|\-|\([a-z0-9]+\))(?:<\/(?:b|strong)>)?)\s*(.*)$/si', $line, $m)) {
                $prefix = $m[1];
                $body = $m[2];
                $clean_pfx = trim(strip_tags($prefix));
                $pfx_w = (strlen($clean_pfx) > 3) ? '22px' : ((strlen($clean_pfx) > 2) ? '16px' : '12px');

                $items[] = '<table style="width: 100%; border: 0 !important; border-collapse: collapse; margin: 0 0 2px 0; padding: 0;">
                    <tr>
                        <td style="width: ' . $pfx_w . '; vertical-align: top; border: 0 !important; padding: 0 2px 1px 0; font-size: inherit; line-height: 1.35;">' . $prefix . '</td>
                        <td style="vertical-align: top; border: 0 !important; padding: 0 0 1px 0; font-size: inherit; line-height: 1.35; text-align: justify;">' . $body . '</td>
                    </tr>
                </table>';
            } else {
                $items[] = '<div style="margin-bottom: 2px; line-height: 1.35; text-align: justify;">' . $line . '</div>';
            }
        }

        if (count($items) === 0) return '';
        return implode("\n", $items);
    }
}


//another indonesian date, untuk jaga-jaga kalo seandainya bisa dipakai
/*function indonesian_date ($timestamp = '', $date_format = 'l, j F Y | H:i', $suffix = 'WIB') {
    if (trim ($timestamp) == '')
    {
            $timestamp = time ();
    }
    elseif (!ctype_digit ($timestamp))
    {
        $timestamp = strtotime ($timestamp);
    }
    # remove S (st,nd,rd,th) there are no such things in indonesia :p
    $date_format = preg_replace ("/S/", "", $date_format);
    $pattern = array (
        '/Mon[^day]/','/Tue[^sday]/','/Wed[^nesday]/','/Thu[^rsday]/',
        '/Fri[^day]/','/Sat[^urday]/','/Sun[^day]/','/Monday/','/Tuesday/',
        '/Wednesday/','/Thursday/','/Friday/','/Saturday/','/Sunday/',
        '/Jan[^uary]/','/Feb[^ruary]/','/Mar[^ch]/','/Apr[^il]/','/May/',
        '/Jun[^e]/','/Jul[^y]/','/Aug[^ust]/','/Sep[^tember]/','/Oct[^ober]/',
        '/Nov[^ember]/','/Dec[^ember]/','/January/','/February/','/March/',
        '/April/','/June/','/July/','/August/','/September/','/October/',
        '/November/','/December/',
    );
    $replace = array ( 'Sen','Sel','Rab','Kam','Jum','Sab','Min',
        'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
        'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des',
        'Januari','Februari','Maret','April','Juni','Juli','Agustus','Sepember',
        'Oktober','November','Desember',
    );
    $date = date ($date_format, $timestamp);
    $date = preg_replace ($pattern, $replace, $date);
    $date = "{$date} {$suffix}";
    return $date;
} */

if (!function_exists('cek_tunggakan')) {
    function cek_tunggakan($nipd = null)
    {
        require_once(APPPATH . '/third_party/siakad_api/sokaru_cfg.php');

        $CI = &get_instance();
        $nipd ??= $CI->session->userdata('nim');
        $is_menunggak = 0;
        $bill_id = 1;

        $surat_keterangan = $CI->db->where('nipd', $nipd)->where('tgl_berakhir_surat >=', 'CURDATE()', false)->get('siakad_keringanan_kuliah');

        $datas    = ['nim' => '00' . $nipd, 'bill_id' => $bill_id];
        $mode    = 'bill_info';
        $hasil    = kirim($Consumer_Key, $Consumer_Secret, $mode, $datas);
        $hasil2    = kirim($Consumer_Key, $Consumer_Secret, $mode, ['nim' => '00' . $nipd, 'bill_id' => 2]);
        $CI->session->set_userdata(['hasil' => $hasil, 'hasil2' => $hasil2]);

        if ($surat_keterangan->num_rows() > 0) {
            $is_menunggak = 0;
        } else {

            if (isset($hasil) && $hasil['responseCode'] == '00') {
                if ($hasil['dataMahasiswa'][0]['TUNGGAKAN'] > 0) {
                    $is_menunggak = 1;
                }
            }
            if (isset($hasil2) && $hasil2['responseCode'] == '00') {
                if ($hasil2['dataMahasiswa'][0]['TUNGGAKAN'] > 0) {
                    $is_menunggak = 1;
                }
            }
        }

        return $is_menunggak;
        //return 0;
    }
}

if (!function_exists('time_ago')) {
    function time_ago($datetime, $full = false) {
        if (empty($datetime)) return '';
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . '';
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' lalu' : 'baru saja';
    }
}