<?php
namespace Wechat\Controller;
use Think\Controller;
use Wxjssdk\JSSDK;
use SebastianBergmann\Comparator\DateTimeComparator;
class LifeController extends Controller 
{
	/**
	 * @name:life_index
	 * @desc： 服务
	 * @author：王宗彬
	 * @addtime:2019-06-06
	 * @version：V1.0.0
	 **/
	public function life_index()
	{
		$article_list = getArticleList('14','0','5','','','','1');
		foreach ($article_list['data'] as &$list) {
			$list['article_title'] = mb_substr(strip_tags($list['article_title']),0,7,'utf-8');
			$list['article_content'] = mb_substr(strip_tags($list['article_content']),0,12,'utf-8');
		}
        $this->assign('article_list',$article_list['data']);
		$this->display('Life/life_index');
	}


	/**
	 * @name:life_volunteer_index
	 * @desc： 志愿者服务
	 * @author：王宗彬
	 * @addtime:2018-05-08
	 * @version：V1.0.0
	 **/
	public function life_volunteer_index()
	{
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
		$db_volunteer_activity = M("life_volunteer_activity");
		$party_no = getCommunistInfo($communist_no, "party_no");
		$time = date('Y-m-d');
		$activity_data = $db_volunteer_activity->where("FIND_IN_SET($party_no,party_no) and activity_endtime <= '$time'")->select();
		$volunteer = M('life_volunteer')->where("communist_no = '$communist_no' and status='2' ")->find();
		if(!empty($volunteer)){
			$volunteer = 1;
		}else {
			$volunteer = 0;
		}
		$this->assign('volunteer',$volunteer);
		if ($activity_data) {
			foreach ($activity_data as &$activity) {
				$activity['activity_thumb'] = getUploadInfo($activity['activity_thumb']);
				$activity['activity_description'] = mb_substr($activity['activity_description'],0,50,'utf-8');
			}
		}
		$this->assign('activity_data',$activity_data);
		$this->display('Life/life_volunteer_index');
	}
	/**
	 * @name:life_volunteer_index_my
	 * @desc： 志愿者服务
	 * @author：王宗彬
	 * @addtime:2018-05-08
	 * @version：V1.0.0
	 **/
	public function life_volunteer_index_my()
	{ 
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
        $db_activity = M("life_volunteer_activity");
        $db_activity_communist = M("life_volunteer_activity_apply");

        $activity_apply_list = $db_activity_communist->where("communist_no= $communist_no and status = 2")->getField('activity_id', true);
        if (!empty($activity_apply_list)){
        	$activity_ids = implode(',', $activity_apply_list);//获取我参加的活动id
        	//查询活动列表
        	$activity_list = $db_activity->where("activity_id in ($activity_ids)")->select();
        }
        if ($activity_list) {
            foreach ($activity_list as &$list) {
                $list['activity_thumb'] = getUploadInfo($list['activity_thumb']);
                $list['activity_description'] = mb_substr($list['activity_description'],0,50,'utf-8');
            }
        }
        $this->assign('activity_list',$activity_list);
		$this->display('Life/life_volunteer_index_my');
	}
	/**
	 * @name:life_volunteer_apply
	 * @desc: 申请志愿者/活动
	 * @author:王宗彬
	 * @addtime:2018-05-08
	 * @version:V1.0.0
	 **/
	public function life_volunteer_apply()
	{
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
		$type = I('get.type');
		$this->assign('type',$type);
		if($type == 1){
			$volunteer = M('life_volunteer')->where("communist_no = '$communist_no'")->find();
			$this->assign('volunteer',$volunteer);
		}else{
			$activity_id = I('get.activity_id');
			$this->assign('activity_id',$activity_id);
			$volunteer_apply = M('life_volunteer_activity_apply')->where("communist_no = '$communist_no' and activity_id = '$activity_id'")->find();
			$this->assign('volunteer_apply',$volunteer_apply);
		}
		$communist_info = getCommunistInfo($communist_no,'communist_no,communist_name,party_no,communist_avatar,communist_birthday');
		if(empty($communist_info['communist_avatar'])){
			$communist_info['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
		}
		$communist_info['party_no'] = getPartyInfo($communist_info['party_no']);
		$this->assign('communist_info',$communist_info);
		$this->display('Life/life_volunteer_apply');
	}
	/**
	 * @name:life_volunteer_integral
	 * @desc: 志愿者积分
	 * @author:王宗彬
	 * @addtime:2018-05-08
	 * @version:V1.0.0
	 **/
	public function life_volunteer_integral()
	{
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
		$communist_info = getCommunistInfo($communist_no,'communist_no,communist_name,party_no,communist_avatar,communist_birthday');
		$communist_info['party_no'] = getPartyInfo($communist_info['party_no']);
		$this->assign('communist_info',$communist_info);
		
		$db_integral = new \Ccp\Model\CcpIntegralLogModel();
		$integral_info['change_integral'] = M('ccp_integral_log')->where("log_relation_no='$communist_no' and log_relation_type='1' and change_type='7' and memo='参加志愿者活动' ")->sum('change_integral');
		if ($integral_info) {
			$integral_list = M('ccp_integral_log')->where("log_relation_no='$communist_no' and log_relation_type='1' and memo='参加志愿者活动' ")->select();
			$integral_info['log_list'] = $integral_list;
		}
		if(empty($integral_info['change_integral'])){
			$integral_info['change_integral'] = '0';
		}
		$this->assign('integral_info',$integral_info);
		$this->display('Life/life_volunteer_integral');
	}
	/**
	 * @life_volunteer_info
	 * @desc: 志愿者积分
	 * @author:王宗彬
	 * @addtime:2018-05-08
	 * @version:V1.0.0
	 **/
	public function life_volunteer_info()
	{
		$activity_id = I('get.activity_id');
		$type = I('get.type');
		$this->assign('type',$type);
		$activity_info = M('life_volunteer_activity')->where("activity_id = '$activity_id'")->find();
		$this->assign('activity_info',$activity_info);
		$this->display('Life/life_volunteer_info');
	}
	/**
	 * @life_volunteer_do_save
	 * @desc: 志愿者申请/志愿者活动申请
	 * @author:王宗彬
	 * @addtime:2018-05-08
	 * @version:V1.0.0
	 **/
	public function life_volunteer_do_save()
	{
		$post = I('post.');
		$type = $post['type'];
		$post['party_no'] = getCommunistInfo($post['communist_no'],'party_no');
		$post['status']	= '1';
		$post['update_time'] = date('Y-m-d H:i:s');
		$post['add_time'] = date('Y-m-d H:i:s');
					//$post['add_staff'] = session('wechat_communist');
		if($type == 1){
			if($post['volunteer_id']){
				$volunteer_id = $post['volunteer_id'];
				$data = M('life_volunteer')->where("volunteer_id = '$volunteer_id'")->save($post);
			}else{
				$data = M('life_volunteer')->add($post);
			}
		}else{
			if($post['apply_id']){
				$apply_id = $post['apply_id'];
				$data = M('life_volunteer_activity_apply')->where("apply_id = '$apply_id'")->save($post);
			}else{
				$data = M('life_volunteer_activity_apply')->add($post);
			}
		}
		if(!empty($data)){
			showMsg('success','成功',U('Life/life_volunteer_index'));
		} else {
			showMsg('error','上传失败',U('Life/life_volunteer_index'));
		}
	}
	/*********************************调查文卷**********************************/
	/**
	 * @life_survey_list
	 * @desc: 调查问卷
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_survey_list()
	{
		
		checkLoginWeixin();
		$type = I('get.type');
		$this->assign('type',$type);
		if($type == '1'){
			$status = '1';
		}else{
			$status = '0 ';
		}
		$communist_no = session('wechat_communist');
		$party_no = getCommunistInfo($communist_no, 'party_no');
		$where = $status == 0 ? " and communist_no = '$communist_no'" : " and communist_no is null";
		$survey_list = M('life_survey')->join('left join sp_life_survey_log as l on sp_life_survey.survey_id=l.survey_id')
		->where("sp_life_survey.status=1 and find_in_set($party_no,sp_life_survey.party_no) $where")
		->field('sp_life_survey.survey_id,sp_life_survey.survey_title')->order('sp_life_survey.add_time desc')->select();
		$this->assign('survey_list',$survey_list);
		$this->display('Life/life_survey_list');
		
	}
	/**
	 * @life_survey_info
	 * @desc: 调查问卷
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_survey_info()
	{
	
		//checkLoginWeixin();


		$status = I('get.status');
	  	$this->assign('status',$status);
        $survey_id = I('get.survey_id');
        $survey_info = getSurveyInfo($survey_id, 1);
        //var_dump($survey_info['questions_list'][0]['item']);die;

        if ($survey_info) {
            foreach ($survey_info['questions_list'] as &$list) {
            	$list['type'] = $list['questions_type'];
                $list['questions_type'] = $list['questions_type'] == '1' ? '单选' : '多选';
                $list['questions_item'] =  explode(',',$list['questions_item'] );
            }
        } 
        $this->assign('survey_info',$survey_info);
        
        $this->display('Life/life_survey_info');
	}
	
	/**
	 * @life_survey_do_save
	 * @desc: 调查问卷投票
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/  
	public function life_survey_do_save()
	{
		$post = I('post.');
		if(empty($_POST['answer_list'])){
			showMsg('error','请选择答案');
		}
		$answer_list = $_POST['answer_list'];
		$survey_list['survey_id'] = $post['survey_id'];
		$survey_list['communist_no'] = session('wechat_communist');
		$survey_list['add_staff'] = session('wechat_communist');
		$survey_list['log_date'] = date('Y-m-d');
		$survey_list['status'] = '1';
		$survey_list['add_time'] = date('Y-m-d H:i:s');
		$result = M('life_survey_log')->add($survey_list);
		$arr = array();
        foreach ($answer_list as $questions_id => $item) {
            $list['communist_no'] = session('wechat_communist');
            $list['survey_id'] = $post['survey_id'];
            $list['questions_id'] = $questions_id;
            $list['answer_item'] = $item;
            $list['add_staff'] = session('wechat_communist');
            $list['status'] = '1';
            $list['add_time'] = date('Y-m-d H:i:s');
            $arr[] = $list;
        }
		$flag = M('life_survey_answer')->addAll($arr);
		if ($result && $flag) {
			showMsg('success','成功',U('Life/life_survey_list',array('type'=>1)));
		} else {
			showMsg('error','上传失败',U('Life/life_survey_list',array('type'=>1)));
		}
	}
	/*********************************投票管理**********************************/
	/**
	 * @life_vote_list
	 * @desc: 投票管理
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_vote_list()
	{
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
		$type = I('get.type'); //1正开始  2结束（投票/时间结束）
		$this->assign('type',$type);
		if($type == 1){  //投票
			$map['subject_starttime'] = array('lt',date('y-m-d H:i:s'));
			$map['subject_endtime'] = array('gt',date('y-m-d H:i:s'));
			$volist_list = M('life_vote_subject as s')->where($map )->select();
			foreach ($volist_list as &$list) {
				$list['option_name'] = M("life_vote_option")->where("subject_id =".$list['subject_id'])->select();
			}
		}else{
// 			$result = M('life_vote_result')->where("communist_no = '$communist_no'")->field('subject_id')->select();
			$map['subject_endtime'] = array('lt',date('y-m-d H:i:s'));
			$volist_list = M('life_vote_subject as s')->where($map)->select();
		}
		$this->assign('volist_list',$volist_list);
		$this->display('Life/life_vote_list');
	
	}
	/**
	 * @life_vote_info
	 * @desc: 投票管理
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_vote_info()
	{

		$life_vote_subject = M('life_vote_subject');
		$life_vote_option = M('life_vote_option');
		$life_vote_result = M('life_vote_result');
		$subject_id = I('get.subject_id');


		$map['communist_no'] = session('wechat_communist');
		$map['subject_id'] = $subject_id;
		$res = $life_vote_result->where($map)->find();
		if(empty($res)){
			$type = I('get.type');
		}else{
			$type = 2;
		}
		$this->assign('type',$type);
		$subject_list = $life_vote_subject->where("subject_id='$subject_id'")->field('subject_id,vote_id,subject_content,is_multiple')->order('add_time desc')->select();
		if(!empty($subject_list)){
			$subject_list[0]['vote_name'] = $vote_info['vote_theme'];
			foreach ($subject_list as &$subject) {
				$option_list = $life_vote_option->where("subject_id=" . $subject['subject_id'])->select();

				foreach ($option_list as &$option) {
					$option['result'] = $life_vote_result->where("option_id=" . $option['option_id'])->count('option_id');
				}
				$subject['option_list'] = $option_list;
			}
			$this->assign("subject_list", $subject_list);
		}
		$this->display('Life/life_vote_info');
	}
	/**
	 * @life_vote_do_save
	 * @desc: 投票
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_vote_do_save()
	{
		$post = I('post.');
		$post['communist_no'] = session('wechat_communist');
		$post['add_time'] = date("Y-m-d H:i:s");
		$post['update_time'] = date("Y-m-d H:i:s");
		$result = M('life_vote_result')->add($post);
		if(!empty($result)){
			showMsg('success','成功',U('Life/life_vote_list',array('type'=>1)));
		} else {
			showMsg('error','上传失败',U('Life/life_vote_list',array('type'=>1)));
		}
	}
	/******************************留言建议**********************************/
	/**
	 * @life_guestbook_index
	 * @desc: 留言建议
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_guestbook_index()
	{
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
		$map['communist_name'] = $communist_no;
		$guestbook_list = M('life_guestbook')->where($map)->order('add_time desc')->select();
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($guestbook_list as &$guestbook) {
			$guestbook['communist_name'] = $communist_name_arr[$guestbook['communist_name']];
			$guestbook['add_time'] = getFormatDate($guestbook['add_time'],'Y-m-d H:m');
		}
		$this->assign('guestbook_list',$guestbook_list);
		$this->display('Life/life_guestbook_index');
	}
	/**
	 * @life_guestbook_edit
	 * @desc: 留言建议
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_guestbook_edit()
	{
		checkLoginWeixin();
		$this->display('Life/life_guestbook_edit');
	}
	/**
	 * @life_guestbook_do_save
	 * @desc: 留言建议
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_guestbook_do_save()
	{
		$post = I('post.');
		$post['add_time'] = date("Y-m-d H:i:s");
		$post['update_time'] = date("Y-m-d H:i:s");
		$post['communist_name'] = session('wechat_communist');
		$post['communist_phone'] = getCommunistInfo(session('wechat_communist'),'communist_mobile');
		$post['party_no'] = getCommunistInfo(session('wechat_communist'),'party_no');
		$result = M('life_guestbook')->add($post);
		if(!empty($result)){
			showMsg('success','成功',U('Life/life_guestbook_index'));
		} else {
			showMsg('error','上传失败',U('Life/life_guestbook_index'));
		}
	}
	/**
	 * @life_guestbook_info
	 * @desc: 留言建议
	 * @author:王宗彬
	 * @addtime:2018-05-15
	 * @version:V1.0.0
	 **/
	public function life_guestbook_info()
	{
		$guestbook_id = I('get.guestbook_id');
		$guestbook_info = M('life_guestbook')->where("guestbook_id = '$guestbook_id'")->find();
		$guestbook_info['communist_name'] = getCommunistInfo($guestbook_info['communist_name']); 

		$this->assign('guestbook_info',$guestbook_info);
		
		$this->display('Life/life_guestbook_info');
		
	}
	/***************************************三务公开**************************************/
	/**
	 * @life_affairs_index
	 * @desc: 三务公开
	 * @author:王宗彬
	 * @addtime:2018-06-04
	 * @version:V1.0.0
	 **/
	public function life_affairs_index()
	{
	    checkLoginWeixin();
	    $ad_list = getbannerList(10);
	    $this->assign('ad_list',$ad_list);
	    $party_no = I('get.party_no');
	    if(empty($party_no)){
	        $communist_no = session('wechat_communist');
	        $party_no = getCommunistInfo($communist_no,'party_no');
	    }
	    $party_list['party_no'] = $party_no;
	    $party_list['party_name'] = getPartyInfo($party_no,'party_name');
	    $party_list['memo'] = getPartyInfo($party_no,'memo');
	    $this->assign('party_list',$party_list);
	    $this->display('Life/life_affairs_index');
	}
	/**
	 * @life_affairs_list
	 * @desc: 三务公开
	 * @author:王宗彬
	 * @addtime:2018-06-04
	 * @version:V1.0.0
	 **/
	public function life_affairs_list()
	{
		$party_no = I('get.party_no');
	    $cat_id = I('get.cat_id');
	    $affairs_info = M('cms_affairs')->where("party_no = '$party_no' and article_cat = '$cat_id'")->order('add_time desc')->select();
	    foreach ($affairs_info as &$affairs) {
	    	$affairs['add_staff'] = getStaffInfo($affairs['add_staff']);
	    	$affairs['article_thumb'] = getUploadInfo($affairs['article_thumb']);
	    }
	    $this->assign('affairs_info',$affairs_info);
	    $this->display('Life/life_affairs_list');
	}
	/**
	 * @life_affairs_party
	 * @desc：党组织列表
	 * @author：王宗彬
	 * @addtime:2018-06-04
	 * @version：V1.0.0
	 **/
	public function life_affairs_party()
	{
	    $party_list = getPartyList('');
	    $this->assign('party_list',$party_list);
	    $this->display('Life/life_affairs_party');
	}
	/**
	 * @life_affairs_info
	 * @desc：党组织列表
	 * @author：王宗彬
	 * @addtime:2018-06-04
	 * @version：V1.0.0
	 **/
	public function life_affairs_info()
	{
	    $article_id = I('get.article_id');
	    $affairs_info = M('cms_affairs')->where("article_id = '$article_id'")->find();
	    $affairs_info['add_staff'] = getStaffInfo($affairs_info['add_staff']);
	    $this->assign('affairs_info',$affairs_info)	;
	    $this->display('Life/life_affairs_info');
	}
	
}