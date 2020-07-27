<?php
/**
 * Created by PhpStorm.
 * User: wangzongbin
 * Date: 2018/4/4
 * Time: 11:06
 */
namespace Allinone\Controller;
use Think\Controller;
class CommunityController extends Controller
{
	/**
	 *  _initialize
	 * @desc 构造函数
	 * @user liubingtao
	 * @date 2018/4/8
	 * @version 1.0.0
	 */
	public function _initialize(){
		$door_communist_no = session('door_communist_no');
		if (!empty($door_communist_no)) {
			$this->assign('is_login', 1);
		} else {
			$this->assign('is_login', 0);
		}
	}
   
	/*******************************志愿者*****************************************/
	/**
	 * @name:ipam_volunteer_list
	 * @desc：志愿者列表
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-05-14
	 * @updatetime:2018-05-14
	 * @version：V1.0.0
	 **/
	public function ipam_volunteer_list()
	{
		$volunteer_intro = getConfig('volunteer_intro');
		$this->assign('volunteer_intro',$volunteer_intro);
		$db_volunteer = M('life_volunteer');
		$volunteer_communist = $db_volunteer->where('status = 2')->field('communist_no')->order('add_time desc')->select();
		$name = M('ccp_communist')->getField('communist_no,communist_name');
		$avatar = M('ccp_communist')->getField('communist_no,communist_avatar');
		foreach ($volunteer_communist as &$communist) {
			$communist['communist_name'] = $name[$communist['communist_no']];
			if($avatar[$communist['communist_no']]){
				$communist['communist_avatar'] = $avatar[$communist['communist_no']];
			}
		}
		$count = $db_volunteer->where('status=2')->count();
		$this->assign('count',$count);
		$this->assign('volunteer_communist',$volunteer_communist);
		$volunteer_activity = M('life_volunteer_activity')->limit(0,3)->order('add_time desc')->select();
		foreach ($volunteer_activity as &$activity) {
			$activity['activity_description'] =  mb_substr(removeHtml($activity['activity_description']),0,80,'utf-8');
			$activity['activity_thumb'] = explode (',', $activity['activity_thumb']);
			$activity['activity_thumb'] = getUploadInfo($activity['activity_thumb'][0]);
		}
		$this->assign('volunteer_activity',$volunteer_activity);
		$web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
 		$this->display('ipam_volunteer_list');
	}
	/**
	 * @name:ipam_volunteer_info
	 * @desc：志愿者详情
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-05-14
	 * @updatetime:2018-05-14
	 * @version：V1.0.0
	 **/
	public function ipam_volunteer_info()
	{
		$activity_id = I('get.activity_id');
		$activity_map['activity_id'] = $activity_id;
		$volunteer_info = M('life_volunteer_activity')->where($activity_map)->find();
		$volunteer_info['add_staff_name'] = getStaffInfo($volunteer_info['add_staff']);
		$this->assign('volunteer_info',$volunteer_info);
		$apply_map['activity_id'] = $activity_id;
		$apply_map['status'] = 2;
		$apply_data = M('life_volunteer_activity_apply')->field('communist_no')->where($apply_map)->select();
		$name = M('ccp_communist')->getField('communist_no,communist_name');
		$avatar = M('ccp_communist')->getField('communist_no,communist_avatar');
		foreach ($apply_data as &$list) {
			$list['communist_name'] = $name[$list['communist_no']];
			if($avatar[$list['communist_no']]){
				$list['communist_avatar'] = __ROOT__."/".$avatar[$list['communist_no']];
			}
		}
		$this->assign('apply_data',$apply_data);
		$count = M('life_volunteer_activity_apply')->where($apply_map)->count();
		$this->assign('count',$count);
		$web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
		$this->display('ipam_volunteer_info');
	}
/*******************************通知公告*****************************************/
	/**
	 * @name:ipam_notice_list
	* @desc：公告列表页
    * @param：null
    * @return：
	* @author：王宗彬
	* @addtime:2018-05-14
	* @updatetime:2018-05-14
	* @version：V1.0.0
	**/
	public function ipam_notice_list()
	{
		//获取新闻动态列表
		$notice_list = getNoticeList('','','1','8');
		$name = M('ccp_communist')->getField('communist_no,communist_name');
		foreach($notice_list as &$list){
	    	$list['notice_attach'] = explode ( ',', $list['notice_attach'] );
	    	$list['notice_attach'] = getUploadInfo($list['notice_attach'][0]);
	    	$list['notice_content'] =  mb_substr(removeHtml($list['notice_content']),0,80,'utf-8');
	    	$list['add_staff'] = peopleNoName($list['add_staff']);
		}
		$this->assign('notice_list',$notice_list);
		$web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
		$this->display('ipam_notice_list');
	}  
	/**
	 * @name:ipam_notice_info
	 * @desc：公告列表页
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-05-14
	 * @updatetime:2018-05-14
	 * @version：V1.0.0
	 **/
	public function ipam_notice_info()
	{
		$notice_id = I('get.notice_id');
		$notice_info = getNoticeInfo($notice_id);
		$notice_info['add_staff'] = peopleNoName($notice_info['add_staff']);
		$this->assign('notice_info',$notice_info);
		$web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
		$this->display('ipam_notice_info');
	}   
	/*******************************三务公开*****************************************/
	/**
	 * @name:ipam_affairs_index
	 * @desc：三务公开
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-05-14
	 * @updatetime:2018-05-14
	 * @version：V1.0.0
	 **/
	public function ipam_affairs_index()
	{
		// $cat_id = I('get.cat_id');
		// $this->assign('cat_id',$cat_id);
		// $cat_list = M('cms_affairs_category')->field("cat_id,cat_name")->select();
		// $this->assign('cat_list',$cat_list);
		// 取第一个分类
		$affair_list1 = getAffairsList(1);
 		foreach ($affair_list1 as &$list) {
 			$list['article_thumb'] = getUploadInfo($list['article_thumb']);
			$list['article_content'] = preg_replace ( "/<img[^>]*>/i", '', $list['article_content'] );
			$list['article_content'] = preg_replace ( "/<\/img>/i", '', $list['article_content'] );
 			$list['article_content'] =  mb_substr(removeHtml($list['article_content']),0,80,'utf-8');
 		}
 		$affair_list2 = getAffairsList(2);
 		foreach ($affair_list2 as &$list) {
 			$list['article_thumb'] = getUploadInfo($list['article_thumb']);
			$list['article_content'] = preg_replace ( "/<img[^>]*>/i", '', $list['article_content'] );
			$list['article_content'] = preg_replace ( "/<\/img>/i", '', $list['article_content'] );
 			$list['article_content'] =  mb_substr(removeHtml($list['article_content']),0,80,'utf-8');
 		}

 		$affair_list3 = getAffairsList(3);
 		foreach ($affair_list3 as &$list) {
 			$list['article_thumb'] = getUploadInfo($list['article_thumb']);
			$list['article_content'] = preg_replace ( "/<img[^>]*>/i", '', $list['article_content'] );
			$list['article_content'] = preg_replace ( "/<\/img>/i", '', $list['article_content'] );
 			$list['article_content'] =  mb_substr(removeHtml($list['article_content']),0,80,'utf-8');
 		}
		$this->assign('affair_list1',$affair_list1);
		$this->assign('affair_list2',$affair_list2);
		$this->assign('affair_list3',$affair_list3);
		$web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
		$this->display('ipam_affairs_index');
	}
	/**
	 * @name:ipam_affairs_info
	 * @desc：新闻详情页面
	 * @param：null
	 * @return：
	 * @author：王宗彬
	 * @addtime:2018-04-11
	 * @updatetime:2018-04-11
	 * @version：V1.0.0
	 **/
	public function ipam_affairs_info() {
		$article_id=I("get.article_id");
		$affairs_map['article_id'] = $article_id;
		$article_info = M('cms_affairs')->where($affairs_map)->find();
		$cat_map['cat_id'] = $article_info['article_cat'];
		$article_info['article_cat'] = M('cms_affairs_category')->where($cat_map)->getField('cat_name');
		$article_info['add_communist_name'] = getStaffInfo($article_info['add_staff']);
		$this->assign('article_info',$article_info);
		$web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
		$this->display('ipam_affairs_info');
	}

}