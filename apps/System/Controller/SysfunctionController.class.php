<?php
namespace System\Controller;
 // 命名空间
use Think\Controller;
use Common\Controller\BaseController;
class SysfunctionController extends BaseController // 继承Controller类
{
	/**
	 * @name  sys_function_index()
	 * @desc  功能管理首页
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2018-9-21
	 */
	public function sys_function_index()
	{
		checkAuth(ACTION_NAME);
		if(IS_AJAX){
			$function_list = $this->getFunctionList();
			//$this->assign('function_list',$function_list);
			$function_list = array(
				'code'=>0,
				'msg'=>'',
				'count'=>0,
				'data'=>$function_list
			);
			$this->ajaxReturn($function_list); // 返回json格式数据
		}
		
		$this->display("Sysfunction/sys_function_index");
	}
	/**
	 * @name  sys_function_ajaxdata()
	 * @desc  功能管理数据列表
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2018-09-21
	 */
	public function sys_function_ajaxdata(){
		$function_list = $this->getFunctionList();
		ob_clean();$this->ajaxReturn($function_list);
	}
	/**
	 * @name  sys_function_edit()
	 * @desc  模块/菜单-添加/编辑页面
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2018-09-21
	 */
	public function sys_function_edit()
	{
		checkAuth(ACTION_NAME);
		$edit = I('get.edit');
		$add = I('get.add');
		$sys_function = M('sys_function');
		$function_id = I('get.function_id');
		$function_map['function_id'] = $function_id;
		$function_data = $sys_function->where($function_map)->find();
		$function_list =  $this->getFunctionList();
		if($add){
			$this->assign('function_pid',$function_data['function_id']);
		}else if($edit){
			$this->assign('function_data',$function_data);
			$this->assign('function_id',$function_id);
		}
		$this->assign('edit',$edit);
		$this->assign('add',$add);
		$this->assign('function_list',$function_list);
		$this->display("Sysfunction/sys_function_edit");
	}
	/**
	 * @name  sys_function_do_save()
	 * @desc  模块/菜单-添加/编辑-执行操作
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2018-09-21
	 */
	public function sys_function_do_save()
	{

		$sys_function = M('sys_function');
		$data = I('post.');
		$data['group_code'] = 'communist';
		$function_id = $data['function_id'];
		if($data['edit'] == 1){//编辑执行
			$data['update_time'] = date('Y-m-d H:i:s');
			$function_oldid = $data['function_oldid'];
			if($function_id == $function_oldid){
				$function_map['function_id'] = $function_oldid;
				$function_data = $sys_function->where("function_id = '$function_oldid'")->save($data);
				if($function_data){
					showMsg('success','操作成功！',U('sys_function_index'));
				}else{
					showMsg('error','操作失败！');
				}
			}else{
				$function_map['function_id'] = $function_id;
				$function_data = $sys_function->where("function_id = '$function_id'")->find();
				if($function_data){
					showMsg('error','菜单ID已存在，ID冲突！');
				}else{
					$function_map['function_id'] = $function_oldid;
					$function_data = $sys_function->where("function_id = '$function_oldid'")->save($data);
					if($function_data){
						showMsg('success','操作成功！',U('sys_function_index'));
					}else{
						showMsg('error','操作失败！');
					}
				}
			}
		}else if($data['add'] == 1){//添加执行
			$data['add_time'] = date('Y-m-d H:i:s');
			$data['update_time'] = date('Y-m-d H:i:s');
			$function_map['function_id'] = $function_id;
			$function_data = $sys_function->where($function_map)->find();
			if($function_data){
				showMsg('error','菜单ID已存在，ID冲突！');
			}else{
				$function_data = $sys_function->add($data);
				if($function_data){
					showMsg('success','操作成功！',U('sys_function_index'));
				}else{
					showMsg('error','操作失败！');
				}
			}
		}
	}
	/**
	 * @name  sys_function_do_del()
	 * @desc  模块/菜单-删除操作 （修改为假删除）
	 * @param 
	 * @return 
	 * @author 王宗彬 杨陆海
	 * @version 版本 V1.0.0
	 * @addtime   2018-09-21
	 */
	public function sys_function_do_del()
	{
		checkAuth(ACTION_NAME);
		$sys_function = M('sys_function');
		$function_id = I('get.function_id');
		$function_list = $this->getFunctionList($function_id);
		$function_map['function_id'] = $function_id;
		$type = I('get.type');
		if($type == 1){
			$function_del = $sys_function->where($function_map)->setField('status',1);
			if($function_del){
				foreach($function_list as $list){
					$function_map['function_id'] = $list['function_id'];
					$function_del = $sys_function->where($function_map)->setField('status',1);
				}
				showMsg('success','操作成功！',U('sys_function_index'));
			}else{
				showMsg('error');
			}
		}elseif ($type == 2){
			$function_del = $sys_function->where($function_map)->delete();
			if($function_del){
				foreach($function_list as $list){
					$function_map['function_id'] = $list['function_id'];
					$function_del = $sys_function->where($function_map)->delete();
				}
				showMsg('success','操作成功！',U('sys_function_index'));
			}else{
				showMsg('error');
			}
		}else{
			$function_del = $sys_function->where($function_map)->setField('status',0);
			if($function_del){
				foreach($function_list as $list){
					$function_map['function_id'] = $list['function_id'];
					$function_del = $sys_function->where($function_map)->setField('status',0);
				}
				showMsg('success','操作成功！',U('sys_function_index'));
			}else{
				showMsg('error');
			}
		}
		
	}
	/**
	 * @name  getFunctionList()
	 * @desc  获取菜单列表
	 * @param $function_pid(父级ID)$num(制表符数量)
	 * @return 菜单列表ajax数据列表
	 * @author 王宗彬
	 * @version 版本 V1.0.2
	 * @updatetime  2016-07-21
	 * @addtime   2018-09-21
	 */
	public function getFunctionList($function_pid = 0,$num = -1){
		$sys_function = M('sys_function');
		$confirm = 'onclick="if(!confirm(' . "'确认停用？子级菜单也将被停用！'" . ')){return false;}"';
		$conf = 'onclick="if(!confirm(' . "'确认启用？子级菜单也将被启用！'" . ')){return false;}"';
		$del = 'onclick="if(!confirm(' . "'确认删除？子级菜单也将被删除！'" . ')){return false;}"';
		$symbol = "├─";
		$tabs = "";
		for($i = 0;$i <= $num; $i++){
			$tabs .= "│&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$tabs .= $symbol;
		$num++;
		$function_list = array();
		$function_map['function_pid'] = $function_pid;
		$function_map['group_code'] = 'communist';
		$sys_function_list = $sys_function->where($function_map)->select();
		foreach($sys_function_list as &$list){
			$but = '';
			//$list['function_name'] = $tabs.'<i class="fa '.$list['function_icon'].'"></i> '.$list['function_name'];
			$list['function_name'] = $tabs.$list['function_name'];
			if($list['status']=='1'){
				$list['status_name']="<font color=''>正常</font>";
				$but = "<a class='btn red btn-xs btn-outline' href='" . U('sys_function_do_del', array(
					'function_id' => $list['function_id'],
				)) . "' $confirm ><i class='fa fa-trash-o'></i>停用</a>";
			}else{
				$list['status_name']="<font color='red'>停用</font>";
				$but = "<a class='btn blue btn-xs btn-outline' href='" . U('sys_function_do_del', array(
					'function_id' => $list['function_id'],'type'=>1
				)) . "' $conf ><i class='fa fa-trash-o'></i>启用</a>";
			}
			$list['operate'] = "<a class='btn blue btn-xs btn-outline' href='" . U('sys_function_edit', array(
					'function_id' => $list['function_id'],
					'add' => 1
				)) . "' target='_self'><i class='fa fa-plus-square-o'></i> 子模块</a> <a class='btn green btn-xs btn-outline' href='" . U('sys_function_edit', array(
					'function_id' => $list['function_id'],
					'edit' => 1
				)) . "' target='_self'><i class='fa fa-edit'></i> 编辑</a> <a class='btn red btn-xs btn-outline' href='" . U('sys_function_do_del', array('function_id' => $list['function_id'],'type'=>2)) . "' $del ><i class='fa fa-trash-o'></i>删除</a>".$but;
			$function_list[] = $list;
			$sys_function_sonlist = $this->getFunctionList($list['function_id'],$num);
			foreach($sys_function_sonlist as &$sonlist){
				$function_list[] = $sonlist;
			}
		}
		return $function_list;
	}
}
