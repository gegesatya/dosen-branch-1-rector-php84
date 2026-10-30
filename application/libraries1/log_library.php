<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_library {
	public function __construct()
	{
	    $this->CI =& get_instance();
	    $this->CI->load->helper('url');

	}

    public function save_log($insert_data)
    {
    	//$this->load->model('log_model','log');
    	$this->CI->load->model('log_model');
		$this->CI->log_model->save($insert_data);
    }

    public function log_history($log_name, $id_kls = '', $nipd= '', $file_bkd = ''){
		$this->save_log(array( 'log_name'=>$log_name, 
							   'log_desc'=>$this->log_name($this->CI->ion_auth->user()->row()->email, $log_name, $id_kls, $nipd, $file_bkd),
							   'log_date'=>date('Y-m-d H:i:s'),
							   'create_by'=>$this->CI->ion_auth->user()->row()->email
							   ));
	}

    public function log_name($user, $log_name, $id_kls = '', $nipd = '', $file_bkd = ''){
        if(!empty($nipd)){
            $mahasiswa = $this->CI->portal_db->select('mp.nipd, m.nm_pd')->from('mahasiswa m')
                                                 ->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
                                                 ->where('mp.nipd', $nipd)
                                                 ->get()->row();
        }else{
            $mahasiswa = "";
        }

    	if($log_name == 'login'){
    		return $user . ' melakukan login ke portal WBI';
    	}elseif($log_name == 'skema_add'){
    		return $user. ' melakukan penambahan record pada Skema Nilai';
    	}elseif($log_name == 'skema_edit'){

    		if($id_kls != ''){
    			$kelas_kuliah = $this->CI->portal_db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
    			return $user. ' melakukan edit data pada Skema Nilai Kelas '. $kelas_kuliah->nm_kls;
    		}else{
    			return $user. ' melakukan edit data pada Skema Nilai';
    		}    
            		
    	}elseif($log_name == 'nilai_add'){
    		return $user. ' melakukan penambahan record pada Nilai';
    	}elseif($log_name == 'nilai_edit'){

    		if($id_kls != ''){
    			$kelas_kuliah = $this->CI->portal_db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();

    			return $user. ' melakukan edit data Nilai Kelas '. $kelas_kuliah->nm_kls.', untuk mahasiswa dengan nama ' .$mahasiswa->nm_pd . '('.$mahasiswa->nipd.')';
    		}else{
    			return $user. ' melakukan edit data Nilai';
    		}

    	}elseif($log_name == 'logout'){
    		return $user. ' melakukan logout dari portal WBI';
    	}elseif($log_name == 'akses_menu'){

    		if($id_kls != ''){
    			$kelas_kuliah = $this->CI->portal_db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
    			return $user. ' mengakses controller '.$this->CI->uri->segment(1) . ' Kelas '. $kelas_kuliah->nm_kls;
    		}else{
    			return $user. ' mengakses controller '.$this->CI->uri->segment(1);
    		}
    		
    	}elseif($log_name == 'evaluasi_diri_add'){
    		$kelas_kuliah = $this->CI->portal_db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
    		return $user. ' melakukan penambahan record evalusi diri pada kelas '.$kelas_kuliah->nm_kls;
    	}elseif($log_name == 'evaluasi_diri_edit'){
    		$kelas_kuliah = $this->CI->portal_db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
    		return $user. ' melakukan edit data evalusi diri pada kelas '.$kelas_kuliah->nm_kls;
    	}elseif ($log_name == 'upload_berkas_bkd') {
            return $user. 'berhasil melakukan upload berkas data BKD '.$file_bkd;
        }elseif($log_name == 'perbaikan_krs'){
            return $user. ' melakukan perbaikan KRS untuk mahasiswa '.$mahasiswa->nm_pd.' ['.$nipd.'] pada tanggal '.date('Y-m-d H:i:s');
        }elseif($log_name == 'approval_krs'){
            return $user. ' melakukan Approval KRS untuk mahasiswa '.$mahasiswa->nm_pd.' ['.$nipd.'] pada tanggal '.date('Y-m-d H:i:s');
        }
    }
}