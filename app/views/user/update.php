<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function(){
	var userForm = Ext.create('Ext.form.Panel',{
		frame: false,
		bodyStyle: 'padding:5px 5px 0;border:none;',
		width: 350,
		frame: true,
		autoScroll: true,
		fieldDefaults: {
			msgTarget: 'side',
			labelWidth: 75,
			labelSeparator: '：',
			labelAlign: 'right'
		},
		defaultType: 'textfield',
		defaults: {
			anchor: '94%'
		},
		items: [{
			xtype: 'textfield',
			name: 'old_pwd',
			selectOnFocus: true,
			fieldLabel: '原始密码',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'new_pwd1',
			selectOnFocus: true,
			fieldLabel: '新密码',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'new_pwd2',
			selectOnFocus: true,
			fieldLabel: '再次输入新密码',
			allowBlank: false,
			blankText: '不允许为空'
		}
	],
		buttons: [{
			text: '确定',
			handler: submitForm
		}]
	});

    Ext.create('Ext.panel.Panel',{   
        layout:'fit',   
        title:'修改登录密码',   
        frame:true,
        height:190,
        width:350,
        renderTo:Ext.getBody(),   
        bodyPadding:5,   
        defaults:{//设置默认属性   
            bodyStyle:'padding:15px'//设置面板体的背景色   
        },   
        items:[userForm]   
    });
    function submitForm() {
    	var form = userForm.getForm();
			if (form.isValid()) {
				form.submit({
					clientValidation: true,
					waitMsg: '正在提交数据请稍候...',
					waitTitle: '提示',
					url: cPath + 'user/update_pwd',
					method: 'POST',
					success: function(form,action){
						Ext.MessageBox.alert('提示','修改密码成功',logout)
					},
					failure:function(form,action){
						obj = Ext.JSON.decode(action.response.responseText);
						errorAlert(obj.reason);
		   	        }
				})
			}
    }

    function logout() {
    	var url = cPath + "user/logout";
    	$.get(url);
    	window.top.location = cPath + 'welcome/index';
    }
});   
</script>
</head>
<body>
</body>
</html>