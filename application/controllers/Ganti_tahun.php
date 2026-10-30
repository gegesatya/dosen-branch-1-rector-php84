<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ganti_tahun extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		$group = [1, 2];
		if (!$this->ion_auth->in_group($group)) {
			$this->session->set_flashdata('message', 'Anda harus Admin atau Dosen untuk mengakses aplikasi ini.');
			redirect('auth/login', 'refresh');
		}
	}

	public function index()
	{

		$output['content'] 				= 'ganti_jurusan';
		$output['halaman'] 			= 'Pilih Tahun Ajaran';
		$output['sub_halaman'] 			= '';
		$output['menu_aktif'] 		= 'm_ganti_tahun';
		$output['parent_3']			= 'active open';
		//$output['master_page'] 			= 'detail';
		$output['tahun'] 				= $this->dd_tahun_ajaran();
		$this->load->view('master_page', $output);
	}

	public function simpan($post_tahun_ajaran)
	{
		$id_smt = $post_tahun_ajaran;
		$id_ptk = $this->session->userdata('id_ptk');
		$nm_smt = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;

		$perkuliahan = $this->db->select('sk.*, mk.nm_mk, sd.nm_ptk, h.nama_hari, ru.nama_ruang')
			->from('feeder_kelas_kuliah sk')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = sk.id_mk')
			->join('siakad_dosen sd', 'sd.id_ptk = sk.id_ptk')
			->join('siakad_hari h', 'h.uniq = sk.hari')
			->join('siakad_ruang ru', 'ru.uniq = sk.ruang')
			->where(['sk.id_smt' => $id_smt])
			->where(['sk.id_ptk' => $id_ptk])
			->order_by('sk.hari')
			->order_by('sk.jam_mulai')
			->order_by('mk.nm_mk')
			->get()->result();

		$data = ['tahun_ajaran' => $post_tahun_ajaran, 'nm_smt' => $nm_smt, 'perkuliahan' => $perkuliahan];
		$this->session->set_userdata($data);

		$segment = $this->session->userdata('address');
		redirect('/' . $segment, 'refresh');
	}

	public function dd_tahun_ajaran()
	{

		$this->db->order_by('id_smt', 'desc');
		$result = $this->db->where('a_periode_aktif', 1)->get('semester');

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result() as $row) {
				// tentukan value (sebelah kiri) dan labelnya (sebelah kanan)
				$dd[$row->id_smt] = $row->nm_smt;
			}
		}
		return $dd;
	}
}
