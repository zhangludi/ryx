<?php
/*******************************************考试中心、试题库***************************************** */
namespace Edu\Controller;

use Edu\Model\EduQuestionsModel;
use Common\Controller\BaseController;
use \Edu\Model\EduExamModel;

class EduexamController extends BaseController{
	/**
	 * @name  edu_exam_index()
	 * @desc  考试管理首页
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime  
	 * @addtime   2017-05-08
	 */
	public function edu_exam_index(){
		checkAuth(ACTION_NAME);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display("Eduexam/edu_exam_index");
	}
	/**
	 * @name  edu_exam_table_data()
	 * @desc  试卷数据页面
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-17
	 */
	public function edu_exam_index_data(){
		$db_edunotes=M('edu_exam');
        $page = I('get.page');
        $pagesize = I('get.limit');
        //$notes_type = I('get.notes_type');
        $page = ($page-1)*$pagesize;
        
        
        $keyword = I('get.keyword');
        $str = I('get.start');
        $strt = strToArr($str, ' - ');  //分割时间
        $start = $strt[0];
        $end = $strt[1];
        if(!empty($start) && !empty($end)){
            $start=$start." 00:00:00";
            $end=$end." 23:59:59";
            $status_map['exam_date']  = array('between',array($start,$end));
        }
        $exam_data['data'] = $db_edunotes->where($status_map)->order('add_time desc')->limit($page,$pagesize)->select();
		$exam_data['count'] = $db_edunotes->where($status_map)->count();
		// if(!empty($keyword)){
        //     $status_map['notes_title']  = array('like','%'.$keyword.'%');
        // }
        // if(!empty($notes_type)){
        //     $notes_map['notes_type'] = $notes_type;
        // }
        $confirm = 'onclick="if(!confirm(' . "'确认开始本次考试？'" . ')){return false;}"';
		$confirms = 'onclick="if(!confirm(' . "'确认结束本次考试？'" . ')){return false;}"';
		$map['status_group'] = 'exam_status';
		$status_name_arr = M('bd_status')->where($map)->getField('status_no,status_name');
		$status_color_arr = M('bd_status')->where($map)->getField('status_no,status_color');
        $communist_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        if(!empty($exam_data['data'] && $exam_data['data'] != 'null')){
            foreach ($exam_data['data'] as &$exam) {
                //$exam['add_staff'] = $communist_name_arr[$exam['add_staff']];
                $exam['add_time']=getFormatDate($exam['add_time'], "Y-m-d");
                $exam_status = $exam['status'];
				$exam['status'] =  "<font color='" . $status_color_arr[$exam_status] . "'>" . $status_name_arr[$exam_status] . "</font> ";
				$operate = "<a onclick='info(".$exam['exam_id'].")' class='layui-btn layui-btn-primary layui-btn-xs'  target='_self'><i class='fa fa-info'></i> 查看</a> ";
				if($exam_status == "11"){
					$operate .= "<a onclick='edit(".$exam['exam_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60'  target='_self'><i class='fa fa-info'></i>编辑</a>&nbsp;&nbsp;&nbsp;<a class='btn red btn-xs btn-outline' href='" . U('edu_exam_save',array('exam_id'=>$exam['exam_id'],'status'=>"21")) . "' target='_self' $confirm><i class='fa fa-edit'></i>开始</a> ";
				}elseif($exam_status == '21'){
					$operate .= "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('edu_exam_save',array('exam_id'=>$exam['exam_id'],'status'=>"31")) . "' target='_self' $confirms><i class='fa fa-edit'></i>结束</a> ";
				}
				$logo = getUploadInfo($exam['exam_thumb']);
				if(empty($logo)){
					$exam['exam_thumb'] ='无';
				}else{
					// 学习缩略图
					$exam['exam_thumb'] = "<img src='$logo' width='50'>";
				}
				$exam['operate'] = $operate;
			}
            $exam_data['code'] = 0;
            $exam_data['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($exam_data);
            
        } else {
            $exam_data['code'] = 0;
            $exam_data['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($exam_data);
        }
	}
    /**
     * @name  edu_exam_edit()
     * @desc  新增/编辑试卷
     * @author 王桥元
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-05-08
     */
    public function edu_exam_edit(){
    	checkAuth(ACTION_NAME);
        $exam_id = I('get.exam_id');
        if (!empty($exam_id)){
            $exam_data = getExamInfo($exam_id);
            $this->assign('exam_data',$exam_data);
            if(!empty($exam_data['exam_topic'])){
            	$this->assign('topic_id',$exam_data['exam_topic']);
            }
        }
        $topic_data = getTopicList(1);
		$this->assign("topic_data", $topic_data);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
        $this->display("Eduexam/edu_exam_edit");
    }
    /**
     * @name  edu_exam_save()
     * @desc  新增/编辑试卷保存方法
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-10-27
     */
    public function edu_exam_save(){
    	$post = $_POST;
        if(empty(I('get.'))){
            $exam_data = I('post.');
            $db_exam = new EduExamModel;
	        $result = $db_exam->updateData($exam_data,'exam_id');
	        if ($result) {
	            showMsg("success", "操作成功！！", U('edu_exam_index'),1);
	        } else {
	            showMsg("error", "操作失败！");
	        }
        }else{
            $exam_data = I('get.');
            if($exam_data['status'] == '21'){
        		$res = getExamInfo($exam_data['exam_id'],'exam_questions');
        		if(empty($res)){
        			showMsg("error", "本次考试没有试题");
        		}
       		}
            $db_exam = new EduExamModel;
	        $result = $db_exam->updateData($exam_data,'exam_id');
	        if ($result) {
	            showMsg("success", "操作成功！！", U('edu_exam_index'));
	        } else {
	            showMsg("error", "操作失败！");
	        }
	    }

    }
	/**
	 * @name  edu_exam_info()
	 * @desc  试题详情
	 * @param 
	 * @return
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function edu_exam_info(){
		checkAuth(ACTION_NAME);
        $db_party = M('ccp_party');
		$exam_id = I("get.exam_id");
		$exam_data = getExamInfo($exam_id);
		$exam_data['exam_status'] = getStatusName("exam_status", $exam_data['status']);
		$exam_data['exam_party'] = getPartyInfo($exam_data['exam_party']);
		$exam_data['exam_group'] = getGroupInfo($exam_data['exam_group']);
		$exam_data['exam_data_r'] = getGroupInfo($exam_data['exam_data_r']);
		$exam_data['exam_topic'] = M("edu_topic")->where("topic_id = ".$exam_data['exam_topic'])->getField("topic_title");
		if($exam_data['is_simulation']==1){
			$exam_data['is_simulation'] = '模拟试题';
		}else{
			$exam_data['is_simulation'] = '正式试题';
		}
		$this->assign("exam",$exam_data);
		$this->assign("exam_id",$exam_id);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display("Eduexam/edu_exam_info");
	}


	/**
	 * @name  edu_exam_questions()
	 * @desc  试卷选择试题
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function edu_exam_questions(){
		$exam_id = I('get.exam_id');
		$this->assign('exam_id',$exam_id);
		$this->display("Eduexam/edu_exam_questions");
	}
	/**
	 * @name  edu_exam_questions_data()
	 * @desc  题数据页面
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function edu_exam_questions_data(){
        $page = I('get.offset');
        $pagesize = I('get.pageSize');
        $search = I('get.search');
		$exam_id = I('get.exam_id');
        $is_contain = I('get.is_contain');
		$questions = getExamInfo($exam_id,'exam_questions');
		if(!empty($questions)){
			$exam_map['questions_id'] = $is_contain == '1'? array('in',$questions) : array('not in',$questions);
		}else{
		    $exam_map['questions_id'] = $is_contain == '1'? array('in','0') : array('not in','0');
        }
		$questions_list['data'] = getQuestionsList($exam_map,'1',$search,$page,$pagesize);
		$num = 1;
        $confirm = 'onclick="if(!confirm(' . "'确认要删除么？'" . ')){return false;}"';
        $type_map['type_group'] = 'questions_type';
        $type_name_arr = M('bd_type')->where($type_map)->getField('type_no,type_name');
        if(!empty($questions_list['data'] && $questions_list['data'] != 'null')){
		    foreach ($questions_list['data'] as &$exam){
		        $exam['num'] = $num;
		        $num++;
		        $exam['questions_type'] = $type_name_arr[$exam['questions_type']];
		        $exam['operate'] = "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('Edu/Eduexam/edu_exam_questions_del',array('questions_id'=>$exam['questions_id'],'exam_id'=>$exam_id)) . "' $confirm><i class='fa fa-edit'></i>删除</a> ";
		    }
		    $questions_list['code'] = 0;
            $questions_list['msg'] = '获取数据成功';
			ob_clean();$this->ajaxReturn($questions_list);
		}else{
			$questions_list['code'] = 0;
            $questions_list['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($questions_list);
		}
	}

	/**
	 * @name  edu_exam_import_index
	 * @desc  导入试题弹窗
	 * @author 曾宪坤
	 * @version 版本 V1.0.0
	 * @addtime   2019-2-18
	 */
	public function questions_import_index(){
		$type = I('get.type');
		if(!empty($type)){
			$this->assign('type',$type);
		}else{
			$this->assign('type','0');
		}

		//总数据、成功数据、错误数据的数量
		//$import_count = M('edu_questions_import')->where("type=1")->count();
		$this->assign('success_count',I("get.success_count"));
		$this->assign('error_count',I("get.error_count"));
		$this->assign('data_count',I("get.data_count"));

		$this->display('questions_import_index');
	}

	/**
	 * @name:questions_data_download()
	 * @desc：试题导入-下载基础数据
	 * @author：曾宪坤
	 * @addtime:2019-02-18
	 * @version：V1.0.0
	 **/
	public function questions_import_download(){
		/* $communist_nation = M('bd_code')->where("code_group = 'communist_nation_code'")->field("code_no,code_name")->select();
		$diploma_level = M('bd_code')->where("code_group = 'diploma_level_code'")->field("code_no,code_name")->select();
		$data = array_merge($communist_nation,$diploma_level);
		$head['code_no']='编号';
		$head['code_name']='名称';
		exportExcel("基础数据",$head,$data); */
    }

	/**
	 * @name:questions_import_save()
	 * @desc：保存导入的试题
	 * @author：曾宪坤
	 * @addtime:2019-02-18
	 * @version：V1.0.0
	 **/
	public function questions_import_save(){
		$type = I('post.type');
		$db_party = M('edu_questions');
		$file = $_FILES['file_stu'];
        if (!empty($file['tmp_name'])){
			$upload = new \Think\Upload();
			$upload->maxSize = 0; // 不限制附件上传大小
			$upload->exts = array('xls','xlsx'); //
			$upload->rootPath = C('TMPL_PARSE_STRING')['__UPLOAD_PATH__']; // 上传文件所在文件夹
			$upload->savePath = 'edu/exam/xls/'; // 设置附件上传目录
			$upload->autoSub = true; // 开启子目录保存
			$info = $upload->upload();
			if (!$info){
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
			$db_import = M('edu_questions_import');
			$m = 0;
			for($i=2;$i<=$highestRow;$i++) {
				if(!empty($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
					$data['questions_title'] = (string)$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();//题干
					$data['questions_item'] = (string)$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();//选项列表
					$data['questions_answer'] = (string)$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();//正确答案
					$data['questions_score'] = (string)$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();//分值
					$data['questions_type'] = (string)$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();//题目类型
					$data['memo'] = (string)$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();//备注
					$data['add_communist'] = session('staff_no');
					$data['add_time'] = date('Y-m-d H:i:s');

                    $communist[]=$data;
                }
            }
            $flag = $db_import->addAll($communist);
            if($flag){
				showMsg(success, '上传成功', U('questions_import_index',array('type'=>$type)));
			}else{
				showMsg(error, '导入上传失败');
            }
		}else{
			showMsg(error, '上传的文件为空，请从新上传数据');
		}
	}

	/**
	 * @name:questions_import_check()
	 * @desc：检查导入的试题，并导入到sp_edu_questions库里
	 * @author：曾宪坤
	 * @addtime:2019-02-18
	 * @version：V1.0.0
	 **/
	public function questions_import_check(){
		$type = I('get.type');
		$success_count=0;
		$error_count=0;
		$data_count=0;
		$db_import = M('edu_questions_import');
		$db_questions = M('edu_questions');
		$import_data = $db_import->where("type=0")->select();

		foreach ($import_data as $value){
			$questions_map["questions_id"]=$value['questions_id'];

			$data['add_communist'] = session('staff_no');
			$data['questions_title'] =$value['questions_title'];//题干
			$data['questions_item'] = $value['questions_item'];//选项列表
			$data['questions_answer'] = $value['questions_answer'];//正确答案
			$data['questions_score'] = $value['questions_score'];//分值
			$data['questions_type'] = $value['questions_type'];//题目类型
			$data['memo'] = $value['memo'];//备注
			$data['add_time'] = date('Y-m-d H:i:s');

 			//如果格式正确
			if($this->check_questions($value)){
				$success_count++;
				$flag_add = $db_questions->add($data);
				$flag_delete=$db_import->where($questions_map)->delete();
			}else{
			//如果格式错误
				$error_count++;
				$value["type"]=1;
				$flag_change=$db_import->where($questions_map)->save($value);
			}
			$data_count++;
		}
		if($data_count==0){
			showMsg(error,'上传的文件为空，请从新上传数据！');
		}
		showMsg(success, '导入成功', U('questions_import_index',array('type'=>$type,'data_count'=>$data_count,'success_count'=>$success_count,'error_count'=>$error_count)));
	}

	/**
	 * @name:check_questions()
	 * @desc：检查题目的格式是否正确
	 * @author：曾宪坤
	 * @addtime:2019-02-21
	 * @version：V1.0.0
	 **/
	public function check_questions($question){
		foreach($questions as $key=>$value){
			if($key!="memo"){
				if(empty(trim($value))){
					return false;
				}
			}
			if($key=="memo"){
				continue;
			}
		}
		$item=$question["questions_item"];//选项
		$type=$question["questions_type"];//题目类型
		$score=$question["questions_score"];//分值
		$answer=$question["questions_answer"];//答案

		$item_list=explode(",",$item);
		$anwser_list=explode(",",$answer);

		$item_count=count($item_list);//选项数量
		$answer_count=count($anwser_list);//答案数量

		if($answer_count<1 || $item_count<1){
			return false;
		}
 		//如果是判断题：答案数量不为1，或者选项数量不为2，报错
		if($type==3){
			if($answer_count!=1 || $item_count!=2){
				return false;
			}
		}
		//单选题和多选题，最多只能有五个选项
		if($item_count>5){
			return false;
		}
		//如果是单选题
		if($type==1){
			if($answer_count!=1){
				return false;
			}
		}
		//如果是多选题
		if($type==2){
			if($answer_count>5){
				return false;
			}
		}
		//题目类型：1,2,3
		if(!in_array($type,array(1,2,3))){
			return false;
		}
		//分值必须是数字
		if(!is_numeric($score)){
			return false;
		}
		//答案正则
		$item_tpl='/^[A-E]{1}\:.{1,}$/';
		$answer_tpl='/^[A-E]{1}$/';
 		foreach($item_list as $item){
			if(!preg_match($item_tpl,$item)){
				return false;
				break;
			}
		}
		foreach($answer_list as $answer){
			if(!preg_match($answer_tpl,$answer)){
				return false;
				break;
			}
		}
		return true;
	}
	
	/**
	 * @name:questions_export_error()
	 * @desc：导出格式错误的数据
	 * @author：曾宪坤
	 * @addtime:2019-02-18
	 * @version：V1.0.0
	 **/
	public function questions_export_error(){
		$db_import = M('edu_questions_import');
		$import_data = $db_import->where("type=1")->field('questions_title,questions_item,questions_answer,questions_score,questions_type,memo')->select();
		$db_import->where("type=1")->delete();

		$head['questions_title'] = "试题名称";
		$head['questions_item'] = "选项列表";
		$head['questions_answer'] = "正确答案";
		$head['questions_score'] = "分值";
		$head['questions_type'] = "题目类型";
		$head['memo'] = "备注";

		exportExcel("格式错误试题",$head,$import_data);
	}

	/**
	 * @name  edu_exam_questions_save()
	 * @desc  试卷选择试题
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function edu_exam_questions_save(){
		$data['exam_id'] = I('post.exam_id');
		$exam_questions = getExamInfo($data['exam_id'],'exam_questions');
		$questions = I('post.questions');
		$questions = substr($questions,0,-1);
		if (!empty($exam_questions)){
			$questions = $exam_questions.','.$questions;
		}
		$data['exam_questions'] = $questions;
		$data['exam_score'] = 0;
		if(!empty($data['exam_id'])){
			$exam_questions = strToArr($questions, ',');
			$questions_score_arr = M('edu_questions')->getField('questions_id,questions_score');
			foreach ($exam_questions as $vo) {
			    $data['exam_score'] += $questions_score_arr[$vo['exam_id']];
			}
		}
		$db_exam = new EduExamModel;
		$result = $db_exam->updateData($data,'exam_id');
		if ($result) {
			showMsg("success", "操作成功！！", '',1);
		} else {
			showMsg("error", "操作失败！！");
		}
	}
	/**
	 * @name  edu_exam_questions_del()
	 * @desc  删除
	 * @author 刘丙涛  杨凯 2018.1.15删除减去试卷总分
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function edu_exam_questions_del(){
		$data = I('get.');
		$data['exam_id'] = I('get.exam_id');
		$status = getExamInfo($data['exam_id'],'status');
		if ($status != '11'){
		    showMsg('error','试卷正在考试或已结束，无法删除题目');
        }
		$questions_id = I('get.questions_id');
		$exam_questions = getExamInfo($data['exam_id'],'exam_questions');
		$exam_questions = explode(',',$exam_questions);
		$exam_questions = array_merge(array_diff($exam_questions, array($questions_id)));
		$data['exam_questions'] = implode(',',$exam_questions);
		$questions = strToArr($data['exam_questions'], ',');
		$questions_score_arr = M('edu_questions')->getField('questions_id,questions_score');
		foreach ($questions as $vo) {
		    $data['exam_score'] += $questions_score_arr[$vo['exam_id']];
		}
		
		$db_exam = new EduExamModel;
		$result = $db_exam->updateData($data,'exam_id');
		if ($result) {
			showMsg("success", "操作成功！！", U('edu_exam_info',array('exam_id'=>$data['exam_id'])));
		} else {
			showMsg("error", "操作失败！！");
		}
	}
    /**
     * @name  edu_exam_communist_data()
     * @desc  考试人员列表
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-11-10
     */
    public function edu_exam_communist_data(){
        $page = I('get.offset');
        $pagesize = I('get.pagesize');
        $exam_id = I('get.exam_id');
        $db_log = M('edu_exam_log');
        $log_map['exam_id'] = $exam_id;
        $count = $db_log->where($log_map)->count();
        $communist_list['data'] = $db_log->where($log_map)->limit($page,$pagesize)->select();
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $party_no_arr = M('ccp_communist')->getField('communist_no,party_no');
        if(!empty($communist_list['data'] && $communist_list['data'] != 'null')){
		    foreach ($communist_list['data'] as &$list){
		        $list['communist_name'] = $communist_name_arr[$list['communist_no']];
		        $list['party_name'] = $party_name_arr[$party_no_arr[$list['communist_no']]];
		        
		        $list['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='".U('Edu/Eduexam/edu_exam_communist_info',array('exam_id'=>$exam_id,'communist_no'=>$list['communist_no']))."'><i class=\"fa fa-info\"></i> 详情  </a>";
		    }
		    $communist_list['code'] = 0;
            $communist_list['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($communist_list);
		}else{
			$communist_list['code'] = 0;
            $communist_list['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($communist_list);
		}
    }
	/**
	 * @name  edu_exam_communist_info()
	 * @desc  考试人试卷详情
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-30
	 */
	public function edu_exam_communist_info(){
	    $communist_no = I('get.communist_no');
	    $exam_id = I('get.exam_id');
	    $data = getExamCommunistInfo($exam_id,$communist_no);
	    $this->assign('data',$data);
	    $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display("Eduexam/edu_exam_communist_info");
	}



    /**
     * @name  edu_questions_index()
     * @desc  题库首页
     * @param
     * @return
     * @author 杨凯
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-10-17
     */
    public function edu_questions_index(){
        checkAuth(ACTION_NAME);
        $this->display("Eduexam/edu_questions_index");
    }
    /**
     * @name  edu_questions_data()
     * @desc  题库数据页面
     * @return
     * @author 杨凯
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-10-17
     */
    public function edu_questions_data(){
    	$db_edunotes=M('edu_questions');
        $pagesize = I('get.limit');
        $page = I('get.page');
        $keyword = I('get.keyword');
        $questions_type = I('get.questions_type');
        if(!empty($questions_type)){
        	$questions_map['questions_type']  = $questions_type;
        }
        if(!empty($keyword)){
        	$questions_map['questions_title']  = array('like','%'.$keyword.'%');
        }
        //$notes_type = I('get.notes_type');
        $page = ($page-1)*$pagesize;
        $exam_data['data'] = $db_edunotes->where($questions_map)->order('status desc')->limit($page,$pagesize)->order('add_time desc')->select();
        $exam_data['count'] = $db_edunotes->where($questions_map)->count();
        //$exam_data = getQuestionsList('','',$search,$page,$pagesize);
        $confirm = 'onclick="if(!confirm(' . "'确认停用此试题？'" . ')){return false;}"';
        $type_map['type_group'] = 'topic_type';
        $type_name_arr = M('bd_type')->where($type_map)->getField('type_no,type_name');
        if(!empty($exam_data['data'] && $exam_data['data'] != 'null')){
	        foreach ($exam_data['data'] as &$exam){
	        	$exam['add_time'] = date( 'Y-m-d ',strtotime($exam['add_time']));
	            $exam_status = $exam['status'];
	            $exam['questions_type'] = $type_name_arr[$exam['questions_type']];
	            $operate = "<a onclick='edit(".$exam['questions_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60'  target='_self'><i class='fa fa-info'></i>编辑</a>&nbsp; ";
	            if($exam_status == "1"){
	                $operate .= " <a class='btn red btn-xs btn-outline' href='" . U('edu_questions_save',array('questions_id'=>$exam['questions_id'],'status'=>"0")) . "' target='_self' $confirm><i class='fa fa-edit'></i>停用</a> ";
	            }else {
	                $operate .= " <a class='btn yellow btn-xs btn-outline' href='" . U('edu_questions_save',array('questions_id'=>$exam['questions_id'],'status'=>"1")) . "' target='_self' $confirm><i class='fa fa-edit'></i>启用</a> ";
	            }
	            $exam['operate'] = $operate;
	        }
	        $exam_data['code'] = 0;
            $exam_data['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($exam_data);
	    }else{
	    	$exam_data['code'] = 0;
            $exam_data['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($exam_data);
	    }
    }
    /**
     * @name  edu_questions_edit()
     * @desc  新增/编辑考试试题
     * @param
     * @return
     * @author 杨凯
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-10-17
     */
    public function edu_questions_edit(){
//        checkAuth(ACTION_NAME);
       /*  $exam_get = I("get.");
        $questions_data = getQuestionsInfo($exam_get['questions_id']);
        $this->assign("questions_data",$questions_data); */
        $exam_get = I("get.");
        $questions_data = getQuestionsInfo($exam_get['questions_id']);
        
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
                    $option_list_a .="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' id='a_q_".$num_i."'><input type='radio' $is_chk name='questions_answer_a' value='$nu' title='$nu'></div>";
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
                        <div class='layui-col-xs12 mt-10' id='b_".$num_i."'>
                            <div class='layui-row'>
                            <div class='layui-col-xs2 text-r fsize-14 fcolor-65 lh-35'><strong>".$nu."：</strong></div>
                            <div class='layui-col-xs8 fsize-14 fcolor-65 lh-35'>
                            <input type='text' name='questions_item_b_$num_i' value='$questions_v' autocomplete='off' class='layui-input'></div>
                            </div>
                            </div>";
                    $str_ans = $questions_data['questions_answer'];
                    $is_chk = (strpos($str_ans,$nu) !== false)?"checked='checked'":'';
                    $option_list_b .="<div class='layui-col-xs2 fsize-14 fcolor-65 lh-35' id='b_q_".$num_i."'><input type='checkbox' $is_chk name='questions_answer_b[]' title='$nu' value='$nu'></div>";
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
                        <div class='layui-col-xs12 mt-10' id='c_".$num_i."'>
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
        $this->assign("option_list_a",$option_list_a);
        $this->assign("option_list_b",$option_list_b);
        $this->assign("option_list_c",$option_list_c);
        $this->assign("que_list_a",$que_list_a);
        $this->assign("que_list_b",$que_list_b);
        $this->assign("que_list_c",$que_list_c);
        $this->assign("questions",$questions_data);
        $this->display("Eduexam/edu_questions_edit");
    }


    /**
     * @name  edu_questions_save()
     * @desc  新增/编辑考试试题保存方法
     * @param
     * @return
     * @author 杨凯
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-10-23
     */
    public function edu_questions_save(){
		$db_questions = M('edu_questions');//new EduQuestionsModel();
        $exam_data = I("post.");
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
            if (empty($questions_answer)){
                showMsg("error", "请选择答案！");
            }
        	

            $exam_data['questions_item'] =$questions_item;
            $exam_data['questions_answer'] =$questions_answer;
            $exam_data['update_time'] = date('Y-m-d H:i:s');
            if (empty($exam_data['questions_id'])){
				$exam_data['add_time'] = date('Y-m-d H:i:s');
				$exam_data['add_communist'] = session('staff_no');
	            $result  = $db_questions->add($exam_data);
	        }else {	          
	            $result  = $db_questions->save($exam_data);
	        }
	        if ($result) {
	            showMsg("success", "操作成功！！", U('edu_questions_index'),1);
	        } else {
	            showMsg("error", "操作失败！！");
	        }
        }else {
            $exam_data['questions_id'] = I("get.questions_id");
            $exam_data['status'] = I("get.status");
        	$db_questions = M('edu_questions');//new EduQuestionsModel();
            $result  = $db_questions->save($exam_data);
            if ($result) {
	            showMsg("success", "操作成功！！", U('edu_questions_index'));
	        } else {
	            showMsg("error", "操作失败！！");
	        }
        }
    }
	/* ******************************************党建考试中心***************************************************** */
	
}
