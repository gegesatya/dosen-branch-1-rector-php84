<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pusat_data_analisa_epom extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pusat_data_analisa_epom_model', 'Pusat_data_analisa_epom_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{

		$output['judul_besar'] 			= 'Analisa EPOM ';
		$output['judul_kecil'] 			= 'Politeknik WBI';
		$output['menu_aktif'] 			= 'm_pusat_data_analisa_epom';
		$output['parent_data_center']	= 'active open';

		$data = $this->db->select('*, concat(id_smt, "\n", prodi) as thn_prodi')->get('siakad_rekap_epom_pusat_data')->result();
		$output['data'] = json_encode($data);
		//var_dump($data);
		//die();

		$id_smt = 20222;
		/*  MPI  63f083bc-b3a7-4396-99b3-c707575f7823
			AP   f28c79a3-18c1-4b40-b3af-b2bedc810e20
			AH   01f25707-0271-4fc0-b957-eb0d9d0ce0c4
			PKA  df50c13b-fd44-4583-b1a4-f22af69b23bf
			TRPL eea49383-ebe8-476b-85fc-b25d80f0cf32 */
		$get_rekap_wbi = $this->db->where(['id_sms' => 'wbi', 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();
		$get_rekap_mpi = $this->db->where(['id_sms' => '63f083bc-b3a7-4396-99b3-c707575f7823', 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();
		$get_rekap_ap = $this->db->where(['id_sms' => 'f28c79a3-18c1-4b40-b3af-b2bedc810e20', 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();
		$get_rekap_ah = $this->db->where(['id_sms' => '01f25707-0271-4fc0-b957-eb0d9d0ce0c4', 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();
		$get_rekap_pka = $this->db->where(['id_sms' => 'df50c13b-fd44-4583-b1a4-f22af69b23bf', 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();
		$get_rekap_trpl = $this->db->where(['id_sms' => 'eea49383-ebe8-476b-85fc-b25d80f0cf32', 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();

		$output['data_epom_total'] = $get_rekap_wbi->epom_total;
		$output['data_epom_belum'] = $get_rekap_wbi->epom_belum;
		$output['data_epom_sudah'] =  $get_rekap_wbi->epom_sudah;
		$output['data_epom_rerata'] =  $get_rekap_wbi->rerata_epom;
		$output['data_epom_max'] =  $get_rekap_wbi->max_epom;
		$output['data_epom_min'] =  $get_rekap_wbi->min_epom;
		$output['data_epom_upper'] =  $get_rekap_wbi->upper_epom;
		$output['data_epom_lower'] =  $get_rekap_wbi->lower_epom;
		$output['data_epom_upper_25'] =  $get_rekap_wbi->upper_3_epom;
		$output['data_epom_lower_25'] = $get_rekap_wbi->lower_3_epom;
		$output['data_epom_rerata_tetap'] =  $get_rekap_wbi->rerata_epom_tetap;
		$output['data_epom_max_tetap'] =  $get_rekap_wbi->max_epom_tetap;
		$output['data_epom_min_tetap'] =  $get_rekap_wbi->min_epom_tetap;
		$output['data_epom_upper_tetap'] =  $get_rekap_wbi->upper_epom_tetap;
		$output['data_epom_lower_tetap'] = $get_rekap_wbi->lower_epom_tetap;
		$output['data_epom_upper_25_tetap'] =  $get_rekap_wbi->upper_3_epom_tetap;
		$output['data_epom_lower_25_tetap'] =  $get_rekap_wbi->lower_3_epom_tetap;
		$output['data_epom_rerata_tidak_tetap'] =  $get_rekap_wbi->rerata_epom_tidak_tetap;
		$output['data_epom_max_tidak_tetap'] =  $get_rekap_wbi->max_epom_tidak_tetap;
		$output['data_epom_min_tidak_tetap'] =  $get_rekap_wbi->min_epom_tidak_tetap;
		$output['data_epom_upper_tidak_tetap'] =  $get_rekap_wbi->upper_epom_tidak_tetap;
		$output['data_epom_lower_tidak_tetap'] =  $get_rekap_wbi->lower_epom_tidak_tetap;
		$output['data_epom_upper_25_tidak_tetap'] =  $get_rekap_wbi->upper_3_epom_tidak_tetap;
		$output['data_epom_lower_25_tidak_tetap'] =  $get_rekap_wbi->lower_3_epom_tidak_tetap;

		$output['data_epom_total_mpi'] = $get_rekap_mpi->epom_total;
		$output['data_epom_belum_mpi'] = $get_rekap_mpi->epom_belum;
		$output['data_epom_sudah_mpi'] =  $get_rekap_mpi->epom_sudah;
		$output['data_epom_rerata_mpi'] =  $get_rekap_mpi->rerata_epom;
		$output['data_epom_max_mpi'] =  $get_rekap_mpi->max_epom;
		$output['data_epom_min_mpi'] =  $get_rekap_mpi->min_epom;
		$output['data_epom_upper_mpi'] =  $get_rekap_mpi->upper_epom;
		$output['data_epom_lower_mpi'] =  $get_rekap_mpi->lower_epom;
		$output['data_epom_upper_25_mpi'] =  $get_rekap_mpi->upper_3_epom;
		$output['data_epom_lower_25_mpi'] = $get_rekap_mpi->lower_3_epom;
		$output['data_epom_rerata_tetap_mpi'] =  $get_rekap_mpi->rerata_epom_tetap;
		$output['data_epom_max_tetap_mpi'] =  $get_rekap_mpi->max_epom_tetap;
		$output['data_epom_min_tetap_mpi'] =  $get_rekap_mpi->min_epom_tetap;
		$output['data_epom_upper_tetap_mpi'] =  $get_rekap_mpi->upper_epom_tetap;
		$output['data_epom_lower_tetap_mpi'] = $get_rekap_mpi->lower_epom_tetap;
		$output['data_epom_upper_25_tetap_mpi'] =  $get_rekap_mpi->upper_3_epom_tetap;
		$output['data_epom_lower_25_tetap_mpi'] =  $get_rekap_mpi->lower_3_epom_tetap;
		$output['data_epom_rerata_tidak_tetap_mpi'] =  $get_rekap_mpi->rerata_epom_tidak_tetap;
		$output['data_epom_max_tidak_tetap_mpi'] =  $get_rekap_mpi->max_epom_tidak_tetap;
		$output['data_epom_min_tidak_tetap_mpi'] =  $get_rekap_mpi->min_epom_tidak_tetap;
		$output['data_epom_upper_tidak_tetap_mpi'] =  $get_rekap_mpi->upper_epom_tidak_tetap;
		$output['data_epom_lower_tidak_tetap_mpi'] =  $get_rekap_mpi->lower_epom_tidak_tetap;
		$output['data_epom_upper_25_tidak_tetap_mpi'] =  $get_rekap_mpi->upper_3_epom_tidak_tetap;
		$output['data_epom_lower_25_tidak_tetap_mpi'] =  $get_rekap_mpi->lower_3_epom_tidak_tetap;

		$output['data_epom_total_ap'] = $get_rekap_ap->epom_total;
		$output['data_epom_belum_ap'] = $get_rekap_ap->epom_belum;
		$output['data_epom_sudah_ap'] =  $get_rekap_ap->epom_sudah;
		$output['data_epom_rerata_ap'] =  $get_rekap_ap->rerata_epom;
		$output['data_epom_max_ap'] =  $get_rekap_ap->max_epom;
		$output['data_epom_min_ap'] =  $get_rekap_ap->min_epom;
		$output['data_epom_upper_ap'] =  $get_rekap_ap->upper_epom;
		$output['data_epom_lower_ap'] =  $get_rekap_ap->lower_epom;
		$output['data_epom_upper_25_ap'] =  $get_rekap_ap->upper_3_epom;
		$output['data_epom_lower_25_ap'] = $get_rekap_ap->lower_3_epom;
		$output['data_epom_rerata_tetap_ap'] =  $get_rekap_ap->rerata_epom_tetap;
		$output['data_epom_max_tetap_ap'] =  $get_rekap_ap->max_epom_tetap;
		$output['data_epom_min_tetap_ap'] =  $get_rekap_ap->min_epom_tetap;
		$output['data_epom_upper_tetap_ap'] =  $get_rekap_ap->upper_epom_tetap;
		$output['data_epom_lower_tetap_ap'] = $get_rekap_ap->lower_epom_tetap;
		$output['data_epom_upper_25_tetap_ap'] =  $get_rekap_ap->upper_3_epom_tetap;
		$output['data_epom_lower_25_tetap_ap'] =  $get_rekap_ap->lower_3_epom_tetap;
		$output['data_epom_rerata_tidak_tetap_ap'] =  $get_rekap_ap->rerata_epom_tidak_tetap;
		$output['data_epom_max_tidak_tetap_ap'] =  $get_rekap_ap->max_epom_tidak_tetap;
		$output['data_epom_min_tidak_tetap_ap'] =  $get_rekap_ap->min_epom_tidak_tetap;
		$output['data_epom_upper_tidak_tetap_ap'] =  $get_rekap_ap->upper_epom_tidak_tetap;
		$output['data_epom_lower_tidak_tetap_ap'] =  $get_rekap_ap->lower_epom_tidak_tetap;
		$output['data_epom_upper_25_tidak_tetap_ap'] =  $get_rekap_ap->upper_3_epom_tidak_tetap;
		$output['data_epom_lower_25_tidak_tetap_ap'] =  $get_rekap_ap->lower_3_epom_tidak_tetap;

		$output['data_epom_total_ah'] = $get_rekap_ah->epom_total;
		$output['data_epom_belum_ah'] = $get_rekap_ah->epom_belum;
		$output['data_epom_sudah_ah'] =  $get_rekap_ah->epom_sudah;
		$output['data_epom_rerata_ah'] =  $get_rekap_ah->rerata_epom;
		$output['data_epom_max_ah'] =  $get_rekap_ah->max_epom;
		$output['data_epom_min_ah'] =  $get_rekap_ah->min_epom;
		$output['data_epom_upper_ah'] =  $get_rekap_ah->upper_epom;
		$output['data_epom_lower_ah'] =  $get_rekap_ah->lower_epom;
		$output['data_epom_upper_25_ah'] =  $get_rekap_ah->upper_3_epom;
		$output['data_epom_lower_25_ah'] = $get_rekap_ah->lower_3_epom;
		$output['data_epom_rerata_tetap_ah'] =  $get_rekap_ah->rerata_epom_tetap;
		$output['data_epom_max_tetap_ah'] =  $get_rekap_ah->max_epom_tetap;
		$output['data_epom_min_tetap_ah'] =  $get_rekap_ah->min_epom_tetap;
		$output['data_epom_upper_tetap_ah'] =  $get_rekap_ah->upper_epom_tetap;
		$output['data_epom_lower_tetap_ah'] = $get_rekap_ah->lower_epom_tetap;
		$output['data_epom_upper_25_tetap_ah'] =  $get_rekap_ah->upper_3_epom_tetap;
		$output['data_epom_lower_25_tetap_ah'] =  $get_rekap_ah->lower_3_epom_tetap;
		$output['data_epom_rerata_tidak_tetap_ah'] =  $get_rekap_ah->rerata_epom_tidak_tetap;
		$output['data_epom_max_tidak_tetap_ah'] =  $get_rekap_ah->max_epom_tidak_tetap;
		$output['data_epom_min_tidak_tetap_ah'] =  $get_rekap_ah->min_epom_tidak_tetap;
		$output['data_epom_upper_tidak_tetap_ah'] =  $get_rekap_ah->upper_epom_tidak_tetap;
		$output['data_epom_lower_tidak_tetap_ah'] =  $get_rekap_ah->lower_epom_tidak_tetap;
		$output['data_epom_upper_25_tidak_tetap_ah'] =  $get_rekap_ah->upper_3_epom_tidak_tetap;
		$output['data_epom_lower_25_tidak_tetap_ah'] =  $get_rekap_ah->lower_3_epom_tidak_tetap;

		$output['data_epom_total_pka'] = $get_rekap_pka->epom_total;
		$output['data_epom_belum_pka'] = $get_rekap_pka->epom_belum;
		$output['data_epom_sudah_pka'] =  $get_rekap_pka->epom_sudah;
		$output['data_epom_rerata_pka'] =  $get_rekap_pka->rerata_epom;
		$output['data_epom_max_pka'] =  $get_rekap_pka->max_epom;
		$output['data_epom_min_pka'] =  $get_rekap_pka->min_epom;
		$output['data_epom_upper_pka'] =  $get_rekap_pka->upper_epom;
		$output['data_epom_lower_pka'] =  $get_rekap_pka->lower_epom;
		$output['data_epom_upper_25_pka'] =  $get_rekap_pka->upper_3_epom;
		$output['data_epom_lower_25_pka'] = $get_rekap_pka->lower_3_epom;
		$output['data_epom_rerata_tetap_pka'] =  $get_rekap_pka->rerata_epom_tetap;
		$output['data_epom_max_tetap_pka'] =  $get_rekap_pka->max_epom_tetap;
		$output['data_epom_min_tetap_pka'] =  $get_rekap_pka->min_epom_tetap;
		$output['data_epom_upper_tetap_pka'] =  $get_rekap_pka->upper_epom_tetap;
		$output['data_epom_lower_tetap_pka'] = $get_rekap_pka->lower_epom_tetap;
		$output['data_epom_upper_25_tetap_pka'] =  $get_rekap_pka->upper_3_epom_tetap;
		$output['data_epom_lower_25_tetap_pka'] =  $get_rekap_pka->lower_3_epom_tetap;
		$output['data_epom_rerata_tidak_tetap_pka'] =  $get_rekap_pka->rerata_epom_tidak_tetap;
		$output['data_epom_max_tidak_tetap_pka'] =  $get_rekap_pka->max_epom_tidak_tetap;
		$output['data_epom_min_tidak_tetap_pka'] =  $get_rekap_pka->min_epom_tidak_tetap;
		$output['data_epom_upper_tidak_tetap_pka'] =  $get_rekap_pka->upper_epom_tidak_tetap;
		$output['data_epom_lower_tidak_tetap_pka'] =  $get_rekap_pka->lower_epom_tidak_tetap;
		$output['data_epom_upper_25_tidak_tetap_pka'] =  $get_rekap_pka->upper_3_epom_tidak_tetap;
		$output['data_epom_lower_25_tidak_tetap_pka'] =  $get_rekap_pka->lower_3_epom_tidak_tetap;

		$output['data_epom_total_trpl'] = $get_rekap_trpl->epom_total;
		$output['data_epom_belum_trpl'] = $get_rekap_trpl->epom_belum;
		$output['data_epom_sudah_trpl'] =  $get_rekap_trpl->epom_sudah;
		$output['data_epom_rerata_trpl'] =  $get_rekap_trpl->rerata_epom;
		$output['data_epom_max_trpl'] =  $get_rekap_trpl->max_epom;
		$output['data_epom_min_trpl'] =  $get_rekap_trpl->min_epom;
		$output['data_epom_upper_trpl'] =  $get_rekap_trpl->upper_epom;
		$output['data_epom_lower_trpl'] =  $get_rekap_trpl->lower_epom;
		$output['data_epom_upper_25_trpl'] =  $get_rekap_trpl->upper_3_epom;
		$output['data_epom_lower_25_trpl'] = $get_rekap_trpl->lower_3_epom;
		$output['data_epom_rerata_tetap_trpl'] =  $get_rekap_trpl->rerata_epom_tetap;
		$output['data_epom_max_tetap_trpl'] =  $get_rekap_trpl->max_epom_tetap;
		$output['data_epom_min_tetap_trpl'] =  $get_rekap_trpl->min_epom_tetap;
		$output['data_epom_upper_tetap_trpl'] =  $get_rekap_trpl->upper_epom_tetap;
		$output['data_epom_lower_tetap_trpl'] = $get_rekap_trpl->lower_epom_tetap;
		$output['data_epom_upper_25_tetap_trpl'] =  $get_rekap_trpl->upper_3_epom_tetap;
		$output['data_epom_lower_25_tetap_trpl'] =  $get_rekap_trpl->lower_3_epom_tetap;
		$output['data_epom_rerata_tidak_tetap_trpl'] =  $get_rekap_trpl->rerata_epom_tidak_tetap;
		$output['data_epom_max_tidak_tetap_trpl'] =  $get_rekap_trpl->max_epom_tidak_tetap;
		$output['data_epom_min_tidak_tetap_trpl'] =  $get_rekap_trpl->min_epom_tidak_tetap;
		$output['data_epom_upper_tidak_tetap_trpl'] =  $get_rekap_trpl->upper_epom_tidak_tetap;
		$output['data_epom_lower_tidak_tetap_trpl'] =  $get_rekap_trpl->lower_epom_tidak_tetap;
		$output['data_epom_upper_25_tidak_tetap_trpl'] =  $get_rekap_trpl->upper_3_epom_tidak_tetap;
		$output['data_epom_lower_25_tidak_tetap_trpl'] =  $get_rekap_trpl->lower_3_epom_tidak_tetap;





		$output['content'] 			= 'pusat_data_analisa_epom/pusat_data_analisa_epom_view';
		$output['after_page'] 		= 'pusat_data_analisa_epom/pusat_data_analisa_epom_after_page';


		$this->load->view('master_page', $output);
	}

	private function data_epom($id_sms = '', $id_smt = null, $id_ptk = '')
	{


		if ($id_sms == '') {
			$rerata = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <>' => 0])
				->select('avg(nilai_epom) as rerata')->from('siakad_rekap_epom')->get()->row()->rerata;

			$rerata_tetap = $this->db->where(['id_smt' => $id_smt, 'sd.id_ikatan_kerja' => 'A'])
				->select('avg(nilai_epom) as rerata')->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->row()->rerata;
			$rerata_tidak_tetap = $this->db->where(['id_smt' => $id_smt, 'sd.id_ikatan_kerja <>' => 'A'])
				->select('avg(nilai_epom) as rerata')->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->row()->rerata;
		} else {
			$rerata = $this->db->where(['re.id_smt' => $id_smt, 'id_sms' => $id_sms])->select('avg(nilai_epom) as rerata')
				->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')->get()->row()->rerata;
			$rerata_tetap = $this->db->where(['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->select('avg(nilai_epom) as rerata')->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->get()->row()->rerata;
			$rerata_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->select('avg(nilai_epom) as rerata')->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->get()->row()->rerata;
		}

		$rerata = round($rerata, 2);
		$rerata_tetap = round($rerata_tetap, 2);
		$rerata_tidak_tetap = round($rerata_tidak_tetap, 2);

		if ($id_sms == '') {
			$total = $this->db->where(['fk.id_smt' => $id_smt])->select('*')
				->from('siakad_evaluasi_dosen ed')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')
				->get()->num_rows();
			$belum = $this->db->where(['fk.id_smt' => $id_smt, 'ed.status_eval' => 0])->select('*')
				->from('siakad_evaluasi_dosen ed')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')
				->get()->num_rows();
			$sudah = $this->db->where(['fk.id_smt' => $id_smt, 'ed.status_eval' => 1])->select('*')
				->from('siakad_evaluasi_dosen ed')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')
				->get()->num_rows();
			$max = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <>' => 0])->select('max(nilai_epom) as maks')
				->from('siakad_rekap_epom')
				->get()->row()->maks;
			$min = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <>' => 0])
				->select('min(nilai_epom) as mins')
				->from('siakad_rekap_epom')
				->get()->row()->mins;
			$upper = $this->db->where(['id_smt' => $id_smt, 'nilai_epom >=' => $rerata])->get('siakad_rekap_epom')->num_rows();
			$lower = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <' => $rerata])->get('siakad_rekap_epom')->num_rows();
			$upper_25 = $this->db->where(['id_smt' => $id_smt, 'nilai_epom >=' => 3])->get('siakad_rekap_epom')->num_rows();
			$lower_25 = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <' => 3])->get('siakad_rekap_epom')->num_rows();

			$max_tetap = $this->db->where(['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja' => 'A'])
				->select('max(nilai_epom) as maks')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->get()->row()->maks;
			$min_tetap = $this->db->where(['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja' => 'A'])
				->select('min(nilai_epom) as mins')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->get()->row()->mins;
			$upper_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
			$lower_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
			$upper_25_tetap = $this->db->where(['id_smt' => $id_smt, 'nilai_epom >=' => 3, 'sd.id_ikatan_kerja' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
			$lower_25_tetap = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <' => 3, 'sd.id_ikatan_kerja' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();

			$max_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt,  'sd.id_ikatan_kerja <>' => 'A'])
				->select('max(nilai_epom) as maks')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->get()->row()->maks;
			$min_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt,  'sd.id_ikatan_kerja <>' => 'A'])
				->select('min(nilai_epom) as mins')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->get()->row()->mins;
			$upper_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
			$lower_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
			$upper_25_tidak_tetap = $this->db->where(['id_smt' => $id_smt, 'nilai_epom >=' => 3, 'sd.id_ikatan_kerja <>' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
			$lower_25_tidak_tetap = $this->db->where(['id_smt' => $id_smt, 'nilai_epom <' => 3, 'sd.id_ikatan_kerja <>' => 'A'])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->get()->num_rows();
		} else {
			$total = $this->db->where(['fk.id_smt' => $id_smt, 'fk.id_sms' => $id_sms])->select('*')->from('siakad_evaluasi_dosen ed')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')->get()->num_rows();
			$belum = $this->db->where(['fk.id_smt' => $id_smt, 'ed.status_eval' => 0, 'fk.id_sms' => $id_sms])->select('*')->from('siakad_evaluasi_dosen ed')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')->get()->num_rows();
			$sudah = $this->db->where(['fk.id_smt' => $id_smt, 'ed.status_eval' => 1, 'fk.id_sms' => $id_sms])->select('*')->from('siakad_evaluasi_dosen ed')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')->get()->num_rows();
			$max = $this->db->where(['re.id_smt' => $id_smt,  'id_sms' => $id_sms])
				->select('max(nilai_epom) as maks')->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')->get()->row()->maks;
			$min = $this->db->where(['re.id_smt' => $id_smt,  'id_sms' => $id_sms])
				->select('min(nilai_epom) as mins')->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')->get()->row()->mins;
			$upper = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => $rerata, 'id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')->get()->num_rows();
			$lower = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => $rerata, 'id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')->get()->num_rows();
			$upper_25 = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => 3, 'id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')->get()->num_rows();
			$lower_25 = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => 3, 'id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')->get()->num_rows();

			$max_tetap = $this->db->where(['re.id_smt' => $id_smt,  'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->select('max(nilai_epom) as maks')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->row()->maks;
			$min_tetap = $this->db->where(['re.id_smt' => $id_smt,  'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->select('min(nilai_epom) as mins')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->row()->mins;
			$upper_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
			$lower_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
			$upper_25_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => 3, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
			$lower_25_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => 3, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();

			$max_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt,  'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->select('max(nilai_epom) as maks')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->row()->maks;
			$min_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt,  'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->select('min(nilai_epom) as mins')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->row()->mins;
			$upper_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
			$lower_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
			$upper_25_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom >=' => 3, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
			$lower_25_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'nilai_epom <' => 3, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms])
				->from('siakad_rekap_epom re')->join('siakad_dosen sd', 'sd.id_ptk = re.id_ptk')
				->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
				->get()->num_rows();
		}

		return [
			$total, $belum, $sudah, $rerata, $max, $min, $upper, $lower, $upper_25, $lower_25,
			$rerata_tetap, $max_tetap, $min_tetap, $upper_tetap, $lower_tetap, $upper_25_tetap, $lower_25_tetap,
			$rerata_tidak_tetap, $max_tidak_tetap, $min_tidak_tetap, $upper_tidak_tetap, $lower_tidak_tetap, $upper_25_tidak_tetap, $lower_25_tidak_tetap
		];
	}

	public function save_data($id_smt)
	{
		//$id_smt = 20221;

		/*  MPI  63f083bc-b3a7-4396-99b3-c707575f7823
			AP   f28c79a3-18c1-4b40-b3af-b2bedc810e20
			AH   01f25707-0271-4fc0-b957-eb0d9d0ce0c4
			PKA  df50c13b-fd44-4583-b1a4-f22af69b23bf
			TRPL eea49383-ebe8-476b-85fc-b25d80f0cf32 */

		$data_epom_total = $this->data_epom('', $id_smt)[0];
		$data_epom_belum = $this->data_epom('', $id_smt)[1];
		$data_epom_sudah = $this->data_epom('', $id_smt)[2];
		$data_epom_rerata = round($this->data_epom('', $id_smt)[3], 2);
		$data_epom_max = round($this->data_epom('', $id_smt)[4], 2);
		$data_epom_min = round($this->data_epom('', $id_smt)[5], 2);
		$data_epom_upper = round($this->data_epom('', $id_smt)[6], 2);
		$data_epom_lower = round($this->data_epom('', $id_smt)[7], 2);
		$data_epom_upper_25 = round($this->data_epom('', $id_smt)[8], 2);
		$data_epom_lower_25 = round($this->data_epom('', $id_smt)[9], 2);
		$data_epom_rerata_tetap = round($this->data_epom('', $id_smt)[10], 2);
		$data_epom_max_tetap = round($this->data_epom('', $id_smt)[11], 2);
		$data_epom_min_tetap = round($this->data_epom('', $id_smt)[12], 2);
		$data_epom_upper_tetap = round($this->data_epom('', $id_smt)[13], 2);
		$data_epom_lower_tetap = round($this->data_epom('', $id_smt)[14], 2);
		$data_epom_upper_25_tetap = round($this->data_epom('', $id_smt)[15], 2);
		$data_epom_lower_25_tetap = round($this->data_epom('', $id_smt)[16], 2);
		$data_epom_rerata_tidak_tetap = round($this->data_epom('', $id_smt)[17], 2);
		$data_epom_max_tidak_tetap = round($this->data_epom('', $id_smt)[18], 2);
		$data_epom_min_tidak_tetap = round($this->data_epom('', $id_smt)[19], 2);
		$data_epom_upper_tidak_tetap = round($this->data_epom('', $id_smt)[20], 2);
		$data_epom_lower_tidak_tetap = round($this->data_epom('', $id_smt)[21], 2);
		$data_epom_upper_25_tidak_tetap = round($this->data_epom('', $id_smt)[22], 2);
		$data_epom_lower_25_tidak_tetap = round($this->data_epom('', $id_smt)[23], 2);

		$data_epom_total_mpi = $this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[0];
		$data_epom_belum_mpi = $this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[1];
		$data_epom_sudah_mpi = $this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[2];
		$data_epom_rerata_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[3], 2);
		$data_epom_max_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[4], 2);
		$data_epom_min_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[5], 2);
		$data_epom_upper_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[6], 2);
		$data_epom_lower_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[7], 2);
		$data_epom_upper_25_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[8], 2);
		$data_epom_lower_25_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[9], 2);
		$data_epom_rerata_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[10], 2);
		$data_epom_max_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[11], 2);
		$data_epom_min_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[12], 2);
		$data_epom_upper_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[13], 2);
		$data_epom_lower_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[14], 2);
		$data_epom_upper_25_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[15], 2);
		$data_epom_lower_25_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[16], 2);
		$data_epom_rerata_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[17], 2);
		$data_epom_max_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[18], 2);
		$data_epom_min_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[19], 2);
		$data_epom_upper_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[20], 2);
		$data_epom_lower_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[21], 2);
		$data_epom_upper_25_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[22], 2);
		$data_epom_lower_25_tidak_tetap_mpi = round($this->data_epom('63f083bc-b3a7-4396-99b3-c707575f7823', $id_smt)[23], 2);

		$data_epom_total_ap = $this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[0];
		$data_epom_belum_ap = $this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[1];
		$data_epom_sudah_ap = $this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[2];
		$data_epom_rerata_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[3], 2);
		$data_epom_max_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[4], 2);
		$data_epom_min_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[5], 2);
		$data_epom_upper_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[6], 2);
		$data_epom_lower_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[7], 2);
		$data_epom_upper_25_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[8], 2);
		$data_epom_lower_25_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[9], 2);
		$data_epom_rerata_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[10], 2);
		$data_epom_max_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[11], 2);
		$data_epom_min_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[12], 2);
		$data_epom_upper_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[13], 2);
		$data_epom_lower_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[14], 2);
		$data_epom_upper_25_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[15], 2);
		$data_epom_lower_25_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[16], 2);
		$data_epom_rerata_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[17], 2);
		$data_epom_max_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[18], 2);
		$data_epom_min_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[19], 2);
		$data_epom_upper_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[20], 2);
		$data_epom_lower_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[21], 2);
		$data_epom_upper_25_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[22], 2);
		$data_epom_lower_25_tidak_tetap_ap = round($this->data_epom('f28c79a3-18c1-4b40-b3af-b2bedc810e20', $id_smt)[23], 2);

		$data_epom_total_ah = $this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[0];
		$data_epom_belum_ah = $this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[1];
		$data_epom_sudah_ah = $this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[2];
		$data_epom_rerata_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[3], 2);
		$data_epom_max_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[4], 2);
		$data_epom_min_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[5], 2);
		$data_epom_upper_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[6], 2);
		$data_epom_lower_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[7], 2);
		$data_epom_upper_25_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[8], 2);
		$data_epom_lower_25_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[9], 2);
		$data_epom_rerata_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[10], 2);
		$data_epom_max_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[11], 2);
		$data_epom_min_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[12], 2);
		$data_epom_upper_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[13], 2);
		$data_epom_lower_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[14], 2);
		$data_epom_upper_25_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[15], 2);
		$data_epom_lower_25_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[16], 2);
		$data_epom_rerata_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[17], 2);
		$data_epom_max_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[18], 2);
		$data_epom_min_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[19], 2);
		$data_epom_upper_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[20], 2);
		$data_epom_lower_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[21], 2);
		$data_epom_upper_25_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[22], 2);
		$data_epom_lower_25_tidak_tetap_ah = round($this->data_epom('01f25707-0271-4fc0-b957-eb0d9d0ce0c4', $id_smt)[23], 2);

		$data_epom_total_pka = $this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[0];
		$data_epom_belum_pka = $this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[1];
		$data_epom_sudah_pka = $this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[2];
		$data_epom_rerata_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[3], 2);
		$data_epom_max_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[4], 2);
		$data_epom_min_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[5], 2);
		$data_epom_upper_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[6], 2);
		$data_epom_lower_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[7], 2);
		$data_epom_upper_25_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[8], 2);
		$data_epom_lower_25_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[9], 2);
		$data_epom_rerata_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[10], 2);
		$data_epom_max_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[11], 2);
		$data_epom_min_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[12], 2);
		$data_epom_upper_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[13], 2);
		$data_epom_lower_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[14], 2);
		$data_epom_upper_25_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[15], 2);
		$data_epom_lower_25_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[16], 2);
		$data_epom_rerata_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[17], 2);
		$data_epom_max_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[18], 2);
		$data_epom_min_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[19], 2);
		$data_epom_upper_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[20], 2);
		$data_epom_lower_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[21], 2);
		$data_epom_upper_25_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[22], 2);
		$data_epom_lower_25_tidak_tetap_pka = round($this->data_epom('df50c13b-fd44-4583-b1a4-f22af69b23bf', $id_smt)[23], 2);

		$data_epom_total_trpl = $this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[0];
		$data_epom_belum_trpl = $this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[1];
		$data_epom_sudah_trpl = $this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[2];
		$data_epom_rerata_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[3], 2);
		$data_epom_max_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[4], 2);
		$data_epom_min_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[5], 2);
		$data_epom_upper_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[6], 2);
		$data_epom_lower_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[7], 2);
		$data_epom_upper_25_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[8], 2);
		$data_epom_lower_25_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[9], 2);
		$data_epom_rerata_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[10], 2);
		$data_epom_max_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[11], 2);
		$data_epom_min_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[12], 2);
		$data_epom_upper_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[13], 2);
		$data_epom_lower_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[14], 2);
		$data_epom_upper_25_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[15], 2);
		$data_epom_lower_25_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[16], 2);
		$data_epom_rerata_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[17], 2);
		$data_epom_max_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[18], 2);
		$data_epom_min_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[19], 2);
		$data_epom_upper_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[20], 2);
		$data_epom_lower_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[21], 2);
		$data_epom_upper_25_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[22], 2);
		$data_epom_lower_25_tidak_tetap_trpl = round($this->data_epom('eea49383-ebe8-476b-85fc-b25d80f0cf32', $id_smt)[23], 2);

		$data_wbi = [
			'id_sms' => 'wbi', 'id_smt' => $id_smt,
			'epom_total' => $data_epom_total,
			'epom_sudah' => $data_epom_sudah,
			'epom_belum' => $data_epom_belum,
			'rerata_epom' => $data_epom_rerata,
			'rerata_epom_tetap' => $data_epom_rerata_tetap,
			'rerata_epom_tidak_tetap' => $data_epom_rerata_tidak_tetap,
			'max_epom' => $data_epom_max,
			'max_epom_tetap' => $data_epom_max_tetap,
			'max_epom_tidak_tetap' => $data_epom_max_tidak_tetap,
			'min_epom' => $data_epom_min,
			'min_epom_tetap' => $data_epom_min_tetap,
			'min_epom_tidak_tetap' => $data_epom_min_tidak_tetap,
			'upper_epom' => $data_epom_upper,
			'upper_epom_tetap' => $data_epom_upper_tetap,
			'upper_epom_tidak_tetap' => $data_epom_upper_tidak_tetap,
			'lower_epom' => $data_epom_lower,
			'lower_epom_tetap' => $data_epom_lower_tetap,
			'lower_epom_tidak_tetap' => $data_epom_lower_tidak_tetap,
			'upper_3_epom' => $data_epom_upper_25,
			'upper_3_epom_tetap' => $data_epom_upper_25_tetap,
			'upper_3_epom_tidak_tetap' => $data_epom_upper_25_tidak_tetap,
			'lower_3_epom' => $data_epom_lower_25,
			'lower_3_epom_tetap' => $data_epom_lower_25_tetap,
			'lower_3_epom_tidak_tetap' => $data_epom_lower_25_tidak_tetap,
			'partisipasi' => round($data_epom_sudah / $data_epom_total * 100, 2)
		];

		$data_mpi = [
			'id_sms' => '63f083bc-b3a7-4396-99b3-c707575f7823', 'id_smt' => $id_smt,
			'epom_total' => $data_epom_total_mpi,
			'epom_sudah' => $data_epom_sudah_mpi,
			'epom_belum' => $data_epom_belum_mpi,
			'rerata_epom' => $data_epom_rerata_mpi,
			'rerata_epom_tetap' => $data_epom_rerata_tetap_mpi,
			'rerata_epom_tidak_tetap' => $data_epom_rerata_tidak_tetap_mpi,
			'max_epom' => $data_epom_max_mpi,
			'max_epom_tetap' => $data_epom_max_tetap_mpi,
			'max_epom_tidak_tetap' => $data_epom_max_tidak_tetap_mpi,
			'min_epom' => $data_epom_min_mpi,
			'min_epom_tetap' => $data_epom_min_tetap_mpi,
			'min_epom_tidak_tetap' => $data_epom_min_tidak_tetap_mpi,
			'upper_epom' => $data_epom_upper_mpi,
			'upper_epom_tetap' => $data_epom_upper_tetap_mpi,
			'upper_epom_tidak_tetap' => $data_epom_upper_tidak_tetap_mpi,
			'lower_epom' => $data_epom_lower_mpi,
			'lower_epom_tetap' => $data_epom_lower_tetap_mpi,
			'lower_epom_tidak_tetap' => $data_epom_lower_tidak_tetap_mpi,
			'upper_3_epom' => $data_epom_upper_25_mpi,
			'upper_3_epom_tetap' => $data_epom_upper_25_tetap_mpi,
			'upper_3_epom_tidak_tetap' => $data_epom_upper_25_tidak_tetap_mpi,
			'lower_3_epom' => $data_epom_lower_25_mpi,
			'lower_3_epom_tetap' => $data_epom_lower_25_tetap_mpi,
			'lower_3_epom_tidak_tetap' => $data_epom_lower_25_tidak_tetap_mpi,
			'partisipasi' => round($data_epom_sudah_mpi / $data_epom_total_mpi * 100, 2)

		];

		$data_ap = [
			'id_sms' => 'f28c79a3-18c1-4b40-b3af-b2bedc810e20', 'id_smt' => $id_smt,
			'epom_total' => $data_epom_total_ap,
			'epom_sudah' => $data_epom_sudah_ap,
			'epom_belum' => $data_epom_belum_ap,
			'rerata_epom' => $data_epom_rerata_ap,
			'rerata_epom_tetap' => $data_epom_rerata_tetap_ap,
			'rerata_epom_tidak_tetap' => $data_epom_rerata_tidak_tetap_ap,
			'max_epom' => $data_epom_max_ap,
			'max_epom_tetap' => $data_epom_max_tetap_ap,
			'max_epom_tidak_tetap' => $data_epom_max_tidak_tetap_ap,
			'min_epom' => $data_epom_min_ap,
			'min_epom_tetap' => $data_epom_min_tetap_ap,
			'min_epom_tidak_tetap' => $data_epom_min_tidak_tetap_ap,
			'upper_epom' => $data_epom_upper_ap,
			'upper_epom_tetap' => $data_epom_upper_tetap_ap,
			'upper_epom_tidak_tetap' => $data_epom_upper_tidak_tetap_ap,
			'lower_epom' => $data_epom_lower_ap,
			'lower_epom_tetap' => $data_epom_lower_tetap_ap,
			'lower_epom_tidak_tetap' => $data_epom_lower_tidak_tetap_ap,
			'upper_3_epom' => $data_epom_upper_25_ap,
			'upper_3_epom_tetap' => $data_epom_upper_25_tetap_ap,
			'upper_3_epom_tidak_tetap' => $data_epom_upper_25_tidak_tetap_ap,
			'lower_3_epom' => $data_epom_lower_25_ap,
			'lower_3_epom_tetap' => $data_epom_lower_25_tetap_ap,
			'lower_3_epom_tidak_tetap' => $data_epom_lower_25_tidak_tetap_ap,
			'partisipasi' => round($data_epom_sudah_ap / $data_epom_total_ap * 100, 2)
		];

		$data_ah = [
			'id_sms' => '01f25707-0271-4fc0-b957-eb0d9d0ce0c4', 'id_smt' => $id_smt,
			'epom_total' => $data_epom_total_ah,
			'epom_sudah' => $data_epom_sudah_ah,
			'epom_belum' => $data_epom_belum_ah,
			'rerata_epom' => $data_epom_rerata_ah,
			'rerata_epom_tetap' => $data_epom_rerata_tetap_ah,
			'rerata_epom_tidak_tetap' => $data_epom_rerata_tidak_tetap_ah,
			'max_epom' => $data_epom_max_ah,
			'max_epom_tetap' => $data_epom_max_tetap_ah,
			'max_epom_tidak_tetap' => $data_epom_max_tidak_tetap_ah,
			'min_epom' => $data_epom_min_ah,
			'min_epom_tetap' => $data_epom_min_tetap_ah,
			'min_epom_tidak_tetap' => $data_epom_min_tidak_tetap_ah,
			'upper_epom' => $data_epom_upper_ah,
			'upper_epom_tetap' => $data_epom_upper_tetap_ah,
			'upper_epom_tidak_tetap' => $data_epom_upper_tidak_tetap_ah,
			'lower_epom' => $data_epom_lower_ah,
			'lower_epom_tetap' => $data_epom_lower_tetap_ah,
			'lower_epom_tidak_tetap' => $data_epom_lower_tidak_tetap_ah,
			'upper_3_epom' => $data_epom_upper_25_ah,
			'upper_3_epom_tetap' => $data_epom_upper_25_tetap_ah,
			'upper_3_epom_tidak_tetap' => $data_epom_upper_25_tidak_tetap_ah,
			'lower_3_epom' => $data_epom_lower_25_ah,
			'lower_3_epom_tetap' => $data_epom_lower_25_tetap_ah,
			'lower_3_epom_tidak_tetap' => $data_epom_lower_25_tidak_tetap_ah,
			'partisipasi' => round($data_epom_sudah_ah / $data_epom_total_ah * 100, 2)
		];

		$data_pka = [
			'id_sms' => 'df50c13b-fd44-4583-b1a4-f22af69b23bf', 'id_smt' => $id_smt,
			'epom_total' => $data_epom_total_pka,
			'epom_sudah' => $data_epom_sudah_pka,
			'epom_belum' => $data_epom_belum_pka,
			'rerata_epom' => $data_epom_rerata_pka,
			'rerata_epom_tetap' => $data_epom_rerata_tetap_pka,
			'rerata_epom_tidak_tetap' => $data_epom_rerata_tidak_tetap_pka,
			'max_epom' => $data_epom_max_pka,
			'max_epom_tetap' => $data_epom_max_tetap_pka,
			'max_epom_tidak_tetap' => $data_epom_max_tidak_tetap_pka,
			'min_epom' => $data_epom_min_pka,
			'min_epom_tetap' => $data_epom_min_tetap_pka,
			'min_epom_tidak_tetap' => $data_epom_min_tidak_tetap_pka,
			'upper_epom' => $data_epom_upper_pka,
			'upper_epom_tetap' => $data_epom_upper_tetap_pka,
			'upper_epom_tidak_tetap' => $data_epom_upper_tidak_tetap_pka,
			'lower_epom' => $data_epom_lower_pka,
			'lower_epom_tetap' => $data_epom_lower_tetap_pka,
			'lower_epom_tidak_tetap' => $data_epom_lower_tidak_tetap_pka,
			'upper_3_epom' => $data_epom_upper_25_pka,
			'upper_3_epom_tetap' => $data_epom_upper_25_tetap_pka,
			'upper_3_epom_tidak_tetap' => $data_epom_upper_25_tidak_tetap_pka,
			'lower_3_epom' => $data_epom_lower_25_pka,
			'lower_3_epom_tetap' => $data_epom_lower_25_tetap_pka,
			'lower_3_epom_tidak_tetap' => $data_epom_lower_25_tidak_tetap_pka,
			'partisipasi' => round($data_epom_sudah_pka / $data_epom_total_pka * 100, 2)
		];

		if ($id_smt == 20201 or $id_smt == 20202) {
			$partisipasi_trpl = 0;
		} else {
			$partisipasi_trpl = round($data_epom_sudah_trpl / $data_epom_total_trpl * 100, 2);
		}

		$data_trpl = [
			'id_sms' => 'eea49383-ebe8-476b-85fc-b25d80f0cf32', 'id_smt' => $id_smt,
			'epom_total' => $data_epom_total_trpl,
			'epom_sudah' => $data_epom_sudah_trpl,
			'epom_belum' => $data_epom_belum_trpl,
			'rerata_epom' => $data_epom_rerata_trpl,
			'rerata_epom_tetap' => $data_epom_rerata_tetap_trpl,
			'rerata_epom_tidak_tetap' => $data_epom_rerata_tidak_tetap_trpl,
			'max_epom' => $data_epom_max_trpl,
			'max_epom_tetap' => $data_epom_max_tetap_trpl,
			'max_epom_tidak_tetap' => $data_epom_max_tidak_tetap_trpl,
			'min_epom' => $data_epom_min_trpl,
			'min_epom_tetap' => $data_epom_min_tetap_trpl,
			'min_epom_tidak_tetap' => $data_epom_min_tidak_tetap_trpl,
			'upper_epom' => $data_epom_upper_trpl,
			'upper_epom_tetap' => $data_epom_upper_tetap_trpl,
			'upper_epom_tidak_tetap' => $data_epom_upper_tidak_tetap_trpl,
			'lower_epom' => $data_epom_lower_trpl,
			'lower_epom_tetap' => $data_epom_lower_tetap_trpl,
			'lower_epom_tidak_tetap' => $data_epom_lower_tidak_tetap_trpl,
			'upper_3_epom' => $data_epom_upper_25_trpl,
			'upper_3_epom_tetap' => $data_epom_upper_25_tetap_trpl,
			'upper_3_epom_tidak_tetap' => $data_epom_upper_25_tidak_tetap_trpl,
			'lower_3_epom' => $data_epom_lower_25_trpl,
			'lower_3_epom_tetap' => $data_epom_lower_25_tetap_trpl,
			'lower_3_epom_tidak_tetap' => $data_epom_lower_25_tidak_tetap_trpl,
			'partisipasi' => $partisipasi_trpl
		];

		$this->db->where('id_smt', $id_smt)->delete('siakad_rekap_epom_pusat_data');
		$this->Pusat_data_analisa_epom_model->save($data_wbi);
		$this->Pusat_data_analisa_epom_model->save($data_mpi);
		$this->Pusat_data_analisa_epom_model->save($data_ap);
		$this->Pusat_data_analisa_epom_model->save($data_ah);
		$this->Pusat_data_analisa_epom_model->save($data_pka);
		$this->Pusat_data_analisa_epom_model->save($data_trpl);
		echo "{elapsed_time}";
	}

	public function ajax_show_epom($tipe, $id_sms = '', $kategori = null)
	{
		$id_smt = 20221;
		$rekap_pusat_data = $this->db->where(['id_sms' => $id_sms, 'id_smt' => $id_smt])->get('siakad_rekap_epom_pusat_data')->row();
		if ($tipe == 'max_institusi') {
			if ($id_sms == 'wbi') {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja' => 'A'];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja <>' => 'A'];
				}
			} else {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms];
				}
			}

			$query = $this->db->where($where)->limit(10)->order_by('nilai_epom', 'desc')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->get();
		} elseif ($tipe == 'min_institusi') {
			if ($id_sms == 'wbi') {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <>' => 0];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <>' => 0, 'sd.id_ikatan_kerja' => 'A'];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <>' => 0, 'sd.id_ikatan_kerja <>' => 'A'];
				}
			} else {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <>' => 0, 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <>' => 0, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <>' => 0, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms];
				}
			}

			$query = $this->db->where($where)->limit(10)->order_by('nilai_epom', 'asc')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->get();
		} elseif ($tipe == 'upper_rerata_institusi') {
			$rerata = $rekap_pusat_data->rerata_epom;
			$rerata_tetap = $rekap_pusat_data->rerata_epom_tetap;
			$rerata_tidak_tetap = $rekap_pusat_data->rerata_epom_tidak_tetap;

			if ($id_sms == 'wbi') {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => $rerata];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A'];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A'];
				}
			} else {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => $rerata, 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms];
				}
			}

			$query = $this->db->where($where)->order_by('nilai_epom', 'desc')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->get();
		} elseif ($tipe == 'lower_rerata_institusi') {
			$rerata = $rekap_pusat_data->rerata_epom;
			$rerata_tetap = $rekap_pusat_data->rerata_epom_tetap;
			$rerata_tidak_tetap = $rekap_pusat_data->rerata_epom_tidak_tetap;

			if ($id_sms == 'wbi') {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => $rerata];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A'];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A'];
				}
			} else {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => $rerata, 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => $rerata_tetap, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => $rerata_tidak_tetap, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms];
				}
			}

			$query = $this->db->where($where)->order_by('nilai_epom', 'asc')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->get();
		} elseif ($tipe == 'upper_3_institusi') {
			if ($id_sms == 'wbi') {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => 3];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => 3, 'sd.id_ikatan_kerja' => 'A'];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => 3, 'sd.id_ikatan_kerja <>' => 'A'];
				}
			} else {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => 3, 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => 3, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom >=' => 3, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms];
				}
			}


			$query = $this->db->where($where)->order_by('nilai_epom', 'desc')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->get();
		} elseif ($tipe == 'lower_3_institusi') {
			if ($id_sms == 'wbi') {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => 3];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => 3, 'sd.id_ikatan_kerja' => 'A'];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => 3, 'sd.id_ikatan_kerja <>' => 'A'];
				}
			} else {
				if ($kategori == 0) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => 3, 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 1) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => 3, 'sd.id_ikatan_kerja' => 'A', 'fk.id_sms' => $id_sms];
				} elseif ($kategori == 2) {
					$where = ['re.id_smt' => $id_smt, 're.nilai_epom <' => 3, 'sd.id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $id_sms];
				}
			}


			$query = $this->db->where($where)->order_by('nilai_epom', 'asc')
				->from('siakad_rekap_epom re')
				->join('siakad_dosen sd', 're.id_ptk = sd.id_ptk')
				->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
				->join('sms s', 's.id_sms = fk.id_sms')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
				->get();
		}


		$this->load->library('table');
		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-bordered">',
			'thead_open'          => '<thead>',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle">',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		];

		$this->table->set_template($tmpl);
		$this->table->set_heading([
			'No',
			'Dosen',
			'Kelas Kuliah',
			'Mata Kuliah',
			'Prodi',
			'EPOM'
		]);


		$i = 1;


		foreach ($query->result() as $l_epom) {
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				['data' => $l_epom->nm_ptk],
				['data' => $l_epom->nm_kls],
				['data' => $l_epom->nm_mk],
				['data' => $l_epom->nm_prodi_english],
				['data' => $l_epom->nilai_epom],

			]);
		}

		/* $this->table->add_row(array(
			array('data' => 'Jumlah SKS / <i>Semester Credit Total</i>', 'class' => 'text-center', 'colspan' => 5),
			array('data' => $sks_mk, 'class' => 'text-center'),
			array('data' => $sks_tm, 'class' => 'text-center'),
			array('data' => $sks_prak, 'class' => 'text-center'),
			array('data' => $sks_prak_lap, 'class' => 'text-center')
		)); */

		/* $nilai_tabel =  $this->table->generate();
		$output['mahasiswa'] = $mahasiswa;
		$output['tabel'] = $nilai_tabel;

		$nm_pd = $mahasiswa->nm_pd;
		$nipd = $mahasiswa->nipd;
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$akademik_record = $this->view_akademik($nipd);

		$log_message = '';
		if (!empty($data_rencana_studi->catatan_dosen_wali)) {
			$log_message =  $data_rencana_studi->catatan_dosen_wali;
		} */

		$r_tabel =  $this->table->generate();
		echo json_encode([
			'tabel' => $r_tabel
			/* 'tabel_krs' => $nilai_tabel, 'tabel_akademik_record' => $akademik_record,
			'nm_pd' => $nm_pd, 'nipd' => $nipd, 'id_smt' => $id_smt,
			'catatan_dosen_wali' => $data_rencana_studi->catatan_dosen_wali,
			'id_status_krs' => $data_rencana_studi->id_status_krs,
			'log_message' => $log_message */
		]);
	}
}
