<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dropdown_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_dokumen()
	{
		$result = $this->db->order_by('nm_dokumen')->get('simlapkerma_tbl_dokumen_kerjasama');
		return $result->result();
	}

	public function get_status()
	{
		$result = $this->db->order_by('id_status_kerjasama')->get('simlapkerma_tbl_status_kerjasama');
		return $result->result();
	}

	public function get_instansi()
	{
		$q = $this->db->order_by('nm_instansi')->from('simlapkerma_tbl_daftar_instansi')->get();
		return $q->result();
	}

	public function get_bidang_kerjasama()
	{
		$result = $this->db->order_by('id_bidang_kerjasama')->get('simlapkerma_tbl_bidang_kerjasama');
		return $result->result();
	}

	public function get_bidang_usaha()
	{
		$result = $this->db->order_by('id_bidang_usaha')->get('simlapkerma_tbl_bidang_usaha');
		return $result->result();
	}

	public function get_bentuk_kegiatan()
	{
		$result = $this->db->order_by('nm_bentuk_kegiatan')->get('simlapkerma_tbl_bentuk_kegiatan');
		return $result->result();
	}

	public function get_mou()
	{
		$result = $this->db->order_by('nomor_mou')->where('id_status_kerjasama', 1)->get('simlapkerma_tbl_daftar_mou');
		return $result->result();
	}

	public function get_moa_()
	{
		$result = $this->db->order_by('nomor_dokumen_moa')->get('simlapkerma_tbl_moa');
		return $result->result();
	}

	public function get_moa()
	{
		$result = $this->db->order_by('nomor_dokumen_moa')->where('status_moa', 1)->get('simlapkerma_tbl_moa');
		return $result->result();
	}

	public function get_jenis_instansi()
	{
		$q = $this->db->from('simlapkerma_tbl_instansi')
			->join('simlapkerma_tbl_sub_instansi', 'simlapkerma_tbl_instansi.id_jenis_instansi = simlapkerma_tbl_sub_instansi.id_instansi')
			->get();
		return $q->result();
	}

	public function get_departement()
	{
		$result = $this->db->order_by('nm_departement')->get('simlapkerma_tbl_departement');
		return $result->result();
	}

	public function get_prodi()
	{
		$result = $this->db->order_by('nm_prodi_english')->get('sms');
		return $result->result();
	}

	public function get_kategori_output_kegiatan()
	{
		$result = $this->db->order_by('nm_kategori_output')->get('simlapkerma_tbl_kategori_output');
		return $result->result();
	}

	public function get_kegiatan()
	{
		$result = $this->db->order_by('id_kegiatan')->get('simlapkerma_tbl_kegiatan');
		return $result->result();
	}
}
