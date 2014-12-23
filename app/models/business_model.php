<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class Business_model extends CI_Model {
	static $table = 'tb_business';
	public function __construct() {
		parent::__construct();
	}
	
	 //获取所有业务
	public function get_list($start, $limit) {
		$sql = "SELECT count(*) num FROM ".self::$table;
		$command1 = $this->db->query($sql)->row_array();
		
		$sql2 =  "SELECT * FROM ".self::$table;
		$command2= $this->db->query($sql2)->result_array();

		$command2 ['num'] = $command1 ['num'];
		if ($command2) {
			return $command2;
		} else {
			return 0;
		}
	}
	
	//添加业务
	public function get_add_type($arr) {
		$this->db->insert(self::$table, $arr);
		return $this->db->insert_id();
	}
	
	//根据name查找业务
	public function get_one($name) {
		$this->db->where('f_name', $name);
		$query = $this->db->get(self::$table);
		if (is_object($query) && $query->num_rows() > 0) {
			$result = $query->row_array(); 
			return $result['f_name'];
		} else {
			return 0;
		}
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
	//编辑业务
	public function get_edit_type($arr, $id) {
		$this->db->where('f_id', $id);
		$this->db->update(self::$table, $arr);
		return $this->db->affected_rows();
	}
	
	//删除业务
	public function get_del_type($id) {
		if (is_array ($id)) {
			for($i = 0; $i < count ( $id ); $i ++) {
				$this->db->delete(self::$table, array('f_id' => $id[$i]));
			}
			return true;
		} else {
			return $this->db->delete(self::$table, array('f_id'=>$id));
		}
	}
	
	/**
	 * 获取需求类型
	 *
	 * @name getUserLevel
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param
	 * @return array
	 * @version 1.0.0.1
	 */
	public function get_option_type() {
		$query = $this->db->get(self::$table);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
}
