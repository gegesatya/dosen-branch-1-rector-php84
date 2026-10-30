<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_arsip_nilai extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Arsip_nilai_model', 'Arsip_nilai_model');
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
		$output['judul_besar'] 		= 'Arsip Nilai ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_arsip_nilai';
		$output['parent_3']			= 'active open';
		$output['content'] 			= 'arsip_nilai/arsip_nilai_view';
		$output['after_page'] 		= 'arsip_nilai/arsip_nilai_after_page';
		//$output['dd_kelas_kuliah'] = $this->Arsip_nilai_model->f_dropdown('arsip_kelas_kuliah', 'id_kls', 'nm_kls');

		$this->load->view('master_page', $output);
	}



	public function ajax_list()
	{
		$list = $this->Arsip_nilai_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_arsip_nilai/cetak_pdf_nilai') . '/' . $n->id_kls . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Nilai</a>';
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
			"recordsTotal" => $this->Arsip_nilai_model->count_all(),
			"recordsFiltered" => $this->Arsip_nilai_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Arsip_nilai_model->get_by_id($id);
		$data->tanggal_berita_acara = ($data->tanggal_berita_acara == '0000-00-00') ? '' : $data->tanggal_berita_acara;
		$id_kls = $data->id_kls;
		$kelas = $this->db->where('id_kls', $id_kls)->get('arsip_kelas_kuliah')->row();

		$data->nm_kls = $kelas->nm_kls;
		$id_mk = $kelas->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('arsip_mata_kuliah')->row()->nm_mk;
		$data->nm_mk = $nm_mk;

		$data->isi_berita_acara = str_ireplace(["<br />"], "\r\n", $data->isi_berita_acara);
		$data->bacaan_materi = str_ireplace(["<br />"], "\r\n", $data->bacaan_materi);
		echo json_encode($data);
	}

	public function cetak_pdf_nilai($id_kls = null, $id_smt = null)
	{
		if ($id_kls <> null)
			$metode_nilai = $this->db->where('id_kls', $id_kls)->get('arsip_kelas_kuliah')->row()->metode_nilai;
		$data_nilai = $this->db->select('mp.nipd, m.nm_pd, n.*')
			->from('arsip_nilai n')
			->join('arsip_kelas_kuliah sk', 'sk.id_kls = n.id_kls')
			->join('mahasiswa_pt mp', 'mp.id_reg_pd = n.id_reg_pd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where('n.id_kls', $id_kls)
			->order_by('right(mp.nipd,4)')
			->get()->result();
		if ($metode_nilai == 1) {

			$data_kuliah = $this->db->select('mk.nm_mk, d.nm_ptk, sms.nm_lemb, sem.nm_smt, 
													sk.tugas, sk.partisipasi, sk.uts, sk.uas')
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
			$output['tugas'] = $data_kuliah->tugas;
			$output['partisipasi'] = $data_kuliah->partisipasi;
			$output['uts'] = $data_kuliah->uts;
			$output['uas'] = $data_kuliah->uas;
			$output['data_nilai'] = $data_nilai;
		} elseif ($metode_nilai == 2) {

			$data_kuliah = $this->db->select('mk.nm_mk, d.nm_ptk, sms.nm_lemb, sem.nm_smt, sk.sikap, sk.pengetahuan, sk.pengetahuan_kuis, sk.pengetahuan_uts, sk.pengetahuan_uas, sk.keterampilan, sk.keterampilan_tugas, sk.keterampilan_unjuk_kinerja, sk.keterampilan_presentasi')
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
			$output['sikap'] = $data_kuliah->sikap;
			$output['pengetahuan'] = $data_kuliah->pengetahuan;
			$output['pengetahuan_kuis'] = $data_kuliah->pengetahuan_kuis;
			$output['pengetahuan_uts'] = $data_kuliah->pengetahuan_uts;
			$output['pengetahuan_uas'] = $data_kuliah->pengetahuan_uas;
			$output['keterampilan'] = $data_kuliah->keterampilan;
			$output['keterampilan_tugas'] = $data_kuliah->keterampilan_tugas;
			$output['keterampilan_unjuk_kinerja'] = $data_kuliah->keterampilan_unjuk_kinerja;
			$output['keterampilan_presentasi'] = $data_kuliah->keterampilan_presentasi;
			$output['data_nilai'] = $data_nilai;
		} elseif ($metode_nilai == 3) {


			$data_kuliah = $this->db->select('mk.nm_mk, d.nm_ptk, sms.nm_lemb, sem.nm_smt, 
													sk.penilaian_lapangan, sk.penilaian_penulisan, sk.pengujian_laporan')
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
			$output['penilaian_lapangan'] = $data_kuliah->penilaian_lapangan;
			$output['penilaian_penulisan'] = $data_kuliah->penilaian_penulisan;
			$output['pengujian_laporan'] = $data_kuliah->pengujian_laporan;
			$output['data_nilai'] = $data_nilai;
		} elseif ($metode_nilai == 4) {
			$data_kuliah = $this->db->select('mk.nm_mk, 
													d.nm_ptk, sms.nm_lemb, sem.nm_smt, 
													sk.bobot_sikap,
													sk.bobot_pengetahuan,
													sk.bobot_pengetahuan_quiz,
													sk.bobot_pengetahuan_uts,
													sk.bobot_pengetahuan_uas,
													sk.bobot_pengetahuan_tugas_terstruktur,
													sk.bobot_keterampilan,
													sk.bobot_keterampilan_unjuk_kinerja,
													sk.bobot_keterampilan_proyek,
													sk.bobot_keterampilan_portofolio,
													sk.bobot_keterampilan_produk,
													sk.bobot_keterampilan_presentasi')
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
			$output['bobot_sikap'] = $data_kuliah->bobot_sikap;
			$output['bobot_pengetahuan'] = $data_kuliah->bobot_pengetahuan;
			$output['bobot_pengetahuan_quiz'] = $data_kuliah->bobot_pengetahuan_quiz;
			$output['bobot_pengetahuan_uts'] = $data_kuliah->bobot_pengetahuan_uts;
			$output['bobot_pengetahuan_uas'] = $data_kuliah->bobot_pengetahuan_uas;
			$output['bobot_pengetahuan_tugas_terstruktur'] = $data_kuliah->bobot_pengetahuan_tugas_terstruktur;
			$output['bobot_keterampilan'] = $data_kuliah->bobot_keterampilan;
			$output['bobot_keterampilan_unjuk_kinerja'] = $data_kuliah->bobot_keterampilan_unjuk_kinerja;
			$output['bobot_keterampilan_proyek'] = $data_kuliah->bobot_keterampilan_proyek;
			$output['bobot_keterampilan_portofolio'] = $data_kuliah->bobot_keterampilan_portofolio;
			$output['bobot_keterampilan_produk'] = $data_kuliah->bobot_keterampilan_produk;
			$output['bobot_keterampilan_presentasi'] = $data_kuliah->bobot_keterampilan_presentasi;

			$output['data_nilai'] = $data_nilai;
		}


		$output['metode_nilai'] = $metode_nilai;
		$html = $this->load->view('cetak/pdf_nilai_matakuliah', $output, true);
		$pdfFilePath  = 'Nilai ' . $data_kuliah->nm_mk . ' Semester ' . $data_kuliah->nm_smt . '.pdf';
		$mpdf = new \Mpdf\Mpdf();

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}
}
