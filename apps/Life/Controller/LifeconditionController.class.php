<?php
/*******************************************民生O2O*****************************************************/
namespace Life\Controller;
use Common\Controller\BaseController;
use Life\Model\LifeConditionModel;
use Life\Model\LifeConditionPersonalModel;
class LifeconditionController extends BaseController{
	/**
	 * @name  life_condition_index()
	 * @desc  搜集民情
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_condition_index()
	{
		checkAuth(ACTION_NAME);
		$type_list = M('life_condition_category')->where("status = 1")->select();
		$this->assign("type_list",$type_list);
		
		$this->display("Lifecondition/life_condition_index");
	}
	/**
	 * @name  life_condition_index_data()
	 * @desc  搜集民情数据获取
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_condition_index_data()
	{
		$get = I("get.");
		if(!empty($get['type_no'])){
			$condition_map['type_no'] = $get['type_no'];
		}
		if(!empty($get['keyword'])){
			$condition_map['condition_personnel'] = array('like','%'.$get['keyword'].'%');
		}
		if($get['status'] >= '0' ){
			$condition_map['status'] = $get['status'];
		}
		$pagesize = I('get.limit');
    	$page = (I('get.page')-1)*$pagesize;
		$db_condition = new LifeConditionModel();
		$condition_data = $db_condition->getConditionList($condition_map,$page,$pagesize);
		$confirm_del = 'onclick="if(!confirm(' . "'确认删除本次民意？'" . ')){return false;}"';
		$status_name_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_color');

		foreach ($condition_data['data'] as &$condition){
			$condition['add_time'] = date("Y-m-d");
			$condition_id = $condition['condition_id'];
			//如果当前状态是待处理，显示指派按钮，否则不显示
			if($condition['status'] == "0"){
				$condition_delegate = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='delegate($condition_id)'>指派</a>";
			}else{
				$condition_delegate = "";
			}
			$condition['status'] = "<font color='" . $status_color_arr[$condition['status']] . "'>" . $status_name_arr[$condition['status']] . "</font> ";
			$condition['condition_title'] = "<a class='fcolor-22' href='" . U('life_condition_info', array('condition_id' => $condition_id)) . "'>".$condition['condition_title']."</a>";
			$condition['operate'] =$condition_delegate. "<a class='layui-btn layui-btn-del layui-btn-xs' href='" .  U('life_condition_del', array('condition_id' => $condition_id)) . "' $confirm_del>删除</a>";
		}

		$condition_data['code'] = 0;
		$condition_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($condition_data);
	}
	/**
	 * @name  life_condition_edit()
	 * @desc  搜集民情编辑/添加
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_condition_edit()
	{
		$condition_id = I("get.condition_id");
		$this->assign("condition_id",$condition_id);
		$communist_list=getCommunistList("","arr","1");
    	$this->assign("communist_list",$communist_list);
		$this->display("Lifecondition/life_condition_edit");
	}
	/**
	 * @name  life_condition_edit_save()
	 * @desc  搜集任务指派保存方法
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_condition_edit_save()
	{
		$post = I("post.");
		$post['condition_delegate_no'] = session('staff_no');
		$condition_personal = new LifeConditionPersonalModel();
		$log_data = $condition_personal->updateData($post,"condition_personal_id");
		if ($log_data) {
			//修改任务状态
			$condition = M('life_condition');
			$map['condition_id'] = $post['condition_id'];
			$con_data['status'] = $post['status'];
			$condition_data = $condition->where($map)->save($con_data);
			//增加操作日志
			$db_condition_log = M("life_condition_log");
			$con_map['condition_id'] = $post['condition_id'];
			$condition_info = $condition->where($con_map)->find();
			$communist_name = getStaffInfo(session("staff_no"),"staff_name");//当前登陆人
			$accept_name = getCommunistInfo($post['condition_accept_no'],"communist_name");//指派人名称
			$accept_party = getPartyInfo(getCommunistInfo($post['condition_accept_no'],"party_no"),"party_name");//指派人部门
			$con_title = $condition_info['condition_title'];//民情名称
			$log['condition_log_content'] = $communist_name." 将民情 '".$con_title."' 指派给了 '".$accept_party."' 支部的 ".$accept_name;
			$log['condition_id'] = $post['condition_id'];
			$log['add_time'] = date("Y-m-d H:i:s");
			$con_log_data = $db_condition_log->add($log);
			
			$alert_title = "您有一条指派任务";
			$alert_url = "Life/lifecondition/condition_id/".$post['condition_id'];
			saveAlertMsg('31', $_POST['condition_accept_no'],$alert_url, $alert_title,'','', '', session('staff_no'));
			if($condition_data){
				showMsg('success', '操作成功！！！', U('Lifevolunteer/life_volunteer_activity_index'),"1");
			}else{
				showMsg('error', '当前任务状态修改失败！！！','');
			}
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  life_condition_info()
	 * @desc  搜集民情详情
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_condition_info()
	{
		$condition_id = I("get.condition_id");
		$db_condition = new LifeConditionModel();
		$condition_data = $db_condition->getConditionInfo($condition_id);
		//查询指派信息
		$db_condition_personal=M('life_condition_personal');
		$condition_map['condition_id'] = $condition_data['condition_id'];
    	$condition_personal_data = $db_condition_personal->where($condition_map)->find();
    	if(!empty($condition_personal_data)){
	    	//指派人
	    	$condition_data['condition_delegate_no'] = getStaffInfo($condition_personal_data['condition_delegate_no'],"staff_name");
	    	//接收人
	    	$condition_data['condition_accept_no'] = getCommunistInfo($condition_personal_data['condition_accept_no'],"communist_name");
			//接收人部门
	    	$condition_data['condition_accept_party_name'] = getPartyInfo(getCommunistInfo($condition_personal_data['condition_accept_no'],"party_no"),"party_name");
    	}
        $condition_data['status_name'] = getStatusName('condition_status',$condition_data['status']);
		$this->assign("condition_data",$condition_data);
		
		//任务历程查询
		$db_condition_log=M('life_condition_log');
		$log_map['condition_id'] = $condition_data['condition_id'];
		$condition_log_data = $db_condition_log->where($log_map)->order("condition_log_id desc")->select();
		$this->assign("condition_log_data",$condition_log_data);
		
		$this->display("Lifecondition/life_condition_info");
	}
	/**
	 * @name  life_condition_del()
	 * @desc  搜集民情删除
	 * @param
	 * @return
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-11-21
	 */
	public function life_condition_del()
	{
		$data = I("get.");
		$db_condition = new LifeConditionModel();
		$result = $db_condition->delData($data);
		if ($result){
		    showMsg('success','操作成功',U('life_condition_index'));
        }else{
            showMsg('error','操作失败');
        }
	}
	/**
	 * @name  life_condition_personal_index()
	 * @desc  我的民情任务
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_condition_personal_index()
	{	
		checkAuth(ACTION_NAME);
		$this->display("Lifecondition/life_condition_personal_index");
	}
	/**
	 * @name  life_condition_personal_index_data()
	 * @desc  我的民情任务数据列表
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_condition_personal_index_data()
	{
		//实例化民情日志表
		$db_condition = new LifeConditionPersonalModel();
        $communist_no = session('staff_no');
        $status = I('get.status');
        $condition_map['condition_accept_no'] = $communist_no;
        if($status !== ''){
        	$condition_map['status'] = $status;
        }
		$condition_log_data['data'] = $db_condition->getConditionPersonalList($condition_map);
		//实例化民情表
		$db_condition = new LifeConditionModel();
		$confirm_del = 'onclick="if(!confirm(' . "'确认删除本次民意？'" . ')){return false;}"';
		$status_name_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_color');
		
		foreach ($condition_log_data['data'] as &$conditionlog){
			$condition_personal_id = $conditionlog['condition_personal_id'];
			$condition_id = $conditionlog['condition_id'];
			// 如果当前状态是待处理，显示指派按钮，否则不显示
			if($conditionlog['status'] == "10"){
				$condition_delegate = "<a class='btn green btn-xs btn-outline' href='".U('Lifecondition/life_condition_personal_save',array('condition_personal_id'=>$condition_personal_id,'condition_id'=>$conditionlog['condition_id'],'status'=>'20'))."'><i class='fa fa-edit'></i> 接受任务 </a>";
			}elseif($conditionlog['status'] == "20"){
				$condition_delegate = "<a class='btn green btn-xs btn-outline' href='".U('Lifecondition/life_condition_personal_save',array('condition_personal_id'=>$condition_personal_id,'condition_id'=>$conditionlog['condition_id'],'status'=>'30'))."'><i class='fa fa-edit'></i> 完成任务 </a>";
			}else{
				$condition_delegate = "<a class='layui-btn layui-btn-del layui-btn-xs' href='".U('Lifecondition/life_condition_personal_del',array('condition_id'=>$condition_id))."'$confirm_del><i class='fa fa-trash-o'></i> 删除 </a>";
			}
			$conditionlog['condition_id'] = " <a class='fcolor-22' href='" . U('life_condition_info', array('condition_id' => $condition_id)) . "' target='_self'>".$condition_id."</a> ";
			//判断当前我的任务状态
			$conditionlog['status'] = "<font color='" . $status_color_arr[$conditionlog['status']] . "'>" . $status_name_arr[$conditionlog['status']] . "</font> ";
			//查询民情主表信息
			$con_data = $db_condition->getConditionInfo($condition_id);
			$conditionlog['condition_personnel'] = $con_data['condition_personnel'];
			$conditionlog['condition_personnel_mobile'] = $con_data['condition_personnel_mobile'];
			$conditionlog['condition_title'] = " <a class='fcolor-22' href='" . U('life_condition_info', array('condition_id' => $condition_id)) . "' target='_self'>".$con_data['condition_title']."</a> ";
			$conditionlog['operate'] = " <a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('life_condition_info', array('condition_id' => $condition_id)) . "' target='_self'><i class='fa fa-info'></i> 详情</a> ".$condition_delegate;
		}
		$condition_log_data['code'] = 0;
		$condition_log_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($condition_log_data);
	}
	/**
	 * @name  life_condition_personal_save()
	 * @desc  修改我的任务状态保存
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_condition_personal_save()
	{
		$post = I("get.");
		$condition_log = new LifeConditionPersonalModel();
		$log_data = $condition_log->updateData($post,"condition_personal_id");
		if ($log_data) {
			//修改任务状态
			$condition = new LifeConditionModel();
			$condition_data = $condition->updateData($post,"condition_id");
			//增加操作日志
			$db_condition_log = M("life_condition_log");
			$condition_info = $condition->getConditionInfo($post['condition_id']);
			$communist_name = getStaffInfo(session("staff_no"),"staff_name");//当前登陆人
			$accept_party = getDeptInfo(getStaffInfo(session("staff_no"),"staff_dept_no"),"dept_name");//当前操作人员部门
			$con_title = $condition_info['condition_title'];//民情名称
			//根据所传参数判断当前操作是接受任务还是任务完成状态
			if($post['status'] == "20"){
				$log['condition_log_content'] = "'".$accept_party."' 部门的 ".$communist_name." 接受了 '".$con_title."' 任务进行处理";
			}else{
				$log['condition_log_content'] = "'".$accept_party."' 部门的 ".$communist_name."完成了 '".$con_title."' 任务";
			}
			$log['condition_id'] = $post['condition_id'];
			$log['add_time'] = date("Y-m-d H:i:s");
			$con_log_data = $db_condition_log->add($log);
			
		}
		if($condition_data){
			showMsg('success', '操作成功！！！', U('Lifecondition/life_condition_personal_index'));
		}else{
			showMsg('error', '当前任务状态修改失败！！！','');
		}
	}
	/**
	 * @name  life_condition_personal_del()
	 * @desc  删除我的任务数据
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-102-06
	 */
	public function life_condition_personal_del(){
		checkAuth(ACTION_NAME);
		$condition_id = I('get.condition_id');
		$condition_map['condition_id'] = $condition_id;
		$res = M('life_condition_personal')->where($condition_map)->delete();
		if($res){
			showMsg('success', '操作成功！！！', U('Lifecondition/life_condition_personal_index'));
		}else{
			showMsg('error', '当前任务状态修改失败！！！','');
		}
	}

	/**
	 * @name:life_condition_cat_index
	 * @desc：获取栏目首页
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-14
	 * @version：V1.0.0
	 **/
	public function life_condition_cat_index(){
		$this->display("Lifecondition/life_condition_cat_index");
	}
	/**
	 * @name:life_condition_cat_index_data
	 * @desc：获取栏目
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-14
	 * @version：V1.0.0
	 **/
	public function life_condition_cat_index_data(){
		$cat_list =$this->getConditioncatLists(0,-1);
		foreach($cat_list as $list){
			$array[] = $list;
		}
		$array['data'] = $array;
		$array['count'] = 0;
		$array['code'] = 0;
		$array['mag'] = 0;
		ob_clean();$this->ajaxReturn($array);
	}
	/**
	 * @name:life_condition_cat_edit
	 * @desc：栏目添加/编辑
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-15
	 * @version：V1.0.0
	 **/
	public function life_condition_cat_edit(){
		$life_bbs_cat = M('life_condition_category');
		$cat_list =$this->getConditioncatLists(0,-1);
		foreach($cat_list as $list){
			$array[] = $list;
		}
		$this->assign('cat_list',$array);

		if(!empty($_GET['cat_id'])){
			$cat_map['cat_id'] = $_GET['cat_id'];
			$cat_map['status'] = 1;
			$cat_data = $life_bbs_cat->where($cat_map)->find();
			$this->assign('cat_data',$cat_data);
		}
		$this->display("Lifecondition/life_condition_cat_edit");
	}
	
	/**
	 * @name:life_condition_cat_do_save
	 * @desc：栏目保存
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-15
	 * @version：V1.0.0
	 **/
	public function life_condition_cat_do_save(){
		$life_bbs_cat = M('life_condition_category');
		$post = $_POST;
		$post['add_staff'] = session('staff_no');
		$post['update_time'] = date('Y-m-d H:i:s');
		$cat_name = $post['cat_name'];
		$cat_name_map['cat_name'] = $cat_name;
		$res = $life_bbs_cat->where($cat_name_map)->select();
		if(empty($res)){
			if(!empty($post['cat_id'])){
				$cat_map['cat_id'] = $post['cat_id'];
				// $cat_map['cat_pid'] = '0';
				$cat_data = $life_bbs_cat->where($cat_map)->save($post);
			}else{
				$post['add_time'] = date('Y-m-d H:i:s');
				$cat_data = $life_bbs_cat->add($post);
			}
			if(!empty($cat_data)){
				showMsg('success', '操作成功！！！', U('life_condition_cat_index'));
			}else{
				showMsg('error', '操作失败！！！','');
			}
		}else{
			showMsg('error', '类型名称已存在！！！','');
		}
	}
	/**
	 * @name:life_condition_cat_do_del
	 * @desc：栏目删除
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-15
	 * @version：V1.0.0
	 **/
	public function life_condition_cat_do_del(){
		$cat_id = $_GET['cat_id'];
		$cat_map['cat_id'] = $cat_id;
		$res = M('life_condition_category')->where($cat_map)->delete();
		M('life_condition')->where("type_no = '$cat_id'")->delete();
		if($res){
			showMsg('success', '操作成功！！！', U('life_condition_cat_index'));
		}else{
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name:getConditioncatLists
	 * @desc：获取栏目列表数据
	 * @param：$cat_pid(父级no) $num(制表符数量)
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-14
	 * @version：V1.0.0
	 **/
	function getConditioncatLists($cat_pid = 0,$num = -1){
		$life_bbs_cat = M('life_condition_category');
		$cat_map['cat_pid'] = $cat_pid;
		$cat_list = $life_bbs_cat->where($cat_map)->select();
		$category_list = array();
		$symbol = "├─";
		$tabs = "";
		for($i = 0;$i <= $num; $i++){
			$tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$tabs .= $symbol;
		$num++;
		
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($cat_list as &$cat) {
			$cat['partcate'] = $tabs.$cat['cat_name'];
			if (!empty($cat['add_staff'])){
			    $cat['add_staff'] = $staff_name_arr[$cat['add_staff']];
			}
			$cat['operate'] = "<a class='layui-btn layui-btn-xs layui-btn-f60' href='" . U('Lifecondition/life_condition_cat_edit', array(
					'cat_id' => $cat['cat_id']
			)) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('Lifecondition/life_condition_cat_do_del', array(
					'cat_id' => $cat['cat_id']
			)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
			$category_list[] = $cat;
			$article_category_sonlist =$this-> getConditioncatLists($cat['cat_id'],$num);
			foreach($article_category_sonlist as &$sonlist){
				$category_list[] = $sonlist;
			}
		}
		return $category_list;
	}
}
