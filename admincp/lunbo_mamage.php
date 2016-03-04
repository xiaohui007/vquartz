<?php
include_once('admincp.php');
include_once(S_ROOT.'./source/makethumb.php');

$lunbo = GetGP('lunbo');
$title = GetGP('title');

if($lunbo == 'edit'){
	$id = GetGP('id');
	$data = $_SGLOBAL['db']->get_one("select * from ".tname('index_switch'). " where id=".$id);
}
if(submitcheck('usergroupsubmit')){

	InitGP(array('id','sort','url'));
	
	empty($url) && showmsg('请输入链接地址',1);
	//empty($content) && showmsg('请输入内容',1);
	
	if($lunbo == 'add'){
		
		if($_FILES['pic']['tmp_name']){
			$result = uploadfile('pic');
			$result['self'] = substr($result['msg'],0,strrpos($result['msg'], '.'));
			
			if($result['ok']=='0'){
				showmsg($result['error'],1);	
			}
			// $resizeimage = new resizeimage($result['msg'], "192", "202", "1", $result['self'].".thumb.jpg");
			// $data_array['thumb_src'] = $result['self'].'.thumb.jpg';
			$data_array['src'] = $result['dir'].'/'.$result['filename'];
		}
		
		$data_array=array(
			'title'=>$title,
			'url'=>$url,
			'src'=>$data_array['src'],
			'time'=>date('Y-m-d H:i:s'),
			'sort'=>$sort,
		);

	}elseif($lunbo == 'edit'){
		
		!$id && showmsg('数据不存在');	
			
		$data_array=array(
			'title'=>$title,
			'sort'=>$sort,
			'time'=>date('Y-m-d H:i:s'),
			'url'=>$url,
		);


		if(!empty($_FILES['pic']['tmp_name'])){
			$result = uploadfile('pic');
			$result['self'] = substr($result['msg'],0,strrpos($result['msg'], '.'));
			if($result['ok']=='0'){
				showmsg($result['error'],1);	
			}

			//删除之前的图片
			if(file_exists('../'.$data['src'])){
				unlink('../'.$data['src']);
			}
			//删除之前的图片
			// if(file_exists($data['thumb_src'])){
			// 	unlink($data['thumb_src']);
			// }
			// $resizeimage = new resizeimage($result['msg'], "192", "202", "1", $result['self'].".thumb.jpg");
			// $data_array['thumb_src'] = $result['self'].'.thumb.jpg';
			$data_array['src'] = $result['dir'].'/'.$result['filename'];
		}

		
	}
	
	if($lunbo == 'edit'){
		!$id && exit('该图片不存在');
		updatetable('index_switch', $data_array, array('id' => $id));
		if(empty($refer)){
			$refer='lunbo_mamage.php?lunbo=edit&id='.$id;
		}
		cpmessage('操作成功',$refer);
	}elseif($lunbo == 'add'){
		
		$id = inserttable('index_switch', $data_array,1);

		cpmessage('操作成功',"lunbo.php");

	}
}


if($lunbo == 'edit'){

	$id=(int)GetGP('id');
	!$id && exit('该图片不存在');
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('index_switch')." WHERE id='$id'");
	if(!$paipai = $_SGLOBAL['db']->fetch_array($query)){
		cpmessage('该图片不存在');
	}
}

$lunbo_do = $lunbo=='add'?'添加':'修改';

include_once('header.php');
?>

<style type="text/css">
.inputclass{width:180px; padding:1px;}
</style>
<div class="mainarea">
<div class="maininner">
	<form method="post" action="lunbo_mamage.php" enctype="multipart/form-data" onsubmit="return show_detail();">
	<input type="hidden" name="formhash" value="<?php echo formhash();?>" />
	<div class="bdrcontent">
		<div class="topactions"><a href="index.php">浏览</a></div>
		<tr>
			<th width="150">标题：</th>
			<td>
				<input type='text' class='normal' name='title' id="title" style="width:200px;" value="<?php if(!empty($paipai['title'])){echo $paipai['title'];}?>" />
			</td>
		</tr>
		<br/>
		<br/>
		<tr>
			<th width="150">排序：</th>
			<td>
				<input type='text' class='normal' name='sort' id="sort" style="width:50px;" value="<?php if(!empty($paipai['sort'])){echo $paipai['sort'];}?>" /> （数字越大越前）
			</td>
		</tr>
		<br/>
		<br/>
		<tr>
			<th width="150">链接地址：</th>
			<td>
				<input type='text' class='normal' name='url' id="url" style="width:350px;" value="<?php if(!empty($paipai['url'])){echo $paipai['url'];}?>" /> (如果没有链接地址不用输入)
			</td>
		</tr>
		<br/>
		<br/>
		<tr>
			<th>上传图片文件：</th>
			<td>
				<input type="file" name="pic" id="pic"  /> (格式：jpg,png,尺寸：1780*840)
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
		<input type="hidden" name="lunbo" value="<?=$lunbo?>">
		<input type="hidden" name="refer" value="<?=$_SGLOBAL['refer']?>">
		<input type="submit" name="usergroupsubmit" value="提交" class="submit"> &nbsp;
		<input type="button" name="buttom" onclick="location.href='lunbo.php'" value="返回列表" class="submit"> &nbsp;<span id="detail"></span>
	</div>
	</form>
</div>
</div>
<div class="side">
	<?php
		$ac='lunbo';
		include_once('side.php');
	?>
</div>

<?php
include_once('footer.php');
?>