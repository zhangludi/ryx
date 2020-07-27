<?php

namespace Oa\Controller;

use Common\Controller\BaseController;

class OaworkController extends BaseController
{
    /**
     * *************************** 日志开始 ***********************
     */
    /**
     * 日志管理首页
     * @name oa_worklog_index()
     * @param
     * @return
     * @author yangluhai-黄子正
     * @version 0.0.1
     * @addtime 2016年8月30日上午11:08:34
     * @updatetime 2017年10月11日上午11:08:34
     */
    public function oa_worklog_index() {
        $is_audit = I('get.is_audit');
        $is_hunt = I('get.is_hunt');
        $select_type = I('get.select_type');
        if (!empty($is_hunt)) {
            $is_audit = I('post.is_audit');
        }
        if(empty($is_audit) && empty($is_hunt) && empty($select_type)){
            session('cat','_1');
        } else if (!empty($is_audit)) {
            // 日志审核搜索条件
            $this->assign('is_audit', $is_audit);
            session('cat','_1');
        } else if($select_type == 5){
            session('cat','_3');
        } else if($select_type == 6){
            session('cat','_4');
        }
        // 日志管理搜索条件
        // 总结类型
        $worklog_type = $select_type;
        $this->assign('worklog_type', $worklog_type);
        $this->display("Oaworklog/oa_worklog_index");
    }

    /**
     * 日志管理首页数据加载
     *
     * @name oa_worklog_index_data()
     * @param
     *
     * @return json数据
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:16:40
     * @updatetime 2017年10月11日上午11:08:34
     * @version 0.01
     */
    public function oa_worklog_index_data(){
        $is_audit = I('get.is_audit');
        $staff_no = $this->staff_no;

        $start = I('get.start');
        $strt = explode(' - ', $start);  //分割时间
        $start_date = $strt[0];
        $end_date = $strt[1];
        $dept_no = I('get.dept_no');
        $add_staff = I('get.add_staff');
		$page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $worklog_list = array();
        $now = date('Y-m-d');
        // 总结类型
        $worklog_type = I('get.worklog_type');
        // 标题及内容
        $worklog_info = I('get.worklog_info');
        $status = I('get.select_status');
        $where = '1=1';
        if (!empty($status)) {
            if ($status == "2") {
                $status = "0";
            }
        }
		// 总结类型(01:每日 02:每周 03:每月 04:会议)
		if (! empty($worklog_type)) {
			$where .= " and  worklog_type='$worklog_type'";
		}
		// 标题及内容
		if (! empty($keyword)) {
			$where .= " and (worklog_title like'%" . $keyword . "%' or worklog_summary like'%" . $keyword . "%' )";
		}
		// 状态
		if ($worklog_status != '' || $worklog_status == '0') {
			$where .= "  and `status`='$worklog_status'";
		}
		// 时间
		if (! empty($start_date) && !empty($end_date)) {
			$where .= " and worklog_date >='$start_date' and worklog_date <='$end_date'";
		}
        //判断是否是日志审核
        if (!empty($is_audit) && $is_audit == "1") {
            //判断该用户是否具有超级管理员权限或 是否有查看/审核全部日志的权限
            if (getStaffAuth($staff_no, '9999') || getStaffAuth($staff_no, '702')) {
                $worklog_list = getWorklogList($add_staff, $staff_no, $worklog_type,
                    $start_date, $end_date, $worklog_info, $status, $dept_no,'',1,$page,$pagesize);
            } else if (getStaffAuth($staff_no, '701')) {
                //是否有查看/审核下属日志的权限
                $worklog_list = getWorklogList($add_staff, $staff_no,
                    $worklog_type, $start_date, $end_date, $worklog_info,
                    $status, '','',1,$page,$pagesize);
				$where .= " and add_staff='$staff_no' or worklog_audit_man='$audit_man'";
            }
        } else {
            // 我的
            $worklog_list = getWorklogList($staff_no, '', $worklog_type,
                $start_date, $end_date, $worklog_info, $status, '','',1,$page,$pagesize);
        }
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $worklog_all = array();
        foreach ($worklog_list as $worklog) {
            $worklog['worklog_type'] = getBdTypeInfo($worklog['worklog_type'], 'worklog_type');
            $worklog['add_staff'] = getCommunistInfo($worklog['add_staff']);
            if ($worklog['status'] == "1") {
				$worklog['info'] = 1;
				$worklog['del'] = 0;
				$worklog['edit'] = 0;
                // 已审核
                $worklog['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='popinfo(".$worklog['worklog_id'].");'><i class='fa fa-info-circle'></i> 查看 </a>  ";
                $worklog['status'] = getStaffInfo($worklog['worklog_audit_man']) . '于' .
                    getFormatDate($worklog['worklog_audit_time'],
                        'Y-m-d') . "进行审核";
            } else {
                // 未审核
                if ($is_audit == "1") {
                    // 审核人自能审核当天的或者上一个工作日的日志
                     if ($worklog['worklog_date'] >=
                         $worklog_time[0]['worklog_date']) {
                         $worklog['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U(
                                 'oa_worklog_info',
                                 array(
                                     'worklog_id' => $worklog['worklog_id']

                                 )) . "'><i class='fa fa-info-circle'></i> 查看 </a>";
                     } else {
                         $worklog['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" .U('oa_worklog_info',array('worklog_id' => $worklog['worklog_id'])) ."'><i class='fa fa-info-circle'></i> 查看 </a>";
                    }
                } else {
                    // 提交人自能修改当天未审核
                    if ($worklog['worklog_date'] >= $now) {
						 $worklog['operate'] = $worklog['operate'] .
                            "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" .
                            U('oa_worklog_edit',
                                array(
                                    'worklog_id' => $worklog['worklog_id']
                                )) .
                            "' ><i class='fa fa-edit'></i> 编辑</a>  " .
                            "<a class='btn btn-xs red btn-outline' href='" .
                            U('oa_worklog_do_del',
                                array(
                                    'worklog_id' => $worklog['worklog_id']
                                )) .
                            "' $confirm><i class='fa fa-trash-o'></i> 删除 </a>  "; 
                    } else {
						 $worklog['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='popinfo(".$worklog['worklog_id'].");'><i class='fa fa-info-circle'></i> 查看 </a>  "; 
                    }
                }
                $worklog['status'] = "<font color='red'>未审核</font>";
            }

            $worklog['worklog_audit_man'] = getStaffInfo(
                $worklog['worklog_audit_man'], 'staff_name');

            array_push($worklog_all, $worklog);
        }
		$count = count($worklog_all);
		$worklog_data = [
			'code'=>0,
			'msg'=>0,
			'count'=>$count,
			'data'=>$worklog_all
		];
        ob_clean();$this->ajaxReturn($worklog_data); // 返回json格式数据
    }

    /**
     * 日志添加/编辑，添加为_add
     *
     * @name oa_worklog_edit()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正  王宗彬
     * @addtime 2016年8月30日上午11:18:09
     * @updatetime 2017年10月11日上午11:08:34
     * @version 0.01
     */
    public function oa_worklog_edit()
    {
        $db_worklog = M('oa_worklog');
        $db_type = M('bd_type');
        $type_list = $db_type->where("type_group='worklog_type'")->select();
        $worklog_id = I('get.worklog_id'); // I方法获取数据
        $worklog_type = I('get.worklog_type'); // I方法获取数据
		$this->assign('worklog_type',$worklog_type);
		
		
        $now_date = date('Y-m-d');
        $staff_no = $this->staff_no;
        $staff_dept = getStaffInfo($staff_no, 'staff_dept_no');
        $dept_man = getDeptInfo($staff_dept, 'dept_manager');
        if ($dept_man == $staff_no) {
            $dept_pno = getDeptInfo($staff_dept, 'dept_pno');
            $dept_man = getDeptInfo($dept_pno, 'dept_manager');
        }
        if (!empty($worklog_id)) {
            // 编辑日志
            $worklog_map['worklog_id'] = $worklog_id;
            $worklog_row = $db_worklog->where($worklog_map)->find();
            $worklogattach = $worklog_row['worklog_attach'];
            if (!empty($worklogattach)) {
                $worklog_attach = strToArr($worklog_row['worklog_attach'], ',');
                $worklog_row['worklog_attach'] = arrToStr($worklog_attach, '`');
            }
        } else {
            // 新增日志
            $staff_name = getStaffInfo($staff_no, 'staff_name');
            $worklog_row['worklog_audit_man'] = $dept_man;
            $worklog_row['worklog_title'] = $now_date . "&nbsp;工作总结";
            $worklog_row['worklog_date'] = $now_date;
            $worklog_row['worklog_type'] = $worklog_type;
        }
        // 获取每日必做任务

        $willdolist = getWilldoList($staff_no, "", $now_date, "", "", "", "0");

        foreach ($willdolist as &$willdo) {
            $willdo['willdo_title'] = mb_substr($willdo['willdo_title'], 0, 6,
                'utf-8');
            $willdo_status = getWilldoLog($willdo['willdo_id'], $now_date,
                $staff_no, 2);
            $willdo['log_status'] = $willdo_status['status'];
        }

        if (count($willdolist) > 0) {
            $this->assign("willdolist", $willdolist);
        }

        // 获取每日工作计划
        $workplanlist = getWorkplanList($staff_no, "", '11,21', "", "", "", "");
        // 当日需完成的工作
        $nowplan_list = array();
        foreach ($workplanlist as &$work) {
            $work['workplan_title'] = mb_substr($work['workplan_title'], 0, 6,
                'utf-8');
            if ($work['workplan_arranger_man'] != $staff_no) {
                $work['arranger_man'] = getStaffInfo($work['workplan_arranger_man']);
            }
            if ($work['status'] == '11' &&
                $work['workplan_expectstart_time'] <= $now_date) {
                array_push($nowplan_list, $work);
            }
            if ($work['status'] == '21') {
                array_push($nowplan_list, $work);
            }
            if (getWorkplanLogList($work["workplan_id"], '22', $now_date)) {

                $work["log_status"] = "1";
            }
        }
        foreach ($nowplan_list as &$nowplan) {
            if (getWorkplanLogList($nowplan["workplan_id"], '21,22', $now_date)) {
                $nowplan["log_status"] = "1";
            }
        }
        if (count($nowplan_list) > 0) {
            $this->assign("workplanlist", $nowplan_list);
        }
        $add_staff = getCommunistInfo($worklog_row['add_staff']);
        $this->assign('add_staff',$add_staff);
        $this->assign("worklog_row", $worklog_row);
        $this->assign("now_date", $now_date);
        $this->assign("type_list", $type_list);
        $this->display("Oaworklog/oa_worklog_edit");
    }

    /**
     * 日志保存操作执行
     *
     * @name oa_worklog_do_save()
     * @param
     * @return
     * @author yangluhai-黄子正   王宗彬
     * @addtime 2016年8月30日上午11:18:09
     * @updatetime 2017年10月11日上午11:08:34
     * @version 0.01
     */
    public function oa_worklog_do_save()
    {

        $db_worklog = M('oa_worklog');
        $worklog_id = I('post.worklog_id');
        $worklog_audit_man = I('post.worklog_audit_man');
        if (empty($worklog_audit_man)) {
            showMsg('error', '请选择审核人', '');
        }
        if (!empty($worklog_id)) { // 有id时执行修改操作
            $_POST['update_time'] = date("Y-m-d H:i:s");
            $worklogattachs = I('post.worklog_attachs');
            if (!empty($worklogattachs)) {
                $worklog_attach = strToArr(I('post.worklog_attachs'), '`');
                $_POST['worklog_attach'] = arrToStr($worklog_attach, ',');
            }
            $oper_res = $db_worklog->save($_POST);
            saveLog(ACTION_NAME, 2, '',
                '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") .
                '对日志编号 [' . $worklog_id . ']进行修改操作');
        } else { // 无id时执行添加操作
            $worklog_date = $_POST['worklog_date'];
            if (empty($worklog_date)) {
                $_POST['worklog_date'] = date("Y-m-d");
            }
            $_POST['add_time'] = date("Y-m-d H:i:s");
            // $_POST['add_staff'] = $this->staff_no;
            $_POST['add_staff'] = $_POST['worklog_commmunist'];
            $_POST['worklog_staff'] = $this->staff_no;
            $_POST['status'] = "0";
            $worklog_attachs = I('post.worklog_attachs');
            if (!empty($worklog_attachs)) {
                $worklog_attach = strToArr($worklog_attachs, '`');
                $_POST['worklog_attach'] = arrToStr($worklog_attach, ',');
            }

            $oper_res = $db_worklog->add($_POST);
            saveLog(ACTION_NAME, 1, '',
                '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") .
                '新增一条日志数据，编号为[' . $oper_res . ']');
        }
        if ($oper_res) {
             showMsg('success', '操作成功！！！', U('oa_worklog_index', array('select_type' => $_POST['worklog_type'])));
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }

    /**
     * 日志详情
     *
     * @name oa_worklog_info()
     * @param
     *
     * @return true/false，
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:19:25
     * @updatetime 2017年10月11日上午11:08:34
     * @version 0.01
     */
    public function oa_worklog_info()
    {
       //  checkAuth(ACTION_NAME . session("cat"));
        $db_worklog = M('oa_worklog');
        $staff_no = $this->staff_no;
        $now = date('Y-m-d');
        $db_worklog = M('oa_worklog');
        $worklog_id = I('get.worklog_id'); // I方法获取数据
        $is_dayaudit = I('get.is_dayaudit');
        if (!empty($worklog_id)) { // 必要的非空判断需要增加,防止报错 worklog_date
            $worklog_map['worklog_id'] = $worklog_id;
            $worklog_row = $db_worklog->where($worklog_map)->find();
            $worklogattach = $worklog_row['worklog_attach'];
            if (!empty($worklogattach)) {
                $worklog_attach = strToArr($worklog_row['worklog_attach'], ',');
                $worklog_row['worklog_attach'] = arrToStr($worklog_attach, '`');
            }
            $worklog_row['worklog'] = $worklog_row['worklog_type'];
            $worklog_row['worklog_type'] = getBdTypeInfo($worklog_row['worklog_type'], 'worklog_type');
            $worklog_row['worklog_communist'] = getCommunistInfo($worklog_row['add_staff']);
            //审核人
            if ($worklog_row['worklog_audit_man'] == $staff_no) {
                // $log_map['worklog_date'] = array('neq', $now);
                // $worklog_time = $db_worklog->DISTINCT('worklog_date')
                //     ->where($log_map)
                //     ->order('worklog_date desc')
                //     ->limit(0, 1)
                //     ->field('worklog_date')
                //     ->select();
                if ($now > $worklog_row['worklog_date']) {
                    $worklog_row['is_audit'] = 1;
                    $this->assign("is_audit", 1); // 第二天审核人可以审核
                }
            }
            $worklog_row['audit_man'] = getStaffInfo($worklog_row['worklog_audit_man'], 'staff_name');
            // 添加人
            $add_staff = $worklog_row['add_staff'];
            $worklog_date = $worklog_row['worklog_date'];
            $workplan_list = getWorkplanLogList('', '22,23', $worklog_date, $add_staff);
            foreach ($workplan_list as &$work) {
                $work_info = getWorkplanInfo($work['workplan_id'], 'all');
                if ($work_info) {
                    $work['log_status'] = $work['status'];
                    $work['workplan_title'] = mb_substr($work_info['workplan_title'], 0, 6, 'utf-8');
                    if ($work_info['workplan_arranger_man'] != $add_staff) {
                        $work['arranger_man'] = getStaffInfo($work_info['workplan_arranger_man']);
                    }
                    $work['workplan_expectend_time'] = $work_info['workplan_expectend_time'];
                }
            }
            if (count($workplan_list) > 0) {
                $this->assign("workplanlist", $workplan_list);
            }
            $this->assign("worklog_row", $worklog_row);
        }

        $this->assign("is_dayaudit", $is_dayaudit);
        $this->display("Oaworklog/oa_worklog_info");
    }

    /**
     * 日志详情操作执行
     *
     * @name oa_worklog_info_do_save()
     * @param
     *
     * @return true/false，
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:19:59
     * @updatetime 2017年10月11日上午11:08:34
     * @version 0.01
     */
    public function oa_worklog_info_do_save()
    {
        checkLogin();
        $db_worklog = M('oa_worklog');
        $is_audit = I('post.is_audit');
        $data = I('post.'); // I方法获取整个数组
        $worklogid = $data['worklog_id'];
        if (!empty($worklogid)) { // 有id时执行修改操作
            $worklog_id = $data['worklog_id'];
            $data['worklog_audit_time'] = date("Y-m-d H:i:s");
            $data['worklog_audit_man'] = $this->staff_no; // 2016-06-16修改
            $worklog_audit_content = trim($data['worklog_audit_content']);
            if (empty($worklog_audit_content)) {
                $data['worklog_audit_content'] = "已阅~~";
            }
            $data['status'] = "1";
            $worklog_map['worklog_id'] = $worklog_id;
            $oper_res = $db_worklog->where($worklog_map)->save($data);
            saveLog(ACTION_NAME, 2, '',
                '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") .
                '对日志编号 [' . $worklog_id . ']进行审核操作');
        } else {
            $worklog_date = $_POST['worklog_date'];
            if (empty($worklog_date)) {
                $_POST['worklog_date'] = date("Y-m-d");
            }
            $_POST['add_time'] = date("Y-m-d H:i:s");
            $_POST['add_staff'] = $this->staff_no;
            $_POST['status'] = "1";
            $worklog_attachs = I('post.worklog_attachs');
            if (!empty($worklog_attachs)) {
                $worklog_attach = strToArr($worklog_attachs, '`');
                $_POST['worklog_attach'] = arrToStr($worklog_attach, ',');
            }
            $oper_res = $db_worklog->add($_POST);
            saveLog(ACTION_NAME, 1, '',
                '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") .
                '新增一条日志数据，编号为[' . $oper_res . ']');
        }
        if ($_POST['worklog_type'] == '05' || $_POST['worklog_type'] == '06') {
            $worklog_type = $_POST['worklog_type'];
        }
        if ($oper_res) {
            if (!empty(!empty($worklog_type))) {
                showMsg('success', '操作成功！！！', U('oa_worklog_index', array('select_type' => $worklog_type)));
            } else {
                showMsg('success', '操作成功！！！', U('oa_worklog_index'));
            }
        } else {
            showMsg('error', '操作失败！！！', U('oa_worklog_index', array('select_type' => $worklog_type)));
        }
    }

    /**
     * 删除日志信息
     *
     * @name oa_worklog_do_del()
     * @param
     *
     * @return true/false，
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:20:47
     * @updatetime 2017年10月11日上午11:08:34
     * @version 0.01
     */
    public function oa_worklog_do_del()
    {
        checkAuth(ACTION_NAME . session("cat"));
        $db_worklog = M('oa_worklog');
        $worklog_id = I('get.worklog_id'); // I方法获取数据
        if (!empty($worklog_id)) { // 必要的非空判断需要增加
            $worklog_map['worklog_id'] = $worklog_id;
            $del_res = $db_worklog->where($worklog_map)->delete();
            saveLog(ACTION_NAME, 3, '',
                '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") .
                '对日志编号 [' . $worklog_id . ']进行删除操作');
        }
        if ($del_res) {
            showMsg('success', '操作成功！！！', U('oa_worklog_index'));
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }

    /**
     * *************************** 日志结束 ***********************
     */

    /**
     * *************************** 工作计划开始 ***********************
     */

    /**
     * 工作计划管理首页
     *
     * @name oa_workplan_index()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:27:36
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_index()
    {
        checkAuth(ACTION_NAME);

        $post_info = I('post.');
        $is_hunt = I('get.is_hunt'); // 是否是搜索条件查询
        $staff_no = $this->staff_no; // 当前登录人
        if ($is_hunt == "1") {
            $is_arranger = $post_info['is_arranger'];
            $is_deptplan = $post_info['is_deptplan'];
        } else {
            // 我安排的工作标志
            $is_arranger = I('get.is_arranger');
            $is_deptplan = I('get.is_deptplan');
            if (!empty($is_deptplan)) {

                $post_info['dept_no'] = getStaffInfo($staff_no, 'staff_dept_no');
                if (getStaffAuth($staff_no, '9999') || getStaffAuth($staff_no, '802')) {

                    $this->assign("is_main", 1);
                }
            }
        }
        if (!empty($is_arranger)) {
            $this->assign("is_arranger", $is_arranger);
        }
        if (!empty($is_deptplan)) {

            $this->assign("is_deptplan", $is_deptplan);
        }
        if (!empty($post_info)) {
            $this->assign('post_info', $post_info);
        }
        $time_range = $post_info['time_range'];
        if(!empty($time_range)){
            $this->assign('time_range', $time_range);
            $time_range_arr = strToArr($time_range, ' - ');  //分割时间
            $start_time = $time_range_arr[0];
            $end_time = $time_range_arr[1];
            if(!empty($start_time)){
                $this->assign('start_time', $start_time);
            }
            if(!empty($end_time)){
                $this->assign('end_time', $end_time);
            }
        }
        $this->display("Oaworkplan/oa_workplan_index");
    }

    /**
     * 工作计划管理首页数据
     *
     * @name oa_workplan_index_data()
     * @param
     *
     * @return json数据
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:30:04
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_index_data()
    {
        $workplan_list = array();

        $arranger_man = I('get.is_arranger'); // 我安排的工作
        $deptplan = I('get.is_deptplan'); // 部门工作计划
        $staff_no = $this->staff_no; // 当前登录人
        $status = I('get.status'); // 状态
        $arranger = I('get.select_arranger'); // 安排人
        $executor = I('get.select_executor'); // 执行人
        $date_type = I('get.date_type'); // 时间类型
        $time_range = explode(' - ',I('get.time_range'));
		
		if(!empty($time_range)){
			$start_time = $time_range[0]; // 开始时间
			$end_time = $time_range[1]; // 结束时间
		}else{
			$start_time = ''; // 开始时间
			$end_time = ''; // 结束时间
		}
		$workplan_info = I('get.plan_info'); // 关键词
        $dept_no = I('get.dept_no'); // 部门
		$page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        // 我安排的工作计划
        if ($arranger_man == '1') {
            $workplan_list = getWorkplanList($executor, $staff_no, $status,
                $start_time, $end_time, $workplan_info, $dept_no, $date_type,'',1,$page,$pagesize);
				$arranger = $staff_no;
        } else if ($deptplan == '1') {
            //部门工作计划
            //是否有超级管理员的权限
            if (getStaffAuth($staff_no, '9999') || getStaffAuth($staff_no, '802')) {
                $workplan_list = getWorkplanList($executor, $arranger, $status,
                    $start_time, $end_time, $workplan_info, $dept_no,
                    $date_type,'',1,$page,$pagesize);
            } else if (getStaffAuth($staff_no, '801')) {
                //是否有查看本部门所有员工工作计划的权限
                $dept_no = getStaffInfo($staff_no, 'staff_dept_no');
                $workplan_list = getWorkplanList($executor, $arranger, $status,
                    $start_time, $end_time, $workplan_info, $dept_no,
                    $date_type,'',1,$page,$pagesize);
            }

        } else {
            // 我的工作计划
            $workplan_list = getWorkplanList($staff_no, $arranger, $status,
                $start_time, $end_time, $workplan_info, $dept_no,
                $date_type,'',1,$page,$pagesize);
        }
        if(!empty($workplan_list)){
            $count = M("oa_workplan")->where($workplan_list[0]['where'])->count();
        } else {
            $count = 0;
        }
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        foreach ($workplan_list as &$workplan) {
            switch ($workplan['status']) {
                case '11': // 还未开始
                    $expectstart_time = getFormatDate($workplan['workplan_expectstart_time'], 'Y-m-d H:i');
                    $expectend_time = getFormatDate($workplan['workplan_expectend_time'], 'Y-m-d H:i');
                    $workplan['workplan_start_time'] = "<font color='red'>$expectstart_time&nbsp&nbsp</font><span class='label label-sm label-success'>预计</span>";
                    $workplan['workplan_end_time'] = "<font color='red'>$expectend_time&nbsp&nbsp</font><span class='label label-sm label-success'>预计</span>";
                    // 安排的工作
                    if ($arranger_man == '1' || $deptplan == '1') {
                        // $workplan['info'] = 1;
						$workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='workplan_info(" .
                            $workplan['workplan_id'] .
                            ",1)'><i class='fa fa-info-circle'></i>查看 </a> "; 
                    }  // 我自己安排的工作
                    else
                        if ($workplan['workplan_arranger_man'] == $workplan['workplan_executor_man']) {
							$workplan['operate'] = " <a  class='layui-btn  layui-btn-xs layui-btn-f60' onclick='workplan_info(" .
                                $workplan['workplan_id'] .
                                ")'><i class='fa fa-info-circle'></i>开始</a> " .
                                "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" .
                                U('oa_workplan_edit',
                                    array(
                                        'workplan_id' => $workplan['workplan_id'],
                                        'workplan_type' => $workplan['workplan_type']
                                    )) . "'><i class='fa fa-edit'></i> 编辑</a>  " .
                                "<a class='layui-btn layui-btn-del layui-btn-xs' href='" .
                                U('oa_workplan_do_del',
                                    array(
                                        'workplan_id' => $workplan['workplan_id']
                                    )) .
                                "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>"; 
                        }  // 我执行的工作
                        else {
							$workplan['start'] = 1;
                            $workplan['operate'] = "<a  class='layui-btn  layui-btn-xs layui-btn-f60' onclick='workplan_info(" .
                                $workplan['workplan_id'] .
                                ")'><i class='fa fa-info-circle'></i>开始</a>  ";
                        }

                    break;
                case '21':
                    $start_time = getFormatDate($workplan['workplan_start_time'],'Y-m-d H:i');
                    $expectend_time = getFormatDate($workplan['workplan_expectend_time'], 'Y-m-d H:i');
                    $workplan['workplan_start_time'] = $start_time;
                    $workplan['workplan_end_time'] = "<font color='red'>$expectend_time&nbsp&nbsp</font><span class='label label-sm label-success'>预计</span>";
                    if ($arranger_man == '1' || $deptplan == '1') {
                        
                         $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('oa_workplan_info', array(
                            'workplan_id' => $workplan['workplan_id'],is_arranger=>'1'
                            )) . "'><i class='fa fa-info-circle'></i>查看 </a> ";
                         
                         $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='workplan_info(" .
                            $workplan[workplan_id] .
                            ",1)'><i class='fa fa-info-circle'></i>查看 </a> "; 
                    } else {
                        
                         $workplan['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('oa_workplan_info', array(
                         'workplan_id' => $workplan['workplan_id']
                         )) . "'><i class='fa fa-info-circle'></i>正在进行</a> ";
                         $workplan['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='workplan_info(" .
                            $workplan['workplan_id'] .
                            ")'><i class='fa fa-info-circle'></i>正在进行 </a> "; 
                    }

                    break;
                case '31':
                    $start_time = getFormatDate($workplan['workplan_start_time'],'Y-m-d H:i');
                    if (!empty($workplan['workplan_end_time'])) {
                        $end_time = getFormatDate($workplan['workplan_end_time'],'Y-m-d H:i');
                    }
                    $workplan['workplan_start_time'] = $start_time;
                    $workplan['workplan_end_time'] = $end_time;
                    // 安排的工作
                    // if($arranger_man=='1' || $deptplan=='1')
                    if ($arranger_man == '1') {
						 $workplan['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='workplan_info(" .
                            $workplan['workplan_id'] .
                            ",1)'><i class='fa fa-info-circle'></i>待审核 </a> "; 
                        $workplan['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('oa_workplan_info', array('workplan_id'
                        => $workplan['workplan_id'] ,is_arranger=>'1')) . "'><i class='fa
                        fa-info-circle'></i>待审核</a> ";
                    }  // 我自己安排的工作
                    else
                        if ($workplan['workplan_arranger_man'] ==
                            $workplan['workplan_executor_man']) {
                            $workplan['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('oa_workplan_info',
                            array('workplan_id' => $workplan['workplan_id'])) . "'><i
                            class='fa fa-info-circle'></i>待审核</a> ";
							 $workplan['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='workplan_info(" .
                                $workplan['workplan_id'] .
                                ")'><i class='fa fa-info-circle'></i>待审核 </a> "; 
                        }  // 我执行的工作
                        else {
                             $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs'  onclick='workplan_info(" .
                                $workplan['workplan_id'] .
                                ")'><i class='fa fa-info-circle'></i>查看</a>  "; 
                        }
                    break;
                default:
                    $audit_time = getFormatDate($workplan['workplan_audit_time'],'Y-m-d H:i');
                    if (!empty($workplan['workplan_start_time'])) {
                        $end_time = getFormatDate($workplan['workplan_start_time'],'Y-m-d H:i');
                    }
                    $workplan['workplan_start_time'] = $end_time;
                    $workplan['workplan_end_time'] = $audit_time;
                    // if($arranger_man=='1' || $deptplan=='1')
                    if ($arranger_man == '1') {
						 $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='workplan_info(" .
                            $workplan['workplan_id'] .
                            ",1)'><i class='fa fa-info-circle'></i>查看 </a> "; 
                        
                         $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('oa_workplan_info', array(
                         'workplan_id' => $workplan['workplan_id'],is_arranger=>'1'
                         )) . "'><i class='fa fa-info-circle'></i>查看</a> " ;
                         
                    } else {
                        
                         $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('oa_workplan_info', array(
                         'workplan_id' => $workplan['workplan_id']
                         )) . "'><i class='fa fa-info-circle'></i>查看</a> " ;
                         
						
                       $workplan['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='workplan_info(" . $workplan['workplan_id'] . ")'><i class='fa fa-info-circle'></i>查看 </a> ";
                    }
            }

            if ($arranger_man != '1' && $workplan['workplan_arranger_man'] != $workplan['workplan_executor_man']) {
                $arranger_man= getStaffInfo($workplan['workplan_arranger_man'], 'staff_name');
                $workplan_title=$workplan['workplan_title'];
                $workplan['workplan_title']="$workplan_title&nbsp&nbsp<span class='label label-sm label-success'>$arranger_man</span>";
            }
            $workplan['workplan_executor_man'] = getStaffInfo($workplan['workplan_executor_man']);
            $workplan['workplan_arranger_man'] = getStaffInfo($workplan['workplan_arranger_man']);
            $workplan_status = $workplan[status];
            $workplan['status_name'] = getStatusName('workplan_status', $workplan_status);
        }
		$workplan_list = ['code'=>0,'msg'=>0,'count'=>$count,'data'=>$workplan_list];
        $this->ajaxReturn($workplan_list); // 返回json格式数据
    }

    /**
     * 工作计划添加/编辑，添加为_add
     *
     * @name oa_workplan_edit()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:30:43
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_edit()
    {
        checkAuth(ACTION_NAME);
        $now_time = date("Y-m-d H:i:s");
        $db_workplan = M('oa_workplan');
        $workplan_id = I('get.workplan_id');
        $is_arranger = I('get.is_arranger');
        if (!empty($workplan_id)) { // 修改工作计划
            $workplan_map['workplan_id'] = $workplan_id;
            $workplan_row = $db_workplan->where($workplan_map)->find();
        } else {
            $workplan_row['workplan_executor_man'] = $this->staff_no;
            $staff_dept_no=getStaffInfo($this->staff_no,'staff_dept_no');
            $dept_manager=getDeptInfo($staff_dept_no,'dept_manager');
            if (empty($dept_manager)) {
                $workplan_row['workplan_arranger_man'] = $this->staff_no;
            } else {
                $workplan_row['workplan_arranger_man'] = $dept_manager;
            }

            $workplan_row['workplan_expectstart_time'] = $now_time;
            $workplan_row['workplan_expectend_time'] = $now_time;
        }
        $this->assign("is_arranger", $is_arranger);
        $this->assign("workplan_row", $workplan_row);
        $this->display("Oaworkplan/oa_workplan_edit");
    }

    /**
     * 工作计划操作执行
     *
     * @name oa_workplan_do_save()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:31:30
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_do_save()
    {
        checkLogin();
        $staff_no = $this->staff_no;
        $db_workplan = M('oa_workplan');
        if (!empty($_POST['workplan_id'])) { // 有id时执行修改操作
            $_POST['update_time'] = date("Y-m-d H:i:s");
            $oper_res = $db_workplan->save($_POST);
            saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $_POST['workplan_id'] . ']进行修改操作');
        } else { // 无id时执行添加操作
            // $_POST['workplan_arranger_man']=$staff_no;
            $_POST['add_time'] = date("Y-m-d H:i:s");
            $_POST['add_staff'] = $staff_no;
            $_POST['status'] = "11";
            if ($_POST['is_arranger'] == '1') {
                // 我安排的工作
                $_POST['workplan_arranger_man'] = $staff_no;
            } else {
                // 我申请的工作
                $_POST['workplan_executor_man'] = $staff_no;
            }

            $oper_res = $db_workplan->add($_POST);


            $alert_title = "您有一条工作计划要做";
            //$alert_url = U('oa_workplan_info',array('workplan_id'=>$oper_res));
            $alert_url = "Oa/Oawork/oa_workplan_info/workplan_id/" . $oper_res;
            saveAlertMsg('41', $_POST['workplan_arranger_man'], $alert_url, $alert_title, '', '', '', $this->staff_no);
            $planlog_summary = $_POST['workplan_title'];
            saveWorkplanLog($oper_res, $planlog_summary, $staff_no, "11", "");
            // (预计开始时间) 执行人：您的《工作标题》已到达预计开始时间，请按时开展工作。如有特殊情况，请进行延期申请。
            if (getConfig('oa_staff') == 1) {
                $msg_param['title'] = "您的《" . $_POST['workplan_title'] . "》已到达预计开始时间，请按时开展工作。如有特殊情况，请进行延期申请";
                $msg_param['alert_type'] = "05";
                $msg_param['alert_man'] = $_POST['workplan_executor_man'];
                $msg_param['alert_param'] = $oper_res;
                $msg_param['alert_title'] = $_POST['workplan_title'];
                $msg_param['alert_content'] = "您的《" . $_POST['workplan_title'] . "》已到达预计开始时间，请按时开展工作。如有特殊情况，请进行延期申请";
                $msg_param['alert_time'] = $_POST['workplan_expectstart_time'];
                $msg_param['alert_cycle'] = 'one';
                $msg_param['add_staff'] = $staff_no;

//                 sendMultiMsg('oa_staff', $staff_no, $_POST['workplan_executor_man'], json_encode($msg_param));


                // （预计开始时间）审核人：xxx（执行人）的《工作标题》工作已到达预计开始时间，请及时跟进工作进度。
                $msg_param['title'] = getStaffInfo($_POST['workplan_executor_man']) . "的《" . $_POST['workplan_title'] . "》工作已到达预计开始时间，请及时跟进工作进度";
                $msg_param['alert_type'] = "05";
                $msg_param['alert_man'] = $_POST['workplan_arranger_man'];
                $msg_param['alert_param'] = $oper_res;
                $msg_param['alert_title'] = $_POST['workplan_title'];
                $msg_param['alert_content'] = getStaffInfo($_POST['workplan_executor_man']) . "的《" . $_POST['workplan_title'] . "》工作已到达预计开始时间，请及时跟进工作进度";
                $msg_param['alert_time'] = $_POST['workplan_expectstart_time'];
                $msg_param['alert_cycle'] = 'one';
                $msg_param['add_staff'] = $staff_no;

//                 sendMultiMsg('oa_staff', $staff_no,  $_POST['workplan_arranger_man'], json_encode($msg_param));
                // 2）预计结束时间：如果还未结束，提醒工作执行人及审核人
                // 执行人：您的《工作标题》已到达预计结束时间，请按时完成工作。如有特殊情况，请进行延期申请。
                $msg_param['title'] = "您的《" . $_POST['workplan_title'] . "》工作已到达预计结束时间，请按时完成工作。如有特殊情况，请进行延期申请";
                $msg_param['alert_type'] = "05";
                $msg_param['alert_man'] = $_POST['workplan_executor_man'];
                $msg_param['alert_param'] = $oper_res;
                $msg_param['alert_title'] = $_POST['workplan_title'];
                $msg_param['alert_content'] = "您的《" . $_POST['workplan_title'] . "》工作已到达预计结束时间，请按时完成工作。如有特殊情况，请进行延期申请";
                $msg_param['alert_time'] = $_POST['workplan_expectend_time'];
                $msg_param['alert_cycle'] = 'one';
                $msg_param['add_staff'] = $staff_no;
//                 sendMultiMsg('oa_staff', $staff_no, $_POST['workplan_executor_man'], json_encode($msg_param));
                // 审核人：安排人：xxx（执行人）的《工作标题》工作已到达预计完成时间，请及时跟进工作进度
                $msg_param['title'] = getStaffInfo($_POST['workplan_executor_man']) . "的《" . $_POST['workplan_title'] . "》工作已到达预计完成时间，请及时跟进工作进度";
                $msg_param['alert_type'] = "05";
                $msg_param['alert_man'] = $_POST['workplan_arranger_man'];
                $msg_param['alert_param'] = $oper_res;
                $msg_param['alert_title'] = $_POST['workplan_title'];
                $msg_param['alert_content'] = getStaffInfo($_POST['workplan_executor_man']) . "的《" . $_POST['workplan_title'] . "》工作已到达预计完成时间，请及时跟进工作进度";
                $msg_param['alert_time'] = $_POST['workplan_expectend_time'];
                $msg_param['alert_cycle'] = 'one';
                $msg_param['add_staff'] = $staff_no;
//                 sendMultiMsg('oa_staff', $staff_no,  $_POST['workplan_arranger_man'], json_encode($msg_param));
            }
            saveLog(ACTION_NAME, 1, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '新增一条工作计划数据，编号为[' . $oper_res . ']');
        }
        if ($oper_res) {

            showMsg(success, '操作成功', U('oa_workplan_index'));
        } else {
            showMsg(error, '操作失败');
        }
    }

    /**
     * 工作计划详情
     *
     * @name oa_workplan_info()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:32:00
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_info()
    {
        checkAuth(ACTION_NAME);
        $db_workplan = M('oa_workplan');
        $db_workplan_log = M('oa_workplan_log');
//         $oa_workplan_perf = M('oa_workplan_perf');
        // 是否显示绩效
        $is_perf = getConfig('is_perf');
        $this->assign("is_perf", $is_perf);
        // 是否是我安排的工作
        $is_arranger = I('get.is_arranger');
        $this->assign("is_arranger", $is_arranger);
        $workplan_id = I('get.workplan_id'); // I方法获取数据
        // $industry=getConfig('industry');
        $workplan_nowork = I('get.workplan_nowork');
        $this->assign("workplan_nowork", $workplan_nowork);
        if (!empty($workplan_id)) {
            $workplan_map['workplan_id'] = $workplan_id;
            $workplan_row = $db_workplan->where($workplan_map)->find();
            if ($workplan_row['workplan_executor_man'] == $workplan_row['workplan_arranger_man']){
                // 自己给自己安排的工作
                $this->assign("is_my", '1');
            }
            // 判断状态值及相应状态颜色
            $status_no = $workplan_row['status'];
            // 分管领导
            $workplan_row['workplan_executor_man'] = getStaffInfo($workplan_row['workplan_executor_man'], 'staff_name');
            // 执行人
            $workplan_row['workplan_arranger_man'] = getStaffInfo($workplan_row['workplan_arranger_man'], 'staff_name');
            $workplan_row['workplan_audit_man'] = getStaffInfo($workplan_row['workplan_audit_man'], 'staff_name');
            $workplan_row['workplan_reject_man'] = getStaffInfo($workplan_row['workplan_reject_man'], 'staff_name');
            $workplan_row['workplan_stop_man'] = getStaffInfo($workplan_row['workplan_stop_man'], 'staff_name');
            $workplan_row['workplan_end_man'] = getStaffInfo($workplan_row['workplan_end_man'], 'staff_name');
            $workplan_row['workplan_expectstart_time'] = getFormatDate($workplan_row['workplan_expectstart_time'], 'Y-m-d');
            $workplan_row['workplan_expectend_time'] = getFormatDate($workplan_row['workplan_expectend_time'], 'Y-m-d');
            $workplan_row['workplan_start_time'] = getFormatDate($workplan_row['workplan_start_time'], 'Y-m-d');
            $workplan_row['workplan_stop_time'] = getFormatDate($workplan_row['workplan_stop_time'], 'Y-m-d');
            $workplan_row['workplan_end_time'] = getFormatDate($workplan_row['workplan_end_time'], 'Y-m-d');
            $workplan_row['workplan_audit_time'] = getFormatDate($workplan_row['workplan_audit_time'], 'Y-m-d');
            $workplan_row['add_time'] = getFormatDate($workplan_row['add_time'], 'Y-m-d');
            $workplan_row['status_name'] = getStatusName("workplan_status", $status_no);

            //获取工作计划记录
            $workplan_log_list = $db_workplan_log->where($workplan_map)
                ->order('add_time desc')
                ->select();
            foreach ($workplan_log_list as &$log) {
                $log['staff_name'] = getStaffInfo($log['add_staff']);
                $log['communist_avatar'] = getStaffInfo($log['add_staff'], 'staff_avatar');
                $log['communist_avatar'] = getUploadInfo($log['communist_avatar']);
                $log['add_date'] = getFormatDate($log['add_time'], 'Y-m-d');
                
                switch ($log['status']) {
                    case '11':
                        // 新增工作计划;
                        $log['plan_info'] = "【新增】" . $log['staff_name'] . "于" . $log['add_date'] . " 添加工作计划，执行人:" . $workplan_row['workplan_executor_man'] . ", 审核人:" . $workplan_row['workplan_arranger_man'] . "";
                        break;
                    case '21':
                        //21：开始工作计划
                        $log['plan_info'] = "【开始】" . $log['staff_name'] . "于" . $log['add_date'] . "  " . $log['planlog_summary'];
                        break;
                    case '22':
                        //22：每日工作总结
                        $log['plan_info'] = "【当日工作总结】" . $log['staff_name'] . "于" . $log['add_date'] . " 添加当日工作总结：" . $log['planlog_summary'];
                        break;
                    case '31':
                        //31：已经完成
                        $log['plan_info'] = "【完成】" . $log['staff_name'] . "于" . $log['add_date'] . " 完成工作";
                        break;
                    case '32':
                        //32：已经审核
                        $log['plan_info'] = "【审核】" . $log['staff_name'] . "于" . $log['add_date'] . " 进行审核，工作意见：" . $workplan_row['workplan_audit_content'] . "！工作评分：" . $workplan_row['workplan_audit_score'];
                        break;
                    case '51':
                        // 51：已经中止
                        $log['plan_info'] = "【中止】" . $log['staff_name'] . "于" . $log['add_date'] . " 中止工作计划";
                        break;
                    case '52':
                        //52：已经驳回
                        $log['plan_info'] = "【驳回重做】" . $log['staff_name'] . "于" . $log['add_date'] . " 驳回了工作，驳回原因：" . $log['planlog_summary'];
                        break;
                    case '71':
                        //71：申请延期
                        $log['plan_info'] = "【申请延期】" . $log['staff_name'] . "于" . $log['add_date'] . " 申请延期，延期时间至：" . $log['planlog_summary'] . "  延期原因：" . $log['memo'];
                        break;
                    case '72':
                        //72：驳回延期申请
                        $log['plan_info'] = "【延期驳回】" . $log['staff_name'] . "于" . $log['add_date'] . " 驳回延期申请，驳回原因：" . $log['planlog_summary'];
                        break;
                    case '73':
                        //73 同意延期
                        $log['plan_info'] = "【同意延期】" . $log['staff_name'] . "于" . $log['add_date'] . " 同意延期，延期时间至:" . $log['planlog_summary'];
                        break;
                }

            }
            $this->assign("workplan_id", $workplan_id);
            $this->assign("workplan_log_list", $workplan_log_list);
            $this->assign("workplan_row", $workplan_row); // 控制器与视图页面的变量尽量保持一致
        }
        $this->display("Oaworkplan/oa_workplan_info");
    }

    /**
     * 工作计划详情操作执行
     *
     * @name oa_workplan_info_do_save()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:32:56
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_info_do_save()
    {
        checkLogin();
        $db_workplan = M('oa_workplan');
        $db_workplan_log = M('oa_workplan_log');
        $plan_nowork = I('get.workplan_nowork');
        $data = I('post.'); // I方法获取整个数组
        if (!empty($data['workplan_id'])) { // 有id时执行修改操作
            $workplan_id = $data['workplan_id'];
            $workplan_map['workplan_id'] = $workplan_id;
            switch ($data['flag']) {
                case "start":
                    $data['status'] = "21";
                    $data['workplan_start_time'] = date("Y-m-d H:i:s");
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, "开始工作计划", $this->staff_no, '21', '');
                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行开始工作操作');
                    break;
                case "continue":
                    $data['status'] = "21";
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, "重新开始工作计划", $this->staff_no, '21', '');
                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行重新开始工作操作');
                    break;
                case "summary":
                    if (empty($data['planlog_summary'])) {
                        $planlog_summary = '今日工作总结已完成';
                    } else {
                        $planlog_summary = $data['planlog_summary'];
                    }
                    $oper_res = saveWorkplanLog($workplan_id, $planlog_summary, $this->staff_no, '22', '');
                    //保存操作日志
                    saveLog(ACTION_NAME, 1, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '新增一条工作进度总结数据，编号为[' . $oper_res . ']');
                    break;
                case "finish":

                    if (empty($data['planlog_summary'])) {
                        $planlog_summary = '工作计划已完成';
                    } else {
                        $planlog_summary = $data['planlog_summary'];
                    }
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, $planlog_summary, $this->staff_no, '31', '');
                    $data['status'] = "31";
                    $data['workplan_end_time'] = date("Y-m-d H:i:s");
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //添加提醒
                    $oa_communist = getConfig("oa_communist");
                    if ($oa_communist == 1) {
                        $workplan_info = $db_workplan->where($workplan_map)->find();
                        $msg_param['title'] = getStaffInfo($this->staff_no) . "的《" . $workplan_info['workplan_title'] . "》工作已完成，请及时进行工作审核";
                        $msg_param['alert_type'] = "05";
                        $msg_param['alert_man'] = $workplan_info['workplan_arranger_man'];
                        $msg_param['alert_param'] = "workplan_id/" . $oper_res;
                        $msg_param['alert_title'] = "工作计划消息提醒";
                        $msg_param['alert_content'] = $msg_param['title'];
                        $msg_param['alert_time'] = $_POST['workplan_expectstart_time'];
                        $msg_param['alert_cycle'] = 'one';
                        $msg_param['add_staff'] = $this->staff_no;
//                         sendMultiMsg($oa_communist, this-$this->staff_no, $workplan_info['workplan_arranger_man'], json_encode($msg_param));
                    }
                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行完成工作操作');
                    break;
                case "audit":
                    $data['status'] = "32";
                    if (empty($data['workplan_edit'])) {
                        $workplan_edit = '工作计划已审核';
                    } else {
                        $workplan_edit = $data['workplan_edit'];
                    }
                    $data['workplan_audit_content'] = $workplan_edit;
                    $data['workplan_audit_time'] = date("Y-m-d H:i:s");
                    $data['workplan_audit_man'] = $this->staff_no;
                    $data['workplan_audit_score'] = $data['workplan_audit_score'];
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, $workplan_edit, $this->staff_no, '32', '');
                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行审核工作操作');
                    break;
                case "reject":
                    $data['status'] = "52";
                    $data['workplan_reject_reasons'] = $data['workplan_edit'];
                    $data['workplan_reject_time'] = date("Y-m-d H:i:s");
                    $data['workplan_reject_man'] = $this->staff_no;
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, $data['workplan_edit'], $this->staff_no, '52', '');
                    // 保存提醒
                    $oa_communist = getConfig("oa_communist");
                    if ($oa_communist == 1) {
                        $workplan_info = $db_workplan->where($workplan_map)->find();
                        $msg_param['title'] = getStaffInfo($this->staff_no) . "已驳回《" . $workplan_info['workplan_title'] . "》的工作，请重新修改提交";
                        $msg_param['alert_type'] = "05";
                        $msg_param['alert_man'] = $workplan_info['workplan_arranger_man'];
                        $msg_param['alert_param'] = "workplan_id/" . $oper_res;
                        $msg_param['alert_title'] = "工作计划消息提醒";
                        $msg_param['alert_content'] = $msg_param['title'];
                        $msg_param['alert_time'] = $_POST['workplan_expectstart_time'];
                        $msg_param['alert_cycle'] = 'one';
                        $msg_param['add_staff'] = $this->staff_no;
//                         sendMultiMsg('oa_communist', this-$this->staff_no, $workplan_info['workplan_arranger_man'], json_encode($msg_param));
                    }

                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行驳回重做操作');
                    break;
                case "end":
                    $data['status'] = "61";
                    $data['workplan_end_content'] = $data['workplan_edit'];
                    $data['workplan_end_time'] = date("Y-m-d H:i:s");
                    $data['workplan_end_man'] = $this->staff_no;
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行结束工作操作');
                    break;
                case "stop":
                    $data['status'] = "51";
                    if (empty($data['planlog_summary'])) {
                        $workplan_edit = '工作计划已中止';
                    } else {
                        $workplan_edit = $data['planlog_summary'];
                    }
                    $data['workplan_stop_content'] = $workplan_edit;
                    $data['workplan_stop_time'] = date("Y-m-d H:i:s");
                    $data['workplan_stop_man'] = $this->staff_no;
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, $workplan_edit, $this->staff_no, '51', '');
                    //保存操作日志
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行中止任务操作');
                    break;
                case "light":
                    $oa_workplan_perf = M('oa_workplan_perf');
                    $workplan = $db_workplan->where($workplan_map)->find();
                    foreach ($data as & $light) {
                        $len = stripos($light, '_');
                        if ($len != false) {
                            $light_type = substr($light, '0', $len);
                            $perf_light = substr($light, $len + 1);

                            $perf['workplan_id'] = $workplan_id;
                            // 执行人
                            if ($light_type == "1") {

                                $perf['perf_communist'] = $workplan['workplan_arranger_man'];
                            } else {
                                // 分配主管
                                $perf['perf_communist'] = $workplan['workplan_executor_man'];
                            }
                            switch ($perf_light) {
                                case 'green':
                                    $perf['perf_light'] = "1";
                                    break;
                                case 'yellow':
                                    $perf['perf_light'] = "3";
                                    break;
                                case 'red':
                                    $perf['perf_light'] = "0";
                                    break;
                                case 'blue':
                                    $perf['perf_light'] = "2";
                                    break;
                                case 'purple':
                                    $perf['perf_light'] = "4";
                                    break;
                            }
                            $perf['add_time'] = date("Y-m-d H:i:s");
                            $perf['add_staff'] = $this->staff_no;
                            $perf['light_type'] = "1";
                            $perf_flag = $oa_workplan_perf->add($perf);
                        }
                    }
                    $data['status'] = "41";
                    $data['workplan_perf_time'] = date("Y-m-d H:i:s");
                    $data['workplan_perf_man'] = $this->staff_no;
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行亮灯操作');
                    // 保存亮灯情况
                    break;
                case 'agree':

                    $data['status'] = "21";
                    $data['workplan_expectend_time'] = $data['workplan_delay_time'];
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    saveWorkplanLog($workplan_id, $data['workplan_delay_time'], $this->staff_no, '73', '');
                    $oa_communist = getConfig("oa_communist");
                    if ($oa_communist == 1) {
                        $workplan_info = $db_workplan->where($workplan_map)->find();
                        $msg_param['title'] = getStaffInfo($this->staff_no) . "已同意《" . $workplan_info['workplan_title'] . "》的延期申请,起止时间由" . $workplan_info['workplan_expectstart_time'] . "——" . $workplan_info['workplan_expectend_time'] . "延期至" . $workplan_info['workplan_expectstart_time'] . "——" . $data['workplan_delay_time']; $// getStaffInfo(this-$this->staff_no)."已同意《".$workplan_info['workplan_title']."》的延期申请。起止时间由".$workplan_info['workplan_expectstart_time']."——".$workplan_info['workplan_expectend_time']."延期至".$workplan_info['workplan_expectstart_time']."——".$data['workplan_delay_time'].";
                        $msg_param['alert_type'] = "05";
                        $msg_param['alert_man'] = $workplan_info['workplan_arranger_man'];
                        $msg_param['alert_param'] = "workplan_id/" . $oper_res;
                        $msg_param['alert_title'] = "工作计划消息提醒";
                        $msg_param['alert_content'] = $msg_param['title'];
                        $msg_param['alert_time'] = $_POST['workplan_expectstart_time'];
                        $msg_param['alert_cycle'] = 'one';
                        $msg_param['add_staff'] = $this->staff_no;
//                         sendMultiMsg('oa_communist', this-$this->staff_no, $workplan_info['workplan_arranger_man'], json_encode($msg_param));
                    }

                    saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行同意延期操作');
                    break;
                case 'donot':
                    //未开展
                    if (empty($data['planlog_summary'])) {
                        $planlog_summary = '今日工作计划未开展';
                    } else {
                        $planlog_summary = $data['planlog_summary'];
                    }

                    $oper_res = saveWorkplanLog($workplan_id, $planlog_summary, $this->staff_no, '23', '');
                    //保存操作日志
                    break;
            }
        }
        if ($plan_nowork == 1) {
            $workplan_id = I('get.workplan_id');
            $oper_res = saveWorkplanLog($workplan_id, "今日工作计划未开展", $this->staff_no, '0', '');
        }
        if ($oper_res) {
            if($data['is_arranger'] == 1){
                showMsg(success, '操作成功', U('oa_workplan_index?is_arranger=1'));
            } else {
                showMsg(success, '操作成功', U('oa_workplan_index'));
            }
        } else {
            showMsg(error, '操作失败');
        }
    }

    /**
     * 申请延期
     *
     * @name oa_workplan_adjourned()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:32:56
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_adjourned()
    {
        $reject = I('get.reject');
        $db_workplan = M('oa_workplan');
        $workplan_id = I('post.workplan_id');
        $adjourned_time = I('post.adjourned_time');
        $adjourned_content = I('post.adjourned_content');
        $communist_no = $this->staff_no;
        $is_my = I('post.is_my');
        if ($reject == '1') {
            // 驳回延期
            if (!empty($workplan_id)) {
                $workplan_map['workplan_id'] = $workplan_id;
                $plan_log = saveWorkplanLog($workplan_id, $adjourned_content, $communist_no, '72', '');
                $oper_res = $db_workplan->where($workplan_map)->setField('status', '21');
                saveLog(ACTION_NAME, 1, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '申请工作计划[' . $workplan_id . ']的延期到[' . $adjourned_time . ']');
            }
        } else {
            // 申请延期
            if (!empty($workplan_id)) {
                if ($is_my == "1") {
                    $data['status'] = '21';
                    $data['workplan_delay_time'] = $adjourned_time;
                    $data['workplan_delay_content'] = $adjourned_content;
                    $workplan_map['workplan_id'] = $workplan_id;
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, $adjourned_time, $communist_no, '71', $adjourned_content);
                    saveWorkplanLog($workplan_id, $adjourned_time, $communist_no, '73', $adjourned_content);
                } else {
                    $data['status'] = '71';
                    $data['workplan_delay_time'] = $adjourned_time;
                    $data['workplan_delay_content'] = $adjourned_content;
                    $workplan_map['workplan_id'] = $workplan_id;
                    $oper_res = $db_workplan->where($workplan_map)->save($data);
                    //保存工作计划记录
                    saveWorkplanLog($workplan_id, $adjourned_time, $communist_no, '71', $adjourned_content);
                }

                //保存提醒
                $oa_communist = getConfig("oa_communist");
                if ($oa_communist == 1) {
                    $communist_name = getStaffInfo($this->staff_no);
                    $workplan_map['workplan_id'] = $workplan_id;
                    $workplan_info = $db_workplan->where($workplan_map)->find();
                    $msg_param['title'] = $communist_name . "的《" . $workplan_info['workplan_title'] . "》工作申请延期，起止时间由" . $workplan_info['workplan_expectstart_time'] . "——" . $workplan_info['workplan_expectend_time'] . "延期至" . $workplan_info['workplan_expectstart_time'] . "——" . $workplan_info['workplan_delay_time'] . "。请及时进行审核。";
                    $msg_param['alert_type'] = "05";
                    $msg_param['alert_man'] = $workplan_info['workplan_arranger_man'];
                    $msg_param['alert_param'] = "workplan_id/" . $oper_res;
                    $msg_param['alert_title'] = "工作计划消息提醒";
                    $msg_param['alert_content'] = $msg_param['title'];
                    $msg_param['alert_time'] = $_POST['workplan_expectend_time'];
                    $msg_param['alert_cycle'] = 'one';
                    $msg_param['add_staff'] = $communist_no;
                }
                //保存系统日志
                saveLog(ACTION_NAME, 1, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '申请工作计划[' . $workplan_id . ']的延期到[' . $adjourned_time . ']');
            }
        }

        if ($oper_res) {

            showMsg(success, '操作成功', U('oa_workplan_index'), 1);
        } else {
            showMsg(error, '操作失败');
        }
    }

    /**
     * 删除工作计划
     *
     * @name oa_workplan_do_del()
     * @param
     *
     * @return
     *
     * @author yangluhai-黄子正
     * @addtime 2016年8月30日上午11:33:28
     * @updatetime 2017年10月30日下午21:27:36
     * @version 0.01
     */
    public function oa_workplan_do_del()
    {
        checkAuth(ACTION_NAME);
        $db_workplan = M('oa_workplan');
        $workplan_id = I('get.workplan_id'); // I方法获取数据
        if (!empty($workplan_id)) { // 必要的非空判断需要增加
            $workplan_map['workplan_id'] = $workplan_id;
            $del_res = $db_workplan->where($workplan_map)->delete();
            saveLog(ACTION_NAME, 3, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行删除操作');
        }
        if ($del_res) {
            showMsg(success, '操作成功', U('oa_workplan_index'), 0);//之前执行了一步
        } else {
            showMsg(error, '操作失败', U('oa_workplan_index'), 0);
        }
    }

    /**
     * 计划重新指派列表
     * @name oa_workplan_reassign()
     * @param
     * @return
     * @author yangluhai
     * @addtime 2016年8月30日上午11:34:10
     * @updatetime 2016年8月30日上午11:34:10
     * @version 0.01
     */
    public function oa_workplan_reassign()
    {
        checkAuth(ACTION_NAME);
        $db_user = M('sys_user');
        $user_id = session("user_id");
        $user_map['user_id'] = $user_id;
        $user_row = $db_user->where($user_map)->find();
        $this->assign("user_row", $user_row);
        $db_workplan = M('oa_workplan');
        $workplan_id = I('get.workplan_id'); // I方法获取数据
        if (!empty($workplan_id)) { // 必要的非空判断需要增加,防止报错
            $workplan_map['workplan_id'] = $workplan_id;
            $workplan_row = $db_workplan->where($workplan_map)->find();
            $workplan_row['workplan_arranger_man'] = getStaffInfo($workplan_row['workplan_arranger_man'], 'communist_name');
            $this->assign("workplan_row", $workplan_row); // 控制器与视图页面的变量尽量保持一致
        }
        $type_list['workplan_date'] = date("Y-m-d");
        $this->display("Oaworkplan/oa_workplan_reassign");
    }

    /**
     * 计划重新指派操作执行
     *
     * @name oa_workplan_reassign_do_save()
     * @param
     *
     * @return
     *
     * @author yangluhai
     * @addtime 2016年8月30日上午11:35:14
     * @updatetime 2016年8月30日上午11:35:14
     * @version 0.01
     */
    public function oa_workplan_reassign_do_save()
    {
        checkLogin();
        $db_workplan = M('oa_workplan');
        $data = I('post.'); // I方法获取整个数组
        if (!empty($data['workplan_id'])) { // 有id时执行修改操作
            $workplan_id = $data['workplan_id'];
            $data['add_time'] = date("Y-m-d H:i:s");
            $data['add_staff'] = getStaffInfo($this->staff_no, 'communist_name');
            $workplan_map['workplan_id'] = $workplan_id;
            $oper_res = $db_workplan->where($workplan_map)->save($data);
            saveLog(ACTION_NAME, 2, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对工作计划编号 [' . $workplan_id . ']进行计划重新指派操作');
        }
        if ($oper_res) {
            $this->success('操作成功', U('oa_workplan_index'));
        } else {
            $this->error('操作失败');
        }
    }
    /**
     * 获取党员姓名
     * @name get_communist_name()
     * @param
     * @return
     * @author 王宗彬
     * @addtime 2016年8月30日上午11:35:14
     * @updatetime 2016年8月30日上午11:35:14
     * @version 0.01
     */
    public function get_communist_name(){
        $communist_no = I('post.communist_no');
        $communist_name = getCommunistInfo($communist_no);
        $res = date('Y-m-d') . "  ".$communist_name."的工作总结";
        $this->ajaxReturn($res);
    }
}
