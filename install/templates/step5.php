<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo $Title; ?> - <?php echo $Powered; ?></title>
<link rel="stylesheet" href="./css/install.css?v=9.0" />
<script src="js/jquery.js"></script>
<?php 
$uri = $_SERVER['REQUEST_URI'];
$root = substr($uri, 0,strpos($uri, "install"));
$admin = $root."../index.php/Admin/admin/";
?>
</head>
<body>
<div class="wrap">
  <?php require './templates/header.php';?>
  <section class="section">
    <div class="">
      <div class="success_tip cc"> <a href="<?php echo $admin;?>" class="f16 b">安装完成，进入后台管理</a>
		<p><!--为了您站点的安全，安装完成后即可将网站根目录下的“Install”文件夹删除，或者/install/目录下创建install.lock文件防止重复安装。--><p>
      </div>
	        <div class="bottom tac"> 
	        <a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/System/Index/index.html" class="btn btn_submit J_install_btn" target="_blank">智慧党建后台管理</a>
	        <a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/Index/Index/index.html" class="btn btn_submit J_install_btn" target="_blank">智慧党建门户网站</a>		
      </div>
      <div class=""> </div>
    </div>
  </section>
</div>
<?php require './templates/footer.php';?>
<script>
$(function(){
	$.ajax({
	type: "POST",
	url: "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push",
	data: {domain:'<?php echo $host;?>',last_domain:'<?php echo $host?>',key_num:'<?php echo $curent_version;?>',install_time:'<?php echo $time;?>',serial_number:'<?php echo $mt_rand_str;?>'},
	dataType: 'json',
	success: function(){}
	});
});
</script>
</body>
</html>