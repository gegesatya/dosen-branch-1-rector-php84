<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_nilai_sempro_model extends CI_Model
{

	public $table = 'siakad_skripsi_nilai_sempro';
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}


	public function get_all_nilai_sempro($id_ptk)
	{
		$this->db->select('s.*, pj.dosen_pembimbing_1, pj.dosen_pembimbing_2, pj.dosen_penguji_sempro_1, pj.dosen_penguji_sempro_2, pj.dosen_penguji_sempro_3, pj.judul_ditetapkan, pj.tanggal_sempro, pj.jam_sempro, pj.tempat_sempro, pj.hasil_sempro as pj_hasil_sempro, m.nm_pd, m.foto')
			->from('siakad_skripsi_nilai_sempro s')
			->where('s.id_penilai', $id_ptk)
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		return $this->db->get()->result();
	}

	public function get_by_id($id)
	{
		$this->db->select('s.*, m.foto, pj.judul_ditetapkan, m.nm_pd, mp.nipd')
			->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		$this->db->where('id_nilai_sempro', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update($this->table, $data, $where);

		return $this->db->affected_rows();
	}

	public function update_berita_acara_sempro($where, $data)
	{
		$this->db->trans_begin();

		$this->db->update($this->table, $data, $where);
		//$this->db->update('siakad_skripsi_pengajuan_judul', $data2, ['id_judul_skripsi' => $id_judul_skripsi]);

		$sempro_hasil = $this->input->post('sempro_hasil');
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');

		if ($sempro_hasil == 'tidak_layak') {
			$data_seminar_sidang = $this->db->where(['id_judul_skripsi' => $id_judul_skripsi])->get('siakad_skripsi_seminar_sidang')->row();

			$dt_insert = [
				'id_judul_skripsi' => $id_judul_skripsi,
				'nipd' => $data_seminar_sidang->nipd,
				'kategori_pengajuan' => $data_seminar_sidang->kategori_pengajuan,
				'tanggal_seminar' => $data_seminar_sidang->tanggal_seminar,
				'jam' => $data_seminar_sidang->jam,
				'tempat' => $data_seminar_sidang->tempat,
				'approval_p1' => $data_seminar_sidang->approval_p1,
				'tgl_approval_p1' => $data_seminar_sidang->tgl_approval_p1,
				'approval_p2' => $data_seminar_sidang->approval_p2,
				'tgl_approval_p2' => $data_seminar_sidang->tgl_approval_p2,
				'tanggal_undangan' => $data_seminar_sidang->tanggal_undangan,
				'nomor_undangan' => $data_seminar_sidang->nomor_undangan,
				'status_undangan' => $data_seminar_sidang->status_undangan,
				'link_dokumen' => $data_seminar_sidang->link_dokumen,
				'is_kaprodi' => $data_seminar_sidang->is_kaprodi,
				'tgl_is_kaprodi' => $data_seminar_sidang->tgl_is_kaprodi,
				'is_akademik' => $data_seminar_sidang->is_akademik,
				'tgl_is_akademik' => $data_seminar_sidang->tgl_is_akademik,
				'pesan_singkat' => $data_seminar_sidang->pesan_singkat,
				'pesan_singkat_kaprodi' => $data_seminar_sidang->pesan_singkat_kaprodi,
				'is_create_form_nilai' => $data_seminar_sidang->is_create_form_nilai,
				'is_create_form_nilai_semhas' => $data_seminar_sidang->is_create_form_nilai_semhas,
				'is_create_form_nilai_sidang' => $data_seminar_sidang->is_create_form_nilai_sidang,
				'penguji_sidang_1' => $data_seminar_sidang->penguji_sidang_1,
				'penguji_sidang_2' => $data_seminar_sidang->penguji_sidang_2,
				'penguji_sidang_3' => $data_seminar_sidang->penguji_sidang_3,
				'html_respon' => $data_seminar_sidang->html_respon,
				'html_table' => $data_seminar_sidang->html_table,
				'tgl_sk_penguji_sidang' => $data_seminar_sidang->tgl_sk_penguji_sidang,
				'no_sk_penguji_sidang' => $data_seminar_sidang->no_sk_penguji_sidang,
				'create_date' => $data_seminar_sidang->create_date,
				'create_by' => $data_seminar_sidang->create_by,
				'change_date' => $data_seminar_sidang->change_date,
				'change_by' => $data_seminar_sidang->change_by,
			];

			$data = $this->security->xss_clean($dt_insert);
			$this->db->insert('siakad_skripsi_seminar_sidang_tidak_layak', $dt_insert);

			$data_nilai = $this->db->where(['id_judul_skripsi' => $id_judul_skripsi])->get('siakad_skripsi_nilai_sempro')->result();

			foreach ($data_nilai as $key => $value) {
				$dt_insert_nilai = [
					'id_judul_skripsi' => $value->id_judul_skripsi,
					'nipd' => $value->nipd,
					'id_penilai' => $value->id_penilai,
					'nilai_komponen_a' => $value->nilai_komponen_a,
					'nilai_komponen_b' => $value->nilai_komponen_b,
					'nilai_komponen_c' => $value->nilai_komponen_c,
					'nilai_komponen_d' => $value->nilai_komponen_d,
					'nilai_komponen_e' => $value->nilai_komponen_e,
					'nilai_akhir' => $value->nilai_akhir,
					'catatan_judul' => $value->catatan_judul,
					'catatan_latar_belakang' => $value->catatan_latar_belakang,
					'catatan_rumusan_masalah' => $value->catatan_rumusan_masalah,
					'catatan_tujuan_penelitian' => $value->catatan_tujuan_penelitian,
					'catatan_tinjauan_pustaka' => $value->catatan_tinjauan_pustaka,
					'catatan_metode_penelitian' => $value->catatan_metode_penelitian,
					'catatan_daftar_pustaka' => $value->catatan_daftar_pustaka,
					'catatan_format_penulisan' => $value->catatan_format_penulisan,
					'catatan_usulan' => $value->catatan_usulan,
					'seminar_hari' => $value->seminar_hari,
					'seminar_tanggal' => $value->seminar_tanggal,
					'seminar_moderator' => $value->seminar_moderator,
					'seminar_nim_moderator' => $value->seminar_nim_moderator,
					'seminar_pembahas_1' => $value->seminar_pembahas_1,
					'seminar_nim_pembahas_1' => $value->seminar_nim_pembahas_1,
					'seminar_pembahas_2' => $value->seminar_pembahas_2,
					'seminar_nim_pembahas_2' => $value->seminar_nim_pembahas_2,
					'seminar_jumlah_mahasiswa' => $value->seminar_jumlah_mahasiswa,
					'seminar_tempat' => $value->seminar_tempat,
					'seminar_jam_mulai' => $value->seminar_jam_mulai,
					'seminar_jam_selesai' => $value->seminar_jam_selesai,
					'create_by' => $value->create_by,
					'create_date' => $value->create_date,
					'change_by' => $value->change_by,
					'change_date' => $value->change_date,
					'semhas_nilai_a' => $value->semhas_nilai_a,
					'semhas_nilai_b' => $value->semhas_nilai_b,
					'semhas_nilai_c' => $value->semhas_nilai_c,
					'semhas_nilai_d' => $value->semhas_nilai_d,
					'semhas_nilai_e' => $value->semhas_nilai_e,
					'semhas_nilai_f' => $value->semhas_nilai_f,
					'semhas_nilai_akhir' => $value->semhas_nilai_akhir,
					'semhas_catatan_judul' => $value->semhas_catatan_judul,
					'semhas_catatan_latar_belakang' => $value->semhas_catatan_latar_belakang,
					'semhas_catatan_rumusan_masalah' => $value->semhas_catatan_rumusan_masalah,
					'semhas_catatan_tujuan_penelitian' => $value->semhas_catatan_tujuan_penelitian,
					'semhas_catatan_tinjauan_pustaka' => $value->semhas_catatan_tinjauan_pustaka,
					'semhas_catatan_metode_penelitian' => $value->semhas_catatan_metode_penelitian,
					'semhas_catatan_daftar_pustaka' => $value->semhas_catatan_daftar_pustaka,
					'semhas_catatan_format_penulisan' => $value->semhas_catatan_format_penulisan,
					'semhas_desain' => $value->semhas_desain,
					'semhas_pengujian' => $value->semhas_pengujian,
					'semhas_kesimpulan' => $value->semhas_kesimpulan,
					'semhas_hari' => $value->semhas_hari,
					'semhas_tanggal' => $value->semhas_tanggal,
					'semhas_moderator' => $value->semhas_moderator,
					'semhas_nim_moderator' => $value->semhas_nim_moderator,
					'semhas_pembahas_1' => $value->semhas_pembahas_1,
					'semhas_nim_pembahas_1' => $value->semhas_nim_pembahas_1,
					'semhas_pembahas_2' => $value->semhas_pembahas_2,
					'semhas_nim_pembahas_2' => $value->semhas_nim_pembahas_2,
					'semhas_jumlah_mahasiswa' => $value->semhas_jumlah_mahasiswa,
					'semhas_tempat' => $value->semhas_tempat,
					'semhas_jam_mulai' => $value->semhas_jam_mulai,
					'semhas_jam_selesai' => $value->semhas_jam_selesai,
					'sidang_nilai_a' => $value->sidang_nilai_a,
					'sidang_nilai_b' => $value->sidang_nilai_b,
					'sidang_nilai_c' => $value->sidang_nilai_c,
					'sidang_nilai_d' => $value->sidang_nilai_d,
					'sidang_nilai_e' => $value->sidang_nilai_e,
					'sidang_nilai_akhir' => $value->sidang_nilai_akhir,
					'sidang_uraian_revisi' => $value->sidang_uraian_revisi,
					'sidang_hasil' => $value->sidang_hasil,
					'sempro_hasil' => $value->sempro_hasil,
					'semhas_hasil' => $value->semhas_hasil,
					'sidang_predikat' => $value->sidang_predikat,
					'sidang_kembali_mengulang' => $value->sidang_kembali_mengulang,
					'sidang_tanggal_mengulang' => $value->sidang_tanggal_mengulang,
					'sidang_hari' => $value->sidang_hari,
					'sidang_tanggal' => $value->sidang_tanggal,
					'sidang_nilai_ratarata' => $value->sidang_nilai_ratarata,
					'sidang_nilai_mutu' => $value->sidang_nilai_mutu,

				];

				$data = $this->security->xss_clean($dt_insert_nilai);
				$this->db->insert('siakad_skripsi_nilai_sempro_tidak_layak', $dt_insert_nilai);
			}

			$this->db->where(['id_judul_skripsi' => $id_judul_skripsi])->delete('siakad_skripsi_nilai_sempro');
			$this->db->where(['id_judul_skripsi' => $id_judul_skripsi])->delete('siakad_skripsi_seminar_sidang');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}

		//return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function get_mahasiswa_by_nim()
	{
		$nim = $this->input->post('nim');
		$data_nim = $this->db->where('nipd', $nim)->select('nm_pd')->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		return $data_nim;
	}
}
