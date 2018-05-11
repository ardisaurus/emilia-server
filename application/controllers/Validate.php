<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Validate extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $token = $this->get('token');
        if ($token != '') {  
            $user = $this->db->query("SELECT * FROM `token_email` WHERE `token`='$token'")->result();
            if ($user) {
                $data = array('active' => 1);
                $this->db->where('email', $user[0]->email);
                $update = $this->db->update('user', $data);
                $this->db->where('token', $user[0]->token);
                $delete = $this->db->delete('token_email');
                if ($delete && $update) {
                    $this->response(array('status' => 'success', 'message' => 'silahkan login'), 200);
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }else{
                $this->response(array('status' => 'token invalid', 502));
            }               
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
}
?>