<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_template_bap extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Template_bap_model', 'Template_bap_model');
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
		$output['judul_besar'] 		= 'Template BAP ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_template_bap';
		$output['parent_6']			= 'active open';
		$output['content'] 			= 'template_bap/template_bap_view';
		$output['after_page'] 		= 'template_bap/template_bap_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Template_bap_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_arsip_bap/cetak_pdf_berita_acara') . '/' . $n->id_kls . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Download BAP</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->nm_kls;
			$row[] = $n->nm_mk;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Template_bap_model->count_all(),
			"recordsFiltered" => $this->Template_bap_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Rencana_pertemuan_model->get_by_id($id);
		$data->tanggal_rencana = ($data->tanggal_rencana == '0000-00-00') ? '' : $data->tanggal_rencana;

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

		$insert = $this->Rencana_pertemuan_model->save($data);
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

		$this->Rencana_pertemuan_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$rencana_pertemuan = $this->db->where('uniq', $id)->get('siakad_rencana_pertemuan');

		if ($rencana_pertemuan->num_rows() > 0) {
			if ($rencana_pertemuan->row()->id_kls_paralel == '') {
				$this->db->trans_begin();
				$this->Rencana_pertemuan_model->delete_by_id($id);

				if ($this->db->where('id_rencana_pertemuan', $id)->get('siakad_absen_master')->num_rows()) {
					$id_absen_master = $this->db->where('id_rencana_pertemuan', $id)->get('siakad_absen_master')->row()->uniq;
					$this->db->where('uniq', $id_absen_master)->delete('siakad_absen_master');
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					echo json_encode(["status" => FALSE]);
				} else {
					$this->db->trans_commit();
					echo json_encode(["status" => TRUE]);
				}
			} else {
				$id_kls_paralel = $rencana_pertemuan->row()->id_kls_paralel;
				$get_kls_paralel = $this->db->where('id_kls_paralel', $id_kls_paralel)->get('siakad_rencana_pertemuan')->result();

				$this->db->trans_begin();

				foreach ($get_kls_paralel as $key => $value) {
					$id_absen_master = $this->db->where('id_rencana_pertemuan', $value->uniq)->get('siakad_absen_master');
					$this->db->where('uniq', $value->uniq)->delete('siakad_rencana_pertemuan');

					if ($id_absen_master->num_rows() > 0) {
						$this->db->where('uniq', $id_absen_master->row()->uniq)->delete('siakad_absen_master');
					}
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					echo json_encode(["status" => FALSE]);
				} else {
					$this->db->trans_commit();
					echo json_encode(["status" => TRUE]);
				}
			}
		}
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

		if ($this->input->post('tanggal_rencana') == '') {
			$data['inputerror'][] = 'tanggal_rencana';
			$data['error_string'][] = 'Tanggal Rencana harus diisi.';
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

		if (!strlen(trim($this->input->post('berita_acara_rencana')))) {
			$data['inputerror'][] = 'berita_acara_rencana';
			$data['error_string'][] = 'Harus diisi.';
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
