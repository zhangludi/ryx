<?php
/***********************************党员、党组织、职务**********************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
use Ccp\Model\CcpCommunistModel;

class CcpcommunistController extends BaseController{
    /**
     * @name:ccp_party_index
     * @desc：部门管理首页
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
	public function ccp_party_index(){
        checkAuth(ACTION_NAME);
        //查询当前登录人组织
        $child_nos_news = session('party_no_auth');//取本级及下级组织
        $child_nos_list = explode(',', $child_nos_news);
        $party_no = I('get.party_no',$child_nos_list[0]);
        $this->assign('party_no',$party_no);

        $child_list = getPartyChildNos($party_no,'list',0);
        //下级个数
        if($party_no==1){
            $child_list_count = count($child_list);
        }else{
            $child_list_count = count($child_list);
        }
        $this->assign('child_list_count',$child_list_count);
        //支部书记
        $db_communist = M('ccp_communist');
        
		$party_branch_secretary = getPartyInfo($party_no,'party_branch_secretary');
        $fuze['communist_no'] = $party_branch_secretary;
        $ava = $db_communist->where($fuze)->field("communist_no,communist_name,party_no,post_no,communist_avatar")->find();
        if(!$ava['communist_name']){
            $ava['communist_name'] = "无";
        }
        if(!$ava['communist_avatar'] || !file_exists(SITE_PATH.$ava['communist_avatar'])){
            $ava['communist_avatar'] = "/statics/public/images/default_photo.jpg";
        }
        $this->assign('ava',$ava);
        $communist_map['party_no'] = $party_no;
        $communist_map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $communist_map['_string'] = "not find_in_set('201',post_no)  and post_no <> ''";
        $communist_avatar_list = $db_communist->where($communist_map)->field('communist_avatar,communist_no,communist_name,post_no')->limit(2)->select();
        foreach($communist_avatar_list as &$list){
            if(!$list['communist_avatar'] || !file_exists(SITE_PATH.$list['communist_avatar'])){
                $list['communist_avatar'] = "/statics/public/images/default_photo.jpg";
            }
            $list['post_no'] = getPartydutyInfo($list['post_no']);
        }
        $this->assign('communist_avatar_list',$communist_avatar_list);
        // $communist_no = session('staff_no');
        $db_meeting = M('oa_meeting');
        $db_party = M('ccp_party');
        $db_communist = M('ccp_communist');
        if(empty($party_no)){
            $child_nos = session('party_no_auth');//取本级及下级组织
            $p_map['status'] = 1;
            $p_map['party_no'] = array('in',$child_nos);
            $party_no = $db_party->where($p_map)->limit(1)->order('party_no asc')->getField('party_no'); // 如果党组织编号为空默认取第一个党组织编号
        } else {
            $child_nos = getPartyChildNos($party_no, 'str');
        }
        $party_map['party_no'] = $party_no;
        $party_map['status'] = 1;
        $party_info = $db_party->where($party_map)->find(); // 获取党组织信息
        if(!$party_info['party_avatar'] || !file_exists(SITE_PATH.$party_info['party_avatar'])){
            $party_info['party_avatar'] = "/statics/apps/page_index/img/pd1_tu1.png";
        }else{
            $party_info['party_avatar'] = getUploadInfo($party_info['party_avatar']);
        }
        $party_info['party_no_num'] = $db_party->where($p_map)->count(); // 党组织数量
        // $integral = M()->query("select AVG(communist_integral) as communist_integral from sp_ccp_communist where status=1 and party_no='".$party_info['party_no']."'"); // 获取该党组织下的党员的平均分
        
        //$party_info['party_integral'] = $party_info['party_integral']+floor($integral['0']['communist_integral']); // 党组织积分 = 党组织积分 + 党员平均分
        $map['party_no']=array('in',session('party_no_auth'));
        $map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $party_info['communist_num'] = $db_communist->where($map)->count();//党员人数
        //部门负责人
        $manager=$party_info['party_manager'];
        if(!empty($manager)){
            $manager_map['staff_no'] = array('in',$manager);
            $staff_data = M('hr_staff')->where($manager_map)->field('staff_no,staff_name,staff_avatar,staff_mobile')->select();
            if(!empty($staff_data)){
                foreach ($staff_data as &$staff_val) {
                    $upload_map['upload_id'] = $staff_val['staff_avatar'];
                    $staff_avatar = M('bd_upload')->where($upload_map)->getField('upload_path');
                    if(!empty($staff_avatar) || file_exists(SITE_PATH.$staff_avatar)){
                        $staff_val['staff_avatar'] = "<img src=".__ROOT__."/uploads/$staff_avatar>";
                    } else {
                        $staff_val['staff_avatar'] = "<img src='../../statics/public/images/default_photo.jpg'>";
                    }
                }
            }
        }
        //党费
        $time = time();
        $month = date('Y-m', $time);
        $where['party_no']=array('in',$child_nos);
        $where['status']=2;
        $where['dues_month'] = $month;
        $party_info['dues_amount']=M("ccp_dues")->where($where)->sum('dues_amount');
        if(empty($party_info['dues_amount'])){
            $party_info['dues_amount'] = 0;
        }
		$status_map['status_group'] = 'meeting_status';
    	$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        // 会议列表
        $meeting_map['meeting_type'] = array('in','2001,2002,2003,2004');
        $meeting_map['party_no'] = array('in',getPartyChildNos($party_no));
        $meeting_map['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $meeting_list = $db_meeting->where($meeting_map)->order('add_time desc')->limit(2)->field('meeting_name,meetting_thumb,meeting_no,meeting_start_time,meeting_host,meeting_addr,party_no,status')->select();
        foreach ($meeting_list as &$meeting) {
            $meeting['meeting_host'] = getCommunistInfo($meeting['meeting_host']);
            $meeting['meeting_start_time'] = date("Y-m-d",strtotime($meeting['meeting_start_time']));
            if($meeting['meetting_thumb'] && file_exists(SITE_PATH.getUploadInfo($meeting['meetting_thumb']))){
                $meeting['meetting_thumb'] = getUploadInfo($meeting['meetting_thumb']);
            }else{
                $meeting['meetting_thumb'] = "/statics/apps/page_index/img/pd1_tu3.png";
            }
            $meeting['communist_count'] = M('oa_meeting_communist')->where('meeting_no='.$meeting['meeting_no'].'')->count();
			$meeting['status'] = $status_name_arr[$meeting['status']];//getStatusName('meeting_status',$data['status']);
		}
        $meeting_num = $db_meeting->where($meeting_map)->count();//会议数量
        //获取党组织列表
            
        // $child_nos_n = getPartyChildNos($party_no, 'str');
        $child_nos_n = session('party_no_auth');//取本级及下级组织
        $p_map['party_no'] = array('in',$child_nos_n);
        $party_list1 = $db_party->where($p_map)->field('party_no,party_pno,party_name,party_avatar')->select();

        $child_nos_n_p = getPartyChildNos($party_no, 'str',0);
        if($child_nos_n_p!=null){
             // $child_nos_n = session('party_no_auth');//取本级及下级组织
            $ppp['party_no'] = array('in',$child_nos_n_p);
            $party_list = $db_party->where($ppp)->field('party_no,party_pno,party_name,party_avatar')->select();
            foreach ($party_list as &$vall) {
                $vall['party_avatar'] = getUploadInfo($vall['party_avatar']);
                $meeting_map1['status'] = 23;
                $meeting_map1['party_no'] = $vall['party_no'];
                $vall['meeting_count'] = $db_meeting->where($meeting_map1)->count();//会议数量
                $communistmap['party_no']=array('in',$vall['party_no']);
                $communistmap['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
                $vall['communist_party_num'] = $db_communist->where($communistmap)->count();//党员人数
            }
        }else{
           $party_list = 0; 
        }
       
        $this->assign('meeting_list',$meeting_list);//动态
		$this->assign('party_list_count',count($party_list));
        $this->assign('party_list',$party_list);
        $this->assign('party_list1',$party_list1);
        $this->assign('party_info',$party_info);
        $this->assign('communist_data',$communist_data);//负责人信息
        $this->assign('meeting_num',$meeting_num);//会议
        $this->assign('party_propagate',$party_info['party_propagate']);//党组织风采
        $this->assign('staff_data',$staff_data);//动态
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);

        //党内动态

        $article_cat_arr =  M("cms_article_category")->where("cat_type=1")->getField("cat_id",true);
        $article_cat_str = arrToStr($article_cat_arr);
        $article_map['article_cat'] = array('in',$article_cat_str);
        $article_map['party_no'] = array('in',getPartyChildNos($party_no));
        $cms_article_list = M('cms_article')->where($article_map)->order('add_time desc')->limit(2)->select();
        $cms_article_count = M('cms_article')->where($article_map)->count();
        foreach ($cms_article_list as &$article) {
            $article['add_time'] = date("Y-m-d",strtotime($article['add_time']));
            if($article['article_thumb'] && file_exists(SITE_PATH.$article['article_thumb'])){
                $article['article_thumb'] = getUploadInfo($article['article_thumb']);
            }else{
                $article['article_thumb'] = "/statics/apps/page_index/img/pd1_tu2.png";
            }
            $article['article_content'] = strip_tags($article['article_content']);
        }
		if(!$cms_article_count){
			$cms_article_count = 0;
		}
        $this->assign('cms_article_count',$cms_article_count);
        $this->assign('cms_article_list',$cms_article_list);
        $this->display("Ccpparty/ccp_party_index");
    }
	public function ccp_party_index_data(){
        $db_communist = M('ccp_communist');
        $db_meeting = M('oa_meeting');
        $db_party = M('ccp_party');
        //党员人数
        $party_no = I('get.party_no');
        $child_nos = getPartyChildNos($party_no,'str',0);

        $child_list = getPartyChildNos($party_no,'list',0);
        //组织列表
        if($child_nos!=null){
            $pp['party_no'] = array('in',$child_nos);
            $data['party_data'] = $db_party->where($pp)->field('party_no,party_name,party_avatar')->select();
            foreach ($data['party_data'] as &$vall) {
                $vall['party_avatar'] = getUploadInfo($vall['party_avatar']);
                $meeting_map1['status'] = 23;
                $meeting_map1['party_no'] = $vall['party_no'];
                $vall['meeting_count'] = $db_meeting->where($meeting_map1)->count();//会议数量
                $communistmap['party_no']=array('in',$vall['party_no']);
                $communistmap['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
                $vall['communist_party_num'] = $db_communist->where($communistmap)->count();//党员人数
            }
        }else{
            $data['party_data'] = null;
        }
        //下级组织
        $data['partychild'] = count($child_list);
        if(!$child_nos){
            $child_nos = '0';
        }
        $map['party_no']=array('in',getPartyChildNos($party_no));
        $map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $data['communist_num'] = $db_communist->where($map)->count();
        // 会议
        $meeting_map['meeting_type'] = array('in','2001,2002,2003,2004');
        $meeting_map['party_no'] = array('in',getPartyChildNos($party_no));
        $meeting_map['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $status_map['status_group'] = 'meeting_status';
    	$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        //会议列表
        $data['meeting_list'] = $db_meeting->where($meeting_map)->order('add_time desc')->limit(2)->field('meeting_name,meetting_thumb,meeting_no,meeting_start_time,meeting_host,meeting_addr,status')->select();
        foreach ($data['meeting_list'] as &$meeting_val) {
            if($meeting_val['meetting_thumb'] || file_exists(SITE_PATH.getUploadInfo($meeting_val['meetting_thumb']))){
                $meeting_val['meetting_thumb'] = getUploadInfo($meeting_val['meetting_thumb']);
            }else{
                $meeting_val['meetting_thumb'] = "".__ROOT__."/static/apps/page_index/new_portrait/img/pd1_qi.png";
            }
            $meeting_val['meeting_host'] = getCommunistInfo($meeting_val['meeting_host']);
            $meeting_val['meeting_start_time'] = date("Y-m-d",strtotime($meeting_val['meeting_start_time']));
            $meeting_val['communist_count'] = M('oa_meeting_communist')->where('meeting_no='.$meeting_val['meeting_no'].'')->count();
			$meeting_val['status'] = $status_name_arr[$meeting_val['status']];//getStatusName('meeting_status',$data['status']);
		}
        //会议数量
        $data['meeting_num'] = $db_meeting->where($meeting_map)->count();
         //党费
        $time = time();
        $month = date('Y-m', $time);
        $where['party_no']=array('in',$child_nos);
        $where['status']=2;
        $where['dues_month'] = $month;
        $data['dues_amount']=M("ccp_dues")->where($where)->sum('dues_amount');
        if(empty($data['dues_amount'])){
            $data['dues_amount'] = 0;
        }
        //组织详情
        $party_map['party_no'] = $party_no;
        $party_map['status'] = 1;
        $data['dataparty_info'] = $db_party->where($party_map)->find(); // 获取党组织信息  
		if(!empty($data['dataparty_info'])){
			$data['dataparty_info'] = "(".$data['dataparty_info'].")";
		}		
        if(!$data['dataparty_info']['party_avatar'] || !file_exists(SITE_PATH.getUploadInfo($data['dataparty_info']['party_avatar']))){
            $data['dataparty_info']['party_avatar'] = "".__ROOT__."/statics/apps/page_index/img/pd1_tu3.png";
        }else{
            $data['dataparty_info']['party_avatar'] = getUploadInfo($data['dataparty_info']['party_avatar']);
        }
        //支部书记
        $db_communist = M('ccp_communist');
		$party_branch_secretary = getPartyInfo($party_no,'party_branch_secretary');
        $fuze['communist_no'] = $party_branch_secretary;
        $ava = $db_communist->where($fuze)->field("communist_no,communist_name,party_no,post_no,communist_avatar")->find();
        if(!$ava['communist_name']){
            $ava['communist_name'] = "无";
        }
		if(!$ava['communist_avatar'] || !file_exists(SITE_PATH.$ava['communist_avatar'])){
            $ava['communist_avatar'] = "/statics/public/images/default_photo.jpg";
        }
		 		
        $data['ava'] = $ava;
        // $this->assign('ava',$ava);
        $communist_map['party_no'] = $party_no;
        $communist_map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $communist_map['_string'] = "not find_in_set('201',post_no) and post_no <> '' ";
        $communist_avatar_list = $db_communist->where($communist_map)->field('communist_avatar,communist_no,communist_name,post_no')->limit(2)->select();
        foreach($communist_avatar_list as &$list){
            $list['post_no'] = getPartydutyInfo($list['post_no']);
            if(!$list['communist_avatar'] || !file_exists(SITE_PATH.$list['communist_avatar'])){
                $list['communist_avatar'] = "/statics/public/images/default_photo.jpg";
            }
        }

        $data['communist_avatar_list'] = $communist_avatar_list;
        
        //党内动态
        $article_cat_arr =  M("cms_article_category")->where("cat_type=1")->getField("cat_id",true);
        $article_cat_str = arrToStr($article_cat_arr);
        $article_map['article_cat'] = array('in',$article_cat_str);
        $article_map['party_no'] = array('in',getPartyChildNos($party_no));
        $cms_article_list = M('cms_article')->where($article_map)->order('add_time desc')->limit(2)->select();
        $cms_article_count = M('cms_article')->where($article_map)->count();
        foreach ($cms_article_list as &$article) {
            $article['add_time'] = date("Y-m-d",strtotime($article['add_time']));
			if($article['article_thumb'] && file_exists(SITE_PATH.getUploadInfo($article['article_thumb']))){
                $article['article_thumb'] = getUploadInfo($article['article_thumb']);
            }else{
                $article['article_thumb'] = "/statics/apps/page_index/img/pd1_tu2.png";
            }
            $article['article_content'] = strip_tags($article['article_content']);
        }
		if(!$cms_article_count){
			$cms_article_count = 0;
		}
        $data['cms_article_count'] = $cms_article_count;
        $data['cms_article_list'] = $cms_article_list;
        ob_clean();$this->ajaxReturn($data);

    }
    /**
     * @name:ccp_party_location
     * @desc：设置经纬度
     * @author：刘长军
     * @addtime:2020-1-2
     * @version：V1.0.0
     **/
    public function ccp_party_location(){
        $field_name = I('get.field_name');
        $this->assign('name',$field_name);
        $this->display("Ccpparty/ccp_party_location");

    }
    /**
     * @name:ccp_party_ajax_info
     * @desc：部门详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_ajax_info(){
        $db_communist = M('ccp_communist');
        $db_meeting = M('oa_meeting');
        $db_party = M('ccp_party');
        $party_no = I('get.party_no');
        $party_map['party_no'] = $party_no;
        $party_map['status'] = 1;
        $party_info = $db_party->where($party_map)->find(); // 获取党组织信息
        $child_nos = getPartyChildNos($party_no,'arr');//党组织编号
        $data['party_no_num'] = count($child_nos);//党组织数量
        $map['party_no']=array('in',$child_nos);
		$map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
		$data['communist_num'] = $db_communist->where($map)->count();//党员人数
        //积分
        //$integral = M()->query("select AVG(communist_integral) as communist_integral from sp_ccp_communist where status=1 and party_no='$party_no'");
        //$party_integral = $party_info['party_integral']+floor($integral['0']['communist_integral']);// 党组织积分 = 党组织积分 + 党员平均分
        $party_integral = $party_info['party_integral']; // 党组织积分
        //部门负责人
		$manager=$party_info['party_manager'];
        if(!empty($manager)){
            $manager_map['staff_no'] = array('in',$manager);
            $staff_data = M('hr_staff')->where($manager_map)->field('staff_no,staff_name,staff_avatar,staff_mobile')->select();
            if(!empty($staff_data)){
                foreach ($staff_data as &$staff_val) {
                    $upload_map['upload_id'] = $staff_val['staff_avatar'];
                    $staff_avatar = M('bd_upload')->where($upload_map)->getField('upload_path');
                    if(!empty($staff_avatar)){
                        $staff_val['staff_avatar'] = "<img src=".__ROOT__."/uploads/$staff_avatar>";
                    } else {
                        $staff_val['staff_avatar'] = "<img src='../../statics/public/images/default_photo.jpg'>";
                    }
                }
            }
        }
        $data['memo'] = $party_info['memo'];//部门简介
        $where['party_no']=array('in',$child_nos);
        $where['status']=2;
        $data['dues_amount']=M("ccp_dues")->where($where)->sum('dues_amount');//党费
        if (empty($data['dues_amount'])){
            $data['dues_amount']=0;
        }
        $meeting_map['meeting_type'] = array('in','2001,2002,2003,2004');
        $meeting_map['party_no'] = $party_no;
        $data['meeting_list'] = $db_meeting->where($meeting_map)->order('add_time desc')->limit('5')->select();
        $meeting_num = $db_meeting->where($meeting_map)->count();//会议数量
        $party_propagate = $party_info['party_propagate'];
        $data['party_manager_arr'] = $staff_data; // 党组织负责人
        if(!empty($party_integral)){
            $data['meeting_num'] = $meeting_num;
        }else{
            $data['meeting_num'] = 0;
        }
        $data['party_name'] = $party_info['party_name'];
        if(!empty($party_integral)){
            $data['party_integral'] = $party_integral;
        }else{
            $data['party_integral'] = 0;
        }
        $data['party_propagate'] = getUploadHtml($party_propagate,'100','100','1','0');
        ob_clean();$this->ajaxReturn($data);
    }
    /**
     * @name:getPartyLists
     * @desc：获取部门列表
     * @param：$party_pno(父级no) $num(制表符数量)
     * @return：
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    function getPartyLists($party_pno = 0,$num = -1,$type=0){
        $db_party = M('ccp_party');
        if($type == 1){
            $party_map['party_no'] = $party_pno;
        }else{
            $party_map['party_pno'] = $party_pno;
        }
        $party_map['status'] = 1;
        $party_list = $db_party->where($party_map)->select();
        $category_list = array();
    	$symbol = "├─";
    	$tabs = "";
    	for($i = 0;$i <= $num; $i++){
    		$tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
    	}
    	$tabs .= $symbol;
    	$num++;
    	$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
    	$party_name_arr = M('ccp_party')->getField('party_no,party_name');
        foreach ($party_list as &$party) {
            if (!empty($party['party_manager'])){
                $manager_list = explode(",", $party['party_manager']); // 分割成数组
                $manager_name = "";
                foreach ($manager_list as & $manager) {
                    if (empty($manager_name)) {
                        $manager_name = $staff_name_arr[$manager];
                    } else {
                        $manager_name .= ',' .  $staff_name_arr[$manager];
                    }
                }
                $party['party_manager'] = $manager_name;
            }
            // if($party['party_pno'] == "0" && $party['party_type'] == "2" ){
            //     $party['operate'] = "";
            // }else{
            //     $party['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('Ccpcommunist/ccp_party_edit', array('party_no' => $party['party_no'])) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('Ccpcommunist/ccp_party_do_del', array('party_no' => $party['party_no'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
            // }
            $party['party_name'] = $tabs.$party['party_name'];
            $party['party_pno'] =  !empty($party_name_arr[$party['party_pno']])? $party_name_arr[$party['party_pno']] : '无';
            if (!empty($party['add_staff'])){
                $party['add_staff'] =  $staff_name_arr[$party['add_staff']];
            }
            $category_list[] = $party;
            $is_child = checkDataAuth(session("staff_no"),"is_child");
            if($is_child){
                $article_category_sonlist =$this->getPartyLists($party['party_no'],$num);
                foreach($article_category_sonlist as &$sonlist){
                    $category_list[] = $sonlist;
                }
            }
            
        }
        return $category_list;
    }
    /**
     * @name:ccp_party_list
     * @desc：部门列表
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_list(){
        checkAuth(ACTION_NAME);
        $party_no = I('get.party_no');
        $type = I('get.type');
        if(!empty($type)){
            $this->assign('type',$type);
			$this->assign('party_no',session('party_no_auth'));
        }else{
            if(!empty($party_no)){
                $this->assign('party_no',$party_no);
            }else{
                $this->assign('party_no',session('party_no_auth'));
			}
        }
        // $category_list =$this->getPartyLists(0,-1);
        // $this->assign('category_list',$category_list);
        $this->display("Ccpparty/ccp_party_list");
    }
    /**
     * @name:ccp_party_list_data
     * @desc：部门列表
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_list_data(){
		$page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
		
		
        $party_no = I('get.party_no');
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $where['party_no'] = array('in',$party_no);//取本级及下级组织
        $category_list = M('ccp_party')->field('party_no,party_pno,party_name,party_manager')->limit($page,$pagesize)->where($where)->select();
		$category_data['count'] = M('ccp_party')->where($where)->count();
        foreach ($category_list as &$list) {
            $list['party_pno'] = !empty($party_name_arr[$list['party_pno']])? $party_name_arr[$list['party_pno']] : '无';
            $list['party_manager'] = getStaffInfo($list['party_manager']);
        }
        if(!empty($category_list)){
            $category_data['code'] = 0;
            $category_data['msg'] = '获取数据成功';
            $category_data['data'] = $category_list;
            ob_clean();$this->ajaxReturn($category_data);
        } else {
            $category_data['code'] = 1;
            $category_data['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($category_data);
        }
    }
   
   /**
     * @name:ccp_party_import_index
     * @desc：部门导入页面
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_import_index(){
        $type = I('get.type');
        if(!empty($type)){
            $this->assign('type',$type);
        }else{
            $this->assign('type','0');
        }
        $import_count = M('ccp_import')->where("import_type=1")->count();
        $this->assign('import_count',$import_count);

        $this->display('ccp_party_import_index');
    }
    /**
     * @name:ccp_party_upload   
     * @desc：部门导入
     * @author：刘丙涛--王宗彬
     * @addtime:2017-05-27
     * @updatetime:2018-08-14
     * @version：V1.0.0
     **/
    public function ccp_party_upload(){
        $type = I('post.type');
        $file = $_FILES['file_stu'];
        if (!empty($file['tmp_name']))
        {
            $upload = new \Think\Upload();
            $upload->maxSize = 0; // 不限制附件上传大小
            $upload->exts = array('xls','xlsx'); //
            $upload->rootPath = C('TMPL_PARSE_STRING')['__UPLOAD_PATH__']; // 上传文件所在文件夹
            $upload->savePath = 'ccp/party/xls/'; // 设置附件上传目录
            $upload->autoSub = true; // 开启子目录保存
            $info = $upload->upload();
            if (!$info) {
                $this->error($upload->getError());
            }
            import("Org.Util.PHPExcel");
            //Vendor('PHPExcel.PHPExcel.IOFactory');
            import("Org.Util.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $file_name= C('TMPL_PARSE_STRING')['__UPLOAD_PATH__'].$info['file_stu']['savepath'].$info['file_stu']['savename'];
            $extension = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );
            if ($extension =='xlsx') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            } else if ($extension =='xls') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            }
            $path=BASE_PATH.$file_name;
            try {
                $objPHPExcel = $objReader->load($path);
            } catch (Exception $e) {
                showMsg('error', '文件格式与扩展名不匹配，请检查后重新上传！');
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            $db_import = M('ccp_import');
            for($i=2;$i<=$highestRow;$i++) {
                if(!empty($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))
                {
                    $data['party_no'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $party_no = $data['party_no'];
                    $data['party_pno'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $data['party_name'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                    $data['import_type'] = '1';//1党组织 2党员
                    $list_add[] = $data;
                }
            }
            $flag = $db_import->addAll($list_add);
            if($flag)
            {
                 showMsg(success, '上传成功', U('ccp_party_import_index',array('type'=>$type)));
            }else{
                showMsg(error, '上传失败',  U('ccp_party_list'));
            }
        }else{
            showMsg(error, '上传的文件为空，请从新上传数据',  U('ccp_party_list'));
        }
    }
     /**
     * @name:ccp_party_import
     * @desc：部门开始导入
     * @author：王宗彬
     * @addtime:2018-08-14
     * @version：V1.0.0
     **/ 
    public function ccp_party_import(){
        $type = I('get.type');
        $db_import = M('ccp_import');
        $db_party = M('ccp_party');
        $import_data = $db_import->where('import_type=1')->select();
        foreach ($import_data as $data) {
            $party_no = $data['party_no'];
            $data['add_staff'] = session('staff_no');
            $data['status'] ='1';
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $party_map['party_no'] = $party_no;
            $res = $db_party->where($party_map)->find();
            if(!empty($res)){
                $flag =$db_party->where($party_map)->save($data);
                if($flag){
                    $db_import->where($party_map)->delete();
                }
            }else{
                $flag1 = $db_party->add($data);
                if($flag1){
                    $db_import->where($party_map)->delete();
                }
            }
        }
        $party_no_auth = checkDataAuth(session("staff_no"),"is_admin",1,"str");
        session('party_no_auth',$party_no_auth);
        // $flag1 = $db_party->saveAll($list_save);
        //$flag = $db_party->addAll($list_add);
        if($flag || $flag1){
             showMsg(success, '导入成功', U('ccp_party_import_index',array('type'=>$type)));
        }else{
            showMsg(error, '导入失败');
        }
    }
    /**
     * @name:case_export_party
     * @desc：部门导出错误信息
     * @author：王宗彬
     * @addtime:2018-08-14
     * @version：V1.0.0
     **/            
    public function case_export_party(){
        $db_import = M('ccp_import');
        $import_data = $db_import->where("import_type=1")->field('party_no,party_pno,party_name')->select();
        $db_import->where("import_type=1")->delete();
        $head['party_no']='党组织编号';
        $head['party_pno']='组织上级编号';
        $head['party_name']='党组织名称';
        exportExcel("党组织",$head,$import_data);
    }
    /**
     * @name:ccp_party_edit
     * @desc：部门添加/编辑，添加为_add
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_edit(){
        
        checkAuth(ACTION_NAME);
        $db_party = M('ccp_party');
        $party_no = I('get.party_no');
        if (! empty($party_no)) {
            $party_row =getPartyInfo($party_no,'all');
            $party_row['longlatitude'] = $party_row['gc_lng'].','.$party_row['gc_lat'];
            $this->assign("party_row", $party_row);
        }
        $type = I('get.type');
        $this->assign("type",$type);
        $this->display("Ccpparty/ccp_party_edit");
    }
    /**
     * @name:ccp_party_info
     * @desc：部门详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_info(){
        checkAuth(ACTION_NAME);
        $this->display("Ccpparty/ccp_party_info");
    }
    /**
     * @name:ccp_party_do_save
     * @desc：部门操作执行
     * @author：王宗彬
     * @addtime:2017-11-17
     * @version：V1.0.0
     **/
    public function ccp_party_do_save(){
        checkLogin();
        $db_party = M('ccp_party');
        $data = I('post.'); // I方法获取整个数组
        $longlatitude = explode(',',$data['longlatitude']);
        $data['gc_lng'] = $longlatitude[0];
        $data['gc_lat'] = $longlatitude[1];
        $party_no=$data['party_no'];
        $party_name=$data['party_name'];
        $type = $data['type']; //判断跳转的页面
        if (!empty($party_no)) {
            // if(!empty($data['party_propagate'])){ // 党支部风采
            //     $party_propagate = str_replace("`",",",$data['party_propagate']);
            //     renameFiles('ccp','party_propagate',$party_no,$party_propagate);
            // }
            // if(!empty($data['party_avatar'])){ // 党支部头像
            //     $party_avatar = str_replace("`",",",$data['party_avatar']);
            //     renameFiles('ccp','party_avatar',$party_no,$party_avatar);
            // }
            $data['update_time'] = date("Y-m-d H:i:s");
            if(!empty($data['party_pwd'])){
               $data['party_pwd'] = md5($data['party_pwd']); 
            }
            $oper_res = $db_party->save($data);
            
            $log_type_name='修改';//系统日志类型名称
            $log_type=2;
        } else {
            if (! empty($party_name)) {
            	$party = getPartyList('');  
            	if(empty($party)){   //是否存在支部  
            		$data['party_no'] = 1;  //第一个支部编号为1
            	}else if(empty($data['party_pno'])){
            		$data['party_no'] = getFlowNo('', 'ccp_party', 'party_no', '1');  //支部编号流水
            	}else{
            		$data['party_no'] = getFlowNo($data['party_pno'], 'ccp_party', 'party_no', '2');  //支部编号流水
            	}
                $data['status'] = '1';
                $data['party_pwd'] = 
                $data['add_time'] = date("Y-m-d H:i:s");
                $data['add_staff'] = $this->staff_no;
                if(!empty($data['party_pwd'])){
                    $data['party_pwd'] = md5($data['party_pwd']); 
                }
                $oper_res = $db_party->add($data);
                // $party_no_auth = getPartyChildNos($communist_info['party_no'], 'str','1','is_admin');
                // session('party_no_auth',  $party_no_auth);
                // if(!empty($data['party_propagate'])){ // 党支部风采
                //     $party_propagate = str_replace("`",",",$data['party_opagate']);
                //     renameFiles('ccp','party_propagate',$oper_res,$party_propagate);
                // }
                // if(!empty($data['party_avatar'])){ // 党支部头像
                //     $party_avatar = str_replace("`",",",$data['party_avatar']);
                //     renameFiles('ccp','party_avatar',$oper_res,$party_avatar);
                // }
                $log_type_name='新增';//系统日志类型名称
                $log_type=1;
            } else {
                showMsg('error', '请将内容填写完整！','');
            }
        }
        $is_admin = checkDataAuth(session("staff_no"),"is_admin");
        if($is_admin){
            $party_no_auth = checkDataAuth(session("staff_no"),"is_admin",1,"str");

        }else{
            // dump($party_no_auth);die;
            $user_map['user_relation_no'] = session('staff_no');
            $charge_party = M("sys_user")->where($user_map)->getField("charge_party");
            $party_map['party_no'] = array('in',$charge_party);
            $party_no_arr=M("ccp_party")->where($party_map)->field('party_no')->getField("party_no",true);
            if(!empty($party_no_arr)){
                $is_child = checkDataAuth(session("staff_no"),"is_child");
                if($is_child){
                    $party_nos_arr=getPartyChildMulNos(arrToStr($party_no_arr),'arr');
                    sort($party_nos_arr);
                }else{
                    foreach ($party_no_arr as $party_val) {
                        $party_nos_arr[] = $party_val;
                    }
                }
            }
            $party_no_auth=arrToStr($party_nos_arr);
        }
        
        session('party_no_auth',  $party_no_auth); // 当前用户可以查看的党组织


        if ($oper_res) {
            saveLog(ACTION_NAME,$log_type,'','操作员['.getStaffInfo(session('staff_no')).']于'.date("Y-m-d H:i:s").$log_type_name.'党组织数据，组织编号为['.$party_no.']');
            if(!empty($type)){
            	showMsg('success', '操作成功！！！',  U('Ccpcommunist/ccp_party_index',array('party_no'=>$party_no)));
            }else{
            	showMsg('success', '操作成功！！！',  U('Ccpcommunist/ccp_party_index'));
            }
        } else {
            showMsg('error', '操作失败','');
        }
    }
    /**
     * @name:ccp_party_do_del
     * @desc：部门删除
     * @author：王宗彬-杨凯
     * @addtime:2017-11-3
     * @update:2018-01-02
     * @version：V1.0.0
     **/
    public function ccp_party_do_del(){
        checkAuth(ACTION_NAME);
        $db_party = M('ccp_party');
        $party_no = I('get.party_no');
        $communist_nolist=getCommunistList($party_no,'str',1);//取当前部门下的所有员工编号
        if($communist_nolist){
            showMsg('error', '该部门下有员工，不可删除','');
        }
        $party_nolist=getPartyChildNos($party_no,'str',0);
        if($party_nolist){
        	showMsg('error', '该部门下有其他部门，不可删除','');
        }
        if($party_no){
            $party_map['party_no'] = $party_no;
            $del_res = $db_party->where($party_map)->delete();
        }
        if ($del_res) {
            saveLog(ACTION_NAME,3,'','操作员['.session('communist_no').']于'.date("Y-m-d H:i:s").'对部门编号 ['.$party_no.']进行删除操作');
            showMsg('success', '操作成功！！！',  U('Ccpcommunist/ccp_party_list'));
        } else {
            showMsg('error', '操作失败','');
        }
    }
    /**
     * @name:ccp_party_duty_index
     * @desc：岗位管理首页
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_duty_index(){
        checkAuth(ACTION_NAME);
        $post_name=I('post.post_name');
        if(!empty($post_name)){
            $this->assign('post_name', $post_name);
        }
        $this->display("Ccppartyduty/ccp_party_duty_index");
    }
    /**
     * @name:ccp_party_duty_index_data
     * @desc：岗位管理数据加载
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_duty_index_data(){
        $post_name=I('get.post_name');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $post_list=getPartydutyList($post_name,$page,$pagesize);
        
        $status_map['status_group'] = 'post_status';
        $status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
        if(!empty($post_list['data'])){
           foreach ($post_list['data'] as &$post) {
                $post['status_name'] = "<font color='" . $status_color_arr[$post['status']] . "'>" . $status_name_arr[$post['status']] . " </font>";
                $post['add_staff'] = getStaffInfo($post['add_staff']);
                $post['add_time'] = getFormatDate($post['add_time'], 'Y-m-d');
            } 
            $post_list['code'] = '0';
            $post_list['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($post_list); // 返回json格式数据 
        }else{
            $post_list['code'] = '0';
            $post_list['msg'] = '获取数据失败';
            ob_clean();$this->ajaxReturn($post_list); // 返回json格式数据 
        }
        
    }
    /**
     * @name:ccp_party_duty_edit
     * @desc：岗位添加/编辑，添加为_add
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_duty_edit(){
        checkAuth(ACTION_NAME);
        $db_partyduty = M('ccp_party_duty');
        $post_no = I('get.post_no'); // I方法获取数据
        if (! empty($post_no)) { // 必要的非空判断需要增加,防止报错
            $post_row =getPartydutyInfo($post_no,'all');
            $this->assign("post_row", $post_row); // 控制器与视图页面的变量尽量保持一致
        }
        $this->display("Ccppartyduty/ccp_party_duty_edit");
    }
    /**
     * @name:ccp_party_duty_do_save
     * @desc：岗位操作执行
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_duty_do_save(){
        checkLogin();
        $db_partyduty = M('ccp_party_duty');
        $post_no=I('post.post_no');
		
            $data['update_time'] = date("Y-m-d H:i:s");
        if (! empty($post_no)) { // 有id时执行修改操作
            $data['post_no']=I('post.post_no');
            $data['post_name']=I('post.post_name');
            $data['memo']=$_POST['memo'];
            $oper_res = $db_partyduty->save($data);            
            $log_type_name='修改';//系统日志类型名称
            $log_type=2;;
        } else { // 无id时执行添加操作
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['add_staff'] = $this->staff_no;
			$data['post_no']=I('post.post_no');
			$data['post_name']=I('post.post_name');
			$data['status'] = 1;
			$data['memo']=$_POST['memo'];
			$oper_res = $db_partyduty->add($data);
			$log_type_name='新增';//系统日志类型名称
			$log_type=1;
        }
        if ($oper_res) {
            saveLog(ACTION_NAME,$log_type,'','操作员['.getStaffInfo(session('staff_no')).']于'.date("Y-m-d H:i:s").$log_type_name.'岗位数据，编号为['.$data['post_no'].']');
            showMsg('success', '操作成功！！！',U('Ccpcommunist/ccp_party_duty_index'),1);
        } else {
            showMsg('error', '操作失败','');
        }
    }
	/**
     * @name:check_party_duty_name
     * @desc：岗位删除
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function check_party_duty_name(){
        $post_name = I('post.post_name');
		$post_name = str_replace(' ', '', $post_name);
		
        $post_no = I('post.post_no');
		if(!empty($post_no)){
			$where['post_no'] = array('neq',$post_no);
		}		
		$where['post_name'] = $post_name;
		$res = M('ccp_party_duty')->where($where)->count();
		if ($res >= 1){
			$this->ajaxReturn(true);
		}else{
			$this->ajaxReturn(false);
		} 
    }
    /**
     * @name:ccp_party_duty_do_del
     * @desc：岗位删除
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_party_duty_do_del(){
        checkAuth(ACTION_NAME);
        $db_partyduty = M('ccp_party_duty');
        $post_no = I('get.post_no'); // I方法获取数据
        if (! empty($post_no)) { // 必要的非空判断需要增加
            $is_enable=I('get.is_enable');
            if($is_enable=='yes'){
                $status=1;
                $log_type_name='启用';
            }else{
                $status=0;
                $log_type_name='停用';
            }
            $duty_map['post_no'] = $post_no;
            $del_res = $db_partyduty->where($duty_map)->setField('status',$status);
        }
        if ($del_res) {
            saveLog(ACTION_NAME,3,'','操作员['.$this->staff_no.']于'.date("Y-m-d H:i:s").'对岗位编号 ['.$post_no.']进行'.$log_type_name.'操作');
            showMsg('success', '操作成功！！！',U('Ccpcommunist/ccp_party_duty_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }
    /**
     * @name:ccp_communist_index
     * @desc：通讯录/党员管理首页
     * @author：王宗彬
     * @addtime:2017-10-10
     * @version：V1.0.1
     **/
    public function ccp_communist_index(){
    	if($_GET['type'] == 2){
    		$cat = '_1';
    	}else{
    		$cat = '_2';
    	}
    	session('cat',$cat);
    	checkAuth(ACTION_NAME.$cat);
    	$type = I('get.type','1');
    	$this->assign('type',$type);//通讯录type=2
        $party_no = I('get.party_no','1');
        $this->assign('party_no',$party_no);
    	if($type==2){
    	    $party_list = getPartyList(0);
			$where['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
			$communist_num = M('ccp_communist')->where($where)->count();
			$where['_string'] = "FIND_IN_SET('201',post_no)";//支部书记
			$communist_num1 = M('ccp_communist')->where($where)->count();
			$where['_string'] = "age_distribute = 1";
			$communist_num2 = M('ccp_communist')->where($where)->count();
			$where['_string'] = "age_distribute = 2";
			$communist_num3 = M('ccp_communist')->where($where)->count();
    	}else{
            $party_no_auth = session('party_no_auth');//取本级及下级组织
            $party_map['status'] = 1;
            $party_map['party_no'] = array('in',$party_no_auth);
    	    $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
    	    //$this->assign('party_no',getCommunistInfo(session("communist_no"),'party_no'));
			$where['party_no']=array('in',$party_no_auth);
			$where['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
			$communist_num = M('ccp_communist')->where($where)->count();	
			$where['_string'] = "FIND_IN_SET('201',post_no)";//支部书记
			$communist_num1 = M('ccp_communist')->where($where)->count();
			$where['_string'] = "age_distribute = 1";
			$communist_num2 = M('ccp_communist')->where($where)->count();
			$where['_string'] = "age_distribute = 2";
			$communist_num3 = M('ccp_communist')->where($where)->count();			
		}
		if(!$communist_num){
			$communist_num = 0;
		}
		if(!$communist_num1){
			$communist_num1 = 0;
		}
		if(!$communist_num2){
			$communist_num2 = 0;
		}
		if(!$communist_num3){
			$communist_num3 = 0;
		}
        $this->assign('party_list',$party_list);
        $this->assign('communist_num',$communist_num);
        $this->assign('communist_num1',$communist_num1);
        $this->assign('communist_num2',$communist_num2);
        $this->assign('communist_num3',$communist_num3);
        $this->display("Ccpcommunist/ccp_communist_index");
    }
    /**
     * @name:ccp_communist_index_data
     * @desc：通讯录/党员数据获取
     * @author：王宗彬
     * @updateime:2017-10-11
     * @version：V1.0.1
     **/
    public function ccp_communist_index_data(){
    	$party_no=I('post.party_no');//点击的党支部编号
    	$pagesize = I('post.pagesize');
    	$page = (I('post.page') - 1) * $pagesize;
    	$keyword = I('post.keyword');
    	$differentiate = I('post.differentiate');
		
    	$ccp_communist = M('ccp_communist');
		$where['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
		if(!empty($party_no)){
			$party_no_arr = getPartyChildMulNos($party_no,'arr');
			$where['party_no']=array('in',$party_no_arr);
		}else{
			$where['party_no']=array('in',session('party_no_auth'));
		}
		if(!empty($keyword)){ // 关键字查询
			$keyword_map['communist_name'] = array('like','%'.$keyword.'%');
			$keyword_map['communist_mobile'] = array('like','%'.$keyword.'%');
			
			if($keyword=='男'){				
				$keyword_map['communist_sex'] = 1;
			}else if($keyword=='女'){				
				$keyword_map['communist_sex'] = 0;
			}			
			//$keyword_map['article_description'] = array('like','%'.$keyword.'%');
			//$keyword_map['article_content'] = array('like','%'.$keyword.'%');
			$keyword_map['_logic'] = 'or';
		}
		if(!empty($keyword_map)){ // 拼接where条件
			$where['_complex'] = $keyword_map;
		}
		if(!empty($differentiate)){
			switch ($differentiate){
				// case 1: //党员
				  // break;  
				case 2://支部书记
				$where['_string'] = "FIND_IN_SET('201',post_no)";//支部书记
				  break;
				case 3://年轻党员
				$where['age_distribute'] = "1";//年轻党员
				  break;  
				case 4://中年党员
				$where['age_distribute'] = "2";//中年党员
				  break;
			}
		}
		$communist_list = M('ccp_communist')->where($where)->limit($page,$pagesize)->select();
		$data['count'] = M('ccp_communist')->where($where)->count();		
		$content = '';
        foreach ($communist_list as &$communist) {
			$communist['communist_sex'] = $communist['communist_sex'] == 1 ? '男' : '女';
			if(!empty($communist['communist_birthday'])){
				$communist_age = M()->query("select (year(now())-year(communist_birthday)-1) + ( DATE_FORMAT(communist_birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age from sp_ccp_communist where  communist_no = '$communist[communist_no]' ");
				$communist['communist_age'] = $communist_age[0][age];
			}else{
				$communist['communist_age'] = 0;
			}
			$communist['party_name'] = getPartyInfo($communist['party_no']);
			$meeting_count = M('oa_meeting_communist')->where("communist_no = $communist[communist_no]")->count('distinct meeting_no');
        	if(!$meeting_count){
				$meeting_count = 0;
			}
			if(!$communist['communist_avatar'] || !file_exists(SITE_PATH.$communist['communist_avatar'])){
				$communist['communist_avatar'] = '/statics/public/images/default_photo.jpg';
			}
            $communist['communist_integral'] = floor($communist['communist_integral']);

			$content .= "
				<li class='clearfix' style='cursor:pointer;' onclick='detail($communist[communist_no])'>
					<div class='pull-left w-70b'>
						<div class='clearfix'>
							<div class='color-333 wryh_blod f-30em-cur pull-left w-78b' >{$communist['communist_name']}
								<span class='f-24em-cur color-666 wryh'>{$communist['communist_sex']}</span>
								<span class='f-24em-cur color-666 wryh'>{$communist['communist_age']}岁</span>
							</div>
							<div onclick='event.cancelBubble = true'>
								<img class='dis-in pull-left' style='width: 0.26rem;height: 0.26rem;cursor:pointer;' src='/statics/apps/page_index/img/bj1.png' alt='' onclick='edit($communist[communist_no])'  >
								<img class='dis-in pull-right' style='width: 0.23rem;height: 0.27rem;cursor:pointer;' src='/statics/apps/page_index/img/sc1.png' alt='' onclick='del($communist[communist_no])'>
							</div>
						</div>
						<div class='f-24em-cur color-666 wryh mt-2em-cur'>{$communist['party_name']}</div>
						<div class='f-24em-cur color-666 wryh mt-5em-cur'>
							<span>积分：<span class='color-ef311e'>{$communist['communist_integral']}</span></span>   
							<span class='ml-04em-cur'>活动：<span class='color-ef311e'>{$meeting_count}</span></span>
							
						</div>
					</div>
					<div class='pull-right' onclick='detail($communist[communist_no])'  style='cursor:pointer;'>
						<img style='width: 1.32rem;height: 1.76rem;border-radius: 0.07rem !important;' src='{$communist[communist_avatar]}' alt=''>
					</div>
				</li>";
        }
        $data['content'] = $content;
       
		ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }
    public function ccp_communist_index_data_data(){
        $party_no=I('get.party_no');//点击的党支部编号
        $status=I('get.status','');
        $post_no=I('get.post_no');
        $communist_diploma=I('get.communist_diploma');
        $communist_sex=I('get.communist_sex');
        $communist_name=I('get.communist_name');
        $communist_no=I('get.communist_no');
        $phone = I('get.phone');
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
        $ccp_communist = M('ccp_communist');
        if(empty($status)){
            $status=COMMUNIST_STATUS_OFFICIAL;//只查询正式党员
        }
        $communist_list=getCommunistList($party_no,'arr','1',$post_no,$status,$communist_name,$communist_no,'',$communist_diploma,$communist_sex,$start,$end,$phone,'',$page,$pagesize);
        
        foreach ($communist_list['data'] as &$communist) {
            /*$communist['communist_name'] = "<a href='".U('ccp_communist_info',array('communist_no'=>$communist['communist_no'],'communist_type'=>$communist['communist_type']))."' class=' fcolor-22 '>".$communist['communist_name']."</a>";*/

            $confirm = 'onclick="if(!confirm(' . "'是否连账号一起删除？'" . ')){return false;}"';
            $button="<a class='btn btn-xs red btn-outline' href='".U('ccp_communist_do_del',array('communist_no'=>$communist['communist_no'],'communist_type'=>$communist['communist_type']))."'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
            if(!empty($communist['communist_ccp_date'])){
                $communist['communist_ccp_date']=getFormatDate($communist['communist_ccp_date'],"Y-m-d");
            }
            $communist['operate'] ="<a class='btn btn-xs blue btn-outline' href='" . U('ccp_communist_edit', array('communist_no' => $communist['communist_no'],'communist_type'=>$communist['communist_type'])) . "'><i class='fa fa-edit'></i> 编辑</a>  ".$button;
            if(empty($communist['communist_tel'])){
                $communist['communist_tel']='暂无手机号';
            }
        }
        $communist_list['code'] = 0;
        $communist_list['msg'] = 0;
        ob_clean();$this->ajaxReturn($communist_list); // 返回json格式数据
    }
	/**
     * @name:ccp_communist_count_ajax
     * @desc：人员count总数
     * @author：王世超
     * @addtime:2016-09-19
     * @version：V1.0.0
     **/
    public function ccp_communist_count_ajax(){
        $party_no=I('post.party_no');//点击的党支部编号
		$keyword = I('post.keyword');
    	$differentiate = I('post.differentiate');
    	$ccp_communist = M('ccp_communist');
		$where['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
		if(!empty($party_no)){
			$party_no_arr = getPartyChildMulNos($party_no,'arr');
			$where['party_no']=array('in',$party_no_arr);
		}else{
			$where['party_no']=array('in',session('party_no_auth'));
		}	
		if(!empty($keyword)){ // 关键字查询
			$keyword_map['communist_name'] = array('like','%'.$keyword.'%');
			$keyword_map['communist_mobile'] = array('like','%'.$keyword.'%');
			if($keyword=='男'){				
				$keyword_map['communist_sex'] = 1;
			}else if($keyword=='女'){				
				$keyword_map['communist_sex'] = 0;
			}
			//$keyword_map['article_description'] = array('like','%'.$keyword.'%');
			//$keyword_map['article_content'] = array('like','%'.$keyword.'%');
			$keyword_map['_logic'] = 'or';
		}
		if(!empty($keyword_map)){ // 拼接where条件
			$where['_complex'] = $keyword_map;
		}
		if(!empty($differentiate)){
			switch ($differentiate){
				// case 1: //党员
				  // break;  
				case 2://支部书记
				$where['_string'] = "FIND_IN_SET('201',post_no)";//支部书记
				  break;
				case 3://年轻党员
				$where['age_distribute'] = "1";//年轻党员
				  break;  
				case 4://中年党员
				$where['age_distribute'] = "2";//中年党员
				  break;
			}
		}
		$data['count'] = M('ccp_communist')->where($where)->count();
		ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }    
	/**
	* @name:ccp_communist_import_index
	* @desc：人员导入
	* @author：王世超
	* @addtime:2016-09-19
	* @version：V1.0.0
	**/
    public function ccp_communist_import_index(){
        $type = I('get.type');
        if(!empty($type)){
            $this->assign('type',$type);
        }else{
            $this->assign('type','0');
        }
        $import_count = M('ccp_import')->where("import_type=2")->count();
        $this->assign('import_count',$import_count);
        $this->display("Ccpcommunist/ccp_communist_import_index");
    }
    /**
     * @name:ccp_communist_basic_data
     * @desc：人员导出基础数据
     * @author：王宗彬
     * @addtime:2017-08-14
     * @version：V1.0.0
     **/
    public function ccp_communist_basic_data(){
        $communist_nation = M('bd_code')->where("code_group = 'communist_nation_code'")->field("code_no,code_name")->select();
        $diploma_level = M('bd_code')->where("code_group = 'diploma_level_code'")->field("code_no,code_name")->select();
        $data = array_merge($communist_nation,$diploma_level);
        $head['code_no']='编号';
        $head['code_name']='名称';
        exportExcel("基础数据",$head,$data);
    }
    /**
     * @name:ccp_communist_import_do_save
     * @desc：人员导入
     * @author：刘丙涛
     * @addtime:20170527
     * @version：V1.0.0
     **/
    public function ccp_communist_import_do_save(){
         $type = I('post.type');
        $db_party = M('ccp_party');
        $file = $_FILES['file_stu'];
        if (!empty($file['tmp_name']))
        {
            $upload = new \Think\Upload();
            $upload->maxSize = 0; // 不限制附件上传大小
            $upload->exts = array('xls','xlsx'); //
            $upload->rootPath = C('TMPL_PARSE_STRING')['__UPLOAD_PATH__']; // 上传文件所在文件夹
            $upload->savePath = 'ccp/fa/xls/'; // 设置附件上传目录
            $upload->autoSub = true; // 开启子目录保存
            $info = $upload->upload();
            if (!$info) {
                $this->error($upload->getError());
            }
            import("Org.Util.PHPExcel");
            //Vendor('PHPExcel.PHPExcel.IOFactory');
            import("Org.Util.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $file_name= C('TMPL_PARSE_STRING')['__UPLOAD_PATH__'].$info['file_stu']['savepath'].$info['file_stu']['savename'];
            $extension = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );
            if ($extension =='xlsx') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            } else if ($extension =='xls') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            }
            $path=BASE_PATH.$file_name;
            try {
                $objPHPExcel = $objReader->load($path);
            } catch (Exception $e) {
                showMsg('error', '文件格式与扩展名不匹配，请检查后重新上传！');
            }
            $objPHPExcel = $objReader->load($file_name);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            $db_import = M('ccp_import');
            $m = 0;
            for($i=2;$i<=$highestRow;$i++) {
                if(!empty($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))
                {
                    $data['communist_name'] = (string)$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();//姓名
                    $data['party_no'] = (string)$objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                    $no = (string)$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();//党员编号
                    if(empty(trim($no))){
                        //if($i=='2'){
						$no = getFlowNo($data['party_no'], 'ccp_communist', 'communist_no', '4');
                        //}
                    }
                    $data['communist_no'] = $no;
                    $data['communist_sex'] = (string)$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue()=='男'?1:0;//性别
                    $data['communist_nation'] = (string)$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();//民族名称
                    $data['communist_birthday'] = (string)$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();//出生日期
                    $data['communist_idnumber'] = (string)$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();//身份证
                    $data['communist_paddress'] = (string)$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();//籍贯
                    $data['communist_address'] = (string)$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();//现住址
                    $data['communist_diploma'] = (string)$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();//学历
                    $data['communist_specialty'] = (string)$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();//专业
                    $data['communist_school'] = (string)$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();//学校
                    $data['communist_graduate_date'] = (string)$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();//参加工作时间(毕业时间)
                    $data['communist_tel'] = (string)$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();//电话
                    $data['communist_mobile'] = (string)$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();//手机号
                    $data['communist_email'] = (string)$objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();//邮箱
                    $data['communist_qq'] = (string)$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();//QQ
                    $data['communist_applytime'] = (string)$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();//申请入党时间
                    $data['communist_ccp_date'] = (string)$objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();//入党时间
                    $data['memo'] = (string)$objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();			

					$data['add_staff'] = session('staff_no');
					$data['status'] ='40';
					$data['add_time'] = date('Y-m-d H:i:s');
					$data['update_time'] = date('Y-m-d H:i:s');
					$comm_map['communist_idnumber'] = $data['communist_idnumber'];
					$res = M('ccp_communist')->where($comm_map)->find();
					if(!empty($res)){
						$flag = M('ccp_communist')->where($comm_map)->save($data);
					}else{
						$flag = M('ccp_communist')->add($data);
						saveCommunistLog($data['communist_no'], '10', '40', session('communist_no'));//添加党员发展历程
					}
                    savePeople($data['communist_no'],$data['communist_name'],$data['communist_idnumber'],1);
                }
            }
            if($flag){
                showMsg(success, '上传成功', U('ccp_communist_import_index',array('type'=>$type)));
            }else{
                showMsg(error, '导上传失败');
            }
        }else{
            showMsg(error, '上传的文件为空，请从新上传数据');
        }
    }
    /**
     * @name:ccp_communist_import
     * @desc：部门开始导入
     * @author：王宗彬
     * @addtime:2018-08-14
     * @version：V1.0.0
     **/ 
    public function ccp_communist_import(){
        $type = I('get.type');
        $db_import = M('ccp_import');
        $db_communist = M('ccp_communist');
        $import_data = $db_import->where('import_type=2')->select();
        foreach ($import_data as $data) {
            $communist_idnumber = $data['communist_idnumber'];
            $data['add_staff'] = session('staff_no');
            $data['status'] ='21';
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $comm_map['communist_idnumber'] = $communist_idnumber;
            $res = $db_communist->where($comm_map)->find();
            if(!empty($res)){
                $flag =$db_communist->where($comm_map)->save($data);
                if($flag){
                    $db_import->where($comm_map)->delete();
                }
            }else{
                $flag1 = $db_communist->add($data);
                if($flag1){
                    $db_import->where($comm_map)->delete();
                }
            }
        }
        // $flag1 = $db_communist->saveAll($list_save);
        // $flag = $db_communist->addAll($list_add);
        //if($flag || $flag1){
             showMsg(success, '导入成功', U('ccp_communist_import_index',array('type'=>$type)));
        //} //else{
        //     showMsg(error, '导入失败');
        // }
    }
    /**
     * @name:case_export_communist
     * @desc：部门导出错误信息
     * @author：王宗彬
     * @addtime:2018-08-14
     * @version：V1.0.0
     **/            
    public function case_export_communist(){
        $db_import = M('ccp_import');

        $import_data = $db_import->where("import_type=2")->field('communist_name,communist_no,communist_sex,communist_nation,communist_birthday,communist_idnumber,communist_paddress,communist_address,communist_diploma,communist_specialty,communist_school,communist_graduate_date,communist_tel,communist_mobile,communist_email,communist_qq,party_no,communist_applytime,communist_ccp_date,memo')->select();
        $db_import->where("import_type=2")->delete();
        $head['communist_name'] = "党员姓名";//党员姓名
        $head['communist_no'] = "党员编号";//党员编号
        $head['communist_sex'] = "性别";//性别
        $head['communist_nation'] = "民族名称";//民族名称
        $head['communist_birthday'] = "出生日期";//出生日期
        $head['communist_idnumber'] = "身份证号";//身份证号
        $head['communist_paddress'] = "籍贯"; //籍贯
        $head['communist_address'] = "现住址";//现住址
        $head['communist_diploma'] = "学历"; //学历
        $head['communist_specialty'] = "专业";//专业
        $head['communist_school'] = "毕业院校";//学校
        $head['communist_graduate_date'] = "毕业时间";//参加工作时间(毕业时间)
        $head['communist_tel'] = "党员电话"; //电话
        $head['communist_mobile'] = "党员手机";//手机号
        $head['communist_email'] = "邮箱"; //邮箱
        $head['communist_qq'] = "QQ号"; //QQ
        $head['party_no'] = "支部名称"; //
        $head['communist_applytime'] = "申请入党时间"; //申请入党时间
        $head['communist_ccp_date'] = "正式入党时间"; //入党时间
        $head['memo'] = "备注";//备注
        exportExcel("党员",$head,$import_data);
    }
    /**
     * @name:ccp_communist_edit
     * @desc：人员添加/编辑，添加为_add
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_edit(){
    	$type = I('get.type','1');//用于区分党员管理进入还是党员纪实进入
    	switch ($type) {
    		case 1:$cat = '_2';break;   // 党员党员添加
    		case 2:$cat = '_1';break;   // 党员关系转入添加
    		case 3:$cat = '_3';break;   //党员发展全纪实党员添加
    		default:$cat = '_4';break;   // 党员关系流入添加
    	}
    	session('cat',$cat);
        checkAuth(ACTION_NAME.session('cat'));
        $communist_no = I('get.communist_no');
        $party_no = I('get.party_no');
        $communist_type = I('get.communist_type');
        $party_type = getPartyInfo($party_no,'party_type');
        if(!empty($party_no)){
            $this->assign('party_type',$party_type);
        }else{
            $this->assign('party_type',$communist_type);
        }
        $ccp_communist = M('ccp_communist');
        if (! empty($communist_no)) {
            $communist = getCommunistInfo($communist_no,'all');
            if(!empty($communist['communist_birthday'])){
                $communist['communist_birthday']=getFormatDate($communist['communist_birthday'],"Y-m-d");
            }
            $communist['communist'] = $communist['communist_no'];
        	$communist['status_nos'] = COMMUNIST_STATUS_OFFRETIRE;
            $this->assign('action','edit');
        }else{
        	switch ($type) {
        		case 1:
        			$communist['status']=21;
        			$communist['status_nos'] = COMMUNIST_STATUS_OFFRETIRE;
        		;break;
        		case 2:
        			$communist['status']=13;
        			$communist['status_nos'] = COMMUNIST_STATUS_OFFICIAL;
        			;break;
        		case 3:
        			$communist['status_nos'] = COMMUNIST_STATUS_DEVELOP;
        			;break;
        		case 4:
        			$communist['status']=22;
        			$communist['status_nos'] = COMMUNIST_STATUS_OFFICIAL;
        			;break;
        	}
            $communist['party_no'] = $party_no;
        }
        // if(empty($communist['communist_no'])){
        //     if(!empty($party_no)){
        //         $prefix_party_no = $party_no;
        //     } else {
        //         $party_no_auth = session('party_no_auth');//取本级及下级组织
        //         $party_map['status'] = 1;
        //         $party_map['party_no'] = array('in',$party_no_auth);
        //         $prefix_party_no = M('ccp_party')->where($party_map)->order('party_no asc')->limit(1)->getField('party_no');
        //     }
        // 	$communist['communist_no'] = getFlowNo($prefix_party_no, 'ccp_communist', 'communist_no', '4');
        // }
        $this->assign('communist', $communist);
        $this->assign('type',$type);
        $this->display("Ccpcommunist/ccp_communist_edit");
    }
    /**
     * @name:ccp_communist_info_persondetail
     * @desc：党员画像
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_info_persondetail(){
        checkAuth(ACTION_NAME);
        $communist_no = I('get.communist_no');
        $ccp_communist = M('ccp_communist');
        $communist_type = I('get.communist_type');
        $this->assign('communist_type',$communist_type);
        $date = date('Y-m-d');
        $this->assign('date',$date);
        $this->assign('type',$type);
        if (! empty($communist_no)) {
            $communist = getCommunistInfo($communist_no,'all');
            $communist['communist_sex'] = $communist['communist_sex']?男:女;
            if ($communist){
                $ranking = M()->query("select integral.rank from (select @rownum:=@rownum+1 rank,`sp_ccp_communist`.* from (select @rownum:=0) a, `sp_ccp_communist` where status=1 order by `communist_integral` desc,`add_time`) integral where integral.communist_no = '$communist_no'");
                $party_ranking = M()->query("select integral.rank from (select @rownum:=@rownum+1 rank,`sp_ccp_communist`.* from (select @rownum:=0) a, `sp_ccp_communist` where status=1 and party_no = '".getCommunistInfo($communist_no,'party_no')."' order by `communist_integral` desc,`add_time`) integral where integral.communist_no = '$communist_no'");
                $communist['ranking'] = $ranking['0']['rank'];
                $communist['party_ranking'] = $party_ranking['0']['rank'];
                $communist_source = getBdCodeInfo($communist['communist_source'],'communist_source_code');
                $communist_phone = json_decode($communist['setting'],true);
                $this->assign('communist_source',$communist_source);
            }
			if(!empty($communist['communist_diploma'])){
				$communist['communist_diploma'] = getBdCodeInfo($communist['communist_diploma'],"diploma_level_code");
			}else{
				$communist['communist_diploma'] = '无';
			}
			$communist['party_no']=getPartyInfo($communist['party_no']);
            $communist['post_no']=getPartydutyInfo($communist['post_no']);
            if(!empty($communist['communist_honor'])){
                $communist['communist_honor_name'] = getBdCodeInfo($communist['communist_honor'],'communist_honor_code');
            }else{
                $communist['communist_honor_name'] = "无";
            }
            if(!empty($communist['communist_label'])){
                $communist['communist_label'] = getBdCodeInfo($communist['communist_label'],'communist_label_code');
            }
            $communist['communist_nation'] = getTableInfo('bd_nation', 'nation_id', $communist['communist_nation'],'nation_name');
			if(!empty($communist['age_distribute'])){
				$communist['age_distribute'] = getBdCodeInfo($communist['age_distribute'],'age_distribute_code');
			}else{
				$communist['age_distribute'] = '党员';
			}
            $communist_data = M()->query("select (year(now())-year(communist_ccp_date)-1) + ( DATE_FORMAT(communist_ccp_date, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as ccp_age from sp_ccp_communist where communist_no = $communist_no ");
            $communist['ccp_age'] = $communist_data[0]['ccp_age'];
            $communist_label_code = explode(',',$communist['communist_label']);
            $one = $communist_label_code[0];
            $this->assign('one',$one);
            $two = $communist_label_code[1];
            $this->assign('two',$two);
            $three = $communist_label_code[2];
            $this->assign('three',$three);
            $four = $communist_label_code[3];
            $this->assign('four',$four);
            $five = $communist_label_code[4];
            $this->assign('five',$five);
            $six = $communist_label_code[5];
            $this->assign('six',$six);
            $communist['communist_integral'] = number_format($communist['communist_integral']);
            if($communist['communist_sex']=="男"){
                $communist['communist_sex_ar'] = "".__ROOT__."/statics/apps/page_index/img/center.png";
            }else{
                $communist['communist_sex_ar'] = "".__ROOT__."/statics/apps/page_index/img/nv.png";
            }		
            $this->assign('communist', $communist);

            $meeting_communist_count = M("oa_meeting_communist")->where("communist_no=$communist_no")->count();
            $this->assign("meeting_communist_count",$meeting_communist_count);
            //dangfei
            $dues_amount = M('ccp_dues')->where("communist_no=$communist_no")->sum("dues_amount");
            $dues_amount = preg_replace('/\.0+$/', '', $dues_amount);
            if(!$dues_amount){
				$dues_amount = 0;
			}
			$this->assign("dues_amount",$dues_amount);

            //学习笔记
            $material_log_count = M('edu_material_log')->where("communist_no=$communist_no and log_type=2")->count();
            if(!$material_log_count){
				$material_log_count = 0;
			}
			$this->assign("material_log_count",$material_log_count);
            
        }
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
        $this->display("Ccpcommunist/ccp_communist_info_persondetail");
    }
    /**
     * @name:ccp_communist_info
     * @desc：人员详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_info(){
        $type = I('get.type','1');//用于区分党员管理进入还是党员纪实进入
        $is_type = I('get.is_type');
        $this->assign('is_type',$is_type);
        switch ($type) {
            case 1:$cat = '_2';break;   // 党员党员添加
            case 2:$cat = '_1';break;   // 党员关系转入添加
            case 3:$cat = '_3';break;   //党员发展全纪实党员添加
            default:$cat = '_4';break;   // 党员关系流入添加
        }
        if(empty(session('cat'))){
            session('cat','_2');
        }
        checkAuth(ACTION_NAME.session('cat'));
        $communist_no = I('get.communist_no');
        $ccp_communist = M('ccp_communist');
        $db_communist_change = M('ccp_communist_change');
        $communist_type = I('get.communist_type');
        $this->assign('communist_type',$communist_type);
        $date = date('Y-m-d');
        $this->assign('date',$date);
        $this->assign('type',$type);
        if (! empty($communist_no)) {
            $communist = getCommunistInfo($communist_no,'all');
            $communist['communist_diploma'] = getBdCodeInfo($communist['communist_diploma'],"diploma_level_code");
			$communist['age_distribute'] = getBdCodeInfo($communist['age_distribute'],'age_distribute_code');
            $communist['party_no']=getPartyInfo($communist['party_no']);
            $communist['post_no'] = getPartydutyInfo($communist['post_no']);
            $communist['communist_integral'] = (int)$communist['communist_integral'];
			$communist['communist_labels'] = $communist['communist_label'];
            if(!empty($communist['communist_label'])){
				$communist['communist_label'] = M('bd_code')->where("code_group = 'communist_label_code' and code_no in($communist[communist_label]) ")->field("code_no,code_name,memo")->select();
            }
			if(!$communist['communist_avatar'] || !file_exists(SITE_PATH.$communist['communist_avatar'])){
				$communist['communist_avatar'] = '/statics/public/images/default_photo.jpg';
			}
            if(!empty($communist['communist_birthday'])){
				$age = M()->query("select (year(now())-year(communist_birthday)-1) + ( DATE_FORMAT(communist_birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age from sp_ccp_communist where  communist_no = '$communist[communist_no]' ");
                $communist['age']= $age[0]['age'];
            }else{
				$communist['age']=0;
			}
			if(!empty($communist['communist_ccp_date'])){
				$ccp_age = M()->query("select (year(now())-year(communist_ccp_date)-1) + ( DATE_FORMAT(communist_ccp_date, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as ccp_age from sp_ccp_communist where  communist_no = '$communist[communist_no]' ");	
                $communist['ccp_age']= $ccp_age[0]['ccp_age'];
            }else{
				$communist['ccp_age']=0;
			}
            $communist['communist_nation'] = getTableInfo('bd_nation', 'nation_id', $communist['communist_nation'],'nation_name');
			//$communist[''] = M('')->where("communist_no='$communist[communist_no]'")->;
			$communist['meeting_communist_count'] = M("oa_meeting_communist")->where("communist_no='$communist[communist_no]'")->count('distinct meeting_no');
        	$communist['dues_amount'] = M('ccp_dues')->where("communist_no='$communist[communist_no]'")->sum("dues_amount");
            $communist['dues_amount'] = preg_replace('/\.0+$/', '', $communist['dues_amount']);
			$communist['notes_count'] = M('edu_notes')->where("add_staff='$communist[communist_no]'")->count();
			if(!$communist['meeting_communist_count']){
				$communist['meeting_communist_count'] = 0;
			}
			if(!$communist['dues_amount']){
				$communist['dues_amount'] = 0;
			}
			if(!$communist['notes_count']){
				$communist['notes_count'] = 0;
			}
			$this->assign('communist', $communist);
            //党员历程
			
			//communist_log_type
			$communist_log = M ('bd_type')->where("type_group='communist_log_type'")->field("type_no,type_name")->select();//getBdTypeList('');	
			foreach($communist_log as &$log){
				$log['log_count'] = sizeof(getCommunistLogList($communist_no,$log['type_no']));
				
				switch ($log['type_no']) {
					case '11':
						$log['type_name'] = '转移';
						break;
					case '12':
						$log['type_name'] = '流动';
						break;
					case '21':
						$log['type_name'] = '党员状态';
						break;
				}	
			}
            $this->assign('communist_log',$communist_log);
			$dev_log_list = getCommunistLogList($communist_no);//党员历程
            $this->assign('dev_log_count',sizeof($dev_log_list));
            $this->assign('dev_log_list',$dev_log_list);
        }        
        $this->display("Ccpcommunist/ccp_communist_info");
    }
	/**
	* @name:ccp_communist_label_add
	* @desc：人员标签
	* @author：wangzongbin
	* @addtime:2017-11-22
	* @version：V1.0.0
	**/
	public function ccp_communist_label_add(){
		$communist_no = I('get.communist_no');
		$this->assign('communist_no',$communist_no);
		$communist_label = getCommunistInfo($communist_no,'communist_label');
		$this->assign('communist_label',$communist_label);
		$this->display("Ccpcommunist/ccp_communist_label_add");
	}
	/**
	* @name:ccp_communist_label_save
	* @desc：人员标签
	* @author：wangzongbin
	* @addtime:2017-11-22
	* @version：V1.0.0
	**/
	public function ccp_communist_label_save(){
		$post = I('post.');
		$where['communist_no'] = $post['communist_no'];
		$post['update_time'] = date('Y-m-d H:i:s');
		$result = M('ccp_communist')->where($where)->save($post);
		if ($result) {
			showMsg('success', '操作成功！！！',  U('Ccpcommunist/ccp_communist_info',array('communist_no'=>$communist_no)),1);
        } else {
            showMsg('error', '操作失败！！！','');
        }
	}
	/**
     * @name:ccp_communist_info_log
     * @desc：历程ajax
     * @author：wangzongbin
     * @addtime:2016-09-19
     * @version：V1.0.0
     **/
    public function ccp_communist_info_log(){
        $type_no=I('post.type_no');//点击的党支部编号
		$communist_no = I('post.communist_no');;
		$data['content'] ="";
		$log_list= getCommunistLogList($communist_no,$type_no);
		foreach ($log_list as &$log) {
			$data['content'] .= "<li class='layui-timeline-item'>
				<i class='layui-icon layui-timeline-axis'>
					<img class='dis-in ml-02mem-cur' style='width: 0.25rem;height: 0.26rem;' src='/statics/apps/page_index/img/yuan.png'>
				</i>
				<div class='layui-timeline-content layui-text'>
					<h3 class='layui-timeline-title'>{$log['add_time']}</h3>
					<p>{$log['log_content']}</p>
				</div>
			</li>";
		}
		ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }
    /**
     * @name:ccp_communist_do_save
     * @desc：人员执行操作
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_do_save(){
        checkLogin();
        $communist_no = I('post.communist');	//判断添加还是修改
        $data = I("post.");	
        $ccp_communist = M('ccp_communist');
        $type = I('post.type');
        if( is_numeric($data['communist_avatar']) ){ 
            $data['communist_avatar'] = getUploadInfo($data['communist_avatar']);
        }
        if (!empty($communist_no)) {//修改
        	$data['update_time'] = date('Y-m-d H:i:s');
        	$data['communist_initial'] = getFirstCharMul($data['communist_name']);
            $comm_map['communist_no'] = $communist_no;
            $result = $ccp_communist->where($comm_map)->save($data);
            $log_type_name='修改';//添加系统日志操作名称
            $log_type=2;//系统日志类型编号
        } else { 
        	//添加
            $communist_no=getFlowNo($data['party_no'], 'ccp_communist', 'communist_no', '4');
            if (!empty($communist_no)) {
                if (! checkRepeat('ccp_communist', 'communist_no', $data['communist_no'])) {
                    $data['add_time'] = date('Y-m-d H:i:s');
                    if(empty($data['communist_avatar'])){
                        $data['communist_avatar'] =null;
                    }
                    if(empty($data['communist_remindtime' ])){
                    	$data['communist_remindtime'] =null;
                    }
                    $data['communist_no'] = $communist_no;
                    $data['communist_initial'] = getFirstCharMul($data['communist_name']);
                    $data['add_staff'] = $this->staff_no;
                    $data['approve_status'] = 21;
                    $result = $ccp_communist->add($data);
                    $log_attach = 0 ;
                   	if(!empty($data['log_attach'])){
                   		$attach = $data['log_attach'];
                   		$log_attach =  str_replace('`','_',$data['log_attach']);
                   	}
					
					//	saveCommunistLog($communist_no, '10', '21', session('staff_no'));//添加党员发展历程
					
         //            if($result && $type == 3){
         //                $content = $data['communist_name']."状态改为". removeHtml(getStatusName('communist_status',$data['status']))."请审核";
         //            	$approval_arr=array(
	    				// 	'approval_template'=>$data['approval_template'],
	    				// 	'approval_name'=>getStaffInfo($this->staff_no).'发起的党员发展审核',
	    				// 	'approval_apply_man'=>$this->staff_no,
	    				// 	'approval_table_name'=>'ccp_communist',
	    				// 	'approval_table_field'=>'communist_no',
	    				// 	'approval_table_field_value'=>$data['communist_no'],
	    				// 	'approval_rewrite_field'=>'approve_status',
	    				// 	'party_no'=>$data['party_no'],
	    				// 	'approval_attach'=> $attach,
         //            	    'approval_content'=>$content,
	    				// 	'approval_callfunction'=>"callsaveCommunist(".$data['communist_no'].",".$data['status'].",'".$log_attach."',".$this->staff_no.",$".status.",$".node_staff.");"
    					// );
         //            	saveOaApproval($approval_arr);
                    	
                        $log_type_name='新增';//添加系统日志操作名称
                        $log_type=1;//系统日志类型编号
                        if($type == '3'){
                            saveCommunistLog($communist_no, 10, $data['status'], session('staff_no'));//添加党员发展历程
                            $fazh='发展';
                        }else{
							saveCommunistLog($communist_no, '10', $data['status'], session('staff_no'));//添加党员发展历程
						}
                //    }
                } else {
                    showMsg('error', '员工编号已存在！！,请重新添加','');
                }
            } else {
                showMsg('error', '必填字段为空！！,请重新添加','');
            }
        }
        if ($result) {
            //添加系统日志
            savePeople($data['communist_no'],$data['communist_name'],$data['communist_idnumber'],1);
            saveLog(ACTION_NAME,1,'','操作员['.getStaffInfo($this->staff_no).']于'.date("Y-m-d H:i:s").$log_type_name.'党员'.$fazh.'信息，编号为['.$communist_no.']，姓名为['.$data['communist_name'].']');
            if($type == '3'){
                showMsg('success', '操作成功！！！',U('Ccpcommunist/ccp_communist_develop_index'),1);
            }else{
                showMsg('success', '操作成功！！！',  U('Ccpcommunist/ccp_communist_index'),1);
				
            }
        } else {
            showMsg('error', '操作失败！！！','');
        }
    }
    
	
	/**
     * @name:ccp_communist_status_data_save
     * @desc：人员状态修改
     * @author：王宗彬
     * @addtime:2016-08-30
     * @version：V1.0.0
     **/
    public function ccp_communist_status_data_save(){
    	$communist_no = I('post.communist_no');
    	$ccp_communist = M('ccp_communist');
    	$post = I('post.');
    	if(!empty($communist_no)){
    		$post['update_time'] = date('Y-m-d H:i:s');
            $comm_map['communist_no'] = $communist_no;
	    	$result = $ccp_communist->where($comm_map)->save($post);
	    	saveCommunistLog($communist_no, 21, $post['status'], $this->staff_no);//添加党员发展历程
	    	//添加系统日志
	    	if($result){
	    		$post['communist_name'] = getCommunistInfo($post['communist_no']);
	    		saveLog(ACTION_NAME,1,'','操作员['.getStaffInfo($this->staff_no).']于'.date("Y-m-d H:i:s").'修改党员状态信息，编号为['.$communist_no.']，姓名为['.$post['communist_name'].']');
	    		showMsg('success', '操作成功！',U('Ccpcommunist/ccp_communist_index'),'1');
	    	}else{
	    		showMsg('error', '操作失败！','');
	    	}
	    }
    }
    /**
     * @name:ccp_communist_check_idnumber
     * @desc：判断是否存在身份证
     * @author：王宗彬
     * @addtime:2016-08-30
     * @version：V1.0.0
     **/
    public function ccp_communist_check_idnumber(){
        $communist_idnumber = I('post.communist_idnumber');
        $communist_no = I('post.communist_no');
        if(!empty($communist_no)){
            $this->ajaxReturn(false);
        }else{
            $comm_map['communist_idnumber'] = $communist_idnumber;
            $ishas = M('ccp_communist')->where($comm_map)->count();
            if ($ishas >= 1){
                $this->ajaxReturn(true);
            }else{
                $this->ajaxReturn(false);
            }  
        }
    }
    /**
     * @name:ccp_communist_do_del
     * @desc：人员删除
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_do_del(){

        checkAuth(ACTION_NAME);
        $communist_no = I('get.communist_no');
        $ccp_communist = M('ccp_communist');
        $type = I('get.type');
        $is_history = I('get.is_history');
		$communist_idnumber = getCommunistInfo($communist_no,'communist_idnumber');
        if (! empty($communist_no)) {
            $comm_map['communist_no'] = $communist_no;
            $result = $ccp_communist->where($comm_map)->delete();
        }
        if ($result) {
			$where['people_no'] = $communist_no;
			$where['people_card'] = $communist_idnumber;
			M('ccp_people')->where($where)->delete();
            if($type){
                $url=U('Ccpcommunist/ccp_communist_develop_index');
            }else{
                $user_map['user_relation_no'] = $communist_no;
            	M('sys_user')->where($user_map)->delete();
                $url=U('Ccpcommunist/ccp_communist_index');
            }
            if($is_history){
                $url=U('Ccpcommunistchange/ccp_communist_history_index');
            }
            showMsg('success', '操作成功！！！',$url);
        } else {
            showMsg('error', '操作失败！！！','');
        }
    }
    /**
     * @name:ccp_communist_develop_index
     * @desc：党员发展全纪实    
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_develop_index(){
    	checkAuth(ACTION_NAME);
    	$cat = '_3';  
    	session('cat',$cat);
    	$status = I('get.status','11');
        $status_list = getStatusList('communist_status',COMMUNIST_STATUS_DEVELOPMENT);
    	$this->assign('status_list', $status_list);
    	$this->assign('status',$status);
    	$this->assign('type',3);
    	$this->display("Ccpcommunist/ccp_communist_develop_index");
    }
    /**
     * @name:ccp_communist_develop_index_data
     * @desc：党员发展数据
     * @author：王宗彬
     * @addtime:2017-10-19
     * @version：V1.0.0
     **/
    public function ccp_communist_develop_index_data(){
    	$status = I('get.status','11');
    	if(empty($status)){
    		$status = COMMUNIST_STATUS_DEVELOP;
            $communist_source = '3';
    	} 
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
    	$communist_name = I('get.communist_name','');
    	$party_no = I('get.party_no','');
    	if($status == 21){
    		$communist_source = '3';
    	}

    	$type = I('get.type');  // 3 党员发展全纪实
    	$communist_list = getCommunistList($party_no,'arr','1','',$status,$communist_name,'','','','','','','',$communist_source,$page,$pagesize);
    	$status_map['status_group'] = 'approval_status';
    	$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
    	$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
        if(!empty($communist_list['data'])){
            foreach ($communist_list['data'] as &$communist) {
                $communist['communist_type'] = $type;
                $communist['add_time'] = getFormatDate($communist['add_time'],"Y-m-d");
                $communist['communist_remindtime'] = getFormatDate($communist['communist_remindtime'],"Y-m-d");
                
                if(!empty($communist['communist_ccp_date'])){
                    $communist['communist_ccp_date']=getFormatDate($communist['communist_ccp_date'],"Y-m-d");
                }
                $save = "";
                if (!empty($communist['approve_status']) && $communist['approve_status'] != 21) {
                    $communist['communiststaus'] = "<font color='" . $status_color_arr[$communist['approve_status']] . "'>" . $status_name_arr[$communist['approve_status']] . "</font> ";
                }
                // if($communist['status'] != 21 && $communist['approve_status'] == 21){  
                //     $save = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='change_status($communist_no)'>状态修改 </a>";
                // }
                // if ($communist['approve_status'] == 31) {
                //     $save = "<a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='change_status($communist_no)'>状态修改 </a>";
                // }
                // if($communist['approve_status'] == 11 || $communist['approve_status'] == 12){
                //     $save = "<a class='layui-btn  layui-btn-xs layui-btn-f60utline' href='".U('ccp_communist_info',array('communist_no'=>$communist_no,'type'=>$type))."'>审核 </a>";
                // }
                // if($communist['status'] == 21){
                //      $save = "";
                // } 
                // $communist['operate'] = $save.$button;
            }
            $communist_list['msg'] = 0;
            $communist_list['code'] = 0;
            ob_clean();$this->ajaxReturn($communist_list); // 返回json格式数据  
        }else{
            $communist_list['code'] = 0;
            ob_clean();$this->ajaxReturn($communist_list); // 返回json格式数据  
        }
    	
    }
    /**
     * @name:ccp_communist_develop_edit
     * @desc：新增/编辑发展纪实
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_develop_edit(){
    	checkAuth(ACTION_NAME);
    	$communist_no = I("get.communist_no");
    	$type = I('get.type'); //是否详情的状态修改
    	$this->assign("type",$type);
    	$ccp_communist = M('ccp_communist');
        $communist = getCommunistInfo($communist_no,'all');
        //页面状态下拉框重新赋值        
        switch ($communist['status']) {
            case '11':
                $status = '13';
                break;
            case '13':
                $status = '15';
                break;
            case '15':
                $status = '17';
                break;
            case '17':
                $status = '21';
                break;
        }
        $this->assign("status",$status);
        $communist['party_no'] = getPartyInfo($communist['party_no']);
        $communist['status'] = getStatusName("communist_status", $communist['status']);



        $communist['status_nos'] = COMMUNIST_STATUS_DEVELOP;
        $this->assign("communist",$communist);
    	$this->display("Ccpcommunist/ccp_communist_develop_edit");
    }
    /**
     * @name:ccp_communist_develop_save
     * @desc：党员发展纪实数据保存
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_develop_save(){
    	$post = $_POST;
    	$communist_log = M("ccp_communist_log");
    	$communist_model = D('Ccp_communist');	//实例化
    	//改变党员状态
    	if(!empty($post['communist_id'])){
    		if($post['status']!=21 && empty($_POST['communist_remindtime']) && empty($_POST['communist_remindstatus'])){
    			showMsg('success', '预警内容不能为空！！！',  U('Ccpcommunist/ccp_communist_develop_edit',array('communist_no'=>$_POST['communist_no'])));
    		}
    		if(empty($_POST['communist_remindtime'])){
    		    $post['communist_remindtime']=null;  //预警时间
    		}
    		if(empty($_POST['communist_remindstatus'])){
    		    $post['communist_remindstatus']=null; //预警内容
    		}
    		if(empty($_POST['communist_ccp_date'])){
    			$post['communist_ccp_date']=null;  //入党时间
    		}
    		$communist_data = $communist_model->updateData($post,"communist_id");
    		
    		$log_attach = 0 ;
    		if(!empty($post['log_attach'])){
    			$attach = $post['log_attach'];
    			$log_attach =  str_replace('`','_',$post['log_attach']);
    		}
    		if($communist_data){//发起审批
    			$approval_arr=array(
					'approval_template'=>$post['approval_template'],
					'approval_name'=>getStaffInfo($this->staff_no).'发起的党员发展审核',
					'approval_apply_man'=>$this->staff_no,
					'approval_table_name'=>'ccp_communist',
					'approval_table_field'=>'communist_no',
					'approval_table_field_value'=>$post['communist_no'],
					'approval_rewrite_field'=>'approve_status',
					'party_no'=>getCommunistInfo($post['communist_no'],'party_no'),
					'approval_attach'=> $attach,
					'approval_content'=>$post['log_content'],
					'approval_callfunction'=>"callsaveCommunist(".$post['communist_no'].",".$post['current_status'].",'".$log_attach."',".$this->staff_no.",$".status.",$".node_staff.");"
    			);
    			saveOaApproval($approval_arr);
    		}
    		//党员发展类型
    		$log_type=10;
    		if(in_array($post['status'], array(COMMUNIST_STATUS_HISTORY))){
    		    $log_type=21; 
    		    saveCommunistLog($post['communist_no'],$log_type,$post['status'],$this->staff_no,$name,$new_name,$log_attach);
    		}
    		
    		$alert_title = getCommunistInfo($post['communist_no'])."预计在".$post['communist_remindtime']."成为".getStatusInfo('communist_status',$post['communist_remindstatus'],status_value);
    		//$alert_url = U('ccp_communist_develop_info',array('communist_id'=>$post['communist_id']));
    		$alert_url = "Ccp/Ccpcommunist/ccp_communist_info/communist_no/".$post['communist_no'];
    		saveAlertMsg('11',getPartyManagerNos($post['communist_no']),$alert_url, $alert_title, $post['communist_remindtime'],'', '', $this->staff_no);
    	}
		if ($communist_data) {
			showMsg('success', '操作成功！！！',  U('ccp_communist_develop_index',array('status'=>$post['status'])),2);
		} else {
            showMsg('error', '操作失败！！！','');
        }
    	//$this->display("Ccpcommunist/ccp_communist_develop_edit");
    }
	/**
	 * @name:ccp_communist_group_index
	 * @desc：人员分组管理首页
	 * @author：王宗彬
	 * @addtime:2017-10-21
	 * @version：V1.0.0
	 **/
	public function ccp_communist_group_index(){
		checkAuth(ACTION_NAME);
		$this->display("Ccpcommunist/ccp_communist_group_index");
	}
	/**
	 * @name:ccp_group_index_data
	 * @desc：人员分组管理首页数据
	 * @author：王宗彬
	 * @addtime:2017-10-21
	 * @version：V1.0.0
	 **/
	public function ccp_communist_group_index_data(){
		$ccp_group = M('ccp_group');
		$group_list = $ccp_group->order('add_time desc')->select();
		$num = 0;
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($group_list as &$group) {
			$num ++;
			$group['add_staff'] = $staff_name_arr[$group['add_staff']];
			$group['num'] = "<a href='" . U('ccp_communist_group_info', array('group_id' => $group['group_id'])) . "' class=' fcolor-22 '>".$num."</a>";
			$group['group_name'] = "<a href='" . U('ccp_communist_group_info', array('group_id' => $group['group_id'])) . "' class=' fcolor-22 '>".$group['group_name']."</a>";
			$group['operate'] = 
					"<a class='btn btn-xs blue btn-outline' href='" . U('ccp_communist_group_edit', array('group_id' => $group['group_id'])) . "'><i class='fa fa-edit'></i> 编辑</a>  " . 
					"<a class='btn btn-xs red btn-outline'  href='" . U('ccp_communist_group_do_del', array('group_id' => $group['group_id'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
		}
		ob_clean();$this->ajaxReturn($group_list); // 返回json格式数据
	}
	/**
	 * @name:ccp_communist_group_edit
	 * @desc：人员分组添加/编辑，单独添加为_add
	 * @author：王宗彬
	 * @addtime:2017-10-21
	 * @version：V1.0.0
	 **/
	public function ccp_communist_group_edit(){
		checkAuth(ACTION_NAME);
		$ccp_group = M('ccp_group');
		$ccp_group_communist = M('ccp_group_communist');
		$group_id = I('get.group_id'); // I方法获取数据
		if (! empty($group_id)) { // 必要的非空判断需要增加,防止报错
            $group_map['group_id'] = $group_id;
			$group_row = $ccp_group->where($group_map)->find();
			$group_id = $group_row['group_id'];
            $group_comm_map['group_communist_id'] = $group_id;
			$group_communist = $ccp_group_communist->where($group_comm_map)->field('communist_no')->select();
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			foreach ($group_communist as $k=>&$communist){
			    $group_row['communist_name'][$k] = $communist_name_arr[$communist['communist_no']];
				$group_row['communist_no'][$k] = $communist['communist_no'];
			}
			$group_row['communist_name'] = arrToStr($group_row['communist_name'],',');
			$group_row['communist_no'] = arrToStr($group_row['communist_no'],',');
			$this->assign("group_row", $group_row); // 控制器与视图页面的变量尽量保持一致
		}
		$this->display("Ccpcommunist/ccp_communist_group_edit");
	}
	/**
	 * @name:ccp_communist_group_info
	 * @desc：人员分组详情
	 * @author：王宗彬
	 * @addtime:2017-10-19
	 * @version：V1.0.0
	 **/
	public function ccp_communist_group_info(){
		checkAuth(ACTION_NAME);
		$ccp_group = M('ccp_group');
		$ccp_group_communist = M('ccp_group_communist');
		$group_list = $ccp_group->select();
		$group_id = I('get.group_id'); // I方法获取数据
		if (! empty($group_id)) { // 必要的非空判断需要增加,防止报错
            $group_map['group_id'] = $group_id;
			$group_row = $ccp_group->where($group_map)->find();
			$group_id = $group_row['group_id'];
            $group_comm_map['group_communist_id'] = $group_id;
			$group_communist = $ccp_group_communist->where($group_comm_map)->field('communist_no')->select();
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			foreach ($group_communist as $k=>&$communist){
			    $group_row['communist_name'][$k] = $communist_name_arr[$communist['communist_no']];
				$group_row['communist_no'][$k] = $communist['communist_no'];
			}
			$group_row['communist_name'] = arrToStr($group_row['communist_name'],',');
			$group_row['communist_no'] = arrToStr($group_row['communist_no'],',');
			$this->assign("group_row", $group_row); // 控制器与视图页面的变量尽量保持一致
		}
		$this->assign('group_list', $group_list);
		$this->display("Ccpcommunist/ccp_communist_group_info");
	}
	/**
	 * @name:ccp_communist_group_do_save
	 * @desc：人员分组保存
	 * @author：王宗彬
	 * @addtime:2017-10-21
	 * @version：V1.0.0
	 **/
	public function ccp_communist_group_do_save(){
		checkLogin();
		$ccp_group = M('ccp_group');
		$ccp_group_communist = M('ccp_group_communist');
		$data = I('post.'); // I方法获取整个数组
		$data['add_staff'] = $this->staff_no;
		$data['status'] = "1";
		$group_id = $data['group_id'];
		if (! empty($group_id)) { // 有id时执行修改操作
            $group_comm_map['group_communist_id'] = $group_id;
			$ccp_group_communist->where($group_comm_map)->delete();
			$data['update_time'] = date("Y-m-d H:i:s");
			$communist_no =  strToArr($data['missive_receiver_no'],',');
			$res = $ccp_group->save($data);
			if($res){
				$data['group_communist_id'] = $group_id;
				$data['add_time'] = date("Y-m-d H:i:s");
				$data['update_time'] = date("Y-m-d H:i:s");
				foreach ($communist_no as $communist){
					$data['communist_no'] = $communist;
					$oper_res = $ccp_group_communist->add($data);
				}
			}
		} else { // 无id时执行添加操作
			if (checkRepeat('ccp_group', 'group_name', $data['group_name'])) {
				showMsg('error', '编号已存在,请重新填写','');
			} else {
				$data['add_time'] = date("Y-m-d H:i:s");
				$data['update_time'] = date("Y-m-d H:i:s");
				$communist_no =  strToArr($data['missive_receiver_no'],',');
				$res = $ccp_group->add($data);
				if($res){
					$data['group_communist_id'] = $res;
					$data['add_time'] = date("Y-m-d H:i:s");
					$data['update_time'] = date("Y-m-d H:i:s");
					foreach ($communist_no as $communist){
						$data['communist_no'] = $communist;
						$oper_res = $ccp_group_communist->add($data);
					}
				}
			}
		}
		if ($oper_res) {
			showMsg('success', '操作成功！！！', U('Ccpcommunist/ccp_communist_group_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}
	/**
	 * @name:ccp_communist_group_do_del
	 * @desc：人员分组删除
	 * @author：王宗彬
	 * @addtime:2017-10-19
	 * @version：V1.0.0
	 **/
	public function ccp_communist_group_do_del(){
		checkAuth(ACTION_NAME);
		$ccp_group = M('ccp_group');
		$ccp_group_communist = M('ccp_group_communist');
		$group_id = I('get.group_id'); // I方法获取数据
		if (! empty($group_id)) { // 必要的非空判断需要增加
            $group_map['group_id'] = $group_id;
			$res = $ccp_group->where($group_map)->delete();
			if($res){
                $group_comm_map['group_communist_id'] = $group_id;
				$del_res = $ccp_group_communist->where($group_comm_map)->delete();
			}
		}
		if ($del_res) {
			showMsg('success', '操作成功！！！', U('Ccpcommunist/ccp_communist_group_index'));
		} else {
			showMsg('error', '操作失败','');
		}
	}

    //导出
    protected function case_export($detailList = array()){ 
        // 报表内容
        $data = array();
        foreach ($detailList as $k => $val){
            $data[$k][name]= $treat['evaluation'];
            $data[$k][age]= $treat['admission'];
            $data[$k][record]= $treat['record'];
            $data[$k][time]= $treat['after_treatment']; 
            $data[$k][sex]= $treat['after_treatment']; 
        }
        // 设置表头
        foreach ($data as $field => $v) {
            if ($field == 'name') {
                $headArr[] = '姓名';
            }
            if ($field == 'age') {
                $headArr[] = '年龄';
            }
            if ($field == 'record') {
                $headArr[] = '学历';
            }
            if ($field == 'time') {
                $headArr[] = '入党时间';
            }
            if ($field == 'sex') {
                $headArr[] = '性别';
            }
        }
        // 报表名名称
        $filename = "党员列表";
        exportExcel($filename, $headArr, $data);
    }
}
