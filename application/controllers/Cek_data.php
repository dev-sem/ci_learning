<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cek_data extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('api', true);
    }

    public function index()
    {
        $data = $this->db->get('telepon')->result();
        echo json_encode($data);
    }

    function cek_get_data()
	{
		$q_coba = "SELECT * FROM telepon";
        $get = $this->db->query($q_coba)->result_array();
		
		// $get = $this->db->query($q_coba);
		// return $get;
		// echo '<pre>';
		echo json_encode($get);
	}

    public function insert()
    {
        // $data = array(
        //     // 'id'            => $this->post('id'),
        //     'nama'          => "Tes",
        //     'nomor'         => "0857666123"
        // );
        $data['nama']   = 'Tes';
        $data['nomor']  = '0857666123';
        $insert = $this->db->insert('telepon', $data);
        echo json_encode($insert);
    }
}
