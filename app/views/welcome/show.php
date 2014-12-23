<?php $this->load->view('extjs.php');?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>develop/css/head_bg.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>develop/css/0.css" id="change_css"/>
<script type="text/javascript" src="<?php echo base_url('develop/js/cookie.js')?>"></script>
<script type="text/javascript">
	var html = '';
	var menu = <?php echo $menu;?>;
	var menu = eval(menu);
	var i = 0;
	var len = "<?php echo $num;?>";
	var htmlGg = '暂无';
	for (i;i<len;i++) {
		html += "<li><a href='javascript:void(0);' rel="+menu.data[i].groupid+" id='nav_"+menu.data[i].groupid+"' hidefocus='true' ><span class='c"+menu.data[i].groupid+"'></span>"+menu.data[i].name+"</a></li>";
	}
	var head_html = "<div class='blue_bg'><div id='logo'><img src='"+bPath +"/images/logo.png' /></div><div id='head_bg'>"+
					"<ul id='click_icon_' class='test_123'>"+html+
					"</ul></div><div style='color:#fff'></div></div>";

	var foot_html = "<div style='text-align:left;border-top:1px dashed #ccc;background:#ECECEC'>"+
	"感谢使用<a href='http://www.webtiro.com' target='_blank' class='blue'></a>"+
	"<span style='float:right'><a href='javascript:void(0);' onclick='goback()'>返回首页</a>"+
	"&nbsp;|&nbsp;<a href='"+cPath+"user/logout'>退出登录</a>"+
	"&nbsp;|&nbsp;<a href='javascript:void(0);' onclick='set_pwd()'>修改密码</a></span></div>";
	
	var ajax = function(config) {
		Ext.Ajax.request({
			url: config.url,
			params: config.params,
			method: 'post',
			callback: function(options,success,response) {
				config.callback(Ext.JSON.decode(response.responseText));
			}
		})
	};
	
	Ext.onReady(function(){
		function changePage(url,title){
			if (url == null) {
				url = cPath + 'welcome/default_page';
			}
			Ext.getDom('contentIframe').src = url;
			Ext.getCmp('main_panel').setTitle(title);
		}
		Ext.getDom('contentIframe').src = cPath + 'welcome/default_page';
		var accordion = Ext.create('Ext.panel.Panel',{
			id: 'accordion_menu',
			autoScroll: false,
			region: 'center',
			bodyStyle: 'border:none',
			layout: 'accordion',
			layoutConfig: {
				animate: true
			}
		});

		var viewport = Ext.create('Ext.container.Viewport',{
			layout: {
	            type: 'border',
	            padding: 5
	        },
			id: 'test',
			items: [{
				//title: '管理系统',
				id: 'head',
				split: false,
				collapsible: false,
				html : head_html,
				region: 'north',
				height: 80,
				bodyStyle: 'border:none'
			},{
				xtype: 'panel',
				id: 'accordion',
				width: 158,
				layout: {
					align: 'stretch',
					type: 'vbox'
				},
				collapsed: false,
				collapsible: true,
				title: '系统操作',
				region: 'west',
				split: true,
				items: [{
					xtype: 'container',
					layout: {
						type: 'border'
					},
					flex: 1,
					items: [
						accordion
					]
				}]
			},{
				xtype: 'container',
				layout: {
					type: 'border'
				},
				region: 'center',
				split: true,
				items: [
					{
						id: 'main_panel',
						xtype: 'panel',
						collapsed: false,
						collapsible: true,
						preventHeader: false,//是否阻止面板标题栏的显示，默认为false
						bodyStyle: 'border:none',
						title: '功能预览',
						region: 'center',
						contentEl: 'contentIframe',
						split: true
					}
				]
			},{
				xtype: 'panel',
				height: 20,
				bodyStyle: 'border:none',
				collapsible: false,
				collapsed: false,
				split: false,
				html: foot_html,
				region: 'south'
			}]
		});

		ajax({
			url: cPath + 'welcome/accordion',//获取面板的地址
			params: {action : "list"},
			callback: addTree
		});
		
		function addTree(data){
			for ( var i = 0; i < data.length; i++) {
				accordion.add(Ext.create("Ext.tree.Panel", {
					id : '_tree_'+i,
					//id: 'tree',
					title: data[i].title,
					iconCls: data[i].iconCls,
					autoScroll: true,
					rootVisible: false,
					viewConfig: {
						loadingText: "正在加载..."
					},
					//useArrows: true,
					store: createStore(data[i].id, i),
					listeners: {
						afterlayout: function(){
							if(this.getView().el){
								var el = this.getView().el;
								var table = el.down("table.x-grid-table");
								if (table) {
									table.setWidth(el.getWidth());
								}
							}
						},
						itemclick: function(view, rec, item, index, e){
							if (rec.get('leaf')){
								changePage(rec.get('url'), rec.get('text'));
								//var tree_id = rec.internalId;
								//保存上次浏览记录
								//$.cookie('tree_url',rec.get('url'));
								//$.cookie('tree_text',rec.get('text'));
							}
						},
						click: {
							element: 'el',
							fn: function(){
								var id = this.id;
								var ids = id.split('_');
								$('#click_icon_').children().siblings().children().removeClass('onnav');
								$('#click_icon_').children().eq(ids[2]).children('a').addClass('onnav');
								//$.cookie('head_icon',ids[2]);
							}
						}
					}
				}));
				accordion.doLayout();
			}
			//加载树数据完毕之后读取cookie并展开对应手风琴
			var eq_id = $.cookie('head_icon') ? $.cookie('head_icon') : 0;
			Ext.getCmp('accordion_menu').items.items[eq_id].expand();
			$('#click_icon_').children().eq(eq_id).children('a').addClass('onnav');
			var _temp_url = $.cookie('tree_url');
			var _temp_text = $.cookie('tree_text');
			changePage(_temp_url,_temp_text);
			accordion.doLayout();
		}

		var model = Ext.define("TreeModel", {
			extend: "Ext.data.Model",
			fields: [
				{name: "id",type: "string"},
				{name: "iconCls",type: "string"},
				{name: "text",type: "string"},
				{name: "url",type: "string"},
				{name: "leaf",type: "boolean"},
				{name: "qtip",type: "string"}
			]
		});

		var createStore  =  function(id,i){
			var me = this;
			return Ext.create("Ext.data.TreeStore",{
				defaultRootId: id ,
				model: model,
				proxy: {
					type: "ajax",
					url: cPath + 'welcome/accordion_child'
			   },
			   clearOnLoad: true,
			   nodeParam: "id"
		   });
	   };

		$("#click_icon_").children().each(function(i){
			$(this).children().click(function(){
				$(this).addClass('onnav').parent().siblings().children().removeClass('onnav');;
				$.cookie('head_icon',i);
				Ext.getCmp('accordion_menu').items.items[i].expand();
			})
		})
	});
function goback() {
	Ext.getDom('contentIframe').src = cPath + 'welcome/default_page';
}

function set_pwd() {
	Ext.getDom('contentIframe').src = cPath + 'user/update_u';
}

</script>
</head>
<body>
<iframe id='contentIframe' name='contentIframe' style='height:100%;width:100%;padding:0px' frameborder="no"></iframe>
<!-- <a href='javascript:void(0);' onclick='goback()'>返回首页</a>"+
					"&nbsp;|&nbsp;<a href='"+cPath+"user/logout'>退出登录</a>"+
					"&nbsp;|&nbsp;<a href='javascript:void(0);' onclick='set_pwd()'>修改密码</a> -->
</body>
</html>