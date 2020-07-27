<?php
/**
 * Created by PhpStorm.
 * User: yangkai 
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Index\Controller;


use Think\Controller;

class EduController extends Controller
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
        foreach ($nav_list as  &$list) {
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
     * ipam_edu_index
     * 笔记ajax
     * 刘长军
     */
    public function ipam_edu_notes_data(){
        $communist_no = session('door_communist_no');
        $notes_actice_type = I('get.notes_actice_type',11);
        $time = date('Y-m-d');
        switch ($notes_actice_type) {
            case 11:
                $notes_list = M('edu_notes as n')
                    ->join('sp_edu_material as m on n.material_id=m.material_id')
                    ->join('sp_edu_material_category as c on c.cat_id = m.material_cat')
                    ->field("substring(n.add_time,9,2) as stringadd")->where("c.cat_type = $notes_actice_type and DATE_FORMAT(n.add_time, '%Y%m') = DATE_FORMAT('$time','%Y%m') and n.add_staff=$communist_no")->select();
                break;
            case 21:
                $notes_list = M('edu_notes as n')
                    ->join('sp_edu_material as m on n.material_id=m.material_id')
                    ->join('sp_edu_material_category as c on c.cat_id = m.material_cat')
                    ->field("substring(n.add_time,9,2) as stringadd")->where("c.cat_type = $notes_actice_type and DATE_FORMAT(n.add_time, '%Y%m') = DATE_FORMAT('$time','%Y%m') and n.add_staff=$communist_no")->select();
                break;
            case 2:
                $notes_list = M('edu_notes')->where("notes_type=2 and DATE_FORMAT(add_time, '%Y%m') = DATE_FORMAT('$time','%Y%m') and add_staff=$communist_no")->field("substring(add_time,9,2) as stringadd")->select();
                break;
            case 3:
                $notes_list = M('edu_notes')->where("notes_type=3 and DATE_FORMAT(add_time, '%Y%m') = DATE_FORMAT('$time','%Y%m') and add_staff=$communist_no")->field("substring(add_time,9,2) as stringadd")->select();
                break;
        }
        $add_time_month = [];
        foreach($notes_list as $data){
            $add_time_month[] = ''.intval($data['stringadd']).'';
        }

        // $notes_list = M('edu_notes')->where("notes_type=$notes_actice_type")->select();
        $this->ajaxReturn($add_time_month);
    }
    public function ipam_edu_notes_list(){
        $notes_actice_type = I('post.notes_actice_type');
        $communist_no = session('door_communist_no');
        $time = I('post.time');
        //文章
        $notes_list = '';
        switch ($notes_actice_type) {
            case 11:
                $notes_list = M('edu_notes as n')
                    ->join('sp_edu_material as m on n.material_id=m.material_id')
                    ->join('sp_edu_material_category as c on c.cat_id = m.material_cat')
                    ->field("n.notes_title,substring(n.add_time,12,5) as time_add,substring(n.add_time,1,10) as add_time")->where("c.cat_type = $notes_actice_type and DATE_FORMAT(n.add_time, '%Y%m%d') = DATE_FORMAT('$time','%Y%m%d') and n.add_staff=$communist_no")->select();
                break;
            case 21:
                $notes_list = M('edu_notes as n')
                    ->join('sp_edu_material as m on n.material_id=m.material_id')
                    ->join('sp_edu_material_category as c on c.cat_id = m.material_cat')
                    ->field("n.notes_title,substring(n.add_time,12,5) as time_add,substring(n.add_time,1,10) as add_time")->where("c.cat_type = $notes_actice_type and DATE_FORMAT(n.add_time, '%Y%m%d') = DATE_FORMAT('$time','%Y%m%d') and n.add_staff=$communist_no")->select();
                break;
            case 2:
                $notes_list = M('edu_notes')->where("notes_type=2 and DATE_FORMAT(add_time, '%Y%m%d') = DATE_FORMAT('$time','%Y%m%d') and add_staff=$communist_no")->field("notes_title,substring(add_time,12,5) as time_add,substring(add_time,1,10) as add_time")->select();
                break;
            case 3:
                $notes_list = M('edu_notes')->where("notes_type=3 and DATE_FORMAT(add_time, '%Y%m%d') = DATE_FORMAT('$time','%Y%m%d') and add_staff=$communist_no")->field("notes_title,substring(add_time,12,5) as time_add,substring(add_time,1,10) as add_time")->select();
                break;
        }
        $this->ajaxReturn($notes_list);
    }
    public function edu_customization_show(){
        $communist_no = session('door_communist_no');
        $customization = getEduCustomization($communist_no);
        if($customization){
            $add_time = M('edu_customization')->where($where)->getField('add_time');
            $times=round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
            $customization['edu_time'] = $times;
            $customization['communist_name'] = getCommunistInfo($communist_no);
            $party_no = M('ccp_communist')->where("communist_no=$communist_no")->getField('party_no');
            $customization['party_name'] = getPartyInfo($party_no);
            $this->ajaxReturn($customization);
        }else{
            $this->ajaxReturn(2);
        }
    }
    /**
     * edu_customization_save
     * 定制学习ajax
     * 刘长军
     */
    public function edu_customization_save(){
        $data_group = I('post.data_group');
        $data_label = I('post.data_label');
        $communist_no = session('door_communist_no');
        $db_customization = M('edu_customization');
        $article['communist_no'] = $communist_no;
        $article['material_group'] = $data_group;
        $article['material_data'] = $data_label;
        $article['add_time'] = date('Y-m-d H:i:s');
        $article['update_time'] = date('Y-m-d H:i:s');
        $article['add_staff'] = $communist_no;
        $customization = $db_customization->add($article);
        $customization_id = '';
        if($customization){
            $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');
        }
        getEduCustomizationSave($communist_no,$data_group,$data_label,$customization_id);
        $this->ajaxReturn(1);
    }
	/**
	 * ipam_edu_index
	 * 学习首页
	 * 刘长军
	 */
	public function ipam_edu_index()
    {
        //专题列表

        $communist_no = session('door_communist_no');
    
        $topic_list = getTopicList();
        foreach ($topic_list as $key=>&$list) {
            $list['topic_img'] = getUploadInfo($list['topic_img']);
            $list['percent'] = getTopicLearnInfo($list['topic_id'],$communist_no);
        }
        $count_topic = count($topic_list);
        $this->assign('count_topic',$count_topic);
        $this->assign('topic_list',$topic_list);
        $communist_no = session('door_communist_no');
        $maps['topic_id'] = $topic_list[0]['topic_id'];
        $add_time = M('edu_topic')->where($maps)->getField('add_time');
        $times = round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
        $this->assign('times',$times);

        $article_data = M("cms_article")->where("article_cat=19")->field('article_id,article_title,article_description,article_thumb')->order("add_time desc")->limit(4)->select();
        foreach($article_data as &$data){
            $data['article_thumb'] = getUploadInfo($data['article_thumb']);
            $data['article_title'] = mb_substr($data['article_title'],0,14,"utf-8")."...";
        }
        $this->assign('article_data',$article_data);
        // 定制部分
        $db_groupdata = M('edu_groupdata');
        //组织
        $party_list = getBdCodeList('party_level_code','');
        $this->assign('party_list',$party_list);
        //对应群体
        $data_group = $db_groupdata->where("group_type=1 and status=1")->select();
        $this->assign('data_group',$data_group);
        //资料标签
        $data_label = $db_groupdata->where("group_type=2 and status=1")->select();
        $this->assign('data_label',$data_label);
        
  //   	/************ 学习课件开始 ************/
  //   	$material_list = $this->get_material_list(21, 0, 5);
  //   	foreach ($material_list as &$list) {
  //   		$list['material_thumb'] = getUploadInfo($list['material_thumb']);
  //   	}
  //   	$this->assign('material_list',$material_list);//视频课件
  //   	$article_list = $this->get_material_list(11, 0, 7);
  //   	foreach ($article_list as &$a_list) {
  //   		$a_list['material_thumb'] = getUploadInfo($a_list['material_thumb']);
  //   	}
  //   	$this->assign('article_list',$article_list);//文章课件
  //   	$cat_list = getMaterialList('','','','','','',0,11);
  //   	$this->assign('cat_list',$cat_list);//课件排行
		// /************ 学习课件结束 ************/
    	
  //   	/************ 考试中心开始 ************/
  //       $db_exam =M('edu_exam');
  //       $exam_data = $this->get_exam_list(0,8); 
  //       foreach ($exam_data as &$e_list) {
  //           $e_list['exam_thumb'] = getUploadInfo($e_list['exam_thumb']);
  //           $e_list['add_staff'] = getStaffInfo($e_list['add_staff']);
  //       }
  //       $this->assign('exam_data',$exam_data);//未结试卷
  //       $exam_list = $db_exam
  //       ->join("sp_edu_exam_log ON sp_edu_exam.exam_id = sp_edu_exam_log.exam_id")
  //       ->where("sp_edu_exam_log.status=1")
  //       ->field("sp_edu_exam.exam_title,sp_edu_exam.exam_id,sp_edu_exam_log.communist_no,sp_edu_exam_log.log_score,sp_edu_exam_log.log_integral")
  //       ->select();
  //       foreach ($exam_list as &$ex_list) {
  //           $ex_list['communist_name'] = getCommunistInfo($ex_list['communist_no']);
  //       }
  //       $this->assign('exam_list',$exam_list);//考试人员展示
  //       /************ 开始中心结束 ************/
  //       /************ 学习笔记开始 ************/
  //       $notes_list_1 = getNotesList('', '', '','', '0', 1,'','',0,6);
  //       $this->assign('notes_list_1',$notes_list_1);//笔记类型1
  //       $notes_list_2 = getNotesList('', '', '','', '0', 2,'','',0,5);
  //       $this->assign('notes_list_2',$notes_list_2);//笔记类型2
  //       $notes_list_3 = getNotesList('', '', '','', '0', 3,'','',0,5);
  //       $this->assign('notes_list_3',$notes_list_3);//笔记类型3
        /************ 学习笔记结束 ************/
        $this->display('ipam_edu_index');
    }
    /**
     * ipam_material_list
     * 学习资料列表
     * yangkai
     */
    public function ipam_material_list(){
    	$topic_id=I('get.topic_id',1);
        $topic_data = M("edu_topic")->where("topic_id=$topic_id")->find();
        $topic_data['topic_img'] = getUploadInfo($topic_data['topic_img']);
        $this->assign('topic_data',$topic_data);
        $communist_no = session('door_communist_no');
        $learn_data = getTopicLearnInfo($topic_id,$communist_no);
        $this->assign('learn_data',$learn_data);
        $maps['topic_id'] = $topic_id;
        
        $add_time = M('edu_topic')->where($maps)->getField('add_time');
        $times = round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
        $content = getCommunistInfo($communist_no,'communist_name')."党员您在".$topic_data['topic_title']."专题学习了".$times."天，希望您加油完成专题学习任务";
        $this->assign('content',$content);


        $db_cat = M('edu_material_category');
        $cat_list = $db_cat->where("status=1 and cat_type=21")->getField('cat_id', true);
        $cat_list = implode(',', $cat_list);
        $material_list = getMaterialList($cat_list,$communist_no, null, null, null, $topic_id);
        foreach ($material_list as &$material) {
            $material['material_thumb'] = getUploadInfo($material['material_thumb']);
            $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d');
        }
        $this->assign('material_vedio',$material_list);
        $cat_list = $db_cat->where("status=1 and cat_type=11")->getField('cat_id', true);
        $cat_list = implode(',', $cat_list);
        $material_data = getMaterialList($cat_list, $communist_no, null, null, null, $topic_id);
        foreach ($material_data as &$data) {
            $data['material_title_q'] = mb_substr($data['material_title'],0,7);
            $data['material_thumb'] = getUploadInfo($data['material_thumb']);
            $data['add_time'] = getFormatDate($data['add_time'],'Y-m-d');
        }
        $this->assign('material_article',$material_data);

        $where['add_staff'] = session('door_communist_no');
        $where['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
        $count =  M('edu_notes')->where($where)->count('notes_id');
        $this->assign('count', $count);
        // $this->assign('notes_list', $notes_list);
    	// if (empty($type)){
    	//     $material_list = $this->get_material_list(21, 0, 14);
    	//     foreach ($material_list as &$list) {
    	//         $list['material_thumb'] = getUploadInfo($list['material_thumb']);
    	//     }
    	//     $this->assign('material_list',$material_list);//视频课件
    	//     $article_list = $this->get_material_list(11, 0, 20);
    	//     foreach ($article_list as &$list_a) {
    	//         $list_a['material_thumb'] = getUploadInfo($list_a['material_thumb']);
    	//     }
    	//     $this->assign('article_list',$article_list);//视频课件
    	//     $this->display('ipam_home_material_list');
    	// }else {
    	//     $this->assign('type',$type);//类型
    	//     $count = $this->get_material_list($type,0,0,1);
    	//     $this->assign('count', $count);
    	    $this->display('ipam_material_list');
    	// }
    }
	 /**
     * 门户考试分页调取
     */
    public function getexamList_data()
    {
        $communist_no = session('door_communist_no');
        $pagesize = I('get.pagesize');
        $is_exam = I('get.is_exam',1);
        $page = (I('get.page') - 1) * $pagesize;
        //$edu_list = M('edu_exam')->field('exam_id,exam_thumb,exam_title,exam_date')->limit($page,$pagesize)->select();
        $exam_list = getExamList('',$communist_no,$is_exam,'',$page,$pagesize);
        $edu_list = $exam_list['data'];
        $data = '';
        switch ($is_exam) {
            case '1':
                $span = '<span class="official_bg">正式</span>&nbsp;';
                $exam_type = '正式';
                break;
            case '3':
                $span = '<span class="mock_bg">模拟</span>&nbsp;';
                $exam_type = '模拟';
                break;
            default:
                $exam_type = '已考';
                break;
        }

        $db_questions = M('edu_questions');
        foreach ($edu_list as $list) {
            $questions_map['questions_id'] = array('in',$list['exam_questions']);
            $questions_score = $db_questions->where($questions_map)->getField('questions_score',true);
            $score_num = 0;
            foreach ($questions_score as $value) {
                $score_num += $value; 
            }
            if($is_exam=='2'){
                $info_exam = 'info_exam_already('.$list['exam_id'].')';
            }else{
                $info_exam = 'info_exam('.$list['exam_id'].','.$is_exam.')';
            }
            $add_name = getStaffInfo($list['add_staff']);
            $data .= '<div class="bb-dashed">
                        <div class="p-20 clearfix" onclick="'.$info_exam.'">
                            <p class="fsize-16 fcolor-33 pull-left">'.$list['exam_title'].'</p>
                            <div class="bg-learning lh-22 pull-right pl-15 pr-15">
                                <span class="color-376BF5 fsize-12">'.$span.'</span>
                            </div>
                            <p class="pt-5 clearfix">
                                <span class="fcolor-99 fsize-12">'.$list['exam_title_type'].'</span>
                                
                                <span class="fcolor-99 fsize-12 pl-5">总分:</span>
                                <span class="fcolor-99 fsize-12">'.$score_num.'</span>
                            </p>
                        </div>
                      </div>';
        }
        ob_clean();$this->ajaxReturn(['content' => $data]);
    }
	/**
	 *  ipam_material_info
	 * @desc 学习资料详情。同时计算积分、浏览记录
	 * @user 隔壁老王
	 * @date 2019/2/20
	 * @version 1.0.0
	 */
    public function ipam_material_info(){
        $communist_no = session('door_communist_no');
		$material_id=I('get.material_id');
		$material_info=getMaterialInfo($material_id);
        $material_info['integral_article'] = getConfig('integral_article'); // 文章积分
        $material_cat=getMaterialInfo($material_id,'material_cat');
        // 判断是文章学习资料还是视频学习型资料
        M('edu_material')->where("material_id=$material_id")->setInc('read_num');
        $res = M('edu_material_communist')->where("material_id='$material_id' and communist_no='$communist_no'")->select();
        if(empty($res)){
            $data['communist_no'] = $communist_no;
            $data['add_staff'] = $communist_no;
            $data['material_id'] = $material_id;
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $data['is_read'] = 1;
            M('edu_material_communist')->add($data);
        }
        //学习人员
        $where['material_id'] = $material_id;
        $material_info['log_list_count'] = M('edu_material_communist')->where($where)->count("DISTINCT communist_no");
        $material_info['log_list'] = M('edu_material_communist')->field('communist_no')->where($where)->order('add_time desc')->group('communist_no')->select();
        foreach ($material_info['log_list'] as &$log) {
            $log['communist_avatar'] = getCommunistInfo($log['communist_no'],'communist_avatar');
            if(empty($log['communist_avatar'])){
                $log['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
            $log['communist_name'] = getCommunistInfo($log['communist_no']);
        }
        $material_info['notes_list'] = M('edu_notes')->field('notes_id,notes_content,add_time,add_staff')->where($where)->limit(3)->order('add_time desc')->select();
        foreach ($material_info['notes_list'] as &$material) {
            $material['notes_content'] = mb_substr(strip_tags($material['notes_content']),0,35,'utf-8');
            $material['add_staff'] = getCommunistInfo($material['add_staff']);
            $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d H:i');
        }
        $this->assign('material_info',$material_info);
        $map['material_cat'] = $material_cat;
        $map['material_topic'] = getMaterialInfo($material_id,'material_topic');

        $material_list = M('edu_material')->where($map)->field('material_id,material_title,material_thumb')->limit(5)->select();
        foreach ($material_list as &$liat) {
            $liat['material_thumb'] = getUploadInfo($liat['material_thumb']);
        }
        $this->assign('material_list',$material_list);
    	$this->assign('material_info', $material_info);
        $this->assign('article_id',$material_id);
    	$this->display('ipam_material_info');
    }
    /**
     *  ipam_material_videoInfo
     * @desc 学习资料详情。同时计算积分、浏览记录
     * @user 曾宪坤
     * @date 2019/2/20
     * @version 1.0.0
     */
    public function ipam_material_videoInfo(){
        $communist_no = session('door_communist_no');
        $material_id=I('get.material_id');
        $material_info=getMaterialInfo($material_id);
        if(!empty($material_info['material_vedio'])){
            $material_info['material_vedio'] = __ROOT__."".$material_info['material_vedio'];
        }
        $material_info['integral_video'] = getConfig('integral_video'); // 视频积分
        $material_cat=getMaterialInfo($material_id,'material_cat');
        // 判断是文章学习资料还是视频学习型资料
        M('edu_material')->where("material_id=$material_id")->setInc('read_num');
        $res = M('edu_material_communist')->where("material_id='$material_id' and communist_no='$communist_no'")->select();
        if(empty($res)){
            $data['communist_no'] = $communist_no;
            $data['add_staff'] = $communist_no;
            $data['material_id'] = $material_id;
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $data['is_read'] = 1;
            M('edu_material_communist')->add($data);
        }
        //学习人员
        $where['material_id'] = $material_id;
        $material_info['log_list_count'] = M('edu_material_communist')->where($where)->count("DISTINCT communist_no");
        $material_info['log_list'] = M('edu_material_communist')->field('communist_no')->where($where)->order('add_time desc')->group('communist_no')->select();
        foreach ($material_info['log_list'] as &$log) {
            $log['communist_avatar'] = getCommunistInfo($log['communist_no'],'communist_avatar');
            if(empty($log['communist_avatar'])){
                $log['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
            $log['communist_name'] = getCommunistInfo($log['communist_no']);
        }
        $material_info['notes_list'] = M('edu_notes')->field('notes_id,notes_content,add_time,add_staff')->where($where)->limit(3)->order('add_time desc')->select();
        foreach ($material_info['notes_list'] as &$material) {
            $material['notes_content'] = mb_substr(strip_tags($material['notes_content']),0,35,'utf-8');
            $material['add_staff'] = getCommunistInfo($material['add_staff']);
            $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d H:i');
        }
        $this->assign('material_info',$material_info);
        $map['material_cat'] = $material_cat;
        $map['material_topic'] = getMaterialInfo($material_id,'material_topic');

        $material_list = M('edu_material')->where($map)->field('material_id,material_title,material_thumb')->limit(5)->select();
        foreach ($material_list as &$liat) {
            $liat['material_thumb'] = getUploadInfo($liat['material_thumb']);
        }
        $this->assign('material_list',$material_list);
        $this->assign('material_info', $material_info);
        $this->display('ipam_material_videoInfo');
    }
    public function set_integral(){
        $communist_no = session('door_communist_no');
        $db_period = M('edu_material_period');
        $db_material = M('edu_material');
        $material_id = I('post.material_id');
        if($material_id){
            $where['material_id'] = $material_id;
        }
        // 记录学习记录
        $communist_integral = getCommunistInfo($communist_no,'communist_integral'); // 党员当前积分数
        $material_type = I('post.material_type');
        $log_table=M("edu_material_log");
        $log_data["communist_no"]=$communist_no;
        $log_data["material_no"]=$material_id;
        $log_data["add_time"]=date("Y-m-d",time()); // 
        $log_data["log_type"]=1;
        $log_id=$log_table->where($log_data)->getField("log_id");
        if(!empty($log_id)){ // 已经看了该学习资料
            $log_table->where("log_id={$log_id}")->save($log_data);
            $this->ajaxReturn(false);
        }else{
            $log_table->add($log_data);
            if($material_type == 1){ // 文章加积分
                $integral_article = getConfig('integral_article');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_article,'学习文章'); 
            } else if($material_type == 2){// 学习视频加积分
                $integral_video = getConfig('integral_video');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_video,'学习视频'); 
            }
        }
        $period_score = $db_material->where($where)->getField('period_score');
        $post['material_id'] = $material_id;
        $post['period_score'] = $period_score;
        $post['communist_no'] = $communist_no;
        $post['add_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        $post['add_staff'] = session('staff_no');
        $post['period_year'] = date('Y');
        $post['period_month'] = date('m');
        $db_period->add($post);
        $this->ajaxReturn(true);
    }

    /**
     * exam_list
     * 考试列表
     * yangkai
     */
    public function ipam_exam_list(){
        $article_list = M('cms_article')->order('add_time desc')->limit(5)->field("article_title,article_view,article_thumb")->select();
        foreach ($article_list as &$value) {
           $value['article_thumb'] = getUploadInfo($value['article_thumb']);
        }
        $this->assign('article_list',$article_list);
    	$db_exam =M('edu_exam');
        $communist_no = session('door_communist_no');
        $count = getExamList('',$communist_no,'1','',0,10);
    	$this->assign('count', $count['count']);
    	$exam_list = $db_exam->join("sp_edu_exam_log ON sp_edu_exam.exam_id = sp_edu_exam_log.exam_id")->where("sp_edu_exam_log.status=1")->field("sp_edu_exam.exam_title,sp_edu_exam.exam_id,sp_edu_exam_log.communist_no,sp_edu_exam_log.log_score,sp_edu_exam_log.log_integral")->order("sp_edu_exam_log.log_score desc")->limit(0,10)->select();
    	foreach ($exam_list as &$ex_list) {
    		$ex_list['communist_name'] = getCommunistInfo($ex_list['communist_no']);
    	}
    	$this->assign('exam_list',$exam_list);//考试人员展示
    	$this->display('ipam_exam_list');
    }
    public function ipam_exam_count_ajax(){
        $value = I('post.value',1);
        $communist_no = session('door_communist_no');
        $count = getExamList('',$communist_no,$value,'',0,10);
        $this->ajaxReturn($count['count']);
    }
    /**
     * exam_attent
     * 参加考试
     * 长军
     */
    public function ipam_exam_attent(){
        $exam_id = I('get.exam_id');
        $is_exam = I('get.is_exam');
        $this->assign('is_exam',$is_exam);
        $communist_no = session('door_communist_no');
        $exam_list = getExamCommunistInfo($exam_id,$communist_no);
        $this->assign('questions_list',json_encode($exam_list));
        $this->assign('exam_list',$exam_list);
    	$this->display('ipam_exam_attent');
    }
    /**
     * ipam_exam_attent_already
     * 已经考试
     *changjun
     */
    public function ipam_exam_attent_already(){
        $exam_id = I('get.exam_id');
        $communist_no = session('door_communist_no');
        $exam_list = getExamCommunistInfo($exam_id,$communist_no);
        $this->assign('questions_list',json_encode($exam_list));
        $this->assign('exam_list',$exam_list);
        $this->display('ipam_exam_attent_already');
    }
    /**
     * ipam_exam_attent_already
     * 学习分析
     * 王宗彬
     */
    public function edu_exam_analysis(){
        $topic_id = I('post.topic_id');
        $communist_no = session('wechat_communist');
        $data['integral'] = getEduTopicIntegral($topic_id,$communist_no);
        $map['_string'] = "memo='学习文章' OR memo='学习视频' OR memo='完成学习笔记' OR memo='通过考试'";
        $data['communist_integra'] = M('ccp_integral_log')->field('log_relation_no as communist_no,sum(change_integral) as integral')->limit(0,5)->where($map)->order('integral desc')->group('log_relation_no')->select();
        $i=1;
        foreach ($data['communist_integra']  as &$communist) {
            $communist[i] = $i++;
            
            $communist['communist_name'] = getCommunistInfo($communist['communist_no']);
            $communist['communist_avatar'] = getCommunistInfo($communist['communist_no'],'communist_avatar');
            if(empty($communist['communist_avatar'])){
                $communist['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
        }
        $this->ajaxReturn($data);
    }
    /**
     /**
     *  set_edu_exam_questions_ajax
     * @desc 写入答案
     * @user 刘长军
     * @date 2019-7-29
     * @version 1.0.0
     */
    public function set_edu_exam_questions_ajax()
    {
        $questions_str = $_POST['questions_arr'];
        $exam_id = I("post.exam_id");
        $end_time = I("post.time_lave");
        $questions_arr=str_replace('{','',$questions_str);
        $questions_arr=str_replace('}','',$questions_arr);
        $questions_arr = explode('","',$questions_arr);
        $str_questions_id = $_POST['str_questions_id'];
        $str_questions_id = substr($str_questions_id,0,strlen($str_questions_id)-1); 
        
        $vall = [];
        foreach($questions_arr as &$list){
            $list1 = explode(':',$list);
            // $keyy[] = str_replace('"','',$list1[0]);
            $vall[] = str_replace('"','',$list1[1]);
        }
        // $exam_questions = M('edu_exam')->where("exam_id=$exam_id")->getField('exam_questions');
        $keyy = explode(',',$str_questions_id);

        $db_edu_exam_log = M('edu_exam_log');
        $db_edu_exam_answer = M('edu_exam_answer');
        $q_arr = array_combine($keyy,$vall);
        //查询当前考试人员名称
        $communist_no = session('door_communist_no');
        M()->startTrans();
        $question['exam_id'] = $exam_id;
        $question['communist_no'] = $communist_no;
        $question['add_time'] = date("Y-m-d H:i:s");
        $point_amount = 0; //所得分数
        $total_points = 0; //总分
        $accuracy = 0; 
        $count = count($q_arr);
        $exam_topic =  M('edu_exam')->where("exam_id = '$exam_id'")->getField('exam_topic');
        $exam_questions =  M('edu_exam')->where("exam_id = '$exam_id'")->getField('exam_questions');
        $exam_questions = explode(',',$exam_questions);
        $arra = '';
        foreach ($exam_questions as $questions){
            if(!isset($q_arr[$questions])){
                $q_arr[$questions] = '';
            }
        }
        foreach ($q_arr as $k => $v) {
            $question['questions_id'] = $k;
            $question['answer_item'] = $v;
            $question_data[] = $question;
            $question_result = getQuestionsInfo($k); //获得当前考题详情
            $total_points += $question_result['questions_score'];
            //当答案为空时 跳出本次循环
            if($v==$question_result['questions_answer']){
                $accuracy++;
                $point_amount += $question_result['questions_score'];
            }
        }
        foreach ($question_data as &$value) {
             switch ($value['answer_item']) {
                case 1:
                    $value['answer_item'] = 'A';
                    break;
                case 2:
                    $value['answer_item'] = 'B';
                    break;
                case 3:
                    $value['answer_item'] = 'C';
                    break;
                case 4:
                    $value['answer_item'] = 'D';
                    break;
            }
        }
        $result = $db_edu_exam_answer->addAll($question_data);
        $exam_integral = getExamInfo($exam_id);
        $point = $point_amount/$total_points;//把所得分数转换成百分比
        $integral_amount = $exam_integral['exam_integral'];
        $data['integral'] = '';
        if((float)$point >= 0.6){
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_amount,'通过考试'); //通过考试给党员加积分
            $data['integral'] = $integral_amount;//积分
            $question['memo'] = 1;
        }
        //添加考试记录
        $question['log_score'] = $point_amount;
        $question['log_date'] = date("Y-m-d H:i:s");
        $question['log_integral'] = $integral_amount;
        $start_time = I('post.exam_time');
        $time = $start_time*60;
        $end_time = explode(':',$end_time);
        $end_time = $end_time['0'] * 60 + $end_time['1'];
        $time_lave = $time - $end_time;
        $question['time_lave'] = gmstrftime('%M:%S',$time_lave);
        $flag = $db_edu_exam_log->add($question);
        if ($result && $flag) {
            M()->commit();
            $data['point_amount'] = $point_amount;//得分
            $data['count'] = $count;//题数
            $data['accuracy'] = round($accuracy/$count)*100;//正确率
            $data['time_lave'] = $question['time_lave'];
            $data['exam_id'] = $exam_id;
            $data['exam_topic'] = $exam_topic;
        }
        $this->ajaxReturn($data);
    }
    
    /**
     * note_list
     * 笔记列表
     * yangkai
     */
    public function ipam_note_list(){

        $where['add_staff'] = session('door_communist_no');
        $where['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
    	$count =  M('edu_notes')->where($where)->count('notes_id');
    	$this->assign('count', $count);
        $this->assign('notes_list', $notes_list);
    	$this->display('ipam_note_list');
    }

    /**
     * getnoteCount
     * 笔记ajax总数
     * yangkai
     */
    public function getnoteCount(){
        $notes_type = I('post.notes_type');
        $material_cat_type = I('post.material_cat_type');
        $time = I('post.time');
        $time_type = I('post.time_type');
        if (!empty($notes_type)) {
            $where['notes_type'] = $notes_type;
        }
        $where['add_staff'] = session('door_communist_no');
        if (!empty($time)) {
            if($time_type == '1'){
                $where['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT('$time','%Y%m')";
            }else{
                $where['_string'] = "DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT('$time','%Y%m%d')";
            }
        }else{
            $where['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
        }
        if(!empty($material_cat_type)){
            $notes_list = M('edu_notes')->where($where)->select();
            $count = '0';
            foreach ($notes_list as &$list) {
                if(!empty($list['material_id'])){
                    $material_cat = M('edu_material')->where("material_id = $list[material_id]")->getField("material_cat");
                    $cat_type = M('edu_material_category')->where("cat_id=$material_cat")->nbgetField('cat_type');
                    if($cat_type == $material_cat_type){
                        $count++;
                    }
                }  
            }
        }else{
            $count = M('edu_notes')->where($where)->count('notes_id');
        }
        ob_clean();$this->ajaxReturn($count);
    }
    /**
     * note_list
     * 笔记详情
     * yangkai
     */
    public function ipam_note_info(){
    	$notes_id=I('get.notes_id');
    	$notes_info=getNotesInfo($notes_id,'all');
        $notes_info['add_staff'] = getCommunistInfo($notes_info['add_staff']);
        $notes_info['notes_thumb'] = getUploadInfo($notes_info['notes_thumb']);
        $notes_info['title'] = '';
        $notes_info['thumb'] = '';
        $notes_info['cat_type'] = '';
        switch ($notes_info['notes_type']) {
            case '1'://学习
                if(!empty($notes_info['material_id'])){
                    $notes_info['title'] =  M('edu_material')->where("material_id = $notes_info[material_id]")->getField("material_title");
                    $notes_info['thumb'] = getUploadInfo(M('edu_material')->where("material_id = $notes_info[material_id]")->getField("material_thumb"));
                    $material_cat = M('edu_material')->where("material_id = $notes_info[material_id]")->getField("material_cat");
                    $notes_info['cat_type'] = M('edu_material_category')->where("cat_id = $material_cat")->getField("cat_type");
                }
                break;
            case '2'://会议
                if(!empty($notes_info['material_id'])){
                   $notes_info['title'] = M('oa_meeting')->where("meeting_no = $notes_info[material_id]")->getField("meeting_name");
                    $notes_info['thumb'] = getUploadInfo(M('oa_meeting')->where("meeting_no = $notes_info[material_id]")->getField("meetting_thumb"));
                    $notes_info['cat_type'] = M('oa_meeting')->where("meeting_no = $notes_info[material_id]")->getField("meeting_type");
                }
                break;
        }
    	$this->assign('notes_info', $notes_info);

        $notes_list = M('edu_notes')->field('notes_id,notes_thumb,notes_title,notes_content')->limit(5)->order('add_time desc')->select();
        foreach ($notes_list as &$list) {
            $list['notes_content'] = mb_substr(removeHtml($list['notes_content']), 0,20,'utf-8');
            $list['notes_thumb'] = getUploadInfo($list['notes_thumb']);
        }
        $this->assign('notes_list',$notes_list);
    	$this->display('ipam_note_info');
    }
    /**
     * set_edu_note
     * 笔记详情
     * 王宗斌
     */
    public function set_edu_note(){
        $post = I('post.');
        $type = $post['type'];
        $material_id = $post['material_id'];
        $material_title = M('edu_material')->where("material_id = '$material_id'")->getField('material_title');
        $post['notes_type'] = 1;
        $post['add_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        $post['add_staff'] = session('door_communist_no');
        $post['notes_title'] = "学习了".$material_title;
        
        $data = M('edu_notes')->add($post);
        if(!empty($data)){
            $communist_no = session('door_communist_no');
            if(!empty($material_id)){
                // 记录学习笔记记录
                $log_table=M("edu_material_log");
                $log_data["communist_no"]=$communist_no;
                $log_data["material_no"]=$material_id;
                $log_data["log_type"]=2;
                $log_id=$log_table->where($log_data)->getField("log_id");
                $log_data["add_time"]=date("Y-m-d",time());
                if(!empty($log_id)){ // 已经写了学习笔记
                    $log_table->where("log_id={$log_id}")->save($log_data);
                }else{ // 没写学习笔记
                    $log_table->add($log_data);
                    $integral_notes_communist = getConfig('integral_notes_communist');
                    $communist_integral = getCommunistInfo($communist_no,'communist_integral');
                    updateIntegral(1,7,$communist_no,$communist_integral,$integral_notes_communist,'完成学习笔记'); // 给签到党员加积分
                }
            } else {
                $integral_notes_communist = getConfig('integral_notes_communist');
                $communist_integral = getCommunistInfo($communist_no,'communist_integral');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_notes_communist,'完成学习笔记'); // 给签到党员加积分
            }
            if($type == '1'){
                showMsg('success', '操作成功！', U('Edu/ipam_material_videoInfo',array('material_id'=>$material_id)));
            }else{
                showMsg('success', '操作成功！', U('Edu/ipam_material_info',array('material_id'=>$material_id)));
            }
        } else {
            showMsg('error', '操作失败！','');
        }
    }
    /************ 公用方法开始 ************/
    /**
     * 门户考试分页调取
     */
    public function getexamList()
    {
        $communist_no = session('door_communist_no');
        $pagesize = I('get.pagesize');
        $is_exam = I('get.is_exam',1);
        $page = (I('get.page') - 1) * $pagesize;
    	//$edu_list = M('edu_exam')->field('exam_id,exam_thumb,exam_title,exam_date')->limit($page,$pagesize)->select();
    	$exam_list = getExamList('',$communist_no,$is_exam,'',$page,$pagesize);
    	$edu_list = $exam_list['data'];
    	$data = '';
        switch ($is_exam) {
            case '1':
                $span = '<span class="official_bg">正式考</span>&nbsp;';
                $exam_type = '正式考试';
                break;
            case '3':
                $span = '<span class="mock_bg">模拟考</span>&nbsp;';
                $exam_type = '模拟考试';
                break;
            default:
                $exam_type = '已考';
                break;
        }

        $db_questions = M('edu_questions');
    	foreach ($edu_list as $list) {
            $questions_map['questions_id'] = array('in',$list['exam_questions']);
            $questions_score = $db_questions->where($questions_map)->getField('questions_score',true);
            $score_num = 0;
            foreach ($questions_score as $value) {
                $score_num += $value; 
            }
            if($is_exam=='2'){
                $info_exam = 'info_exam_already('.$list['exam_id'].')';
            }else{
                $info_exam = 'info_exam('.$list['exam_id'].','.$is_exam.')';
            }
    	    $add_name = getStaffInfo($list['add_staff']);
    		$data .= '<li class="clearfix" onclick="'.$info_exam.'">
                        <div class="pull-left">
                          <div class="all_list_tit white-space">'.$span.$list['exam_title'].'</div>
                          <div class="all_list_type white-space">类型：'.$exam_type."&nbsp;&nbsp;&nbsp;".'     '.$list['exam_title_type'].'</div>
                        </div>
                        <div class="pull-right all_list_score">'.$score_num.'分</div>
                      </li>';
        }
        ob_clean();$this->ajaxReturn(['content' => $data]);
    }
    /**
     * 门户笔记分页调取
     */
    public function getnoteList()
    {
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;
        $time = I('post.time');
        $time_type = I('post.time_type');
        $notes_type = I('post.notes_type');
        $material_cat_type = I('post.material_cat_type');
        if(!empty($material_cat_type)){
            $where = "n.add_staff = ".session('door_communist_no');
            if (!empty($notes_type)) {
                $where .= " and n.notes_type = $notes_type";
            }
            if (!empty($time)) {
                if($time_type == '1'){
                    $where .= " and DATE_FORMAT(n.add_time,'%Y%m')=DATE_FORMAT('$time','%Y%m')";
                }else{
                    $where .= " and DATE_FORMAT(n.add_time,'%Y%m%d')=DATE_FORMAT('$time','%Y%m%d')";
                }
            }else{
                $where .= " and DATE_FORMAT(n.add_time,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
            }
            $where .= " and c.cat_type = $material_cat_type";
            $edu_list = M('edu_notes as n')->field('n.notes_id,n.notes_thumb,n.notes_title,n.notes_type,n.notes_content,n.add_staff,n.add_time,n.material_id')->join("left join sp_edu_material as m on n.material_id=m.material_id")->where($where)->join("left join sp_edu_material_category as c on m.material_cat = c.cat_id")->order('n.add_time desc')->limit($page,$pagesize)->select();
        }else{
            $where['add_staff'] = session('door_communist_no');
            if (!empty($notes_type)) {
                $where['notes_type'] = $notes_type;
            }
            if (!empty($time)) {
               
                if($time_type == '1'){
                    $where['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT('$time','%Y%m')";
                }else{
                    $where['_string'] = "DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT('$time','%Y%m%d')";
                }
            }else{
                $where['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
            }
            $edu_list = M('edu_notes')->where($where)->field('notes_id,notes_thumb,notes_title,notes_type,notes_content,add_staff,add_time,material_id')->order('add_time desc')->limit($page,$pagesize)->select();
        }
        foreach ($edu_list as &$list) {
            $list['notes_content'] = mb_substr(strip_tags($list['notes_content']),0,13,'utf-8');
            switch ($list['notes_type']) {
                case '1':
                    if(!empty($material_cat_type)){
                        if(!empty($list['material_id'])){
                            $material_cat = M('edu_material')->where("material_id = $list[material_id]")->getField("material_cat");
                            $cat_type = M('edu_material_category')->where("cat_id=$material_cat")->getField('cat_type');
                            $material_title = M('edu_material')->where("material_id = $list[material_id]")->getField("material_title");
                            $material_thumb = M('edu_material')->where("material_id = $list[material_id]")->getField("material_thumb");
                            if(($cat_type == $material_cat_type) && ($material_cat_type == '11')){
                                $data .= "<li class='clearfix'>
                                    <div class='pull-left edu_notes_l'>
                                      <div class='notes_article_bg'>
                                        ".getFormatDate($list['add_time'], "d")."
                                      </div>
                                      <div class='dotted_line'></div>
                                      <img class='article_bg' src='../../../statics/apps/page_portal/portal/images/icon_article.png'>
                                    </div>
                                    <div class='pull-left edu_notes_r'>
                                        <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                                        <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                        <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                                        </a>
                                        <a class='see-details see_news_detail'  href='".U('Edu/ipam_material_info', array('material_id' => $list['material_id']))."'>
                                        <div class='clearfix edu_original'>
                                            <div class='pull-left'>
                                              <div class='edu_original_tit'>".$material_title."</div>
                                              <div class='edu_original_type'>文章详情</div>
                                            </div>
                                            <div class='pull-right edu_original_r'>
                                              <img style='width: 147px;height: 109px;' src='".getUploadInfo($material_thumb)."'>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                </li>";
                            }elseif(($cat_type == $material_cat_type) && ($material_cat_type == '21')){
                                $data .= "<li class='clearfix'>
                                <div class='pull-left edu_notes_l'>
                                    <div class='notes_video_bg'>".getFormatDate($list['add_time'], "d")."</div>
                                    <div class='dotted_line'></div>
                                    <img class='article_bg' src='../../../statics/apps/page_portal/portal/images/icon_video.png'>
                                </div>
                                <div class='pull-left edu_notes_r'>
                                    <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                                    <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                    <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                                    </a>
                                    <a class='see-details see_news_detail'  href='".U('Edu/ipam_material_videoInfo', array('material_id' => $list['material_id']))."'>
                                    <div class='clearfix edu_original'>
                                        <div class='pull-left'>
                                            <div class='edu_original_tit'>".$material_title."</div>
                                            <div class='edu_original_type'>视频详情</div>
                                        </div>
                                        <div class='pull-right edu_original_r'>
                                            <img style='width: 147px;height: 109px;' src='".getUploadInfo($material_thumb)."'>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            </li>";
                            }
                        }
                    }else{
                        if(!empty($list['material_id'])){
                            $material_cat = M('edu_material')->where("material_id = $list[material_id]")->getField("material_cat");
                            $material_title = M('edu_material')->where("material_id = $list[material_id]")->getField("material_title");
                            $cat_type = M('edu_material_category')->where("cat_id=$material_cat")->getField('cat_type');
                            if($cat_type == '11'){
                                /*$data .= "<li class='clearfix'>
                                    <div class='pull-left edu_notes_l'>
                                      <div class='notes_article_bg'>
                                        ".getFormatDate($list['add_time'], "d")."
                                      </div>
                                      <div class='dotted_line'></div>
                                      <img class='article_bg' src='../../../statics/apps/page_portal/portal/images/icon_article.png'>
                                    </div>
                                    <div class='pull-left edu_notes_r'>
                                      <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                      <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                                      <div class='clearfix edu_original'>
                                        <div class='pull-left'>".$material_title."</div>
                                          <div class='edu_original_type'>文章详情</div>
                                        </div>
                                        <div class='pull-right edu_original_r'>
                                          <img style='width: 147px;height: 109px;' src='".getUploadInfo($material_thumb)."'>
                                        </div>
                                      </div>
                                    </div>
                                </li>";*/
                                $data .= "<li class='clearfix'>
                                    <div class='pull-left edu_notes_l'>
                                      <div class='notes_article_bg'>
                                        ".getFormatDate($list['add_time'], "d")."
                                      </div>
                                      <div class='dotted_line'></div>
                                      <img class='article_bg' src='../../../statics/apps/page_portal/portal/images/icon_article.png'>
                                    </div>
                                    <div class='pull-left edu_notes_r'>
                                        <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                                        <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                        <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                                        </a>
                                        <a class='see-details see_news_detail'  href='".U('Edu/ipam_material_info', array('material_id' => $list['material_id']))."'>
                                        <div class='clearfix edu_original'>
                                            <div class='pull-left'>
                                              <div class='edu_original_tit'>".$material_title."</div>
                                              <div class='edu_original_type'>文章详情</div>
                                            </div>
                                            <div class='pull-right edu_original_r'>
                                              <img style='width: 147px;height: 109px;' src='".getUploadInfo($material_thumb)."'>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                </li>";
                            }else{
                                $data .= "<li class='clearfix'>
                                <div class='pull-left edu_notes_l'>
                                    <div class='notes_video_bg'>".getFormatDate($list['add_time'], "d")."</div>
                                    <div class='dotted_line'></div>
                                    <img class='article_bg' src='../../../statics/apps/page_portal/portal/images/icon_video.png'>
                                </div>
                                <div class='pull-left edu_notes_r'>
                                    <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                                    <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                    <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                                    </a>
                                    <a class='see-details see_news_detail'  href='".U('Edu/ipam_material_videoInfo', array('material_id' => $list['material_id']))."'>
                                    <div class='clearfix edu_original'>
                                        <div class='pull-left'>
                                          <div class='edu_original_tit'>".$material_title."</div>
                                          <div class='edu_original_type'>视频详情</div>
                                        </div>
                                        <div class='pull-right edu_original_r'>
                                            <img style='width: 147px;height: 109px;' src='".getUploadInfo($material_thumb)."'>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            </li>";
                            }
                        }else{
                            $data .= "<li class='clearfix'>
                            <div class='pull-left notes_daily_l'>
                                <div class='notes_daily_bg'>".getFormatDate($list['add_time'], "d")."</div>
                                <div class='dotted_line'></div>
                            </div>
                            <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                            <div class='pull-left edu_notes_r'>
                                <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                            </div>
                            </a>
                        </li>";
                        }
                    }
                    break;
                case '2':
                    if(!empty($list['material_id'])){
                        $meeting_name = M('oa_meeting')->where("meeting_no = $list[material_id]")->getField("meeting_name");
                        $meeting_type = M('oa_meeting')->where("meeting_no = $list[material_id]")->getField("meeting_type");
                        $meetting_thumb = M('oa_meeting')->where("meeting_no = $list[material_id]")->getField("meetting_thumb");
                        $data .= "<li class='clearfix'>
                            <div class='pull-left edu_notes_l'>
                                <div class='notes_meeting_bg'>".getFormatDate($list['add_time'], "d")."</div>
                                <div class='dotted_line'></div>
                                <img class='article_bg' src='../../../statics/apps/page_portal/portal/images/icon_meeting.png'>
                            </div>
                            <div class='pull-left edu_notes_r'>
                                <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                                <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                                <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                                </a>
                                <a class='see-details see_news_detail'  href='".U('Meeting/ipam_meeting_detail', array('meeting_no' => $list['material_id'],'type_no' => $meeting_type))."'>
                                <div class='clearfix edu_original'>
                                    <div class='pull-left'>
                                      <div class='edu_original_tit'>".$meeting_name."</div>
                                      <div class='edu_original_type'>会议详情</div>
                                    </div>
                                    <div class='pull-right edu_original_r'>
                                      <img  style='width: 147px;height: 109px;' src='".getUploadInfo($meetting_thumb)."'>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </li>";
                    }else{
                        $data .= "<li class='clearfix'>
                        <div class='pull-left notes_daily_l'>
                            <div class='notes_daily_bg'>".getFormatDate($list['add_time'], "d")."</div>
                            <div class='dotted_line'></div>
                        </div>
                        <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                        <div class='pull-left edu_notes_r'>
                            <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                            <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                        </div>
                        </a>
                      </li>";
                    }
                    break;
                default:
                    $data .= "<li class='clearfix'>
                        <div class='pull-left notes_daily_l'>
                            <div class='notes_daily_bg'>".getFormatDate($list['add_time'], "d")."</div>
                            <div class='dotted_line'></div>
                        </div>
                        <a class='see-details see_news_detail'  href='".U('ipam_note_info', array('notes_id' => $list['notes_id']))."'>
                        <div class='pull-left edu_notes_r'>
                            <div class='edu_notes_r_tit white-space'>".$list['notes_title']."</div>
                            <div class='edu_notes_r_des white-space'>".$list['notes_content']."</div>
                        </div>
                        </a>
                      </li>";
                    break;
            }
        }
    	ob_clean();$this->ajaxReturn(['content' => $data]);
    }
    /**
     * 门户学习资料分页调取
     */
    public function getmaterialList()
    {	$type=I('post.type');
    	$pagesize = I('post.pagesize');
    	$page = (I('post.page') - 1) * $pagesize;
    	//$edu_list = M('edu_exam')->field('exam_id,exam_thumb,exam_title,exam_date')->limit($page,$pagesize)->select();
    	$material_list = $this->get_material_list($type,$page,$pagesize,0);
    	$data = '';
    	foreach ($material_list as $list) {
    		$add_name=getStaffInfo($list['add_staff']);
   
    		$data .= "<li class='w-all mr-5 pt-15 pb-15 hr-sb-f5f5f5'>
                    <a href='#' class='see-details'>
                        <div class=' hr-s-dfdfdf pull-left w-220 h-140 mr-15 over-h po-re' style='height: 120px;'>
                            <img src='".getUploadInfo($list['material_thumb'])."' class='di-b w-all center-ver' style='height: 120px;' alt='图书图片'>
                        </div>
                        <div class='public_card_text_lr_txt'>
                            <div class='mb-20'><img src='../statics/apps/page_portal/images/study_note_icon.png' class='mr-5' alt=''>".$list['material_title']."</div>
                            <div class='public_card_text_lr_txt_content fcolor-99'>作者：".getStaffInfo($list['add_staff'])."</div>
                         
                            <div class='public_card_text_lr_txt_content fcolor-99'>时间：".$list['add_time']."</div>
                            <a href='".U('ipam_material_info',array('material_id'=>$list['material_id']))."' class='w-100 h-30 lh-30 pull-right fsize-14 public_button_stroke-red see-details'>查看详情</a>
                        </div>
                    </a>
                </li>";
    	}
    	ob_clean();$this->ajaxReturn(['content' => $data]);
    }
        /**
     * material_list
     * 获取课件列表
     *$cat_type类型11文章21视频
     */
    public function get_material_list($cat_type,$page,$count,$num=0){
        $cate_map['cat_type'] = $cat_type;
    	$cat_data = M('edu_material_category')->where($cate_map)->getField('cat_id',true);
    	$cat_list = arrToStr($cat_data, ',');
    	$article_list = getMaterialList($cat_list,'','','','','',$page,$count,$num);
    	return $article_list;
    }
    
    /**
     * material_list
     * 获取未结束考试列表
     *
     */
    public function get_exam_list($page,$count){
    	$db_exam =M('edu_exam');
    	$where['status']=array('neq',31);
    	$exam_data = $db_exam->where($where)->order('exam_date desc')->limit($page,$count)->select();
    	return $exam_data;
    }


    /************ 定制学习 ************/
    public function ipam_customization_index(){
        $communist_no = session('door_communist_no');
        $customization = getEduCustomization($communist_no);
        $this->assign('customization',$customization);
        $where['communist_no'] = $communist_no;
        $add_time = M('edu_customization')->where($where)->getField('add_time');
        $days=round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
        // $content = getCommunistInfo($communist_no,'communist_name')."您好！";
        // $content2 = "您已学习了".$times."天";
        $this->assign('days',$days);
        // $this->assign('content1',$content1);
        // $this->assign('content2',$content2);
        $customization_data = M('edu_customization')->where('communist_no="'.$communist_no.'"')->find();
        //百分比
        $percent = getEduCustomization($communist_no);
        $this->assign('percent',$percent);

        $customization_id = M('edu_customization')->where('communist_no="'.$communist_no.'"')->getField('customization_id');
        //视频
        $vedio['customization_id'] = $customization_id;
        $vedio['edu_type'] = 2;
        $vedio_ids = M('edu_customization_log')->where($vedio)->getField('all_data_id');
        if (!empty($vedio_ids)) {
            $vedio_map['material_id'] = array('in',$vedio_ids);
            $material_list = M('edu_material')->where($vedio_map)->select();
            foreach ($material_list as &$m_list) {
                $m_list['material_thumb'] = getUploadInfo($m_list['material_thumb']);
                $m_list['add_time'] = getFormatDate($m_list['add_time'],'Y-m-d');
            }
        }
        //课件
        $material['customization_id'] = $customization_id;
        $material['edu_type'] = 1;
        $material_ids = M('edu_customization_log')->where($material)->getField('all_data_id');
        if (!empty($material_ids)) {
            $material_map['material_id'] = array('in',$material_ids);
            $material_data = M('edu_material')->where($material_map)->select();
            foreach ($material_data as &$data) {
                $data['material_thumb'] = getUploadInfo($data['material_thumb']);
                $data['add_time'] = getFormatDate($data['add_time'],'Y-m-d');
            }
        }
        $this->assign('material_vedio',$material_list);
        $this->assign('material_data',$material_data);

        //考试
        $exam_simulation = getExamList('',$communist_no,3,'',0,10,$customization_data['material_group'],$customization_data['material_data']);//模拟
        $exam_no = getExamList('',$communist_no,1,'',0,10,$customization_data['material_group'],$customization_data['material_data']);//未考
        $exam_already = getExamList('',$communist_no,2,'',0,10,$customization_data['material_group'],$customization_data['material_data']);//已考
        
        $this->assign('exam_simulation',$exam_simulation['data']);
        $this->assign('exam_no',$exam_no['data']);
        $this->assign('exam_already',$exam_already['data']);
        $this->display('Edu/ipam_customization_index');
    }

    /**
    *定制学习笔记
    *刘长军
    */
    public function edu_notes_list_ajax(){
        $year_mo = $_POST['year_mo'];
        $year_mo_day = I('post.year_mo_day');
        $communist_no = session('door_communist_no');
        // $year_mo_day = I('post.year_mo_day');
        $edu_type = I('post.edu_type');
        $material['edu_type'] = $edu_type;//1课件笔记     2视频笔记   
        $material_ids = M('edu_customization_log')->where($material)->getField('all_data_id');
        switch ($edu_type) {
            case 1:
                $src = '../../../statics/apps/page_portal/portal/customization/images/icon_article.png';
                break;
            case 2:
                $src = '../../../statics/apps/page_portal/portal/customization/images/icon_video.png';
                break;
        }
        if($material_ids){
            $maps['material_id'] = array('in',$material_ids);
            $maps['add_staff'] = $communist_no;
            $year_mo_s = $year_mo.'-2'; 
            if($year_mo){
                $maps['_string'] = "DATE_FORMAT(add_time,'%Y%m')=DATE_FORMAT('$year_mo_s','%Y%m')";
            }else if($year_mo_day){
                $maps['_string'] = "DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT('$year_mo_day','%Y%m%d')";
            }
            $notes_list = M('edu_notes')->where($maps)->order('add_time desc')->select();
            if($notes_list){
                foreach ($notes_list as &$value) {
                    $value['material_list'] = M('edu_material')->where("material_id=".$value['material_id']."")->field("material_title,material_thumb")->find();
                    $value['material_list']['material_thumb'] = getUploadInfo($value['material_list']['material_thumb']);
                }
            }
        }
        $data = '';
        if($notes_list){
            foreach ($notes_list as $list) {
                $data .= '<li class="clearfix">
                          <div class="pull-left edu_notes_l">
                              <div class="notes_article_bg">
                                  20
                              </div>
                              <div class="dotted_line"></div>
                              <img class="article_bg" src="'.$src.'">
                          </div>
                          <div class="pull-left edu_notes_r">
                              <a href='.U('ipam_note_info', array('notes_id' => $list['notes_id'])).'><div class="edu_notes_r_tit white-space">'.$list['notes_title'].'</div></a>
                              <div class="edu_notes_r_des white-space">'.$list['notes_content'].'</div>
                              <div class="clearfix edu_original">
                                  <div class="pull-left">
                                      <div class="edu_original_tit">'.$list['material_list']['material_title'].'</div>
                                      <div class="edu_original_type">文章详情</div>
                                  </div>
                                  <div class="pull-right edu_original_r">
                                      <img src="'.$list['material_list']['material_thumb'].'" style="width:165px;height:114px;">
                                  </div>
                              </div>
                          </div>
                      </li>';
            }
        }
        $this->ajaxReturn($data);
    }

}