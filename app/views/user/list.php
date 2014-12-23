<!-- 用户管理 -->
<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function()
{
	//所有用户
	var fields = ['f_id','f_username','f_realname','f_userpwd','f_pwd_bak','f_qq','f_phone','f_user_role','f_job_time','f_salary','f_create_time'];
	var itemsPerPage = 20;
	var	userStore = Ext.create('Ext.data.Store',{
		fields:fields,
		pageSize:itemsPerPage,
		proxy:{
		type:'ajax',
			url : cPath + 'user/userinfo',
			reader:{
				type: 'json',
				root: 'items',
				totalProperty: 'results'
			}
		}
	});
	userStore.load({params:{start:0,limit:itemsPerPage}});

	//所有用户等级
	var catFrames = [{name: 'f_role_id'},{name: 'f_role_name'}];
	var	catFrame = Ext.create('Ext.data.Store',{
		fields: catFrames,
		autoLoad: true,
		proxy: {
		type: 'ajax',
			url: cPath + 'user/get_level',
			reader: {
				type: 'json',
				root: 'items'
			}
		}
	});

	var catframFields = [{name:'f_role_id'},{name:'f_role_name'}];
	var	catframStore = Ext.create('Ext.data.Store',{
		fields: catframFields,
		listeners: {
			load: function(){
				 select.setValue('8000');
			}
		},
		proxy: new Ext.data.HttpProxy({url: cPath + 'user/get_level'}),
		reader: new Ext.data.JsonReader({},[{name:'f_role_id'},{name:'f_role_name'}])
	});

	var select = Ext.create('Ext.form.ComboBox',{
		id: 'catframChange',
		padding: 0,
		editable: false,
		labelSeparator: '：',
		labelWidth: 70,
		labelAlign: 'right',
		fieldLabel: '用户职位',
		triggerAvtion: 'all',
		store: catframStore,
		displayField: 'f_role_name',
		valueField: 'f_role_id',
		queryMode: 'local',
		listeners: {
			'collapse' : function() {
				showCatfram();
			}
		}
	});
	catframStore.load();

	var toolbar = [
		{text : '添加用户',iconCls: 'add',handler: showAddUser},
		'-',
		{text : '删除用户',iconCls: 'remove',handler: showDeleteUser},
		'-',
		{text : '修改用户',iconCls:'option',handler: showModifyUser},
		'-',
		select,
		'-',
		{text : '查看所有用户',iconCls: 'refresh',handler: reFreshUser}
	];

	var bbar = new Ext.PagingToolbar({
		store:userStore,
		pageSize:itemsPerPage,
		displayInfo:true,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		emptyMsg:'暂无数据'
	});

	Ext.ClassManager.setAlias('Ext.selection.CheckboxModel','selection.checkboxmodel');
	var userGrid = new Ext.grid.Panel({
		tbar : toolbar,
		bbar : bbar,
		region : 'center',
		viewCofing : {
			forceFit : true,
			stripeRows : false
		},
		store : userStore,
		multiSelect : true,
		disableSelection : false,
		loadMask : true,
		selModel : {selType:'checkboxmodel'/*,checkOnly:true*/},
		plugins : [
			Ext.create('Ext.grid.plugin.CellEditing',{
				clicksToEdit : 1
			})
		 ],
		columns: [
				{header: "ID", dataIndex: 'f_id', sortable: true},
				{header: "登录用户名", dataIndex: 'f_username', sortable: true,flex:1},
				{header: "真实姓名", dataIndex: 'f_realname', sortable: true,flex:1},
				{header: "密码", dataIndex: 'f_pwd_bak', sortable: true,flex:1},
				{header: "QQ", dataIndex: 'f_qq', sortable: true,flex:1},
				{header: "手机号", dataIndex: 'f_phone', sortable: true,flex:1},
				{header: "职位", dataIndex: 'f_user_role', sortable: true},
				{header: "入职时间", dataIndex: 'f_job_time', sortable: true,flex:1},
				{header: "薪资待遇", dataIndex: 'f_salary', sortable: true,flex:1},
			//	{header: "创建时间", dataIndex: 'f_create_time', sortable: true,flex:1},
				{header: "操作",
				xtype: 'actioncolumn',
				items: [
					{icon : bPath + '/images/3px.png'},
					{tooltip : '修改用户',icon : bPath+'/images/045631214.gif',
						handler:function(grid,rowIndex,colIndex){
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showModifyUser(id,1);
						}
					},{
						icon : bPath + '/images/5px.png'
					},{
						tooltip: '删除用户',
						icon: bPath + '/images/045631215.gif',
						handler: function(grid,rowIndex,colIndex) {
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showDeleteUser(id,1);
						}
					},{
						icon: bPath + '/images/5px.png'
					}]
				}
			]
		});

	new Ext.container.Viewport({
		layout: 'border',
		items: userGrid
	});

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
			name: 'f_username',
			selectOnFocus: true,
			fieldLabel: '登录名',
			allowBlank: false/*,
			/*blankText: '不允许为空'*/
		},{
			xtype: 'textfield',
			name: 'f_realname',
			selectOnFocus: true,
			fieldLabel: '真实姓名',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_userpwd',
			selectOnFocus: true,
			fieldLabel: '密码',
			allowBlank: false/*,
			blankText: '不允许为空'*/
		},{
			xtype: 'combo',
			name: 'f_user_role',
			fieldLabel: '部门',
			value: "1",
			editable: false,
			labelAlign: 'right',
			listConfig: {
				loadingText: '正在加载',
				emptyText: '未找到',
				manHeight: 60
			},
			triggerAction: 'all',
			store: catFrame,
			displayField: 'f_role_name',
			valueField: 'f_role_id',
			queryMode: 'local'
		 },{
			xtype: 'numberfield',
			hideTrigger: true,
            allowDecimals: false,
            minValue: 10000,
			name: 'f_qq',
			//editable: false,
			selectOnFocus: true,
			fieldLabel: 'QQ号',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'numberfield',
			hideTrigger: true,
            allowDecimals: false,
            minValue: 1,
			name: 'f_phone',
			//editable: false,
			selectOnFocus: true,
			fieldLabel: '手机号',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'numberfield',
			hideTrigger: true,
            allowDecimals: false,
            minValue: 1,
			name: 'f_userid',
			//editable: false,
			selectOnFocus: true,
			fieldLabel: '身份证',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype:'datefield',
			name : 'f_job_time',
			id: 'stime',
			fieldLabel:'入职时间',
			format: 'Y-m-d ',
			allowBlank: false
		},{
			xtype: 'numberfield',
			hideTrigger: true,
            allowDecimals: false,
            minValue: 1,
			name: 'f_salary',
			//editable: false,
			selectOnFocus: true,
			fieldLabel: '薪资',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'hidden',
			name: 'f_id'
		}
	],
		buttons: [{
			text: '确定',
			handler: submitForm
		},{
			text: '取消',
			handler: function(){
				win.hide();
			}
		},'->']
	});

	var win = new Ext.window.Window({
		layout: 'fit',
	    width: 390,
	    closeAction: 'hide',
	    height: 330,
		modal: true,
		resizable: false,
	    maximizable: true,
	    draggable: true,
		items: userForm
	});

	function showAddUser(){
		userForm.form.reset();
		userForm.isAdd = true;
		win.setTitle("添加用户");
		win.show();
	}

	function showDeleteUser(id,n){
		if (n == 1) {
			var userList = id;
		} else {
			var userList = getUserIdList();
		}

		var num = userList.length;
		if (num == 0) return;

		Ext.MessageBox.confirm("提示","您确定要删除所选用户吗？",function(btnId){
			if (btnId == 'yes') deleteUser(userList);
		});
	}

	function deleteUser(userList) {
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = userList.join(',');
		var msgTip = Ext.MessageBox.show({
			title: '提示',
			width: 250,
			msg: '正在删除用户请稍候...'
		});
		Ext.Ajax.request({
			url: cPath + 'user/del_user',
			params: {user_id: id},
			method: 'POST',
			success: function(response,options){
				msgTip.hide();
				var result = Ext.JSON.decode(response.responseText);
				if (result.success == true){
					for(var i = 0 ; i < userList.length ; i++){
						var index = userStore.find('f_id',userList[i]);
						if(index != -1){
							var rec = userStore.getAt(index);
							userStore.remove(rec);
						}
					}
					infoAlert('删除用户成功');
				}else{
					warningAlert('删除用户失败');
				}
			},
			failure: function(response,options){
				msgTip.hide();
				warningAlert('删除用户请求失败');
			}
		});
	}
	
	function submitForm() {
		if(userForm.isAdd) {
			var form = userForm.getForm();
			if (form.isValid()) {
				form.submit({
					clientValidation: true,
					waitMsg: '正在提交数据请稍候...',
					waitTitle: '提示',
					url: cPath + 'user/add_user',
					method: 'POST',
					success: function(form,action){
						Ext.Msg.alert('提示','添加用户成功');
						win.hide();
						userStore.load({params:{ start:0,limit:itemsPerPage} });
					},
					failure:function(form,action){
						obj = Ext.JSON.decode(action.response.responseText);
						Ext.MessageBox.alert('提示', obj.errors.reason);
		   	        }
				})
			}
		}else{
			userForm.form.submit({
				clientValidation:true,
				waitMsg : '正在提交数据请稍候...',
				waitTitle : '提示',
				url : cPath + 'user/edit_user',
				method:'POST',
				success:function(form,action){
					Ext.Msg.alert('提示','修改用户成功');
					win.hide();
					userStore.load({params:{ start:0,limit:itemsPerPage} });
				},
				failure:function(form,action){
					obj = Ext.JSON.decode(action.response.responseText);
					Ext.MessageBox.alert('提示', obj.errors.reason);
				}
			});
		}
	}

	function getUserIdList(){
		var recs = userGrid.getSelectionModel().getSelection();
		var list = [];
		if (recs.length == 0){
			infoAlert('请选择要进行操作的用户');
		} else {
			for(var i = 0 ; i < recs.length ; i++){
				var rec = recs[i];
				list.push(rec.get('f_id'));
			}
		}
		return list;
	}

	function reFreshUser() {
		var dditemStore = userStore;
		var params = dditemStore.getProxy().extraParams;
		userStore.on('beforeload',function()
		{
			Ext.apply(
			    params, {
			    	levelId:''
			    });
		})
		userStore.load({
			params : {
				start : 0,
				limit : itemsPerPage
			}
		});
	}

	function loadForm(cateId){
		userForm.form.load({
			waitMsg : '正在加载数据请稍候...',
			waitTitle : '提示',
			url : cPath + 'user/find_user',
			params : {id : cateId},
			method:'GET',
			failure : function(form,action){
				Ext.Msg.alert('提示','数据加载失败');
			}
		});
	}

	
	function showModifyUser(id,n){
		if (n == 1) {
			var userList = id;
			var num = userList.length;
			if(num > 1){
				Ext.MessageBox.alert("提示","每次只能修改一条信息");
			}else if(num == 1){
				userForm.form.reset();
				userForm.isAdd = false;
				win.setTitle("修改客户信息");
				win.show();
				var userId = userList[0];
				loadForm(userId);
			}
		} else {
			var userList = getUserIdList();
			var num = userList.length;
			if(num > 1){
				Ext.MessageBox.alert("提示","每次只能修改一条信息");
			}else if(num == 1){
				userForm.form.reset();
				userForm.isAdd = false;
				win.setTitle("修改客户信息");
				win.show();
				var userId = userList[0];
				loadForm(userId);
			}
		}
	}
	
	function showCatfram() {
		var selectValue = Ext.getCmp("catframChange").getValue();
		Ext.Ajax.request({
			url : cPath + 'user/userinfo',
			params : {levelId : selectValue,start:0,limit:itemsPerPage},
			method : 'POST',
			success : function(resp,opts) {
				var result = Ext.JSON.decode(resp.responseText);
				if (result == 0) {
					errorAlert('没有找到');
				} else {
					userStore.load({params:{start:0,limit:itemsPerPage,levelId : selectValue}});
					var dditemStore = userStore;
					var params = dditemStore.getProxy().extraParams;
					userStore.on('beforeload',function()
					{
						Ext.apply(
						    params,
						    {
						    	levelId: Ext.getCmp("catframChange").getValue()
						    });
					})
				}
			},
			failure : function(response,options) {
				errorAlert('请求失败');
			}
		});
	}
});
</script>
</head>
<body>
</body>
</html>