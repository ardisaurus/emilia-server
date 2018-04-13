<?php

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
    }

    function index_get() {            
        $email = $this->get('email');
        if ($email == '') {
            $this->db->select('email');
            $this->db->select('name');
            $this->db->select('dob'); 
            $this->db->select('level'); 
            $this->db->select('active');            
            $this->db->order_by("level", "asc");
            $user = $this->db->get('user')->result();
        } else {
            $this->db->select('email');
            $this->db->select('name');
            $this->db->select('dob'); 
            $this->db->select('level'); 
            $this->db->select('active');
            $this->db->where('email', $email);
            $user = $this->db->get('user')->result();
        }
        $this->response($user, 200);
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="insert") {
            $data = array(
                    'email'     => $this->post('email'),
                    'name'      => $this->post('name'),
                    'password'  => $this->post('password'),
                    'level'     => $this->post('level'),
                    'dob'       => $this->post('dob'));
            $insert = $this->db->insert('user', $data);
            if ($insert) {
                $this->response($data, 200);
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="auth") {
            $email = $this->post('email');
            $password = $this->post('password');
            $this->db->select('email');
            $this->db->select('password');
            $this->db->where('email', $email);
            $user = $this->db->get('user')->result();
            if ($user[0]->password==$password ) {
                $data[0] = array('status' => "success");
                $this->response($data, 200);
            } else {
                $data[0] = array('status' => "fail");
                $this->response($data, 200);
            }  
        }elseif ($action=="delete") {

            $email = $this->post('email');

            $device = $this->db->query("SELECT `own_dvc_id` FROM `ownership` WHERE `own_email`='$email'")->result();
            if ($device) {
                foreach ($device as $devicedata) {
                    $this->db->where('own_dvc_id', $devicedata->own_dvc_id);
                    $delete = $this->db->delete('ownership');
                    if ($delete) {
                        $data = array('dvc_name' => '', 'dvc_password_sc' => '');
                        $this->db->where('dvc_id', $devicedata->own_dvc_id);
                        $update = $this->db->update('device', $data);
                    }
                    $device = $this->db->query("SELECT `hst_dvc_id` FROM `history` WHERE `hst_dvc_id`='$devicedata->own_dvc_id'")->result();
                    if ($device) {
                        $this->db->where('hst_dvc_id', $devicedata->own_dvc_id);
                        $delete = $this->db->delete('history');
                    }
                }
            }

            $this->db->where('email', $email);
            $delete = $this->db->delete('user');
            if ($delete) {
                $this->response(array('status' => 'success'), 201);
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="update") {
            $email = $this->post('email');
            $part = $this->post('part');
            if ($part=="email") {
                $data = array('email' => $this->post('new_email'));
            }elseif ($part=="name") {
                $data = array('name' => $this->post('name'));
            }elseif ($part=="password") {
                $data = array('password' => $this->post('password'));
            }elseif ($part=="dob") {
                $data = array('dob' => $this->post('dob'));
            }else{            
                $this->response(array('status' => 'fail', 502));
            }
            $this->db->where('email', $email);
            $update = $this->db->update('user', $data);
            if ($update) {
                $this->response($data, 200);
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