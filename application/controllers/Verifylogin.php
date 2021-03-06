<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {

 function __construct()
 {
   parent::__construct();
   $this->load->model('login_model','',TRUE);
 }

 function index()
 {
   //This method will have the credentials validation
   $this->load->library('form_validation');

   $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

   if($this->form_validation->run() == FALSE)
   {
     //Field validation failed.  User redirected to login page
    $this->load->helper(array('form'));
    $data['session_data'] = $this->session->userdata('logged_in');
    $session_data = $this->session->userdata('logged_in');
    $data['user_total'] = $this->db->count_all("students");
    $data['username'] = $session_data['username'];
    $data['currentPage'] = 'login';
    $data['title'] = "Login ";
    $data['name'] = $this->input->post('search_input');
    $this->load->view('template/header.php',$data);
    $this->load->view('login');
    $this->load->view('template/footer.php',$data);
   }
   else
   {
     //Go to private area
     redirect('admin/dashboard', 'refresh');
   }

 }

 function check_database($password)
 {
   //Field validation succeeded.  Validate against database
   $username = $this->input->post('username');

   //query the database
   $result = $this->login_model->login($username, $password);

   if($result)
   {
     $sess_array = array();
     foreach($result as $row)
     {
       $sess_array = array(
         'id' => $row->id,
         'username' => $row->username
       );
       $this->session->set_userdata('logged_in', $sess_array);
     }
     return TRUE;
   }
   else
   {
     $this->form_validation->set_message('check_database', 'Invalid username or password');
     return false;
   }
 }
}
?>