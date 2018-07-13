<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Latestaccess extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
            $email = $this->get('email');
            $y=0;
            $device = $this->db->query("SELECT `own_dvc_id` FROM `ownership` where `own_email`='$email'")->result();
            foreach ($device as $dvc) {
                $date = $this->db->query("SELECT max(`hst_date`) as date, max(`hst_time`) as time FROM `history` WHERE `hst_dvc_id`='$dvc->own_dvc_id'")->result();
                if($date[0]->date&&$date[0]->time){
                    $tdate=$date[0]->date." ".$date[0]->time;
                }
                $x=strtotime($tdate);
                if ($x>$y) {
                    $s=$tdate;
                    $y=strtotime($s);
                }
            }
            if ($device) {
                $data[0] = array('latest_access' => $s);
                $this->response(array("result"=>$data, 200));
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
    }
}
?>