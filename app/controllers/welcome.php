<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Welcome extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 后台登录页面
	 */
	public function index() {
		$this->load->view("welcome/welcome");
	}
	
	/**
	 * 登录成功之后显示默认页面
	 */
	public function default_page() {
		$arr = array( 
			'server_host' => $_SERVER['SERVER_SOFTWARE'], 
			'os' => get_system(), 
			'sql' => $this->db->version(), 
			'php' => phpversion(), 
			'register_globals' => ini_get('register_globals') ? '开启' : '关闭', 
			'file_uploads' => ini_get('file_uploads') ? '允许' : '禁止', 
			'upload_max_filesize' => ini_get('upload_max_filesize'), 
			'post_max_size' => ini_get('post_max_size'), 
			'php_display_errors' => ini_get('display_errors') ? '开启' : '禁用', 
			'php_error_reporting' => ini_get('error_reporting'), 
			'magic_quotes_gpc' => ini_get('magic_quotes_gpc') ? '开启' : '禁用', 
			'ip' => get_ip(), 
			'time' => date('Y-m-d H:i:s'), 
			'domain' => get_domain() 
		);
		$this->load->view('welcome/default', $arr);
	}
	

	/**
	 * 后台首页
	 */
	public function show() {
		if (!$this->session->userdata('USER'))exit('get out!');
		$user = $this->session->userdata('USER');
		$this->load->model('purview_model');
		$menu = $this->getCatidByNlevel($user['user_level']);
		$arr['data'] = $menu;
		$this->load->view('welcome/show',array('menu'=>json_encode($arr),'num'=>count($arr['data'])));
		
	}
	/**
	 * 检测登录
	 */
	public function login_check() {
		$user_name = quotes(trim($this->input->post('user_name')));
		$user_pwd  = md5(quotes(trim($this->input->post('user_pwd'))). SF_KEY);
		if ($user_name != '' && $user_pwd != '') {
			$this->load->model('user_model');
			$user_id = $this->user_model->check_user($user_name,$user_pwd);
			if ($user_id) {
				$this->get_purview($user_id);
				echo '{success:true,msg:{reason:"ok"}}';
			} else {
				echo '{success:true,msg:{reason:"error"}}';
			}
		} else {
			echo '{success:true,msg:{reason:"error"}}';
		}
	}
	
	/**
	 * 用户登录之后根据权限，获取操作列表
	 */
	public function get_purview($user_id) {
		//$user_id = 1;
		$this->load->model('user_model');
		$user_level = $this->user_model->get_role($user_id);
		//$user_web_id = $this->user_model->get_web($user_id);
		//$user_level = 8000;
		//$user_id = 1;
		//获得该等级用户 有多少大类管理权限
		$categroys = $this->getCatidByNlevel($user_level);
		
		$pm_arrlist = array();
		foreach ((array)$categroys as $level_cats) {
			$catid = $level_cats['groupid'];
			$catname = $level_cats['name'];
		
			$arr_temp = $this->getProInfoByCatid($catid, $user_level);
		
			//3级分类操作，现在不需要开启
			/* for ($i = 0; $i < count($arr_temp); $i++) {
				if ($arr_temp[$i]['f_link'] == '') {
					$three_child = $this->purview_model->getProInfoByCatidChild($arr_temp[$i]['f_nid']);
					$arr_temp[$i]['f_link'] = $three_child;
				}
			} */
			$pm_arrlist[$catid] = array($catname,$arr_temp);
		}
		$this->session->set_userdata(array('PMLIST' => $pm_arrlist));
		$this->session->set_userdata(array('USER' => array('user_id'=>$user_id, 'user_level'=>$user_level)));
	}
	
	/**
	 * 获得该等级用户 有多少大类管理权限
	 *
	 * @name getCatidByNlevel
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param int $nlevel
	 * @return array
	 * @version 1.0.0.1
	 */
	public function getCatidByNlevel($nlevel) {
		$sql1 = "SELECT `f_classid` FROM `tb_web_save` WHERE `f_nlevel`=$nlevel limit 1";
		$query1 = $this->db->query($sql1);
		$command1 = $query1->row_array();
	
		$sql2 = "SELECT `f_nid` AS 'groupid',`f_name` AS name FROM `tb_web_config` WHERE `f_nid` IN (SELECT DISTINCT(f_cid) FROM `tb_web_action` WHERE `f_nid` IN ($command1[f_classid]))";
		$query2 = $this->db->query($sql2);
		$command2 = $query2->result_array();
		return $command2;
	}
	/**
	 * 通过类型和等级获得用户的权限 根据isorder排序
	 *
	 * @name getProInfoByCatid
	 * @access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category 　\protected\models\WebCat.php
	 * @param int $catid,$nlevel
	 * @return array
	 * @version 1.0.0.1
	 */
	public function getProInfoByCatid($catid, $nlevel) {
		$this->load->model('purview_model');
		$class = $this->purview_model->get_all_purview($nlevel);
		$purviews = $this->purview_model->get_purview($class['f_classid'],$catid);
		return $purviews;
	}
	
	
	public function accordion() {
		$arrlist = $this->session->userdata('PMLIST');
		$iconArr = array('user','money','software','systemmanage','shop','web','linux','ad','data','revenue');
		$menuArr = array();
		$i = 0;
		foreach ((array)$arrlist as $key => $value)	{
			$id = $key;
			$menuname = $value[0];
			$str = "{title:'" . $menuname . "',id:'" . $id . "',iconCls:'" . $iconArr[$i] . "'}";
			$menuArr[] = $str;
			$i++;
		}
		$result = "[" . implode(",", $menuArr) . "]";
		echo $result;
	}
	
	
	/**
	 * 获取手风琴下面的树数据
	 *
	 * @name AccordionChild
	 * @Access public
	 * @author More  vericmore@gmail.com 2012-02-21
	 * @category　\protected\controllers\SiteController.php
	 * @param
	 * @return
	 * @version 1.0.0.1
	 */
	public function accordion_child() {
		//$this->checkSession($_SESSION["USER"]["userId"]);
		$orgId = $this->input->get_post('id');
		$twoArray = $this->session->userdata('PMLIST');
		if (count($twoArray) > 0) {
			$tempArray = array();
			foreach ($twoArray as $key => $value) {
				if ($orgId == $key) {
					$menuArray = $twoArray[$orgId][1];
					for ($i = 0;$i < count($menuArray);$i++) {
						$twoId = $menuArray[$i]['f_nid'];
						$twoUrl = $menuArray[$i]['f_link'];
						$twoName = $menuArray[$i]['f_name'];
						if (is_array($twoUrl))
						{
							$str = "{text:'" . $twoName . "',id:'" . $twoId . "',iconCls:'bulletin'}";
						}
						else
						{
							$str = "{text:'" . $twoName . "',id:'" . $twoId . "',url:'" . site_url() . "/" . $twoUrl . "',leaf:true,qtip:'" . $twoName . "',iconCls:'accordion'}";
						}
						$tempArray[] = $str;
					}
				} else {
					$menuArray = $twoArray[$key][1];

					for ($i = 0;$i < count($menuArray);$i++) {
						$twoUrl = $menuArray[$i]['f_link'];
						$twoId = $menuArray[$i]['f_nid'];
						if (is_array($twoUrl)) {
							for ($j = 0;$j < count($twoUrl);$j++) {
								if ($orgId == $twoUrl[$j]['f_catid'] && $twoId = $orgId) {
									$str = "{text:'" . $twoUrl[$j]['f_name'] . "',id:'tree_" . $twoId . "',url:'" . site_url() . "/" . $twoUrl[$j]['f_link'] . "',leaf:true,qtip:'" . $twoUrl[$j]['f_name'] . "',iconCls:'accordion'}";
									$tempArray[] = $str;
								}
							}
						}
					}

				}
			}

			$sonResult = "[" . implode(",", $tempArray) . "]";
			echo $sonResult;
		} else {
			echo "[{text:'无权限',url:''}]";
		}
	}
}