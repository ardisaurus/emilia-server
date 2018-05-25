<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Admindeviceman extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $dvc_id = $this->get('dvc_id');
        $ownership=$this->get('ownership');
        if ($ownership!=1) {
            if ($dvc_id == '') { 
                $device = $this->db->query("SELECT `dvc_id`,`dvc_status` FROM `device` LEFT JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` where `ownership`.`own_dvc_id` is null")->result();
            } else {
                $device = $this->db->query("SELECT `dvc_id`,`dvc_status` FROM `device` LEFT JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` where `ownership`.`own_dvc_id` is null AND `device`.`dvc_id`='$dvc_id'")->result();
            }
        }else{
            if ($dvc_id == '') {  
                $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`ownership`.`own_email` as `email`, `user`.`name` as `name` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` inner JOIN `user` on `ownership`.`own_email`=`user`.`email` ")->result();
            } else {
                $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`ownership`.`own_email` as `email`, `user`.`name` as `name` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` inner JOIN `user` on `ownership`.`own_email`=`user`.`email` WHERE `device`.`dvc_id`='$dvc_id'")->result();
            }
        }
        $this->response(array("result"=>$device, 200));
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="insert") {
            $data = array(
                    'dvc_id'      => $this->post('dvc_id'),
                    'dvc_password'  => $this->post('dvc_password'));
            $insert = $this->db->insert('device', $data);
            if ($insert) {
                $this->response(array("result"=>$data, 200));
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="reset_password") {
            $data = array(
                    'dvc_id'      => $this->post('dvc_id'),
                    'dvc_password'  => $this->post('dvc_password'));
            $this->db->where('dvc_id', $data['dvc_id']);
            $update = $this->db->update('device', $data);
            if ($update) {
                $this->response(array("result"=>$data, 200));
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }else{
            $this->response(array('status' => 'fail', 502));
        }
    }

    function index_put() {
        $this->response(array('status' => 'fail', 502));
    }

    function index_delete() {
        $this->response(array('status' => 'fail', 502));
    }
}
?>