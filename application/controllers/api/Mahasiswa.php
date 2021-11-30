<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Mahasiswa extends REST_Controller
{

    function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->db = $this->load->database('api', true);
        $this->load->model('mahasiswa_m', 'mhs');

        // untuk percobaan validasi
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    // Get Data
    public function index_get()
    {
        $id = $this->get('id');
        // jika id tidak ada (tidak panggil) 
        if ($id === null) {
            // maka panggil semua data
            $mahasiswa = $this->mhs->getMahasiswa();
            // tapi jika id di panggil maka hanya id tersebut yang akan muncul pada data tersebut
        } else {
            $mahasiswa = $this->mhs->getMahasiswa($id);
        }

        if ($mahasiswa) {
            $this->response([
                'status' => true,
                'data' => $mahasiswa
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'id not found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code

        }
    }

    // delete data
    public function index_delete()
    {
        $id = $this->delete('id');
        $mahasiswa = $this->mhs->getMahasiswa($id);

        if ($id == null) {
            $this->response([
                'status' => false,
                'message' => 'provide an id'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($mahasiswa) {
                // Ok
                $this->mhs->deleteMahasiswa($id);
                $this->response([
                    'status' => true,
                    'id' => $id,
                    'message' => 'deleted success'
                ], REST_Controller::HTTP_OK);
            } else {
                // id not found
                $this->response([
                    'status' => false,
                    'message' => 'id not found'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    // post data
    public function index_post()
    {
        // DIGUNAKAN UNTUK VALIDASI INPUT
        $this->form_validation->set_rules(
            'nrp',
            'nrp',
            'trim|required|min_length[4]'
            // kostum message error
            // ,array(
            //     'min_length' => '{field} kurang dari {param} karakter'
            // )
        );
        $this->form_validation->set_rules(
            'nama',
            'nama',
            'trim|required|min_length[5]'
            // kostum message error
            // ,array(
            //     'min_length' => '{field} kurang dari {param} karakter'
            // )
        );
        $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
        $this->form_validation->set_rules('jurusan', 'jurusan', 'trim|required');

        // PARAMETER DATA YANG AKAN DIKIRIM
        $data = [
            'nrp' => $this->post('nrp'),
            'nama' => $this->post('nama'),
            'email' => $this->post('email'),
            'jurusan' => $this->post('jurusan')
        ];

        // LOLOS ATAU TIDAK VALIDASI
        if ($this->form_validation->run() == FALSE) {
            // MENAMPILKAN PESAN ERROR DARI VALIDASI
            $nrp = form_error('nrp');
            $nama = form_error('nama');
            $email = form_error('email');
            $jurusan = form_error('jurusan');

            $this->response([
                'status' => false,
                'message' => 'failed create data',
                'data' => [
                    'nrp'       => $data['nrp'] == null ? $nrp : $data['nrp'] . $nrp,
                    'nama'      => $data['nama'] == null ? $nama : $data['nama'] . $nama,
                    'email'     => $data['email'] == null ? $email : $data['email'] . $email,
                    'jurusan'   => $data['jurusan'] == null ? $jurusan : $data['jurusan'] . $jurusan
                ]
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            // CEK APAKAH DATA SUDAH ADA
            $nrpPost = $data['nrp'];
            $mahasiswaNrp = $this->mhs->getMahasiswaNrp($nrpPost);
            if ($mahasiswaNrp) {
                // jika ada data maka tampilkan respon error
                $this->response([
                    'status' => false,
                    'message' => 'nrp sudah terdaftar!'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                // jika tidak ada data maka insert
                $this->mhs->createMahasiswa($data);
                $this->response([
                    'status' => true,
                    'message' => 'new mahasiswa has been created',
                    'data' => [
                        'nrp'       => $data['nrp'],
                        'nama'      => $data['nama'],
                        'email'     => $data['email'],
                        'jurusan'   => $data['jurusan']
                    ]
                ], REST_Controller::HTTP_CREATED);
            }
        }
    }

    // update data
    public function index_put()
    {
        $id = $this->put('id');

        if ($id == null) {
            $this->response([
                'status' => false,
                'message' => 'parameter id tidak ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $mahasiswa = $this->mhs->getMahasiswa($id);
            if ($mahasiswa) {
                // PARAMETER DATA YANG AKAN DIKIRIM
                $data = [
                    'nrp' => $this->put('nrp'),
                    'nama' => $this->put('nama'),
                    'email' => $this->put('email'),
                    'jurusan' => $this->put('jurusan')
                ];

                if ($data['nrp'] == null || $data['nama'] == null || $data['email'] == null || $data['jurusan'] == null) {
                    $this->response([
                        'status' => false,
                        'message' => "periksa kembali data",
                        'data' => [
                            'nrp' => 'required|varchar',
                            'nama' => 'required|varchar',
                            'email' => 'required|valid_email',
                            'jurusan' => 'required|varchar'
                        ]
                    ], REST_Controller::HTTP_NOT_FOUND);
                } else {
                    $nrpPost = $data['nrp'];
                    $mahasiswaNrp = $this->mhs->getMahasiswaNrp($nrpPost);
                    if ($mahasiswaNrp) {
                        // jika ada data maka tampilkan respon error
                        $this->response([
                            'status' => false,
                            'message' => 'nrp sudah terdaftar!'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    } else {
                        if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                            $this->mhs->updateMahasiswa($data, $id);
                            $this->response([
                                'status' => true,
                                'id' => $id,
                                'message' => 'updated success'
                            ], REST_Controller::HTTP_OK);
                        } else {
                            $this->response([
                                'status' => false,
                                'message' => 'email tidak valid!'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }
                    }
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "data dengan id $id tidak ditemukan"
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }


        // if ($this->mhs->updateMahasiswa($data, $id) > 0) {
        //     $this->response([
        //         'status' => true,
        //         'message' => 'update mahasiswa has been updated'
        //     ], REST_Controller::HTTP_NO_CONTENT);
        // } else {
        //     $this->response([
        //         'status' => false,
        //         'message' => 'failed to update data'
        //     ], REST_Controller::HTTP_BAD_REQUEST);
        // }
    }
}
