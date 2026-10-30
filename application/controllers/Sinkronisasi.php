<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sinkronisasi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
        if (!$this->ion_auth->in_group($group)){
			//return show_error('Forbidden Access');
			redirect('auth/logout', 'refresh');
		}

		
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Kehadiran Perkuliahan';
        $output['judul_kecil'] 		= 'Politeknik WBI';
        $output['menu_aktif'] 		= 'm_kehadiran';
        $output['parent_2']			= 'active open';
        $output['content'] 			= 'sinkronisasi/sinkronisasi_view';
		$output['after_page'] 		= 'sinkronisasi/sinkronisasi_after_page';
		
		$id_kls = $this->db->where(['id_smt'=>20192])->get('feeder_kelas_kuliah')->result();

		$html_tabel = '';
		$html_tabel .= '<table class="table table-striped table-bordered table-hover" id="datatable_ajax">
						<thead>
						<tr role="row" class="heading">
							<td rowspan="2" style="text-align: center;vertical-align: middle;">IDKLS</td>
							<td colspan="15" class="text-center">Minggu Ke</td>
						</tr>
						<tr role="row" class="heading">
							<td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td>
							<td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td>
						</tr>
						</thead>
						<tbody>';

		foreach($id_kls as $l){
			if($this->session->flashdata('pesan') == $l->id_kls){
				$html_tabel .= '<tr class="success">
							<td>'.$l->id_kls.'</td>
							<td><a href="'.site_url('sinkronisasi/edit_data/'.$l->id_kls).'">Ed</a></td>
							
							</tr>';
			}else{
				$html_tabel .= '<tr>
							<td>'.$l->id_kls.'</td>
							<td><a href="'.site_url('sinkronisasi/edit_data/'.$l->id_kls).'">Ed</a></td>
							
							</tr>';
			}
			
		}

		$html_tabel .= '</tbody></table>';

		$output['html_tabel'] = $html_tabel;
        $this->load->view('master_page', $output);
	}

	public function edit_data($id_kls){
		for ($i=1; $i <= 15; $i++) { 
			$data_absen_lama = $this->db->from('siakad_absen_detail d')->join('siakad_absen_master m', 'd.id_absen_master = m.uniq')
			->where(['id_kls'=> $id_kls, 'minggu_ke'=>$i])
			->get();

			if($data_absen_lama->num_rows() > 0){
				foreach ($data_absen_lama->result() as $key => $value) {
					$this->db->where(['id_kls'=>$id_kls, 'id_reg_pd'=>$value->id_reg_pd])
					->update('siakad_absen_detail_baru', ['meet_'.$i => $value->status_kehadiran]);
				}
			}
		}
		$this->session->set_flashdata('pesan', $id_kls);
		redirect($_SERVER['HTTP_REFERER']);
		
	}
	


}
