<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_history_ta extends CI_Controller
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

		$output['judul_besar'] = 'History Bimbingan dan Menguji Tugas akhir ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_history_ta';
		$output['parent_history'] = 'active open';
		$output['content'] = 'history_ta/history_ta_view';
		$output['after_page'] = 'history_ta/history_ta_after_page';
		$output['tugas_akhir'] = $this->tugas_akhir();

		$this->load->view('master_page', $output);
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
	private function button2($id_smt)
	{
		$html_download = '<a href="javascript:void(0)" 
									class="btn btn-xs btn-default green-stripe"
									onclick="ajax_berita_acara(\'' . $id_smt . '\')">
								View</a>';

		return $html_download;
	}


	public function tugas_akhir()
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
						<td rowspan='2' class='text-center'>JML Bimbingan</td>
						<td colspan='3' class='text-center'>Jumlah Menguji</td>
					</tr>
					<tr>
						<td class='text-center'>SEMPRO</td>
						<td class='text-center'>SEMHAS</td>
						<td class='text-center'>SIDANG</td>
					</tr>";
		$table .= "<tr>
					<td rowspan='2'>1.</td>
					<td rowspan='2'>TS-2</td>
					<td>$id_smt_1</td>
					<td  class='text-center'>" . $this->get_jumlah_bimbingan($id_smt_1)->num_rows() . "</td>
					<td  class='text-center'>" . $this->get_jumlah_sempro($id_smt_1)->num_rows() . "</td>
					<td  class='text-center'>" . $this->get_jumlah_semhas($id_smt_1)->num_rows() . "</td>
					<td  class='text-center'>" . $this->get_jumlah_sidang($id_smt_1)->num_rows() . "</td>
					
				  </tr>";
		$table .= "<tr>
					<td>$id_smt_2</td>
					<td class='text-center'>" . $this->get_jumlah_bimbingan($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sempro($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_semhas($id_smt_2)->num_rows() . "</td>
					<td class='text-center'>" .  $this->get_jumlah_sidang($id_smt_2)->num_rows() . "</td>
					
				  </tr>";
		$table .= "<tr>
					<td rowspan='2'>2.</td>
					<td rowspan='2'>TS-1</td>
					<td>$id_smt_3</td>
					<td class='text-center'>" . $this->get_jumlah_bimbingan($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sempro($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_semhas($id_smt_3)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sidang($id_smt_3)->num_rows() . "</td>
					
				  </tr>";
		$table .= "<tr>
					<td>$id_smt_4</td>
					<td class='text-center'>" . $this->get_jumlah_bimbingan($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sempro($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_semhas($id_smt_4)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sidang($id_smt_4)->num_rows() . "</td>
					
				  </tr>";
		$table .= "<tr>
					<td rowspan='2'>3.</td>
					<td rowspan='2'>TS</td>
					<td>$id_smt_5</td>
					<td class='text-center'>" . $this->get_jumlah_bimbingan($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sempro($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_semhas($id_smt_5)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sidang($id_smt_5)->num_rows() . "</td>
					
				  </tr>";
		$table .= "<tr>
					<td>$id_smt_6</td>
					<td class='text-center'>" . $this->get_jumlah_bimbingan($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sempro($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_semhas($id_smt_6)->num_rows() . "</td>
					<td class='text-center'>" . $this->get_jumlah_sidang($id_smt_6)->num_rows() . "</td>
					
				  </tr>";
		$table .= "</tbody>";
		$table .= "</table>";

		return $table;
	}

	private function get_jumlah_bimbingan($id_semester)
	{
		$id_ptk_feeder = $this->db->where('id_ptk', $this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->id_ptk;
		$q = $this->db->where(['id_smt' => $id_semester, 'dosen_pembimbing_1' => $id_ptk_feeder])
			->from('siakad_skripsi_pengajuan_judul')
			->get();
		return $q;
	}

	private function get_jumlah_sempro($id_semester)
	{
		$id_ptk_feeder = $this->db->where('id_ptk', $this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->id_ptk;

		$q = $this->db->where(['id_smt' => $id_semester, 'id_penilai' => $id_ptk_feeder, 'nilai_akhir <>' => NULL])
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_pengajuan_judul j', 'j.id_judul_skripsi = n.id_judul_skripsi')
			->get();
		return $q;
	}

	private function get_jumlah_semhas($id_semester)
	{
		$id_ptk_feeder = $this->db->where('id_ptk', $this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->id_ptk;

		$q = $this->db->where(['id_smt' => $id_semester, 'id_penilai' => $id_ptk_feeder, 'semhas_nilai_akhir <>' => NULL])
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_pengajuan_judul j', 'j.id_judul_skripsi = n.id_judul_skripsi')
			->get();
		return $q;
	}

	private function get_jumlah_sidang($id_semester)
	{
		$id_ptk_feeder = $this->db->where('id_ptk', $this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->id_ptk;

		$q = $this->db->where(['id_smt' => $id_semester, 'id_penilai' => $id_ptk_feeder, 'sidang_nilai_akhir <>' => NULL])
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_pengajuan_judul j', 'j.id_judul_skripsi = n.id_judul_skripsi')
			->get();
		return $q;
	}
}
