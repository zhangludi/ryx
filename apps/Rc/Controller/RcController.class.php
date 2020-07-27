<?php
namespace Rc\Controller;
use Common\Controller\BaseController;
class RcController extends BaseController{
    /**
     * @name  rc_meeting_index()
     * @desc  会议报表
     * @author 刘丙涛
     * @time   20171023
     */
    public function rc_meeting_index(){
    	
        $this->display('Rcmeeting/rc_meeting_index');
    }
    /**
     * @name  rc_meeting_data()
     * @desc  会议报表数据获取
     * @author 刘丙涛
     * @time   20171023
     */
    public function rc_meeting_data(){

        $db_meeting = M('oa_meeting');
        $db_party = M('ccp_party');
        $meeting_where = 'status=23';
        $array = $db_party->where('status=1')->field('party_no,party_name')->select();
        $start = I('get.start');
        if (!empty($start)){
            $start = explode(' - ',$start);
            $meeting_where .= " and (UNIX_TIMESTAMP(meeting_start_time)>UNIX_TIMESTAMP('".$start['0']."') and UNIX_TIMESTAMP(meeting_start_time)<UNIX_TIMESTAMP('".$start['1']."'))";
        }
        foreach ($array as &$list){
            $branch_meeting = $db_meeting->where($meeting_where." and meeting_type=2002 and party_no=".$list['party_no'])->count();//支部会议
            $zhiwei_meeting = $db_meeting->where($meeting_where." and meeting_type=2003 and party_no=".$list['party_no'])->count();//支委会议
            $group_meeting = $db_meeting->where($meeting_where." and meeting_type=2004 and party_no=".$list['party_no'])->count();//小组会议
            $communist_lecture = $db_meeting->where($meeting_where." and meeting_type=2005 and party_no=".$list['party_no'])->count();//党课
            $count = $branch_meeting+$zhiwei_meeting+$group_meeting+$communist_lecture;
            $list['branch_meeting'] = "<span  onclick='att_list(2002,".$list['party_no'].")'>".$this->setcolor($branch_meeting)."</span>";
            $list['zhiwei_meeting'] = "<span  onclick='att_list(2003,".$list['party_no'].")'>".$this->setcolor($zhiwei_meeting)."</span>";
            $list['group_meeting'] = "<span  onclick='att_list(2004,".$list['party_no'].")'>".$this->setcolor($group_meeting)."</span>";
            $list['communist_lecture'] = "<span  onclick='att_list(2005,".$list['party_no'].")'>".$this->setcolor($communist_lecture)."</span>";
            $list['num'] = $count;
            $list['count'] = "<span  onclick='att_list(0,".$list['party_no'].")'>".$this->setcolor($count)."</span>";
        }
        ob_clean();$this->ajaxReturn($array);
    }
    /**
     * @name  setcolor()
     * @desc  会议数据
     * @author 刘丙涛
     * @time   20171023
     */
    private function setcolor($num){
        if ($num == 0){
            $num = "<font color='red'>".$num."次</font>";
        }else{
            $num = "<font color='green'>".$num."次</font>";
        }
        return $num;
    }
        /**
     * @name  rc_meeting_list()
     * @desc  会议列表
     * @author 刘丙涛
     * @time   20171023
     */
    public function rc_meeting_list(){
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        $meeting_type = I("get.meeting_type");
        $party_no = I("get.party_no");
        if ($meeting_type == '0'){
            $where = "(status=23 or meeting_start_time <date_sub(now(), interval 5 hour)) and party_no=$party_no";
        }else{
            $where = "(status=23 or meeting_start_time <date_sub(now(), interval 5 hour)) and party_no=$party_no and meeting_type=$meeting_type";
        }
        $data = $oa_meeting->where($where)->select();
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $type_name_arr = M('bd_type')->where("type_group = 'meeting_type'")->getField('type_no,type_name');
        foreach($data as &$list){
            $list['meeting_host_name'] = $communist_name_arr[$list['meeting_host']];
            $list['meeting_type_name'] = $type_name_arr[$list['meeting_type']];
            $result2 = getMeetingCommunistCount($list['meeting_no']);
            $list['shoulded_info']= $result2['attended'].'/'.$result2['should'];
            $list['operate'] = "<button type='button' onclick='att_info(".$list['meeting_no'].")' class='btn btn-xs green btn-outline' href=><i class='fa fa-info-circle'></i>  详情</button>  ";
        }

        $this->assign("row",$data);
        $this->display("Rcmeeting/rc_meeting_list");
    }
        /**
     * @name  rc_meeting_info()
     * @desc  会议详情
     * @author 刘丙涛
     * @time   20171023
     */
    public function rc_meeting_info(){
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        //实例化考勤机表对象
        $_att_machine=M("oa_att_machine");
        //实例化参会人员表对象
        $oa_meeting_communist=M("oa_meeting_communist");
        $_att_log=M("oa_att_log");
        $meeting_no=I("get.meeting_no");
        if($meeting_no){
            $row=getMeetingInfo($meeting_no);
            //判断该会场是否有摄像头
            if($row['meeting_camera']=="no"){
                $row['meeting_camera']="该会场暂无摄像头";                
            }else{
                $is_camera='1';
            }
            $row["shoulded_info"]=$row["attended"]."/".$row["should"];
            $this->assign("is_camera",$is_camera);
            $this->assign("row",$row);
            $att_meeting_communist=array();
            $where['meeting_no']="$meeting_no";
            $meeting=$oa_meeting_communist->where($where)->select();
            $i=0;
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            $party_name_arr = M('ccp_party')->getField('party_no,party_name');
            foreach($meeting as &$v){
                $v['communist_name'] = $communist_name_arr[$v['communist_no']];
                $v['party_name'] = $party_name_arr[$v['party_no']];
                $i=$i+1;
                $v["meeting_ids"]=$i;
                $where1=array(
                    "meeting_no"=>"$meeting_no",
                    "communist_no"=>$v['communist_no']
                );
                $data=array();
                $data=$oa_meeting_communist->where($where1)->find();
                $meeting_precheck_time=getIsChecked($meeting_no,$v['communist_no']);
                //判断签到时间是否符合规定
                if($meeting_precheck_time!="no"){
                    $v['status_name']="<font color=red>已签到</font>";
                    $att_arr=$_att_log->where("att_id='$meeting_precheck_time'")->find();
                    if($att_arr["att_manner"]=="12"){
                        $v["ckeck_type_name"]="手机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $v["check_addr"]=coordinateToAddr($data["checked_addr"]);
                        $v["study_content"]=$data["study_content"];
    
    
                    }
                    else{
                        $v["ckeck_type_name"]="考勤机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $mach=getMachineInfo($row["machine_no"]);
                        $v["check_addr"]=$mach["machine_addr"];
                        $v["study_content"]=$data["study_content"];
                    }
                }else{
                    $v['status_name']="未签到";
                }
            }
    
            $this->assign("meeting_result",$meeting);
        }
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
        $this->display("Rcmeeting/rc_meeting_info");
    }
    // 绩效汇总
    public function rc_communist_index()
    {
    	$party_list = checkDataAuth(session("staff_no"),"is_admin",1,"arr");
    	$this->assign('party_list',$party_list);
    	$this->display("Rc/rc_communist_index");
    }
    // 绩效汇总数据加载
    public function report_oaworkplan_perf_index_data()
    {
    	$party_id = I('get.party_id');
        $comm_map['status'] = 1;
    	if (! empty($party_id)) {
    		$comm_map['party_no'] = $party_id;
    	}
    	$perf_list=array();
    	$oa_workplan = M('oa_workplan');
    	$ccp_communist = M('ccp_communist');
    	$bd_code = M('bd_code');
    	$oa_workplan_perf = M('oa_workplan_perf');
    	$communist_list = $ccp_communist->where($comm_map)->select();
    	$code_list = $bd_code->where("code_group='perf_light'")->select();
    	
    	$post_name_arr = M('ccp_party_duty')->getField('post_no,post_name');
    	$party_name_arr = M('ccp_party')->getField('party_no,party_name');
    	foreach ($communist_list as &$communist) {
    		$flag = $oa_workplan_perf->where("perf_communist=" . $communist['communist_no'])->count();
    		$score = 0;
    		if ($flag > 0) {
    			foreach ($code_list as & $code) {
    				$light = $code['code_no'];
    				$perf_count = $oa_workplan_perf->where("perf_light='$light' and perf_communist=" . $communist['communist_no'])->count();
    				if ($perf_count > 0) {
    					$score += $code['memo'] * $perf_count;
    				}
    			}
    			$communist['party_name'] = $party_name_arr[$communist['party_no']];
    			$communist['post_name'] = $post_name_arr[$communist['communist_post_no']];
    			$communist['score'] = $score;
    		}
    	}
    	ob_clean();$this->ajaxReturn($perf_list);
    }   
}
