<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class Purview_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	//获取所有权限
	public function get_all_purview($nlevel) {
		$this->db->select('f_classid');
		$this->db->limit(1);
		$query = $this->db->get_where('tb_web_save',array('f_nlevel'=>$nlevel));
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	
	public function get_purview($res,$catid) {
		$sql = "SELECT f_nid,f_name,f_link FROM tb_web_action WHERE f_cid=$catid AND f_nid IN($res)";
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 根据传过来的2级id，查找3级
	 *
	 * @name getProInfoByCatidChild
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category 　\protected\models\WebCat.php
	 * @param int $id
	 * @return array
	 * @version 1.0.0.1
	 */
	public function getProInfoByCatidChild($id) {
		$query = $this->db->get_where('tb_web_action',array('f_cid'=>$id));
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	
	//获取所有操作大类条数
	public function get_nums() {
		$this->db->from('tb_web_config');
		return $this->db->count_all_results();
	}

	//获取所有操作大类
	public function get_config($start,$limit) {
		$this->db->limit($limit,$start);
		$query = $this->db->get('tb_web_config');
		if (is_object($query) && $query->num_rows()) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 获取操作大类
	 * @param unknown_type $cate_id
	 */
	public function get_category($cate_id) {
		$query = $this->db->get_where('tb_web_config',array('f_nid'=>$cate_id));
		if (is_object($query) && $query->num_rows()) {
			return $query->row_array();
		} else {
			return array();
		}
	}

	public function get_user_purview($level) {
		$sql1 = "SELECT `f_classid` FROM `tb_web_save` WHERE `f_nlevel`=$level limit 1";
		$query1 = $this->db->query($sql1);
		$command1 = $query1->row_array();

		$sql2 = "SELECT `f_cid` AS 'groupid',`f_name` AS name FROM `tb_web_config` WHERE `f_cid` IN (SELECT DISTINCT(f_cid) FROM `tb_web_action` WHERE `f_nid` IN ($command1[f_classid]))";

		$query2 = $this->db->query($sql2);
		$command2 = $query2->result_array();
		return $command2;
	}

	/**
	 * 获得所有管理权限
	 *
	 * @name get_manage_class
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param int $start,$limit,$module_id
	 * @return array
	 * @version v 1.0
	 */
	public function get_manage_class($start, $limit,$module_id) {
		if (!empty($module_id)) {
			$sql = "SELECT count(*) num FROM tb_web_action WHERE `f_cid`='{$module_id}'";
			$query1 = $this->db->query($sql);
			$command1 = $query1->row_array();
			$sql2 = "SELECT b.* , a.f_name AS catename FROM `tb_web_action` b,`tb_web_config` a WHERE b.f_cid='{$module_id}' AND a.f_nid='{$module_id}'";
			$query2 = $this->db->query($sql2);
			$command2 = $query2->result_array();
			$command2 ['num'] = $command1 ['num'];
			if ($command2) {
				return $command2;
			} else {
				return 0;
			}
		} else {
			$sql = "SELECT count(*) num FROM tb_web_action";
			$query1 = $this->db->query($sql);
			$command1 = $query1->row_array();
			$sql2 = "SELECT b.* , a.f_name AS catename FROM tb_web_action b LEFT JOIN  tb_web_config a ON a.f_nid=b.f_cid ORDER BY f_isorder limit {$start},{$limit}";
			$query2 = $this->db->query($sql2);
			$command2 = $query2->result_array();
			$command2 ['num'] = $command1 ['num'];
			if ($command2) {
				return $command2;
			} else {
				return 0;
			}
		}
	}

	/**
	 * 系统管理二级页面，所属模块下拉框，读数据库cat
	 *
	 * @name get_cat_fram_class
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return array
	 * @version v 1.0
	 */
	public function get_cat_fram_class() {
		$this->db->select('f_nid,f_name');
		$query = $this->db->get('tb_web_config');
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	 * 系统管理-》页面管理二级分类删除类别
	 *
	 * @name del_class_category
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param int $id
	 * @return array
	 * @version v 1.0
	 */
	public function del_class_category($id) {
		if (is_array ($id)) {
			for($i = 0; $i < count ( $id ); $i ++) {
				$this->db->delete('tb_web_action', array('f_nid' => $id[$i]));
			}
			return true;
		} else {
			return $this->db->delete('tb_web_action',array('f_nid'=>$id));
		}
	}

	/**
	 * 系统管理-》页面管理二级分类根据id查找单个用户信息(修改前先去查找)
	 *
	 * @name find_class_category
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param int $id
	 * @return array
	 * @version v 1.0
	 */
	public function find_class_category($id) {
		$query = $this->db->get_where('tb_web_action',array('f_nid'=>$id));
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}

	//查找分类名称
	public function find_class_name($name) {
		$this->db->select('f_name');
		$query = $this->db->get_where('tb_web_action',array('f_name'=>$name));
		return $query->num_rows();
	}
	/**
	 * 系统管理-》页面管理二级分类修改分类
	 *
	 * @name getEditClasscategory
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param int $nid array $data
	 * @return array
	 * @version v 1.0
	 */
	public function get_edit_class_category($data,$nid) {
		$this->db->where('f_nid',$nid);
		$this->db->update('tb_web_action',$data);
		return $this->db->affected_rows();
	}

	public function get_add_class_category($data) {
		$this->db->insert('tb_web_action',$data);
		return $this->db->affected_rows();
	}

	/**
	 * 获取用户等级信息
	 *
	 * @name getUserLevel
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param
	 * @return array
	 * @version 1.0.0.1
	 */
	public function get_userlevel() {
		$sql = "SELECT f_role_id as f_role_value,f_role_name FROM tb_user_role WHERE f_status=1";
		$query = $this->db->query($sql);
		if (is_object($query) && $query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	/**
	 * 获取用户等级相应的权限
	 *
	 * @name getUserPurview
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param int $start,$limit,$nlevel
	 * @return array
	 * @version 1.0.0.1
	 */
	public function get_userpurview($start, $limit, $nlevel) {
		$sql1 = "SELECT count(*) as num FROM tb_web_action b LEFT JOIN  tb_web_config a ON  a.f_nid=b.f_cid";
		$query1 = $this->db->query($sql1);
		$count = $query1->row_array();
		
		$sql2 = "SELECT b.* , a.f_name AS catename FROM tb_web_action b LEFT JOIN  tb_web_config a ON  a.f_nid=b.f_cid LIMIT $start,$limit";
		$query2 = $this->db->query($sql2);
		$arrList = $query2->result_array();
	
		$sql3 = "SELECT `f_classid` FROM `tb_web_save` WHERE `f_nlevel`=$nlevel LIMIT 1";
		$query3 = $this->db->query($sql3);
		$catidsin = $query3->row_array();
		$str = $catidsin ["f_classid"];
		$arrIn = explode ( ',', $str );
		for($i = 0; $i < count ( $arrList ); $i ++) {
			$f_nid = $arrList [$i] ['f_nid'];
			if (in_array ( $f_nid, $arrIn )) {
				$arrList [$i] ['check'] = 1;
			} else {
				$arrList [$i] ['check'] = 0;
			}
		}
		$arrList ['count'] = $count ['num'];
		return $arrList;
	}
	
	/**
	 * 更新用户权限，若是第一次则添加
	 *
	 * @name getAddPurview
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param int $id,$lvel
	 * @return int
	 * @version 1.0.0.1
	 */
	public function add_purview($postId, $level) {
		$sql = "SELECT * FROM tb_web_save WHERE f_nlevel='{$level}'";
		$query = $this->db->query($sql);
		$arr = $query->row_array();
		if (empty ( $arr )) {
			//$postId = trim ( $postId, ',' );
			$insertSql = "INSERT INTO tb_web_save(f_nlevel,f_classid)VALUES('{$level}','{$postId}')";
			$insertResult = $this->db->query($insertSql);
			return $this->db->insert_id();
		} else {
			$calssidsOld = explode ( ',', $arr ['f_classid'] );
			$calssidsPost = explode ( ',', $postId );
			if ($arr['f_classid'] == '') {
				$classIdNew = $postId;
			}
			else {
				$classIdNew = implode(',', array_unique(array_merge($calssidsOld, $calssidsPost)));
			}
			$updateSql = "UPDATE tb_web_save SET f_classid='{$classIdNew}'  WHERE f_nlevel='{$level}'";
			
			$updateResult = $this->db->query($updateSql);
			return $this->db->affected_rows();
		}
	}
	
	/**
	 * 更新用户权限
	 *
	 * @name getAddPurview
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param int $id,$lvel
	 * @return int
	 * @version 1.0.0.1
	 */
	public function del_purview($id, $level) {
		$selectSql = "SELECT f_classid FROM tb_web_save WHERE f_nlevel='{$level}'";
		$query  = $this->db->query($selectSql);
		$selectResult = $query->row_array();
		$splitArray = explode ( ',', $selectResult ['f_classid'] );
	
		if (is_array ( $id )) {
			$result = array_diff ( $splitArray, $id );
		} else {
			$newid [] = $id;
			$result = array_diff ( $splitArray, $newid );
		}
		$impArray = implode ( ',', $result );
		$upSql = "UPDATE tb_web_save SET f_classid='{$impArray}' WHERE f_nlevel={$level}";
		$command = $this->db->query($upSql);
		return $this->db->affected_rows();
	}
	
	/**
	 * 系统管理-》页面管理二级分类删除类别
	 *
	 * @name DelClasscAtegory
	 * @access public
	 * @author Dengmengmeng 2012-02-20
	 * @category 　\protected\models\WebCat.php
	 * @param int $id
	 * @return array
	 * @version :1.0.0.1
	 */
	public function getdelcategory($id) {
		if (is_array($id)) {
			for($i = 0; $i < count ( $id ); $i ++) {
				$this->db->where('f_nid',$id[$i]);
				$this->db->delete('tb_web_config');
			}
			return 1;
		} else {
			$this->db->where('f_nid',$id);
			if($this->db->delete('tb_web_config')) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	/**
	 * 系统管理-》页面管理二级分类修改分类
	 *
	 * @name getEditClasscategory
	 * @access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param int $nid array $data
	 * @return array
	 * @version v 1.0
	 */
	public function get_edit_category($data,$nid) {
		$this->db->where('f_nid',$nid);
		$this->db->update('tb_web_config',$data);
		return $this->db->affected_rows();
	}
	
	//查找分类名称
	public function find_category_name($name) {
		$this->db->select('f_name');
		$query = $this->db->get_where('tb_web_config',array('f_name'=>$name));
		return $query->num_rows();
	}
	
	/**
	 * 系统管理-》系统管理添加
	 *
	 * @name getAddClassCategory
	 * @access public
	 * @author Dengmengmeng 2012-02-20
	 * @category 　\protected\models\WebCat.php
	 * @param $fname,$fcatid,$fisorder,$fntype,$flink
	 * @return array
	 * @version :1.0.0.1
	 */
	public function getaddcategory($arr) {
		$this->db->where('f_name',$arr['f_name']);
		$result = $this->db->get('tb_web_config');
		if (is_object($result) && $result->num_rows() > 0) {
			return 0;
		} else {
			$this->db->insert('tb_web_config', $arr);
			return $this->db->insert_id();
		}
	}
	
}