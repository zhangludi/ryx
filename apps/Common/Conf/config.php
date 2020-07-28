<?php
return array(
	'LOAD_EXT_CONFIG' => 'config_com,config_database,config_pay',
    'URL_CASE_INSENSITIVE' => true,
    //'URL_MODEL' => '1', // URL模式,
    'URL_MODEL' => '1', // URL模式,
    //'DEFAULT_MODULE'        =>  'System',  // 默认模块 */
    'DEFAULT_MODULE'        =>  'Index',  // 默认模块 */
	'DEFAULT_MODULE'        =>  'Index',  // 默认模块 */
	//'DEFAULT_ACTION'   =>  'index', 
// 	'DEFAULT_MODULE' => 'Index_gx', // 默认模块
// 'DEFAULT_CONTROLLER' => 'Cms', // 默认控制器名称
// 'DEFAULT_ACTION' => 'cms_article_index', // 默认操作名称
	'LOAD_EXT_FILE' => 'function_new,function_index,function_oa,function_ccp,function_cust,function_life,function_cms,function_com,function_fa,function_rc,function_sys,function_wx,function_per,function_att,function_edu,function_pam,function_supervise,function_saas,function_hr', //自定义公共function文件 */
	
	
	'AUTOLOAD_NAMESPACE'=>array(
		'Addon'=> APP_PATH.'Addon',
	),
      
	'COOKIE_EXPIRE' => time()+3600*24*30, //cookie的生存时间
	
    //'TMPL_ACTION_ERROR' => '../statics/tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    //'TMPL_ACTION_SUCCESS' => './statics/tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    // 'TMPL_EXCEPTION_FILE' => './data/common/think_exception.tpl',// 异常页面的模板文件
    
    'TMPL_PARSE_STRING' => array(
        '__STATICS__' => __ROOT__ . '/statics',
        '__JS__' => __ROOT__ . '/statics/js',
        '__IMAGE__' => __ROOT__ . '/statics/images',
        '__CSS__' => __ROOT__ . '/statics/css',
        '__UPLOAD__' => __ROOT__ . '/uploads',
        '__UPLOAD_PATH__' => 'uploads/',
    ),
	
    'JUMP_MODULE'        =>  'System',  //自定义验证跳转模块
	'JUMP_djindex'		=>  'apps_lydj/Index',  //自定义验证跳转模块
	'TMPL_ACTION_SUCCESS'=>'Public:dispatch_jump',
	'TMPL_ACTION_ERROR'=>'Public:dispatch_jump',
    //'DEFAULT_FILTER'    => 'strip_tags,htmlspecialchars,stripslashes',//strip_tags,
    //'REQUEST_VARS_FILTER'=>true,
	
	'dj_key' =>'101',

)
;