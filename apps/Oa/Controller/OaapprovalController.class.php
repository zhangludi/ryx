<?php

namespace Oa\Controller;

use Common\Controller\BaseController;

class OaapprovalController extends BaseController
{

    /*******************************************************
     * 作用：OA审批流程管理
     * 作者：靳邦龙
     * 时间：2017.11.01
     *******************************************************/

    /** @name    oa_approval_index()
     * @desc    审批首页
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_index()
    {
        if ($_GET['type'] == 3) {
            session('type', $_GET['type']);//用来区分页面code编码
            checkAuth(ACTION_NAME . "_3");
        } elseif ($_GET['type'] == 2) {
            session('type', $_GET['type']);//用来区分页面code编码
            checkAuth(ACTION_NAME . "_2");
        } elseif ($_GET['type'] == 1) {
            session('type', $_GET['type']);//用来区分页面code编码
            checkAuth(ACTION_NAME);
        } else {
            $_GET['type'] = session('type');
        }
        //左侧ztree数据
        $tpl_list = getOaApprovalTplList();
        $this->assign('tpl_list', $tpl_list);

        $this->assign('app_type', $_GET['type']);
        $this->assign('template_no', $_GET['template_no']);
        $this->display("Oaapproval/oa_approval_index");
    }

    /** @name    ()
     * @desc    流程管理列表数据获取
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_list_data()
    {
        $template_no = I('get.template_no');
        $approval_name = I('get.approval_name');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $db_approval = M('oa_approval');
        $staff_no = $this->staff_no;
        if ($_GET['type'] == 1) {//带我审批列表
            $map['status'] = array(array('eq', 11), array('eq', 12), 'or');//查询状态为待审或审核中的数据
            if ($staff_no) {
                $map['_string'] = "FIND_IN_SET('$staff_no',node_staff)";
            }
        } elseif ($_GET['type'] == 2) {//我发起的审批列表，不区分状态
            if ($staff_no) {
                $map['approval_apply_man'] = $staff_no;
            }
        } elseif ($_GET['type'] == 3) {//我参与的审批

            $oa_approval_node = M('oa_approval_node');

            $where['node_staff_real'] = array('eq', $staff_no);
            $no_arr = $oa_approval_node->where($where)->getField('approval_no', true);//取实际节点表实际审核人是登陆者的数据
            if(!empty($no_arr)){
                $map['approval_no'] = array('in', $no_arr);
            }
        }
        if (!empty($template_no)) {
            $map['approval_template'] = array('eq', $template_no);
        }
        if (!empty($approval_name)) {
            $map['approval_name'] = array('like', '%'.$approval_name.'%');
        }
        $map['status'] = array('neq', 0);//0代表已删除数据
        $approval_list = $db_approval->where($map)->order("approval_no desc")->limit($page,$pagesize)->select();
        $approval_count = $db_approval->where($map)->count();
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        $status_name_arr = M('bd_status')->where("status_group = 'approval_status'")->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where("status_group = 'approval_status'")->getField('status_no,status_color');
        foreach ($approval_list as &$approval) {
            $approval['status'] = "<font color='" . $status_color_arr[$approval['status']] . "'>" . $status_name_arr[$approval['status']] . "</font> ";
            $approval['approval_time'] = getFormatDate($approval['approval_time'], 'Y-m-d');
            $approval['approval_apply_man'] = $staff_name_arr[$approval['approval_apply_man']];
            $approval['operate'] = "<a onclick='info(".$approval['approval_no'].','.$approval['approval_template'].")' class='layui-btn layui-btn-primary layui-btn-xs' ><i class='fa fa-info-circle'></i> 查看 </a>  "
                . "<a class='layui-btn layui-btn-del layui-btn-xs' onclick='data_del(" . $approval['approval_no'] . ")' href='javascript:void(0);'><i class='fa fa-trash-o'></i> 删除 </a>  ";
        }
        $data['code'] = 0;
        $data['msg'] = '获取数据成功';
        $data['count'] = $approval_count;
        $data['data'] = $approval_list;
        ob_clean();
        $this->ajaxReturn($data);//返回json格式数据
    }

    /** @name    oa_approval_template_select()
     * @desc    模板选择列表页面
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_select()
    {
        $this->assign('template_no', $_GET['template_no']);
        $this->display("Oaapproval/oa_approval_template_select");
    }

    /** @name    oa_approval_add()
     * @desc    流程管理添加
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_add()
    {
        $type = session('type');//用来区分页面code编码
        if ($type == 2) {
            checkAuth(ACTION_NAME . "_2");
        } else {
            checkAuth(ACTION_NAME);
        }
        $template_no = I('get.template_no');
        $db_template = M('oa_approval_template');//查询模板标题
        $template_map['template_no'] = $template_no;
        $template_list = $db_template->where($template_map)->find();
        $template_list['template_name'] = I('get.approval_title', $template_list['template_name']);
        $this->assign('template_list', $template_list);

        $db_staff = M("hr_staff");
        $staff_no = session('staff_no');
        $comm_map['staff_no'] = $staff_no;
        $communist_list = $db_staff->where($comm_map)->find();
        $communist_list['communist_party'] = getPartyInfo($communist_list['party_no']);
        $communist_list['communist_post'] = getPartydutyInfo($communist_list['post_no']);
        $this->assign('communist_list', $communist_list);
        $this->display("Oaapproval/oa_approval_add");
    }

    /** @name    oa_approval_edit()
     * @desc    流程管理修改
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_edit()
    {
        $type = session('type');//用来区分页面code编码
        if ($type == 2) {
            checkAuth(ACTION_NAME . "_2");
        } else {
            checkAuth(ACTION_NAME);
        }
        $approval_no = I('get.approval_no');

        //审批单信息
        $db_approval = M('oa_approval');
        $approval_map['approval_no'] = $approval_no;
        $approval_list = $db_approval->where($approval_map)->find();
        $this->assign('approval_list', $approval_list);
        //发起人信息        
        $db_communist = M("ccp_communist");
        $communist_no = $approval_list['approval_apply_man'];
        $comm_map['communist_no'] = $communist_no;
        $communist_list = $db_communist->where($comm_map)->find();
        $communist_list['communist_party'] = getPartyInfo($communist_list['party_no']);
        $communist_list['communist_post'] = getPartydutyInfo($communist_list['post_no']);

        $this->assign('communist_list', $communist_list);
        $this->display("Oaapproval/oa_approval_edit");
    }

    /** @name    oa_approval_do_save()
     * @desc    流程保存执行
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_do_save()
    {
        checkLogin();
        $db_approval = M("oa_approval");

        $data = I('post.');
        $data['approval_apply_man'] = session('staff_no');
        $set_res = saveOaApproval($data);
        if ($set_res['status'] == 1) {
            showMsg('success', '操作成功！', U('oa_approval_info', array("approval_no" => $set_res['approval_no'])));
        } else {
            showMsg('error', '操作失败！');
        }

    }

    /** @name    oa_approval_edit_do_save()
     * @desc    流程保存执行（只允许修改内容），审批流不改变
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_edit_do_save()
    {
        checkLogin();
        $db_approval = M("oa_approval");
        $data = I('post.');
        //保存审批单基本信息
        $data['update_time'] = date('Y-m-d H:i:s');
        $data['status'] = 11;//待审核状态
        $map['approval_no'] = array('eq', $data['approval_no']);
        $save_res = $db_approval->where($map)->save($data);
        if ($save_res) {
            showMsg('success', '操作成功！', U('oa_approval_info', array("approval_no" => $data['approval_no'])));
        } else {
            showMsg('error', '操作失败！');
        }
    }

    /** @name    oa_approval_info()
     * @desc    流程管理详情
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_info()
    {
        $approval_no = I('get.approval_no');
        $url_no = I('get.url_no');
        $this->assign('url_no', $url_no);
        //审批单信息
        $db_approval = M('oa_approval');
        $approval_map['approval_no'] = $approval_no;
        $approval_list = $db_approval->where($approval_map)->find();
        $approval_list['apply_man_post'] = getPost(getStaffInfo($approval_list['approval_apply_man'], 'staff_post_no'));
        $approval_list['apply_man_party'] = getDeptInfo(getStaffInfo($approval_list['approval_apply_man'], 'staff_dept_no'));
        $approval_list['apply_man_name'] = getStaffInfo($approval_list['approval_apply_man']);
        $this->assign('approval_list', $approval_list);
        $this->assign('approval_status', $approval_list['status']);
        $this->assign('approval_node_id', $approval_list['node_id']);
        //审批流程图
        $db_log_node = M('oa_approval_node');
        $node_list = $db_log_node->where($approval_map)->select();
        $this->assign("node_list", $node_list);
        $this->assign("node_count", count($node_list));
        //查询流转意见
        $db_log = M('oa_approval_log');
        $approval_map['status'] = 1;
        $log_list = $db_log->where($approval_map)->limit(0, 999)->select();
        $log_count = sizeof($log_list);
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($log_list as &$log) {
            $log['log_staff_name'] = $staff_name_arr[$log['log_staff']];
        }
        $this->assign('log_count', $log_count);
        $this->assign('log_list', $log_list);
        //驳回状态、待审状态且当前登录人是发起人可编辑
        $staff_no = $this->staff_no;
        if (($approval_list['status'] == 11 || $approval_list['status'] == 31) && $staff_no == $approval_list['approval_apply_man']) {
            $this->assign('edit_show', '1');
        }
        $this->assign('staff_no', $staff_no);
        $this->display("oa_approval_info");
    }

    /** @name    oa_approval_do_del()
     * @desc    审批流删除
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_do_del()
    {
        $type = session('type');//用来区分页面code编码
        if ($type == 2) {
            checkAuth(ACTION_NAME . "_2");
        } else {
            checkAuth(ACTION_NAME);
        }
        $approval_no = I("get.approval_no");
        $db_approval = M('oa_approval');
        $data['update_time'] = date("Y-m-d H:i:s");
        $data['status'] = 0;//假删除
        $approval_map['approval_no'] = $approval_no;
        $del_res = $db_approval->where($approval_map)->save($data);
        if ($del_res) {
            ob_clean();
            $this->ajaxReturn(1);
        } else {
            ob_clean();
            $this->ajaxReturn(0);
        }

    }

    /** @name    oa_approval_log_edit()
     * @desc    流转意见填写弹窗
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_log_edit()
    {
        checkLogin();
        $node_id = I('get.node_id');
        $log_type = I('get.log_type');
        $url_no = I('get.url_no');
        $this->assign('url_no', $url_no);
        $this->assign('node_id', $node_id);
        $this->assign('log_type', $log_type);
        $this->display("Oaapproval/oa_approval_log_edit");
    }

    /** @name    oa_approval_log_do_save()
     * @desc    流转意见保存方法
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_log_do_save()
    {
        checkLogin();
        $db_node = M("oa_approval_node");
        $db_log = M("oa_approval_log");
        $data = I("post.");
        $node_row = getTableInfo('oa_approval_node', 'node_id', $data['node_id'], 'all');//区当前节点数据
        //第一步修改当前节点状态
        if ($data['log_type'] == '退回') {
            $where['approval_no'] = array('eq', $node_row['approval_no']);
            $min_id = $db_node->where($where)->getField("MIN(node_id)");
            //将除第一个节点外所有节点的状态改为待审核
            $map['node_id'] = array('neq', $min_id);
            $map['approval_no'] = array('eq', $node_row['approval_no']);

            $node['update_time'] = date('Y-m-d H:i:s');
            $node['status'] = 11;//待审核
            $node['node_staff_real'] = null;//清空实际审核人

            $node_save = $db_node->where($map)->save($node);

            //将审批单改为驳回状态
            $app['status'] = 31;//驳回
            $app['update_time'] = date('Y-m-d H:i:s');
            $app_save = M("oa_approval")->where($where)->save($app);

            $mode = 'return';//setOaApprovalCurrentNode参数
        } else {
            $node['node_staff_real'] = session('staff_no');//实际审核人
            $node['node_post'] = getCommunistInfo(session('communist_no'), 'post_no');//实际审核人岗位
            $node['update_time'] = date('Y-m-d H:i:s');
            $node['status'] = 21;//同意

            $map['node_id'] = array('eq', $data['node_id']);
            $node_save = $db_node->where($map)->save($node);

            $mode = 'agree';//setOaApprovalCurrentNode参数
        }
        //第二部：向log表插入流水
        $log['approval_no'] = $node_row['approval_no'];
        //$log['node_name']=$data['node_name'];
        $log['log_staff'] = session('staff_no');
        $log['log_type'] = $data['log_type'];
        $log['log_time'] = date('Y-m-d H:i:s');
        $log['log_content'] = $data['log_content'];
        $log['add_staff'] = session('staff_no');
        $log['update_time'] = date('Y-m-d H:i:s');
        $log['add_time'] = date('Y-m-d H:i:s');
        $log['status'] = 1;
        $log_save = $db_log->add($log);
        //第三步：修改审批单对应节点信息
        setOaApprovalCurrentNode($log['approval_no'], $mode);
        OaApprovalWriteBack($log['approval_no']);//回写状态
        if ($log_save) {
            if (!empty($data['url_no'])) {
                showMsg('success', '操作成功！', U($data['url_no']), 3);
            } else {
                showMsg('success', '操作成功！', '', 1);
            }
        } else {
            showMsg('error', '操作失败！', '');
        }
    }

    /** @name    oa_approval_template_index()
     * @desc    模板管理首页
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_index()
    {
        checkAuth(ACTION_NAME);
        $this->display("Oaapproval/oa_approval_template_index");
    }

    /** @name    oa_approval_template_list_data()
     * @desc    模板管理列表数据获取
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_list_data()
    {
        $db_tpl = M('oa_approval_template');
        $template_name = I('get.template_name');
        $template_map['status'] = 1;
        if(!empty($template_name)){
            $template_map['template_name'] = array('like','%'.$template_name.'%');
        }
        $tpl_list = $db_tpl->where($template_map)->order("add_time desc")->select();

        $num = 0;
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';

        foreach ($tpl_list as &$tpl) {
            $num++;
            $tpl['num'] = $num;
            $tpl['add_staff'] = $staff_name_arr[$tpl['add_staff']];

            $tpl['add_time'] = getFormatDate($tpl['add_time'], 'Y-m-d');

            $tpl['select_operate'] = "<button class='layui-btn layui-btn-primary layui-btn-xs' onclick='tpl_info({$tpl['template_no']})'><i class='fa fa-info-circle'></i> 预览 </button>"
                . "<a class='btn btn-xs blue btn-outline' href='" . U('oa_approval_add', array('template_no' => $tpl['template_no'])) . "'><i class='fa fa-edit'></i>  选择</a>";
            $tpl['operate'] = "<button class='layui-btn layui-btn-primary layui-btn-xs' onclick='tpl_info($tpl[template_no])'><i class='fa fa-info-circle'></i> 预览 </button><a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('oa_approval_template_node_list', array('template_no' => $tpl['template_no'])) . "'><i class='fa fa-info-circle'></i>编辑审批流程</a>"
                ."<button class='layui-btn  layui-btn-xs layui-btn-f60' onclick='tpl_edit($tpl[template_no])'><i class='fa fa-edit'></i> 编辑 </button>"
                . "<a class='layui-btn layui-btn-del layui-btn-xs ' href='" . U('oa_approval_template_do_del', array('template_no' => $tpl['template_no'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
        }
        $data['code'] = 0;
        $data['msg'] = '获取数据成功';
        $data['count'] = 0;
        $data['data'] = $tpl_list;
        ob_clean();
        $this->ajaxReturn($data);//返回json格式数据
    }

    /** @name    oa_approval_template_edit()
     * @desc    模板编辑弹窗
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_edit()
    {
        checkAuth(ACTION_NAME);
        $template_no = I('get.template_no');
        if (!empty($template_no)) {
            $db_tpl = M('oa_approval_template');
            $tpl_map['template_no'] = $template_no;
            $tpl_list = $db_tpl->where($tpl_map)->find();
            $this->assign("template_list", $tpl_list);
        }
        $this->display("Oaapproval/oa_approval_template_edit");
    }

    /** @name    oa_approval_template_info()
     * @desc    模板详情
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_info()
    {
        checkAuth(ACTION_NAME);
        $template_no = $_GET['template_no'];//对应模板编号

        //审批流程图
        $db_log_node = M('oa_approval_template_node');
        $tpl_map['template_no'] = $template_no;
        $node_list = $db_log_node->where($tpl_map)->select();
        $this->assign("node_list", $node_list);

        $this->display("Oaapproval/oa_approval_template_info");
    }

    /** @name    oa_approval_template_do_save()
     * @desc    模板保存执行
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_do_save()
    {
        checkLogin();
        $data = I('post.');
        $db_tpl = M('oa_approval_template');
        $data['update_time'] = date("Y-m-d H:i:s");
        if (!empty($data['template_no'])) {
            $tpl_map['template_no'] = $data['template_no'];
            $save_res = $db_tpl->where($tpl_map)->save($data);
        } else {
            $data['add_time'] = date("Y-m-d H:i:s");
            $data['status'] = 1;
            $data['add_staff'] = $this->staff_no;
            $data['template_no'] = getFlowNo(1, 'oa_approval_template', 'template_no', 3);
            $save_res = $db_tpl->add($data);
            if ($save_res) {
                $db_node = M('oa_approval_template_node');
                //添加第一个发起人节点
                $first_node['template_no'] = $data['template_no'];
                $first_node['node_no'] = getFlowNo($data['template_no'], "oa_approval_template_node", "node_no", 4);
                $first_node['node_name'] = "发起人";
                $first_node['node_order'] = 0;
                $first_node['add_staff'] = $this->staff_no;
                $first_node['status'] = 1;
                $first_node['add_time'] = $first_node['update_time'] = date("Y-m-d H:i:s");
                $firstnode = $db_node->add($first_node);
            }
        }
        if ($save_res) {
            showMsg('success', '操作成功！', U('oa_approval_template_index'), 1);
        } else {
            showMsg('error', '操作失败！', U('oa_approval_template_edit'));
        }
    }

    /** @name    oa_approval_template_do_del()
     * @desc    模板删除执行（假删除）
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_do_del()
    {
        checkAuth(ACTION_NAME);
        $template_no = I('get.template_no');
        if (!empty($template_no)) {
            $db_tpl = M('oa_approval_template');
            $data['status'] = 0;
            $tpl_map['template_no'] = $template_no;
            $del_res = $db_tpl->where($tpl_map)->save($data);
        }
        if ($del_res) {
            showMsg('success', '操作成功！', U('oa_approval_template_index'));
        } else {
            showMsg('error', '操作失败！', U('oa_approval_template_index'));
        }
    }

    /** @name    oa_approval_node_list_data()
     * @desc    节点列表数据获取
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_node_list_data()
    {
        $db_node = M('oa_approval_template_node');

        $node_list = $db_node->order("add_time desc")->select();
        $num = 0;
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($node_list as &$node) {
            $num++;
            $node['add_staff'] = $staff_name_arr[$node['add_staff']];

            $node['num'] = $num;
            $node['operate'] = "<button class='btn btn-xs blue btn-outline' onclick='communist_select($node[node_no])'><i class='fa fa-edit'></i>选择审批人</button>";
        }
        ob_clean();
        $this->ajaxReturn($node_list);//返回json格式数据
    }

    /** @name    oa_approval_template_node_list()
     * @desc    节点列表首页
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_node_list()
    {
        $this->assign('template_no', $_GET['template_no']);
        $this->display("Oaapproval/oa_approval_template_node_list");
    }

    /** @name    oa_approval_template_node_list_data()
     * @desc    节点列表数据获取
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_node_list_data()
    {
        $template_no = $_GET['template_no'];
        $node_name = $_GET['node_name'];
        $db_node = M('oa_approval_template_node');
        if(!empty($node_name)){
            $tpl_map['node_name'] = array('like','%'.$node_name.'%');
        }
        $tpl_map['template_no'] = $template_no;
        $node_list = $db_node->where($tpl_map)->order("node_order")->select();
        $num = 0;
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $post_name_arr = M('hr_post')->getField('post_no,post_name');
        foreach ($node_list as &$node) {
            $num++;
            $node['add_staff'] = $communist_name_arr[$node['add_staff']];
            $node['node_return_no'] = getOaApprovalNodeInfo($node['node_return_no']);
            $node['node_post'] = $post_name_arr[$node['node_post']];
            $node['num'] = $num;
            if ($num == 1) {
                $node['operate'] = "<button class='layui-btn  layui-btn-xs layui-btn-f60' onclick='node_edit($node[node_no],0,$num)'><i class='fa fa-edit'></i> 编辑</button> ";
            } else {
                $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
                $node['operate'] = "<button class='layui-btn  layui-btn-xs layui-btn-f60' onclick='node_edit($node[node_no],0)'><i class='fa fa-edit'></i> 编辑</button> "
                    . "<a $confirm class='layui-btn layui-btn-del layui-btn-xs' href='" . U('oa_approval_template_node_do_del', array('node_no' => $node['node_no'], 'template_no' => $template_no)) . "'><i class='fa fa-trash-o'></i> 删除</a> ";
            }
        }
        $data['code'] = 0;
        $data['msg'] = '获取数据成功';
        $data['count'] = 0;
        $data['data'] = $node_list;
        ob_clean();
        $this->ajaxReturn($data);//返回json格式数据
    }

    /** @name    oa_approval_template_node_edit()
     * @desc    节点编辑弹窗
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_node_edit()
    {
        checkAuth(ACTION_NAME);
        $template_no = I('get.template_no');
        $node_no = I('get.node_no');
        if ($node_no) {
            $node_row = getOaApprovalNodeInfo($node_no, 'all');
            $this->assign('node_row', $node_row);
        }
        $this->assign('template_no', $template_no);
        $this->assign('node_no', $node_no);
        $this->display("Oaapproval/oa_approval_template_node_edit");
    }

    /** @name    oa_approval_template_node_do_save()
     * @desc    节点保存执行
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_node_do_save()
    {
        checkLogin();
        $data = I("post.");
        $db_node = M('oa_approval_template_node');
        $data['update_time'] = date('Y-m-d H:i:s');
        if (!empty($data['node_no'] && $data['node_no'] != 'undefined')) {
            $map['node_no'] = array('eq', $data['node_no']);
            $save_res = $db_node->where($map)->save($data);
        } else {
            $staff_no_str = getStaffList('',$data['node_staff'],'str');
            $data['node_staff'] = $staff_no_str;
            $data['add_staff'] = session('staff_no');
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['node_no'] = getFlowNo($data['template_no'], "oa_approval_template_node", "node_no", 4);
            $save_res = $db_node->add($data);
        }
        if ($save_res) {
            showMsg('success', '操作成功！', U('oa_approval_template_node_list', array('template_no' => $data['template_no'])), 1);
        } else {
            showMsg('error', '操作失败！');
        }

    }

   /** @name    oa_approval_template_node_do_del()
     * @desc    节点删除执行
     * @author  靳邦龙
     * @addtime 2017年11月1日
     * @version V1.0.0
     */
    public function oa_approval_template_node_do_del()
    {
        checkAuth(ACTION_NAME);
        $db_node = M('oa_approval_template_node');
        $node_no = I("get.node_no");
        $template_no = I("get.template_no");
        $node_map['node_no'] = $node_no;
        $del_res = $db_node->where($node_map)->delete();
        if ($del_res) {
            showMsg('success', '删除成功', U('oa_approval_template_node_list', array('template_no' => $template_no)));
        } else {
            showMsg('error', '删除失败');
        }
    }


}
