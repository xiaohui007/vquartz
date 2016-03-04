<?php 
include_once('common.php');

$id = GetGP('id');
$info = $_SGLOBAL['db']->get_one("select * from ".tname('product')." where id='".$id."' AND display='1'");
if(empty($info)){
	header("Location:product.php");
	exit;
}
$info['img'] = 'uploads/product/'.$info['id'].'-small.jpg';
$info['big'] = 'uploads/product/'.$info['id'].'-big.jpg';

//查看其它该类产品
$list = array();
$query = $_SGLOBAL['db']->query("select id,title,src from ".tname('product')." where id!='".$info['id']."' AND display='1' AND xilie='".$info['xilie']."' ORDER BY no_order desc LIMIT 8");
while($value = $_SGLOBAL['db']->fetch_array($query)){
	// if(file_exists($value['src'].'.thumb.png')){
	// 	$value['thumb'] = $value['src'].'.thumb.png';
	// }else{
	// 	$value['thumb'] = $value['src'];
	// }
	$value['img'] = 'uploads/product/'.$value['id'].'-small.jpg';

	$list[$value['id']] = $value;
}

$index_id = 'id="p-product_detail"';
$title="产品详细-".$info['title']; $index_nav="2"; include 'inc/header.php'; 
?>
<style media="print">
　　.Noprint{display:none;} //隐藏不需要的控件
　　.PageNext{page-break-after: always;} //分页打印标记
</style>
	<div class="container">
		<div class="inner">
			<div class="menu">
				<div class="menu-bar"><i></i></div>
			</div>

			<!-- Start 详细 -->
			<div class="p_detail">
			 	<div class="p_view">
					<img src="<?=$info['big']?>">
				</div>
				<div class="p_intro">
					<!--标题编号-->
					<div class="serial">No.<?=$info['title']?></div>
					<div class="data">
						<!-- Start 内容数据 -->
						<h2>标准规格</h2>
						<p>
							<?php if($info['guige']){echo $info['guige'];}else{echo '暂无信息';}?>
						</p>
						<h2>可定制规格</h2>
						<p>
							<?php if($info['guigedingzhi']){echo $info['guigedingzhi'];}else{echo '暂无信息';}?>
						</p>
						<!-- End 内容数据 -->
					</div>
					<object id="WebBrowser" classid="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2" height="0"
                width="0">
          </object>
					<!-- Start 打印按钮 -->
					<a class="btn-printer" href="javascript:document.all.WebBrowser.ExecWB(7,1);">打印</a>
					<!-- End 打印按钮 -->
				</div>
				<!-- End 详细 -->
			</div>

			<?php if($list){ ?>
			<!-- Start 其他更多 -->
			<div class="otherwise">
				<h3>查看其他</h3>
				<div class="swiper-container swiper-otherwise">
					<div class="swiper-wrapper">
						<?php foreach ($list as $key => $value) {?>
						<div class="swiper-slide">
							<a class="m_prod" href="product_detail.php?id=<?=$value['id']?>">
								<img src="<?=$value['img']?>">
								<i></i><span>No.<?=$value['title']?></span>
							</a>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="swiper-control">
					<a class="swiper-next"></a>
					<a class="swiper-prev"></a>
				</div>
			</div>
			<!-- End 其他更多 -->
			<?php } ?>
		</div>
	</div>
<?php
include 'inc/footer.php';
?>