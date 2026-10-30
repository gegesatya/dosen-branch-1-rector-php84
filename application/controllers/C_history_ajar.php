<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_history_ajar extends CI_Controller
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
		$id_ptk = $this->session->userdata('id_ptk');
		$data = $this->db->group_start()
				->where('j.id_sdm', $id_ptk)
				->or_where('j.id_sdm_2', $id_ptk)
				->or_where('j.id_sdm_3', $id_ptk)
			->group_end()
			->where('j.id_smt', $id_smt)
			->where_in('j.id_kategori_jadwal', [1, 5, 6, 7, 8])
			->select('j.*, h.nama_hari, s.nm_prodi_english, sjd.sks_mk')
			->from('siakad_jadwal j')
			->join('siakad_hari h', 'j.id_hari = h.uniq')
			->join('sms s', 'j.id_sms = s.id_sms', 'left')
			->join('siakad_jadwal_detail sjd', 'j.id = sjd.id_jadwal', 'left')
			->group_by('j.id')
			->get()->result();

		$i = 1;
		$tabel = "<table class='table table-bordered'>";
		$tabel .= "<tbody>";
		$tabel .= "<tr>
						<td>No</td>
						<td>Mata Kuliah</td>
						<td>SKS</td>
						<td>Prodi</td>
						<td>BAP</td>
						<td>Nilai</td>
						<td>Kehadiran</td>
						<td>EPOM</td>
						<td>RPS</td>
					</tr>";
		foreach ($data as $key => $value) {
			if ($id_smt <= 20241) {
				$rps = $this->db->where('id_jadwal', $value->id)->get('siakad_rps_mk_prodi');
			} elseif ($id_smt >= 20242) {
				$rps = $this->db->where('id_jadwal', $value->id)->get('siakad_new_rps_mk_prodi');
			}


			if ($rps->num_rows()) {
				$id_rps = $rps->row(1)->id_rps;
				if ($id_smt <= 20241) {
					$html_rps = "<a href='" . site_url() . "C_daftar_rps_lama/preview_rps/" . md5($id_rps) . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>";
				} elseif ($id_smt >= 20242) {
					$html_rps = "<a href='" . site_url() . "C_rps_obsolete/preview_rps_baru/" . md5($id_rps) . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>";
				}
			} else {
				$html_rps = '-';
			}

			if ($id_smt <= 20232) {
				$tabel .= "<tr>
							<td>" . $i++ . "</td>
							<td>" . $value->tag_name . ' <br><small>' . $value->nama_hari . ', Jam : ' . substr($value->jam_mulai, 0, 5) . '</small>' . "</td>
							<td>" . $value->sks_mk . "</td>
							<td>" . $value->nm_prodi_english . "</td>
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
			} else {
				$tabel .= "<tr>
							<td>" . $i++ . "</td><td>" . $value->tag_name . ' <br><small>' . $value->nama_hari . ', Jam : ' . substr($value->jam_mulai, 0, 5) . '</small>' . "</td>
							<td>" . $value->sks_mk . "</td>
							<td>" . $value->nm_prodi_english . "</td>
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
		}
		$tabel .= "</tbody>";
		$tabel .=	"</table>";

		$arr = ['tabel' => $tabel];

		echo  json_encode($arr);
	}

	public function get_seminar_by_smt($id_smt)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$data = $this->db->group_start()
				->where('j.id_sdm', $id_ptk)
				->or_where('j.id_sdm_2', $id_ptk)
				->or_where('j.id_sdm_3', $id_ptk)
			->group_end()
			->where('j.id_smt', $id_smt)
			->where_in('j.id_kategori_jadwal', [1, 5, 6, 7, 8])
			->select('j.*, s.nm_prodi_english, sjd.sks_mk')
			->from('siakad_jadwal j')
			->join('sms s', 'j.id_sms = s.id_sms', 'left')
			->join('siakad_jadwal_detail sjd', 'j.id = sjd.id_jadwal', 'left')
			->group_by('j.id')
			->get()->result();

		$i = 1;
		$tabel = "<table class='table table-bordered'>";
		$tabel .= "<tbody>";
		$tabel .= "<tr><td>No</td><td>Mata Kuliah</td><td>SKS</td><td>Prodi</td><td>BAP</td><td>Nilai</td><td>Kehadiran</td><td>EPOM</td><td>RPS</td></tr>";
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
							<td>" . $value->sks_mk . "</td>
							<td>" . $value->nm_prodi_english . "</td>
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
					<td  class='text-center'>" . SELF::button($id_smt_1) . "</td>
				  </tr>";
		$table .= "<tr>
					<td>$id_smt_2</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_2)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_2)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button($id_smt_2) . "</td>

				  </tr>";
		$table .= "<tr>
					<td rowspan='2'>2.</td>
					<td rowspan='2'>TS-1</td>
					<td>$id_smt_3</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_3)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_3)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button($id_smt_3) . "</td>

				  </tr>";
		$table .= "<tr>
					<td>$id_smt_4</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_4)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_4)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button($id_smt_4) . "</td>

				  </tr>";
		$table .= "<tr>
					<td rowspan='2'>3.</td>
					<td rowspan='2'>TS</td>
					<td>$id_smt_5</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_5)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_5)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button($id_smt_5) . "</td>

				  </tr>";
		$table .= "<tr>
					<td>$id_smt_6</td>
					<td class='text-center'>" . $this->get_makul_by_ts_prodi($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_prodi($id_smt_6)->row()->sks_mk) * 1 . "</td>
					<td class='text-center'>" . $this->get_makul_by_ts_non_prodi($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . ($this->get_sks_makul_by_ts_non_prodi($id_smt_6)->row()->sks_mk) * 1 . "</td>
					<td  class='text-center'>" . SELF::button($id_smt_6) . "</td>

				  </tr>";
		$table .= "</tbody>";
		$table .= "</table>";

		return $table;
	}

	private function get_makul_by_ts_prodi($id_semester)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$q = $this->db->group_start()
				->where('j.id_sdm', $id_ptk)
				->or_where('j.id_sdm_2', $id_ptk)
				->or_where('j.id_sdm_3', $id_ptk)
			->group_end()
			->where(['j.id_smt' => $id_semester, 'jd.id_sms' => $this->session->userdata('prodi')])
			->where_in('j.id_kategori_jadwal', [1, 5, 6, 7, 8])
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->group_by('j.id')
			->get();
		return $q;
	}

	private function get_sks_makul_by_ts_prodi($id_semester)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$q = $this->db->group_start()
				->where('j.id_sdm', $id_ptk)
				->or_where('j.id_sdm_2', $id_ptk)
				->or_where('j.id_sdm_3', $id_ptk)
			->group_end()
			->where(['j.id_smt' => $id_semester, 'jd.id_sms' => $this->session->userdata('prodi')])
			->where_in('j.id_kategori_jadwal', [1, 5, 6, 7, 8])
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
		$id_ptk = $this->session->userdata('id_ptk');
		$q = $this->db->group_start()
				->where('j.id_sdm', $id_ptk)
				->or_where('j.id_sdm_2', $id_ptk)
				->or_where('j.id_sdm_3', $id_ptk)
			->group_end()
			->where(['j.id_smt' => $id_semester, 'jd.id_sms <>' => $this->session->userdata('prodi')])
			->where_in('j.id_kategori_jadwal', [1, 5, 6, 7, 8])
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->group_by('j.id')
			->get();
		return $q;
	}

	private function get_sks_makul_by_ts_non_prodi($id_semester)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$q = $this->db->group_start()
				->where('j.id_sdm', $id_ptk)
				->or_where('j.id_sdm_2', $id_ptk)
				->or_where('j.id_sdm_3', $id_ptk)
			->group_end()
			->where(['j.id_smt' => $id_semester, 'jd.id_sms <>' => $this->session->userdata('prodi')])
			->where_in('j.id_kategori_jadwal', [1, 5, 6, 7, 8])
			->select('sum(mk.sks_mk) as sks_mk')
			->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->get();
		return $q;
	}
}
