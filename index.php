<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

define('ENTER_ACTION', 'index');//自定义常量：config_enter.php中用于判断登录口
define('LOGIN_ACTION', 'login');//登录口
define('INDEX_ACTION', 'index');//首页地址
define('MAIN_ACTION', 'main');//主页地址
define('GROUP_CODE', 'communist');//菜单分组
define('APP_CODE', 'APP_SPIPAMV3');//app编码
define('TV_CODE', 'TV_SPIPAMV3');//TV编码
define('WX_CODE', 'WX_SPIPAMV3');//微信编码
define('PORTAL_CODE', 'PORTAL_SPIPAMV3');//门户编码
define('BIGDATA_CODE', 'BIGDATA_SPIPAMV3');//大数据编码
define('ALLINONE_CODE', 'ALLINONE_SPIPAMV3');//自助服务终端编码

//党员状态分组
define ( 'COMMUNIST_STATUS_DEVELOP', '10,11,13,15,17,18,21' ); //发展党员状态  带入党申请审核
define ( 'COMMUNIST_STATUS_DEVELOPMENT', '11,13,15,17,18,21' ); //发展党员状态
define ( 'COMMUNIST_STATUS_COURSE', '11,12,13,14,15,16,17,18,19,20' ); //发展党员生活历程
define ( 'COMMUNIST_STATUS_OFFICIAL', '21,22' ); // 正式党员状态
define ( 'COMMUNIST_STATUS_HISTORY', '31,32,33,34' ); //历史党员状态
define ( 'COMMUNIST_STATUS_RETIRE', '32,33,34' );  //死亡，失联，清退状态

define ( 'COMMUNIST_STATUS_OFFRETIRE', '21,32,33,34' );  //死亡，失联，清退状态
//消息提醒类型分组
define ( 'ALERTMSG_INSTANT', '12,13,15,21,22,31,32,43,44,51,52' ); //即时提醒，阅读后消失
define ( 'ALERTMSG_TIMELY', '11,14,41,42' ); //按时提醒，过期后消失

//define('LIMIT_NUM', true);

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))die('require PHP > 5.3.0 !');
//当前目录路径
define('SITE_PATH', getcwd() . '/');
//项目路径
define('APP_DEBUG', true);
//当前文件目录
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
// 定义应用目录
define('APP_PATH', SITE_PATH . 'apps/');
// 应用公共目录
define('COMMON_PATH', APP_PATH . 'Common/');
//应用运行缓存目录
define("RUNTIME_PATH", SITE_PATH . "Runtime/");
//模板存放路径
define('UPLOAD_PATH', APP_PATH . 'uploads/');
//模板存放路径
define('INCLUDE_PATH', SITE_PATH . 'include/');
//公共文件路径
define('APP_PUBLIC_PATH',SITE_PATH . "statics/");
//服务器地址
define('BASE_HOST','localhost');
//账户
define('BASE_USER','root');
//密码
define('BASE_PWD','root');
//判断ERP是否已经安装
if (!file_exists('./install/install.lock')) {
    header("Location: ./install/index.php");die;
}
// 引入ThinkPHP入口文件
require SITE_PATH . 'include/spcore/spcore.php';