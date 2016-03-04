<?php
include_once('admincp.php');
include_once(S_ROOT.'./source/makethumb.php');

$action = GetGP('action');

if(submitcheck('usergroupsubmit')){

	InitGP(array('refer','id','title','guige','guigedingzhi','xilie','date'));

	if($action == 'add'){
		if($_FILES['pic']['tmp_name']){
			empty($_FILES['pic']['tmp_name']) && showmsg('请上传产品',1);
			$result = uploadfile('pic');
			if($result['ok']=='0'){
				showmsg($result['error'],1);	
			}
			$resizeimage = new resizeimage($result['msg'], "80", "80", "1", $result['msg'].".thumb.jpg");
		}
		
		$data_array=array(
			'title'=>$title,
			'guige'=>$guige,
			'guigedingzhi'=>$guigedingzhi,
			'xilie'=>$xilie,
			'src'=>$result['dir'].'/'.$result['filename'],
			'date'=>date('Y-m-d H:i:s'),
		);
		
	}elseif($action == 'edit'){
		$data_array=array(
			'title'=>$title,
			'guige'=>$guige,
			'guigedingzhi'=>$guigedingzhi,
			'xilie'=>$xilie,
			'date'=>$date,
		);
		if(!empty($_FILES['pic']['tmp_name'])){
			$result = uploadfile('pic');
			if($result['ok']=='0'){
				showmsg($result['error'],1);	
			}
			//删除之前的产品
			if(file_exists('../'.$data['pic_path'])){
				//unlink('../'.$data['pic_path']);
			}
			//删除之前的产品
			if(file_exists('../'.$data['pic_path'].".thumb.jpg")){
				//unlink('../'.$data['pic_path'].".thumb.jpg");
			}
			$resizeimage = new resizeimage($result['msg'], "80", "80", "1", $result['msg'].".thumb.jpg");
			$data_array['src'] = $result['dir'].'/'.$result['filename'];
		}
	}

	if($action == 'edit'){
		!$id && exit('该产品不存在');
		updatetable('product', $data_array, array('id' => $id));
		if(empty($refer)){
			$refer='product_mamage.php?action=edit&id='.$id;
		}
		cpmessage('操作成功',$refer);
	}elseif($action == 'add'){
		$id = inserttable('product', $data_array,1);
		cpmessage('添加操作成功',"product_mamage.php?action=add");
	}
}


if($action == 'edit'){

	$id=(int)GetGP('id');
	!$id && exit('该产品不存在');
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('product')." WHERE id='$id'");
	if(!$paipai = $_SGLOBAL['db']->fetch_array($query)){
		cpmessage('该产品不存在');
	}
	
}

//读取分类
$query = $_SGLOBAL['db']->query("select * from ".tname('xilie')."  where display='1' ORDER BY sort ASC ");
while($value = $_SGLOBAL['db']->fetch_array($query)){      
  $xilielist[$value['id']] = $value['name'];
}

$action_do = $action=='add'?'添加':'修改';

include_once('header.php');
?>
<script type="text/javascript" src="../js/jquery-1.6.1.min.js" ></script>
<style type="text/css">
@import url(js/themes/default/default.css);
</style>

<script charset="utf-8" src="js/kindeditor-min.js"></script>
<script charset="utf-8" src="js/zh_CN.js"></script>
<script type="text/javascript">

			var editor;
			KindEditor.ready(function(K) {
				editor = K.create('textarea[name="guigedingzhi"]', {
					resizeType : 1,
					uploadJson : '../upload_json.php'
				});
			});


</script>

<style type="text/css">
.inputclass{width:180px; padding:1px;}
</style>
<div class="mainarea">
<div class="maininner">
	<form method="post" action="product_mamage.php" enctype="multipart/form-data" onsubmit="return show_detail();">
	<input type="hidden" name="formhash" value="<?php echo formhash();?>" />
	<div class="bdrcontent">
		<div class="topactions"><a href="product.php">浏览</a></div>
		<div class="title">
			<h3><?=$action_do?> 产品</h3>
		</div>
		<table cellspacing="0" cellpadding="0" class="formtable" style="text-align:left;">
		<tr>
			<th width="120">标题：</th>
			<td>
				<input type='text' class='normal' name='title' id="title" style="width:300px;" value="<?php if(!empty($paipai['title'])){echo $paipai['title'];}?>" />
			</td>
		</tr>
		<tr>
			<th>标准规格：</th>
			<td>
				<input type='text' class='normal' name='guige' id="guige" style="width:300px;" value="<?php if(!empty($paipai['guige'])){echo $paipai['guige'];}?>" />
			</td>
		</tr>
		<tr>
			<th>系列：</th>
			<td>
				<select name="xilie" id="xilie" >
                	<?php
                    foreach($xilielist as $key=>$val){
					?>
                    <option value="<?=$key?>" <?php if($key==4){echo 'selected="selected"';}?> ><?=$val?></option>
                    <?php
					}
					?>
                </select> &nbsp;
			</td>
		</tr>
        
		<tr>
			<th>上传产品文件：</th>
			<td>
				<input type="file" name="pic" id="pic"  /> (格式：jpg,png；尺寸宽：500px 高：500px)
			</td>
		</tr>
		<tr>
			<th>可定制规格：</th>
			<td>
				<textarea name='guigedingzhi' style="height:150px;width:650px;" id="guigedingzhi" >
                    <?php if(!empty($paipai['guigedingzhi'])){echo $paipai['guigedingzhi'];}?>
                </textarea>
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
		<input type="button" name="buttom" onclick="location.href='product.php'" value="返回列表" class="submit"> &nbsp;<span id="detail"></span>
	</div>
	</form>
</div>
</div>
<div class="side">
	<?php
		$ac='product';
		include_once('side.php');
	?>
</div>

<?php
include_once('footer.php');
?>