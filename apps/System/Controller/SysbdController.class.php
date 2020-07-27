<?php
namespace System\Controller;
 // 命名空间
use Think\Controller;
use Common\Controller\BaseController;
use Zend\Db\Sql\Where;

class SysbdController extends BaseController // 继承Controller类
{
	 /**
	 * @name:sys_bd_type_index               
	 * @desc：类型首页
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_index()
	{
		checkAuth(ACTION_NAME);
		$group_id = I('get.group_id');
		$db_group = M('bd_group');
		$group_list = $db_group->where("group_pid=2")->select();
		$this->assign('group_list', $group_list);
		$this->assign('group_id', $group_id);
		$this->display("Sysbd/sys_bd_type_index");
	} 
	/**
	 * @name:sys_bd_type_table               
	 * @desc：类型列表
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-09-14
	 * @version：V1.0.0
	**/
	public function sys_bd_type_table()
	{
		$group_id = I('get.group_id');
		$this->assign('group_id',$group_id);
		$this->display("Sysbd/sys_bd_type_table");
	}
	/**
	 * @name:sys_bd_type_index_data               
	 * @desc：类型首页数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_index_data()
	{
		$db_type = M('bd_type');
		$db_group = M('bd_group');
		$group_id = I('get.group_id');
		$page = I('get.page');
		$limit = I('get.limit');
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		if (! empty($group_id)) {
			$db_group_map['group_id'] = $group_id;
			$group_row = $db_group->where($db_group_map)->find();
			$group_map['type_group'] = $group_row['group_code'];
		}
		$type_list = $db_type->where($group_map)->limit(($page-1)*$limit,$limit)->select();
		$count = $db_type->where($group_map)->count();
		$num = 0;
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($type_list as &$type) {
			$num ++;
			$type['num'] = $num;
			$type['add_staff'] = $communist_name_arr[$type['add_staff']];
			$type['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('sys_bd_type_edit', array(
				'type_id' => $type['type_id'],
				'group_id' => $group_id
			)) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('sys_bd_type_do_del', array(
				'type_id' => $type['type_id']
			)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		ob_clean();
		$type_list = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$type_list
		);
		$this->ajaxReturn($type_list); // 返回json格式数据
	}
	
	/**
	 * @name:sys_bd_type_edit               
	 * @desc：类型修改页面
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_edit()
	{
		checkAuth(ACTION_NAME);
		$db_type = M('bd_type');
		$db_group = M('bd_group');
		$group_list = $db_group->where("group_pid = 2")->select();
		$this->assign("group_list", $group_list);
		$type_id = I('get.type_id'); // I方法获取数据
		if (! empty($type_id)) { // 必要的非空判断需要增加,防止报错
			$type_map['type_id'] = $type_id;
			$type_row = $db_type->where($type_map)->find();
			$this->assign("type_row", $type_row); // 控制器与视图页面的变量尽量保持一致
			$where= "type_group = '$type_row[type_group]'";
			$group_map['group_code'] = $type_row['type_group'];
			$group_info=$db_group->where($group_map)->find();
			$this->assign("group_id", $group_info['group_id']);
		}
		$type_list = $db_type->where($where)->select();
		$this->assign("type_list", $type_list);
		$this->display("Sysbd/sys_bd_type_edit");
	}
	/**
	 * @name:sys_bd_type_do_save               
	 * @desc：类型保存方法
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_do_save()
	{
		checkLogin();
		$db_type = M('bd_type');
		$data = I('post.'); // I方法获取整个数组
		$type_id=$data['type_id'];
		if (! empty($type_id)) { // 有id时执行修改操作
			$data['update_time'] = date("Y-m-d H:i:s");
			$oper_res = $db_type->save($data);
		} else { // 无id时执行添加操作
		    $type_no=$data['type_no'];
			$data['type_group']=$data['type_group'];
			if (! empty($type_no)) {
				if (checkRepeat('bd_type', 'type_no', $data['type_no'],'type_group',$data['type_group'])) {
					showMsg('error', '编号已存在,请重新填写','');
				} else {
					$data['add_time'] = date("Y-m-d H:i:s");
					$data['add_staff'] = session('staff_no');
					$oper_res = $db_type->add($data);
				}
			} else {
				showMsg('error', '请将内容填写完整！','');
			}
		}
		if ($oper_res) {
			showMsg('success', '操作成功！！！', U('Sysbd/sys_bd_type_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:sys_bd_type_list               
	 * @desc：类型列表
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_list()
	{
		checkAuth(ACTION_NAME);
		$this->display("Sysbd/sys_bd_type_list");
	}
	/**
	 * @name:sys_bd_type_ajax               
	 * @desc：类型数据ajax获取
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_ajax()
	{
		$db_type = M('bd_type');
		$group_code = I("get.group_code");
		$type_map['type_group'] = $group_code;
		$type_list = $db_type->where($type_map)->select();
		ob_clean();$this->ajaxReturn($type_list);
	}
	/**
	 * @name:sys_bd_type_do_del               
	 * @desc：类型删除
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_type_do_del()
	{
		checkAuth(ACTION_NAME);
		$db_type = M('bd_type');
		$type_id = I('get.type_id'); // I方法获取数据
		if (! empty($type_id)) { // 必要的非空判断需要增加
			$type_map['type_id'] = $type_id;
			$del_res = $db_type->where($type_map)->delete();
		}
		if ($del_res) {
			showMsg('success', '操作成功！！！', U('Sysbd/sys_bd_type_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:sys_bd_tree_type_data               
	 * @desc：左侧tree目录数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_tree_type_data()
	{
		$db_group = M('bd_group');
		$group_id = I("get.group_id");
		$group_list = $db_group->where("group_pid = 2")->select();
		foreach ($group_list as &$group) {
			$group[id] = $group['group_id'];
			if ($group['group_pid'] == 2) {
				$group['parent'] = '#';
			} else {
				$group['parent'] = $group['group_pid'];
			}
			$group[text] = $group['group_name'];
			if ($group_id == $group['group_id']) {
				$group[text] = "<span style='background:gray;color:white'>" . $group['group_name'] . "</span>";
			}
			$group[icon] = 'fa fa-folder icon-lg icon-state-success';
		}
		ob_clean();$this->ajaxReturn($group_list);
	}
	// (8) 类型表（用于iframe引用）
	/*
	 * public function sys_bd_type_table()
	 * {
	 * checkAuth(ACTION_NAME);
	 * $group_code = I("get.group_code");
	 * if(!empty($group_code)){
	 * $this->assign("group_code",$group_code);
	 * }
	 * $this->display("Sysbd/sys_bd_type_index");
	 * }
	 */
	// (9) 类型表数据页面（用于iframe引用）
	/*
	 * public function sys_bd_type_table_data()
	 * {
	 * $db_type=M('bd_type');
	 * $group_code = I("get.group_code");
	 * $num=0;
	 * $where = "";
	 * if(!empty($group_code)){
	 * $where .= "group_code = '$group_code'";
	 * }
	 * $confirm = 'onclick="if(!confirm('."'确认删除？'".')){return false;}"';
	 * $type_list = $db_type->where($where)->select();
	 * foreach($type_list as &$type){
	 * $num++;
	 * $type['num']=$num;
	 * $type['add_staff']=getStaffInfo($type['add_staff'],'staff_name');
	 * $type['operate']="<a class='btn btn-xs blue btn-outline' href='".U('sys_bd_type_edit',array('type_id'=>$type['type_id']))."'><i class='fa fa-edit'></i> 编辑</a> "
	 * ."<a class='btn btn-xs red btn-outline' href='".U('sys_bd_type_do_del',array('type_id'=>$type['type_id']))."'><i class='fa fa-trash-o'></i> 删除 </a> ";
	 * }
	 * ob_clean();$this->ajaxReturn($type_list);
	 * }
	 */
	/**
	 * @name:sys_bd_code_index               
	 * @desc：基础资料首页
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_index()
	{
		
		checkAuth(ACTION_NAME);
		$group_id = I('get.group_id');
		$db_group = M('bd_group');
		$group_list = $db_group->where("group_pid=3")->select();
		$this->assign('group_list', $group_list);
		$this->assign('group_id', $group_id);
		$this->display("Sysbd/sys_bd_code_index");
	}
	/**
	 * @name:sys_bd_code_index_data               
	 * @desc：基础资料首页数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_index_data()
	{
		$db_code = M('bd_code');
		$db_group = M('bd_group');
		$group_id = I('get.group_id');
		$page = I('get.page');
		$limit = I('get.limit');
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		if (! empty($group_id)) {
			$db_group_map['group_id'] = $group_id;
			$group_row = $db_group->where($db_group_map)->find();
			$code_map['code_group'] = $group_row['group_code'];
		}
		$code_list = $db_code->where($code_map)->limit(($page-1)*$limit,$limit)->select();
		$count = $db_code->where($code_map)->count();
		$num = 0;
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($code_list as &$code) {
			$num ++;
			$code['num'] = $num;
			$code['add_staff'] = $communist_name_arr[$code['add_staff']];
			$code['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('sys_bd_code_edit', array(
				'code_id' => $code['code_id'],
				'group_id' => $group_id
			)) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('sys_bd_code_do_del', array(
				'code_id' => $code['code_id']
			)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		ob_clean();
		$code_list = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$code_list
		);
		$this->ajaxReturn($code_list); // 返回json格式数据
	}
	/**
	 * @name:sys_bd_code_edit               
	 * @desc：添加/编辑，添加为_add
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_edit()
	{
		checkAuth(ACTION_NAME);
		$db_code = M('bd_code');
		$db_group = M('bd_group');
		$group_id = I("get.group_id");
		$this->assign("group_id", $group_id);
		$group_list = $db_group->where("group_pid = 3")->select();
		$this->assign("group_list", $group_list);
		$code_id = I('get.code_id'); // I方法获取数据
		if (! empty($code_id)) { // 必要的非空判断需要增加,防止报错
			$code_map['code_id'] = $code_id;
			$code_row = $db_code->where($code_map)->find();
			$this->assign("code_row", $code_row); // 控制器与视图页面的变量尽量保持一致
			$group_map['code_group'] = $code_row['code_group'];
		}
		
		/* if (! empty($group_id)) {
			$db_group_map['group_id'] = $group_id;
			$group_row = $db_group->where($db_group_map)->find();
			$group_map['code_group'] = $group_row['group_code'];
		} */
		$code_list = $db_code->where($group_map)->select();
		$this->assign("code_list", $code_list);
		$this->display("Sysbd/sys_bd_code_edit");
	}

	/**
	 * @name:sys_bd_code_do_save               
	 * @desc：编码保存
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_do_save()
	{
		checkLogin();
		$db_code = M('bd_code');
		$data = I('post.'); // I方法获取整个数组
		$code_id=$data['code_id'];
		if (!empty($code_id)) { // 有id时执行修改操作
			$data['update_time'] = date("Y-m-d H:i:s");
			$oper_res = $db_code->save($data);
		} else { // 无id时执行添加操作
		    $code_no=$data['code_no'];
			if (!empty($code_no)) {
				$code_map['code_group'] = $data['code_group'];
				$code_map['code_no'] = $data['code_no'];
				$code_count = M('bd_code')->where($code_map)->count();
				if ($code_count > 0) {
					showMsg('error', '该分组下编号已存在,请重新填写','');
				} else {
					$data['add_time'] = date("Y-m-d H:i:s");
					$data['add_staff'] = session('staff_no');
					$oper_res = $db_code->add($data);
				}
			} else {
				showMsg('error', '请将内容填写完整！','');
			}
		}
		if ($oper_res) {
			showMsg('success', '操作成功！！！',  U('Sysbd/sys_bd_code_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:sys_bd_code_ajax               
	 * @desc：编码数据ajax获取
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_ajax()
	{
		$db_code = M('bd_code');
		$group_code = I("get.group_code");
		$group_map['code_group'] = $group_code;
		$code_list = $db_code->where($group_map)->select();
		ob_clean();$this->ajaxReturn($code_list);
	}
	/**
	 * @name:sys_bd_code_list               
	 * @desc：编码列表
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_list()
	{
		checkAuth(ACTION_NAME);
		$this->display("Sysbd/sys_bd_code_list");
	}
	/**
	 * @name:sys_bd_code_do_del               
	 * @desc：编码删除
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_code_do_del()
	{
		checkAuth(ACTION_NAME);
		$db_code = M('bd_code');
		$code_id = I('get.code_id'); // I方法获取数据
		if (! empty($code_id)) { // 必要的非空判断需要增加
			$code_map['code_id'] = $code_id;
			$del_res = $db_code->where($code_map)->delete();
		}
		if ($del_res) {
			showMsg('success', '操作成功！！！',  U('Sysbd/sys_bd_code_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:sys_bd_tree_code_data               
	 * @desc：左侧tree目录编码数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_tree_code_data()
	{
		$db_group = M('bd_group');
		$group_id = I("get.group_id");
		$group_list = $db_group->where("group_pid = 3")->select();
		foreach ($group_list as &$group) {
			$group[id] = $group['group_id'];
			if ($group['group_pid'] =="3") {
				$group['parent'] = '#';
			} else {
				$group['parent'] = $group['group_pid'];
			}
			$group[text] = $group['group_name'];
			if ($group_id == $group['group_id']) {
				$group[text] = "<span style='background:gray;color:white'>" . $group['group_name'] . "</span>";
			}
			$group[icon] = 'fa fa-folder icon-lg icon-state-success';
		}
		ob_clean();$this->ajaxReturn($group_list);
	}
	// (8) 类型表（用于iframe引用）
	/*
	 * public function sys_bd_code_table()
	 * {
	 * checkAuth(ACTION_NAME);
	 * $type_code = I("get.type_code");
	 * if(!empty($type_code)){
	 * $this->assign("type_code",$type_code);
	 * }
	 * $this->display("Sysbd/sys_bd_type_index");
	 * }
	 */
	// (9) 类型表数据页面（用于iframe引用）
	/*
	 * public function sys_bd_code_table_data()
	 * {
	 * $db_code=M('bd_code');
	 * $type_code = I("get.type_code");
	 * $num=0;
	 * $where = "";
	 * if(!empty($type_code)){
	 * $where .= "code_group = '$type_code'";
	 * }
	 * $confirm = 'onclick="if(!confirm('."'确认删除？'".')){return false;}"';
	 * $code_list = $db_code->where($where)->select();
	 * foreach($code_list as &$code){
	 * $num++;
	 * $code['num']=$num;
	 * $code['add_staff']=getStaffInfo($code['add_staff'],'communist_name');
	 * $code['operate']="<a class='btn btn-xs blue btn-outline' href='".U('sys_bd_code_edit',array('code_id'=>$code['code_id']))."'><i class='fa fa-edit'></i> 编辑</a> "
	 * ."<a class='btn btn-xs red btn-outline' href='".U('sys_bd_code_do_del',array('code_id'=>$code['code_id']))."'><i class='fa fa-trash-o'></i> 删除 </a> ";
	 * }
	 * ob_clean();$this->ajaxReturn($code_list);
	 * }
	 */
	/**
	 * @name:sys_bd_status_index               
	 * @desc：状态管理首页
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_status_index()
	{
		checkAuth(ACTION_NAME);
		$group_id = I('get.group_id');
		$db_group = M('bd_group');
		$group_list = $db_group->where("group_pid=1")->select();
		$this->assign('group_list', $group_list);
		$this->assign('group_id', $group_id);
		$this->display("Sysbd/sys_bd_status_index");
	}
	/**
	 * @name:sys_bd_status_index_data               
	 * @desc：状态管理首页数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_status_index_data()
	{
		$db_status = M('bd_status');
		$db_group = M('bd_group');
		$group_id = I('get.group_id');
		$page = I('get.page');
		$limit = I('get.limit');
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		if (! empty($group_id)) {
			$db_group_map['group_id'] = $group_id;
			$group_row = $db_group->where($db_group_map)->find();
			$group_map['status_group'] = $group_row['group_code'];
		}
		$status_list = $db_status->where($group_map)->limit(($page-1)*$limit,$limit)->select();
		$count = $db_status->where($group_map)->count();
		$num = 0;
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($status_list as &$status) {
			$num ++;
			$status['num'] = $num;
			$status['status_name'] = "<font color='" . $status[status_color] . "'>$status[status_name]</font>";
			$status['add_staff'] = $communist_name_arr[$status['add_staff']];
			$status['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('sys_bd_status_edit', array(
				'status_id' => $status['status_id'],
				'group_id' => $group_id
			)) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('sys_bd_status_do_del', array(
				'status_id' => $status['status_id']
			)) . "'$confirm ><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		ob_clean();
		$status_list = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$status_list
		);
		$this->ajaxReturn($status_list); // 返回json格式数据
	}
	
	/**
	 * @name:sys_bd_status_edit               
	 * @desc：添加/编辑，单独添加为_add
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_status_edit()
	{
		checkAuth(ACTION_NAME);
		$db_status = M('bd_status');
		$db_group = M('bd_group');
		$group_id = I("get.group_id");
		$this->assign("group_id", $group_id);
		$group_list = $db_group->where("group_pid = 1")->select();
		$this->assign("group_list", $group_list);
		$status_id = I('get.status_id'); // I方法获取数据
		if (! empty($status_id)) { // 必要的非空判断需要增加,防止报错
			$status_map['status_id'] = $status_id;
			$status_row = $db_status->where($status_map)->find();
			$this->assign("status_row", $status_row); // 控制器与视图页面的变量尽量保持一致
		}
		if (! empty($group_id)) {
			$db_group_map['group_id'] = $group_id;
			$group_row = $db_group->where($db_group_map)->find();
			$group_map['status_group'] = $group_row['group_code'];
		}
		$status_list = $db_status->where($group_map)->select();
		$this->assign('status_list', $status_list);
		$this->display("Sysbd/sys_bd_status_edit");
	}
	/**
	 * @name:sys_bd_status_do_save               
	 * @desc：状态数据保存
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_status_do_save()
	{
		checkLogin();
		$db_status = M('bd_status');
		$data = I('post.'); // I方法获取整个数组
		$status_id=$data['status_id'];
		if (! empty($status_id)) { // 有id时执行修改操作
			$data['update_time'] = date("Y-m-d H:i:s");
			$oper_res = $db_status->save($data);
		} else { // 无id时执行添加操作
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['add_staff'] = session('staff_no');
			$oper_res = $db_status->add($data);
		}
		if ($oper_res) {
			showMsg('success', '操作成功！！！',U('Sysbd/sys_bd_status_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:sys_bd_status_list               
	 * @desc：状态列表
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_status_list()
	{
		checkAuth(ACTION_NAME);
		$this->display("Sysbd/sys_bd_status_list");
	}
	/**
	 * @name:sys_bd_status_do_del               
	 * @desc：状态删除
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_status_do_del()
	{
		checkAuth(ACTION_NAME);
		$db_status = M('bd_status');
		$status_id = I('get.status_id'); // I方法获取数据
		if (! empty($status_id)) { // 必要的非空判断需要增加
			$status_map['status_id'] = $status_id;
			$del_res = $db_status->where($status_map)->delete();
		}
		if ($del_res) {
		showMsg('success', '操作成功！！！',U('Sysbd/sys_bd_status_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:sys_bd_tree_status_data               
	 * @desc：左侧tree目录状态数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_tree_status_data()
	{
		$db_group = M('bd_group');
		$group_id = I("get.group_id");
		$group_list = $db_group->where("group_pid = 1")->select();
		foreach ($group_list as &$group) {
			$group[id] = $group['group_id'];
			if ($group['group_pid'] == 1) {
				$group['parent'] = '#';
			} else {
				$group['parent'] = $group['group_pid'];
			}
			$group[text] = $group['group_name'];
			if ($group_id == $group['group_id']) {
				$group[text] = "<span style='background:gray;color:white'>" . $group['group_name'] . "</span>";
			}
			$group[icon] = 'fa fa-folder icon-lg icon-state-success';
		}
		ob_clean();$this->ajaxReturn($group_list);
	}
	// (8) 类型表（用于iframe引用）
	/*
	 * public function sys_bd_status_table()
	 * {
	 * checkAuth(ACTION_NAME);
	 * $type_code = I("get.type_code");
	 * if(!empty($type_code)){
	 * $this->assign("type_code",$type_code);
	 * }
	 * $this->display("Sysbd/sys_bd_type_index");
	 * }
	 */
	// (9) 类型表数据页面（用于iframe引用）
	/*
	 * public function sys_bd_status_table_data()
	 * {
	 * $db_status=M('bd_status');
	 * $type_code = I("get.type_code");
	 * $num=0;
	 * $where = "";
	 * if(!empty($type_code)){
	 * $where .= "status_group = '$type_code'";
	 * }
	 * $confirm = 'onclick="if(!confirm('."'确认删除？'".')){return false;}"';
	 * $status_list = $db_status->where($where)->select();
	 * foreach($status_list as &$status){
	 * $num++;
	 * $status['num']=$num;
	 * $status['add_staff']=getStaffInfo($status['add_staff'],'communist_name');
	 * $status['operate']="<a class='btn btn-xs blue btn-outline' href='".U('sys_bd_status_edit',array('status_id'=>$status['status_id']))."'><i class='fa fa-edit'></i> 编辑</a> "
	 * ."<a class='btn btn-xs red btn-outline' href='".U('sys_bd_status_do_del',array('status_id'=>$status['status_id']))."'><i class='fa fa-trash-o'></i> 删除 </a> ";
	 * }
	 * ob_clean();$this->ajaxReturn($status_list);
	 * }
	 */
	 /**
	 * @name:sys_bd_group_index               
	 * @desc：分组管理首页
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_group_index()
	{
		checkAuth(ACTION_NAME);
		$this->display("Sysbd/sys_bd_group_index");
	}
	 /**
	 * @name:sys_bd_group_index_data               
	 * @desc：分组管理首页数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_group_index_data()
	{
		$db_group = M('bd_group');
		$db_group = M('bd_group');
		$group_id = I('get.group_id');
		$page = I('get.page');
		$limit = I('get.limit');
		$group_list = $db_group->limit(($page-1)*$limit,$limit)->select();
		$count = $db_group->limit(($page-1)*$limit,$limit)->count();
		$num = 0;
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($group_list as &$group) {
			$num ++;
			$group['num'] = $num;
			$group['add_staff'] = $communist_name_arr[$group['add_staff']];
			$group['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('sys_bd_group_edit', array(
				'group_id' => $group['group_id']
			)) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('sys_bd_group_do_del', array(
				'group_id' => $group['group_id']
			)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		ob_clean();
		$group_list = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$group_list
		);
		$this->ajaxReturn($group_list); // 返回json格式数据
	}
	 /**
	 * @name:sys_bd_group_edit               
	 * @desc：添加/编辑，单独添加为_add
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_group_edit()
	{
		checkAuth(ACTION_NAME);
		$db_group = M('bd_group');
		$group_list = $db_group->select();
		$group_id = I('get.group_id'); // I方法获取数据
		if (! empty($group_id)) { // 必要的非空判断需要增加,防止报错
			$group_map['group_id'] = $group_id;
			$group_row = $db_group->where($group_map)->find();
			$this->assign("group_row", $group_row); // 控制器与视图页面的变量尽量保持一致
		}
		$this->assign('group_list', $group_list);
		$this->display("Sysbd/sys_bd_group_edit");
	}
	 /**
	 * @name:sys_bd_group_do_save               
	 * @desc：分组数据
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_group_do_save()
	{
		checkLogin();
		$db_group = M('bd_group');
		$data = I('post.'); // I方法获取整个数组
		if (! empty($data['group_id'])) { // 有id时执行修改操作
			$data['update_time'] = date("Y-m-d H:i:s");
			$oper_res = $db_group->save($data);
		} else { // 无id时执行添加操作
			if (! empty($data['group_no'])) {
				if (checkRepeat('bd_group', 'group_no', $data['group_no'])) {
					showMsg('error', '编号已存在,请重新填写','');
				} else {
					$data['group_pid'] = 0;
					$data['add_time'] = date("Y-m-d H:i:s");
					$data['add_staff'] = session('staff_no');
					$oper_res = $db_group->add($data);
				}
			} else {
				showMsg('error', '请将内容填写完整！','');
			}
		}
		if ($oper_res) {
			showMsg('success', '操作成功！！！', U('Sysbd/sys_bd_group_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	 /**
	 * @name:sys_bd_group_do_del               
	 * @desc：分组数据删除
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
	public function sys_bd_group_do_del()
	{
		checkAuth(ACTION_NAME);
		$db_group = M('bd_group');
		$group_id = I('get.group_id'); // I方法获取数据
		if (! empty($group_id)) { // 必要的非空判断需要增加
			$group_map['group_id'] = $group_id;
			$del_res = $db_group->where($group_map)->delete();
		}
		if ($del_res) {
			showMsg('success', '操作成功！！！', U('Sysbd/sys_bd_group_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
}
