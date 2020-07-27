<?php
/**
 * 办公平台
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:34
 */

namespace Api\Controller;


use Api\Validate\CommunistNoValidate;
use Api\Validate\DateValidate;
use Api\Validate\NumberValidate;
use Api\Validate\RequireValidate;
use Oa\Model\OaWorklogModel;
use Oa\Model\OaNoticeModel;
use Oa\Model\OaWorkplanModel;

class OaController extends Api
{

    /********************************工作日报***************************/
    /**
     *  get_oa_worklog_list
     * @desc 获取工作日志列表
     * @param int communist_no 党员编号
     * @param int worklog_type 类型
     * @param string keyword 关键词
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_oa_worklog_list()
    {
        (new CommunistNoValidate())->goCheck();

        (new NumberValidate(['worklog_type', 'page', 'pagesize']))->goCheck();

        $communist_no = I("post.communist_no");
        $pagesize = I("post.pagesize");
        $page = I("post.page");
        $worklog_type = I("post.worklog_type");
        $keyword = I("post.keyword");
        $worklog_list = (new OaWorklogModel())->getWorklogList($communist_no, $worklog_type, $keyword, $page, $pagesize);

        if ($worklog_list) {
            foreach ($worklog_list as &$work) {
                $work['add_staff'] = getStaffInfo($work['add_staff']);
            }
            $this->send('获取成功', $worklog_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_oa_worklog_info
     * @desc 获取工作日志详情
     * @param int worklog_id 日志ID
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_oa_worklog_info()
    {
        (new NumberValidate(['worklog_id']))->goCheck();

        $db_worklog = M('oa_worklog');

        $worklog_id = I('post.worklog_id'); // I方法获取数据

        $worklog_info = $db_worklog->where("worklog_id=$worklog_id")->find();

        if ($worklog_info) {
            $worklog_info['audit_man'] = getCommunistInfo($worklog_info['worklog_audit_man'], 'communist_name');

            $worklog_info['worklog_attach'] = getUploadInfo($worklog_info['worklog_attach']);

            $worklog_info['worklog_type'] = getBdTypeInfo($worklog_info['worklog_type'], 'worklog_type');
            $communist = getCommunistInfo($worklog_info['add_staff'], 'communist_name,communist_avatar');
            $worklog_info['communist_name'] = $communist['communist_name'];
            $worklog_info['communist_avatar'] = '/' . $communist['communist_avatar'];
            $this->send('获取成功', $worklog_info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_oa_worklog
     * @desc 添加日报
     * @param int communist_no 党员编号
     * @param int worklog_type 类型
     * @param string worklog_title 标题
     * @param int worklog_audit_man 审核人
     * @param string worklog_summary 内容
     * @param int worklog_attach 附件ID
     * @param string worklog_address 地址
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function set_oa_worklog()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['worklog_type', 'worklog_audit_man']))->goCheck();
        (new RequireValidate(['worklog_title', 'worklog_summary']))->goCheck();
        
        $worklog = I('post.');

        $worklog['add_staff'] = I('post.communist_no');
        $worklog['worklog_staff'] = 9999;
        $worklog['worklog_date'] = date("Y-m-d");
        $worklog['status'] = 0;


        $result = (new OaWorklogModel())->Post($worklog);

        if ($result) {

            $this->send('添加成功', null, 1);
        } else {
            $this->send('添加失败');
        }
    }

    /********************************end***************************/

    /********************************通知公告***************************/

    /**
     *  get_oa_notice_list
     * @desc 通知公告列表
     * @param int communist_no 党员编号
     * @param int status 状态
     * @param int page
     * @param int pagesize
     * @user liubingtao --- liuchangjun
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_oa_notice_list()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['status']))->goCheck();

        $communist_no = I("post.communist_no");
        $status = I("post.status");
        $pagesize = I('post.pagesize');
        $page = I('post.page');
        $page = ($page-1)*$pagesize;
        $db_notice = M('oa_notice');
        if($status == '1'){
            $data = $db_notice->where("FIND_IN_SET('$communist_no',notice_communist)")->order('add_time desc')->select();
            $notice_data = array();
            foreach ($data as $list) {
                $where['notice_id'] = $list['notice_id'];
                $where['communist_no'] = $communist_no;
                $res = M('oa_notice_log')->where($where)->getField('log_id');
                if(!$res){
                    $notice_data[] = $list;
                }
            }
        }else{
            $where = " and l.communist_no=$communist_no";
            $notice_data = $db_notice->join('LEFT JOIN sp_oa_notice_log as l on sp_oa_notice.notice_id=l.notice_id')->where("FIND_IN_SET('$communist_no',notice_communist)" . $where)->field('sp_oa_notice.notice_id,notice_title,notice_content,sp_oa_notice.add_staff,sp_oa_notice.add_time,is_app_pc')->group('notice_id')->order('add_time desc')->select(); 
        }
        // $where = $status == 1 ? " and (l.communist_no is null or l.communist_no != $communist_no)" : " and l.communist_no=$communist_no";
        // $notice_data = $db_notice->join('LEFT JOIN sp_oa_notice_log as l on sp_oa_notice.notice_id=l.notice_id')->
        // where("FIND_IN_SET('$communist_no',notice_communist)" . $where)->
        // field('sp_oa_notice.notice_id,notice_title,notice_content,sp_oa_notice.add_staff,sp_oa_notice.add_time,is_app_pc')->limit($page, $pagesize)->group('notice_id')->order('add_time desc')->select();
        if ($notice_data) {
            foreach ($notice_data as &$notice) {
                // $communist = getCommunistInfo($notice['add_staff'], 'communist_name,communist_avatar');
                //$notice['communist_avatar'] = empty($communist['communist_avatar']) ? null : '/' . $communist['communist_avatar'];
                // dump($communist);die;
                // $notice['add_staff'] = $communist['communist_name'];
                $notice['add_staff'] = peopleNoName($notice['add_staff']);
                
                $notice['add_time'] = getFormatDate($notice['add_time'], "Y-m-d");
                $notice['update_time'] = getFormatDate($notice['update_time'], "Y-m-d");
                $notice['is_read'] = $status;
            }

            $this->send('获取成功', $notice_data, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_oa_notice_info
     * @desc 获取公告详情
     * @param int communist_no 党员编号
     * @param int notice_id 公告ID
     * @param int is_read 是否阅读
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_oa_notice_info()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['notice_id', 'is_read']))->goCheck();

        $oa_notice_log = M('oa_notice_log');
        $notice_id = I("post.notice_id");
        $communist_no = I("post.communist_no");
        $is_read = I("post.is_read");

        $notice_info = getNoticeInfo($notice_id);

        if ($notice_info) {

            //查询当前公告已读人员列表
            $notice_log_list = $oa_notice_log->where("notice_id = $notice_id")->order('add_time desc')->select();
            foreach ($notice_log_list as &$list) {
                $list['communist_name'] = getCommunistInfo($list['communist_no']);
            }
            $notice_info['read_list'] = $notice_log_list;
            //发布人
            $notice_info['add_staff'] = peopleNoName($notice_info['add_staff']);
            
            $notice_info['notice_attach'] = getUploadInfo($notice_info['notice_attach']);
            if ($is_read == 1) {
                $notict['notice_id'] = $notice_id;
                $notict['communist_no'] = $communist_no;
                $notict['add_time'] = date("Y-m-d H:i:s");
                $oa_notice_log->add($notict);
            }
            $this->send('获取成功', $notice_info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_oa_notice
     * @desc 添加公告
     * @param int communist_no 党员编号
     * @param string notice_title 公告标题
     * @param string notice_content 公告内容
     * @param string notice_communist 接收人
     * @param int notice_attach 附件ID
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function set_oa_notice()
    {
        (new CommunistNoValidate())->goCheck();
        (new RequireValidate(['notice_title', 'notice_content', 'notice_communist']))->goCheck();

        $db_notice = new OaNoticeModel();

        $data = I('post.');
        $data['is_app_pc'] = '2';
        $data['add_staff'] = peopleNo($data['communist_no'],1);

        $result = $db_notice->Post($data);

        if ($result) {
            $communist = explode(',', $data['notice_communist']);
            foreach ($communist as $value) {
                $alert_url = "Oa/Oanotice/oa_notice_info/notice_id/" . $result;
                $alert_title = $data['notice_title'];
                saveAlertMsg('21', $value, $alert_url, $alert_title, null, null, null, $value);
            }
            $this->send('发送成功', null, 1);
        } else {
            $this->send('发送失败');
        }
    }

    /********************************end***************************/
    /***************************消息提醒***********************************/
    /**
    *  get_alert_list
    * @desc 消息提醒   
    * @param int communist_no 党员编号
    * @param int status 状态
    * @param int page
    * @param int pagesize
    * @user liubingtao --- liuchangjun
    * @date 2018/2/1
    * @version 1.0.0
    */
    public function get_alert_list()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['status']))->goCheck();
        $communist_no = I("post.communist_no");
        $status = I("post.status");
        $pagesize = I('post.pagesize');
        $page = I('post.page');
        $page = ($page-1)*$pagesize;
        $type_array = M('bd_type')->where("type_group = 'alert_type'")->getField('type_no,type_name');
        if($status == '1'){
            $map['status'] = array('eq','0');
        }else{
            $map['status'] = array('eq','1');
        }
        if($communist_no){
            $map['_string'] = "FIND_IN_SET('$communist_no',alert_man)";
        }
        $alert_list=M('bd_alertmsg')->field("alert_id,alert_man,alert_title,alert_type,alert_content,add_time")->where($map)->order('add_time DESC')->limit($page,$pagesize)->select();
        foreach ($alert_list as &$msg){
            $msg['alert_title']="【".$type_array[$msg['alert_type']]."】".$msg['alert_title'];
            $msg['alert_man']=getCommunistInfo($msg['alert_man']);
        }
        if(!empty($alert_list)){
            $this->send('获取成功', $alert_list, 1);
        }else{
            $this->send();
        }
        
    }
    /**
     *  get_alert_info
     * @desc 获取公告详情
     * @param int communist_no 党员编号
     * @param int notice_id 公告ID
     * @param int is_read 是否阅读
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_alert_info()
    {
        (new NumberValidate(['alert_id']))->goCheck();
        $alert_id = I("post.alert_id");
        $map['alert_id'] = $alert_id;
        $alert_info=M('bd_alertmsg')->field("alert_id,alert_man,alert_title,alert_content,add_time")->where($map)->find();
        $alert_info['alert_man']=getCommunistInfo($alert_info['alert_man']);
        if ($alert_info) {
            $post['status'] = 1;
            M('bd_alertmsg')->where($map)->save($post);
            $this->send('获取成功', $alert_info, 1);
        } else {
            $this->send();
        }
    }
    /******************************end***************************/
    /********************************公文***************************/

    /**
     *  get_examine_template
     * @desc 获取审批模板
     * @user liubingtao
     * @date 2018/2/5
     * @version 1.0.0
     */
    public function get_examine_template()
    {
        $db_tpl = M('oa_approval_template');
        $template_list = $db_tpl->where('status=1')->field('template_no,template_name')->
        order("add_time desc")->select();
        if ($template_list) {
            $this->send('获取成功', $template_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_oa_missive
     * @desc 发起公文
     * @param int communist_no 党员编号
     * @param string missive_receiver_no 接收人
     * @param string missive_cc 抄送人
     * @param string missive_title 公文主题
     * @param string missive_content 公文内容
     * @param int missive_attach 附件ID
     * @param int approval_template 审批模版编号
     * @user liubingtao
     * @date 2018/2/5
     * @version 1.0.0
     */
    public function set_oa_missive()
    {
        (new CommunistNoValidate())->goCheck();
        // (new NumberValidate(['approval_no']))->goCheck();
        (new RequireValidate(['missive_title', 'missive_content']))->goCheck();

        $db_missive = M("oa_missive");
        $data = I("post.");
        $data['is_app_pc'] = '2';
        $missive_communist = $data['communist_no'];
        $data['missive_communist'] = $missive_communist;//missive_communist
        $data['add_staff'] = $missive_communist;
        $data['status'] = 21;
        $prefix = "D" . date("Ymd");
        $data['missive_no'] = getFlowNo($prefix, "oa_missive", 'missive_no', 2);
        $data["add_time"] = date("Y-m-d H:i:s");
        $data["missive_date"] = date("Y-m-d H:i:s");
        $data["update_time"] = date("Y-m-d H:i:s");
        $data["missive_corporation"] = getPartyInfo(getCommunistInfo($missive_communist, 'party_no'));

        $missive_res = $db_missive->add($data);

        $a = str_replace(',', '_', $data["missive_receiver"]);
        if ($missive_res) { // 发起审批

        //     $approval_arr = array(
        //         'approval_template' => $data['template_no'],
        //         'approval_name' => getCommunistInfo(session('communist_no')) . '发起的' . $_POST['imail_title'],
        //         'approval_apply_man' => session('communist_no'),
        //         'approval_table_name' => 'oa_missive',
        //         'approval_table_field' => 'missive_no',
        //         'approval_table_field_value' => $data['missive_no'],
        //         'approval_rewrite_field' => 'status',
        //         'approval_attach' => $data['missive_attach'],
        //         'approval_content' => null,
        //         'approval_callfunction' => "callsaveOamissive(" . $data['missive_no'] . "," . $data['missive_title'] . ",'" . $a . "'," . $missive_communist . ",$" . status . ",$" . node_staff . ");"
        //     );
        //     saveOaApproval($approval_arr);
            $alert_url = "Oa/Oamissive/oa_missive_info/type/1/missive_no/" . $data['missive_no'];
            $alert_title = $data['missive_title'];
            saveAlertMsg('43', $data['missive_receiver_no'], $alert_url, $alert_title, '', '', '', session('communist_no'));

            $this->send('添加成功！等待审批', null, 1);
        } else {
            $this->send('添加失败');
        }
    }

    /**
     *  get_oa_missive_list
     * @desc 获取公文列表
     * @param int communist_no 党员编号
     * @param int type 我发送的/我接收的
     * @param int keywork 关键词
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_oa_missive_list()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['type']))->goCheck();
        $communist_no = I("post.communist_no");
        $keywork = I("post.keywork");
        $type = I("post.type");
        $where = "1=1";
        if (!empty($keywork)) {
            $where .= " and missive_title like '%$keywork%'";
        }
        if ($type == 1) {
            $where .= " and (FIND_IN_SET('$communist_no',missive_receiver) or FIND_IN_SET('$communist_no',missive_cc)) and status=21";//已审核
        } else {
            $where .= " and status>0 and missive_communist='$communist_no'";
        }
        $missive_list = M("oa_missive")->where($where)->order("add_time desc")->select();
        if ($missive_list) {
            foreach ($missive_list as &$mis) {
                $mis['missive_communist'] = getCommunistInfo($mis['missive_communist']);
                if ($type == 1) {
                    $maps = " and sign_communist=$communist_no";
                }
                $sign = M("oa_missive_sign")->where("missive_no='{$mis['missive_no']}' and status=21 $maps")->count();
                if ($sign > 0) {
                    $mis['is_read'] = "已读";
                } else {
                    $mis['is_read'] = "未读";
                }
                $mis['status'] = getStatusName('approval_status', $mis['status']);
            }
            $this->send('获取成功', $missive_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_oa_missive_info
     * @desc 获取公文信息
     * @param string missive_no 公文编号
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_oa_missive_info()
    {
        // (new NumberValidate(['missive_no','communist_no']))->goCheck();
        $db_missive = M("oa_missive");
        $db_missive_sign = M("oa_missive_sign");
        
        $missive_no = I('post.missive_no');
        $communist_no = I('post.communist_no');
        $data = $db_missive->where("missive_no='$missive_no'")->find();
        if ($data) {
            $db_missive_sign->add($a);
            $data['missive_attach'] = getUploadInfo($data['missive_attach']);
            $data['missive_receiver'] = getCommunistInfo($data['missive_receiver']);
            $data['missive_cc'] = getCommunistInfo($data['missive_cc']);
            $data['missive_communist'] = getCommunistInfo($data['missive_communist']);

            $missive_map['missive_no'] = $missive_no;
            $missive_map['sign_communist'] = $communist_no;
            $sign_info = M("oa_missive_sign")->where($missive_map)->find();
            if(empty($sign_info)){
                $missive_row['missive_no'] = $missive_no;
                $missive_row['sign_communist'] = $communist_no;
                $missive_row['add_staff'] = $communist_no;
                $missive_row['sign_time'] = date('Y-m-d H:i:s');
                $missive_row['update_time'] = date('Y-m-d H:i:s');
                $missive_row['add_time'] = date('Y-m-d H:i:s');
                M("oa_missive_sign")->add($missive_row);
            }
            
            $this->send('获取成功', $data, 1);
        } else {
            $this->send();
        }
    }
    /********************************公文end***************************/


    /********************************工作计划***************************/

    /**
     *  get_oa_workplan_list
     * @desc 获取工作计划列表
     * @param int communist_no 党员编号
     * @param int is_arranger 我安排的/我执行的
     * @param int keywork 关键词
     * @param int page 条数
     * @param int pagesize 页数
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function get_oa_workplan_list()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['is_arranger', 'page', 'pagesize']))->goCheck();
        $is_arranger = I('post.is_arranger'); // 我安排的工作
        $communist_no = I('post.communist_no'); // 当前登录人
        $keywork = I('post.keywork'); // 关键词
        $page = I('post.page'); // 关键词
        $pagesize = I('post.pagesize'); // 关键词
        $page = ($page-1)*$pagesize;
        $list = (new OaWorkplanModel())->getWorkplanList($communist_no, $is_arranger, null,
            $keywork, $page, $pagesize);
        // var_dump(M()->getLastSql());die;
        if ($list) {
            foreach ($list as &$item) {
                $item['workplan_executor_man'] = getCommunistInfo($item['workplan_executor_man']);
                $item['workplan_audit_man'] = getCommunistInfo($item['workplan_audit_man']);
            }
            $this->send('获取成功', $list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_oa_workplan_info
     * @desc 获取工作计划详情
     * @param int workplan_id 工作计划ID
     * @user liubingtao
     * @date 2018/2/5
     * @version 1.0.0
     */
    public function get_oa_workplan_info()
    {
        $workplan_id = I('post.workplan_id'); // I方法获取数据
        $info = (new OaWorkplanModel())->getWorkplanInfo($workplan_id);
        if ($info) {
            $info['workplan_expectstart_time'] = getFormatDate($info['workplan_expectstart_time'], 'Y-m-d H:i');
            $info['workplan_expectend_time'] = getFormatDate($info['workplan_expectend_time'], 'Y-m-d H:i');
            $info['workplan_audit_man'] = getCommunistInfo($info['workplan_audit_man'], 'communist_name');
            $this->send('获取成功', $info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_oa_workplan
     * @desc 添加工作计划
     * @param string workplan_title 标题
     * @param string workplan_content 内容
     * @param string workplan_expectstart_time 预计开始时间
     * @param string workplan_expectend_time 预计结束时间
     * @param int workplan_executor_man 执行人
     * @param int workplan_arranger_man 安排人
     * @param string memo 备注
     * @user liubingtao
     * @date 2018/2/5
     * @version 1.0.0
     */
    public function set_oa_workplan()
    {
        (new CommunistNoValidate('workplan_executor_man'))->goCheck();
        (new RequireValidate(['workplan_title', 'workplan_content']))->goCheck();
        // (new DateValidate(['workplan_expectstart_time', 'workplan_expectend_time','Y-m-d']))->goCheck();
        $data = I('post.');
        $result = (new OaWorkplanModel())->Post($data);
        if ($result) {
            saveWorkplanLog($result, $data['workplan_title'], $data['workplan_executor_man'], "11", "");
            $this->send('添加成功', null, 1);
        } else {
            $this->send('添加失败');
        }
    }

    /**
     * @name  perf_mine()
     * @desc  我的绩效
     * @param 
     * @return 
     * @author ljj
     * @version 版本 V1.0.0
     * @updatetime   2018-04-18
     * @addtime   2018-04-18
     */
    public function perf_mine(){
        $communist_no = I('post.communist_no') ? I('post.communist_no') : 9999; // I方法获取党员编号
        $communist_info=M("ccp_communist")->where("communist_no='$communist_no'")->field('communist_id,communist_no,communist_name,party_no,post_no')->find();
        $Yer=date("Y");
        $date=date("m");
        $date_list=getdatelist('','12');
        $num = 1;
        $date_num = intval($date);
        $communist_info['party_name'] = getPartyInfo($communist_info['party_no']);
        $communist_info['post_name'] = getPartydutyInfo($communist_info['post_no']);

        $per="";
        $score='0';
        foreach($date_list as $date_key => &$date_val){
            if($num <= $date_num){
                $entering_date = $date_val['0'];
                $communist_info['entering_detail'][$date_key]['date'] = $Yer.'年'.$entering_date.'月';
                $entering=M("perf_assess")->where("assess_relation_no=".$communist_no." and assess_cycle='".$entering_date."' and assess_year='".$Yer."'")->select();
                $communist_info['entering_detail'][$date_key]['per_sum'] = 0;
                foreach($entering as &$row){
                    $where1['item_id']=$row['item_id'];
                    $items=M('perf_assess_template_item')->where($where1)->find();
                    $row['item_name']=$items['item_name'];
                    $row['item_proportion']=$items['item_proportion'];
                    $row['assess_score']=$row['assess_score']*($row['item_proportion']/100);
                    $communist_info['entering_detail'][$date_key]['per_sum']+=$row['assess_score'];
                }
                $score = $communist_info['entering_detail'][$date_key]['per_sum'];
                $num++;

            }
        }
        $communist_info['score'] = $score;
        $this->send('获取成功', $communist_info, 1);
    }
     /***************************************视频会议**********************************************/
     /**
     * @name  get_oa_meeting_video_list
     * @desc  获取视频会议人员列表
     * @param $video_room string 房间号
     * @return array
     * @time   2017-12-19
     */
    public function get_oa_meeting_video_list(){
        $oa_video = M('oa_meeting_video'); 
        $video_room = I('post.video_room');
        if($video_room){
            $list = $oa_video->where("video_room = '$video_room'")->select(); 
            if($list){
                $data['status'] = '1';
                $data['msg'] = '查询成功';
                $data['list'] = $list;
            }
        }else{
            $data['status'] = '0';
            $data['msg'] = '没有房间好';
        }
        ob_clean();$this->ajaxReturn($data);
    }
    
    /**
     * @name  save_oa_meeting_video
     * @desc  添加/编辑视频会议人员
     * @param $video_room string 房间号
     * @param $staff_name  人员姓名
     * @param $staff_no  人员标识
     * @param $is_anchor  是否位主播
     * @param $is_hide  是否举手
     * @param $video_id   视频会议表ID
     * @return array
     * @time   2017-12-19
     */
    public function save_oa_meeting_video(){
        $oa_video = M('oa_meeting_video');
        $post = $_POST;
        if($post['staff_name']){
            $staff_name = $post['staff_name'];
            $da = $oa_video->where("staff_name = '$staff_name'")->select();
            
        }
        if(empty($da)){
            $video_id = I('post.video_id');
            if(!empty($video_id)){
                $res = $oa_video->where("video_id = '$video_id'")->save($post);
            }else{
                $res = $oa_video->add($post);
            }
        }else{
            $res = 0;
        }
        
        
        if($res){
            $data['status'] = '1';
            $data['msg'] = '操作成功';
        }else{
            $data['status'] = '0';
            $data['msg'] = '操作失败';
        }
        ob_clean();$this->ajaxReturn($data);
    }
    
    /**
     * @name  del_oa_meeting_video
     * @desc  删除视频会议人员
     * @param $video_room   房间号
     * 
     * @param $video_id   视频会议人员表ID
     * @return array
     * @time   2017-12-19
     */
    public function del_oa_meeting_video(){
        $oa_video = M('oa_meeting_video');
        
//      $video_id = I('post.video_id');
        $video_room = I('post.video_room');
//      if(!empty($video_id)){
//          $del_data = $oa_video->where("video_id = '$video_id'")->delete();
//      }else{
//          $$del_data = $oa_video->where("1=1")->delete();
//      }
        if(!empty($video_room)){
            $del_data = $oa_video->where("video_room = '$video_room'")->delete();
        }else{
            $del_data = $oa_video->where("1=1")->delete();
        }
        if($del_data){
            $data['status'] = '1';
            $data['msg'] = '操作成功';
        }else{
            $data['status'] = '0';
            $data['msg'] = '操作失败';
            
        }
        ob_clean();$this->ajaxReturn($data);
    }
    /**
     * @name  get_oa_meeting_video
     * @desc  视频会议列表
     * @return array
     * @time   2017-12-19
     */
    public function get_oa_meeting_video(){
        $oa_video = M('oa_meeting_video');
        $type_no = I('post.type_no');
        if(!empty($type_no)){
            $map['type_no'] = array('eq',$type_no);
        }
        $video_data = $oa_video->where($map)->select();
        if($video_data){
            $data['list'] = $video_data;
            $data['status'] = '1';
            $data['msg'] = '操作成功';
        }else{
            $data['status'] = '0';
            $data['msg'] = '操作失败';
    
        }
        ob_clean();$this->ajaxReturn($data);
    }
}
