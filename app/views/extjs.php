<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OA</title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>develop/ext4.0/resources/css/ext-all-12px.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>develop/css/icon.css"/>
<script type="text/javascript" src="<?php echo base_url();?>develop/ext4.0/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>develop/ext4.0/locale/ext-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>develop/js/jquery.min.js"></script>
<script type="text/javascript">
var cPath = '<?php echo site_url();?>';
var bPath = '<?php echo base_url();?>develop';
Ext.QuickTips.init();
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', bPath+'/ext4.0/ux/');
Ext.require([
             'Ext.data.*',
             'Ext.grid.*',
             'Ext.util.*',
             'Ext.form.*',
             'Ext.ux.data.PagingMemoryProxy',
             'Ext.ux.ProgressBarPager'
         ]);
Ext.override(Ext.Window, {
	constrain: true,
	constrainHeader: true
});
Ext.apply(Ext.form.field.VTypes,{
	name: function(v){
		return /^[\u0391-\uFFE5]+$/.test(v);
	},
	nameText: '只能输入中文！',
	nameMask: /^[\u0391-\uFFE5]+$/
})

//错误，xx
function errorAlert(string) {
	Ext.MessageBox.show({
		title: "警告",
		msg: "错误原因：" + string,
		buttons: Ext.MessageBox.OK,
		icon: Ext.MessageBox.ERROR
	});
}

//感叹号
function infoAlert(string) {
	Ext.MessageBox.show({
		title: "提示",
		msg: string,
		buttons: Ext.MessageBox.OK,
		icon: Ext.MessageBox.INFO
	});
}
//黄色感叹号
function warningAlert(string) {
	Ext.MessageBox.show({
		title: "提示",
		msg: string,
		buttons: Ext.MessageBox.OK,
		icon: Ext.MessageBox.WARNING
	});
}

//问号
function questionAlert(string) {
	Ext.MessageBox.show({
		title: "提示",
		msg: string,
		buttons: Ext.MessageBox.OK,
		icon: Ext.MessageBox.QUESTION
	})
}
</script>
<style type="text/css">
.x-grid-row-over .x-grid-cell-inner{font-weight:bold;cursor:pointer;}
.x-action-col-cell img{height:16px;width:16px;cursor:pointer;}
</style>