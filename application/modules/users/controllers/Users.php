<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Description of users
 *
 * @author Uji Baba
 *
 */
class Users extends MY_Controller {

    function __construct(){
        parent::__construct();
        $this->output->section('header','welcome/header');
        $this->output->section('sidebar','welcome/sidebar');
        $this->output->section('footer','welcome/footer');
        $this->output->set_title('TSD Admin');
        $this->output->set_template('admin');
    }

    function index() {
    	
    	$this->load->model('user');
    	$data['users'] = $this->user->get_users();
        $this->output->append_title('Users');
        $this->output->css('assets/themes/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css');
        $this->output->js('assets/themes/admin/bower_components/datatables.net/js/jquery.dataTables.min.js');
        $this->output->js('assets/themes/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js');
        $this->load->view('home',$data);
    }

}

/* End of file Users.php */
/* Location: ./application/modules/users/controllers/users.php */