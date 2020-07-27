<?php
/***********************************不合格党员,软弱涣散党组织*********************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
class CcpfailController extends BaseController{
	

	/**
	 * @name:ccp_party_fail_index
	 * @desc：软弱涣散党组织
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-02-10
	 * @version：V1.0.0
	 **/
	public function ccp_party_fail_index(){
		checkAuth(ACTION_NAME);
		//$party_list = checkDataAuth(session("staff_no"),"is_admin",1,"arr");
		$where['party_no'] = array('in',session('party_no_auth'));
		$party_list = M('ccp_party')->where($where)->select();
		$this->assign('party_list',$party_list);
		$week = date('W',strtotime(date('Y-m-d H:i:s')));
		$res = $this->weekday(date(Y),$week);
		$this->display('Ccpfail/ccp_party_fail_index');
	}
	/**
	 * @name:ccp_party_fail_index_data
	 * @desc：软弱涣散党组织表格数据加载
	 * @param：
	 * @return：
	 * @author：王宗彬 
	 * @addtime:2018-02-10
	 * @version：V1.0.0
	 **/
	public function ccp_party_fail_index_data(){
		$oa_meeting=M("oa_meeting"); //会议
		$life_volunteer = M('life_volunteer_activity');//活动
		$integral_log = M('ccp_integral_log');//积分
		
		$party_no=I('get.party_no');//点击的党支部编号
		$party_name = I('get.party_name');
		$volunteer_sum = getConfig('party_volunteer');
		$meeting_sum = getConfig('party_meeting');
		$integral_sum = getConfig('party_integral');
	
		$party_method = getConfig('party_method');
		switch ($party_method) {
			case 1:   //周
				$week = date('W',strtotime(date('Y-m-d H:i:s')));
				$res = $this->weekday(date(Y),$week);
				$start = date("Y-m-d H:i:s",$res['start']);
				$end =  date("Y-m-d 23:59:59",$res['end']);
				;break;
			case 2:   //月
				$start = date('Y-m-01 00:00:00');
				$end = date('Y-m-t 23:59:59');
				;break;
			case 3:   //季度
				$season = ceil(date('n') /3); //获取季度
				$start = date('Y-m-01 H:i:s',mktime(0,0,0,($season - 1) *3 +1,1,date('Y')));
				$end = date('Y-m-t 23:59:59',mktime(0,0,0,$season * 3,1,date('Y')));
				;break;
			case 4:   //半年
				$season = ceil(date('n') /6);
				$start = date('Y-m-01 H:i:s',mktime(0,0,0,($season - 1) *6 +1,1,date('Y')));
				$end = date('Y-m-t 23:59:59',mktime(0,0,0,$season * 6,1,date('Y')));
				;break;
			default:  //一年
				$start = date("Y-01-01 00:00:00");
				$end =  date("Y-12-31 23:59:59");
				;break;
		}
		if(!empty($party_name)){
			$where['party_name'] = array('like',"%$party_name%");
		}
		if(!empty($party_no)){
			$party_nos = getPartyChildNos($party_no);
			$where['party_no'] = array('in',"$party_nos");
		}else{
			$where['party_no'] = array('in',session('party_no_auth'));
		}
		$party_list = M('ccp_party')->field('party_no,party_name,party_pno,party_manager')->where($where)->order('add_time desc')->select(); 
		$un_data = array();
		$i=0;
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
		foreach ($party_list as $k=>&$list) {
			//会议
			$map['meeting_real_end_time'] =  array(array('egt',$start),array('elt',$end),'AND');
			$map['status'] = array('eq',23);
			$map['_string'] = 'FIND_IN_SET('.$list['party_no'].',party_no)';
			$att_meeting_index=$oa_meeting->where($map)->count();
			$list['meeting_count'] = $att_meeting_index.'/'.$meeting_sum;
			//活动
			$where['activity_endtime'] = array(array('egt',$start),array('elt',$end),'AND');
			$where['_string'] = 'FIND_IN_SET('.$list['party_no'].',party_no)';
			$life_activity = $life_volunteer->where($where)->count();
			$list['volunteer_sum'] = $life_activity.'/'.$volunteer_sum;
			//积分
			$res['add_time'] = array(array('egt',$start),array('elt',$end),'AND');
			$res['_string'] = 'FIND_IN_SET('.$list['party_no'].',log_relation_no)';
			$integral = $integral_log->where($res)->sum('change_integral');
			if(empty($integral)){
				$integral = '0';
			}
			$list['integral_sum'] = $integral.'/'.$integral_sum;
			if($att_meeting_index < $meeting_sum || $life_activity < $volunteer_sum || $integral < $integral_sum){
				$un_data[$i]['party_check'] = "<input type='checkbox' name='party_no' value='" .$list['party_no']."'>";
				$un_data[$i]['party_no'] = $list['party_no'];
				$un_data[$i]['party_name'] = $list['party_name'];
				$un_data[$i]['party_pno'] = $party_name_arr[$list['party_pno']];
				$un_data[$i]['party_manager'] = getStaffInfo($list['party_manager']);
				$un_data[$i]['meeting_count'] = $list['meeting_count'];
				$un_data[$i]['volunteer_sum'] = $list['volunteer_sum'];
				$un_data[$i]['integral_sum'] = $list['integral_sum'];
				$un_data[$i]['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('Ccp/Ccpcommunist/ccp_party_index', array('party_no' => $list['party_no'])) . "'>详情</a>";
				$i++;
			}
		}
		$un_data_arr['data'] = $un_data;
		$un_data_arr['count'] = $i;
		$un_data_arr['code'] = 0;
		ob_clean();$this->ajaxReturn($un_data_arr);
	}
	
	
	
	
	/**
	 * @name:ccp_communist_fail_index
	 * @desc：不合格党员
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-02-10
	 * @version：V1.0.0
	 **/
	public function ccp_communist_fail_index(){
		checkAuth(ACTION_NAME);
		//$party_list = checkDataAuth(session("staff_no"),"is_admin",1,"arr");
		$where['party_no'] = array('in',session('party_no_auth'));
		$party_list = M('ccp_party')->where($where)->select();
		$this->assign('party_list',$party_list);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display('Ccpfail/ccp_communist_fail_index');
	}
	/**
	 * @name:ccp_communist_fail_index_data
	 * @desc：不合格党员表格数据加载
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-02-10
	 * @version：V1.0.0
	 **/
	public function ccp_communist_fail_index_data(){
		$oa_meeting=M("oa_meeting"); //会议
		$life_volunteer = M('life_volunteer_activity');//活动
		$integral_log = M('ccp_integral_log');//积分
	
		$party_no=I('get.party_no');//点击的党支部编号
		
		$communist_name=I('get.communist_name');
		$status=COMMUNIST_STATUS_OFFICIAL;//只查询正式党员
		$communist_list = getCommunistList($party_no,'arr','1',$post_no,$status,$communist_name,$communist_no,'',$communist_diploma,$communist_sex,$start,$end,$phone,'',$communist_no);
		$volunteer_sum = getConfig('communist_volunteer');
		$meeting_sum = getConfig('communist_meeting');
		$integral_sum = getConfig('communist_integral');
		
		$communist_method = getConfig('communist_method');
		switch ($communist_method) {
			case 1:   //周
				$week = date('W',strtotime(date('Y-m-d H:i:s')));
				$res = $this->weekday(date(Y),$week);
				$start = date("Y-m-d H:i:s",$res['start']);
				$end =  date("Y-m-d 23:59:59",$res['end']);
				;break;
			case 2:   //月
				$start = date('Y-m-01 00:00:00');
				$end = date('Y-m-t 23:59:59');
				;break;
			case 3:   //季度
				$season = ceil(date('n') /3); //获取季度
				$start = date('Y-m-01 H:i:s',mktime(0,0,0,($season - 1) *3 +1,1,date('Y')));
				$end = date('Y-m-t 23:59:59',mktime(0,0,0,$season * 3,1,date('Y'))); 
				;break;
			case 4:   //半年
				$season = ceil(date('n') /6); //获取季度
				$start = date('Y-m-01 H:i:s',mktime(0,0,0,($season - 1) *6 +1,1,date('Y')));
				$end = date('Y-m-t 23:59:59',mktime(0,0,0,$season * 6,1,date('Y'))); 
				;break;
			default:  //一年
				$start = date("Y-01-01 00:00:00");
				$end =  date("Y-12-31 23:59:59");
				;break;
		}
		//会议
		$map['meeting_real_end_time'] =  array(array('egt',$start),array('elt',$end),'AND');
		$map['status'] = array('eq',23);
		$att_meeting_index=$oa_meeting->where($map)->field('meeting_no')->select();
		$att_meeting_index = arrToStr(array_column($att_meeting_index,'meeting_no'), ',');
		//活动
		$activity['activity_endtime'] = array(array('egt',$start),array('elt',$end),'AND');
		$life_activity = $life_volunteer->where($activity)->field('activity_id')->select();
		$life_activity = arrToStr(array_column($life_activity,'activity_id'), ',');
		
		$communist_list['count'];
		$meeting_communist = M('oa_meeting_communist');//会议参加人员流水
		$life_volunteer_activity = 	M('life_volunteer_activity_apply');  //志愿者申请表
		$data = array();
		$i=0;
		
		foreach ($communist_list as $k=>&$communist){
			//三会一课
			$where['communist_no'] = array('eq',$communist['communist_no']);
			$where['meeting_no'] = array('in',$att_meeting_index);
			$meeting = $meeting_communist->where($where)->count();
  			$communist['meeting_count'] = $meeting.'/'.$meeting_sum;
// 			//活动
			$life['communist_no'] = array('eq',$communist['communist_no']);
			$life['activity_id'] = array('in',$life_activity);
			$life['status'] = array('eq',2);  //审核通过的
 			$life_activity_sum = $life_volunteer_activity->where($life)->count();
 			$communist['volunteer_sum'] = $life_activity_sum.'/'.$volunteer_sum;
// 			//积分
			$res['add_time'] = array(array('egt',$start),array('elt',$end),'AND');
			$res['_string'] = 'FIND_IN_SET('.$communist['communist_no'].',log_relation_no)';
			$integral = $integral_log->where($res)->sum('change_integral');
			if(empty($integral)){
				$integral = '0';
			}
			$communist['integral_sum'] = $integral;
			if($meeting < $meeting_sum || $life_activity_sum < $volunteer_sum || $integral < $integral_sum){
				
				$data[$i]['communist_check'] = "<input type='checkbox' name='communist_no' value='" .$communist['communist_no']."'>";
				$data[$i]['communist_no'] = $communist['communist_no'];
				$data[$i]['communist_name'] = $communist['communist_name'];
				$data[$i]['communist_birthday'] = $communist['communist_birthday'];
				$data[$i]['communist_paddress'] = $communist['communist_paddress'];
				$data[$i]['communist_mobile'] = $communist['communist_mobile'];
				$data[$i]['volunteer_count'] = $communist['meeting_count'];
				$data[$i]['meeting_count'] = $communist['volunteer_sum'];
				$data[$i]['integral'] = $communist['integral_sum'];
				$data[$i]['communist_ccp_date'] = $communist['communist_ccp_date'];
				$data[$i]['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('Ccp/Ccpcommunist/ccp_communist_info', array('communist_no' => $communist['communist_no'])) . "'>详情</a>";
				$i++;
			}
			
		}
		$un_data_arr['data'] = $data;
		$un_data_arr['count'] = $i;
		$un_data_arr['code'] = 0;
		ob_clean();$this->ajaxReturn($un_data_arr);
	}
	
	
	
	/**
	 * 获取某年第几周的开始日期和结束日期
	 * @param int $year
	 * @param int $week 第几周;
	 */
	public function weekday($year,$week=1){
		$year_start = mktime(0,0,0,1,1,$year);
		$year_end = mktime(0,0,0,12,31,$year);
		 
		// 判断第一天是否为第一周的开始
		if (intval(date('W',$year_start))===1){
			$start = $year_start;//把第一天做为第一周的开始
		}else{
			$week++;
			$start = strtotime('+1 monday',$year_start);//把第一个周一作为开始
		}
		 
		// 第几周的开始时间
		if ($week===1){
			$weekday['start'] = $start;
		}else{
			$weekday['start'] = strtotime('+'.($week-0).' monday',$start);
		}
		 
		// 第几周的结束时间
		$weekday['end'] = strtotime('+1 sunday',$weekday['start']);
		if (date('Y',$weekday['end'])!=$year){
			$weekday['end'] = $year_end;
		}
		return $weekday;
	}
	/**
	 * @name:ccp_notice_fail
	 * @desc：通知
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-02-23
	 * @version：V1.0.0
	 **/
	public function ccp_notice_fail(){
		$type = I('get.type');
		if($type == '1'){
			$party_no = I('get.party_no');
			$party_no = strToArr($party_no, ',');
			
			$party_name_arr = M('ccp_party')->getField('party_no,party_name');
			$party_manager_arr = M('ccp_party')->getField('party_no,party_manager');
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			foreach ($party_no as &$party){
			    $communist_no = $party_manager_arr[$party];
				$alert_url = "/fail/_party_fail_index";
				$alert_title = $party_name_arr[$party]."成为软弱涣散组织";
				saveAlertMsg(52, $communist_no, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('communist_no'));
			}
			$url = U('ccp_party_fail_index');
		}else {
			$alert_man = I('get.communist_no');
			$alert_url = "/fail/_communist_fail_index";
			$whh['communist_no'] = array('in',$alert_man);
			$commun_name = M('ccp_communist')->where($whh)->getField('communist_name',true);
			$commun_name_str = implode(',',$commun_name);
			$alert_title = $commun_name_str."成为不合格党员";
			saveAlertMsg(51, $alert_man, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('communist_no'));
			$url = U('ccp_communist_fail_index');
		}
		showMsg(success, '通知成功！',$url);
	}
	
}
