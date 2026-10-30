<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_model extends CI_Model {
    public $table = 'siakad_log';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
    }

    private function _get_datatables_query()
    {
        $user = $this->ion_auth->user()->row();
        $this->db->from($this->table);
        $this->db->join('feeder_mata_kuliah', 'feeder_mata_kuliah.id_mk = feeder_kelas_kuliah.id_mk');
        $this->db->where('feeder_kelas_kuliah.id_smt', $this->session->userdata('tahun_ajaran'));
        //$this->db->where('feeder_kelas_kuliah.id_sms', $this->session->userdata('prodi'));
        $this->db->where('feeder_kelas_kuliah.id_ptk', $user->id_ptk);

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

    private function _get_nilai_datatables_query($id_kls)
    {
        
        $this->db->from('feeder_nilai');
        $this->db->join('mahasiswa_pt', 'mahasiswa_pt.id_reg_pd = feeder_nilai.id_reg_pd');
        $this->db->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd');
        //$this->db->where('feeder_nilai.id_smt', $this->session->userdata('tahun_ajaran'));
        $this->db->where('feeder_nilai.id_kls', $id_kls);

        $i = 0;
    
        foreach ($this->column2 as $item) // loop column 
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

                if(count($this->column2) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $column2[$i] = $item; // set column array variable to order processing
            $i++;
        }
        
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($column2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order2))
        {
            $order2 = $this->order2;
            $this->db->order_by(key($order2), $order2[key($order2)]);
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

     public function get_nilai_datatables($id_kls)
    {
        $this->_get_nilai_datatables_query($id_kls);
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

    public function count_filtered2($id_kls)
    {
        $this->_get_nilai_datatables_query($id_kls);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all2($id_kls)
    {
       
        $this->db->where('feeder_nilai.id_kls', $id_kls);
        $this->db->from('feeder_nilai');
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

    public function get_nilai_by_id($id_kls, $id_reg_pd){
        $this->db->select('mahasiswa.nm_pd, nilai_tugas, nilai_partisipasi, nilai_uts, nilai_uas, tugas, partisipasi, uts, uas');
        $this->db->from('feeder_nilai');
        $this->db->join('feeder_kelas_kuliah', 'feeder_kelas_kuliah.id_kls = feeder_nilai.id_kls');
        $this->db->join('mahasiswa_pt', 'mahasiswa_pt.id_reg_pd = feeder_nilai.id_reg_pd');
        //$this->db->join('feeder_kelas_kuliah', 'feeder_kelas_kuliah.id_kls = feeder_kelas_kuliah.id_kls');
        $this->db->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd');

        $this->db->where(['feeder_nilai.id_kls'=>$id_kls, 'feeder_nilai.id_reg_pd'=>$id_reg_pd]);
        $query = $this->db->get();

        return $query->row();
    }

    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }


}
