<?php
namespace Oa\Controller;
// 命名空间
use Common\Controller\BaseController;


class OanoticeController extends BaseController // 继承Controller类
{
     /**
     **************************** 公告模块开始 ***********************
     */
    /**
     * 公告首页
     *
     * @name oa_notice_index();
     * @param
     * @return
     * @author 王宗彬
     *         @updatetime 2017年10月18日
     * @version 0.01
     */
 	public function oa_notice_index(){
        checkAuth(ACTION_NAME);
        $this->display("Oanotice/oa_notice_index");
    }

    /**
     * 公告数据加载
     * @name oa_notice_index_data()
     * @param
     * @return
     * @author   王宗彬
     *         @updatetime 2016年8月30日上午11:46:31
				@updatetime 2017年10月18日
				@updatetime 2017年11月27日  更改查找status=1的数据
     * @version 1.01
     */
    public function oa_notice_index_data(){
      $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
      $str = I('get.start');
      $strt = strToArr($str, ' - ');  //分割时间
      $start = $strt[0];
      $end = $strt[1];
  		$page = I('get.page');
  		$limit = I('get.limit');
      $notice_content = I('get.notice_content');
      $staff_no = session('staff_no');
      if(!empty($start) || !empty($end) || !empty($notice_content) ){
        $db_notice=M('oa_notice');
        $oa_notice_viewrecord = M('oa_notice_log');
			 //标题及内容
  			if(!empty($notice_content)){
  				$notice_map['notice_content'] = array('like','%'.$notice_content.'%');
  				$notice_map['notice_title'] = array('like','%'.$notice_content.'%');
  				$notice_map['_logic'] = 'or';
  			} 
  			if(!empty($notice_map)){
  				$map['_complex'] = $notice_map;
  			}
  			$map['status'] = 1;
  			if(!empty($start) && !empty($end)){   
  				$end=$end." 23:59:59";
  				$map['update_time']  = array('between',array($start,$end));
  			}
  			$viewrecord_list = M('oa_notice')->where($map)->field('notice_id,notice_title,add_staff,update_time')->limit(($page-1)*$limit,$limit)->select();
  			$count = M("oa_notice")->where($map)->count();
        }else{
    			$notice_map['status'] = 1;
                $viewrecord_list = M("oa_notice")->field("notice_id,notice_title,add_staff,update_time")->where($notice_map)->order('update_time desc')->limit(($page-1)*$limit,$limit)->select();
    			$count = M("oa_notice")->where($notice_map)->count();
        }
        $communist_name_arr = M('ccp_people')->getField('people_id,people_name');
        foreach ($viewrecord_list as &$list) {
        	$notice_id = $list['notice_id'];
        	$list['update_time'] = getFormatDate($list['update_time'], "Y-m-d H:i");
        	if($list['add_staff'] ==peopleNo($staff_no,2) ){
        		$list['is_add'] = '0';
            
        	//	$list['notice_title'] = "<a class='fcolor-22' onclick='notice_info(" . $notice_id . ",0)'>".$list['notice_title']."</a>";
        		//$list['operate'] = " <a href='" . U('Oanotice/oa_notice_edit', array('notice_id' => $notice_id)) . "' class='btn btn-xs blue btn-outline'><i class='fa fa-edit'></i>编辑</a><a href='" . U('Oanotice/oa_notice_del', array('notice_id' => $notice_id)) . "' $confirm class='btn btn-xs red btn-outline'><i class='fa fa-trash-o'></i> 删除</a>";
        	}else{
              $list['is_add'] = '1';
        		//$list['notice_title'] = "<a class='fcolor-22' onclick='notice_info(" . $notice_id . ",1)'>".$list['notice_title']."</a>";
        		//$list['operate'] = "<a href='" . U('Oanotice/oa_notice_edit', array('notice_id' => $notice_id)) . "' class='btn btn-xs blue btn-outline'><i class='fa fa-edit'></i>编辑</a><a href='" . U('Oanotice/oa_notice_del', array('notice_id' => $notice_id)) . "' $confirm class='btn btn-xs red btn-outline'><i class='fa fa-trash-o'></i> 删除</a>";
        	}
        	$list['add_staff'] = peopleNoName($list['add_staff']);
			$list['update_time'] = getFormatDate($list['update_time'], 'Y-m-d');
        }
        ob_clean();
		$viewrecord_list = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$viewrecord_list
		);
		$this->ajaxReturn($viewrecord_list); 
    }
    
    
    /**
     * 公告详情数据
     *
     * @name oa_notice_info()
     * @param
     *
     * @return
     *
     * @author 王世超  王宗彬
     *         @updatetime 2016年8月30日上午11:46:57
				@updatetime 2017年10月18日
     * @version 0.01
     */
    public function oa_notice_info(){
        checkAuth(ACTION_NAME);
        $db_notice = M('oa_notice');
        $db_viewrecord = M('oa_notice_log');
        $is_add = I('get.is_add'); // I方法获取数据
        $notice_id = I('get.notice_id'); // I方法获取数据
        if ($is_add == 0) // 登录人为选择人时
        {
           // $communist_no = session('staff_no');
           // $is_no = getNoticeLogInfo($notice_id,$communist_no);//是否存在数据
           // if(empty($is_no)){
           // 	$data['notice_id'] = $notice_id;
           // 	$data['communist_no'] = $communist_no;
           // 	$data['add_staff'] = $communist_no;
           // 	$data['update_time'] = date('Y-m-d H:i:s');
           // 	$data['add_time'] = date('Y-m-d H:i:s');
           // 	$db_viewrecord->add($data);
           // }
           $notice_info = getNoticeInfo($notice_id, 'all');
           $notice_info['add_staff'] = peopleNoName($notice_info['add_staff']);
           $noticeattach = $notice_info['notice_attach'];
           if (! empty($noticeattach)) {
               $notice_attach = strToArr($notice_info['notice_attach'], ',');
               $notice_info['notice_attach'] = arrToStr($notice_attach, '`');
           }
        } else { // 登录人为发布公告人时
           $notice_info = getNoticeInfo($notice_id, 'all');
           $notice_info['add_staff'] = peopleNoName($notice_info['add_staff']);
           $noticeattach = $notice_info['notice_attach'];
           if (! empty($noticeattach)) {
               $notice_attach = strToArr($notice_info['notice_attach'], ',');
               $notice_info['notice_attach'] = arrToStr($notice_attach, '`');
           }
           $notice_log_map['notice_id'] = $notice_id;
          	$is_read = M('oa_notice_log')->where($notice_log_map)->order('add_time desc')->limit(10)->select();//是否存在数据
          	$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
           foreach ($is_read as &$list) {
               $list['communist_no'] = $communist_name_arr[$list['communist_no']];
           }
           $this->assign('read_name', $is_read);
           $this->assign('is_add', 1);
        }
        $notice_info = getNoticeInfo($notice_id, 'all');
        $notice_info['add_staff'] = peopleNoName($notice_info['add_staff']);;
        $noticeattach = $notice_info['notice_attach'];
        if (! empty($noticeattach)) {
            $notice_attach = strToArr($notice_info['notice_attach'], ',');
            $notice_info['notice_attach'] = arrToStr($notice_attach, '`');
        }
        $this->assign('notice_info', $notice_info);
        $this->display("Oanotice/oa_notice_info");
    }
    
     /**
     * 添加/修改公告信息
     *
     * @name oa_notice_edit()
     * @param
     *
     * @return
     *
     * @author 王宗彬
     *         @updatetime 2017年10月18日
     * @version 0.01
     */
    public function oa_notice_edit(){
        checkAuth(ACTION_NAME);
        $notice_id = I('get.notice_id'); // I方法获取数据
        $db_notice = M('oa_notice');
        $db_viewrecord = M('oa_notice_log');
        if ($notice_id) {
           $notice_info = getNoticeInfo($notice_id, 'all');
           $noticeattach = $notice_info['notice_attach'];
           if (! empty($noticeattach)) {
               $notice_attach = strToArr($notice_info['notice_attach'], ',');
               $notice_info['notice_attach'] = arrToStr($notice_attach, '`');
           }
           $notice_info['communist_name'] = strToArr( $notice_info['notice_communist'], ',');
           $notice_info['communist_no'] =$notice_info['notice_communist'];
           $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
           foreach ($notice_info['communist_name'] as  $k=>&$communist){
               $notice_info['communist_name'][$k]= $communist_name_arr[$communist];
           }
           $notice_info['communist_name'] = arrToStr($notice_info['communist_name'], ',');
         	$this->assign('notice_info', $notice_info);
        }
        $this->assign('notice_info', $notice_info);
        $this->display("Oanotice/oa_notice_edit");
    }
    /**
     * 保存公告信息
     *
     * @name oa_notice_save
     * @param
     * @return
     * @author 王宗彬
     *         @updatetime 2017年10月18日
     * @version 0.01
     */
    public function oa_notice_save(){
        $db_notice = M('oa_notice');
        $post = $_POST;
        $party_nos = "";
        $post['dept_no'] = implode(',', $post['dept_no']);
        $post['update_time'] = date("Y-m-d H:i:s");
        $post['notice_communist'] = $post['missive_receiver_no'];
        if(!empty($post['notice_id'])){
            $notice_map['notice_id'] = $post['notice_id'];
        	  $notice_data = $db_notice->where($notice_map)->save($post);
        }else{
          // $post['add_staff'] = $this->staff_no;
        	$post['add_staff'] = peopleNo(session('staff_no'),2);
			$post['add_time'] = date("Y-m-d H:i:s");
        	$post['status'] = 1;
        	$notice_data = $db_notice->add($post);
        	$alert_url = "Oa/Oanotice/oa_notice_info/notice_id/".$notice_data;
        	$alert_title = $post['notice_title'];
        	saveAlertMsg('21', $post['notice_communist'],$alert_url, $alert_title, '', '', '', $this->staff_no);
        }
        if ($notice_data) {
        	saveLog(ACTION_NAME, 1, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '新增一条公告数据，编号为[' . $add_not . ']');
        	showMsg('success', '操作成功！！！', U('oa_notice_index'),1);
        } else {
        	showMsg('error', '操作失败！！！', '');
        }
    }
    /**
     * 删除公告信息
     *
     * @name oa_notice_del();
     * @param
     * @return
     * @author 王宗彬
     *         @updatetime 2017年10月18日
     *         @updatetime 2017年11月27日  更改假删除status=0的数据
     * @version 0.01
     */
	public function oa_notice_del(){
   	 	checkAuth(ACTION_NAME);
      $db_notice = M('oa_notice');
    	$db_viewrecord = M('oa_notice_log');
      $notice_id = I('get.notice_id'); // I方法获取数据
      if (!empty($notice_id)) { // 必要的非空判断需要增加
  		  $notice['status'] = 0;
        $notice_map['notice_id'] = $notice_id;
        $notice_data = $db_notice->where($notice_map)->save($notice);
        saveLog(ACTION_NAME, 3, '', '操作员[' . $this->staff_no . ']于' . date("Y-m-d H:i:s") . '对公告编号 [' . $notice_id . ']进行删除操作');
        showMsg('success', '操作成功！！！', U('Oanotice/oa_notice_index'));
      }else {
            showMsg('error', '操作失败！！！', '');
		  }
    }

}
