<?php
if (!defined('BASEPATH')) exit('No direct script access allowed!');
class Client extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('client_model');
	}
	//渲染首页
	public function index() {
		if (!$this->session->userdata('USER')) exit('get out!');
		$this->load->view('client/index');
	}
	
	//获取所有业务类别
	public function get_list() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];

		$nlevel = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] :'';
		if (empty($nlevel)) {
			$nlevel = 0;
		}
		$num = $this->client_model->get_list($start,$limit,$nlevel);
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
			echo $this->client_model->get_del_type($id);
		} else {
			return_error();
		}
	
	}
	
	//根据id查找业务
	public function find_type() {
		if ($this->input->get('id')) {
			$this->load->model('client_model');
			$id = intval($this->input->get('id'));
			$result = $this->client_model->get_one_byid($id);
			return_json($result);
		} else {
			return_error();
		}
	}
	
	//添加业务
	public function add_type() {
		if ($this->input->post()) {
			$arr['f_name'] = $this->input->post('f_name');
			if (!$this->client_model->get_one($arr['f_name'])) {
				
				$arr['f_name'] = $this->input->post('f_name');
				$arr['f_phone'] = $this->input->post('f_phone');
				$arr['f_tell'] = $this->input->post('f_tell');
				$arr['f_address'] = $this->input->post('f_address');
				$arr['f_type'] = $this->input->post('f_type');
				$arr['f_company'] = $this->input->post('f_company');
				if ($this->client_model->get_add_type($arr) > 0) {
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
			$arr['f_name'] = $this->input->post('f_name');
			$arr['f_phone'] = $this->input->post('f_phone');
			$arr['f_tell'] = $this->input->post('f_tell');
			$arr['f_address'] = $this->input->post('f_address');
			$arr['f_type'] = $this->input->post('f_type');
			$arr['f_company'] = $this->input->post('f_company');
			
			$result = $this->client_model->get_edit_type($arr, $id);
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
		$result = $this->client_model->get_client_type($start,$limit,$nlevel);
		$n = array_pop($result);
		$num = '{results:'.$n.',items:'.json_encode($result).'}';
		echo $num;
	}
	
	//获取用户
	public function get_users() {
		$result = $this->client_model->get_cilents();
		if ($result) {
			echo json_encode($result);
		}
	}
	
	//计算总金额
	public function get_count_all() {
		$this->load->model('contract_model');
		$result = $this->contract_model->get_count_all();
		if (!empty($result)) {
			echo $result['num'];
		} else {
			echo 0;
		}
	}
	
	//计算定金总金额
	public function get_count_payment() {
		$this->load->model('contract_model');
		$result = $this->contract_model->get_count_payment();
		if (!empty($result)) {
			echo $result['num'];
		} else {
			echo 0;
		}
	}
	
	//计算交易金额
	public function count_payment_complete() {
		$this->load->model('contract_model');
		echo $this->contract_model->count_payment_complete();
	}
	
}