<?php
/********************************绩效相关基础层方法 开始*************************************/

/**
 * @name  getAssessTplItemInfo()
 * @desc  获取绩效模板考核项详情
 * @param $template_id模板id
 * 		  $filed  查询字段
 * 		  $where  搜索条件
 * @return true/false
 * @author 王桥元
 * @version 版本 V1.1.0
 * @updatetime
 * @addtime   2017-07-28
 */
function getAssessTplItemInfo($template_id,$field="",$where){
	$db_assess = M("perf_assess_template_item");

	if(!empty($template_id)){
		$where['template_id'] = $template_id;

		if($field == ""){
			$item_data = $db_assess->where($where)->find();
		}else{
			$item_data = $db_assess->where($where)->field($field)->find();
		}
	}
	return $item_data;
}
/**
 * @name  getAssessTplItemList()
 * @desc  获取绩效模板考核项列表
 * @param $template_id 模板id
 * 		  $filed  查询字段
 * 		  $where  搜索条件
 * @return true/false
 * @author 王桥元-黄子正
 * @version 版本 V1.1.0
 * @updatetime 2017-10-29
 * @addtime   2017-07-28
 */
function getAssessTplItemList($template_id,$field="",$where=""){
    $db_assess = M("perf_assess_template_item");
	if(!empty($template_id)){
		$where['template_id'] = $template_id;
		
		if($field == ""){
			$template_item = $db_assess->where($where)->select();
		}else{
			$template_item = $db_assess->where($where)->field($field)->select();
		}
		if($template_item){
		    $type_name_arr = M('bd_type')->where("type_group = 'template_type'")->getField('type_no,type_name');
		    $type_names_arr = M('bd_type')->where("type_group = 'cycle_type'")->getField('type_no,type_name');
		    $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		    foreach ($template_item as &$item){
		        if(!empty($item['item_type'])){
		            $item['type_name'] = $type_name_arr[$item['item_type']];
		        }
		        if(!empty($item['item_cycle_type'])){
		            $item['cycle_name'] = $type_names_arr[$item['item_cycle_type']];
		        }
		        $item['communist_name']=$communist_name_arr[$item['item_manager']];
		    }
		}
	}
	if(!empty($template_item)){
		return $template_item;
	}else {
		return false;
	}
}
/**
 * @name  getAssessTplInfo()
 * @desc  获取绩效模板
 * @param $template_id 模板id
 * 		  $filed  查询字段
 * 		  $where  搜索条件
 * @return true/false
 * @author 王桥元
 * @version 版本 V1.1.0
 * @updatetime
 * @addtime   2017-07-27
 */
function getAssessTplInfo($template_id="",$field="",$where=""){
	$db_assess_template = M("perf_assess_template");
	if(!empty($template_id)){
		$where['template_id'] = $template_id;
		if($field == ""){
			$assess_data = $db_assess_template->where($where)->find();
		}else{
			$assess_data = $db_assess_template->where($where)->field($field)->find();
		}
		return $assess_data;
	}else{
		if($field == ""){
			$assess_data = $db_assess_template->where($where)->order("update_time desc")->select();
	
		}else{
			$assess_data = $db_assess_template->where($where)->field($field)->order("update_time desc")->select();
			
		}
	}
	$num = $db_assess_template->where($where)->count();
	if($assess_data){
	    $adax_arr['count'] = $num;
	    $adax_arr['data'] = $assess_data;
	    return $adax_arr;
	}else {
		return false;
	}
	
}
/**
 * @name  saveAssessTpl()
 * @desc  绩效模板保存方法
 * @param $post
 * @return true/false
 * @author 王桥元
 * @version 版本 V1.1.0
 * @updatetime   
 * @addtime   2017-07-27
 */
function saveAssessTpl($post){
	$db_assess_template = M("perf_assess_template");
	$post['update_time'] = date("Y-m-d H:i:s");
	$post['status']='1';
	if(!empty($post['template_id'])){
		$where['template_id'] = $post['template_id'];
		$assess_data = $db_assess_template->where($where)->save($post);
	}else{
		$post['add_time'] = date("Y-m-d H:i:s");
		$post['add_staff'] = session('staff_no');
		$assess_data = $db_assess_template->add($post);
	}
	return true;
}



	/**
 * @name  getAssessList()
 * @desc  获取考核项列表
 * @param $assess_id 考核项id 
 * @param $dep_no 部门编号    必传
 * @param $group 区分党员,党支部 必传
 * @return 考核项列表
 * @author 袁文豪
 * @version 版本 V1.1.0
 * @updatetime   2016/09/21
 * @addtime   2016-09-21
 */
 function getAssessList($assess_id,$party_no,$group){
		if(!empty($assess_id)){
			$where['item_id']=$assess_id;
		}
		if(!empty($party_no)){
			$where['post_no']=$party_no;
		}
		if(!empty($group)){
			$where['item_group']=$group;
		}
		$assess=M("perf_assess_template_item")->where($where)->select();
		return  $assess;
		$type_name_arr = M('bd_type')->where("type_group = 'template_type'")->getField('type_no,type_name');
		$type_names_arr = M('bd_type')->where("type_group = 'cycle_type'")->getField('type_no,type_name');
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach($assess as &$row){
		    $row['type_name'] = $type_name_arr[$row['item_type']];
		    $row['cycle_name'] = $type_names_arr[$row['item_cycle_type']];
			$row['communist_name']=$communist_name_arr[$row['select_communist']];
		}
		if(!empty($assess)){
			return $assess;
		}else{
			return array();
		}
		
 }
/**
 * @name  getAssessInfo()
 * @desc  获取考核项
 * @param $time 周期
 * @return 
 * @author 袁文豪-黄子正
 * @version 版本 V1.1.0
 * @updatetime   2017/10/29
 * @addtime   2016-09-21
 */
function getAssessInfo($party_no,$time,$group='party',$ass_type=''){
    $where['party_no']=$party_no;
	$party=M("ccp_party")->where($where)->field("party_name,party_no")->find();
	$where1['template_relation_type']=$group;
	$where1['_string']="FIND_IN_SET($party_no,template_relation_no)";
	$template_id=M('perf_assess_template')->where($where1)->getField('template_id');
	// dump($template_id);
	// exit;
	if($template_id){
		$map['item_group']=$group;
		if($ass_type == '1'){
			$map['item_pid']='0';
		}
		// 获取绩效模板项列表
	    $tpl_item_list = getAssessTplItemList($template_id,"",$map);
        $assess_name="";
        $score="";
        if(!empty($tpl_item_list)){
            foreach($tpl_item_list as &$performance){
                $arr['assess_relation_no'] = $party_no;
                $arr['assess_relation_type'] = $group;
                $arr['assess_cycle'] = $time;
                if($ass_type == '1' && $performance['item_pid'] == '0'){
                	$assess_name.='{"text":"'.$performance['item_name'].'", "max":100},';
                	$item_arra = M('perf_assess_template_item')->where("item_pid = $performance[item_id]")->field('item_id,item_proportion')->select();
		            $entering_sum_num=0;
		            foreach($item_arra as &$arra){
		            	$arr['item_id'] = $arra['item_id'];
		            	$entering=M("perf_assess")->where($arr)->getfield("assess_score");
		            	$entering_sum=$entering*($arra['item_proportion']/100);
		            	$entering_sum_num += (float)$entering_sum; 
		            }
		            $score.= empty($entering_sum_num)?0:$entering_sum_num.','; //"$entering";
                 }else{
                 	$assess_name.='{"text":"'.$performance['item_name'].'", "max":100},';
                	$arr['item_id'] = $performance['item_id'];
                	$entering=M("perf_assess")->where($arr)->getfield("assess_score");
                	$entering=$entering*($performance['item_proportion']/100);
                	$score.=empty($entering)?"0,":$entering.",";
                }
            }
            if($ass_type == '1'){
            	$party['assess_name']="[".$assess_name."]";
            	$party['score']="[".$score."]";
            }else{
            	$party['assess_name']="[".substr($assess_name,0,-1)."]";
            	$party['score']="[".substr($score,0,-1)."]";
            }
            
        }else{
            $party['assess_name']='[{"text":0, "max":10}]';
            $party['score']="[0]";
        }
	} else {
		$party['assess_name']='[{"text":0, "max":10}]';
        $party['score']="[0]";
	}
	if(!empty($party)){
		return $party;
	}else{
		return false;
	}
}
 /**
 * @name  getAssessItemSelect()
 * @desc  获取考核项列表
 * @param
 * @param 
 * @param 
 * @return 考核项列表
 * @author 王宗彬
 * @version 版本 V1.1.0
 * @updatetime   2016/09/21
 * @addtime   2019-04-24
 **/
 function getAssessItemSelect($selected_no){
	$assess_item = M('perf_assess_item');
	$map['status']=1;
	$assess_list = $assess_item->where($map)->field('item_id,item_name')->order('add_time desc')->select();
	$status_options = "";
	foreach($assess_list as &$list ) {
		$selected = "";
		if ($selected_no == $list ['item_id']) {
			$selected = "selected=true";
		}
		$item_options .= "<option $selected value='" . $list ['item_id'] . "'>" . $list ['item_name'] . "</option>";
	}
	if (!empty ($item_options)){
		return $item_options;
	} else {
		return "<option value=''>无数据</option>";
	}
 }
/********************************绩效相关基础层方法 结束*************************************/

/********************************绩效相关业务层方法 开始*************************************/


/********************************绩效相关业务层方法 结束*************************************/

?>