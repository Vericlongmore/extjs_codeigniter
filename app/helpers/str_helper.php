<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('return_json')) {
	function return_json($str) {
		echo '{success : true, data : '.json_encode($str).'}';
	}
}

if (!function_exists('return_error')) {
	function return_error_json($str) {
		echo '{success : false, reason : '.json_encode($str).'}';
	}
}

if (!function_exists('return_success')) {
	function return_success() {
		echo '{success:true}';
	}
}

if (!function_exists('return_error')) {
	function return_error() {
		echo '{success:false}';
	}
}
if (!function_exists('return_error_name')) {
	function return_error_name() {
		echo "{ success: false, errors: { reason: '错误 ' }}";
	}
}


if (!function_exists('get_system'))
{
	function get_system()
	{
		$sys = $_SERVER['HTTP_USER_AGENT'];
		if (stripos($sys, "NT 6.1")) $os = "Windows 7";
		elseif (stripos($sys, "NT 6.0")) $os = "Windows Vista";
		elseif (stripos($sys, "NT 5.1")) $os = "Windows XP";
		elseif (stripos($sys, "NT 5.2")) $os = "Windows Server 2003";
		elseif (stripos($sys, "NT 5")) $os = "Windows 2000";
		elseif (stripos($sys, "NT 4.9")) $os = "Windows ME";
		elseif (stripos($sys, "NT 4")) $os = "Windows NT 4.0";
		elseif (stripos($sys, "98")) $os = "Windows 98";
		elseif (stripos($sys, "95")) $os = "Windows 95";
		elseif (stripos($sys, "Mac")) $os = "Mac";
		elseif (stripos($sys, "Linux")) $os = "Linux";
		elseif (stripos($sys, "Unix")) $os = "Unix";
		elseif (stripos($sys, "FreeBSD")) $os = "FreeBSD";
		elseif (stripos($sys, "SunOS")) $os = "SunOS";
		elseif (stripos($sys, "BeOS")) $os = "BeOS";
		elseif (stripos($sys, "OS/2")) $os = "OS/2";
		elseif (stripos($sys, "PC")) $os = "Macintosh";
		elseif (stripos($sys, "AIX")) $os = "AIX";
		else $os = "未知操作系统";
		return $os;
	}
}

if (!function_exists('get_ip'))
{
	function get_ip()
	{
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) $ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) $ip = getenv("REMOTE_ADDR");
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = "unknown";
		return ($ip);
	}
}


if (!function_exists('get_domain'))
{
	function get_domain() {
		/* 协议 */
		$protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

		/* 域名或IP地址 */
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		} elseif (isset($_SERVER['HTTP_HOST'])) {
			$host = $_SERVER['HTTP_HOST'];
		} else {
			/* 端口 */
			if (isset($_SERVER['SERVER_PORT'])) {
				$port = ':' . $_SERVER['SERVER_PORT'];
				if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
					$port = '';
				}
			} else {
				$port = '';
			}

			if (isset($_SERVER['SERVER_NAME'])) {
				$host = $_SERVER['SERVER_NAME'] . $port;
			} elseif (isset($_SERVER['SERVER_ADDR'])) {
				$host = $_SERVER['SERVER_ADDR'] . $port;
			}
		}

		return $protocol . $host;
	}
}

if (!function_exists('quotes')) {
	function quotes($content)
	{
		//如果magic_quotes_gpc=Off，那么就开始处理
		if (!get_magic_quotes_gpc()) {
			//判断$content是否为数组
			if (is_array($content)) {
				//如果$content是数组，那么就处理它的每一个单无
				foreach ($content as $key=>$value) {
					$content[$key] = addslashes($value);
				}
			} else {
				//如果$content不是数组，那么就仅处理一次
				addslashes($content);
			}
		} else {
			//如果magic_quotes_gpc=On，那么就不处理
		}
		//返回$content
		return $content;
	}
}