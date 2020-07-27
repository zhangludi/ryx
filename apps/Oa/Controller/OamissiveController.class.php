<?php

namespace Oa\Controller;

use Common\Controller\BaseController;

class OamissiveController extends BaseController // 继承Controller类
{

    /****************************** 公文收发开始 ************************/

    /**
     * @name oa_missive_index()
     * @desc 公文收件箱（只查看）
     * @author 王宗彬
     * @addtime 2017年10月16日
     * @version V1.0.0
     */
    public function oa_missive_index()
    {

        $type = $_GET['type'];
        if ($type == "2") {
            $action = ACTION_NAME . "_2";
        } else if ($type == "1") {
            $action = ACTION_NAME . "_1";
        }
        checkAuth($action);
        $this->assign("action", $action);
        $this->assign("type", $type);
        $this->display("Oamissive/oa_missive_index");
    }

    /**
     * @name oa_missive_index_data()
     * @desc 公文ajax数据
     * @author   王宗彬
     * @addtime 2016年12月22日
     * @updatetime 2017年10月16日
     * @version V1.0.0
     */
    public function oa_missive_index_data()
    {
        $str = I('get.start');
        $strt = strToArr($str, ' - ');  //分割时间
        $start_time = $strt[0];
        $end_time = $strt[1];
		$page = I('get.page');
		$limit = I('get.limit');
        $communist = I('get.missive_communist');
        $keywords = I('get.keywords');
        $missive_communist = $this->staff_no;
        $map = "1=1";
        if (!empty($communist)) {
            $map .= " and missive_communist='$communist'";
        }
        if (!empty($start_time)) {
            $start_time = $start_time.' 00:00:00';
            $map .= " and missive_date>='$start_time'";
        }
        if (!empty($end_time)) {
            $end_time = $end_time.' 23:59:59';
            $map .= " and missive_date<='$end_time'";
        }
        if (!empty($keywords)) {
            $map .= " and (missive_content like '%$keywords%' or missive_title like '%$keywords%')";
        }
        $type = $_GET['type'];
        switch ($type) {
            case 1://收件箱
                $where = "(FIND_IN_SET('$missive_communist',missive_receiver) or FIND_IN_SET($missive_communist,missive_cc)) and status=21";//已审核
                break;
            case 2://我发起的
                $where = "status>0 and missive_communist='$missive_communist'";
                break;
        }
        $where = $map . " and ($where)";
        $db_missive = M("oa_missive");
        $missive_list = $db_missive->where($where)->order("missive_id desc")->limit(($page-1)*$limit,$limit)->select();
		$count = $db_missive->where($where)->count();
        $status_name_arr = M('bd_status')->where("status_group = 'approval_status'")->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where("status_group = 'approval_status'")->getField('status_no,status_color');
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        foreach ($missive_list as &$mis) {
            $mis['missive_receiver'] = strToArr($mis['missive_receiver'], ',');
            foreach ($mis['missive_receiver'] as $k => &$party_no) {
                $mis['missive_receiver'][$k] = $communist_name_arr[$party_no];
            }
            //$mis['missive_id'] = "<a class='fcolor-22' href='" . U('oa_missive_info', array('type' => $type, 'missive_no' => $mis['missive_no'])) . "'>" . $mis['missive_id'] . "</a>";
            //$mis['missive_title'] = "<a class='fcolor-22' >" . $mis['missive_title'] . "</a>";
            $mis['missive_date'] = getFormatDate($mis['missive_date'], 'Y-m-d H:i');
			$mis['is_type'] = $type;
         //   $mis['operate'] = $mis['operate'] . "<a $confirm class='btn btn-xs red btn-outline' href='" . U('oa_missive_do_del', array('type' => $type, 'missive_no' => $mis['missive_no'])) . "'><i class='fa fa-trash-o'></i> 删除  </a>"; 
            $mis['missive_receiver'] = arrToStr($mis['missive_receiver'], ',');
            // if (mb_strlen($mis['missive_receiver'], 'utf-8') > 8) {
            //     $mis['missive_receiver'] = getStrCut($mis['missive_receiver'], 8) . '...';
            // }
            $mis['missive_communist'] = getStaffInfo($mis['missive_communist']);
			if ($type == 1) {
                $sign_map['missive_no'] = $mis['missive_no'];
                $sign_map['sign_communist'] = $missive_communist;
                $sign = M("oa_missive_sign")->where($sign_map)->count();
                if ($sign > 0) {
                    $mis['status'] = "<span style='color:green;'>已读</span>";
                } else {
                    $mis['status'] = "<span style='color:red;'>未读</span>";
                }
            } else {
                $mis['status'] = "<font color='" . $status_color_arr[$mis['status']] . "'>" . $status_name_arr[$mis['status']] . "</font> ";
//             	if($mis['status']=="1"){
//             		$mis['status'] = "<span style='color:green;'>发送成功</span>";
//             	}else{
//             		$mis['status'] = "<span style='color:red;'>发送失败</span>";
//             	}
            }
			if(empty($mis['missive_receiver'])){
				$mis['missive_receiver']="暂无收件人";
			}
			if(empty($mis['missive_communist'])){
				$mis['missive_communist']="暂无拟稿人";
			}
			$mis['missive_date'] = getFormatDate($mis['missive_date'], 'Y-m-d');
        }
		$missive_list = [
			'code'=>0,
			'msg'=>0,
			'count'=>$count,
			'data'=>$missive_list
		];
        ob_clean();$this->ajaxReturn($missive_list); // 返回json格式数据
    }

    /**
     * @name oa_missive_edit()
     * @desc 公文编辑
     * @author 王宗彬
     * @addtime 2017-10-16
     * @version V1.0.0
     */
    public function oa_missive_edit()
    {
        $staff_name = getStaffInfo($this->staff_no);
        $dept_name = getDeptInfo(getStaffInfo($this->staff_no, 'staff_dept_no'));
        $this->assign('staff_name', $staff_name);
        $this->assign('dept_name', $dept_name);
        $this->display("Oamissive/oa_missive_edit");
    }

    /**
     * @name oa_missive_do_save()
     * @desc 公执行
     * @author 王宗彬
     * @addtime 2017年10月16日
     * @version V1.0.0
     * @updatetime 2017.09.05 杨凯 去掉审核和提醒直接审核
     */
    public function oa_missive_do_save()
    {
        $db_missive = M("oa_missive");
        $data = I("post.");
        $data['countersign_communists'] = strUnique(rtrim($data['countersign_communists'], ','));
        $data['ausstellung_communists'] = strUnique(rtrim($data['ausstellung_communists'], ','));
        $data['missive_receiver'] = strUnique(rtrim($data['missive_receiver_no'], ','));
        $data['missive_cc'] = strUnique(rtrim($data['missive_cc'], ','));
        $data['missive_content'] = $_POST['imail_content'];
        $data['missive_corporation'] = $_POST['missive_corporation'];
        $data['missive_attach'] = $_POST['imail_attach'];
        $data['missive_title'] = $_POST['imail_title'];
        if ($data['status'] == 1) {
            $data['log_time'] = date("Y-m-d H:i:s");//发送时间
            $data['status'] = '1';
        }
        if (empty($data['missive_id'])) {
            $data['add_staff'] = $this->staff_no;
            $data['missive_communist'] = $this->staff_no;
            $prefix = "D" . date("Ymd");
            $data['missive_no'] = getFlowNo($prefix, "oa_missive", 'missive_no', 2);
            $data["add_time"] = date("Y-m-d H:i:s");
            $data["missive_date"] = date("Y-m-d H:i:s");
            $data["update_time"] = date("Y-m-d H:i:s");
            $data["missive_corporation"]=getDeptInfo(getStaffInfo($data['missive_communist'],'staff_dept_no'));
            $missive_res = $db_missive->add($data);
            $a = str_replace(',', '_', $data["missive_receiver"]);
            if ($missive_res && $data['approval_template']) {//发起审批
                $content = getStaffInfo($this->staff_no) . "发布" . $data['missive_title'] . "公文请审核";
                $approval_arr = array(
                    'approval_template' => $data['approval_template'],
                    'approval_name' => getStaffInfo($this->staff_no) . '发起的' . $_POST['imail_title'],
                    'approval_apply_man' => $this->staff_no,
                    'approval_table_name' => 'oa_missive',
                    'approval_table_field' => 'missive_no',
                    'approval_table_field_value' => $data['missive_no'],
                    'approval_rewrite_field' => 'status',
                    'approval_attach' => $data['missive_attach'],
                    'approval_content' => $content,
                    'approval_callfunction' => "callsaveOamissive('" . $data['missive_no'] . "','" . $data['missive_title'] . "','" . $a . "'," . $this->staff_no . ",$" . status . ",$" . node_staff . ");"
                );
                saveOaApproval($approval_arr);
            }
        } else {
            $data["update_time"] = date("Y-m-d H:i:s");
            $missive_map['missive_id'] = $data['missive_id'];
            $missive_res = $db_missive->where($missive_map)->save($data);
            $data['missive_no'] = $db_missive->where($missive_map)->getField('missive_no');
        }
        if ($missive_res) {
            showMsg('success', '操作成功！！！', U('Oa/Oamissive/oa_missive_index/type/2'),1);//跳转到待发起的公文页面
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }

    /**
     * @name oa_missive_info()
     * @desc 公文详情
     * @author 王宗彬
     * @addtime 2016年10月16日
     * @version V1.0.0
     */
    public function oa_missive_info()
    {
        checkAuth(ACTION_NAME);
        $db_missive = M("oa_missive");
        $db_log = M("oa_missive_sign");
        $missive_no = I('get.missive_no');
        $missive_map['missive_no'] = $missive_no;
        $missive_row = $db_missive->where($missive_map)->find();
        $missive_log_list = $db_log->where($missive_map)->select();
        $missive_row['missive_receiver'] = strToArr($missive_row['missive_receiver'], ',');
        foreach ($missive_row['missive_receiver'] as $k => &$communist_no) {
            $missive_row['missive_receiver'][$k] = getCommunistInfo($communist_no);
        }
        $missive_row['missive_receiver'] = arrToStr($missive_row['missive_receiver'], ',');
        $this->assign("missive_row", $missive_row);
        $this->assign("log_list", $missive_log_list);
        $this->assign("count", sizeof($missive_log_list));
        /* 新增当前登录人是否已阅 ,20170105*/
        $type = $_GET['type'];
        if ($type == 1) {
            $sign_communist = $this->staff_no;
            $sign_map['sign_communist'] = $sign_communist;
            $sign_map['missive_no'] = $missive_no;
            $count = M("oa_missive_sign")->where($sign_map)->count();
        } else {
            /* 获取 所有查阅人*/
            $sign_map['missive_no'] = $missive_no;
            $sign_staff_arr = M("oa_missive_sign")->where($sign_map)->select();
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            foreach ($sign_staff_arr as &$staff) {
                $staff['sign_communist'] = $communist_name_arr[$staff['sign_communist']];
            }
            $this->assign('sign_communist_arr', $sign_staff_arr);
            $this->assign('type', $type);
        }
        $this->display("Oamissive/oa_missive_info");
    }

    /**
     * @name oa_missive_do_del()
     * @desc 公文删除
     * @author 王宗彬
     * @addtime 2017年10月16日
     * @version V1.0.0
     */
    public function oa_missive_do_del()
    {
        checkAuth(ACTION_NAME);
        $db_missive = M("oa_missive");
        $missive_no = I('get.missive_no');
        $type = I('get.type');
        $missive_map['missive_no'] = $missive_no;
        $missive_del = $db_missive->where($missive_map)->delete();
        if ($missive_del) {
            if ($type == "1") {
                showMsg('success', '操作成功！！！', U('Oa/Oamissive/oa_missive_index/type/1'));
            } else {
                showMsg('success', '操作成功！！！', U('Oa/Oamissive/oa_missive_index/type/2'));
            }
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }
}
