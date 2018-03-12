<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * User class.
 *
 * @extends CI_Model
 */
class Authmodel extends CI_Model {

    private $table = 'users';
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }

    public function get_users(){
        $users[] = array(
            'name'  =>  'test 1',
            'email' =>  'test1@gmail.com'
        );
        $users[] = array(
            'name'  =>  'test 2',
            'email' =>  'test2@gmail.com'
        );
        return $users;
    }

    public function image_upload(){
        $config['upload_path']          = './uploads/profile_pictures/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 1024;
        $config['file_name']            = time();
        if(!is_writable($config['upload_path'])){
            chmod($config['upload_path'], 777);
        }

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('image')){
            $ret['status'] = false;
            $ret['error'] = $this->upload->display_errors();
        }
        else{
            $data =  $this->upload->data();
            $ret['status'] = true;
            $ret['file_name'] = $data['file_name'];
        }
        return $ret;
    }


    //Backend Validation
    public function verify_validation(){

        $action =  $this->router->method;
        if ($action == 'index'){
            $this->form_validation->set_rules('username', 'Username', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            return FALSE ;
        } else {
            return TRUE;
        }
    }


    public function check_login($username,$password){
        $data['username'] = $username;
        $query = $this->db->get_where($this->table,$data);
        $userData = $query->row();
        if ($userData){
            if (password_verify($password,$userData->password)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }


    public function store_login_info($username){
        $ip = $this->input->ip_address();
        $time = date("Y-m-d H:i:s");
        $data['last_login_ip'] = $ip;
        $data['last_login_time'] = $time;
        $this->db->where('username', $username);
        $this->db->update($this->table, $data);
    }

    public function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

}