<?php
echo '<div class="bdrcontent">
	<div class="title"><h3>操作消息</h3></div>
	<p>';
if($url_forward){
	echo '<a href="'.$url_forward.'">'.$message.'</a>';
}else{
	echo $message;
}
echo '</p>
</div>
<div class="footactions">
	&nbsp; ';
if($url_forward){
echo '<a href="'.$url_forward.'">确定</a> &nbsp; ';
}
echo '<a href="javascript:history.back(-1)">返回上一页</a> &nbsp; <a href="index.php">管理首页</a>
</div>';
?>
