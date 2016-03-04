<?php

include_once('./common.php');

$step = GetGP('step');
if($step == 2){
	InitGP(array('name','tele','info'));
	
	empty($name) && showmsgiframe('请输入您的姓名');
	empty($tele) && showmsgiframe('请输入您的手机号码');
	!is_numeric($tele) && showmsgiframe('您的手机号码格式有误');
	if(strlen($tele)!=11){
		showmsgiframe('您的手机号码格式有误');	
	}
	empty($info) && showmsgiframe('请输入您的留言信息');
	
	if($_SGLOBAL['db']->get_one("SELECT id FROM ".tname('contact')." WHERE tele='".$tele."' AND isshow='0'")){
		showmsgiframe('您已经提交过留言信息');	
	}
	
	$data_array=array(
		'name'=>$name,
		'tele'=>$tele,
		'info'=>$info,
		'date'=>date('Y-m-d H:i:s'),
	);
	inserttable('contact',$data_array);
		
	showmsgiframe('感谢您的留言信息！',1);
	
}

function showmsgiframe($msg='',$ok='0'){
	header('Content-Type: text/html; charset=utf-8');
	echo json_encode(array('error'=>$msg,'ok'=>$ok));
	exit;
}

?>