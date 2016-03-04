<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title>丹姿悦植粹管理系统</title>
<style type="text/css">
@import url(css/style.css);
</style>
<script type="text/javascript">
function checkAll(form, name) {
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name.match(name)) {
			e.checked = form.elements['chkall'].checked;
		}
	}
}
</script>
</head>
<body>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>

	<div id="header">
	<div class="headerwarp">
		<h1 class="logo"></h1>
		<ul class="menu">
<!--			<li><a href="../index.php">网站首页</a></li>
-->		</ul>
		<div class="nav_account">
        <a href="../index.php" style="font-size:16px; color:#000; font-weight:bold;">丹姿悦植粹-管理系统</a><br />
		<?php
		if(!empty($_SCOOKIE['admin_user'])&&!empty($_SCOOKIE['admin_uid'])){
		?>
			欢迎您，<?=$_SCOOKIE['admin_user']?> &nbsp;&nbsp;
			<a href="admin_login.php?ac=logout">退出</a>
		<?php
		}
		?>
		</div>
		</div>
	</div>

	<div id="wrap">
		<div id="cp_content">
