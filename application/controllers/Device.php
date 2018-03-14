<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Device extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $dvc_id = $this->get('dvc_id');
        if ($dvc_id == '') {
            $this->db->select('dvc_id');
            $this->db->select('dvc_name');
            $this->db->select('dvc_status');
            $device = $this->db->get('device')->result();
        } else {
            $this->db->select('dvc_id');
            $this->db->select('dvc_name');
            $this->db->select('dvc_status');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
        }
        $this->response($device, 200);
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="insert") {
            $data = array(
                    'dvc_id'      => $this->post('dvc_id'),
                    'dvc_password'  => $this->post('dvc_password'));
            $insert = $this->db->insert('device', $data);
            if ($insert) {
                $this->response($data, 200);
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }else{
            $this->response(array('status' => 'fail', 502));
        }
    }

}
?>