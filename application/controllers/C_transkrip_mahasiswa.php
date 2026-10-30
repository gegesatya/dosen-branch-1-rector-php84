<?php
defined('BASEPATH') or exit('No direct script access allowed');

class c_transkrip_mahasiswa extends CI_Controller
{
	public $setting;
	public function __construct()
	{
		parent::__construct();

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}


		$this->setting = $this->db->get('siakad_setting')->row();
	}
	public function index()
	{
	}

	/* public function get_transkrip($id_reg_pd){
		$nilai_transfer = $this->db->select('*')
							   ->from('nilai_transfer nt')
							   ->join('feeder_mata_kuliah mk', 'mk.id_mk = nt.id_mk')
							   ->where('id_reg_pd', $id_reg_pd)
							   ->get()->result();
		
		$nilai = $this->db->select('sn.*, mk.kode_mk, mk.nm_mk, sk.sks_mk, sk.id_smt')
						  ->from('feeder_nilai sn')
						  ->join('feeder_kelas_kuliah sk', 'sk.id_kls = sn.id_kls')
						  ->join('feeder_mata_kuliah mk', 'mk.id_mk = sk.id_mk')
						  ->where('id_reg_pd', $id_reg_pd)
						  ->order_by('sk.id_smt')
						  ->get()->result();

		$transkrip = array();

		$sks_nilai_transfer = 0;
		$sks_indeks_nilai_transfer = 0;

		$sks_total = 0;
		$sks_indeks_total = 0;

		$i=1;
		foreach ($nilai_transfer as $key => $value) {
			$sks_nilai_transfer = $sks_nilai_transfer + $value->sks_diakui;
			$sks_indeks_nilai_transfer = $sks_indeks_nilai_transfer + ($value->sks_diakui*$value->nilai_angka_diakui);

			$sks_total = $sks_total + $value->sks_diakui;
			$sks_indeks_total = $sks_indeks_total + ($value->sks_diakui*$value->nilai_angka_diakui);
			array_push($transkrip, array(
									'nomor'=>$i.'.',
									'kode_mk'=>$value->kode_mk,
									'nm_mk'=>$value->nm_mk,
									'id_smt'=>'Hasil Konversi',
									'sks_mk'=>$value->sks_diakui,
									'nilai_huruf'=>$value->nilai_huruf_diakui,
									'nilai_indeks'=>$value->nilai_angka_diakui,
									'sks_indeks'=>$value->sks_diakui * $value->nilai_angka_diakui
									));
			$i++;
		}

		$ip_transfer =0;
		if($sks_nilai_transfer > 0){
			$ip_transfer = round($sks_indeks_nilai_transfer/$sks_nilai_transfer, 2);
		}

		if($sks_nilai_transfer>0){
			array_push($transkrip, array(
									'nomor'=>'',
									'kode_mk'=>'',
									'nm_mk'=>'',
									'id_smt'=>'<b>TOTAL</b>',
									'sks_mk'=>'<b>'.$sks_nilai_transfer.'</b>',
									'nilai_huruf'=>'',
									'nilai_indeks'=>'',
									'sks_indeks'=>'<b>'.$sks_indeks_nilai_transfer.'</b>'
								), array(
									'nomor'=>'',
									'kode_mk'=>'',
									'nm_mk'=>'',
									'id_smt'=>'<b>Indeks Prestasi Kumulatif Transfer</b>',
									'sks_mk'=>'',
									'nilai_huruf'=>'',
									'nilai_indeks'=>'',
									'sks_indeks'=>'<b>'.$ip_transfer.'</b>'
								)
						);		
		}
		
		$my_id_smt = 0;
		$yout_id_smt = 0;
		$sks_semester = 0;
		$sks_indeks_semester = 0;
		

		$ip = 0;
		$ipk = 0;

		foreach ($nilai as $key => $value) {
			$your_id_smt = $value->id_smt;
			if($my_id_smt == 0){
				$my_id_smt = $value->id_smt;
			}

			
			if($my_id_smt <> $your_id_smt){
				array_push($transkrip, array(
										'nomor'=>'',
										'kode_mk'=>'',
										'nm_mk'=>'',
										'id_smt'=>'<b>SUB TOTAL</b>',
										'sks_mk'=>'<b>'.$sks_semester.'</b>',
										'nilai_huruf'=>'',
										'nilai_indeks'=>'',
										'sks_indeks'=>'<b>'.$sks_indeks_semester.'</b>'
									), array(
										'nomor'=>'',
										'kode_mk'=>'',
										'nm_mk'=>'',
										'id_smt'=>'<b>Indeks Prestasi Semester</b>',
										'sks_mk'=>'',
										'nilai_huruf'=>'',
										'nilai_indeks'=>'',
										'sks_indeks'=>'<b>'.round($sks_semester>0?$sks_indeks_semester/$sks_semester:0, 2).'</b>'
									)
				);
				$sks_semester = 0;
				$sks_indeks_semester = 0;
			}

			$sks_semester = $sks_semester + $value->sks_mk;
			$sks_indeks_semester = $sks_indeks_semester + ($value->sks_mk*$value->nilai_indeks);
			$sks_total = $sks_total + $value->sks_mk;
			$sks_indeks_total = $sks_indeks_total + ($value->sks_mk*$value->nilai_indeks);

			array_push($transkrip, array(
									'nomor'=>$i.'.',
									'kode_mk'=>$value->kode_mk,
									'nm_mk'=>$value->nm_mk,
									'id_smt'=>$value->id_smt,
									'sks_mk'=>$value->sks_mk,
									'nilai_huruf'=>$value->nilai_huruf,
									'nilai_indeks'=>$value->nilai_indeks,
									'sks_indeks'=>$value->sks_mk*$value->nilai_indeks
									));

			$my_id_smt = $your_id_smt;
			$i++;
		}

		if($sks_semester <> 0){
			$ip = $sks_indeks_semester/$sks_semester;
		}

		if($sks_total <> 0){
			$ipk = round($sks_indeks_total/$sks_total,2);
		}

		array_push($transkrip, array(
								'nomor'=>'',
								'kode_mk'=>'',
								'nm_mk'=>'',
								'id_smt'=>'<b>SUB TOTAL</b>',
								'sks_mk'=>'<b>'.$sks_semester.'</b>',
								'nilai_huruf'=>'',
								'nilai_indeks'=>'',
								'sks_indeks'=>'<b>'.$sks_indeks_semester.'</b>'
							), array(
								'nomor'=>'',
								'kode_mk'=>'',
								'nm_mk'=>'',
								'id_smt'=>'<b>Indeks Prestasi Semester</b>',
								'sks_mk'=>'',
								'nilai_huruf'=>'',
								'nilai_indeks'=>'',
								'sks_indeks'=>'<b>'.round($sks_semester>0?$sks_indeks_semester/$sks_semester:0, 2).'</b>'
							), array(
								'nomor'=>'',
								'kode_mk'=>'',
								'nm_mk'=>'',
								'id_smt'=>'<b>TOTAL</b>',
								'sks_mk'=>'<b>'.$sks_total.'</b>',
								'nilai_huruf'=>'',
								'nilai_indeks'=>'',
								'sks_indeks'=>'<b>'.$sks_indeks_total.'</b>'
							), array(
								'nomor'=>'',
								'kode_mk'=>'',
								'nm_mk'=>'',
								'id_smt'=>'<b>Indeks Prestasi Kumulatif</b>',
								'sks_mk'=>'',
								'nilai_huruf'=>'',
								'nilai_indeks'=>'',
								'sks_indeks'=>'<b>'.$ipk.'</b>'
							));


		$sks_semester = 0;
		$sks_indeks_semester = 0;	

		$id_smt = $this->session->userdata("tahun_ajaran");
		$nm_semester = $this->db->where('id_smt', $this->setting->tahun_ajaran_future)->get('semester')->row();

		$output['halaman'] = 'Transkrip Nilai ';
        $output['sub_halaman'] = '';
        $output['master_page'] = 'ajax_pagination';
        $output['mahasiswa'] = $this->db->select('*')
        							->from('mahasiswa m')
        							->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
        							->join('sms s', 's.id_sms = mp.id_sms')
        							->where('mp.id_reg_pd', $id_reg_pd)
        							->get()->row();

        $nm_ptk = $this->db->where('id_ptk',$this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->nm_ptk;
		$photo = $this->db->where('id_ptk',$this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->photo;
		$perkuliahan = $this->db->where(array('id_smt'=>$id_smt))->get('feeder_kelas_kuliah')->result();
        $output['nm_ptk'] = $nm_ptk;
        $output['photo'] = !empty($photo)?$photo:'no_photo.jpg';

        $output['content'] = 'transkrip/v_transkrip';
        $output['transkrip'] 			= $transkrip;

        $ion_user = $this->ion_auth->user()->row();
        $nipd = $ion_user->nim;
		$mahasiswa_pt = $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->row();
       	$respon = $this->load->view('transkrip/v_transkrip', $output, true);
        echo $respon;
	} */

	public function get_transkrip($nipd)
	{
		$nilai_transfer = $this->db->select('*')
			->from('nilai_transfer nt')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = nt.id_mk')
			->where('id_reg_pd', $nipd)
			->get()->result();

		$nilai = $this->db->select('sn.*, mk.kode_mk, mk.nm_mk, sk.sks_mk, sk.id_smt')
			->from('feeder_nilai sn')
			->join('feeder_kelas_kuliah sk', 'sk.id_kls = sn.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = sk.id_mk')
			->join('mahasiswa_pt mp', 'mp.id_reg_pd_feeder = sn.id_reg_pd')
			->where('mp.nipd', $nipd)
			->order_by('sk.id_smt')
			->get()->result();

		$transkrip = [];

		$sks_nilai_transfer = 0;
		$sks_indeks_nilai_transfer = 0;

		$sks_total = 0;
		$sks_indeks_total = 0;

		$i = 1;
		foreach ($nilai_transfer as $key => $value) {
			$sks_nilai_transfer = $sks_nilai_transfer + $value->sks_diakui;
			$sks_indeks_nilai_transfer = $sks_indeks_nilai_transfer + ($value->sks_diakui * $value->nilai_angka_diakui);

			$sks_total = $sks_total + $value->sks_diakui;
			$sks_indeks_total = $sks_indeks_total + ($value->sks_diakui * $value->nilai_angka_diakui);
			array_push($transkrip, [
				'nomor' => $i . '.',
				'kode_mk' => $value->kode_mk,
				'nm_mk' => $value->nm_mk,
				'id_smt' => 'Hasil Konversi',
				'sks_mk' => $value->sks_diakui,
				'nilai_huruf' => $value->nilai_huruf_diakui,
				'nilai_indeks' => $value->nilai_angka_diakui,
				'sks_indeks' => $value->sks_diakui * $value->nilai_angka_diakui
			]);
			$i++;
		}

		$ip_transfer = 0;
		if ($sks_nilai_transfer > 0) {
			$ip_transfer = round($sks_indeks_nilai_transfer / $sks_nilai_transfer, 2);
		}

		if ($sks_nilai_transfer > 0) {
			array_push($transkrip, [
				'nomor' => '',
				'kode_mk' => '',
				'nm_mk' => '',
				'id_smt' => '<b>TOTAL</b>',
				'sks_mk' => '<b>' . $sks_nilai_transfer . '</b>',
				'nilai_huruf' => '',
				'nilai_indeks' => '',
				'sks_indeks' => '<b>' . $sks_indeks_nilai_transfer . '</b>'
			], [
				'nomor' => '',
				'kode_mk' => '',
				'nm_mk' => '',
				'id_smt' => '<b>Indeks Prestasi Kumulatif Transfer</b>',
				'sks_mk' => '',
				'nilai_huruf' => '',
				'nilai_indeks' => '',
				'sks_indeks' => '<b>' . $ip_transfer . '</b>'
			]);
		}

		$my_id_smt = 0;
		$yout_id_smt = 0;
		$sks_semester = 0;
		$sks_indeks_semester = 0;


		$ip = 0;
		$ipk = 0;

		foreach ($nilai as $key => $value) {
			$your_id_smt = $value->id_smt;
			if ($my_id_smt == 0) {
				$my_id_smt = $value->id_smt;
			}


			if ($my_id_smt <> $your_id_smt) {
				array_push($transkrip, [
					'nomor' => '',
					'kode_mk' => '',
					'nm_mk' => '',
					'id_smt' => '<b>SUB TOTAL</b>',
					'sks_mk' => '<b>' . $sks_semester . '</b>',
					'nilai_huruf' => '',
					'nilai_indeks' => '',
					'sks_indeks' => '<b>' . $sks_indeks_semester . '</b>'
				], [
					'nomor' => '',
					'kode_mk' => '',
					'nm_mk' => '',
					'id_smt' => '<b>Indeks Prestasi Semester</b>',
					'sks_mk' => '',
					'nilai_huruf' => '',
					'nilai_indeks' => '',
					'sks_indeks' => '<b>' . round($sks_semester > 0 ? $sks_indeks_semester / $sks_semester : 0, 2) . '</b>'
				]);
				$sks_semester = 0;
				$sks_indeks_semester = 0;
			}

			$sks_semester = $sks_semester + $value->sks_mk;
			$sks_indeks_semester = $sks_indeks_semester + ($value->sks_mk * $value->nilai_indeks);
			$sks_total = $sks_total + $value->sks_mk;
			$sks_indeks_total = $sks_indeks_total + ($value->sks_mk * $value->nilai_indeks);

			array_push($transkrip, [
				'nomor' => $i . '.',
				'kode_mk' => $value->kode_mk,
				'nm_mk' => $value->nm_mk,
				'id_smt' => $value->id_smt,
				'sks_mk' => $value->sks_mk,
				'nilai_huruf' => $value->nilai_huruf,
				'nilai_indeks' => $value->nilai_indeks,
				'sks_indeks' => $value->sks_mk * $value->nilai_indeks
			]);

			$my_id_smt = $your_id_smt;
			$i++;
		}

		if ($sks_semester <> 0) {
			$ip = $sks_indeks_semester / $sks_semester;
		}

		if ($sks_total <> 0) {
			$ipk = round($sks_indeks_total / $sks_total, 2);
		}

		array_push($transkrip, [
			'nomor' => '',
			'kode_mk' => '',
			'nm_mk' => '',
			'id_smt' => '<b>SUB TOTAL</b>',
			'sks_mk' => '<b>' . $sks_semester . '</b>',
			'nilai_huruf' => '',
			'nilai_indeks' => '',
			'sks_indeks' => '<b>' . $sks_indeks_semester . '</b>'
		], [
			'nomor' => '',
			'kode_mk' => '',
			'nm_mk' => '',
			'id_smt' => '<b>Indeks Prestasi Semester</b>',
			'sks_mk' => '',
			'nilai_huruf' => '',
			'nilai_indeks' => '',
			'sks_indeks' => '<b>' . round($sks_semester > 0 ? $sks_indeks_semester / $sks_semester : 0, 2) . '</b>'
		], [
			'nomor' => '',
			'kode_mk' => '',
			'nm_mk' => '',
			'id_smt' => '<b>TOTAL</b>',
			'sks_mk' => '<b>' . $sks_total . '</b>',
			'nilai_huruf' => '',
			'nilai_indeks' => '',
			'sks_indeks' => '<b>' . $sks_indeks_total . '</b>'
		], [
			'nomor' => '',
			'kode_mk' => '',
			'nm_mk' => '',
			'id_smt' => '<b>Indeks Prestasi Kumulatif</b>',
			'sks_mk' => '',
			'nilai_huruf' => '',
			'nilai_indeks' => '',
			'sks_indeks' => '<b>' . $ipk . '</b>'
		]);


		$sks_semester = 0;
		$sks_indeks_semester = 0;

		$id_smt = $this->session->userdata("tahun_ajaran");
		$nm_semester = $this->db->where('id_smt', $this->setting->tahun_ajaran_future)->get('semester')->row();

		$output['halaman'] = 'Transkrip Nilai ';
		$output['sub_halaman'] = '';
		$output['master_page'] = 'ajax_pagination';
		$output['mahasiswa'] = $this->db->select('*')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where('mp.nipd', $nipd)
			->get()->row();

		$nm_ptk = $this->db->where('id_ptk', $this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->nm_ptk;
		$photo = $this->db->where('id_ptk', $this->session->userdata('id_ptk'))->get('siakad_dosen')->row()->photo;
		//$perkuliahan = $this->db->where(array('id_smt'=>$id_smt))->get('feeder_kelas_kuliah')->result();
		$output['nm_ptk'] = $nm_ptk;
		$output['photo'] = !empty($photo) ? $photo : 'no_photo.jpg';

		$output['content'] = 'transkrip/v_transkrip';
		$output['transkrip'] 			= $transkrip;

		$ion_user = $this->ion_auth->user()->row();
		$nipd = $ion_user->nim;
		$mahasiswa_pt = $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->row();
		$respon = $this->load->view('transkrip/v_transkrip', $output, true);
		echo $respon;
	}
}
