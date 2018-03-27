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
            $this->response(array('status' => 'fail', 502));            
        } else {
            $device = $this->db->query("SELECT `dvc_status` FROM `device` where `device`.`dvc_id`='$dvc_id'")->result();
        }
        $this->response($device, 200);
        // $data[0] = array('dvc_status' => 1);
        // $this->response($data, 200);
    }

    function index_post() {
        $this->response(array('status' => 'fail', 502));
    }

    function index_put() {
        $this->response(array('status' => 'fail', 502));
    }

    function index_delete() {
        $this->response(array('status' => 'fail', 502));
    }
}
?>