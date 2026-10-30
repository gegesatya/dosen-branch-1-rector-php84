<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_arsip_bap extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Arsip_bap_model', 'Arsip_bap_model');
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
		$output['judul_besar'] 		= 'Arsip BAP ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_arsip_bap';
		$output['parent_3']			= 'active open';
		$output['content'] 			= 'arsip_bap/arsip_bap_view';
		$output['after_page'] 		= 'arsip_bap/arsip_bap_after_page';
		$output['dd_jadwal'] 		= $this->Arsip_bap_model->f_dropdown('siakad_jadwal', 'id', 'tag_name', 'tag_name', $this->session->userdata('id_ptk'));

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Arsip_bap_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_arsip_bap/cetak_pdf_berita_acara') . '/' . $n->id_kls . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> View BAP</a>';
			$html_copy = '<a href="javascript:void(0)" 
						 class="btn btn-sm btn-default green-stripe"
						 onclick="ajax_copy(\'' . $n->id_kls . '\')">
						 <i class="fa fa-file"></i> Copy BAP</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->nm_kls;
			$row[] = $n->nm_mk;
			/* $row[] = $html_copy; */
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Arsip_bap_model->count_all(),
			"recordsFiltered" => $this->Arsip_bap_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_berita_acara($id_kls = null, $id_smt = null)
	{
		//$pertemuan = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master')->result();
		if ($id_smt < 20181) {
			$pertemuan = $this->db->select('uniq, 
												  id_rencana_pertemuan, 
												  id_kls, minggu_ke,
												  tanggal_berita_acara, 
												  isi_berita_acara as berita_acara_rencana')->from('arsip_absen_master a')
				->where('a.id_kls', $id_kls)
				->get()->result();
		} else {
			$pertemuan = $this->db->select('*')->from('arsip_absen_master a')
				->join('arsip_rencana_pertemuan p', 'p.uniq = a.id_rencana_pertemuan')
				->where('a.id_kls', $id_kls)
				->get()->result();
		}

		$data_kuliah = $this->db->select('mk.nm_mk, d.nm_ptk, sms.nm_lemb, sem.nm_smt')
			->from('arsip_kelas_kuliah sk')
			->join('arsip_mata_kuliah mk', 'mk.id_mk = sk.id_mk')
			->join('siakad_dosen d', 'd.id_ptk = sk.id_ptk')
			->join('sms sms', 'sk.id_sms = sms.id_sms')
			->join('semester sem', 'sk.id_smt = sem.id_smt')
			->where('sk.id_kls', $id_kls)->get()->row();


		$output['nm_ptk'] = $data_kuliah->nm_ptk;
		$output['nm_mk'] = $data_kuliah->nm_mk;
		$output['nm_lemb'] = $data_kuliah->nm_lemb;
		$output['nm_smt'] = $data_kuliah->nm_smt;
		$output['list_pertemuan'] = $pertemuan;
		$output['statistik'] = $this->count_statistik($id_kls);
		$output['id_smt'] = $id_smt;
		$output['tipe_file'] = 1;
		$output['class_table'] = 'table-black';

		$html = $this->load->view('cetak/pdf_berita_acara_perkuliahan', $output, true);
		$pdfFilePath  = 'Berita Acara Mengajar ' . $data_kuliah->nm_mk . ' Semester ' . $data_kuliah->nm_smt . '.pdf';

		$mpdf = new \Mpdf\Mpdf();

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	private function count_statistik($id_kls)
	{
		$dt_kelas = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master')->row();
		$dt_absen = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master');
		$dt_teori = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Teori'])
			->get('arsip_absen_master');
		$dt_praktek = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Praktek'])
			->get('arsip_absen_master');

		$jumlah_mahasiswa = $this->db->where('id_kls', $id_kls)->get('feeder_nilai')->num_rows();
		$jumlah_pertemuan 	= $dt_absen->num_rows();
		$jumlah_teori 		= $dt_teori->num_rows();
		$jumlah_praktek 	= $dt_praktek->num_rows();

		$data = [];
		$data['jumlah_pertemuan'] 	= $jumlah_pertemuan;
		$data['jumlah_teori'] 		= $jumlah_teori;
		$data['jumlah_praktek'] 	= $jumlah_praktek;
		$data['jumlah_mahasiswa'] 	= $jumlah_mahasiswa;
		return $data;
	}

	public function get_copy_data($id_kls)
	{

		$jml_data = $this->db->where('id_kls', $id_kls)->get('arsip_rencana_pertemuan');
		//$data = $this->db->where('id_kls', $id_kls)->get('siakad_absen_master')->row();
		$data_kls = $this->db->where('id_kls', $id_kls)->get('arsip_kelas_kuliah')->row();

		if ($jml_data->num_rows() >= 14) {
			$id_mk = $data_kls->id_mk;
			$nm_mk = $this->db->where('id_mk', $id_mk)->get('arsip_mata_kuliah')->row()->nm_mk;
			$data_kls->nm_mk = $nm_mk;
			$table = '<table class="table table-bordered">
						<thead><tr><th>#</th><th>Tgl</th><th>Bahan Kajian</th><th>Sumber Belajar</th></tr></thead>
						<tbody>';

			foreach ($jml_data->result() as $key => $value) {
				$table .= '<tr>
							<td>' . $value->pertemuan . '</td>
							<td>' . $value->tanggal_rencana . '</td>
							<td>' . $value->berita_acara_rencana . '</td>
							<td>' . $value->bacaan_materi . '</td>
						</tr>';
			}

			$table .= '</tbody></table>';
			echo json_encode(['data_kls' => $data_kls, 'bap_table' => $table]);
		} else {
			echo json_encode(['status' => FALSE, 'message' => 'Mohon lengkapi sumber data, jumlah berita acara harus 15 minggu']);
		}
	}

	public function ajax_pertemuan_copy_data()
	{
		$this->_validate();
		$id_kls_sumber = $this->security->xss_clean($this->input->post('id_kls')); //sumber
		$id_jadwal_tujuan = $this->security->xss_clean($this->input->post('id_jadwal')); //tujuan
		$start_date = strtotime($this->input->post('tanggal_mulai'));

		$jadwal_master = $this->db->where('id', $id_jadwal_tujuan)->get('siakad_jadwal');

		$rps_sumber = $this->db->where(['id_kls' => $id_kls_sumber])->get('arsip_rencana_pertemuan')->result();
		//$cek_rps_tujuan = $this->db->where(['id_kls' => $id_jadwal_tujuan])->get('siakad_rencana_pertemuan')->result();

		$data = [];

		foreach ($rps_sumber as $key => $value) {
			//cek rencana pertemuan exist
			$rp_exist = $this->db->where(['id_jadwal' => $id_jadwal_tujuan, 'pertemuan' => $value->pertemuan])
				->get('siakad_rencana_pertemuan');

			if ($rp_exist->num_rows() == 0) {
				$id_jadwal 				= $id_jadwal_tujuan;
				$tanggal_rencana 		= date('Y-m-d', $start_date);
				$pertemuan 				= $value->pertemuan;
				$jam_mulai 				= $jadwal_master->row()->jam_mulai;
				$jam_selesai 			= $jadwal_master->row()->jam_selesai;
				$jam_teori 				= $value->jam_teori;
				$jam_praktek 			= $value->jam_praktek;
				$berita_acara_rencana	= $value->berita_acara_rencana;
				$bacaan_materi			= $value->bacaan_materi;
				$create_by 				= $this->ion_auth->user()->row()->id;
				$create_date 			= date('Y-m-d h:i:s');
				$data[] = compact(
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
			}

			$start_date = strtotime("+7 day", $start_date);
		}

		if ($data) {
			$this->db->insert_batch('siakad_rencana_pertemuan', $data);
		}

		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('tanggal_mulai') == '') {
			$data['inputerror'][] = 'tanggal_mulai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('id_jadwal') == '') {
			$data['inputerror'][] = 'id_jadwal';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
