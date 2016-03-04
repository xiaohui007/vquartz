<?php 
include_once('common.php');

$id = GetGP('id');
$info = $_SGLOBAL['db']->get_one("select * from ".tname('active')." where id='".$id."' AND display='1'");
if(empty($info)){
	header("Location:album.php");
	exit;
}
$info['img'] = 'uploads/image/'.$info['id'].'-small.jpg';
$info['big'] = 'uploads/image/'.$info['id'].'-big.jpg';

//查看该相册的产品
if($info['pids']){
	$plist = array();
	$query = $_SGLOBAL['db']->query("select id,title,src from ".tname('product')." where id in (".$info['pids'].") AND display='1' ORDER BY no_order desc LIMIT 8");
	while($value = $_SGLOBAL['db']->fetch_array($query)){
		// if(file_exists($value['src'].'.thumb.png')){
		// 	$value['thumb'] = $value['src'].'.thumb.png';
		// }else{
		// 	$value['thumb'] = $value['src'];
		// }
		$value['img'] = 'uploads/product/'.$value['id'].'-small.jpg';

		$plist[$value['id']] = $value;
	}
}

//查看其它该类产品
$list = array();
$query = $_SGLOBAL['db']->query("select id,title,src from ".tname('active')." where id!='".$info['id']."' AND display='1' AND xilie='".$info['xilie']."' ORDER BY no_order desc LIMIT 8");
while($value = $_SGLOBAL['db']->fetch_array($query)){
	// if(file_exists($value['src'].'.thumb.png')){
	// 	$value['thumb'] = $value['src'].'.thumb.png';
	// }else{
	// 	$value['thumb'] = $value['src'];
	// }
	$value['img'] = 'uploads/image/'.$value['id'].'-small.jpg';

	$list[$value['id']] = $value;
}


$index_id = 'id="p-album_detail"';
$title="相册详细-".$info['title']; $index_nav="3"; include 'inc/header.php'; 
?>
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
					<div class="serial"><?=$info['title']?> 产品照</div>
					<?php if($plist){ ?>
					<div class="p_pics">
						<h3>相关产品</h3>
						<div class="swiper-container swiper-related">
							<div class="swiper-wrapper">
								
								<!-- Start 内容数据 -->
								<div class="swiper-slide">
									<?php foreach ($plist as $key => $value) { ?>
									<a href="product_detail.php?id=<?=$value['id']?>">
										<img src="<?=$value['img']?>">
										<span>No.<?=$value['title']?></span>
									</a>
									<?php } ?>
								</div>
								<!-- <div class="swiper-slide">
									<a href="#">
										<img src="img/demo/product2.jpg">
										<span>No.A3109</span>
									</a>
									<a href="#">
										<img src="img/demo/product4.jpg">
										<span>No.A3109</span>
									</a>
								</div> -->
								<!-- End 内容数据 -->

							</div>
						</div>
						<div class="swiper-control">
							<a class="swiper-next"></a>
							<a class="swiper-prev"></a>
						</div>
					</div>
					<?php } ?>
					<!-- Start 打印按钮 -->
					<a class="btn-printer" href="javascript:;">打印</a>
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
						<?php foreach ($list as $key => $value) { ?>
						<div class="swiper-slide">
							<a class="m_graphic" href="album_detail.php?id=<?=$value['id']?>">
								<img src="<?=$value['img']?>" title="<?=$value['title']?>" />
								<i></i>
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