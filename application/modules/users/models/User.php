<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * User class.
 *
 * @extends CI_Model
 */
class User extends CI_Model {
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

    public function get_user($username){
        $this->db->where('username',$username);
        $this->db->select('u.*,r.role_name as type');
        $this->db->from($this->table.' u');
        $this->db->join('user_roles r','u.user_role=r.id');
        $query = $this->db->get();
        $result = $query->row();
        if ($result){
            return $result;
        }
        return false;
    }


}