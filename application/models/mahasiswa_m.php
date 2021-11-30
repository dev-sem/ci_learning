<?php
    class mahasiswa_m extends CI_model {

        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database('api', true);
        }

        public function getMahasiswa($id = null) {
            if($id === null) {
                return $this->db->get('mahasiswa')->result_array(); 
            } else {
                return $this->db->get_where('mahasiswa', ['id' => $id])->result_array();
            }
        }

        public function getMahasiswaNrp($nrp = null) {
            // if($nrp === null) {
            //     return $this->db->get('mahasiswa')->result_array(); 
            // } else {
            //     return $this->db->get_where('mahasiswa', ['nrp' => $nrp])->result_array();
            // }
            return $this->db->get_where('mahasiswa', ['nrp' => $nrp])->result_array();
        }

        public function deleteMahasiswa($id) {
            $this->db->delete('mahasiswa', ['id' => $id]);
            return $this->db->affected_rows();
        }

        public function createMahasiswa($data) {
            $this->db->insert('mahasiswa', $data);
            return $this->db->affected_rows();
        } 

        public function updateMahasiswa($data, $id) {
            $this->db->update('mahasiswa', $data, ['id' => $id]);
            return $this->db->affected_rows();
        }
    }
?>