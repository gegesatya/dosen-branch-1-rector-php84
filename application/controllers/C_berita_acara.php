<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_berita_acara extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Berita_acara_model', 'Berita_acara_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Rencana Pertemuan';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_berita_acara';
		$output['parent_2']			= 'active open';
		$output['content'] 			= 'berita_acara/berita_acara_view';
		$output['after_page'] 		= 'berita_acara/berita_acara_after_page';
		$output['dd_kelas_kuliah'] = $this->Berita_acara_model->f_dropdown('feeder_kelas_kuliah', 'id_kls', 'nm_kls');

		$this->load->view('master_page', $output);
	}

	public function save_id_kls_session($id_kls)
	{
		$this->session->set_userdata('id_kls_session', $id_kls);
	}

	public function ajax_list($id_kls = NULL)
	{
		$list = $this->Berita_acara_model->get_datatables($id_kls);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rencana) {
			$no++;
			$row = [];
			$row[] = $rencana->minggu_ke;
			$row[] = date('d M Y', strtotime($rencana->tanggal_berita_acara));
			$row[] = substr($rencana->jam_mulai, 0, 5) . ' sd ' . substr($rencana->jam_selesai, 0, 5);
			$row[] = $rencana->isi_berita_acara;
			$row[] = $rencana->bacaan_materi;

			$row[] = '<a class="btn btn-sm btn-primary btn-xs" 
						 href="javascript:void(0)" 
						 onclick="edit_rencana(' . "'" . $rencana->uniq . "'" . ')">
						 <i class="icon-pencil"></i>
					</a>
					<a class="btn btn-sm btn-danger btn-xs" 
						 href="javascript:void(0)" 
						 onclick="delete_rencana(' . "'" . $rencana->uniq . "'" . ')">
						 <i class="icon-trash"></i>
					</a>';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Berita_acara_model->count_all($id_kls),
			"recordsFiltered" => $this->Berita_acara_model->count_filtered($id_kls),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Berita_acara_model->get_by_id($id);
		$data->tanggal_berita_acara = ($data->tanggal_berita_acara == '0000-00-00') ? '' : $data->tanggal_berita_acara;
		$id_kls = $data->id_kls;
		$kelas = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();

		$data->nm_kls = $kelas->nm_kls;
		$id_mk = $kelas->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data->nm_mk = $nm_mk;

		$data->isi_berita_acara = str_ireplace(["<br />"], "\r\n", $data->isi_berita_acara);
		$data->bacaan_materi = str_ireplace(["<br />"], "\r\n", $data->bacaan_materi);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate('add');
		$id_kls 			= $this->session->userdata('id_kls_session');
		$berita_acara = $this->db->where('id_kls', $id_kls)->get('siakad_absen_master');
		$meeting = $berita_acara->num_rows() + 1;

		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_berita_acara')));

		$tanggal_berita_acara 	= $tanggal;
		$minggu_ke 			= $meeting;
		$jam_mulai 			= $this->input->post('jam_mulai');
		$jam_selesai 		= $this->input->post('jam_selesai');
		$jam_teori 			= $this->input->post('jam_teori');
		$jam_praktek 		= $this->input->post('jam_praktek');
		$isi_berita_acara =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('isi_berita_acara'));
		$bacaan_materi =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('bacaan_materi'));
		//$jumlah_hadir 		= $this->input->post('jumlah_hadir');
		//$jumlah_tidak_hadir = $this->input->post('jumlah_tidak_hadir');
		//$kemampuan_akhir=  str_ireplace(array("\r\n", "\n", "\r"), '<br />', $this->input->post('kemampuan_akhir'));
		//$indikator_capaian=  str_ireplace(array("\r\n", "\n", "\r"), '<br />', $this->input->post('indikator_capaian'));
		//$metode_pembelajaran=  str_ireplace(array("\r\n", "\n", "\r"), '<br />', $this->input->post('metode_pembelajaran'));

		$create_by 			= $this->ion_auth->user()->row()->id;
		$create_date 		= date('Y-m-d h:i:s');

		$data = compact(
			'id_kls',
			'tanggal_berita_acara',
			'minggu_ke',
			'jam_mulai',
			'jam_selesai',
			'jumlah_hadir',
			'jumlah_tidak_hadir',
			'jam_teori',
			'jam_praktek',
			'isi_berita_acara',
			'bacaan_materi',
			'create_by',
			'create_date'
		);

		$data = $this->security->xss_clean($data);

		$insert = $this->Berita_acara_model->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate('update');
		//$rencana_pertemuan = $this->db->where('uniq', $this->input->post('uniq'))->get('siakad_rencana_pertemuan');
		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_berita_acara')));

		$id_kls 			= $this->session->userdata('id_kls_session');
		$tanggal_berita_acara 	= $tanggal;
		$pertemuan 			= $this->input->post('minggu_ke');
		$jam_mulai 			= $this->input->post('jam_mulai');
		$jam_selesai 		= $this->input->post('jam_selesai');
		$jam_teori 			= $this->input->post('jam_teori');
		$jam_praktek 		= $this->input->post('jam_praktek');
		//$jam_teori 			= $this->input->post('jam_teori');
		//$jam_praktek 		= $this->input->post('jam_praktek');
		$isi_berita_acara =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('isi_berita_acara'));
		$bacaan_materi =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('bacaan_materi'));
		//$kemampuan_akhir=  str_ireplace(array("\r\n", "\n", "\r"), '<br />', $this->input->post('kemampuan_akhir'));
		//$indikator_capaian=  str_ireplace(array("\r\n", "\n", "\r"), '<br />', $this->input->post('indikator_capaian'));
		//$metode_pembelajaran=  str_ireplace(array("\r\n", "\n", "\r"), '<br />', $this->input->post('metode_pembelajaran'));

		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d h:i:s');

		$data = compact(
			'id_kls',
			'tanggal_berita_acara',
			'minggu_ke',
			'jam_mulai',
			'jam_selesai',
			'jam_teori',
			'jam_praktek',
			'isi_berita_acara',
			'bacaan_materi',
			'change_by',
			'change_date'
		);

		$data = $this->security->xss_clean($data);
		$this->Berita_acara_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$this->Berita_acara_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function get_kelas_kuliah()
	{
		$id_kls = $this->session->userdata('id_kls_session');
		$data_kls = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();

		$nm_mk = $this->db->where('id_mk', $data_kls->id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data_kls->nm_mk = $nm_mk;
		$data_kls->jam_mulai = date('H:i', strtotime($data_kls->jam_mulai));
		$data_kls->jam_selesai = date('H:i', strtotime($data_kls->jam_selesai));
		echo json_encode($data_kls);
	}

	private function _validate($tipe)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		/* if($this->input->post('id_kls') == '')
		{
			$data['inputerror'][] = 'id_kls';
			$data['error_string'][] = 'Belum memilih kelas.';
			$data['status'] = FALSE;
		} */

		if ($this->input->post('tanggal_berita_acara') == '') {
			$data['inputerror'][] = 'tanggal_berita_acara';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		/* if($this->input->post('pertemuan') == '')
		{
			$data['inputerror'][] = 'pertemuan';
			$data['error_string'][] = 'Pertemuan harus diisi.';
			$data['status'] = FALSE;
		} */

		if ($this->input->post('jam_mulai') == '') {
			$data['inputerror'][] = 'jam_mulai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_selesai') == '') {
			$data['inputerror'][] = 'jam_selesai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_teori') == '') {
			$data['inputerror'][] = 'jam_teori';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_praktek') == '') {
			$data['inputerror'][] = 'jam_praktek';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if (!strlen(trim($this->input->post('isi_berita_acara')))) {
			$data['inputerror'][] = 'isi_berita_acara';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($tipe == 'update') {
			if ($this->input->post('minggu_ke') == '') {
				$data['inputerror'][] = 'minggu_ke';
				$data['error_string'][] = 'Harus diisi.';
				$data['status'] = FALSE;
			}
		}

		/* if($tipe == 'insert'){
			//cari pertemuan double..
			$tanggal_rencana = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));
			$rencana_pertemuan = $this->db
									  ->where(array(
									  		'tanggal_rencana'=>$tanggal_rencana,
									  		'jam_mulai'=>$this->input->post('jam_mulai'),
									  		'id_kls'=>$this->session->userdata('id_kls_session'),
									  	))
									  ->get('siakad_rencana_pertemuan');
			if($rencana_pertemuan->num_rows() > 0){
				$data['inputerror'][] = 'tanggal_rencana';
				$data['error_string'][] = 'Sudah ada Rencana Perkuliahan dengan kategori yang sama pada tanggal dan waktu yang diinput.';
				$data['status'] = FALSE;
			}
		} */


		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	/*copy berita acara dari rencana perkuliahan*/
	public function get_copy_data()
	{
		$id_kls = $this->session->userdata('id_kls_session');
		$jml_data = $this->db->where('id_kls', $id_kls)->get('siakad_rencana_pertemuan');
		$data = $this->db->where('id_kls', $id_kls)->get('siakad_absen_master')->row();
		$data_kls = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();

		if ($jml_data->num_rows() >= 14) {
			$id_mk = $data_kls->id_mk;
			$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
			$data_kls->nm_mk = $nm_mk;
			echo json_encode($data_kls);
		} else {
			echo json_encode(['status' => FALSE, 'message' => 'Mohon lengkapi sumber data, jumlah berita acara harus 15 minggu']);
		}
	}

	public function ajax_pertemuan_copy_data()
	{
		$id_kls = $this->security->xss_clean($this->input->post('id_kls')); //sumber
		$kelas_kuliah_sumber = $this->db->where(['id_kls' => $id_kls])
			->get('feeder_kelas_kuliah')->row();

		$rencana_pertemuan = $this->db->where(['id_kls' => $id_kls])
			->get('siakad_rencana_pertemuan')->result();
		$cek_berita_acara_existing = $this->db->where(['id_kls' => $id_kls])->get('siakad_absen_master')->num_rows();


		$data = [];
		foreach ($rencana_pertemuan as $key => $value) {
			$id_kls 				= $id_kls;
			$tanggal_berita_acara 	= $value->tanggal_rencana;
			$minggu_ke 				= $value->pertemuan;
			$jam_mulai 				= $value->jam_mulai;
			$jam_selesai 			= $value->jam_selesai;
			$jam_teori 				= $value->jam_teori;
			$jam_praktek 			= $value->jam_praktek;
			$isi_berita_acara		= $value->berita_acara_rencana;
			$bacaan_materi			= $value->bacaan_materi;
			$create_by 				= $this->ion_auth->user()->row()->id;
			$create_date 			= date('Y-m-d h:i:s');
			$data[] = compact(
				'id_kls',
				'tanggal_berita_acara',
				'minggu_ke',
				'jam_mulai',
				'jam_selesai',
				'jam_teori',
				'jam_praktek',
				'isi_berita_acara',
				'bacaan_materi',
				'kemampuan_akhir',
				'indikator_capaian',
				'metode_pembelajaran',
				'create_by',
				'create_date'
			);
		}

		$this->db->trans_begin();

		if ($cek_berita_acara_existing > 0) {
			$this->db->where('id_kls', $id_kls)->delete('siakad_absen_master');
		}

		$this->db->insert_batch('siakad_absen_master', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(["status" => FALSE]);
		} else {
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}
}
