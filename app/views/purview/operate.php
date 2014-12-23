<!-- 功能管理 -->
<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function(){
	var fields = ['f_nid','catename','f_name','f_link', 'f_isorder'];
	var itemsPerPage = 80;
	var	userStore = Ext.create('Ext.data.Store',{
		fields:fields,
		pageSize:itemsPerPage,
		proxy:{
		type:'ajax',
			url : cPath + 'purview/get_action',
			reader:{
				type: 'json',
				root: 'items',
				totalProperty: 'results'
			}
		}
	});
	userStore.load({params:{start:0,limit:itemsPerPage}});

	var catFrames = [{name: 'f_name'},{name: 'f_nid'}];
	var	catFrame = Ext.create('Ext.data.Store',{
		fields: catFrames,
		autoLoad: true,
		proxy: {
		type: 'ajax',
			url: cPath + 'purview/cat_fram_class',
			reader: {
				type: 'json',
				root: 'items'
			}
		}
	});

	var catframFields = [{name:'f_nid'},{name:'f_name'}];
	var	catframStore = Ext.create('Ext.data.Store',{
		fields: catframFields,
		listeners: {
			load: function(){
				select.setValue('1');
			}
		},
		proxy: new Ext.data.HttpProxy({url: cPath + 'purview/cat_fram_class'}),
		reader: new Ext.data.JsonReader({},[{name:'f_nid'},{name:'f_name'}])
	});

	var select = Ext.create('Ext.form.ComboBox',{
		id: 'catframChange',
		padding: 0,
		editable: false,
		labelSeparator: '：',
		labelWidth: 70,
		labelAlign: 'right',
		fieldLabel: '所属模块',
		triggerAvtion: 'all',
		store: catframStore,
		displayField: 'f_name',
		valueField: 'f_nid',
		queryMode: 'local',
		listeners: {
			'collapse' : function() {
				showCatfram();
			}
		}
	});
	catframStore.load();

	var toolbar = [
		{text : '新增功能',iconCls: 'add',handler: showAddUser},
		'-',
		{text : '修改功能',iconCls: 'option',handler: showModifyUser},
		'-',
		{text : '删除功能',iconCls: 'remove',handler: showDeleteUser},
		'-',
		select,
		'-',
		{text : '查看所有功能',iconCls: 'refresh',handler: reFreshUser}
	];

	var bbar = new Ext.PagingToolbar({
		store:userStore,
		pageSize:itemsPerPage,
		displayInfo:true,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		emptyMsg:'暂无数据'
	});

	var row = Ext.create('Ext.grid.RowNumberer',{
		text:'行号',
		width:35
	})

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
				{header: "功能ID",dataIndex: 'f_nid', sortable: true},
				{header: "功能名称",dataIndex: 'f_name', sortable: true,flex:1},
				{header: "所属模块",dataIndex: 'catename', sortable: true},
				{header: "排序值",dataIndex: 'f_isorder', sortable: true,editor:{xtype:'textfield',selectOnFocus:true,id:'isorder',allowBlank:false}},
				{header: "功能地址",dataIndex: 'f_link', sortable: true,flex:1},
				{header: "操作",
				xtype: 'actioncolumn',
				items: [
					{icon: bPath+'/images/3px.png'},
					{tooltip: '修改功能',icon: bPath+'/images/045631214.gif',
						handler: function(grid,rowIndex,colIndex){
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_nid'));
							showModifyUser(id,1);
						}
					},{
						icon: bPath+'/images/5px.png'
					},{
						tooltip: '删除功能',
						icon: bPath+'/images/045631215.gif',
						handler: function(grid,rowIndex,colIndex) {
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_nid'));
							showDeleteUser(id,1);
						}
					},{
						icon: bPath+'/images/5px.png'
					}]
				}
			]
		});

	userGrid.on('edit', function(editor, e) {
		   var r = e.record;
		   var list = r.get('f_nid');
		   var sort = Ext.getCmp('isorder').getValue();
			Ext.Ajax.request({
				url: cPath + 'purview/update_cat_sort',
				params: {id: list,cid: sort},
				method: 'POST',
				success: function(response,options){
					userStore.load({params:{start: 0,limit: itemsPerPage}});
				},
				failure: function(response,options){
					Ext.Msg.alert('提示','排序请求失败！');
				}
			});
	});

	new Ext.container.Viewport({
		layout: 'border',
		items: userGrid
	});

	Ext.QuickTips.init();
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
			name: 'f_name',
			selectOnFocus: true,
			fieldLabel: '功能名称',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'combo',
			name: 'f_cid',
			fieldLabel: '所属模块',
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
			valueField: 'f_nid',
			queryMode: 'local'
		 },{
			xtype: 'numberfield',
			name: 'f_isorder',
			fieldLabel: '排序值',
			allowDecimals: false,
            minValue: 0,
			regex: /^[0-9]\d{0,1}$/,
			regexText: '请输入正确序号！',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_link',
			fieldLabel: '功能地址'
		}/*,{
			xtype: 'fieldset',
			title: '添加三级功能',
			checkboxToggle: true,
			checkboxName: 'three',
			collapsed: true,
			labelAlign: 'right',
			defaults: {
				labelSeparator: '：',
				labelWidth: 110,
				anchor: '100%'
			},
			items: [{
				xtype: 'textfield',
				name: 'f_three_link',
				fieldLabel: '上级功能ID'
			}]
		}*/,{
			xtype: 'hidden',
			name: 'f_nid'
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
	    width: 380,
	    closeAction: 'hide',
	    height: 230,
		modal: true,
		resizable: false,
	    maximizable: true,
	    draggable: true,
		items: userForm
	});

	function showAddUser(){
		userForm.form.reset();
		userForm.isAdd = true;
		win.setTitle("新增功能");
		win.show();
	}

	function showModifyUser(id,n){
		if (n == 1) {
			var userList = id;
		} else {
			var userList = getUserIdList();
		}

		var num = userList.length;
		if (num == 0) return;
		if (num > 1) {
			Ext.MessageBox.alert("提示","每次只能修改一条信息");
		} else if(num == 1) {
			userForm.form.reset();
			userForm.isAdd = false;
			win.setTitle("修改功能信息");
			win.show();
			var userId = userList[0];
			loadForm(userId);
		}
	}

	function showDeleteUser(id,n){
		if (n == 1) {
			var userList = id;
		} else {
			var userList = getUserIdList();
		}

		var num = userList.length;
		if (num == 0) return;

		Ext.MessageBox.confirm("提示","您确定要删除所选功能吗？",function(btnId){
			if (btnId == 'yes') deleteUser(userList);
		});
	}

	function deleteUser(userList) {
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = userList.join(',');
		var msgTip = Ext.MessageBox.show({
			title: '提示',
			width: 250,
			msg: '正在删除功能请稍候...'
		});
		Ext.Ajax.request({
		url: cPath + 'purview/del_class_category',
		params: {id: id},
		method: 'POST',
		success: function(response,options){
			msgTip.hide();
			var result = Ext.JSON.decode(response.responseText);
			if (result == true){
				for(var i = 0 ; i < userList.length ; i++){
					var index = userStore.find('f_nid',userList[i]);//被删用户的id
					if(index != -1){
						var rec = userStore.getAt(index);
						userStore.remove(rec);
					}
				}
				//reFreshUser();
				Ext.Msg.alert('提示','删除功能成功');

			}else{
				Ext.Msg.alert('提示','删除功能失败');
			}
		},
		failure: function(response,options){
			msgTip.hide();
			Ext.Msg.alert('提示','删除功能请求失败');
		}
	});
}
	function loadForm(userId){
		userForm.form.load({
			waitMsg : '正在加载数据请稍候...',
			waitTitle : '提示',
			url : cPath + 'purview/find_class_category',
			params : {userId:userId},
			method:'GET',
			failure:function(form,action){
				Ext.Msg.alert('提示','数据加载失败');
			}
		});
	}
	function submitForm(){
		if(userForm.isAdd){
			var form = userForm.getForm();
			if (form.isValid()) {
				form.submit({
					clientValidation: true,
					waitMsg: '正在提交数据请稍候...',
					waitTitle: '提示',
					url: cPath + 'purview/add_class_category',
					method: 'POST',
					success: function(form,action){
						Ext.Msg.alert('提示','添加功能成功');
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
				url : cPath + 'purview/edit_class_category',
				method:'POST',
				success:function(form,action){
					Ext.Msg.alert('提示','修改功能成功');
					win.hide();
					userStore.load({params:{ start:0,limit:itemsPerPage} });
				},
				failure:function(form,action){
					obj = Ext.JSON.decode(action.response.responseText);
					//errorAlert(obj.errors.reason);
					Ext.MessageBox.alert('提示', obj.errors.reason);
				}
			});
		}
	}
	function updateUserGrid(userId){
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
				f_nid: values['f_nid'],
				f_name: values['f_name']
			}, 'User');
			userStore.add(rec);
		}
	}

	function getUserIdList(){
		var recs = userGrid.getSelectionModel().getSelection();
		var list = [];
		if (recs.length == 0){
			infoAlert('请选择要进行操作的功能');
		} else {
			for(var i = 0 ; i < recs.length ; i++){
				var rec = recs[i];
				list.push(rec.get('f_nid'));
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
			    params,
			    {
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

	function showCatfram() {
		var selectValue = Ext.getCmp("catframChange").getValue();
		Ext.Ajax.request({
			url : cPath + 'purview/get_action',
			params : {levelId : selectValue,start:0,limit:itemsPerPage},
			method : 'POST',
			success : function(resp,opts){
				var result = Ext.JSON.decode(resp.responseText);
				if (result == 0)
				{
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
			failure : function(response,options){
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