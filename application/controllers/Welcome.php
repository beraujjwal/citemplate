<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	function __construct(){
		parent::__construct();
		$this->output->section('header','welcome/header');
		$this->output->section('sidebar','welcome/sidebar');
		$this->output->section('footer','welcome/footer');
		$this->output->set_title('TSD Admin');
		$this->output->set_template('admin');
	}



	public function index()
	{
		$this->output->append_title('Welcome');
		$this->load->view('welcome/sample_view');
	}

	public function table(){
		$this->output->append_title('Table');
		$this->output->css('assets/themes/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css');
		$this->output->js('assets/themes/admin/bower_components/datatables.net/js/jquery.dataTables.min.js');
		$this->output->js('assets/themes/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js');
		$this->load->view('welcome/sample_table');

	}




}
