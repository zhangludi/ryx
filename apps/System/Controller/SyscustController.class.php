<?php
namespace System\Controller;
use Think\Controller;
class SyscustController extends Controller
{

	/**
	 * @name  syscust_sys_log_index()
	 * @desc  日志列表
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @addtime   2018-6-26
	 */
	public function syscust_sys_log_index(){
	    $opinion_type = I('get.opinion_type');
	    $this->assign('opinion_type',$opinion_type);
	    $this->display();
	}
	
	/**
	 * @name  syscust_sys_log_index_data()
	 *  @desc  意见反馈列表数据
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @addtime   2018-6-26
	 */
	public function syscust_sys_log_index_data(){
	    $opinion_type = I('get.opinion_type');
	    $keyword = I('get.keyword');
	    $count = I('get.limit');
	    $page = I('get.offset');
	    $start_time = I('get.start_time');
	    $end_time = I('get.end_time');
	    $db_syslog=M('sys_log');
	    if (!empty($keyword)){
	        $where['log_newcontent'] = array('like',"%$keyword%");
	    }
	    if(!empty($start_time) and !empty($end_time)){
	        $where['add_time'] = array(array('gt',$start_time),array('elt',$end_time),'and');
	    }elseif (!empty($start_time) and empty($end_time)){
	        $where['add_time'] = array('gt',$start_time);
	    }elseif (empty($start_time) and !empty($end_time)){
	        $where['add_time'] = array('elt',$end_time);
	    }
	    $where['opinion_type'] = $opinion_type;
	    $log_list=$db_syslog->where($where)->limit($page,$count)->order('log_id desc')->select();
	    $log_num=$db_syslog->where($where)->count();
	    foreach($log_list as &$log){
	        $log['add_communist_name'] = getCommunistInfo($log['add_communist']);
	    }
	    $data['data'] = $log_list;
	    $data['count'] = $log_num;
	    $this->ajaxReturn($data);
	}
	/**
	 * @name  syscust_sys_log_del()
	 * @desc  日志列表删除
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @addtime   2018-6-26
	 */
	public function syscust_sys_log_del(){
	    checkAuth(ACTION_NAME);//判断越权
	    $db_syslog=M('sys_log');
	    $log_del=$db_syslog->where('log_id >0')->delete();
	    saveLog(ACTION_NAME,1,'','操作员['.getCommunistInfo(session('communist_no')).']于'.date("Y-m-d H:i:s").'对系统日志进行清空！');
	    if($log_del){
	        showMsg('success', '操作成功！',U('syscust_sys_log_index'));
	    }else{
	        showMsg('error', '操作失败！');
	    }
	}
	
	
}