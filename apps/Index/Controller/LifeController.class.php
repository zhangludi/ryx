<?php
/**
 * Created by PhpStorm.
 * User: yangkai 
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Index\Controller;


use Think\Controller;

class LifeController extends Controller
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
		if (!empty($door_communist_no)) {
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
	 * 民生首页
	 */
	public function ipam_life_index()
    {
    	$article_data = getArticleList(17,0,5);
    	foreach ($article_data['data'] as &$list_a) {
    		$list_a['article_thumb'] = getUploadInfo($list_a['article_thumb']);
    	}
    	$this->assign('article_data',$article_data['data']);//扶贫动态
    	$survey_data = getSurveyList(1);
    	$this->assign('survey_data',$survey_data);//问卷
    	$condition_data = M('life_condition')->order('condition_id desc')->limit(0,3)->select();
    	$this->assign('condition_data',$condition_data);//o2o
    	
    	// $help_list = getCommunistHelpList();
    	// foreach ($help_list as &$list_h) {
    	// 	$list_h['communist_name'] = getCommunistInfo($list_h['communist_no']);
    	// }
    	
    	$help_list = M('ccp_help_measures')->order('add_time desc')->select();
    	foreach($help_list as &$list){
    		$list['add_time'] = substr($list['add_time'],0,10);
    		if($list['measures_genre']==1){
    			$list['measures_genre'] = '贫困村';
    		}else if($list['measures_genre']==2){
    			$list['measures_genre'] = '贫困户';
    		}
    		if($list['measures_genre']=='贫困村'){
	            $array_measures_genre = strToArr($list['measures_genre_no'],',');
	            // dump($array_measures_genre);die;
	            $poor_village = M("poor_village")->where(['is_poor'=>1])->select();
	            foreach($array_measures_genre as &$measures){
	                foreach($poor_village as &$village){
	                    if($measures == $village['poor_village_id']){
	                       $measures =  $village['poor_village_name'];
	                    }
	                }
	            }
	            $b = implode(',',$array_measures_genre);
	            $list['measures_genre_no'] = $b;
        	}
        	if($list['measures_genre']=='贫困户'){
	            $array_measures_genre = strToArr($list['measures_genre_no'],',');
	            $poor_household = M("poor_household")->where(['is_poor_village'=>1])->select();
	            foreach($array_measures_genre as &$measures){
	                foreach($poor_household as &$household){
	                    if($measures==$household['poor_household_id']){
	                       $measures =  $household['poor_household_name'];
	                    }
	                }
	            }
	       	 	$b = implode(',',$array_measures_genre);
	        	$list['measures_genre_no'] = $b;
	        }
    		//
    	}
    	$this->assign('help_list',$help_list);//帮助
        $this->display('ipam_life_index');
    }
    /**
     * 民生o2o
     */
    public function ipam_o2o_list(){
		$count = M('life_condition')->count();
		$this->assign('count',$count);//帮助
		$this->display('ipam_o2o_list');
	}
	/**
	 * 门户o2o分页调取
	 */
	public function geto2oList()
	{	$type = I('post.type');
	$pagesize = I('post.pagesize');
	$page = (I('post.page') - 1) * $pagesize;
	
	$condition_data = M('life_condition')->order('condition_id desc')->limit($page,$pagesize)->select();
	// 		$edu_list = M('life_survey')->field('survey_id,survey_title,add_staff,add_time')->limit($page,$pagesize)->select();
	$data = '';
	foreach ($condition_data as $list) {
	    
	    $add_name=$list['condition_personnel'];//getCommunistInfo($list['condition_personnel']);
		$data .= "
                    <li class='w-all mr-5 pt-15 pb-15 hr-sb-f5f5f5'>
                        <a href='#' class='see-details' >
                            <div class=' hr-s-dfdfdf pull-left w-220 h-140 mr-15 over-h po-re'>
                                <img src='".getUploadInfo($list['condition_thumb'])."' class='di-b w-all center-ver' alt='图书图片'>
                            </div>
                            <div class='public_card_text_lr_txt'>
                                <div class='mb-20'><img src='../statics/apps/page_portal/images/study_note_icon.png' class='mr-5' alt=''>".$list['condition_title']."</div>
                                <div class='public_card_text_lr_txt_content fcolor-99'>作者：$add_name</div>
                             
                                <div class='public_card_text_lr_txt_content fcolor-99'>时间：".$list['add_time']."</div>
                                <a href='".U('ipam_o2o_info',array('condition_id'=>$list['condition_id']))."' class='w-100 h-30 lh-30 pull-right fsize-14 public_button_stroke-red see-details'>查看详情</a>
                            </div>
                        </a>
                    </li>
                    ";
	}
	ob_clean();$this->ajaxReturn(['content' => $data]);
	}
	/**
	 * 民生o2o详情
	 */
	public function ipam_o2o_info(){
		$where['condition_id']=I('get.condition_id');
		$condition_data = M('life_condition')->where($where)->find();
		$this->assign('condition_data',$condition_data);//帮助
		$type = "condition";
		$article_id = I('get.condition_id');
		$this->assign('type',$type);
		$this->assign('article_id',$article_id);
		$this->display('ipam_o2o_info');
	}
	/**
	 * 民生o2o申请
	 */
	public function ipam_o2o_edu(){
		$this->display('ipam_o2o_edu');
	}
	/**
	 * 调查问卷
	 */
	public function ipam_home_survey_list(){
		$db_Survey = M('life_survey');
		$count = $db_Survey->order('add_time desc')->count();
		$this->assign('count', $count);
    	$this->display('ipam_home_survey_list');
	}
	/**
	 * 调查问卷详情
	 */
	public function ipam_survey_info(){
	    $survey_id = I('get.survey_id');
	    $db_survey_questions =M('life_survey_questions');
	    $survey_map['survey_id'] = $survey_id;
	    $survey_list = M('life_survey')->where($survey_map)->field('survey_id,survey_title,add_staff,add_time')->find();
	    $this->assign('survey_list',$survey_list);
	    $questions_list = $db_survey_questions->where($survey_map)->select();
	    foreach ($questions_list as &$list) {
	        $list['questions_item'] = explode(',',$list['questions_item']);
	    }
	    $this->assign('questions_list',$questions_list);
		$this->display('ipam_survey_info');
	}
	/**
	 * 调查问卷保存
	 */
	public function ipam_survey_save(){
	    $communist_no = session('door_communist_no');
	    $survey_id = I('post.survey_id');
	    $questions_arr = I('post.questions_arr');
	    M()->startTrans();
	    $survey_list['survey_id'] = $survey_id;
	    $survey_list['communist_no'] = $communist_no;
	    $survey_list['log_date'] = date('Y-m-d');
	    $survey_list['add_staff'] = $communist_no;
	    $survey_list['status'] = '1';
	    $survey_list['add_time'] = date('Y-m-d H:i:s');
	    $result = M('life_survey_log')->add($survey_list);
	    $arr = array();
	    foreach ($questions_arr as $k=>$item) {
	            $list['communist_no'] = $communist_no;
	            $list['survey_id'] = $survey_id;
	            $list['questions_id'] = $k;
	            $list['answer_item'] = $item[0];
	            $list['add_staff'] = $communist_no;
	            $list['status'] = '1';
	            $list['add_time'] = date('Y-m-d H:i:s');
	           
	            $arr[] = $list;
	    }
	    $flag = M('life_survey_answer')->addAll($arr);
	    
	    if ($result && $flag) {
	        M()->commit();
	        showMsg('success', '操作成功！！！', U('ipam_home_survey_list'));
	    } else {
	        M()->rollback();
	        showMsg('error', '操作失败！！！','');
	    }
	}
	/**
	 * 门户调查分页调取
	 */
	public function getsurveyList()
	{
		$pagesize = I('post.pagesize');
		$page = (I('post.page') - 1) * $pagesize;
		$communist_no = session('door_communist_no');
        $party_no = getCommunistInfo($communist_no, 'party_no');
		//$edu_list = M('life_survey')->field('survey_id,survey_title,add_staff,add_time')->limit($page,$pagesize)->order('add_time desc')->select();
		$survey_list = M('life_survey')
            ->join("(select * from sp_life_survey_log where communist_no = $communist_no) as l on sp_life_survey.survey_id=l.survey_id", 'LEFT')
            ->where("sp_life_survey.status=1 and find_in_set($party_no,sp_life_survey.party_no) and communist_no is null")
            ->field('sp_life_survey.survey_id,sp_life_survey.survey_title,sp_life_survey.add_staff,sp_life_survey.add_time')->select();
		$data = '';
		foreach ($survey_list as $list) {
			$add_name=getStaffInfo($list['add_staff']);
			$data .= " <li class='mt-25 pb-25 hr-sb-dfdfdf '>
                        <a href='".U('ipam_survey_info',array('survey_id'=>$list['survey_id']))."' class='see-details'>
                            <span class='fcolor-33 fsize-20 font-bold career_question_title'>".$list['survey_title']."</span>
                            
                            <p class='fcolor-b3b3b3 mt-15'>
                                <span class='mr-15 pr-15 hr-sr-b3b3b3'>发布人：".$add_name."</span>
                                <span class='mr-15 pr-15 hr-sr-b3b3b3'>发布时间：".$list['add_time']."</span>
                               
                            </p>
                            <p class='fcolor-66 mt-25'>为进一了解当情况，请你们认真填写你们的宝贵意见！！</p>
                        </a>
                    </li> 
                    ";
		}
		ob_clean();$this->ajaxReturn(['content' => $data]);
	}


	/**
	 * 精准扶贫列表
	 */
	public function ipam_help_list(){
		
		$type=I('get.type');
		if (empty($type)){
		    $article_data = getArticleList(13,0,13);
		    foreach ($article_data['data'] as &$list_a) {
		        $list_a['article_thumb'] = getUploadInfo($list_a['article_thumb']);
		    }
		    $this->assign('article_data',$article_data['data']);//扶贫政策
		    $ae_data = getArticleList(17,0,5);
		    foreach ($ae_data['data'] as &$list_b) {
		        $list_b['article_thumb'] = getUploadInfo($list_b['article_thumb']);
		    }
		    $this->assign('ae_data',$ae_data['data']);//扶贫动态
		    $help_data = getHelpList();
		    foreach ($help_data as &$list_h) {
		        $list_h['communist_name'] = getCommunistInfo($list_h['communist_no']);
		        $party_no_h = getCommunistInfo($list_h['communist_no'],'party_no');
		        if(!empty($party_no_h)){
		        	$list_h['party_name_short'] = getPartyInfo($party_no_h,'party_name_short');
		        }
		    } 
		    $this->assign('help_data',$help_data);//扶贫记录
		    $this->display('ipam_home_help_list');
		}else {
		    $this->assign('type',$type);//类型
		    $db_Survey = M('life_survey');
		    $count = getArticleList($type,'','','','','',1);
		    $this->assign('count', $count);
		    $this->display('ipam_help_list');
		}
		
	}
	/**
	 * 门户精准扶贫分页调取
	 */
	public function gethelpList()
	{	$type = I('post.type');
		$pagesize = I('post.pagesize');
		$page = (I('post.page') - 1) * $pagesize;
		$article_list = getArticleList($type,$page,$pagesize);
		
// 		$edu_list = M('life_survey')->field('survey_id,survey_title,add_staff,add_time')->limit($page,$pagesize)->select();
		$data = '';
		foreach ($article_list['data'] as $list) {
			$add_name=getStaffInfo($list['add_staff']);
			$data .= " <li class='mt-25 pb-25 hr-sb-dfdfdf '>
                        <a class='see-details' href='".U('ipam_help_info',array('cat_id'=>$list['article_id']))."'>
                            <span class='fcolor-33 fsize-20 font-bold career_question_title'>".$list['article_title']."</span>
	
                            <p class='fcolor-b3b3b3 mt-15'>
                                <span class='mr-15 pr-15 hr-sr-b3b3b3'>发布人：".$add_name."</span>
                                <span class='mr-15 pr-15 hr-sr-b3b3b3'>发布时间：".$list['add_time']."</span>
                
                            </p>
                           
                        </a>
                    </li>
                    ";
		}
		ob_clean();$this->ajaxReturn(['content' => $data]);
	}
	public function ipam_help_info(){
		$cat_id = I('get.cat_id');
		$helpinfo = getArticleInfo($cat_id,'all');
		$helpinfo['article_thumb'] = getUploadInfo($helpinfo['article_thumb']);
	
		$this->assign('helpinfo',$helpinfo);//扶贫记录
 		$type = "help";
		$article_id = I('get.cat_id');
		$this->assign('type',$type);
		$this->assign('article_id',$article_id);
		$this->display('ipam_help_info');
	}
	/**
	 * 便民服务大厅
	 */
    public function ipam_home_service(){
        $this->display('ipam_home_service');
    }

    /**
	 * 便民服务大厅详情
	 */
    public function ipam_service_detail(){
    	$type = I('get.type');
    	if($type == 'flow'){
    		// 工作流程
    		$life_service_info = M('cms_article')->where("article_cat = '1401'")->order('add_time desc')->find();
    		$life_service_info['title'] = $life_service_info['article_title'];
			$life_service_info['content'] = $life_service_info['article_content'];
    	} else if($type == 'service'){
			// 服务窗口
    		$life_service_info = M('cms_article')->where("article_cat = '1402'")->order('add_time desc')->find();
    		$life_service_info['title'] = $life_service_info['article_title'];
			$life_service_info['content'] = $life_service_info['article_content'];
    	} else {
    		// 联系我们
    		$life_service_info = M('cms_article_category')->where("cat_id = '22'")->find();
    		$life_service_info['title'] = $life_service_info['cat_title'];
			$life_service_info['content'] = $life_service_info['cat_content'];
    	}
		$life_service_info['add_staff'] = '超级管理员';
    	$this->assign('data',$life_service_info);//扶贫记录
        $this->display('ipam_service_detail');
    }

}