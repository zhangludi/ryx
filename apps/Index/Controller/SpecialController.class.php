<?php
/**
 * Created by PhpStorm.
 * User: wangzongbin
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Index\Controller;


use Think\Controller;

class SpecialController extends Controller
{
	/**
	 *  _initialize
	 * @desc 构造函数
	 * @user liubingtao
	 * @date 2018/4/8
	 * @version 1.0.0
	 */
	public function _initialize()
	{
		$door_communist_no = session('door_communist_no');
        if($door_communist_no){
			$this->assign('is_login', 1);
		} else {
			$this->assign('is_login', 0);
		}
        $where['status'] = '1';
        $nav_list = M('sys_nav')->where($where)->order('nav_order asc')->select();
        foreach ($nav_list as  $key=>&$list) {
            if($key==4 ||$key==3 ){
                $list['class_login'] = 'see-details';
            }
            $list['nav_url'] = U($list['nav_url']);
        }
        $this->assign('nav_list',$nav_list);
        $where['status'] = '1';
        $blogroll_list = M('sys_blogroll')->where($where)->select();
        foreach ($blogroll_list as  &$list) {

            if($list['blogroll_type'] == '1'){
                $list['code'] = "<li><a href='".$list['blogroll_url']."'>".$list['blogroll_name']."</a> </li>";
            }else{
                $list['code'] =  "<li><a href='".U($list['blogroll_url'])."' target='_blank'>".$list['blogroll_name']."</a></li>";
            }
        }
        $this->assign('blogroll_list',$blogroll_list);
	}
	
	/**
	 * @name  ipam_special_index()
	 * @desc  专题专栏
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2018-04-08
	 */
    public function ipam_special_index()
    {
    	// $ad_data = getBannerList(3);
    	// foreach ($ad_data as &$list){
    	// 	switch ($list['ad_id']) {
    	// 		case 11 :$list['hre'] = U("Index/Special/ipam_special_study/topic_id/1");break;
    	// 		case 12 :$list['hre'] = U("Index/Edu/ipam_exam_list");break;
    	// 		case 13 :$list['hre'] = U("Index/Special/ipam_special_study/topic_id/2");break;
    	// 	}
    	// }
    	 
    	// $this->assign("ad_data",$ad_data);
        $communist_no = session('door_communist_no');
    
        $topic_list = getTopicList();
        foreach ($topic_list as $key=>&$list) {
            $list['topic_img'] = getUploadInfo($list['topic_img']);
            $list['percent'] = getTopicLearnInfo($list['topic_id'],$communist_no);
        }
        $this->assign('topic_list',$topic_list); 
        $this->display('ipam_special_index');
    }
    /**
     * @name  ipam_special_study()
     * @desc  三严三实
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2018-04-08
     */
    public function ipam_special_study()
    {
    	$topic_id = I('get.topic_id');
    	$this->assign('topic_id',$topic_id);
    	$topic_list = getTopicInfo($topic_id,'all');
    	$topic_list['topic_img'] = getUploadInfo($topic_list['topic_img']);
    	$this->assign('topic_list',$topic_list);
    	//视频学习
    	$edu_material = getMaterialChildNos('2');
        $material_map['material_topic'] = $topic_id;
        $material_map['material_cat'] = array('in',$edu_material);
    	$edu_material_list = M('edu_material')->field("material_id,material_title,material_thumb")->where($material_map)->limit(0,5)->order("add_time desc")->select();
    	foreach ($edu_material_list as &$list) {
    		$list['material_thumb'] = getUploadInfo($list['material_thumb']);
    	}
    	$this->assign("edu_material",$edu_material_list[0]);
    	$this->assign("edu_material_list",$edu_material_list);
    	//视频列表
    	$edu_material_data = M('edu_material')->field("material_id,material_title,material_thumb")->where($material_map)->limit(0,8)->order("add_time desc")->select();
    	foreach ($edu_material_data as &$data) {
    		$data['material_thumb'] = getUploadInfo($data['material_thumb']);
    		$data['add_time'] = getFormatDate($data['add_time'], "Y-m-d H:i:s");
    	}
    	$this->assign("edu_material_data",$edu_material_data);
    	//学习课件
    	$material_article = getMaterialChildNos('1');
        $article_map['material_topic'] = $topic_id;
        $article_map['material_cat'] = array('in',$material_article);
    	$edu_material_article = M('edu_material')->field("material_id,material_title,material_thumb,material_desc,add_time")->where($article_map)->limit(0,3)->order("add_time desc")->select();
    	foreach ($edu_material_article as &$article) {
    		$article['material_thumb'] = getUploadInfo($article['material_thumb']);
    	}
    	$this->assign("edu_material_article",$edu_material_article);
    	//两学一做/三严三实实况
    	$material_article_list = M('edu_material')->field("material_id,material_title,material_thumb")->where($article_map)->limit(0,10)->order("add_time desc")->select();
    	foreach ($material_article_list as &$k) {
    		$k['material_thumb'] = getUploadInfo($k['material_thumb']);
    	}
    	$this->assign("material_article_list",$material_article_list);
    	//参加考试
        $exam_map['exam_topic'] = $topic_id;
        $exam_map['status'] = array('elt',31);
    	$exam_data = M('edu_exam')->field("exam_id,exam_thumb,exam_title,exam_date,exam_time")->where($exam_map)->limit(0,10)->order("add_time desc")->select();
    	foreach ($exam_data as $key=>&$li) {
    		if($key == '4' || $key == '9'){
    			$li['is_no'] = 1;
     		}
    		$li['exam_thumb'] = getUploadInfo($li['exam_thumb']);
    	}
    	$this->assign('exam_data',$exam_data);
        $this->display('ipam_special_study');
    }
    /**
     * @name  ipam_party_mien_index()
     * @desc  支部风采
     * @param
     * @return
     * @author 刘长军
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2019-07-08
     */
    public function ipam_party_mien_index(){

        $party_no = I('get.party_no');
        $db_meeting = M('oa_meeting');
        $db_party = M('ccp_party');
        $db_communist = M('ccp_communist');
        $communist_no = session('door_communist_no');
        if($communist_no){
            if(empty($party_no)){
                $communist_no = session('door_communist_no');
                $party_no = $db_communist->where("communist_no=$communist_no")->getField('party_no');
            }
            $child_nos = getPartyChildNos($party_no, 'str');//取本级及下级组织
            
            //党组织数量
            $p_map['party_no'] = array('in',$child_nos);
            $p_map['status'] = 1;
            $party_count = $db_party->where($p_map)->count();
            //党员人数
            $map['party_no']=array('in',$child_nos);
            $map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
            $communist_num = $db_communist->where($map)->count();
            //直属下级个数
            $map_part['party_pno'] = $party_no;
            $map_part['status'] = 1;
            $party_p_count = $db_party->where($map_part)->count();
            
            //会议数量
            $meeting_map['meeting_type'] = array('in','2001,2002,2003,2004');
            $meeting_map['party_no'] = $party_no;
            $meeting_num = $db_meeting->where($meeting_map)->count();//会议数量
            $this->assign('party_count',$party_count);
            $this->assign('communist_num',$communist_num);
            $this->assign('party_p_count',$party_p_count);
            $this->assign('meeting_num',$meeting_num);

            $party_data = $db_party->where("party_no=$party_no")->find();
            if($party_data['party_avatar']){
                $party_data['party_avatar'] = getUploadInfo($party_data['party_avatar']);
            }else{
               $party_data['party_avatar'] = __ROOT__.'/uploads/public/'.'org_mien_introduce.png';
            }
            $party_data['party_level_code'] = getBdCodeInfo($party_data['party_level_code'],'party_level_code');
            $this->assign('party_data',$party_data);
            $map_part['party_pno'] = $party_no;
            $map_part['status'] = 1;
            $party_list = $db_party->where($map_part)->select();
            foreach($party_list as &$list){
                if($list['party_avatar']){
                    $list['party_avatar']  = getUploadInfo($list['party_avatar']);
                }else{
                   $list['party_avatar'] = __ROOT__.'/uploads/public/org_mien_introduce.png';
                }
                $part_nos['party_pno'] = $list['party_no'];
                $list['nos'] = $db_party->where($part_nos)->count();
            }
            $this->assign('party_list',$party_list);
        }
        $this->display('ipam_party_mien_index');
    }
    /**
     * @name  ipam_party_mien_info()
     * @desc  支部风采详情
     * @param
     * @return
     * @author 刘长军
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2019-07-08
     */
    public function ipam_party_mien_info(){
        $party_no = I('get.party_no');
        $db_party = M('ccp_party');
        $db_communist = M('ccp_communist');
        $db_meeting = M('oa_meeting');

        $child_nos = getPartyChildNos($party_no, 'str');//取本级及下级组织
        //党组织数量
        $p_map['party_no'] = array('in',$child_nos);
        $p_map['status'] = 1;
        $party_count = $db_party->where($p_map)->count();
        //党员人数
        $map['party_no']=array('in',$child_nos);
        $map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $communist_num = $db_communist->where($map)->count();
        //直属下级个数
        $map_part['party_pno'] = $party_no;
        $map_part['status'] = 1;
        $party_p_count = $db_party->where($map_part)->count();
        
        //会议数量
        $meeting_map['meeting_type'] = array('in','2001,2002,2003,2004');
        $meeting_map['party_no'] = $party_no;
        $meeting_num = $db_meeting->where($meeting_map)->count();//会议数量
        $this->assign('party_count',$party_count);
        $this->assign('communist_num',$communist_num);
        $this->assign('party_p_count',$party_p_count);
        $this->assign('meeting_num',$meeting_num);

        $party_data = $db_party->where("party_no=$party_no")->find();
        if($party_data['party_avatar']){
            $party_data['party_avatar']  = getUploadInfo($party_data['party_avatar']);
        }else{
           $party_data['party_avatar'] = __ROOT__.'/uploads/public/'.'org_mien_introduce.png';
        }
        $party_data['party_level_code'] = getBdCodeInfo($party_data['party_level_code'],'party_level_code');
        $this->assign('party_data',$party_data);

        //支部风采
        $party_propagate = $db_party->where("party_no=$party_no")->getField('party_propagate');
        $party_propagate = getUploadInfo($party_propagate);
        $party_propagate = explode(',',$party_propagate);
        $this->assign('party_propagate',$party_propagate);
        
        //领导班子
        $party_manager = $db_party->where("party_no=$party_no")->getField('party_manager');
        $party_manager = explode(',',$party_manager);
        $ava = [];
        foreach($party_manager as &$list){
            $staff_avatar = M('hr_staff')->where("staff_no=$list")->getField('staff_avatar');
            $ava[] = getUploadInfo($staff_avatar);
        }
        $this->assign('ava',$ava);
        // dump($ava);//头像
        // dump($party_manager);die;//负责人编号、
        $communist_map['party_no'] = $party_no;
        $communist_map['_string'] = "find_in_set('101',post_no)";
        $communist_avatar_list = $db_communist->where($communist_map)->field('communist_avatar')->select();
        $this->assign('communist_avatar_list',$communist_avatar_list);
        $this->display('ipam_party_mien_info');
    }

}