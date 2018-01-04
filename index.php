<?php 

include_once('common.php');

if($_SGLOBAL['ismobile']){
	Obheader('wap/index.php');	
}

$imginfo = array();
$query = $_SGLOBAL['db']->query("SELECT src,url,title FROM ".tname('index_switch')." WHERE 1 ORDER BY sort DESC");
while($value = $_SGLOBAL['db']->fetch_array($query)){
  $imginfo[] = $value;
}
//print_r($imginfo);exit;
$index_id = 'id="p-index"';
$title="首页"; $index_nav="1"; include 'inc/header.php'; 

?><div class="feature">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<!-- Start Slides -->
				<?php foreach($imginfo as $val){?>
				<div class="swiper-slide">
					<div class="pic" title="<?=$val['title']?>" style="background-image: url('<?=$val['src']?>');"></div>
					<?php if($val['url']){ ?>
					<a class="btn-detail" href="<?=$val['url']?>"><i></i><span>了解更多</span></a>
					<?php } ?>
				</div>
				<?php } ?>
				<!-- End Slides -->
			</div>
			<div class="swiper-pagination"></div>
		</div>
	</div>
<?php
include 'inc/footer.php';
?>