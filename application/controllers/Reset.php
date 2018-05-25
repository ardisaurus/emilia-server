<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Reset extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    function index_get() {
        $this->response(array('status' => 'fail', 502));
    }

    function index_post() {
        $action=$this->post('action');        
        if ($action=="password") {
            $email=$this->post('email');
            $this->db->select('email');
            $this->db->where('email', $email);
            $user = $this->db->get('user')->result();
            if ($user) {
                $password=$this->gen_random(8);
                $data = array('password' => md5($password));
                $this->db->where('email', $email);
                $update = $this->db->update('user', $data);
                if ($update) {
                    $subject='Emilia : Reset Password Akun '.date("Y-m-d h:i:s");
                    $message='Silahkan login kembali dengan email : '.$email.' dan kata sandi : '.$password;
                    $this->send($email, $subject, $message);
                } else {
                    $data[0] = array('status' => "fail");
                    $this->response($data, 502);
                }
            } else { 
                $data[0] = array('status' => "fail");
                $this->response($data, 502);
            }
        }else{ 
            $data[0] = array('status' => "fail");
            $this->response($data, 502);
        }
    }

    function index_put() {
        $this->response(array('status' => 'fail', 502));
    }

    function index_delete() {
        $this->response(array('status' => 'fail', 502));
    }

    function gen_random($length) {
        $characters = 'abcdefghijklmnopqrstuvwxyz1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function send($email, $subject, $message){  
    $config = Array(  
      'protocol' => 'smtp',  
      'smtp_host' => 'ssl://smtp.googlemail.com',  
      'smtp_port' => 465,  
      'smtp_user' => 'foradisti@gmail.com',   
      'smtp_pass' => 'tengger7',   
      'mailtype' => 'html',   
      'charset' => 'iso-8859-1'  
    );  
    $this->load->library('email', $config);  
    $this->email->set_newline("\r\n");  
    $this->email->from('foradisti@gmail.com', 'Admin : Emilia Smart Lock');   
    $this->email->to($email);   
    $this->email->subject($subject);   
    $this->email->message($message);  
    if (!$this->email->send()) {  
      $data[0] = array('status' => "fail");
      $this->response(array("result"=>$data, 200)); 
    }else{  
      $data[0] = array('status' => "success");
      $this->response(array("result"=>$data, 200));
    }  
  }
  
}
?>