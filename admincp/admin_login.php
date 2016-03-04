<?php
//session_start();
include_once('../common.php');

$ac = GetGP('ac');

if($ac == 'logout'){
		ssetcookie('admin_uid', '',-86400 * 365);
		ssetcookie('admin_user', '',-86400 * 365);
		//unset($_SESSION['admin_uid']);
		//unset($_SESSION['admin_user']);
		cpmessage('退出成功',"admin_login.php");
		//	ObHeader('admin_login.php');
}

if(submitcheck('adloginsubmit')){

	//$content = sreadfile('attachment/data/config.php');
	//list($adminusername,$adminpwd,$adminid,$address) = explode('\t',$content);
	$username=GetGP('adusername');
	$password=GetGP('adpassword');
	//$admin = $_SGLOBAL['db']->get_one("SELECT * FROM ".tname('aduser')." WHERE username='$username' AND password='$password'");

	if($username=='admin'&& $password=='123456'){
		//$_SESSION['admin_user']=$username;
		ssetcookie('admin_uid', '1',86400);
		ssetcookie('admin_user', $username,86400);
		ObHeader('index.php');
	}else{
		cpmessage('用户名或者密码有误');
	}
}


//对话框
function cpmessage($msgkey, $url_forward='', $second=1, $values=array()) {
	$message = $msgkey;
	if(!empty($url_forward)) {
		$second = $second * 1500;
		$message .= "<script>setTimeout(\"window.location.href ='$url_forward';\", $second);</script>";
	}
	include_once('header.php');
	include_once('message.php');
	include_once('footer.php');
	exit();
}


include_once('header.php');
?>
<div class="mainarea" style="text-align:center; width:100%;">
<div class="maininner" style="width:360px; text-align:left; margin:0 auto;">

	<form method="post" action="admin_login.php">
	<input type="hidden" name="formhash" value="<?php echo formhash();?>" />
	<br>
	<div class="bdrcontent" >
		<div class="title">
			<h3>管理登录</h3>
		</div>
		<table cellspacing="0" cellpadding="0" class="formtable">
		<tr>
			<th style="width:10em;">用户名</th>
			<td>
				<input type="text" id="adusername" class="t_input" name="adusername" value="" />
			</td>
		</tr>
		<tr>
			<th style="width:10em;">密  码</th>
			<td>
				<input type="password" id="adpassword" class="t_input" name="adpassword" value="" />
			</td>
		</tr>
		</table>
	</div>
	<div class="footactions">
		<input type="submit" name="adloginsubmit" value="提交" class="submit"> &nbsp;
	</div>
	</form>
</div>
</div>

<?php
include_once('footer.php');
?>