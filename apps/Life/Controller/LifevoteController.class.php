<?php
namespace Life\Controller;
 // 命名空间
use Common\Controller\BaseController;

class LifevoteController extends BaseController // 继承Controller类
{
	/**
	 * @name:life_vote_subject_index
	 * @desc：投票列表
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_vote_subject_index(){
		checkAuth(ACTION_NAME);
		$this->display("Lifevote/life_vote_subject_index");
	}
	/**
	 * @name:life_vote_subject_data
	 * @desc：投票列表数据
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_vote_subject_data(){
		$vote_subject = M('life_vote_subject');
		$subject_list['data'] = $vote_subject->order('add_time desc')->select();
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$num = 1;
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($subject_list['data'] as &$subject) {

			$subject['subject_starttime'] = getFormatDate($subject['subject_starttime'], 'Y-m-d H:i');
			$subject['subject_endtime'] = getFormatDate($subject['subject_endtime'], 'Y-m-d H:i');
			
			$subject['num'] = $num ++;
			$subject['add_staff'] = $staff_name_arr[$subject['add_staff']];
			$subject_endtime = $subject['subject_endtime'];
			$nowdate = date('Y-m-d H:i:s');
			if ($subject_endtime > $nowdate) {
				$subject['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('Lifevote/life_vote_subject_edit', array(
						'subject_id' => $subject['subject_id']
				)) . "'><i class='fa fa-edit'></i>修改 </a>  " . "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('Lifevote/life_vote_subject_do_del', array(
						'subject_id' => $subject['subject_id']
				)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
			}else{
				$subject['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('Lifevote/life_vote_info', array(
						'subject_id' => $subject['subject_id']
				)) . "'><i class='fa fa-info-circle'></i>查看投票结果</a>  " . "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('Lifevote/life_vote_subject_do_del', array(
						'subject_id' => $subject['subject_id']
				)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
			}
		}
		$subject_list['code'] = 0;
		$subject_list['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($subject_list); // 返回json格式数据
	}
	/**
	 * @name:life_vote_subject_edit
	 * @desc：投票数据添加/编辑
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_vote_subject_edit()
	{
		checkAuth(ACTION_NAME);
		$subject_id = I('get.subject_id');
		// if (! empty($subject_id)) {
		// 	$vote_subject = M('life_vote_subject');
		// 	$subject_map['subject_id'] = $subject_id;
		// 	$subject_data = $vote_subject->where($subject_map)->find();
		// }
		// $this->assign('subject_data', $subject_data);
		// $this->display("Lifevote/life_vote_subject_edit");
		$questions_data = getVoteInfoData($subject_id);
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
                    <input type='text' placeholder='请输入选项' name='questions_item_a_1' value='' autocomplete='off' class='layui-input'></div>
                </div>
            </div>
            <div class='layui-col-xs12 mt-10'>
                <div class='layui-row'>
                    <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>B：</strong></div>
                    <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                    <input type='text' placeholder='请输入选项' name='questions_item_a_2' value='' autocomplete='off' class='layui-input'></div>
                    <div class='layui-col-xs1 text-r fsize-14 fcolor-65 lh-35'>
                    </div>
                </div>
            </div>";
        if (!empty($questions_data)){
            $questions_arr = explode(',', $questions_data['subject_option']);
            $questions_num = count($questions_arr);
            $num_i = 0;
            $nu_arr = array('1'=>A,'2'=>B,'3'=>C,'4'=>D,'5'=>E,'6'=>F,'7'=>G,'8'=>H,'9'=>I,'10'=>J,'11'=>K,'12'=>L,'13'=>M,'14'=>N,'15'=>O);
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
                            <input type='text' placeholder='请输入选项' name='questions_item_a_$num_i' value='$questions_v' autocomplete='off' class='layui-input'></div>
                        </div>
                    </div>";
            }
        }else {
            $questions_data = array('questions_type'=>1);
        }
        $questions_data['questions_type'] = 1;
        $this->assign("option_list_a",$option_list_a);
        $this->assign("que_list_a",$que_list_a);
        $this->assign("questions",$questions_data);
        // dump($option_list_a);die;
        // var_dump($que_list_a);die;
		$this->display("Lifevote/life_vote_subject_edit");

	}
	/**
	 * @name:life_vote_subject_do_save
	 * @desc：投票数据执行保存操作
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_vote_subject_do_save(){
		checkLogin();
        $life_vote_option = M('life_vote_option');
		$exam_data = I('post.');
	    $subject_id = I('post.subject_id');
        if (!empty($subject_id)){
            $subject_map['subject_id'] = $subject_id;
            $vote_flag = $life_vote_option->where($subject_map)->delete();
        }
		if (!empty($exam_data)){
            $questions_item='';
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
            $exam_data['questions_item'] =$questions_item;
            $exam_data['questions_answer'] =$questions_answer;
        }else {
            $exam_data['questions_id'] = I("get.questions_id");
            $exam_data['status'] = I("get.status");
        }
        
        $db_questions = M('life_vote_subject');//new EduQuestionsModel();
        $exam_data['add_time'] = date('Y-m-d H:i:s');
    	$exam_data['add_staff'] = session('staff_no');
    	$exam_data['update_time'] = date('Y-m-d H:i:s');

    	$exam_data['subject_option'] = $exam_data['questions_item'];
        if (empty($subject_id)){
            $result  = $db_questions->add($exam_data);
            $subject_id = $result;
        }else {
        	$exam_data['update_time'] = date('Y-m-d H:i:s');

            $result  = $db_questions->save($exam_data);
        }
        if ($result) {
            $vote_list = explode(',', $exam_data['subject_option']);
            foreach ($vote_list as &$option_name) {
                $option['option_name'] = $option_name;
                $option['subject_id'] = $subject_id;
                $option['add_time'] = date("Y-m-d H:i:s");
                $option['update_time'] = date("Y-m-d H:i:s");
                $option['add_staff'] = session('staff_no');
                $life_vote_option->add($option);
            }
            showMsg("success", "操作成功！！", U('life_vote_subject_index'));
        } else {
            showMsg("error", "操作失败！！");
        }
		
	}
	/**
	 * @name:life_vote_info
	 * @desc：查看投票结果详情
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_vote_info()
	{
		checkAuth(ACTION_NAME);
		$subject_id = I('get.subject_id');
		$life_vote_subject = M('life_vote_subject');
		$life_vote_option = M('life_vote_option');
		$life_vote_result = M('life_vote_result');
		$subject_map['subject_id'] = $subject_id;
		$subject_list = $life_vote_subject->where($subject_map)->field('subject_id,vote_id,subject_content,is_multiple')->select();
		// $subject_list[0]['vote_name'] = $vote_info['vote_theme'];
		foreach ($subject_list as &$subject) {
			$vote_map['subject_id'] = $subject['subject_id'];
			$option_list = $life_vote_option->where($vote_map)->select();
			foreach ($option_list as &$option) {
				$vote_result_map['option_id'] = $option['option_id'];
				$option['result'] = $life_vote_result->where($vote_result_map)->count('option_id');
			}
			$subject['option_list'] = $option_list;
		}
		$this->assign("subject_list", $subject_list);
		$this->display("Lifevote/life_vote_info");
	}
	/**
	 * @name:life_vote_subject_do_del
	 * @desc：投票数据删除
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_vote_subject_do_del()
	{
		//checkAuth(ACTION_NAME);
		$vote_id = I('get.vote_id');
		$subject_id = I('get.subject_id');
		$vote_subject = M('life_vote_subject');
		$life_vote_option = M('life_vote_option');
		$vote_map['subject_id'] = $subject_id;
		$flag = $vote_subject->where($vote_map)->delete();
		$flag = $life_vote_option->where($vote_map)->delete();
		if ($flag) {
			showMsg("success", "删除成功！！", U('Lifevote/life_vote_subject_index'));
		} else {
			showMsg("error", "删除失败！！",U('Lifevote/life_vote_subject_index'));
		}
	}
	
}
