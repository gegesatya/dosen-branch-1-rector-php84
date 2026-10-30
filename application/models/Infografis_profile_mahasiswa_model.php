<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Infografis_profile_mahasiswa_model extends CI_Model
{
	// =========================================================
	// OPTIMIZED STATS QUERIES
	// =========================================================

	public function get_global_mhs_stats()
	{
		$this->db->select("
			COUNT(*) as total_mhs,
			SUM(CASE WHEN jk = 'L' THEN 1 ELSE 0 END) as total_laki,
			SUM(CASE WHEN jk = 'P' THEN 1 ELSE 0 END) as total_perempuan
		");
		$this->db->where('jk !=', '');
		return $this->db->get('mahasiswa')->row();
	}

	public function get_global_pt_stats()
	{
		$this->db->select("
			COUNT(*) as mhs_total,
			SUM(CASE WHEN year(tgl_masuk_sp) = 2025 THEN 1 ELSE 0 END) as mhs_baru
		");
		return $this->db->get('mahasiswa_pt')->row();
	}

	public function get_alumni_stats()
	{
		$this->db->select("
			COUNT(*) as mhs_do_lulus,
			SUM(CASE WHEN tanggal_keluar > '2024-09-01' AND tanggal_keluar < '2025-09-30' AND nama_jenis_keluar = 'Lulus' THEN 1 ELSE 0 END) as jumlah_lulusan,
			MIN(CASE WHEN tanggal_keluar > '2024-09-01' AND tanggal_keluar < '2025-09-30' AND nama_jenis_keluar = 'Lulus' THEN ipk END) as ipk_min,
			MAX(CASE WHEN tanggal_keluar > '2024-09-01' AND tanggal_keluar < '2025-09-30' AND nama_jenis_keluar = 'Lulus' THEN ipk END) as ipk_max,
			AVG(CASE WHEN tanggal_keluar > '2024-09-01' AND tanggal_keluar < '2025-09-30' AND nama_jenis_keluar = 'Lulus' THEN ipk END) as ipk_lulus
		");
		return $this->db->get('feeder_lulus_do')->row();
	}

	public function get_ipk_semester_stats()
	{
		$this->db->select("
			AVG(CASE WHEN id_smt = 20241 THEN ipk END) as rerata_ipk_ganjil,
			AVG(CASE WHEN id_smt = 20242 THEN ipk END) as rerata_ipk_genap
		");
		$this->db->where_in('id_smt', [20241, 20242]);
		return $this->db->get('kuliah_mahasiswa')->row();
	}

	public function get_prodi_demographics()
	{
		$this->db->select("
			mp.id_sms,
			COUNT(m.id_pd) as total,
			SUM(CASE WHEN m.jk = 'L' THEN 1 ELSE 0 END) as laki,
			SUM(CASE WHEN m.jk = 'P' THEN 1 ELSE 0 END) as perempuan
		");
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'inner');
		$this->db->group_by('mp.id_sms');
		
		$result = $this->db->get()->result();
		$stats = [];
		foreach ($result as $row) {
			$stats[$row->id_sms] = $row;
		}
		return $stats;
	}

	public function get_prodi_alumni()
	{
		$this->db->select("id_sms, COUNT(*) as alumni");
		$this->db->where('id_jns_keluar', 1);
		$this->db->group_by('id_sms');
		
		$result = $this->db->get('feeder_lulus_do')->result();
		$stats = [];
		foreach ($result as $row) {
			$stats[$row->id_sms] = $row->alumni;
		}
		return $stats;
	}

	public function get_pekerjaan_ayah_stats()
	{
		$this->db->select("
			p.nm_pekerjaan as nama_pekerjaan,
			SUM(CASE WHEN mp.id_sms = '".MPI."' THEN 1 ELSE 0 END) as mpi,
			SUM(CASE WHEN mp.id_sms = '".AP."' THEN 1 ELSE 0 END) as ap,
			SUM(CASE WHEN mp.id_sms = '".AH."' THEN 1 ELSE 0 END) as ah,
			SUM(CASE WHEN mp.id_sms = '".PKA."' THEN 1 ELSE 0 END) as pka,
			SUM(CASE WHEN mp.id_sms = '".TRPL."' THEN 1 ELSE 0 END) as trpl,
			COUNT(m.id_pd) as total
		");
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'inner');
		$this->db->join('pekerjaan p', 'm.id_pekerjaan_ayah = p.id_pekerjaan', 'left');
		$this->db->where('(m.id_pekerjaan_ayah != 98 OR m.id_pekerjaan_ayah IS NULL)');
		$this->db->group_by('p.nm_pekerjaan');
		$result = $this->db->get()->result();

		$grouped = [];
		foreach ($result as $row) {
			$name = empty($row->nama_pekerjaan) || $row->nama_pekerjaan == 'Tidak Diketahui' ? 'Lainnya' : $row->nama_pekerjaan;
			if (!isset($grouped[$name])) {
				$row->nama_pekerjaan = $name;
				$grouped[$name] = $row;
			} else {
				$grouped[$name]->mpi += $row->mpi;
				$grouped[$name]->ap += $row->ap;
				$grouped[$name]->ah += $row->ah;
				$grouped[$name]->pka += $row->pka;
				$grouped[$name]->trpl += $row->trpl;
				$grouped[$name]->total += $row->total;
			}
		}

		usort($grouped, fn($a, $b) => $b->total <=> $a->total);

		return array_values($grouped);
	}

	public function get_pekerjaan_ibu_stats()
	{
		$this->db->select("
			p.nm_pekerjaan as nama_pekerjaan,
			SUM(CASE WHEN mp.id_sms = '".MPI."' THEN 1 ELSE 0 END) as mpi,
			SUM(CASE WHEN mp.id_sms = '".AP."' THEN 1 ELSE 0 END) as ap,
			SUM(CASE WHEN mp.id_sms = '".AH."' THEN 1 ELSE 0 END) as ah,
			SUM(CASE WHEN mp.id_sms = '".PKA."' THEN 1 ELSE 0 END) as pka,
			SUM(CASE WHEN mp.id_sms = '".TRPL."' THEN 1 ELSE 0 END) as trpl,
			COUNT(m.id_pd) as total
		");
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'inner');
		$this->db->join('pekerjaan p', 'm.id_pekerjaan_ibu = p.id_pekerjaan', 'left');
		$this->db->where('(m.id_pekerjaan_ibu != 98 OR m.id_pekerjaan_ibu IS NULL)');
		$this->db->group_by('p.nm_pekerjaan');
		$result = $this->db->get()->result();

		$grouped = [];
		foreach ($result as $row) {
			$name = empty($row->nama_pekerjaan) || $row->nama_pekerjaan == 'Tidak Diketahui' ? 'Lainnya' : $row->nama_pekerjaan;
			if (!isset($grouped[$name])) {
				$row->nama_pekerjaan = $name;
				$grouped[$name] = $row;
			} else {
				$grouped[$name]->mpi += $row->mpi;
				$grouped[$name]->ap += $row->ap;
				$grouped[$name]->ah += $row->ah;
				$grouped[$name]->pka += $row->pka;
				$grouped[$name]->trpl += $row->trpl;
				$grouped[$name]->total += $row->total;
			}
		}

		usort($grouped, fn($a, $b) => $b->total <=> $a->total);

		return array_values($grouped);
	}

	public function get_status_orang_tua_meninggal_stats()
	{
		// Menggunakan agregasi SQL langsung untuk memastikan tiap kategori bersifat mutually exclusive:
		// - Ayah Meninggal   : ayah=98 DAN ibu != 98
		// - Ibu Meninggal    : ibu=98  DAN ayah != 98
		// - Keduanya Meninggal: ayah=98 DAN ibu=98
		$this->db->select("
			mp.id_sms,
			SUM(CASE WHEN m.id_pekerjaan_ayah = 98 AND (m.id_pekerjaan_ibu IS NULL OR m.id_pekerjaan_ibu != 98) THEN 1 ELSE 0 END) as ayah_meninggal,
			SUM(CASE WHEN m.id_pekerjaan_ibu = 98 AND (m.id_pekerjaan_ayah IS NULL OR m.id_pekerjaan_ayah != 98) THEN 1 ELSE 0 END) as ibu_meninggal,
			SUM(CASE WHEN m.id_pekerjaan_ayah = 98 AND m.id_pekerjaan_ibu = 98 THEN 1 ELSE 0 END) as keduanya_meninggal
		");
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'inner');
		$this->db->where('(m.id_pekerjaan_ayah = 98 OR m.id_pekerjaan_ibu = 98)');
		$this->db->group_by('mp.id_sms');
		$result = $this->db->get()->result();

		$stats = [
			'Ayah Meninggal'    => (object)['nama_pekerjaan' => 'Ayah Meninggal',    'mpi' => 0, 'ap' => 0, 'ah' => 0, 'pka' => 0, 'trpl' => 0, 'total' => 0],
			'Ibu Meninggal'     => (object)['nama_pekerjaan' => 'Ibu Meninggal',     'mpi' => 0, 'ap' => 0, 'ah' => 0, 'pka' => 0, 'trpl' => 0, 'total' => 0],
			'Keduanya Meninggal'=> (object)['nama_pekerjaan' => 'Keduanya Meninggal','mpi' => 0, 'ap' => 0, 'ah' => 0, 'pka' => 0, 'trpl' => 0, 'total' => 0],
		];

		$prodi_key_map = [
			MPI  => 'mpi',
			AP   => 'ap',
			AH   => 'ah',
			PKA  => 'pka',
			TRPL => 'trpl',
		];

		foreach ($result as $row) {
			if (!isset($prodi_key_map[$row->id_sms])) continue;
			$key = $prodi_key_map[$row->id_sms];

			$stats['Ayah Meninggal']->$key    += (int)$row->ayah_meninggal;
			$stats['Ayah Meninggal']->total    += (int)$row->ayah_meninggal;

			$stats['Ibu Meninggal']->$key     += (int)$row->ibu_meninggal;
			$stats['Ibu Meninggal']->total     += (int)$row->ibu_meninggal;

			$stats['Keduanya Meninggal']->$key += (int)$row->keduanya_meninggal;
			$stats['Keduanya Meninggal']->total += (int)$row->keduanya_meninggal;
		}

		return array_values($stats);
	}

	public function get_penghasilan_ayah_stats()
	{
		$this->db->select("
			MAX(p.id_penghasilan) as id_penghasilan,
			p.nm_penghasilan as nama_penghasilan,
			SUM(CASE WHEN mp.id_sms = '".MPI."' THEN 1 ELSE 0 END) as mpi,
			SUM(CASE WHEN mp.id_sms = '".AP."' THEN 1 ELSE 0 END) as ap,
			SUM(CASE WHEN mp.id_sms = '".AH."' THEN 1 ELSE 0 END) as ah,
			SUM(CASE WHEN mp.id_sms = '".PKA."' THEN 1 ELSE 0 END) as pka,
			SUM(CASE WHEN mp.id_sms = '".TRPL."' THEN 1 ELSE 0 END) as trpl,
			COUNT(m.id_pd) as total
		");
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'inner');
		$this->db->join('penghasilan p', 'm.id_penghasilan_ayah = p.id_penghasilan', 'left');
		$this->db->group_by('p.nm_penghasilan');
		$result = $this->db->get()->result();

		$grouped = [];
		foreach ($result as $row) {
			$name = empty(trim((string)$row->nama_penghasilan)) || $row->nama_penghasilan == '0' || $row->nama_penghasilan == 'Tidak Diketahui' ? 'Tidak Diisi' : $row->nama_penghasilan;
			if (!isset($grouped[$name])) {
				$row->nama_penghasilan = $name;
				if ($name == 'Tidak Diisi') $row->id_penghasilan = -1;
				$grouped[$name] = $row;
			} else {
				$grouped[$name]->mpi += $row->mpi;
				$grouped[$name]->ap += $row->ap;
				$grouped[$name]->ah += $row->ah;
				$grouped[$name]->pka += $row->pka;
				$grouped[$name]->trpl += $row->trpl;
				$grouped[$name]->total += $row->total;
			}
		}

		usort($grouped, fn($a, $b) => $b->id_penghasilan <=> $a->id_penghasilan);

		return array_values($grouped);
	}

	public function get_penghasilan_ibu_stats()
	{
		$this->db->select("
			MAX(p.id_penghasilan) as id_penghasilan,
			p.nm_penghasilan as nama_penghasilan,
			SUM(CASE WHEN mp.id_sms = '".MPI."' THEN 1 ELSE 0 END) as mpi,
			SUM(CASE WHEN mp.id_sms = '".AP."' THEN 1 ELSE 0 END) as ap,
			SUM(CASE WHEN mp.id_sms = '".AH."' THEN 1 ELSE 0 END) as ah,
			SUM(CASE WHEN mp.id_sms = '".PKA."' THEN 1 ELSE 0 END) as pka,
			SUM(CASE WHEN mp.id_sms = '".TRPL."' THEN 1 ELSE 0 END) as trpl,
			COUNT(m.id_pd) as total
		");
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'inner');
		$this->db->join('penghasilan p', 'm.id_penghasilan_ibu = p.id_penghasilan', 'left');
		$this->db->group_by('p.nm_penghasilan');
		$result = $this->db->get()->result();

		$grouped = [];
		foreach ($result as $row) {
			$name = empty(trim((string)$row->nama_penghasilan)) || $row->nama_penghasilan == '0' || $row->nama_penghasilan == 'Tidak Diketahui' ? 'Tidak Diisi' : $row->nama_penghasilan;
			if (!isset($grouped[$name])) {
				$row->nama_penghasilan = $name;
				if ($name == 'Tidak Diisi') $row->id_penghasilan = -1;
				$grouped[$name] = $row;
			} else {
				$grouped[$name]->mpi += $row->mpi;
				$grouped[$name]->ap += $row->ap;
				$grouped[$name]->ah += $row->ah;
				$grouped[$name]->pka += $row->pka;
				$grouped[$name]->trpl += $row->trpl;
				$grouped[$name]->total += $row->total;
			}
		}

		usort($grouped, fn($a, $b) => $b->id_penghasilan <=> $a->id_penghasilan);

		return array_values($grouped);
	}

	public function get_mhs_angkatan_prodi_stats()
	{
		$this->db->select("
			YEAR(mp.tgl_masuk_sp) as angkatan,
			SUM(CASE WHEN mp.id_sms = '".MPI."' THEN 1 ELSE 0 END) as mpi_terdaftar,
			SUM(CASE WHEN mp.id_sms = '".MPI."' AND mp.id_jns_keluar IS NULL THEN 1 ELSE 0 END) as mpi_aktif,
			SUM(CASE WHEN mp.id_sms = '".AP."' THEN 1 ELSE 0 END) as ap_terdaftar,
			SUM(CASE WHEN mp.id_sms = '".AP."' AND mp.id_jns_keluar IS NULL THEN 1 ELSE 0 END) as ap_aktif,
			SUM(CASE WHEN mp.id_sms = '".AH."' THEN 1 ELSE 0 END) as ah_terdaftar,
			SUM(CASE WHEN mp.id_sms = '".AH."' AND mp.id_jns_keluar IS NULL THEN 1 ELSE 0 END) as ah_aktif,
			SUM(CASE WHEN mp.id_sms = '".PKA."' THEN 1 ELSE 0 END) as pka_terdaftar,
			SUM(CASE WHEN mp.id_sms = '".PKA."' AND mp.id_jns_keluar IS NULL THEN 1 ELSE 0 END) as pka_aktif,
			SUM(CASE WHEN mp.id_sms = '".TRPL."' THEN 1 ELSE 0 END) as trpl_terdaftar,
			SUM(CASE WHEN mp.id_sms = '".TRPL."' AND mp.id_jns_keluar IS NULL THEN 1 ELSE 0 END) as trpl_aktif,
			COUNT(mp.id_pd) as total_terdaftar,
			SUM(CASE WHEN mp.id_jns_keluar IS NULL THEN 1 ELSE 0 END) as total_aktif
		");
		$this->db->from('mahasiswa_pt mp');
		$this->db->where("YEAR(mp.tgl_masuk_sp) >=", 2015);
		$this->db->where("YEAR(mp.tgl_masuk_sp) <=", 2025);
		$this->db->group_by('YEAR(mp.tgl_masuk_sp)');
		$this->db->order_by('angkatan', 'ASC');
		$result = $this->db->get()->result();

		$stats = [];
		foreach ($result as $row) {
			$stats[$row->angkatan] = $row;
		}

		$final_stats = [];
		for ($year = 2015; $year <= 2025; $year++) {
			if (isset($stats[$year])) {
				$final_stats[$year] = $stats[$year];
			} else {
				$final_stats[$year] = (object)[
					'angkatan' => $year,
					'mpi_terdaftar' => 0, 'mpi_aktif' => 0,
					'ap_terdaftar' => 0, 'ap_aktif' => 0,
					'ah_terdaftar' => 0, 'ah_aktif' => 0,
					'pka_terdaftar' => 0, 'pka_aktif' => 0,
					'trpl_terdaftar' => 0, 'trpl_aktif' => 0,
					'total_terdaftar' => 0, 'total_aktif' => 0
				];
			}
		}

		return $final_stats;
	}

	public function get_alumni_data()
	{
		$sql = "
			SELECT mp.nipd, m.nm_pd, s.nm_lemb as nm_prodi,
				COALESCE(NULLIF(YEAR(mp.tgl_masuk_sp), 0), CONCAT('20', LEFT(mp.nipd, 2))) as tahun_masuk, 
				YEAR(mp.tgl_keluar) as tahun_keluar
			FROM mahasiswa_pt mp
			JOIN mahasiswa m ON m.id_pd = mp.id_pd
			JOIN sms s ON s.id_sms = mp.id_sms
			WHERE mp.id_jns_keluar = '1'
			ORDER BY s.nm_lemb ASC, mp.nipd ASC, tahun_masuk ASC, tahun_keluar ASC
		";
		return $this->db->query($sql)->result();
	}

	public function get_all_asal_mahasiswa()
	{
		return $this->db->select('count(nm_wil) as total, nm_wil')
				->from('mahasiswa m')
				->join('wilayah w', 'm.id_kabupaten = w.id_wil')
				->group_by('nm_wil')
				->order_by('total', 'DESC')
				->get()->result();
	}

	public function get_asal_mahasiswa_per_tahun()
	{
		$this->db->select("YEAR(mp.tgl_masuk_sp) as tahun, w.nm_wil, COUNT(*) as total");
		$this->db->from("mahasiswa m");
		$this->db->join("mahasiswa_pt mp", "mp.id_pd = m.id_pd", "inner");
		$this->db->join("wilayah w", "m.id_kabupaten = w.id_wil");
		$this->db->where("YEAR(mp.tgl_masuk_sp) >", 0);
		$this->db->group_by(["YEAR(mp.tgl_masuk_sp)", "w.nm_wil"]);
		$this->db->order_by("tahun", "DESC");
		$this->db->order_by("total", "DESC");
		
		$query = $this->db->get()->result();
		
		$data_per_tahun = [];
		foreach ($query as $row) {
			$data_per_tahun[$row->tahun][] = $row;
		}
		
		return $data_per_tahun;
	}
}
