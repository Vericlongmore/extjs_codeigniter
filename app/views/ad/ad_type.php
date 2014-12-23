<!-- 功能管理 -->
<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function()
{
	var fields = ['f_id','f_from','f_name','f_status', 'f_create_time'];
	var itemsPerPage = 20;
	var	userStore = Ext.create('Ext.data.Store',{
		fields : fields,
		pageSize : itemsPerPage,
		proxy:{
		type:'ajax',
			url : cPath + 'ad/get_ad_types',
			reader:{
				type: 'json',
				root: 'items',
				totalProperty: 'results'
			}
		}
	});
	userStore.load({params:{start:0,limit:itemsPerPage}});

	var toolbar = [
		{text : '新增站点',iconCls: 'add',handler: showAddUser},
		'-',
		{text : '修改站点',iconCls: 'option',handler: showModifyUser},
		'-',
		{text : '删除站点',iconCls: 'remove',handler: showDeleteUser}
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
		columns: [
				{header: "站点ID",dataIndex: 'f_id', sortable: true},
				{header: "站点域名",dataIndex: 'f_from', sortable: true,flex:1},
				{header: "站点名称",dataIndex: 'f_name', sortable: true},
				{header: "状态",dataIndex: 'f_status', sortable: true, renderer: checked},
				{header: "添加时间",dataIndex: 'f_create_time', sortable: true,flex:1},
				{header: "操作",
				xtype: 'actioncolumn',
				items: [
					{icon: bPath + '/images/3px.png'},
					{tooltip: '修改站点',icon: bPath+'/images/045631214.gif',
						handler: function(grid,rowIndex,colIndex){
							var rec = grid.getStore().getAt(rowIndex);
							var id = [];
							id.push(rec.get('f_id'));
							showModifyUser(id,1);
						}
					},{
						icon: bPath + '/images/5px.png'
					},{
						tooltip: '删除站点',
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
			anchor: '100%'
		},
		items: [{
			xtype: 'textfield',
			name: 'f_from',
			selectOnFocus: true,
			fieldLabel: '站点域名',
			allowBlank: false,
			blankText: '不允许为空'
		},{
			xtype: 'textfield',
			name: 'f_name',
			selectOnFocus: true,
			fieldLabel: '站点名称',
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
	    width: 380,
	    closeAction: 'hide',
	    height: 140,
		modal: true,
		resizable: false,
	    maximizable: true,
	    draggable: true,
		items: userForm
	});

	function showAddUser(){
		userForm.form.reset();
		userForm.isAdd = true;
		win.setTitle("新增站点");
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
			win.setTitle("修改站点信息");
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

		Ext.MessageBox.confirm("提示","您确定要删除所选站点吗？",function(btnId){
			if (btnId == 'yes') deleteUser(userList);
		});
	}

	function deleteUser(userList) {
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = userList.join(',');
		var msgTip = Ext.MessageBox.show({
			title: '提示',
			width: 250,
			msg: '正在删除站点请稍候...'
		});
		Ext.Ajax.request({
			url: cPath + 'ad/del_ad_tpye',
			params: {id: id},
			method: 'POST',
			success: function(response,options){
				msgTip.hide();
				var result = Ext.JSON.decode(response.responseText);
				if (result == true){
					for(var i = 0 ; i < userList.length ; i++){
						var index = userStore.find('f_id',userList[i]);
						if(index != -1){
							var rec = userStore.getAt(index);
							userStore.remove(rec);
						}
					}
					Ext.Msg.alert('提示','删除站点成功');

				}else{
					Ext.Msg.alert('提示','删除站点失败');
				}
			},
			failure: function(response,options){
				msgTip.hide();
				Ext.Msg.alert('提示','删除站点请求失败');
			}
		});
	}
	function loadForm(userId){
		userForm.form.load({
			waitMsg : '正在加载数据请稍候...',
			waitTitle : '提示',
			url : cPath + 'ad/find_ad_type',
			params : {id:userId},
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
					url: cPath + 'ad/add_ad_type',
					method: 'POST',
					success: function(form,action){
						Ext.Msg.alert('提示','添加站点成功');
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
				url : cPath + 'ad/edit_ad_type',
				method:'POST',
				success:function(form,action){
					Ext.Msg.alert('提示','修改站点成功');
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
			infoAlert('请选择要进行操作的站点');
		} else {
			for(var i = 0 ; i < recs.length ; i++){
				var rec = recs[i];
				list.push(rec.get('f_id'));
			}
		}
		return list;
	}

	function checked(value){
		if (value == 1) {
			return "<img src='" + bPath + "/images/select.gif'/>";
		} else {
			return "<img src='" + bPath + "/images/noselect.gif'/>";
		}
	}

});
</script>
</head>
<body>
</body>
</html>