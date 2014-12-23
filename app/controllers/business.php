<?php
if (!defined('BASEPATH')) exit('No direct script access allowed!');
class Business extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('business_model');
	}

	//渲染首页
	public function index() {
		if (!$this->session->userdata('USER')) exit('get out!');
		$this->load->view('business/index');
	}

	//获取所有业务类别
	public function get_list() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$num = $this->business_model->get_list($start, $limit);
		if ($num) {
			$n = array_pop($num);
			$num = '{results:' . $n . ',items:' . json_encode($num) . '}';
			echo $num;
		} else {
			echo '[0]';
		}
	}
	
	//删除业务
	public function del_type() {
		if ($this->input->post()) {
			$id = $this->input->post('id');
			if (isset($id[1]) && $id[1] != '' ) $id = explode(',', $id);
			echo $this->business_model->get_del_type($id);
		} else {
			return_error();
		}
		
	}
	
	//根据id查找业务
	public function find_type() {
		if ($this->input->get('id')) {
			$id = intval($this->input->get('id'));
			$result = $this->business_model->get_one_byid($id);
			return_json($result);
		} else {
			return_error();
		}
	}
	
	//添加业务
	public function add_type() {
		if ($this->input->post()) {
			$arr['f_name'] = $this->input->post('f_name');
			if (!$this->business_model->get_one($arr['f_name'])) {
				if ($this->business_model->get_add_type($arr) > 0) {
					return_success();
				} else {
					return_error();
				}				
			} else {
				return_error_name();
			}
		} else {
			return_error();
		}
	}
	
	//修改业务
	public function edit_type() {
		if ($this->input->post()) {
			$id = intval($this->input->post('f_id'));
			$data = array(
					'f_name' => quotes(trim($this->input->post('f_name'))),
			);
			$result = $this->business_model->get_edit_type($data, $id);
			if ($result > 0) {
				return_success();
			} else {
				return_error();
			}
		}
	}
	
	//下拉框选项获取需求类型
	public function option_type() {
		$result = $this->business_model->get_option_type();
		if ($result) {
			echo json_encode($result);
		}
	}
	
}