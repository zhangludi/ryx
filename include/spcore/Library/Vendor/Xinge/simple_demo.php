<?php
require_once ('XingeApp.php');

//Android 版
//给单个设备下发通知
// var_dump(XingeApp::PushTokenAndroid(2100207271, "1ba88e7fbec92384374ebce412ff653c", "title", "content", "token"));
//给单个帐号下发通知
//var_dump(XingeApp::PushAccountAndroid(2100207271, "1ba88e7fbec92384374ebce412ff653c", "标题", "内容", "9999"));
//给所有设备下发通知
//var_dump(XingeApp::PushAllAndroid(2100207271, "1ba88e7fbec92384374ebce412ff653c", "标题", "内容"));
// //给标签选中设备下发通知
// var_dump(XingeApp::PushTagAndroid(2100207271, "1ba88e7fbec92384374ebce412ff653c", "title", "content", "tag"));

// //IOS版
// //开发环境下 给单个设备下发通知
// var_dump(XingeApp::PushTokenIos(10000, "secretKey", "content", "token", XingeApp::IOSENV_DEV));
// //开发环境下 给单个帐号下发通知
// var_dump(XingeApp::PushAccountIos(10000, "secretKey", "content", "account", XingeApp::IOSENV_DEV));
// //开发环境下 给所有设备下发通知
// var_dump(XingeApp::PushAllIos(10000, "secretKey", "content", XingeApp::IOSENV_DEV));
// //开发环境下 给标签选中设备下发通知
// var_dump(XingeApp::PushTagIos(10000, "secretKey", "content", "tag", XingeApp::IOSENV_DEV));
$push = new XingeApp(2100207271, '1ba88e7fbec92384374ebce412ff653c');
$mess = new Message();
$mess->setExpireTime(86400);
$mess->setTitle('标题111');
$mess->setContent('内容34545');
$mess->setType(Message::TYPE_NOTIFICATION);
$ret = $push->CreateMultipush($mess, XingeApp::IOSENV_DEV);
if (!($ret['ret_code'] === 0))
    return $ret;
else
{
    $result=array();
    $accountList1 = array('9999', '9999', '9999');
    array_push($result, $push->PushAccountListMultiple($ret['result']['push_id'], $accountList1));//追加数组
    $accountList2 = array('9999', '9999', '9999');
    array_push($result, $push->PushAccountListMultiple($ret['result']['push_id'], $accountList2));
    return ($result);
}