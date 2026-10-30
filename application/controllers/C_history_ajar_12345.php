<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_history_ajar_12345 extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		// $id_ptk = $this->session->userdata('id_ptk');
		// $id_smt = $this->session->userdata('tahun_ajaran');


		$output['judul_besar'] = 'History Ajar Dosen ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_history_ajar';
		$output['parent_history'] = 'active open';
		$output['content'] = 'history_ajar/history_ajar_view';
		$output['after_page'] = 'history_ajar/history_ajar_after_page';
		$output['journey'] = $this->journey();

		$this->load->view('master_page', $output);
	}

	public function get_journey_by_smt($id_smt)
	{



		$data = $this->db->where(['id_sdm' => $this->session->userdata('id_ptk'), 'id_smt' => $id_smt, 'id_kategori_jadwal' => 1])
			->from('siakad_jadwal j')->join('siakad_hari h', 'j.id_hari = h.uniq')
			->get()->result();

		$i = 1;
		$tabel = "<table class='table table-bordered'>";
		$tabel .= "<tbody>";
		$tabel .= "<tr>
						<td>No</td>
						<td>Mata Kuliah</td>
						<td>BAP</td>
						<td>Nilai</td>
						<td>Kehadiran</td>
						<td>EPOM</td>
						<td>RPS</td>
						<td>SK Ajar</td>
					</tr>";
		foreach ($data as $key => $value) {
			$rps = $this->db->where('id_jadwal', $value->id)->get('siakad_rps_mk_prodi');
			if ($rps->num_rows()) {
				$id_rps = $rps->row(1)->id_rps;
				$html_rps = "<a href='" . site_url() . "C_rps_online/preview_rps/" . md5($id_rps) . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>";
			} else {
				$html_rps = '-';
			}

			$tabel .= "<tr>
							<td>" . $i++ . "</td><td>" . $value->tag_name . ' <br><small>' . $value->nama_hari . ', Jam : ' . substr($value->jam_mulai, 0, 5) . '</small>' . "</td>
							<td> 
								<a href='" . site_url() . "C_summary_bap/cetak_pdf_berita_acara/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_nilai/cetak_pdf_nilai/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_kehadiran/cetak_pdf_absen/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_epom/cetak_pdf_epom/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_epom/cetak_pdf_epom/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								$html_rps
							</td>
						</tr>";
		}
		$tabel .= "</tbody>";
		$tabel .=	"</table>";

		$arr = ['tabel' => $tabel];

		echo  json_encode($arr);
	}

	public function get_seminar_by_smt($id_smt)
	{
		$data = $this->db->where(['id_sdm' => $this->session->userdata('id_ptk'), 'id_smt' => $id_smt, 'id_kategori_jadwal' => 1])
			->from('siakad_jadwal')
			->get()->result();

		$i = 1;
		$tabel = "<table class='table table-bordered'>";
		$tabel .= "<tbody>";
		$tabel .= "<tr><td>No</td><td>Mata Kuliah</td><td>BAP</td><td>Nilai</td><td>Kehadiran</td><td>EPOM</td><td>RPS</td></tr>";
		foreach ($data as $key => $value) {
			$rps = $this->db->where('id_jadwal', $value->id)->get('siakad_rps_mk_prodi');
			if ($rps->num_rows()) {
				$id_rps = $rps->row(1)->id_rps;
				$html_rps = "<a href='" . site_url() . "C_rps_online/preview_rps/" . $id_rps . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>";
			} else {
				$html_rps = '-';
			}

			$tabel .= "<tr>
							<td>" . $i++ . "</td><td>" . $value->tag_name . "</td>
							<td> 
								<a href='" . site_url() . "C_summary_bap/cetak_pdf_berita_acara/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_nilai/cetak_pdf_nilai/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_kehadiran/cetak_pdf_absen/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								<a href='" . site_url() . "C_summary_epom/cetak_pdf_epom/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>
							</td>
							<td>
								$html_rps
							</td>
						</tr>";
		}
		$tabel .= "</tbody>";
		$tabel .=	"</table>";

		$arr = ['tabel' => $tabel];

		echo  json_encode($arr);
	}


	private function button($id_smt)
	{
		$html_download = '<a href="javascript:void(0)" 
									class="btn btn-xs btn-default green-stripe"
									onclick="ajax_download(\'' . $id_smt . '\')">
								View</a>';

		return $html_download;
	}

	private function button_12345($id_smt)
	{
		$html_download = '<a href="javascript:void(0)" 
									class="btn btn-xs btn-default green-stripe"
									onclick="ajax_download_2(\'' . $id_smt . '\')">
								View</a>';

		return $html_download;
	}

	private function button2($id_smt)
	{
		$html_download = '<a href="javascript:void(0)" 
									class="btn btn-xs btn-default green-stripe"
									onclick="ajax_berita_acara(\'' . $id_smt . '\')">
								View</a>';

		return $html_download;
	}

	public function journey()
	{
		$id_smt = $this->session->userdata('id_smt');
		$ts = substr($id_smt, 0, 4);

		$ts_2 = $ts - 2;
		$ts_1 = $ts - 1;

		$id_smt_1 = $ts_2 . "1";
		$id_smt_2 = $ts_2 . "2";
		$id_smt_3 = $ts_1 . "1";
		$id_smt_4 = $ts_1 . "2";
		$id_smt_5 = $ts . "1";
		$id_smt_6 = $ts . "2";

		$table = "<table class='table table-bordered table-sm'>";

		$table .= "<tbody>";
		$table .= "<tr>
						<td rowspan='2'>No</td>
						<td rowspan='2'>TAHUN</td>
						<td rowspan='2' class='text-center'>Semester</td>
						<td colspan='2' class='text-center'>MK Prodi</td>
						<td colspan='2' class='text-center'>MK Luar Prodi</td>
						<td rowspan='2' class='text-center'>Action</td>
					</tr>
					<tr>		
						<td class='text-center'>Jml MK</td>
						<td class='text-center'>Jml SKS</td>
						<td class='text-center'>Jml MK</td>
						<td class='text-center'>Jml SKS</td>
					</tr>";
		$table .= "<tr>
					<td rowspan='2'>1.</td>
					<td rowspan='2'>TS-2</td>
					<td>$id_smt_1</td>
					<td  class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_1)->num_rows() . "</td>
					<td  class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_1)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_1)->num_rows() . "</td>
					<td  class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_1)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button_12345($id_smt_1) . "</td>
				  </tr>";
		$table .= "<tr>
					<td>$id_smt_2</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_2)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_2)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button_12345($id_smt_2) . "</td>

				  </tr>";
		$table .= "<tr>
					<td rowspan='2'>2.</td>
					<td rowspan='2'>TS-1</td>
					<td>$id_smt_3</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_3)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_3)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button_12345($id_smt_3) . "</td>

				  </tr>";
		$table .= "<tr>
					<td>$id_smt_4</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_4)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_4)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button_12345($id_smt_4) . "</td>

				  </tr>";
		$table .= "<tr>
					<td rowspan='2'>3.</td>
					<td rowspan='2'>TS</td>
					<td>$id_smt_5</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_5)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_5)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button_12345($id_smt_5) . "</td>

				  </tr>";
		$table .= "<tr>
					<td>$id_smt_6</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_6)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_6)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button_12345($id_smt_6) . "</td>

				  </tr>";
		$table .= "</tbody>";
		$table .= "</table>";

		return $table;
	}

	private function get_makul_by_ts_prodi($id_semester)
	{
		$q = $this->db->where(['j.id_smt' => $id_semester, 'id_sdm' => $this->session->userdata('id_ptk'), 'jd.id_sms' => $this->session->userdata('prodi')])
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->get();
		return $q;
	}

	private function get_sks_makul_by_ts_prodi($id_semester)
	{
		$q = $this->db->where(['j.id_smt' => $id_semester, 'id_sdm' => $this->session->userdata('id_ptk'), 'jd.id_sms' => $this->session->userdata('prodi')])
			->select('sum(mk.sks_mk) as sks_mk')
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->get();
		return $q;
	}

	private function get_makul_by_ts_non_prodi($id_semester)
	{
		$q = $this->db->where(['j.id_smt' => $id_semester, 'id_sdm' => $this->session->userdata('id_ptk'), 'jd.id_sms <>' => $this->session->userdata('prodi')])
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->get();
		return $q;
	}

	private function get_sks_makul_by_ts_non_prodi($id_semester)
	{
		$q = $this->db->where(['j.id_smt' => $id_semester, 'id_sdm' => $this->session->userdata('id_ptk'), 'jd.id_sms <>' => $this->session->userdata('prodi')])
			->select('sum(mk.sks_mk) as sks_mk')
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->get();
		return $q;
	}

	public function cetak_sk_mengajar()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$data_dosen = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row();
		$status_dosen = $this->db->where('id_ikatan_kerja', $data_dosen->id_ikatan_kerja)->get('ikatan_kerja_dosen')->row();
		$jabatan_akademik = $this->db->where('id_ptk', $id_ptk)->limit(1)->get('siakad_skripsi_dosen')->row();
		$output['nomor_sk'] = '-';
		$output['tanggal_sk'] = '-';

		$a = substr($id_smt, 4, 1);
		$b = substr($id_smt, 0, 4);
		$d = $b + 1;
		$c = $b . '/' . $d;

		if ($a == 1) {
			$semester = 'Semester Ganjil Tahun Akademik ' . $c;
		} else {
			$semester = 'Semester Genap Tahun Akademik ' . $c;
		}
		$i = 1;
		$data_ajar = $this->db->where(['id_sdm' => $id_ptk, 'j.id_smt' => $id_smt, 'id_kategori_jadwal' => 1])
			->select('j.*,kk.id_kls, kk.nm_kls, mk.kode_mk, mk.nm_mk, mk.sks_mk, s.nm_prodi_english, fmk.smt')
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('sms s', 's.id_sms = kk.id_sms')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('feeder_mata_kuliah_kurikulum fmk', 'fmk.id_mk = mk.id_mk')
			->order_by('fmk.smt')->get()->result();

		$tbl = "<table class='table table-borderless'>";
		$tbl .= "<tbody>";
		$tbl .= "<tr>
						<td style='width:150px'>Nama</td>
						<td style='width:10px'>:</td>
						<td>" . $data_dosen->nm_ptk . "</td>
					</tr>
					<tr>
						<td>NIDN</td>
						<td>:</td>
						<td>" . $data_dosen->nidn . "</td>
					</tr>
					<tr>
						<td>Jabatan Akademik</td>
						<td>:</td>
						<td>" . $jabatan_akademik->jabatan_akademik . "</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>:</td>
						<td>" . $status_dosen->nm_ikatan_kerja . "</td>
					</tr>";
		$tbl .= "</tbody>";
		$tbl .= "</table>";

		$tbl_ajar = "<table class='table table-borderless'>";
		$tbl_ajar .= "<thead>
							<tr>
								<th>NO</th>
								<th>KODE MK</th>
								<th>MATA KULIAH</th>
								<th>SKS</th>
								<th>SMT</th>
								<th>PRODI</th>
							</tr>
						</thead>";
		$tbl_ajar .= "<tbody>";
		foreach ($data_ajar as $key => $da) {
			$sks = $da->sks_mk * 1;
			$tbl_ajar .= "<tr>
								<td>$i</td>
								<td>$da->kode_mk</td>
								<td>$da->nm_mk</td>
								<td>$sks</td>
								<td>$da->smt</td>
								<td>$da->nm_prodi_english</td>
							</tr>";
			$i++;
		}

		$tbl_ajar .= "</tbody>";
		$tbl_ajar .= "</table>";


		$output['tahun_akademik'] = $semester;
		$output['tbl_1'] = $tbl;
		$output['tbl_ajar'] = $tbl_ajar;
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Legal']);

		// $this->load->view('cetak/pdf_sk_mengajar', $output);
		$html = $this->load->view('cetak/pdf_sk_mengajar', $output, true);
		$pdfFilePath  = 'sk_mengajar_tahun_ajaran.pdf';

		$mpdf->SetHTMLHeader($this->header());
		$mpdf->SetHTMLFooter($this->footer());


		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
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
		$h = '<div style="position: absolute; right:40; top: 20;">
				<img src="' . FCPATH  . 'assets/image_web/logo-white-3.png' . '" width="200px" />
			</div>';

		return $h;
	}
}
