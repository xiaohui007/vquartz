<?php
//session_start();
include_once('../common.php');

//需要管理员登录

if(empty($_SCOOKIE['admin_user'])) {
	ObHeader('admin_login.php');
}


//$isfounder = ckfounder($_SGLOBAL['supe_uid']);

//来源
//if(!preg_match("/admincp\.php/", $_SGLOBAL['refer'])) $_SGLOBAL['refer'] = "admincp.php?ac=$ac";

//菜单激活
$menuactive = array($ac => ' class="active"');

$_TPL['menunames'] = array(
	'index' => '统计信息',
	'product'  => '产品管理',
	'active'  => '相册管理',
	'lunbo'=>'首页轮播图片',
	'contact'=>'留言管理',
);

//获取限制条件
function getwheres($intkeys, $strkeys, $randkeys, $likekeys, $pre='') {

	$wherearr = array();
	$urls = array();

	foreach ($intkeys as $var) {
		$value = isset($_GET[$var])?stripsearchkey($_GET[$var]):'';
		if(strlen($value)) {
			$wherearr[] = "{$pre}{$var}='".intval($value)."'";
			$urls[] = "$var=$value";
		}
	}

	foreach ($strkeys as $var) {
		$value = isset($_GET[$var])?stripsearchkey($_GET[$var]):'';
		if(strlen($value)) {
			$wherearr[] = "{$pre}{$var}='$value'";
			$urls[] = "$var=".rawurlencode($value);
		}
	}

	foreach ($randkeys as $vars) {
		$value1 = isset($_GET[$vars[1].'1'])?$vars[0]($_GET[$vars[1].'1']):'';
		$value2 = isset($_GET[$vars[1].'2'])?$vars[0]($_GET[$vars[1].'2']):'';
		if($value1) {
			$wherearr[] = "{$pre}{$vars[1]}>='$value1'";
			$urls[] = "{$vars[1]}1=".rawurlencode($_GET[$vars[1].'1']);
		}
		if($value2) {
			$wherearr[] = "{$pre}{$vars[1]}<='$value2'";
			$urls[] = "{$vars[1]}2=".rawurlencode($_GET[$vars[1].'2']);
		}
	}

	foreach ($likekeys as $var) {
		$value = isset($_GET[$var])?stripsearchkey($_GET[$var]):'';
		if(strlen($value)>1) {
			$wherearr[] = "{$pre}{$var} LIKE '%$value%'";
			$urls[] = "$var=".rawurlencode($value);
		}
	}

	return array('wherearr'=>$wherearr, 'urls'=>$urls);
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

//获取排序
function getorders($alloworders, $default, $pre='') {
	$orders = array('sql'=>'', 'urls'=>array());
	if(!empty($_GET['orderby']) && in_array($_GET['orderby'], $alloworders)) {
		$orders['sql'] = " ORDER BY {$pre}$_GET[orderby] ";
		$orders['urls'][] = "orderby=$_GET[orderby]";
	} else {
		$orders['sql'] = empty($default)?'':" ORDER BY $default ";
		return $orders;
	}

	if(!empty($_GET['ordersc']) && $_GET['ordersc'] == 'desc') {
		$orders['urls'][] = 'ordersc=desc';
		$orders['sql'] .= ' DESC ';
	} else {
		$orders['urls'][] = 'ordersc=asc';
	}
	return $orders;
}

//生成excel

function make_excel($table,$pram){
	$url = str_replace(substr($pram,strpos($pram,'.')),'make_excel',$pram);
	$url .= strpos($url, '?') ? '&' : '?';
	$url .= 'dotable='.$table;
	ObHeader($url);
}



function uploadfile($inputname,$all_array=array('gif','jpg','jpeg','png','JPG','JPEG','flv','FLV'),$num='')
{
	global $_SGLOBAL;
	$attachdir='../uploads';//上传文件保存路径，结尾不要带/
	$dirtype=2;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
	$maxattachsize=209715200;//最大上传大小，默认是200M
	$ok=0;

	$err = "";
	$msg = "";
	$upfile=$_FILES[$inputname];
	if(!empty($upfile['error']))
	{
		switch($upfile['error'])
		{
			case '1':
				$err = '文件大小超过了php.ini定义的upload_max_filesize值';
				break;
			case '2':
				$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
				break;
			case '3':
				$err = '文件上传不完全';
				break;
			case '4':
				$err = '无文件上传';
				break;
			case '6':
				$err = '缺少临时文件夹';
				break;
			case '7':
				$err = '写文件失败';
				break;
			case '8':
				$err = '上传被其它扩展中断';
				break;
			case '999':
			default:
				$err = '无有效错误代码';
		}
	}elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
		$err = '无文件上传';
	}else{
			$temppath=$upfile['tmp_name'];
			$fileinfo=pathinfo($upfile['name']);
			$extension=$fileinfo['extension'];
			if(!in_array($extension,$all_array)){
				$err = '格式不正确';
				return array('ok'=>$ok,'error'=>$err);
			}
			$filesize=filesize($temppath);
			//if($filesize <= $maxattachsize)
			if($maxattachsize)
			{
				switch($dirtype)
				{
					case 1: $attach_subdir = 'day_'.date('ymd'); break;
					case 2: $attach_subdir = 'month_'.date('ym'); break;
					case 3: $attach_subdir = 'ext_'.$extension; break;
				}
				$attach_dir = $attachdir.'/'.$attach_subdir;
				if(!is_dir($attach_dir))
				{
					@mkdir($attach_dir, 0777);
					@fclose(fopen($attach_dir.'/index.htm', 'w'));
				}
				PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
				$randshu = rand(1,1000000);
				$filename=date("YmdHis").$randshu.$num.'.'.$extension;
				$target = $attach_dir.'/'.$filename;

				move_uploaded_file($upfile['tmp_name'],$target);
				$msg=$target;
				$ok = 1;
			}
			else $err='文件大小超过'.$maxattachsize.'字节';

			@unlink($temppath);
	}
	return array('ok'=>$ok,'error'=>$err,'msg'=>$msg,'dir'=>'uploads/'.$attach_subdir,'filename'=>$filename);
}

function js_show($msg){
	echo '<script type="text/javascript">alert(\''.$msg.'\');parent.hide_show();</script>';
}

?>