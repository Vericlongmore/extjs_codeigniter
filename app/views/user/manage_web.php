<html>
<head>
<title>代理管理站点</title>
<style type="text/css">
.m{margin:5px 0 0 0;}
</style>
</head>
<body>
	<form method="post" name="myform" id="myform" action="<?php echo site_url('user/add_user_web');?>">
	<div class="m">
		所有代理：<select name="proxy">
			<?php foreach ($userinfo as $k=>$v):?>
				<option id="<?php echo $v['f_id']?>" name="<?php echo $v['f_id']?>"><?php echo $v['f_username']?></option>
			<?php endforeach;?>
		</select>
	</div>

	<div class="m">
		所有站点：
		<?php foreach($web as $key=>$value):?>
			<input type="checkbox" name="web[]" value="<?php echo $value['f_id']?>" id="web_<?php echo $value['f_id']?>"/><label for="web_<?php echo $value['f_id']?>"><?php echo $value['f_name'];?></label>
		<?php endforeach;?>
	</div>

	<div class="m">
		<input type="submit" value="提交" name="submit"/>
	</div>

	</form>
</body>
</htlm>