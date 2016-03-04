<!doctype html>
<html <?php if($index_id){echo $index_id;}?> >
<head>
	<meta charset="utf-8">
	<title><?=$title?>-Vquartz</title>
	<meta name="keywords" content="<?=$title?>">
	<meta name="description" content="<?=$title?>">
	<meta name="applicable-device" content="pc">
    <script src="js/lib/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="js/lib/html5shiv.js"></script>
    <![endif]-->
	<link rel="stylesheet" type="text/css" href="css/com.css">
	<?php
	if($index_nav==1){
	?>
	<link rel="stylesheet" type="text/css" href="css/swiper2.css">
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<?php
  }else{
  ?>
  <link rel="stylesheet" type="text/css" href="css/swiper.min.css">
  <link rel="stylesheet" type="text/css" href="css/cont.css">
  <?php
  }
	?>
</head>
<body>
	<header>
		<div class="logo">Vquartz</div>
		<!-- Start 导航 -->
		<div class="language">
			<a class="active" href="javascript:;">CN</a>
			<!-- <a href="javascript:;">EN</a> -->
		</div>
		<nav class="active-<?=$index_nav?>">
			<ul>
	<li class="nav-1"><a href="index.php">首　页</a></li>
	<li class="nav-2"><a href="product.php">产　品</a></li>
	<li class="nav-3"><a href="album.php">图　册</a></li>
	<li class="nav-4"><a href="about.php">关　于</a></li>
	<li class="nav-5"><a href="contact.php">联　系</a></li>
</ul>
		</nav>
		<!-- End 导航 -->
	</header>