<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Description of users
 *
 * @author Sunil
 *
 */
class Examples extends MY_Controller {

    function __construct(){
        parent::__construct();
        if (!$this->session->userdata('logged_in')){
            redirect(base_url('users/auth'));
        }
        $this->load->model('example');
        $this->output->section('header','welcome/header');
        $this->output->section('sidebar','welcome/sidebar');
        $this->output->section('footer','welcome/footer');
        $this->output->set_template('admin');
    }

    function index() {
        //For Listing
    	$data['examples'] = $this->example->get_examples();
        $this->output->set_title('Example Management');
        $this->output->css('assets/themes/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css');
        $this->output->js('assets/themes/admin/bower_components/datatables.net/js/jquery.dataTables.min.js');
        $this->output->js('assets/themes/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js');
        $this->load->view('listing',$data);
    }

    function add(){
        if($this->example->verify_validation()) {
            //If it has post Request
            $params = array(
                'status' => $this->input->post('status'),
                'name' => $this->input->post('name'),
                'details' => $this->input->post('details')
            );
            $this->example->add_example($params);
            redirect('examples/index');
        }
        else {
            //Show Add Page
            $this->output->set_title('New Example');
            $this->load->view('add');
        }
    }


    function edit($id=0){
        // check if the row exists before trying to edit it
        $data['example'] = $this->example->get_example($id);
        if(isset($data['example']->id)) {
            if($this->example->verify_validation()) {
               $params = array(
                   'status' => $this->input->post('status'),
                   'name' => $this->input->post('name'),
                   'details' => $this->input->post('details')
                );

                $this->example->update_example($id,$params);
                redirect('examples/index');
            }
            else
            {
                $this->output->set_title('Edit Example');
                $this->load->view('edit',$data);
            }
        }
        else{
            show_error('The Content you are trying to edit does not exist.');
        }
    }


    function delete($id=0){
        $data['example'] = $this->example->get_example($id);

        // check if the user exists before trying to delete it
        if(isset($data['example']->id)) {
            $this->example->delete_example($id);
            redirect('examples/index');
        }
        else {
            show_error('The content you are trying to delete does not exist.');
        }
    }

    function togglestatus($id=0){
        $data['example'] = $this->example->get_example($id);
        if(isset($data['example']->id)) {
            $this->example->toggle_status($id);
            redirect('examples/index');
        }
        else {
            show_error('The content you are trying to delete does not exist.');
        }

    }

}

/* End of file Users.php */
/* Location: ./application/modules/examples/controllers/examples.php */