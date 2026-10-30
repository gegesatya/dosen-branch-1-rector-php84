<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_jadwal extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Jadwal_model', 'Jadwal_model');
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
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$siakad_krs = $this->db->where(['id_dosen_siakad' => $id_ptk, 'id_status_krs' => 2, 'id_smt' => $id_smt])->get('siakad_krs');
		if ($siakad_krs->num_rows() > 0) {
			$this->session->set_flashdata('msg_krs', 'Periksa kembali menu KRS, ada mahasiswa membutuhkan persetujuan KRS.');
			$this->session->set_flashdata('jumlah', $siakad_krs->num_rows());
		}

		$output['judul_besar'] = 'Jadwal Kuliah ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_jadwal';
		$output['parent_2'] = 'active open';
		$output['content'] = 'jadwal/jadwal_view';
		$output['after_page'] = 'jadwal/jadwal_after_page';
		$output['dd_rps'] 	= $this->Jadwal_model->f_dropdown_rps();

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Jadwal_model->get_datatables();
		$id_smt = $this->session->userdata('tahun_ajaran');
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$jam = substr($rg->jam_mulai, 0, 5) . ' sd ' . substr($rg->jam_selesai, 0, 5);
			$skema = $html_publish = $html_nilai = $html_download_nilai = $dinilai = '';

			$detail = $this->db->where('id_jadwal', $rg->id)
				->select('kk.nm_kls, mk.nm_mk,  mk.kode_mk, mk.id_mk, mk.sks_mk')
				->from('siakad_jadwal_detail jd')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->order_by('nm_kls')
				->get();

			$jumlah_siswa = $this->db->where(['id_jadwal' => $rg->id])->get('siakad_nilai_baru')->num_rows();
			$rencana = $this->db->where(['id_jadwal' => $rg->id])->get('siakad_rencana_pertemuan')->num_rows();
			$realisasi = $this->db->where(['id_jadwal' => $rg->id, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();


			if ($detail->num_rows() == 0) {
				$nmkls = $kk = $kuri = '-';
			} else {
				$nmkls = $kk = $kuri = '';
				foreach ($detail->result() as $key => $value) {
					$kk .= '[' . $value->kode_mk . '] ' . $value->nm_mk . ' (' . ($value->sks_mk * 1) . ' SKS)<br>';
					$nmkls .=  $value->nm_kls . '<br>';
				}
			}

			$publish = $rg->is_lapor_nilai == 1 ? '<span class="text-success">Sudah Publish</span>' : '<span class="text-danger">Belum Publish</span>';
			$sudah_nilai = $this->db->where(['id_jadwal' => $rg->id, 'nilai_huruf !=' => NULL])->get('siakad_nilai_baru')->num_rows();
			$belum_nilai = $jumlah_siswa - $sudah_nilai;

			if ($rg->is_skema == 1) {
				$skema = '<small>Skema Nilai :<p><b>Sikap = </b>' . $rg->sikap .
					' %, <b>Quiz =</b> ' . $rg->quiz . '%, <b>Tugas =</b> ' . $rg->tugas_terstruktur .
					' %, <b>UTS = </b>' . $rg->uts . ' %, <b>UAS = </b>' . $rg->uas . ' %</p>
					<p><b>Unjuk Kinerja = </b>' . $rg->unjuk_kinerja . ' %, <b>Proyek = </b>' . $rg->proyek .
					' %, <b>Portofolio = </b>' . $rg->portofolio . ' %, <b>Produk = </b>' . $rg->produk . ' %, <b>Presentasi = </b>' . $rg->presentasi . '%</p>
					</small>';
				$html_nilai = '<a href="' . site_url('C_isi_nilai_excel/index/' . $rg->id) . '" 
							class="btn btn-xs btn-default green-stripe btn-block margin-top-10">
							Isi Nilai</a>';

				$html_publish = '<a href="' . site_url('C_lapor_nilai') . '" 
							class="btn btn-xs btn-default blue-stripe btn-block margin-top-10">
							Publish</a>';

				$html_download_nilai = '<a href="' . site_url('C_summary_nilai/cetak_pdf_nilai/' . $rg->id) . '" target="new"
							class="btn btn-xs btn-default blue-stripe margin-top-10">
							PDF Nilai</a>';
				$dinilai = "<b>Sudah Dinilai : " . $sudah_nilai . ", Belum Dinilai : " . $belum_nilai . '</b>';
				// $html_download_nilai ='<a href=" site_url() . "C_summary_nilai/cetak_pdf_nilai/" . $value->id . "/" . $id_smt . "' target='new' class='btn btn-sm btn-default green-stripe'><i class='fa fa-download'></i></a>';
			}

			$jadwal_detail = $this->db->where(['id_jadwal' => $rg->id, 'a.id_kls' => $rg->id_kls])
				->from('siakad_jadwal_detail a')
				->join('feeder_kelas_kuliah b', 'a.id_kls = b.id_kls')
				->join('feeder_mata_kuliah c', 'c.id_mk = b.id_mk')
				->get();

			$data_rps = '';

			// if ($id_smt <= 20241) {
			if ($jadwal_detail->num_rows() > 0) {
				$details = $jadwal_detail->result();
				foreach ($details as $key => $value) {
					if (!empty($value->id_mk)) {
						$data_rps = $this->db->where(['id_mk' => $value->id_mk, 'id_jadwal' => $rg->id])->get('siakad_rps_mk_prodi');
					}
				}
			}
			// } else {
			// 	if ($jadwal_detail->num_rows() > 0) {
			// 		$details = $jadwal_detail->result();
			// 		foreach ($details as $key => $value) {
			// 			if (!empty($value->id_mk)) {
			// 				$data_rps = $this->db->where(['id_mk' => $value->id_mk, 'id_jadwal' => $rg->id])->get('siakad_rps_mk_prodi');
			// 			}
			// 		}
			// 	}
			// }


			// if($data_rps<> ''){
			// 	if ($data_rps->num_rows() > 0) {
			// 		$id_rps = $data_rps->row()->id_rps;
			// 		$rps = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online')->row();
			// 		$general_mk = 'RPS : '.$rps->general_mk;
			// 	}else{
			// 		$general_mk='';
			// 	}
			// }



			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nama_hari . '<br><small>' . $jam . '<br>' . $rg->nama_ruang . '</small>';
			$row[] = $kk . '<br>' . $skema . '<br>' . $dinilai . '<br>' . $html_download_nilai;
			$row[] = "Jumlah Siswa : " . $jumlah_siswa . '<br>Realisasi : ' . $realisasi . '<br>Rencana : ' . $rencana .
				'<br>Status Publish : ' . $publish;

			$html_copy = '';
			$html_dialog_rps = '<a href="javascript:void(0)" 
							class="btn btn-xs btn-default green-stripe btn-block"
							onclick="ajax_pilih_rps(\'' . $rg->id . '\')">
							Pilih RPS</a>';

			$html_ganti_rps = '<a href="javascript:void(0)" 
							class="btn btn-xs btn-default red-stripe btn-block"
							onclick="delete_rps(\'' . $rg->id . '\')">
							Reset RPS</a>';

			$html_skema_nilai = '<a href="javascript:void(0)" 
							class="btn btn-xs btn-default green-stripe btn-block"
							onclick="edit_skema_nilai(\'' . $rg->id . '\')">
							Skema Nilai</a>';



			if ($data_rps <> '') {
				if ($data_rps->num_rows() > 0) {
					$html_preview = '<a href="' . site_url('C_daftar_rps_lama/preview_rps/' . md5($data_rps->row()->id_rps)) .
						'" class="btn btn-default btn-xs green-stripe btn-block margin-bottom-10" target="_new"  title="Preview RPS"> View </a>';
					$html_copy = '<a href="javascript:void(0)" 
							class="btn btn-xs btn-default green-stripe btn-block"
							onclick="ajax_copy(\'' . $data_rps->row()->id_rps . '\',\'' . $rg->id . '\')">
							Generate Meeting</a>';
				} else {
					$html_preview = '';
					$html_copy = '';
				}
				$row[] = $html_preview . $html_ganti_rps;
				// $row[] = $html_preview;
			} else {
				$row[] = $html_dialog_rps;
				// $row[] = 'Belum ada RPS';
			}

			$row[] = $html_skema_nilai . $html_nilai . $html_publish;

			if ($rg->is_generate_bap == 0) {
				$row[] = $html_copy;
			} else {
				$row[] = '<a href="' . site_url('C_rencana_pertemuan/index/' . $data_rps->row()->id_jadwal) . '" class="btn btn-default btn-xs green-stripe" title="Virtual Class"> Virtual Class</a>';;
			}


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Jadwal_model->count_all(),
			"recordsFiltered" => $this->Jadwal_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function get_copy_data($id_rps)
	{
		$data = $this->db->where('a.id_rps', $id_rps)
			->select('a.*, mk.nm_mk, mk.kode_mk, b.general_mk')
			->from('siakad_rps_mk_prodi a')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = a.id_mk')
			->join('siakad_rps_online b', 'b.id_rps = a.id_rps')
			->get()->row();
		$nm_ptk = $this->db->where('id_ptk', $data->id_ptk)->get('siakad_dosen')->row();
		$data->nm_ptk = $nm_ptk->nm_ptk;
		echo json_encode($data);
	}



	public function get_rps($id_jadwal)
	{
		$data = $this->db->where('id', $id_jadwal)
			->select('j.*, h.nama_hari')
			->from('siakad_jadwal j')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->get()->row();

		$data->jam = substr($data->jam_mulai, 0, 5) . ' sd ' . substr($data->jam_selesai, 0, 5);

		$detail = $this->db->where('id_jadwal', $id_jadwal)
			->select('kk.nm_kls, mk.nm_mk,  mk.kode_mk, mk.id_mk')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('nm_kls')
			->get();

		if ($detail->num_rows() == 0) {
			$nmkls = $kk = '-';
		} else {
			$nmkls = $kk = '';
			foreach ($detail->result() as $key => $value) {
				$kk .= '[' . $value->nm_kls . '] ' . $value->kode_mk . ' - ' . $value->nm_mk . '<br>';
				//$nmkls .=  $value->nm_kls . '<br>';
			}
		}

		$data->matkul = $kk;

		echo json_encode($data);
	}

	public function ajax_generate_bap()
	{
		//$this->_validate();
		$id_rps = $this->security->xss_clean($this->input->post('id_rps')); //sumber
		$id_jadwal = $this->security->xss_clean($this->input->post('id_jadwal')); //tujuan
		$start_date = strtotime($this->input->post('tanggal_mulai'));

		$rps = $this->db->where('o.id_rps', $id_rps)->from('siakad_rps_online o')
			->join('siakad_rps_online_detail d', 'o.id_rps = d.id_rps')->get()->result();
		$jadwal_master = $this->db->where('id', $id_jadwal)->get('siakad_jadwal');

		$data = [];

		$minggu_a = 'NIL';

		foreach ($rps as $key => $value) {

			/* $rp_exist = $this->db->where(['id_jadwal' => $id_jadwal_tujuan, 'pertemuan' => $value->pertemuan])
				->get('siakad_rencana_pertemuan'); */

			//if ($rp_exist->num_rows() == 0) {
			$id_jadwal 				= $id_jadwal;
			//$tanggal_rencana 		= $start_date;
			$pertemuan 				= $value->kolom_2;
			$minggu 				= $value->kolom_1;
			$jam_mulai 				= $jadwal_master->row()->jam_mulai;
			$jam_selesai 			= $jadwal_master->row()->jam_selesai;
			$jam_teori 				= $value->kolom_3;
			$jam_praktek 			= $value->kolom_4;
			$berita_acara_rencana	= $value->kolom_7;
			$bacaan_materi			= $value->kolom_5;
			$create_by 				= $this->ion_auth->user()->row()->id;
			$create_date 			= date('Y-m-d h:i:s');

			//}

			if ($minggu_a <> $minggu) {
				if ($minggu <> 1) {
					$start_date = strtotime("+7 day", $start_date);
				}
			}

			$tanggal_rencana 		= date('Y-m-d', $start_date);
			$data[] = compact(
				'id_jadwal',
				'tanggal_rencana',
				'pertemuan',
				'minggu',
				'jam_mulai',
				'jam_selesai',
				'jam_teori',
				'jam_praktek',
				'berita_acara_rencana',
				'bacaan_materi',
				'create_by',
				'create_date'
			);

			$minggu_a = $value->kolom_1;
		}

		if ($data) {
			$data = $this->security->xss_clean($data);
			$this->db->insert_batch('siakad_rencana_pertemuan', $data);
			$this->db->where(['id' => $id_jadwal])->update('siakad_jadwal', ['is_generate_bap' => 1]);
		}

		echo json_encode(["status" => TRUE]);
	}

	public function ajax_save_rps()
	{
		$this->_validate();
		$id_jadwal = $this->input->post('id_jadwal');
		$id_rps = $this->input->post('ddRps');

		$jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)
			->select('*')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'jd.id_kls = kk.id_kls')
			->get();

		$id_smt = $this->session->userdata('tahun_ajaran');
		$data = [];

		if ($jadwal_detail->num_rows() > 0) {
			foreach ($jadwal_detail->result() as $key => $value) {
				$data[] = [
					'id_rps' => $id_rps,
					'id_jadwal' => $id_jadwal,
					'id_mk' => $value->id_mk,
					'id_kls' => $value->id_kls,
					'id_smt' => $id_smt,
					'id_ptk' => $this->session->userdata('id_ptk')
				];
			}
		}

		$data = $this->security->xss_clean($data);
		$this->Jadwal_model->save_rps($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete_rps($id_jadwal)
	{
		$id_smt = $this->session->userdata('tahun_ajaran');
		$this->Jadwal_model->delete_rps($id_jadwal, $id_smt);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('ddRps') == 0) {
			$data['inputerror'][] = 'ddRps';
			$data['error_string'][] = 'RPS belum dipilih.';
			$data['status'] = FALSE;
		} else {
			$data['inputerror'][] = 'ddRps';
			$data['error_string'][] = '';
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
