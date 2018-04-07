<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Nugen extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get() {
        $dvc_id=$this->gen_random(5);
        while ($this->id_check($dvc_id)==true) {
            $dvc_id=$this->gen_random(5);        
        }
        $data[0] = array('dvc_id' => $dvc_id);
        $this->response($data, 200);
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

    function gen_random($length) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 2; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $num = '0123456789';
        $numLength = strlen($num);
        $randomNum = '';
        for ($i = 0; $i < $length-2; $i++) {
            $randomNum .= $num[rand(0, $numLength - 1)];
        }
        $randomString=$randomString.$randomNum;
        return $randomString;
    }
    
    function id_check($dvc_id)
    {
        $device = $this->db->query("SELECT `dvc_id`,`dvc_status` FROM `device` LEFT JOIN `ownership` ON `device`.`dvc_id`=`ownership`.`own_dvc_id` where `ownership`.`own_dvc_id` is null AND `device`.`dvc_id`='$dvc_id'")->result();
        if (count($device)>0) {
            return true;
        }else{
            return false;
        }
    }
}
?>