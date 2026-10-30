<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class Qr_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function create_qr($datanya, $nipd = null)
	{
		$writer = new PngWriter();
		$qrCode = new QrCode(
			data: (string)$datanya,
			encoding: new Encoding('UTF-8'),
			errorCorrectionLevel: ErrorCorrectionLevel::Medium,
			size: 65,
			margin: 2,
			roundBlockSizeMode: RoundBlockSizeMode::None,
			foregroundColor: new Color(0, 0, 0),
			backgroundColor: new Color(255, 255, 255)
		);

		$result = $writer->write($qrCode);
		return $result->getDataUri();
	}

	public function footer()
	{
		$f = '<div style="font-size: 11px; color: #006a6f ;bottom:15;left:85;position:absolute">
				<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
				<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
					Kabupaten Deli Serdang – Sumatera Utara<br>
					(061) 42074111; Laman : www.wbi.ac.id<br>
				</p>
 		 	</div>';
		return $f;
	}

	public function header()
	{
		$imgPath = FCPATH . 'assets/image_web/kop_wbi.png';
		$imgData = base64_encode(file_get_contents($imgPath));
		$h = '<table width="100%" style="border:0;"><tr>
				<td style="border:0; text-align:right;">
					<img src="data:image/png;base64,' . $imgData . '" width="180px" />
				</td>
			</tr></table>';

		return $h;
	}

	public function judul($enkrip)
	{
		$j = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
			->join('sms s', 's.id_sms = mp.id_sms', 'left')
			->where('md5(j.id_judul_skripsi)', $enkrip)->get()->row();
		return $j;
	}

	public function judul_undangan($id_judul_skripsi, $tipe)
	{
		return $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
			->join('sms s', 's.id_sms = mp.id_sms', 'left')
			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = j.id_judul_skripsi')
			->where(['md5(ss.id_judul_skripsi)' => $id_judul_skripsi, 'ss.kategori_pengajuan' => $tipe])->get()->row();
	}

	public function judul_by_uniq($uniq, $tipe)
	{
		return $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
			->join('sms s', 's.id_sms = mp.id_sms', 'left')
			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = j.id_judul_skripsi')
			->where(['md5(ss.uniq)' => $uniq, 'ss.kategori_pengajuan' => $tipe])->get()->row();
	}

	public function judul_berita_acara($enkrip)
	{
		$j = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
			->join('sms s', 's.id_sms = mp.id_sms', 'left')
			->where('md5(ns.id_judul_skripsi)', $enkrip)->get()->row();

		return $j;
	}

	public function nilai_sidang($judul)
	{
		$n = $this->db->select('avg(sidang_nilai_a) as nilai_a, avg(sidang_nilai_b) as nilai_b, avg(sidang_nilai_c) as nilai_c, 
								avg(sidang_nilai_d) as nilai_d, avg(sidang_nilai_e) as nilai_e, avg(sidang_nilai_akhir) as sidang_nilai_akhir')
			->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->where('j.id_judul_skripsi', $judul->id_judul_skripsi)->get()->row();

		return $n;
	}

	public function nilai_semhas($judul)
	{
		return $this->db->select('avg(semhas_nilai_a) as nilai_a, avg(semhas_nilai_b) as nilai_b, avg(semhas_nilai_c) as nilai_c, 
		avg(semhas_nilai_d) as nilai_d, avg(semhas_nilai_e) as nilai_e, avg(semhas_nilai_f) as nilai_f')
			->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->where('j.id_judul_skripsi', $judul->id_judul_skripsi)->get()->row();
	}

	public function nilai_semhas_by_dosen($judul, $id_ptk)
	{
		return $this->db->select('semhas_nilai_a,semhas_nilai_b,semhas_nilai_c,semhas_nilai_d,semhas_nilai_e,semhas_nilai_f')
			->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->where(['j.id_judul_skripsi' => $judul->id_judul_skripsi, 'id_penilai' => $id_ptk])->get()->row();
	}

	public function nilai_akhir_by_dosen($id_ptk, $id_judul_skripsi)
	{
		$where = ['j.id_judul_skripsi' => $id_judul_skripsi, 'ns.id_penilai' => $id_ptk];
		$nilai_akhir_semhas = $this->db->select('*')
			->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->where($where)->get()->row();

		return $nilai_akhir_semhas;
	}

	public function nilai_rata_rata_akhir_semhas($judul)
	{
		$q = $this->db->where(['id_judul_skripsi' => $judul->id_judul_skripsi])
			->select('avg(semhas_nilai_akhir) as semhas_nilai_akhir')
			->get('siakad_skripsi_nilai_sempro')->row();

		$rata_rata = round($q->semhas_nilai_akhir, 2);

		return $rata_rata;
	}

	public function nilai_sempro($judul)
	{
		return $this->db->select('avg(nilai_komponen_a) as nilai_a, avg(nilai_komponen_b) as nilai_b, avg(nilai_komponen_c) as nilai_c, 
		avg(nilai_komponen_d) as nilai_d, avg(nilai_komponen_e) as nilai_e')->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->where('j.id_judul_skripsi', $judul->id_judul_skripsi)->get()->row();
	}

	public function nilai_sempro_by_dosen($judul, $id_ptk)
	{
		return $this->db->select('nilai_komponen_a, nilai_komponen_b, nilai_komponen_c, 
		nilai_komponen_d, nilai_komponen_e')->from('siakad_skripsi_pengajuan_judul j')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = j.id_judul_skripsi')
			->where(['j.id_judul_skripsi' => $judul->id_judul_skripsi, 'id_penilai' => $id_ptk])->get()->row();
	}



	/* function nilai_akhir_semhas_by_dosen($judul)
	{
		return $this->db->where('id_penilai', $judul->dosen_pembimbing_1)->get('siakad_skripsi_nilai_sempro')->row()->semhas_nilai_akhir;
	} */

	public function data_sidang($judul)
	{
		$ds = $this->db->where(['id_judul_skripsi' => $judul->id_judul_skripsi, 'kategori_pengajuan' => 3, 'nipd' => $judul->nipd])->get('siakad_skripsi_seminar_sidang')->row();
		return $ds;
	}

	public function nama_dosen($id_ptk)
	{
		return $this->db->where('sd.id_ptk', $id_ptk)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
	}

	public function penguji_sidang($penguji)
	{
		$p = $this->db->where('sd.id_ptk', $penguji)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		return $p;
	}

	public function nilai_penguji($penguji, $judul)
	{
		$p = $this->db->where(['id_penilai' => $penguji, 'id_judul_skripsi' => $judul->id_judul_skripsi])->get('siakad_skripsi_nilai_sempro')->row();
		return $p;
	}


	//dipakai untuk usulan semhas dan usulan sempro
	public function usulan($judul, $id_sms)
	{
		$s = $this->db->where(['id_judul_skripsi' => $judul->id_judul_skripsi, 'sd.id_sms' => $id_sms])
			->select('*')->from('siakad_skripsi_nilai_sempro sn')
			->join('siakad_skripsi_dosen sd', 'sd.id_ptk = sn.id_penilai')
			->get()->result();

		return $s;
	}


	public function nama_kaprodi($id_sms, $id_smt)
	{
		return  $this->db->where(['id_sms' => $id_sms, 'id_smt' => $id_smt])->get('siakad_administrasi_prodi')->row();
	}

	public function berita_acara($judul)
	{
		return  $this->db->where(['id_penilai' => $judul->dosen_pembimbing_1, 'nipd' => $judul->nipd])->get('siakad_skripsi_nilai_sempro')->row();
	}

	public function nama_dosen_pembimbing($judul)
	{
		return $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_skripsi_dosen sd')->join('siakad_dosen d', 'sd.id_ptk = d.id_ptk')->get()->row();
	}

	public function template_tabel()
	{
		return [
			'table_open'          => '<table class="table table-black">',
			'thead_open'          => '<thead >',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center">',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		];
	}
}
