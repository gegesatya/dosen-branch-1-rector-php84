<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * C_jadwal - Smart Dispatcher Controller
 *
 * Mengarahkan eksekusi secara transparan ke:
 * - C_jadwal_legacy (Format RPS Lama) saat mode = 'legacy' / default
 * - C_jadwal_obe    (Format RPS OBE) saat mode = 'obe'
 *
 * Sesuai pengaturan di Menu Setting -> Pengaturan RPS & Maintenance.
 */
class C_jadwal extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Maintenance_model');
	}

	public function _remap($method, $params = [])
	{
		$mode = $this->Maintenance_model->get_rps_menu_mode();
		$target_class = ($mode === 'obe') ? 'C_jadwal_obe' : 'C_jadwal_legacy';

		if (!class_exists($target_class, false)) {
			require_once APPPATH . 'controllers/' . $target_class . '.php';
		}

		$controller_instance = new $target_class();

		if (method_exists($controller_instance, $method)) {
			return call_user_func_array([$controller_instance, $method], $params);
		} else {
			show_404();
		}
	}
}
