<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * User class.
 *
 * @extends CI_Model
 */
class User extends CI_Model {

    public function __construct() {
        parent::__construct();

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


}