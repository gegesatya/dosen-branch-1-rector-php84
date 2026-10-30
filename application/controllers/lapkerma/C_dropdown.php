<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_dropdown extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Dropdown_model', 'Dropdown_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [17];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function get_dokumen()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_dokumen());
	}

	public function get_status()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_status());
	}

	public function get_instansi()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_instansi());
	}

	public function get_jenis_instansi()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_jenis_instansi());
	}

	public function get_bidang_kerjasama()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_bidang_kerjasama());
	}

	public function get_bidang_usaha()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_bidang_usaha());
	}

	public function get_bentuk_kegiatan()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_bentuk_kegiatan());
	}

	public function get_moa()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_moa());
	}

	public function get_mou()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_mou());
	}

	public function get_departement()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_departement());
	}

	public function get_prodi()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_prodi());
	}

	public function get_kategori_output_kegiatan()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_kategori_output_kegiatan());
	}

	public function get_kegiatan()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Dropdown_model->get_kegiatan());
	}
}
