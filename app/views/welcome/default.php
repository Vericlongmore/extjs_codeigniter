<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
a						{ text-decoration: none; color: #002280 }
a:hover					{ text-decoration: underline }
body					{ font-size: 9pt; }
table					{ font: 9pt Tahoma, Verdana; color: #000000 }
input,select,textarea	{ font: 9pt Tahoma, Verdana; color: #000000; font-weight: normal; background-color: #F8F8F8 }
select					{ font: 9pt Tahoma, Verdana; color: #000000; font-weight: normal; background-color: #F8F8F8 }
.nav					{ font: 9pt Tahoma, Verdana; color: #000000; font-weight: bold }
.nav a					{ color: #000000 }
.header					{ font: 9pt Tahoma, Verdana; color: #FFFFFF; font-weight: bold; background-color: #8CBDEF }
.header a				{ color: #FFFFFF }
.category				{ font: 9pt Tahoma, Verdana; color: #000000; background-color: #fcfcfc }
.tableborder			{ background: #CDE2F8; border: 1px solid #8CBDEF }
.singleborder			{ font-size: 0px; line-height: 1px; padding: 0px; background-color: #F8F8F8 }
.smalltxt				{ font: 9pt Tahoma, Verdana }
.outertxt				{ font: 9pt Tahoma, Verdana; color: #000000 }
.outertxt a				{ color: #000000 }
.bold					{ font-weight: bold }
</style>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><div align="center"><strong>
        <h3>欢迎使用OA信息管理系统</h3>
        </strong></div></td>
  </tr>
  <tr>
    <td><table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
        <tr class="header">
          <td height="25">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><a href="javascript:;"><strong>系统信息</strong></a></td>
                <td><div align="right"></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="3" cellspacing="1">
              <tr bgcolor="#FFFFFF">
                <td width="50%" height="25">服务器软件：<?php echo $server_host;?></td>
                <td height="25">操作系统：<?php echo $os;?></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td height="25">PHP版本：<?php echo $php;?></td>
                <td height="25">MYSQL版本：<?php echo $sql;?></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td height="25">全局变量：<?php echo $register_globals;?></td>
                <td height="25">上传文件：<?php echo $file_uploads;?></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td height="25">登陆者IP：<?php echo $ip;?></td>
                <td height="25">当前时间：<?php echo $time;?></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td height="25">程序版本：<a href="#" target="_blank"><strong>OA v1.0</strong></a></td>
                <td height="25">使用域名:<?php echo $domain;?></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td width="50%" height="25">会员注册：关闭</td>
                <td height="25">会员投稿：关闭</td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td height="25">未审核评论：<a href="#">0</a> 条&nbsp;&nbsp;,&nbsp;&nbsp;未审核会员: <a href="#">0</a> 人</td>
                <td height="25">管理员个数：<a href="#">2</a> 人</td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
        <tr class="header">
          <td height="25">开发团队</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="3" cellspacing="1">

			<tr bgcolor="#FFFFFF">
                <td height="25">开发与支持团队</td>
                <td></td>
			</tr>
			<tr bgcolor="#FFFFFF">
                <td height="25">默认模板设计</td>
                <td></td>
			</tr>
			<tr bgcolor="#FFFFFF">
                <td height="25">论坛管理</td>
                <td></td>
			</tr>
			<tr bgcolor="#FFFFFF">
                <td height="25">特别感谢</td>
                <td></td>
			</tr>
		</table></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>
</body>
</html>