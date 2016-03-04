<?php
include_once('admincp.php');

$show_list_award = array(
	'产品信息'=>'product',
	'相册信息'=>'active',
	'未查看留言'=>'contact',
);

if($_SESSION['admin_user'] != 'admin'){
	//$count_where = " AND `adminid`='".$_SESSION['admin_uid']."'";
}else{
	//$count_where = "";
}

$product = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('product')." WHERE 1 ".$count_where),0);
$active = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('active')." WHERE 1 ".$count_where),0);
$contact = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('contact')." WHERE isshow='0' ".$count_where),0);

include_once('header.php');
?>
<div class="mainarea">
<div class="maininner">
	<div class="bdrcontent">
	<?php
		$key = 1;
		foreach($show_list_award as $rkey=>$value){
	?>
		<p><?=$key?>.<?=$rkey?> <strong><?=${$value}?></strong> </p>
	<?php
		$key++;
	}
	?>
	</div>
</div>
</div>

<div class="side">
	<?php
		$ac='index';
		include_once('side.php');
	?>
</div>
<?php
include_once('footer.php');
?>