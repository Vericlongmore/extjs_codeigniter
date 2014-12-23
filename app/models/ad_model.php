<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class Ad_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 获取所有广告
	 */
	public function get_list() {
		$query = $this->db->get('tb_ad_list');
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	/**
	 * 获取所有来源广告
	 */
	public function get_from_list($start, $limit, $module_id, $web_id) {
		//$sql = "SELECT * FROM "
		/*$sql = "SELECT l.*,t.f_name AS web_name FROM tb_ad_list l,tb_ad_type t WHERE t.f_id IN($web_id) AND l.f_pid=t.f_id AND l.status=1";
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}*/
		if (!empty($module_id)) {
			$sql = "SELECT count(*) num FROM tb_ad_list l WHERE l.f_pid={$module_id} AND l.f_status=1";
			$query1 = $this->db->query($sql);
			$command1 = $query1->row_array();
			$sql2 = "SELECT l.*,t.f_name AS web_name FROM tb_ad_list l LEFT JOIN tb_ad_type t ON l.f_pid=t.f_id WHERE l.f_pid={$module_id} AND l.f_status=1";
			$query2 = $this->db->query($sql2);
			$command2 = $query2->result_array();
			$command2 ['num'] = $command1 ['num'];
		} else {
			$sql = "SELECT count(*) num FROM tb_ad_list l,tb_ad_type t WHERE t.f_id IN($web_id) AND l.f_pid=t.f_id AND l.f_status=1";
			$query1 = $this->db->query($sql);
			$command1 = $query1->row_array();
			$sql2 = "SELECT l.*,t.f_name AS web_name FROM tb_ad_list l,tb_ad_type t WHERE t.f_id IN($web_id) AND l.f_pid=t.f_id AND l.f_status=1 LIMIT {$start},{$limit}";
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
	 * 获取单条广告
	 */
	public function get_one($id) {
		$query = $this->db->get_where('tb_ad_list',array('id'=>$id));
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	/**
	 * 获取单条来源广告
	 */
	public function get_one_from($id) {
		$sql = "SELECT l.*,t.web_name AS web_name FROM tb_ad_list AS l,tb_ad_type AS t WHERE l.id={$id} AND l.fid=t.web_id";
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	/**
	 * 添加广告
	 */
	
	public function add_one($arr) {
		$this->db->insert('tb_ad_list', $arr);
		return $this->db->insert_id();
	}
	
	/**
	 * 修改一条广告
	 */
	public function update_one($arr) {
		$this->db->where('f_id', $arr['id']);
		$this->db->update('tb_ad_list', $arr);
		return $this->db->affected_rows();
		
	}
	
	/**
	 * 删除一条广告
	 */
	public function del_one($id) {
		if (is_array($id)) {
			for($i = 0,$num = count($id); $i < $num; $i ++) {
				$sql = "UPDATE tb_ad_list SET f_status=0 WHERE f_id={$id[$i]}";
				$this->db->query($sql);
			}
			return 1;
		} else {
			$sql = "UPDATE tb_ad_list SET f_status=0 WHERE f_id={$id}";
			$command = $this->db->query($sql);
			if ($command) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	/**
	 * 获取所有广告来源
	 */
	public function ad_type($web_id) {
		$sql = "SELECT f_id,f_name FROM tb_ad_type WHERE f_id IN({$web_id})";
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	/**
	 * 查询某个网站下的广告
	 */
	public function get_type_list($id) {
		$query = $this->db->get_where('tb_ad_list', array('f_id'=>$id));
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 获取所有站点
	 */
	public function get_web() {
		$query = $this->db->get('tb_ad_type');
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 管理站点
	 */
	public function get_ad_types($start, $limit) {
		$sql = "SELECT count(*) num FROM tb_ad_type";
		$query1 = $this->db->query($sql);
		$command1 = $query1->row_array();
		$sql2 = "SELECT * FROM tb_ad_type ORDER BY f_create_time DESC LIMIT {$start},{$limit}";
		$query2 = $this->db->query($sql2);
		$command2 = $query2->result_array();
		$command2 ['num'] = $command1 ['num'];
		if ($command2) {
			return $command2;
		} else {
			return 0;
		}
	}

	/**
	 * 添加站点
	 */
	public function add_ad_type($arr) {
		$this->db->insert('tb_ad_type',$arr);
		return $this->db->insert_id();
	}

	/**
	 * 查找单个站点
	 */
	public function find_ad_type($id) {
		$this->db->select('f_id, f_from, f_name');
		$query = $this->db->get_where('tb_ad_type', array('f_id'=>$id, 'f_status'=>1));
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}

	/**
	 * 修改站点信息
	 */
	public function edit_ad_type($data, $id) {
		$this->db->where('f_id',$id);
		$this->db->update('tb_ad_type',$data);
		return $this->db->affected_rows();
	}

	/**
	 * 删除站点
	 */
	public function del_ad_type($id) {
		if (is_array ($id)) {
			for($i = 0; $i < count ( $id ); $i ++) {
				$this->db->delete('tb_ad_type', array('f_id' => $id[$i]));
			}
			return true;
		} else {
			return $this->db->delete('tb_ad_type', array('f_id'=>$id));
		}
	}
}
