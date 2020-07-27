<?php
/*************************志愿者服务************************************************/
namespace Life\Controller;
use Common\Controller\BaseController;
use Life\Model\LifeVolunteerModel;
use Life\Model\LifeVolunteerActivityModel;
class LifevolunteerController extends BaseController{
	 /**
	 * @name:life_volunteer_index
	 * @desc：志愿者列表
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-06-14
	 * @version：V1.0.0
	 **/
	public function life_volunteer_index()
	{
		checkAuth(ACTION_NAME);
		$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
		$this->assign('party_list',$party_list);
		$this->display("Lifevolunteer/life_volunteer_index");
	}
	/**
	 * @name:life_volunteer_index_data
	 * @desc：志愿者列表数据获取
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-08-30
	 * @updateime:2016-11-17
	 * @version：V1.0.1
	 **/
	public function life_volunteer_index_data(){
		$pagesize = I('get.limit');
    	$page = (I('get.page')-1)*$pagesize;
		$party_no = I("get.party_no");
		$party_nos = getPartyChildNos($party_no);
		$volunteer_data = getCommunistVolunteerList($party_nos,$page,$pagesize);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$status_name_arr = M('bd_status')->where("status_group = 'communist_volunteer_status'")->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where("status_group = 'communist_volunteer_status'")->getField('status_no,status_color');
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		
		foreach ($volunteer_data['data'] as &$volunteer){
		    $volunteer['status'] = "<font color='" . $status_color_arr[$volunteer['status']] . "'>" . $status_name_arr[$volunteer['status']] . "</font> ";
			$volun_id = $volunteer['volunteer_id'];
			//志愿者信息
			$volunteer['communist_name'] = $communist_name_arr[$volunteer['communist_no']];
			$volunteer['add_time'] = getFormatDate($volunteer['add_time'],'Y-m-d');
			//支部信息
			$volunteer['party_name'] =  $party_name_arr[$volunteer['party_no']];
			$volunteer['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='audit_volunteer($volun_id)'>查看</a>"."<a class='layui-btn layui-btn-del layui-btn-xs' href='".U("life_volunteer_del",array("volun_id"=>$volun_id))."' $confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		$volunteer_data['code'] = 0;
        $volunteer_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($volunteer_data); // 返回json格式数据
	}
	/**
	 * @life_volunteer_config_edit
	 * @desc： 志愿者简介编辑
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_config_edit(){
		$sys_config = M('sys_config');
		$config = $sys_config->where("config_code = 'volunteer_intro'")->field("config_value")->find();
		$this->assign('config',$config);
		$this->display("Lifevolunteer/life_volunteer_config_edit");		
	}
	/**
	 * @life_volunteer_config_do_save
	 * @desc： 志愿者简介保存
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_config_do_save(){
		$sys_config = M('sys_config');
		$data = I('post.');
		$data['update_time'] = date('Y-m-d H:i:s');
		$data['add_staff'] = session('staff_no');
		$life_config_save = $sys_config->where("config_code = 'volunteer_intro'")->save($data);
		if($life_config_save){
			showMsg('success','操作成功',U('Lifevolunteer/life_volunteer_index'),1);
		}else{
			showMsg('error','操作失败');
		}
	}
	/**
	 * @name:life_volunteer_audit
	 * @desc：志愿者申请
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_audit()
	{
		checkAuth(ACTION_NAME);
		$volunteer_id = I("get.volunteer_id");
		$volunteer = new LifeVolunteerModel();
		$volunteer_data =$volunteer->getCommunistVolunteerInfo($volunteer_id);
		$volunteer_data['communist_name'] = getcommunistInfo($volunteer_data['communist_no']);
		$volunteer_data['party_name'] = getPartyInfo($volunteer_data['party_no'],"party_name");
		$volunteer_data['volunteer_audit_man'] = getStaffInfo($volunteer_data['volunteer_audit_man']);
		$this->assign("volunteer_data",$volunteer_data);
 		$res = checkAuth(life_volunteer_do_save);
		$this->assign("res",$res);
		$this->display("Lifevolunteer/life_volunteer_audit");
	}
	
	/**
	 * @life_volunteer_do_save
	 * @desc： 审核志愿者保存操作
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_do_save()
	{
		$post = I("post.");
		$post['volunteer_audit_man'] = session('staff_no');
		$post['volunteer_audit_time'] = date("Y-m-d H:i:s");
		$apply_data = saveCommunistVolunteer($post);
		if ($apply_data) {
			//修改当前人员状态为志愿者
			$db_ccp_communist = M("ccp_communist");
			$data['is_volunteer'] = "1";
			$comm_map['communist_no'] = $communist_no;
			$is_volunteer = $db_ccp_communist->where($comm_map)->save($data);
			
			showMsg('success', '操作成功！！！', U('Lifevolunteer/life_volunteer_activity_index'),"1");
		} else {
			showMsg('error', '操作失败！！！', U('Lifevolunteer/life_volunteer_activity_index'),"1");
		}
	}
	
	/**
	 * @name:life_volunteer_del
	 * @desc：删除志愿者
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_del()
	{
		checkAuth(ACTION_NAME);
		$db_volunteer=M('life_volunteer');
		$volun_id = I("get.volun_id");
		$volunteer_map['volunteer_id'] = $volun_id;
		$volun_data = $db_volunteer->where($volunteer_map)->delete();
		if ($volun_data) {
			showMsg('success', '操作成功！！！', U('Lifevolunteer/life_volunteer_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	
	/**
	 * @name:life_volunteer_activity_index
	 * @desc：志愿活动列表
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_index()
	{
		checkAuth(ACTION_NAME);
		$this->display("Lifevolunteer/life_volunteer_activity_index");
	}
	/**
	 * @name:life_volunteer_catgroy_index_data
	 * @desc：志愿活动列表数据
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_index_data()
	{
		//$activity =  D('LifeVolunteerActivity');
		$activity_title = I('get.activity_title');
		/*if(!empty($activity_title)){
			$where['activity_title'] = $activity_title;
		}*/
		$page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
		$activity = new LifeVolunteerActivityModel();
		$activity_data = $activity->getCommunistVolunteerActivityList('',$activity_title,$page,$pagesize);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$status_name_arr = M('bd_status')->where("status_group = 'communist_volunteer_status'")->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where("status_group = 'communist_volunteer_status'")->getField('status_no,status_color');
		foreach ($activity_data['data'] as &$activity){
			$activity_id = $activity['activity_id'];
			$activity['status'] = "<font color='" . $status_color_arr[$activity['status']] . "'>" . $status_name_arr[$activity['status']] . "</font> ";
			//参加人数
			$activity['communist_num'] =0 ;
			$db_volunteer_apply=M('life_volunteer_activity_apply');
			$apply_map['activity_id'] = $activity_id;
			$apply_map['status'] = 2;
			$activity['communist_num'] = $db_volunteer_apply->where($apply_map)->count();
			$activity['activity_time'] = $activity['activity_starttime'].' - '.$activity['activity_endtime'];
			//$activity['activity_id'] = "<a class='fcolor-22' href='".U('life_volunteer_activity_info',array('activity_id'=>$activity_id))."'>$activity_id</a>";
			//$activity['activity_title'] = "<a class='fcolor-22' href='".U('life_volunteer_activity_info',array('activity_id'=>$activity_id))."'>".$activity['activity_title']."</a>";
			//$activity['operate'] = "<a class='layui-btn layui-btn-del layui-btn-xs' href='".U('life_volunteer_activity_delete',array("activity_id"=>$activity_id))."' $confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		$activity_data['code'] = 0;
        $activity_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($activity_data);
	}
	
	/**
	 * @name:life_volunteer_activity_edit
	 * @desc：志愿活动添加/编辑
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_edit()
	{
		checkAuth(ACTION_NAME);
		$this->display("Lifevolunteer/life_volunteer_activity_edit");
	}
	/**
	 * @name:life_volunteer_activity_do_save
	 * @desc：志愿活动保存方法
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_do_save()
	{
		$post = I("post.");
		$party_list = $post['party_no'];
		$party_list = explode(',',$party_list);
		$time = explode(' - ',$post['activity_time']);
		$post['activity_starttime'] = $time['0'];
		$post['activity_endtime'] = $time['1'];
		$post['activity_organizer'] = getStaffInfo(session('staff_no'));
		$post['add_staff'] = session('staff_no');
		$post['add_time'] = date('Y-m-d H:i:s');
		$post['update_time'] = date('Y-m-d H:i:s');
		$activity =  D('LifeVolunteerActivity');
		$activity_data = M('life_volunteer_activity')->add($post);
		// 组织志愿者活动给党组织加积分
		$party_map['party_no'] = array('in',$post['party_no']);
		$party_integral_arr = M('ccp_party')->where($party_map)->getField('party_no,party_integral');
		$integral_volunteer_party = getConfig('integral_volunteer_party');
		foreach ($party_list as $party_val) {
			updateIntegral(2,7,$party_val,$party_integral_arr[$party_val],$integral_volunteer_party,'组织志愿者活动'); 
		}//给党组织加积分
		if ($activity_data) {
			showMsg('success', '操作成功！！！', U('life_volunteer_activity_index'),"1");
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name:life_volunteer_activity_info
	 * @desc：志愿活动详情
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_info()
	{
		checkAuth(ACTION_NAME);
		$activity_id = I("get.activity_id");
		$activity = D('LifeVolunteerActivity');
		$activity_data = $activity->getActivityinfo($activity_id);
		//查询参见人员
		$apply_map['activity_id'] = $activity_id;
        $communist_num = M('life_volunteer_activity_apply')->where($apply_map)->select();
		if (!empty($communist_num)){
		    $party_name_arr = M('ccp_party')->getField('party_no,party_name');
		    $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		    $status_name_arr = M('bd_status')->where("status_group = 'apply_status'")->getField('status_no,status_name');
		    $status_color_arr = M('bd_status')->where("status_group = 'apply_status'")->getField('status_no,status_color');
            foreach ($communist_num as &$communist){
                $communist['audit_status'] = "<font color='" . $status_color_arr[$communist['status']] . "'>" . $status_name_arr[$communist['status']] . "</font> ";
                $communist['communist_name'] = $communist_name_arr[$communist['communist_no']];
                $communist['party_name'] = $party_name_arr[$communist['party_no']];
            }
            $this->assign("communist",$communist_num);
        }
		$this->assign("activity",$activity_data);
		$this->display("Lifevolunteer/life_volunteer_activity_info");
	}

	/**
	 * @name:life_volunteer_activity_delete
	 * @desc：志愿活动删除
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_delete()
	{
		checkAuth(ACTION_NAME);
		$db_volunteer_activity = M("life_volunteer_activity");
		$life_volunteer_activity_apply = M("life_volunteer_activity_apply");
		$activity_id = I("get.activity_id");
		$act_map['activity_id'] = $activity_id;
		$activity_data = $db_volunteer_activity->where($act_map)->delete();
		$life_volunteer_activity_apply->where($act_map)->delete();
		if ($activity_data) {
			showMsg('success', '操作成功！！！', U('Lifevolunteer/life_volunteer_activity_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	
	/**
	 * @name:life_volunteer_activity_audit
	 * @desc：志愿活动审核
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_audit()
	{
		checkAuth(ACTION_NAME);
		$apply_id = I("get.apply_id");
		$activity_id = I("get.activity_id");
		$activity = D('LifeVolunteerActivity');
		$activity_data = $activity->getActivityinfo($activity_id);
		$apply_data = getActivityJoinInfo($apply_id);
		$communist_data = getcommunistInfo($apply_data['communist_no'],'all');
		$apply_data['communist_name'] = getcommunistInfo($apply_data['communist_no']);
		$apply_data['party_name'] = getPartyInfo($apply_data['party_no'],"party_name");
		$apply_data['activity_integral'] = $activity_data['activity_integral'];
		$this->assign("apply_data",$apply_data);
		$this->assign('activity_data',$activity_data);
		$this->display("Lifevolunteer/life_volunteer_activity_audit");
	}
	/**
	 * @name：life_volunteer_activity_apply_audit
	 * @desc：志愿参加活动申请审核
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_apply_audit()
	{
		$db_activity_apply = M("life_volunteer_activity_apply");
		$post = I("post.");
		$communist_no = $post['communist_no'];
		$Integral = $post['activity_integral'];
		$post['apply_audit_man'] = session('staff_no');
		$post['apply_audit_time'] = date("Y-m-d H:i:s");
		$apply_data = saveActivityJoinApply($post);
		//if($post['communist_no']){
			//         	saveCommunistLog($post['communist_no'],'17','','',$post['help_name']);
			//         }
		if ($apply_data) {
			$integral_volunteer_communist = getConfig('integral_volunteer_communist');
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_volunteer_communist,'参加志愿者活动'); // 给签到党员加积分
			showMsg('success', '操作成功！！！', U('Lifevolunteer/life_volunteer_activity_index'),"1");
		} else {
			showMsg('error', '操作失败！！！', U('Lifevolunteer/life_volunteer_activity_index'),"1");
		}
	}
	/**
	 * @name：life_volunteer_activity_apply_del
	 * @desc：志愿参加活动申请删除
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2016-11-17
	 * @version：V1.0.0
	 **/
	public function life_volunteer_activity_apply_del(){
		$apply_id = I('get.apply_id');
		$activity_id = I('get.activity_id');
		$life_volunteer_activity_apply = M("life_volunteer_activity_apply");
		$apply_map['apply_id'] = $apply_id;
		$activity_data = $life_volunteer_activity_apply->where($apply_map)->delete();
		if ($activity_data) {
			showMsg('success', '操作成功！！！', U('Lifevolunteer/life_volunteer_activity_info',array('activity_id'=>$activity_id)));
		} else {
			showMsg('error', '操作失败！！！', U('Lifevolunteer/life_volunteer_activity_info',array('activity_id'=>$activity_id)));
		}
		
	}
}
