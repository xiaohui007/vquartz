
	<div class="block style1">
		<h2>管理菜单</h2>
		<ul class="folder">
		<?php
		foreach($_TPL['menunames'] as $key => $value){
			if($ac==$key){
				echo '<li class="active">';
			}else{
				echo '<li>';
			}
		?>
		<a href="<?=$key?>.php"><?=$value?></a></li>
		<?php
		}
		?>
		</ul>
	</div>