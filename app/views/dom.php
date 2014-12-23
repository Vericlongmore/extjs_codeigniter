<script type="text/javascript" defer="defer">

document.onreadystatechange = statechange();
function statechange() {
	console.log(document.readyState);
	if (document.readyState == 'loading') {
		var eq_id = $.cookie('head_icon') ? $.cookie('head_icon') : 0;
		$('#click_icon_').children().eq(eq_id).children('a').addClass('onnav');
	}
}

/*document.onreadystatechange = subSomething;
function subSomething() {
	if(document.readyState == "complete") {
		var eq_id = $.cookie('head_icon') ? $.cookie('head_icon') : 0;
		$('#click_icon_').children().eq(eq_id).children('a').addClass('onnav');
		console.log(eq_id);
		console.log(Ext.getCmp('accordion_menu'));
		//Ext.getCmp('accordion_menu').items.items[eq_id].expand()
		

		//var left_tree_id = $.cookie('left_tree');
	//	var node = Ext.getDom(left_tree_id);
		//Ext.tree.DefaultSelectionModel(left_tree_id)

	//	var tree = Ext.getCmp('_tree_0');
		//var tree_node = Ext.getCmp('tree_3');
		//console.log(tree_node);
		//console.log(asd1);
		//tree.getSelectionModel().select((tree.getStore()).getNodeById(102),true);//选中对应的树节点
      //  tree.getSelectionModel().select((tree.getStore()).getNodeById(102),true);//选中对应的树节点
     //   var t = Ext.getCmp(tab.id);
       // var n = t.getSelectionModel();
       // var p = n.id;
       // alert(p);
       // var ii = t.parentNode;
        //alert(ii);//.parentNode.get('id')
       // var a = Ext.getCmp(tab.id);
       // var root = tree.getRootNode();
        //var node = root.findChild( "id", tab.pid);
        //var node_ = root.findChild( "id", tab.id);
        //node.expand( node );
        //console.log();

		//var node2 = $('#' + left_tree_id).click();
		//console.log(node);
		//console.log(node2);
		//Ext.getCmp(left_tree_id).select();
		//console.log(Ext.getCmp(left_tree_id));
		//console.log($('#' + left_tree_id));
		//console.log(Ext.getCmp(left_tree_id));
	}
}*/
</script>