<?php

@define('IN_SITE', TRUE);
define('D_BUG', '1');

D_BUG?error_reporting(7):error_reporting(0);
//date_default_timezone_set("PRC");
set_magic_quotes_runtime(0);

$_SGLOBAL = $_SCONFIG = $_SCOOKIE = array();


//程序目录 定义程序目录常量。其中DIRECTORY_SEPARATOR是路径分隔符，linux上就是’/’ windows上是’\’
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

//基本文件 加入@,达到找不到该文件时不提示错误信息
if(!@include_once(S_ROOT.'./config.php')) {
	exit();
}

//session_start();

//通用函数
include_once(S_ROOT.'./source/function_common.php');


//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];

//GPC过滤
$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = saddslashes($_GET);
	$_POST = saddslashes($_POST);
}

//本站URL
if(empty($_SC['siteurl'])) $_SC['siteurl'] = getsiteurl();

//链接数据库
dbconnect();

//COOKIE
$prelength = strlen($_SC['cookiepre']);
foreach($_COOKIE as $key => $val) {
	if(substr($key, 0, $prelength) == $_SC['cookiepre']) {
		$_SCOOKIE[(substr($key, $prelength))] = empty($magic_quote) ? saddslashes($val) : $val;
	}
}

//启用GIP
if ($_SC['gzipcompress'] && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
} else {
	ob_start();
}

$_SGLOBAL['inajax'] = empty($_GET['inajax'])?0:intval($_GET['inajax']);
$_SGLOBAL['refer'] = empty($_SERVER['HTTP_REFERER'])?'':$_SERVER['HTTP_REFERER'];
$_SGLOBAL['ismobile'] = ismobile();

checkauth();

$xilie_array = array(
	5=>'弹力面膜系列',
	1=>'悦净颜系列',
	2=>'悦水润系列',
	3=>'悦美白系列',
	4=>'悦莹润系列',
);

$gongneng_array = array(
	1=>'美白',
	2=>'补水',
	3=>'深层清洁',
	4=>'控油',
	5=>'防晒',
	6=>'祛黑头',
	7=>'舒缓敏感',
);
$pinlei_array = array(
	1=>'面膜',
	2=>'洁面',
	3=>'护肤水',
	4=>'乳液\面霜',
	5=>'眼部护理',
	6=>'修颜\粉底霜',
	7=>'防晒',
	9=>'手部\身体护理',
	10=>'超值套装',
);

$fuzhi_array = array(
	1=>'所有肤质适用',
	2=>'油性/混合性肌肤',
	3=>'中性/干性肌肤',
	4=>'脆弱肌肤',
	5=>'毛孔粗大肌肤',
	6=>'缺水肤质',
	7=>'出油肌肤',
	8=>'细纹、皱纹等肌肤',
	9=>'暗黄、肤色不均肌肤',
	10=>'污垢沉淀，有黑头的肌肤',
	11=>'干燥、无光泽肌肤',
	12=>'出油、毛孔粗大肌肤',
);

$xilie_info_array = array(
	1=>array(
		'title'=>'悦净颜系列',
		'dec'=>'<h3>青春美颜洗出来</h3><p>萃取天然花朵精粹，温和软化肌肤角质，渗透毛孔，有效清除肌肤的污垢，唤醒肌肤活力，在洁肤的同时保持肌肤柔嫩润泽，为肌肤带来清新感受。</p>',
		'img'=>'yjy/propic.png',
	),
	2=>array(
		'title'=>'悦水润系列',
		'dec'=>'<h3>深层补水  养出肌肤水盈透</h3><p>特含保湿佳品库拉索芦荟叶提取物，配合多种天然植物，萃取其精华成分，水润直达肌底并持续锁水，让肌肤水润饱满一整天，缔造水灵、娇嫩、光滑的美丽肌肤！</p>',
		'img'=>'ysr/propic.png',
	),
	3=>array(
		'title'=>'悦美白系列',
		'dec'=>'<h3>层层美白养出来</h3><p>蕴含天女木兰、人参等多种美白植物精华成分，迅速唤醒肌肤美白活力，由内而外调养，帮助减轻暗淡肤色，肌肤由内而外净白剔透，呈现透白光彩的美丽肤色，焕发光彩！</p>',
		'img'=>'ymb/propic.png',
	),
	4=>array(
		'title'=>'悦莹润系列',
		'dec'=>'<h3>繁花精粹   白里透红</h3><p>萃取玫瑰、金盏花及石榴精华成分，繁花精粹汇集成一整瓶精华水，直达肌底，天然调养，迅速唤醒肌肤活力，缔造肌肤新鲜水灵、娇艳欲滴。</p>',
		'img'=>'yyr/propic.png',
	),
	5=>array(
		'title'=>'弹力面膜系列',
		'dec'=>'<h3>植物粘蛋白  唤醒水润弹力</h3><p>植物粘蛋白、维生素   等滋养成分，帮助修护肌肤弹力网，提升肌肤紧致度，使得肌肤富有弹性！帮助肌肤表层形成保护膜，有效锁住水分，令肌肤水润弹润！</p><h3>天丝面膜材质</h3><p>轻透细致，吸水性好，天然材质，可降解。完美贴合肌肤，轻盈无负担，实现对每寸肌肤的轻柔呵护。<span class="not">*植物粘蛋白指长柔毛薯蓣根提取物</span></p>',
		'img'=>'ytr/propic.png',
	),
);

?>