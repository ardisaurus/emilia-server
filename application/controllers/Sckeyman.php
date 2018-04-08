<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Sckeyman extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $dvc_id = $this->get('dvc_id');
        $this->db->where('dvc_id', $dvc_id);
        $device = $this->db->get('device')->result();
        if ($device[0]->dvc_password_sc!='') {
            $data[0] = array('status' => "success");
            $this->response($data, 200);
        } else {
            $data[0] = array('status' => "fail");
            $this->response($data, 200);
        }
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="insert") {
            $data = array(
                    'dvc_id'            => $this->post('dvc_id'),
                    'dvc_password_sc'   => $this->post('dvc_password_sc'));
            $this->db->where('dvc_id', $this->post('dvc_id'));
            $update = $this->db->update('device', $data);
            if ($update) {
                $this->response($data, 200);
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="auth") {
            $dvc_id = $this->post('dvc_id');
            $dvc_password = $this->post('dvc_password_sc');
            $this->db->select('dvc_id');
            $this->db->select('dvc_password_sc');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
            if ($device[0]->dvc_password_sc==$dvc_password ) {
                $data[0] = array('status' => "success");
                $this->response($data, 200);
            } else {
                $data[0] = array('status' => "fail");
                $this->response($data, 200);
            }
        }elseif ($action=="delete") {
            $dvc_id = $this->post('dvc_id');
            $data = array('dvc_password_sc' => '');
            $this->db->where('dvc_id', $dvc_id);
            $update = $this->db->update('device', $data);
            if ($update) {
                $data[0] = array('status' => "success");
                $this->response($data, 200);
            } else {
                $data[0] = array('status' => "fail");
                $this->response($data, 200);
            }
        }else{
            $this->response(array('status' => 'fail', 502));
        }
    }
}
?>