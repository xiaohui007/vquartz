<?php
include_once('common.php');

InitGP(array('xilie'));

empty($xilie) && $xilie = 1;

$wheresql = '';
$tablesql = '';
$pageurl = 'product.php?d=1';

if($xilie){
  $wheresql .= " AND A.xilie='".$xilie."'"; 
  $pageurl .='&xilie='.$xilie;
}

$orderby = 'A.no_order DESC';

$page = GetGP('page');
$count =$_SGLOBAL['db']->get_value("SELECT count(*) FROM ".tname('product')." A ".$tablesql." WHERE A.display='1' ".$wheresql);
$perpage = GetGP('perpage');
empty($perpage) && $perpage=12;
$allpage = ceil($count/$perpage);
$page>$allpage && $page = $allpage;
$page = empty($page)?1:intval($page);
if($page<1) $page = 1;
$start = ($page-1)*$perpage;

$list = array();
if($count>0){
  $query = $_SGLOBAL['db']->query("select A.* from ".tname('product')." A ".$tablesql." where A.display='1' $wheresql ORDER BY $orderby LIMIT $start,$perpage ");
  while($value = $_SGLOBAL['db']->fetch_array($query)){
    
    //$value['xilie'] = $xilie_array[$value['xilie']];
    // if(file_exists($value['src'].'.thumb.png')){
    //   $value['img'] = $value['src'].'.thumb.png';
    // }else{
    //   $value['img'] = $value['src'];
    // }

    $value['img'] = 'uploads/product/'.$value['id'].'-small.jpg';

    $list[$value['id']] = $value;
  }
  $multpage = multi($count,$perpage,$page,$pageurl);
}

//读取分类
$query = $_SGLOBAL['db']->query("select * from ".tname('xilie')."  where display='1' AND type='1' ORDER BY sort ASC ");
while($value = $_SGLOBAL['db']->fetch_array($query)){      
  $xilielist[] = $value;
}

$title="产品"; $index_nav="2"; include 'inc/header.php'; 
?>
<style type="text/css">
.pagination em{display: none;}
.pagination strong{background-color:#d4900a;width: 30px;line-height: 30px;height: 30px;font-size: 16px;display: inline-block;color: #fff;margin-left: 5px;}
.pagination a{margin-left: 5px;}
</style>
	<div class="container">
		<div class="inner">
			<div class="menu">
				<div class="menu-bar"><i></i></div>
				<!-- Start 种类菜单 -->
				<ul>
					<?php
					foreach($xilielist as $val){
					?>
					<li><a <?php if($val['id']==$xilie){echo 'class="active"';} ?> href="product.php?xilie=<?php echo $val['id']; ?>"><?=$val['name']?></a></li>
					<?php
					}
					?>
				</ul>
				<!-- End 种类菜单 -->
			</div>
			<div class="p_list">
				<?php if($list){ ?>
				<ul>
				<?php foreach ($list as $key => $value) {?>
					<li>
						<a class="m_prod" href="product_detail.php?id=<?php echo $value['id'];?>">
							<img src="<?php echo $value['img']; ?>">
							<i></i><span>No.<?php echo $value['title']; ?></span>
						</a>
					</li>
				<?php } ?>
				</ul>
				<?php } ?>
				<div class="pagination">
					<?php echo $multpage;  ?>
				</div>
				<!-- <div class="pagination">
					<a href="javascript:;">◀</a>
					<a href="javascript:;">1</a>
					<a class="curr" href="javascript:;">2</a>
					<span>...</span>
					<a href="javascript:;">99</a>
					<a href="javascript:;">▶</a>
				</div> -->
			</div>
		</div>
	</div>
<?php
include 'inc/footer.php';
?>