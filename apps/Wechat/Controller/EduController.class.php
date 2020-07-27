<?php
namespace Wechat\Controller;
use Think\Controller;
use Wxjssdk\JSSDK;
use Edu;
use SebastianBergmann\Comparator\DateTimeComparator;
use Edu\Model\EduNotesModel;
class EduController extends Controller 
{
    /**
    * @name:edu_index
    * @desc：学习
    * @author：王宗彬
    * @addtime:2019-06-05
    * @version：V1.0.0
    **/
    public function edu_index()
    {
        checkLoginWeixin();
        $communist_no = session('wechat_communist');
        $this->assign('communist_no',$communist_no);
        $topic_list = getTopicList($page,$pagesize);
        foreach ($topic_list as $key=>&$list) {
            $list['img_url'] = getUploadInfo($list['topic_img']);
            $list['learn_rate'] = getTopicLearnInfo($list['topic_id'],$communist_no)['rate'];
            $data[$key] = $list['learn_rate'];
        }
        $this->assign('count',count($topic_list));
        $this->assign('topic_list',$topic_list);
        $this->assign('data',json_encode($data));
        $artic_list = getArticleList('19','0','5');
        $array = array();
        foreach ($artic_list['data'] as $item){
            $lists['article_id'] = $item['article_id'];
            $lists['article_title'] = $item['article_title'];//mb_substr(strip_tags($item['article_title']),0,15,'utf-8');
            $lists['article_thumb'] = getUploadInfo($item['article_thumb']);
            $lists['add_time'] = date('Y-m-d',strtotime($item['add_time']));
            $lists['add_staff'] = peopleNoName($item['add_staff']);
            $array[] = $lists;
        }
        $this->assign('array',$array);
        
        $db_customization = M('edu_customization');
        $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');
        if($customization_id){
            $arr = getEduCustomization($communist_no);
            $this->assign('arr',$arr);
            $this->assign('show','1');
        }else{
            $this->assign('show','2');
        }

        $time = date('Y-m-d');
        $where['add_staff'] = $communist_no;
        if (!empty($time)) {
            $where['_string'] = "DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT('$time','%Y%m%d')";
        }
        $where['notes_type'] = 1;
        $notes_list = M('edu_notes')->field('notes_id,notes_title,notes_type,add_time,material_id')->where($where)->order('add_time desc')->limit(0,10)->select();
        foreach ($notes_list as &$list_n) {
            $list_n['add_time'] = getFormatDate($list_n['add_time'], "H:i");
            if(!empty($list_n['material_id'])){
                $material_cat =  M('edu_material')->where("material_id = $list_n[material_id]")->getField("material_cat");
                $cat_type = M('edu_material_category')->where("cat_id=$material_cat")->getField('cat_type');
                switch ($cat_type) {
                    case '11':
                        $list_n['notes_type'] = '课件笔记';
                        break;
                    case '21':
                        $list_n['notes_type'] = '视频笔记';
                        break;
                }
            }else{
                $list_n['notes_type'] = getBdTypeInfo($list_n['notes_type'], "notes_type");
            }
        }
        $this->assign('notes_list',$notes_list);
        $this->display('Edu/edu_index');
    }
    /**
    * @name:edu_mask
    * @desc：定制标签
    * @author：刘长军
    * @addtime:2019-07-07
    * @version：V1.0.0
    **/
    public function edu_mask(){

        $db_groupdata = M('edu_groupdata');

        $list = getBdCodeList('party_level_code','');
        //对应群体
        $data_groupdata = $db_groupdata->where("group_type=1 and status=1")->select();
        //资料标签
        $data_groupdata1 = $db_groupdata->where("group_type=2 and status=1")->select();
        $data['zuzhi'] = $list;
        $data['qunti'] = $data_groupdata;
        $data['biaoqian'] = $data_groupdata1;
        $this->assign('data',$data);
        $this->display('Edu/edu_mask');
    }
    /**
    * @name:edu_mask_save
    * @desc：定制标签
    * @author：刘长军
    * @addtime:2019-07-07
    * @version：V1.0.0
    **/
    
    public function edu_mask_save(){
        $post = I('post.');
        $material_group = I('post.material_group');
        $material_data = I('post.material_data');
        $communist_no = session('wechat_communist');
        $db_customization = M('edu_customization');

        // $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');

        // if($customization_id){
        //     $arr = getEduCustomization($communist_no);
        // }
        $article['communist_no'] = $communist_no;
        $article['material_group'] = $material_group;
        $article['material_data'] = $material_data;
        $article['add_time'] = date('Y-m-d H:i:s');
        $article['update_time'] = date('Y-m-d H:i:s');
        $article['add_staff'] = $communist_no;
        $customization = $db_customization->add($article);
        $customization_id = '';
        if($customization){
            $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');
        }
        getEduCustomizationSave($communist_no,$material_group,$material_data,$customization_id);
        showMsg('success', '操作成功！', U('Edu/edu_index'));
    }
    /**
    * @name:edu_notes_data
    * @desc：ajax 学习笔记
    * @author：王宗彬
    * @addtime:2019-06-10
    * @version：V1.0.0
    **/
    public function edu_notes_data(){
        $time = I('post.time');
        $communist_no = I('post.communist_no');
        $pagesize = I('post.pagesize'); 
        $page = I('post.page');
        $where['add_staff'] = $communist_no;
        if (!empty($time)) {
            $where['_string'] = "DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT('$time','%Y%m%d')";
        }
        $where['notes_type'] = 1;
        $notes_list = M('edu_notes')->field('notes_id,notes_title,notes_type,add_time,material_id')->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
        foreach ($notes_list as &$list) {
            if(!empty($list['material_id'])){
                $material_cat = M('edu_material')->where("material_id = $list[material_id]")->getField("material_cat");
                $cat_type = M('edu_material_category')->where("cat_id=$material_cat")->getField('cat_type');
                switch ($cat_type) {
                    case '11':
                        $notes_type = '课件笔记';
                        break;
                    case '21':
                        $notes_type = '视频笔记';
                        break;
                }
            }else{
                $notes_type = getBdTypeInfo($list['notes_type'], "notes_type");
            }
            $data .= "<li class='over-h ml-12em' >
                <div class='pull-left w-15b po-re pb-20em'>
                    <p class='f-12em lh-24em color-9e'>".getFormatDate($list['add_time'], "H:i")."</p>
                    <img class='po-ab top-0 right-f13em wh-24em bor-ra-100b' src='../../../statics/apps/page_wechat/images/images_edu/notes_time.png' alt='' />
                </div>
                <a href='".U('Edu/edu_notes_info',array('notes_id'=>$list['notes_id']))."' style='vertical-align: bottom;'>
                <div class='pull-left bj_bj w-80b bl-1-dedede pt-8em pl-8em'>
                    <p class='ellipsis-one f-14em icon_medium color-212121 mb-5em pl-20em f-w'>".$list['notes_title']."</p>
                    <p class='f-10em icon_medium fcolor-66 pb-8em pl-20em'>类型：".$notes_type."</p>
                </div>
                </a>
            </li>";
        }
        ob_clean();$this->ajaxReturn(['content' => $data]);
    }


    /***************************************在线党校***********************************************/
    /**
     * @name:edu_material
     * @desc：在线党校首页
     * @author：王宗彬
     * @addtime:2018-05-07
     * @version：V1.0.0
     **/
    public function edu_material()
    {
        $type = I('get.type');
        $this->assign('type',$type);
        $topic_list =  getTopicList('');
        foreach ($topic_list as &$list) {
            $list['topic_img'] = getUploadInfo($list['topic_img']);
        }
        $this->assign('topic_list',$topic_list);
        
        $this->display("Edu/edu_material");
    }
	/**
	 * @name:edu_material_index
	 * @desc：在线党校首页
	 * @author：王宗彬
	 * @addtime:2018-05-07
	 * @version：V1.0.0
	 **/
	public function edu_material_index()
	{
        checkLoginWeixin();
        $zhuanti = I('get.zhuanti');
        $this->assign('zhuanti',$zhuanti);
        $type = I('get.type');
        $this->assign('type',$type);
        $db_material_category = M('edu_material_category');
        $db_material = M('edu_material');
        $customization = I('get.customization');
        $this->assign('customization',$customization);
        if($customization){
            $this->assign('customization',$customization);
            $communist_no = session('wechat_communist');
            $where['communist_no'] = $communist_no;
            $add_time = M('edu_customization')->where($where)->getField('add_time');
            $times=round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
            $content = getCommunistInfo($communist_no,'communist_name')."您好！";
            $content1 = "欢迎进入您的定制学习";
            $content2 = "您已学习了".$times."天";
            $this->assign('content',$content);
            $this->assign('content1',$content1);
            $this->assign('content2',$content2);
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
            $this->assign('material_list',$material_list);
            $this->assign('material_data',$material_data);
            //考试
            // $group_data = M('edu_customization')->where('communist_no="'.$communist_no.'"')->find();
            $exam_list3 = getExamList('',$communist_no,3,'',0,10,$customization_data['material_group'],$customization_data['material_data']);//正式考试未考
            $exam_list1 = getExamList('',$communist_no,1,'',0,10,$customization_data['material_group'],$customization_data['material_data']);
            $exam_list2 = getExamList('',$communist_no,2,'',0,10,$customization_data['material_group'],$customization_data['material_data']);
           
            $this->assign('exam_list3',$exam_list3['data']);
            $this->assign('exam_list2',$exam_list2['data']);
            $this->assign('exam_list1',$exam_list1['data']);
           


            //笔记
            $maps['add_staff'] = $communist_no;
            $maps['material_id'] = array('in',$vedio_ids.','.$material_ids);
            $db_material = M('edu_material');
            $notes_list = M('edu_notes')->where($maps)->limit(6)->order('add_time desc')->select();
            if($notes_list){
                foreach ($notes_list as &$list_s) {
                    if($list_s['material_id']){
                        if($list_s['material_id']){
                            $list_s['material'] = $db_material->where('material_id="'.$list_s['material_id'].'"')->find();
                        }
                        $material_cat = $db_material->where("material_id = '".$list_s['material_id']."'")->getField("material_cat");
                        $cat_type = $db_material_category->where('cat_id="'.$material_cat.'"')->getField('cat_type');
                        switch ($cat_type) {
                            case '11':
                                $list_s['is_video'] = 11;
                                break;
                            case '21':
                                $list_s['is_video'] = 21;
                                break;
                        }
                    }
                    $list_s['add_staff'] = getCommunistInfo($list_s['add_staff']);
                    $list_s['add_time'] = getFormatDate($list_s['add_time'],'H:i');
                    if($list_s['notes_type']==3){
                        $list_s['richang'] = 1;
                    }
                    $list_s['notes_type'] = getBdTypeInfo($list_s['notes_type'], "notes_type");
                    if(!empty($list_s['notes_thumb'])){
                        $list_s['notes_thumb'] = getUploadInfo($list_s['notes_thumb']);
                    }else{
                        $list_s['notes_thumb'] = "/statics/public/images/biji.jpg";
                    }
                }
                foreach ($notes_list as &$list) {
                    switch ($list['is_video']) {
                        case '11':
                            $notes_list2[] = $list;
                            break;
                        case '21':
                            $notes_list1[] = $list;
                            break;
                    }
                }
            }
            $notes_list3 = $notes_list;
            $this->assign('notes_list3',$notes_list3);
            $this->assign('notes_list1',$notes_list1);
            $this->assign('notes_list2',$notes_list2);
        }else{
            $topic_id = I('get.topic_id');
            $this->assign('topic_id',$topic_id);
            $communist_no = session('wechat_communist');
            $maps['topic_id'] = $topic_id;
            $topic_title = M('edu_topic')->where($maps)->getField('topic_title');
            $add_time = M('edu_topic')->where($maps)->getField('add_time');
            $times = round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
            $content = getCommunistInfo($communist_no,'communist_name')."您好！欢迎回到".$topic_title."专题学习，此专题已开展了".$times."天";
            $this->assign('content',$content);

            $percent = getTopicLearnInfo($topic_id,$communist_no);
            $this->assign('percent',$percent);

            $db_cat = M('edu_material_category');
            $cat_list = $db_cat->where("status=1 and cat_type=21")->getField('cat_id', true);
            $cat_list = implode(',', $cat_list);
            $material_list = getMaterialList($cat_list,$communist_no, null, null, null, $topic_id,0,6);
            foreach ($material_list as &$material) {
                $material['material_thumb'] = getUploadInfo($material['material_thumb']);
                $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d');
            }
            $this->assign('material_list',$material_list);
            $cat_list = $db_cat->where("status=1 and cat_type=11")->getField('cat_id', true);
            $cat_list = implode(',', $cat_list);
            $material_data = getMaterialList($cat_list, $communist_no, null, null, null, $topic_id,0,6);
            foreach ($material_data as &$data) {
                $data['material_thumb'] = getUploadInfo($data['material_thumb']);
                $data['add_time'] = getFormatDate($data['add_time'],'Y-m-d');
            }
            $this->assign('material_data',$material_data);
            $exam_list3 = getExamList($topic_id,$communist_no,3,'',0,6);
            $this->assign('exam_list3',$exam_list3['data']);
            $exam_list1 = getExamList($topic_id,$communist_no,1,'',0,6);
            $this->assign('exam_list1',$exam_list1['data']);
            $exam_list2 = getExamList($topic_id,$communist_no,2,'',0,6);
            $this->assign('exam_list2',$exam_list2['data']);
            $material_id_arr = $db_material->where("material_topic=$topic_id")->field('material_id')->select();
            $id_arr = [];
            foreach($material_id_arr as $material_id){
                $id_arr[] = $material_id['material_id'];
            }
            $id_arr = implode(',',$id_arr);
            $maps['add_staff'] = $communist_no;
            $maps['material_id'] = array('in',$id_arr);
            $notes_list = M('edu_notes')->where($maps)->limit(10)->order('add_time desc')->select();
            if ($notes_list) {
                foreach ($notes_list as &$list) {
                    if($list['material_id']){
                        $material_cat = $db_material->where("material_id = '".$list['material_id']."'")->getField("material_cat");
                        $cat_type = $db_material_category->where('cat_id="'.$material_cat.'"')->getField('cat_type');
                        switch ($cat_type) {
                            case '11':
                                $list['is_video'] = 11;
                                break;
                            case '21':
                                $list['is_video'] = 21;
                                break;
                        }
                    }
                    if($list['notes_type']==3){
                        $list['richang'] = 1;
                    }
                    if(!empty($list['notes_thumb'])){
                        $list['notes_thumb'] = getUploadInfo($list['notes_thumb']);
                    }else{
                        $list['notes_thumb'] = "/statics/public/images/biji.jpg";
                    }
                    $list['add_time'] = getFormatDate($list['add_time'],"m-d H:i");
                    $list['notes_type'] = getBdTypeInfo($list['notes_type'],'notes_type');;
                }
                $notes_list3 = $notes_list;
                foreach ($notes_list as &$notes) {
                    switch ($notes['is_video']) {
                        case '11':
                            $notes_list2[] = $notes;
                            break;
                        case '21':
                            $notes_list1[] = $notes;
                            break;
                    }
                }
               
                $this->assign('notes_list3',$notes_list3);
                $this->assign('notes_list1',$notes_list1);
                $this->assign('notes_list2',$notes_list2);
            }
        }
		$this->display('Edu/edu_material_index');
	}
	/**
	 * @edu_material_list
	 * @desc：在线党校栏目列表页
	 * @author：王宗彬
	 * @addtime:2018-05-07
	 * @version：V1.0.0
	 **/
	public function edu_material_list()
	{
		$type = I('get.type');
		$cat_list = M('edu_material_category')->where("cat_type = '$type'")->select();
		$this->assign("cat_list",$cat_list);
		$this->display('Edu/edu_material_list');
	}
	
	/**
	 * @edu_material_list_list
	 * @desc：在线党校列表页
	 * @author：王宗彬
	 * @addtime:2018-05-07
	 * @version：V1.0.0
	 **/
	public function edu_material_list_list()
	{
		
		$cat_id = I('get.cat_id');
		$type = I('get.type');
		$this->assign('type',$type);
		$material_list = getMaterialList($cat_id,'','','','','','','');
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($material_list as &$list){
			$list['material_thumb'] = getUploadInfo($list['material_thumb']);
			$list['add_staff'] = $communist_name_arr[$list['add_staff']];
			$list['add_time'] = getFormatDate($list['add_time'],'Y-m-d');
			$list['material_title'] = mb_substr($list['material_title'], 0, 7, 'utf-8');
		}
		$this->assign('material_list',$material_list);
		$this->display('Edu/edu_material_list_list');
	}
    /**
     * @name:edu_edu_material_del
     * @desc：
     * @author：王宗彬
     * @addtime:2018-05-07
     * @version：V1.0.0
     **/
    public function edu_edu_material_del(){
        $communist_no = session('wechat_communist');
        $customization_id = M('edu_customization')->where('communist_no="'.$communist_no.'"')->getField('customization_id');
        $res = M('edu_customization')->where('communist_no="'.$communist_no.'"')->delete();
        if(!empty($res)){
            M('edu_customization_log')->where('customization_id="'.$customization_id.'"')->delete();
            showMsg('success', '操作成功！',U('Edu/edu_index'));
        }else{
            showMsg('error', '操作失败！','');
        }


    }
	/**
	 * @edu_material_info
	 * @desc：在线党校文章详情
	 * @author：王宗彬
	 * @addtime:2018-05-07
	 * @version：V1.0.0
	 **/
	public function edu_material_info()
	{
        checkLoginWeixin();
        $notes_id = I('get.notes_id');
        $this->assign('notes_id',$notes_id);
        $type = I('get.type');
        $this->assign('type',$type);
        $customization = I('get.customization');
        $this->assign('customization',$customization);
        $db_material = M('edu_material');
		$material_id = I('get.material_id');
        $communist_no = session('wechat_communist');
		$material_list = getMaterialInfo($material_id);
        $material_list['material_duration_integral'] = getConfig('integral_article');
        $res = M('edu_material_communist')->where("material_id='$material_id' and communist_no='$communist_no'")->select();
        if(empty($res)){
            $data['communist_no'] = $communist_no;
            $data['add_staff'] = $communist_no;
            $data['material_id'] = $material_id;
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $data['is_read'] = 1;
            if($data['communist_no']){
                M('edu_material_communist')->add($data);
            }
        }
        // 判断今天是否已经学习该学习资料
        $log_table=M("edu_material_log");
        $log_data["communist_no"]=$communist_no;
        $log_data["material_no"]=$material_id;
        $log_data["add_time"]=date("Y-m-d",time()); // 
        $log_data["log_type"]=1;
        $log_id=$log_table->where($log_data)->getField("log_id");
		if(!empty($log_id)){
            $this->assign('is_has',1);
        } else {
            $this->assign('is_has',2);
        }
		$this->assign('material_list',$material_list);
        $db_material->where("material_id=$material_id")->setInc('read_num');
        //学习人员
        $where['material_id'] = $material_id;
        $material_info['log_list_count'] = M('edu_material_communist')->where($where)->count("DISTINCT communist_no");
        $material_info['log_list'] = M('edu_material_communist')->field('communist_no')->where($where)->limit(3)->order('add_time desc')->group('communist_no')->select();
        foreach ($material_info['log_list'] as &$log) {
            $log['communist_avatar'] = getCommunistInfo($log['communist_no'],'communist_avatar');
            if(empty($log['communist_avatar'])){
                $log['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
            $log['communist_name'] = getCommunistInfo($log['communist_no']);
        }
        $material_info['notes_list'] = M('edu_notes')->field('notes_id,notes_content,add_time,add_staff')->where($where)->order('add_time desc')->select();
        foreach ($material_info['notes_list'] as &$material) {
            $material['notes_content'] = mb_substr(strip_tags($material['notes_content']),0,35,'utf-8');
            $material['add_staff'] = getCommunistInfo($material['add_staff']);
            $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d');
        }
        $this->assign('material_info',$material_info);
		$this->display('Edu/edu_material_info');
	}
    public function set_integral_material(){
        $communist_no = session('wechat_communist');
        $db_period = M('edu_material_period');
        $db_material = M('edu_material');
        $material_id = I('post.material_id');
        if($material_id){
            $where['material_id'] = $material_id;
        }
        $material_cat = M('edu_material')->where("material_id = '$material_id'")->getField('material_cat');
        $cat_type = M('edu_material_category')->where("cat_id = '$material_cat'")->getField('cat_type');
        // 记录学习记录
        $log_table=M("edu_material_log");
        $log_data["communist_no"]=$communist_no;
        $log_data["material_no"]=$material_id;
        $log_data["add_time"]=date("Y-m-d",time()); // 
        if($cat_type == 21){
            $log_data["log_type"]=2;
        } else {
            $log_data["log_type"]=1;
        }
        $log_id=$log_table->where($log_data)->getField("log_id");
        if(!empty($log_id)){ // 已经看了该学习资料
            $log_table->where("log_id={$log_id}")->save($log_data);
            $this->ajaxReturn(false);
        }else{
            $log_table->add($log_data);
            // 学习视频加积分
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            $integral_article = getConfig('integral_article');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_article,'学习文章');
        }

        $period_score = $db_material->where($where)->getField('period_score');
        $post['material_id'] = $material_id;
        $post['period_score'] = $period_score;
        $post['communist_no'] = $communist_no;
        $post['add_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        $post['add_staff'] = session('wechat_communist');
        $post['period_year'] = date('Y');
        $post['period_month'] = date('m');
        $db_period->add($post);
        $this->ajaxReturn(true);
    }
	/**
	 * @edu_material_videoInfo
	 * @desc：在线党校视频详情
	 * @author：王宗彬
	 * @addtime:2018-05-07
	 * @version：V1.0.0
	 **/
	public function edu_material_videoInfo()
	{
		checkLoginWeixin();
        $notes_id = I('get.notes_id');
        $this->assign('notes_id',$notes_id);
        $type = I('get.type');
        $this->assign('type',$type);
        $customization = I('get.customization');
        $this->assign('customization',$customization);
        $db_material = M('edu_material');
		$material_id = I('get.material_id');
		$material_list = getMaterialInfo($material_id);
        $communist_no = session('wechat_communist');
        $material_list['material_duration_integral'] = getConfig('integral_video');

        $res = M('edu_material_communist')->where("material_id='$material_id' and communist_no='$communist_no'")->select();
        if(empty($res)){
            $data['communist_no'] = $communist_no;
            $data['add_staff'] = $communist_no;
            $data['material_id'] = $material_id;
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $data['is_read'] = 1;
            if($data['communist_no']){
                M('edu_material_communist')->add($data);
            }
        }
        // 判断今天是否已经学习该学习资料
        $log_table=M("edu_material_log");
        $log_data["communist_no"]=$communist_no;
        $log_data["material_no"]=$material_id;
        $log_data["add_time"]=date("Y-m-d",time()); // 
        $log_data["log_type"]=2;
        $log_id=$log_table->where($log_data)->getField("log_id");
        if(!empty($log_id)){
            $this->assign('is_has',1);
        } else {
            $this->assign('is_has',2);
        }
        $this->assign('material_list',$material_list);
        $db_material->where("material_id=$material_id")->setInc('read_num');
        //学习人员
        $where['material_id'] = $material_id;
        $material_info['log_list_count'] = M('edu_material_communist')->where($where)->count("DISTINCT communist_no");
        $material_info['log_list'] = M('edu_material_communist')->field('communist_no')->where($where)->limit(3)->order('add_time desc')->group('communist_no')->select();
        foreach ($material_info['log_list'] as &$log) {
            $log['communist_avatar'] = getCommunistInfo($log['communist_no'],'communist_avatar');
            if(empty($log['communist_avatar'])){
                $log['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
            $log['communist_name'] = getCommunistInfo($log['communist_no']);
        }
        $material_info['notes_list'] = M('edu_notes')->field('notes_id,notes_content,add_time,add_staff')->where($where)->order('add_time desc')->select();
        foreach ($material_info['notes_list'] as &$material) {
            $material['notes_content'] = mb_substr(strip_tags($material['notes_content']),0,35,'utf-8');
            $material['add_staff'] = getCommunistInfo($material['add_staff']);
            $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d');
        }
        $this->assign('material_info',$material_info);
		$this->display('Edu/edu_material_videoInfo');
	}
    /**
     * @edu_learn_look
     * @desc：以学人员列表
     * @author：王宗彬
     * @addtime:2018-05-07
     * @version：V1.0.0
     **/
    public function edu_learn_look(){

        $material_id = I('get.material_id');
        $where['material_id'] = $material_id;
        $communist_info = M('edu_material_communist')->field('communist_no')->where($where)->order('add_time desc')->group('communist_no')->select();
        foreach ($communist_info as &$communist) {
            $communist['communist_avatar'] = getCommunistInfo($communist['communist_no'],'communist_avatar');
            if(empty($communist['communist_avatar'])){
                $communist['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
            $communist['communist_name'] = getCommunistInfo($communist['communist_no']);
        }
        $this->assign('communist_info',$communist_info);
        $this->display('edu_learn_look');
    }


	public function set_integral(){
        $communist_no = session('wechat_communist');
        $db_period = M('edu_material_period');
        $db_material = M('edu_material');
        $material_id = I('post.material_id');
        if($material_id){
            $where['material_id'] = $material_id;
        }
        $material_cat = M('edu_material')->where("material_id = '$material_id'")->getField('material_cat');
        $cat_type = M('edu_material_category')->where("cat_id = '$material_cat'")->getField('cat_type');
        // 记录学习记录
        $log_table=M("edu_material_log");
        $log_data["communist_no"]=$communist_no;
        $log_data["material_no"]=$material_id;
        $log_data["add_time"]=date("Y-m-d",time()); // 
        if($cat_type == 21){
            $log_data["log_type"]=2;
        } else {
            $log_data["log_type"]=1;
        }
        $log_id=$log_table->where($log_data)->getField("log_id");
        if(!empty($log_id)){ // 已经看了该学习资料
            $log_table->where("log_id={$log_id}")->save($log_data);
            $this->ajaxReturn(false);
        }else{
            $log_table->add($log_data);// 学习视频加积分
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            $integral_video = getConfig('integral_video');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_video,'学习视频');
        }
        $period_score = $db_material->where($where)->getField('period_score');
        $post['material_id'] = $material_id;
        $post['period_score'] = $period_score;
        $post['communist_no'] = $communist_no;
        $post['add_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        $post['add_staff'] = session('wechat_communist');
        $post['period_year'] = date('Y');
        $post['period_month'] = date('m');
        $db_period->add($post);
        $this->ajaxReturn(true);
    }
	/**
	 * @name:edu_material_do_save
	 * @desc：学习笔记
	 * @author：王宗彬
	 * @addtime:2018-04-25
	 * @version：V1.0.0
	 **/
	public function edu_material_do_save()
	{
		$post = I('post.');
        $type = $post['type'];
		$material_id = $post['material_id'];
		$material_title = M('edu_material')->where("material_id = '$material_id'")->getField('material_title');
        $material_cat = M('edu_material')->where("material_id = '$material_id'")->getField('material_cat');
        $cat_type = M('edu_material_category')->where("cat_id = '$material_cat'")->getField('cat_type');
		$post['notes_type'] = 1;
		$post['add_time'] = date('Y-m-d H:i:s');
		$post['update_time'] = date('Y-m-d H:i:s');
		$post['add_staff'] = session('wechat_communist');
		$post['notes_title'] = "学习了".$material_title;
        
		$data = M('edu_notes')->add($post);
		if(!empty($data)){
            $communist_no = session('wechat_communist');
            if(!empty($material_id)){
                // 记录学习笔记记录
                $log_table=M("edu_material_log");
                $log_data["communist_no"]=$communist_no;
                $log_data["material_no"]=$material_id;
                if($cat_type == 21){
                    $log_data["log_type"]=2;
                } else {
                    $log_data["log_type"]=1;
                }
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
                showMsg('success', '操作成功！', U('Edu/edu_material_videoInfo',array('material_id'=>$material_id)));
            }else{
                showMsg('success', '操作成功！', U('Edu/edu_material_info',array('material_id'=>$material_id)));
            }
		} else {
			showMsg('error', '操作失败！','');
		}
	}
	/***************************************考试列表***********************************************/
    /**
    * @name:edu_exam_analysis
    * @desc：学习分析
    * @author：王宗彬
    * @addtime:2019-07-05
    * @version：V1.0.0
    **/
    public function edu_exam_analysis(){
        $topic_id = I('get.topic_id');
        $this->assign('topic_id',$topic_id);
        $communist_no = session('wechat_communist');
        $maps['topic_id'] = $topic_id;
        $topic_title = M('edu_topic')->where($maps)->getField('topic_title');
        $integral = getEduTopicIntegral($topic_id,$communist_no);
        $this->assign('topic_title',$topic_title);
        $this->assign('integral',$integral['integral']);

        $percent = getTopicLearnInfo($topic_id,$communist_no);
        $this->assign('percent',$percent);
        $data = getEduTopicIntegral($topic_id,$communist_no);
        $this->assign('data',$data);
        $map['_string'] = "memo='学习文章' OR memo='学习视频' OR memo='完成学习笔记' OR memo='通过考试'";
        $communist_integra = M('ccp_integral_log')->field('log_relation_no as communist_no,sum(change_integral) as integral')->limit(0,10)->where($map)->order('integral desc')->group('log_relation_no')->select();
        foreach ($communist_integra as &$communist) {
            $communist['communist_name'] = getCommunistInfo($communist['communist_no']);
            $communist['communist_avatar'] = getCommunistInfo($communist['communist_no'],'communist_avatar');
            if(empty($communist['communist_avatar'])){
                $communist['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
            }
        }
        $this->assign('communist_integra',$communist_integra);

        $this->display('Edu/edu_exam_analysis');
    }


    /**
    * @name:edu_exam_list
    * @desc：考试列表
    * @author：王宗彬
    * @addtime:2018-05-07
    * @version：V1.0.0
    **/
    public function edu_exam_list(){
        checkLoginWeixin();
        $ad_list = getbannerList(8);
        $this->assign('ad_list',$ad_list);
        $communist_no = session('wechat_communist');
        $exam_data1 = $this->edu_exam_list_data($communist_no,1);//未考
        $exam_data2 = $this->edu_exam_list_data($communist_no,2);//已考
        $exam_data3 = $this->edu_exam_list_data($communist_no,3);//模拟

        $this->assign('exam_data1',$exam_data1);
        $this->assign('exam_data2',$exam_data2);
        $this->assign('exam_data3',$exam_data3);
        $this->display('Edu/edu_exam_list');
    }

    public function edu_exam_list_data($communist_no,$is_exam)
    {
       
        $db_questions = M('edu_questions');
        $page = 0;
        $pagesize = 100000;
        
        $exam_list = getExamList('',$communist_no,$is_exam,'',$page,$pagesize);
        $exam_data = $exam_list['data'];
        if ($exam_data) {
            foreach ($exam_data as &$list){
                $list['exam_thumb'] = getUploadInfo($list['exam_thumb']);
                if (!empty($list['exam_questions'])){
                    $list['questions_num'] = $db_questions->where("questions_id in({$list['exam_questions']})")->count();
                    $list['score'] = $db_questions->where("questions_id in({$list['exam_questions']})")->sum('questions_score');
                    // 判断考试是否进行完成   1 未参加 2 进行中
                    $exam_join_map['communist_no'] = $communist_no;
                    $exam_join_map['exam_id'] = $list['exam_id'];
                    $answer_count = M('edu_exam_answer')->where($exam_join_map)->count();
                    if($answer_count == 0){
                        $list['join_flag'] = 1; // 未参加
                        $list['join_val'] = '未参加'; // 未参加
                    } else if($answer_count < $list['questions_num']){
                        $list['join_flag'] = 2; // 未参加
                        $list['join_val'] = '进行中'; // 未参加
                    }
                }else{
                    $list['questions_num'] = 0;
                    $list['score'] += 0;
                }
                if(empty($list['score'])){
                     $list['score'] = 0;
                }
            }
            return $exam_data;
        }
    }
    /**
     * @name:edu_exam_my
     * @desc：我参加的考试
     * @author：王宗彬
     * @addtime:2018-05-07
     * @version：V1.0.0
     **/
    public function edu_exam_my()
    {
        $is_exam = I('get.is_exam');
        $db_questions = M('edu_questions');
        $page = 0;
        $pagesize = 100000;
        $communist_no = session('wechat_communist');
        $exam_list = getExamList('',$communist_no,$is_exam,'',$page,$pagesize);
        $exam_data = $exam_list['data'];
        if ($exam_data) {
            foreach ($exam_data as &$list){
                $list['exam_thumb'] = getUploadInfo($list['exam_thumb']);
                if (!empty($list['exam_questions'])){
                    $list['questions_num'] = $db_questions->
                    where("questions_id in({$list['exam_questions']})")->count();
                    $list['score'] = $db_questions->
                    where("questions_id in({$list['exam_questions']})")->sum('questions_score');
                }else{
                    $list['questions_num'] = 0;
                    $list['score'] += 0;
                }
            }
            $this->assign('exam_data',$exam_data);
        }
        $this->display('Edu/edu_exam_my');
    }
    
    /**
     * @name:edu_exam_info
     * @desc：考试详情
     * @author：王宗彬
     * @addtime:2018-05-07
     * @version：V1.0.0
     **/
    public function edu_exam_info()
    {
        $zhuanti = I('get.zhuanti');
        $this->assign('zhuanti',$zhuanti);
        $customization_type = I('get.customization_type');
        $this->assign('customization_type',$customization_type);
        $type_topic=I('get.type_topic');
        $this->assign('type_topic',$type_topic);
        $topic_id=I('get.topic_id');
        $this->assign('topic_id',$topic_id);
        $exam_id = I('get.exam_id');
        $zhengshi = I('get.zhengshi');
        $moni = I('get.moni');
        $this->assign('zhengshi',$zhengshi);
        $this->assign('moni',$moni);
        $type = I('get.type');
        $this->assign('type',$type);
        $communist_no = session('wechat_communist');
        if($type == '1'){
            $exam_list = getExamCommunistInfo($exam_id,$communist_no);
        }else{
            $exam_list = getExamCommunistInfo($exam_id,$communist_no);
            foreach ($exam_list['questions_list'] as &$list) {
                $list['questions_item'] = explode(',',$list['questions_item']);
            }
        }
        $count_num = count($exam_list['questions_list']);
        $this->assign('exam_list',$exam_list);
        $length = count($exam_list['questions_list']);
        $this->assign('length',$length);
        $this->assign('exam_list1',json_encode($exam_list));

        $this->assign('count_num',$count_num);
        $this->display('Edu/edu_exam_info');
    }

    /**
    /**
     *  set_edu_exam_questions
     * @desc 写入答案
     * @user 王宗彬-刘长军
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function set_edu_exam_questions()
    {
        $questions_str = $_POST['questions_arr'];
        $exam_id = I("post.exam_id");
        $end_time = I("post.time_lave");
        $questions_arr=str_replace('{','',$questions_str);
        $questions_arr=str_replace('}','',$questions_arr);
        $questions_arr = explode('","',$questions_arr);
        $str_questions_id = $_POST['str_questions_id'];
        $str_questions_id = substr($str_questions_id,0,strlen($str_questions_id)-1); 
        $keyy = explode(',',$str_questions_id);
        foreach ($keyy as $key=>$yy) {
            foreach($questions_arr as &$list){
                $list1 = explode(':',$list);
                $list1[0] = str_replace('"','',$list1[0]);
                $list1[1] = str_replace('"','',$list1[1]);
                if(($key+1) == $list1[0]){
                    $q_arr[$yy] = $list1[1];
                }else{
                    if(!$q_arr[$yy]){
                        $q_arr[$yy] = '';
                    }
                }
            }
        }
        $db_edu_exam_log = M('edu_exam_log');
        $db_edu_exam_answer = M('edu_exam_answer');
        //查询当前考试人员名称
        $communist_no = session('wechat_communist');
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
        
        $flag = $db_edu_exam_log->add($question);
        if ($result && $flag) {
            M()->commit();
            $start_time = I('post.exam_time');
            $time = $start_time*60;
            $end_time = explode(':',$end_time);
            $end_time = $end_time['0'] * 60 + $end_time['1'];
            $time_lave = $time - $end_time;
           
            $data['point_amount'] = $point_amount;//得分
            $data['count'] = $count;//题数
            $data['accuracy'] = ($accuracy/$count)*100;//正确率
            $data['time_lave'] = gmstrftime('%M:%S',$time_lave);
            $data['exam_id'] = $exam_id;
            $data['exam_topic'] = $exam_topic;
            $this->assign('data',$data);
            $zhuanti = I("post.zhuanti");
            $this->assign('zhuanti',$zhuanti);
            $this->display('Edu/edu_notes_guidance');
            // showMsg('success', '操作成功！', U('Edu/edu_exam_info',array('is_exam'=>1)));
        } else {
            // showMsg('error', '操作失败！','');
            $this->display('Edu/edu_notes_list');
       }
    }
    /***************************************学习笔记***********************************************/
    /**
     * @edu_notes_list
     * @desc：学习笔记
     * @author：王宗彬
     * @addtime:2018-06-04
     * @version：V1.0.0
     **/
    public function edu_notes_list()
    {
        checkLoginWeixin();
        $type = I('get.type');
        if($type == '1'){
            $notes_type = '1';
        }
        $this->assign('type',$type);
        $db_edu_material = M("edu_material");
        $communist_no = session('wechat_communist');
        $this->assign('communist_no',$communist_no);
        $page = 0;
        $pagesize = 100000;
        $note_time = date('Y-m-d');
        $this->assign('note_time',$note_time);
        $notes_list = (new \Edu\Model\EduNotesModel())->getNotesList($communist_no, $page, $pagesize, '', '', $notes_type, '',$note_time);
        foreach ($notes_list as &$list) {
            $ttt['material_id'] = $list['material_id'];
            $list['update_time'] = getFormatDate($list['update_time'], "Y-m-d H:i");
            $list['communist_avatar'] =  getCommunistInfo($list['add_staff'],'communist_avatar');
            $list['add_staff'] = getCommunistInfo($list['add_staff']);
            $list['add_time'] = getFormatDate($list['add_time'],'H:i');
            $list['material_thumb'] = getUploadInfo($list['material_thumb']);
            if ($list['notes_type']) {
                $list['notes_type_name'] = getBdTypeInfo($list['notes_type'], "notes_type");
            }
            if ($list['topic_type']) {
                $list['topic_type'] = getTopicInfo($list['topic_type']);
            }
            $list['notes_thumb'] = getUploadInfo($list['notes_thumb']);
            
            $list['material'] = $db_edu_material->where('material_id="'.$list['material_id'].'"')->find();
            if($list['material']){
                $material_cat = $db_edu_material->where("material_id = '".$list['material_id']."'")->getField('material_cat');
                $cat_type = M("edu_material_category")->where("cat_id = $material_cat")->getField('cat_type');
                if($cat_type==21){
                    $list['material']['material_thumb'] = getUploadInfo($list['material']['material_thumb']);
                    $list['material']['material_vedio'] = __ROOT__."/uploads/video/".$list['material']['material_vedio'];
                    $list['type_video'] = 'yes';
                }else{
                    $list['material']['material_thumb'] = getUploadInfo($list['material']['material_thumb']);
                    $list['type_video'] = 'no';
                }  
            }
        }
        $this->assign('notes_list',$notes_list);
        $this->display('Edu/edu_notes_list');
    }
    /**
     * @edu_notes_list_data
     * @desc：学习笔记
     * @author：王宗彬
     * @addtime:2018-06-04
     * @version：V1.0.0
     **/
    public function edu_notes_list_data()
    {
        $db_edu_material = M("edu_material");
        $communist_no = I('post.communist_no');;
        $pagesize = 100000;
        $note_time = I('post.note_time');
        $topic_id = I('post.topic_id');
        $notes_type = I('post.notes_type');
        $material_id = I('post.material_id');
        $notes_list = (new EduNotesModel())->getNotesList($communist_no, $page, $pagesize, $keyword, $topic_id,$notes_type,$material_id,$note_time);
        foreach ($notes_list as &$list) {
            $ttt['material_id'] = $list['material_id'];
            $list['update_time'] = getFormatDate($list['update_time'], "Y-m-d H:i");
            $list['communist_avatar'] =  getCommunistInfo($list['add_staff'],'communist_avatar');
            $list['add_staff'] = getCommunistInfo($list['add_staff']);
            $list['add_time'] = getFormatDate($list['add_time'],'H:i');
            $list['material_thumb'] = getUploadInfo($list['material_thumb']);
            if ($list['notes_type']) {
                $list['notes_type_name'] = getBdTypeInfo($list['notes_type'], "notes_type");
            }
            if ($list['topic_type']) {
                $list['topic_type'] = getTopicInfo($list['topic_type']);
            }
            $list['notes_thumb'] = getUploadInfo($list['notes_thumb']);
            
            $list['material'] = $db_edu_material->where('material_id="'.$list['material_id'].'"')->find();
            if($list['material']){
                $material_cat = $db_edu_material->where("material_id = '".$list['material_id']."'")->getField('material_cat');
                $cat_type = M("edu_material_category")->where("cat_id = $material_cat")->getField('cat_type');
                if($cat_type==21){
                    $data .= "<div class='edu_notes_con mt-12em mb-12em p-12em clearfix w-100b' >
                        <div class='pull-left mr-8em'>
                            <img class='w-32em h-32em' src='../../../statics/apps/page_wechat/public/images/edu_notes_shipin.png'  />
                        </div>
                        <div class='pull-left w-85b over-h'>
                            <a href='".U('Edu/edu_notes_info',array('notes_id'=>$list['notes_id']))."'>
                            <div class='color-21 f-14em f-w'>".$list['notes_title']."</div>
                            <div class='f-12em color-a3'>".$list['add_time']."</div>
                            <div class='icon_regular color-21 f-14em mt-15em'>".$list['notes_content']."</div>
                            </a>
                            <a href='".U('Edu/edu_material_videoInfo',array('material_id'=>$list['material_id']))."'>
                            <div class='border-3em-de border-radius-25em clearfix mt-15em p-5em'>
                                <img class='w-281em h-178em over-h m-a' src='".getUploadInfo($list['material']['material_thumb'])."' alt=''/>
                                <div class='icon_regular f-16em color-12 f-w'>".$list['material']['material_title']."</div>     
                            </div>
                            </a>
                        </div>
                    </div>";
                }else{
                    $data .= "<div class='edu_notes_con mt-12em p-12em clearfix w-100b' >
                        <div class='pull-left mr-8em'>
                            <img class='w-32em h-32em' src='../../../statics/apps/page_wechat/public/images/edu_notes_tu1.png' />
                        </div>
                        <div class='pull-left w-85b over-h'>
                            <a href='".U('Edu/edu_notes_info',array('notes_id'=>$list['notes_id']))."'>
                            <div class='color-21 f-14em f-w'>".$list['notes_title']."</div>
                            <div class='f-12em color-a3'>".$list['add_time']."</div>
                            <div class='icon_regular color-21 f-14em mt-15em'>".$list['notes_content']."</div>
                            </a>
                            <a href='".U('Edu/edu_material_info',array('material_id'=>$list['material_id']))."'>
                            <div class='border-3em-de border-radius-25em clearfix mt-15em p-8em'>
                                <div class='pull-left w-70b' style='width: 69%;'>
                                    <div class='color-12 f-16em f-w mr-10em'>".$list['material']['material_title']."</div>
                                    <div class='f-11em color-a4 mt-9em'>".$list['topic_type']."</div>        
                                </div>
                                <div class='pull-left'>
                                    <img class='w-80em h-58em over-h' src='".getUploadInfo($list['material']['material_thumb'])."' alt='' />           
                                </div>                      
                            </div>
                            </a>                      
                        </div>          
                    </div>"; 
                }
            }else{
                $data .= "<div class='edu_notes_con mt-12em p-12em clearfix w-100b' >
                    <div class='pull-left mr-8em'>
                        <img class='w-32em h-32em' src='../../../statics/apps/page_wechat/public/images/edu_notes_daily.png' />
                    </div>  
                    <a href='".U('Edu/edu_notes_info',array('notes_id'=>$list['notes_id']))."'>
                    <div class='pull-left w-85b over-h'>
                        <div class='color-21 f-14em f-w'>".$list['notes_title']."</div>
                        <div class='f-12em color-a3'>".$list['add_time']."</div>
                        <div class='icon_regular color-21 f-14em mt-15em'>".$list['notes_content']."</div>
                    </div> 
                    </a>                 
                </div>";
            }
        }
        $this->ajaxReturn($data);
    }
    /**
     * @name  edu_notes_edit()
     * @desc  学习笔记数据添加
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @addtime 2017-06-04
     *  @updatetime 2017-12-20  增加笔记所属专题
     * */
    public function edu_notes_edit()
    {
        $type = I('get.type');
        $this->assign('type',$type);
        $this->display('Edu/edu_notes_edit');
    }
    /**
     * @name  edu_notes_do_save()
     * @desc  学习笔记数据添加
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @addtime 2017-06-04
     *  @updatetime 2017-12-20  增加笔记所属专题
     * */
    public function edu_notes_do_save()
    {
        $data = I('post.');
        if(!empty($_FILES['file']['name'])){
            $upload = upFiles($_FILES["file"], 'oa', '1', session('wechat_communist'));
        }
        $data['notes_thumb'] = $upload['upload_id'];
        $data['add_staff'] = session('wechat_communist');
        $data['update_time'] = date('Y-m-d H:i:s');
        $data['add_time'] = date('Y-m-d H:i:s');
        $result = M('edu_notes')->add($data);
        if(!empty($result)){
            $integral_notes_communist = getConfig('integral_notes_communist');
            $communist_integral = getCommunistInfo(session('wechat_communist'),'communist_integral');
            updateIntegral(1,7,session('wechat_communist'),$communist_integral,$integral_notes_communist,'完成学习笔记'); // 给签到党员加积分
            showMsg('success','提交成功',U('Edu/edu_notes_list'));
        } else {
            showMsg('error','上传失败',U('Edu/edu_notes_edit'));
        }
    }
    /**
     * @edu_notes_info
     * @desc：学习笔记详情
     * @author：王宗彬
     * @addtime:2018-06-04
     * @version：V1.0.0
     **/
    public function edu_notes_info()
    {
        $notes_id = I('get.notes_id'); // I方法获取数据
        $notes_info = (new \Edu\Model\EduNotesModel())->getNotesInfo($notes_id);
        $notes_info['communist_name'] = getCommunistInfo($notes_info['add_staff']);
        switch ($notes_info['notes_type']) {
            case '1':
                $where['material_id'] = $notes_info['material_id'];
                $material = M('edu_material')->where($where)->field('material_id,material_title,material_thumb,material_vedio')->find();
                $notes_info['material_id'] = $material['material_id'];
                $notes_info['material_title'] = $material['material_title'];
                $notes_info['material_thumb'] = getUploadInfo($material['material_thumb']);
                if(!empty($material['material_vedio'])){
                     $notes_info['material_vedio'] = '/'.C('TMPL_PARSE_STRING')['__UPLOAD_PATH__']."video/".$material['material_vedio'];
                }
                break;
        }
        $notes_info['notes_type'] = getBdTypeInfo($notes_info['notes_type'], 'notes_type', 'type_name');
        
        $this->assign('notes_info',$notes_info);
        $this->display('Edu/edu_notes_info');
    }
    
}