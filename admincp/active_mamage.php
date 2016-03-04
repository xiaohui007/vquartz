<?php
include_once('admincp.php');
include_once(S_ROOT.'./source/makethumb.php');

$action = GetGP('action');

if(submitcheck('usergroupsubmit')){

	InitGP(array('id','title','date','pids'));
	
	empty($title) && showmsg('请输入标题',1);
	
	if($action == 'add'){
		
		$data_array=array(
			'title'=>$title,
			'pids'=>$pids,
			'date'=>date("Y-m-d H:i:s"),
		);
		
		if($_FILES['pic']['tmp_name']){
			$result = uploadfile('pic');
			if($result['ok']=='0'){
				showmsg($result['error'],1);	
			}
			$resizeimage = new resizeimage($result['msg'], "192", "202", "1", $result['msg'].".thumb.jpg");
			$data_array['src'] = $result['dir'].'/'.$result['filename'];
		}
	}elseif($action == 'edit'){
		
		!$id && showmsg('数据不存在');	
		
		$data_array=array(
			'title'=>$title,
			'pids'=>$pids,
			'date'=>$date,
		);
		
		if(!empty($_FILES['pic']['tmp_name'])){
			$result = uploadfile('pic');
			if($result['ok']=='0'){
				showmsg($result['error'],1);	
			}
			//删除之前的图片
			if(file_exists('../'.$data['src'])){
				//unlink('../'.$data['src']);
			}
			//删除之前的图片
			if(file_exists('../'.$data['src'].".thumb.jpg")){
				//unlink('../'.$data['src'].".thumb.jpg");
			}
			$resizeimage = new resizeimage($result['msg'], "192", "202", "1", $result['msg'].".thumb.jpg");
			$data_array['src'] = $result['dir'].'/'.$result['filename'];
		}
		
	}
	
	if($action == 'edit'){
		!$id && exit('该相册不存在');
		updatetable('active', $data_array, array('id' => $id));
		if(empty($refer)){
			$refer='active_mamage.php?action=edit&id='.$id;
		}
		cpmessage('操作成功',$refer);
	}elseif($action == 'add'){
		$id = inserttable('active', $data_array,1);
		cpmessage('操作成功',"active_mamage.php?action=add");
	}
}


if($action == 'edit'){

	$id=(int)GetGP('id');
	!$id && exit('该相册不存在');
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('active')." WHERE id='$id'");
	if(!$paipai = $_SGLOBAL['db']->fetch_array($query)){
		cpmessage('该相册不存在');
	}

}

$action_do = $action=='add'?'添加':'修改';

include_once('header.php');
?>

<style type="text/css">
.inputclass{width:180px; padding:1px;}
</style>
<div class="mainarea">
<div class="maininner">
	<form method="post" action="active_mamage.php" enctype="multipart/form-data" onsubmit="return show_detail();">
	<input type="hidden" name="formhash" value="<?php echo formhash();?>" />
	<div class="bdrcontent">
		<div class="topactions"><a href="active.php">浏览</a></div>
		<div class="title">
			<h3><?=$action_do?> 相册</h3>
		</div>
		<table cellspacing="0" cellpadding="0" class="formtable" style="text-align:left;">
		<tr>
			<th width="150">相册标题：</th>
			<td>
				<input type='text' class='normal' name='title' id="title" style="width:300px;" value="<?php if(!empty($paipai['title'])){echo $paipai['title'];}?>" />
			</td>
		</tr>
		<tr>
			<th>关联产品：</th>
			<td>
				<input type='text' class='normal' name='pids' id="pids" style="width:400px;" value="<?php if(!empty($paipai['pids'])){echo $paipai['pids'];}?>" /> （输入关联产品id，多个产品用,隔开）
			</td>
		</tr>
		<tr>
			<th>上传图片文件：</th>
			<td>
				<input type="file" name="pic" id="pic"  /> (格式：jpg,png,尺寸：192*202)
			</td>
		</tr>
		<?php
        if($action == 'edit'){
        ?>
        <tr>
            <th> 发布时间：</th>
            <td><input type="text" name="date" id="date" value="<?php if(!empty($paipai['date'])){echo $paipai['date'];}?>"  /></td>
        </tr>
        <?php
        }
        ?>
		</table>
	</div>
	<div class="footactions">
		<input type="hidden" name="id" value="<?=$id?>">
		<input type="hidden" name="action" value="<?=$action?>">
		<input type="hidden" name="refer" value="<?=$_SGLOBAL['refer']?>">
		<input type="submit" name="usergroupsubmit" value="提交" class="submit"> &nbsp;
		<input type="button" name="buttom" onclick="location.href='active.php'" value="返回列表" class="submit"> &nbsp;<span id="detail"></span>
	</div>
	</form>
</div>
</div>
<div class="side">
	<?php
		$ac='active';
		include_once('side.php');
	?>
</div>

<?php
include_once('footer.php');
?>