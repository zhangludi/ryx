<?php

/********************************志愿者，留言相关基础层方法 开始*************************************/

/**
 * @name:saveCommunistVolunteer()
 * @desc：志愿者保存
 * @param：$volunteer_data 志愿者数据
 * @return：指定员工指定字段的值
 * @author:王桥元
 * @addtime:2017-05-23
 * @version：V1.0.0
 **/
function saveCommunistVolunteer($volunteer_data){
	$db_volunteer=M('life_volunteer');
	$volunteer_data['update_time'] = date("Y-m-d H:i:s");
	if(!empty($volunteer_data['volunteer_id'])){
		$volunteer_map['volunteer_id'] = $volunteer_data['volunteer_id'];
		$volunteer = $db_volunteer->where($volunteer_map)->save($volunteer_data);
	}else{
		$volunteer_data['add_time'] = date("Y-m-d H:i:s");
		$volunteer_data['add_staff'] = session('staff_no');
		$volunteer_data['status'] = "11";
		$volunteer = $db_volunteer->add($volunteer_data);
	}
	
	if($volunteer){
		return true;
	}
	else{
		return null;
	}
}
/**
 * @name:getCommunistVolunteerList()
 * @desc：获取志愿者列表
 * @param：$party_no 部门编号
 * @return：部门志愿者列表
 * @author:王桥元
 * @addtime:2017-05-23
 * @version：V1.0.0
 **/
function getCommunistVolunteerList($party_nos,$page,$pagesize){
	$db_volunteer=M('life_volunteer');
	if(!empty($party_nos)){
		$where['party_no'] = array('in',$party_nos);
	}
	$volunteer_data['data'] = $db_volunteer->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
	$volunteer_data['count'] = $db_volunteer->where($where)->count();
    return $volunteer_data;
}
/**
 * @name:getActivityJoinInfo
 * @desc：获取志愿者活动 人员
 * @param：$apply_id：活动id
 * @param：		$activity_id 志愿者活动申请表ID
 * @return：true/false
 * @author:王宗彬
 * @addtime:2017-05-23
 * @version：V1.0.0
 **/
function getActivityJoinInfo($apply_id){
	$db_volunteer_apply=M('life_volunteer_activity_apply');
	$apply_map['apply_id'] = $apply_id;
	$apply_data = $db_volunteer_apply->where($apply_map)->find();
	if($apply_data){
		return $apply_data;
	}else{
		return "";
	}
}
/**
 * @name:saveActivityJoinApply
 * @desc：志愿者活动申请
 * @param：$apply_data 申请数据
 * @return：true/false
 * @author:王桥元
 * @addtime:2017-05-23
 * @version：V1.0.0
 **/
function saveActivityJoinApply($apply_data){
	$db_volunteer_apply=M('life_volunteer_activity_apply');
	$apply_data['update_time'] = date("Y-m-d H:i:s");
	if(!empty($apply_data['apply_id'])){
		$apply_map['apply_id'] = $apply_data['apply_id'];
		$apply_arr = $db_volunteer_apply->where($apply_map)->save($apply_data);
		if($apply_data['status'] == '2'){
			saveCommunistLog($apply_data['communist_no'],'20','',session('communist_no'),$apply_data['activity_title']);
		}
		//saveCommunistLog($apply_data['communist_no'],'20','',session('communist_no'),$apply_data['activity_title']);
	}else{
		$apply_data['add_time'] = date("Y-m-d H:i:s");
		$apply_data['add_staff'] = session('staff_no');
		$apply_data['status'] = "11";
		$apply_arr = $db_volunteer_apply->add($apply_data);
	}
	if($apply_arr){
		return true;
	}else{
		return false;
	}
}


/******************************end*************************************/
/**
 * @name    getSurveyList()
 * @desc    调查问卷
 * @param $status 问卷状态
 * @return
 * @author  刘丙涛
 * @version 版本 V1.0.0
 * @time    2017-11-09
 */
function getSurveyList($status,$page,$pagesize){
    $db_Survey = M('life_survey');
    if (!empty($status)){
        $where['status'] = $status;
    }
	if(!empty($pagesize)){
		$Survey_data['data'] = $db_Survey->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
		$Survey_data['count'] = $db_Survey->where($where)->count();
	}else{
		$Survey_data = $db_Survey->where($where)->order('add_time desc')->select();
	}
    if($Survey_data){
        return $Survey_data;
    } else {
        return false;
    }
}
/**
 * @name    getSurveyInfo()
 * @desc    获取调查问卷详情
 * @param   $urvey_id  调查问卷id
 * @param   $is_answer  是否获取选择人数
 * @return  array
 * @author  刘丙涛
 * @version 版本 V1.0.0
 * @time    2017-11-9
 */
function getSurveyInfo($survey_id,$is_answer){
    $db_survey = M('life_survey');
    $db_questions = M('life_survey_questions');
    $db_answer = M('life_survey_answer');
    $survey_map['survey_id'] = $survey_id;
    $survey_info = $db_survey->where($survey_map)->find();
    $survey_info['party_name'] = getPartyInfo($survey_info['party_no']);
    $questions_list = $db_questions->where($survey_map)->select();
    if ($is_answer == '1'){
        foreach ($questions_list as &$list){
        	$map['questions_id'] = $list['questions_id'];
            $item_sum = $db_answer->where($map)->field('answer_item,COUNT(*) as count')->group('answer_item')->select();
            $list['item'] = $item_sum;
        }
    }
    $data['survey_info'] = $survey_info;
    $data['questions_list'] = $questions_list;
    if($data['survey_info']){
        return $data;
    } else {
        return false;
    }
}
/**
 * @name    getSurveyQuestionsInfo()
 * @desc    获取民意问卷问题详情
 * @param   $questions_id  民意问卷id
 * @return  民意问卷列表
 * @author  王桥元
 * @version 版本 V1.0.0
 * @time    2017-06-21
 */
function getSurveyQuestionsInfo($questions_id){
    $db_Survey_questions = M('life_survey_questions');
    $questions_map['questions_id'] = $questions_id;
    $Survey_data = $db_Survey_questions->where($questions_map)->find();
    if($Survey_data){
        return $Survey_data;
    } else {
        return false;
    }
}

/**************************************留言建议***********************************************/

/**
 * @name:getGuestbookList
 * @desc：获取留言列表
 * @param：$communist_no 党员编号
 * @author：王宗彬
 * @addtime:2017-12-02
 * @version：V1.0.0
 **/
function getGuestbookList($party_no, $page, $pagesize){
	$list_guestbook = M("life_guestbook");

	$where = [];
	$child_nos_n = session('party_no_auth');//取本级及下级组织
	if(!empty($party_no)) : $party_no = getPartyChildNos($party_no); $where['party_no'] = array('in',$party_no); endif;
	if(empty($party_no)){
		$where['party_no'] = array('in',$child_nos_n);
	}
	//$where['status'] = array('eq',1);
	//$where['guestbook_pid'] = array('eq','0 ');

	$data = $list_guestbook->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
	$count = $list_guestbook->where($where)->count();
	$arr['data'] = $data;
	$arr['count'] = $count;
	return $arr;
}
/**
 * @name:getGuestbookInfo
 * @desc：获取留言列表
 * @param：$communist_no 党员编号
 * @author：王宗彬
 * @addtime:2017-12-02
 * @version：V1.0.0
 **/
function getGuestbookInfo($guestbook_id,$field='all'){
	$list_guestbook = M("life_guestbook");
	$where['guestbook_id'] = $guestbook_id;
	if($field == 'all'){
		$guestbook_data = $list_guestbook->where($where)->find();
		return $guestbook_data;
	}else{
		$guestbook_data = $list_guestbook->where($where)->field($field)->find();
		return $guestbook_data[$field];
	}
}

/********************************志愿者，留言相关基础层方法 结束*************************************/

/********************************志愿者，留言相关业务层方法 开始*************************************/

/**
 * @name:CheckVolunteer
 * @desc：判断是否为志愿者
 * @param：$communist_no 人员编号
 * @return：true/false
 * @author:王桥元
 * @addtime:2017-05-23
 * @version：V1.0.0
 **/
function CheckVolunteer($communist_no){
	$db_volunteer=M('life_volunteer');
	$where['communist_no'] = $communist_no;
	$where['status'] = 21;
	$apply_arr = $db_volunteer->where($where)->find();
	if($apply_arr){
		return true;
	}else{
		return false;
	}
}

/********************************志愿者，留言相关业务层方法 结束*************************************/

/**

 * @name:getPoorVillage

 * @desc：贫困村

 * @param：$measures_genre_no 贫困村编号

 * @return：true/false

 * @author:刘长军

 * @addtime:2018-11-23

 * @version：V1.0.0

 **/
function getPoorVillage($measures_genre_no) {
	$poor_village = M("poor_village")->where(['is_poor'=>1])->select();
	$array_measures_genre = strToArr($measures_genre_no,',');//将字符串转换成数组
	$village_options = '';
    foreach($poor_village as &$village){
    	$selected = '';
    	foreach($array_measures_genre as &$measures){
    		if($village['poor_village_id']==$measures){
    			$selected = 'selected';
    		}
    	}
    	$village_options .="<option $selected value='".$village['poor_village_id']."' >".$village['poor_village_name']."</option>";
    }
    if(!empty($village_options)){
    	return $village_options;
    }else{
    	return '<option>无数据</option>';
    }
    
}
/**

 * @name:getPoorVillage

 * @desc：贫困户

 * @param：$measures_genre_no 贫困户编号

 * @return：true/false

 * @author:刘长军

 * @addtime:2018-11-23

 * @version：V1.0.0

 **/
function getPoorhousehold($measures_genre_no) {
	$poor_household = M("poor_household")->where("is_poor_village=1")->select();
	$array_measures_genre = strToArr($measures_genre_no,',');//将字符串转换成数组
	$household_options = '';
    foreach($poor_household as &$household){
    	$selected = '';
    	foreach($array_measures_genre as &$measures){
    		if($household['poor_household_id']==$measures){
    			$selected = 'selected';
    		}
    	}
    	$household_options .="<option $selected value='".$household['poor_household_id']."' >".$household['poor_household_name']."</option>";
    }
    if(!empty($household_options)){
    	return $household_options;
    }else{
    	return '<option>无数据</option>';
    }
}
/**

 * @name  getHelpteamSelect()

 * @desc  获取部门列表

 * @param 当前部门的编号   $selected_no（支持多个）

 * @return 带选中状态的部门下拉列表（HTML代码）

 * @author 靳邦龙--王彬

 * @memo 部门编号修改为多编号（逗号分隔）

 * @version 版本 V1.0.1

 * @updatetime 2016-07-20

 * @addtime   2016-04-28

 */

function getHelpteamSelect($selected_no){

    $help_team=M('ccp_communist_help');

    $team_list=$help_team->field('help_id,help_name')->select();

    $team_options="";

	$select_arr = strToArr($selected_no);

    foreach($team_list as &$team){

		$selected="";

		foreach($select_arr as $arr){

			if($arr==$team['help_id']){

				$selected="selected=true";

			}

		}

		$team_options.="<option $selected  value='".$team['help_id']."'>".$team['help_name']."</option>";
    }

    if(!empty($team_options)){

        return $team_options;

    }else{

        return "<option value=''>无数据</option>";

    }

}

/**
 * @name  getSurveyInfo()
 * @desc  获取考题详情
 * @param 考题编号   $questions_id
 * @return 调查问卷
 * @author 刘长军
 * @time   2018-12-12
 */
function getSurveyInfoData($questions_id,$field="all"){
	$db_exam_questions=M('life_survey_questions');
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
 * @name  getVoteInfoData()
 * @desc  获取投票
 * @param 投票编号   $questions_id
 * @return 投票
 * @author 刘长军
 * @time   2018-12-12
 */
function getVoteInfoData($questions_id,$field="all"){
	$db_exam_questions=M('life_vote_subject');
	if($field == "all"){
		$exam_data = $db_exam_questions->where("subject_id = '".$questions_id."'")->find();
	}else{
		$exam_data = $db_exam_questions->where("subject_id = '".$questions_id."'")->getField($field);
	}
	if(!empty($exam_data)){
		return $exam_data;
	}else{
		return "";
	}
}

?>