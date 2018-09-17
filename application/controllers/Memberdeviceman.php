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
                    'dvc_password_sc'   => md5($this->post('dvc_password_sc')));
            $this->db->where('dvc_id', $this->post('dvc_id'));
            $update = $this->db->update('device', $data);
            if ($update) {
                $this->response(array("result"=>$data, 200));
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        }elseif ($action=="create") {
            $dvc_id = $this->post('dvc_id');
            $device = $this->db->query("SELECT `dvc_id` FROM `device` WHERE `dvc_id`='$dvc_id'")->result();
            if ($device) {
                $session_id=$this->gen_random(9);
                while ($this->session_id_check($session_id)==true) {
                    $session_id=$this->gen_random(9);        
                }
                $keys = $this->generate_keys();
                $data['session_id']=$session_id;
                $data['public_key']=$keys[1];
                $data['private_key']=$keys[2];
                $data['modulo']=$keys[0];
                $insert = $this->db->insert('session', $data);
                if ($insert) {
                    $data2['session_id']=$session_id;
                    $data2['public_key']=$keys[1];
                    $data2['modulo']=$keys[0];
                    $this->response(array("result"=>$data2, 200));
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            } else {
                $data = array('status' => "fail");
                $this->response(array("result"=>$data, 200));
            }
        }elseif ($action=="auth") {
            // Authorize user with primary access password
            $dvc_id = $this->post('dvc_id');
            $session_id = $this->post('session_id');
            $cipher_rsa = $this->post('cipher_rsa');
            $cipher_aes = $this->post('cipher_aes');
            $dvc_password=md5($this->dec($session_id, $cipher_rsa, $cipher_aes));
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
        }elseif ($action=="auth_unencripted") {
            // Authorize user with primary access password unencripted
            $dvc_id = $this->post('dvc_id');
            $dvc_password=md5($this->post('dvc_password'));
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
        }elseif ($action=="auth_sc") {
            // Authorize user with secondary access password
            $dvc_id = $this->post('dvc_id');
            $session_id = $this->post('session_id');
            $cipher_rsa = $this->post('cipher_rsa');
            $cipher_aes = $this->post('cipher_aes');
            $dvc_password=md5($this->dec($session_id, $cipher_rsa, $cipher_aes));
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
        }elseif ($action=="auth_sc_unencripted") {
            // Authorize user with secondary access password
            $dvc_id = $this->post('dvc_id');
            $dvc_password=md5($this->post('dvc_password'));
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
            $data = array('dvc_status' => 1);
            $this->db->where('dvc_id', $dvc_id);
            $update = $this->db->update('device', $data);
            if ($update) {
                $data2 = array( 'hst_email' => $email,
                                'hst_date' => date("Y-m-d"),
                                'hst_time' => date("H:i:s"),
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
        }elseif ($action=="unlock_sc") {
            //unlock with secondary access password
            $email = $this->post('email');
            $dvc_id = $this->post('dvc_id');
            $data = array('dvc_status' => 1);
            $this->db->where('dvc_id', $dvc_id);
            $update = $this->db->update('device', $data);
            if ($update) {
                $data2 = array( 'hst_email' => $email,
                    'hst_date' => date("Y-m-d"),
                    'hst_time' => date("H:i:s"),
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
        }elseif ($action=="lock") {
            //lock device
            $email = $this->post('email');
            $dvc_id = $this->post('dvc_id');
            $this->db->select('dvc_id');
            $this->db->where('dvc_id', $dvc_id);
            $device = $this->db->get('device')->result();
            if ($device[0]->dvc_id==$dvc_id ) {
                $data = array('dvc_status' => 0);
                $this->db->where('dvc_id', $dvc_id);
                $update = $this->db->update('device', $data);
                if ($update) {
                    $data2 = array( 'hst_email' => $email,
                                    'hst_date' => date("Y-m-d"),
                                    'hst_time' => date("H:i:s"),
                                    'hst_status' => 0,
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
                $this->response(array('status' => 'fail', 502));
            }             
        }elseif ($action=="update") {
            //update device detail
            $dvc_id = $this->post('dvc_id');
            $part = $this->post('part');
            if ($part=="name") {
                $data = array('dvc_name' => $this->post('dvc_name'));
            }elseif ($part=="password") {
                $data = array('dvc_password' => md5($this->post('dvc_password')));
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
        }elseif ($action=="delete_session") {
            $session_id = $this->post('session_id');             
            if($this->session_id_check($session_id)==true){
                $data['session_id']=$session_id;
                $this->db->where('session_id', $session_id);
                $delete = $this->db->delete('session');
                if ($delete) {
                    $this->response(array('status' => 'success', 200));
                } else {
                    $this->response(array('status' => 'fail', 502));
                }
            }else{
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

    function dec($session_id, $cipher_rsa, $cipher_aes){        
        $session = $this->db->query("SELECT `private_key`, `modulo` FROM `session` where `session`.`session_id`='$session_id'")->result();
        $plain_rsa = $this->rsa_decrypt($cipher_rsa,  $session[0]->private_key,  $session[0]->modulo);
        $plain=$this->decrypt128($cipher_aes, $plain_rsa);
        return $plain;
    }

    function gen_random($length) {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    function session_id_check($session_id)
    {
        $session = $this->db->query("SELECT `session_id` FROM `session` WHERE `session_id`='$session_id'")->result();
        if (count($session)>0) {
            return true;
        }else{
            return false;
        }
    }

    // ===================== RSA 

    public function generate_keys ()
  		{ 
    			global $primes,  $maxprimes; 
			$primes = array (4507,  4513,  4517,  4519,  4523,  4547,  4549,  4561,  4567,  4583,  4591,  4597, 
			4603,  4621,  4637,  4639,  4643,  4649,  4651,  4657,  4663,  4673,  4679,  4691,  4703,  4721,  4723,  4729,  4733,  4751, 
			4759,  4783,  4787,  4789,  4793,  4799,  4801,  4813,  4817,  4831,  4861,  4871,  4877,  4889,  4903,  4909,  4919,  4931, 
			4933,  4937,  4943,  4951,  4957,  4967,  4969,  4973,  4987,  4993,  4999,  5003,  5009,  5011,  5021,  5023,  5039,  5051, 
			5059,  5077,  5081,  5087,  5099,  5101,  5107,  5113,  5119,  5147,  5153,  5167,  5171,  5179,  5189,  5197,  5209,  5227, 
			5231,  5233,  5237,  5261,  5273,  5279,  5281,  5297,  5303,  5309,  5323,  5333,  5347,  5351,  5381,  5387,  5393,  5399, 
			5407,  5413,  5417,  5419,  5431,  5437,  5441,  5443,  5449,  5471,  5477,  5479,  5483,  5501,  5503,  5507,  5519,  5521, 
			5527,  5531,  5557,  5563,  5569,  5573,  5581,  5591,  5623,  5639,  5641,  5647,  5651,  5653,  5657,  5659,  5669,  5683, 
			5689,  5693,  5701,  5711,  5717,  5737,  5741,  5743,  5749,  5779,  5783,  5791,  5801,  5807,  5813,  5821,  5827,  5839, 
			5843,  5849,  5851,  5857,  5861,  5867,  5869,  5879,  5881,  5897,  5903,  5923,  5927,  5939,  5953,  5981,  5987,  6007, 
			6011,  6029,  6037,  6043,  6047,  6053,  6067,  6073,  6079,  6089,  6091,  6101,  6113,  6121,  6131,  6133,  6143,  6151, 
			6163,  6173,  6197,  6199,  6203,  6211,  6217,  6221,  6229,  6247,  6257,  6263,  6269,  6271,  6277,  6287,  6299,  6301, 
			6311,  6317,  6323,  6329,  6337,  6343,  6353,  6359,  6361,  6367,  6373,  6379,  6389,  6397,  6421,  6427,  6449,  6451, 
			6469,  6473,  6481,  6491,  6521,  6529,  6547,  6551,  6553,  6563,  6569,  6571,  6577,  6581,  6599,  6607,  6619,  6637, 
			6653,  6659,  6661,  6673,  6679,  6689,  6691,  6701,  6703,  6709,  6719,  6733,  6737,  6761,  6763,  6779,  6781,  6791, 
			6793,  6803,  6823,  6827,  6829,  6833,  6841,  6857,  6863,  6869,  6871,  6883,  6899,  6907,  6911,  6917,  6947,  6949, 
			6959,  6961,  6967,  6971,  6977,  6983,  6991,  6997,  7001,  7013,  7019,  7027,  7039,  7043,  7057,  7069,  7079,  7103, 
			7109,  7121,  7127,  7129,  7151,  7159,  7177,  7187,  7193,  7207,  7211,  7213,  7219,  7229,  7237,  7243,  7247,  7253, 
			7283,  7297,  7307,  7309,  7321,  7331,  7333,  7349,  7351,  7369,  7393,  7411,  7417,  7433,  7451,  7457,  7459,  7477, 
			7481,  7487,  7489,  7499,  7507,  7517,  7523,  7529,  7537,  7541,  7547,  7549,  7559,  7561,  7573,  7577,  7583,  7589, 
			7591,  7603,  7607,  7621,  7639,  7643,  7649,  7669,  7673,  7681,  7687,  7691,  7699,  7703,  7717,  7723,  7727,  7741, 
			7753,  7757,  7759,  7789,  7793,  7817,  7823,  7829,  7841,  7853,  7867,  7873,  7877,  7879,  7883,  7901,  7907,  7919, 
			7927,  7933,  7937,  7949,  7951,  7963,  7993,  8009,  8011,  8017,  8039,  8053,  8059,  8069,  8081,  8087,  8089,  8093, 
			8101,  8111,  8117,  8123,  8147,  8161,  8167,  8171,  8179,  8191,  8209,  8219,  8221,  8231,  8233,  8237,  8243,  8263, 
			8269,  8273,  8287,  8291,  8293,  8297,  8311,  8317,  8329,  8353,  8363,  8369,  8377,  8387,  8389,  8419,  8423,  8429, 
			8431,  8443,  8447,  8461,  8467,  8501,  8513,  8521,  8527,  8537,  8539,  8543,  8563,  8573,  8581,  8597,  8599,  8609, 
			8623,  8627,  8629,  8641,  8647,  8663,  8669,  8677,  8681,  8689,  8693,  8699,  8707,  8713,  8719,  8731,  8737,  8741, 
			8747,  8753,  8761,  8779,  8783,  8803,  8807,  8819,  8821,  8831,  8837,  8839,  8849,  8861,  8863,  8867,  8887,  8893, 
			8923,  8929,  8933,  8941,  8951,  8963,  8969,  8971,  8999,  9001,  9007,  9011,  9013,  9029,  9041,  9043,  9049,  9059, 
			9067,  9091,  9103,  9109,  9127,  9133,  9137,  9151,  9157,  9161,  9173,  9181,  9187,  9199,  9203,  9209,  9221,  9227, 
			9239,  9241,  9257,  9277,  9281,  9283,  9293,  9311,  9319,  9323,  9337,  9341,  9343,  9349,  9371,  9377,  9391,  9397, 
			9403,  9413,  9419,  9421,  9431,  9433,  9437,  9439,  9461,  9463,  9467,  9473,  9479,  9491,  9497,  9511,  9521,  9533); 

			mt_srand((double)microtime()*1000000); 

    			$maxprimes = count($primes) - 1; 
			$p = $primes[mt_rand(0,  $maxprimes)]; 
    
			while (empty($q) || ($p==$q)) $q = $primes[mt_rand(0,  $maxprimes)];

			$n 	= $p*$q;
    			$pi 	= ($p - 1) * ($q - 1);
    			$e 	= $this->tofindE($pi,  $p,  $q);
    			$d 	= $this->extend($e, $pi);
    			$keys 	= array ($n, $e, $d);  
    			return $keys; 
    		} 

  		function mo ($g,  $l) { 
    			return $g - ($l * floor ($g/$l)); 
  		} 

  		function extend ($Ee, $Epi) { 
    			$u1 	= 1; 
    			$u2 	= 0; 
    			$u3 	= $Epi; 
    			$v1 	= 0; 
    			$v2 	= 1; 
    			$v3 	= $Ee; 
    
			while ($v3 != 0) { 
        			$qq = floor($u3/$v3); 
        			$t1 = $u1 - $qq * $v1; 
        			$t2 = $u2 - $qq * $v2; 
        			$t3 = $u3 - $qq * $v3; 
        			$u1 = $v1; 
        			$u2 = $v2; 
        			$u3 = $v3; 
        			$v1 = $t1; 
        			$v2 = $t2; 
        			$v3 = $t3; 
        			$z = 1; 
    			} 
    			$uu 	= $u1; 
    			$vv 	= $u2; 
    
			if ($vv < 0) { 
        			$inverse = $vv + $Epi; 
    			} else { 
        			$inverse = $vv; 
    			} 
  			return $inverse;
  		}


  		function GCD($e, $pi) { 
    			$y 	= $e; 
    			$x 	= $pi; 
    
			while ($y != 0) { 
        			$w =  $this->mo($x ,  $y); 
        			$x = $y; 
        			$y = $w; 

    			} 
    			return $x; 
  		} 


 		function tofindE($pi) { 
    			global $primes,  $maxprimes; 
    			$great 	= 0; 
    			$cc 	= mt_rand (0, $maxprimes); 
    			$startcc = $cc; 
    
		while ($cc >= 0) { 
       			$se = $primes[$cc]; 
        		$great = $this->GCD($se, $pi); 
        		$cc--; 
        	if ($great == 1) break; 
    } 
    if ($great == 0) { 
        $cc = $startcc + 1; 
        while ($cc <= $maxprimes) { 
            $se = $primes[$cc]; 
            $great = $this->GCD($se, $pi); 
            $cc++; 
            if ($great == 1) break; 
        } 
    } 
    return $se; 
  }

    function rsa_encrypt ($m,  $e,  $n) { 
        $asci = array (); 
        for ($i=0; $i<strlen($m); $i+=3) { 
            $tmpasci="1"; 
            for ($h=0; $h<3; $h++) { 
                if ($i+$h <strlen($m)) { 
                    $tmpstr = ord (substr ($m,  $i+$h,  1)) - 30; 

                    if (strlen($tmpstr) < 2) { 
                        $tmpstr ="0".$tmpstr; 
                    } 
                } else { 
                    break; 
                } 
                $tmpasci .=$tmpstr; 
            } 
            array_push($asci,  $tmpasci."1"); 
        } 
    
        $coded = "";
        for ($k=0; $k< count ($asci); $k++) { 
            $resultmod = $this->powmod($asci[$k],  $e,  $n); 
            $coded .= $resultmod." "; 
        } 
        return trim($coded); 
    } 

    function powmod ($base,  $exp,  $modulus) { 
        $accum = 1; 
        $i = 0; 
        $basepow2 = $base; 
        while (($exp >> $i)>0) { 
            if ((($exp >> $i) & 1) == 1) { 
                $accum = $this->mo(($accum * $basepow2) ,  $modulus); 
            } 
            $basepow2 = $this->mo(($basepow2 * $basepow2) ,  $modulus); 
            $i++; 
        } 
        return $accum; 
    }

    function rsa_decrypt ($c,  $d,  $n) { 
    global $resultd;
    global $deencrypt;
        $decryptarray = explode(" ",  $c); 
        for ($u=0; $u<count ($decryptarray); $u++) { 
            if ($decryptarray[$u] == "") { 
                array_splice($decryptarray,  $u,  1); 
            } 
        } 
        for ($u=0; $u< count($decryptarray); $u++) { 
            $resultmod = $this->powmod($decryptarray[$u],  $d,  $n); 
            $deencrypt.= substr ($resultmod, 1, strlen($resultmod)-2); 
        } 
        for ($u=0; $u<strlen($deencrypt); $u+=2) { 
            $resultd .= chr(substr ($deencrypt,  $u,  2) + 30); 

        } 
        return $resultd; 
    }

    // ========================= AES

    public static function cipher($input, $w)
    {
        $Nb = 4; 
        $Nr = count($w) / $Nb - 1; 

        $state = array(); 
        for ($i = 0; $i < 4 * $Nb; $i++) $state[$i % 4][floor($i / 4)] = $input[$i];

        $state = self::addRoundKey($state, $w, 0, $Nb);

        for ($round = 1; $round < $Nr; $round++) {
            $state = self::subBytes($state, $Nb);
            $state = self::shiftRows($state, $Nb);
            $state = self::mixColumns($state, $Nb);
            $state = self::addRoundKey($state, $w, $round, $Nb);
        }

        $state = self::subBytes($state, $Nb);
        $state = self::shiftRows($state, $Nb);
        $state = self::addRoundKey($state, $w, $Nr, $Nb);

        $output = array(4 * $Nb); 
        for ($i = 0; $i < 4 * $Nb; $i++) $output[$i] = $state[$i % 4][floor($i / 4)];
        return $output;
    }


    private static function addRoundKey($state, $w, $rnd, $Nb)
    {
        for ($r = 0; $r < 4; $r++) {
            for ($c = 0; $c < $Nb; $c++) $state[$r][$c] ^= $w[$rnd * 4 + $c][$r];
        }
        return $state;
    }

    private static function subBytes($s, $Nb)
    {
        for ($r = 0; $r < 4; $r++) {
            for ($c = 0; $c < $Nb; $c++) $s[$r][$c] = self::$sBox[$s[$r][$c]];
        }
        return $s;
    }

    private static function shiftRows($s, $Nb)
    {
        $t = array(4);
        for ($r = 1; $r < 4; $r++) {
            for ($c = 0; $c < 4; $c++) $t[$c] = $s[$r][($c + $r) % $Nb]; 
            for ($c = 0; $c < 4; $c++) $s[$r][$c] = $t[$c]; 
        } 
        return $s; 
    }

    private static function mixColumns($s, $Nb)
    {
        for ($c = 0; $c < 4; $c++) {
            $a = array(4); 
            $b = array(4); 
            for ($i = 0; $i < 4; $i++) {
                $a[$i] = $s[$i][$c];
                $b[$i] = $s[$i][$c] & 0x80 ? $s[$i][$c] << 1 ^ 0x011b : $s[$i][$c] << 1;
            }
            $s[0][$c] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3]; 
            $s[1][$c] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3]; 
            $s[2][$c] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3]; 
            $s[3][$c] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3];
        }
        return $s;
    }

    public static function keyExpansion($key)
    {
        $Nb = 4; 
        $Nk = count($key) / 4; 
        $Nr = $Nk + 6;

        $w = array();
        $temp = array();

        for ($i = 0; $i < $Nk; $i++) {
            $r = array($key[4 * $i], $key[4 * $i + 1], $key[4 * $i + 2], $key[4 * $i + 3]);
            $w[$i] = $r;
        }

        for ($i = $Nk; $i < ($Nb * ($Nr + 1)); $i++) {
            $w[$i] = array();
            for ($t = 0; $t < 4; $t++) $temp[$t] = $w[$i - 1][$t];
            if ($i % $Nk == 0) {
                $temp = self::subWord(self::rotWord($temp));
                for ($t = 0; $t < 4; $t++) $temp[$t] ^= self::$rCon[$i / $Nk][$t];
            } else if ($Nk > 6 && $i % $Nk == 4) {
                $temp = self::subWord($temp);
            }
            for ($t = 0; $t < 4; $t++) $w[$i][$t] = $w[$i - $Nk][$t] ^ $temp[$t];
        }
        return $w;
    }

    private static function subWord($w)
    {
        for ($i = 0; $i < 4; $i++) $w[$i] = self::$sBox[$w[$i]];
        return $w;
    }

    private static function rotWord($w)
    {
        $tmp = $w[0];
        for ($i = 0; $i < 3; $i++) $w[$i] = $w[$i + 1];
        $w[3] = $tmp;
        return $w;
    }

    private static $sBox = array(
        0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,
        0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,
        0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,
        0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,
        0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,
        0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,
        0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,
        0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,
        0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,
        0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,
        0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,
        0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,
        0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,
        0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,
        0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,
        0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16);

    private static $rCon = array(
        array(0x00, 0x00, 0x00, 0x00),
        array(0x01, 0x00, 0x00, 0x00),
        array(0x02, 0x00, 0x00, 0x00),
        array(0x04, 0x00, 0x00, 0x00),
        array(0x08, 0x00, 0x00, 0x00),
        array(0x10, 0x00, 0x00, 0x00),
        array(0x20, 0x00, 0x00, 0x00),
        array(0x40, 0x00, 0x00, 0x00),
        array(0x80, 0x00, 0x00, 0x00),
        array(0x1b, 0x00, 0x00, 0x00),
        array(0x36, 0x00, 0x00, 0x00));

    public function encrypt128($str, $secret)
    {
        $block = mcrypt_get_block_size("rijndael_128", "ecb");
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);

        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $secret, $str, MCRYPT_MODE_ECB));
    }

    public function decrypt128($str, $secret)
    {
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $secret, $str, MCRYPT_MODE_ECB);

        $len = strlen($str);
        $pad = ord($str[$len - 1]);

        return substr($str, 0, strlen($str) - $pad);
    }
}
?>