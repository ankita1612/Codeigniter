<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Datatable extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('datatable_model', 'dm');
    }
	
	function index() {
		$this->load->view('datatable', NULL);
	}
	function get_single_product() {
		$id=$this->input->get("id");
		$products = $this->dm->get_single_product($id);
		echo json_encode($products);
	}
	function get_products() {
		$products = $this->dm->get_products();
		echo json_encode($products);
	}
	
	function delete_product() {
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;
		
		if($this->dm->delete_product($id) === TRUE) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	
	
	function add_product() {
		$data = $this->input->get();		
		//print_R($data);
		if(empty($data['id'])	)	
		{
			$return=$this->dm->add_product($data);
			if($return==0 || $return=='')
			{
				$json['success']="0";
				$json['msg']="Error in insert data.";
			}
			else
			{
				$json['success']="1";
				$json['msg']="Sucessfully added.";
			}
		}
		else
		{
			$return = $this->dm->edit_product($data);			
			$json['success']="1";
			$json['msg']="Sucessfully edited.";
			
		}
			
		echo json_encode($json);
	}
}

/* End of file Datatable.php */
/* Location: ./application/controllers/Datatable.php */