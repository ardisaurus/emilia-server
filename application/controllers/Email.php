 <?php defined('BASEPATH') OR exit('No direct script access allowed');  
 class Email extends CI_Controller {

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
    $this->email->from('foradisti@gmail.com', 'Admin:Emilia');   
    $this->email->to($email);   
    $this->email->subject($subject);   
    $this->email->message($message);  
    if (!$this->email->send()) {  
      show_error($this->email->print_debugger());   
    }else{  
      echo 'Success to send email';   
    }  
  }

  public function test(){
    $email='ardinisme@gmail.com';
    $subject='Percobaan email';
    $message='Ini adalah email percobaan untuk Tutorial CodeIgniter';
    $this->send($email, $subject, $message);
  }

 }