<?php



namespace Supervise\Controller;



use Think\Controller;
use Common\Controller\BaseController;

class SupervisecaseController extends BaseController {

	/**
	 * @name:supervise_case_index
	 * @desc：案件列表
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_index(){
		checkAuth(ACTION_NAME);
		$this->display("supervise_case_index");
	}
	/**
	 * @name:supervise_case_data
	 * @desc：案件表数据查询
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_data(){
		$count = I('get.limit');
		$page = (I('get.page')-1)*$count;
		$str = I('get.start_time');
		$type = I('get.type');
		$keywords = I('get.keywords');
		$strt = strToArr($str, ' - ');  //分割时间
		$start_time = $strt[0];
		$end_time = $strt[1]; 
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$case_data = getSuperviseCaseList('',$page,$count,$start_time,$end_time,'all',$keywords);
		$status_name_arr = M('bd_status')->where("status_group = 'case_status'")->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where("status_group = 'case_status'")->getField('status_no,status_color');
		foreach ($case_data['data'] as &$case){
			//$case['case_title'] = "<a class='fcolor-22' href='" . U('supervise_case_info', array('case_id' => $case['case_id'])) . "' target='_self'>".mb_substr($case['case_title'], 0, 10, 'utf-8')."</a>";
			$case['case_title'] = mb_substr($case['case_title'], 0, 10, 'utf-8');
			$case['case_ource'] = mb_substr(strip_tags($case['case_ource']), 0, 10, 'utf-8');
			$case['status_name'] = "<font color='" . $status_color_arr[$case['status']] . "'>" . $status_name_arr[$case['status']] . "</font> ";
			$case['add_staff'] = getStaffInfo($case['add_staff']);
			//无添加人信息时
			if(empty($case['add_staff'])){
				$case['add_staff']='暂无添加人';
			}
			switch ($case['status']){
				case 0:$case['operate'] = "<a class=' layui-btn layui-btn-primary layui-btn-xs' onclick='info(".$case['case_id'].")'  target='_self'><i class='fa fa-edit'></i>查看</a>";
						break;
				case 1:$case['operate'] = "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('supervise_case_del', array('case_id' => $case['case_id'])) . "' $confirm><i class='fa fa-trash-o'></i>删除</a>
						<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='case_reject(0,".$case['case_id'].")' target='_self'><i class='fa fa-edit'></i>驳回</a>&nbsp;&nbsp;
						<a type='button' class='btn green btn-xs btn-outline' href='" . U('supervise_case_status', array('case_id' => $case['case_id'],'status'=>'2')) . "' target='_self'><i class='fa fa-edit'></i>通过立案</a>";
						break;
				case 2:$case['operate'] = "<a class='fcolor-green'  target='_self'>案件调查</a> &nbsp; <a class='layui-btn layui-btn-primary layui-btn-xs' onclick='info(".$case['case_id'].")' target='_self'>查看</a>";
						break;
				case 3:$case['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs ' onclick='case_reject(4,".$case['case_id'].")' target='_self'><i class='fa fa-edit'></i>驳回</a><a  type='button' class=' layui-btn  layui-btn-xs layui-btn-f60' onclick='case_hear(5,".$case['case_id'].")' target='_self'><i class='fa fa-edit'></i>同意</a>";
						break;
				case 4:$case['operate'] = "<a class=' layui-btn layui-btn-primary layui-btn-xs'  onclick='info(".$case['case_id'].")' target='_self'><i class='fa fa-edit'></i>查看</a>";
						break;
				case 5:$case['operate'] = "<a class=' layui-btn layui-btn-primary layui-btn-xs' onclick='info(".$case['case_id'].")' target='_self'><i class='fa fa-edit'></i>查看</a>";
						break;
			}
			$case['add_time'] = getFormatDate($case['add_time'], 'Y-m-d');
		}
		$case_data['code'] = 0;
		ob_clean();$this->ajaxReturn($case_data);
	}

	/**

	 * @name:supervise_case_edit
	 * @desc：案件表数据添加界面
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_edit(){
		checkAuth(ACTION_NAME);
		$this->display("supervise_case_edit");
	}

	/**
	 * @name:supervise_case_info
	 * @desc：案件详情界面
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_info(){
		checkAuth(ACTION_NAME);
		$case_id=I('get.case_id');
		$case_data = getSuperviseCaseList($case_id);
		$this->assign('case_data',$case_data);
		$this->display("supervise_case_info");
	}
	/**
	 * @name:supervise_case_save
	 * @desc：案件表数据添加
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_save(){
		$supervise_case = M('supervise_case');
		$supervise_case_communist = M('supervise_case_log');
		$_POST['add_time'] = date("Y-m-d H:i:s");
		$_POST['add_staff'] = session("staff_no");
		$_POST['status'] = 1;
 		$case_id = $supervise_case->add($_POST);
		$case_communist = strToArr($_POST['case_communist_no']);
		foreach ($case_communist as &$communist_id){
			$case_date['case_id']=$case_id;
			$case_date['communist_id']=$communist_id;
			$case_date['add_staff']=$_POST['add_staff'];
			$case_date['add_time'] = date("Y-m-d H:i:s");
			$supervise_case_communist->add($case_date);
		}
		showMsg('success', '操作成功！！！',U('supervise_case_index'));
	}

	/**
	 * @name:supervise_case_del
	 * @desc:删除案件信息数据
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_del(){
		checkAuth(ACTION_NAME);
		$supervise_case = M('supervise_case');
		$supervise_case_log = M('supervise_case_log');
		$case_id = I('get.case_id');
		$case_data = $supervise_case->delete($case_id);
		$case_map['case_id'] = $case_id;
		$supervise_case_log->where($case_map)->delete();
		if ($case_data) {
			showMsg('success', '操作成功！！！',U('supervise_case_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}

	/**
	 * @name:supervise_case_log_index
	 * @desc：案件人员列表
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_log_index(){
		checkAuth(ACTION_NAME);
		$this->display("supervise_case_log_index");
	}

	/**
	 * @name:supervise_case_log_data
	 * @desc：案件人员数据查询
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_log_data(){
		$count = I('get.limit');
		$page = (I('get.page')-1)*$count;
		$str = I('get.start');
		$type = I('get.type');
		$strt = strToArr($str, ' - ');  //分割时间
		$start_time = $strt[0];
		$end_time = $strt[1];
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$case_data = getSuperviseCaselogList('',$page,$count,$start_time,$end_time);
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		$status_name_arr = M('bd_status')->where("status_group = 'case_communist_status'")->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where("status_group = 'case_communist_status'")->getField('status_no,status_color');
		$communist_name_arr = M('ccp_communist')->where("1=1")->getField('communist_no,communist_name');
		foreach ($case_data['data'] as &$case){
			if ($case['case_id']==0) {
				$case['case_title'] ="内部添加";
			}else {
				//$case['case_title'] ="<a class='fcolor-22' href='" . U('supervise_case_info', array('case_id' => $case['case_id'])) . "' target='_self'>".mb_substr(getTableInfo('supervise_case','case_id',$case['case_id'],'case_title'), 0, 10, 'utf-8')."</a>";
				$case['case_title'] ="<a class='fcolor-22' onclick='case_log_info($case[case_id])' target='_self'>".mb_substr(getTableInfo('supervise_case','case_id',$case['case_id'],'case_title'), 0, 10, 'utf-8')."</a>";
			}
			$case['status_name'] = "<font color='" . $status_color_arr[$case['status']] . "'>" . $status_name_arr[$case['status']] . "</font> ";
			$case['communist_name'] = $communist_name_arr[$case['communist_id']];
			if(empty($case['communist_name'])){
				$case['communist_name']='暂无处分人员';
			}
			if ($case['status']==0) {
					$case['operate'] .= "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('supervise_case_log_del', array('ce_id' => $case['ce_id'])) . "' $confirm><i class='fa fa-trash-o'></i>删除</a>
				<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='case_log_hear(1,".$case['ce_id'].")' target='_self'><i class='fa fa-edit'></i>处分</a>";
			}else{
				$case['operate'] .= "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='case_log_hear_info(".$case['ce_id'].")' $confirm><i class='fa fa-user'></i>处理详情</a>";
			}
			$case['add_time'] = getFormatDate($case['add_time'], 'Y-m-d');
		}
		$case_data['code'] = 0;
		ob_clean();$this->ajaxReturn($case_data);
	}

	/**
	 * @name:supervise_case_log_info
	 * @desc：人员处理详情界面
	 * @param：
	 * @author：杨凯
	 * @addtime:2017-11-29
	 * @version：V1.0.0
	 **/
	public function supervise_case_log_info(){
		checkAuth(ACTION_NAME);
		$ce_id=I('get.ce_id');
		$case_data = getIaselogInfo($ce_id);
		$case_data['communist_name'] = getCommunistInfo($case_data['communist_id'], 'communist_name');
		$case_data['add_staff_name'] = getCommunistInfo($case_data['add_staff'], 'communist_name');
		if ($case_data['judge']==0) {
			$case_data['judge_name'] = '不处分';
		}else {
			$case_data['judge_name'] = '处分';
			$case_data['punish_type_name'] = getBdTypeInfo($case_data['punish_type'],'punish_type');
		}
		$this->assign('case_data',$case_data);
		$this->display("supervise_case_log_info");

	}

	/**

	 * @name:supervise_case_log_del

	 * @desc:删除案件信息数据

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_log_del(){

		checkAuth(ACTION_NAME);

		$supervise_case_log = M('supervise_case_log');
		$ce_id = I('get.ce_id');
		$log_map['ce_id'] = $ce_id;
		$ce_data = $supervise_case_log->where($log_map)->delete();
		if ($ce_data) {
			showMsg('success', '操作成功！！！',U('supervise_case_log_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}

	/**

	 * @name:supervise_case_log_hear

	 * @desc：人员修改列表

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_log_hear(){

		$status=I('get.status');

		$ce_id=I('get.ce_id');

		$this->assign('status',$status);

		$this->assign('ce_id',$ce_id);

		$this->display("supervise_case_log_hear");

	}

	/**

	 * @name:supervise_case_log_hear_edit

	 * @desc：单独添加涉案人员

	 * @param：

	 * @author：杨凯

	 * @addtime:2018-01-10

	 * @version：V1.0.0

	 **/

	public function supervise_case_log_hear_edit(){

// 		checkAuth(ACTION_NAME);

		$this->display("supervise_case_log_hear_edit");

	}

	/**

	 * @name:supervise_case_log_hear_save

	 * @desc：案件表数据保存

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_log_hear_save(){

		$supervise_case_communist = M('supervise_case_log');

		$case_communist = strToArr($_POST['case_communist_no']);
	
		foreach ($case_communist as &$communist_id){
			$case_date['case_id']=0;
			$case_date['communist_id']=$communist_id;
			$case_date['memo']=$_POST['memo'];
			$case_date['add_staff']=session("staff_no");
			$case_date['add_time'] = date("Y-m-d H:i:s");
			$case_log_id = $supervise_case_communist->add($case_date);

		}

		if ($case_log_id) {

			showMsg('success', '操作成功！！！',U('supervise_case_log_index'),1);

		} else {

			showMsg('error', '操作失败！！！','');

		}

	}

	/**

	 * @name:supervise_case_status

	 * @desc:修改审核状态

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_status(){

		$status = I('get.status');

		checkAuth(ACTION_NAME);

		$supervise_case = M('supervise_case');

		$case['case_id'] = I('get.case_id');

		$case['status'] = $status;

		$case['update_time'] =date("Y-m-d H:i:s");

		$case_data = $supervise_case->save($case);

		if ($case_data) {

			showMsg('success', '操作成功！！！',U('supervise_case_index'));

		} else {

			showMsg('error', '操作失败！！！','');

		}

	}

	/**

	 * @name:supervise_case_hear

	 * @desc：案件审核列表

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_hear(){

		$status=I('get.status');

		$case_id=I('get.case_id');

		$this->assign('status',$status);

		$this->assign('case_id',$case_id);

		$this->display("supervise_case_hear");

	}

	/**

	 * @name:supervise_case_hear_save

	 * @desc：审批/调查保存数据

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_hear_save(){
		$supervise_case = M('supervise_case');
		$case_data = $supervise_case->save($_POST);
		if ($case_data) {
		    if (I('post.status')==3){
		        showMsg('success', '操作成功！！！',U('supervise_case_index'));
		    }
			showMsg('success', '操作成功！！！',U('supervise_case_index'),'1');
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}

	/**

	 * @name:supervise_case_hear_log_save

	 * @desc：审批/调查保存数据

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_hear_log_save(){

		$supervise_case = M('supervise_case_log');

		$case_data = $supervise_case->save($_POST);

		if ($case_data) {

			showMsg('success', '操作成功！！！',U('supervise_case_index'),1);

		} else {

			showMsg('error', '操作失败！！！','');

		}

	

	}

	/**

	 * @name:supervise_case_regect

	 * @desc：驳回界面

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_reject(){

		$status=I('get.status');

		$case_id=I('get.case_id');

		$this->assign('status',$status);

		$this->assign('case_id',$case_id);

		$this->display("supervise_case_reject");

	}

	/**

	 * @name:supervise_case_reject_save

	 * @desc：驳回保存数据

	 * @param：

	 * @author：杨凯

	 * @addtime:2017-11-29

	 * @version：V1.0.0

	 **/

	public function supervise_case_reject_save(){

		$supervise_case = M('supervise_case');

		$case_data = $supervise_case->save($_POST);

		if ($case_data) {

			showMsg('success', '操作成功！！！',U('supervise_case_index'),2);

		} else {

			showMsg('error', '操作失败！！！','');

		}

	}

}

