<?php
if (!defined('BASEPATH')) exit('No direct script access allowed!');
class Ad extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('ad_model');
	}
	
	
	/**
	 * 渲染广告页面
	 */
	 public function index() {
	 	if (!$this->session->userdata('USER'))exit('get out!');
		$this->load->view('ad/index');
	}
	
	/**
	 * 获取所有广告
	 */
	public function get_ad() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$module_id = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] : '';
		$user = $this->session->userdata('USER');
		$web_id = $user['web_id'];
		//$num = $this->user_model->userinfo($start, $limit, $module_id);
		$result = $this->ad_model->get_from_list($start, $limit, $module_id, $web_id);
		
		if ($result) {
			$n = array_pop($result);
			foreach ($result as $k=>$v) {
				$result[$k]['f_end_time'] = date('Y-m-d H:i:s', $v['f_end_time']);
			}
			$result = '{results:' . $n . ',items:' . json_encode($result) . '}';
			echo $result;
		} else {
			echo '[0]';
		}
	}
	
	/**
	 * 获取所有广告来源
	 */
	public function get_ad_type() {
		$user = $this->session->userdata('USER');
		$web_id = $user['web_id'];
		$this->load->model('ad_model');
		$result = $this->ad_model->ad_type($web_id);
		if (!empty($result)) echo json_encode($result);
	}
	
	/**
	 * 添加广告
	 */
	public function add_ad() {
		if($this->input->post()) {
			$f_link = rtrim(quotes($this->input->post('f_link')),'http://');
			$f_link = str_ireplace('http://','',$f_link);
			$data = array(
					'f_pid' => quotes($this->input->post('f_pid')),
					'f_name' => quotes($this->input->post('f_name')),
					'f_ip' => quotes($this->input->post('f_ip')),
					'f_line' => quotes($this->input->post('f_line')),
					'f_desc' => quotes($this->input->post('f_desc')),
					'f_qq' => quotes($this->input->post('f_qq')),
					'f_link' => $f_link,
					'f_bgcolor' => $this->input->post('f_bgcolor'),
					'f_istop' => $this->input->post('f_istop'),
					'f_end_time' => strtotime("+".$this->input->post('f_end_time')."day"),
			);
			$this->load->model('ad_model');
			if ($this->ad_model->add_one($data)) {
				return_success();
			} else {
				return_error();
			}
		} else {
			return_error();
		}
	}
	
	/**
	 * 查找单条广告
	 */
	public function find_ad() {
		$id = $this->input->get('ad_id');
		if ($id) {
			$this->load->model('ad_model');
			$result = $this->ad_model->get_one($id);
			return_json($result);
		} else {
			return_error();
		}
	}
	
	/**
	 * 删除广告
	 */
	public function del_ad() {
		$id = $this->input->post('ad_id');
		$this->load->model('ad_model');
		if (isset($id[1]) && $id[1] != '' ) {
			$id = explode(',', $id);
			if ($this->ad_model->del_one($id)) {
				return_success();
			} else {
				return_error();
			}
		} else {
			if ($this->ad_model->del_one($id)) {
				return_success();
			} else {
				return_error();
			}
		}
	}

	/**
	 * 站点管理
	 */
	public function ad_type() {
		$this->load->view('ad/ad_type');
	}

	/**
	 * 获取所有站点
	 */
	public function get_ad_types() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$num = $this->ad_model->get_ad_types($start, $limit);
		if ($num) {
			$n = array_pop($num);
			$num = '{results:' . $n . ',items:' . json_encode($num) . '}';
			echo $num;
		} else {
			echo '[0]';
		}
	}

	/**
	 * 添加站点
	 */
	public function add_ad_type() {
		$arr['f_from']  = quotes(trim($this->input->post('f_from')));
		$arr['f_name'] = quotes(trim($this->input->post('f_name')));
		$result = $this->ad_model->add_ad_type($arr);
		if ($result > 0 ) {
			return_success();
		} else {
			return_error_json('添加失败');
		}
	}
	/**
	 * 删除站点
	 */
	public function del_ad_tpye() {
		$id = $this->input->post('id');
		if (isset($id[1]) && $id[1] != '' ) $id = explode(',', $id);
		echo $this->ad_model->del_ad_type($id);
	}
	/**
	 * 查找单条站点数据
	 */
	public function find_ad_type() {
		$id = $this->input->get('id');
		$result = $this->ad_model->find_ad_type($id);
		return_json($result);
	}
	/**
	 * 修改站点
	 */
	public function edit_ad_type() {
		if ($_POST) {
			$id = intval($this->input->post('f_id'));
			$data = array(
					'f_name' => quotes(trim($this->input->post('f_name'))),
					'f_from' => quotes(trim($this->input->post('f_from'))),
				);
			$result = $this->ad_model->edit_ad_type($data, $id);
			if ($result > 0) {
				return_success();
			} else {
				return_error();
			}
		}
	}
}