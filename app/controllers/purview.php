<?php
if (!defined('BASEPATH')) exit('No direct script access allowed!');
class Purview extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('purview_model');
	}

	//操作页面
	public function operate() {
		if (!$this->session->userdata('USER')) exit('get out!');
		$this->load->view('purview/operate');
	}

	//获取所有类别
	public function get_action() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$module_id = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] : '';
		$num = $this->purview_model->get_manage_class($start, $limit, $module_id);
		if ($num) {
			$n = array_pop($num);
			$num = '{results:' . $n . ',items:' . json_encode($num) . '}';
			echo $num;
		} else {
			echo '[0]';
		}
	}
	
	//系统功能
	public function economy() {
		if (!$this->session->userdata('USER')) exit('get out!');
		$this->load->view('purview/economy');
	}

	//系统功能列表
	public function config() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$num = $this->get_config($start,$limit);
		$n = array_pop($num);
		$num = '{results:'.$n.',items:'.json_encode($num).'}';
		echo $num;

	}

	////系统功能列表
	private function get_config($start, $limit) {
		$num = $this->purview_model->get_nums('sf_web_config');
		$info = $this->purview_model->get_config($start,$limit,'sf_web_config');
		$info ['num'] = $num;
		return $info;
	}

	//功能权限管理
	public function page() {
		$this->load->view('purview/page');	
	}
	
	public function user_purview() {
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$nlevel = isset($_REQUEST['levelId']) ? $_REQUEST['levelId'] :'';
		if (empty($nlevel)) {
			$nlevel = 8000;
		}
		$result = $this->purview_model->get_userpurview($start,$limit,$nlevel);
		$n = array_pop($result);
		$num = '{results:'.$n.',items:'.json_encode($result).'}';
		echo $num;
	}
	
	//系统管理->页面权限管理,下拉框选项获取用户等级
	public function userlevel() {
		$result = $this->purview_model->get_userlevel();
		if ($result) {
			$num = json_encode($result);
			echo $num;
		}
	}
	
	/**
	 * 系统管理->页面权限管理，下拉框选中后，下面的表格显示数据，所选角色对应的权限
	 *
	 * @name UserPurview
	 * @Access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category　\protected\controllers\ManagecatController.php
	 * @param
	 * @return string
	 * @version 1.0.0.1
	 */
	/* public function actionUserPurview()
	{
		$start = $_REQUEST['start'];
		$limit = $_REQUEST['limit'];
		$nlevel = $_REQUEST['levelId'];
		if (empty($nlevel))
		{
			$nlevel = 27000;
		}
		$result = WebCat::model()->getUserPurview($start,$limit,$nlevel);
		$n = array_pop($result);
		$num = '{results:'.$n.',items:'.json_encode($result).'}';
			echo $num;
		} */
	
	//新增或者更新用户权限
	public function add_purview() {
		$f_nid = $this->input->post('id');
		$nlevel = $this->input->post('nlevel');
		$result = $this->purview_model->add_purview($f_nid,$nlevel);
		if ($result > 0 ) {
			echo 1;
		} else {
			echo 0;
		}
	}
	
	/**
	 * 删除用户权限
	 *
	 * @name DelPurview
	 * @Access public
	 * @author More  vericmore@gmail.com 2012-02-23
	 * @category　\protected\controllers\ManagecatController.php
	 * @param array $_POST
	 * @return string
	 * @version 1.0.0.1
	 */
	public function actionDelPurview() {
		$nlevel = $this->input->post('nlevel');
		$f_nid = $this->input->post('id');
		if (isset($f_nid[1]) && $f_nid[1] != '' ) {
			$f_nid = explode(',', $f_nid);
		}
		$result = $this->purview_model->delpurview($f_nid,$nlevel);
		if ($result > 0) {
			echo 1;
		} else {
			echo 0;
		}
	}
	
	/**
	 * 查找单条数据
	 */
	public function find_category() {
		$cate_id = $this->input->get('cateId');
		$info = $this->purview_model->get_category($cate_id);
		return_json($info);
	}

	/**
	 * 系统管理二级页面，所属模块下拉框，读数据库cat
	 *
	 * @name CatFramClass
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function cat_fram_class() {
		$framcat = $this->purview_model->get_cat_fram_class();
		if (!empty($framcat)) echo json_encode($framcat);
	}

	/**
	 * 系统管理->页面管理二级分类删除类别
	 *
	 * @name del_class_category
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function del_class_category() {
		$id = $this->input->post('id');
		if (isset($id[1]) && $id[1] != '' ) $id = explode(',', $id);
		echo $this->purview_model->del_class_category($id);
	}


	/**
	 * 系统管理->页面管理二级分类根据id查找单个用户信息(修改前先去查找)
	 *
	 * @name find_class_category
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function find_class_category() {
		$id = $_GET['userId'];
		$result = $this->purview_model->find_class_category($id);
		return_json($result);
	}

	/**
	 * 系统管理->页面管理二级分类修改分类
	 *
	 * @name edit_class_category
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function edit_class_category() {
		if($this->input->post()) {
			$nid = $this->input->post('f_nid');
			$data = array(
					'f_name' => $this->input->post('f_name'),
					'f_cid' => $this->input->post('f_cid'),
					'f_isorder' => $this->input->post('f_isorder'),
					'f_link' => $this->input->post('f_link')
				);
			$this->load->model('purview_model');
			/* $find_name = $this->purview_model->find_class_name($this->input->post('f_name'));
			if ($find_name > 0) {
				echo "{ success: false, errors: { reason: '功能名重复 ' }}";
				exit;
			} */
			$result = $this->purview_model->get_edit_class_category($data,$nid);
			if ($result > 0) {
				return_success();
			} else {
				return_error();
			}
		}
	}

	/**
	 * 系统管理->页面管理二级分类添加
	 *
	 * @name add_class_category
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function add_class_category() {
		if($this->input->post()) {
			$data = array(
				'f_name' => $this->input->post('f_name'),
				'f_cid' => $this->input->post('f_cid'),
				'f_isorder' => $this->input->post('f_isorder'),
				'f_link' => $this->input->post('f_link')
			);
			$this->load->model('purview_model');

			$find_name = $this->purview_model->find_class_name($this->input->post('f_name'));
			if ($find_name > 0) {
				echo "{ success: false, errors: { reason: '类别重复 ' }}";
				exit;
			}
			$result = $this->purview_model->get_add_class_category($data);
			if ($result > 0) {
				return_success();
			} else {
				return_error();
			}
		}
	}

	/**
	 * 系统管理->页面权限管理,下拉框选项获取用户等级
	 *
	 * @name get_user_level
	 * @Access public
	 * @More  vericmore@gmail.com
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function user_level() {
		$this->load->model('purview_model');
		$result = $this->purview_model->get_all_role();
		if (!empty($result)) {
			echo json_encode($result);
		}
	}

	/**
	 * 删除用户权限
	 *
	 * @name DelPurview
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-24
	 * @category
	 * @param array $_POST
	 * @return string
	 * @version 1.0.0.1
	 */
	public function del_purview() {
		$nlevel = $this->input->get_post('nlevel');
		$f_nid = $this->input->get_post('id');

		if (isset($f_nid[1])) {
			$f_nid = explode(',', $f_nid);
		}
		$this->load->model('purview_model');
		$result = $this->purview_model->del_purview($f_nid,$nlevel);
		echo json_encode($result);
	}
	
	
	public function del_category() {
		$id = $this->input->post('id');
		$this->load->model('purview_model');
		if (isset($id[1])) {
			$id = explode(',', $id);
		}
		$info = $this->purview_model->getdelcategory($id);
		echo $info;
	}
	
	/**
	 * 系统管理->页面管理二级分类修改分类
	 *
	 * @name edit_class_category
	 * @Access public
	 * @More  vericmore@gmail.com 2013-01-23
	 * @category
	 * @param
	 * @return string
	 * @version v 1.0
	 */
	public function edit_category() {
		if($this->input->post()) {
			$nid = $this->input->post('f_nid');
			$data = array(
					'f_name' => $this->input->post('f_name'),
			);
	
			$this->load->model('purview_model');
	
			$find_name = $this->purview_model->find_category_name($this->input->post('f_name'));
			if ($find_name > 0) {
				echo "{ success: false, errors: { reason: '功能名重复 ' }}";
				exit;
			}
			$result = $this->purview_model->get_edit_category($data,$nid);
			if ($result > 0) {
				return_success();
			} else {
				return_error_name();
			}
		}
	}
	
	public function add_category() {
		if($this->input->post()) {
			$arr['f_name'] = $this->input->post('f_name');
			$this->load->model('purview_model');
			$result = $this->purview_model->getaddcategory($arr);
			if ($result == 0)
			{
				return_error_name();
			} else if ($result > 0) {
				return_success();
			} else {
				echo return_error();
			}
		} else {
			echo return_error();
		}
	}
}