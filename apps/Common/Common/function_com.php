<?php
/****************阿里云sdk文件调用（不可删除）****************/
ini_set("display_errors", "on");
require_once SITE_PATH. 'include/spcore/Library/Aliyun/vendor/autoload.php';
// require_once dirname(__DIR__) . '/api_sdk/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();
/****************阿里云sdk文件调用（不可删除）****************/
// use Alidayu\AlidayuClient as Client;
// use Alidayu\Request\SmsNumSend;
/***************************** 阿里云短信方法开始 ************************/
/**

/**
 * @name  getAcsClient()
 * @desc  取得AcsClient(阿里云短信sdk配置信息)
 * @param 
 * @author 黄子正
 * @addtime 2017-11-22
 * @updatetime 2017-11-22
 * @return
 *
 */
 function getAcsClient() {
     $acsClient = null;
    //产品名称:云通信流量服务API产品,开发者无需替换
    $product = "Dysmsapi";

    //产品域名,开发者无需替换
    $domain = "dysmsapi.aliyuncs.com";

    // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
    $sys_config=M('sys_config');
    
    $accessKeyId = $sys_config->where("config_code = 'AliYunAccessKeyId '")->getField('config_value'); // AccessKeyId

    $accessKeySecret = $sys_config->where("config_code = 'AliYunAccessKeySecret '")->getField('config_value');; // AccessKeySecret


    // 暂时不支持多Region
    $region = "cn-hangzhou";

    // 服务结点
    $endPointName = "cn-hangzhou";


//     if(static::$acsClient == null) {
        if($acsClient == null) {

        //初始化acsClient,暂不支持region化
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
//         static::$acsClient = new DefaultAcsClient($profile);
        $acsClient = new DefaultAcsClient($profile);
    }
//     return static::$acsClient;
    return $acsClient;
}
 
/**
 * @name  sendAliYunSmsConfig($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null, $smsUpExtendCode = null)
 * @desc  阿里云短信发送参数配置
 * @param string $signName <p>
 * 必填, 短信签名，应严格"签名名称"填写，参考：<a href="https://dysms.console.aliyun.com/dysms.htm#/sign">短信签名页</a>
 * </p>
 * @param string $templateCode <p>
 * 必填, 短信模板Code，应严格按"模板CODE"填写, 参考：<a href="https://dysms.console.aliyun.com/dysms.htm#/template">短信模板页</a>
 * (e.g. SMS_0001)
 * </p>
 * @param string $phoneNumbers 必填, 短信接收号码 (e.g. 12345678901)
 * @param array|null $templateParam <p>
 * 选填, 假如模板中存在变量需要替换则为必填项 (e.g. Array("code"=>"12345", "product"=>"阿里通信"))
 * </p>
 * @param string|null $outId [optional] 选填, 发送短信流水号 (e.g. 1234)
 * @param string|null $smsUpExtendCode [optional] 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
 * @return stdClass(对象)
 * @author 黄子正
 * @addtime 2017-11-22
 * @updatetime 2017-11-22
 */
 function sendAliYunSmsConfig($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null, $smsUpExtendCode = null) {
     $acsClient = null;
    // 初始化SendSmsRequest实例用于设置发送短信的参数
    $request = new SendSmsRequest();

    // 必填，设置雉短信接收号码
    $request->setPhoneNumbers($phoneNumbers);

    // 必填，设置签名名称
    $request->setSignName($signName);

    // 必填，设置模板CODE
    $request->setTemplateCode($templateCode);

    // 可选，设置模板参数
    if($templateParam) {
        $request->setTemplateParam(json_encode($templateParam));
    }

    // 可选，设置流水号
    if($outId) {
        $request->setOutId($outId);
    }

    // 选填，上行短信扩展码
    if($smsUpExtendCode) {
        $request->setSmsUpExtendCode($smsUpExtendCode);
    }

    // 发起访问请求
//     $acsResponse = static::getAcsClient()->getAcsResponse($request);
    $acsResponse = getAcsClient()->getAcsResponse($request);

    // 打印请求结果
    // var_dump($acsResponse);

    return $acsResponse;

}

/**
 * @name  queryDetails($phoneNumbers, $sendDate, $pageSize = 10, $currentPage = 1, $bizId=null)
 * @desc  阿里云短信查询
 * @param string $phoneNumbers 必填, 短信接收号码 (e.g. 12345678901)
 * @param string $sendDate 必填，短信发送日期，格式Ymd，支持近30天记录查询 (e.g. 20170710)
 * @param int $pageSize 必填，分页大小
 * @param int $currentPage 必填，当前页码
 * @param string $bizId 选填，短信发送流水号 (e.g. abc123)
 * @return stdClass
 * @author 黄子正
 * @addtime 2017-11-22
 * @updatetime 2017-11-22
 */
 function queryDetails($phoneNumbers, $sendDate, $pageSize = 10, $currentPage = 1, $bizId=null) {
     $acsClient = null;
    // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
    $request = new QuerySendDetailsRequest();

    // 必填，短信接收号码
    $request->setPhoneNumber($phoneNumbers);

    // 选填，短信发送流水号
    $request->setBizId($bizId);

    // 必填，短信发送日期，支持近30天记录查询，格式Ymd
    $request->setSendDate($sendDate);

    // 必填，分页大小
    $request->setPageSize($pageSize);

    // 必填，当前页码
    $request->setCurrentPage($currentPage);

    // 发起访问请求
//     $acsResponse = static::getAcsClient()->getAcsResponse($request);
    $acsResponse = getAcsClient()->getAcsResponse($request);

    // 打印请求结果
    // var_dump($acsResponse);

    return $acsResponse;
}
/**
 * @name  aliyun_send($msg_receiver,$template_code,$template_param)
 * @desc  阿里云短信发送（所有参数只能为数组或字符串格式）
 * @param array $msg_receiver        （短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式）
 * @param string $template_type      （模板类型编号。TemplateCode1为修改密码验证；；）
 * @param array $template_param      （模板参数数组，详情见config表对应memo）
 * @author 黄子正
 * @addtime 2017-11-22
 * @updatetime 2017-11-22
 * @return 成功返回OK，失败返回错误信息
 * 
 */
function aliyun_send($msg_receiver,$template_type,$template_param){
    $sys_config=M('sys_config');
    $template_code=$sys_config->where("config_code = '$template_type'")->getField('config_value');//获取模板编号
    $SignName=$sys_config->where("config_code = 'SignName'")->getField('config_value');//获取短信签名
    $reuslt=sendAliYunSmsConfig($SignName,$template_code,$msg_receiver,$template_param,'123');
    return $reuslt->Message;
//     $a=sendAliYunSmsConfig("森普软件", 
//         "SMS_78850049", 
//         "15966663899", 
//         Array(  // 短信模板中字段的值
//             "code"=>"123456"
//         ),
//         "123");
}
/***************************** 阿里云短信方法结束 ************************/
/***************************** 消息中心方法开始 ************************/

/**
 * @name  sendMultiMsg()
 * @desc  多渠道消息提醒
 * @param string $type_code        config_code值
 * @param string $msg_sender       发送人
 * @param array $msg_receiver      接收人
 * @param string $msg_param        参数json
 * 		{staff_no:员工编号,cust_no:客户编号,supplier_no:供应商编号,recruit_no:招聘者编号,
 *		title:推送标题,time:推送时间,subject:邮件主题,alert_type:类型,$alert_man:提醒人,
 *		$alert_param:参数,$alert_title:事件名称,$alert_content:内容,$alert_time:提醒时间,
 *		$alert_cycle:提醒周期,$add_staff:添加人}
 * @author 王彬
 * @addtime 2016-08-09
 * @updatetime 2016-08-09
 * @return 
 *
  */
function sendMultiMsg($type_code,$msg_sender,$msg_receiver,$msg_param)
{
	$com_msg_log = M('com_msg_log');
	$com_msg_template = M('com_msg_template');
	$com_msg_type = M('com_msg_type');
	$hr_staff = M('hr_staff');
	$crm_cust = M('crm_cust');
	$crm_cust_contact = M('crm_cust_contact');
	$crm_supplier = M('crm_supplier');
	$crm_supplier_contact = M('crm_supplier_contact');
	$hr_recruit = M('hr_recruit');
	
	$dt = getPersonnelInfo($type_code,$msg_receiver);
	
	if(stripos($type_code,'other') > 0){
		$msg_template = null;
	}else{
		
		$type_data = $com_msg_type->where("type_code = '$type_code'")->field('template_id')->find();
		$msg_template = $type_data['template_id'];
	}
	
	$data['type_code'] = $type_code;
	$data['msg_sender'] = $msg_sender;
	$data['msg_receivers'] = $msg_receiver;
	$data['msg_template'] = $msg_template;
	$data['msg_param'] = $msg_param;
	$data['is_send'] = 0;
	$data['is_sms'] = 0;
	$data['add_staff'] = $msg_sender;
	$data['add_time'] = date('Y-m-d H:i:s');
	$data['update_time'] = date('Y-m-d H:i:s');
	$msglog_id = $com_msg_log->add($data);//向log表插入数据-状态为失败状态
	
	if(stripos($type_code,'ther_') > 0){
		$param_arr = (Array)json_decode($msg_param);
		$sms_uid = getConfig('sms_uid');
		$sms_pwd = getConfig('sms_pwd');
		$sms_ip = getConfig('sms_ip');
		$sms_sign = getConfig('sms_sign');
		$sms_content = str_replace('${staff_name}',$dt['staff_name'],$param_arr['sms_content']);
		$sms_content = str_replace('${cust_name}',$dt['cust_name'],$sms_content);
		$sms_content = str_replace('${supplier_name}',$dt['supplier_name'],$sms_content);
		$sms_content = str_replace('${recruit_name}',$dt['recruit_name'],$sms_content);
		$sms_content = str_replace('${staff_post}',$dt['staff_post'],$sms_content);
		$sms_content = str_replace('${cust_post}',$dt['cust_post'],$sms_content);
		$sms_content = str_replace('${supplier_post}',$dt['supplier_post'],$sms_content);
		$sms_content = str_replace('${recruit_post}',$dt['recruit_post'],$sms_content);
		if($sms_uid != "" && $sms_pwd != "" && $sms_ip != "" && $sms_sign != ""){//判断是否设置企信通帐号
			$sendSms_data = sendSms($dt['sms_phones'],$sms_content);
			if($sendSms_data){//如果发送成功修改log表状态-改为成功状态
				$data['is_send'] = 1;
				$data['msg_content'] = $sms_content;
				$data['is_sms'] = 1;//是否发送了短息0：否 1：是（用于标记）
				$msg_log_data = $com_msg_log->where("msglog_id = '$msglog_id'")->save($data);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
	    
		$template_data = $com_msg_template->where("template_id = '$msg_template'")->find();//查询模版表
		
		$sms_content = str_replace('${staff_name}',$dt['staff_name'],$template_data['template_content']);
		$sms_content = str_replace('${cust_name}',$dt['cust_name'],$sms_content);
		$sms_content = str_replace('${supplier_name}',$dt['supplier_name'],$sms_content);
		$sms_content = str_replace('${recruit_name}',$dt['recruit_name'],$sms_content);
		$sms_content = str_replace('${staff_post}',$dt['staff_post'],$sms_content);
		$sms_content = str_replace('${cust_post}',$dt['cust_post'],$sms_content);
		$sms_content = str_replace('${supplier_post}',$dt['supplier_post'],$sms_content);
		$sms_content = str_replace('${recruit_post}',$dt['recruit_post'],$sms_content);
		$template_param = strToArr($template_param);
		$param_arr = (Array)json_decode($msg_param);
		foreach($template_param as $param){
			if(stripos($param,'staff')  > 0){
				$other_dt = getPersonnelInfo($param,$param_arr['staff_no']);
				$sms_content = str_replace('${staff_name}',$other_dt['staff_name'],$sms_content);
				$sms_content = str_replace('${staff_post}',$other_dt['staff_post'],$sms_content);
			}
			if(stripos($param,'cust') > 0){
				$other_dt = getPersonnelInfo($param,$param_arr['cust_no']);
				$sms_content = str_replace('${cust_name}',$other_dt['cust_name'],$sms_content);
				$sms_content = str_replace('${cust_post}',$other_dt['cust_post'],$sms_content);
			}
			if(stripos($param,'supplier') > 0){
				$other_dt = getPersonnelInfo($param,$param_arr['supplier_no']);
				$sms_content = str_replace('${supplier_name}',$other_dt['supplier_name'],$sms_content);
				$sms_content = str_replace('${supplier_post}',$other_dt['supplier_post'],$sms_content);
			}
			if(stripos($param,'recruit') > 0){
				$other_dt = getPersonnelInfo($param,$param_arr['recruit_no']);
				$sms_content = str_replace('${recruit_name}',$other_dt['recruit_name'],$sms_content);
				$sms_content = str_replace('${recruit_post}',$other_dt['recruit_post'],$sms_content);
			}
		}
	}
	
	if($template_data['is_alertmsg'] == '1'){//判断是否发送消息提醒
	 
		$param_arr = (Array)json_decode($msg_param);
		$sendAlertMsg_data = sendAlertMsg($param_arr['alert_type'],$param_arr['alert_man'],$param_arr['alert_param'],$param_arr['alert_title'],$param_arr['alert_content'],$param_arr['alert_time'],$param_arr['alert_nexttime'],$param_arr['alert_cycle']);
		if($sendAlertMsg_data){//如果发送成功修改log表状态-改为成功状态
			$data['is_send'] = 1;
			$msg_log_data = $com_msg_log->where("msglog_id = '$msglog_id'")->save($data);
		}
	}
	if($template_data['is_sms'] == '1'){//判断是否发送短信
		$config = getConfig('sms_sendway');
		if($config == '1'){//发送企信通
			$sms_uid = getConfig('sms_uid');
			$sms_pwd = getConfig('sms_pwd');
			$sms_ip = getConfig('sms_ip');
			$sms_sign = getConfig('sms_sign');
			if($sms_uid != "" && $sms_pwd != "" && $sms_ip != "" && $sms_sign != ""){//判断是否设置企信通帐号
				$sendSms_data = sendSms($dt['sms_phones'],$sms_content);
				if($sendSms_data){//如果发送成功修改log表状态-改为成功状态
					$data['msg_content'] =$sms_content;
					$data['is_send'] = 1;
					$msg_log_data = $com_msg_log->where("msglog_id = '$msglog_id'")->save($data);
				}
			}
		}else if($config == '0'){//发送阿里大于
			$AlidayuAppKey = getConfig('AlidayuAppKey');
			$AlidayuAppSecret = getConfig('AlidayuAppSecret');
			$AlidayuApiEnv = getConfig('AlidayuApiEnv');
			$AlidayuSignName = getConfig('AlidayuSignName');
			if($AlidayuAppKey != "" && $AlidayuAppSecret != "" && $AlidayuApiEnv != "" && $AlidayuSignName != ""){//判断是否设置阿里大于帐号
				$template_aliparam = $template_data['template_aliparam'];
				$template_aliparam = str_ireplace('"',"",$template_aliparam);
				$template_aliparam = str_ireplace('{',"",$template_aliparam);
				$template_aliparam = str_ireplace('}',"",$template_aliparam);
				$template_aliparam = strToArr($template_aliparam);
				$alidt = array();
				foreach($template_aliparam as $param){
					$par = strToArr($param,':');
					foreach($par as $val){
						if($val == 'name'){
							$alidt['name'] = $dt['name'];
						}elseif($val == 'code'){
							$alidt['code'] = getVcode(1,3);
						}elseif($val == 'product'){
							$alidt['code'] = getConfig('AlidayuSignName');
						}
					}
				}
				$sendAliSMS_data = sendAliSMS($dt['sms_phones'],$template_data['alidayu_code'],$alidt);
				if($sendAliSMS_data['alibaba_aliqin_fc_sms_num_send_response']['result']['success']){//如果发送成功修改log表状态-改为成功状态
					$data['is_send'] = 1;
					$msg_log_data = $com_msg_log->where("msglog_id = '$msglog_id'")->save($data);
				}
			}
		}
	}
	if($template_data['is_email'] == '1'){//判断是否发送邮件
		$mail_host = getConfig('mail_host');
		$mail_from = getConfig('mail_from');
		$mail_fromname = getConfig('mail_fromname');
		$mail_password = getConfig('mail_password');
		$mail_username = getConfig('mail_username');
		if($mail_host != "" && $mail_from != "" && $mail_fromname != "" && $mail_password != "" && $mail_username != ""){//判断是否设置邮件帐号
			$email_arr = (Array)json_decode($msg_param);
			$subject = $email_arr['subject'];//主题
			$attachment = $email_arr['attachment'];//附件
			$sendEMail_data = sendEMail($dt['mailto'],$dt['name'],$subject,$sms_content,$attachment);
			if($sendEMail_data){//如果发送成功修改log表状态-改为成功状态
				$data['is_send'] = 1;
				$msg_log_data = $com_msg_log->where("msglog_id = '$msglog_id'")->save($data);
			}
		}
	}
	if($template_data['is_pushmsg'] == '1'){//判断是否信鸽推送消息
		$accessid = getConfig('accessid');
		$secretkey = getConfig('secretkey');
		if($accessid != "" && $secretkey != ""){//判断是否设置信鸽帐号
			$pushmsg_arr = (Array)json_decode($msg_param);
			$title = $pushmsg_arr['title'];//标题
			$sms_content = $pushmsg_arr['alert_content'];//内容
			$time = $pushmsg_arr['time'];
			$sendPushMsg_data = sendPushMsg($title,$sms_content,$msg_receiver,$time);
			if($sendPushMsg_data){//如果发送成功修改log表状态-改为成功状态
				$data['is_send'] = 1;
				$msg_log_data = $com_msg_log->where("msglog_id = '$msglog_id'")->save($data);
			}
		}
	}
}


/***************************** 消息中心方法结束 ************************/

/***************************** 获取人员信息方法开始 ************************/

/**
 * @name  getPersonnelInfo()
 * @desc  获取消息接受人员信息
 * @param string $type_code        config_code值
 * @param array $msg_receiver      接收人
 * @author 王彬
 * @addtime 2016-08-25
 * @updatetime 
 * @return 
 *
  */
function getPersonnelInfo($type_code,$msg_receiver){
	$hr_staff = M('hr_staff');
	$crm_cust_contact = M('crm_cust_contact');
	$crm_supplier_contact = M('crm_supplier_contact');
	$hr_recruit = M('hr_recruit');
	$dt = array();
	if(stripos($type_code,'staff')  > 0){
		$msg_type = 'staff';
	}
	if(stripos($type_code,'cust') > 0){
		$msg_type = 'cust';
	}
	if(stripos($type_code,'supplier') > 0){
		$msg_type = 'supplier';
	}
	if(stripos($type_code,'recruit') >= 0){
		$msg_type = 'recruit';
	}
	switch($msg_type){//判断接收人类型-查询接受人数据信息
		case 'staff'://员工
			if(stripos($msg_receiver,',') > 0){
				$msg_receiver = strToArr($msg_receiver);
				$sms_phones = "";
				foreach($msg_receiver as $receiver){
					$staff_data = $hr_staff->where("staff_no = '$receiver'")->find();
					$sms_phones .= $staff_data['staff_tel'].",";//电话
				}
				$sms_phones = rtrim($sms_phones,',');
			}else{
				$staff_data = $hr_staff->where("staff_no = '$msg_receiver'")->find();
				$dt['mailto'] = $staff_data['staff_email'];//邮箱
				$dt['staff_name'] = $staff_data['staff_name'];//姓名
				$dt['staff_post'] = getPost($cust_data['staff_post_no']);//职位
				$dt['sms_phones'] = $staff_data['staff_tel'];//电话
			}
			break;
		case 'cust'://客户
			if(stripos($msg_receiver,',') > 0){
				$msg_receiver = strToArr($msg_receiver);
				$sms_phones = "";
				foreach($msg_receiver as $receiver){
					$cust_data = $crm_cust_contact->where("custcontact_no = '$receiver'")->find();
					$sms_phones .= $cust_data['staff_tel'].",";//电话
				}
				$sms_phones = rtrim($sms_phones,',');
			}else{
				$cust_data = $crm_cust_contact->where("custcontact_no = '$msg_receiver'")->find();
				$dt['mailto'] = $cust_data['custcontact_email'];//邮箱
				$dt['cust_name'] = $cust_data['custcontact_name'];//姓名
				$dt['cust_post'] = $cust_data['custcontact_post'];//职位
				$dt['sms_phones'] = $cust_data['custman_tel'];//电话
			}
			break;
		case 'supplier'://供应商
			if(stripos($msg_receiver,',') > 0){
				$msg_receiver = strToArr($msg_receiver);
				$sms_phones = "";
				foreach($msg_receiver as $receiver){
					$supplier_data = $crm_supplier_contact->where("supcontact_no = '$receiver'")->find();
					$sms_phones .= $supplier_data['staff_tel'].",";//电话
				}
				$sms_phones = rtrim($sms_phones,',');
			}else{
				$supplier_data = $crm_supplier_contact->where("supcontact_no = '$msg_receiver'")->find();
				$dt['mailto'] = $supplier_data['supcontact_email'];//邮箱
				$dt['supplier_name'] = $supplier_data['supcontact_name'];//姓名
				$dt['supplier_post'] = $supplier_data['supcontact_post'];//职位
				$dt['sms_phones'] = $supplier_data['supcontact_mobile'];//电话
			}
			break;
		case 'recruit'://招聘
			if(stripos($msg_receiver,',') > 0){
				$msg_receiver = strToArr($msg_receiver);
				$sms_phones = "";
				foreach($msg_receiver as $receiver){
					$recruit_data = $hr_recruit->where("recruit_no = '$receiver'")->find();
					$sms_phones .= $recruit_data['staff_tel'].",";//电话
				}
				$sms_phones = rtrim($sms_phones,',');
			}else{
				$recruit_data = $hr_recruit->where("recruit_no = '$msg_receiver'")->find();
				$dt['mailto'] = $recruit_data['recruit_email'];//邮箱
				$dt['recruit_name'] = $recruit_data['recruit_name'];//姓名
				$dt['recruit_post'] = $recruit_data['recruit_intentionjobs'];//职位
				$dt['sms_phones'] = $recruit_data['recruit_mobile'];//电话
			}
			break;
	}
	return $dt;
}

/***************************** 获取人员信息方法结束 ************************/

/***************************** 消息提醒方法开始 ************************/
/**
 * @name  sendAlertMsg()
 * @desc  添加消息提醒到队列
 * @param  $alert_type   提醒类型（事物的提醒）
 *         $alert_man        提醒人
 *         $alert_param        跳转地址
 *         $alert_title     提醒标题
 *         $alert_content       提醒内容
 *         $alert_time       提醒时间
 *         $alert_nexttime       下次提醒时间
 *         $alert_cycle       提醒周期(one,hour,day,week,month,year)
 * @return true/false
 * @author 王彬
 * @addtime   2016-07-18
 */
function sendAlertMsg($alert_type,$alert_man,$alert_param,$alert_title,$alert_content,$alert_time,$alert_nexttime,$alert_cycle,$add_staff){
	$db_alertmsg=M("bd_alertmsg");
	$data['alert_type']=$alert_type;
	$data['alert_man']=$alert_man;
	$data['alert_param']=$alert_param;
	$data['alert_title']=$alert_title;
	$data['alert_content']=$alert_content;
	$data['alert_time']=$alert_time;
	$data['alert_nexttime']=$alert_nexttime;
	$data['alert_cycle']=$alert_cycle;
	$data['add_time']=date("Y-m-d H:i:s");
	$data['update_time']=date("Y-m-d H:i:s");
	if(empty($add_staff))
	{
		$data['add_staff']=session('staff_no');
		
	}else
	{
		$data['add_staff']=$add_staff;
	}
	$data['status']=0;
	
	$msg_add=$db_alertmsg->add($data);
	
	if($msg_add){
		return true;
	}else{
		return false;
	}
}



/**
 * @name  setAlertNexttime()
 * @desc  修改下次提醒时间
 * @param $alert_id
 * @return true/false
 * @author 王彬
 * @memo 
 * @version 版本 V1.0.0
 * @updatetime   
 * @addtime   2016-07-18
 */
function setAlertNexttime($alert_id)
{
	$bd_alertmsg = M('bd_alertmsg');
	if(!empty($alert_id)){
		$alertmsg_data = $bd_alertmsg->where("alert_id = '$alert_id'")->find();
		$alert_time = $alertmsg_data['alert_time'];//提醒时间
		$alert_cycle = $alertmsg_data['alert_cycle'];//提醒周期
		if($alert_cycle == 'hour' || $alert_cycle == 'day' || $alert_cycle == 'week' || $alert_cycle == 'month' || $alert_cycle == 'year'){
			$alert_nexttime = date("Y-m-d 00:00:00",strtotime("$alert_time +1 $alert_cycle"));
			$data['alert_nexttime'] = $alert_nexttime;
			$alertmsg_data = $bd_alertmsg->where("alert_id = '$alert_id'")->save($data);
			if($alertmsg_data){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}
/**
 * @name  setAlertStatus()
 * @desc  修改提醒状态 
 * @param $alert_id $status(状态 0：未读 1：已读 --不传该参数默认修改为已读)
 * @return true/false
 * @author 王彬
 * @memo 
 * @version 版本 V1.0.0
 * @updatetime   
 * @addtime   2016-07-18
 */
function setAlertStatus($alert_id,$status = 1)
{
	$bd_alertmsg = M('bd_alertmsg');
	if(!empty($alert_id)){
		if($status != 1){
			$status = 0;
		}
		$data['status'] = $status;
		$alertmsg_data = $bd_alertmsg->where("alert_id = '$alert_id'")->save($data);
		/* 针对公文收发增加此代码 */
		$msg_row=$bd_alertmsg->where("alert_id = '$alert_id'")->field('alert_param,alert_type')->find();
		if($msg_row['alert_type']==16){
		    $param=$msg_row['alert_param'];
		    $alertmsg_data = $bd_alertmsg->where("alert_param = '$param'")->save($data);
		}
		/* 结束 */
		
		if($alertmsg_data){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

/***************************** 消息提醒方法结束 ************************/

/***************************** 站内信方法开始 ************************/
/**
 * @name  sendImail()
 * @desc  添加邮件到队列
 * @param $imail_receiver  接收人编号
 *        $content 内容
 *        $title  标题
 *        $imail_attach  附件
 *        $people_type  员工类型
 *        $type 平台发送1、app发送2
 *        $staff_no 发送人
 * @return true/false
 * @author 靳邦龙
 * @time   2016-04-28
 */
function sendImail($imail_receiver,$content,$title,$imail_attach,$people_type,$type,$staff_no){
	if(!empty($imail_receiver)){
		$db_imail_outbox = M("com_imail_outbox");
		$db_imail_inbox = M("com_imail_inbox");

		$post['add_time'] = date("Y-m-d H:i:s");

		if(empty($staff_no)){
			$staff_no= session('staff_no');
		}
		$post['add_staff'] = $staff_no;
		//古城修改为发送人
		$post['imail_sender'] = $staff_no;
		$post['imail_receivers'] = $imail_receiver;

		$post['imail_content'] = $content;
		$post['imail_title'] = $title;
		$post['imail_attach'] = $imail_attach;
		$post['status'] = 1;
		if(empty($people_type)){
			$post['people_type'] = 0;
		}else{
			$post['people_type'] = $people_type;
		}

		$outbox_id = $db_imail_outbox->add($post);
		if(!empty($outbox_id)){
			$receive = explode(",", $imail_sender);
			foreach($receive as &$r){
				$post['imail_receiver'] = $r;
				$post['imail_contentid'] = $outbox_id;

				$inbox_data = $db_imail_inbox->add($post);
			}
		}
	}
	if($inbox_data){
		return true;
	}else{
		return false;
	}

}
/**
 * @name  getImailList()
 * @desc  获取内部邮件列表
 * @param 员工编号    $staff_no
 *        是否已读      $is_read  0未读    1已读       空   全部
 * @param 信件类型       $type[1公开信      2收件箱   3发件箱]
 * @return 内部邮件列表
 * @author 靳邦龙-王彬
 * @addtime   2016-05-11
 * @updatetime   2016-08-10
 */
function getImailList($staff_no,$type,$is_read,$page=0,$count=10){
	if(!empty($staff_no)){
		$start = ($page-1)*$count;
		if($type==1){//公开信待修改***********************
			$db_outbox = M("com_imail_outbox");
			if($page > 0){
				$outbox_data = $db_outbox->where("status = 1 and is_check = 1")->order('add_time desc')->limit($start,$count)->select();
			}else{
				$outbox_data = $db_outbox->where("status = 1 and is_check = 1")->order('add_time desc')->select();
			}
			return $outbox_data;
		}elseif($type==2){
			$db_imail_inbox = M();
			if($is_read != ''){
				$where="and inbox.is_read = $is_read";
			}else{
				$where='';
			}
			if($page > 0){
				$imail_sql = "select o.imail_id,o.imail_attach,o.imail_sender,o.imail_title,o.add_time,inbox.is_read,inbox.imail_receiver from sp_com_imail_outbox as o,sp_com_imail_inbox as inbox where inbox.imail_contentid = o.imail_id and o.status = 1 and inbox.imail_receiver = $staff_no and inbox.is_del = '0' $where order by add_time desc limit $start, $count";
			}else{
				$imail_sql = "select o.imail_id,o.imail_attach,o.imail_sender,o.imail_title,o.add_time,inbox.is_read,inbox.imail_receiver from sp_com_imail_outbox as o,sp_com_imail_inbox as inbox where inbox.imail_contentid = o.imail_id and o.status = 1 and inbox.imail_receiver = $staff_no and inbox.is_del = '0' $where order by add_time desc";
			}
			$inbox_list = $db_imail_inbox->query($imail_sql);
			return $inbox_list;
		}elseif($type==3){
			$db_imail_outbox = M("com_imail_outbox");
			if($page > 0){
				$outbox_list=$db_imail_outbox->where("status = 1 and imail_sender=$staff_no")->order('add_time desc')->limit($start,$count)->select();
			}else{
				$outbox_list=$db_imail_outbox->where("status = 1 and imail_sender=$staff_no")->order('add_time desc')->select();
			}
			return $outbox_list;
		}
	}

}
/**
 * @name  getImailinfo()
 * @desc  获取内部邮件详情
 * @param 邮件id   $imail_id
 * @return 内部邮件详细内容
 * @author 王桥元
 * @time   2016-05-11
 */
function getImailinfo($imail_id){
	if(!empty($imail_id)&&is_numeric($imail_id)){
		$db_imail = M();
		$imail_sql = "select o.*,inbox.imail_read_time from sp_com_imail_outbox as o,sp_com_imail_inbox as inbox where inbox.imail_contentid = o.imail_id and o.imail_id = $imail_id";
		$imail_info = $db_imail->query($imail_sql);

		//查询发件人姓名
		if(!empty($imail_info[0]['imail_sender'])){
			$imail_info['send_user'] =getStaff($imail_info[0]['imail_sender'], "staff_name");
		}
		//查询收件人姓名
		if(!empty($imail_info[0]['imail_receivers'])){
			$imail_info['receiver_user'] = getStaff($imail_info[0]['imail_receivers'], "staff_name");
		}
		return $imail_info;
	}
}


/***************************** 短信方法开始 ***********************


/***************************** 短信方法结束 ************************/
/***************************** 阿里大鱼大鱼方法开始 ************************/

/**
 * @name  sendAliSMS()
 * @desc  阿里大鱼短信群发接口
 * @param string $sms_phones         电话（多电话）
 * @param string $sms_template       短信模板ID（SMS_585014）
 * @param array $sms_param           参数-数组（方法内被解析为json）
 * @param string $sms_sign           签名
 * @param string $sendtime    
 * @author 王彬
 * @return data['alibaba_aliqin_fc_sms_num_send_response']['result']['success']
 * @add_time 2016-08-15
 *
  */
function sendAliSMS($sms_phones,$sms_template,$sms_param,$sendtime){
	$client  = new Client;
	$request = new SmsNumSend;
	
	$sms_sign = getConfig('AlidayuSignName');
	
	// 设置请求参数
	$req = $request->setSmsTemplateCode($sms_template)
		->setRecNum($sms_phones)
		->setSmsParam(json_encode($sms_param))
		->setSmsFreeSignName($sms_sign)
		->setSmsType('normal')
		->setExtend('demo');
	return $client->SendSMS($req);
}
/***************************** 阿里大鱼大鱼方法结束 ************************/
/***************************** 阿里云方法开始 ************************/



/**
 * @name  		sendAliYunSms($msg_receiver,$template_code,$template_param)
 * @desc  		阿里云短信发送（所有参数只能为数组或字符串格式）
 * @param 		array $msg_receiver        （短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式）
 * @param 		string $template_type      （模板类型编号。TemplateCode1为修改密码验证；；）
 * @param 		array $template_param      （模板参数数组，详情见config表对应memo）
 * @author 		黄子正
 * @version 	版本 V1.0.1
 * @addtime 	2017-11-22
 * @updatetime  2018-02-08
 * @return 		成功返回OK，失败返回错误信息
 * 
 */
function sendAliYunSms($msg_receiver,$template_type,$template_param){
    $sys_config=M('sys_config');
    $template_code=$sys_config->where("config_code = '$template_type'")->getField('config_value');//获取模板编号
    $SignName=$sys_config->where("config_code = 'AliYunSignName'")->getField('config_value');//获取短信签名
    $template_code=trim($template_code);//去空格处理
	$reuslt=sendAliYunSmsConfig($SignName,$template_code,$msg_receiver,$template_param,'123');
	// 向短信发送日志表中添加记录
	$staff_no=9999;
	$data=array();
	$data['type_code']='cust';
	$data['msg_sender']=$staff_no;//发送人编号
	$data['msg_receivers']=$msg_receiver;//接收人编号
	$data['msg_template']=$template_type;//模板ID
	$data['msg_param']=$template_param;//模版参数json格式
	$data['is_send']=1;//是否发送成功（0：否 1：是）
	$data['is_sms']=1;//是否是发送短息(1：是 0：否）-只用于短信列表发送记录查询数据
	$data['add_staff']=$staff_no;
	$data['add_time']=date('Y-m-d H:i:s');
	$data['update_time']=date('Y-m-d H:i:s');
	$data['status']='1';
	$sms_add=M('com_msg_log')->add($data);
	saveLog(ACTION_NAME,1,'','操作员['.$staff_no.']于'.date("Y-m-d H:i:s").'新增一条数据，编号为['.$sms_add.']');//系统日志
    return $reuslt->Message;
	//测试用例
    // $a=sendAliYunSmsConfig("蚂蚁店赢", 
        // "SMS_132655063", 
        // "15264160178", 
        // Array(  // 短信模板中字段的值
            // "code"=>"123456"
        // ),
        // "123");
		// return $a;
}


/***************************** 推送方法开始 ************************/

/**
 * @name  sendPushMsg()  目前不支持IOS设备
 * @desc  信鸽消息推送
 * @param string $title         推送标题
 * @param string $content       推送内容
 * @param unknown $accounts     员工编号
 * @param datetime $times       发送时间    格式（Y-m-d H:i:s）
 * @return 1 代表成功      
 *$push_status=sendXinGeMsg('新审批', '有需要您审批的申请！', 0, $log_row['Node_staff']);
  */

 function sendPushMsg($title,$content,$accounts,$time)
 {
	 $android_push_status=sendXinGeMsg($title, $content, 0, $accounts,$time);
	 $Ios_push_status=sendXinGeMsg($title, $content, 1, $accounts,$time);
     return array("ANDROID_status"=>$android_push_status,"IOS_status"=>$Ios_push_status);
 }
 
/**
 * @name  sendXinGeMsg()
 * @desc  信鸽消息推送
 * @param string $title         推送标题
 * @param string $content       推送内容
 * @param number $environment   IOS与安卓区分       0安卓        1苹果
 * @param unknown $accounts     员工编号
 * @return 0代表成
 *$push_status=sendXinGeMsg('新审批', '有需要您审批的申请！', 0, $log_row['Node_staff']);
  */

 function sendXinGeMsg($title,$content,$environment,$accounts,$time)
 {
	vendor('Xinge.XingeApp');
	/* 获取信鸽配置 */
	if($environment==1){//IOS设备
	    /* 混合 */
	    $accessid=getConfig('Ios_accessid');
	    $secretkey=getConfig('Ios_secretkey');
	    /* 原生 */
	    $o_accessid=getConfig('Ios_accessid_original');
	    $o_secretkey=getConfig('Ios_secretkey_original');
	    $arr=strToArr($accounts);
	    foreach ($arr as $account){
	        $ret=\XingeApp::PushAccountIos($o_accessid,$o_secretkey,$content,$account, \XingeApp::IOSENV_DEV);//原生
	        $ret=\XingeApp::PushAccountIos($accessid,$secretkey,$content,$account, \XingeApp::IOSENV_DEV);
	    }
	}else{//andrioad设备
	    /* 混合 */
//	    $accessid=getConfig('accessid');
//            $secretkey=getConfig('secretkey');
            $accessid="2100258721";
	    $secretkey="6c42ae4b4a6014862dbf6ebd6a4d6431";
	    $push = new \XingeApp($accessid,$secretkey);
	    /* 原生 */
//	    $o_accessid=getConfig('accessid_original');
//	    $o_secretkey=getConfig('secretkey_original');
            $o_accessid="2100258721";
	    $o_secretkey="6c42ae4b4a6014862dbf6ebd6a4d6431";
	    $o_push = new \XingeApp($o_accessid,$o_secretkey);

		$style = new \Style(0,1,1,0,0);
		$mess = new \Message();
		$mess->setExpireTime(86400);
		$mess->setSendTime($time);
		$mess->setStyle($style);
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setType(\Message::TYPE_NOTIFICATION);
		
		$style = new \Style(0, 1, 1, 0, 0);
		$action = new \ClickAction();
		$action->setActionType(ClickAction::TYPE_URL);
		$action->setUrl("http://xg.qq.com");
		
		$accountList = strToArr($accounts);
		foreach ($accountList as $account){
		    $ret = $o_push->PushSingleAccount(0, $account, $mess);//原生
		    //$ret = $push->PushSingleAccount(0, $account, $mess);
		}
// 		if(sizeof($accountList)==1){//单发
// 		    $ret = $push->PushSingleAccount(0, $accounts, $mess);
// // 		    $ret = $o_push->PushSingleAccount(0, $accounts, $mess);
// 		}else{
// 		    $ret = $push->CreateMultipush($mess,$environment);
// 		    if (!($ret['ret_code'] === 0))
// 		        return $ret;
// 		    else
// 		    {
// 		        $result=array();
// 		        array_push($result, $push->PushAccountListMultiple($ret['result']['push_id'], $accountList));//追加数组
// 		        array_push($result, $o_push->PushAccountListMultiple($ret['result']['push_id'], $accountList));//追加数组
// 		        $ret=$result;
// 		    }
// 		}
	}
//        dump($ret);
//        dump($ret);
//       dump($ret);
//        die();
	if($ret[ret_code]==0){
	    return true;
	}else{
	    return false;
	}
	
 }

 
/***************************** 推送方法结束 ************************/

/***************************** 公文方法开始 ************************/


/**
 * @name  sendDoc()
 * @desc  添加公文到队列
 * @param $send_id  发送人
 *        $receive_id  接收人(","号分割的staff_no字符串)
 *        $content 内容
 *        $title  标题
 *        $attach  辅件
 * @return true/false
 * @author 靳邦龙
 * @time   2016-04-28
 */
function sendDoc($send_id,$receive_id,$content,$title){
	if(!empty($receive_id)){
		$db_doc_outbox = M("com_doc_outbox");
		$db_doc_inbox = M("com_doc_inbox");

		$post['add_time'] = date("Y-m-d H:i:s");
		$post['add_staff'] = $send_id;

		//古城修改为发送人
		$post['doc_sender'] = $send_id;
		$post['doc_receivers'] = $receive_id;//测试staff_no

		$post['doc_content'] = $content;
		$post['doc_title'] = $title;
		$post['status'] = 2;

		$doc_outbox_data=$db_doc_outbox->add($post);
		if (!empty($doc_outbox_data)){
			$post['doc_contentid']=$doc_outbox_data;
			$receive=explode(',', $receive_id);
			foreach ($receive as &$rec){
				$post['doc_receiver']=$rec;
				$doc_inbox_data=$db_doc_inbox->add($post);
			}
		}

		/* $outbox_id = $db_doc_outbox->add($post);
		 if(!empty($outbox_id)){
		 $post['doc_receiver'] = $receive_id;
		 $post['doc_contentid'] = $outbox_id;
		 $inbox_data = $db_doc_inbox->add($post);
		 } */
	}
	if($doc_inbox_data){
		return true;
	}else{
		return false;
	}

}
/**
 * @name  getDocList()
 * @desc  获取公文列表
 * @param 员工编号       $staff_no
 * @param 信件类型       $type[1公开信      2收件箱   3发件箱]
 * @return 返回公文列表
 * @author 王桥元
 * @time   2016-05-11
 */
function getDocList($staff_no,$type){
	if(!empty($staff_no)&&is_numeric($staff_no)){
		$db_staff = M("hr_staff");
		if($type==1){
			$db_doc_check = M("com_imail_outbox");
			$check_data = $db_doc_check->where("is_check = 1")->select();
			return $check_data;
		}elseif($type==2){
			$db_docuemnt_inbox = M();
			$Docuemnt_sql = "select o.imail_id,o.imail_title,o.add_time,o.is_check from sp_com_imail_outbox as o,sp_com_imail_inbox as inbox where inbox.imail_contentid = o.imail_id and o.status = 2 and inbox.imail_receiver = $staff_no";
			$inbox_list = $db_docuemnt_inbox->query($Docuemnt_sql);
			return $inbox_list;
		}elseif($type==3){
			$db_imail_outbox = M("com_imail_outbox");
			$outbox_list=$db_imail_outbox->where("status = 2 and imail_sender=$staff_no")->select();
			return $outbox_list;
		}
	}
}
/**
 * @name  getDocContent()
 * @desc  获取公文内容
 * @param 公文发件箱的id   $doc_id
 * @return 内部邮件列表
 * @author 王桥元
 * @time   2016-05-11
 */
function getDoc($doc_id,$field='doc_title'){
	if(!empty($doc_id)&&is_numeric($doc_id)){
		$db_doc_outbox = M("com_doc_outbox");
		$doc_row=$db_doc_outbox->where("doc_id=$doc_id")->field($field)->find();
	}
	if($doc_row){
		return $doc_row[$field];
	}else{
		return "无数据";
	}
}

/***************************** 公文方法结束 ************************/
/**
 * @desc    保存消息提醒
 * @name    saveAlertMsg()
 * @param   alert_type(提醒类型)
 * @param   alert_man(提醒人)
 * @param   alert_url(链接地址)
 * @param   alert_title 标题
 * @param   alert_time 提醒时间
 * @param   alert_nexttime 下次提醒时间
 * @param   alert_cycle 周期
 * @param   status=1
 * @return
 * @author  杨陆海
 * @version 版本 V1.0.1
 * @time    2016-08-27
 * @update  2016-09-23
 */
function saveAlertMsg($alert_type, $alert_man,$alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, $add_staff,$status=0,$alert_content) {
	$bd_alertmsg = M ( 'bd_alertmsg' );
	$data ['alert_type'] = $alert_type;
	$data ['alert_man'] = $alert_man;
	$data ['alert_url'] = $alert_url;
	$data ['alert_title'] = $alert_title;
	if(!empty($alert_time)){
		$data ['alert_time'] = $alert_time;
	}
	if(!empty($alert_nexttime)){
		$data ['alert_nexttime'] = $alert_nexttime;
	}
	$data ['alert_cycle'] = $alert_cycle;
	$data ['alert_content'] = $alert_content;
	$data ['status'] = $status;
	$data ['update_time'] = date ( "Y-m-d H:i:s" );
	$data ['add_staff'] = $add_staff;
	$data ['add_time'] = date ( "Y-m-d H:i:s" );
	$data ['memo'] = $memo;
	try {
		$alert_flag = $bd_alertmsg->add ( $data );
	} catch ( Exception $e ) {
		return $e;
	}
	return $alert_flag;
}
/**
 * @desc    获取提醒消息列表
 * @name    getAlertMsgList()
 * @param   alert_type(提醒类型)
 * @param   alert_man(提醒人)
 * @param   计算的周期类型： $cycle_type 
 *              seconds：秒，minutes：分钟，hours：小时，days：天，week：周，months：月，years：年
 * @param   周期数量：$cycle_num（整数）
 * @param   status=0
 * @return  二维数组
 * @author  靳邦龙
 * @version 版本 V1.0.1
 * @time    2017-11-15
 */
function getAlertMsgList( $alert_man,$alert_type,$start_time='',$end_time='',$status='0',$offset,$limit) {
	$bd_alertmsg = M ( 'bd_alertmsg' );
	if($alert_type){
	    $map['alert_type']=array('in',$alert_type);
	}
	$map['_string']='1=1';
	if($alert_man){
	    $map['_string'] = "FIND_IN_SET('$alert_man',alert_man)";
	}
	if($period){
	    $start_time=date("Y-m-d H:i:s");
	    $end_time=getTimePoint('plus', $period, $period_num, $format = 'Y-m-d H:i:s', $start_time) ;
	}
	if(!empty($start_time)){
	    $map['_string'] = $map['_string']." and alert_time >='$start_time'";
	}
	if(!empty($end_time)){
	     $map['_string'] = $map['_string']." and alert_time <='$end_time'";
	}
	if(!empty($status)){
	    $map['status'] = array('eq',$status);
	}else{
	    $map['status'] = array('eq',0);
	}
	$type_array = M('bd_type')->where("type_group = 'alert_type'")->getField('type_no,type_name');
	if(!empty($limit)){
		$alert_list=$bd_alertmsg->where($map)->order('add_time DESC')->limit($offset,$limit)->select();
		$msg_count=$bd_alertmsg->where($map)->count();
		foreach ($alert_list as &$msg){
			$msg['type_name']=$type_array[$msg['alert_type']];
		}
	 	$msg_list['code'] = 0;
        $msg_list['msg'] = '获取成功';
        $msg_list['count'] = $msg_count;
        $msg_list['data'] = $alert_list;
	} else {
		$msg_list=$bd_alertmsg->where($map)->order('add_time DESC')->select();
		foreach ($msg_list as &$msg){
			$msg['type_name']=$type_array[$msg['alert_type']];
		}
	}
	return $msg_list;
}
/**
 * @name  getAlertMsgInfo()
 * @desc  获取消息提醒详情
 * @param $alert_id
 * @param $field
 * @return 单条记录
 * @author 靳邦龙
 * @version 版本 V1.0.0
 * @addtime 2017-11-15
 */
function getAlertMsgInfo($alert_id,$field='all'){
	$bd_alertmsg = M('bd_alertmsg');
	$bd_type = M('bd_type');
	$ccp_communist = M('ccp_communist');
	$alert_map['alert_id'] = $alert_id;
	if($field=='all'){
		$alertmsg_data = $bd_alertmsg->where($alert_map)->find();
		if($alertmsg_data){
			$alertmsg_data['alert_type'] =getBdTypeInfo($alertmsg_data['alert_type'], 'alert_type');
			$alertmsg_data['alert_man'] =getCommunistInfo($alertmsg_data['alert_man']);
		}
	}else{
	    $alertmsg_data = $bd_alertmsg->where($alert_map)->getField($field);
	}
	return $alertmsg_data;
}
/**
 * @name  saveAlertNexttime()
 * @desc  修改下次提醒时间
 * @param $alert_id
 * @return true/false
 * @author 王彬
 * @version 版本 V1.0.0
 * @addtime   2016-07-18
 */
function saveAlertNexttime($alert_id){
	$bd_alertmsg = M('bd_alertmsg');
	$alert_map['alert_id'] = $alert_id;
	if(!empty($alert_id)){
		$alertmsg_data = $bd_alertmsg->where($alert_map)->find();
		$alert_time = $alertmsg_data['alert_time'];//提醒时间
		$alert_cycle = $alertmsg_data['alert_cycle'];//提醒周期
		if($alert_cycle == 'hour' || $alert_cycle == 'day' || $alert_cycle == 'week' || $alert_cycle == 'month' || $alert_cycle == 'year'){
			$alert_nexttime = date("Y-m-d 00:00:00",strtotime("$alert_time +1 $alert_cycle"));
			$data['alert_nexttime'] = $alert_nexttime;
			$alertmsg_data = $bd_alertmsg->where($alert_map)->save($data);
		}
	}
	if($alertmsg_data){
	    return true;
	}else{
	    return false;
	}
}

/**
 * @name  saveAlertStatus()
 * @desc  修改提醒状态 
 * @param $alert_id (提醒ID)
 * @param $status(状态 0：未读 1：已读 --不传该参数默认修改为已读)
 * @return true/false
 * @author 王彬
 * @version 版本 V1.0.0
 * @addtime   2016-07-18
 */
function saveAlertStatus($alert_id,$status = 1){
	$bd_alertmsg = M('bd_alertmsg');
	if(!empty($alert_id)){
		if($status != 1){
			$status = 0;
		}
		$alert_map['alert_id'] = $alert_id;
		$data['status'] = $status;
		$alertmsg_data = $bd_alertmsg->where($alert_map)->save($data);
	}
	if($alertmsg_data){
	    return true;
	}else{
	    return false;
	}
}
/**
 * @name  setSendImail()
 * @desc  添加邮件到队列
 * @param $imail_receiver  接收人编号
 *        $content 内容
 *        $title  标题
 *        $imail_attach  附件
 *        $people_type  员工类型
 *        $type 平台发送1、app发送2
 *        $communist_no 发送人
 * @return true/false
 * @author 靳邦龙
 * @time   2016-04-28
 */
function setSendImail($imail_receiver,$content,$title,$imail_attach,$people_type,$type,$staff_no){
	if(!empty($imail_receiver)){
		$db_imail_outbox = M("com_imail_outbox");
		$db_imail_inbox = M("com_imail_inbox");
		$post['add_time'] = date("Y-m-d H:i:s");
		$post['update_time'] = date("Y-m-d H:i:s");
		$post['add_staff'] = $staff_no;
		
		//古城修改为发送人
		$post['imail_sender'] = $staff_no;
		$post['imail_receivers'] = $imail_receiver;
		$post['imail_content'] = $content;
		$post['imail_title'] = $title;
		$post['imail_attach'] = $imail_attach;
		$post['status'] = 1;
		if(empty($people_type)){
			$post['people_type'] = 0;
		}else{
			$post['people_type'] = $people_type;
		}

		$outbox_id = $db_imail_outbox->add($post);
		if(!empty($outbox_id)){
			$receive = explode(",", $imail_receiver);
			foreach($receive as &$r){
				$post['imail_receiver'] = $r;
				$post['imail_contentid'] = $outbox_id;
				$inbox_data = $db_imail_inbox->add($post);
			}
		}
	}
	if($inbox_data){
		return $outbox_id;
	}else{
		return false;
	}

}



/**
 * @name  sendEmail()
 * @desc  发送邮件
 * @param $mailto  接收人邮箱
 *        $name 接收人姓名
 *        $subject  邮件主题
 *        $body 邮件内容
 *        $attachment 附件
 * @return true/false
 * @author 靳邦龙-王彬
 * @addtime   2016-04-28
 * @updatetime   2016-08-09
 */
/**
 * 邮件发送函数
 */
function sendEMail($mailto, $name, $subject, $body, $attachment) {
	//$smtp=M("sys_smtp");//获取默认的配置项
	//$sm=$smtp->where("remark=1")->find();
	Vendor('PHPMailer.PHPMailerAutoload');
	//不带附件的邮件发送
	$mail = new PHPMailer(); //实例化
	$mail->IsSMTP(); // 启用SMTP
	 
	$mail->Host=getConfig('mail_host'); //smtp服务器的名称    C('MAIL_HOST');
	$mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
	 
	$mail->Username =getConfig('mail_username');  //你的邮箱名    C('MAIL_USERNAME');
	$mail->Password =getConfig('mail_password'); //邮箱密码       C('MAIL_PASSWORD')
	$mail->From = getConfig('mail_from'); //发件人地址（也就是你的邮箱地址） C('MAIL_FROM')
	$mail->FromName = getConfig('mail_fromname'); //发件人姓名  C('MAIL_FROMNAME')
	$mail->AddAddress($mailto,$name);
	$mail->WordWrap = 50; //设置每行字符长度
	$mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
	$mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
	$mail->Subject =$subject; //邮件主题
	$mail->Body = $body; //邮件内容
	$mail->AltBody = "这是一个纯文本的非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示

	if(is_array($attachment)){ // 添加附件
		foreach ($attachment as $file){
			is_file($file) && $mail->AddAttachment($file);
		}
		$attached = true;
	}
	/* 	if(!$mail->Send()){//打印错误信息
	 echo $mail->ErrorInfo;
	 } */
	return($mail->Send());

}
/**
 * @name  sendSms()
 * @desc  添加短信到队列
 * @param $sms_phones  手机号
 *        $sms_content 内容
 *        $sendtime 
 * @return true/false
 * @author 靳邦龙-王彬
 * @addtime   2016-04-28
 * @updatetime   2016-08-09
 */
function sendSms($sms_phones,$sms_content,$sendtime)
{
	// 模拟提交数据函数
	$uid=getConfig('sms_uid');//企业账户
	$pwd=md5(getConfig('sms_pwd'));//企业密码md5加密
	$ip=getConfig('sms_ip');
	$sms_content=urlencode($sms_content.getConfig('sms_sign'));//短信内容
	
	$data='CORPID='.$uid.'&CPPW='.$pwd.'&PHONE='.$sms_phones.'&CONTENT='.$sms_content;
	//$sendurl='http://60.209.7.12:8080/smsServer/submit';
	$sendurl='http://'.$ip.':8080/smsServer/submit?CORPID='.$uid.'&CPPW='.$pwd.'&PHONE='.$sms_phones.'&CONTENT='.$sms_content;
	
	$return_msg=getHttpPage('post',$sendurl,$post);
	if ($return_msg) {
		return $return_msg;// 返回数据
	}else{
		return null;
	}
}

/**
 * @name  		sendHuaWeiYunSms($msg_receiver,$template_code,$template_param)
 * @desc  		华为云短信发送（所有参数只能为字符串格式）
 * @param 		array $msg_receiver        （短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式）
 * @param 		string $template_type_no      （模板类型编号。TemplateCode1为修改密码验证；；）
 * @param 		array $template_param      （模板参数数组，详情见config表对应memo）
 * @author 		ljj
 * @version 	版本 V1.0.1
 * @addtime 	2018-07-16
 * @updatetime  2018-07-16
 * @return 		成功返回数组中code = 00000，失败返回错误信息
 * 
 */
function sendHuaWeiYunSms($msg_receiver,$template_type_no,$template_param){
    // 请从应用管理页面获取APP接入地址，替换url中的ip地址和端口
	$url = 'https://117.78.29.66:10443/sms/batchSendSms/v1';
	// 请从应用管理页面获取APP_Key和APP_Secret进行替换
	$APP_KEY = 'ZNfaQuzcIPu97viak19V638j9aIL';
	$APP_SECRET = 'Owj17h1f1qJUmE9P7b28iCYOV898';
	// 请从模板管理页面获取模板ID进行替换
	$TEMPLATE_ID = '0f0d8387cb48402297d1afd0ffcec9d7';
	//模板变量请务必根据实际情况修改，查看更多模板变量规则
	//如模板内容为“您有${NUM_2}件快递请到${TXT_32}领取”时，templateParas可填写为["3","人民公园正门"]
	//双变量示例：$TEMPLATE_PARAS = '["3","人民公园正门"]';
	$TEMPLATE_PARAS = '['.$template_param.']';
	// 填写短信签名中的通道号，请从签名管理页面获取
	$sender = $template_type_no;
	// 填写短信接收人号码
	$receiver = $msg_receiver;
	// 状态报告接收地址，为空或者不填表示不接收状态报告
	$statusCallback = '';
	$client = new Client();
	try {
	    $response = $client->request('POST', $url, [
	        'form_params' => [
	            'from' => $sender,
	            'to' => $receiver,
	            'templateId' => $TEMPLATE_ID,
	            'templateParas' => $TEMPLATE_PARAS,
	            'statusCallback' => $statusCallback
	        ],
	        'headers' => [
	            'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
	            'X-WSSE' => buildWsseHeader($APP_KEY, $APP_SECRET)
	        ],
	        'verify' => false
	    ]);
	    $result = Psr7\str($response);
	    $json_start_num = strpos($result, '{');
	    $json_res = substr($result, $json_start_num);
	    $object_res = json_decode($json_res);
	    $array_res = object_array($object_res);
	    return $array_res;
	} catch (RequestException $e) {
	    echo $e;
	    echo Psr7\str($e->getRequest()), "\n";
	    if ($e->hasResponse()) {
	        echo Psr7\str($e->getResponse());
	    }
	}
}

function buildWsseHeader($appKey, $appSecret){
    $now = date('Y-m-d\TH:i:s\Z');
    $nonce = uniqid();
    $base64 = base64_encode(hash('sha256', ($nonce . $now . $appSecret)));
    return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", 
                $appKey, $base64, $nonce, $now); 
}

/**
 * @name  getSmsBalance()
 * @desc  获取短信余额
 * @author 靳邦龙
 * @time   2016-04-28
 */
function getSmsBalance(){
	return "1000";
}
?>