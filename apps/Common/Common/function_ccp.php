<?php

use Api\Controller\PublicController;
use GuzzleHttp\RetryMiddleware;
/********************************党员,党组织,党日相关基础层方法 开始*************************************/

/**
* @name:getCommunistInfo               
* @desc：获取指定员工指定字段的值，支持单调数据多字段查询&&多条数据单字段查询
* @param：$communist_no(支持多个)员工编号   
* $field  字段名 (支持多个)                  
* @return：指定员工指定字段的值,支持多个查询
* @author：靳邦龙
* @addtime:2016-04-20
* @update: 2017-10-23
* @version：V1.0.1
**/
function getCommunistInfo($communist_no,$field='communist_name'){
    if(!empty($communist_no)){
        $db_communist=M('ccp_communist');
        $no_arr = strToArr($communist_no);
        $no_arr_length = sizeof($no_arr);//取编号数量
        if($field!='all'){
            $arr=strToArr($field);
            $arr_length=sizeof($arr);
            if($arr_length==1){//如果是一个字段，返回字符串
                $map['communist_no']  = array('in',$no_arr);
                $communist_value=$db_communist->where($map)->getField($field,true);
                $communist_value = arrToStr($communist_value);
            }else if($arr_length>1){//如果是多字段查询，查询单条数据，多个字段
            	if($no_arr_length>1){//多条数据select查询
                    $map['communist_no']  = array('in',$no_arr);
                    $communist_value=$db_communist->where($map)->field($field)->select();
                }elseif($no_arr_length==1){//单条数据，find查询
                    $map['communist_no']  = array('eq',$communist_no);
                    $communist_value=$db_communist->where($map)->field($field)->find();
                }
            }
        }elseif($field=='all'){//查询完整记录
            if($no_arr_length>1){//多条数据select查询
                $map['communist_no']  = array('in',$no_arr);
                $communist_value=$db_communist->where($map)->select();
            }elseif($no_arr_length==1){//单条数据，find查询
                $map['communist_no']  = array('eq',$communist_no);
                $communist_value=$db_communist->where($map)->find();
            }
		}
    }
	if($communist_value){
		return $communist_value;
	}else{
		return null;
	}
}
/**
 * @name  getCommunistList()
 * @desc  获取当前部门/岗位下所有员工编号
 * @param $party_no（多部门以逗号分割）
 * @param $post_no 岗位编号(多岗位)
 * @param $status 状态(支持多个)
 * @param $communist_level 职称
 * @param $communist_diploma 学历
 * @param $communist_sex 性别
 * @param $communist_name 员工姓名
 * @param $communist_no 员工工号
 * @param $returntype 'arr':返回数组 'str':返回编号字符串 
 * @param isChild 是否返回下级部门员工    **** 多部门此参数不支持
 * @return $returntype=arr   返回二维数组；str 返回员工编号字符串
 * @return $communist_source 来源类型
 * @author 靳邦龙
 * @addtime   2017-11-06
 */
function getCommunistList($party_no,$returntype='str',$ischild = '1',$post_no,$status,$communist_name,$communist_no,$communist_level,$communist_diploma,$communist_sex,$start,$end,$communist_mobile,$communist_source,$page,$pagesize){
	//print_r($party_no);
	$ccp_communist = M('ccp_communist');
	if(!empty($party_no)){
	    if($ischild == '1'){
	        $party_nos_arr=getPartyChildMulNos($party_no,'arr');
	    }else{
	        $party_nos_arr=strToArr($party_no);
	    }
	    $map['party_no']=array('in',$party_nos_arr);
	}else{
		$map['party_no']=array('in',session('party_no_auth'));
	}
	if(!empty($communist_source)){
		$map['communist_source'] = array('eq',$communist_source);
	}
	if(!empty($post_no)){
	    $post_nos_arr=strToArr($post_no);
	    $map['post_no']=array('in',$post_nos_arr);
	}
	if(!empty($status)){
	    $status_arr=strToArr($status);
	    $map['status']=array('in',$status_arr);
	} else {
		$map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
	}
	if(!empty($communist_no)){
	    $map['communist_no'] = array('in',$communist_no);
	}
	if(!empty($communist_name)){
	    $map['communist_name'] = array('like',"%$communist_name%");
	}
	if(!empty($communist_sex)){
	    if($communist_sex==2){
	        $map['communist_sex'] = array('neq',1);
	    }else{
	        $map['communist_sex'] = array('eq',$communist_sex);
	    }
	}
	if(!empty($communist_level)){
	    $map['communist_level'] = array('eq',$communist_level);
	}
	if(!empty($communist_mobile)){
	    $map['communist_mobile'] = array('like',"%$communist_mobile%");
	}
	if(!empty($communist_diploma)){
	    $map['communist_diploma'] = array('eq',$communist_diploma);
	}
	if(!empty($start)&&empty($end)){
	    $map['communist_ccp_date']  = array('egt',$start);
	}elseif(!empty($end)&&empty($start)){
	   $map['communist_ccp_date']  = array('elt',$end);
	}elseif(!empty($start)&&!empty($end)){
	    $map['_string'] = "communist_ccp_date>=$start and communist_ccp_date<=$end";
	}
	if($returntype == 'str'){
		$communists_no = $ccp_communist->where($map)->getField('communist_no',true);
		$communists_no=arrToStr($communists_no);
		return $communists_no;
	}else if($returntype == 'arr'){
		if (!empty($pagesize)){
			$communist_list = $ccp_communist->where($map)->limit($page,$pagesize)->select();
			$count = $ccp_communist->where($map)->count();
		}else{
			$communist_list = $ccp_communist->where($map)->select();
		}
	    if ($communist_list) {
	        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
	        $post_name_arr = M('ccp_party_duty')->getField('post_no,post_name');
	        $code_map['code_group'] = 'diploma_level_code';
	        $code_name_arr = M('bd_code')->where($code_map)->getField('code_no,code_name');
	        $status_map['status_group'] = 'communist_status';
	        $status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
	        $status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
            foreach ($communist_list as &$communist){
                $communist['party_no'] = $party_name_arr[$communist['party_no']];
                $communist['post_no'] = getPartydutyInfo($communist['post_no']);
                $communist['communist_age']=getCommunistAge($communist['communist_birthday']);
                $communist['communist_diploma'] = $code_name_arr[$communist['communist_diploma']];
                $communist['communiststaus'] = "<font color='" . $status_color_arr[$communist['status']] . "'>" . $status_name_arr[$communist['status']] . "</font> ";
                $communist['communist_sex'] = $communist['communist_sex'] == 1 ? '男' : '女';
            }
        }
	    if (!empty($pagesize)){
	    	$arr['data'] = $communist_list;
	    	$arr['count'] = $count;
	    	return $arr;
	    }else{
	    	return $communist_list;
	    }
		
	}

}
/**
* @name:getCommunistSelect               
* @desc：获取员工列表
* @param：$selected_no(支持多个)当前员工编号    
* @param：$party_no    部门编号支持多个
* @param：$post_no    (支持多个)岗位编号    
* @param：$status     (支持多个)状态值   
* @param：$is_managercommunist     是否查询自己作为部门管理员的   所有下级员工数据 1是
* @param：$is_uncreate_user 是否查询未分配账号的   
* @return：返回选中状态的全部下拉列表（HTML代码）。
* @author：靳邦龙 
* @addtime:2017-11-06
* @version：V1.1.0
**/
function getCommunistSelect($selected_no,$party_no,$post_no,$status,$is_managercommunist,$is_uncreate_user){
    $db_communist=M('ccp_communist');
    $selected_no_arr=explode(",",$selected_no);//分割成数组
    if(!empty($party_no)){
        $party_nos_arr=strToArr($party_no);
        $map['party_no']=array('in',$party_nos_arr);
    }
    if(!empty($post_no)){
	    $post_nos_arr=strToArr($post_no);
	    $map['post_no']=array('in',$post_nos_arr);
	}
	if(!empty($status)){
	    $status_arr=strToArr($status);
	    $map['status']=array('in',$status_arr);
	}else{
		 $map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
	}
	if(!empty($is_managercommunist)){
		$communist_nos_str=getManagerCommunistNos(session('communist_no'));
		$communist_nos_arr=strToArr($communist_nos_str);
		if($communist_nos_arr){
		    $map['communist_no'] = array('in',$communist_nos_arr);
		}
	}
	if(!$map){//赋默认查询条件，否则报错
	    $map['status'] =array('neq','');
	}
	$where['_complex'] = $map;
	if($is_uncreate_user==1){
	    $db_user=M('sys_user');
	    $user_communist_nos=$db_user->getField('user_relation_no',true);
	    if($user_communist_nos){
	        $where['communist_no']  = array('not in',$user_communist_nos);
	    }
	}
	$communist_list=$db_communist->where($where)->select();
    $communist_options="";
	foreach ($communist_list as &$communist){
		$selected="";
		if(in_array($communist['communist_no'], $selected_no_arr)){//判断角色id是否存在于数组中
			$selected="selected=true";
		}
		$communist_options.="<option $selected value='".$communist['communist_no']."'>".$communist['communist_name']."</option>";
	}
    if(!empty($communist_options)){
        return $communist_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}

/**
 * @name  getCommunistPartyList()
 * @desc  获取员工所在部门及下属部门列表
 * @param $communist_no(员工编号)
 * @return 部门列表
 * @author 王彬
 * @version 版本 V1.0.0
 * @update    2017-10-17 getPartyChildNos($party_no,'str',1);
 * @addtime   2016-07-19
 */
function getCommunistPartyList($communist_no){
	$ccp_party = M('ccp_party');
	//根据人员编号获取部门
    $party_no = getCommunistInfo($communist_no, "party_no");
    if(!empty($party_no)){
    	$party_nos = getPartyChildNos($party_no,'str',1);
    	$party_list = $ccp_party->where("party_no in($party_nos) and status = 1")->select();
    }
    return $party_list;
}
/**
 * @name:saveCommunistLog
 * @desc：保存员工历程
 * @param：$communist_no员工编号
 * @param：$log_type 员工历程类型编号关联bd_type表
 * @param：$status  党员发展状态(当$log_type=10时，此参数必填)
 * @param：$add_staff  添加人编号
 * @param：$name  党员关系转移的原支部名称/学习资料名称/考试名称/会议名称/投稿名称/精准扶贫活动名称/第一书记党支部名称/志愿者活动名称
 * @param：$new_name  党员关系转移或流动党员的新组织名称(当$log_type=11/12时，此参数必填)
 * @param：$log_attach  附件ID
 * @return：bool   true/false
 * @author：靳邦龙
 * @addtime:2017-11-08
 * @version：V1.0.0
 **/
function saveCommunistLog($communist_no,$log_type,$status,$add_staff,$name,$new_name,$log_attach){
	$db_communist_log = M('ccp_communist_log');
    $logs_data['communist_no'] = $communist_no;
    $logs_data['log_name']= getCommunistInfo($communist_no);
    $logs_data['log_type'] = $log_type;
    $logs_data['add_staff'] = $add_staff; 
    $logs_data['add_time'] = date("Y-m-d H:i");
    $logs_data['log_attach'] = $log_attach;
   // $logs_data['status'] = 1;
    
    //历程内容
    $type_name='['.getBdTypeInfo($log_type, 'communist_log_type').']';
    $tody='于'.date('Y-m-d');
    $communist_name=$logs_data['log_name'];
    switch ($log_type) {
        case 21:  //党员状态改变
        	$type_name='['.getStatusInfo('communist_status',$status,status_name).']';
        	$content='状态改为'.getStatusInfo('communist_status',$status,status_name);
        	break;
        case 10://党员发展的内容
        	//$logs_data['status'] = 0; //党员发展未审核  1是已审核
        	if($status == '11' || $status == '21'){
        		$tody= date('Y-m-d');
        		$type_name='['.getStatusInfo('communist_status',$status,status_name).']';
            	$content='提交'.getStatusInfo('communist_status',$status,status_name);
        	}else{
        		$type_name='['.getStatusInfo('communist_status',$status,status_name).']';
            	$content='状态改为'.getStatusInfo('communist_status',$status,status_name);
        	}
            break;
        case 11://党员关系转移
            $content="由$name 转移到 $new_name";
            break;
        case 12://  流动党员
            $content="由$name 流动到 $new_name";
            break;
        case 13://学习
            $content="学习了《".$name."》资料";
            break;
        case 14://考试
            $content="参加了《".$name."》 考试";
            break;
        case 15:// 会议
            $content="参加了《".$name." 会议";
            break;
        case 16://投稿
            $content="投递了《".$name."》 稿件";
            break;
        case 17:// 精准扶贫
            $content="帮扶了".$name;
            ;break;
        case 18://第一书记
            $content="成为".$name." 第一书记";
            ;break;
        case 19://双联双创活动
            $content="参加了".$name."双联双创活动";
            ;break;
       case 20://志愿者活动
        	$content="参加了".$name."志愿者活动";
       ;break;
        default:;break;
    }
    $logs_data['log_content']= $type_name.$tody.$communist_name.$content;
    if($log_type==10||$log_type==21){
    	if($status == '1' || $status == '40'){
    		$logs_data['log_content']= $type_name.getStaffInfo($add_staff).$tody.' '.$communist_name.$content;
    	}else{
    		$logs_data['log_content']= $type_name.getStaffInfo($add_staff).$tody.'将'.$communist_name.$content;
    	}
        
    }
    $res=$db_communist_log->add($logs_data);
    if($res){
        return true;
    }else{
        return false;
    }
}
/**
 * @name:getCommunistLogList
 * @desc：获取指定员工历程
 * @param：$communist_no员工编号
 * @param：$log_type 类型
 * @return：员工历程列表
 * @author：靳邦龙
 * @addtime:2017-11-08
 * @version：V1.0.0
 **/
function getCommunistLogList($communist_no,$log_type){
	if(!empty($communist_no)){
		$db_communist_log=M('ccp_communist_log');
		$map['_string'] = "FIND_IN_SET('$communist_no',communist_no)";
		//$map['status']=array('eq',1);
		if($log_type){
		    $map['log_type']=array('in',strtoarr($log_type));
		}
		$log_list=$db_communist_log->where($map)->order('add_time desc')->select();
		foreach ($log_list as &$list) {
			$list['add_time'] = getFormatDate($list['add_time'],'Y-m-d H:i');
		}

	}
    return $log_list;
}

/**
* @name:getManagerCommunistNos               
* @desc：获取自己作为管理员的部门员工列表字符串
* @param：$communist_no员工编号
* @param：$noself 是否包含自己
* @param：$format 返回数据格式  arr数组     str字符串             
* @return：$format=str以逗号隔 开的当前员工管理的所有部门的员工communist_no字符串；arr：一维数组
* @author：王飞 -靳邦龙
* @addtime:2017-05-13
* @update:2017-11-06 
* @version：V1.0.1
**/
function getManagerCommunistNos($communist_no,$noself=1,$format='str'){
	$ccp_party = M('ccp_party');
	$ccp_communist = M('ccp_communist');
	$party_map['_string']="find_in_set('$communist_no',party_manager)";
	$party_list = $ccp_party->where($party_map)->getField('party_no',true);
	if ($party_list){
	    $party_nos=arrToStr($party_list);
	    $allparty_listget=getPartyChildMulNos($party_nos,'arr');
		$map['status']=array('neq','0');
		$map['party_no']=array('in',$allparty_listget);
		$managercommunist_no = $ccp_communist->where($map)->getField('communist_no',true);
	}
	if ($noself==1) {
	    $managercommunist_no[]=$communist_no;
	    $managercommunist_no = array_unique($managercommunist_no);
	}
	if($format=='str'){
	   return arrToStr($managercommunist_no);
	}else{
	    return $managercommunist_no;
	}
}
/**
 * @name:getCommunistAvatar
 * @desc：获取员工头像/色值
 * @param：$communist_avatar （色值/头像字符串）
 * 			$communist_name （名称）
 * @return：html标签
 * @author：王彬
 * @addtime: 2016-11-23
 * @updatetime: 2016-11-23
 * @version：V1.0.0
 **/
function getCommunistAvatar($communist_avatar,$communist_name,$type = 1){
	$num = strrpos($communist_avatar,"#");
	if($num === 0){
		if($type == 1){
			$str = "<i style='background:".$communist_avatar.";'>$communist_name</i>";
		}else{
			$str = '<img alt="" class="img-circle" src="'.C('TMPL_PARSE_STRING')['__STATICS__'].'/layouts/layout/images/avatar3_small.jpg" />';
		}
	}else{
	    $communist_avatar=str_replace("uploads/", '', $communist_avatar);
		if($type == 1){
			$str = "<i><img src='".__ROOT__.'/'.$communist_avatar."'></i>";
		}else{
			$str = '<img alt="" class="img-circle" src="'.__ROOT__.'/'.$communist_avatar.'" />';
		}
	}
	return $str;
}

/**
* @name:getPartydutyInfo           
* @desc：获取岗位名称
* @param：$partyduty_no 岗位编号  支持逗号拼接的多编号查询   
* @return：岗位名称或逗号拼接的字段值
* @author：靳邦龙
* @addtime:2016-04-20
* @update:2017-10-23 多编号查询
* @version：V1.0.1
**/
function getPartydutyInfo($partyduty_no,$field='post_name'){
    if(!empty($partyduty_no)){
        $db_partyduty=M('ccp_party_duty');
		if($field=='all'){
			$duty_map['post_no'] = $partyduty_no;
			$partyduty_field=$db_partyduty->where($duty_map)->find();
		}else{
		    $no_arr = strToArr($partyduty_no);
		    $map['post_no']  = array('in',$no_arr);
		    $partyduty_info=$db_partyduty->where($map)->getField($field,true);
		    $partyduty_field = arrToStr($partyduty_info);
		}
    }
    if($partyduty_field){
        return $partyduty_field;
    }else{
        return '无';
    }
}
/**
* @name:getPartydutyList               
* @desc：获取岗位列表
* @param：$post_name 模糊查询岗位名称   
* @return：岗位列表
* @author：王世超
* @addtime:2016-08-10
* @version：V1.0.0
**/
function getPartydutyList($partyduty_name,$page,$pagesize){
	$db_partyduty = M('ccp_party_duty');
	if(!empty($partyduty_name)){
		$duty_map['post_name'] = array('like','%'.$partyduty_name.'%');
	}
    $partyduty_list['data'] = $db_partyduty->where($duty_map)->limit($page,$pagesize)->select();
    $partyduty_list['count'] = $db_partyduty->where($duty_map)->count();
	return $partyduty_list;
}
/**
* @name:getPartydutySelect               
* @desc：获取岗位下拉列表
* @param：$selected_no（支持多个）  当前岗位的编号
* @return：带选中状态的岗位下拉列表（HTML代码）
* @author：靳邦龙
* @addtime: 2016-04-28
* @version：V1.0.0
**/
function getPartydutySelect($selected_no,$field="post_name"){
    $db_partyduty=M('ccp_party_duty');
	if($field=='post_recruitname')
	{
		 $partyduty_list=$db_partyduty->where("status=2")->field('post_no,'.$field)->select();
	}else{
		 $partyduty_list=$db_partyduty->where("status in (1,2)")->field('post_no,'.$field)->select();
	}
    $selected_no_arr=strToArr($selected_no);//分割成数组
    $partyduty_options="";
    foreach($partyduty_list as &$partyduty){
        $selected="";
        foreach ($selected_no_arr as &$arr)
        {
            if($arr==$partyduty['post_no']){
                $selected="selected=true";
            }
        }
        $partyduty_options.="<option $selected value='".$partyduty['post_no']."'>".$partyduty[$field]."</option>";
    }
    if(!empty($partyduty_options)){
        return $partyduty_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}
/**
 * @name  getPartyInfo()
 * @desc  获取部门名称
 * @param 部门编号   $party_no
 * @param        $field  字段名 传入此参数时、查询此字段的值
 * @return 部门名称
 * @author 靳邦龙  杨凯 
 * @time   2016-04-20
 */
function getPartyInfo($party_no,$field='party_name'){
	$db_party=M('ccp_party');
        if($field=='all') {
        	$no_arr = strToArr($party_no);
        	$map['party_no']  = array('in',$no_arr);
            $party_fieldinfo=$db_party->where($map)->find();           
        }else{
        	if($party_no){
	            $no_arr = strToArr($party_no);
	            $map['party_no']  = array('in',$no_arr);
	            $party_info=$db_party->where($map)->getField($field,true);
	            $party_fieldinfo = arrToStr($party_info);
        	}
        }
       
    if($party_fieldinfo){    	
		return $party_fieldinfo;
    }else{
        return null;
    }
}

/**
 * @desc    超级管理员对应数据
 * @name    getPartyData
 * @param   $is_auth string 是否有对应权限
 * @param   $returntype string 返回类型 arr/str
 * @return 部门数组/部门字符串
 * @author  刘丙涛
 * @addtime 2017-10-13
 * @version V1.0.0
 */
function getPartyData($is_auth,$communist_no,$returntype='arr'){
    $ccp_party = M('ccp_party');
    if ($is_auth){//超管
        $party_str = getPartyChildNos();
    }else{
    	$party_map['_string']="find_in_set('$communist_no',party_manager)";
        $party_no = $ccp_party->where($party_map)->getField('party_no',true);
        if (!empty($party_no)){//部门负责人
            $party_nos=arrToStr($party_no);
            $party_str= getPartyChildMulNos($party_nos);
        }else{//非负责人
            $party_str = getCommunistInfo($communist_no,'party_no');
            if(empty($party_str)){
            	$party_str = getPartyChildNos();
            }
        }
    }
    if($returntype == 'arr'){
    	$p_map['party_no'] = array('in',$party_str);
    	$p_map['status'] = 1;
        $flag = $ccp_party->where($p_map)->field('party_no,party_name,party_pno')->select();
    }else{
        $flag = $party_str;
    }
    return $flag;
}

/**
 * @name  getPartyManagerNos()
 * @desc  获取某人所在部门管理员
 * @param $communist_no 人员
 * @return 部门名称
 * @author 杨陆海-靳邦龙
 * @time   2017-10-23
 */
function getPartyManagerNos($communist_no){
    $communist_party=getCommunistInfo($communist_no,'party_no');
    $party_man=getPartyInfo($communist_party,'party_manager');
    return $party_man;
}
/**
 * @name  getPartyList()
 * @desc  获取子级部门列表
 * @param $party_no(部门编号) 
 * @return 部门列表
 * @author 靳邦龙
 * @version 版本 V1.0.0
 * @addtime   2017-10-16
 */
function getPartyList($party_pno='0',$party_name){
	$db_party = M('ccp_party');	
	if(!empty($party_name)){		
		$where['party_name'] = array('like',"%$party_name%");	
	}	
	if($party_pno!=""){		
		$where['party_pno'] = array('eq',$party_pno);		
		$party_list = $db_party->where($where)->select();
	}else{		
		$party_list = $db_party->select();
	}
	return $party_list;
}
/**
 * @name  getPartySelect()
 * @desc  获取部门列表
 * @param 当前部门的编号   $selected_no（支持多个）
 * @return 带选中状态的部门下拉列表（HTML代码）
 * @author 靳邦龙--王彬
 * @memo 部门编号修改为多编号（逗号分隔）
 * @version 版本 V1.0.1
 * @updatetime 2016-07-20
 * @addtime   2016-04-28
 */
function getPartySelect($selected_no,$ids){
    $db_party=M('ccp_party');
	// if(!empty($ids)){
		$where['party_no'] = array('in',session('party_no_auth'));
	// }
	

    $party_list=$db_party->where($where)->field('party_no,party_name,party_type')->select();
	
    $party_options="";
	$select_arr = strToArr($selected_no);
    foreach($party_list as &$party){
		$selected="";
		foreach($select_arr as $arr){
			if($arr==$party['party_no']){
				$selected="selected=true";
			}
		}
		$party_options.="<option $selected data-type='".$party['party_type']."' value='".$party['party_no']."'>".$party['party_name']."</option>";
    }
    if(!empty($party_options)){
        return $party_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}

/**
 * @name getPartypno()
 * @desc  最上级no
 * @param
 * @return 人员绩效
 * @author 袁文豪
 * @version 版本 V1.1.0
 * @updatetime   2016/09/21
 * @addtime   2016-09-21
 */
function getPartypno($party_no){
	$party_map['party_no'] = $party_no;
	$party_pno = M('ccp_party')->where($party_map)->getField('party_pno');
	if($party_pno != '0'){
		return getPartypno($party_pno);
	}else{
		return $party_no;
	}

}
/**
 * @name  getpartycommunistList()
 * @desc  人员绩效
 * @param
 * @return 人员绩效
 * @author 袁文豪
 * @version 版本 V1.1.0
 * @updatetime   2016/09/21
 * @addtime   2016-09-21
 */
function getpartycommunistList(){
    $party_info=M("ccp_party")->field("party_name,party_no")->where("party_pno=1")->select();
    foreach($party_info as &$row){
    	$party_map['party_pno'] = $row['party_no'];
        $row['party_n']=M("ccp_party")->field("party_name,party_no,party_manager")->where($party_map)->select();
        foreach($row['party_n'] as &$communist){
            if($communist['party_manager']!=""){
                $communist_arr=explode(",",$communist['party_manager']);
            }
            $comm_map['party_no'] = $communist['party_no'];
            $communist['communist_name']=M("ccp_communist")->field("communist_id,post_no,communist_no,communist_name")->where($comm_map)->select();
            foreach($communist['communist_name'] as &$position){
                if (in_array($position['communist_no'], $communist_arr)){
                    $position['position']="负责人";
                }
            }
        }
    }
    return $party_info;
}
/**
 * @name:   getPartyChildMulNos
 * @desc：       获取多个部门所有下级编号
 * @param：   $party_nos  逗号拼接的多个部门编号
 * @param：   $format 返回数据格式  arr数组     str字符串
 * @return：str:以逗号隔 开部门的员工party_no字符串; arr： 一维数组
 * @author：靳邦龙
 * @addtime:2017-11-06
 * @version：V1.0.1
 **/
function getPartyChildMulNos($party_nos,$format='str'){
    if($party_nos){
        $party_list=strToArr($party_nos);
        $allparty_listget=array();
        foreach($party_list as &$party_no){
            $allparty_listget[]= getPartyChildNos($party_no);
        }
        $allparty_listget = arrToStr($allparty_listget);
        $allparty_listget = array_unique(strToArr($allparty_listget));
    }
    if($format=='str'){
        return arrToStr($allparty_listget);
    }else{
        return $allparty_listget;
    }
}
/**
 * @name  getPartyChildNos()
 * @desc  获取当前部门的下级部门列表
 * @param 当前部门no
 * @param noself 是否包含自己
 * @param format 返回格式str或array
 * @return array 以逗号隔开的当前部门及所有下级部门的party_no字符串或数组
 * @author 靳邦龙
 * @time   2017-10-16
 */
function getPartyChildNos($party_pno='0',$format='str',$noself='1',$is_admin){
	if($is_admin == 'is_admin'){
		$staff_no = session('staff_no');
		$comm_map['communist_no'] = $communist_no;
		$comm_map['type_no'] = 'is_admin';
		$communist_auth = M('sys_user_auth')->where($comm_map)->count();
	} else {
		$communist_auth = 0;
	}
	if($communist_auth > 0){ // 判断是否是有超级管理员权限
		// 有权限查看全部党组织
		$party_no_arr=M("ccp_party")->where('status = 1')->field('party_no')->select();
		if(!empty($party_no_arr)){
			foreach ($party_no_arr as $party_val) {
				$party_nos_arr[] = $party_val['party_no'];
			}
		}
	} else {
		// 无权限只允许查看本级及下级
		$party_nos_arr=getPartyChildNoselfNos($party_pno);
		if($noself==1){ // 
	        $party_nos_arr[]=$party_pno;
	    }
	}
    if($format=='str'){
        $party_nos_arr=arrToStr($party_nos_arr);
    }
    return $party_nos_arr;
}
/**
 * @name  getPartyChildNoselfNos()
 * @desc  获取当前部门的所有下级部门编号列表（供getPartyChildNos调用）
 * @param 当前部门no
 * @return 所有下级部门编号的一维数组
 * @author 靳邦龙
 * @time   2017-10-16
 */
function getPartyChildNoselfNos($party_pno){
    $ccp_party=M('ccp_party');
    $party_map['party_pno'] = $party_pno;
    $party_map['status'] = 1;
    $party_list = $ccp_party->where($party_map)->getField('party_no',true);
    if($party_list){
        foreach($party_list as $party_no){
            $next_nos_arr=getPartyChildNoselfNos($party_no);
            if($next_nos_arr){
                $party_list=array_merge($party_list,$next_nos_arr);
            }
        }
    }
    return $party_list;
}
/**
 * @name getCommunistTransferInfo()
 * @desc  获取党员转移信息
 * @param   $log_id 日志id
 * @param   $field  all所有字段 ，
 * @param    $where 条件
 * @return  array
 * @author 刘丙涛
 * @version 版本 V1.0.0
 * @addtime   2017-5-11
 */
function getCommunistTransferInfo($log_id,$field = 'all',$where='log_type is null'){
    $db_communist_log = M('ccp_communist_log');
    if (empty($log_id)){
        $communist_log = $db_communist_log->where($where)->find();
    }else{
        $communist_log = $db_communist_log->where($where." and log_id=$log_id")->find();
    }

    if($field != 'all'){
        $communist_log = $communist_log[$field];
    }
    return $communist_log;
}

/**
 * @name getPartyDynamicInfo()
 * @desc  获取党组织动态
 * @param  $party_no
 * @return  array
 * @author 刘丙涛
 * @version 版本 V1.0.0
 * @addtime   2017-5-20
 */
function getPartyDynamicInfo($party_no){
    $db_material = M('edu_material');//学习
    $db_exam = M('cms_exam');//考试
    $db_meeting = M('oa_meeting');//会议
    $db_article = M('cms_article');//文章
//    $communist_no = getCommunistList($party_no);
    $material_list = $db_material->order('add_time desc')->limit('5')->select();
    $exam_map['party_no'] = $party_no;
    $exam_list = $db_exam->where($exam_map)->order('add_time desc')->limit('5')->select();
    $meeting_list = $db_meeting->where($exam_map)->order('add_time desc')->limit('5')->select();
    $article_list = $db_article->order('add_time desc')->limit('5')->select();
    $data['material_list'] = $material_list;
    $data['exam_list'] = $exam_list;
    $data['meeting_list'] = $meeting_list;
    $data['article_list'] = $article_list;
    return $data;
}
/**
 * @name  getCommunistCount()
 * @desc  获取人员数量
 * @param $party_no （多部门以逗号分割）
 * @param $post_no 岗位编号(多岗位)
 * @param $status 状态(支持多个)
 * @param $communist_sex 性别
 * @param $communist_birthday 用于首页查询年龄段人数
 * @param $communist_ccp_date 用于首页查询党龄段人数
 * @return int
 * @author 刘丙涛
 * @addtime   2017-11-08
 */
function getCommunistCount($party_no,$post_no,$status,$communist_sex,$communist_birthday,$communist_ccp_date){
    $db_communist = M('ccp_communist');
    if(!empty($party_no)){
        $map['party_no']=array('in',$party_no);
    }
    if(!empty($post_no)){
        $map['post_no']=array('in',$post_no);
    }
    if(!empty($status)){
        $map['status']=array('in',$status);
    }
    if(!empty($communist_sex)){
        if($communist_sex==2){
            $map['communist_sex'] = array('neq',1);
        }else{
            $map['communist_sex'] = array('neq',$communist_sex);
        }
    }
    if(!empty($communist_birthday)){
        $map['_communist_birthday'] = "$communist_birthday";
    }
    if(!empty($communist_ccp_date)){
        $map['_communist_ccp_date'] = "$communist_ccp_date";
    }
    $count = $db_communist->where($map)->count();
    return $count;
}

/**
 * @name:getCommunistRank()
 * @desc：获取指定员工排名
 * @param $communist_no(支持多个)员工编号
 * @return array
 * @author:刘丙涛
 * @addtime:2017-10-17
 * @updatetime 2017-12-15 根据新版积分更新计算方法
 * @version：V1.0.1
 **/
function getCommunistRank($communist_no,$year,$type){
	$communist_map['communist_no'] = $communist_no;
	$party_no = M('ccp_communist')->where($communist_map)->getField('party_no');
	
	if (!empty($communist_no)){
		if(empty($year)){
			$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
			$comm_map['communist_no'] = array('in',$communist_no);
			if($type == "integral_volunteer_communist"){
				$comm_map['memo'] = "参加志愿者活动";
			}
			$data = M('ccp_communist')->where($comm_map)->field('communist_integral as integral')->order('integral desc,communist_no asc')->find();
			$rank_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
			$ranking = M('ccp_communist')->where($rank_map)->field('communist_no,communist_integral as integral')->order('integral desc,communist_no asc')->select();
			$rank_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
			$rank_map['party_no'] = $party_no;
			$party_ranking = M('ccp_communist')->where($rank_map)->field('communist_no,communist_integral as integral')->order('integral desc,communist_no asc')->select();
		}else{
			$year = date("Y");
			$map = ' ';
			if($type == "integral_volunteer_communist"){
				$map = " and memo = 参加志愿者活动";
			}
			$data = M()->query("SELECT IFNULL(log.integral_total,0) as integral FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1 AND YEAR = $year GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no $map WHERE c.`status` IN (21, 22) and c.communist_no IN ($communist_no)    ORDER BY communist_integral DESC ,c.communist_no asc , c.communist_no asc ");
			$data = $data[0];
			$ranking = M()->query("SELECT c.communist_no,IFNULL(log.integral_total,0) as communist_integral FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1 AND YEAR = $year GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no WHERE c.`status` IN (21, 22) ORDER BY communist_integral DESC , c.communist_no asc ");
			$party_ranking = M()->query("SELECT c.communist_no,IFNULL(log.integral_total,0) as communist_integral FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1 AND YEAR = $year GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no WHERE c.`status` IN (21, 22) and c.party_no = $party_no ORDER BY communist_integral DESC , c.communist_no asc ");
		}
		foreach ($ranking as $key => $rank_val) {
			if($rank_val['communist_no'] == $communist_no){
				$data['ranking'] = $key+1;
				break;
			}
		}
		foreach ($party_ranking as $key => $party_rank_val) {
			if($party_rank_val['communist_no'] == $communist_no){
				$data['party_ranking'] = $key+1;
				break;
			}
		}
	}
	return $data;
}
/**
 * @name:getCommunistIntegralList()
 * @desc：获取人员积分列表
 * @param $communist_no(支持多个)员工编号
 * @return array
 * @author:刘丙涛  王宗彬(添加优秀党员和纪检约谈按钮)
 * @addtime:2017-10-17
 * @version：V1.0.1
 **/
function getCommunistIntegralList($communist_no,$party_no,$is_year,$page,$pagesize){
	$party_no = getPartyChildNos($party_no);
    $data['count'] = getCommunistCount($party_no,'',COMMUNIST_STATUS_OFFICIAL);
    if(empty($is_year)){
    	$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
    	$comm_map['party_no'] = array('in',$party_no);
    	//$communist_list = M()->query("SELECT c.party_no,c.communist_no, c.communist_name,IFNULL(log.integral_total,0) as communist_integral FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1  GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no WHERE c.`status` IN (21, 22) and c.party_no IN ($party_no) ORDER BY communist_integral DESC,c.communist_no asc limit $page,$pagesize");
    	$communist_list = M('ccp_communist')->where($comm_map)->field('communist_no,communist_name,party_no,communist_integral')->order('communist_integral desc,communist_no asc ')->limit($page,$pagesize)->select();
    }else{
    	$year = date("Y");
    	$communist_list = M()->query("SELECT c.party_no,c.communist_no, c.communist_name,IFNULL(log.integral_total,0) as communist_integral FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1 AND YEAR = $year GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no WHERE c.`status` IN (21, 22) and c.party_no IN ($party_no) ORDER BY communist_integral DESC,c.communist_no asc limit $page,$pagesize");
    }
    if (!empty($communist_list)){
    	$num = $page+1;
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        foreach ($communist_list as &$list){
        	if($list['communist_integral'] < 0){
        		$list['communist_integral'] = 0;
        	}
        	$list['integral_total'] = $list['communist_integral'];
            $list['party_name'] = $party_name_arr[$list['party_no']];
            if (empty($list['integral_total'])){
                $list['integral_total'] = 0;
            }
            $list['num'] = $num;
            $list['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='".U('ccp_communist_integral_info',array('is_year'=>$is_year,'communist_integral'=>$list['communist_integral'],'rank'=>$num,'communist_no' => $list['communist_no']))."'><i class='fa fa-info-circle'></i>查看</a>";
            $list['excellent'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='".U('ccp_communist_integral_index_operation',array('type'=>'1','communist_no'=>$list['communist_no']))."'>优秀党员</a>";
            $list['talk'] = "<a class='layui-btn layui-btn-del layui-btn-xs' href='".U('ccp_communist_integral_index_operation',array('talk'=>'1','type'=>'1','communist_no'=>$list['communist_no']))."'>纪检约谈</a>";
            $num++;
        }
        $data['data'] = $communist_list;
        return $data;
    }else{
    	return false;
    }
}
/**************************民主评议公共方法开始  2017-10-24添加***********************************/
/**
 * @name getCommentInfo($comment_no,$field='all')
 * @desc  获取当前评议人员详情
 * @param  $comment_no:民主评议编号
 * @param  $field:查询字段
 * @return
 * @author 黄子正
 * @version 版本 V1.0.0
 * @updatetime   2017-10-24 16:31:00
 * @addtime   2017-10-24 16:31:00
 */
function getCommentInfo($comment_no,$field="all"){
    $db_communist=M('ccp_communist_comment');
    $where['status']='1';
    $where['comment_no']=$comment_no;
    if($field == "all"){
        $data=$db_communist->where($where)->find();
    }else{
        $data=$db_communist->where($where)->getField($field);
    }
    return $data;
}
/**
 * @name getCommentSelect($comment_no,)
 * @desc  
 * @author 
 * @version 版本 V1.0.0
 * @updatetime   2017-10-24 16:31:00
 * @addtime   2017-10-24 16:31:00
 */
function getCommentSelect($comment_no){
    $db_communist=M('ccp_communist_comment');
    $type_list=$db_communist->field('comment_no,comment_title')->select();
    $type_options="";
    foreach($type_list as &$type){
    	$selected="";
    	if($comment_no==$type['comment_no']){
    		$selected="selected=true";
    	}
    	$type_options.="<option $selected value='".$type['comment_no']."'>".$type['comment_title']."</option>";
    }
    if(!empty($type_options)){
    	return $type_options;
    }else{
    	return "<option value=''>无数据</option>";
    }
}
/**
 * @name getCommentList()
 * @desc   获取民主评议列表
 * @param   $comment_no  民主评议ID 
 * @param   $comment_title 民主评议标题
 * @param   $party_no  部门编号 
 * @param   $start，$end  时间范围
 * @return
 * @author  王宗彬
 * @version 版本 V1.0.0
 * @addtime   2017-11-13 12:01:00
 * @updatetime   2017-11-13 12:01:00
 */
function getCommentList($comment_no,$comment_title,$party_no,$start,$end,$page,$pagesize){
    $db_communist=M('ccp_communist_comment');
    $where['status'] =  array('eq',"1"); 
    if(!empty($comment_no)){  
    	$where['comment_no'] = array('in',"$comment_no");
    }
    if(!empty($comment_title)){// 标题
      	$where['comment_title'] = array('like',"%$comment_title%");
    }
    if(!empty($party_no)){// 部门
    	$party_no = getPartyChildNos($party_no,'str');
    	$where['party_no'] = array('in',"$party_no");
    }
	if(!empty($start)){  //时间
		$start = $start.' 00:00:00';
    	if(!empty($end)){
    		$end = $end.' 23:59:59';
            $where['comment_date']=array(array('gt',$start),array('lt',$end),'and');
   		}else{
        	$where['comment_date']=array('gt',$start);
    	}
    }else{
        if(!empty($end)){
            $where['comment_date']=array('lt',$end);
        }
    }
    $data['data'] = $db_communist->where($where)->order('comment_date desc')->limit($page,$pagesize)->select();
    $data['count'] = $db_communist->where($where)->count();
    $data['code'] = 0;
    $data['msg'] = "获取数据成功";
    return $data;
}
/**
 * @name getCommentDetailsList($where,$field='all')
 * @desc  获取当前参赛人员详情
 * @param  $where:查询条件
 * @param  $field:查询字段
 * @return
 * @author 黄子正
 * @version 版本 V1.0.0
 * @updatetime   2017-10-24 16:31:00
 * @addtime   2017-10-24 16:31:00
 */
function getCommentDetailsList($where,$field="all"){
    $db_communist=M('ccp_communist_comment_details');
    $where['status']='1';
    if($field == "all"){
        $data=$db_communist->where($where)->select();
    }else{
        $data=$db_communist->where($where)->field($field)->select();
    }
    return $data;
}
/******************************搜集民情********************************* */
/**
 * @name    getSurveyList()
 * @desc    获取民情列表
 * @param
 * @return  民情列表
 * @author  王桥元
 * @version 版本 V1.0.0
 * @time    2017-06-20
 */
function getCommunistConditionList(){
    $db_condition = M(' _communist_condition');
    $condition_data = $db_condition->select();
    if($condition_data){
        return $condition_data;
    } else {
        return false;
    }
}
/**
 * @name    setCommunistConditionList()
 * @desc    民情添加/修改
 * @param   $condition_data  民情数据
 * @return  true/false
 * @author  王桥元
 * @version 版本 V1.0.0
 * @time    2017-06-20
 */
function SaveCondition($condition_data){
    $db_condition = M('hr_communist_condition');
    $condition_data['update_time'] = date("Y-m-d H:i:s");
    if(!empty($condition_data['condition_id'])){
        $condition_id = $condition_data['condition_id'];
        $condition_map['condition_id'] = $condition_id;
        $condition = $db_condition->where($condition_map)->save($condition_data);
    }else{
        $condition_data['add_time'] = date("Y-m-d H:i:s");
        $condition_data['add_staff'] = session('staff_no');
        $condition = $db_condition->add($condition_data);
    }
    if($condition){
        return true;
    } else {
        return false;
    }
}
/**
 * @name    getCommunistConditionInfo()
 * @desc    获取民情列表
 * @param   $condition_id 民情id
 * @return  民情列表
 * @author  王桥元
 * @version 版本 V1.0.0
 * @time   2017-06-20
 */
function getCommunistConditionInfo($condition_id,$field="all"){
    $db_condition = M('hr_communist_condition');
    $condition_map['condition_id'] = $condition_id;
    if($field=="all"){
        $condition_data = $db_condition->where($condition_map)->find();
    }else{
        $condition_data = $db_condition->where($condition_map)->field($field)->find();
    }
    if($condition_data){
        return $condition_data;
    } else {
        return false;
    }
}
/**
 * @name:saveHelpCommunist
 * @desc：保存扶贫对象
 * @param：$help_data 扶贫对象数据
 * @return：true/false
 * @author:王桥元
 * @addtime:2017-06-12
 * @version：V1.0.0
 **/
function saveHelpCommunist($help_data){
	$db_help=M('ccp_communist_help');
	$help_data['update_time'] = date("Y-m-d H:i:s");
	if(!empty($help_data['help_id'])){
		$help_map['help_id'] = $help_data['help_id'];
		$help = $db_help->where($help_map)->save($help_data);
	}else{
	    $help_data['help_name'] =getCommunistInfo($help_data['help_no']);
	    $help_data['status'] =1;
		$help_data['add_time'] = date("Y-m-d H:i:s");
		$help_data['add_staff'] = session('staff_no');
		$help = $db_help->add($help_data);
	}
	if($help){
		return true;
	}else{
		return false;
	}
}
function saveHelpCommunistdata($help_data){
	$db_help=M('ccp_communist_help');
	$help_data['update_time'] = date("Y-m-d H:i:s");
	if(!empty($help_data['help_id'])){
		$help_map['help_id'] = $help_data['help_id'];
		$help = $db_help->where($help_map)->save($help_data);
	}else{
	    $help_data['status'] =1;
		$help_data['add_time'] = date("Y-m-d H:i:s");
		$help_data['add_staff'] = session('staff_no');
		$help = $db_help->add($help_data);
	}
	if($help){
		return true;
	}else{
		return false;
	}
}
/**
 * @name:getHelpList
 * @desc：获取帮扶列表
 * @param：
 * @return：帮扶人员列表
 * @author：王桥元  王宗彬
 * @addtime:2017-06-06
 * updetime：2017-10-23
 * @version：V1.0.0
 **/
function getHelpList($help_name){
	$map['status'] = array('neq',0);
	if($help_name){
		$map['help_name'] = array('like',"%$help_name%");
	}
	$db_help = M("ccp_communist_help");
	$help_data = $db_help->where($map)->order('add_time DESC')->select();
    return $help_data;
}
/**
 * @name:getHelpInfo
 * @desc：获取帮扶详情
 * @param：$help_id 帮扶id	$field 查询字段
 * @return：帮扶人员详情
 * @author：王桥元
 * @addtime:2017-06-12
 * @version：V1.0.0
 **/
function getHelpInfo($help_id,$field="all"){
	$db_help = M("ccp_communist_help");
	$help_map['help_id'] = $help_id;
	if($field == "all"){
		$help_data = $db_help->where($help_map)->find();
	}else{
		$help_data = $db_help->where($help_map)->getfield($field);
	}
	return $help_data;
}
/**
 * @name:getCommunistHelpList
 * @desc：获取帮扶列表
 * @param：$communist_no 党员编号
 * @return：帮扶人员列表
 * @author：王桥元 王玮琪
 * @addtime:2017-06-06
 * @updetime:2017-07-13
 * @version：V1.0.0
 **/
function getCommunistHelpList($communist_no=""){
	$db_help = M("ccp_communist_help");
	if(!empty($communist_no)){
		$where['_string']='FIND_IN_SET("'.$communist_no.'", communist_no)';
		$help_communist = $db_help->where($where)->select();
	}else{
		$help_communist = $db_help->where("status <> 0")->group("communist_no")->select();
	}
	return $help_communist;
}
/**
 * @name  getSecretaryList()
 * @desc  获取第一书记列表
 * @param $communist_no 编号
 * @param $secretary_type  第一数据/双联双创
 * @return int
 * @author 王宗彬
 * @version 版本 V1.0.0
 * @addtime   2017-11-14
 */
function getSecretaryList($communist_no,$secretary_type,$page,$pagesize){
    $ccp_secretary = M('ccp_secretary');
    $where['status'] = array('eq',1);
	if(!empty($secretary_type)){
		$where['secretary_type'] = array('eq',"$secretary_type");  //区别第一书记和双联双创
	} 
    $secretary_list['data'] = $ccp_secretary->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
    $secretary_list['count'] = $ccp_secretary->where($where)->count();
    $secretary_list['code'] = 0;
    $secretary_list['msg'] = 0;
    return $secretary_list;
}

/**
 * @name  getpartydayList()
 * @desc  获取党日计划列表
 * @param  $partyday_title  标题
 * @param  $start  开始时间
 * @param  $end  结束时间
 * @param  $end  结束时间
 * @return $time 活动时间
 * @author 杨凯
 * @version 版本 V1.0.0
 * @addtime   2017-12-18
 */
function getpartydayList($partyday_title,$start,$end,$approval_staff='all',$status='all',$time,$page,$pagesize){
	$partyday = M('ccp_partyday_plan');
	if ($approval_staff != 'all') {
		$where['add_staff'] = array('eq',$approval_staff);
		$where['approval_staff'] = array('eq',$approval_staff);
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
	}
	if ($status!='all') {
		$map['status'] = array('eq',$status);
	}
	if(!empty($partyday_title)){
		$map['partyday_title'] = array('like',"%$partyday_title%");
	}
	if(!empty($time)){
		$map['partyday_time'] = array('like',"%$time%");
	}
	if(!empty($start) && !empty($end)){
		$end=$end." 23:59:59";
		$map['partyday_time'] = array('between',array("$start","$end"));
	}
	if(!empty($pagesize)){
		$partyday_list = $partyday->where($map)->limit($page,$pagesize)->order('add_time desc')->select();
		$count = $partyday->where($map)->count();
		$arr['data'] = $partyday_list;
		$arr['count'] = $count;
		return $arr;
	}else{
		$partyday_list = $partyday->where($map)->select();
		return $partyday_list;
	}
	
	
}
/**
 * @name  getpartydayInfo()
 * @desc  获取党日计划详情
 * @param unknown $work_id
 * @return 返回详情
 */
function getpartydayInfo($partyday_id){
	$partyday_bd = M('ccp_partyday_plan');
	if(!empty($partyday_id)){
		$map['partyday_id'] = array('eq',$partyday_id);
	}
	$partyday_list = $partyday_bd->where($map)->find();
	return $partyday_list;
}
/**
 * @name  getpartydaylogInfo()
 * @desc  获取党日计划跟踪详情
 * @param unknown $work_id
 * @return 返回详情
 */
function getpartydaylogInfo($partyday_id,$reveal,$partyday_log_id){
	$partyday_bd = M('ccp_partyday_plan_log');
	if(!empty($partyday_id)){
		if (strpos($partyday_id,',')) {
			$map['partyday_id'] = array('in',$partyday_id);
		}else {
			$map['partyday_id'] = array('eq',$partyday_id);
		}
	}
	if(!empty($partyday_log_id)){
		$map['partyday_log_id'] = array('eq',$partyday_log_id);
	}
	if(!empty($reveal)){
		$map['reveal'] = array('eq',$reveal);
	}
	$partyday_list = $partyday_bd->where($map)->order('add_time desc')->select();
	return $partyday_list;
}
/**
 * @name  getpartydaycheckInfo()
 * @desc  获取党日计划是否填写
 * @param unknown $work_id
 * @return 返回详情
 */
function getpartydaycheckInfo($partyday_id,$party_no,$partyday_log_id){
	$partyday_bd = M('ccp_partyday_plan_log');
	if(!empty($partyday_id)){
		$map['partyday_id'] = array('eq',$partyday_id);
	}
	if(!empty($party_no)){
		$map['party_no'] = array('eq',$party_no);
	}
	if(!empty($partyday_log_id)){
		$map['partyday_log_id'] = array('eq',$partyday_log_id);
	}
	$partyday_list = $partyday_bd->where($map)->find();
	if(empty($partyday_list)){
		return false;
	}else {
		return $partyday_list;
	}
}

/**
 * @name  getPlanRevealInfo()
 * @desc  获取计划汇总展示信息
 * @param  $reveal_date 时间查询
 * @param unknown $work_id
 * @return 返回详情
 */
function getPlanRevealInfo($reveal_date){
	$cust_reveal = M('cust_reveal');
	if(!empty($reveal_date)){
		$map['reveal_date'] = array('eq',$reveal_date);
	}
	$reveal_list = $cust_reveal->where($map)->find();
	if(empty($reveal_list)){
		return false;
	}else {
		return $reveal_list;
	}
}
/********************************党员,党组织相关基础层方法 结束*************************************/

/********************************党员,党组织相关业务层方法 开始*************************************/
/**
 * @name  	callsaveCommunist()
 * @desc  党员发展全纪实回掉方法
 * @param $communist_no(员工编号)
 * @param $status(状态)
 * @param $communist_no(员工编号)
 * @param $log_attach 附件
 * @return 部门列表
 * @author 王宗彬
 * @version 版本 V1.0.0
 * @addtime   2017-12-26
 */
function callsaveCommunist($communist_no,$current_status,$log_attach,$auditor_no,$status,$node_staff){
	if(!empty($log_attach)){
		$log_attach =  str_replace('_',',',$log_attach);  //把_分割改成 ，分割
	}
	$db_communist = M('ccp_communist');
	if ($status == 11) {
		$alert_title = "你有".getCommunistInfo($communist_no)."的一条发展状态要审核";
		$alert_url = "Ccp/Ccpcommunist/ccp_communist_info/communist_no/".$communist_no."/type/3";
	    saveAlertMsg('11',$node_staff,$alert_url, $alert_title,'','', '', $auditor_no);
	}else if($status == 12){
		$alert_title = "你有".getCommunistInfo($communist_no)."的一条发展状态要审核";
		$alert_url = "Ccp/Ccpcommunist/ccp_communist_info/communist_no/".$communist_no."/type/3";
	    saveAlertMsg('11',$node_staff,$alert_url, $alert_title,'','', '', $auditor_no);
	}else if($status == 21){
		if($current_status == 17){
			$data['communist_ccp_date'] = date('Y-m-d H:i:s');
			$db_communist->where("communist_no='$communist_no'")->save($data);
		}else{
			$list = $db_communist->where("communist_no='$communist_no'")->find();
			saveCommunistLog($communist_no,10,$list['status'],$auditor_no,'','',$log_attach);
		}
	}else if ($status == 31){
		if(empty($current_status)){
			$data['status'] = '11';
		}else{
			$data['status'] = $current_status;
		}
		$db_communist->where("communist_no='$communist_no'")->save($data);
	}else{
		return false;
	}
}

/**
 * @name  updateIntegral()
 * @desc  获取积分变动情况
 * @param $change_type 1 党员 2 党支部
 * @param $update_type 7 增加 8 减少
 * @param $integral_relation_no 关系编号
 * @param $integral_num 现有积分数
 * @param $change_integral_num 变更积分数
 * @param $integral_reason 变更原因
 * @author ljj
 * @version 版本 V1.0.0
 * @addtime   2018-8-12
 */
function updateIntegral($change_type,$update_type,$integral_relation_no,$integral_num,$change_integral_num,$integral_reason,$cause){	
	if($change_type == 1){
		if($update_type == 7){
			$integral_data['communist_integral'] = (float)$integral_num + (float)$change_integral_num;
		} else if($update_type == 8){
			$integral_data['communist_integral'] = (float)$integral_num - (float)$change_integral_num;
		}
		if($integral_data['communist_integral'] < 0){
			$integral_data['communist_integral'] = 0;
		}
		$integral_map['communist_no'] = $integral_relation_no;
		M('ccp_communist')->where($integral_map)->save($integral_data);
		saveIntegralLog($change_type,$integral_relation_no,$change_integral_num,$update_type,$integral_reason,$cause);//增加流水记录
	} else if($change_type == 2){
		if($update_type == 7){
			$integral_data['party_integral'] = (float)$integral_num + (float)$change_integral_num;
		} else if($update_type == 8){
			$integral_data['party_integral'] = (float)$integral_num - (float)$change_integral_num;
		}
		if($integral_data['party_integral'] < 0){
			$integral_data['party_integral'] = 0;
		}
		$integral_map['party_no'] = $integral_relation_no;
		M('ccp_party')->where($integral_map)->save($integral_data);
		saveIntegralLog($change_type,$integral_relation_no,$change_integral_num,$update_type,$integral_reason,$cause);//增加流水记录
	}
}

/**
 * @name  saveIntegralLog()
 * @desc  保存积分变动情况
 * @param $log_relation_type 1 党员 2 党支部
 * @param log_relation_no 关系编号
 * @param $change_integral 变动积分数
 * @param $change_type 变动类型 7 增加 8 减少
 * @param $memo 变动原因
 * @author ljj
 * @version 版本 V1.0.0·
 * @addtime    2018-08-10
 * @update_time  2018-08-10
 */
 function saveIntegralLog($log_relation_type,$log_relation_no,$change_integral,$change_type,$memo,$cause){
	$integral_log=M("ccp_integral_log");
	$inte['log_relation_type']=$log_relation_type; // 关系类型
	$inte['log_relation_no']=$log_relation_no; // 关系编号
	$inte['change_integral']=$change_integral; // 变更积分数
	$inte['change_type']=$change_type; // 变更类型
	$inte['memo']=$memo; // 变更原因
	$inte['update_time'] =date("Y-m-d H:i:s");
	$inte['add_time']=date("Y-m-d H:i:s");
	$inte['status']=1;
	$inte['add_staff']=session('staff_no');
	$inte['year']=date('Y'); // 年
	$inte['month']=date('m');// 月
	$inte['cause'] = $cause;
	$flag=$integral_log->add($inte);
	return $flag;
}
/********************************党员,党组织相关业务层方法 结束***********************************/

/**
 * @name  savePeople()
 * @desc  党员表非党员表统计
 * @param $people_no 党员非党员编号
 * @param people_name 姓名
 * @param $people_card 身份证
 * @param $status 1党员 2工作人员 3工作人员党员  4群众
 * @author 刘长军
 * @version 版本 V1.0.0·
 * @addtime    2019-02-25
 * @update_time  2019-02-25
 */
 function savePeople($people_no,$people_name,$people_card,$status){
	if(!empty($people_no)){
		$where['people_no'] = $people_no;
	}		
	if(!empty($people_card)){
		$where['people_card'] = $people_card;
	}	
	$res = M('ccp_people')->where($where)->find();
	if(!empty($res)){
		$data['people_no'] = $people_no;
		$data['people_name'] = $people_name;
		$data['people_card'] = $people_card;
		$data['status'] = $status;
		$data['add_time'] = date('Y-m-d H:i:s');
		$data['update_time'] = date('Y-m-d H:i:s');
		$data['add_staff'] = session('staff_no');
		$people = M('ccp_people')->where($where)->save($data);
	}else{
		$data['people_no'] = $people_no;
		$data['people_name'] = $people_name;
		$data['people_card'] = $people_card;
		$data['status'] = $status;
		$data['add_time'] = date('Y-m-d H:i:s');
		$data['update_time'] = date('Y-m-d H:i:s');
		$data['add_staff'] = session('staff_no');
		$people = M('ccp_people')->add($data);
	}
    if($people){
    	return true;
    }else{
    	return false;
    }
 }
 /**
 * @name  peopleNo()
 * @desc  通过党员编号获取people_ID
 * @param $no 党员/非党员编号
 * @param $type 1党员 2工作人员 3工作人员党员  4群众
 * @author 刘长军
 * @version 版本 V1.0.0·
 * @addtime    2018-08-10
 * @update_time  2018-08-10
 */
function peopleNo($no,$type){
	$map_people['people_no'] = $no;
	$map_people['status'] = $type;
    $people_id = M('ccp_people')->where($map_people)->getField('people_id');
    if($people_id){
    	return $people_id;
    }else{
    	return false;
    }
}
 /**
 * @name  peopleNoName()
 * @desc  党员表非党员表统计
 * @param $people_id 
 * @author 刘长军
 * @version 版本 V1.0.0·
 * @addtime    2018-08-10
 * @update_time  2018-08-10
 */
function peopleNoName($people_id){
 	$where['people_id'] = $people_id;
    $people_name = M('ccp_people')->where($where)->getField('people_name');
    if($people_name){
    	return $people_name;
    }else{
    	return false;
    }
 }
 
 
 function getPartyFreeStatus($party_free_status_arr){
	 
   foreach ($party_free_status_arr as &$party_free_status){
		$selected="";
		
		$party_free_status_string.="<option   value='".$party_free_status['sp_party_free_status_id']."'>".$party_free_status['sp_party_free_status']."</option>";
	}
	
	return $party_free_status_string;
}
?>