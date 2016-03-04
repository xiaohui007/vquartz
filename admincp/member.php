<?php
include_once('admincp.php');

if (submitcheck('listsubmit')) {

	if($_POST['uids'] && is_array($_POST['uids']) && $_POST['optype']) {
		$url = "member.php?perpage=$_GET[perpage]&page=$_GET[page]";
		switch ($_POST['optype']) {
			case '3':
				//删除
				$_SGLOBAL['db']->query("DELETE FROM ".tname('member')." WHERE id IN (".simplode($_POST['uids']).")");
				break;
		}
	}
	cpmessage('操作成功', $url);

}

	$mpurl = 'member.php';

	//处理搜索
	$intkeys = array('id','typeid','type','form');
	$strkeys = array();
	if($_GET['date1']&&$_GET['date2']){
		$randkeys = array(array('kong','date'));
	}else{
		$randkeys = array();
	}
	$likekeys = array('name','realname','tele','address');
	$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys, 'b.');
	$wherearr = $results['wherearr'];
	$mpurl .= '?'.implode('&', $results['urls']);

	$wheresql = empty($wherearr)?'1':implode(' AND ', $wherearr);

	//排序
	$orders = getorders(array('name'), 'date desc', 'b.');
	$ordersql = $orders['sql'];
	if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
	$orderby = array($_GET['orderby']=>' selected');
	$ordersc = array($_GET['ordersc']=>' selected');

	//显示分页
	$perpage = empty($_GET['perpage'])?0:intval($_GET['perpage']);
	if(!in_array($perpage, array(20,50,100,200,500))) $perpage = 20;
	$mpurl .= '&perpage='.$perpage;
	$perpages = array($perpage => ' selected');

	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;

	$csql = "SELECT COUNT(*) FROM ".tname('member')." b WHERE $wheresql";
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($csql), 0);

	$selectsql = '*';

	$list = array();
	$multi = '';

		//$qsql = "SELECT $selectsql FROM ".tname('member')." b WHERE $wheresql $ordersql LIMIT $start,$perpage";
	
	if($_GET['outreport']){
		$qsql = "SELECT $selectsql FROM ".tname('member')." b WHERE $wheresql $ordersql ";
	}else{
		$qsql = "SELECT $selectsql FROM ".tname('member')." b WHERE $wheresql $ordersql LIMIT $start,$perpage";
	}


	if($count) {
		$query = $_SGLOBAL['db']->query($qsql);
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($value['type']==1){
				$value['type'] = '新浪';
			}elseif($value['type']==2){
				$value['type'] = '腾讯';
			}else{
				$value['type'] = '本地';	
			}
			if($value['form']==1){
				$value['form'] = 'pc';	
			}elseif($value['form']==2){
				$value['form'] = 'mobile';	
			}
			$list[] = $value;
		}
		$multi = multi($count, $perpage, $page, $mpurl);
	}
	
if($_GET['outreport']){
	make_excel2($list);
}


function kong($date){
	return $date.' 00:00:00';
}

//导出excel
function make_excel2($datas){
	
   $filename = "data".date("YmdHis").".xls";
   Header("Content-Type: application/vnd.ms-excel");
   Header("Accept-Ranges:bytes");
   Header("Content-type:charset=utf-8");   
   Header("Content-Disposition: attachment; filename=".$filename);
   Header("Pragma: no-cache");
   Header("Expires: 0");
   echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>';
   echo '<table width="100%" border="1">';
   echo '<tr><td>名称</td><td>姓名</td><td>地址</td><td>电话</td><td>是否有购车意向</td><td>参加时间</td>';   
   echo '</tr>';

   if($datas)
	{
		foreach($datas as $data)
		{
			echo '<tr>
			<td>'.$data['name'].'</td>
			<td>'.$data['realname'].'</td>
			<td>'.$data['address'].'</td>
			<td>'.$data['tele'].'</td>
			<td>'.$data['yixiang'].'</td>
			<td>'.$data['date'].'</td>';
			echo '</tr>';
		}
	}
   echo '</table>';
   echo '</body></html>';
   exit;
}



include_once('header.php');
?>
<div class="mainarea">
<div class="maininner">

	<form method="get" action="member.php">
	<div class="block style4">

		<table cellspacing="3" cellpadding="3">
		<tr><th>用户名</th><td>
			<input type="text" name="name" value="<?=$_GET['name']?>" >
		</td>
        <th>所属分类</th><td>
			<select name="type" id="type" >
                <option value="" >全部</option>
                <option value="1" <?php if($_GET['type']=='1'){echo 'selected';}?> >新浪</option>
                <option value="2" <?php if($_GET['type']=='1'){echo 'selected';}?> >腾讯</option>
                <option value="3" <?php if($_GET['type']=='3'){echo 'selected';}?> >本地</option>
            </select> &nbsp;
		</td>
        </tr>
		<tr><th>真实姓名</th><td>
			<input type="text" name="realname" value="<?=$_GET['realname']?>" >
		</td>
        <th>上传类型</th><td>
			<select name="form" id="form" >
                <option value="" >全部</option>
                <option value="1" <?php if($_GET['type']=='1'){echo 'selected';}?> >电脑注册</option>
                <option value="2" <?php if($_GET['type']=='2'){echo 'selected';}?> >手机注册</option>
            </select> &nbsp;
		</td>
        </tr>
		<tr><th>电话</th><td>
			<input type="text" name="tele" value="<?=$_GET['tele']?>" >
		</td>
        <th>地址</th><td>
			<input type="text" name="address" value="<?=$_GET['address']?>" >
		</td>
        </tr>
		<tr><th>结果排序</th>
		<td colspan="3">
		<select name="orderby">
		<option value="">默认排序</option>
		<option value="title"<?=$orderby['title']?>>名称</option>
		</select>
		<select name="ordersc">
		<option value="desc"<?=$ordersc['desc']?>>递减</option>
		<option value="asc"<?=$ordersc['asc']?>>递增</option>
		</select>
		<select name="perpage">
		<option value="20"<?=$perpages['20']?>>每页显示20个</option>
		<option value="50"<?=$perpages['50']?>>每页显示50个</option>
		<option value="100"<?=$perpages['100']?>>每页显示100个</option>
		<option value="200"<?=$perpages['200']?>>每页显示200个</option>
		<option value="500"<?=$perpages['500']?>>每页显示500个</option>
		</select>
		<input type="hidden" name="ac" value="member">
		<input type="submit" name="searchsubmit" value="搜索" class="submit">&nbsp; <input type="submit" class="submit" name="outreport" value="导出查询名单"  />
		</td>
		</tr>
		</table>

	</div>
	</form>
<?php
if($list){
?>
	<form method="post" action="member.php?perpage=<?=$perpage?>&page=<?=$page?>" onsubmit="return show_detail();">
	<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
	<div class="bdrcontent">
		<div class="topactions"><a href="member.php">浏览</a></div>

		<p>总共有满足条件的数据 <strong><?=$count?></strong> 个</p>

		<table cellspacing="0" cellpadding="0" class="formtable">

		<tr><td width="25">&nbsp;</td><th>用户名</th><th>分类/id</th><th style="text-align:left;">详细信息</th></tr>

		<?php

		foreach($list as $value){

		?>

		<tr>

			<td>

				<input type="checkbox" name="uids[]" value="<?=$value['id']?>">

			</td>

			<td>

				<?=$value['name']?>
				<br />(<?=$value['form']?>)
			</td>

			<td>

				<?=$value['type']?> <br />
				<?=$value['typeid']?><br />
                姓名：<?=$value['realname']?><br />
				电话：<?=$value['tele']?><br />
				地址：<?=$value['address']?>

			</td>

			<td align="left">
				加入时间：<?=$value['date']?><br />
				最后登录时间：<?=$value['lastdate']?><br />
				加入ip：<?=$value['ip']?><br />
				最后登录ip：<?=$value['lastip']?><br />
			</td>

		</tr>

		<?php

		}

		?>

		</table>



	</div>



	<div class="footactions">

		<input type="checkbox" id="chkall" name="chkall" onclick="checkAll(this.form, 'uids')">全选 &nbsp;&nbsp;

		操作类型：

		<select name="optype">

			<option value="3">  删  除  </option>

		</select>

		<input type="submit" name="listsubmit" value="批量操作" class="submit">

		<input type="hidden" name="mpurl" value="<?=$mpurl?>">

		<div class="pages"><?=$multi?></div>

	</div>



	</form>

<?php

}else{

?>

	<div class="bdrcontent">

		<div class="topactions"><a href="member.php">浏览</a></div>

		<p>指定条件下还没有数据</p>

	</div>

<?php

}

?>

</div>

</div>

<script type="text/javascript">

function show_detail(){

	if(confirm('确认操作该操作？')){

		return true;

	}else{

		return false;

	}

}

</script>

<div class="side">

	<?php

		$ac='member';

		include_once('side.php');

	?>

</div>



<?php

include_once('footer.php');

?>