<?php
include_once('admincp.php');

if (submitcheck('listsubmit')) {

	if($_POST['uids'] && is_array($_POST['uids']) && $_POST['optype']) {
		$url = "contact.php?perpage=$_GET[perpage]&page=$_GET[page]";
		switch ($_POST['optype']) {
			case '3':
				//删除
				$_SGLOBAL['db']->query("DELETE FROM ".tname('contact')." WHERE id IN (".simplode($_POST['uids']).")");
				break;
			case '1':
				//删除
				$_SGLOBAL['db']->query("update ".tname('contact')." set isshow='1' WHERE id IN (".simplode($_POST['uids']).")");
				
				//$countss = count($_POST['uids']);
				//$_SGLOBAL['db']->query("update ".tname('contact')." set num=num-$countss WHERE id IN (".simplode($_POST['uids']).")");				
				
				break;
			case '2':
				//删除
				$_SGLOBAL['db']->query("update ".tname('contact')." set isshow='0' WHERE id IN (".simplode($_POST['uids']).")");
				break;
		}
	}
	cpmessage('操作成功', $url);

}

	$mpurl = 'contact.php';

	//处理搜索
	$intkeys = array('id','pid','isshow');
	$strkeys = array();
	if($_GET['date1']&&$_GET['date2']){
		$randkeys = array(array('kong','date'));
	}else{
		$randkeys = array();
	}
	$likekeys = array('name','tele');
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

	$csql = "SELECT COUNT(*) FROM ".tname('contact')." b WHERE $wheresql";
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($csql), 0);

	$selectsql = '*';

	$list = array();
	$multi = '';

	$qsql = "SELECT $selectsql FROM ".tname('contact')." b WHERE $wheresql $ordersql LIMIT $start,$perpage";

	if($count) {
		$query = $_SGLOBAL['db']->query($qsql);
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['isshow'] = $value['isshow']==1?'<b style="color:#F00">已查看</b>':'未查看';
			$list[] = $value;
		}
		$multi = multi($count, $perpage, $page, $mpurl);
	}
	


function kong($date){
	return $date.' 00:00:00';
}

include_once('header.php');
?>
<div class="mainarea">
<div class="maininner">

	<form method="get" action="contact.php">
	<div class="block style4">

		<table cellspacing="3" cellpadding="3">
		<tr>
		<th>姓名</th><td>
			<input type="text" name="name" value="<?=$_GET['name']?>" >
		</td>        
        </tr>
        <tr><th>电话</th><td>
			<input type="text" name="tele" value="<?=$_GET['tele']?>" >
		</td>
		<th>查看状态</th><td>
			<select name="isshow">
			<option value="">不限</option>
			<option value="1" <?php if(1==$_GET['isshow']){echo 'selected="selected"';}?> >已查看</option>
			<option value="0" <?php if('0'==$_GET['isshow']){echo 'selected="selected"';}?> >未查看</option>
			</select>
		</td>        
        </tr>
		<tr><th>结果排序</th>
		<td colspan="3">
		<select name="orderby">
		<option value="">默认排序</option>
		<option value="name"<?=$orderby['name']?>>名称</option>
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
		<input type="hidden" name="ac" value="contact">
		<input type="submit" name="searchsubmit" value="搜索" class="submit">
		</td>
		</tr>
		</table>

	</div>
	</form>
<?php
if($list){
?>
	<form method="post" action="contact.php?perpage=<?=$perpage?>&page=<?=$page?>" onsubmit="return show_detail();">
	<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
	<div class="bdrcontent">
		<div class="topactions"><a href="contact.php">浏览</a></div>
		<p>总共有满足条件的数据 <strong><?=$count?></strong> 个</p>
		<table cellspacing="0" cellpadding="0" class="formtable">
		<tr><td width="25">&nbsp;</td><th>信息</th><th>留言内容</th><!--<th width="100" style="text-align:center;">管理</th>--></tr>
		<?php
		foreach($list as $value){
		?>
		<tr>
			<td>
				<input type="checkbox" name="uids[]" value="<?=$value['id']?>">
			</td>
			<td>
				姓名：<?=$value['name']?> <br />
				手机：<?=$value['tele']?> <br />
				留言时间：<?=$value['date']?> <br />
				状态：<?=$value['isshow']?> <br />
			</td>
			<td>
				<?php echo nl2br($value['info']); ?> <br />
			</td>
			<!--<td align="center">
				<a href="contact_mamage.php?id=<?=$value['id']?>&action=edit"> 管理</a>
			</td>-->
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
			<option value="1">  已查看  </option>
			<option value="2">  查看不通过  </option>
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
		<div class="topactions"><a href="contact.php">浏览</a></div>
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
		$ac='contact';
		include_once('side.php');
	?>
</div>

<?php
include_once('footer.php');
?>