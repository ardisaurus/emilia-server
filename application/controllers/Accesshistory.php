<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Accesshistory extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $dvc_id = $this->get('dvc_id');
        if ($dvc_id != '') {  
            $device = $this->db->query("SELECT `hst_date`,`hst_time`,`hst_dvc_id`,`hst_email`, `user`.`name` as `hst_user_name` FROM `history` LEFT JOIN `user` ON `history`.`hst_email`=`user`.`email` ORDER BY `hst_date` DESC, `hst_time` DESC")->result();   
             $this->response($device, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
}
?>