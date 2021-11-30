<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Kontak extends REST_Controller
{

    function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->db = $this->load->database('api', true);
        // $this->load->database();
    }

    //Menampilkan data kontak
    function index_get()
    {
        $wilayah = $this->get('wilayah');
        if ($wilayah == '') {
            $no = 1;
            // $q_coba = "SELECT * FROM telepon";
            // $kontak = $this->db->query($q_coba)->result();
            $kontak = $this->db->get('telepon_basic')->result();
            // $kontak = $this->get_data();
            $this->response($kontak, 200);
        } else {
            $this->db->where('wilayah', $wilayah);
            $kontak = $this->db->get('telepon_basic')->result();
            $this->response($kontak, 200);
        }
    }

    //Mengirim atau menambah data kontak baru
    function index_post()
    {
        // $data = array(
        //     // 'id'            => $this->post('id'),
        //     'nama'          => $this->post('nama'),
        //     'nomor'         => $this->post('nomor')
        // );

        $tgl_daftar = $this->post('tgl_daftar');

        if ($tgl_daftar == null) {
            $this->response([
                'status' => FALSE,
                'message' => 'Harap isi parameter yang telah ditentukan.',
                // 'parameter' => ['nama' => 'varchar(50)', 'nomor' => 'number(13)']
                'parameter' => ['tgl_daftar' => 'date']
            ]);
        } else {
            $q_coba = "SELECT * FROM telepon_basic WHERE tgl_daftar = '$tgl_daftar'";
            $kontak = $this->db->query($q_coba)->result();
            $ttlData = $this->db->query($q_coba)->num_rows();

            $no = 1;
            foreach ($kontak as $key) {
                $no++;
                $dataSend['id_basic']           = $key->id_telepon_basic;
                $dataSend['nama']               = $key->nama;
                $dataSend['nomor']              = $key->nomor;
                $dataSend['wilayah']            = $key->wilayah;
                // $dataSend['tgl_daftar']         = $key->tgl_daftar;
                
                $insert = $this->db->insert('telepon', $dataSend);
                if ($insert) {
                    // $this->response($data, 200);
                    $this->response([
                        'status' => TRUE,
                        'total data' => $ttlData,
                        'data' => [$kontak]
                        // // 'parameter' => ['nama' => 'varchar(50)', 'nomor' => 'number(13)']
                        // 'parameter' => ['tgl_daftar' => 'date']
                    ], 200);
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }
            
        }
    }

    //Memperbarui data kontak yang telah ada
    function index_put()
    {
        $id = $this->put('id');
        $data = array(
            'id'       => $this->put('id'),
            'nama'          => $this->put('nama'),
            'nomor'    => $this->put('nomor')
        );
        $this->db->where('id', $id);
        $update = $this->db->update('telepon', $data);
        if ($update) {
            $this->response($data, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }

    //Menghapus salah satu data kontak
    function index_delete()
    {
        $id = $this->delete('id');
        $this->db->where('id', $id);
        $delete = $this->db->delete('telepon');
        if ($delete) {
            $this->response(array('status' => 'success'), 201);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }


    //Masukan function selanjutnya disini
}
