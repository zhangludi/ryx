<?php
return array(


    /*信鸽配置*/
    'xinge_config' => array(
        'accessid' =>'2100208842',
        'secretkey' => 'c80a8f8f3cc2765f92aedf39c5382aee'
    ),
	
	'Alidayu_config' => array(
		'AlidayuAppKey'    => '23317867',  // app key
		'AlidayuAppSecret' => 'fd370afd2abc56e2d31a9640589e3e31',  // app secret
		'AlidayuApiEnv'    => 1, // api请求地址，1为正式环境，0为沙箱环境
	),
	
	'SMS_config' => array(
		'SMS_ID'    => 'simpro',  // app key
		'SMS_PWD' => 'simpro',  // app secret
		'SMS_IP'    => '60.209.7.12', // api请求地址，1为正式环境，0为沙箱环境
		'SMS_sign'    => '【森普软件】', // api请求地址，1为正式环境，0为沙箱环境
	),
	
	'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
	
)
;