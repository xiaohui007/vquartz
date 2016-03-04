<?php 
//include_once('common.php');
$index_id = 'id="p-album_detail"';
$title="联系我们"; $index_nav="5"; include 'inc/header.php'; 
?>
	<div class="container contbg">
		<div class="inner">
			<div class="contact">
				<article>
					<hgroup>
						<h2>联系我们</h2>
					</hgroup>

					<h3>您好！</h3>
					<p>感谢您来到至美石业，若您有合作意向，请您为我们留言或使用以下方式联系我们，我们将尽快给你回复，并为您提供最真诚的服务，谢谢。</p>
					<ol>
						<li>
							<i class="ico ico-fex"></i>
							0766-8226813
						</li>
						<li>
							<i class="ico ico-phone"></i>
							+86 138-2688-4210（总经理）<br>
							0766-8226812（采购部赖小姐）<br>
							0766-8223803（业务部）<br>
						</li>
						<li>
							<i class="ico ico-email"></i>
							总经理邮箱：perfectstone@vip.163.com<br>
							公司邮箱：vquartz@vquartz.com<br>
							林小姐邮箱：collin@vquartz.com<br>
							吴小姐邮箱：v3@vquartz.com<br>
						</li>
						<li>
							<i class="ico ico-address"></i>
							广东省云浮市初城工业区北二路
						</li>
					</ol>
				</article>

				<form class="message" name="useinfo" id="useinfo">
					<!-- Start 留言输入 -->
					<div class="clrB">
						<label for="name">姓名:</label>
						<input id="name" class="name" name="name" type="text">
					</div>

					<div class="clrB">
						<label for="phone">电话:</label>
						<input class="phone" name="tele" id="tele" type="text" maxlength="11">
					</div>

					<div class="clrB">
						<label for="info">信息:</label>
						<textarea class="info" id="info" name="info"></textarea>
					</div>

					<input type="button" class="btnSubmit" onclick="save();" value="发送">
				    <!--错误提示-->
					<span class="error" id="error_msg" style="display:none"></span>
					<!--成功提示-->
					<span class="success" id="success_msg" style="display:none">留言成功，请耐心等待客服回复</span>
					<!--失败提示-->
					<span class="fail" id="fail_msg" style="display:none">留言失败，请<a href="javascript:PAGE.fn.reload();">刷新页面</a></span>
					<!-- End 留言输入 -->
					<input type="hidden" name="step" id="step" value="2" />
				</form>
			</div>
		</div>
	</div>
<script type="text/javascript">
function save(){
    $.ajax({
        url:"save.php?inajax=1&t="+Math.random(),
        type:"POST",
        dataType:"json",
        data:$("#useinfo").serialize(),
        beforeSend: function() {
            $('#error_msg').html('正在加载中...').show();
        },
        error: function(request) {
            alert(request.responseText);
        },
        success:function(data){
            if(data.ok=='0'){
                $('#error_msg').html(data.error).show();
            }else if(data.ok=='1'){
                $('#error_msg').html('').hide();
                $('#success_msg').show();
								$("#tele").val('');
								$("#name").val('');
								$("#info").val('');
            }
        }
    }); 
}
</script>
<?php
include 'inc/footer.php';
?>