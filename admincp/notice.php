<?php
include_once('admincp.php');

$action = GetGP('action');


if(submitcheck('usergroupsubmit')){

	InitGP(array('refer','id','content'));
	
	if($action == 'edit'){
		$data_array=array(
			'content'=>$content,
		);
	}

	if($action == 'edit'){
		!$id && exit('该公告不存在');
		updatetable('notice', $data_array, array('id' => $id));
		if(empty($refer)){
			$refer='notice.php?action=edit&id='.$id;
		}
		cpmessage('操作成功','notice.php');
	}
}



$id=(int)GetGP('id');
!$id && $id=1;
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('notice')." WHERE id='$id'");
if(!$paipai = $_SGLOBAL['db']->fetch_array($query)){
	cpmessage('该公告不存在');
}

$action_do = $action=='add'?'添加':'修改';

include_once('header.php');
?>
<style type="text/css">
.inputclass{width:180px; padding:1px;}
</style>
<div class="mainarea">
<div class="maininner">
	<form method="post" action="notice.php" enctype="multipart/form-data" onsubmit="return show_detail();">
	<input type="hidden" name="formhash" value="<?php echo formhash();?>" />
	<div class="bdrcontent">
		<div class="topactions"><a href="notice.php">浏览</a></div>
		<div class="title">
			<h3><?=$action_do?> 公告</h3>
		</div>
		<table cellspacing="0" cellpadding="0" class="formtable" style="text-align:left;">
		<tr>
			<th>公告内容：</th>
			<td>
				<textarea name="content" style="width:400px; height:100px;"><?=$paipai['content']?></textarea>  &nbsp;
			</td>
		</tr>
		</table>
	</div>
	<div class="footactions">
		<input type="hidden" name="id" value="<?=$id?>">
		<input type="hidden" name="action" value="edit">
		<input type="hidden" name="refer" value="<?=$_SGLOBAL['refer']?>">
		<input type="submit" name="usergroupsubmit" value="提交" class="submit"> &nbsp;
		<input type="button" name="buttom" onclick="location.href='notice.php'" value="返回列表" class="submit"> &nbsp;<span id="detail"></span>
	</div>
	</form>
</div>
</div>
<div class="side">
	<?php
		$ac='notice';
		include_once('side.php');
	?>
</div>

<?php
include_once('footer.php');
?>