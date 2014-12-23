<?php
if (!defined('BASEPATH')) exit('No direct script access allowed!');
class Contract extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('contract_model');
	}
	//渲染首页
	public function index() {
		if (!$this->session->userdata('USER')) exit('get out!');
		$this->load->view('contract/index');
	}
	
	//获取所有合同
	public function get_list() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];

		$nlevel = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] :'';
		if (empty($nlevel)) {
			$nlevel = 0;
		}
		$num = $this->contract_model->get_list($start,$limit,$nlevel);
		if ($num) {
			$n = array_pop($num);
			$this->load->model('business_model');
			$type = $this->business_model->get_option_type();
			foreach ($num as $n_k => $n_v) {
				foreach ($type as $t_v) {
					if ($t_v['f_id'] == $n_v['f_type']) {
						$num[$n_k]['f_type'] = $t_v['f_name'];						
					}
				}
			}
			$this->load->model('client_model');
			$clients = $this->client_model->get_cilents();
			foreach ($num as $n_k => $n_v) {
				foreach ($clients as $c_v) {
					if ($c_v['f_id'] == $n_v['f_user']) {
						
						$num[$n_k]['f_clients'] = $c_v['f_name'];
					}
				}
			}
			$num = '{results:' . $n . ',items:' . json_encode($num) . '}';
			echo $num;
		} else {
			echo '[0]';
		}
	}
	
	//删除合同
	public function del_type() {
		if ($this->input->post()) {
			$id = $this->input->post('id');
			if (isset($id[1]) && $id[1] != '' ) $id = explode(',', $id);
			echo $this->contract_model->get_del_type($id);
		} else {
			return_error();
		}
	
	}
	
	//修改合同状态为已支付完成
	public function edit_status() {
		if ($this->input->post()) {
			$id = $this->input->post('id');
			if (isset($id[1]) && $id[1] != '' ) $id = explode(',', $id);
			echo $this->contract_model->get_edit_status($id);
		} else {
			return_error();
		}
	
	}
	
	//根据id查找合同
	public function find_type() {
		if ($this->input->get('id')) {
			$id = intval($this->input->get('id'));
			$result = $this->contract_model->get_one_byid($id);
			return_json($result);
		} else {
			return_error();
		}
	}
	
	//添加合同
	public function add_contract() {
		if ($this->input->post()) {
			$arr['f_name'] = $this->input->post('f_name');
			$arr['f_desc'] = $this->input->post('f_desc');
			$arr['f_price'] = $this->input->post('f_price');
			$arr['f_deposit'] = $this->input->post('f_deposit');
			$arr['f_user'] = $this->input->post('f_user');
			
			$arr['f_mobile'] = $this->input->post('f_mobile');
			$arr['f_qq'] = $this->input->post('f_qq');
			$arr['f_service'] = $this->input->post('f_service');
			
			$arr['f_type'] = $this->input->post('f_type');
			//$arr['f_status'] = $this->input->post('f_status');
		
			$arr['f_create_time'] = date('Y-m-d H:i:s');
			if ($this->contract_model->get_add_contract($arr) > 0) {
				return_success();
			} else {
				return_error();
			}
		} else {
			return_error();
		}
	}
	
	//修改业务
	public function edit_type() {
		if ($this->input->post()) {
			$id = intval($this->input->post('f_id'));
			$arr['f_name'] = $this->input->post('f_name');
			$arr['f_phone'] = $this->input->post('f_phone');
			$arr['f_tell'] = $this->input->post('f_tell');
			$arr['f_address'] = $this->input->post('f_address');
			$arr['f_type'] = $this->input->post('f_type');
			$arr['f_company'] = $this->input->post('f_company');
			
			$result = $this->contract_model->get_edit_type($arr, $id);
			if ($result > 0) {
				return_success();
			} else {
				return_error();
			}
		}
	}
	
	public function client_type() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$nlevel = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] :'';
		if (empty($nlevel)) {
			$nlevel = 1;
		}
		$result = $this->contract_model->get_client_type($start,$limit,$nlevel);
		$n = array_pop($result);
		$num = '{results:'.$n.',items:'.json_encode($result).'}';
		echo $num;
	}
	
}