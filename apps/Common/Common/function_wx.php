<?php

/**
 * @name  getweixinoppenid()
 * @desc  获取微信传参code
 * @return
 * @author 范喆
 * @time   2016-09-20
 */
function getweixinoppenid($href){
    $APPID= wx1bb4a12c1d137979;
    //AppSecret：d989228e1614d86a9189ca0be71da4de
    //$APPID='wxd64bb03f0df43a3c';//公众号的appid
    $REDIRECT_URI=$href;
    //判断扫码过来的时候有没有传过来参数
    if($_GET['id']){
        $REDIRECT_URI.='&pid='.$_GET['id'];
    }
    if($_GET['distributionflg']){
        $REDIRECT_URI.='&distributionflg='.$_GET['distributionflg'];
    }
    $scope='snsapi_userinfo';
    //$scope='snsapi_userinfo';//需要授权
    $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
    header("Location:".$url);
}

/***************************** 删除类方法结束 ************************/


/**
 * @name  checkWeixinOpenid()
 * @desc  验证是否绑定
 * @param 
 * @return 
 * @author 范喆
 * @time   2017-03-16
 */
function checkWeixinOpenid($openid)
{
    $where['openid']=$openid;
    $communist=M("ccp_communist")->where($where)->find();
    $check="";
    if (!$communist){
        $check="<script>location.href='".U('wx_binding')."'</script>";
    }
    return $check;
}


?>

