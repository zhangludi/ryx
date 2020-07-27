<?php
namespace System\Controller;
 // 命名空间
use Think\Controller;
use Common\Controller\BaseController;

class SysauthController extends BaseController // 继承Controller类
{
	 /**
	 * @name:sys_role_index               
	 * @desc：角色管理首页
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_index()
    {
    	checkAuth(ACTION_NAME);
        
        $this->display("Sysauth/sys_role_index");
    }
	 /**
	 * @name:sys_role_index_data               
	 * @desc：角色管理首页数据加载
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_index_data()
    {
        $industry = getConfig('industry');
        $sys_role = M('sys_role');
        $sys_list = $sys_role->where("role_type = 1")->select();
        $confirm = 'onclick="if(!confirm(' . "'确认停用？'" . ')){return false;}"';
        $confirms = 'onclick="if(!confirm(' . "'确认启用？'" . ')){return false;}"';
        $num = 1;
        
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        $type_name_arr = M('bd_type')->where("type_group = 'role_type'")->getField('type_no,type_name');
        foreach ($sys_list as &$role) {
            if ($industry == 'pam'){
				if ($role['role_type'] == '1'){
					$role['dt'] = 'communist';
				}else{
					$role['dt'] = 'weixin';
				}
                if ($role['role_type'] == '1'){
                    $button = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='role_edit(" . $role['role_id'] . ")' href='javascript:volid(0);'>编辑</a>  "
                        ."<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('Sysauth/sys_role_auth_list', array('role_id' => $role['role_id'],'dt'=>'communist')) . "'>授权 </a>  ";
                }else{
                    $button = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='role_edit(" . $role['role_id'] . ")' href='javascript:volid(0);'>编辑</a>  "
                        ."<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('Sysauth/sys_role_auth_list', array('role_id' => $role['role_id'],'dt'=>'weixin')) . "'>授权 </a>  ";
                } 
                $role['role_type'] = $type_name_arr[$role['role_type']];
                 if($role['status']==0)
                {
                    $role['operate'] =  $button."<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('sys_role_do_del', array('role_id' => $role['role_id'],'is_enable'=>'yes')) . "'$confirms>启用 </a>  ";
                }else{
                    $role['operate'] = $button."<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('sys_role_do_del', array('role_id' => $role['role_id'])) . "'$confirm>停用 </a>  ";
                } 
            }else{
                if($role['status']==0)
                {
                    $role['operate'] = "<a class='btn btn-xs blue btn-outline' onclick='role_edit(" . $role['role_id'] . ")' href='javascript:volid(0);'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'communist'
                        )) . "'><i class='fa fa-info-circle'></i>授权 </a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'agent'
                        )) . "'><i class='fa fa-info-circle'></i>门店授权 </a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'cust'
                        )) . "'><i class='fa fa-info-circle'></i>客户授权 </a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'app'
                        )) . "'><i class='fa fa-info-circle'></i>APP授权 </a>  " . "<a class='btn btn-xs blue btn-outline' href='" . U('sys_role_do_del', array(
                            'role_id' => $role['role_id'],'is_enable'=>'yes'
                        )) . "'$confirms><i class='fa fa-trash-o'></i> 启用 </a>  ";
                }else{
                    $role['operate'] = "<a class='btn btn-xs blue btn-outline' onclick='role_edit(" . $role['role_id'] . ")' href='javascript:volid(0);'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'communist'
                        )) . "'><i class='fa fa-info-circle'></i>授权 </a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'agent'
                        )) . "'><i class='fa fa-info-circle'></i>门店授权 </a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'cust'
                        )) . "'><i class='fa fa-info-circle'></i>客户授权 </a>  " . "<a class='btn btn-xs green btn-outline' href='" . U('Sysauth/sys_role_auth_list', array(
                            'role_id' => $role['role_id'],'dt'=>'app'
                        )) . "'><i class='fa fa-info-circle'></i>APP授权 </a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('sys_role_do_del', array(
                            'role_id' => $role['role_id']
                        )) . "'$confirm><i class='fa fa-trash-o'></i> 停用 </a>  ";
                }
            }
			if($role['status']==0)
			{
				$role['statusname']="已停用";
			}else{
				$role['statusname']="已启用";
			}
            $role['update_time'] = getFormatDate($role['update_time'],"Y-m-d H:i");
            $role['num'] = $num ++;
			
		
            $role['add_staff'] = $staff_name_arr[$role['add_staff']];
            
        }
        $sys_arr['code'] = 0;
        $sys_arr['msg'] = 0;
        $sys_arr['count'] = 0;
        $sys_arr['data'] = $sys_list;
		ob_clean();$this->ajaxReturn($sys_arr); // 返回json格式数据
    }
	/**
	 * @name:sys_role_list               
	 * @desc：角色列表
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_list()
    {
        checkAuth(ACTION_NAME);
        $this->display("Sysauth/sys_role_list");
    }
	/**
	 * @name:sys_role_list_data               
	 * @desc：角色列表数据加载
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_list_data()
    {
        $this->display("Sysauth/sys_role_list_data");
    }
	/**
	 * @name:sys_role_edit               
	 * @desc：角色添加/修改，添加为_add
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_edit()
    {
        checkAuth(ACTION_NAME);
        $role_id = I('get.role_id');
        if (! empty($role_id)) {
            $sys_role = M('sys_role');
            $role_map['role_id'] = $role_id;
            $role_list = $sys_role->where($role_map)->find();
            $this->assign("role_list", $role_list);
        }
        $this->display("Sysauth/sys_role_edit");
    }
    /**
     * @name:sys_role_do_save               
     * @desc：角色保存执行
     * @param：
     * @return：
     * @author：王世超
     * @addtime:2016-08-30
     * @version：V1.0.0
    **/
    public function sys_role_do_save()
    {
        checkLogin();
        $role_id = I('post.role_id');
        $sys_role = M('sys_role');
        $role_name = I('post.role_name');
        $data['role_name'] = $role_name;
        $data['role_type'] = I('post.role_type');;
        $data['add_staff'] = session('staff_no');
        ;
        $data['status'] = '1';
        $data['update_time'] = date('Y-m-d H:i:s');
        $data['memo'] = I('post.memo');
        if (! empty($role_id)) {
            $role_map['role_id'] = $role_id;
            $result = $sys_role->where($role_map)->save($data);
            saveLog(ACTION_NAME,2,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'对角色编号 ['.$role_id.']进行修改操作');
        } else {
            if (empty($role_name)) {
                showMsg('error', '用户角色名不能为空！！','');
            }
            if (checkRepeat('sys_role', 'role_name', $role_name)) {
                showMsg('error', '用户角色名已存在！！','');
            }
            $data['add_time'] = date('Y-m-d H:i:s');
            $result = $sys_role->add($data);
            saveLog(ACTION_NAME,1,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'新增一条角色数据，编号为['.$result.']');
        }
		
        if ($result) {
            // 当你在iframe页面关闭自身时
            showMsg('success','操作成功！！！',null,1);
            //echo "<script>parent.location.reload();</script>";
            //  showMsg('success', '操作成功！！！', U('Sysauth/sys_role_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }
	/**
	 * @name:sys_role_info               
	 * @desc：角色详情
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_info()
    {
        checkAuth(ACTION_NAME);
        $this->display("Sysauth/sys_role_info");
    }
	/**
	 * @name:sys_role_do_del               
	 * @desc：角色删除
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_do_del()
    {
        checkAuth(ACTION_NAME);
        $role_id = I('get.role_id');
        $sys_role = M('sys_role');
		$is_enable=I('get.is_enable');
        $role_map['role_id'] = $role_id;
		if($is_enable=='yes') {
			$result = $sys_role->where($role_map)->setField('status',1);
			saveLog(ACTION_NAME,3,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'对角色编号 ['.$role_id.']进行启用操作');
		}else{
			$result = $sys_role->where($role_map)->setField('status',0);
			saveLog(ACTION_NAME,3,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'对角色编号 ['.$role_id.']进行停用操作');
		}
        if ($result) {
			showMsg('success', '操作成功', U('Sysauth/sys_role_index'));
        } else {
			showMsg('error', '操作失败','');
        }
    }
    /**
	 * @name:sys_role_auth_list               
	 * @desc：角色已授权功能菜单列表
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_auth_list()
    {
        checkAuth(ACTION_NAME);
		$dt = I('get.dt');
		switch($dt){
			case "communist":$this->assign("title","后台权限设置");
				break;
			case "agent":$this->assign("title","门店权限设置");
				break;
			case "cust":$this->assign("title","客户权限设置");
				break;
			case "app":$this->assign("title","APP权限设置");
				break;
            case "weixin":$this->assign("title","微信权限设置");
                break;
		}
        $role_id = I('get.role_id');
        $sys_auth = M('sys_auth');
        $role_map['role_id'] = $role_id;
        $sys_role_auth = $sys_auth->where($role_map)->select();
        $role_list = "";
        foreach ($sys_role_auth as &$role_auth) {
            if ($role_list == "") {
                $role_list = $role_auth['function_id'];
            } else {
                $role_list .= "," . $role_auth['function_id'];
            }
        }
        $this->assign('role_id', $role_id);
        $this->assign('role_name', getRoleInfo($role_id));
        $this->assign('role_auth', $role_list);
        $sys_function = M('sys_function');
        $function_map['status'] = 1;
        $function_map['group_code'] = $dt;
        $sys_function_list = $sys_function->where($function_map)
            ->order('function_order')
            ->field('function_id,function_name,function_pid')
            ->select();
        $this->assign('function', $sys_function_list);
        $this->display("Sysauth/sys_role_auth_list");
    }
    /**
	 * @name:sys_role_auth_do_save               
	 * @desc：保存角色已授权功能菜单
	 * @param：
	 * @return：
	 * @author：王世超
	 * @addtime:2016-08-30
	 * @version：V1.0.0
	**/
    public function sys_role_auth_do_save()
    {
        checkLogin();
        $sys_auth = M('sys_auth');
        $sys_role = M('sys_role');
        $role_id = I('post.role_id');
        $data['role_id'] = $role_id;
        $function_list = I('post.role_auth');
        $data['add_staff'] = session('staff_no');
        $data_role['add_staff'] = session('staff_no');
        $data['status'] = '1';
        $data['update_time'] = date('Y-m-d H:i:s');
        $data_role['update_time'] = date('Y-m-d H:i:s');
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['memo'] = I('post.memo');
        $data['add_time'] = date('Y-m-d H:i:s');
        if (! empty($function_list)) {
            $role_map['role_id'] = $role_id;
            $result = $sys_auth->where($role_map)->delete();
            $function_arr = explode(',', $function_list);
            foreach ($function_arr as &$function_id) {
                $data['function_id'] = $function_id;
                $result = $sys_auth->add($data);
            }
			$sys_role->where($role_map)->save($data_role);
        }else{
            showMsg('error', '请选择客户权限','');
        }
        if ($result) {
			saveLog(ACTION_NAME,2,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'对角色编号 ['.$role_id.']进行角色授权操作');
			showMsg('success', '操作成功',U('Sysauth/sys_role_index'));
        } else {
           showMsg('error', '操作失败','');
        }
    }
}
