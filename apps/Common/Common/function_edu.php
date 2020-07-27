<?php
use phpDocumentor\Reflection\Type;

/********************************学习相关基础层方法 开始*************************************/

/**
 * @name  getMaterialList()
 * @desc  获取当前栏目及下级栏目所有文章列表
 * @param $cat_id 栏目ID
 * @param $communist_no 党员编号
 * @param $keyword 搜索关键词
 * @param $start_time 搜索开始时间
 * @param $end_time 搜索结束时间
 * @param $topic_id 专题
 * @param $page 第几条开
 * @param $count 条数
 * @return 文章列表
 * @author 杨凯 刘丙涛
 * @version 版本 V1.1.0
 * @updatetime   2017/12/6重构 适应接口
 * @addtime   2016-07-15
 */
function getMaterialList($cat_id=null,$communist_no=null,$keyword=null,$start_time=null,$end_time=null,$topic_id=null,$page=0,$count=10,$num=null,$type=null,$material_group=null,$material_data=null){
	$edu_material = M('edu_material');
	if(!empty($keyword)){
		$keyword_map['material_title'] = array('like','%'.$keyword.'%');
		$keyword_map['material_keyword'] = array('like','%'.$keyword.'%');
		$keyword_map['_logic'] = 'or';
	}
	if(!empty($start_time) && !empty($end_time)){
		$material_map['add_time'] =  array('between',array($start_time,$end_time));
	}
    if (!empty($topic_id)){
    	$material_map['material_topic'] = $topic_id;
    }
    if(!empty($material_group) && !empty($material_data)){
    	$material_map['_string'] = "find_in_set('".$material_group."',material_group) and find_in_set('".$material_data."',material_data)";
    }
    if (!empty($cat_id)) {
    	$material_map['material_cat'] = array('in',$cat_id);
    }
    if(!empty($keyword_map)){
    	$material_map['_complex'] = $keyword_map;
    }
	if (empty($count)){
        $article_list = $edu_material->where($material_map)->order("add_time desc")->select();
    }else{
        $article_list = $edu_material->where($material_map)->order("add_time desc")->limit($page,$count)->select();
    }
    if (!empty($num)) {
    	 $article_list = $edu_material->where($material_map)->count();
    }
    if(!empty($type)){
        $arr['data'] = $article_list;
        $arr['count'] = $edu_material->where($material_map)->count();;
        return $arr;
    }
	return $article_list;
}

/**
 * @name  getMaterialInfo()
 * @desc  获取文章详情
 * @param
 * 		@$article_id(文章ID)
 * 		@$field(all:获取所有字段值)
 * @return 文章详情
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-26
 */
function getMaterialInfo($article_id,$field='all'){
	$edu_material = M('edu_material');
	$material_map['material_id'] = $article_id;
	if($field == 'all'){
		$article_data = $edu_material->where($material_map)->find();
		if(!empty($article_data['material_vedio'])){
			$article_data['material_vedio'] = explode ( ',', $article_data['material_vedio'] );
			$article_data['material_vedio'] = '/'.C('TMPL_PARSE_STRING')['__UPLOAD_PATH__']."video/".$article_data['material_vedio'][0];
		}else{
			$article_data['material_vedio'] = "";
		}
		$article_data['material_attach'] = getUploadInfo($article_data['material_attach']);
		$article_data['material_thumb'] = getUploadInfo($article_data['material_thumb']);
		$article_data['material_cat_id'] = $article_data['material_cat'];
		$article_data['material_cat'] = getMaterialCatInfo($article_data['material_cat']);
		$article_data['material_topic_id'] = $article_data['material_topic'];
		$article_data['material_topic'] = getTopicInfo($article_data['material_topic']);
		$article_data[add_staff] = getStaffInfo($article_data[add_staff]);
		return $article_data;
	}else{
		$article_data = $edu_material->where($material_map)->field($field)->find();
		return $article_data[$field];
	}
}

/**
 * @name  delMaterial()
 * @desc  删除文章
 * @param $article_id(文章ID)
 * @return true/false
 * @author 王彬
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2016-07-15
 */
function delMaterial($article_id){
	$edu_material = M('edu_material');
	$material_map['material_id'] = $article_id;
	$article_data = $edu_material->where($material_map)->delete();
	if($article_data){
		return true;
	}else{
		return false;
	}
}

/**
 * @name  getMaterialCatInfo()
 * @desc  获取文章类型名称
 * @param 类型id   $cat_id
 * @param 字段值   $field（all:取全部字段）
 * @return 文章字段值/info
 * @author 靳邦龙-王彬
 * @add_time   2016-04-27
 * @update_time   2016-08-09
 */
function getMaterialCatInfo($cat_id,$field='cat_name' ){
	if(!empty($cat_id )&&is_numeric($cat_id )){
		$cat_map['cat_id'] = $cat_id;
		$db_type=M('edu_material_category');
		if($field == 'all'){
			$cat_data=$db_type->where($cat_map)->find();
			if($cat_data){
				return $cat_data;
			}else{
				return "无此类型";
			}
		}else{
			$cat_name=$db_type->where($cat_map)->field($field)->find();
			if($cat_name){
				return $cat_name[$field];
			}else{
				return "无此类型";
			}
		}
	}else{
		return "无此类型";
	}
}

/**
 * @name  getMaterialCatList()
 * @desc  获取文章类型列表
 * @param $cat_ids  类型ids
 *        $cat_pid  文章栏目id
 *        $status 查询文章标题状态
 * @return 获取文章类型列表json
 * @author 杨凯
 * @add_time   2017-10-11
 * @update_time   
 */
function getMaterialCatList($cat_ids,$cat_pid,$communist_no,$status='NULL',$party_no){
	$article_category = M("edu_material_category");
	if(!empty($cat_ids)){
		$cat_map['cat_id'] = array('in',$cat_ids);
		$cat_list = $article_category->where($cat_map)->order('cat_id desc')->select();
		if($cat_list){
			return $cat_list;
		}else{
			return false;
		}
	}else if(!empty($cat_pid) || $cat_pid == 0){//获取文章所有栏目列表
		$ccp_communist = M('ccp_communist');
		if(empty($communist_no)){
			$staff_no = session('staff_no');
		}
		if($status != 'NULL'){
			$cat_map['status'] = $status;
		}
		//$party_no = getCommunistInfo($communist_no,'party_no');
		if(!empty($party_no)){
			$cat_map['cat_pid'] = $cat_pid;
			$cat_map['_string'] = "FIND_IN_SET('$party_no',share_party)";
			$cat_list = $article_category->where($cat_map)->select();
		}else{
			$cat_map['cat_pid'] = $cat_pid;
			$cat_list = $article_category->where($cat_map)->select();
		}
		$category_list = array();
		foreach($cat_list as $list){
			$category_list[] = $list;
			$cat_list_to = getMaterialCatList('',$list['cat_id'],$communist_no,$status);
			foreach($cat_list_to as $list_to){
				$category_list[] = $list_to;
			}
		}
		return $category_list;
	}
}

/**
 * @name  getMaterialCatSelect()
 * @desc  获取文章类型下拉列表
 * @param 当前部门的编号   $selected_id
 *         $cat_pid  类型父级id
 *         $type [1:返回全部，2：按pid取值]
 *         $selected_id 当前选中的类型id
 * @return 带选中状态的文章类型下拉列表（HTML代码）
 * @author 靳邦龙
 * @time   2016-04-28
 */
function getMaterialCatSelect($cat_pid,$cat_ids,$selected_id){
	$db_type=M('edu_material_category');
	if($cat_pid!=''){
		$type_map['material_cat_pid'] = $cat_pid;
	}
	$cat_list=$db_type->where($type_map)->field('material_cat_id,material_cat_name')->select();
	$cat_options="";
	foreach($cat_list as &$type){
		$selected="";
		if($selected_id==$type['material_cat_id']){
			$selected="selected=true";
		}
		$cat_options.="<option $selected value='".$type['material_cat_id']."'>".$type['material_cat_name']."</option>";
	}
	if(!empty($cat_options)){
		return $cat_options;
	}else{
		return "<option value=''>无数据</option>";
	}
}

/**
 * @name  getMaterialChildNos()
 * @desc  获取学习资料下级编号
 * @param
 * 		@$material_cat_id(资料栏目ID)
 * 		@$noself(是否包含自己)
 * @return true/false
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-25
 */
function getMaterialChildNos($material_cat_id,$noself){
	if (empty($noself)){
		$catstr .= $material_cat_id.",";
	}
	$edu_material_category = M('edu_material_category');
	$cat_map['cat_pid'] = $material_cat_id;
	$cat_id = $edu_material_category->where($cat_map)->select();
	
	if($cat_id){
		foreach($cat_id as $cat){
			if (empty($cat)){
				$catstr = $cat['cat_id'].",";
			}else{
				$catstr .= ",".$cat['cat_id'].",";
			}
			$catstr .= ",".getMaterialChildNos($cat['cat_id'],1);
			$catstr = str_replace(",,",",",$catstr);
		}
	}
	$catstr = trim($catstr,',');
	return $catstr;
}

/**
 * @name  delMaterialCat()
 * @desc  删除当前栏目
 * @param $cat_id(栏目ID)
 * @return true/false
 * @author 王彬
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2016-07-15
 */
function delMaterialCat($cat_id){
	$edu_material_category = M('edu_material_category');
	$cat_map['material_cat_id'] = $cat_id;
	$article_category_data = $edu_material_category->where($cat_map)->delete();
	if($article_category_data){
		return true;
	}else{
		return false;
	}
}

/**
 * @name  saveMaterialLog()
 * @desc  写入学习日志
 * @param
 * 		@$material_id(资料ID)
 * 		@$communist_no(志愿者编号)
 * @return true/false
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-25
 */
function saveMaterialLog($material_id,$communist_no){
	$edu_material_communist = M('edu_material_communist');
	if(!empty($material_id) && !empty($communist_no)){
		$material_log_data = getMaterialLogInfo($material_id, $communist_no);
		if($material_log_data == "0"){
			$material_log['material_id'] = $material_id;
			$material_log['communist_no'] = $communist_no;
			$material_log['add_time'] = date("Y-m-d H:i:s");
			$material_log[add_staff] = session('staff_no');
			$material_log['is_read'] = 1;
			$material_data = $edu_material_communist->add($material_log);
		}
	}
	if(!empty($material_data)){
		return true;
	}else{
		return false;
	}
}

/**
 * @name  getMaterialLogInfo()
 * @desc  获取学习记录信息
 * @param
 * 		@$material_id(资料ID)
 * 		@$communist_no(志愿者编号)
 * @return true/false
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-25
 */
function getMaterialLogInfo($material_id,$communist_no){
	$edu_material_communist = M('edu_material_communist');
	if(!empty($material_id) && !empty($communist_no)){
		$material_map['material_id'] = $material_id;
		$material_map['communist_no'] = $communist_no;
		$material_map['is_read'] = 1;
		$material_log_data = $edu_material_communist->where($material_map)->find();
	}
	if(!empty($material_log_data)){
		return $material_log_data;
	}else{
		return "0";
	}
}

/**
 * @name  getMaterialAttachList()
 * @desc  获取学习资料附件
 * @param $field_where(需要查询的字段要求)
 * @param $field_name(需要查询的字段名称)(all:获取所有字段值)
 * @return 附件信息
 * @author 杨凯
 * @version 版本 V1.0.0
 * @addtime   2017-09-04
 */
function getMaterialAttachList($field_where='',$field_name='all'){

    $edu_material = M('edu_material');
    if($field_name == 'all'){
        $where='1=1';
    }else {
        $where[$field_name] = $field_where;
    }
    $article_data = $edu_material->where($where)->select();
    foreach ($article_data as $k=>$v){
        $article_data[$k]['material_attach'] = getUploadInfo($v['material_attach']);
        $article_data[$k]['add_time'] = date('Y-m-d',strtotime($v['add_time']));
    }
    return $article_data;

}

/**
 * @name  getMaterialVideoList()
 * @desc  获取视频与文章资料
 * @param $communist_no (党员编号)
 * @param $article_num (文章数量)
 * @return 附件信息
 * @author 刘丙涛
 * @version 版本 V1.0.0
 * @addtime   2017-09-04
 */
function getMaterialVideoList($communist_no,$topic_id,$article_num='4',$video_num='3'){
    $db_cat = M('edu_material_category');
    $article_cat = $db_cat->where('status=1 and cat_type=11')->getField('cat_id',true);
    $video_cat = $db_cat->where('status=1 and cat_type=21')->getField('cat_id',true);
    if (!empty($article_cat)){
        $article_cat = implode(',',$article_cat);

        $article_list = getMaterialList($article_cat,$communist_no,'','','',$topic_id,'0',$article_num);
        foreach ($article_list as &$list){
            $list['material_thumb'] = getUploadInfo($list['material_thumb']);
            $list[add_staff] = getCommunistInfo($list[add_staff]);
        }
        $data['article_list'] = $article_list;
    }
    if (!empty($video_cat)){
        $video_cat = implode(',',$video_cat);
        $video_list = getMaterialList($video_cat,$communist_no,'','','',$topic_id,'0',$video_num);
        foreach ($video_list as &$item){
            $item['material_thumb'] = getUploadInfo($item['material_thumb']);
            $item[add_staff] = getCommunistInfo($item[add_staff]);
        }
        $data['video_list'] = $video_list;
    }
    if (!empty($article_cat) && !empty($video_cat)){
        $data['cat_str'] = $article_cat.','.$video_cat;
        return $data;
    }else{
        return false;
    }
}

/**
 * @name  getMaterialNotesList()
 * @desc  获取笔记列表
 * @param
 * 		@$material_id(资料ID)
 * @return 文章详情
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-26
 */
function getMaterialNotesList($material_id,$field = 'all'){
	$edu_material_notes = M('edu_notes');
	$material_map['material_id'] = $material_id;
	$material_map['status'] = array('neq',0);
	if($field == 'all'){
		$material_data = $edu_material_notes->where($material_map)->select();
	}else{
		$material_data = $edu_material_notes->where($material_map)->field($field)->select();
	}
	return $material_data;
}
/**
 * @name  getCommunistNotesInfo()
 * @desc  获取笔记详情
 * @param
 * 		@$material_id(资料ID)
 * 		@$communist_no (党员编号)
 * @return 文章详情
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-26
 */
function getCommunistNotesInfo($material_id,$communist_no){
	$edu_material_notes = M('edu_material_notes');
	$material_map['material_id'] = $material_id;
	$material_map['material_notes_communist'] = $communist_no;
	$material_map['status'] = array('neq',0);
	$material_data = $edu_material_notes->where($material_map)->find();
	if($material_data){
		return $material_data;
	}else{
		return null;
	}
}
/**
 * @name  saveCommunistNotes()
 * @desc  添加笔记
 * @param
 * 		@$material_id(资料ID)
 * 		@$communist_no (党员编号)
 * @author 王桥元
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2017-05-26
 */
function saveCommunistNotes($notes_data){
	$edu_material_notes = M('edu_notes');
	$notes_data['update_time'] = date("Y-m-d H:i:s");
	$notes_data['add_time'] = date("Y-m-d H:i:s");
	$notes_data['add_staff'] = session('staff_no');
	$material_data = $edu_material_notes->add($notes_data);
	if($material_data){
		return true;
	}else{
		return false;
	}
}

/***************************** 备忘录开始 ************************/
 /**
 * @name:getNotesInfo            
 * @desc：获取备忘录名称
 * @param：备忘录id $notes_id
 * $field  字段名 传入此参数时、查询此字段的值
 * @return：备忘录名称/整条数据
 * @author：王世超
 * @addtime:2016-08-11
 * @version：V1.0.0
**/
function getNotesInfo($notes_id,$field='notes_title'){
    if(!empty($notes_id)){
        $oa_notes = M('edu_notes');
        $notes_map['notes_id'] = $notes_id;
        $notes_fieldinfo=$oa_notes->where($notes_map)->find();
        if($field!='all'){
            $notes_fieldinfo=$notes_fieldinfo[$field];
        }
    }
    if($notes_fieldinfo){
        return $notes_fieldinfo;
    }else{
        return null;
    }
}
 /**
 * @name:getNotesList               
 * @desc：获取笔记列表
 * @param：$communist_no 员工编号
 * 		  $notes_classify 备忘录分类      （新增）
 * 		  $notes_content 搜索内容
 * 		  $start 开始时间
 * 		  $end 结束时间
 * 		  $is_timedesc 接口按时间分类 
 * 		  $notes_classify 笔记类型
 *  	  $material 按文章查询
 *   	  $topic_id 学习专题id
 *        $page 分页
 *        $pagesize 每页显示数量
 * @return：array
 * @author：王世超-李祥超
 * @addtime:2016-08-23
 * @updatatime:2016-09-21
 * @version：V1.0.0
**/
function getNotesList($communist_no,$notes_content,$start,$end,$is_timedesc,$notes_classify,$material,$topic_id,$page,$pagesize,$type){
   $db_notes=M('edu_notes');
   //分类 2016-09-21新增
   if(!empty($communist_no)){
   	$notes_map['add_staff'] = $communist_no;
   }
   if(!empty($notes_classify)){
   	$notes_map['notes_type'] = $notes_classify;
   }
   //标题及内容
   if(!empty($notes_content)){
   	$notes_map['notes_title'] = array('like','%'.$notes_content.'%');
   }
	if(!empty($start) && !empty($end)){
       $start = $start." 00:00:000";
       $end=$end." 23:59:59";
       $notes_map['update_time'] = array('between',array($start,$end));
   	}
	//20171208 增加按文章搜索
	if ($material){
		$notes_map['material_id'] = array('in',$material);
   	}
   	//20171221 增加按学习专题查询
   	if ($topic_id){
   		$notes_map['topic_type'] = $topic_id;
   	}
   	if(!empty($page) || !empty($pagesize)){
		$notes_list = $db_notes->order('add_time desc')->where($notes_map)->limit($page,$pagesize)->select();
   		$count = $db_notes->where($notes_map)->count();
   	}else{
   		$notes_list =$db_notes->order('add_time desc')->where($notes_map)->select();
   	}
 	$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
 	$type_map['type_group'] = "notes_type";
 	$type_name_arr = M('bd_type')->where($type_map)->getField('type_no,type_name');
    foreach($notes_list as &$list){
        $list['update_time'] = getFormatDate($list['update_time'],"Y-m-d H:i");
        $list['add_staff']= $staff_name_arr[$list['add_staff']];

        $list['material_thumb']= getUploadInfo($list['notes_thumb']);
        if($list['notes_type']){
            $list['notes_type']=$type_name_arr[$list['notes_type']];
        }
        if($list['topic_type']){
       	 $list['topic_type']=getTopicInfo( $list['topic_type']);
        }
    }
   if(!empty($type)){
       $arr['data'] = $notes_list;
       $arr['count'] = $count;
       return $arr;
   }
   return $notes_list;
}



/**
 *  @name  getTopicList()
 *  @desc  专题栏目列表
 * @param unknown $status
 * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, unknown, multitype:, object>|boolean
 */
function getTopicList($page,$pagesize){
	$edu_topic = M('edu_topic');
	if(!empty($pagesize)){
		$topic_data = $edu_topic->where("status = '1'")->order('add_time desc')->limit($page,$pagesize)->select();
	}else{
		$topic_data = $edu_topic->where("status = '1'")->order('add_time desc')->select();
	}
	
	if($topic_data){
		return $topic_data;
	}else{
		return false;
	}
}
/**
 *  @name  getTopicInfo()
 *  @desc  专题栏目列表
 * @param unknown $status
 * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, unknown, multitype:, object>|boolean
 */
function getTopicInfo($topic_id,$field='topic_title'){
	if(!empty($topic_id)){
        $db_topic=M('edu_topic');
        $topic_map['topic_id'] = $topic_id;
        $notice_info=$db_topic->where($topic_map)->find();
        if($field!='all') {
            $notice_info=$notice_info[$field];
        }
    }
    if($notice_info){
        return $notice_info;
    }else{
        return null;
    }
}

/**
 * @name  getTopicSelect()
 * @desc  获取专题列表
 * @param 当前专题编号   $selected_no（支持多个）
 * @return 带选中状态的部门下拉列表（HTML代码）
 * @author 刘丙涛
 * @version 版本 V1.0.0
 * @addtime   2017-10-26
 */
function getTopicSelect($selected_no){
    $db_topic = M('edu_topic');
    $topic_map['status'] = 1;
	$topic_list = $db_topic->field('topic_id,topic_title')->where($topic_map)->select();
    $topic_options="";
	$select_arr = strToArr($selected_no);
    foreach($topic_list as &$topic){
		$selected="";
		foreach($select_arr as $arr){
			if($arr==$topic['topic_id']){
				$selected="selected=true";
			}
		}
		$topic_options.="<option $selected value='".$topic['topic_id']."'>".$topic['topic_title']."</option>";
    }
    if(!empty($topic_options)){
        return $topic_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}

/**
 * @name  getEduCustomization()
 * @desc  定制学习列表
 * @param 党员编号   $communist_no
 * @param 对应群体   $material_group
 * @param 资料标签   $material_data
 * @author 刘长军
 * @version 版本 V1.0.0
 * @addtime   2019-7-5
 */
function getEduCustomization($communist_no){
	$db_customization_log = M('edu_customization_log');
    $db_customization = M('edu_customization');
	$learn_data = [];

    $customization_id = $db_customization->where('communist_no="'.$communist_no.'"')->getField('customization_id');

	//课件
	$where['customization_id'] = $customization_id;
	$where['edu_type'] = 1;
	$article = $db_customization_log->where($where)->field('edu_num,all_data_id')->find();
	//课件学习
	if($article['all_data_id']){
		$material_map['communist_no'] = $communist_no;
		$material_map['material_no'] = array('in',$article['all_data_id']);
		$material_map['log_type'] = 1;
		$learn_article_num = M('edu_material_log')->where($material_map)->count("DISTINCT material_no");
	}
	
	//课件计算
	$learn_data['article']['num'] = $article['edu_num'];
    $learn_data['article']['learn'] = $learn_article_num;
    $learn_data['article']['learn_rate'] = round(($learn_article_num/$article['edu_num']) * 100,1);
    $learn_data['article']['residue'] = $article['edu_num'] - $learn_article_num;

	//视频
	$where2['customization_id'] = $customization_id;
	$where2['edu_type'] = 2;
	$article_video = $db_customization_log->where($where2)->field('edu_num,all_data_id')->find();
	//学习视频
	if($article_video['all_data_id']){
		$video_log_map['communist_no'] = $communist_no;
		$video_log_map['material_no'] = array('in',$article_video['all_data_id']);
		$video_log_map['log_type'] = 1;
		$learn_video_num = M('edu_material_log')->where($video_log_map)->count("DISTINCT material_no");
	}
	
	//视频计算
	$learn_data['video']['num'] = $article_video['edu_num'];
    $learn_data['video']['learn'] = $learn_video_num;
    $learn_data['video']['learn_rate'] = round(($learn_video_num/$article_video['edu_num']) * 100,1);
    $learn_data['video']['residue'] = $article_video['edu_num'] - $learn_video_num;

	//考试
	$where3['customization_id'] = $customization_id;
	$where3['edu_type'] = 3;
	$article_exam = $db_customization_log->where($where3)->field('edu_num,all_data_id')->find();
	//已经考试
	if($article_exam['all_data_id']){
		$exam_log_map['communist_no'] = $communist_no;
		$exam_log_map['exam_id'] = array('in',$article_exam['all_data_id']);
		$learn_exam_num = M('edu_exam_log')->where($exam_log_map)->count("DISTINCT exam_id");
	}
	
	//考试计算
    $learn_data['exam']['num'] = $article_exam['edu_num'];
    $learn_data['exam']['learn'] = $learn_exam_num;
    $learn_data['exam']['learn_rate'] = round(($learn_exam_num/$article_exam['edu_num']) * 100, 1);
    $learn_data['exam']['residue'] = $article_exam['edu_num'] - $learn_exam_num;

    //笔记im
    if($article['all_data_id'] !='' && $article['all_data_id'] !=null){
    	$aaaa = explode(',',$article['all_data_id']);
    }else if($article_video['all_data_id'] !='' && $article_video['all_data_id'] !=null){
    	$bbbb = explode(',',$article_video['all_data_id']);
    }
    if($aaaa){
    	$material_notes_id_arr = $aaaa;
    }
    if($bbbb){
    	$material_notes_id_arr = $bbbb;
    }
    if($bbbb && $aaaa){
    	$material_notes_id_arr = array_merge($aaaa, $bbbb);
    }
    $topic_notes_num = count($material_notes_id_arr);
	if($topic_notes_num != '0'){
		$notes_map['communist_no'] = $communist_no;
		$notes_map['log_type'] = 2;
		$notes_map['material_no'] = array('in',$material_notes_id_arr);
		$learn_notes_num = M('edu_material_log')->where($notes_map)->count("DISTINCT material_no");
	}else{
		$learn_notes_num = 0;
	}
    if(!empty($material_notes_id_arr)){
		$ppp['add_staff'] = $communist_no;
		$ppp['material_id'] = array('in',$material_notes_id_arr);
		$learn_notes_count = M('edu_notes')->where($ppp)->count();
	}else{
		$learn_notes_count=0;
	}
    
	$learn_data['notes']['num'] = $topic_notes_num;
    $learn_data['notes']['learn'] = $learn_notes_num;
    $learn_data['notes']['learn_rate'] = round(($learn_notes_num/$topic_notes_num) * 100, 1);
    $learn_data['notes']['residue'] = $topic_notes_num - $learn_notes_num;
    $learn_data['notes']['count'] = $learn_notes_count;
    $learn_data['sum_learn_rate'] = round((($learn_article_num+$learn_video_num+$learn_exam_num+$learn_notes_num)/($article['edu_num']+$article_video['edu_num']+$article_exam['edu_num']+$topic_notes_num)) * 100,1);
	return $learn_data;
}
/**
 * @name  getEduCustomizationSave()
 * @desc  定制学习保存
 * @param 党员编号   $communist_no
 * @param 对应群体   $material_group
 * @param 资料标签   $material_data
 * @author 刘长军
 * @version 版本 V1.0.0
 * @addtime   2019-7-5
 */
function getEduCustomizationSave($communist_no,$material_group,$material_data,$customization_id){
	$db_cat = M('edu_material_category');
	$db_material = M('edu_material');
	$db_customization_log = M('edu_customization_log');
    // 学习课件
    $article_cat_list = $db_cat->where("status=1 and cat_type=11")->getField('cat_id', true);
    if (!empty($article_cat_list)) {
        $article_cat_list = implode(',', $article_cat_list);
        $map_material['material_cat'] = array('in',$article_cat_list);
        $map_material['_string'] = "find_in_set('".$material_group."',material_group) and find_in_set('".$material_data."',material_data)";
        $article_list = $db_material->where($map_material)->getField('material_id',true);
        $article_num = $db_material->where($map_material)->count('material_id');//课件数量
        $article_str = implode(',', $article_list);
        $article['customization_id'] = $customization_id;
        $article['edu_type'] = 1;
        $article['edu_num'] = $article_num;
        $article['all_data_id'] = $article_str;
        $article['add_time'] = date('Y-m-d H:i:s');
        $article['update_time'] = date('Y-m-d H:i:s');
        $article['add_staff'] = $communist_no;
        $db_customization_log->add($article);

    }
    // 学习视频
    $video_cat_list = $db_cat->where("status=1 and cat_type=21")->getField('cat_id', true);
    if (!empty($video_cat_list)) {
        $video_cat_list = implode(',', $video_cat_list);
        $map_material_video['_string'] = "find_in_set('".$material_group."',material_group) and find_in_set('".$material_data."',material_data)";
        $map_material_video['material_cat'] = array('in',$video_cat_list);
        $video_id_arr = M('edu_material')->where($map_material_video)->getField('material_id', true);
        $article_video_num = $db_material->where($map_material_video)->count('material_id');//视频数量
        $article_video_str = implode(',', $video_id_arr);
        $article_video['customization_id'] = $customization_id;
        $article_video['edu_type'] = 2;
        $article_video['edu_num'] = $article_video_num;
        $article_video['all_data_id'] = $article_video_str;
        $article_video['add_time'] = date('Y-m-d H:i:s');
        $article_video['update_time'] = date('Y-m-d H:i:s');
        $article_video['add_staff'] = $communist_no;
        $db_customization_log->add($article_video);
    }
    //考试
	$party_no = M('ccp_communist')->where("communist_no=$communist_no")->getField('party_no');
	$exam_map['status'] = '21';
    $exam_map['_string'] = "find_in_set('".$material_group."',exam_group) and find_in_set('".$material_data."',exam_data_r) and find_in_set('".$party_no."',exam_party)";
    $topic_exam_id_arr = M('edu_exam')->where($exam_map)->getField('exam_id', true);
    $topic_exam_num = M('edu_exam')->where($exam_map)->count('exam_id');//考试数量
    $exam_id_arr = implode(',', $topic_exam_id_arr);
    $exam['customization_id'] = $customization_id;
    $exam['edu_type'] = 3;
    $exam['edu_num'] = $topic_exam_num;
    $exam['all_data_id'] = $exam_id_arr;
    $exam['add_time'] = date('Y-m-d H:i:s');
    $exam['update_time'] = date('Y-m-d H:i:s');
    $exam['add_staff'] = $communist_no;
    $db_customization_log->add($exam);

    return true;
}


/**
 * @name  getTopicLearnInfo()
 * @desc  获取专题学习进度
 * @param 当前专题编号   $topic_id
 * @return 带选中状态的部门下拉列表（HTML代码）
 * @author 刘丙涛
 * @version 版本 V1.0.0
 * @addtime   2017-10-26
 */
function getTopicLearnInfo($topic_id,$communist_no){
	//  获取专题下的课件、视频、考试的各个总数量
	$topic_article_num = 0;
	$topic_video_num = 0;
	$topic_exam_num = 0;

	$db_cat = M('edu_material_category');
	// 学习课件数量
    $article_cat_list = $db_cat->where("status=1 and cat_type=11")->getField('cat_id', true);

    if (!empty($article_cat_list)) {
        $article_cat_list = implode(',', $article_cat_list);

        $topic_article_num = getMaterialList($article_cat_list, null, null, null, null, $topic_id, null, null,1);
        $material_map['material_cat'] = array('in',$article_cat_list);
        $material_map['material_topic'] = $topic_id;
        $article_id_arr = M('edu_material')->where($material_map)->getField('material_id', true);
    }
    // 学习视频数量
    $video_cat_list = $db_cat->where("status=1 and cat_type=21")->getField('cat_id', true);
    if (!empty($video_cat_list)) {
        $video_cat_list = implode(',', $video_cat_list);
        $topic_video_num = getMaterialList($video_cat_list, null, null, null, null, $topic_id, null, null,1);
        
        $material_map['material_cat'] = array('in',$video_cat_list);
        $material_map['material_topic'] = $topic_id;
        $video_id_arr = M('edu_material')->where($material_map)->getField('material_id', true);
    }

    // 学习笔记总数
    if($video_id_arr != '' && $video_id_arr!=null){
    	$material_notes_id_arr = $video_id_arr;
    }else if( $article_id_arr != '' && $article_id_arr!=null){
    	$material_notes_id_arr = $article_id_arr;
    }
    if($video_id_arr && $article_id_arr){
    	$material_notes_id_arr = array_merge($article_id_arr, $video_id_arr);
    }

    $topic_notes_num = count($material_notes_id_arr);

    // 考试数量
    $party_no = M('ccp_communist')->where("communist_no=$communist_no")->getField('party_no');
	$exam_map['_string'] = "find_in_set('".$party_no."',exam_party)";
    $exam_map['exam_topic'] = $topic_id;
	$exam_map['status'] = '21';
    $topic_exam_num = M('edu_exam')->where($exam_map)->count();
    $topic_exam_id_arr = M('edu_exam')->where($exam_map)->getField('exam_id', true);
    // 获取人员已经学习和参加的 课件、视频 和考试的数量
    $learn_article_num = 0;
    $learn_video_num = 0;
    $learn_exam_num = 0;
    $learn_notes_num = 0;
    // 课件学习数
    if(!empty($article_id_arr)){
		$article_log_map['communist_no'] = $communist_no;
	 	$article_log_map['material_no'] = array('in',$article_id_arr);
	 	$article_log_map['log_type'] = 1;
	 	$article_id_str = implode(',', $article_id_arr);
	    $learn_article_num = M('edu_material_log')->where($article_log_map)->count("DISTINCT material_no");
	    //var_dump(M()->getLastSql());die;
	    //$learn_article_num = M()->query("SELECT COUNT(DISTINCT communist_no) as count FROM sp_edu_material_log WHERE communist_no = $communist_no AND 'material_no' IN ($article_id_str) AND log_type = 1");
    }
    // 视频学习数
    if(!empty($video_id_arr)){
    	$video_log_map['communist_no'] = $communist_no;
    	$video_log_map['material_no'] = array('in',$video_id_arr);
    	$video_log_map['log_type'] = 2;
    	$learn_video_num = M('edu_material_log')->where($video_log_map)->count("DISTINCT material_no");
    }
    // 参加考试数量
    if(!empty($topic_exam_id_arr)){
    	$exam_log_map['communist_no'] = $communist_no;
    	$exam_log_map['exam_id'] = array('in',$topic_exam_id_arr);
    	$learn_exam_num = M('edu_exam_log')->where($exam_log_map)->count("DISTINCT exam_id");
    }
    // 已完成的学习笔记数
    if(!empty($material_notes_id_arr)){
    	$notes_map['communist_no'] = $communist_no;
    	$notes_map['log_type'] = 2;
        $material_notes_id_arr = implode(',', $material_notes_id_arr);
    	$notes_map['material_no'] = array('in',$material_notes_id_arr);
    	$learn_notes_num = M('edu_material_log')->where($notes_map)->count("DISTINCT material_no");

    	$pppp['add_staff'] = $communist_no;
    	$pppp['material_id'] = array('in',$material_notes_id_arr);
    	$learn_notes_count = M('edu_notes')->where($pppp)->count();
    }

    $learn_data['article']['num'] = $topic_article_num;
    $learn_data['article']['learn'] = $learn_article_num;
    $learn_data['article']['learn_rate'] = round(($learn_article_num/$topic_article_num) * 100,1);
    $learn_data['article']['residue'] = $topic_article_num - $learn_article_num;

    $learn_data['video']['num'] = $topic_video_num;
    $learn_data['video']['learn'] = $learn_video_num;
    $learn_data['video']['learn_rate'] = round(($learn_video_num/$topic_video_num) * 100,1);
    $learn_data['video']['residue'] = $topic_video_num - $learn_video_num;


    $learn_data['exam']['num'] = $topic_exam_num;
    $learn_data['exam']['learn'] = $learn_exam_num;
    $learn_data['exam']['learn_rate'] = round(($learn_exam_num/$topic_exam_num) * 100, 1);
    $learn_data['exam']['residue'] = $topic_exam_num - $learn_exam_num;


    $learn_data['notes']['num'] = $topic_notes_num;
    $learn_data['notes']['learn'] = $learn_notes_num;
    $learn_data['notes']['learn_rate'] = round(($learn_notes_num/$topic_notes_num) * 100, 1);
    $learn_data['notes']['residue'] = $topic_notes_num - $learn_notes_num;
    $learn_data['notes']['count'] = $learn_notes_count;
    
    $learn_data['rate'] = round(($learn_article_num + $learn_video_num + $learn_exam_num)/($topic_article_num + $topic_video_num + $topic_exam_num) * 100 , 1);
    return $learn_data;
}
/**
 * @name  getEduTopicIntegral()
 * @desc  获取专题学习积分
 * @param 当前专题编号   $topic_id
 * @return 
 * @author 王宗斌
 * @version 版本 V1.0.0
 * @addtime   2019-7-4
 */
function getEduTopicIntegral($topic_id,$communist_no){
	
	$db_cat = M('edu_material_category');
	// 学习课件数量
    $article_cat_list = $db_cat->where("status=1 and cat_type=11")->getField('cat_id', true);
    if(!empty($article_cat_list)){
		$article_where['material_cat'] = array('in',$article_cat_list);
	}
	$article_where['material_topic'] = $topic_id;
	$material_data_arr = M("edu_material")->where($article_where)->getField('material_id',true);
	if(!empty($material_data_arr)){
		$article_log_map['communist_no'] = $communist_no;
		$article_log_map['material_no'] = array('in',$material_data_arr);
		$learn_article_num = M('edu_material_log')->where($article_log_map)->count("DISTINCT communist_no");
		$integral_article = getConfig('integral_article') * $learn_article_num;
	}else{
		$integral_article = 0;
	}
	
	//视频
	$video_cat_list = $db_cat->where("status=1 and cat_type=21")->getField('cat_id', true);
	if(!empty($video_cat_list)){
		$video_where['material_cat'] = array('in',$video_cat_list);
	}
	
	$video_where['material_topic'] = $topic_id;
	$video_data_arr = M("edu_material")->where($video_where)->getField('material_id',true);
	
	if(!empty($video_data_arr)){
		$video_log_map['communist_no'] = $communist_no;
		$video_log_map['material_no'] = array('in',$video_data_arr);
		$learn_video_num = M('edu_material_log')->where($video_log_map)->count("DISTINCT communist_no");
		$integral_video = getConfig('integral_video') * $learn_video_num;
	}else{
		$integral_video = 0;
	}
	
	// 学习笔记
    $material_notes_arr = array_merge($material_data_arr, $video_data_arr);
    
    if(!empty($material_notes_arr)){
    	$notes_map['add_staff'] = $communist_no;
		$notes_map['material_id'] = array('in',$material_notes_arr);
		$learn_notes_num = M('edu_notes')->where($notes_map)->count("DISTINCT material_id");
		$integral_notes_communist = getConfig('integral_notes_communist') * $learn_notes_num;
	}else{
		$integral_notes_communist = 0;
	}
	//考试
	$exam_where['exam_topic'] = $topic_id;
	$topic_exam_id_arr = M('edu_exam')->where($exam_where)->getField('exam_id', true);
	if(!empty($topic_exam_id_arr)){
		$exam_map['communist_no'] = $communist_no;
		$exam_map['exam_id'] = array('in',$topic_exam_id_arr);
		$exam_map['memo'] = 1;
		$learn_exam_num = M('edu_exam_log')->where($exam_map)->count("DISTINCT communist_no");
		$learn_exam_data = M('edu_exam_log')->where($exam_map)->getField('exam_id', true);
		if(!empty($learn_exam_data)){
			$exam_map['exam_id'] = array('in',$learn_exam_data);
			$exam_integral = M('edu_exam_log')->where($exam_map)->sum('log_integral');
		}else{
			$exam_integral = 0;
		}
		
	}else{
		$exam_integral = 0;
	}
	
	$data['integral_article'] = $integral_article;//文章
	$data['integral_video'] = $integral_video;//视频
	$data['integral_notes_communist'] = $integral_notes_communist;//笔记
	$data['exam_integral'] = $exam_integral;//考试
	$data['integral'] = $integral_article+$integral_video+$integral_notes_communist+$exam_integral;
    return $data;
}
/**
 * @name  getExamList()
 * @desc  获取考试列表
 * @param $exam_topic 专题
 * @param $communist_no 党员编号
 * @param $is_exam 是否考试 1:未考2:已考 3模拟
 * @param $page 页数
 * @param $pagesize 每页显示几条数据
 * @return 考试列表
 * @author 刘丙涛
 * @time   2017-11-09
 */
function getExamList($exam_topic,$communist_no,$is_exam,$search,$page,$pagesize,$exam_group,$exam_data_r){
	$db_exam =M('edu_exam');
    if (!empty($communist_no)){
        $where = 'find_in_set('.getCommunistInfo($communist_no,'party_no').', exam_party)';
        
        if ($is_exam == '1'){
            $where .= " and e.status=21 and l.exam_id is null and e.is_simulation=0";
            $l_name = "(SELECT * FROM sp_edu_exam_log where communist_no = $communist_no)";
        }else if($is_exam == '2'){
            $where .= " and communist_no=$communist_no";
            $l_name = "sp_edu_exam_log";
        }else{
            $where .= " and e.status=21 and e.is_simulation=1 and l.exam_id is null";
            $l_name = "(SELECT * FROM sp_edu_exam_log where communist_no = $communist_no)";
        }
        if (!empty($exam_topic)){
            $where .= " and exam_topic=$exam_topic";
        }
        if (!empty($exam_group) && !empty($exam_data_r)){
            $where .= " and find_in_set($exam_group , exam_group) and find_in_set($exam_data_r , exam_data_r)";
        }
        $num = $db_exam->query("select count(e.exam_id) as count from sp_edu_exam as e left join sp_edu_exam_log as l on e.exam_id=l.exam_id where $where");
        $count = $num['0']['count'];
        $exam_list = $db_exam->query("select e.exam_id,e.exam_topic,e.exam_thumb,e.exam_questions,e.add_staff,e.is_simulation,e.exam_title,e.exam_time,e.exam_integral,e.exam_date  from sp_edu_exam as e left join $l_name as l on
            e.exam_id=l.exam_id where $where order by e.add_time desc limit $page,$pagesize");
    }else{
        $where['status'] = array('neq','');
        if (!empty($search)){
            $where['exam_title'] = array('like',"'%$search%'");
        }
        $count = $db_exam->where($where)->count();
        $exam_list = $db_exam->where($where)
            ->field("*,(select count(log_id) from sp_edu_exam_log where sp_edu_exam_log.exam_id=sp_edu_exam.exam_id) as exam_communist_num")
            ->order('add_time desc')->limit($page,$pagesize)->select();
    }
    foreach($exam_list as &$list){
    	if(!empty($list['exam_thumb'])){
    		$list['exam_thumb'] = getUploadInfo($list['exam_thumb']);
    	}else{
    		$list['exam_thumb'] = "/statics/public/images/kaoshi.png";
    	}
    	$list['questions_num'] = M('edu_questions')->where("questions_id in({$list['exam_questions']})")->count();
    	$list['exam_title_type'] = "时长".$list['exam_time']."分钟/".$list['questions_num']."大题";
    	$list['log_score'] = M('edu_exam_log')->where("exam_id = ".$list['exam_id'] . ' and communist_no = ' .$communist_no )->getField('log_score');
    }
	if(!empty($exam_list)){
	    $data['data'] = $exam_list;
	    $data['count'] = $count;
		return $data;
	}else{
		return false;
	}
}
/**
 * @name  getExamInfo()
 * @desc  获取试卷信息
 * @param $exam_id   试卷id
 * @param $field  字段
 * @return 试卷详情
 * @author 王桥元
 * @time   2017-05-11
 */
function getExamInfo($exam_id,$field='all'){
    $db_exam = M('edu_exam');
    $exam_map['exam_id'] = $exam_id;
    if ($field =='all'){
        $exam_data = $db_exam->where($exam_map)->field('*,(select count(log_id) from sp_edu_exam_log where sp_edu_exam.exam_id=sp_edu_exam_log.exam_id) as count')->find();
    }else{
        $exam_data = $db_exam->where($exam_map)->getField($field);
    }
    return $exam_data;
}

/**
 * @name  getExamCommunistInfo()
 * @desc  获取人员考试详情
 * @param  $exam_id   试卷编号
 * @return $communist_no 党员编号
 * @return 考试列表
 * @author 刘丙涛
 * @time   2017-11-10
 */
function getExamCommunistInfo($exam_id,$communist_no){
    $db_questions = M('edu_questions');//题库表
    $db_log = M('edu_exam_log');//考试记录
    $db_answer = M('edu_exam_answer');//答题表
    $exam_data = getExamInfo($exam_id);//试卷信息
    $questions_id = $exam_data['exam_questions'];//试卷id串
    //考试人
    $exam_data['communist_name'] = getCommunistInfo($communist_no);
    if(!empty($questions_id)){
    	$questions_map['questions_id'] = array('in',$questions_id);
        $exam_data['exam_point'] = $db_questions->where($questions_map)->sum('questions_score');
        $communist_questions = $db_questions->where($questions_map)->field('questions_id,questions_title,questions_item,questions_answer,questions_type,memo')->select();
        $exam_data['questions_num'] = count($communist_questions);
    }
    $log_map['communist_no'] = $communist_no;
    $log_map['exam_id'] = $exam_id;
    $branch = $db_log->where($log_map)->field('log_score,log_integral,time_lave')->find();
    //考试试题列表
   
    $num = 1;
    $answer_map['communist_no'] = $communist_no;
    $answer_map['exam_id'] = $exam_id;
    foreach($communist_questions as &$list){
        $list['num'] = $num;
        $answer_map['questions_id'] = $list['questions_id'];
        $list['my_questions_item'] = $db_answer->where($answer_map)->getField('answer_item');
        $list['type'] = $list['questions_type'];
        $list['questions_type'] = getBdTypeInfo($list['questions_type'],'topic_type');
        $list['answer'] = $list['my_questions_item']==$list['questions_answer']?1:0;
        $num++;
    }
    $data['exam'] = $exam_data;
    $data['questions_list'] = $communist_questions;
    $data['branch'] = $branch;
    return $data;
}

/**
 * @name  getQuestionsList()
 * @desc  获取题库列表
 * @param
 * @return 考试列表
 * @author 杨凯
 * @time   2017-10-17
 */
function getQuestionsList($exam_map,$status,$search,$page,$pagesize){
	$db_exam =M('edu_questions');
    if (!empty($search)){
    	$exam_map['questions_title'] = array('like','%'.$search.'%');
    }
    if (!empty($status)){
    	$exam_map['status'] = $status;
    }
    if(empty($page) && empty($pagesize)){
    	$data = $db_exam->where($exam_map)->order('add_time desc')->select();
    }else {
    	$count = $db_exam->where($exam_map)->count();
    	$questions = $db_exam->where($exam_map)->limit($page,$pagesize)->order('add_time desc')->select();
    	$data['count'] = $count;
    	$data['data'] = $questions;
    }
    return $data;
}

/**
 * @name  getQuestionsInfo()
 * @desc  获取考题详情
 * @param 考题编号   $questions_id
 * @return 考题详情
 * @author 杨凯
 * @time   2017-10-17
 */
function getQuestionsInfo($questions_id,$field="all"){
	$db_exam_questions=M('edu_questions');
	if($field == "all"){
		$exam_data = $db_exam_questions->where("questions_id = '".$questions_id."'")->find();
	}else{
		$exam_data = $db_exam_questions->where("questions_id = '".$questions_id."'")->getField($field);
	}
	if(!empty($exam_data)){
		return $exam_data;
	}else{
		return "";
	}
}

/**
 * @name  getGroupSelect()
 * @desc  获取对应群体/资料标签
 * @param
 *		@group_type   1对应群体    2资料标签
 * 		@str  选中项
 * @return 列表
 * @author 刘长军
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2019-03-28
 */
function getGroupSelect($group_type,$str){
	$db_groupdata=M('edu_groupdata');
	$where['group_type'] = $group_type;
	$where['status'] = 1;
	$groupdata = $db_groupdata->where($where)->select();
	$data_list = '';
	$str_array = explode(',',$str);
	foreach($groupdata as &$list){
		$selected = "";
		foreach($str_array as $dd){
			if($dd == $list['group_id']){
				$selected = "selected=true";
			}
		}
		$data_list .="<option value='".$list['group_id']."'".$selected.">".$list['group_title']."</option>";
	}
	return $data_list;
}
/**
 * @name  getGroupInfo()
 * @desc  获取对应群体/资料标签
 * @param
 * 		@str  ID
 * @return 列表
 * @author 刘长军
 * @version 版本 V1.0.0
 * @updatetime
 * @addtime   2019-03-28
 */
function getGroupInfo($str){
	$str_array = explode(',',$str);
	$db_groupdata=M('edu_groupdata');
	$groupdata = $db_groupdata->select();
	$data_str = [];
	foreach($str_array as $dd){
		foreach($groupdata as $list){
			if($dd == $list['group_id']){
				$data_str[] = $list['group_title'];
			}
		}
	}
	$data_str = implode(',',$data_str);
	return $data_str;
}

/********************************学习相关业务层方法 开始*************************************/



?>