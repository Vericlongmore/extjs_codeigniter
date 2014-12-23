<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {
	static $table = 'tb_user_info';
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 获取用户详情
	 * @return multitype:
	 */
	public function userinfo($start, $limit, $module_id) {
		if (!empty($module_id)) {
			$sql = "SELECT count(*) num FROM tb_user_info WHERE `f_user_role`='{$module_id}' AND `f_status`=1";
			$query1 = $this->db->query($sql);
			$command1 = $query1->row_array();
			$sql2 = "SELECT * FROM `tb_user_info` WHERE `f_user_role`='{$module_id}' AND `f_status`=1 ORDER BY f_create_time DESC";
			$query2 = $this->db->query($sql2);
			$command2 = $query2->result_array();
			$command2 ['num'] = $command1 ['num'];
		} else {
			$sql = "SELECT count(*) num FROM tb_user_info";
			$query1 = $this->db->query($sql);
			$command1 = $query1->row_array();
			$sql2 = "SELECT * FROM tb_user_info WHERE `f_status`=1 ORDER BY f_create_time DESC LIMIT {$start},{$limit}";
			$query2 = $this->db->query($sql2);
			$command2 = $query2->result_array();
			$command2 ['num'] = $command1 ['num'];
			
		}
		if ($command2) {
			return $command2;
		} else {
			return 0;
		}
	}

	/**
	 * 检测用户名和密码
	 * @param $user_id
	 * @return NULL
	 */
	public function check_user($user_name,$user_pwd) {
		$this->db->select('f_id');
		$query = $this->db->get_where('tb_user_info',array('f_username'=>$user_name,'f_userpwd'=>$user_pwd));
		if (is_object($query) && $query->num_rows() > 0) {
			$result = $query->row_array();
			return $result['f_id'];
		} else {
			return false;
		}
	}
	
	/**
	 * 获取用户权限
	 */
	public function get_role($user_id) {
		$this->db->select('f_user_role');
		$query = $this->db->get_where('tb_user_info', array('f_id'=>$user_id));
		if (is_object($query) && $query->num_rows() > 0) {
			$result = $query->row_array();
			return $result['f_user_role'];
		} else {
			return 0;
		}
	}

	/**
	 * 获取用户旗下站点
	 */
	public function get_web($user_id) {
		$this->db->select('f_web_id');
		$query = $this->db->get_where('tb_user_info', array('f_id'=>$user_id));
		if (is_object($query) && $query->num_rows() > 0) {
			$result = $query->row_array();
			return $result['f_web_id'];
		} else {
			return 0;
		}
	}
	
	/**
	 * 获取用户等级
	 */
	public function get_level() {
		$query = $this->db->get('tb_user_role');
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 添加新用户
	 */
	public function add_user($arr) {
		$this->db->insert('tb_user_info',$arr);
		return $this->db->insert_id();
	}
	
	//根据id查找业务
	public function get_one_byid($id) {
		$this->db->where('f_id', $id);
		$query = $this->db->get(self::$table);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return 0;
		}
	}
	
	/**
	 * 修改用户资料
	 */
	public function edit_user($id, $arr) {
		$this->db->where('f_id', $id);
		$this->db->update('tb_user_info', $arr);
		return $this->db->affected_rows();
	}

	/**
	 * 修改登录密码
	 */
	public function update_pwd($uid, $old_pwd, $pwd) {
		$this->db->where('f_id', $uid);
		$this->db->where('f_userpwd', $old_pwd);
		$this->db->update('tb_user_info',array('f_userpwd'=>$pwd));
		return $this->db->affected_rows();
	}
	/**
	 * 删除用户
	 */
	public function del_user($id) {
		$arr['f_status'] = 2;
		if (is_array($id)) {
			for($i = 0,$num = count($id); $i < $num; $i ++) {
				$this->db->where('f_id', $id[$i]);
				$this->db->update(self::$table, $arr);
				//$sql = "DELETE FROM tb_user_info WHERE f_id={$id[$i]}";
				//$this->db->query($sql);
			}
			return 1;
		} else {
			$this->db->where('f_id', $id[$i]);
			$this->db->update(self::$table, $arr);
			return 1;
		}
		
	}

	/**
	 * 获取代理信息
	 */
	public function get_proxy() {
		$this->db->where('f_user_role !=' ,'8000');
		$query = $this->db->get('tb_user_info');
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 更新代理管理站点
	 */
	public function add_user_web($user, $web) {
		$data = array(
               'f_web_id' => $web
            );

		$this->db->where('f_username', $user);
		$this->db->update('tb_user_info', $data); 
		return $this->db->affected_rows();
	}
}
?>