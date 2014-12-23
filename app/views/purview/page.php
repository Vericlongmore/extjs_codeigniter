<!-- 功能权限管理 -->
<?php $this->load->view('extjs.php');?>
<script type="text/javascript">
Ext.onReady(function()
{
	var itemsPerPage = 19;
	var levelFields = [{name:'f_role_value'},{name:'f_role_name'}];
	var	levelStore = Ext.create('Ext.data.Store',{
		fields:levelFields,
		listeners:{
			load:function(){
				//var record = levelStore.getAt(0).get('f_level_value');
				select.setValue('8000');
			}
		},
		proxy:new Ext.data.HttpProxy({url : cPath + 'purview/userlevel'}),
		reader:new Ext.data.JsonReader({},[{name:'f_role_value'},{name:'f_role_name'}])
	});
	
	var select = Ext.create('Ext.form.ComboBox',{
		id:'purviewChange',
		padding:0,
		editable:false, 
		labelSeparator:'：',
		labelWidth:70,
		labelAlign:'right',
		fieldLabel:'用户等级',
		triggerAvtion:'all',
		store: levelStore,
		displayField:'f_role_name',
		valueField:'f_role_value',
		queryMode:'local',
		listeners:{
			'collapse' : function() {
				showUserPurview();
			}
		}
	});
	levelStore.load();

	var purviewFields = [{name:'f_nid'},{name:'f_name'},{name:'f_link'},{name:'f_isorder'},{name:'catename'},{name:'check'}];
	var purviewStore  = Ext.create('Ext.data.Store',{
		fields:purviewFields,
		pageSize:itemsPerPage,
		proxy:{
			type:'ajax',
			url: cPath + 'purview/user_purview',
			reader:{
				type:'json',
				root:'items',
				totalProperty:'results'
				}
			}
		});
	purviewStore.load({params:{start:0,limit:itemsPerPage}});
	
	var toolbar = [
		           	select,
		           	'-',
		       		{text : '添加所选权限',iconCls:'add',handler:showAddPurview},
		       		'-',
		    		{text : '移除所选权限',iconCls:'remove',handler:showDelPurview}];

	var bbar = new Ext.PagingToolbar({
		store:purviewStore,
		pageSize:itemsPerPage,
		displayInfo:true,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		emptyMsg:'暂无数据'
	});
	
	Ext.ClassManager.setAlias('Ext.selection.CheckboxModel','selection.checkboxmodel');
	var userGrid = new Ext.grid.Panel({
		tbar : toolbar,
		bbar:bbar, 
		region: 'center',
		store: purviewStore,
		viewCofing:{
			forceFit:true,
			stripeRows:true
		},
		multiSelect:true,
		disableSelection:false,
		loadMask:true,
		selModel : {selType:'checkboxmodel'/*,checkOnly:true*/},
		columns: [
			{header: "功能编号", width: 160, dataIndex: 'f_nid', sortable: true},
			{header: "功能名称", width: 180, dataIndex: 'f_name', sortable: true,flex:1},
			{header: "排序", width: 150, dataIndex: 'f_isorder', sortable: true},
			{header: "所属模块", width: 150, dataIndex: 'catename', sortable: true},
			{header: "是否拥有该权限", width: 150,id:'purviewId', dataIndex: 'check', sortable: true,renderer:checked}
			]
		});

	new Ext.container.Viewport({
		layout:'border',
		items : userGrid
	});

	function showUserPurview() {
		var selectValue = Ext.getCmp("purviewChange").getValue();
		Ext.Ajax.request({
			url : cPath + 'purview/user_purview',
			params : {levelId : selectValue,start:0,limit:itemsPerPage},
			method : 'POST',
			success : function(resp,opts){
				var result = Ext.JSON.decode(resp.responseText);
				if (result == 0)
				{
					Ext.Msg.alert('提示','没有找到');
				} else {
					purviewStore.load({params:{start:0,limit:itemsPerPage,levelId : selectValue}});
					var dditemStore = purviewStore;
					var params = dditemStore.getProxy().extraParams;
					purviewStore.on('beforeload',function()
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
	
	function checked(value){
		if (value == 1) {
			return "<img src='" + bPath + "/images/select.gif'/>";
		} else {
			return "<img src='" + bPath + "/images/noselect.gif'/>";
		}
	}
	
	function showAddPurview(id,n)
	{
		if (n == 1)
		{
			var purviewList = id;
			var num = purviewList.length;
			if(num == 0){
				return;
			}
			Ext.MessageBox.confirm("提示","您确定要更改权限吗？",function(btnId){
				if(btnId == 'yes'){
					AddPurview(purviewList);
				}
			});
		} else {
			var purviewList = getUserIdList();
			var num = purviewList.length;
			if(num == 0){
				return;
			}
			Ext.MessageBox.confirm("提示","您确定要更改权限吗？",function(btnId){
				if(btnId == 'yes'){
					AddPurview(purviewList);
				}
			});
		}
	}
	
	function AddPurview(purviewList)
	{
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = purviewList.join(',');
		var msgTip = Ext.MessageBox.show({
			title:'提示',
			width : 250,
			msg:'正在更新权限，请稍候...'
		});
		var selectVal = Ext.getCmp("purviewChange").getValue();
		Ext.Ajax.request({
			url : cPath + 'purview/add_purview',
			params : {id : id,nlevel:selectVal},
			method : 'POST',
			success : function(response,options){
				msgTip.hide();
				var result = Ext.JSON.decode(response.responseText);
				if (result == true) {
					Ext.Msg.alert('提示','权限更新成功');
					purviewStore.load({params:{ start:0,limit:itemsPerPage} });
				} else {
					Ext.Msg.alert('提示','权限更新失败');
				}
			},
			failure : function(response,options){
				msgTip.hide();
				Ext.Msg.alert('提示','请求失败');
			}
		});
	}

	function showDelPurview(id,n) {
		if (n == 1) {
			var purviewList = id;
			var num = purviewList.length;
			if (num == 0) {
				return;
			}
			Ext.MessageBox.confirm("提示","您确定要删除权限吗？",function(btnId){
				if(btnId == 'yes'){
					DelPurview(purviewList);
				}
			});
		} else {
			var purviewList = getUserIdList();
			var num = purviewList.length;
			if(num == 0){
				return;
			}
			Ext.MessageBox.confirm("提示","您确定要删除权限吗？",function(btnId){
				if(btnId == 'yes'){
					DelPurview(purviewList);
				}
			});
		}
	}
	
	function DelPurview(purviewList)
	{
		var selectedRecord = userGrid.getSelectionModel().getSelection();
		var id = purviewList.join(',');
		var msgTip = Ext.MessageBox.show({
			title:'提示',
			width : 250,
			msg:'正在删除权限，请稍候...'
		});
		var selectVal2 = Ext.getCmp("purviewChange").getValue();
		Ext.Ajax.request({
			url : cPath + 'purview/del_purview',
			params : {id : id,nlevel:selectVal2},
			method : 'POST',
			success : function(response,options){
				msgTip.hide();
				var result = Ext.JSON.decode(response.responseText);
				if (result == true) {
					Ext.Msg.alert('提示','权限删除成功');
					purviewStore.load({params:{ start:0,limit:itemsPerPage} });
				} else {
					Ext.Msg.alert('提示','权限删除失败');
				}
			},
			failure : function(response,options){
				msgTip.hide();
				Ext.Msg.alert('提示','请求失败');
			}
		});
	}

	function getUserIdList() {
		var recs = userGrid.getSelectionModel().getSelection();
		var list = [];
		if(recs.length == 0){
			Ext.MessageBox.show({
				title: "提示",
				msg: "请选择要进行操作的功能",
				buttons: Ext.MessageBox.OK,
				icon: Ext.MessageBox.INFO
			});
		}else{
			for(var i = 0 ; i < recs.length ; i++){
				var rec = recs[i];
				list.push(rec.get('f_nid'));
			}
		}
		return list;
	}
});
</script>
</head>
<body>
</body>
</html>