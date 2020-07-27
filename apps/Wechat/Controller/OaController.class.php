<?php
namespace Wechat\Controller;
use Think\Controller;
use Wxjssdk\JSSDK;
use SebastianBergmann\Comparator\DateTimeComparator;
class OaController extends Controller 
{
	/*****************************通知公告*******************************/
	/**
	 * @name:oa_notic_index
	 * @desc：通知公告
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_notice_index()
	{
		checkLoginWeixin();
		//checkAuthweixin(ACTION_NAME);
		$communist_no = session('wechat_communist');
		$status = I("get.status",1);
		$this->assign('status',$status);
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
		if ($notice_data) {
            foreach ($notice_data as &$notice) {
				$notice['add_staff'] = peopleNoName($notice['add_staff']);
                $notice['add_time'] = getFormatDate($notice['add_time'], "Y-m-d");
                $notice['update_time'] = getFormatDate($notice['update_time'], "Y-m-d");
                $notice['is_read'] = $status;
				$notice['notice_content'] = mb_substr(strip_tags($notice['notice_content']),0,60,'utf-8');
			}
			$this->assign('notice_data',$notice_data);
		} 
		$this->display("Oa/oa_notice_index");
	}
	
	/**
	 * @name:oa_notice_edit
	 * @desc：通知公告添加页
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_notice_edit()
	{
		//checkAuthweixin(ACTION_NAME);
		$this->display("Oa/oa_notice_edit");
	}
	
	/**
	 * @name:oa_notice_do_save
	 * @desc：通知公告执行
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_notice_do_save()
	{
		$data = I('post.');
		if(!empty($_FILES['file']['name'])){
			$upload = upFiles($_FILES["file"], 'oa', '1', session('wechat_communist'));
		}
		$data['notice_attach'] = $upload['upload_id'];
		$db_notice = new \Oa\Model\OaNoticeModel();
				//$data['add_staff'] = session('wechat_communist');
		$result = $db_notice->Post($data);
		if ($result) {
			$communist = explode(',', $data['notice_communist']);
			foreach ($communist as $value) {
				$alert_url = "Oa/Oanotice/oa_notice_info/notice_id/" . $result;
				$alert_title = $data['notice_title'];
				saveAlertMsg('21', $value, $alert_url, $alert_title, null, null, null, $value);
			}
			showMsg('success','成功',U('Oa/oa_notice_index'));
		} else {
			showMsg('error','上传失败',U('Oa/oa_notice_edit'));
		}
	}
	/**
	 * @name:oa_notic_info
	 * @desc：通知公告详情
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_notice_info()
	{
		//checkAuthweixin(ACTION_NAME);
		$oa_notice_log = M('oa_notice_log');
		$communist_no = session('wechat_communist');
		$notice_id = I('get.notice_id');
		$notice_info = getNoticeInfo($notice_id,'all');
// 		$notice_info['notice_attach'] = getUploadInfo($notice_info['notice_attach']);
		if ($notice_info) {
			//查询当前公告已读人员列表
			$notice_log_list = $oa_notice_log->where("notice_id = $notice_id")->field('log_id,notice_id,communist_no,is_read')->select();
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			foreach ($notice_log_list as &$list) {
			    $list['communist_name'] = $communist_name_arr[$list['communist_no']];
			}
			//发布人
			$notice_info['add_staff'] = getStaffInfo($notice_info['add_staff']);
			$is_read = $oa_notice_log->where("communist_no = '$communist_no' and notice_id = '$notice_id'")->getField('is_read');
			if (empty($is_read)) {
				$notict['notice_id'] = $notice_id;
				$notict['communist_no'] = $communist_no;
				//$notict['add_staff'] = session(wechat_communist);
				$notict['add_time'] = date("Y-m-d H:i:s");
				$notict['update_time'] = date("Y-m-d H:i:s");
				if(!empty($notict['communist_no'])){
					$oa_notice_log->add($notict);
				}
			}
		}
		$this->assign('notice_log_list',$notice_log_list);
		$this->assign('notice_info',$notice_info);
		$this->display("Oa/oa_notice_info");
	}
	

	/**
	* @name:download
	* @desc：下载
	* @author：刘丙涛
	* @addtime:2017-06-20
	* @version：V1.0.0
	**/
	public function download()
	{
		$db_upload = M('bd_upload');
		$upload_id = I('get.upload_id');
		$notice_id = I('get.notice_id');
		$upload_id_arr = explode ( '`', $upload_id );
		$upload_map['upload_id'] = array('in',$upload_id_arr);
		$upload_list = $db_upload->where($upload_map)->field('upload_path,upload_source')->select();
		$notice_title = getNoticeInfo($notice_id,'notice_title');
		$this->assign('upload_list',$upload_list);
		$this->assign('notice_title',$notice_title);
		$this->display('Oa/download');
	}
	
	/*****************************通知公告结束*******************************/
	/*****************************三会一课*******************************/	
	
	/**
	 * @oa_meeting_list
	 * @desc：会议列表
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_meeting_list()
	{
		checkLoginWeixin();
		//checkAuthweixin(ACTION_NAME);
		$ad_list = getbannerList(7);
		$this->assign('ad_list',$ad_list);
		$status = '11,21';
		$meeting_list = getCommunistMeetingList(session('wechat_communist'),'',$status,'0','3');
		//var_dump($meeting_list);die;
		$this->assign('meeting_list',$meeting_list);
		$meeting_type = getBdTypeList('meeting_type');
		foreach ($meeting_type as &$list) {
			$list['lists'] = getCommunistMeetingList(session('wechat_communist'), $list['type_no'], $status);
			if(empty($list['lists'])){
				$list['is_exist'] = '1';
			}
		}
		$this->assign('meeting_type',$meeting_type);
		$this->display("Oa/oa_meeting_list");
	}
	/**
	 * @oa_meeting_history
	 * @desc：历史会议列表
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_meeting_history()
	{
		$status = '23';
		// $type = I('get.meeting_type',2001);
		// $this->assign('type1',$type);
		$meeting_type = getBdTypeList('meeting_type');
		$this->assign('meeting_type',$meeting_type);
		// $meeting_list = getCommunistMeetingList(session('wechat_communist'),$type, $status);
		// $this->assign('meeting_list',$meeting_list);
		$this->display("Oa/oa_meeting_history");
	}
	/**
	 * @oa_meeting_history
	 *ajax
	 **/
	public function oa_meeting_history_data()
	{
		$status = '23';
		$meeting_type = I('post.meeting_type',2001);
		$meeting_list = getCommunistMeetingList(session('wechat_communist'),$meeting_type, $status);
		ob_clean();$this->ajaxReturn($meeting_list);
	}
	 /**
	 * @oa_meeting_signIn
	 * @desc：会议签到页
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_meeting_signIn()
	{
		$meeting_no = I('get.meeting_no');
		$wechat_communist = session('wechat_communist');
		$meeting_info = getMeetingweixinInfo($meeting_no, $wechat_communist);
		$map['material_id'] = $meeting_info['meeting_no'];
		/*$map['notes_title'] = "参加会议".$meeting_info['meeting_name'];
		$map['add_communist'] = $wechat_communist;
		$map['notes_type'] = 2;*/
		$meeting_notes = M("edu_notes")->where($map)->select();
		if(empty($meeting_notes)){
			$meeting_notes = '';
		} else {
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			foreach ($meeting_notes as &$notes) {
			    $notes['add_communist_name'] = $communist_name_arr[$notes['add_communist']];
			}
		}
		$this->assign('meeting_notes',$meeting_notes);
		$meeting_info['meeting_type'] = getBdTypeInfo($meeting_info['meeting_type'], 'meeting_type');
		if($meeting_info['checked'] == 'no' || empty($meeting_info['checked'])){
			$meeting_info['checked'] = "0";
		}
		if($meeting_info['meeting_video']){
			$meeting_video = explode(',', $meeting_info['meeting_video']);
			$video = array();
			foreach ($meeting_video as &$list) {
				$video[] = __ROOT__."/uploads/video/".$list;
			}
			$meeting_info['meeting_video'] = $video;
		}else{
			$meeting_info['meeting_video'] = array();
		}
		$meeting_info['meetting_thumb'] = getUploadInfo($meeting_info['meetting_thumb']);
 		$this->assign('notes_thumb',$notes_thumb);
		$this->assign('meeting_info',$meeting_info);
		//var_dump($meeting_info);die;
		$this->display("Oa/oa_meeting_signIn");
	}
	/**
	 *  set_oa_meeting_sign
	 * @desc 会议签到
	 * @user 王宗彬
	 * @date 2018/2/1
	 * @version 1.0.0
	 */
	public function set_oa_meeting_sign()
	{
		$meeting_no = I('get.meeting_no');
		$communist_no = session('wechat_communist');
		$meeting_info = getMeetingInfo($meeting_no, 'all', '1');
		if (!$meeting_info) : showMsg('success', '签到失败！', U('Oa/oa_meeting_signIn',array('meeting_no'=>$meeting_no))); endif;
		if ($meeting_info['status'] != 21) : showMsg('success', '会议未开始', U('Oa/oa_meeting_signIn',array('meeting_no'=>$meeting_no))); endif;
		$time = date("Y-m-d H:i:s");
		if ($time >= $meeting_info['meeting_real_start_time']) {
			$db_log = M('oa_att_log');
			$log_list['att_no'] = $communist_no;
			$log_list['att_address'] = '';
			$log_list['att_date'] = date('Y-m-d');
			$log_list['att_time'] = date('H:i:s');
			$log_list['check_time'] = date('Y-m-d H:i:s');
			$log_list['att_manner'] = '12';
			$log_list['att_relation_no'] = $meeting_no;
				//$log_list['add_staff'] = $communist_no;
			$log_list['status'] = '1';
			$log_list['add_time'] = date('Y-m-d H:i:s');
			$result = $db_log->add($log_list);
			if ($result) {
				showMsg('success', '签到成功！', U('Oa/oa_meeting_signIn',array('meeting_no'=>$meeting_no)));
			} else {
				showMsg('error', '签到失败！', U('Oa/oa_meeting_signIn',array('meeting_no'=>$meeting_no)));
			}
		} else {
			showMsg('error', '未在会议时间内签到！', U('Oa/oa_meeting_signIn',array('meeting_no'=>$meeting_no)));
		}
	}
	/**
	 *  oa_meeting_type_list
	 * @desc 会议
	 * @user 王宗彬
	 * @date 2018/2/1
	 * @version 1.0.0
	 */
	public function oa_meeting_type_list()
	{
		
	}
	
	
	/**
	 * @edu_notes_do_save
	 * @desc：会议笔记
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function edu_notes_do_save()
	{
		$db_notes = M("edu_notes");
		$data = I('post.');
		if(!empty($_FILES['file']['name'])){
			$upload = upFiles($_FILES["file"], 'oa', '1', session('wechat_communist'));
			$data['notes_thumb'] = $upload['upload_id'];
		}
		$meeting_no = $data['material_id'];
		$title = M('oa_meeting')->where("meeting_no = $meeting_no")->getField('meeting_name');
				//$data['add_staff'] = session('wechat_communist');
		$data['notes_type'] = 2;
		$data['notes_title'] = "参加会议".$title;
		$data['update_time'] =  date('Y-m-d H:i:s');
		if(!empty($data['notes_id'])){
			$notes_id = $data['notes_id'];
			$article_data = $db_notes->where("notes_id = '$notes_id'")->save($data);
		}else {
			$data['add_time'] = date('Y-m-d H:i:s');
			$data['add_communist'] = session('wechat_communist');
			$article_data = $db_notes->add($data);
		}
		if ($article_data) {
			showMsg('success', '操作成功！', U('Oa/oa_meeting_list'));
		} else {
			showMsg('error', '操作失败！','');
		}
	}
	/**
	 * @oa_meeting_minutes_index
	 * @desc：会议记录
	 * @author：王宗彬
	 * @addtime:2018-06-04
	 * @version：V1.0.0
	 **/
	public function oa_meeting_minutes_index()
	{
	    $minutes_list = getMeetingminutesList('',1);
	    $party_name_arr = M('ccp_party')->getField('party_no,party_name');
	    $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
	    foreach ($minutes_list['data'] as &$list) {
	        $list['party_name'] = $party_name_arr[$list['party_name']];
	        $list['communist_name'] = getStaffInfo($list['add_staff']);
	        
	        $list['meeting_minutes_thumb'] = getUploadInfo($list['meeting_minutes_thumb']);
	    }
	    $this->assign('minutes_list',$minutes_list['data']);
	    $this->display("Oa/oa_meeting_minutes_list");
	}
	/**
	 * @oa_meeting_minutes_info
	 * @desc：会议记录
	 * @author：王宗彬
	 * @addtime:2018-06-04
	 * @version：V1.0.0
	 **/
	public function oa_meeting_minutes_info()
	{
	    
	    $meeting_minutes_id = I('get.meeting_minutes_id');
	    $meeting_minutes = M('oa_meeting_minutes')->where("meeting_minutes_id = '$meeting_minutes_id'")->find();
	    $meeting_minutes['add_staff'] = getStaffInfo($meeting_minutes['add_staff']);
	    $this->assign('meeting_minutes',$meeting_minutes);
	    
	    $this->display("Oa/oa_meeting_minutes_info");
	}
	
	
	/*****************************书记信箱*******************************/
	/**
	 * @name:oa_email_index
	 * @desc：书记信箱
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_email_index()
	{
	 	checkLoginWeixin();
		//checkAuthweixin(ACTION_NAME);
		$communist_no = session('wechat_communist');
		$status = I("get.status");
		$this->assign('status',$status);
		$where = "1=1";
		if($status == 1){
			$where .= " and imail_receiver in('$communist_no')";
			$imail_data = $imail_data = M('com_imail_inbox as i')->field("i.inbox_id,i.imail_receiver,i.imail_receiver,i.add_staff,i.add_time,i.is_read,o.imail_sender,o.imail_title,o.imail_content,o.imail_id")->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->order('add_time desc')->where($where)->select();
		}else{
			$db_imail_outbox = M("com_imail_outbox");
			$where .= " and imail_sender in('$communist_no') and add_staff = '$communist_no'";
			$outbox_sql = "select * from sp_com_imail_outbox  where $where order by add_time desc ";
			$imail_data = $db_imail_outbox->query($outbox_sql);
		}
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($imail_data as &$imail) {
		    $imail['add_staff'] = $staff_name_arr[$imail['add_staff']];
			$imail['add_time'] = getFormatDate($imail['add_time'], "Y-m-d");
			$imail['imail_content'] = mb_substr(removeHtml($imail['imail_content']),0,10,'utf-8');
		}
		$this->assign('imail_data',$imail_data);
		$this->display("Oa/oa_email_index");
	}
	
	/**
	 * @name:oa_emaile_edit
	 * @desc：书记信箱添加页
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_email_edit()
	{
		//checkAuthweixin(ACTION_NAME);
		$this->display("Oa/oa_email_edit");
	}
	
	/**
	 * @name:oa_email_do_save
	 * @desc：书记信箱执行
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_email_do_save()
	{
		$post = I('post.');
		$data = setSendImail($post['imail_receivers'],$post['imail_content'],$post['imail_title'],'','',1,session('wechat_communist'));
		if(!empty($data)){
			showMsg('success', '操作成功！', U('Oa/oa_email_index',array('status'=>1)));
		} else {
			showMsg('error', '操作失败！','');
		}
		
		
	}
	/**
	 * @name:oa_email_info
	 * @desc：书记信箱详情
	 * @author：王宗彬
	 * @addtime:2018-04-26
	 * @version：V1.0.0
	 **/
	public function oa_email_info()
	{
		$imail_id = I('get.imail_id');
		$inbox_id = I('get.inbox_id');
		$status = I('get.status');
		$this->assign('status',$status);
		if($status == '1'){
			$imail_data = M('com_imail_inbox as i')->field("i.inbox_id,i.imail_receiver,i.imail_receiver,i.add_staff,i.add_time,i.is_read,o.imail_sender,o.imail_title,o.imail_content,o.imail_id")->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->order('add_time desc')->where("inbox_id = '$inbox_id'")->find();
		}else{
			$imail_data = M("com_imail_outbox")->where("imail_id = '$imail_id'")->find();
		}
		$this->assign('imail_data',$imail_data);
		
		$this->display("Oa/oa_email_info");
	}
	/*****************************党员投稿*******************************/
	/**
	 * @name:oa_article_list
	 * @desc：党员投稿
	 * @author：刘长军
	 * @addtime:2019-02-26
	 * @version：V1.0.0
	 **/
	public function oa_article_list()
	{
		checkLoginWeixin();
		$where['article_cat'] = 15;
		$where['status'] = 1;
		$article_data = M('cms_article')->where($where)->order('add_time desc')->select();
		foreach($article_data as &$data){
			$logo = getUploadInfo($data['article_thumb']);
            $data['article_thumb'] = $logo;
		}
		$this->assign('article_data',$article_data);
		$this->display("Oa/oa_article_list");

	}
	/**
	 * @name:oa_article_list
	 * @desc：党员投稿
	 * @author：刘长军
	 * @addtime:2019-02-26
	 * @version：V1.0.0
	 **/
	public function oa_article_info(){
		$article_id = I('get.article_id');
		if(!empty($article_id)){
			$article_info = getArticleInfo($article_id,'all');
		}
		$article_info['add_staff'] = getStaffInfo($article_info['add_staff']);
		$this->assign('article_info',$article_info);
		$this->display("Oa/oa_article_info");
	}
	/**
	 * @name:oa_article_edit
	 * @desc：党员投稿
	 * @author：刘长军
	 * @addtime:2019-02-26
	 * @version：V1.0.0
	 **/
	public function oa_article_edit(){
		$this->display("Oa/oa_article_edit");
	}
	/**
	 * @name:oa_article_save
	 * @desc：党员投稿
	 * @author：刘长军
	 * @addtime:2019-02-26
	 * @version：V1.0.0
	 **/
	public function oa_article_save(){
		$post = I('post.');
		if(!empty($_FILES['file']['name'])){
			$upload = upFiles($_FILES["file"], 'oa', '1', session('wechat_communist'));
		}
		$db_article = M("cms_article");
		$post['add_time'] = date('Y-m-d H:i:s');
		$post['update_time'] = date('Y-m-d H:i:s');
		$post['status'] = 21;
		$post['article_thumb'] = $upload['upload_id'];
		$post['article_cat'] = 15;
		$post['add_staff'] = peopleNo(session('wechat_communist'),1);
		$article_data = $db_article->add($post);
		if($article_data){
			showMsg('success', '操作成功！', U('Oa/oa_article_list'));
		} else {
			showMsg('error', '操作失败！','');
		}
	}
	/**************************消息提醒***************************/
	/**
     *  alert_msg_index
     * @desc 消息提醒
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function alert_msg_index()
    {
        checkLoginWeixin();
        $communist_no = session('wechat_communist');
        $status = I("get.status",1);
        $this->assign('status',$status);
        $type_array = M('bd_type')->where("type_group = 'alert_type'")->getField('type_no,type_name');
        if($status == '1'){
            $map['status'] = array('eq','0');
        }else{
            $map['status'] = array('eq','1');
        }
        if($communist_no){
            $map['_string'] = "FIND_IN_SET('$communist_no',alert_man)";
        }
        $alert_list=M('bd_alertmsg')->field("alert_id,alert_man,alert_title,alert_type,alert_content,add_time")->where($map)->order('add_time DESC')->select();
        foreach ($alert_list as &$msg){
            $msg['alert_title']="【".$type_array[$msg['alert_type']]."】".$msg['alert_title'];
            $msg['alert_man']=getCommunistInfo($msg['alert_man']);
        }
        $this->assign('alert_list',$alert_list);
        $this->display('alert_msg_index');
    }
    /**
     *  alert_msg_info
     * @desc 消息提醒
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function alert_msg_info()
    {
        $alert_id = I("get.alert_id");
        $map['alert_id'] = $alert_id;
        $alert_info=M('bd_alertmsg')->field("alert_id,alert_man,alert_title,alert_content,add_time")->where($map)->find();
        $data['status'] = 1;
        M('bd_alertmsg')->where($map)->save($data);
        $alert_info['alert_man']=getCommunistInfo($alert_info['alert_man']);
        $this->assign('alert_info',$alert_info);
        $this->display('alert_msg_info');
    }
}