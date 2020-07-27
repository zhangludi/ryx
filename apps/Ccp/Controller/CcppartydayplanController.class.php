<?php
/************************************************************************************************ */
namespace ccp\Controller;
use Common\Controller\BaseController;
use Think\Model;
class CcppartydayplanController extends BaseController{
	/**
	 * @name  ccp_partyday_cat_index()
	 * @desc  党日计划栏目首页
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_cat_index(){

		$this->display("ccp_partyday_cat_index");
	}
	/**
	 * @name  ccp_partyday_cat_index_data()
	 * @desc   党日计划栏目加载
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_cat_index_data(){
		$partyday_title = I('get.partyday_title');
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
		$approval_staff = session('staff_no');
		$offset = I('get.offset');
		$limit = I('get.limit');
		$party_data = getpartydayList($partyday_title,$start,$end,$approval_staff,'all','',$offset,$limit);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$status_map['status_group'] = 'partyday_status';
		$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
		foreach ($party_data['data'] as &$list) {
			$partyday_id = $list['partyday_id'];
			$list['partyday_title'] = "<a href='" . U('ccp_partyday_cat_info', array('partyday_id' => $partyday_id)) . "' class='fcolor-22' >".$list['partyday_title']."</a>";
			$status = $list['status'];
			$list['status_name'] = "<font color='" . $status_color_arr[$list['status']] . "'>" . $status_name_arr[$list['status']] . "</font> ";
			if ($status==0) {
				// $list['operate'] = "<a href='" . U('ccp_partyday_cat_edit', array('partyday_id' => $partyday_id)) . "' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>
				$list['operate'] = "<a onclick='edit($partyday_id)' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>
	             			<a href='" . U('ccp_partyday_cat_del', array('partyday_id' => $partyday_id,'status'=>$status)) . "' $confirm class='layui-btn layui-btn-del layui-btn-xs'>删除</a>";
				if ($list['approval_staff']==$approval_staff && $list['status']==0) {
					$list['operate'] .= "<a href='" . U('ccp_partyday_cat_status', array('partyday_id' => $partyday_id,'status'=>1)) . "' class='layui-btn  layui-btn-xs layui-btn-f60'><i class='fa fa-edit'></i>通过</a>
	             			<a href='" . U('ccp_partyday_cat_status', array('partyday_id' => $partyday_id,'status'=>2)) . "' class='layui-btn layui-btn-primary layui-btn-xs'>驳回</a>";
				}
			}else {
				//$list['operate'] = "<a href='" . U('ccp_partyday_cat_info', array('partyday_id' => $partyday_id)) . "' class='layui-btn layui-btn-primary layui-btn-xs'><i class='fa fa-edit'></i>查看</a>";
				$list['operate'] = "<a onclick='info($partyday_id)' class='layui-btn layui-btn-primary layui-btn-xs'><i class='fa fa-edit'></i>查看</a>";
			}
			
		}
		$party_data['code'] = 0;
		ob_clean();$this->ajaxReturn($party_data);
	}
	
	/**
	 * @name  ccp_partyday_cat_info()
	 * @desc  党日计划详情
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_cat_info(){
		$cat_list = getpartydayInfo($_GET['partyday_id']);
		$this->assign('cat_list',$cat_list);
		$this->display("ccp_partyday_cat_info");
	}
	/**
	 * @name  ccp_partyday_cat_edit()
	 * @desc  党日计划添加修改
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_cat_edit(){
		checkAuth(ACTION_NAME);
		if (!empty($_GET['partyday_id'])) {
			$partyday_list = getpartydayInfo($_GET['partyday_id']);
			$partyday_list['approval_staff_name'] = getStaffInfo($partyday_list['approval_staff']);
			$this->assign('partyday_list',$partyday_list);
		}
		
		$this->display("ccp_partyday_cat_edit");
	}
	/**
	 * @name  ccp_partyday_cat_save()
	 * @desc  新增/编辑 保存
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2018-12-18
	 * */
	public function ccp_partyday_cat_save(){
 		
		$db_partyday = M('ccp_partyday_plan');
		$partyday_id = I("post.partyday_id");
		$post = $_POST;
		if (!empty($post['approval_staff_no'])) {
			$post['approval_staff'] = $post['approval_staff_no'];
		}
		if(!empty($partyday_id)){
			$post['update_time'] = date("Y-m-d H:i:s");
			$post['update_staff'] = session('staff_no');
			$partyday_map['partyday_id'] = $partyday_id;
			$partyday_data = $db_partyday->where($partyday_map)->save($post);
		}else {
			$post['add_staff'] = session('staff_no');
			$post['add_time'] = date("Y-m-d H:i:s");
			$partyday_data = $db_partyday->add($post);
		}
		if ($partyday_data) {
			showMsg('success', '操作成功！！！', U('ccp_partyday_cat_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  ccp_partyday_cat_del()
	 * @desc  党日计划删除
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_cat_del(){
		checkAuth(ACTION_NAME);
		if (!empty($_GET['partyday_id'])) {
			$db_partyday = M('ccp_partyday_plan');
			$partyday_id = $_GET['partyday_id'];
			$partyday_map['partyday_id'] = $partyday_id;
			$partyday_data = $db_partyday->where($partyday_map)->delete();
			showMsg('success', '操作成功！！！', U('ccp_partyday_cat_index'));
		}else {
				showMsg('error', '删除失败！！！','');
			}
	}
	/**
	 * @name:ccp_partyday_cat_status
	 * @desc:审核
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-12-20
	 * @version：V1.0.0
	 **/
	public function ccp_partyday_cat_status(){
		$status = I('get.status');
 		checkAuth(ACTION_NAME);
		$db_partyday_category = M('ccp_partyday_plan');
		$partyday['partyday_id'] = I('get.partyday_id');
		$partyday['status'] = $status;
		/* if ($status==1) {
 			$party_list=getPartyList('');
			$party_log['partyday_id'] = $partyday['partyday_id'];
			foreach ($party_list as &$list){
			$party_log['party_no'] = $list['party_no'];
				M('ccp_partyday_plan_log')->add($party_log);
			}
		} */
		$partyday['update_time'] =date("Y-m-d H:i:s");
		$partyday_data = $db_partyday_category->save($partyday);
		if ($partyday_data) {
			showMsg('success', '操作成功！！！',U('ccp_partyday_cat_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  ccp_partyday_tracking_index()
	 * @desc  党日计划跟踪首页
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_tracking_index(){
		// 		checkAuth(ACTION_NAME);
		$this->display("ccp_partyday_tracking_index");
	}
	/**
	 * @name  ccp_partyday_tracking_index_data()
	 * @desc   党日计划栏目加载
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_tracking_index_data(){
		$partyday_title = I('get.partyday_title');
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
		$approval_staff = session('staff_no');
		$party_data = getpartydayList($partyday_title,$start,$end,$approval_staff,1);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$status_map['status_group'] = 'partyday_status';
		$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
		
		foreach ($party_data as &$list) {
			$partyday_id = $list['partyday_id'];
			$list['partyday_id'] = "<a href='" . U('ccp_partyday_tracking_info', array('partyday_id' => $partyday_id)) . "' class='fcolor-22' >".$partyday_id."</a>";
			$list['partyday_title'] = "<a href='" . U('ccp_partyday_tracking_info', array('partyday_id' => $partyday_id)) . "' class='fcolor-22' >".$list['partyday_title']."</a>";
			$status = $list['status'];
			$list['status_name'] = "<font color='" . $status_color_arr[$list['status']] . "'>" . $status_name_arr[$list['status']] . "</font> ";
			$list['operate'] = "<a href='" . U('ccp_partyday_tracking_info', array('partyday_id' => $partyday_id)) . "' class='layui-btn layui-btn-primary layui-btn-xs'>查看</a>
					<a href='" . U('ccp_partyday_check_list', array('partyday_id' => $partyday_id)) . "' class='layui-btn  layui-btn-xs layui-btn-f60'>打分</a>";
		}
		$party_data_arr['data'] =$party_data;
		$party_data_arr['code'] = 0;
		ob_clean();$this->ajaxReturn($party_data_arr);
	}
	/**
	 * @name  ccp_partyday_tracking_info()
	 * @desc  党日计划跟踪详情
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_tracking_info(){
		checkAuth(ACTION_NAME);
		$cat_list = getpartydayInfo($_GET['partyday_id']);
		$tracking = checkDataAuth(session('staff_no'),'is_plan');  //是否有审核权限
		if ($tracking) {
			$this->assign('log_status',1);
		}
		
		
		$this->assign('cat_list',$cat_list);
		$this->display("ccp_partyday_tracking_info");
	}

	/**
	 * @name:ccp_partyday_log_info_data
	 * @desc：党日计划修改数据加载
	 * @param：
	 * @return：
	 * @author：杨凯
	 * @addtime:2017-12-21 15:54:00
	 * @version：V1.0.0
	 **/
	public function ccp_partyday_log_info_data(){
		$partyday_id=$_GET['partyday_id'];
		if(!empty($partyday_id)){
			$cat_list = getpartydaylogInfo($partyday_id);
			$status_map['status_group'] = 'partyday_log_status';
			$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
			$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
			$party_name_arr = M('hr_dept')->getField('dept_no,dept_name');
			foreach ($cat_list as &$list){
			    $list['party_name']=$party_name_arr[$list['party_no']];
				$list['status_name']="<font color='" . $status_color_arr[$list['status']] . "'>" . $status_name_arr[$list['status']] . "</font> ";
			}
		}
		ob_clean();$this->ajaxReturn($cat_list);
	}
	/**
	 * @name:ccp_partyday_log_info_save
	 * @desc：党日计划组织积分添加/修改
	 * @author：杨凯
	 * @addtime:2017-12-21 15:54:00
	 * @version：V1.0.0
	 **/
	public function ccp_partyday_log_info_save(){
		checkAuth(ACTION_NAME);
		$data=I('post.');
		$partyday_id = $data['partyday_id'];
		$where['partyday_id']=$partyday_id;
		//添加主表数据
		$comment['partyday_log_status']=1;
		$result = M('ccp_partyday_plan')->where($where)->save($comment);
		$json = $data['json'];
		$dt_json = str_replace('&quot;','"',$json);
		$dt_json = (Array)json_decode($dt_json,true);
		foreach($dt_json as &$v){
			$details=$v;
			$grade = array($details['meeting_grade'],$details['dues_grade'],$details['cms_grade'],$details['response_grade'],$details['condition_grade'],$details['workable_grade'],$details['cultivate_grade'],$details['activity_grade'],$details['partyday_grade']);
			$grade_num = array_sum($grade);
			$details['total_grade'] =$grade_num;
			$details['average_grade'] =$grade_num/9;
			$details['status']='1';
			$flag=M('ccp_partyday_plan_log')->save($details);
			if ($flag) {
				$flag_good = 1;
			}
		}
		if($result || $flag_good){
			showMsg('success','操作成功',U('ccp_partyday_tracking_index'));
		}else{
			showMsg('error','操作失败');
		}
	}
	/**
	 * @name:ccp_partyday_log_save_email
	 * @desc：党日计划发送邮件
	 * @author：杨凯
	 * @addtime:2018-01-04 15:54:00
	 * @version：V1.0.0
	 **/
	public function ccp_partyday_log_save_email(){
		$data=I('post.');
		$mailto = '805650264@qq.com' ;   //邮箱
		$name = '单县杨凯' ;  // $name 接收人姓名
		$subject= '测试'  ;   // $subject  邮件主题
		$body = $_POST['jshttp'];  //$body 邮件内容
		$attachment = '';     // $attachment 附件
		$a=sendEMail($mailto, $name, $subject, $body, $attachment);
		if($a){
			showMsg('success','操作成功',U('ccp_partyday_tracking_index'));
		}else{
			showMsg('error','操作失败');
		}
	}
	/**
	 * @name:ccp_partyday_log_info_status
	 * @desc：党日计划积分跟踪审核
	 * @param：
	 * @return：
	 * @author：杨凯
	 * @addtime:2017-12-21 15:54:00
	 * @version：V1.0.0
	 **/
	public function ccp_partyday_log_info_status(){
		checkAuth(ACTION_NAME);
		if ($_GET['partyday_log_status']==1) {
			$partyday_id = $_GET['partyday_id'];
			$where['partyday_id']=$partyday_id;
			$comment['partyday_log_status']=$_GET['status_log'];
			$result = M('ccp_partyday_plan')->where($where)->save($comment);
			$log['status']=$_GET['status_log'];
			$result_log = M('ccp_partyday_plan_log')->where($where)->save($log);
		}
		if($result){
			showMsg('success','操作成功',U('ccp_partyday_tracking_index'));
		}else{
			showMsg('error','操作失败');
		}
	}
	
	
	/**
	 * @name  ccp_partyday_check_index()
	 * @desc  党日计划栏目首页
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_check_index(){
		$this->display("ccp_partyday_check_index");
	}
	/**
	 * @name  ccp_partyday_check_list()
	 * @desc  党日计划栏目首页
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_check_list(){
		$partyday_id = I('get.partyday_id');
		$this->assign('partyday_id',$partyday_id);
		$this->display("ccp_partyday_tracking_list");
	}
	/**
	 * @name  ccp_partyday_check_index_data()
	 * @desc   党日计划栏目加载
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_check_index_data(){
		$partyday_title = I('get.partyday_title');
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
		$page = I('get.offset');
    	$pagesize = I('get.limit');
		$approval_staff = session('staff_no');
		$party_data = getpartydayList($partyday_title,$start,$end,'all',1,$page,$pagesize);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$status_map['status_group'] = 'partyday_status';
		$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
		$log_map['add_staff'] = $approval_communist;
		$check_party_plan_arr = M('ccp_partyday_plan_log')->where($log_map)->getField("partyday_id,add_staff");
		foreach ($party_data as &$list) {
			$partyday_id = $list['partyday_id'];
			$list['partyday_id'] = "<a href='" . U('ccp_partyday_check_info', array('partyday_id' => $partyday_id,'type' => 'info')) . "' class='fcolor-22' >".$partyday_id."</a>";
			$list['partyday_title'] = "<a href='" . U('ccp_partyday_check_info', array('partyday_id' => $partyday_id,'type' => 'info')) . "' class='fcolor-22' >".$list['partyday_title']."</a>";
			$status = $list['status'];
			$list['status_name'] = "<font color='" . $status_color_arr[$list['status']] . "'>" . $status_name_arr[$list['status']] . "</font> ";
			if(!empty($check_party_plan_arr[$partyday_id])){
				$list['operate'] = "<a class='btn btn-xs blue btn-outline'>已填写</a>";
			} else {
				$list['operate'] = "<a href='" . U('ccp_partyday_check_info', array('partyday_id' => $partyday_id,'type' => 'info')) . "' class='layui-btn layui-btn-primary layui-btn-xs' >查看</a>"."<a href='" . U('ccp_partyday_check_info', array('partyday_id' => $partyday_id)) . "' class='layui-btn  layui-btn-xs layui-btn-f60'><i class='fa fa-edit'></i>填写</a>";
			}
		}
		$party_data_arr['data'] =$party_data;
		$party_data_arr['code'] = 0;
		ob_clean();$this->ajaxReturn($party_data_arr);
	}
	
	/**
	 * @name  ccp_partyday_check_list_data()
	 * @desc   党日计划栏目加载
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_check_list_data(){
		$partyday_title = I('get.partyday_title');
		$partyday_id = I('get.partyday_id');
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
		$approval_staff = session('staff_no');
		$party_data['data'] = getpartydaylogInfo($partyday_id);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		$party_name_arr = M('hr_dept')->getField('dept_no,dept_name');
		foreach ($party_data['data'] as &$list) {
			$partyday_id = $list['partyday_id'];
			$partyday_log_id = $list['partyday_log_id'];
			$list['partyday_id'] = "<a href='" . U('ccp_partyday_check_info', array('partyday_id' => $partyday_id)) . "' class='fcolor-22' >".$partyday_id."</a>";
			$list['partyday_title'] = "<a href='" . U('ccp_partyday_check_info', array('partyday_id' => $partyday_id)) . "' class='fcolor-22' >".$list['partyday_title']."</a>";
			$list['party_no_name'] = $party_name_arr[$list['party_no']];
			$list['add_staff_name'] = $staff_name_arr[$list['add_staff']];
			$list['operate'] = "<a href='" . U('ccp_partyday_tracking_list_info', array('partyday_id' => $partyday_id,'partyday_log_id' => $partyday_log_id)) . "' class='btn btn-xs blue btn-outline'><i class='fa fa-edit'></i>打分</a>";
	
		}
		$party_data['code'] = 0;
        $party_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($party_data);
	}
	
	/**
	 * @name  ccp_partyday_check_info()
	 * @desc  党日计划填写详情
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_check_info(){
		//checkAuth(ACTION_NAME);
		$cat_list = getpartydayInfo($_GET['partyday_id']);
		$type = I('get.type');
		$this->assign('cat_list',$cat_list);
		$party_no = getStaffInfo(session('staff_no'),'staff_dept_no');
		$partyday_log = getpartydaycheckInfo($_GET['partyday_id'],$party_no);
		$this->assign('partyday_log',$partyday_log);
		$this->assign('type',$type);
		$this->display("ccp_partyday_check_info");
	}
	/**
	 * @name  ccp_partyday_tracking_list_info()
	 * @desc  党日计划填写详情
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-12-18
	 */
	public function ccp_partyday_tracking_list_info(){
		$cat_list = getpartydayInfo($_GET['partyday_id']);
		$this->assign('cat_list',$cat_list);
		$party_no = getStaffInfo(session('staff_no'),'staff_dept_no');
		$partyday_log = getpartydaycheckInfo($_GET['partyday_id'],'',$_GET['partyday_log_id']);
		$this->assign('partyday_log',$partyday_log);
		$this->display("ccp_partyday_tracking_list_info");
	}
	/**
	 * @name:ccp_partyday_check_save
	 * @desc：党日计划组织内容回填添加/修改
	 * @author：杨凯
	 * @addtime:2017-12-21 15:54:00
	 * @version：V1.0.0
	 **/
	public function ccp_partyday_check_save(){
		$data=I('post.');
		$staff_dept_no = getStaffInfo(session('staff_no'),'staff_dept_no');
		if (!empty($data['partyday_log_id'])) {
			$data['staff_dept_no'] = $staff_dept_no;
			$data['partyday_log_id'] = $data['partyday_log_id'];
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['update_time'] = date("Y-m-d H:i:s");
			$data['add_staff'] = session('staff_no');
			$log_map['partyday_log_id'] = $data['partyday_log_id'];
			$partyday_plan_log=M('ccp_partyday_plan_log')->where($log_map)->save($data);
		}else {
			$data['party_no'] = $staff_dept_no;
			$data['add_staff'] = session('staff_no');
			$data['add_time'] = date("Y-m-d H:i:s");
			$partyday_plan_log=M('ccp_partyday_plan_log')->add($data);
		}
		if($partyday_plan_log){
			if (!empty($data['type'])) {
				showMsg('success','操作成功',U('ccp_partyday_tracking_index'));
			}
			showMsg('success','操作成功',U('ccp_partyday_check_index'));
		}else{
			showMsg('error','操作失败');
		}
	}
}

