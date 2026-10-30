<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_lihat_cv extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
	}


	public function download_cv($nipd)
	{
		$mahasiswa_pt = $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->row();
		$output['data_pribadi'] = $this->db->where('nipd', $nipd)
			->get('simlapkerma_cv')->row();

		$nm_lemb = $this->db->where('id_sms', $mahasiswa_pt->id_sms)->get('sms')->row()->nm_lemb;

		$output['edukasi'] = $this->db->where('nipd', $nipd)->get('simlapkerma_cv_riwayat_akademik');
		$output['sertif'] = $this->db->where('nipd', $nipd)->get('simlapkerma_cv_riwayat_pelatihan');
		$output['prestasi'] = $this->db->where('nipd', $nipd)->get('simlapkerma_cv_prestasi');
		$output['organisasi'] = $this->db->where('nipd', $nipd)->get('simlapkerma_cv_pengalaman_organisasi');
		$output['nm_lemb'] = $nm_lemb;

		$data_cc = $this->db->where('nipd', $nipd)->get('simlapkerma_cv')->num_rows();

		$output['data_cc'] = $data_cc;
		$mahasiswa_reload = $this->db->where('id_pd', $mahasiswa_pt->id_pd)->get('mahasiswa')->row();
		$foto = !empty($mahasiswa_reload->foto) ? $mahasiswa_reload->foto : 'no_image.png';
		$output['src_image'] = 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $foto;

		$html = $this->load->view('cetak/pdf_cv_mahasiswa', $output);
	}
}
