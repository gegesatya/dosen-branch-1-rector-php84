<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_informasi_statistik_kaprodi extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Informasi_statistik_kaprodi_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}



		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			//redirect('auth/logout', 'refresh');
			return show_error('Forbidden Access.');
		}

		/* $username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		} */
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Informasi dan Statistik ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_informasi_statistik_kaprodi';
		$output['parent_kaprodi']	= 'active open';
		$output['content'] 			= 'informasi_statistik_kaprodi/informasi_statistik_kaprodi_view';
		$output['after_page'] 		= 'informasi_statistik_kaprodi/informasi_statistik_kaprodi_after_page';

		$this->load->view('master_page', $output);
	}



	public function ajax_list()
	{
		$list = $this->Informasi_statistik_kaprodi_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_informasi_statistik_kaprodi/hitung_rata_ips') .  '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Lihat</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->keterangan;
			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Informasi_statistik_kaprodi_model->count_all(),
			"recordsFiltered" => $this->Informasi_statistik_kaprodi_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function hitung_rata_ips($id_smt)
	{

		$semester = substr($id_smt, -1);
		$angkatan_1 = substr($id_smt, 2, 2);
		$angkatan_2 = $angkatan_1 - 1;
		$angkatan_3 = $angkatan_2 - 1;
		$angkatan_4 = $angkatan_3 - 1;

		$kuliah_mahasiswa_1 = $this->hitung_rerata($id_smt, $angkatan_1);
		$kuliah_mahasiswa_2 = $this->hitung_rerata($id_smt, $angkatan_2);
		$kuliah_mahasiswa_3 = $this->hitung_rerata($id_smt, $angkatan_3);
		$kuliah_mahasiswa_4 = $this->hitung_rerata($id_smt, $angkatan_4);

		$kuliah_mahasiswa_1_prodi = $this->hitung_rerata($id_smt, $angkatan_1, $this->session->userdata('prodi'));
		$kuliah_mahasiswa_2_prodi = $this->hitung_rerata($id_smt, $angkatan_2, $this->session->userdata('prodi'));
		$kuliah_mahasiswa_3_prodi = $this->hitung_rerata($id_smt, $angkatan_3, $this->session->userdata('prodi'));
		$kuliah_mahasiswa_4_prodi = $this->hitung_rerata($id_smt, $angkatan_4, $this->session->userdata('prodi'));

		/* $kuliah_mahasiswa_1_mpi = $this->hitung_rerata($id_smt, $angkatan_1, '63f083bc-b3a7-4396-99b3-c707575f7823');
		$kuliah_mahasiswa_2_mpi = $this->hitung_rerata($id_smt, $angkatan_2, '63f083bc-b3a7-4396-99b3-c707575f7823');
		$kuliah_mahasiswa_3_mpi = $this->hitung_rerata($id_smt, $angkatan_3, '63f083bc-b3a7-4396-99b3-c707575f7823');
		$kuliah_mahasiswa_4_mpi = $this->hitung_rerata($id_smt, $angkatan_4, '63f083bc-b3a7-4396-99b3-c707575f7823');

		$kuliah_mahasiswa_1_ap = $this->hitung_rerata($id_smt, $angkatan_1, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$kuliah_mahasiswa_2_ap = $this->hitung_rerata($id_smt, $angkatan_2, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$kuliah_mahasiswa_3_ap = $this->hitung_rerata($id_smt, $angkatan_3, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$kuliah_mahasiswa_4_ap = $this->hitung_rerata($id_smt, $angkatan_4, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');

		$kuliah_mahasiswa_1_ah = $this->hitung_rerata($id_smt, $angkatan_1, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$kuliah_mahasiswa_2_ah = $this->hitung_rerata($id_smt, $angkatan_2, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$kuliah_mahasiswa_3_ah = $this->hitung_rerata($id_smt, $angkatan_3, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$kuliah_mahasiswa_4_ah = $this->hitung_rerata($id_smt, $angkatan_4, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');

		$kuliah_mahasiswa_1_pka = $this->hitung_rerata($id_smt, $angkatan_1, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$kuliah_mahasiswa_2_pka = $this->hitung_rerata($id_smt, $angkatan_2, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$kuliah_mahasiswa_3_pka = $this->hitung_rerata($id_smt, $angkatan_3, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$kuliah_mahasiswa_4_pka = $this->hitung_rerata($id_smt, $angkatan_4, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');

		$kuliah_mahasiswa_1_trpl = $this->hitung_rerata($id_smt, $angkatan_1, 'eea49383-ebe8-476b-85fc-b25d80f0cf32');
		$kuliah_mahasiswa_2_trpl = $this->hitung_rerata($id_smt, $angkatan_2, 'eea49383-ebe8-476b-85fc-b25d80f0cf32');
		$kuliah_mahasiswa_3_trpl = $this->hitung_rerata($id_smt, $angkatan_3, 'eea49383-ebe8-476b-85fc-b25d80f0cf32');
		$kuliah_mahasiswa_4_trpl = $this->hitung_rerata($id_smt, $angkatan_4, 'eea49383-ebe8-476b-85fc-b25d80f0cf32'); */

		$output['sem_a_ips'] = $kuliah_mahasiswa_1->row()->ips;
		$output['sem_b_ips'] = $kuliah_mahasiswa_2->row()->ips;
		$output['sem_c_ips'] = $kuliah_mahasiswa_3->row()->ips;
		$output['sem_d_ips'] = $kuliah_mahasiswa_4->row()->ips;

		$output['sem_a_ipk'] = $kuliah_mahasiswa_1->row()->ipk;
		$output['sem_b_ipk'] = $kuliah_mahasiswa_2->row()->ipk;
		$output['sem_c_ipk'] = $kuliah_mahasiswa_3->row()->ipk;
		$output['sem_d_ipk'] = $kuliah_mahasiswa_4->row()->ipk;

		$output['sem_a_ip_institusi'] = $kuliah_mahasiswa_1->row()->ip_institusi;
		$output['sem_b_ip_institusi'] = $kuliah_mahasiswa_2->row()->ip_institusi;
		$output['sem_c_ip_institusi'] = $kuliah_mahasiswa_3->row()->ip_institusi;
		$output['sem_d_ip_institusi'] = $kuliah_mahasiswa_4->row()->ip_institusi;

		$output['sem_a_ips_prodi'] = $kuliah_mahasiswa_1_prodi->row()->ips;
		$output['sem_b_ips_prodi'] = $kuliah_mahasiswa_2_prodi->row()->ips;
		$output['sem_c_ips_prodi'] = $kuliah_mahasiswa_3_prodi->row()->ips;
		$output['sem_d_ips_prodi'] = $kuliah_mahasiswa_4_prodi->row()->ips;

		$output['sem_a_ipk_prodi'] = $kuliah_mahasiswa_1_prodi->row()->ipk;
		$output['sem_b_ipk_prodi'] = $kuliah_mahasiswa_2_prodi->row()->ipk;
		$output['sem_c_ipk_prodi'] = $kuliah_mahasiswa_3_prodi->row()->ipk;
		$output['sem_d_ipk_prodi'] = $kuliah_mahasiswa_4_prodi->row()->ipk;

		$output['sem_a_ip_institusi_prodi'] = $kuliah_mahasiswa_1_prodi->row()->ip_institusi;
		$output['sem_b_ip_institusi_prodi'] = $kuliah_mahasiswa_2_prodi->row()->ip_institusi;
		$output['sem_c_ip_institusi_prodi'] = $kuliah_mahasiswa_3_prodi->row()->ip_institusi;
		$output['sem_d_ip_institusi_prodi'] = $kuliah_mahasiswa_4_prodi->row()->ip_institusi;

		/* $output['sem_a_ips_ap'] = $kuliah_mahasiswa_1_ap->row()->ips;
		$output['sem_b_ips_ap'] = $kuliah_mahasiswa_2_ap->row()->ips;
		$output['sem_c_ips_ap'] = $kuliah_mahasiswa_3_ap->row()->ips;
		$output['sem_d_ips_ap'] = $kuliah_mahasiswa_4_ap->row()->ips;

		$output['sem_a_ipk_ap'] = $kuliah_mahasiswa_1_ap->row()->ipk;
		$output['sem_b_ipk_ap'] = $kuliah_mahasiswa_2_ap->row()->ipk;
		$output['sem_c_ipk_ap'] = $kuliah_mahasiswa_3_ap->row()->ipk;
		$output['sem_d_ipk_ap'] = $kuliah_mahasiswa_4_ap->row()->ipk;

		$output['sem_a_ip_institusi_ap'] = $kuliah_mahasiswa_1_ap->row()->ip_institusi;
		$output['sem_b_ip_institusi_ap'] = $kuliah_mahasiswa_2_ap->row()->ip_institusi;
		$output['sem_c_ip_institusi_ap'] = $kuliah_mahasiswa_3_ap->row()->ip_institusi;
		$output['sem_d_ip_institusi_ap'] = $kuliah_mahasiswa_4_ap->row()->ip_institusi;

		$output['sem_a_ips_ah'] = $kuliah_mahasiswa_1_ah->row()->ips;
		$output['sem_b_ips_ah'] = $kuliah_mahasiswa_2_ah->row()->ips;
		$output['sem_c_ips_ah'] = $kuliah_mahasiswa_3_ah->row()->ips;
		$output['sem_d_ips_ah'] = $kuliah_mahasiswa_4_ah->row()->ips;

		$output['sem_a_ipk_ah'] = $kuliah_mahasiswa_1_ah->row()->ipk;
		$output['sem_b_ipk_ah'] = $kuliah_mahasiswa_2_ah->row()->ipk;
		$output['sem_c_ipk_ah'] = $kuliah_mahasiswa_3_ah->row()->ipk;
		$output['sem_d_ipk_ah'] = $kuliah_mahasiswa_4_ah->row()->ipk;

		$output['sem_a_ip_institusi_ah'] = $kuliah_mahasiswa_1_ah->row()->ip_institusi;
		$output['sem_b_ip_institusi_ah'] = $kuliah_mahasiswa_2_ah->row()->ip_institusi;
		$output['sem_c_ip_institusi_ah'] = $kuliah_mahasiswa_3_ah->row()->ip_institusi;
		$output['sem_d_ip_institusi_ah'] = $kuliah_mahasiswa_4_ah->row()->ip_institusi;

		$output['sem_a_ips_pka'] = $kuliah_mahasiswa_1_pka->row()->ips;
		$output['sem_b_ips_pka'] = $kuliah_mahasiswa_2_pka->row()->ips;
		$output['sem_c_ips_pka'] = $kuliah_mahasiswa_3_pka->row()->ips;
		$output['sem_d_ips_pka'] = $kuliah_mahasiswa_4_pka->row()->ips;

		$output['sem_a_ipk_pka'] = $kuliah_mahasiswa_1_pka->row()->ipk;
		$output['sem_b_ipk_pka'] = $kuliah_mahasiswa_2_pka->row()->ipk;
		$output['sem_c_ipk_pka'] = $kuliah_mahasiswa_3_pka->row()->ipk;
		$output['sem_d_ipk_pka'] = $kuliah_mahasiswa_4_pka->row()->ipk;

		$output['sem_a_ip_institusi_pka'] = $kuliah_mahasiswa_1_pka->row()->ip_institusi;
		$output['sem_b_ip_institusi_pka'] = $kuliah_mahasiswa_2_pka->row()->ip_institusi;
		$output['sem_c_ip_institusi_pka'] = $kuliah_mahasiswa_3_pka->row()->ip_institusi;
		$output['sem_d_ip_institusi_pka'] = $kuliah_mahasiswa_4_pka->row()->ip_institusi;

		$output['sem_a_ips_trpl'] = $kuliah_mahasiswa_1_trpl->row()->ips;
		$output['sem_b_ips_trpl'] = $kuliah_mahasiswa_2_trpl->row()->ips;
		$output['sem_c_ips_trpl'] = $kuliah_mahasiswa_3_trpl->row()->ips;
		$output['sem_d_ips_trpl'] = $kuliah_mahasiswa_4_trpl->row()->ips;

		$output['sem_a_ipk_trpl'] = $kuliah_mahasiswa_1_trpl->row()->ipk;
		$output['sem_b_ipk_trpl'] = $kuliah_mahasiswa_2_trpl->row()->ipk;
		$output['sem_c_ipk_trpl'] = $kuliah_mahasiswa_3_trpl->row()->ipk;
		$output['sem_d_ipk_trpl'] = $kuliah_mahasiswa_4_trpl->row()->ipk;

		$output['sem_a_ip_institusi_trpl'] = $kuliah_mahasiswa_1_trpl->row()->ip_institusi;
		$output['sem_b_ip_institusi_trpl'] = $kuliah_mahasiswa_2_trpl->row()->ip_institusi;
		$output['sem_c_ip_institusi_trpl'] = $kuliah_mahasiswa_3_trpl->row()->ip_institusi;
		$output['sem_d_ip_institusi_trpl'] = $kuliah_mahasiswa_4_trpl->row()->ip_institusi; */

		if ($semester == 1) {
			$output['sem_a'] = 1;
			$output['sem_b'] = 3;
			$output['sem_c'] = 5;
			$output['sem_d'] = 7;
		} else {
			$output['sem_a'] = 2;
			$output['sem_b'] = 4;
			$output['sem_c'] = 6;
			$output['sem_d'] = 8;
		}

		$output['mhs_aktif_a'] = $this->hitung_mhs_aktif($id_smt, $angkatan_1);
		$output['mhs_aktif_b'] = $this->hitung_mhs_aktif($id_smt, $angkatan_2);
		$output['mhs_aktif_c'] = $this->hitung_mhs_aktif($id_smt, $angkatan_3);
		$output['mhs_aktif_d'] = $this->hitung_mhs_aktif($id_smt, $angkatan_4);

		$output['mhs_aktif_a_prodi'] = $this->hitung_mhs_aktif($id_smt, $angkatan_1, $this->session->userdata('prodi'));
		$output['mhs_aktif_b_prodi'] = $this->hitung_mhs_aktif($id_smt, $angkatan_2, $this->session->userdata('prodi'));
		$output['mhs_aktif_c_prodi'] = $this->hitung_mhs_aktif($id_smt, $angkatan_3, $this->session->userdata('prodi'));
		$output['mhs_aktif_d_prodi'] = $this->hitung_mhs_aktif($id_smt, $angkatan_4, $this->session->userdata('prodi'));

		/* $output['mhs_aktif_a_ap'] = $this->hitung_mhs_aktif($id_smt, $angkatan_1, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$output['mhs_aktif_b_ap'] = $this->hitung_mhs_aktif($id_smt, $angkatan_2, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$output['mhs_aktif_c_ap'] = $this->hitung_mhs_aktif($id_smt, $angkatan_3, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$output['mhs_aktif_d_ap'] = $this->hitung_mhs_aktif($id_smt, $angkatan_4, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');

		$output['mhs_aktif_a_ah'] = $this->hitung_mhs_aktif($id_smt, $angkatan_1, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$output['mhs_aktif_b_ah'] = $this->hitung_mhs_aktif($id_smt, $angkatan_2, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$output['mhs_aktif_c_ah'] = $this->hitung_mhs_aktif($id_smt, $angkatan_3, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$output['mhs_aktif_d_ah'] = $this->hitung_mhs_aktif($id_smt, $angkatan_4, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');

		$output['mhs_aktif_a_pka'] = $this->hitung_mhs_aktif($id_smt, $angkatan_1, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$output['mhs_aktif_b_pka'] = $this->hitung_mhs_aktif($id_smt, $angkatan_2, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$output['mhs_aktif_c_pka'] = $this->hitung_mhs_aktif($id_smt, $angkatan_3, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$output['mhs_aktif_d_pka'] = $this->hitung_mhs_aktif($id_smt, $angkatan_4, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');

		$output['mhs_aktif_a_trpl'] = $this->hitung_mhs_aktif($id_smt, $angkatan_1, 'eea49383-ebe8-476b-85fc-b25d80f0cf32');
		$output['mhs_aktif_b_trpl'] = $this->hitung_mhs_aktif($id_smt, $angkatan_2, 'eea49383-ebe8-476b-85fc-b25d80f0cf32');
		$output['mhs_aktif_c_trpl'] = $this->hitung_mhs_aktif($id_smt, $angkatan_3, 'eea49383-ebe8-476b-85fc-b25d80f0cf32');
		$output['mhs_aktif_d_trpl'] = $this->hitung_mhs_aktif($id_smt, $angkatan_4, 'eea49383-ebe8-476b-85fc-b25d80f0cf32'); */

		$output['sebaran_ips_wbi'] = $this->sebaran_ips($id_smt);
		$output['sebaran_ips_prodi'] = $this->sebaran_ips($id_smt, $this->session->userdata('prodi'));
		/* $output['sebaran_ips_ap'] = $this->sebaran_ips($id_smt, 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
		$output['sebaran_ips_ah'] = $this->sebaran_ips($id_smt, '01f25707-0271-4fc0-b957-eb0d9d0ce0c4');
		$output['sebaran_ips_pka'] = $this->sebaran_ips($id_smt, 'df50c13b-fd44-4583-b1a4-f22af69b23bf');
		$output['sebaran_ips_trpl'] = $this->sebaran_ips($id_smt, 'eea49383-ebe8-476b-85fc-b25d80f0cf32'); */

		$output['id_smt'] = $id_smt;
		$output['nm_smt'] = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;

		if ($id_smt >= 20201) {
			$epom_dosen_tetap = $this->db->where(['re.id_smt' => $id_smt, 'id_ikatan_kerja' => 'A'])
				->select('*')->from('siakad_rekap_epom re')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('siakad_jadwal sj', 'sj.id = re.id_jadwal')
				->join('siakad_dosen sd', 'sd.id_ptk = sj.id_sdm')
				->order_by('s.id_sms', 'kode_mk')
				->get();

			$epom_dosen_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'id_ikatan_kerja <>' => 'A'])
				->select('*')->from('siakad_rekap_epom re')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('siakad_jadwal sj', 'sj.id = re.id_jadwal')
				->join('siakad_dosen sd', 'sd.id_ptk = sj.id_sdm')
				->order_by('s.id_sms', 'kode_mk')
				->get();

			$rerata_epom_dosen_tetap = $this->db
				->where(['re.id_smt' => $id_smt, 'id_ikatan_kerja' => 'A'])
				->select('avg(nilai_epom) as nilai_epom, fk.id_sms')->from('siakad_rekap_epom re')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->group_by('fk.id_sms')
				->get();

			$rerata_epom_dosen_tidak_tetap = $this->db
				->where(['re.id_smt' => $id_smt, 'id_ikatan_kerja <>' => 'A'])
				->select('avg(nilai_epom) as nilai_epom, fk.id_sms')->from('siakad_rekap_epom re')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->group_by('fk.id_sms')
				->get();

			$output['epom_tetap'] = $epom_dosen_tetap;
			$output['epom_tidak_tetap'] = $epom_dosen_tidak_tetap;
			$output['rerata_epom_dosen_tetap'] = $rerata_epom_dosen_tetap;
			$output['rerata_epom_dosen_tidak_tetap'] = $rerata_epom_dosen_tidak_tetap;
		} else {
			$output['epom_tetap'] = [];
			$output['epom_tidak_tetap'] = [];
			$output['rerata_epom_dosen_tetap'] = [];
			$output['rerata_epom_dosen_tidak_tetap'] = [];
		}



		$html = $this->load->view('cetak/informasi_statistik_kaprodi', $output, true);
		$pdfFilePath  = 'Nilai.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	private function hitung_rerata($id_smt, $angkatan, $id_sms = '')
	{
		if ($id_sms == '') {
			$kuliah_mahasiswa = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A'])->like('km.nipd', $angkatan, 'after')
				->select('avg(ips) as ips, avg(km.ipk) as ipk, avg(km.ip_institusi) as ip_institusi')
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get();
		} else {
			$kuliah_mahasiswa = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'id_sms' => $id_sms])->like('km.nipd', $angkatan, 'after')
				->select('avg(ips) as ips, avg(km.ipk) as ipk, avg(km.ip_institusi) as ip_institusi')
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get();
		}


		return $kuliah_mahasiswa;
	}


	private function hitung_mhs_aktif($id_smt, $angkatan, $id_sms = '')
	{
		if ($id_sms == '') {
			$kuliah_mahasiswa = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A'])->like('km.nipd', $angkatan, 'after')
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();
		} else {
			$kuliah_mahasiswa = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'id_sms' => $id_sms])->like('km.nipd', $angkatan, 'after')
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();
		}


		return $kuliah_mahasiswa;
	}

	private function sebaran_ips($id_smt,  $id_sms = '')
	{
		$arr_ips = [];

		if ($id_sms == '') {
			/* $jumlah = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A'])->like('km.nipd', $angkatan, 'after')
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows(); */

			$ips_a = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips >' => 3.5])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();

			$ips_b = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips >=' => 3.0, 'ips <=' => 3.5])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();

			$ips_c = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips >=' => 2.5, 'ips <' => 3.0])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();

			$ips_d = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips <' => 2.5])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();
		} else {
			/* $jumlah = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'id_sms' => $id_sms])->like('km.nipd', $angkatan, 'after')
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows(); */

			$ips_a = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips >' => 3.5, 'id_sms' => $id_sms])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();

			$ips_b = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips >=' => 3.0, 'ips <=' => 3.5, 'id_sms' => $id_sms])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();

			$ips_c = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips >=' => 2.5, 'ips <' => 3.0, 'id_sms' => $id_sms])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();

			$ips_d = $this->db->where(['id_smt' => $id_smt, 'id_stat_mhs' => 'A', 'ips <' => 2.5, 'id_sms' => $id_sms])
				->from('kuliah_mahasiswa km')
				->join('mahasiswa_pt mp', 'mp.nipd = km.nipd')
				->get()->num_rows();
		}

		$arr_ips = [$ips_a, $ips_b, $ips_c, $ips_d];

		return $arr_ips;
	}
}
