<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OA - 信息管理系统</title>
<link rel="stylesheet" type="text/css"	href="<?php echo base_url('develop/ext4.0/resources/css/ext-all-12px.css')?>" />
<link rel="stylesheet" type="text/css"	href="<?php echo base_url('develop/css/icon.css')?>" />
<script type="text/javascript" src="<?php echo base_url('develop/js/jquery.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('develop/ext4.0/bootstrap.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('develop/ext4.0/locale/ext-lang-zh_CN.js')?>"></script>
<style type="text/css">
<!--
body {margin:0px;padding:0;background-color: #016aa9;overflow: hidden;font-size: 12px;}
-->
</style>
<script type="text/javascript">
var cPath = '<?php echo site_url();?>';
var bPath = '<?php echo base_url();?>';
var uname = new Ext.form.TextField( {
	id: 'uname',
	style: 'margin:20px 0 0 0',
	fieldLabel: '用户名',
	name: 'username',
	width: 250,
	value: 'more',
	iconCls: 'login',
	allowBlank: false,
	blankText: '用户名不能为空'
});
var pwd = new Ext.form.TextField( {
    id: 'pwd',
    style: 'margin:10px 0 0 0',
    inputType: 'password',
    fieldLabel: '密　码',
    width: 250,
    value: 123456,
    maxLength: 40,
    name: 'password',
    allowBlank: false,
    blankText: '密码不能为空'
});

Ext.onReady(function() {
    Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	var simple = new Ext.FormPanel( {
		labelWidth: 75,
		fieldDefaults: {
		    width: 150,
		    labelSeparator: '：',
			labelAlign: 'right'
		},
		defaultType: 'textfield',
		bodyStyle: 'padding:30 0 0 5;',
		buttonAlign: 'center',
		border: false,
		id: "form",
		items: [uname,pwd],
		buttons: [ {
			text: '登录',
			type: 'submit',
			id: 'sb',
			handler: save
		},{
			text: '重置',
			handler: function() {
				simple.form.reset();
			}
		}]
	});
	document.onkeydown = function(event){
		var e = event || window.event || arguments.callee.caller.arguments[0];
		if(e && e.keyCode==13){
			save();
		}
	};

	function save() {
		var userName = uname.getValue();
		var userPass = pwd.getValue();
		if (simple.form.isValid()) {
		simple.form.submit({
			waitMsg: '正在进行登陆验证,请稍候...',
			waitTitle: '请稍等',
		    url: cPath + 'welcome/login_check',
		    params: {user_name:userName,user_pwd:userPass},
		    method: 'post',
			success: function(form, action) {
				obj = Ext.JSON.decode(action.response.responseText);
				var result = obj.msg.reason;
				if (result == 'ok') {
					window.location.href = cPath + 'welcome/show';
				} else {
					Ext.Msg.alert('错误提示', "您的输入用户信息有误，请核实后重新输入！");
				}
			},
		    failure : function(form, action) {
		    	switch (action.failureType) {
		        	case Ext.form.Action.CLIENT_INVALID:
		            	Ext.Msg.alert('错误提示', '表单数据非法请核实后重新输入！');
		                break;
					case Ext.form.Action.CONNECT_FAILURE:
		            	Ext.Msg.alert('错误提示', '网络连接异常！');
		                break;
					case Ext.form.Action.SERVER_INVALID:
		            	Ext.Msg.alert('错误提示', "您的输入用户信息有误，请核实后重新输入！");
		                simple.form.reset();
				}
					obj = Ext.JSON.decode(action.response.responseText);
					errorAlert(obj.errors.reason);
				}
			});
		}
	};
	var win = new Ext.Window({
		id: 'win',
		title: '用户登录',
		iconCls: 'login',
		layout: 'fit',
		align: 'center',
		width: 330,
		height: 182,
		resizable: false,
		draggable: false,
		border: false,
		bodyStyle: 'padding:5px;',
		maximizable: false,
		closeAction: 'close',
		closable: false,
		items: simple
	});
		win.show();
	    pwd.focus(false, 100);
});

function errorAlert(string) {
	Ext.MessageBox.show({
		title: "提示",
		msg: "错误原因："+string,
		buttons: Ext.MessageBox.OK,
		icon: Ext.MessageBox.ERROR
	});
}
</script>
</head>
<body>
</body>
</html>