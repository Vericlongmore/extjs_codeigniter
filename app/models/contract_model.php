<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class contract_model extends CI_Model {
	static $table = 'tb_contract';
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 获取所有合同
	 */
	public function get_list($start, $limit, $type_id) {
		if ($type_id > 0) {
			$sql1 = "SELECT count(*) as num FROM ".self::$table." WHERE f_type='{$type_id}'";
			$count = $this->db->query($sql1)->row_array();
			
			$sql2 = "SELECT * FROM ".self::$table." WHERE f_type=$type_id LIMIT $start,$limit";
			$arrList = $this->db->query($sql2)->result_array();
			
			$arrList['num'] = $count['num'];
			if ($arrList) {
				return $arrList;
			} else {
				return 0;
			}
		} else {
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
	}
	
	//添加合同
	public function get_add_contract($arr) {
		$this->db->insert(self::$table, $arr);
		return $this->db->insert_id();
	}
	
	//根据name查找合同
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
	//根据id查找合同
	public function get_one_byid($id) {
		$this->db->where('f_id', $id);
		$query = $this->db->get(self::$table);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return 0;
		}
	}
	//编辑合同
	public function get_edit_type($arr, $id) {
		$this->db->where('f_id', $id);
		$this->db->update(self::$table, $arr);
		return $this->db->affected_rows();
	}
	
	/**
	 * 删除合同
	 */
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
	
	//编辑合同
	public function get_edit_status($id) {
		if (is_array ($id)) {
			for($i = 0; $i < count ( $id ); $i ++) {
				$this->db->where('f_id',$id);
				$this->db->update(self::$table, array('f_status' => 2));
			}
			return true;
		} else {
			$this->db->where('f_id',$id);
			return $this->db->update(self::$table, array('f_status' => 2));
		}
	}
	/**
	 * 获取需求对应的用户
	 *
	 * @name getUserPurview
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param int $start,$limit,$nlevel
	 * @return array
	 * @version 1.0.0.1
	 */
	public function get_client_type($start, $limit, $type_id) {
		$sql1 = "SELECT count(*) as num FROM ".self::$table." WHERE f_type='{$type_id}'";
		$count = $this->db->query($sql1)->row_array();
	
		$sql2 = "SELECT * FROM ".self::$table." WHERE f_type=$type_id LIMIT $start,$limit";
		$arrList = $this->db->query($sql2)->result_array();
	
		$arrList['num'] = $count['num'];
		if ($arrList) {
			return $arrList;
		} else {
			return 0;
		}
	}
	
	
	//获取所有金额总数
	public function get_count_all() {
		$sql = "SELECT SUM(f_price) as num FROM " .self::$table;
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	
	//获取所有定金金额总数
	public function get_count_payment() {
		$sql = "SELECT SUM(f_deposit) as num FROM " .self::$table;
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	
	//获取交易完成金额
	public function count_payment_complete() {
		$sql = "SELECT SUM(f_deposit) as num FROM " .self::$table. " WHERE f_status=1";
		$query = $this->db->query($sql);
		
		$sql2 = "SELECT SUM(f_price) as num FROM " .self::$table. " WHERE f_status=2";
		$query2 = $this->db->query($sql2);
		
		if ($query->num_rows() > 0 && $query2->num_rows() > 0) {
			$result = $query->row_array();
			$result2 = $query2->row_array();
			$count_num = $result['num'] + $result2['num'];
			return $count_num;
		} else {
			return 0;
		}
	}
}
