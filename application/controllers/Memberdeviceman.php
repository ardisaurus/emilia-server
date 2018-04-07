<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Memberdeviceman extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $own_email = $this->get('email');
        $dvc_id = $this->get('dvc_id');
        if ($own_email != '') { 
            if ($dvc_id == '') {  
                $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`dvc_name`,`ownership`.`own_email` as `email` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` WHERE `ownership`.`own_email`= '$own_email'")->result();                
            } else {
                $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`dvc_name`,`ownership`.`own_email` as `email` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` WHERE `device`.`dvc_id`='$dvc_id' AND `ownership`.`own_email`= '$own_email'")->result();
            }
            $this->response($device, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="insert") {
            $data = array(
                    'own_dvc_id'      => $this->post('own_dvc_id'),
                    'own_email'  => $this->post('own_email'));
            $this->db->select('dvc_id');
            $this->db->where('dvc_id', $data['own_dvc_id']);
            $cekdvc = $this->db->get('device')->result();
            if ($cekdvc) {
                $this->db->where('own_dvc_id', $data['own_dvc_id']);
                $cekowndvc = $this->db->get('ownership')->result();
                if ($cekowndvc) {
                   $this->response(array('status' => 'fail', 502));
                }else{
                   $insert = $this->db->insert('ownership', $data);
                    if ($insert) {
                        $this->response($data, 200);
                    } else {
                        $this->response(array('status' => 'fail', 502));
                    }
                }
            }else{
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="auth") {
            $dvc_id = $this->post('dvc_id');
            $dvc_password = $this->post('dvc_password');
            $this->db->select('dvc_id');
            $this->db->select('dvc_password');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
            if ($device[0]->dvc_password==$dvc_password ) {
                $data[0] = array('status' => "success");
                $this->response($data, 200);
            } else {
                $data[0] = array('status' => "fail");
                $this->response($data, 200);
            }
        }elseif ($action=="id_check") {
            $dvc_id = $this->post('dvc_id');
            $device = $this->db->query("SELECT `dvc_id`,`dvc_status` FROM `device` LEFT JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` where `ownership`.`own_dvc_id` is null AND `device`.`dvc_id`='$dvc_id'")->result();
            if ($device) {
                $data[0] = array('status' => "success");
                $this->response($data, 200);
            } else {
                $data[0] = array('status' => "fail");
                $this->response($data, 200);
            }
        }elseif ($action=="unlock") {
            $email = $this->post('email');
            $dvc_id = $this->post('dvc_id');
            $dvc_password = $this->post('dvc_password');
            if ($dvc_password!='') {
                $this->db->select('dvc_id');
                $this->db->select('dvc_password');
                $this->db->where('dvc_id', $dvc_id);
                $device = $this->db->get('device')->result();
                if ($device[0]->dvc_password==$dvc_password ) {
                    $data = array('dvc_status' => 1);
                    $this->db->where('dvc_id', $dvc_id);
                    $update = $this->db->update('device', $data);
                    if ($update) {
                        $data2 = array( 'hst_email' => $email,
                                        'hst_date' => date("Y-m-d"),
                                        'hst_time' => date("h:i:s"),
                                        'hst_dvc_id' => $dvc_id);
                        $update2 = $this->db->insert('history', $data2);
                        if ($update2) {
                            $this->response($data2, 200);
                        } else {
                            $this->response(array('status' => 'fail', 502));
                        }
                    } else {
                        $this->response(array('status' => 'fail', 502));
                    }
                }else{
                    $data = array('dvc_status' => 0);
                    $this->db->where('dvc_id', $dvc_id);
                    $update = $this->db->update('device', $data);
                    if ($update) {
                        $this->response($data, 200);
                    } else {
                        $this->response(array('status' => 'fail', 502));
                    }
                }
            }else{
                $data = array('dvc_status' => 0);
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $this->response($data, 200);
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }                
        }elseif ($action=="update") {
            $dvc_id = $this->post('dvc_id');
            $part = $this->post('part');
            if ($part=="name") {
                $data = array('dvc_name' => $this->post('dvc_name'));
            }elseif ($part=="password") {
                $data = array('dvc_password' => $this->post('dvc_password'));
            }else{            
                $this->response(array('status' => 'fail', 502));
            }
            $this->db->where('dvc_id', $dvc_id);
            $update = $this->db->update('device', $data);
            if ($update) {
                $this->response($data, 200);
            } else {
                $this->response(array('status' => 'fail', 502));
            }

        }elseif ($action=="delete") {
            $dvc_id = $this->post('dvc_id');
            $this->db->where('own_dvc_id', $dvc_id);
            $delete = $this->db->delete('ownership');
            if ($delete) {
                $data = array('dvc_name' => '');
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $data[0] = array('status' => "success");
                    $this->response($data, 200);
                } else {
                    $data[0] = array('status' => "fail");
                    $this->response($data, 200);
                }
            } else {
                $data[0] = array('status' => "fail");
                $this->response($data, 200);
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