<?php
namespace System\Controller;

// 命名空间
use System\Model\SysUserModel;
use Think\Controller;
use Common\Controller\BaseController;

class SysuserController extends BaseController// 继承Controller类

{
    /***************************** 用户管理开始 ************************/

    /**
     * @name  sys_user_index()
     * @desc  用户管理首页
     * @param
     * @return
     * @author yangluhai
     * @addtime   2016年8月30日下午1:44:43
     * @updatetime  2016年8月30日下午1:44:43
     * @version 0.01
     */
    public function sys_user_index()
    {
        checkAuth(ACTION_NAME);
        $this->display("Sysuser/sys_user_index");
    }
    /**
     * @name  sys_user_index_data()
     * @desc  用户的列表数据加载
     * @param
     * @return
     * @author yangluhai
     * @addtime   2016年8月30日下午1:45:05
     * @updatetime  2016年8月30日下午1:45:05
     * @version 0.01
     */
    public function sys_user_index_data()
    {
        $db_user   = new SysUserModel();
        $user_name = I('get.user_name');
        if (!empty($user_name)) {
            $user_map['user_name'] = array('like','%'.$user_name.'%');
        }
        $status = I('get.status');
        if (!empty($status)) {
            if ($status == 2) {
                $status = 0;
            }
            $user_map['status'] = $status;
        }
        $user_role = I('get.user_role');
        if (!empty($user_role)) {
            $user_map['_string'] = "find_in_set('$user_role',user_role)";
        }
        $start = I('get.start');
        $end   = I('get.end');
        if (!empty($start) && !empty($end)) {
            if ($start == $end) {
                $end = $end . " 23:59:59";
            }
            $user_map['last_login_time']  = array('between',$start,$end);
        }
		$user_list          = $db_user->selectData('1', $user_map);
		$confirm            = 'onclick="if(!confirm(' . "'确认停用？'" . ')){return false;}"';
        $confirms           = 'onclick="if(!confirm(' . "'确认启用？'" . ')){return false;}"';
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($user_list as &$user) {
            $result = checkDataAuth(session('staff_no'));
            if($result){
				$user['operate'] = 1;
			}else{
				$user['operate'] = 0;
			}
			$user['user_relation_nos'] = $user['user_relation_no'];
			 
			if ($result) {
                $operate = "<button type='button' onclick='data_access(" . (string) $user['user_relation_no'] . ")' class='layui-btn layui-btn-primary layui-btn-xs'>数据权限</button>";
                $stop    = "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('sys_user_del', array('user_id' => $user['user_id'], 'status' => '0')) . "' $confirm><i class='fa fa-trash-o'></i> 停用 </a>";
                $start   = "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('sys_user_del', array('user_id' => $user['user_id'], 'status' => '1')) . "' $confirms><i class='fa fa-trash-o'></i> 启用 </a>  ";
            } else {
                $operate = "";
                $stop    = "";
                $start   = "";
            }

            if ($user['status'] == "1") {
                $user['operate'] = $operate . "<a onclick='edit(".$user['user_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60' >编辑</a>" . $stop;
            } else {
                $user['operate'] = $operate . "<a onclick='edit(".$user['user_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60' >编辑</a>" . $start;
            }
            if ($user['status'] == "1") {
                $user['status'] = "可用";
            } else {
                $user['status'] = "不可用";
            }
            $role_list = explode(',', $user['user_role']);
            $role_name = "";
            foreach ($role_list as &$role_id) {
                if (empty($role_name)) {
                    $role_name = getRoleInfo($role_id);
                } else {
                    $role_name .= ',' . getRoleInfo($role_id);
                }
            }
            $user['last_login_time']  = getFormatDate($user['last_login_time'], 'Y-m-d H:i');
            $user['update_time']      = getFormatDate($user['update_time'], 'Y-m-d H:i');
            $user['add_staff']    = $staff_name_arr[$user['add_staff']];
            $user['user_role']        = $role_name;
			$user['user_relation_no'] = $staff_name_arr[$user['user_relation_no']];
        }
        $user_arr['code'] = 0;
        $user_arr['count'] = 0;
        $user_arr['msg'] = 0;
        $user_arr['data'] = $user_list;
		ob_clean();$this->ajaxReturn($user_arr); // 返回json格式数据
    }
    /**
     * @name  sys_user_data_access()
     * @desc  数据权限页面
     * @param
     * @return
     * @author 王彬
     * @addtime   2016-11-22
     * @updatetime  2016-11-22
     * @version V1.0.0
     */
    public function sys_user_data_access()
    {
        $sys_user_auth_type = M('sys_user_auth_type');
        $sys_user_auth      = M('sys_user_auth');

        $staff_no   = I("get.staff_no");
        $staff_data = getStaffInfo($staff_no, "all");
        $this->assign("staff_data", $staff_data);

        $post_name = getPost($staff_data['staff_post_no']);
        $this->assign("post_name", $post_name);
        $dept_name = getDeptInfo($staff_data['staff_dept_no']);
        $this->assign("dept_name", $dept_name);
        $auth_type_list = $sys_user_auth_type->where("status=1 and auth_type = 1")->order("add_time desc")->select();
		$this->assign("auth_type_list", $auth_type_list);
        $auth_map['communist_no'] = $staff_no;
        $auth_lt = $sys_user_auth->where($auth_map)->select();
        $this->assign("auth_lt", $auth_lt);
        $this->display("Sysuser/sys_user_data_access");
    }
    /**
     * @name  sys_user_data_access_do_save()
     * @desc  数据权限保存方法
     * @param
     * @return
     * @author 王彬
     * @addtime   2016-11-22
     * @updatetime  2016-11-22
     * @version V1.0.0
     */
    public function sys_user_data_access_do_save()
    {
        $sys_user_auth = M('sys_user_auth');
        $data          = I("post.");
        $staff_no  = $data['staff_no'];
        $type_no_arr   = $data['type_no'];
        //删除当前员工的数据权限数据
        $auth_map['communist_no'] = $staff_no;
        $auth_del = $sys_user_auth->where($auth_map)->delete();
        $dt['communist_no']  = $staff_no;
        $dt['add_staff'] = session('staff_no');
        $dt['add_time']      = date("Y-m-d H:i:s");
        $dt['update_time']   = date("Y-m-d H:i:s");
        $dt['status']        = "1";
        $dt['auth_typea'] = 1;
        //数据循环添加
        foreach ($type_no_arr as $no) {
            $dt['type_no']      = $no;
            $dt['select_value'] = true;
            $dt['select_value'] = 1;
            $auth_add           = $sys_user_auth->add($dt);
        }
        echo "<script>alert('操作成功!');</script>";
        echo "<script>parent.layer.closeAll();</script>";
    }
    /**
     * @name  sys_user_edit()
     * @desc  编辑用户信息
     * @param
     * @author 刘丙涛
     * @addtime   2017-10-13
     * @version 1.0.0
     */
    public function sys_user_edit()
    {
        checkAuth(ACTION_NAME);
        $user_id = I('get.user_id');
        $tab  = I('get.tab');
        $this->assign('tab', $tab);
        if (trim($user_id)) {
            $db_user   = new SysUserModel();
            $user_data = $db_user->getUserinfo($user_id);
            $this->assign('list', $user_data);
        }
        $this->display("Sysuser/sys_user_edit");
    }

    /**
     * @name  sys_user_repetition()
     * @desc  用户名去重
     * @param
     * @author 刘长军
     * @addtime   2018-12-21
     * @version 1.0.0
     */
    public function sys_user_repetition()
    {
        $user_name = I('post.user_name');
        $where['user_name'] = $user_name;
        $sys_user_data = M('sys_user')->where($where)->find();
        if($sys_user_data==null){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }
    /**
     * @name  sys_user_do_save()
     * @desc  用户的保存操作
     * @param
     * @author 刘丙涛
     * @addtime   2017-10-13
     * @version 1.0.0
     */
    public function sys_user_do_save()
    {
        checkLogin();
        $data    = I('post.');
        if ($data['user_pwd']) {
            $data['user_pwd'] = md5($data['user_pwd']);
        }else{
            if(!empty($data['user_name'])){
                $map['user_name'] = $data['user_name'];
            }
            $data['user_pwd'] = M('sys_user')->where($map)->getField('user_pwd');
        }
        $data['add_staff'] = session('staff_no');
        if(!empty($data['user_id'])){
            $map['user_id'] = $data['user_id'];
            $result = M('sys_user')->where($map)->save($data);
        }else{
            $result = M('sys_user')->add($data);
        }
        if ($result || $result == '') {
            showMsg('success', '操作成功', U('Sysuser/sys_user_index'),1);
        } else {
            showMsg('error', '操作失败', '');
        }
    }
    /**
     * @name  get_communist_select()
     * @desc  根据部门筛选人员    ,用于添加用户，获取人员下拉列表，防止数据过大
     * @param
     * @author 靳邦龙
     * @return 只返回当前部门人员
     * @addtime   2017-11-29
     * @version 1.0.0
     */
    public function get_staff_select()
    {
        $dept_no     = $_GET['dept_no'];
        $hr_staff = M('hr_staff');

        $map['staff_dept_no'] = $dept_no;
        $map['status']   = 1;
        if ($is_uncreate_user == 1) {
            $db_user            = M('sys_user');
            $user_communist_nos = $db_user->getField('user_relation_no', true);
            if ($user_communist_nos) {
                $map['staff_id'] = array('not in', $user_communist_nos);
            }
        }
        $staff_list    = $hr_staff->where($map)->select();
        $satff_options = "";
        foreach ($staff_list as &$staff) {
            $selected = "";
            if (in_array($staff['staff_no'], $selected_no_arr)) {
//判断角色id是否存在于数组中
                $selected = "selected=true";
            }
            $satff_options .= "<option $selected value='" . $staff['staff_no'] . "'>" . $staff['staff_name'] . "</option>";
        }
        if (!empty($satff_options)) {
            echo $satff_options;
        } else {
            echo "<option value=''>无数据</option>";
        }
    }
    /**
     * @name  sys_user_del()
     * @desc  用户启用与停用
     * @param
     * @author 刘丙涛
     * @addtime   2017-10-13
     * @version 1.0.0
     */
    public function sys_user_del()
    {
        checkLogin();
        $db_user = new SysUserModel();
        $result  = $db_user->setUserstatus();
        if ($result) {
            showMsg('success', '操作成功', U('Sysuser/sys_user_index'));
        } else {
            showMsg('error', '操作失败', '');
        }
    }
    /***************************** 用户管理结束 ************************/
}
