<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/1/16
 * Time: 下午1:43
 */
namespace Oa\Controller;
use Common\Controller\BaseController;
class OawilldoController extends BaseController
{
    /**
     * 必做任务首页
     *
     * @name oa_willdo_index()
     * @param
     * @return
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:38:18
     *         @updatetime 2016年8月30日上午11:38:18
     * @version 0.01
     */
    public function oa_willdo_index()
    {
        checkAuth(ACTION_NAME);

        $is_hunt = I('get.is_hunt');
        $is_main = I('get.is_main');
        if (! empty($is_hunt)) {
            // 必做任务搜索条件
            // 任务周期：
            $willdo_cycle = I('post.willdo_cycle');
            if (! empty($willdo_cycle)) {
                $this->assign('willdo_cycle', $willdo_cycle);
            }
            // 开始时间
            $time = I('post.time');
            if (!empty($time)) {
                $times = explode(' - ', $time);
                $this->assign('start', $times[0]);
                $this->assign('end', $times[1]);
                $this->assign('time', $time);
            }
            // 标题及内容
            $willdo_content = I('post.willdo_content');
            if (! empty($willdo_content)) {
                $this->assign('willdo_content', $willdo_content);
            }
        }

        $this->display("Oawilldo/oa_willdo_index");
    }

    /**
     * 必做任务首页数据
     *
     * @name oa_willdo_index_data()
     * @param
     * @return
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:40:24
     *         @updatetime 2016年8月30日上午11:40:24
     * @version 0.01
     */
    public function oa_willdo_index_data()
    {
        $db_willdo_log = M('oa_willdo_log');
        // 任务周期：
        $willdo_cycle = I('get.willdo_cycle');
        // 标题及内容
        $willdo_content = I('get.willdo_content');
		$time = explode(' - ',I('get.time'));
        if (! empty($time[0])) {
            $start_time = getFormatDate($time[0], 'H:i');
        }
        if (! empty($time[1])) {
            $end_time = getFormatDate($time[1], 'H:i');
        }
		$page = I('get.page');
		$limit = I('get.limit');
        $staff_no = $this->staff_no;
        $willdo_list = getWilldoList($staff_no, $willdo_cycle, "", $willdo_content, $start_time, $end_time, '',$page,$limit); // getWilldoList//getWilldoList($staff_no,$willdo_cycle,$choosedate,$willdo_content,$start,$end);
		$count = M('oa_willdo')->where($willdo_list[0]['where'])->count();
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($willdo_list as &$willdo) {
            if ($willdo['status'] == 2) {
                $willdo['status'] = "<font color='yellow'>任务已终止</font>";
                $willdo['willdo_status'] = 2;
				$willdo['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='willdo_info(" . $willdo['willdo_id'] . ",2)'><i class='fa fa-info-circle'></i>查看 </a>"; 
            } else {

                $date_now = date("Y-m-d");

                //$willdo_info = $db_willdo_log->where("willdo_id=" . $willdo['willdo_id'] . " AND add_time like '$date_now%' and status='2'")->find();
                if (getWilldoLogInfo($willdo['willdo_id'], $date_now, $staff_no, 2)) {
                    $willdo['status'] = "<font>今日已执行</font>";
                    $willdo['willdo_status'] = 1;
					 $willdo['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='willdo_info(" . $willdo['willdo_id'] . ",1)'><i class='fa fa-info-circle'></i> 查看 </a>  " . "<a class='btn btn-xs blue btn-outline' href='" . U('oa_willdo_edit', array(
                            'willdo_id' => $willdo['willdo_id']
                        )) . "'><i class='fa fa-info-circle'></i> 编辑 </a>  "; 
                } else {
                    $willdo['status'] = "<font color='red'>今日未执行</font>";
                    $willdo['willdo_status'] = 0;
					$willdo['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' onclick='willdo_info(" . $willdo['willdo_id'] . ",0)'><i class='fa fa-info-circle'></i>查看 </a>  " . "<a class='btn btn-xs blue btn-outline'  href='" . U('oa_willdo_edit', array(
                            'willdo_id' => $willdo['willdo_id']
                        )) . "'><i class='fa fa-info-circle'></i> 编辑 </a>  "; 
                }
            }
            $willdo['add_staff'] = $staff_name_arr[$willdo['add_staff']];
            // 转化是否提醒标识
            if ($willdo['is_alert'] == 1) {
                $willdo['is_alert'] = '是';
            } else {
                $willdo['is_alert'] = '否';
            }
			$willdo['add_time'] = getFormatDate($willdo['add_time'], 'Y-m-d');
        }
        ob_clean();
		$willdo_list = [
			'code'=>0,
			'msg'=>0,
			'count'=>$count,
			'data'=>$willdo_list
		];
		$this->ajaxReturn($willdo_list); // 返回json格式数据
    }

    /**
     * 必做任务添加/编辑，添加为_add
     *
     * @name oa_willdo_edit()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:42:25
     *         @updatetime 2016年8月30日上午11:42:25
     * @version 0.01
     */
    public function oa_willdo_edit()
    {
        checkAuth(ACTION_NAME);
        $db_willdo = M('oa_willdo');
        $willdo_id = I('get.willdo_id'); // I方法获取数据
        if (! empty($willdo_id)) { // 必要的非空判断需要增加,防止报错
            $willdo_row = getWilldoInfo($willdo_id, 'all');
            if ($willdo_row['willdo_cycle'] == "04") {
                $willdo_operdate = $willdo_row['willdo_operdate'];
                $willdo_row['willdo_operdate'] = substr($willdo_operdate, 0, 2);
                $willdo_row['willdo_num'] = substr($willdo_operdate, 2, 2);
            } else {
                $willdo_row['willdo_operdate'] = $willdo_row['willdo_operdate'];
            }
        }
        $this->assign("willdo_row", $willdo_row);
        // $this->assign("type_list",$type_list);
        $this->display("Oawilldo/oa_willdo_edit");
    }

    /**
     * 保存必做任务信息
     *
     * @name oa_willdo_do_save()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:42:53
     *         @updatetime 2016年8月30日上午11:42:53
     * @version 0.01
     */
    public function oa_willdo_do_save()
    {
        // 提醒时间 1：半小时，2：2小时 ，3：24小时，4：36小时，5：48小时
        // 提醒类型 01:每日，02：每周，03：每月，04：每年
//        checkLogin();
        $db_willdo = M('oa_willdo');
        $bd_alertmsg = M('bd_alertmsg');
        $data = I('post.'); // I方法获取整个数组
        $willdo_id = $data['willdo_id'];
        $willdo_cycle = $data['willdo_cycle'];
        $get_session_user_no = $this->staff_no;
        $willdo_time_type = I('post.willdo_time_type');
        $year = substr($willdo_time_type,0,4);
        $month = substr($willdo_time_type,5,2);
        $day = substr($willdo_time_type,8,2);
        if ($willdo_cycle == 4) {
            $data['willdo_operdate'] = $month.$day;
        }
        if ($willdo_cycle == 3) {
            $data['willdo_operdate'] = $day;
        }
        if($data['is_alert'] == 'on'){
            $data['is_alert'] = 1;
        } else {
            $data['is_alert'] = 0;
        }
        $data['willdo_content'] = $_POST['willdo_content'];
        $data['status'] = '0';
        if (! empty($willdo_id)) {
            // 有id时执行修改操作
            $oper_res = $db_willdo->save($data);
            saveLog(ACTION_NAME, 2, '', '操作员[' .$get_session_user_no. ']于' . date("Y-m-d H:i:s") . '对必做任务编号 [' . $willdo_id . ']进行修改操作');
        } else {
            $data['add_time'] = date("Y-m-d H:i:s");
            $data['update_time'] = date("Y-m-d H:i:s");
            $data['add_staff'] = $this->staff_no;
            $oper_res = $db_willdo->add($data);
            saveWilldoLog($oper_res, "新增必做任务", "1", $get_session_user_no);
            if($data['is_alert'] == "1"){
                $is_willdomsg = getConfig('willdo_staff');
                if ($is_willdomsg == 1) {
                    $data['alert_type'] = "05";
                    $data['alert_man'] = $this->staff_no;
                    $data['alert_param'] = $oper_res;
                    $data['alert_title'] = "您有任务即将开展";
                    $willdo_time_type = I('post.willdo_time_type');
                    switch($willdo_time_type){
                        case "1":
                            $willdo_time_type = 30;
                            $hours = "minute";
                            break;
                        case "2":
                            $willdo_time_type = 2;
                            $hours = "hours";
                            break;
                        case "3":
                            $willdo_time_type = 24;
                            $hours = "hours";
                            break;
                        case "4":
                            $willdo_time_type = 36;
                            $hours = "hours";
                            break;
                        case "5":
                            $willdo_time_type = 48;
                            $hours = "hours";
                            break;
                    }
                    $alert_cycle = I('post.willdo_cycle');
                    switch($alert_cycle){
                        case "01":
                            $data['alert_cycle'] = "day";
                            $alert_time = date("Y-m-d",strtotime("+1 day"))." ".$data['willdo_start_time'].":00";
                            $data['alert_time'] = date("Y-m-d H:i:s",strtotime("$alert_time -$willdo_time_type $hours"));
                            break;
                        case "02":
                            $data['alert_cycle'] = "week";
                            $alert_time = date("Y-m-d",strtotime("+1 week"))." ".$data['willdo_start_time'].":00";
                            $data['alert_time'] = date("Y-m-d H:i:s",strtotime("$alert_time -$willdo_time_type $hours"));
                            break;
                        case "03":
                            $data['alert_cycle'] = "month";
                            $alert_time = date("Y-m-d",strtotime("+1 month"))." ".$data['willdo_start_time'].":00";
                            $data['alert_time'] = date("Y-m-d H:i:s",strtotime("$alert_time -$willdo_time_type $hours"));
                            break;
                        case "04":
                            $data['alert_cycle'] = "year";
                            $alert_time = date("Y-m-d",strtotime("+1 year"))." ".$data['willdo_start_time'].":00";
                            $data['alert_time'] = date("Y-m-d H:i:s",strtotime("$alert_time -$willdo_time_type $hours"));
                            break;
                    }
                    $data['alert_content'] = "您的《".$data['alert_title']."》任务时间为：".$data['alert_time']."。即将开展。";
                    $data['add_staff'] = $get_session_user_no;
                    sendMultiMsg('willdo_staff', $get_session_user_no, $get_session_user_no, json_encode($data));
                }
            }
        }
        if ($oper_res) {
            showMsg('success', '操作成功！！！', U('Oawilldo/oa_willdo_index'),1);
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }

    /**
     * 必做任务详情
     *
     * @name oa_willdo_info()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:43:27
     *         @updatetime 2016年8月30日上午11:43:27
     * @version 0.01
     */
    public function oa_willdo_info()
    {

        checkAuth(ACTION_NAME);
        $willdo_log = M('oa_willdo_log');
        $willdo_id = I('get.willdo_id'); // I方法获取数据
        $willdo_status = I('get.willdo_status'); // 0:今日未执行，1：今天已执行，2：已终止//3未开展
        if (! empty($willdo_id)) { // 必要的非空判断需要增加,防止报错
            $willdo_row = getWilldoInfo($willdo_id, 'all');
            $willdo_row['willdo_cycle'] = $willdo_row['willdo_cyda'];
            // 判断状态值及相应状态颜色
            $willdo_row['status_name'] = getStatusName("willdo_status", $willdo_row[status]);
            $willdo_row['willdo_time'] = getFormatDate($willdo_row['willdo_start_time'], "H:i") . "--" . getFormatDate($willdo_row['willdo_end_time'], "H:i");
            $willdolog=getWilldoLogInfo($willdo_id, date('Y-m-d'), $willdo_row['add_staff'], 2);
            if ($willdolog) {
                $willdo_row["willdolog_summary"] = $willdolog['willdolog_summary'];
                $willdo_row["willdolog_id"] = $willdolog['willdolog_id'];
                $willdo_row["is_flag"]="1";
            }
            $willdo_row['add_staff'] = getStaffInfo($willdo_row['add_staff']);
            //获取必做任务记录
            $willdo_map['willdo_id'] = $willdo_id;
            $willdo_list = $willdo_log->where($willdo_map)->order('add_time desc')->select();
            $staff_avatar_arr = M('hr_staff')->getField('staff_no,staff_avatar');
            $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
            foreach ($willdo_list as &$willdo){
                $willdo['staff_avatar']=getUploadInfo($staff_avatar_arr[$willdo['add_staff']]);
                if(!$willdo['staff_avatar']){
                	$willdo['staff_avatar'] = '/statics/public/images/default_photo.jpg';
                }
                $willdo['staff_name']=$staff_name_arr[$willdo['add_staff']];
                $willdo['update_time']=getFormatDate($willdo['update_time'], "Y-m-d H:i");
                // 1：今日已完成   2：新增必做
                if($willdo['status']=="1"){
                    $willdo['willdo_log']="【新增】".$willdo['staff_name']."于".$willdo['update_time']." 必做任务，任务周期：".$willdo_row['willdo_cyda'];
                }
                if($willdo['status']=="2"){
                    $willdo['willdo_log']="【今日总结】".$willdo['staff_name']."于".$willdo['update_time']." 进行总结：".$willdo['willdolog_summary'];
                }
                if($willdo['status']=="3"){
                    $willdo['willdo_log']="【中止】".$willdo['staff_name']."于".$willdo['update_time']." 中止必做任务";
                }
                if($willdo['status']=="4"){
                    $willdo['willdo_log']="【恢复】".$willdo['staff_name']."于".$willdo['update_time']." 恢复必做任务";
                }
                if($willdo['status']=="5"){
                    $willdo['willdo_log']="【今日总结】".$willdo['staff_name']."于".$willdo['update_time']." 进行总结：".$willdo['willdolog_summary'];
                }
            }
            if (count($willdo_list) > 0) {
                $this->assign("willdo_list", $willdo_list);
            }
            $this->assign("willdo_row", $willdo_row); // 控制器与视图页面的变量尽量保持一致
            $this->assign("willdo_status", $willdo_status);
        }
        $this->display("Oawilldo/oa_willdo_info");
    }

    /**
     * 必做任务详情操作执行
     *
     * @name oa_willdo_info_do_save()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:43:49
     *         @updatetime 2016年8月30日上午11:43:49
     * @version 0.01
     */
    public function oa_willdo_info_do_save()
    {
        checkLogin();
        $db_willdo = M('oa_willdo');
        $willdo_log = M('oa_willdo_log');
        $data = I('post.'); // I方法获取整个数组
        $willdoid = $data['willdo_id'];
        $willdolog_id = $data['willdolog_id'];
        $get_session_user_no = $this->staff_no;
        $flag = $data['flag'];
        $willdo_status = I('get.willdo_status');

        if (! empty($willdoid)) {
            switch ($flag) {
                case "finish":
                    $willdolog_summary = $data['willdolog_summary'];
                    if (empty($willdolog_summary)) {
                        $willdolog_summary = "必做任务已完成！！！";
                    }
                    $willdo_flag = saveWilldoLog($willdoid, $willdolog_summary, "2", $get_session_user_no);

                    break;
                case "modify":
                    if (! empty($willdolog_id)) {
                        $logdata['update_time'] = date("Y-m-d H:i:s");
                        $logdata['willdolog_summary'] = $data['willdolog_summary'];
                        $willdo_map['willdolog_id'] = $willdolog_id;
                        $willdo_flag = $willdo_log->where($willdo_map)->save($logdata);
                    }
                    break;

                case "stop":
                    $willdo_map['willdo_id'] = $willdoid;
                    $willdo_flag = $db_willdo->where($willdo_map)->setField('status', '2');
                    saveWilldoLog($willdoid, "必做任务已中止", "3", $get_session_user_no);
                    break;
                case "recover":
                    $willdo_map['willdo_id'] = $willdoid;
                    $willdo_flag = $db_willdo->where($willdo_map)->setField('status', '0');
                    saveWilldoLog($willdoid, "必做任务已恢复", "4", $get_session_user_no);
                    break;
                case "donot":
                    $willdolog_summary = $data['willdolog_summary'];
                    if (empty($willdolog_summary)) {
                        $willdolog_summary = "今日必做任务未开展";
                    }
                    $willdo_flag = saveWilldoLog($willdoid, $willdolog_summary, "5",$get_session_user_no);
                    break;
            }
        }
        if ($willdo_flag) {
            saveLog(ACTION_NAME, 2, '', '操作员[' . $get_session_user_no. ']于' . date("Y-m-d H:i:s") . '对今日必做任务任务总结编号 [' . $willdolog_id . ']进行修改操作');
            showMsg('success', '操作成功！！！', U('Oa/oa_willdo_index'), 1);
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }

    /**
     * 删除必做任务
     *
     * @name oa_willdo_do_del()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:44:07
     *         @updatetime 2016年8月30日上午11:44:07
     * @version 0.01
     */
    public function oa_willdo_do_del()
    {
        $db_willdo = M('oa_willdo');
        $db_willdo_log = M('oa_willdo_log');
        $willdo_id = I('get.willdo_id'); // I方法获取数据
        if (! empty($willdo_id)) { // 必要的非空判断需要增加
            $willdo_map['willdo_id'] = $willdo_id;
            $del_res = $db_willdo->where($willdo_map)->delete();
            $del_wolldolog = $db_willdo_log->where($willdo_map)->delete();
        }
        if ($del_res) {
            showMsg('success', '操作成功！！！', U('Oa/oa_willdo_index'));
        } else {
            showMsg('error', '操作失败！！！', '');
        }
    }

    /**
     * 必做任务执行情况明细
     *
     * @name oa_willdo_executiondetail();
     * @param
     * @return
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:44:33
     *         @updatetime 2016年8月30日上午11:44:33
     * @version 0.01
     */
    public function oa_willdo_executiondetail()
    {
        $day_start = date('Y-m-d 00:00:00');
        $db_willdo_log = M('oa_willdo_log');
        $willdo_id = I('get.willdo_id');
        $log_map['willdo_id'] = $willdo_id;
        $log_map['add_time'] = array('lt',$day_start);
        $willdo_list = $db_willdo_log->where($log_map)->order('add_time desc')->select();
        $this->assign("willdo_list", $willdo_list);
        $this->display("Oawilldo/oa_willdo_executiondetail");
    }
}