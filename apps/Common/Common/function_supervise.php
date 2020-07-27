<?php
/********************************优秀党员，纪检约谈基础层方法 开始*************************************/

/**
 * 获取优秀党员党组织数据
 * getSuperviseOutstandingList
 * @param number $communist_no 查询人id
 * @param number $page 分页数量
 * @param number $count 每页数量
 * @param unknown $start_time 起始时间
 * @param unknown $end_time 结束时间
 * @param unknown $type 类型1为党员2为党组织
 * @return unknown 返回：返回二维数组包含分页数组和查询出的数据数组
 * @author：杨凯
 * @addtime:2017-12-7
 */
function getSuperviseOutstandingList($communist_no=0,$page=0,$count=10,$start_time,$end_time,$type){
	$supervise_outstanding = M('supervise_outstanding');
	if(!empty($type)){
		$where['outstanding_type'] = $type;
	}
	if(!empty($start_time) && !empty($end_time)){
		$start_time = $start_time.' 00:00:00';
		$end_time = $end_time.' 23:59:59';
		$where['add_time']= array(array('egt',$start_time),array('elt',$end_time));
	}
	if (!empty($communist_no)) {
		$where['communist_no']= $communist_no;
	}
	$count_zong = $supervise_outstanding->where($where)->count();
	$supervise_list = $supervise_outstanding->where($where)->limit($page,$count)->order('add_time desc')->select();
	$data['data'] = $supervise_list;
	$data['count'] = $count_zong;
	$data['code'] = 0;
	$data['msg'] = "获取数据成功";
	return $data;

	
}
/**
 * 添加优秀党员/党组织
 * @param number $add_no 添加的编号
 * @param unknown $add_staff 添加人
 * @param unknown $type 类型党员为1，党组织为2
 * @author：杨凯
 * @addtime:2017-12-7
 */
function setSuperviseOutstandingInfo($add_no,$add_staff,$type=1){
	$supervise_outstanding = M('supervise_outstanding');
	$month = date("m");
	$where['month']= $month;
	if($type==1){
		$where ['communist_no']= $add_no;
		$add_id = $supervise_outstanding->where($where)->find();
		$status = $supervise_outstanding->where("communist_no = $add_no")->field('status')->select();
		$outstanding_data['communist_no'] = $add_no;
	}else {
		$where['party_no']= $add_no;
		$add_id = $supervise_outstanding->where($where)->find();
		$status = $supervise_outstanding->where("party_no = $add_no")->field('status')->select();
		$outstanding_data['party_no'] = $add_no;
	}
	$status = implode(',', array_column($status,'status'));
	if (empty($add_id)) {
		if(substr_count($status,'0')){
			return '之前月份存在没有审核，亲审核后再添加！';
		}else{
			$outstanding_data['outstanding_type'] = $type;
			$outstanding_data['add_staff'] = $add_staff;
			$outstanding_data['add_time'] = date("Y-m-d H:i:s");
			$outstanding_data['month'] = date("m");
			$supervise_list = $supervise_outstanding->add($outstanding_data);
			return '添加成功';
		}
	}else {
		return '本月已有数据！';
	}			
}
/************************************  约谈 *****************************************************/

/**
 * 获取约谈党员党组织数据
 * getSuperviseChatList
 * @param number $chat_id 查询id
 * @param number $page 分页数量
 * @param number $count 每页数量
 * @param unknown $start_time 起始时间
 * @param unknown $end_time 结束时间
 * @param unknown $type 类型1为党员2为党组织
 * @return unknown 返回：返回二维数组包含分页数组和查询出的数据数组
 * @author：杨凯
 * @addtime:2017-12-7
 */
function getSuperviseChatList($chat_id=0,$page=0,$count=10,$start_time,$end_time,$type){
	$supervise_chat = M('supervise_chat');
	if (!empty($type)) {
		$where['chat_type']= $type;
	}
	if(!empty($start_time) && !empty($end_time)){
		$start_time = $start_time.' 00:00:00';
		$end_time = $end_time.' 23:59:59';
		$where['add_time']= array(array('egt',$start_time),array('elt',$end_time));
	}
	if (!empty($chat_id)) {
		$where['chat_id']= $chat_id;
		$supervise_list = $supervise_chat->where($where)->find();
		return $supervise_list;
	}else {
		$count_zong = $supervise_chat->where($where)->limit($page,$count)->count();
		$supervise_list = $supervise_chat->where($where)->order('add_time desc')->limit($page,$count)->select();
		if (!empty($supervise_list)) {
			$data['data'] = $supervise_list;
			$data['count'] = $count_zong;
			return $data;
		}else {
			return '';
		}
	}
	

}

/**
 * 添加约谈党员/党组织
 * @param number $add_no 添加编号
 * @param unknown $add_staff 添加人
 * @param unknown $type 类型党员为1，党组织为2
 * @author：杨凯
 * @addtime:2017-12-7
 */
function setSuperviseChatInfo($add_no,$add_staff,$type=1){
	$supervise_chat = M('supervise_chat');
	$where['status']= '0';
	if ($type==1) {
		$where['communist_no']= $add_no;
		$add_id = $supervise_chat->where($where)->find();
		$outstanding_data['communist_no'] = $add_no;
	}else {
		$where['party_no']= $add_no;
		$add_id = $supervise_chat->where($where)->find();
		$outstanding_data['party_no'] = $add_no;
		$outstanding_data['communist_no'] = getPartyInfo($add_no,'party_manager');
	}
	if (empty($add_id)) {
		$outstanding_data['chat_type'] = $type;
		$outstanding_data['add_staff'] = $add_staff;
		$outstanding_data['add_time'] = date("Y-m-d H:i:s");
		$supervise_list = $supervise_chat->add($outstanding_data);
		return '申请约谈成功';
	}else {
		return '已有数据正在等待预约！';
	}
}



/**
 * 获取案件数据
 * getSuperviseCaseList
 * @param number case_id 案件id
 * @param number $page 分页数量
 * @param number $count 每页数量
 * @param unknown $start_time 起始时间
 * @param unknown $end_time 结束时间
 * @return unknown 返回：返回二维数组包含分页数组和查询出的数据数组
 * @author：杨凯
 * @addtime:2017-12-7
 */
function getSuperviseCaseList($case_id=0,$page=0,$count=10,$start_time,$end_time,$status='all',$keywords){
	$supervise_case = M('supervise_case');
	if ($status !='all') {
		$where['status'] = $status;
	}
	if($keywords){
		$where['_string'] ="  (case_title like '%$keywords%' or case_content like '%$keywords%' or case_situation like '%$keywords%')";
	}
	if(!empty($start_time) && !empty($end_time)){
		$start_time = $start_time.' 00:00:00';
		$end_time = $end_time.' 23:59:59';
		$where['add_time']= array(array('egt',$start_time),array('elt',$end_time));
	}
	if (!empty($case_id)) {
		$where['case_id'] = $case_id;
		$supervise_list = $supervise_case->where($where)->find();
		return $supervise_list;
	}else {
		$count_zong = $supervise_case->where($where)->limit($page,$count)->count();
		$supervise_list = $supervise_case->where($where)->limit($page,$count)->order('add_time desc')->select();
		
		if (!empty($supervise_list)) {
			$data['data'] = $supervise_list;
			$data['count'] = $count_zong;
			return $data;
		}else {
			return '';
		}
	}
	

}
/**
 * 获取案件人员数据
 * getSuperviseCaseList
 * @param number case_id 案件id
 * @param number $page 分页数量
 * @param number $count 每页数量
 * @param unknown $start_time 起始时间
 * @param unknown $end_time 结束时间
 * @return unknown 返回：返回二维数组包含分页数组和查询出的数据数组
 * @author：杨凯
 * @addtime:2017-12-7
 */
function getSuperviseCaselogList($case_id=0,$page=0,$count=10,$start_time,$end_time,$status='all'){
	$supervise_case_log = M('supervise_case_log');
	if ($status !='all') {
		$where['status'] =$status;
	}
	if(!empty($keywords)){
		$where['_string'] = "(article_content like '%$keywords%' or article_title like '%$keywords%' or article_keyword like '%$keywords%')";
	}
	if(!empty($start_time) && !empty($end_time)){
		$start_time = $start_time.' 00:00:00';
		$end_time = $end_time.' 23:59:59';
		$where['add_time']= array(array('egt',$start_time),array('elt',$end_time));
	}
	if (!empty($case_id)) {
		$where['case_id'] =$case_id;
		$supervise_list = $supervise_case_log->where($where)->order('add_time desc')->find();
		return $supervise_list;
	}else {
		$count_zong = $supervise_case_log->where($where)->count();
		$supervise_list = $supervise_case_log->where($where)->order('add_time desc')->limit($page,$count)->select();
		if (!empty($supervise_list)) {
			$data['data'] = $supervise_list;
			$data['count'] = $count_zong;
			return $data;
		}else {
			return '';
		}
	}


}
/**
 * 获取案件人员数据详情
 * getIaselogInfo
 * @param number caid 案件id
 * @param unknown $field 需要查询的字段,支持逗号隔开的字符串
 * @return unknown 返回：返回二维数组包含分页数组和查询出的数据数组
 * @author：杨凯
 * @addtime:2017-12-7
 */
function getIaselogInfo($ceid,$field = "all"){
	$supervise_case_log = M('supervise_case_log');
	if (!empty($ceid)) {
		$where['ce_id'] = $ceid;
		if ($field == "all") {
			$supervise_info = $supervise_case_log->where($where)->find();
		}else {
			$supervise_info = $supervise_case_log->where($where)->getField($field);
		}
	}
	if (!empty($supervise_info)) {
		return $supervise_info;
	}else {
		return false;
	}	
}

/********************************优秀党员，纪检约谈基础层方法 结束*************************************/

/********************************优秀党员，纪检约谈业务层方法 结束*************************************/



/********************************优秀党员，纪检约谈业务层方法 结束*************************************/



?>