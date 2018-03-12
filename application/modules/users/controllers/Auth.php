<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Description of users
 *
 * @author Sunil
 *
 */
class Auth extends MY_Controller {

    function __construct(){
        parent::__construct();
        $this->output->set_template('login');
        $this->output->set_title('TSD Admin');

    }

    function index() {
        if ($this->session->userdata('logged_in')){
            redirect(base_url('users'));
        }
    	$this->load->model('user');
        $this->load->model('authmodel');
        $this->load->library('user_agent');
        if ($this->agent->referrer()){
            $data['referrer'] = base64_encode($this->agent->referrer());
        }

        $error = '';
        if ($this->input->method(TRUE) === 'POST'){
            if ($this->authmodel->verify_validation()){
                $username = $this->input->post('username',true);
                $password = $this->input->post('password');
                if ($this->authmodel->check_login($username,$password)){
                    $user_data = $this->user->get_user($username);
                    if ($user_data != false) {
                        $this->session->set_userdata('logged_in', $user_data);
                        $this->session->set_flashdata('success', 'You have successfully logged in.');
                        //Store Login Info
                        $this->authmodel->store_login_info($username);
                        //Redirect
                        $ref_url = base64_decode($this->input->post('referrer'));
                        if ($ref_url){
                            redirect($ref_url);
                        }else{
                            redirect(base_url('users'));
                        }

                    }else{
                        $error = 'Error';
                    }
                }else{
                    $error = 'Invalid Username or Password';
                }
            }else{
                $error = validation_errors();
            }
        }
        $data['error'] = $error;

        $this->load->view('login',$data);
    }





    public function logout(){
        $sess_array = array(
            'id' => ''
        );
        $this->session->unset_userdata('logged_in', $sess_array);
        $this->session->set_flashdata('success', 'Successfully Logout');
        redirect(base_url('users/auth'));
    }

}

/* End of file AuthModel_model.php */
/* Location: ./application/modules/users/controllers/AuthModel_model.php */