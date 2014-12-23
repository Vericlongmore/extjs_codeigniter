<!-- 合同管理 -->
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

	/*获取项目发起人start*/
	/*var pro_users_filed = [{name: 'f_name'},{name: 'f_id'}];
	var	pro_users = Ext.create('Ext.data.Store',{
		fields: pro_users_filed,
		autoLoad: true,
		proxy: {
		type: 'ajax',
			url: cPath + 'client/get_users',
			reader: {
				type: 'json',
				root: 'items'
			}
		}
	});*/
	/*获取项目发起人end*/

	
	var fields = ['f_id','f_name','f_desc','f_price','f_deposit','f_user','f_mobile','f_qq','f_type','f_status','f_service','f_edit_time','f_create_time'];
	var itemsPerPage = 20;
	var	userStore = Ext.create('Ext.data.Store',{
		fields : fields,
		pageSize : itemsPerPage,
		proxy : {
		type : 'ajax',
			url : cPath + 'contract/get_list',
			reader : {
				type : 'json',
				root : 'items',
				totalProperty : 'results'
			}
		}
	});
	userStore.load({params:{start:0,limit:itemsPerPage}});
	var toolbar = [
		{text : '新增合同',iconCls:'add',handler: showAddUser},
		'-',
		{text : '修改合同',iconCls:'option',handler: showModifyUser},
		'-',
		/*{text : '删除合同',iconCls:'remove',handler: showDeleteUser},
		'-',*/
		select,
		'-',
		{text : '查看所有功能',iconCls: 'refresh',handler: reFreshUser},
		'-',
		{text : '计算所有金额',iconCls: 'money',handler: count_all},
		'-',
		{text : '计算所有定金金额',iconCls: 'money',handler: count_payment},
		'-',
		{text : '交易完成金额',iconCls: 'money',handler: count_payment_complete},
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
				{header: "ID", width: 30, dataIndex: 'f_id', sortable: true},
				{header: "名称", width: 150, dataIndex: 'f_name', sortable: true,flex:1},
				{header: "简介", width: 200, dataIndex: 'f_desc', sortable: true,flex:1},
				{header: "价格", width: 150, dataIndex: 'f_price', sortable: true,flex:1},
				{header: "定金", width: 150, dataIndex: 'f_deposit', sortable: true,flex:1},
				{header: "负责人", width: 150, dataIndex: 'f_user', sortable: true,flex:1},
				{header: "电话", width: 150, dataIndex: 'f_mobile', sortable: true,flex:1},
				{header: "QQ", width: 150, dataIndex: 'f_qq', sortable: true,flex:1},
				{header: "服务器", width: 150, dataIndex: 'f_service', sortable: true,flex:1},
				{header: "类型", width: 150, dataIndex: 'f_type', sortable: true,flex:1},
				{header: "状态", width: 150, dataIndex: 'f_status', sortable: true,flex:1,renderer: check_status},
				{header: "创建时间", width: 150, dataIndex: 'f_create_time', sortable: true,flex:1},
				{header: "操作",
				xtype:'actioncolumn',
				items:[
					{icon : bPath + '/images/3px.png'},
					{tooltip : '修改合同',icon : bPath+'/images/045631214.gif',
						handler:function(grid,rowIndex,colIndex){
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showModifyUser(id,1);
						}
					},{
						icon : bPath + '/images/5px.png'
					},{
						tooltip : '支付完成',
						icon : bPath + '/images/0456311.gif',
						handler:function(grid,rowIndex,colIndex) {
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showDeleteUser(id,1);
					}}/*,{
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
					}*/]
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
			fieldLabel : '项目名称',
			allowBlank : false,
			blankText  : '不允许为空',
			vtype : 'name'
		},{
			xtype : 'textarea',
			name : 'f_desc',
			fieldLabel : '项目简介',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype : 'textfield',
			name : 'f_price',
			fieldLabel : '项目价格',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype : 'textfield',
			name : 'f_deposit',
			fieldLabel : '项目定金',
			allowBlank : false,
			blankText  : '不允许为空'
			//vtype : 'name'
		},{
			xtype : 'textfield',
			name: 'f_user',
			fieldLabel: '项目发起人',
			allowBlank : false,
			blankText  : '不允许为空'
		},{
			xtype : 'textfield',
			name: 'f_mobile',
			fieldLabel: '联系电话',
			allowBlank : false,
			blankText  : '不允许为空'
		},{
			xtype : 'textfield',
			name: 'f_qq',
			fieldLabel: '联系QQ',
			allowBlank : false,
			blankText  : '不允许为空'
		},{
			xtype : 'textfield',
			name: 'f_service',
			fieldLabel: '服务器',
			allowBlank : false,
			blankText  : '不允许为空'
		},{
			xtype: 'combo',
			name: 'f_type',
			fieldLabel: '项目类型',
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
		 }/*,{
			xtype:'datefield',
			name : 'f_stime',
			id: 'f_stime',
			fieldLabel:'开始时间',
			format: 'Y-m-d ',
			allowBlank: false			
		},{
			xtype:'datefield',
			name : 'f_etime',
			fieldLabel:'结束时间',
			id:'f_etime',
			format: 'Y-m-d ' ,
			//selectOnFocus:true,  //选择后选中 
			allowBlank: false 			
		}*/,{
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
		win.setTitle("新增合同");
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
				win.setTitle("修改合同信息");
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
				Ext.MessageBox.confirm("提示","您确定所选合同支付完成吗？",function(btnId){
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
			Ext.MessageBox.confirm("提示","您确定所选合同支付完成吗？",function(btnId){
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
			msg:'正在确定合同请稍候...'
		});
			Ext.Ajax.request({
			url : cPath + 'contract/edit_status',
			params : {id : id},
			method : 'POST',
			success : function(response,options){
				msgTip.hide();
				var result = Ext.JSON.decode(response.responseText);
				if(result == true){
					for(var i = 0 ; i < userList.length ; i++){
						var index = userStore.find('f_id',userList[i]);
						if(index != -1){
							reFreshUser();
					//		var rec = userStore.getAt(index);
					//		userStore.remove(rec);
						}
					}
					Ext.Msg.alert('提示','审核合同成功');
				}else{
					Ext.Msg.alert('提示','审核合同失败');
				}
			},
			failure : function(response,options){
				msgTip.hide();
				Ext.Msg.alert('提示','审核合同请求失败');
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
					url : cPath + 'contract/add_contract',
					method: 'POST',
					success:function(form,action){
						Ext.Msg.alert('提示','添加合同成功');
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
					Ext.Msg.alert('提示','修改合同成功');
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
				msg: "请选择要进行操作的合同",
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

	function check_status(value){
		if (value == 1) {
			return "<color style='color:red'>已支付定金</color>";
		} else {
			return "<color style='color:green'>已付清余额</color>";
		}
	}

	//总金额
	function count_all() {
		$.get(cPath + 'client/get_count_all',function(data){
			Ext.Msg.alert('提示',data);
		});
	}

	//定金金额
	function count_payment() {
		$.get(cPath + 'client/get_count_payment',function(data){
			Ext.Msg.alert('提示',data);
		});
	}

	//交易完成金额
	function count_payment_complete() {
		$.get(cPath + 'client/count_payment_complete',function(data){
			Ext.Msg.alert('提示',data);
		});
	}
});
</script>
</head>
<body>
</body>
</html>