<?php
/*******************************************学习笔记管理***************************************************** */
namespace Edu\Controller;
use Common\Controller\BaseController;
class EdunotesController extends BaseController{
    /**
	* @name  edu_notes_index()
	* @desc  笔记首页
	* @param
	* @return
	* @author 王宗彬
	* @version 版本 V1.0.0
	* @addtime 2017-11-1
	* */
    public function edu_notes_index(){
		$notes_type = $_GET['notes_type'];
		$this->assign('notes_type',$notes_type);
		switch ($notes_type) {
			case 2:$notes_type = "_2";break;
		}
		session('notes_type',$notes_type);
        checkAuth(ACTION_NAME.$notes_type);
        $this->display("Edunotes/edu_notes_index");
    }
    /**
    * @name  edu_notes_index_data()
    * @desc  笔记首页数据加载
    * @param
    * @return
    * @author 王宗彬
    * @version 版本 V1.0.0
    * @addtime 2017-11-1
    * */
    public function edu_notes_index_data(){
        $db_edunotes=M('edu_notes');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $notes_type = I('get.notes_type');
        $page = ($page-1)*$pagesize;
        $keyword = I('get.keyword');
        $str = I('get.start');
        $strt = strToArr($str, ' - ');  //分割时间
        $start = $strt[0];
        $end = $strt[1];
        if(!empty($start) && !empty($end)){
            $start=$start." 00:00:00";
            $end=$end." 23:59:59";
            $notes_map['update_time']  = array('between',array($start,$end));
        }
        if(!empty($keyword)){
            $notes_map['notes_title']  = array('like','%'.$keyword.'%');
        }
        if(!empty($notes_type)){
            $notes_map['notes_type'] = $notes_type;
        }
        $notes_data['data'] = $db_edunotes->where($notes_map)->order('add_time desc')->limit($page,$pagesize)->select();
        $notes_data['count'] = $db_edunotes->where($notes_map)->count();
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $firm = 'onclick="if(!confirm(' . "'确认进行此操作？'" . ')){return false;}"';
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        if(!empty($notes_data['data'] && $notes_data['data'] != 'null')){
            foreach ($notes_data['data'] as &$material) {
                $material['cat_name'] = getArticleCatInfo($material['material_cat']);
                $material['add_staff'] = $communist_name_arr[$material['add_staff']];
                $material['add_time']=getFormatDate($material['add_time'], "Y-m-d");
                $material['notes_type']=getBdTypeInfo($material['notes_type'],'notes_type');
                if(!empty($material['material_content'])){
                    $material['material_content'] = mb_substr(strip_tags($material['material_content']), 0, 10, 'utf-8');
                }
            }
            $notes_data['code'] = 0;
            $notes_data['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($notes_data);
        } else {
            $notes_data['code'] = 0;
            $notes_data['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($notes_data);
        }
    }
    /**
     * @name  edu_notes_edit()
     * @desc  笔记数据修改/添加
     * @param
     * @return
     * @author 王宗彬   杨凯  
     * @version 版本 V1.0.0
     * @addtime 2017-11-1
     *  @updatetime 2017-12-20  增加笔记所属专题
     * */
    public function edu_notes_edit(){
        checkAuth(ACTION_NAME.session('notes_type'));
		$notes_type = I('get.notes_type');
		$this->assign('notes_type', $notes_type);
        $notes_id = I('get.notes_id'); // I方法获取数据
        $db_notes = M('edu_notes');
        $db_alertmsg = M('bd_alertmsg');
        if ($notes_id) {
            $notes_info = getNotesInfo($notes_id, 'all');
            if ($notes_info['alert_time'] != null) {
                $notes_info['is_alert'] = 1;
                $notes_info['alert_time'] = getFormatDate($notes_info['alert_time'], "Y-m-d H:i");
                $notes_info['notes_thumb'] = getUploadInfo($notes_info['notes_thumb']);
            }
            $this->assign('notes_info', $notes_info);
        }
		
		//对应群体、资料标签
		$notes_group=M("bd_code")->where("code_group='notes_group_code'")->field("code_no,code_name")->select();
		$notes_label=M("bd_code")->where("code_group='notes_label_code'")->field("code_no,code_name")->select();
		$this->assign("notes_group",$notes_group);
		$this->assign("notes_label",$notes_label);

        $topic_data = getTopicList(1);
        $this->assign("topic_data", $topic_data);
        $this->display("Edunotes/edu_notes_edit");
    }
    /**
     * @name  edu_notes_save()
     * @desc  笔记数据保存
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @addtime 2017-11-1
     * */
    public function edu_notes_save(){
        checkLogin();
        $post = $_POST;
        $staff_no = session('staff_no');
        $notes = M('edu_notes');
        $alert = M('bd_alertmsg');
        $notes_id = I('post.notes_id');
        if (!empty($notes_id)) // 修改
        {
			$notes_data['notes_type'] = $_POST['notes_type'];
			$notes_data['topic_type'] = $_POST['topic_type'];
			$notes_data['notes_title'] = I('post.notes_title');
			$notes_data['notes_id'] = $notes_id;
			$notes_data['notes_content'] = $_POST['notes_content'];
			$notes_data['notes_thumb'] = $_POST['notes_thumb'];
			// $notes_data['add_staff'] = $staff_no;
			$notes_data['update_time'] = date("Y-m-d H:i:s");
			$notes_data['add_time'] = date("Y-m-d H:i:s");
			//保存 对应群体、资料标签
			$notes_data['notes_group'] = I('post.notes_group');
			$notes_data['notes_label'] = I('post.notes_label');

            $notes_data['memo'] = I('post.memo');
            $notes_map['notes_id'] = $notes_id;
            $data_not = $notes->where($notes_map)->save($notes_data);
            saveLog(ACTION_NAME, 2, '', '操作员[' . session('staff_no') . ']于' . date("Y-m-d H:i:s") . '对备忘录编号 [' . $notes_data['notes_id'] . ']进行修改操作');
        } else{ // 添加
            $notes_data['notes_type'] = $_POST['notes_type'];
            $notes_data['topic_type'] = $_POST['topic_type'];
            $notes_data['notes_title'] = I('post.notes_title');
            $notes_data['notes_id'] = $notes_id;
            $notes_data['notes_content'] = $_POST['notes_content'];
            $notes_data['notes_thumb'] = $_POST['notes_thumb'];
            // $notes_data['add_staff'] = $staff_no;
            $notes_data['update_time'] = date("Y-m-d H:i:s");
            $notes_data['add_time'] = date("Y-m-d H:i:s");
			//保存 对应群体、资料标签
			$notes_data['notes_group'] = I('post.notes_group');
			$notes_data['notes_label'] = I('post.notes_label');

            $notes_data['memo'] = I('post.memo');
            $notes_data['status'] = 1;
            $data_not = $notes->add($notes_data);
            $staff_name = getStaffInfo($staff_no,staff_name);
            $type = getBdTypeInfo($notes_data['notes_type'],'notes_type',type_name);
            // 给党员加积分
            //$notes_integral_num = getConfig('integral_meeting_communist');
            //$communist_integral = getCommunistInfo($communist_no,'communist_integral');
            //updateIntegral(1,7,$communist_no,$communist_integral,$notes_integral_num,'完成学习笔记'); // 给签到党员加积分
            saveLog(ACTION_NAME, 2, '', '操作员[' . session('staff_no') . ']于' . date("Y-m-d H:i:s") . '对笔记编号 [' . $notes_data['notes_id'] . ']进行添加操作');
        }
        if ($data_not) {
        	showMsg('success', '操作成功！！！', U('edu_notes_index'),1);
        } else {
        	showMsg('error', '操作失败！！！', '');
        }
    }
    
    
    /**
     * @name  edu_notes_info()
     * @desc  笔记详情
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @addtime 2017-11-1
     * */
    public function edu_notes_info(){
        checkAuth(ACTION_NAME.session('notes_type'));
        $db_notes = M('edu_notes');
        $notes_id = I('get.notes_id'); // I方法获取数据
        $notes_info = getNotesInfo($notes_id, 'all');
        $notes_info['add_staff'] = getCommunistInfo( $notes_info['add_staff']);
        $notes_info['notes_type'] = getBdTypeInfo($notes_info['notes_type'],'notes_type','type_name');
        $notes_info['add_time'] = getFormatDate($notes_info['add_time'],'Y-m-d');
        if(!empty($notes_info['notes_thumb'])){
            $notes_info['notes_img'] =  getUploadInfo($notes_info['notes_thumb']);
            $this->assign('notes_img', $notes_info['notes_img']);
        }
		//查询 对应群体、资料标签
		$group_map["code_group"] = "notes_group_code";
		$group_map["code_no"] = $notes_info["notes_group"];
		$label_map["code_group"] = "notes_label_code";
		$label_map["code_no"] = $notes_info["notes_label"];
		$notes_info["notes_group"]=M("bd_code")->where($group_map)->getField("code_name");
		$notes_info["notes_label"]=M("bd_code")->where($label_map)->getField("code_name");
        //查询原件的标题、添加人、资料类型、添加时间
        $material_data = getMaterialInfo($notes_info["material_id"],'all');
        $this->assign('material_data', $material_data);
        $notes_map["notes_id"]=$notes_id;
        $material_map["material_id"]=$notes_info["material_id"];
        $notes_type=M("edu_notes")->where($notes_map)->getField("notes_type");      //笔记类型
        $material_cat=M("edu_material")->where($material_map)->getField("material_cat");  //原文件id
        //如果是学习笔记，在模板中做判断操作
		//如果是会议笔记，显示会议内容
		if($notes_type==2){
			$meeting_info=getMeetingInfo($notes_info["material_id"]);//获得会议信息
			$meeting_info['party_no'] = getPartyInfo($meeting_info['party_no']);
			$meeting_info['meeting_host'] = getCommunistInfo($meeting_info['meeting_host']);
            $meeting_info['add_time'] = getFormatDate($meeting_info['add_time'],'Y-m-d');

            $type_map['type_group'] = 'meeting_type';
            $type_map['type_no'] = $meeting_info['meeting_type'];
            $meeting_info['meeting_type_val'] = M('bd_type')->where($type_map)->getField('type_name');
            $meeting_info['meeting_type'] = getBdTypeInfo($meeting_info['meeting_type'],'meeting_type');
			$this->assign("meeting_info",$meeting_info);
            // 人员签到表格显示
            $att_meeting_communist=array();
            $where['meeting_no']=$meeting_info['meeting_no'];
            //实例化参会人员表对象
            $oa_meeting_communist=M("oa_meeting_communist");
            $meeting=$oa_meeting_communist->where($where)->select();
            $i=0;
            $party_name_arr = M('ccp_party')->getField('party_no,party_name');
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            $meeting_no = $meeting_info['meeting_no'];
            $_att_log=M("oa_att_log");
            foreach($meeting as &$v){
                $v['communist_name'] = $communist_name_arr[$v['communist_no']];
                $v['party_name'] =  $party_name_arr[$v['party_no']];
                $i=$i+1;
                $v["meeting_ids"]=$i;
                
                $meeting_precheck_time=getIsChecked($meeting_no,$v['communist_no']);
                
                //判断签到时间是否符合规定
                if($meeting_precheck_time!="no"){
                    $v['status_name']="<font color=red>已签到</font>";
                    $att_arr=$_att_log->where("att_id='$meeting_precheck_time'")->find();
                    if($att_arr["att_manner"]=="12"){
                        $att_address = M('oa_att_log')->where('att_id = '.$meeting_precheck_time.'')->getField('att_address');
                        $v["ckeck_type_name"]="手机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $v["check_addr"]=coordinateToAddr($att_address);
                    } else{
                        $v["ckeck_type_name"]="考勤机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $mach=getMachineInfo($row["machine_no"]);
                        $v["check_addr"]=$mach["machine_addr"];
                    }
                }else{
                    $v['status_name']="未签到";
                }
            }
            $this->assign("meeting_result",$meeting);
		}
		//如果是日常笔记 或者 其他，不显示原件，在模板中做判断
		/* if($notes_type==3){
		} */

		//原件类型
		$this->assign("notes_type",$notes_type);
		$this->assign("material_cat",$material_cat);

		$this->assign('notes_info', $notes_info);
        $this->display("Edunotes/edu_notes_info");
    }
    
    /**
     * @name  edu_notes_del()
     * @desc  删除笔记
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @addtime 2017-11-1
     * */
    public function edu_notes_del(){
        checkAuth(ACTION_NAME.session('notes_type'));
        $db_notes = M('edu_notes');
        $notes_id = I('get.notes_id'); // I方法获取数据
        $notes_map['notes_id'] = $notes_id;
        $notes_del = $db_notes->where($notes_map)->delete();
        if ($notes_del) {
        	saveLog(ACTION_NAME, 3, '', '操作员[' . session('staff_no') . ']于' . date("Y-m-d H:i:s") . '对备忘录编号 [' . $notes_id . ']进行删除操作');
        	showMsg('success', '操作成功！！！', U('Edunotes/edu_notes_index'));
        } else {
        	showMsg('error', '操作失败！！！', '');
        }    
        
    }

}
    
