<?php
if (!defined('BASEPATH')) exit('No direct script access allowed!');
class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}
	
	//渲染用户管理页面
	public function manage() {
		$this->load->view('user/list');
	}
	
	//获取所有用户等级
	public function get_level() {
		$result = $this->user_model->get_level();
		echo json_encode($result);
	}
	
	//获取所有用户信息
	public function userinfo() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$module_id = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] : '';
		$num = $this->user_model->userinfo($start, $limit, $module_id);
		if ($num) {
			$n = array_pop($num);
			$level = $this->user_model->get_level();
			foreach ($num as $key=>$value) {
				foreach ($level as $v) {
					if ($v['f_role_id'] == $value['f_user_role']) {
						$num[$key]['f_user_role'] = $v['f_role_name']; 
					}
				}
				$num[$key]['f_create_time'] = date('Y-m-d H:i:s', $value['f_create_time']);
			}
			$num = '{results:' . $n . ',items:' . json_encode($num) . '}';
			echo $num;
		} else {
			echo '[0]';
		}
	}
	
	public function update_u() {
		$this->load->view('user/update');
	}
	//修改用户密码
	public function update_pwd() {
		$userinfo = $this->session->userdata('USER');
		$old_pwd = quotes(trim($this->input->post('old_pwd')));
		$new_pwd1 = quotes(trim($this->input->post('new_pwd1')));
		$new_pwd2 = quotes(trim($this->input->post('new_pwd2')));
		if ($new_pwd1 !== $new_pwd2) {
			return_error_json('两次密码不一致');
		} else {
			$pwd = md5(quotes(trim($new_pwd1)). SF_KEY);
			$old_pwd = md5(quotes(trim($old_pwd)). SF_KEY);
			$result = $this->user_model->update_pwd($userinfo['user_id'], $old_pwd, $pwd);
			if ($result) {
				return_success();
			} else {
				return_error_json('原始密码不正确');
			}
		}
	}
	
	//添加用户
	public function add_user() {
		$arr['f_username']  = quotes(trim($this->input->post('f_username')));
		$arr['f_realname']  = quotes(trim($this->input->post('f_realname')));
		$arr['f_userpwd'] = md5(quotes(trim($this->input->post('f_userpwd'))). SF_KEY);
		$arr['f_pwd_bak'] = quotes(trim($this->input->post('f_userpwd')));
		$arr['f_userid'] = quotes(trim($this->input->post('f_userid')));
		$arr['f_qq'] = quotes(trim($this->input->post('f_qq')));
		$arr['f_phone'] = quotes(trim($this->input->post('f_phone')));
		$arr['f_job_time'] = quotes(trim($this->input->post('f_job_time')));
		$arr['f_salary'] = quotes(trim($this->input->post('f_salary')));
		$arr['f_user_role'] = quotes(trim($this->input->post('f_user_role')));
		$arr['f_create_time'] = time();
		$result = $this->user_model->add_user($arr);
		if ($result > 0 ) {
			return_success();
		} else {
			return_error_json('添加失败');
		}
	}
	
	//查找用户
	public function find_user() {
		if ($this->input->get('id')) {
			$id = intval($this->input->get('id'));
			$result = $this->user_model->get_one_byid($id);
			return_json($result);
		} else {
			return_error();
		}
	}
	//修改用户
	public function edit_user() {
		if ($this->input->post()) {
			$id = intval($this->input->post('f_id'));
			$arr['f_username']  = quotes(trim($this->input->post('f_username')));
			$arr['f_realname']  = quotes(trim($this->input->post('f_realname')));
			$arr['f_userpwd'] = md5(quotes(trim($this->input->post('f_userpwd'))). SF_KEY);
			$arr['f_pwd_bak'] = quotes(trim($this->input->post('f_userpwd')));
			$arr['f_userid'] = quotes(trim($this->input->post('f_userid')));
			$arr['f_qq'] = quotes(trim($this->input->post('f_qq')));
			$arr['f_phone'] = quotes(trim($this->input->post('f_phone')));
			$arr['f_job_time'] = quotes(trim($this->input->post('f_job_time')));
			$arr['f_salary'] = quotes(trim($this->input->post('f_salary')));
			$arr['f_user_role'] = quotes(trim($this->input->post('f_user_role')));
				
			$result = $this->user_model->edit_user($id, $arr);
			if ($result > 0) {
				return_success();
			} else {
				return_error();
			}
		} else {
			return_error();
		}
	}
	
	//删除用户
	public function del_user() {
		$id = $this->input->post('user_id');
		if (isset($id[1]) && $id[1] != '' ) {
			$id = explode(',', $id);
			if ($this->user_model->del_user($id)) {
				return_success();
			} else {
				return_error();
			}
		} else {
			if ($this->user_model->del_user($id)) {
				return_success();
			} else {
				return_error();
			}
		}
	}
	
	/**
	 * 退出
	 */
	public function logout() {
		$this->session->unset_userdata('USER');
		$url = site_url('welcome/index');
		header('Location:' . $url);
	}


	/**
	 * 渲染设置代理网站页面
	 */
	public function manage_web() {
		$result = $this->get_proxy();
		$web = $this->get_web();
		$arr = array('userinfo' => $result, 'web' => $web);
		$this->load->view('user/manage_web', $arr);
	}

	/**
	 * 代理管理站点
	 */
	public function get_proxy() {
		$this->load->model('user_model');
		return $this->user_model->get_proxy();
	}

	/**
	 * 获取所有站点
	 */
	public function get_web() {
		$this->load->model('ad_model');
		return $this->ad_model->get_web();
	}

	public function add_user_web() {
		$user = quotes($this->input->post('proxy'));
		$web = implode(',', $this->input->post('web'));
		if ($this->user_model->add_user_web($user, $web)) {
			echo '添加成功！';
		} else {
			echo '添加失败！请联系More或者重试一次';
		}
	}
}