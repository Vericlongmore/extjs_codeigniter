<!-- 广告管理 -->
<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function()
{
	//所有广告
	var fields = ['f_id','web_name','f_pid','f_name','f_ip','f_line','f_desc','f_qq','f_link','f_bgcolor','f_istop','f_end_time','f_status'];
	var itemsPerPage = 80;
	var	userStore = Ext.create('Ext.data.Store',{
		fields:fields,
		pageSize:itemsPerPage,
		proxy:{
		type:'ajax',
			url : cPath + 'ad/get_ad',
			reader:{
				type: 'json',
				root: 'items',
				totalProperty: 'results'
			}
		}
	});
	userStore.load({params:{start:0,limit:itemsPerPage}});

	//所有广告来源
	var catFrames = [{name: 'f_name'},{name: 'f_id'}];
	var	catFrame = Ext.create('Ext.data.Store',{
		fields: catFrames,
		autoLoad: true,
		proxy: {
		type: 'ajax',
			url: cPath + 'ad/get_ad_type',
			reader: {
				type: 'json',
				root: 'items'
			}
		}
	});

	var catframFields = [{name:'f_name'},{name:'f_id'}];
	var	catframStore = Ext.create('Ext.data.Store',{
		fields: catframFields,
		listeners: {
			load: function(){
				var record = catframStore.getAt(0).get('f_id');
				select.setValue(record);
			}
		},
		proxy: new Ext.data.HttpProxy({url: cPath + 'ad/get_ad_type'}),
		reader: new Ext.data.JsonReader({},[{name:'f_id'},{name:'f_name'}])
	});

	var select = Ext.create('Ext.form.ComboBox',{
		id: 'catframChange',
		padding: 0,
		editable: false,
		labelSeparator: '：',
		labelWidth: 70,
		labelAlign: 'right',
		fieldLabel: '所属站点',
		triggerAvtion: 'all',
		store: catframStore,
		displayField: 'f_name',
		valueField: 'f_id',
		queryMode: 'local',
		listeners: {
			'collapse' : function() {
				showCatfram();
			}
		}
	});
	catframStore.load();

	var toolbar = [
		{text : '添加广告',iconCls: 'add',handler: showAddUser},
		'-',
		{text : '删除广告',iconCls: 'remove',handler: showDeleteUser},
		'-',
		select,
		'-',
		{text : '查看所有广告',iconCls: 'refresh',handler: reFreshUser}
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
				{header: "广告ID" , dataIndex: 'f_id', sortable: true, width:50},
				{header: "所属站点", dataIndex: 'web_name', sortable: true},
				{header: "名称", dataIndex: 'f_name', sortable: true},
				{header: "IP", dataIndex: 'f_ip', sortable: true,flex:.3},
				{header: "线路", dataIndex: 'f_line', sortable: true},
				{header: "详细介绍", dataIndex: 'f_desc', sortable: true,flex:.3},
				{header: "QQ", dataIndex: 'f_qq', sortable: true},
				{header: "主页", dataIndex: 'f_link', sortable: true},
				{header: "背景颜色", dataIndex: 'f_bgcolor', sortable: true, renderer: check_color},
				{header: "是否黄金位", dataIndex: 'f_istop', sortable: true, renderer: check_top},
				{header: "广告结束时间", dataIndex: 'f_end_time', sortable: true, flex:.3},
				/*{header: "线路", dataIndex: 'f_isorder', sortable: true,editor:{xtype:'textfield',selectOnFocus:true,id:'isorder',allowBlank:false}},
				{header: "类别管理", dataIndex: 'f_ntype', sortable: true},
				{header: "页面地址", dataIndex: 'f_link', sortable: true,flex:1},*/
				{header: "操作",
				xtype: 'actioncolumn',
				items: [
					{icon: bPath + '/images/3px.png'},
					{
						tooltip: '删除广告',
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

	var f_bg = [
			{name:'name'}, {name:'post'},	
			];
	var f_bg_ = Ext.create('Ext.data.Store',{
		fields: f_bg,
		data: [
			{name: '是', post: 'FFFF00'},
			{name: '不是', post: 'FFFFCC'}
		]
	});

	var PostInfo = [{name:'name'},{name:'post'}]; 
	var postStore = Ext.create('Ext.data.Store',{   
		fields:PostInfo,   
		data:[     
			{name:'是',post:'1'},     
			{name:'不是',post:'0'}
		] 
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
			name: 'f_name',
			selectOnFocus: true,
			fieldLabel: '私服名称',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'combo',
			name: 'f_pid',
			fieldLabel: '所属站点',
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
			xtype: 'textfield',
			name: 'f_ip',
			selectOnFocus: true,
			fieldLabel: 'IP',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_line',
			selectOnFocus: true,
			fieldLabel: '线路',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_desc',
			selectOnFocus: true,
			fieldLabel: '详细介绍',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_qq',
			selectOnFocus: true,
			fieldLabel: 'QQ',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_link',
			selectOnFocus: true,
			fieldLabel: '主页地址',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'combo',
			fieldLabel: '是否套黄',
			editable: false,
			store: f_bg_,
			displayField : 'name',
			valueField: 'post',
			name: 'f_bgcolor',
			value: 'FFFFCC',
			//emptyText: '请选择是否套黄',
			queryMode :'local',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'combo',
			fieldLabel: '是否黄金位',
			editable: false,
			store: postStore,
			displayField : 'name',
			valueField: 'post',
			name: 'f_istop',
			//emptyText: '请选择是否黄金位',
			value: '0',
			queryMode :'local',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'numberfield',
			name: 'f_end_time',
			hideTrigger: false,
            allowDecimals: false,
            minValue: 1,
			selectOnFocus: true,
			selectOnFocus: true,
			fieldLabel: '广告持续天数',
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
	    width: 395,
	    closeAction: 'hide',
	    height: 368,
		modal: true,
		resizable: false,
	    maximizable: true,
	    draggable: true,
		items: userForm
	});

	function showAddUser(){
		userForm.form.reset();
		userForm.isAdd = true;
		win.setTitle("添加私服");
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

		Ext.MessageBox.confirm("提示","您确定要删除所选私服吗？",function(btnId){
			if (btnId == 'yes') deleteUser(userList);
		});
	}

	function deleteUser(userList) {
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = userList.join(',');
		var msgTip = Ext.MessageBox.show({
			title: '提示',
			width: 250,
			msg: '正在删除私服请稍候...'
		});
		Ext.Ajax.request({
			url: cPath + 'ad/del_ad',
			params: {ad_id: id},
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
					infoAlert('删除私服成功');
				}else{
					warningAlert('删除私服失败');
				}
			},
			failure: function(response,options){
				msgTip.hide();
				warningAlert('删除私服请求失败');
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
					url: cPath + 'ad/add_ad',
					method: 'POST',
					success: function(form,action){
						Ext.Msg.alert('提示','添加广告成功');
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
				url : cPath + 'add/edit_ad',
				method:'POST',
				success:function(form,action){
					Ext.Msg.alert('提示','修改分类成功');
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
			infoAlert('请选择要进行操作的私服');
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
			url : cPath + 'ad/get_ad',
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

	function check_color(value) {
		var html = '<span style="background:#'+ value +'">背景颜色</span>';
		return html;
	}

	function check_top(value) {
		if (value == 1) {
			return "是";
		} else {
			return "普通";
		}
	}
});
</script>
</head>
<body>
</body>
</html>