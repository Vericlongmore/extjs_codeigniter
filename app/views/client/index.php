<!-- 客户类型 -->
<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function() {
	/*业务分类start*/
	var levelFields = [{name:'f_id'},{name:'f_name'}];
	var	levelStore = Ext.create('Ext.data.Store',{
		fields:levelFields,
		listeners:{
			load:function(){
				select.setValue('1');
			}
		},
		proxy:new Ext.data.HttpProxy({url : cPath + 'business/option_type'}),
		reader:new Ext.data.JsonReader({},[{name:'f_id'},{name:'f_name'}])
	});
	
	var select = Ext.create('Ext.form.ComboBox',{
		id:'purviewChange',
		padding:0,
		editable:false, 
		labelSeparator:'：',
		labelWidth:70,
		labelAlign:'right',
		fieldLabel:'需求类型',
		triggerAvtion:'all',
		store: levelStore,
		displayField:'f_name',
		valueField:'f_id',
		queryMode:'local',
		listeners:{
			'collapse' : function() {
				showUserPurview();
			}
		}
	});
	levelStore.load();
	/*业务分类end*/

	/*表单里面业务分类start*/
	var catFrames = [{name: 'f_name'},{name: 'f_id'}];
	var	catFrame = Ext.create('Ext.data.Store',{
		fields: catFrames,
		autoLoad: true,
		proxy: {
		type: 'ajax',
			url: cPath + 'business/option_type',
			reader: {
				type: 'json',
				root: 'items'
			}
		}
	});
	/*表单里面业务分类end*/

	var fields = ['f_id','f_name','f_phone','f_tell','f_address','f_company','f_type'];
	var itemsPerPage = 20;
	var	userStore = Ext.create('Ext.data.Store',{
		fields : fields,
		pageSize : itemsPerPage,
		proxy : {
		type : 'ajax',
			url : cPath + 'client/get_list',
			reader : {
				type : 'json',
				root : 'items',
				totalProperty : 'results'
			}
		}
	});
	userStore.load({params:{start:0,limit:itemsPerPage}});
	var toolbar = [
		{text : '新增客户',iconCls:'add',handler: showAddUser},
		'-',
		{text : '修改客户',iconCls:'option',handler: showModifyUser},
		'-',
		{text : '删除客户',iconCls:'remove',handler: showDeleteUser},
		'-',
		select,
		'-',
		{text : '查看所有客户',iconCls: 'refresh',handler: reFreshUser}
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
		store : userStore,
		viewCofing : {
			forceFit : true,
			stripeRows : false
		},
		multiSelect : true,
		disableSelection : false,
		loadMask : true,
		selModel : {selType:'checkboxmodel',checkOnly:true},
		columns: [
				{header: "标识ID", dataIndex: 'f_id', sortable: true},
				{header: "姓名", width: 150, dataIndex: 'f_name', sortable: true,flex:1},
				{header: "手机", width: 150, dataIndex: 'f_phone', sortable: true,flex:1},
				{header: "电话", width: 150, dataIndex: 'f_tell', sortable: true,flex:1},
				{header: "地址", width: 150, dataIndex: 'f_address', sortable: true,flex:1},
				{header: "需求", width: 150, dataIndex: 'f_type', sortable: true,flex:1},
				{header: "公司名称", width: 150, dataIndex: 'f_company', sortable: true,flex:1},
				{header: "操作",
				xtype:'actioncolumn',
				items:[
					{icon : bPath + '/images/3px.png'},
					{tooltip : '修改客户',icon : bPath+'/images/045631214.gif',
						handler:function(grid,rowIndex,colIndex){
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showModifyUser(id,1);
						}
					},{
						icon : bPath + '/images/5px.png'
					},{
						tooltip : '删除客户',
						icon : bPath + '/images/045631215.gif',
						handler:function(grid,rowIndex,colIndex) {
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showDeleteUser(id,1);
						}
					},{
						icon : bPath + '/images/5px.png'
					}]
				}
			]
		});

	new Ext.container.Viewport({
		layout:'border',
		items : userGrid
	});

	Ext.QuickTips.init();
	/*Ext.define("User", {
		extend : "Ext.data.Model",
		fields : [{name: 'f_nid'},{name: 'f_catid'},{name:'f_name'}]
	});*/
	var userForm = Ext.create('Ext.form.Panel',{
		bodyStyle : 'padding:5px 5px 0;border:none;',
		//width:350,
		autoScroll : true,
		fieldDefaults : {
			msgTarget : 'side',
			labelWidth : 75,
			labelSeparator : '：',
			labelAlign : 'right'
		},
		defaultType : 'textfield',
		defaults : {
			anchor : '100%'
		},
		items : [{
			xtype : 'textfield',
			name : 'f_name',
			fieldLabel : '客户名称',
			allowBlank : false,
			blankText  : '不允许为空',
			vtype : 'name'
		},{
			xtype : 'textfield',
			name : 'f_phone',
			fieldLabel : '客户手机',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype : 'textfield',
			name : 'f_tell',
			fieldLabel : '客户电话',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype : 'textfield',
			name : 'f_address',
			fieldLabel : '客户地址',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype: 'combo',
			name: 'f_type',
			fieldLabel: '业务类型',
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
			displayField: 'f_name',
			valueField: 'f_id',
			queryMode: 'local'
		 },{
			xtype : 'textarea',
			name : 'f_company',
			fieldLabel : '公司名称',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype :'hidden',
			name : 'f_id'
		}
	],
		buttons:[{
			text : '确定',
			handler : submitForm
		},{
			text : '取消',
			handler : function(){
				win.hide();
			}
		}]
	});
	var win = new Ext.window.Window({
		layout:'fit',
	    width:380,
	    closeAction:'hide',
		modal :true,
		items : userForm
	});

	function showAddUser(){
		userForm.form.reset();
		userForm.isAdd = true;
		win.setTitle("新增客户");
		win.show();
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

	function showDeleteUser(id,n) {
		if (n == 1) {
			var userList = id;
			var num = userList.length;
			if(num == 0){
				return;
			}
				Ext.MessageBox.confirm("提示","您确定要删除所选客户吗？",function(btnId){
				if(btnId == 'yes'){
					deleteUser(userList);
				}
			});
		} else {
			var userList = getUserIdList();
			var num = userList.length;
			if(num == 0){
				return;
				}
			Ext.MessageBox.confirm("提示","您确定要删除所选客户吗？",function(btnId){
				if(btnId == 'yes'){
					deleteUser(userList);
				}
			});
		}
	}
	function deleteUser(userList){
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = userList.join(',');
		var msgTip = Ext.MessageBox.show({
			title:'提示',
			width : 250,
			msg:'正在删除客户请稍候...'
		});
			Ext.Ajax.request({
			url : cPath + 'client/del_type',
			params : {id : id},
			method : 'POST',
			success : function(response,options){
				msgTip.hide();
					var result = Ext.JSON.decode(response.responseText);
				if(result == true){
					for(var i = 0 ; i < userList.length ; i++){
						var index = userStore.find('f_id',userList[i]);
						if(index != -1){
							var rec = userStore.getAt(index);
							userStore.remove(rec);
						}
						}
					Ext.Msg.alert('提示','删除客户成功');
				}else{
					Ext.Msg.alert('提示','删除客户失败');
				}
			},
			failure : function(response,options){
				msgTip.hide();
				Ext.Msg.alert('提示','删除客户请求失败');
			}
		});
	}

	function loadForm(cateId){
		userForm.form.load({
			waitMsg : '正在加载数据请稍候...',
			waitTitle : '提示',
			url : cPath + 'client/find_type',
			params : {id : cateId},
			method:'GET',
			failure : function(form,action){
				Ext.Msg.alert('提示','数据加载失败');
			}
		});
	}

	function submitForm(){
		if(userForm.isAdd){
			var form = userForm.getForm();
			if (form.isValid()) {
				form.submit({
					clientValidation:true,
					waitMsg: '正在提交数据请稍候...',
					waitTitle: '提示',
					url : cPath + 'client/add_type',
					method: 'POST',
					success:function(form,action){
						Ext.Msg.alert('提示','添加客户成功');
						win.hide();
						userStore.load({params:{ start:0,limit:itemsPerPage} });
					},
					failure:function(form,action){
						obj = Ext.JSON.decode(action.response.responseText);
						Ext.MessageBox.alert('提示', obj.errors.reason);
		   	        }
				});
			}
		}else{
			userForm.form.submit({
				clientValidation:true,
				waitMsg: '正在提交数据请稍候...',
				waitTitle: '提示',
				url: cPath + 'client/edit_type',
				method: 'POST',
				success: function(form,action){
					Ext.Msg.alert('提示','修改客户成功');
					win.hide();
					userStore.load({params:{start: 0,limit: itemsPerPage}});
				},
				failure:function(form,action){
					obj = Ext.JSON.decode(action.response.responseText);
					Ext.MessageBox.alert('提示', obj.errors.reason);
				}
			});
		}
	}
	/*function updateUserGrid(userId){
		var values = userForm.form.getValues();
		var index = userStore.find('id',values['id']);
		var userTypeField = userForm.form.findField('id');
		var userTypeName = userTypeField.getRawValue();
		if(index != -1){
			var item = userStore.getAt(index);
			for(var fieldName in values){
				item.set(fieldName,values[fieldName]);
			}
			item.set('name',userName);
			item.commit();
		}else{
			var rec = Ext.ModelMgr.create({
				f_nid: f_nid,
				f_catid: values['f_catid'],
				f_name: values['f_name']
			}, 'User');
			userStore.add(rec);
	}
		}*/

	function showUserPurview() {
		var selectValue = Ext.getCmp("purviewChange").getValue();
		Ext.Ajax.request({
			url : cPath + 'client/get_list',
			params : {levelId : selectValue,start:0,limit:itemsPerPage},
			method : 'POST',
			success : function(resp,opts){
				var result = Ext.JSON.decode(resp.responseText);
				if (result == 0)
				{
					Ext.Msg.alert('提示','没有找到');
				} else {
					userStore.load({params:{start:0,limit:itemsPerPage,levelId : selectValue}});
					var dditemStore = userStore;
					var params = dditemStore.getProxy().extraParams;
					userStore.on('beforeload',function()
					{
						Ext.apply(   
						    params,
						    {   
						    	levelId:Ext.getCmp("purviewChange").getValue()
						    });
					})
				}
			},
			failure : function(response,options){
				Ext.Msg.alert('提示','请求失败！');
			}
		});
	}
	
	function getUserIdList(){
		var recs = userGrid.getSelectionModel().getSelection();
		var list = [];
		if(recs.length == 0) {
			Ext.MessageBox.show({
				title: "提示",
				msg: "请选择要进行操作的客户",
				buttons: Ext.MessageBox.OK,
				icon: Ext.MessageBox.INFO
			});
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
		userStore.on('beforeload',function() {
			Ext.apply(
			    params, {levelId:''}
			);
		})
		userStore.load({params: {start: 0,limit: itemsPerPage}});
	}
});
</script>
</head>
<body>
</body>
</html>