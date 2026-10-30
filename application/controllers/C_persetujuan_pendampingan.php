<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_persetujuan_pendampingan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		// Ensure user is in kaprodi group (5)
		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}

		// Verify against kaprodi table
		$id_ptk = $this->session->userdata('id_ptk');
		$kaprodi = $this->db->get_where('kaprodi', ['id_ptk' => $id_ptk])->row();
		if (!$kaprodi) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}

		// Load model
		$this->load->model('E_persetujuan_pendampingan_model');
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Persetujuan Pendampingan';
		$output['judul_kecil'] 		= 'eKinerja';
		$output['menu_aktif'] 		= 'm_persetujuan_pendampingan';
		$output['parent_kinerja']	= 'active open';
		$output['content'] 			= 'persetujuan_pendampingan/v_persetujuan_pendampingan';
		$output['after_page'] 		= 'persetujuan_pendampingan/v_persetujuan_pendampingan_after_page';
		
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$kaprodi = $this->db->get_where('kaprodi', ['id_ptk' => $id_ptk])->row();
		$id_sms = $kaprodi->id_sms;

		$list = $this->E_persetujuan_pendampingan_model->get_datatables($id_sms);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $row) {
			$no++;
			$nestedData = [];
			$nestedData[] = $no;
			$nestedData[] = $row->nm_ptk;
			$nestedData[] = $row->nama_semester;
			$nestedData[] = $row->kategori;
			$nestedData[] = $row->jumlah;
			
			// PDF file link
			if (!empty($row->pdf_surat_keterangan)) {
				$nestedData[] = '<a href="javascript:void(0)" onclick="show_pdf(\'' . base_url('uploads/pendampingan/' . $row->pdf_surat_keterangan) . '\', \'' . $row->kategori . '\', \'' . $row->id_smt . '\', \'' . addslashes($row->nm_ptk) . '\', ' . $row->id_pendampingan . ', ' . $row->jumlah . ', ' . $row->approve_by_kaprodi . ')" class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> Lihat SK</a>';
			} else {
				$nestedData[] = '<span class="label label-default">Tidak ada file</span>';
			}

			// Status
			if ($row->approve_by_kaprodi == 1) {
				$nestedData[] = '<span class="label label-success">Disetujui</span>';
				$nestedData[] = '<button class="btn btn-xs btn-danger" onclick="change_status(' . $row->id_pendampingan . ', 0)"><i class="fa fa-times"></i> Batalkan</button>';
			} else {
				$nestedData[] = '<span class="label label-warning">Belum Disetujui</span>';
				if (!empty($row->pdf_surat_keterangan)) {
					$nestedData[] = '<button class="btn btn-xs btn-success" onclick="change_status(' . $row->id_pendampingan . ', 1)"><i class="fa fa-check"></i> Setujui</button>';
				} else {
					$nestedData[] = '-';
				}
			}

			$data[] = $nestedData;
		}

		echo json_encode([
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_persetujuan_pendampingan_model->count_all($id_sms),
			"recordsFiltered" => $this->E_persetujuan_pendampingan_model->count_filtered($id_sms),
			"data" => $data,
			"csrf_name" => $this->security->get_csrf_token_name(),
			"csrf_hash" => $this->security->get_csrf_hash()
		]);
	}

	public function change_status()
	{
		$id = $this->input->post('id_pendampingan');
		$status = $this->input->post('status');

		// Validate status
		if (!in_array($status, [0, 1])) {
			echo json_encode(['status' => false, 'message' => 'Status tidak valid.']);
			return;
		}

		// Update database
		$this->db->where('id_pendampingan', $id);
		$this->db->update('e_kinerja_pendampingan', [
			'approve_by_kaprodi' => $status,
			'change_by' => $this->session->userdata('user_id'),
			'change_date' => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'status' => true,
			'csrf_name' => $this->security->get_csrf_token_name(),
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}
}
