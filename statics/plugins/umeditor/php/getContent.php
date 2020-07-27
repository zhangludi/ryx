<script type="text/javascript" src="../third-communist/jquery.min.js"></script>
<script type="text/javascript" src="../third-communist/mathquill/mathquill.min.js"></script>
<link rel="stylesheet" type="text/css" href="../third-communist/mathquill/mathquill.css"/>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<?php

    //获取数据
    error_reporting(E_ERROR|E_WARNING);
    $content =  htmlspecialchars($_POST['myEditor']);

    //存入数据库或者其他操作

    //显示
    echo  "<div class='content'>".htmlspecialchars_decode($content)."</div>";
