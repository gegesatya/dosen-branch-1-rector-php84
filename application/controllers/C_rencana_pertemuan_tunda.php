<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_rencana_pertemuan_tunda extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rencana_pertemuan_tunda_model');
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
		$output['judul_besar'] 		= 'Berita Acara Perkuliahan ';
		$output['judul_kecil'] 		= 'Tahun Ajaran ' . $this->session->userdata('setting')->id_smt_tunda . ' (Tunda/Revisi)';
		$output['menu_aktif'] 		= 'm_rencana_pertemuan_tunda';
		$output['parent_tunda']			= 'active open';
		$output['content'] 			= 'rencana_pertemuan_tunda/rencana_pertemuan_tunda_view';
		$output['after_page'] 		= 'rencana_pertemuan_tunda/rencana_pertemuan_tunda_after_page';
		$output['dd_kelas_kuliah'] = $this->Rencana_pertemuan_tunda_model->f_dropdown();
		$this->load->view('master_page', $output);
	}

	public function save_id_kls_session($id_kls)
	{
		$this->session->set_userdata('id_kls_session', $id_kls);
	}

	public function ajax_list($id_kls = NULL)
	{
		$list = $this->Rencana_pertemuan_tunda_model->get_datatables($id_kls);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rencana) {
			if ($rencana->is_realisasi == 1) {
				$status_realisasi = 'Sudah';
				$tgl_realisasi = tgl_indo(date('Y-m-d', strtotime($rencana->tanggal_realisasi)));
			} else {
				$status_realisasi = 'Belum';
				$tgl_realisasi = '';
			}
			$no++;
			$row = [];
			$row[] = '<strong>W : </strong>' . $rencana->minggu . '<br><strong>M : </strong>' . $rencana->pertemuan;
			$row[] = tgl_indo($rencana->tanggal_rencana) . '<br><small>' . substr($rencana->jam_mulai, 0, 5) . ' sd ' . substr($rencana->jam_selesai, 0, 5) . '</small><br><strong>Bahasan : </strong>' . $this->getExcerpt($rencana->berita_acara_rencana, 0, 100) . '<br><strong>Referensi : </strong>' . $this->getExcerpt($rencana->bacaan_materi, 0, 100);
			$row[] = $rencana->materi_tersampaikan;
			$row[] = $status_realisasi;
			$row[] = $tgl_realisasi;

			$row[] = '<a class="btn btn-sm btn-primary btn-xs" 
						 href="javascript:void(0)" 
						 onclick="edit_rencana(' . "'" . $rencana->uniq . "'" . ')">
						 <i class="icon-pencil"></i>
					</a>';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Rencana_pertemuan_tunda_model->count_all($id_kls),
			"recordsFiltered" => $this->Rencana_pertemuan_tunda_model->count_filtered($id_kls),
			"data" => $data,
		];

		echo json_encode($output);
	}

	private function getExcerpt($str, $startPos = 0, $maxLength = 100)
	{
		if (strlen($str) > $maxLength) {
			$excerpt   = substr($str, $startPos, $maxLength - 3);
			$lastSpace = strrpos($excerpt, ' ');
			$excerpt   = substr($excerpt, 0, $lastSpace);
			$excerpt  .= '...';
		} else {
			$excerpt = $str;
		}

		return $excerpt;
	}

	public function get_copy_data()
	{
		$id_kls = $this->session->userdata('id_kls_session');
		$id_ptk = $this->session->userdata('id_ptk');

		$data = $this->db->where('id_ptk', $id_ptk)->get('siakad_rencana_pertemuan')->row();
		$data_kls = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();

		//if($jml_data->num_rows() >= 14){
		$id_mk = $data_kls->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data_kls->nm_mk = $nm_mk;
		echo json_encode($data_kls);
		//}else{
		//	echo json_encode(['status'=>FALSE, 'message'=>'Mohon lengkapi sumber data, jumlah rencana pertemuan harus 15 minggu']);
		//}
	}

	public function ajax_edit($id)
	{
		$data = $this->Rencana_pertemuan_tunda_model->get_by_id($id);
		$data->tanggal_rencana = ($data->tanggal_rencana == '0000-00-00') ? '' : $data->tanggal_rencana;
		$data->jam_mulai = substr($data->jam_mulai, 0, 5);
		$data->jam_selesai = substr($data->jam_selesai, 0, 5);

		$data->berita_acara_rencana = str_ireplace(["<br />"], "\r\n", $data->berita_acara_rencana);
		$data->bacaan_materi = str_ireplace(["<br />"], "\r\n", $data->bacaan_materi);

		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate('add');
		$id_jadwal			= $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan');
		$meeting = $rencana_pertemuan->num_rows() + 1;

		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));

		$tanggal_rencana 	= $tanggal;
		$pertemuan 			= $meeting;
		$jam_mulai 			= $this->input->post('jam_mulai');
		$jam_selesai 		= $this->input->post('jam_selesai');
		$jam_teori 			= $this->input->post('jam_teori');
		$jam_praktek 		= $this->input->post('jam_praktek');
		$berita_acara_rencana =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('berita_acara_rencana'));
		$bacaan_materi =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('bacaan_materi'));

		$create_by 			= $this->ion_auth->user()->row()->id;
		$create_date 		= date('Y-m-d h:i:s');

		$data = compact(
			'id_jadwal',
			'tanggal_rencana',
			'pertemuan',
			'jam_mulai',
			'jam_selesai',
			'jam_teori',
			'jam_praktek',
			'berita_acara_rencana',
			'bacaan_materi',
			'create_by',
			'create_date'
		);

		$insert = $this->Rencana_pertemuan_tunda_model->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate('update');
		$rencana_pertemuan = $this->db->where('uniq', $this->input->post('uniq'))->get('siakad_rencana_pertemuan');
		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));

		//$id_kls 			= $this->session->userdata('id_kls_session');
		$tanggal_rencana 	= $tanggal;
		$pertemuan 			= $this->input->post('pertemuan');
		$jam_mulai 			= $this->input->post('jam_mulai');
		$jam_selesai 		= $this->input->post('jam_selesai');
		$jam_teori 			= $this->input->post('jam_teori');
		$jam_praktek 		= $this->input->post('jam_praktek');
		$berita_acara_rencana =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('berita_acara_rencana'));
		$bacaan_materi		= str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('bacaan_materi'));
		$checked 			= $this->input->post('is_realisasi');
		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d h:i:s');

		if (isset($checked)) {
			$is_realisasi = 1;
			$tanggal_realisasi = $this->input->post('tanggal_realisasi');
		} else {
			$is_realisasi = 0;
			$tanggal_realisasi = NULL;
		}

		$data = compact(
			'tanggal_rencana',
			'pertemuan',
			'jam_mulai',
			'jam_selesai',
			'jam_teori',
			'jam_praktek',
			'is_realisasi',
			'tanggal_realisasi',
			'berita_acara_rencana',
			'bacaan_materi',
			'change_by',
			'change_date'
		);

		$this->Rencana_pertemuan_tunda_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$rencana_pertemuan = $this->db->where('uniq', $id)->get('siakad_rencana_pertemuan')->row();

		if ($rencana_pertemuan->is_realisasi == 0) {
			$this->Rencana_pertemuan_tunda_model->delete_by_id($id);
			echo json_encode(["status" => TRUE]);
		} else {
			echo json_encode(["status" => FALSE, "message" => "Tidak bisa dihapus karena sudah realisasi."]);
		}
	}

	public function ajax_delete_all()
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$rp_exist = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan')->result();
		$this->Rencana_pertemuan_tunda_model->delete_all($id_jadwal);
		echo json_encode(["status" => TRUE]);
	}

	public function get_kelas_kuliah()
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$data_kls = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();
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

		if ($this->input->post('tanggal_rencana') == '') {
			$data['inputerror'][] = 'tanggal_rencana';
			$data['error_string'][] = 'Tanggal Rencana harus diisi.';
			$data['status'] = FALSE;
		}

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

		if (!strlen(trim($this->input->post('berita_acara_rencana')))) {
			$data['inputerror'][] = 'berita_acara_rencana';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		$dateObj1 = DateTime::createFromFormat('d.m.Y H:i', "10.10.2010 " . $this->input->post('jam_mulai'));
		$dateObj2 = DateTime::createFromFormat('d.m.Y H:i', "10.10.2010 " . $this->input->post('jam_selesai'));
		if ($dateObj1 == false) {
			$data['inputerror'][] = 'jam_mulai';
			$data['error_string'][] = 'Format Jam Salah.';
			$data['status'] = FALSE;
		}

		if ($dateObj2 == false) {
			$data['inputerror'][] = 'jam_selesai';
			$data['error_string'][] = 'Format Jam Salah.';
			$data['status'] = FALSE;
		}

		if ($tipe == 'insert') {
			//cari pertemuan double..
			$tanggal_rencana = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));
			$rencana_pertemuan = $this->db
				->where([
					'tanggal_rencana' => $tanggal_rencana,
					'jam_mulai' => $this->input->post('jam_mulai'),
					'id_kls' => $this->session->userdata('id_kls_session'),
				])
				->get('siakad_rencana_pertemuan');
			if ($rencana_pertemuan->num_rows() > 0) {
				$data['inputerror'][] = 'tanggal_rencana';
				$data['error_string'][] = 'Sudah ada Rencana Perkuliahan dengan kategori yang sama pada tanggal dan waktu yang diinput.';
				$data['status'] = FALSE;
			}
		}


		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
