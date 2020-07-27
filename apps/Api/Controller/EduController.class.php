<?php
/**
 * 学习平台
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:30
 */

namespace Api\Controller;

use Api\Validate\CommunistNoValidate;
use Api\Validate\NumberValidate;
use Api\Validate\RequireValidate;
use Edu\Model\EduNotesModel;

class EduController extends Api
{
    /*****************专题*************************/
    /**
     *  get_edu_topic_list
     * @desc 获取专题列表
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_edu_topic_list()
    {
    	
    	$page = I('post.page');
    	$pagesize = I('post.pagesize');
        $communist_no = I('post.communist_no');
    	$page = ($page - 1) * $pagesize;
        $topic_list = getTopicList($page,$pagesize);
        if ($topic_list) {
            foreach ($topic_list as &$list) {
                $list['img_url'] = getUploadInfo($list['topic_img']);
                $list['learn_rate'] = getTopicLearnInfo($list['topic_id'],$communist_no)['rate'];
            }
            $this->send('获取成功', $topic_list, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_edu_topic_info
     * @desc 获取专题详情
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_edu_topic_data()
    {
        $db_topic = M('edu_topic');
        $communist_no = I('post.communist_no');
        $topic_id = I('post.topic_id');
        $type = I('post.type');
            switch ($type) {
                case '1':
                    $maps['topic_id'] = $topic_id;
                    $topic_title = $db_topic->where($maps)->getField('topic_title');
                    $add_time = $db_topic->where($maps)->getField('add_time');
                    $times = round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
                    $data = getCommunistInfo($communist_no,'communist_name')."您好！欢迎回到".$topic_title."专题学习，此专题已开展了".$times."天";
                    break;
                case '2':
                    $maps['topic_id'] = $topic_id;
                    $topic_title = $db_topic->where($maps)->getField('topic_title');
                    $integral = getEduTopicIntegral($topic_id,$communist_no);
                    $data = "完成".$topic_title."专题学习";
                    $data1 = "获得".$integral['integral']."积分";
                    break;
                case '3':
                    $where['communist_no'] = $communist_no;
                    $add_time = M('edu_customization')->where($where)->getField('add_time');
                    $times=round(BetweenTwoDays(date('Y-m-d H:i:s'),$add_time),0);
                    $data['data'] = getCommunistInfo($communist_no,'communist_name')."您好！";
                    $data['data1'] = "欢迎进入您的定制学习";
                    $data['data2'] = "您已学习了".$times."天";
                    break;
            }
            $this->send('获取成功', $data, 1,'data1',$data1);
    }
    /**
     *  get_edu_topic_info
     * @desc 获取专题详情
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_edu_topic_info()
    {
        (new NumberValidate(['topic_id']))->goCheck();

        $db_topic = M('edu_topic');

        $communist_no = I('post.communist_no');
        $topic_id = I('post.topic_id');
        $maps['topic_id'] = ['eq', $topic_id];

        $topic_list = $db_topic->where($maps)->find();

        if ($topic_list) {
            $topic_list['learn_data'] = getTopicLearnInfo($topic_id,$communist_no);
            $topic_list['topic_img'] = getUploadInfo($topic_list['topic_img']);
            $this->send('获取成功', $topic_list, 1);

        } else {

            $this->send();
        }
    }
    /*********************end*****************************/
    /********************学习资料***************************/
    /**
     *  get_edu_material_cat_list
     * @desc 资料类型列表
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_edu_material_cat_list()
    {
        (new NumberValidate(['type']))->goCheck();

        $type = I('post.type');

        $db_cat = M('edu_material_category');

        $cat_list = $db_cat->where("cat_type=$type and status=1")->
        field('cat_id,cat_name')->select();

        if ($cat_list) {
            $this->send('获取成功', $cat_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_edu_material_list
     * @desc 资料列表
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_material_list()
    {
        (new NumberValidate(['page', 'pagesize']))->goCheck();
        $cat_id = I('post.cat_id');
        $topic_id = I('post.topic_id');
        $page = I('post.page');
        $pagesize = I('post.pagesize');

        $page = ($page - 1) * $pagesize;

        $material_list = getMaterialList($cat_id, null, null, null, null, $topic_id, $page, $pagesize);
        if ($material_list) {
            foreach ($material_list as &$item) {
                $item['material_thumb'] = getUploadInfo($item['material_thumb']);
                $item['add_staff'] = getStaffInfo($item['add_staff']);
            }
            $this->send('获取成功', $material_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_edu_material_info
     * @desc 资料详情
     * @param int material_id 资料ID
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_material_info()
    {
//     	(new CommunistNoValidate())->goCheck();
//         (new NumberValidate(['material_id']))->goCheck();

        $db_material = M('edu_material');
        $material_id = I('post.material_id');
        $communist_no = I('post.communist_no');
        $material_info = getMaterialInfo($material_id);
        $material_info['add_time'] = getFormatDate($material_info['add_time'],'Y-m-d');
        $communist_integral = getCommunistInfo($communist_no,'communist_integral');

        if(!empty($material_info['material_vedio'])){
            $material_info['material_vedio'] = __ROOT__."".$material_info['material_vedio'];
        }
        $res = M('edu_material_communist')->where("material_id='$material_id' and communist_no='$communist_no'")->select();
        if(empty($res)){
        	$data['communist_no'] = $communist_no;
        	$data['add_staff'] = $communist_no;
        	$data['material_id'] = $material_id;
        	$data['add_time'] = date('Y-m-d H:i:s');
        	$data['update_time'] = date('Y-m-d H:i:s');
        	$data['is_read'] = 1;
            if($communist_no){
                M('edu_material_communist')->add($data);
            }
        }
        if ($material_info) {
            $db_material->where("material_id=$material_id")->setInc('read_num');
            $where['material_id'] = $material_id;
            $material_info['log_list_count'] = M('edu_material_communist')->where($where)->count("DISTINCT communist_no");
            $material_info['log_list'] = M('edu_material_communist')->field('communist_no')->where($where)->limit(3)->order('add_time desc')->group('communist_no')->select();
            foreach ($material_info['log_list'] as &$log) {
                $log['communist_avatar'] = getCommunistInfo($log['communist_no'],'communist_avatar');
                $log['communist_name'] = getCommunistInfo($log['communist_no']);
            }
            $material_info['notes_list'] = M('edu_notes')->field('notes_id,notes_content,add_time,add_staff')->where($where)->order('add_time desc')->select();
            foreach ($material_info['notes_list'] as &$material) {
                $material['notes_content'] = mb_substr(strip_tags($material['notes_content']),0,35,'utf-8');
                $material['add_staff'] = getCommunistInfo($material['add_staff']);
                $material['add_time'] = getFormatDate($material['add_time'],'Y-m-d');
            }
            $this->send('获取成功', $material_info, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  set_integral
     * @desc 资料详情
     * @param int material_id 资料ID
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function set_integral()
    {
        $communist_no = I('post.communist_no');
        $db_period = M('edu_material_period');
        $db_material = M('edu_material');
        $material_id = I('post.material_id');
        if($material_id){
            $where['material_id'] = $material_id;
        }
        $material_type = I('post.material_type');
        // 记录学习记录
        $communist_integral = getCommunistInfo($communist_no,'communist_integral'); // 党员当前积分数

        $material_cat = M('edu_material')->where("material_id = '$material_id'")->getField('material_cat');
        $cat_type = M('edu_material_category')->where("cat_id = '$material_cat'")->getField('cat_type');
        
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
            $data['is_has'] = 1;
        }else{
            $log_table->add($log_data);
            $data['is_has'] = 2;
            if($material_type == 1){ // 文章加积分
                $integral_article = getConfig('integral_article');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_article,'学习文章'); 
            } else if($material_type == 2){
                // 学习视频加积分
                $integral_video = getConfig('integral_video');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_video,'学习视频'); 
            }
        }
        $data['integral_video'] = getConfig('integral_video'); // 视频积分
        $data['integral_article'] = getConfig('integral_article'); // 文章积分
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
        $this->send('操作成功', $data, 1);
    }
    /**
    课件 视频 考试  笔记  百分比
    **/
    public function get_edu_material_percent(){
        $topic_id = I('post.topic_id');
        $communist_no = I('post.communist_no');
        $is_customization = I('post.is_customization');
        if($is_customization==1){
           $percent = getEduCustomization($communist_no);
        }else{
            $percent = getTopicLearnInfo($topic_id,$communist_no);
        }
        $this->send('操作成功', $percent, 1);
    }
    /**
     *   
     * @desc 获取文章资料视频资料
     * @param int type 资料类型
     * @param int topic_id 专题ID
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_material()
    {
        (new NumberValidate(['page', 'pagesize', 'type']))->goCheck();
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;
        $type = I('post.type');

        $is_customization = I('post.is_customization');
        $communist_no = I('post.communist_no');


        $topic_id = I('post.topic_id');
        $db_cat = M('edu_material_category');
        $cat_list = $db_cat->where("status=1 and cat_type=$type")->getField('cat_id', true);
        if (!empty($cat_list)) {
            $cat_list = implode(',', $cat_list);
            if($is_customization==1){
                $group_data = M('edu_customization')->where('communist_no="'.$communist_no.'"')->find();
                $material_list = getMaterialList($cat_list, null, null, null, null, '', $page, $pagesize,'','',$group_data['material_group'],$group_data['material_data']);
            }else{
                $material_list = getMaterialList($cat_list, null, null, null, null, $topic_id, $page, $pagesize);
            }
            if ($material_list) {
                if($type == 21){ // 视频
                    $log_type = 2;
                } else {
                    $log_type = 1;
                }
                foreach ($material_list as &$list) {
                    // 判断该学习资料是否已经被学习
                    $log_map['communist_no'] = $communist_no;
                    $log_map['material_no'] = $list['material_id'];
                    $log_map['log_type'] = $log_type;
                    $learn_num = M('edu_material_log')->where($log_map)->count();
                    if($learn_num > 0 ){
                        $list['is_learn'] = 1;
                    } else {
                        $list['is_learn'] = 0;
                    }
                    $list['material_thumb'] = getUploadInfo($list['material_thumb']);
                    $list['add_staff'] = getStaffInfo($list['add_staff']);
                }
                $this->send('获取成功', $material_list, 1);
            } else {
                $this->send();
            }
        } else {
            $this->send();
        }
    }
    
    /**
     *  get_edu_material_communist
     * @desc 获取查看学习资料的人
     * @param int material_id 关联对应的ID 资料或会议
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_material_communist()
    {
    	(new NumberValidate(['material_id']))->goCheck();
    	$material_id = I('post.material_id'); // I方法获取数据
    	$communist_list = M('edu_material_communist')->field('log_id,material_id,communist_no')->where("material_id = '$material_id'")->group('communist_no')->select();
        $count = M('edu_material_communist')->where("material_id = '$material_id'")->count('material_id');
    	foreach ($communist_list as &$list) {
    		$list['communist_avatar'] = getCommunistInfo($list['communist_no'],'communist_avatar');
    		$list['communist_name'] = getCommunistInfo($list['communist_no']);
    		$list['add_time'] = getFormatDate($list['add_time'],'Y-m-d');
    	}
    	if($communist_list) {
    		 $this->send('获取成功', $communist_list, 1,'count',$count);
    	} else {
    		$this->send();
    	}
    }
    
    /****************************end**********************************/

    /*************************学习笔记**********************************/
    /**
     *  get_edu_notes_list
     * @desc 获取学习笔记列表
     * @param int communist_no 党员编号
     * @param int topic_id 专题ID
     * @param string keyword 关键词搜索
     * @param int page
     * @param int pagesize
     * @user liubingtao-liuchangjun
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_notes_list()
    {
        //(new CommunistNoValidate())->goCheck();

        (new NumberValidate(['page', 'pagesize']))->goCheck();
        $is_notes  = I('post.is_notes');  //1视频   2课件

        $communist_no = I('post.communist_no');
        $is_customization = I('post.is_customization');
        $keyword = I('post.keyword');//笔记类型
        $topic_id = I('post.topic_id');//笔记类型
        $note_time = I('post.note_time');//笔记时间
        $material_id = I('post.material_id');//学习文章Id

        $type = I('post.type');

        $notes_type = I('post.notes_type');
        $db_edu_material = M("edu_material");
        $pagesize = I('post.pagesize'); 

        $page = (I('post.page') - 1) * $pagesize;

        
        
        if($is_customization==2){
            $material_id_arr = $db_edu_material->where("material_topic=$topic_id")->field('material_id')->select();
            $id_arr = [];
            foreach($material_id_arr as $list){
                $id_arr[] = $list['material_id'];
            }
            $id_arr = implode(',',$id_arr);
            $maps['add_staff'] = $communist_no;
            $maps['material_id'] = array('in',$id_arr);
            $notes_list = M('edu_notes')->where($maps)->limit($page,$pagesize)->order('add_time desc')->select();
        }else if($is_customization==1){
            $material_group_data = M('edu_customization')->where("communist_no=$communist_no")->field('material_data,material_group')->find();
            $material_d_g['_string'] = "find_in_set('".$material_group_data['material_data']."',material_data) and find_in_set('".$material_group_data['material_group']."',material_group)";
            $material_d_g_list = $db_edu_material->where($material_d_g)->getField('material_id',true);
            $material_d_g_list_str = implode(',',$material_d_g_list);
            $list_str['material_id'] = array('in',$material_d_g_list_str);
            $list_str['add_staff'] = $communist_no;
            $notes_list = M('edu_notes')->where($list_str)->select();
        }else{
            $notes_list = (new EduNotesModel())->getNotesList($communist_no, $page, $pagesize, $keyword, $topic_id,$notes_type,$material_id,$note_time);
        }
        if ($notes_list) {
            foreach ($notes_list as &$list) {
                $ttt['material_id'] = $list['material_id'];
                $list['material_vedio'] = $db_edu_material->where($ttt)->getField("material_vedio");
                $list['update_time'] = getFormatDate($list['update_time'], "Y-m-d H:i");
                $list['communist_avatar'] =  getCommunistInfo($list['add_staff'],'communist_avatar');
                $list['add_staff'] = getCommunistInfo($list['add_staff']);
                $list['add_time'] = getFormatDate($list['add_time'],'H:i');
                if(!empty($list['communist_avatar'])){
                	$list['communist_avatar'] = '/'.$list['communist_avatar'];
                }else{
                	$list['communist_avatar']="";
                }
                $list['material_thumb'] = getUploadInfo($list['material_thumb']);
                if(!empty($type)){
                    if ($notes_type == '1' && !empty($list['material_id'])) {
                        $material_cat = $db_edu_material->where("material_id = '".$list['material_id']."'")->getField('material_cat');
                        $cat_type = M("edu_material_category")->where("cat_id = $material_cat")->getField('cat_type');
                        switch ($cat_type) {
                            case '11':
                                $list['notes_type_name'] = '课件笔记';
                                break;
                            case '21':
                                $list['notes_type_name'] = '视频笔记';
                                break;
                        }
                    }else{
                        $list['notes_type_name'] = getBdTypeInfo($list['notes_type'], "notes_type");
                    }
                }else{
                    if ($list['notes_type']) {
                        $list['notes_type_name'] = getBdTypeInfo($list['notes_type'], "notes_type");
                    }
                }
                
                if($list['notes_type']==3){
                    $list['richang'] = 1;
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

                // if($list['material_id']){
                //     $material_cat = $db_edu_material->where("material_id = '".$list['material_id']."'")->getField('material_cat');
                //     $cat_type = M("edu_material_category")->where("cat_id = $material_cat")->getField('cat_type');
                // }
                // if($cat_type==21){
                //     // $material_list = $db_edu_material->where("material_id = $material_id")->find();
                //     $list['material'] = $db_edu_material->where('material_id="'.$list['material_id'].'"')->find();
                //      $list['material_vedio'] = __ROOT__."/uploads/video/".$list['material_vedio'];
                //     $list['type_video'] = 'yes';
                // }else{
                //     $list['material'] = $db_edu_material->where('material_id="'.$list['material_id'].'"')->find();
                //     $list['material_thumb'] = getUploadInfo($list['material_thumb']);
                //     $list['type_video'] = 'no';
                // }  
            }
            $db_material = M('edu_material');
            $db_material_category = M('edu_material_category');
            if($is_notes==1){
                $aa = [];
                foreach ($notes_list as &$list) {
                    if($list['material_id']){
                        $material_cat = $db_material->where("material_id = '".$list['material_id']."'")->getField("material_cat");
                        $cat_type = $db_material_category->where('cat_id="'.$material_cat.'"')->getField('cat_type');
                        switch ($cat_type) {
                            case '21':
                                $aa[] = $list;
                                break;
                            
                           
                        }
                    }
                }
                $this->send('获取成功', $aa, 1);
            }else if($is_notes==2){
                $aa = [];
                foreach ($notes_list as &$list) {
                    if($list['material_id']){
                        $material_cat = $db_material->where("material_id = '".$list['material_id']."'")->getField("material_cat");
                        $cat_type = $db_material_category->where('cat_id="'.$material_cat.'"')->getField('cat_type');

                        switch ($cat_type) {
                            case '11':
                                $aa[] = $list;
                                break;
                            
                        }
                    }
                }
                $this->send('获取成功', $aa, 1);
            }else{
                $this->send('获取成功', $notes_list, 1);
            }
        } else {
            $this->send();
        }
    }

    /**
     *  get_edu_notes_info
     * @desc 获取学习笔记详情
     * @param int notes_id 笔记ID
     * @param int communist_no 会员编号
     * @param int type 笔记类型
     * @param int material_id 关联对应的ID 资料或会议
     * @user liubingtao 
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_notes_info()
    {
//        (new NumberValidate(['notes_id']))->goCheck();
        $notes_id = I('post.notes_id'); // I方法获取数据
        $communist_no = I('post.communist_no'); // I方法获取数据
        $type = I('post.type'); // I方法获取数据
        // $material_id = I('post.material_id'); // I方法获取数据
		$material_id = M('edu_notes')->where("notes_id = '$notes_id'")->getField('material_id');
        $notes_info = (new EduNotesModel())->getNotesInfo($notes_id, $communist_no, $type, $material_id);
        if($material_id){
            switch ($notes_info['notes_type']) {
                case '1':
                     $db_edu_material = M("edu_material");
                    $material_cat = $db_edu_material->where("material_id = $material_id")->getField('material_cat');
                    $cat_type = M("edu_material_category")->where("cat_id = $material_cat")->getField('cat_type');
                    if($cat_type==21){
                        $material_list = $db_edu_material->where("material_id = $material_id")->find();
                        $material_list['material_thumb'] = getUploadInfo($material_list['material_thumb']);
                        $material_list['material_vedio'] = __ROOT__."/uploads/video/".$material_list['material_vedio'];
                        $material_list['type_video'] = 'yes';
                    }else{
                        $material_list = $db_edu_material->where("material_id = $material_id")->find();
                        $material_list['material_thumb'] = getUploadInfo($material_list['material_thumb']);
                        $material_list['type_video'] = 'no';
                    } 
                    break;
            }
        }
        if ($notes_info) {
            $communist_data = getCommunistInfo($notes_info['add_staff'], "communist_name,communist_avatar");
            $notes_info['communist_avatar'] =  $communist_data['communist_avatar'];
            $notes_info['notes_thumb'] = getUploadInfo($notes_info['notes_thumb']);
            $notes_info['add_communist'] = $communist_data['communist_name'];
            $notes_info['notes_type'] = getBdTypeInfo($notes_info['notes_type'], 'notes_type', 'type_name');
            $notes_info['add_time'] = getFormatDate($notes_info['add_time'],'Y-m-d');
            $notes_info['material'] =  $material_list;
            $this->send('获取成功', $notes_info, 1);
        } else {
            $this->send();
        }
    }
    
    /**
     *  set_edu_notes
     * @desc 提交学习笔记
     * @param int communist_no 党员编号
     * @param int notes_type 笔记类型
     * @param string notes_title 笔记标题
     * @param string notes_content 笔记内容
     * @param int notes_thumb 附件ID
     * @param int material_id 关联对应的ID 资料或会议
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function set_edu_notes()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['notes_type']))->goCheck();
        (new RequireValidate(['notes_title', 'notes_content']))->goCheck();

        $notes = M('edu_notes');

        $data = I('post.');
        $communist_no = I('post.communist_no');

        $data['update_time'] = date("Y-m-d H:i:s");
        $data['add_staff'] = $communist_no;
        $data['add_time'] = date("Y-m-d H:i:s");
        $material_id = $data['material_id'];
        $material_title = M('edu_material')->where("material_id = '$material_id'")->getField('material_title');
        $material_cat = M('edu_material')->where("material_id = '$material_id'")->getField('material_cat');
        $cat_type = M('edu_material_category')->where("cat_id = '$material_cat'")->getField('cat_type');


        $result = $notes->add($data);
        if ($result) {
            $material_id = $data['material_id'];
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
                if(!empty($log_id)){ // 已经看了该学习资料
                    $log_table->where("log_id={$log_id}")->save($log_data);
                }else{ // 没有看该学习资料
                    $log_table->add($log_data);
                }
                $integral_notes_communist = getConfig('integral_notes_communist');
                $communist_integral = getCommunistInfo($communist_no,'communist_integral');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_notes_communist,'完成学习笔记'); // 给签到党员加积分
            } else {
                $integral_notes_communist = getConfig('integral_notes_communist');
                $communist_integral = getCommunistInfo($communist_no,'communist_integral');
                updateIntegral(1,7,$communist_no,$communist_integral,$integral_notes_communist,'完成学习笔记'); // 给签到党员加积分
            }
            
            $this->send('操作成功', '', 1);
        } else {
            $this->send();
        }
    }
    /*****************************end************************************/

    /*****************************考试中心********************************/

    /**
     *  get_edu_exam_list
     * @desc 获取我的考试列表
     * @param int communist_no 党员编号
     * @param int topic_id 专题ID
     * @param int is_exam 已考未考
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_exam_list()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['page', 'pagesize', 'is_exam']))->goCheck();
        $db_questions = M('edu_questions');
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;
        $communist_no = I('post.communist_no'); // 党员编号
        $is_customization = I('post.is_customization'); 
        $topic_id = I('post.topic_id'); // 专题id
        $is_exam = I('post.is_exam'); // 1 正式考试未考  2  所有已经考过的模拟和正式考试 3未考的模拟考试
        $exam_group = I('post.exam_group'); // 对应群体
        $exam_data_r = I('post.exam_data'); // 资料标签

        if($is_customization==1){
            $group_data = M('edu_customization')->where('communist_no="'.$communist_no.'"')->find();
            $exam_list = getExamList('',$communist_no,$is_exam,'',$page,$pagesize,$group_data['material_group'],$group_data['material_data']);
        }else{
            $exam_list = getExamList($topic_id,$communist_no,$is_exam,'',$page,$pagesize,$exam_group,$exam_data_r);
        }
        $exam_data = $exam_list['data'];
        if ($exam_data) {
            foreach ($exam_data as &$list){
                switch ($list['is_simulation']) {
                    case 0:
                        $list['is_simulation_name'] = '正式考试';
                        break;
                    case 1:
                        $list['is_simulation_name'] = '模拟考试';
                        break;
                }
                // $list['exam_thumb'] = getUploadInfo($list['exam_thumb']);
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

            $this->send('获取成功', $exam_data, 1);
        } else {
            $this->send();
        }

    }

    /**
     *  get_edu_exam_info
     * @desc 获取我的考试详情
     * @param int communist_no 党员编号
     * @param int exam_id 考试ID
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_exam_info()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['exam_id']))->goCheck();

        $communist_no = I('post.communist_no');
        $exam_id = I('post.exam_id');
        //查询试卷详情
        $exam_list = getExamCommunistInfo($exam_id, $communist_no);
        if ($exam_list['exam']) {
            $this->send('获取成功', $exam_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_edu_exam_questions
     * @desc 写入答案
     * @param int exam_id 试卷ID
     * @param int communist_no 党员编号
     * @param string questions_arr 答案 json
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function set_edu_exam_questions()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['exam_id']))->goCheck();
         
        $end_time = I("post.time_lave");

        $db_edu_exam_log = M('edu_exam_log');
        $db_edu_exam_answer = M('edu_exam_answer');

        $exam_id = I("post.exam_id");
        $communist_no = I("post.communist_no");
        $question_arr = $_POST['questions_arr'];
        $exam['exam_id'] = $exam_id;
        $start_time = M('edu_exam')->where($exam)->getField('exam_time');
        //查询当前考试人员名称
        $communist_name = getCommunistInfo($communist_no, "communist_name");

        //json格式装数组，并去掉接受值两边的双引
        $q_arr = json_decode($question_arr, true);

        M()->startTrans();
        $question['exam_id'] = $exam_id;
        $question['communist_no'] = $communist_no;
        $question['add_time'] = date("Y-m-d H:i:s");
        $point_amount = 0; //所得分数
        $total_points = 0; //总分
        $accuracy = 0; 
        //["":"","":""]
        foreach ($q_arr as $k => $v) {
            if(!empty($k) && is_numeric($k)){
                $question['questions_id'] = $k;
                $question['answer_item'] = $v;
            }
            $question_data[] = $question;
            $question_result = getQuestionsInfo($k); //获得当前考题详情
            $total_points += $question_result['questions_score'];
            //当答案为空时 跳出本次循环
            if($v==$question_result['questions_answer']){
                $accuracy++;
                $point_amount += $question_result['questions_score'];
            }
        }
        $count = count($question_data);
        $result = $db_edu_exam_answer->addAll($question_data);

        $exam_integral = getExamInfo($exam_id);

        $point = $point_amount/$total_points;//把所得分数转换成百分比

        $integral_amount = $exam_integral['exam_integral'];
        $data['integral'] = '';
        if((float)$point >= 0.6){
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_amount,'通过考试'); //通过考试给党员加积分
            $data['integral'] = $integral_amount;//积分
            $question['memo'] = '1';
        }
        //添加考试记录
        $question['log_score'] = $point_amount;
        $question['log_date'] = date("Y-m-d H:i:s");
        $question['log_integral'] = $integral_amount;
        $flag = $db_edu_exam_log->add($question);

        if ($result && $flag) {
            M()->commit();
            $time = $start_time*60;
            $end_time = explode(':',$end_time);
            $end_time = $end_time['0'] * 60 + $end_time['1'];
            $time_lave = $time - $end_time;
           
            $data['point_amount'] = $point_amount;//得分
            $data['count'] = $count;//题数
            $data['accuracy'] = round(($accuracy/$count)*100,2);//正确率
            $data['time_lave'] = gmstrftime('%M:%S',$time_lave);
            $this->send('操作成功', $data, 1);
        } else {
            M()->rollback();
            $this->send('操作失败');
        }

    }
     /**
     *  get_edu_topic_integral
     * @desc 写入答案
     * @param int topic_id 专题ID
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_topic_integral()
    {
        (new NumberValidate(['topic_id']))->goCheck();
        (new CommunistNoValidate())->goCheck();
        $topic_id = I('post.topic_id');
        $communist_no = I('post.communist_no');
        $type = I('post.type');
        $data = getEduTopicIntegral($topic_id,$communist_no);
        if($data){
           $this->send('操作成功', $data, 1);
        } else {
            $this->send('操作失败');
        }

    }
    /**
     *  get_edu_topic_integral
     * @desc 写入答案
     * @param int topic_id 专题ID
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_edu_topic_communist_integral()
    {
        $map['_string'] = "memo='学习文章' OR memo='学习视频' OR memo='完成学习笔记' OR memo='通过考试'";
        $communist_integra = M('ccp_integral_log')->field('log_relation_no as communist_no,sum(change_integral) as integral')->order('integral desc')->limit(0,10)->where($map)->group('log_relation_no')->select();
        foreach ($communist_integra as &$communist) {
            $communist['communist_name'] = getCommunistInfo($communist['communist_no']);
            $communist['communist_avatar'] = getCommunistInfo($communist['communist_no'],'communist_avatar');
        }
        if($communist_integra){
           $this->send('操作成功', $communist_integra, 1);
        } else {
            $this->send('操作失败');
        }
    }
    /**
     *  edu_group_list_type
     * @desc 定制学习标签
     * @param int topic_id 专题ID
     * @user changjun
     * @date 2019/7/5
     * @version 1.0.0
     */
    public function edu_group_list_type(){

        $db_groupdata = M('edu_groupdata');

        $list = getBdCodeList('party_level_code','');
        //对应群体
        $data_groupdata = $db_groupdata->where("group_type=1 and status=1")->select();
        //资料标签
        $data_groupdata1 = $db_groupdata->where("group_type=2 and status=1")->select();
        $data['zuzhi'] = $list;
        $data['qunti'] = $data_groupdata;
        $data['biaoqian'] = $data_groupdata1;
        $this->send('操作成功', $data, 1);
    }

    /**
     *  edu_customization_group_data
     * @desc 定制学习
     * @param int
     * @user 刘长军
     * @date 2019/7/5
     * @version 1.0.0
     */
    public function edu_customization_group_data(){
        $material_group = I('post.material_group');
        $material_data = I('post.material_data');
        $communist_no = I('post.communist_no');
        $type = I('post.type');
        $db_customization = M('edu_customization');
        if($type){
            $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');
            $db_customization->where('communist_no="'.$communist_no.'"')->delete();
            M('edu_customization_log')->where('customization_id="'.$customization_id.'"')->delete();
            $this->send('操作成功', '', 1);
        }else{
            $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');
            if($customization_id){
                $arr = getEduCustomization($communist_no);
                $this->send('获取成功', $arr, 1);
                
            }else{
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
                $this->send('保存成功','1',1);
            }
        }
        
        
    }

    public function customization_off_no(){
        $communist_no = I('post.communist_no');
        $db_customization = M('edu_customization');
        $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');
        if($customization_id){
            $this->send('已经进行定制学习','1',1);
        }else{
            $this->send('未已经进行定制学习','2',1);

        }
    }
}
