<?php
/************************************问卷调查**************************************************/
namespace Life\Controller;

use Common\Controller\BaseController;
use Life\Model\LifeSurveyModel;
use Life\Model\LifeSurveyQuestionsModel;

class LifesurveyController extends BaseController{
	/**
	 * @name  life_survey_index()
	 * @desc  调查问卷
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-26
	 */
	public function life_survey_index(){
		checkAuth(ACTION_NAME);
		$this->display("Lifesurvey/life_survey_index");
	}
	/**
	 * @name  life_survey_index_data()
	 * @desc  问卷表数据页面
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-26
	 */
	public function life_survey_index_data()
	{
		$page = I('get.page');
		$pagesize = I('get.limit');
		$page = ($page-1)*$pagesize;
		$survey_data = getSurveyList('',$page,$pagesize);
		$confirm_del = 'onclick="if(!confirm(' . "'确认删除本次问卷？'" . ')){return false;}"';
		foreach ($survey_data['data'] as &$survey){
			$survey_id = $survey['survey_id'];
			$survey['survey_id'] = "<a class='fcolor-22' href='" . U('life_survey_info', array('survey_id' => $survey_id)) . "'> ".$survey['survey_id']."</a>";
			$survey['survey_title'] = "<a class='fcolor-22' href='" . U('life_survey_info', array('survey_id' => $survey_id)) . "'> ".$survey['survey_title']."</a>";
			if(!empty($survey['add_time'])){
				$survey['add_time'] = getFormatDate($survey['add_time'],'Y-m-d');
			}
            $survey['add_staff'] = getStaffInfo($survey['add_staff']);
			$survey['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('life_survey_info', array('survey_id' => $survey_id)) . "'> 查看</a><a class='layui-btn  layui-btn-xs layui-btn-f60' onclick='save_open($survey_id)'  ><i class='fa fa-trash-o'></i>编辑</a>";
			switch ($survey['status']){
				case 1:$survey['operate'] .= "&nbsp;&nbsp;&nbsp;<a class='btn btn-xs red btn-outline' href='" .  U('life_survey_save', array('survey_id' => $survey_id,'status'=>0)) . "' ><i class='fa fa-edit'></i>停用</a>";break;
				case 0:$survey['operate'] .= "<a class='layui-btn layui-btn-del layui-btn-xs' href='" .  U('life_survey_save', array('survey_id' => $survey_id,'status'=>1)) . "'><i class='fa fa-edit'></i>展示</a>";break;
			}		
		}
		$survey_data['code'] = 0;
        $survey_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($survey_data);
	}
	/**
	 * @name  life_survey_edit()
	 * @desc  问卷修改
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-26
	 */
	public function life_survey_edit(){
		checkAuth(ACTION_NAME);
		$survey_id = I('get.survey_id');
		if(!empty($survey_id)){
			$survey_info = getSurveyInfo($survey_id,'1');
			$this->assign('survey_info',$survey_info['survey_info']);
			$this->assign('survey_id',$survey_id);
		}
		$this->display("Lifesurvey/life_survey_edit");
	}
	/**
	 * @name  life_survey_save()
	 * @desc  问卷修改保存操作
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-26
	 */
	public function life_survey_save()
	{
		$survey_data = I("post.");
        $db_survey = M('life_survey');
		if (empty($survey_data)){
		    $is_auth = 1;
            $survey_data = I("get.");
            $result = $db_survey->save($survey_data);
            if (empty($result)){
                showMsg('success', '操作成功', U('Life/Lifesurvey/life_survey_index'));
            }else{
                showMsg('success', '操作成功', U('Life/Lifesurvey/life_survey_index'));
            }
        }else{
            //$db_survey = new LifeSurveyModel();
            $survey_data['add_staff'] = session('staff_no');
            $survey_data['add_time'] = date('Y-m-d H:i:s');
            $survey_data['update_time'] = date('Y-m-d H:i:s');
            //$result = $db_survey->updateData($survey_data,'survey_id');
            if (empty($survey_data['survey_id'])){
                $result = $db_survey->add($survey_data);
            }else {
                $result = $db_survey->save($survey_data);
            }
            if($result){
                if (empty($survey_data['survey_id'])){
                    showMsg('success', '操作成功', U('Life/Lifesurvey/life_survey_index'),1);
                }else{
                    showMsg('success', '操作成功', U('Life/Lifesurvey/life_survey_index'),1);
                }
            } else {
                showMsg('error', '操作失败');
            }
        }
		
	}
	/**
	 * @name  life_survey_info()
	 * @desc  调查问卷详情
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-11-09
	 */
	public function life_survey_info()
	{
		checkAuth(ACTION_NAME);
        $db_answer = M('life_survey_answer');
		$survey_id = I("get.survey_id");
		$survey_data = getSurveyInfo($survey_id,'1');
		$answer_map['survey_id'] = $survey_id;
		$answer_map['status'] = 1;
		$communist_list = $db_answer->where($answer_map)->group('communist_no')->select();
		if(!empty($communist_list)){
		    $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		    foreach($communist_list as &$list){
		        $list['communist_name'] = $communist_name_arr[$list['communist_no']];
                $list['operate'] = "<a class='btn blue btn-xs btn-outline' href='" . U('life_survey_result_info', array('survey_id' => $survey_id,'communist_no'=>$list['communist_no'])) . "'><i class='fa fa-info'></i>详情</a>";
            }
            $this->assign('communist_list',$communist_list);
		}
        if(!empty($survey_data)){
            foreach($survey_data['questions_list'] as &$question){
                $question_item_arr = explode(",",$question['questions_item']);
                if(!empty($question_item_arr)){
                    foreach ($question_item_arr as $item_key => $item_value) {
                        $question['questions_item_str'].= $item_value;
                    }
                }
            }
        }
		$this->assign("survey",$survey_data);
		$this->assign("survey_id",$survey_id);
		$this->display("Lifesurvey/life_survey_info");
	}
	/**
	 * @name  life_survey_delete()
	 * @desc  调查问卷删除
	 * @param $survey_id(问卷ID)
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-06-21
	 */
	public function life_survey_delete(){
		$survey_id = I("get.");
		$db_survey = new LifeSurveyModel;
		$result = $db_survey->delData($survey_id);
		if($result){
			showMsg('success', '操作成功', U('Life/Lifesurvey/life_survey_index'));
		} else {
			showMsg('error', '操作失败');
		}
	}
	/**
	 * @name  life_survey_questions_edit()
	 * @desc  新增/编辑考试试题
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_survey_questions_edit()
	{
		$exam_get = I("get.");
		// dump($exam_get);die;
		// if(!empty($exam_get['questions_id'])){
		// 	$questions_data = getSurveyQuestionsInfo($exam_get['questions_id']);
		// }
		$this->assign("survey_id",$exam_get['survey_id']);
		// $this->assign("questions",$questions_data);
		// $this->display("Lifesurvey/life_survey_questions_edit");
		$questions_data = getSurveyInfoData($exam_get['questions_id']);
		// dump($questions_data);die;
		$que_list_a ="
            <input type='hidden' id='num_no_a' name='num_no_a' value='2' />
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>问题选项：</strong></div>
                   <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                    <button type='button' onclick='buttondel();' class='layui-btn layui-btn-primary'>删除选项</button>
                    </div>
                    <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                    <button type='button' onclick='buttonfun();' class='layui-btn pull-right'>增加选项</button>
                    </div>
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>A：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' name='questions_item_a_1' value='' autocomplete='off' class='layui-input'></div>
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>B：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' name='questions_item_a_2' value='' autocomplete='off' class='layui-input'></div>
                    <div class='layui-col-xs1 text-r fsize-14 fcolor-65 lh-35'>
                    </div>
                </div>
            </div>";
        
        $option_list_a ="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' ><input type='radio' name='questions_answer_a' value='A' title='A'></div>
                            <div  class='layui-col-xs2 fsize-14 fcolor-65 lh-35'><input type='radio' name='questions_answer_a' value='B' title='B'></div>";
        $que_list_b ="
            <input type='hidden' id='num_no_b' name='num_no_b' value='2' />
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>问题选项：</strong></div>
                   <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                    <button type='button' onclick='buttondel();' class='layui-btn layui-btn-primary'>删除选项</button>
                    </div>
                    <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                    <button type='button' onclick='buttonfun();' class='layui-btn pull-right'>增加选项</button>
                    </div>
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>A：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' name='questions_item_b_1' value='' autocomplete='off' class='layui-input'></div>
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>B：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' name='questions_item_b_2' value='' autocomplete='off' class='layui-input'></div>
                    <div class='layui-col-xs1 text-r fsize-14 fcolor-65 lh-35'>
                    </div>
                </div>
            </div>";
        $option_list_b ="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' ><input type='checkbox'  name='questions_answer_b[]' title='A' value='A'></div>
     						  <div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' ><input type='checkbox' name='questions_answer_b[]' title='B' value='B'></div>";
        
        $que_list_c ="
            <input type='hidden' id='num_no_c' name='num_no_c' value='2' />
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>问题选项：</strong></div>
            
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>A：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' name='questions_item_c_1' value='' autocomplete='off' class='layui-input'></div>
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>B：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' name='questions_item_c_2' value='' autocomplete='off' class='layui-input'></div>
                    <div class='layui-col-xs1 text-r fsize-14 fcolor-65 lh-35'>
                    </div>
                </div>
            </div>";
        
        $option_list_c ="<input type='radio' name='questions_answer_c' value='A' title='A'>
                            <input type='radio' name='questions_answer_c' value='B' title='B'>";
        
        if (!empty($questions_data)){
            $questions_arr = explode(',', $questions_data['questions_item']);
            $questions_num = count($questions_arr);
            $num_i = 0;
            $nu_arr = array('1'=>A,'2'=>B,'3'=>C,'4'=>D,'5'=>E,'6'=>F,'7'=>G,'8'=>H,'9'=>I,'10'=>J,'11'=>K,'12'=>L,'13'=>M,'14'=>N,'15'=>O);
            if ($questions_data['questions_type'] == 1){
                $que_list_a = '';
                $option_list_a = '';
                $que_list_a ="
                        <input type='hidden' id='num_no_a' name='num_no_a' value='$questions_num' />
                        <div class='layui-col-xs12 mt-10'>
                            <div class='layui-row'>
                                <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>问题选项：</strong></div>
                               <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                                <button type='button' onclick='buttondel();' class='layui-btn layui-btn-primary'>删除选项</button>
                                </div>
                                <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                                <button type='button' onclick='buttonfun();' class='layui-btn pull-right'>增加选项</button>
                                </div>
                            </div>
                        </div>
                        ";
                
                foreach ($questions_arr as &$arr){
                    $num_i++;
                    $questions_v = substr($arr,'2');//内容
                    $nu = $nu_arr[$num_i];//选项名称
                    $que_list_a .="
                        <div class='layui-col-xs12 mt-10' id='a_".$num_i."'>
                            <div class='layui-row'>
                            <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>".$nu."：</strong></div>
                                <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                                <input type='text' name='questions_item_a_$num_i' value='$questions_v' autocomplete='off' class='layui-input'></div>
                            </div>
                        </div>";
                    $is_chk = $questions_data['questions_answer']==$nu?"checked":'';
                    $option_list_a .="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' ><input type='radio' $is_chk name='questions_answer_a' value='$nu' title='$nu'></div>";
                }
            }elseif ($questions_data['questions_type'] == 2){
                $que_list_b = '';
                $option_list_b = '';
                $que_list_b ="
                <input type='hidden' id='num_no_b' name='num_no_b' value='$questions_num' />
                <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>问题选项：</strong></div>
                <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                <button type='button' onclick='buttondel();' class='layui-btn layui-btn-primary'>删除选项</button>
                </div>
                <div class='layui-col-xs3 fsize-14 fcolor-65 lh-35'>
                <button type='button' onclick='buttonfun();' class='layui-btn pull-right'>增加选项</button>
                </div>
                </div>
                </div>
                ";
                
                foreach ($questions_arr as &$arr){
                    $num_i++;
                    $questions_v = substr($arr,'2');//内容
                    $nu = $nu_arr[$num_i];//选项名称
                    $que_list_b .="
                        <div class='layui-col-xs12 mt-10'>
                            <div class='layui-row'>
                            <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>".$nu."：</strong></div>
                            <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                            <input type='text' name='questions_item_b_$num_i' value='$questions_v' autocomplete='off' class='layui-input'></div>
                            </div>
                            </div>";
                    $str_ans = $questions_data['questions_answer'];
                    $is_chk = (strpos($str_ans,$nu) !== false)?"checked='checked'":'';
                    $option_list_b .="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' ><input type='checkbox' $is_chk name='questions_answer_b[]' title='$nu' value='$nu'></div>";
                }
            }elseif ($questions_data['questions_type'] == 3){
                $que_list_c = '';
                $option_list_c = '';
                $que_list_c ="
                <input type='hidden' id='num_no_c' name='num_no_c' value='$questions_num' />
                <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>问题选项：</strong></div>
                </div>
                </div>
                ";
                
                foreach ($questions_arr as &$arr){
                    $num_i++;
                    $questions_v = substr($arr,'2');//内容
                    $nu = $nu_arr[$num_i];//选项名称
                    $que_list_c .="
                        <div class='layui-col-xs12 mt-10'>
                            <div class='layui-row'>
                            <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>".$nu."：</strong></div>
                            <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                            <input type='text' name='questions_item_c_$num_i' value='$questions_v' autocomplete='off' class='layui-input'></div>
                            </div>
                            </div>";
                    $is_chk = $questions_data['questions_answer']==$nu?"checked":'';
                    $option_list_c .="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' ><input type='radio' $is_chk name='questions_answer_c' value='$nu' title='$nu'></div>";
                }
            }
        }else {
            $questions_data = array('questions_type'=>1);
        }
        // dump($questions_data);die;
        $this->assign("option_list_a",$option_list_a);
        $this->assign("option_list_b",$option_list_b);
        $this->assign("option_list_c",$option_list_c);
        $this->assign("que_list_a",$que_list_a);
        $this->assign("que_list_b",$que_list_b);
        $this->assign("que_list_c",$que_list_c);
        $this->assign("questions",$questions_data);
        // $this->display("Eduexam/edu_questions_edit");
		$this->display("Lifesurvey/life_survey_questions_edit");

	}
	/**
	 * @name  life_survey_questions_do_save()
	 * @desc  新增/编辑考试试题保存方法
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_survey_questions_save()
	{
		$exam_data = I("post.");
		// dump($data);die;
		// /* $db_questions = new LifeSurveyQuestionsModel;
		// $result = $db_questions->updateData($data,'questions_id'); */
		// $db_questions = M('life_survey_questions');
		// $data['add_staff'] = session('staff_no');
		// $data['add_time'] = date('Y-m-d H:i:s');
		// $data['update_time'] = date('Y-m-d H:i:s');
		// if (empty($data['questions_id'])){
		//       $result = $db_questions->add($data);
		// }else {
		//     $result = $db_questions->save($data);
		// }
		// if ($result) {
		// 	showMsg("success", "操作成功！！",'',"1");
		// } else {
		// 	showMsg("error", "操作失败！！");
		// }
		if (!empty($exam_data)){
            $questions_item='';
            if ($exam_data['questions_type'] == 1){
                for ($i=1;$i<=$exam_data['num_no_a'];$i++){
                    $nu_arr = array('1'=>A,'2'=>B,'3'=>C,'4'=>D,'5'=>E,'6'=>F,'7'=>G,'8'=>H,'9'=>I,'10'=>J,'11'=>K,'12'=>L,'13'=>M,'14'=>N,'15'=>O);
                    $nu = $nu_arr[$i];
                    $v = $exam_data['questions_item_a_'.$i];
                    if ($i ==1){
                        $questions_item .= $nu.':'.$v;
                    }else {
                        $questions_item .= ','.$nu.':'.$v;
                    }
                    
                }
                $questions_answer = $exam_data['questions_answer_a'];
            }else if ($exam_data['questions_type'] == 2){
                for ($i=1;$i<=$exam_data['num_no_b'];$i++){
                    $nu_arr = array('1'=>A,'2'=>B,'3'=>C,'4'=>D,'5'=>E,'6'=>F,'7'=>G,'8'=>H,'9'=>I,'10'=>J,'11'=>K,'12'=>L,'13'=>M,'14'=>N,'15'=>O);
                    $nu = $nu_arr[$i];
                    $v = $exam_data['questions_item_b_'.$i];
                    if ($i ==1){
                        $questions_item .= $nu.':'.$v;
                    }else {
                        $questions_item .= ','.$nu.':'.$v;
                    }
                    
                }
                $questions_answer = implode(',',$exam_data['questions_answer_b']);
            }else if ($exam_data['questions_type'] == 3){
                for ($i=1;$i<=$exam_data['num_no_c'];$i++){
                    $nu_arr = array('1'=>A,'2'=>B,'3'=>C,'4'=>D,'5'=>E,'6'=>F,'7'=>G,'8'=>H,'9'=>I,'10'=>J,'11'=>K,'12'=>L,'13'=>M,'14'=>N,'15'=>O);
                    $nu = $nu_arr[$i];
                    $v = $exam_data['questions_item_c_'.$i];
                    if ($i ==1){
                        $questions_item .= $nu.':'.$v;
                    }else {
                        $questions_item .= ','.$nu.':'.$v;
                    }
                    
                }
                $questions_answer = $exam_data['questions_answer_c'];
            }
            // if (empty($questions_answer)){
            //     showMsg("error", "请选择答案！");
            // }
            $exam_data['questions_item'] =$questions_item;
            $exam_data['questions_answer'] =$questions_answer;
        }else {
            $exam_data['questions_id'] = I("get.questions_id");
            $exam_data['status'] = I("get.status");
        }
        // dump($exam_data);die;
        $db_questions = M('life_survey_questions');//new EduQuestionsModel();
        $exam_data['add_time'] = date('Y-m-d H:i:s');
    	$exam_data['add_staff'] = session('staff_no');
    	$exam_data['update_time'] = date('Y-m-d H:i:s');
        if (empty($exam_data['questions_id'])){
            $result  = $db_questions->add($exam_data);
        }else {
        	$exam_data['update_time'] = date('Y-m-d H:i:s');
            $result  = $db_questions->save($exam_data);
        }
         // dump(M()->getLastSql());die;
        // dump($result);die;
        if ($result) {
            // showMsg("success", "操作成功！！", U('life_survey_result_info',array('survey_id'=>$exam_data['survey_id'])));
            showMsg("success", "操作成功！！", U('life_survey_info?survey_id='.$exam_data['survey_id']));
        } else {
            showMsg("error", "操作失败！！");
        }
  //       if ($result) {
		// 	showMsg("success", "操作成功！！",'',"1");
		// } else {
		// 	showMsg("error", "操作失败！！");
		// }

	
	}
	/**
	 * @name  life_survey_questions_del()
	 * @desc  问卷题目删除
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-05-08
	 */
	public function life_survey_questions_del()
	{
		$questions_id = I("get.questions_id");
		$survey_id = I("get.survey_id");
		$db_survey_questions = M('life_survey_questions');
		$questions_map['questions_id'] = $questions_id;
		$survey_data = $db_survey_questions->where($questions_map)->delete();
		
		
		$url = U('life_survey_info',array("survey_id"=>$survey_id));
		if ($survey_data) {
			showMsg("success", "操作成功！！", $url);
		} else {
			showMsg("error", "操作失败！！",$url);
		}
	
	}
    /**
     * @name  life_survey_result_info()
     * @desc  党员问卷详情
     * @param
     * @return
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-11-20
     */
    public function life_survey_result_info()
    {
        $communist_no = I("get.communist_no");
        $survey_id = I("get.survey_id");
        $db_answer = M('life_survey_answer');
        $communist_info = getCommunistInfo($communist_no,'communist_name,party_no');
        $communist_info['party_name'] = getPartyInfo($communist_info['party_no']);
        $survey_data = getSurveyInfo($survey_id);
        foreach ($survey_data['questions_list'] as &$list){
        	$answer_map['communist_no'] = $communist_no;
        	$answer_map['questions_id'] = $list['questions_id'];
            $list['item'] = $db_answer->where($answer_map)->getField('answer_item');
        }
        $this->assign('survey',$survey_data);
        $this->assign('communist_info',$communist_info);
        $this->display("Lifesurvey/life_survey_result_info");
    }
}
