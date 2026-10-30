<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edom_model extends CI_Model {

    public $table = 'feeder_kelas_kuliah';
    public $column = ['nm_mk', 'siakad_dosen.nm_ptk']; //set column field database for order and search
    public $order = ['nm_kls' => 'asc']; // default order 

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
    }

    private function _get_datatables_query()
    {
        $this->db->select('feeder_kelas_kuliah.*, feeder_mata_kuliah.id_mk, feeder_mata_kuliah.kode_mk, feeder_mata_kuliah.nm_mk, siakad_dosen.nm_ptk');
        $this->db->from($this->table);
        $this->db->join('feeder_mata_kuliah', 'feeder_mata_kuliah.id_mk = feeder_kelas_kuliah.id_mk');
        $this->db->join('siakad_dosen', 'siakad_dosen.id_ptk = feeder_kelas_kuliah.id_ptk');
        $this->db->where('feeder_kelas_kuliah.id_smt', $this->session->userdata('tahun_ajaran'));
        $this->db->where('feeder_kelas_kuliah.id_sms', $this->session->userdata('prodi'));

        $i = 0;
    
        foreach ($this->column as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $column[$i] = $item; // set column array variable to order processing
            $i++;
        }
        
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->where('feeder_kelas_kuliah.id_smt', $this->session->userdata('tahun_ajaran'));
        $this->db->where('feeder_kelas_kuliah.id_sms', $this->session->userdata('prodi'));
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    

    public function get_by_id($id_kls)
    {
        $this->db->from($this->table);
        $this->db->join('feeder_mata_kuliah', 'feeder_mata_kuliah.id_mk = feeder_kelas_kuliah.id_mk');
        $this->db->where('id_kls',$id_kls);
        $query = $this->db->get();

        return $query->row();
    }


    //dari nilai_model di mahasiswa
    public function get_idregpd($nim){
        //$user = $this->ion_auth->user()->row();
        $num = $this->db
                    ->where('nipd', $nim)
                    ->where('id_sms', $this->session->userdata('prodi'))
                    ->get('mahasiswa_pt');

        if($num->num_rows() > 0){
            return $num->row()->id_reg_pd;
        }else{
            return 'nothing';
        }
        
    }

}
