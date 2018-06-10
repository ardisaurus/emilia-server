<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Memberdeviceman extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    function index_get() {
        $own_email = $this->get('email');
        $dvc_id = $this->get('dvc_id');
        $level = $this->get('level');
        if ($own_email != '') {
            if ($level == 1) {  
                if ($dvc_id == '') {  
                    // Show all device list as secondary access
                    $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`dvc_name`,`ownership`.`own_email` as `email` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` WHERE `ownership`.`own_email`= '$own_email' AND `ownership`.`own_level`=1")->result();                
                } else {
                    //  Show device list as secondary access by id
                    $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`dvc_name`,`ownership`.`own_email` as `email` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` WHERE `device`.`dvc_id`='$dvc_id' AND `ownership`.`own_email`= '$own_email' AND `ownership`.`own_level`=1")->result();
                }                
            } else {
                if ($dvc_id == '') {
                    //  Show all device list as primary access
                    $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`dvc_name`,`ownership`.`own_email` as `email` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` WHERE `ownership`.`own_email`= '$own_email' AND `ownership`.`own_level`=0")->result();                
                } else {
                    //  Show all device list as primary access
                    $device = $this->db->query("SELECT `dvc_id`,`dvc_status`,`dvc_name`,`ownership`.`own_email` as `email` FROM `device` inner JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` WHERE `device`.`dvc_id`='$dvc_id' AND `ownership`.`own_email`= '$own_email' AND `ownership`.`own_level`=0")->result();
                }
            }            
            $this->response(array("result"=>$device, 200));
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="insert") {
            //insert new device into owned device as primary access
            $data = array(
                    'own_dvc_id'      => $this->post('own_dvc_id'),
                    'own_level'      => 0,
                    'own_email'  => $this->post('own_email'));
            $this->db->select('dvc_id');
            $this->db->where('dvc_id', $data['own_dvc_id']);
            $cekdvc = $this->db->get('device')->result();
            if ($cekdvc) {
                $this->db->where('own_dvc_id', $data['own_dvc_id']);
                $cekowndvc = $this->db->get('ownership')->result();
                if ($cekowndvc) {
                   $this->response(array('status' => 'failed', 502));
                }else{
                   $insert = $this->db->insert('ownership', $data);
                    if ($insert) {
                        $this->response(array("result"=>$data, 200));
                    } else {
                        $this->response(array('status' => 'fail', 502));
                    }
                }
            }else{
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="insert_sc") {
            //insert new device into owned device as secondary access
            $data = array(
                    'own_dvc_id'      => $this->post('own_dvc_id'),
                    'own_level'      => 1,
                    'own_email'  => $this->post('own_email'));
            $this->db->select('dvc_id');
            $this->db->where('dvc_id', $data['own_dvc_id']);
            $cekdvc = $this->db->get('device')->result();
            if ($cekdvc) {                
                $insert = $this->db->insert('ownership', $data);
                if ($insert) {
                    $this->response(array("result"=>$data, 200));
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }else{
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="insert_sc_key") {
            //add/edit secondary key to device
            $data = array(
                    'dvc_id'            => $this->post('dvc_id'),
                    'dvc_password_sc'   => $this->post('dvc_password_sc'));
            $this->db->where('dvc_id', $this->post('dvc_id'));
            $update = $this->db->update('device', $data);
            if ($update) {
                $this->response(array("result"=>$data, 200));
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="auth") {
            // Authorize user with primary access password
            $dvc_id = $this->post('dvc_id');
            $dvc_password = $this->post('dvc_password');
            $this->db->select('dvc_id');
            $this->db->select('dvc_password');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
            if ($device[0]->dvc_password==$dvc_password ) {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="forgot_password") {
            // forgot device password with primary access password
            $email = $this->post('email');
            $password = $this->post('password');
            $this->db->select('email');
            $this->db->select('password');
            $this->db->where('email', $email);
            $user = $this->db->get('user')->result();
            if ($user[0]->password==$password ) {
                $dvc_id = $this->post('dvc_id');
                $dvc_password = $this->post('dvc_password');
                $this->db->select('dvc_id');
                $this->db->select('dvc_password');
                $data = array('dvc_password' => $this->post('dvc_password'));
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $data = array('status' => "success");
                    $this->response(array("result"=>$data, 200));
                } else {
                    $data = array('status' => "fail");
                    $this->response(array("result"=>$data, 200));
                }
            } else {
                $data = array('status' => $user[0]->password."nm");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="auth_sc") {
            // Authorize user with secondary access password
            $dvc_id = $this->post('dvc_id');
            $dvc_password = $this->post('dvc_password_sc');
            $this->db->select('dvc_id');
            $this->db->select('dvc_password_sc');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
            if ($device[0]->dvc_password_sc==$dvc_password ) {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="sc_check") {
            // Check secondary access enable/disable
            $dvc_id = $this->post('dvc_id');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
            if ($device[0]->dvc_password_sc!='') {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="id_check") {
            //check device without owner
            $dvc_id = $this->post('dvc_id');
            $device = $this->db->query("SELECT `dvc_id`,`dvc_status` FROM `device` LEFT JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` where `ownership`.`own_dvc_id` is null AND `device`.`dvc_id`='$dvc_id'")->result();
            if ($device) {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="id_check_sc") {
            //check device with owner
            $dvc_id = $this->post('dvc_id');
            $device = $this->db->query("SELECT `dvc_id`,`dvc_status` FROM `device` LEFT JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` where `ownership`.`own_dvc_id` is not null AND `device`.`dvc_id`='$dvc_id' AND `device`.`dvc_password_sc`!=''")->result();
            if ($device) {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="unlock") {
            //unlock with primary access password
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
                            $this->response(array("result"=>$data2, 200));
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
                        $this->response(array("result"=>$data, 200));
                    } else {
                        $this->response(array('status' => 'fail', 502));
                    }
                }
            }else{
                $data = array('dvc_status' => 0);
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $this->response(array("result"=>$data, 200));
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }
        }elseif ($action=="unlock_sc") {
            //unlock with secondary access password
            $email = $this->post('email');
            $dvc_id = $this->post('dvc_id');
            $dvc_password_sc = $this->post('dvc_password_sc');
            if ($dvc_password_sc!='') {
                $this->db->select('dvc_id');
                $this->db->select('dvc_password_sc');
                $this->db->where('dvc_id', $dvc_id);
                $device = $this->db->get('device')->result();
                if ($device[0]->dvc_password_sc==$dvc_password_sc ) {
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
                            $this->response(array("result"=>$data2, 200));
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
                        $this->response(array("result"=>$data, 200));
                    } else {
                        $this->response(array('status' => 'fail', 502));
                    }
                }
            }else{
                $data = array('dvc_status' => 0);
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $this->response(array("result"=>$data, 200));
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }               
        }elseif ($action=="update") {
            //update device detail
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
                $this->response(array("result"=>$data, 200));
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="delete") {
            //delete device from owned device list primary access            
            $dvc_id = $this->post('dvc_id');
            $device = $this->db->query("SELECT `hst_dvc_id` FROM `history` WHERE `hst_dvc_id`='$dvc_id'")->result();
            if ($device) {
                $this->db->where('hst_dvc_id', $dvc_id);
                $delete = $this->db->delete('history');
            }
            $this->db->where('own_dvc_id', $dvc_id);
            $delete = $this->db->delete('ownership');
            if ($delete) {
                $data = array('dvc_name' => '', 'dvc_password_sc' => '');
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $data = array('status' => "success");
                    $this->response(array("result"=>$data, 200));
                } else {
                    $data = array('status' => "fail");
                    $this->response(array("result"=>$data, 200));
                }
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="delete_sc") {
            //delete device from owned device list secondary access
            $dvc_id = $this->post('dvc_id');
            $email = $this->post('email');
            $this->db->where('own_dvc_id', $dvc_id);
            $this->db->where('own_email', $email);
            $this->db->where('own_level', 1);
            $delete = $this->db->delete('ownership');
            if ($delete) {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="delete_sc_key") {
            //delete secondary access key/password
            $dvc_id = $this->post('dvc_id');
            $device = $this->db->query("SELECT `own_dvc_id`,`own_level` FROM `ownership` WHERE `ownership`.`own_level` = 1 AND `ownership`.`own_dvc_id`='$dvc_id'")->result();
            if ($device) {
                $this->db->where('own_dvc_id', $dvc_id);
                $this->db->where('own_level', 1);
                $delete = $this->db->delete('ownership');
            }
            $data = array('dvc_password_sc' => '');
            $this->db->where('dvc_id', $dvc_id);
            $update = $this->db->update('device', $data);
            if ($update) {
                $data = array('status' => "success");
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
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