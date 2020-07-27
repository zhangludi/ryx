<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
html{ overflow-y: scroll; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
img{ border: 0; }
.error{ padding: 24px 48px;}
.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px;-webkit-transform: rotate(90deg);width: 100px; margin-left: 45%;margin-top: 10%;}
.error_title { font-size: 32px; line-height: 48px; margin-left: 43%;}
.error_desc {line-height: 48px; margin-left: 33%;}
.error_info {line-height: 48px; margin-left: 43.5%;cursor:pointer;}
.error .content{ padding-top: 10px}
.error .info{ margin-bottom: 12px; }
.error .info .title{ margin-bottom: 3px; }
.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
.error .info .text{ line-height: 24px; }
.copyright{ padding: 12px 48px; color: #999; }
.copyright a{ color: #000; text-decoration: none; }
</style>
</head>
<body>
<div class="error">
	<p class="face">: )</p>
	<h1 class="error_title">功能错误</h1>
	<h5 class="error_desc">功能可能存在问题,请联系开发人员进行修改！非常抱歉对你造成的不便！</h5>
	<h5 class="error_info">点击显示错误信息>></h5>
	<div class="content" style="display: none;">
		<h1><?php echo strip_tags($e['message']);?></h1>
		<?php if(isset($e['file'])) {?>
			<div class="info">
				<div class="title">
					<h3>错误位置</h3>
				</div>
				<div class="text">
					<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
				</div>
			</div>
		<?php }?>
		<?php if(isset($e['trace'])) {?>
			<div class="info">
				<div class="title">
					<h3>TRACE</h3>
				</div>
				<div class="text">
					<p><?php echo nl2br($e['trace']);?></p>
				</div>
			</div>
		<?php }?>
	</div>
</div>
<div class="copyright">
</div>
</body>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
	$(".error_info").click(function(){
		var content_show_status = $(".error").find('.content').css('display');
		if(content_show_status == 'none'){
			$(this).html('点击隐藏错误信息>>');
			$(".error").find('.content').show();
		} else {
			$(this).html('点击显示错误信息>>');
			$(".error").find('.content').hide();
		}
	});
</script>
</html>